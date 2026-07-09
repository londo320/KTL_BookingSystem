<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Import the base controller
use App\Models\Customer;
use App\Models\Depot;
use App\Models\User;
use App\Notifications\AccessGrantedNotification;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    // Display the list of users
    public function index(Request $request)
    {
        $showDeleted = $request->get('show_deleted', false);
        $search = $request->get('search', '');
        $customerFilter = $request->get('customer_id', '');
        $depotFilter = $request->get('depot_id', '');
        
        // Start with base query
        if ($showDeleted) {
            $query = User::onlyTrashed();
        } else {
            $query = User::query();
        }
        
        // Add relationships
        $query->with(['roles', 'depots', 'customers']);
        
        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply customer filter
        if (!empty($customerFilter)) {
            $query->whereHas('customers', function($q) use ($customerFilter) {
                $q->where('customers.id', $customerFilter);
            });
        }
        
        // Apply depot filter
        if (!empty($depotFilter)) {
            $query->whereHas('depots', function($q) use ($depotFilter) {
                $q->where('depots.id', $depotFilter);
            });
        }
        
        // Order alphabetically by name
        $query->orderBy('name', 'asc');
        
        $users = $query->paginate(15)->appends($request->query());
        
        $roles = Role::all(); // Get all roles for the dropdown
        $depots = Depot::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('warehouse.users.index', compact(
            'users', 'roles', 'depots', 'customers', 'showDeleted', 
            'search', 'customerFilter', 'depotFilter'
        ));
    }

    // Show the form for editing a user
    public function edit($id)
    {
        $user = User::withTrashed()->with(['roles', 'depots', 'customers', 'functions', 'customRoles'])->findOrFail($id);
        
        // Check if current user can edit this user
        if (!$user->canBeEditedBy(auth()->user())) {
            if ($user->isProtectedSystemOwner()) {
                abort(403, 'This is a protected system owner account. Only they can edit their own profile.');
            } else {
                abort(403, 'You do not have permission to edit this user.');
            }
        }
        
        $roles = Role::all(); // Get all roles for the checkboxes
        $depots = Depot::all();
        $customers = Customer::all();  // Get all customers for selection
        $customRoles = \App\Models\CustomRole::active()->orderBy('display_name')->get();

        return view('warehouse.users.edit_comprehensive', compact('user', 'roles', 'depots', 'customers', 'customRoles'));
    }

    // Show user details
    public function show($id)
    {
        $user = User::withTrashed()->with(['roles', 'depots', 'customers', 'functions', 'customRoles'])->findOrFail($id);
        
        // Check if current user can view this user
        if (!$user->canBeEditedBy(auth()->user())) {
            if ($user->isProtectedSystemOwner()) {
                abort(403, 'This is a protected system owner account.');
            } else {
                abort(403, 'You do not have permission to view this user.');
            }
        }

        return redirect()->route('app.users.edit', $user->id);
    }

    // Update the user's data
    public function update(Request $request, $id)
    {
        // Find the user first to check permissions (including soft-deleted)
        $user = User::withTrashed()->findOrFail($id);
        
        // Check if current user can edit this user (but allow Paul Carr to appear editable)
        if (!$user->canBeEditedBy(auth()->user()) && !$user->isProtectedSystemOwner()) {
            abort(403, 'You do not have permission to edit this user.');
        }
        
        // For protected system owner editing themselves, allow less strict validation
        $isProtectedOwnerEditingSelf = $user->isProtectedSystemOwner() && auth()->user()->id === $user->id;
        
        // Validate the input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'is_active' => 'required|boolean',
            'role_ids' => $isProtectedOwnerEditingSelf ? 'nullable|array' : 'required|array', // Protected user can assign any roles
            'role_ids.*' => 'integer|exists:roles,id',
            'customer_ids' => 'nullable|array',            // Multiple customers (optional)
            'customer_ids.*' => 'exists:customers,id',       // Ensure all selected customers are valid
            'depot_ids' => $isProtectedOwnerEditingSelf ? 'nullable|array' : 'required|array', // Protected user not restricted
            'depot_ids.*' => 'exists:depots,id',           // Ensure all selected depots are valid
            'depot_id' => 'nullable|exists:depots,id',    // Default depot (optional)
            'customer_id' => 'nullable|exists:customers,id', // Legacy customer for customer role
            'function_keys' => 'nullable|array',           // Function assignments
            'function_keys.*' => 'string',                 // Function key strings
            'custom_role_ids' => 'nullable|array',         // Custom role assignments
            'custom_role_ids.*' => 'integer|exists:custom_roles,id',
        ]);

        // Update user basic fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_active = $validated['is_active'];
        $user->depot_id = $validated['depot_id']; // Set default depot
        $user->customer_id = $validated['customer_id']; // Legacy customer field
        
        // Protected system owner can never be disabled
        if ($user->isProtectedSystemOwner() && !$user->is_active) {
            $user->is_active = true; // Force to active
        }
        
        $user->save();

        // Check if this is Paul Carr being edited by someone else
        $isPaulCarrEditedByOther = $user->isProtectedSystemOwner() && auth()->user()->id !== $user->id;
        
        if (!$isPaulCarrEditedByOther) {
            // Check if user previously had no access (for welcome email)
            $hadPreviousRoles = $user->roles()->exists();
            $hadPreviousDepots = $user->depots()->exists();
            $wasNewUser = !$hadPreviousRoles || !$hadPreviousDepots;
            
            // Sync multiple roles via pivot 
            $roleIds = $validated['role_ids'] ?? [];
            
            // Protected system owner can assign themselves any roles (including removing admin if they want)
            // but we ensure they always have access through other means
            $user->roles()->sync($roleIds);

            // Sync depots (many-to-many relationship)
            $depotIds = $validated['depot_ids'] ?? [];
            if ($user->isProtectedSystemOwner() && empty($depotIds)) {
                // Protected user gets access to all depots if none specified
                $depotIds = \App\Models\Depot::pluck('id')->toArray();
            }
            $user->depots()->sync($depotIds);

            // Sync multiple customers (many-to-many relationship)
            $customerIds = $validated['customer_ids'] ?? [];
            $user->customers()->sync($customerIds);
            
            // Send welcome email if user was previously without access and now has both roles and depots
            if ($wasNewUser && !empty($roleIds) && !empty($depotIds)) {
                $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
                $depotNames = \App\Models\Depot::whereIn('id', $depotIds)->pluck('name')->toArray();
                
                $user->notify(new AccessGrantedNotification($roleNames, $depotNames));
            }

            // Sync custom roles, auto-attaching any CustomRole function bundle
            // whose name matches a newly assigned Spatie role (e.g.
            // gate-security -> the "Gate Security" bundle), so the role label
            // always comes with working permissions rather than just a name.
            $customRoleIds = $validated['custom_role_ids'] ?? [];
            $customRoleIds = array_unique(array_merge($customRoleIds, $this->matchingCustomRoleIds($roleIds)));
            $user->assignCustomRoles($customRoleIds);
        }

        // Handle individual function assignments (only as additions to custom roles)
        $assignedRoles = Role::whereIn('id', $validated['role_ids'])->pluck('name')->toArray();
        $isAdmin = in_array('admin', $assignedRoles);
        
        if (!$isAdmin && array_intersect(['warehouse', 'depot-admin', 'site-admin'], $assignedRoles)) {
            // For warehouse roles, individual functions work alongside custom roles
            $functionKeys = $validated['function_keys'] ?? [];
            
            // Always assign individual functions (they work in addition to custom roles)
            $validFunctionKeys = array_intersect($functionKeys, \App\Models\UserFunction::getAllFunctionKeys());
            $user->assignFunctions($validFunctionKeys);
        } else {
            // Clear functions for non-warehouse roles or admin
            $user->functions()->delete();
        }

        // If the password reset checkbox was checked, generate a new password
        if ($request->filled('reset_password')) {
            $newPassword = $this->generatePassword();
            $user->password = bcrypt($newPassword);
            $user->save();

            return redirect()->route('app.users.edit', $user->id)
                ->with('success', 'User updated successfully.')
                ->with('new_password', $newPassword);
        }

        return redirect()->route('app.users.index')
            ->with('success', 'User updated successfully.');
    }

    // Helper method to generate a random password
    private function generatePassword($length = 12)
    {
        // Expanded EFF-style wordlist for secure password generation (500+ words)
        $words = [
            'abacus', 'absorb', 'accent', 'action', 'atomic', 'attach', 'august', 'avenue',
            'awning', 'backup', 'badge', 'banana', 'beacon', 'behalf', 'benzine', 'bicycle',
            'bishop', 'blanket', 'bleach', 'blimp', 'blocks', 'bloody', 'blouse', 'blunt',
            'bobsled', 'bogey', 'bombard', 'bookend', 'boxing', 'broken', 'bucket', 'budget',
            'buffet', 'buggy', 'bumper', 'bundle', 'butane', 'cabinet', 'cactus', 'camera',
            'campus', 'canal', 'cannon', 'canoe', 'canvas', 'carbon', 'career', 'castle',
            'catalog', 'cement', 'center', 'ceramic', 'chamber', 'channel', 'chapter', 'charcoal',
            'charge', 'chicken', 'chrome', 'church', 'circle', 'circus', 'classic', 'climate',
            'closet', 'cluster', 'coaster', 'coating', 'cobalt', 'cocaine', 'coconut', 'coding',
            'coffee', 'coiled', 'column', 'combat', 'comedy', 'comet', 'comfort', 'common',
            'company', 'complex', 'concept', 'conduct', 'connect', 'console', 'contact', 'content',
            'contest', 'context', 'copper', 'correct', 'cosmic', 'costume', 'cottage', 'county',
            'couple', 'courage', 'cover', 'cowboy', 'cradle', 'craft', 'crater', 'crazy',
            'create', 'credit', 'crisp', 'crystal', 'cubic', 'culture', 'cursor', 'custom',
            'cymbal', 'damage', 'danger', 'daring', 'dating', 'daybed', 'debris', 'decade',
            'decor', 'defeat', 'degree', 'delete', 'deluxe', 'demand', 'density', 'depot',
            'derive', 'design', 'desktop', 'device', 'diesel', 'digital', 'dilemma', 'dining',
            'diploma', 'direct', 'discard', 'domain', 'donate', 'dosage', 'double', 'dragon',
            'drain', 'drama', 'drawer', 'dream', 'dress', 'dried', 'drift', 'drill',
            'drink', 'drive', 'drone', 'drums', 'drying', 'duck', 'duke', 'dune',
            'during', 'dusk', 'dutch', 'dwarf', 'dynamic', 'eagle', 'early', 'earth',
            'easel', 'eastern', 'eating', 'echo', 'eclipse', 'economy', 'edge', 'editor',
            'effect', 'eight', 'either', 'eject', 'elbow', 'elder', 'eleven', 'elite',
            'email', 'ember', 'emerge', 'empire', 'employ', 'enable', 'encore', 'endless',
            'enemy', 'energy', 'engage', 'engine', 'enjoy', 'enough', 'enroll', 'entire',
            'entry', 'equal', 'error', 'escape', 'ethics', 'evoke', 'exact', 'exam',
            'exceed', 'except', 'excuse', 'execute', 'exile', 'exist', 'expand', 'expect',
            'expert', 'expose', 'extend', 'extent', 'fabric', 'facial', 'factor', 'falcon',
            'family', 'famous', 'fancy', 'father', 'fault', 'favor', 'feast', 'federal',
            'female', 'fender', 'ferry', 'fiber', 'fiddle', 'field', 'figure', 'filter',
            'final', 'finger', 'finish', 'fiscal', 'fixed', 'fizzy', 'flame', 'flash',
            'fleet', 'flesh', 'flight', 'float', 'flood', 'floor', 'flower', 'fluid',
            'flush', 'focal', 'folder', 'force', 'forest', 'forget', 'formal', 'format',
            'former', 'fossil', 'foster', 'found', 'fourth', 'frame', 'freeze', 'french',
            'fresh', 'friday', 'friend', 'front', 'frost', 'frozen', 'fruit', 'fusion',
            'future', 'gadget', 'galaxy', 'garage', 'garden', 'gather', 'gender', 'gentle',
            'ghost', 'giant', 'gift', 'given', 'glacier', 'glass', 'global', 'glory',
            'glove', 'golden', 'gossip', 'govern', 'grace', 'grade', 'grain', 'grand',
            'grant', 'grape', 'graph', 'grass', 'grave', 'great', 'green', 'greet',
            'grief', 'grill', 'grind', 'gross', 'ground', 'group', 'grown', 'guard',
            'guess', 'guest', 'guide', 'guild', 'guilty', 'guitar', 'hammer', 'handle',
            'hangar', 'happen', 'harbor', 'hardly', 'harsh', 'hatch', 'hazard', 'health',
            'hearing', 'heart', 'heavy', 'height', 'helmet', 'helper', 'hidden', 'hiking',
            'history', 'hockey', 'holder', 'honest', 'honor', 'hoping', 'horizon', 'horror',
            'hotel', 'hover', 'human', 'humble', 'humor', 'hybrid', 'iconic', 'ideal',
            'image', 'impact', 'import', 'income', 'indeed', 'index', 'infant', 'inform',
            'inject', 'injury', 'inline', 'inner', 'input', 'insect', 'inside', 'inspire',
            'install', 'intact', 'intake', 'intend', 'invest', 'invite', 'involve', 'island',
            'issue', 'ivory', 'jacket', 'jargon', 'jasmine', 'jersey', 'jigsaw', 'jobless',
            'jockey', 'joking', 'jolly', 'judge', 'juice', 'jumbo', 'jumper', 'jungle',
            'junior', 'junk', 'justice', 'kernel', 'kettle', 'kidney', 'kitten', 'knife',
            'knot', 'known', 'label', 'labor', 'ladder', 'landed', 'laptop', 'large',
            'laser', 'last', 'later', 'launch', 'lawyer', 'leader', 'league', 'learn',
            'lease', 'least', 'leather', 'leave', 'ledge', 'legal', 'legend', 'lemon',
            'length', 'lesson', 'level', 'lever', 'liberty', 'license', 'likely', 'limit',
            'linear', 'linked', 'liquid', 'listen', 'litter', 'living', 'lizard', 'loan',
            'lobby', 'local', 'locate', 'locked', 'lodge', 'logic', 'lonely', 'lookup',
            'loop', 'loose', 'lounge', 'lovely', 'lower', 'lucky', 'lumber', 'lunar',
            'lunch', 'luxury', 'lying', 'machine', 'madness', 'magic', 'magnet', 'makeup',
            'manage', 'mandate', 'mango', 'manner', 'manual', 'maple', 'marble', 'march',
            'margin', 'marine', 'marker', 'market', 'marvel', 'master', 'matrix', 'matter',
            'mature', 'meadow', 'measure', 'media', 'medical', 'medium', 'member', 'memory',
            'mental', 'mentor', 'method', 'metric', 'middle', 'mighty', 'miller', 'mining',
            'minor', 'minute', 'miracle', 'mirror', 'missing', 'mission', 'mistake', 'mixture',
            'mobile', 'modern', 'modest', 'modify', 'moment', 'monday', 'money', 'monitor',
            'month', 'moral', 'mortar', 'mother', 'motion', 'motor', 'mount', 'mouse',
            'mouth', 'move', 'movie', 'muffin', 'muscle', 'museum', 'music', 'mutual',
            'myself', 'mystery', 'napkin', 'narrow', 'nation', 'native', 'nature', 'nearby',
            'nearly', 'nectar', 'needle', 'nephew', 'nerve', 'nested', 'network', 'neural',
            'never', 'newer', 'nicely', 'night', 'noble', 'nobody', 'noise', 'nomad',
            'normal', 'north', 'notice', 'notion', 'novel', 'number', 'nurse', 'nylon',
            'object', 'obtain', 'ocean', 'offer', 'office', 'often', 'online', 'only',
            'opener', 'option', 'orange', 'orbit', 'order', 'organ', 'origin', 'other',
            'outcome', 'outer', 'output', 'outside', 'oval', 'overall', 'owner', 'oxygen',
            'ozone', 'package', 'packed', 'paddle', 'palace', 'panel', 'panic', 'paper',
            'parade', 'parent', 'park', 'parcel', 'party', 'patch', 'pathway', 'patrol',
            'pause', 'payment', 'peace', 'peach', 'peaked', 'peanut', 'peasant', 'pecan',
            'pelican', 'penalty', 'pencil', 'people', 'pepper', 'perfect', 'permit', 'person',
            'phone', 'phrase', 'physics', 'piano', 'pickup', 'picture', 'piece', 'pilot',
            'pink', 'pioneer', 'pipeline', 'pistol', 'pitch', 'pizza', 'place', 'plain',
            'planet', 'plastic', 'plate', 'platform', 'playoff', 'please', 'pledge', 'pliers',
            'plot', 'pluck', 'plugin', 'plunge', 'plywood', 'pocket', 'poem', 'point',
            'polar', 'policy', 'polite', 'polygon', 'pony', 'poplar', 'popular', 'portal',
            'portion', 'posture', 'potato', 'pottery', 'poverty', 'powder', 'power', 'praise',
            'prayer', 'precise', 'predict', 'prefer', 'premium', 'prepare', 'present', 'preset',
            'pretty', 'prevent', 'price', 'pride', 'primary', 'prime', 'print', 'prior',
            'prison', 'private', 'prize', 'problem', 'produce', 'product', 'profile', 'program',
            'project', 'promise', 'promote', 'proof', 'proper', 'protect', 'proud', 'provide',
            'public', 'puddle', 'pulse', 'pumice', 'punch', 'pupil', 'purple', 'purpose',
            'pursue', 'puzzle', 'pyramid', 'qualify', 'quality', 'quarter', 'question', 'quick',
            'quiet', 'quilt', 'quit', 'quote', 'rabbit', 'racing', 'radar', 'radio',
            'radius', 'ragdoll', 'raisin', 'random', 'ranger', 'rapid', 'rarely', 'rating',
            'ratio', 'reach', 'reader', 'ready', 'realm', 'reason', 'rebel', 'rebuild',
            'recall', 'recent', 'recipe', 'record', 'recover', 'reduce', 'refer', 'reform',
            'refuse', 'regard', 'region', 'regular', 'reject', 'relate', 'relax', 'relief',
            'remain', 'remark', 'remind', 'remote', 'remove', 'render', 'repair', 'repeat',
            'replace', 'reply', 'report', 'rescue', 'resent', 'reset', 'resist', 'resort',
            'result', 'retail', 'retain', 'retire', 'return', 'reveal', 'review', 'revise',
            'revolt', 'reward', 'rhythm', 'ribbon', 'rider', 'ridge', 'rifle', 'right',
            'rigid', 'rigor', 'rinse', 'ritual', 'rival', 'river', 'robot', 'rocket',
            'roller', 'roman', 'roster', 'rotate', 'rough', 'round', 'route', 'royal',
            'rubber', 'ruby', 'ruffle', 'rugby', 'ruler', 'rumor', 'runway', 'rural',
            'rustic', 'sacred', 'saddle', 'safari', 'safety', 'salmon', 'sample', 'saturn',
            'sauce', 'savage', 'saving', 'scale', 'scan', 'scare', 'scene', 'schema',
            'school', 'science', 'scope', 'score', 'scout', 'screen', 'script', 'scroll',
            'search', 'season', 'second', 'secret', 'sector', 'secure', 'select', 'senate',
            'senior', 'sense', 'sequel', 'series', 'serve', 'session', 'setup', 'seven',
            'shadow', 'shake', 'shame', 'shape', 'share', 'shark', 'sharp', 'shell',
            'shelter', 'sheriff', 'shield', 'shift', 'shine', 'shirt', 'shock', 'shoot',
            'shore', 'shower', 'shrimp', 'shrink', 'shuffle', 'sibling', 'sight', 'sigma',
            'signal', 'silent', 'silver', 'simple', 'single', 'sister', 'sixth', 'sketch',
            'skill', 'sleep', 'slice', 'slide', 'slight', 'slope', 'small', 'smart',
            'smile', 'smoke', 'smooth', 'snake', 'snow', 'soccer', 'social', 'sodium',
            'solar', 'solid', 'solve', 'sonic', 'sorry', 'sound', 'source', 'south',
            'space', 'spare', 'speak', 'special', 'speed', 'spend', 'sphere', 'spice',
            'spider', 'spine', 'spiral', 'spirit', 'split', 'spoke', 'sport', 'spray',
            'spread', 'spring', 'squad', 'square', 'stable', 'stadium', 'staff', 'stage',
            'stake', 'stamp', 'stand', 'staple', 'start', 'state', 'static', 'statue',
            'status', 'steady', 'steam', 'steel', 'steep', 'steer', 'stem', 'stereo',
            'stick', 'still', 'stock', 'stone', 'stood', 'stop', 'store', 'storm',
            'story', 'strand', 'strap', 'stream', 'street', 'stress', 'strict', 'strike',
            'string', 'strip', 'stroke', 'strong', 'stuck', 'studio', 'study', 'stuff',
            'stupid', 'style', 'submit', 'subtle', 'subway', 'sudden', 'suffer', 'sugar',
            'summer', 'sunday', 'sunset', 'super', 'supply', 'sure', 'surface', 'survey',
            'switch', 'symbol', 'syntax', 'system', 'table', 'tackle', 'taken', 'talent',
            'talked', 'target', 'taught', 'taxi', 'teach', 'team', 'tech', 'temple',
            'tenant', 'tender', 'tennis', 'tension', 'term', 'test', 'text', 'thank',
            'that', 'theft', 'their', 'theme', 'theory', 'therapy', 'these', 'thick',
            'thing', 'think', 'third', 'those', 'though', 'thread', 'three', 'threw',
            'through', 'throw', 'thumb', 'thunder', 'ticket', 'tidal', 'tight', 'timber',
            'timing', 'tissue', 'title', 'toast', 'today', 'token', 'tomato', 'tongue',
            'topic', 'torch', 'total', 'touch', 'tough', 'towel', 'tower', 'track',
            'trade', 'train', 'trash', 'travel', 'treat', 'trend', 'trial', 'tribe',
            'trick', 'tried', 'triple', 'truck', 'trust', 'truth', 'trying', 'tumor',
            'tunnel', 'turkey', 'turned', 'turtle', 'twelve', 'twenty', 'twice', 'twist',
            'typing', 'ultra', 'unable', 'uncle', 'under', 'unfold', 'unhappy', 'union',
            'unique', 'unite', 'unity', 'unless', 'unlock', 'until', 'update', 'upper',
            'urban', 'urgent', 'usage', 'useful', 'user', 'usual', 'value', 'vapor',
            'varied', 'vector', 'vendor', 'venture', 'verify', 'verse', 'versus', 'vessel',
            'victim', 'video', 'view', 'viral', 'virus', 'visit', 'visual', 'vital',
            'vocal', 'voice', 'volume', 'voter', 'voyage', 'waffle', 'walker', 'wallet',
            'walnut', 'wanted', 'warm', 'warn', 'waste', 'watch', 'water', 'wave',
            'wealth', 'weapon', 'weather', 'weekly', 'weight', 'weird', 'welcome', 'west',
            'whale', 'wheat', 'wheel', 'where', 'which', 'while', 'white', 'whole',
            'whose', 'widow', 'width', 'wild', 'window', 'wine', 'wing', 'winter',
            'wisdom', 'wise', 'wish', 'with', 'wizard', 'woman', 'wonder', 'wooden',
            'wool', 'word', 'work', 'world', 'worry', 'worse', 'worst', 'worth',
            'would', 'wrap', 'write', 'wrong', 'wrote', 'year', 'yellow', 'young',
            'youth', 'zebra', 'zero', 'zone'
        ];
        
        // Generate password with 2-3 words plus numbers for better security
        $wordCount = rand(2, 3);
        $selectedWords = [];
        for ($i = 0; $i < $wordCount; $i++) {
            $selectedWords[] = ucfirst($words[array_rand($words)]);
        }
        
        $password = implode('', $selectedWords) . rand(100, 999);

        // Ensure minimum length
        return strlen($password) >= $length ? $password : $password . rand(10, 99);
    }

    // Store method for creating a user
    public function store(Request $request)
    {

        // DEBUG: dump all input
        // dd($request->all());

        // Validate the input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role_ids' => 'required|array',           // Multiple roles
            'role_ids.*' => 'integer|exists:roles,id',
            'customer_ids' => 'nullable|array',            // Multiple customers (optional)
            'customer_ids.*' => 'exists:customers,id',       // Ensure all selected customers are valid
            'depot_ids' => 'required|array',
            'depot_ids.*' => 'exists:depots,id',
            'depot_id' => 'nullable|exists:depots,id',    // Default depot (optional)
            'password' => 'required_without:generate_password|string|min:8',
            'generate_password' => 'nullable|boolean',
        ]);

        // Handle password generation
        $password = $validated['password'] ?? null;
        if ($request->filled('generate_password') || ! $password) {
            $password = $this->generatePassword();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($password),
            'depot_id' => $validated['depot_id'],
        ]);

        // Sync multiple roles via pivot
        $user->roles()->sync($validated['role_ids']);

        // Auto-attach any CustomRole function bundle whose name matches an
        // assigned Spatie role (e.g. gate-security -> the "Gate Security"
        // bundle), so new users get working permissions immediately rather
        // than just a role label.
        $matchingCustomRoleIds = $this->matchingCustomRoleIds($validated['role_ids']);
        if (! empty($matchingCustomRoleIds)) {
            $user->assignCustomRoles($matchingCustomRoleIds);
        }

        // Sync depots
        $user->depots()->sync($validated['depot_ids']);

        // Sync multiple customers (many-to-many relationship)
        $customerIds = $validated['customer_ids'] ?? [];
        $user->customers()->sync($customerIds);

        // Show generated password if applicable
        if ($request->filled('generate_password') || ! $validated['password']) {
            return redirect()->route('app.users.edit', $user->id)
                ->with('success', 'User created successfully.')
                ->with('new_password', $password);
        }

        return redirect()->route('app.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Find CustomRole ids whose name matches one of the given Spatie role
     * ids, converting hyphens to underscores (e.g. gate-security ->
     * gate_security) since the two role systems use different naming
     * conventions. Used so assigning a Spatie role that has a same-named
     * CustomRole function bundle also grants those functions automatically.
     */
    private function matchingCustomRoleIds(array $roleIds): array
    {
        $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
        $customRoleNames = array_map(fn ($name) => str_replace('-', '_', $name), $roleNames);

        return \App\Models\CustomRole::whereIn('name', $customRoleNames)->pluck('id')->toArray();
    }

    public function create()
    {
        $roles = Role::all();
        $depots = Depot::all();
        $customers = Customer::all();

        return view('warehouse.users.create', compact('roles', 'depots', 'customers'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->isProtectedSystemOwner()) {
            abort(403, 'Cannot delete protected system owner account.');
        }
        
        if (!$user->canBeEditedBy(auth()->user())) {
            abort(403, 'You do not have permission to delete this user.');
        }
        
        // Clean up all relationships and fields before soft-deleting
        $user->roles()->detach();
        $user->depots()->detach();
        $user->customers()->detach();
        if (method_exists($user, 'customRoles')) {
            $user->customRoles()->detach();
        }
        $user->functions()->delete();
        
        // Clear default depot and customer fields
        $user->depot_id = null;
        $user->customer_id = null;
        $user->save();
        
        $user->delete();
        
        return redirect()->route('app.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if (!$user->trashed()) {
            return redirect()->route('app.users.index')
                ->with('error', 'User is not deleted.');
        }
        
        if ($user->isProtectedSystemOwner()) {
            abort(403, 'Cannot restore protected system owner account.');
        }
        
        if (!$user->canBeEditedBy(auth()->user())) {
            abort(403, 'You do not have permission to restore this user.');
        }
        
        $user->restore();
        
        return redirect()->route('app.users.index')
            ->with('success', 'User restored successfully.');
    }
}
