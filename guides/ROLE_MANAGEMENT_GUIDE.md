# Role Management Guide - KTL Booking System

## 🎯 Quick Start

### Access Role Management
**URL**: `http://test.test/app/custom-roles`

**Or navigate**: Admin Panel → Custom Roles (in sidebar)

**Note**: If you get 404, make sure you're logged in as admin

---

## 📋 Create Predefined Roles (One-Click Setup)

### Step 1: Go to Role Management
1. Login as admin (`admin@ktl.com` / `password`)
2. Navigate to `http://test.test/app/custom-roles`
3. Click **"📋 Create Predefined Roles"** button (top right)
4. Confirm the popup (creates 7 roles)

### Step 2: Roles Created Automatically
This creates 7 ready-to-use roles:

| Role | Functions | Who Should Use It |
|------|-----------|------------------|
| **Warehouse Operative** | Check-in, assign bays, update status | Daily warehouse floor staff |
| **Warehouse Manager** | All warehouse operations + reports | Warehouse supervisors |
| **Forklift Driver** | View tasks, update tipping progress | Equipment operators |
| **Yard Controller** | Manage vehicle movements, bay allocation | Yard management staff |
| **Gate Security** | Check vehicles in/out, verify bookings | Security personnel |
| **Reports Viewer** | Read-only access to all data | Management oversight |
| **Customer Admin** | Manage company bookings and users | Customer company admins |

### Step 3: Assign to Users
1. Go to **Admin → Users**
2. Edit a user
3. Assign them to a custom role
4. They immediately get those permissions

---

## ➕ Create Custom Roles (Manual)

### Step 1: Create New Role
1. Go to `/admin/custom-roles`
2. Click **"➕ Create Custom Role"**

### Step 2: Fill in Details
- **Name**: Technical name (e.g., `shift_supervisor`)
- **Display Name**: Friendly name (e.g., "Shift Supervisor")
- **Description**: What this role does
- **Active**: Check to enable immediately

### Step 3: Assign Functions
Check the boxes for functions this role should have access to:

#### Example: Shift Supervisor Role
```
✓ Dashboard & Navigation
  ✓ dashboard.warehouse

✓ Bookings - Core Operations
  ✓ bookings.view
  ✓ bookings.create
  ✓ bookings.edit
  ✓ bookings.show

✓ Bookings - Status & Operations
  ✓ bookings.arrival
  ✓ bookings.departure
  ✓ bookings.assign-bay

✓ Tipping Operations
  ✓ tipping-workflow.dashboard
  ✓ tipping-workflow.show
```

### Step 4: Save
Click **"Create Custom Role"** - role is now ready to assign!

---

## 🔧 Available Functions

### Dashboard & Navigation
- `dashboard.view` - Main dashboard
- `dashboard.warehouse` - Warehouse dashboard

### Bookings - Core
- `bookings.view` - View bookings list
- `bookings.create` - Create new booking
- `bookings.edit` - Edit existing booking
- `bookings.show` - View booking details
- `bookings.delete` - Delete booking
- `bookings.search` - Search bookings

### Bookings - Status & Operations
- `bookings.arrival` - Mark as arrived
- `bookings.arrival.form` - Access arrival form
- `bookings.departure` - Mark as departed
- `bookings.assign-bay` - Assign to bay
- `bookings.transfer-bay` - Transfer between bays
- `bookings.move-to-waiting` - Move to waiting area
- `bookings.clear-bay` - Clear from bay

### Bookings - Actions
- `bookings.rebook` - Rebook cancelled booking
- `bookings.cancel` - Cancel booking
- `bookings.history` - View history
- `bookings.start-tipping` - Start tipping
- `bookings.complete-tipping` - Complete tipping

### Bookings - Export
- `bookings.export.pdf` - Export to PDF
- `bookings.export.csv` - Export to CSV
- `bookings.export.excel` - Export to Excel
- `bookings.download-pdf` - Download PDF
- `bookings.email-pdf` - Email PDF

### Factory Operations
- `factory-bookings.view` - View factory bookings
- `factory-bookings.create` - Create factory booking
- `factory-bookings.show` - View details
- `factory-bookings.edit` - Edit factory booking
- `factory-bookings.start-processing` - Start processing
- `factory-bookings.complete` - Complete booking
- `factory-bookings.mark-departed` - Mark departed

### Tipping Operations
- `tipping-workflow.dashboard` - Tipping dashboard
- `tipping-workflow.show` - View workflow
- `tipping-workflow.drop-trailer` - Drop trailer
- `tipping-workflow.move-to-location` - Move trailer
- `tipping-workflow.drop-trailer-detached` - Drop detached
- `tipping-workflow.move-to-bay` - Move to bay
- `tipping-workflow.start-tipping` - Start tipping
- `tipping-workflow.complete-tipping` - Complete tipping
- `tipping-workflow.trailer-depart` - Trailer departure

### Operations
- `operations.assign-drop-zone` - Assign drop zone
- `operations.shunt-to-bay` - Shunt to bay
- `operations.start-tipping` - Start tipping
- `operations.complete-tipping` - Complete tipping

---

## 👥 Assign Roles to Users

### Option 1: During User Creation
1. Admin → Users → Create User
2. Fill in user details
3. Select **Custom Role** from dropdown
4. Save

### Option 2: Edit Existing User
1. Admin → Users
2. Find user and click **Edit**
3. Change **Custom Role** dropdown
4. Save

### Option 3: Bulk Assignment
_(Coming soon - assign role to multiple users at once)_

---

## 🎨 Role Templates (Common Combinations)

### Template 1: Basic Operator
**Good for**: New warehouse staff
```
Functions:
- dashboard.warehouse
- bookings.view
- bookings.show
- bookings.arrival
- bookings.departure
```

### Template 2: Senior Operator
**Good for**: Experienced staff
```
Functions:
- dashboard.warehouse
- bookings.view/create/edit/show
- bookings.arrival/departure
- bookings.assign-bay/transfer-bay
- tipping-workflow.dashboard/show
```

### Template 3: Department Manager
**Good for**: Supervisors with reporting
```
Functions:
- All from Senior Operator
+ bookings.export.pdf/csv/excel
+ factory-bookings.view/create/edit
+ operations permissions
```

### Template 4: Read-Only Observer
**Good for**: Management/compliance
```
Functions:
- dashboard.view
- bookings.view/show
- bookings.export.pdf/csv
- factory-bookings.view
```

---

## 🔄 Manage Existing Roles

### View Role Details
1. Go to `/admin/custom-roles`
2. Click **👁️ View** on any role
3. See:
   - All assigned functions
   - Users with this role
   - Activity status

### Edit Role
1. Click **✏️ Edit** on role
2. Modify:
   - Display name
   - Description
   - Function assignments
3. Save changes
4. **All users with this role** automatically get updated permissions

### Activate/Deactivate Role
1. Click **⏸️ Deactivate** or **▶️ Activate**
2. Inactive roles:
   - Cannot be assigned to new users
   - Existing users lose permissions temporarily
3. Activate again to restore

### Delete Role
1. Click **🗑️ Delete**
2. ⚠️ **Can only delete if NO users assigned**
3. Remove users from role first, then delete

---

## 🧪 Testing Role Access

### Test Scenario 1: Warehouse Operative
```bash
# 1. Create role with basic functions
# 2. Assign to test user
# 3. Login as that user
# Expected: Can view/check-in, cannot delete
```

### Test Scenario 2: Forklift Driver
```bash
# 1. Create role with tipping functions only
# 2. Assign to test user
# 3. Login as that user
# Expected: Can update tipping, cannot create bookings
```

### Test Scenario 3: Viewer
```bash
# 1. Create role with view/export only
# 2. Assign to test user
# 3. Login as that user
# Expected: Can see everything, cannot modify anything
```

---

## 🚨 Troubleshooting

### Problem: 404 Not Found
**Solution**: The route is `/app/custom-roles` or `/admin/custom-roles`
- Try: `http://test.test/app/custom-roles`
- Make sure you're logged in as admin

### Problem: Can't See "Create Predefined Roles" Button
**Solution**: Clear browser cache or check user permissions
- Must be logged in as admin
- Check if `admin` role is assigned

### Problem: User Has Role But Can't Access Functions
**Checklist**:
1. Is the role **Active**? (check role status)
2. Does the role have functions assigned? (edit role and check)
3. Is the user actually assigned to the role? (edit user and check)
4. Try logout and login again

### Problem: Functions Not Working
**Solution**: Function key might be misspelled
- Check exact function key name
- Compare with list in UserFunction.php
- Edit role and re-select functions

---

## 📊 Role vs Spatie Permission Roles

### Your System Has TWO Role Systems:

#### 1. Spatie Roles (Original)
- **Examples**: admin, depot-admin, site-admin, customer
- **Created by**: RoleSeeder (`php artisan db:seed --class=RoleSeeder`)
- **Used for**: High-level system access (admin vs customer)
- **Assigned to**: User model with `->assignRole('admin')`

#### 2. Custom Roles (New - Function-Based)
- **Examples**: warehouse_operative, forklift_driver, yard_controller
- **Created by**: CustomRole model or UI
- **Used for**: Granular function access control
- **Assigned to**: user_custom_roles pivot table

### How They Work Together:
1. **Spatie Role** controls which interface user sees (admin/warehouse/customer portal)
2. **Custom Role** controls what functions they can perform within that interface

### Example User Setup:
```
User: Emma Davis
├── Spatie Role: "site-admin" (can access warehouse interface)
└── Custom Role: "warehouse_operative" (can check-in, assign bays, but not delete)
```

---

## 📚 Quick Reference

| Task | URL | Notes |
|------|-----|-------|
| **View Roles** | `/app/custom-roles` | List all custom roles |
| **Create Predefined** | Click button in UI | Creates 7 standard roles |
| **Create Custom** | `/app/custom-roles/create` | Build your own role |
| **Edit Role** | Click ✏️ Edit | Change functions anytime |
| **Assign to User** | Admin → Users → Edit | Select from dropdown |

---

## 🚀 Next Steps

1. ✅ Create predefined roles (one-click)
2. ✅ Assign roles to test users
3. ✅ Login as different roles to test access
4. ✅ Create custom roles for your specific needs
5. ✅ Adjust function assignments as needed

**Pro Tip**: Start with predefined roles, then customize them based on your actual workflow!

---

**Last Updated:** 2026-02-16
**System:** Custom function-based role management with dynamic permissions
