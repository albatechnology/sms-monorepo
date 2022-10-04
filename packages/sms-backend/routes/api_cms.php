<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');

    // Product Categories
    Route::post('product-categories/media', 'ProductCategoryApiController@storeMedia')->name('product-categories.storeMedia');
    Route::apiResource('product-categories', 'ProductCategoryApiController');

    // Product Tags
    Route::apiResource('product-tags', 'ProductTagApiController');

    // Products
    Route::post('products/media', 'ProductApiController@storeMedia')->name('products.storeMedia');
    Route::apiResource('products', 'ProductApiController');

    // Product Units
    Route::post('product-units/media', 'ProductUnitsApiController@storeMedia')->name('product-units.storeMedia');
    Route::apiResource('product-units', 'ProductUnitsApiController');

    // Companies
    Route::apiResource('companies', 'CompanyApiController');

    // Channel Categories
    Route::apiResource('channel-categories', 'ChannelCategoryApiController');

    // Channels
    Route::apiResource('channels', 'ChannelApiController');

    // Leads
    Route::apiResource('leads', 'LeadsApiController');

    // Activities
    Route::apiResource('activities', 'ActivityApiController');

    // Items
    Route::apiResource('items', 'ItemApiController');

    // Item Product Units
    Route::apiResource('item-product-units', 'ItemProductUnitApiController');

    // Customers
    Route::apiResource('customers', 'CustomerApiController');

    // Addresses
    Route::apiResource('addresses', 'AddressApiController');

    // Discounts
    Route::apiResource('discounts', 'DiscountApiController');

    // Promos
    Route::post('promos/media', 'PromoApiController@storeMedia')->name('promos.storeMedia');
    Route::apiResource('promos', 'PromoApiController');

    // Banners
    Route::apiResource('banners', 'BannerApiController');

    // Payment Categories
    Route::apiResource('payment-categories', 'PaymentCategoryApiController');

    // Payment Types
    Route::apiResource('payment-types', 'PaymentTypeApiController');

    // Activity Comments
    Route::apiResource('activity-comments', 'ActivityCommentApiController');

    // Orders
    Route::apiResource('orders', 'OrderApiController', ['except' => ['store']]);

    // Order Trackings
    Route::apiResource('order-trackings', 'OrderTrackingApiController', ['except' => ['store', 'update', 'destroy']]);

    // Tax Invoices
    Route::apiResource('tax-invoices', 'TaxInvoiceApiController');

    // Order Details
    Route::post('order-details/media', 'OrderDetailApiController@storeMedia')->name('order-details.storeMedia');
    Route::apiResource('order-details', 'OrderDetailApiController', ['except' => ['store', 'update', 'destroy']]);

    // Payments
    Route::post('payments/media', 'PaymentApiController@storeMedia')->name('payments.storeMedia');
    Route::apiResource('payments', 'PaymentApiController');

    // Shipments
    Route::apiResource('shipments', 'ShipmentApiController');

    // Invoices
    Route::post('invoices/media', 'InvoiceApiController@storeMedia')->name('invoices.storeMedia');
    Route::apiResource('invoices', 'InvoiceApiController');

    // Targets
    Route::apiResource('targets', 'TargetApiController');

    // Catalogues
    Route::post('catalogues/media', 'CatalogueApiController@storeMedia')->name('catalogues.storeMedia');
    Route::apiResource('catalogues', 'CatalogueApiController');

    // Stocks
    Route::apiResource('stocks', 'StockApiController', ['except' => ['store', 'destroy']]);

    // Stock Transfers
    Route::apiResource('stock-transfers', 'StockTransferApiController', ['except' => ['update', 'destroy']]);

    // Supervisor Types
    Route::apiResource('supervisor-types', 'SupervisorTypeApiController');

    // Target Schedules
    Route::apiResource('target-schedules', 'TargetScheduleApiController');
});
