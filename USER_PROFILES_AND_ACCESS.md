# KTL Booking System - User Profiles & Access Control

## 🎯 Overview

The system uses **Custom Function-Based Access Control** allowing you to create roles and assign specific functions (permissions) to each role dynamically through the admin interface.

---

## 📋 Test User Accounts

All test users have the password: **`password`**

### System Administrators

| Name | Email | Role | Access Level |
|------|-------|------|-------------|
| System Administrator | `admin@ktl.com` | **admin** | Full system access |
| Sarah Johnson | `depot.admin@ktl.com` | **depot-admin** | Depot management |
| Mike Williams | `site.admin@ktl.com` | **site-admin** | Site operations |

### Warehouse Staff

| Name | Email | Role | Primary Function |
|------|-------|------|-----------------|
| David Thompson | `warehouse.manager@ktl.com` | **warehouse-manager** | Oversees warehouse operations |
| Emma Davis | `warehouse.op1@ktl.com` | **warehouse-operative** | Day shift operations |
| James Wilson | `warehouse.op2@ktl.com` | **warehouse-operative** | Night shift operations |

### Equipment Operators

| Name | Email | Role | Location |
|------|-------|------|----------|
| Tom Brown | `forklift1@ktl.com` | **forklift-driver** | Main Depot |
| Lucy Martinez | `forklift2@ktl.com` | **forklift-driver** | Main Depot |
| Chris Anderson | `forklift3@ktl.com` | **forklift-driver** | North Depot |

### Yard Management

| Name | Email | Role | Responsibility |
|------|-------|------|---------------|
| Kevin Moore | `yard.controller@ktl.com` | **yard-controller** | Vehicle movement control |

### Security Personnel

| Name | Email | Role | Location |
|------|-------|------|----------|
| Robert Taylor | `security1@ktl.com` | **gate-security** | Main Gate |
| Michelle Garcia | `security2@ktl.com` | **gate-security** | North Gate |

### Customers

| Name | Email | Role | Company |
|------|-------|------|---------|
| John Smith | `john.smith@abclogistics.com` | **customer-admin** | ABC Logistics Ltd |
| Jane Cooper | `jane.cooper@abclogistics.com` | **customer** | ABC Logistics Ltd |
| Peter Walsh | `peter@xyztransport.com` | **customer-admin** | XYZ Transport |
| Lisa White | `lisa@xyztransport.com` | **customer** | XYZ Transport |

### Viewers

| Name | Email | Role | Purpose |
|------|-------|------|---------|
| Manager Review | `viewer@ktl.com` | **viewer** | Read-only management oversight |

---

## 🔑 Role Descriptions & Menu Access

### 1. **Admin** (System Administrator)
**Full access to everything**

#### Menu Access:
- ✅ Dashboard
- ✅ **Bookings** (All)
  - View, Create, Edit, Delete, Rebook, Cancel
  - History, Reports
- ✅ **Operations**
  - On-Site Bookings
  - Factory Bookings
  - Warehouse Workflow
  - Bay Management
- ✅ **Management**
  - Customers
  - Carriers
  - Depots
  - Vehicles
  - Trailers
- ✅ **Configuration**
  - Users & Roles
  - Permissions (Function Management)
  - System Settings
  - Booking Types
  - Equipment Types
- ✅ **Inbound Module**
  - Slot Management
  - Slot Templates
  - Slot Release Rules
  - Bay Capacity Rules
- ✅ **Outbound Module**
  - Order Management
  - Dispatch
- ✅ **Reports**
  - All reports

**Use Case:** System setup, user management, troubleshooting

---

### 2. **Depot Admin**
**Manages specific depot operations**

#### Menu Access:
- ✅ Dashboard (Depot-specific)
- ✅ **Bookings**
  - View, Create, Edit, Cancel (own depot)
  - Rebook, History
- ✅ **Operations**
  - Bay Assignment
  - Vehicle Check-in
  - Warehouse Workflow
- ✅ **Management**
  - Customers (view/edit)
  - Carriers (view)
  - Vehicles (own depot)
- ✅ **Configuration**
  - Bay Settings
  - Slot Templates (own depot)
  - Time Windows
- ✅ **Reports**
  - Depot-specific reports

**Use Case:** Day-to-day depot management, booking oversight

---

### 3. **Site Admin**
**Similar to Depot Admin but site-focused**

#### Menu Access:
- Similar to Depot Admin
- Focus on site operations rather than administrative tasks

**Use Case:** On-site operational management

---

### 4. **Warehouse Manager**
**Oversees warehouse operations and staff**

#### Menu Access:
- ✅ Dashboard (Warehouse)
- ✅ **Bookings**
  - View all bookings
  - Update booking status
  - Assign bays
- ✅ **Operations**
  - Bay Management
  - Vehicle Workflow
  - On-Site Bookings
  - Factory Bookings
- ✅ **Management**
  - Staff assignments
  - Equipment allocation
- ✅ **Reports**
  - Warehouse performance
  - Bay utilization

**Use Case:** Coordinate warehouse activities, manage staff, optimize bay usage

---

### 5. **Warehouse Operative**
**Handles day-to-day warehouse tasks**

#### Menu Access:
- ✅ Dashboard (Simplified)
- ✅ **Bookings**
  - View today's bookings
  - Update arrival status
  - Mark departed
- ✅ **Operations**
  - Check-in vehicles
  - Assign to bays
  - Update tipping status
- ❌ Edit/Delete bookings
- ❌ Configuration
- ❌ Reports

**Use Case:** Process arriving vehicles, update statuses, manage day-to-day flow

---

### 6. **Forklift Driver**
**Equipment operator with limited system access**

#### Menu Access:
- ✅ Dashboard (Task list)
- ✅ **Operations**
  - View assigned tasks
  - Update tipping progress
  - Mark tipping complete
- ✅ **My Tasks**
  - Current bay assignment
  - Next vehicle in queue
- ❌ Booking management
- ❌ Bay assignment
- ❌ Reports

**Use Case:** See assigned work, update tipping progress, complete tasks

---

### 7. **Yard Controller**
**Manages vehicle movements in yard**

#### Menu Access:
- ✅ Dashboard (Yard overview)
- ✅ **Operations**
  - Vehicle locations
  - Bay availability
  - Parking assignments
  - Priority queue
- ✅ **Bookings**
  - View all arrivals
  - Update vehicle locations
  - Assign parking/bays
- ✅ **Reports**
  - Yard utilization
  - Vehicle dwell time

**Use Case:** Direct traffic, manage parking, optimize yard space

---

### 8. **Gate Security**
**Vehicle check-in at gate**

#### Menu Access:
- ✅ Dashboard (Arrivals)
- ✅ **Check-in**
  - Verify booking
  - Record arrival time
  - Check documents
  - Issue gate pass
- ✅ **View Bookings**
  - Today's expected arrivals
  - Booking details (read-only)
- ❌ Edit bookings
- ❌ Bay management
- ❌ Reports

**Use Case:** Check vehicles in/out, verify bookings, security checks

---

### 9. **Customer Admin**
**Company admin - can manage users and bookings**

#### Menu Access:
- ✅ **Customer Dashboard**
- ✅ **My Bookings**
  - View all company bookings
  - Create new bookings
  - Edit bookings (before cutoff)
  - Cancel bookings
- ✅ **Book Delivery**
  - Available slots
  - Equipment selection
  - Special instructions
- ✅ **Company Users**
  - Invite users
  - Manage permissions
  - View user activity
- ✅ **Reports**
  - Company booking history
  - Usage statistics
- ❌ Other companies' data
- ❌ System configuration

**Use Case:** Manage company bookings, add users, monitor delivery schedule

---

### 10. **Customer** (Standard)
**Basic customer user**

#### Menu Access:
- ✅ **Customer Dashboard**
- ✅ **My Bookings**
  - View own bookings
  - Create new bookings
  - Edit own bookings (before cutoff)
  - Cancel own bookings
- ✅ **Book Delivery**
  - Available slots
  - Select time/bay
- ❌ Manage users
- ❌ View other users' bookings
- ❌ Reports

**Use Case:** Book deliveries, track own bookings

---

### 11. **Viewer**
**Read-only access for management oversight**

#### Menu Access:
- ✅ View-only access to:
  - Dashboard
  - All bookings
  - Reports
  - Bay status
  - Vehicle locations
- ❌ Cannot create/edit/delete anything

**Use Case:** Management oversight, monitoring, reporting

---

## 🛠️ Function-Based Access Control

The system uses **custom functions** that you can assign to roles dynamically.

### Available Functions (Examples):

| Function | Description |
|----------|-------------|
| `dashboard.view` | View dashboard |
| `dashboard.warehouse` | View warehouse dashboard |
| `bookings.view` | View bookings |
| `bookings.create` | Create new bookings |
| `bookings.edit` | Edit existing bookings |
| `bookings.delete` | Delete bookings |
| `bookings.cancel` | Cancel bookings |
| `bookings.rebook` | Rebook cancelled bookings |
| `bookings.history` | View booking history |
| `customers.view` | View customers |
| `customers.create` | Create customers |
| `customers.edit` | Edit customers |
| `customers.delete` | Delete customers |
| `bays.view` | View tipping bays |
| `bays.assign` | Assign vehicles to bays |
| `bays.manage` | Manage bay settings |
| `slots.view` | View time slots |
| `slots.create` | Create time slots |
| `slots.edit` | Edit/block slots |
| `slots.delete` | Delete slots |
| `users.view` | View users |
| `users.create` | Create users |
| `users.edit` | Edit users |
| `users.delete` | Delete users |
| `reports.view` | View reports |
| `factory-bookings.view` | View factory bookings |
| `factory-bookings.create` | Create factory bookings |

### How to Manage Functions:

1. **Admin → Configuration → Role Management**
2. Create or edit a role
3. Assign functions to that role
4. Users with that role automatically get those permissions

---

## 📥 How to Create Test Users

### Option 1: Run the Seeder
```bash
# Run role seeder first
php artisan db:seed --class=RoleSeeder

# Then run user profiles seeder
php artisan db:seed --class=TestUserProfilesSeeder
```

### Option 2: Via Admin Interface
1. Login as admin (`admin@ktl.com`)
2. Go to **Admin → Users → Create User**
3. Fill in details
4. Assign role
5. Assign functions (or let role handle it)

---

## 🧪 Testing Access Control

### Test Scenario 1: Warehouse Operative
```bash
# Login as: warehouse.op1@ktl.com / password
# Expected:
# ✅ Can see today's bookings
# ✅ Can update arrival status
# ✅ Can assign to bay
# ❌ Cannot delete bookings
# ❌ Cannot access configuration
```

### Test Scenario 2: Forklift Driver
```bash
# Login as: forklift1@ktl.com / password
# Expected:
# ✅ Can see assigned tasks
# ✅ Can update tipping status
# ❌ Cannot create bookings
# ❌ Cannot assign bays
```

### Test Scenario 3: Customer
```bash
# Login as: jane.cooper@abclogistics.com / password
# Expected:
# ✅ Can book delivery slots
# ✅ Can view own bookings
# ❌ Cannot see other companies
# ❌ Cannot access admin area
```

### Test Scenario 4: Gate Security
```bash
# Login as: security1@ktl.com / password
# Expected:
# ✅ Can check in vehicles
# ✅ Can view today's arrivals
# ❌ Cannot edit bookings
# ❌ Cannot access warehouse area
```

---

## 🔐 Custom Role Creation

**Note:** You mentioned you created a user-defined role creator. To use it:

1. **Admin → Configuration → Roles**
2. Click **"Create New Role"**
3. Enter role details:
   - Name (e.g., "shift-supervisor")
   - Description
   - Guard (usually "web")
4. **Assign Functions**:
   - Check boxes for required functions
   - Or use "Select All" for specific categories
5. **Save**
6. **Assign to Users**:
   - Go to Users
   - Edit user
   - Select the new custom role

### Example Custom Roles:

- **Shift Supervisor**: Warehouse operative + some management functions
- **Quality Controller**: View-only + specific quality check functions
- **Compliance Officer**: Read-only + audit trail access
- **Night Shift Manager**: Limited admin access for overnight operations

---

## 📊 Access Matrix

| Feature | Admin | Depot Admin | Warehouse Manager | Warehouse Op | Forklift | Yard Controller | Security | Customer Admin | Customer | Viewer |
|---------|-------|-------------|-------------------|--------------|----------|-----------------|----------|----------------|----------|--------|
| **Dashboard** | ✅ All | ✅ Depot | ✅ Warehouse | ✅ Simple | ✅ Tasks | ✅ Yard | ✅ Gate | ✅ Customer | ✅ Customer | ✅ Read |
| **Create Bookings** | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ |
| **Edit Bookings** | ✅ | ✅ | ✅ | ⚠️ Limited | ❌ | ⚠️ Status | ❌ | ✅ Own | ✅ Own | ❌ |
| **Delete Bookings** | ✅ | ✅ | ⚠️ Limited | ❌ | ❌ | ❌ | ❌ | ⚠️ Limited | ❌ | ❌ |
| **Bay Assignment** | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Tipping Status** | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ View | ⚠️ View | ❌ | ❌ | ⚠️ View |
| **User Management** | ✅ | ⚠️ Limited | ❌ | ❌ | ❌ | ❌ | ❌ | ⚠️ Company | ❌ | ❌ |
| **Slot Management** | ✅ | ✅ | ⚠️ View | ⚠️ View | ❌ | ⚠️ View | ❌ | ⚠️ View | ⚠️ View | ⚠️ View |
| **Reports** | ✅ All | ✅ Depot | ✅ Warehouse | ❌ | ❌ | ✅ Yard | ❌ | ✅ Company | ❌ | ✅ All |
| **Configuration** | ✅ | ⚠️ Limited | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

Legend:
- ✅ Full Access
- ⚠️ Limited/Conditional Access
- ❌ No Access

---

## 🚀 Quick Start Guide

### For Testing:
```bash
# 1. Seed roles
php artisan db:seed --class=RoleSeeder

# 2. Seed test users
php artisan db:seed --class=TestUserProfilesSeeder

# 3. Login and test
# All passwords: password
```

### URLs to Test:
- **Admin**: `http://test.test/admin/login`
- **Warehouse**: `http://test.test/warehouse/login`
- **Customer**: `http://test.test/customer/login`

---

**Last Updated:** 2026-02-16
**System:** Custom function-based role & permission system with Spatie Laravel Permission
