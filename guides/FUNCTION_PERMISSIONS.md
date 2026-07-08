# Function-Based Permission System

This system provides granular, function-based permissions that replace rigid role-based views.

## Overview

Users can now be assigned specific functions rather than broad roles. This allows for precise control over what each user can access and do within the system.

## Blade Directives

### @canFunction
Check if the current user has a specific function:
```blade
@canFunction('bookings.edit')
    <button class="btn-primary">Edit Booking</button>
@endcanFunction
```

### @cannotFunction  
Check if the current user does NOT have a specific function:
```blade
@cannotFunction('bookings.delete')
    <p class="text-gray-500">You cannot delete bookings</p>
@endcannotFunction
```

### @hasAnyFunction
Check if the current user has ANY of the specified functions:
```blade
@hasAnyFunction(['bookings.view', 'bookings.edit'])
    <a href="{{ route('bookings.index') }}">Bookings</a>
@endhasAnyFunction
```

### @hasAllFunctions
Check if the current user has ALL of the specified functions:
```blade
@hasAllFunctions(['bookings.edit', 'bookings.update'])
    <button class="btn-warning">Save Changes</button>
@endhasAllFunctions
```

## Model Methods

### User Model Methods

#### hasFunction($functionKey)
```php
if (auth()->user()->hasFunction('bookings.edit')) {
    // User can edit bookings
}
```

#### assignFunctions($functionKeys)
```php
$user->assignFunctions(['bookings.view', 'bookings.edit']);
```

#### getFunctionKeys()
```php
$userFunctions = auth()->user()->getFunctionKeys();
// Returns array of function keys assigned to user
```

## Function Categories

The system includes over 250+ functions organized into 21 categories:

1. **Dashboard & Navigation** - Basic dashboard access
2. **Bookings - Core Operations** - CRUD operations for bookings
3. **Bookings - Status & Operations** - Arrival, departure, bay assignment
4. **Bookings - Actions & Workflow** - Rebooking, cancellation, history
5. **Bookings - Export & Documents** - PDF, CSV, Excel exports
6. **Factory Operations** - Factory booking management
7. **Factory Workflow** - Factory-specific processes
8. **Tipping Operations** - Tipping workflow management
9. **Operations Control** - Site operations and control
10. **Tipping Infrastructure** - Locations and bays management
11. **Depot Map & Visualization** - Map-based operations
12. **Priority & Settings** - Priority management
13. **Slots Management** - Slot creation and templates
14. **Booking Configuration** - Booking types and rules
15. **Customer Management** - Customer CRUD and behavior
16. **Carrier Management** - Carrier operations and merging
17. **Depot & Location Management** - Depot and location setup
18. **Product Management** - Product and pallet types
19. **Trailer Management** - Trailer types and operations
20. **Time & Scheduling** - Arrival time settings
21. **User & System Management** - User management and settings
22. **Reports & Analytics** - Reporting functions
23. **Special Functions** - Emergency and recovery functions

## Role Hierarchy

- **admin**: Has access to ALL functions automatically
- **site-admin**: Can be assigned warehouse functions + site management
- **depot-admin**: Can be assigned warehouse functions + depot management  
- **warehouse**: Can be assigned specific warehouse functions
- **customer**: Only has customer portal access (no function assignments)

## Usage Examples

### Navigation Menu
```blade
@hasAnyFunction(['bookings.view', 'bookings.create'])
<li class="nav-item">
    <a href="#" class="nav-link">Bookings</a>
    <ul class="dropdown-menu">
        @canFunction('bookings.view')
        <li><a href="{{ route('bookings.index') }}">View All</a></li>
        @endcanFunction
        
        @canFunction('bookings.create')
        <li><a href="{{ route('bookings.create') }}">Create New</a></li>
        @endcanFunction
    </ul>
</li>
@endhasAnyFunction
```

### Action Buttons
```blade
<div class="booking-actions">
    @canFunction('bookings.edit')
    <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary">
        Edit
    </a>
    @endcanFunction
    
    @canFunction('bookings.delete')
    <form method="POST" action="{{ route('bookings.destroy', $booking) }}">
        @csrf @method('DELETE')
        <button class="btn btn-danger">Delete</button>
    </form>
    @endcanFunction
    
    @canFunction('bookings.export.pdf')
    <a href="{{ route('bookings.pdf', $booking) }}" class="btn btn-secondary">
        Export PDF
    </a>
    @endcanFunction
</div>
```

### Controller Protection
```php
public function edit(Booking $booking)
{
    if (!auth()->user()->hasFunction('bookings.edit')) {
        abort(403, 'You do not have permission to edit bookings.');
    }
    
    return view('bookings.edit', compact('booking'));
}
```

### Conditional Content
```blade
<div class="dashboard-widgets">
    @canFunction('dashboard.warehouse')
    <div class="widget">
        <h3>Warehouse Overview</h3>
        <!-- Widget content -->
    </div>
    @endcanFunction
    
    @hasAnyFunction(['reports.daily', 'reports.weekly', 'reports.monthly'])
    <div class="widget">
        <h3>Reports</h3>
        @canFunction('reports.daily')
        <a href="{{ route('reports.daily') }}">Daily Report</a>
        @endcanFunction
        
        @canFunction('reports.weekly')
        <a href="{{ route('reports.weekly') }}">Weekly Report</a>
        @endcanFunction
        
        @canFunction('reports.monthly')
        <a href="{{ route('reports.monthly') }}">Monthly Report</a>
        @endcanFunction
    </div>
    @endhasAnyFunction
</div>
```

## Best Practices

1. **Use descriptive function keys**: `bookings.edit` instead of `edit`
2. **Group related functions**: Use consistent prefixes like `bookings.`, `factory.`, `reports.`
3. **Check permissions in controllers**: Always validate permissions server-side
4. **Hide UI elements**: Use blade directives to hide buttons/links users can't access
5. **Provide meaningful error messages**: Use clear 403 error messages
6. **Document new functions**: Add new functions to `UserFunction::getAllFunctions()`

## Migration from Role-Based Views

Old role-based approach:
```blade
@if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('warehouse'))
    <button>Edit Booking</button>
@endif
```

New function-based approach:
```blade
@canFunction('bookings.edit')
    <button>Edit Booking</button>
@endcanFunction
```

This provides much more granular control and easier management of permissions.