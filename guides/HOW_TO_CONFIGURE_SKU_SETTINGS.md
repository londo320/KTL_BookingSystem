# How to Configure SKU Settings for Customers

## 🎯 Quick Access

**To configure SKU/PO settings for a customer:**

1. Go to **Customers** page in admin
2. Find the customer you want to configure
3. Click the **📦 Booking Config** button

**Direct URL:** `/app/customers/{customer_id}/booking-config`

## 📋 What You Can Configure

### Global Settings (Apply to All Depots)
- **Show SKU/Product Fields:** Show or hide the entire PO/SKU section
- **Require PO Data:** Make PO numbers mandatory or optional

### Depot-Specific Overrides
- Override global settings for specific depots
- Leave dropdown as "Use Global Setting" to inherit from global config

## 🔧 Configuration Options

### Option 1: Show SKU Fields
- ✅ **Enabled** - Full PO/SKU section visible in booking form
- ❌ **Disabled** - PO/SKU section completely hidden

### Option 2: Require PO Data
- ✅ **Required** - User must provide PO numbers when creating booking
- ⚪ **Optional** - PO numbers are optional, booking can be created without them

## 💡 Common Scenarios

### Scenario 1: Disable SKU for All Depots
1. Go to customer's Booking Config page
2. Set **Global Settings:**
   - Show SKU Fields: **Disabled**
   - Require PO Data: **Optional**
3. Leave all depot-specific settings as "Use Global Setting"
4. Click **Save Configuration**

**Result:** Customer won't see SKU fields at any depot

### Scenario 2: Enable at One Depot Only
1. Go to customer's Booking Config page
2. Set **Global Settings:**
   - Show SKU Fields: **Disabled**
   - Require PO Data: **Optional**
3. For the specific depot you want to enable:
   - Show SKU Fields: **Enabled**
   - Require PO Data: **Required**
4. Click **Save Configuration**

**Result:** Customer sees SKU fields only at the specified depot

### Scenario 3: Disable at One Depot Only
1. Go to customer's Booking Config page
2. Set **Global Settings:**
   - Show SKU Fields: **Enabled**
   - Require PO Data: **Required**
3. For the specific depot you want to disable:
   - Show SKU Fields: **Disabled**
   - Require PO Data: **Optional**
4. Click **Save Configuration**

**Result:** Customer sees SKU fields at all depots except the specified one

## 📸 Visual Guide

### Step 1: Customer List
```
┌─────────────────────────────────────────────────┐
│ Customers Management                  + New     │
├─────────────────────────────────────────────────┤
│ Name        Email           Actions             │
│ ACME Corp   acme@...        [Edit]              │
│                             [📦 Booking Config]  │ ← Click here
│                             [🔧 Limits]          │
└─────────────────────────────────────────────────┘
```

### Step 2: Configuration Page
```
┌─────────────────────────────────────────────────┐
│ Booking Configuration                           │
│ Configure SKU/PO requirements for ACME Corp     │
├─────────────────────────────────────────────────┤
│ 🌐 Global Settings (All Depots)                 │
│                                                  │
│ Show SKU/Product Fields:                        │
│  ○ Enabled - Show SKU/PO fields                │
│  ● Disabled - Hide SKU/PO fields               │
│                                                  │
│ Require PO Data:                                │
│  ○ Required - PO data must be provided         │
│  ● Optional - PO data is optional              │
├─────────────────────────────────────────────────┤
│ 🏭 Depot-Specific Overrides                     │
│                                                  │
│ Depot A                                         │
│  Show SKU: [Use Global Setting ▼]              │
│  Require PO: [Use Global Setting ▼]            │
│                                                  │
│ Depot B                                         │
│  Show SKU: [Enabled ▼]                         │
│  Require PO: [Required ▼]                      │
└─────────────────────────────────────────────────┘
         [Cancel]  [💾 Save Configuration]
```

## 🧪 Testing Your Configuration

After saving, test by:

1. Go to **Create Booking** page
2. Select the configured customer
3. Select a depot
4. Check if PO/SKU section appears or is hidden

**Expected Behavior:**
- If **Disabled**: You'll see a gray notice saying "SKU/Product configuration is not required"
- If **Enabled**: You'll see the full PO/SKU section with all fields

## 📝 Notes

- Changes take effect immediately
- Existing bookings are not affected
- Default behavior (if no config exists): All fields enabled and required
- You can change settings anytime without affecting existing bookings

## 🔄 Configuration Priority

1. **Depot-specific** setting (if set)
2. **Global** setting (if depot-specific not set)
3. **Default** (if no configuration exists) - All enabled

## 🆘 Troubleshooting

**Problem:** Config page doesn't save
- Check you have admin/site-admin role
- Check all required fields are selected

**Problem:** Form still shows SKU fields
- Clear browser cache
- Check depot-specific override isn't enabled
- Verify global setting is set to "Disabled"

**Problem:** Can't find Booking Config button
- Check you're on the Customers list page: `/app/customers`
- Check you have admin access
- The button appears in the "Actions" column

## 📧 Support

If you need help configuring customers, check:
- `IMPLEMENTATION_GUIDE.md` - Full technical guide
- `TASK1_IMPLEMENTATION_SUMMARY.md` - Implementation details
