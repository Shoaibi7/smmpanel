# SMM Panel - API Management System - Quick Start Guide

## Overview
Complete implementation of API Providers & Services Management system with:
- ✅ Database migrations (api_providers table + services table updates)
- ✅ Models with relationships, scopes, and methods
- ✅ API integration service with Guzzle HTTP
- ✅ Form request validations
- ✅ Admin controllers with CRUD operations
- ✅ Admin panel views with Blade templates
- ✅ Routes configuration
- ✅ Error handling and logging
- ✅ Caching system

## Files Created/Modified

### Migrations
- `database/migrations/2026_02_10_150000_create_api_providers_table.php` (NEW)
- `database/migrations/2026_02_10_150001_update_services_table.php` (NEW)

### Models
- `app/Models/ApiProvider.php` (NEW)
- `app/Models/Service.php` (UPDATED)

### Services
- `app/Services/ApiProviderService.php` (NEW)

### Http Requests
- `app/Http/Requests/ApiProviderRequest.php` (NEW)
- `app/Http/Requests/ServiceRequest.php` (NEW)

### Controllers
- `app/Http/Controllers/Admin/ApiProviderController.php` (NEW)
- `app/Http/Controllers/Admin/ServiceController.php` (NEW)

### Views
- `resources/views/layouts/admin.blade.php` (NEW)
- `resources/views/admin/api-providers/index.blade.php` (NEW)
- `resources/views/admin/api-providers/create.blade.php` (NEW)
- `resources/views/admin/api-providers/edit.blade.php` (NEW)
- `resources/views/admin/api-providers/show.blade.php` (NEW)
- `resources/views/admin/services/index.blade.php` (NEW)
- `resources/views/admin/services/create.blade.php` (NEW)
- `resources/views/admin/services/edit.blade.php` (NEW)
- `resources/views/admin/services/show.blade.php` (NEW)
- `resources/views/admin/services/bulk-import.blade.php` (NEW)

### Routes
- `routes/web.php` (UPDATED)

## Installation Steps

### Step 1: Run Database Migrations
```bash
php artisan migrate
```

Expected output:
```
Migrating: 2026_02_10_150000_create_api_providers_table
Migrated:  2026_02_10_150000_create_api_providers_table (XXXms)
Migrating: 2026_02_10_150001_update_services_table
Migrated:  2026_02_10_150001_update_services_table (XXXms)
```

### Step 2: Verify Guzzle HTTP is Installed
```bash
composer require guzzlehttp/guzzle
```

### Step 3: Clear Cache (Recommended)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Testing the Implementation

### 1. Access Admin Dashboard
Navigate to your admin panel:
```
http://your-domain.com/admin/api-providers
http://your-domain.com/admin/services
```

### 2. Add Your First API Provider
1. Go to `/admin/api-providers`
2. Click "Add New Provider"
3. Fill in the form:
   - **Name:** Test Provider (e.g., "SocialPanel")
   - **Short Name:** test-provider (auto-generated from name)
   - **API URL:** https://api.example.com/api/v2
   - **API Key:** Your provider's API key
   - **Status:** Enabled
4. Submit the form

### 3. Test the Connection
On the provider detail page, click "Test Connection" button. You should see a success message if credentials are valid.

### 4. Sync Services
Click "Sync Services" to import all available services from the provider's API.

### 5. Manage Services
- View all synced services in `/admin/services`
- Edit individual services
- Enable/disable multiple services
- Search and filter by various criteria

## API Response Format

Your API provider should respond in the following formats:

### Balance Endpoint
Request: `POST /api/v2` with `action=balance&key=YOUR_KEY`

Response:
```json
{
  "balance": "150.50",
  "currency": "USD"
}
```

### Services Endpoint
Request: `POST /api/v2` with `action=services&key=YOUR_KEY`

Response:
```json
[
  {
    "service": "1001",
    "name": "Instagram Followers",
    "type": "Default",
    "category": "Instagram",
    "rate": "0.50",
    "min": "10",
    "max": "100000",
    "dripfeed": false,
    "refill": false,
    "cancel": true
  },
  {
    "service": "1002",
    "name": "Instagram Likes",
    "type": "Default",
    "category": "Instagram",
    "rate": "0.02",
    "min": "10",
    "max": "50000",
    "dripfeed": true,
    "refill": true,
    "cancel": false
  }
]
```

## Key Features Overview

### API Providers
- ✅ Add/Edit/Delete providers
- ✅ Test connection before saving
- ✅ Sync balance from API
- ✅ Sync all services from API
- ✅ Enable/disable providers
- ✅ Soft delete with restore option
- ✅ View provider details and associated services

### Services
- ✅ Manual service creation
- ✅ Bulk import from providers
- ✅ Advanced filtering (provider, category, status, type)
- ✅ Search functionality
- ✅ Bulk enable/disable operations
- ✅ Sync individual service details
- ✅ Edit service information
- ✅ Support for service features (dripfeed, refill, cancel)

### Admin Interface
- ✅ Responsive Tailwind CSS design
- ✅ Dark mode support
- ✅ DataTable pagination
- ✅ AJAX operations with loading spinner
- ✅ Toast notifications
- ✅ Confirmation modals for destructive actions
- ✅ Real-time status updates

## Troubleshooting

### Issue: Migrations fail
**Solution:**
- Ensure database is empty or has existing tables
- Check database connection in `.env`
- Run `php artisan migrate:rollback --step=2` to undo and retry

### Issue: API connection test fails
**Solution:**
- Verify API URL format (must start with http:// or https://)
- Check API key is correct and not expired
- Ensure provider's API is accessible
- Check firewall/network restrictions
- View `storage/logs/laravel.log` for detailed errors

### Issue: Services not importing
**Solution:**
- First, run "Test Connection" to verify credentials
- Check that provider's API returns services in expected format
- Verify API response includes required fields: service, name, rate, min, max
- Check `storage/logs/laravel.log` for API response errors

### Issue: Database relationships not working
**Solution:**
- Ensure all migrations have run: `php artisan migrate:status`
- Check foreign key constraints are properly created
- Verify model relationships are correctly defined

## Database Structure

### api_providers Table
```
id (bigint, PK)
api_name (varchar 255)
short_name (varchar 100, UNIQUE)
api_url (varchar 500)
api_key (text)
balance (decimal 12,2)
services_count (integer)
status (enum: enabled, disabled)
last_sync_at (timestamp, nullable)
created_at (timestamp)
updated_at (timestamp)
deleted_at (timestamp, nullable)
```

### services Table (Updated)
```
NEW COLUMNS:
api_provider_id (bigint FK)
api_service_id (varchar 100)
type (enum: 13 types)
rate (decimal 10,4)
refill (boolean)
cancel (boolean)
status (enum: active, inactive)
```

## Performance Notes

- Balance data cached for 5 minutes
- Services list cached for 1 hour
- Database indexes on api_providers.status and api_providers.short_name
- Database indexes on services.api_provider_id and services.api_service_id
- Efficient pagination (20 items per page)
- Bulk operations update multiple records in single query

## Security Considerations

✅ CSRF protection on all forms
✅ Admin authentication required
✅ API keys encrypted in transit
✅ Form request validation
✅ SQL injection prevention (ORM usage)
✅ Authorization checks
✅ Soft deletes preserve data
✅ Error messages don't expose sensitive info
✅ HTTP timeouts (30 seconds) prevent hanging

## Next Steps

1. **Test with Real API Provider:**
   - Use a live SMM API provider's credentials
   - Test balance sync and service import
   - Verify service details are correctly imported

2. **Customize as Needed:**
   - Adjust caching durations in ApiProviderService
   - Modify service types enum in migration
   - Add custom service fields as needed
   - Implement markup percentage logic

3. **Add Frontend Integration:**
   - Create service display pages
   - Implement order placement
   - Show available services to users

4. **Setup Background Jobs (Optional):**
   - Queue service syncs
   - Schedule automatic balance updates
   - Implement notifications for low balance

## Support

For issues or questions:
1. Check `storage/logs/laravel.log` for detailed error information
2. Verify all migrations ran successfully using `php artisan migrate:status`
3. Test API provider credentials carefully
4. Review the IMPLEMENTATION_GUIDE.md for detailed documentation

## Summary

- ✅ All features implemented as specified
- ✅ All CRUD operations working
- ✅ Database properly structured with relationships
- ✅ Admin interface fully functional
- ✅ Error handling and logging in place
- ✅ Ready for production testing

**Status:** Implementation Complete ✓
**Date:** February 10, 2026
