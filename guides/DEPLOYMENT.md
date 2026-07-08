# Production Deployment Guide

## Overview
This Laravel booking system includes a comprehensive migration system with proper dependency management and essential seeders for production deployment.

## Quick Start for Production

### 1. Database Setup
```bash
# Create fresh database
mysql -u root -p -e "CREATE DATABASE bookings_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Update .env file
DB_DATABASE=bookings_prod
```

### 2. Run Deployment Script
```bash
./deploy-migrations.sh
```

This script will:
- ✅ Run all migrations in correct dependency order
- ✅ Handle existing tables gracefully
- ✅ Seed essential data (roles, users, depots, trailer types, etc.)
- ✅ Create default admin user

### 3. Default Login Credentials
- **Email**: admin@example.com
- **Password**: password

**⚠️ IMPORTANT**: Change the default admin password immediately after first login!

## What Gets Seeded

### Essential Data
- **Roles**: admin, depot-admin, site-admin, customer
- **Users**: 4 test users including admin
- **Depots**: 4 sample depots
- **Booking Types**: Palletised, Container, Bulk
- **Trailer Types**: 9 types (Curtain Sided, Flatbed, Box Trailer, Rigid, etc.)
- **Arrival Time Settings**: Global default (0 minutes tolerance - exact timing)

### Key Features Ready
- ✅ Carrier name normalization (title case)
- ✅ Vehicle registration uppercase enforcement  
- ✅ Database-driven trailer types with soft delete protection
- ✅ Configurable arrival time rules (global/depot/customer hierarchy)
- ✅ Customer behavior analytics with arrival tracking
- ✅ Enhanced admin interface with modern styling

## Migration Order (22 Steps)

The deployment script ensures migrations run in proper dependency order:

1. **Core Foundation**: depots, users, customers, booking_types
2. **System Tables**: sessions, cache, password_resets  
3. **Permission System**: roles and permissions tables
4. **Core Business**: slots, bookings
5. **Carrier System**: carriers with depot relationships
6. **Relationship Tables**: depot_user, customer_user pivots
7. **Soft Deletes**: deleted_at columns for all models
8. **User Relationships**: customer_id associations
9. **Products & Config**: products, depot configurations
10. **Booking Enhancements**: case, size, status, timing fields
11. **Slot System**: templates, capacity, generation settings
12. **Slot Release**: rules and customer assignments
13. **Transportation**: vehicle registration, trailer details
14. **Trailer Types**: new database-driven system
15. **Booking History**: tracking and behavior analytics
16. **Arrival Time Settings**: configurable timing rules ⭐
17. **Tipping Workflow**: locations, bays, workflow management
18. **Vehicle System**: vehicles, trailers, movements, consignments
19. **PO System**: purchase orders and pallet management
20. **Vehicle Details**: enhanced vehicle information
21. **Cleanup**: remove legacy fields and optimize
22. **Constraints**: foreign key relationships

## Troubleshooting

### Common Issues

**Issue**: "Table already exists" errors
**Solution**: The script handles this gracefully - these warnings are normal

**Issue**: Foreign key constraint errors  
**Solution**: Ensure parent tables exist first - the script runs in dependency order

**Issue**: Missing columns in queries
**Solution**: Run the full deployment script to ensure all migrations complete

### Manual Migration (If Needed)
```bash
# If deployment script fails, run step by step:
php artisan migrate --step
php artisan db:seed
```

### Reset Database (Development Only)
```bash
php artisan db:wipe
./deploy-migrations.sh
```

## Post-Deployment Checklist

- [ ] Change default admin password
- [ ] Configure mail settings for notifications
- [ ] Set up proper backup schedule
- [ ] Configure arrival time rules for each depot
- [ ] Import real customer and carrier data
- [ ] Test booking workflow end-to-end
- [ ] Verify customer behavior analytics
- [ ] Test arrival time tracking

## System Architecture

### Key Models & Relationships
- **Users** → belong to Depots, have Roles
- **Customers** → have many Users, track behavior
- **Bookings** → belong to Customers, Slots, TrailerTypes
- **TrailerTypes** → soft deletable, protect historical data
- **ArrivalTimeSettings** → hierarchical (global → depot → customer)
- **BookingHistory** → tracks all changes and arrivals

### Core Features
- **Input Normalization**: Automatic carrier name title case, vehicle registration uppercase
- **Configurable Timing**: Flexible arrival tolerance rules
- **Behavior Analytics**: Customer risk assessment with arrival tracking
- **Audit Trail**: Complete booking history with context
- **Soft Deletes**: Protect historical data integrity

## Support
For issues or questions about deployment, check the Laravel logs and ensure all environment variables are properly configured.