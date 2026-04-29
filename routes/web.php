<?php

use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\ApiProviderController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\PaymentController;

Route::view('/', 'welcome');
Route::get('blog', function () {
    $posts = \App\Models\Post::where('is_published', true)->latest()->paginate(9);
    return view('blog.index', compact('posts'));
})->name('blog.index');

Route::get('blog/{slug}', function ($slug) {
    $post = \App\Models\Post::where('slug', $slug)->firstOrFail();
    return view('blog.show', compact('post'));
})->name('blog.show');

Route::get('services', function () {
    $categories = \App\Models\Category::where('is_active', true)->get();
    $services = \App\Models\Service::with('category')
        ->where('is_active', true)
        ->when(request('search'), fn($q) => $q->where('name', 'like', '%'.request('search').'%'))
        ->when(request('category'), fn($q) => $q->where('category_id', request('category')))
        ->paginate(20);
    return view('services.index', compact('services', 'categories'));
})->name('services.public');
Route::view('faq', 'faq')->name('faq');
Route::view('contact', 'contact')->name('contact');
// Volt::route('servicespage', 'services.index')->name('services.public');

Route::get('dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Volt::route('order/new', 'orders.create')
    ->middleware(['auth', 'verified'])
    ->name('orders.create');

Volt::route('orders', 'orders.index')
    ->middleware(['auth', 'verified'])
    ->name('orders.index');

Volt::route('orders/{order}', 'orders.show')
    ->middleware(['auth', 'verified'])
    ->name('orders.show');

Volt::route('funds', 'funds.add-funds')
    ->middleware(['auth', 'verified'])
    ->name('funds.index');

Volt::route('api-access', 'api.index')
    ->middleware(['auth', 'verified'])
    ->name('api.access');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Payment Gateway Routes
Route::middleware(['auth'])->group(function () {
    Route::get('payment/initiate/{amount}', [PaymentController::class, 'index'])->name('payment.initiate');
    Route::post('payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::post('payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');
    Route::post('payment/notify', [PaymentController::class, 'notify'])->name('payment.notify');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Volt::route('dashboard', 'admin.dashboard')->name('dashboard');
    Volt::route('categories', 'admin.categories.index')->name('categories');
    Volt::route('orders', 'admin.orders.index')->name('orders');
    Volt::route('orders/{order}', 'admin.orders.show')->name('orders.show');
    Volt::route('users', 'admin.users.index')->name('users');
    Volt::route('deposits', 'admin.deposits.index')->name('deposits');
    Volt::route('blog', 'admin.blog.index')->name('blog');
    Volt::route('settings', 'admin.settings.index')->name('settings');
    Volt::route('api', 'admin.api.index')->name('api');

    // API Providers Management
    Route::resource('api-providers', ApiProviderController::class)->except(['create']);
    Route::post('api-providers/{apiProvider}/toggle', [ApiProviderController::class, 'toggle'])
        ->name('api-providers.toggle');
    Route::post('api-providers/{apiProvider}/sync-balance', [ApiProviderController::class, 'syncBalance'])
        ->name('api-providers.sync-balance');
    Route::post('api-providers/{apiProvider}/sync-services', [ApiProviderController::class, 'syncServices'])
        ->name('api-providers.sync-services');
    Route::post('api-providers/{apiProvider}/test-connection', [ApiProviderController::class, 'testConnection'])
        ->name('api-providers.test-connection');
    Route::post('api-providers/{apiProvider}/restore', [ApiProviderController::class, 'restore'])
        ->name('api-providers.restore');

    // Services Management
    Route::resource('services', ServiceController::class)->except(['create']);
        // AJAX: Fetch services from API provider
        Route::post('services/fetch-api-services', [ServiceController::class, 'fetchApiServices'])->name('services.fetch-api-services');
    Route::get('services/import/{apiProvider}', [ServiceController::class, 'bulkImport'])
        ->name('services.bulk-import');
    Route::post('services/import-store', [ServiceController::class, 'bulkImportStore'])
        ->name('services.bulk-import-store');
    Route::post('services/bulk-enable', [ServiceController::class, 'bulkEnable'])
        ->name('services.bulk-enable');
    Route::post('services/bulk-disable', [ServiceController::class, 'bulkDisable'])
        ->name('services.bulk-disable');
    Route::post('services/{service}/sync', [ServiceController::class, 'syncFromProvider'])
        ->name('services.sync');
});

require __DIR__.'/auth.php';
