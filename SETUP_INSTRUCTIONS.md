# Quick Setup Instructions

## 🚀 Set Up Test Users & Roles

### Step 1: Run Database Seeders
```bash
# Create all roles (including custom ones like forklift-driver, yard-controller, etc.)
php artisan db:seed --class=RoleSeeder

# Create 16 test user profiles across all roles
php artisan db:seed --class=TestUserProfilesSeeder
```

### Step 2: Login and Test
All test users have the password: **`password`**

**Try these accounts:**
- **Admin**: `admin@ktl.com`
- **Warehouse Operative**: `warehouse.op1@ktl.com`
- **Forklift Driver**: `forklift1@ktl.com`
- **Customer**: `jane.cooper@abclogistics.com`
- **Security**: `security1@ktl.com`

### Step 3: Access URLs
- **Admin Panel**: http://test.test/admin/login
- **Warehouse**: http://test.test/warehouse/login
- **Customer Portal**: http://test.test/customer/login

---

## 📚 Documentation Files Created

1. **USER_PROFILES_AND_ACCESS.md** - Complete guide to all roles and access levels
2. **SIMPLE_TEST_GUIDE.md** - Step-by-step testing instructions
3. **BOOKING_TEST_PLAN.md** - Comprehensive technical test scenarios
4. **QUICK_REFERENCE.md** - Quick commands and troubleshooting

---

## ✅ What's Included

### 16 Test Users Created:
1. System Administrator (admin)
2. Depot Admin
3. Site Admin
4. Warehouse Manager
5. Warehouse Operative #1 (Day)
6. Warehouse Operative #2 (Night)
7. Forklift Driver #1 (Main)
8. Forklift Driver #2 (Main)
9. Forklift Driver #3 (North)
10. Yard Controller
11. Gate Security #1
12. Gate Security #2
13. Customer Admin (ABC Logistics)
14. Customer User (ABC Logistics)
15. Customer Admin (XYZ Transport)
16. Customer User (XYZ Transport)
17. Viewer (Read-only)

### 11 Roles Created:
- admin
- depot-admin
- site-admin
- warehouse-manager
- warehouse-operative
- forklift-driver
- yard-controller
- gate-security
- customer
- customer-admin
- viewer

---

## 🔧 Custom Roles

You can create additional custom roles via your admin interface:
1. Login as admin
2. Go to **Configuration → Role Management**
3. Create custom role
4. Assign specific functions/permissions
5. Assign to users

---

## 🎯 Next Steps

1. ✅ Run the seeders (above)
2. ✅ Login as different users to test access
3. ✅ Check the documentation files for detailed guides
4. ✅ Create custom roles if needed via admin panel

---

**Need Help?** Check USER_PROFILES_AND_ACCESS.md for complete details!
