# SMM Panel - API Providers & Services Management System

## Implementation Complete ✓

This document describes the comprehensive API Providers & Services Management system that has been implemented for your Laravel SMM Panel.

## Features Implemented

### 1. API Providers Management

#### Database Schema
- **Table:** `api_providers`
- **Fields:** api_name, short_name, api_url, api_key, balance, services_count, status, last_sync_at, timestamps, soft deletes

#### Model: `App\Models\ApiProvider`
**Features:**
- Relationships: `hasMany(Service)`
- Scopes: `active()`, `inactive()`, `recent()`
- Methods:
  - `testConnection()` - Verify API credentials
  - `syncBalance()` - Fetch and update balance
  - `fetchServices()` - Import services from API
- Accessors: `formatted_balance`
- Soft delete support

#### Controller: `App\Http\Controllers\Admin\ApiProviderController`
**Actions:**
- `index()` - List all providers with search/filter
- `create()` - Show creation form
- `store()` - Save new provider
- `show()` - Display provider details and services
- `edit()` - Show edit form
- `update()` - Update provider
- `destroy()` - Soft delete provider
- `toggle()` - Enable/disable provider (AJAX)
- `syncBalance()` - Sync balance from API (AJAX)
- `syncServices()` - Import services (AJAX)
- `testConnection()` - Test API connection (AJAX)
- `restore()` - Restore soft-deleted provider

#### Views
1. **index.blade.php** - List view with DataTable, search, filters, status toggle
2. **create.blade.php** - Form to add new provider
3. **edit.blade.php** - Form to edit provider
4. **show.blade.php** - Provider details with associated services

#### Routes
```
GET    /admin/api-providers              - index
GET    /admin/api-providers/create       - create
POST   /admin/api-providers              - store
GET    /admin/api-providers/{id}         - show
GET    /admin/api-providers/{id}/edit    - edit
PUT    /admin/api-providers/{id}         - update
DELETE /admin/api-providers/{id}         - destroy
POST   /admin/api-providers/{id}/toggle  - toggle status
POST   /admin/api-providers/{id}/sync-balance - sync balance
POST   /admin/api-providers/{id}/sync-services - sync services
POST   /admin/api-providers/{id}/test-connection - test connection
POST   /admin/api-providers/{id}/restore - restore
```

---

### 2. Services Management

#### Database Schema Updates
- **New Table:** services table updated with:
  - api_provider_id (foreign key)
  - api_service_id (string)
  - type (enum with 13 service types)
  - rate (decimal 10,4)
  - refill (boolean)
  - cancel (boolean)
  - status (enum: active/inactive)

#### Model: `App\Models\Service`
**Features:**
- Relationships: `belongsTo(ApiProvider)`, `belongsTo(Category)`, `hasMany(Order)`
- Scopes: `active()`, `byProvider()`, `byCategory()`, `dripfeedEnabled()`
- Methods:
  - `calculateFinalPrice($markup)` - Calculate price with markup
  - `checkAvailability()` - Verify service is available
  - `syncFromProvider()` - Update from provider API
- Accessors: `formatted_rate`, `final_price`
- Support for both `price_per_k` and `rate` fields (backward compatible)

#### Controller: `App\Http\Controllers\Admin\ServiceController`
**Actions:**
- `index()` - List services with advanced filtering
- `create()` - Show creation form
- `store()` - Save new service
- `show()` - Display service details
- `edit()` - Show edit form
- `update()` - Update service
- `destroy()` - Delete service
- `bulkImport()` - Show bulk import form
- `bulkImportStore()` - Process bulk import
- `bulkEnable()` - Enable multiple services (AJAX)
- `bulkDisable()` - Disable multiple services (AJAX)
- `syncFromProvider()` - Update single service (AJAX)

#### Views
1. **index.blade.php** - List with checkboxes, bulk actions, advanced filters
2. **create.blade.php** - Service creation form
3. **edit.blade.php** - Service editing form
4. **show.blade.php** - Service details
5. **bulk-import.blade.php** - Bulk import interface with progress

#### Routes
```
GET    /admin/services              - index
GET    /admin/services/create       - create
POST   /admin/services              - store
GET    /admin/services/{id}         - show
GET    /admin/services/{id}/edit    - edit
PUT    /admin/services/{id}         - update
DELETE /admin/services/{id}         - destroy
GET    /admin/services/import/{provider} - bulkImport
POST   /admin/services/import-store - bulkImportStore
POST   /admin/services/bulk-enable  - bulkEnable
POST   /admin/services/bulk-disable - bulkDisable
POST   /admin/services/{id}/sync    - syncFromProvider
```

---

### 3. API Integration Layer

#### Service: `App\Services\ApiProviderService`
**Methods:**
- `testConnection()` - Test API credentials
- `getBalance()` - Fetch current balance
- `getServices()` - Get all services list
- `createOrder($serviceId, $link, $quantity)` - Place new order
- `checkOrderStatus($orderId)` - Get order status
- `checkMultipleOrderStatus($orderIds)` - Batch status check
- `createRefill($orderId)` - Request refill
- `checkRefillStatus($refillId)` - Check refill status
- `cancelOrder($orderId)` - Cancel order

**Features:**
- Guzzle HTTP client for API communication
- Automatic response normalization
- 30-second timeout configuration
- Caching: Balance (5 min), Services (1 hour)
- Comprehensive error handling with logging
- Proper exception handling

**Supported API Format:**
```
POST /api/v2
Parameters: action, key, and action-specific params
Response: JSON format with proper error handling
```

---

### 4. Validation

#### ApiProviderRequest Form Request
- api_name: required|string|max:255
- short_name: required|unique|max:100 (auto-slugified)
- api_url: required|url|max:500
- api_key: required|string|min:5
- status: required|in:enabled,disabled

#### ServiceRequest Form Request
- api_provider_id: required|exists
- category_id: required|exists
- name: required|string|max:500
- type: required|in:[all 13 types]
- rate: required|numeric|min:0
- min_order: required|integer|min:1
- max_order: required|integer|min:1
- status: required|in:active,inactive
- dripfeed, refill, cancel: nullable|boolean

---

### 5. Admin Layout & UI

#### Admin Layout: `resources/views/layouts/admin.blade.php`
**Features:**
- Responsive two-column layout (sidebar + main)
- Dark mode support
- Navigation with active states
- Alert system (success, error)
- Loading spinner
- AJAX utilities
- Toast notifications
- DataTables integration

#### Styling
- Tailwind CSS
- Custom brand colors
- Responsive design  
- Dark mode compatible
- Bootstrap 5 pagination

---

## Database Migrations

Two migrations have been created:

### Migration 1: Create API Providers Table
**File:** `2026_02_10_150000_create_api_providers_table.php`
- Creates api_providers table with all required fields
- Soft delete support
- Indexes on status and short_name

### Migration 2: Update Services Table
**File:** `2026_02_10_150001_update_services_table.php`
- Adds new columns to existing services table
- Preserves backward compatibility
- Adds foreign key to api_providers
- Adds indexes for performance

---

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Ensure Guzzle is Installed
```bash
composer require guzzlehttp/guzzle
```

### 3. Access the Admin Panel
```
/admin/api-providers - Manage API providers
/admin/services - Manage services
```

---

## Usage Examples

### Add New API Provider
1. Navigate to `/admin/api-providers`
2. Click "Add New Provider"
3. Fill in:
   - API Provider Name
   - Short Name (for reference)
   - API Base URL
   - API Key
   - Status (enabled/disabled)
4. Submit - Connection test runs automatically
5. On provider page, click "Sync Services" to import available services

### Import Services from API Provider
1. Go to API provider detail page
2. Click "Import Services" in the services section
3. Select category (optional)
4. Click "Start Import"
5. Services are fetched and stored in database

### Manage Services
1. Navigate to `/admin/services`
2. Use filters: Provider, Category, Status, Features, Date Range
3. Select multiple services and bulk enable/disable
4. Edit individual service details
5. Sync single service from provider

### Bulk Enable/Disable
1. Select services with checkboxes
2. Click "Enable Selected" or "Disable Selected"
3. All selected services are updated

---

## API Integration Standards

The system supports standard SMM API providers using the following format:

### Standard Endpoints
- **Balance:** `POST /api/v2?action=balance&key=YOUR_KEY`
- **Services:** `POST /api/v2?action=services&key=YOUR_KEY`
- **Add Order:** `POST /api/v2?action=add&key=KEY&service=ID&link=LINK&quantity=QTY`
- **Check Status:** `POST /api/v2?action=status&key=KEY&order=ID`
- **Create Refill:** `POST /api/v2?action=refill&key=KEY&order=ID`
- **Cancel Order:** `POST /api/v2?action=cancel&key=KEY&order=ID`

### Expected Response Format
```json
// Balance Response
{ "balance": "100.00", "currency": "USD" }

// Services Response (Array)
[
  {
    "service": "1",
    "name": "Service Name",
    "type": "Default",
    "category": "Category",
    "rate": "0.50",
    "min": "10",
    "max": "100000",
    "dripfeed": false,
    "refill": false,
    "cancel": true
  }
]

// Order Response
{ "order": "12345" }

// Status Response
{
  "charge": "0.50",
  "start_count": "1000",
  "status": "Completed",
  "remains": "0",
  "currency": "USD"
}
```

---

## Error Handling

All operations include comprehensive error handling:
- Try-catch blocks on all API calls
- User-friendly error messages
- Detailed logging in laravel.log
- AJAX error responses with status codes
- Timeout protection (30 seconds)
- Graceful fallback values

---

## Caching

Performance optimizations implemented:
- **Balance cache:** 5 minutes
- **Services cache:** 1 hour
- Cache invalidation on manual sync
- Automatic cache clearing on imports

---

## Security Features

- CSRF protection on all forms
- Authentication required (admin middleware)
- API keys encrypted in database
- Authorization checks via Form Requests
- SQL injection prevention via ORM
- User role validation
- Soft delete for data preservation

---

## Relationships

```
ApiProvider
  ├── hasMany Services
  └── hasMany Orders (through Services)

Service
  ├── belongsTo ApiProvider
  ├── belongsTo Category
  └── hasMany Orders

Category
  └── hasMany Services

Order
  └── belongsTo Service
  └── belongsTo User
```

---

## Key Files

### Models
- `app/Models/ApiProvider.php`
- `app/Models/Service.php` (updated)

### Controllers
- `app/Http/Controllers/Admin/ApiProviderController.php`
- `app/Http/Controllers/Admin/ServiceController.php`

### Requests
- `app/Http/Requests/ApiProviderRequest.php`
- `app/Http/Requests/ServiceRequest.php`

### Services
- `app/Services/ApiProviderService.php`

### Views
- `resources/views/layouts/admin.blade.php`
- `resources/views/admin/api-providers/*`
- `resources/views/admin/services/*`

### Routes
- `routes/web.php` (updated)

---

## Testing Checklist

- [ ] API provider CRUD operations work
- [ ] Connection test succeeds with valid credentials
- [ ] Balance sync fetches and updates correctly
- [ ] Services sync imports all available services
- [ ] Service bulk import works with category assignment
- [ ] Bulk enable/disable works on multiple services
- [ ] Sync individual service from provider
- [ ] Pagination works with all lists
- [ ] Search filters return correct results
- [ ] Status toggles update immediately
- [ ] Soft delete preserves data
- [ ] Error messages display for failed operations
- [ ] DataTables render correctly
- [ ] Dark mode works on all pages
- [ ] Mobile responsive design works

---

## Next Steps (Optional Features)

1. **Queue Jobs:**
   - `app/Jobs/SyncProviderBalance.php`
   - `app/Jobs/SyncProviderServices.php`
   - Schedule daily/hourly syncs

2. **API Endpoints for Frontend:**
   - Create frontend API routes for service display
   - Price markup calculation endpoint

3. **Analytics:**
   - Track API provider usage
   - Monitor balance changes
   - Service popularity reporting

4. **Notifications:**
   - Low balance alerts
   - Sync failure notifications
   - Service unavailability alerts

5. **Advanced Features:**
   - Markup percentage management per service
   - Price rules and conditions
   - Service scheduling
   - Whitelist/blacklist management

---

## Support & Troubleshooting

### Issue: "Connection test failed"
- Verify API URL format: `https://api.provider.com/api/v2`
- Ensure API key is correct and not expired
- Check provider's rate limiting
- Verify network connectivity

### Issue: "Services import returns empty"
- Test API connection first
- Verify provider has active services
- Check API response format matches expectations
- Review logs in `storage/logs/laravel.log`

### Issue: "Authorization denied" error
- Ensure user has admin role
- Check middleware configuration in routes
- Verify `auth('admin')` middleware exists

---

## Version Information
- Laravel: 10.x+
- PHP: 8.1+
- Tailwind CSS: Latest
- Bootstrap: 5
- DataTables: 1.13.6
- Guzzle HTTP: Latest

---

**Implementation Date:** February 10, 2026
**Status:** ✓ Complete and Ready for Testing
