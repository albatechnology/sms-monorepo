<?php

use App\Http\Controllers\API\V1\ActivityCommentController;
use App\Http\Controllers\API\V1\ActivityController;
use App\Http\Controllers\API\V1\AddressController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CartController;
use App\Http\Controllers\API\V1\CartDemandController;
use App\Http\Controllers\API\V1\ChannelController;
use App\Http\Controllers\API\V1\InteriorDesignController;
use App\Http\Controllers\API\V1\CompanyController;
use App\Http\Controllers\API\V1\CustomerController;
use App\Http\Controllers\API\V1\CustomerDepositController;
use App\Http\Controllers\API\V1\DiscountController;
use App\Http\Controllers\API\V1\LeadController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\PaymentController;
use App\Http\Controllers\API\V1\ProductCategoryController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\ProductTagController;
use App\Http\Controllers\API\V1\ProductUnitController;
use App\Http\Controllers\API\V1\PromoController;
use App\Http\Controllers\API\V1\PushNotificationController;
use App\Http\Controllers\API\V1\QaMessageController;
use App\Http\Controllers\API\V1\QaTopicController;
use App\Http\Controllers\API\V1\ReportController;
use App\Http\Controllers\API\V1\StockController;
use App\Http\Controllers\API\V1\TargetController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\DashboardController;
use App\Http\Controllers\API\V1\NewReportController;
use App\Http\Controllers\API\V1\OrderDetailController;
use App\Http\Controllers\API\V1\PromoCategoryController;
use App\Http\Controllers\API\V1\SubscribtionController;
use App\Http\Controllers\API\V1\VoucherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** Push Notification */
Route::put('/notifications/{code}/unsubscribe', [PushNotificationController::class, 'unsubscribe'])->name('push-notification.unsubscribe');
Route::post('subscribe', [SubscribtionController::class, 'subscribe']);

// Route::get('/orders/export-quotation', [OrderController::class, 'exportQuotation'])->name('orders.export-quotation');
Route::middleware(['auth:sanctum', 'impersonate'])->group(function () {
    /** Users */
    Route::get('/users/me', [UserController::class, 'me'])->name('users.me');
    Route::get('/users/channels', [UserController::class, 'channels'])->name('users.channels');
    Route::get('/users/product-brands', [UserController::class, 'productBrands'])->name('users.productBrands');
    Route::get('/users/supervisor-types', [UserController::class, 'supervisorTypes'])->name('users.supervisor.types');
    Route::get('/users/supervisor', [UserController::class, 'supervisor'])->name('users.supervisor');
    Route::get('/users/supervised', [UserController::class, 'supervised'])->name('users.supervised');
    Route::get('/users/report-list', [UserController::class, 'indexUserForReport'])->name('users.forReport');
    Route::put('/users/channel/{channel}', [UserController::class, 'channel'])->name('users.channel');
    Route::post('/users/password', [UserController::class, 'changePassword'])->name('users.password');
    Route::resource('users', UserController::class)->only(['index', 'show']);

    /** Companies and Channel*/
    Route::get('company-accounts', [CompanyController::class, 'accountsIndex'])->name('company-accounts.index');
    Route::resource('companies', CompanyController::class)->only(['index', 'show']);
    Route::get('channels/default', [ChannelController::class, 'default'])->name('channels.default');
    Route::resource('channels', ChannelController::class)->only(['index', 'show']);

    /** Companies and Channel*/
    Route::get('interior-designs/reports/{interior_design}/leads', [InteriorDesignController::class, 'reportLeads']);
    Route::get('interior-designs/reports/{interior_design}/leads/{lead}', [InteriorDesignController::class, 'reportLeadActivities']);
    Route::resource('interior-designs', InteriorDesignController::class)->only(['index', 'show']);

    /** Global data */
    Route::get('/customers/find-by-phone', [CustomerController::class, 'getCustomerByPhone'])->name('customers.addresses.findByPhone');
    Route::post('/customers/addresses', [CustomerController::class, 'storeWithAddress'])->name('customers.addresses.store');
    Route::get('/customers/{customer}/leads', [CustomerController::class, 'getCustomerLeads'])->name('customers.leads');
    Route::get('/customers/vouchers', [CustomerController::class, 'customerVouchers'])->name('customers.vouchers');
    Route::get('/customers/{customer}/activities', [CustomerController::class, 'getCustomerActivities'])->name('customers.activities');
    Route::resource('customers', CustomerController::class);
    Route::resource('addresses', AddressController::class);

    /** Push Notification */
    Route::post('/notifications/test', [PushNotificationController::class, 'test'])->name('push-notification.test');
    Route::put('/notifications/clear', [PushNotificationController::class, 'clear'])->name('push-notification.clear');
    Route::get('/notifications/', [PushNotificationController::class, 'index'])->name('push-notification.index');
    Route::put('/notifications/{code}/subscribe', [PushNotificationController::class, 'subscribe'])->name('push-notification.subscribe');

    /** Dashboard */
    Route::get('/dashboard/media', [DashboardController::class, 'media']);
    Route::get('/dashboard/cart-demands', [DashboardController::class, 'cartDemands']);
    Route::get('/dashboard/cart-demands/detail', [DashboardController::class, 'cartDemandDetail']);
    Route::get('/dashboard/pelunasan', [DashboardController::class, 'pelunasan']);
    Route::get('/dashboard/index-top-sales/{type}', [DashboardController::class, 'indexTopSales']);
    Route::get('/dashboard/top-sales/{type}', [DashboardController::class, 'topSales']);
    Route::get('/dashboard/brand-categories/{brandCategoryId}', [DashboardController::class, 'detailBrandCategories']);
    Route::get('/dashboard/brand-categories', [DashboardController::class, 'brandCategories']);
    Route::get('/dashboard/interior-designs', [DashboardController::class, 'interiorDesign']);
    Route::get('/dashboard/interior-designs/detail/{interiorDesignId?}', [DashboardController::class, 'interiorDesignDetail']);
    Route::get('/dashboard/sales-estimation/{brandCategoryId}', [DashboardController::class, 'salesEstimation']);
    Route::get('/dashboard/report-leads', [DashboardController::class, 'reportLeads']);
    Route::get('/dashboard/report-leads-optimized', [DashboardController::class, 'reportLeadsOptimized']);
    Route::get('/dashboard/report-leads/details', [DashboardController::class, 'reportLeadsDetails']);
    Route::get('/dashboard/report-leads/closing-deals', [\App\Http\Controllers\API\V1\ClosingDealsController::class, 'noOfLeads']);
    Route::get('/dashboard/report-leads/hot', [DashboardController::class, 'reportHot']);
    Route::get('/dashboard/report-leads/drop', [DashboardController::class, 'reportDrop']);
    // Route::get('/dashboard/report-leads/hot-new', [DashboardController::class, 'reportHotNew']);
    Route::get('/dashboard/report-leads/status', [DashboardController::class, 'reportStatus']);
    Route::get('/dashboard/report-leads/status-new', [DashboardController::class, 'reportStatusNew']);
    Route::get('/dashboard/report-brands', [DashboardController::class, 'reportBrands']);
    Route::get('/dashboard/report-brands/details', [DashboardController::class, 'reportBrandsDetails']);

    /** New Report */
    Route::get('new-reports', [NewReportController::class, 'index'])->name('newReports.index');
    Route::get('new-reports/deals', [NewReportController::class, 'deals'])->name('newReports.deals');
    Route::get('new-reports/quotation', [NewReportController::class, 'quotation'])->name('newReports.quotation');
    Route::get('new-reports/new-leads', [NewReportController::class, 'newLeads'])->name('newReports.newLeads');
    Route::get('new-reports/active-leads', [NewReportController::class, 'activeLeads'])->name('newReports.activeLeads');
    Route::get('new-reports/follow-up', [NewReportController::class, 'followUp'])->name('newReports.followUp');
    Route::get('new-reports/lead-status', [NewReportController::class, 'leadStatus'])->name('newReports.leadStatus');
    Route::get('new-reports/details', [NewReportController::class, 'details'])->name('newReports.details');
    Route::get('new-reports/interior-designs', [NewReportController::class, 'interiorDesigns'])->name('newReports.interiorDesigns');
    Route::get('new-reports/interior-designs/details', [NewReportController::class, 'interiorDesignDetails'])->name('newReports.interiorDesignDetails');
    Route::get('new-reports/brand-details', [NewReportController::class, 'brandDetails'])->name('newReports.brandDetails');

    // Route::get('new-reports/test', [NewReportController::class, 'test'])->name('newReports.test');
    Route::get('new-reports/leads', [NewReportController::class, 'leads'])->name('newReports.leads');
    Route::get('new-reports/invoice', [NewReportController::class, 'invoice'])->name('newReports.invoice');

    /** Tenanted data */
    Route::middleware(['default_tenant'])->group(function () {
        Route::get('/leads/list', [LeadController::class, 'list'])->name('leads.list');
        Route::get('/leads/unhandled', [LeadController::class, 'unhandledIndex'])->name('leads.unhandled');
        Route::get('/leads/categories', [LeadController::class, 'categories'])->name('leads.categories');
        Route::get('/leads/sub-categories/{leadCategory}', [LeadController::class, 'subCategories'])->name('leads.subCategories');
        Route::get('/leads/{lead}/product-brands', [LeadController::class, 'productBrands']);
        Route::put('/leads/{lead}/assign', [LeadController::class, 'assign']);
        Route::resource('leads', LeadController::class);

        Route::get('activities/active/{lead_id}', [ActivityController::class, 'active']);
        Route::get('activities/{activity}/comments', [ActivityController::class, 'getActivityComments'])->name('activities.comments');
        Route::get('activities/report', [ActivityController::class, 'report']);
        Route::get('activities/report/detail', [ActivityController::class, 'detail']);
        Route::get('activities/report/detail/{userId}', [LeadController::class, 'activityReport']);
        Route::post('activities/{activity}/image', [ActivityController::class, 'uploadImageOfActivity'])->name('activities.image');
        Route::resource('activities', ActivityController::class);
        Route::resource('activity-comments', ActivityCommentController::class);

        Route::resource('customer-deposits', CustomerDepositController::class)->only(['store']);

        Route::get('brands', [ProductController::class, 'brands'])->name('brands');
        Route::get('models', [ProductController::class, 'models'])->name('models');
        Route::get('models/{model}', [ProductController::class, 'model'])->name('models.get');
        Route::get('versions', [ProductController::class, 'versions'])->name('versions');
        Route::get('category-codes', [ProductController::class, 'categoryCodes'])->name('category-codes');
        Route::resource('products', ProductController::class)->only(['index', 'show']);

        Route::post('product-units/{productUnit}/upload', [ProductUnitController::class, 'uploadProductUnitImage'])->name('productUnit.upload');
        Route::get('colours', [ProductUnitController::class, 'colours'])->name('colours');
        Route::get('coverings', [ProductUnitController::class, 'coverings'])->name('coverings');
        Route::resource('product-units', ProductUnitController::class)->only(['index', 'show']);
        Route::resource('product-tags', ProductTagController::class)->only(['index', 'show']);
        Route::resource('product-categories', ProductCategoryController::class)->only(['index', 'show']);

        Route::get('/qa-topics/{topic}/qa-messages', [QaTopicController::class, 'messages'])->name('qa-topics.qa-messages');
        Route::resource('qa-topics', QaTopicController::class);
        Route::resource('qa-messages', QaMessageController::class);

        Route::group(['prefix' => 'carts', 'as' => 'carts.'], function () {
            Route::get('', [CartController::class, 'index'])->name('index');
            Route::put('', [CartController::class, 'sync'])->name('sync');

            Route::group(['prefix' => 'stocks', 'as' => 'stocks.'], function () {
                Route::get('{productUnitId}', [CartController::class, 'stockIndex'])->name('index');
                Route::put('', [CartController::class, 'stockSync'])->name('sync');
            });
        });

        Route::post('cart-demands', [CartDemandController::class, 'sync'])->name('cart-demands.sync');
        Route::post('cart-demands/{cartDemand}/upload', [CartDemandController::class, 'uploadImage'])->name('cart-demands.uploadImage');
        Route::resource('cart-demands', CartDemandController::class)->only(['index', 'destroy']);

        Route::get('vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
        Route::post('vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
        Route::get('vouchers/{voucher}', [VoucherController::class, 'show'])->name('vouchers.show');

        Route::get('discounts', [DiscountController::class, 'index'])->name('discounts.index');
        Route::get('discounts/{code}', [DiscountController::class, 'discountGetByCode'])->name('discounts.code');

        Route::get('/orders/export-quotation', [OrderController::class, 'exportQuotation'])->name('orders.export-quotation');
        Route::post('/orders/preview', [OrderController::class, 'preview'])->name('orders.preview');
        Route::post('/orders/preview-update/{order}', [OrderController::class, 'previewUpdate'])->name('orders.previewUpdate');
        Route::get('/orders/waiting-approval', [OrderController::class, 'indexWaitingApproval'])->name('orders.waiting-approval');
        Route::get('/orders/list-approval', [OrderController::class, 'listApproval'])->name('orders.list-approval');
        Route::put('/orders/approve/{order}', [OrderController::class, 'approve'])->name('orders.approve');
        Route::put('/orders/cancel/{order}', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/request-approval/{order}', [OrderController::class, 'requestApproval'])->name('orders.request-approval');
        Route::post('/orders/clone/{order}', [OrderController::class, 'clone'])->name('orders.clone');
        Route::resource('orders', OrderController::class)->only(['store', 'show', 'index', 'update']);

        Route::post('/order-details/{orderDetail}/upload', [OrderDetailController::class, 'uploadImage'])->name('order-details.uploadImage');
        Route::resource('order-details', OrderDetailController::class)->only(['show']);

        Route::get('/payment-categories', [PaymentController::class, 'indexPaymentCategory'])->name('payment-categories.index');
        Route::get('/payment-types', [PaymentController::class, 'indexPaymentType'])->name('payment-types.index');
        Route::post('/payments/{payment}/proof', [PaymentController::class, 'uploadProofOfPayment'])->name('payments.proof');
        Route::resource('payments', PaymentController::class)->except(['destroy']);

        Route::get('/stocks/indexNew', [StockController::class, 'indexNew'])->name('stocks.indexNew');
        Route::get('/stocks/product-unit/{productUnitId}', [StockController::class, 'productChannel'])->name('stocks.productChannel');
        Route::get('/stocks/extendedNew/{channelId}', [StockController::class, 'extendedNew'])->name('stocks.extendedNew');
        Route::get('/stocks/extended/detail/{companyId}/{channelId}/{productUnitId}', [StockController::class, 'extendedDetail'])->name('stocks.index.extended.detail');
        Route::get('/stocks/extended', [StockController::class, 'indexExtended'])->name('stocks.index.extended');
        Route::resource('stocks', StockController::class)->only(['show', 'index']);

        Route::get('/reports/sales-revenue', [ReportController::class, 'salesRevenue']);
        Route::get('/reports/by-leads', [ReportController::class, 'reportByLeads']);
        Route::resource('reports', ReportController::class)->only(['show', 'index']);
        Route::resource('targets', TargetController::class)->only(['index']);

        Route::resource('promo-categories', PromoCategoryController::class)->only(['index', 'show']);
        Route::resource('promos', PromoController::class)->only(['index', 'show']);
    });
});

Route::prefix('auth')->group(function () {
    Route::post('/token', [AuthController::class, 'token'])->name('auth.token');
    // Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
});
