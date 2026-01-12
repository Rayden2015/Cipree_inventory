<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LevyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EnduserController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TotalTaxController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MyAccountController;
use App\Http\Controllers\TaxLeviesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthoriserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StoreRequestController;
use App\Http\Controllers\DashboardNavigationController;
use App\Http\Controllers\StockPurchaseRequestController;
use App\Http\Controllers\ErrorLogController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});


Route::resource('users', UserController::class);
Route::resource('uom', UomController::class);
Route::get('search_users', [UserController::class, 'searchUsers'])->name('search.users');

// Error Logs (Admin Only)
Route::prefix('error-logs')->name('error-logs.')->group(function () {
    Route::get('/', [ErrorLogController::class, 'index'])->name('index');
    Route::get('/search', [ErrorLogController::class, 'search'])->name('search');
    Route::get('/search-files', [ErrorLogController::class, 'searchFiles'])->name('search-files');
    Route::get('/{id}', [ErrorLogController::class, 'show'])->name('show');
});

Route::resource('company', CompanyController::class);
Route::resource('suppliers', SupplierController::class);
Route::get('suppliers/import', [App\Http\Controllers\SupplierController::class, 'showImportForm'])->name('suppliers.import.form');
Route::post('suppliers/import', [App\Http\Controllers\SupplierController::class, 'import'])->name('suppliers.import');
Route::get('suppliers/import/template', [App\Http\Controllers\SupplierController::class, 'downloadImportTemplate'])->name('suppliers.import.template');
Route::get('supplier_search', [SupplierController::class, 'supplier_search'])->name('supplier_search');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Banner routes
Route::post('banner/dismiss/session', [HomeController::class, 'dismissBannerSession'])->name('banner.dismiss.session');
Route::post('banner/dismiss/permanent', [HomeController::class, 'dismissBannerPermanent'])->name('banner.dismiss.permanent');

//myaccount routes
Route::get('myaccounts', [MyAccountController::class, 'index'])->name('myaccounts.index');
Route::put('myaccounts/update/{id}', [MyAccountController::class, 'update'])->name('myaccounts.update');
Route::put('myaccounts/changepassword', [MyAccountController::class, 'changepassword'])->name('myaccounts.changepassword');

//item
Route::resource('items', ItemController::class);
Route::get('items/import', [App\Http\Controllers\ItemController::class, 'showImportForm'])->name('items.import.form');
Route::post('items/import', [App\Http\Controllers\ItemController::class, 'import'])->name('items.import');
Route::get('items/import/template', [App\Http\Controllers\ItemController::class, 'downloadImportTemplate'])->name('items.import.template');
Route::get('item_search', [App\Http\Controllers\ItemController::class, 'item_search'])->name('item_search');
Route::get('product_history', [App\Http\Controllers\ItemController::class, 'product_history'])->name('product_history');
Route::get('product_history_show/{id}', [App\Http\Controllers\ItemController::class, 'product_history_show'])->name('product_history_show');
Route::get('itemspersite', [App\Http\Controllers\ItemController::class, 'itemspersite'])->name('itemspersite');
Route::get('/export-items-per-site', [ItemController::class, 'exportItemsPerSite'])->name('export.items_per_site');


// parts routes
Route::resource('parts', PartsController::class);
Route::get('ajax-autocomplete-search', [PartsController::class, 'selectSearch']);
Route::get('ajax-autocomplete-site', [PartsController::class, 'selectSite']);

// department controller
Route::resource('departmentslist', DepartmentController::class);
// section controller
Route::resource('sectionslist', SectionController::class);

// locations
Route::resource('locations', LocationController::class);
//inventories
Route::resource('inventories', InventoryController::class);
Route::post('inventory_action', [InventoryController::class, 'inventory_action'])->name('inventories.action');
Route::post('update_inventory_item/{id}', [InventoryController::class, 'update_inventory_item'])->name('inventories.update_inventory_item');
Route::get('ajax-autocomplete-category', [InventoryController::class, 'selectCategory']);
Route::get('ajax-autocomplete-deliveredby', [InventoryController::class, 'selectDeliveredBy']);
Route::get('inventory_search', [InventoryController::class, 'inventory_search'])->name('inventory_search');
Route::get('out_of_stock_search', [InventoryController::class, 'out_of_stock_search'])->name('out_of_stock_search');
Route::get('inventory_home_search', [InventoryController::class, 'inventory_home_search'])->name('inventory_home_search');
Route::get('inventory_history_search', [InventoryController::class, 'inventory_history_search'])->name('inventory_history_search');
Route::get('inventory_history_date_search', [InventoryController::class, 'inventory_history_date_search'])->name('inventory_history_date_search');
Route::get('out_of_stock', [InventoryController::class, 'out_of_stock'])->name('inventories.out_of_stock');

Route::get('ajax-autocomplete-item', [InventoryController::class, 'getItem']);
Route::get('ajax-autocomplete-items', [InventoryController::class, 'getItems']);
Route::get('ajax-autocomplete-location', [InventoryController::class, 'getLocation']);
Route::get('ajax-autocomplete-selectlocation', [InventoryController::class, 'selectLocation']);
Route::get('inventory_item_history', [InventoryController::class, 'inventory_item_history'])->name('inventories.inventory_item_history');
Route::get('inventory_history_show/{id}', [InventoryController::class, 'inventory_history_show'])->name('inventories.inventory_history_show');
Route::get('inventory_history_edit/{id}', [InventoryController::class, 'inventory_history_edit'])->name('inventories.inventory_history_edit');
Route::put('inventory_history_update/{id}', [InventoryController::class, 'inventory_history_update'])->name('inventories.inventory_history_update');
Route::post('update_inventory_history/{id}', [InventoryController::class, 'update_inventory_history'])->name('inventories.update_inventory_history');
Route::delete('inventory_history_destroy/{id}', [InventoryController::class, 'inventory_history_destroy'])->name('inventories.inventory_history_destroy');
Route::get('generateinventoryPDF/{id}', [InventoryController::class, 'generateinventoryPDF'])->name('inventories.generateinventoryPDF');
Route::post('check_waybill', [InventoryController::class, 'check_waybill'])->name('check_waybill');
Route::post('check_po_number', [InventoryController::class, 'check_po_number'])->name('check_po_number');
Route::post('check_invoice_number', [InventoryController::class, 'check_invoice_number'])->name('check_invoice_number');
// Route::post('/check_superior',['uses'=>'PagesController@checkEmail']);
// Route::post('sendProductNotification', [UserController::class, 'sendProductNotification'])->name('stores.sendproductnotification');
//categories
Route::resource('categories', CategoryController::class);
// sites
Route::resource('sites', SiteController::class);
// endusers
Route::resource('endusers', EnduserController::class);
Route::get('endusers/import', [App\Http\Controllers\EnduserController::class, 'showImportForm'])->name('endusers.import.form');
Route::post('endusers/import', [App\Http\Controllers\EnduserController::class, 'import'])->name('endusers.import');
Route::get('endusers/import/template', [App\Http\Controllers\EnduserController::class, 'downloadImportTemplate'])->name('endusers.import.template');
Route::get('endusersearch', [EnduserController::class, 'search'])->name('endusers.search');
Route::get('endusersort', [EnduserController::class, 'endusersort'])->name('endusersort');
Route::get('enduser_show/{id}', [EnduserController::class, 'show'])->name('endusers.show');
// purchases routes
Route::resource('purchases', PurchaseController::class);
Route::get('generate_order/{id}', [PurchaseController::class, 'generate_order'])->name('purchases.generate_order');
Route::get('purchase_order_draft/{id}', [PurchaseController::class, 'purchase_order_draft'])->name('purchases.purchase_order_draft');
Route::post('savelevytax_porder/{id}', [PurchaseController::class, 'savelevytax_porder'])->name('savelevytax_porder');
Route::get('purchase_list', [PurchaseController::class, 'purchase_list'])->name('purchases.purchase_list');
Route::get('purchase_edit/{id}', [PurchaseController::class, 'purchase_edit'])->name('purchases.purchase_edit');
Route::put('purchase_update/{id}', [PurchaseController::class, 'purchase_update'])->name('purchases.purchase_update');
Route::put('purchase_update_row/{id}', [PurchaseController::class, 'purchase_update_row'])->name('purchases.purchase_update_row');
Route::delete('purchase_destroy/{id}', [PurchaseController::class, 'purchase_destroy'])->name('purchases.purchase_destroy');
Route::get('showlist/{id}', [PurchaseController::class, 'showlist'])->name('purchases.showlist');
Route::get('editlist/{id}', [PurchaseController::class, 'editlist'])->name('purchases.editlist');
Route::post('purchaselist_update', [PurchaseController::class, 'purchaselist_update'])->name('purchases.purchaselist_update');
Route::post('action', [PurchaseController::class, 'action'])->name('purchases.action');
Route::get('requested', [PurchaseController::class, 'requested'])->name('purchases.requested');
Route::get('initiated', [PurchaseController::class, 'initiated'])->name('purchases.initiated');
Route::get('approved', [PurchaseController::class, 'approved'])->name('purchases.approved');
Route::get('ordered', [PurchaseController::class, 'ordered'])->name('purchases.ordered');
Route::get('delivered', [PurchaseController::class, 'delivered'])->name('purchases.delivered');

Route::get('req_all', [PurchaseController::class, 'req_all'])->name('purchases.req_all');
Route::get('all_requests', [PurchaseController::class, 'all_requests'])->name('purchases.all_requests');
Route::get('all_initiates', [PurchaseController::class, 'all_initiates'])->name('purchases.all_initiates');
Route::get('all_approves', [PurchaseController::class, 'all_approves'])->name('purchases.all_approves');
Route::get('all_orders', [PurchaseController::class, 'all_orders'])->name('purchases.all_orders');
Route::get('all_delivers', [PurchaseController::class, 'all_delivers'])->name('purchases.all_delivers');
Route::get('generatePDF/{id}', [PurchaseController::class, 'generatePDF'])->name('purchases.generatePDF');
Route::get('generatePurchaseOrderPDF/{id}', [PurchaseController::class, 'generatePurchaseOrderPDF'])->name('purchases.generatePurchaseOrderPDF');
Route::get('ajax-autocomplete-part', [PurchaseController::class, 'selectPart']);
Route::get('ajax-autocomplete-search', [PurchaseController::class, 'selectSearch']);
Route::get('ajax-autocomplete-enduser', [PurchaseController::class, 'selectEnduser']);
Route::get('ajax-autocomplete-requester', [PurchaseController::class, 'selectRequester']);
Route::get('ajax-autocomplete-tax', [PurchaseController::class, 'selectTax']);
Route::get('ajax-autocomplete-levy', [PurchaseController::class, 'selectLevy']);
Route::get('po_all_requests', [PurchaseController::class, 'po_all_requests'])->name('purchases.po_all_requests');
Route::put('save_draft/{id}', [PurchaseController::class, 'save_draft'])->name('save_draft');
Route::get('drafts', [PurchaseController::class, 'drafts'])->name('purchases.drafts');


//storerequest routes
Route::get('request_search', [StoreRequestController::class, 'request_search'])->name('stores.request_search');
Route::get('requester_search', [StoreRequestController::class, 'requester_search'])->name('stores.requester_search');
Route::get('ajax-autocomplete-locations', [StoreRequestController::class, 'selectLocations']);
Route::get('cart', [StoreRequestController::class, 'cart'])->name('cart');
Route::get('add-to-cart/{id}', [StoreRequestController::class, 'addToCart'])->name('add.to.cart');
Route::patch('update-cart', [StoreRequestController::class, 'update'])->name('update.cart');
Route::delete('remove-from-cart', [StoreRequestController::class, 'remove'])->name('remove.from.cart');
Route::delete('sorders/destroy/{id}', [StoreRequestController::class, 'destroy'])->name('sorders.destroy');
Route::delete('requester_store_delete/{id}', [StoreRequestController::class, 'requester_store_delete'])->name('stores.requester_store_delete');
Route::post('sorders/store/', [StoreRequestController::class, 'store'])->name('sorders.store');
Route::get('store_lists/', [StoreRequestController::class, 'store_lists'])->name('sorders.store_lists');
Route::get('store_list_view/{id}', [StoreRequestController::class, 'store_list_view'])->name('sorders.store_list_view');
Route::get('requester_store_list_view/{id}', [StoreRequestController::class, 'requester_store_list_view'])->name('sorders.requester_store_list_view');
Route::get('generatesorderPDF/{id}', [StoreRequestController::class, 'generatesorderPDF'])->name('sorders.generatesorderPDF');
Route::get('authoriser_store_list_view_dash/{id}', [StoreRequestController::class, 'authoriser_store_list_view_dash'])->name('sorders.authoriser_store_list_view_dash');
Route::get('store_list_edit/{id}', [StoreRequestController::class, 'store_list_edit'])->name('sorders.store_list_edit');
Route::put('store_list_update/{id}', [StoreRequestController::class, 'store_list_update'])->name('stores.store_list_update');
Route::put('sorder_update/{id}', [StoreRequestController::class, 'sorder_update'])->name('stores.sorder_update');
Route::put('requester_sorder_update/{id}', [StoreRequestController::class, 'requester_sorder_update'])->name('stores.requester_sorder_update');
Route::post('stores_action', [StoreRequestController::class, 'stores_action'])->name('stores.action');
Route::get('stores/approved_status/{id}', [StoreRequestController::class, 'approved_status'])->name('stores.approved_status');
Route::get('stores/depart_auth_approved_status/{id}', [StoreRequestController::class, 'depart_auth_approved_status'])->name('stores.depart_auth_approved_status');
Route::get('stores/denied_status/{id}', [StoreRequestController::class, 'denied_status'])->name('stores.denied_status');
Route::get('stores/depart_auth_denied_status/{id}', [StoreRequestController::class, 'depart_auth_denied_status'])->name('stores.depart_auth_denied_status');
Route::get('store_officer_lists', [StoreRequestController::class, 'store_officer_lists'])->name('stores.store_officer_lists');
Route::get('received_history_page', [StoreRequestController::class, 'received_history_page'])->name('stores.received_history_page');

Route::get('store_officer_list_search', [StoreRequestController::class, 'store_officer_list_search'])->name('stores.store_officer_list_search');
Route::get('store_requester_lists', [StoreRequestController::class, 'store_requester_lists'])->name('stores.store_requester_lists');
Route::get('store_officer_edit/{id}', [StoreRequestController::class, 'store_officer_edit'])->name('stores.store_officer_edit');
Route::put('update_manual_remarks/{id}', [StoreRequestController::class, 'update_manual_remarks'])->name('stores.update_manual_remarks');
Route::get('requester_edit/{id}', [StoreRequestController::class, 'requester_edit'])->name('stores.requester_edit');
Route::put('store_officer_update/{id}', [StoreRequestController::class, 'store_officer_update'])->name('stores.store_officer_update');
Route::put('requester_store_update/{id}', [StoreRequestController::class, 'requester_store_update'])->name('stores.requester_store_update');
Route::get('supply_history', [StoreRequestController::class, 'supply_history'])->name('stores.supply_history');
Route::get('supply_history_search', [StoreRequestController::class, 'supply_history_search'])->name('stores.supply_history_search');
Route::get('supply_history_search_item', [StoreRequestController::class, 'supply_history_search_item'])->name('stores.supply_history_search_item');
Route::get('requester_store_lists', [StoreRequestController::class, 'requester_store_lists'])->name('stores.requester_store_lists');
Route::get('fetch_single_enduser/{id}', [StoreRequestController::class, 'fetch_single_enduser'])->name('fetch_single_enduser');
Route::get('fetch_single_enduser1/{id}', [StoreRequestController::class, 'fetch_single_enduser1'])->name('fetch_single_enduser1');
Route::delete('sorderpart_delete/{id}', [StoreRequestController::class, 'sorderpart_delete'])->name('sorderpart_delete');
Route::delete('/sorderpart/delete/{id}', [StoreRequestController::class, 'deleteSorderPart'])->name('sorderpart_delete');


// authoriser module
Route::get('authorise_req_all', [AuthoriserController::class, 'req_all'])->name('authorise.req_all');
Route::get('authorise_all_requests', [AuthoriserController::class, 'all_requests'])->name('authorise.all_requests');
Route::get('authorise_all_initiates', [AuthoriserController::class, 'all_initiates'])->name('authorise.all_initiates');
Route::get('authorise_all_approves', [AuthoriserController::class, 'all_approves'])->name('authorise.all_approves');
Route::get('authorise_all_orders', [AuthoriserController::class, 'all_orders'])->name('authorise.all_orders');
Route::get('authorise_all_delivers', [AuthoriserController::class, 'all_delivers'])->name('authorise.all_delivers');
Route::get('approved_status/{id}', [AuthoriserController::class, 'approved_status'])->name('authorise.approved_status');
Route::get('depart_auth_approved_status/{id}', [AuthoriserController::class, 'depart_auth_approved_status'])->name('depart_auth_authorise.approved_status');
Route::get('denied_status/{id}', [AuthoriserController::class, 'denied_status'])->name('authorise.denied_status');

Route::get('depart_auth_denied_status/{id}', [AuthoriserController::class, 'depart_auth_denied_status'])->name('depart_auth_authorise.denied_status');
//order
Route::resource('orders', OrderController::class);
Route::post('orders_action/{id}', [OrderController::class, 'orders_action'])->name('orders.action');
Route::get('orders/view/{id}', [OrderController::class, 'view'])->name('orders.view');
Route::get('admincreate', [OrderController::class, 'admincreate'])->name('orders.admincreate');
Route::post('orderstore', [OrderController::class, 'orderstore'])->name('orders.orderstore');
Route::put('orders_update/{id}', [OrderController::class, 'update'])->name('orders.update');
// notification
Route::get('read/{id}', [NotificationController::class, 'read'])->name('notification.read');

Route::post('fetch_single_product', [InventoryController::class, 'fetch_single_product'])->name('fetch_single_product');

//SMS routes
Route::post('/sms/send/{to}/{content}', [SmsController::class, 'sendSms']);

//dashboard tab navigations
Route::get('pending_po_approvals', [DashboardNavigationController::class, 'pending_po_approvals'])->name('dashboard.pending_po_approvals');
Route::get('approved_request', [DashboardNavigationController::class, 'approved_request'])->name('dashboard.approved_request');
Route::get('approved_pos', [DashboardNavigationController::class, 'approved_pos'])->name('dashboard.approved_pos');
Route::get('processed_request', [DashboardNavigationController::class, 'processed_request'])->name('dashboard.processed_request');
Route::get('pending_request_approvals', [DashboardNavigationController::class, 'pending_request_approvals'])->name('dashboard.pending_request_approvals');
Route::get('pending_stock_approvals', [DashboardNavigationController::class, 'pending_stock_approvals'])->name('dashboard.pending_stock_approvals');
Route::get('processed_pos', [DashboardNavigationController::class, 'processed_pos'])->name('dashboard.processed_pos');
Route::get('reorder_level', [DashboardNavigationController::class, 'reorder_level'])->name('dashboard.reorder_level');
Route::get('out_of_stock', [DashboardNavigationController::class, 'out_of_stock'])->name('dashboard.out_of_stock');
Route::get('out_of_stock_view/{id}', [DashboardNavigationController::class, 'out_of_stock_view'])->name('dashboard.out_of_stock_view');
Route::get('low_stock_view/{id}', [DashboardNavigationController::class, 'low_stock_view'])->name('dashboard.low_stock_view');
Route::get('stock_request_pending', [DashboardNavigationController::class, 'stock_request_pending'])->name('dashboard.stock_request_pending');
Route::get('sofficer_stock_request_pending', [DashboardNavigationController::class, 'sofficer_stock_request_pending'])->name('dashboard.sofficer_stock_request_pending');
Route::get('rfi_pending_approval', [DashboardNavigationController::class, 'rfi_pending_approval'])->name('dashboard.rfi_pending_approval');
Route::get('rfi_approved_requests', [DashboardNavigationController::class, 'rfi_approved_requests'])->name('dashboard.rfi_approved_requests');
Route::get('rfi_processed_requests', [DashboardNavigationController::class, 'rfi_processed_requests'])->name('dashboard.rfi_processed_requests');
Route::get('rfi_denied', [DashboardNavigationController::class, 'rfi_denied'])->name('dashboard.rfi_denied');
Route::get('po_total_value_of_approved_pos_mtd', [DashboardNavigationController::class, 'po_total_value_of_approved_pos_mtd'])->name('dashboard.po_total_value_of_approved_pos_mtd');
Route::get('po_total_value_of_supplied_pos_mtd', [DashboardNavigationController::class, 'po_total_value_of_supplied_pos_mtd'])->name('dashboard.po_total_value_of_supplied_pos_mtd');
Route::get('po_total_value_of_pending_pos_mtd', [DashboardNavigationController::class, 'po_total_value_of_pending_pos_mtd'])->name('dashboard.po_total_value_of_pending_pos_mtd');
Route::get('po_approved_stock_requests', [DashboardNavigationController::class, 'po_approved_stock_requests'])->name('dashboard.po_approved_stock_requests');
Route::get('po_approved_direct_requests', [DashboardNavigationController::class, 'po_approved_direct_requests'])->name('dashboard.po_approved_direct_requests');
Route::get('po_approved_pos', [DashboardNavigationController::class, 'po_approved_pos'])->name('dashboard.po_approved_pos');
Route::get('po_denied_requests', [DashboardNavigationController::class, 'po_denied_requests'])->name('dashboard.po_denied_requests');
Route::get('active_user_accounts', [DashboardNavigationController::class, 'active_user_accounts'])->name('dashboard.active_user_accounts');
Route::get('disabled_user_accounts', [DashboardNavigationController::class, 'disabled_user_accounts'])->name('dashboard.disabled_user_accounts');
Route::get('active_endusers', [DashboardNavigationController::class, 'active_endusers'])->name('dashboard.active_endusers');
Route::get('disabled_endusers', [DashboardNavigationController::class, 'disabled_endusers'])->name('dashboard.disabled_endusers');
Route::get('departments', [DashboardNavigationController::class, 'departments'])->name('dashboard.departments');
Route::get('sections', [DashboardNavigationController::class, 'sections'])->name('dashboard.sections');
Route::get('dpr_pending_approval', [DashboardNavigationController::class, 'dpr_pending_approval'])->name('dashboard.dpr_pending_approval');
Route::get('dpr_approved', [DashboardNavigationController::class, 'dpr_approved'])->name('dashboard.dpr_approved');
Route::get('dpr_processed', [DashboardNavigationController::class, 'dpr_processed'])->name('dashboard.dpr_processed');
Route::get('dpr_denied', [DashboardNavigationController::class, 'dpr_denied'])->name('dashboard.dpr_denied');

Route::get('reorder_level_search', [DashboardNavigationController::class, 'reorder_level_search'])->name('reorder_level_search');

Route::get('items_list_site', [DashboardNavigationController::class, 'items_list_site'])->name('items_list_site');

// Route::post('feedback', 'FeedbackController@store')->name('feedback.store');

// Review controller routes
Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
// Route::get('images', [ReviewController::class, 'images'])->name('reviews.images');
Route::get('reviews/show/{id}', [ReviewController::class, 'show'])->name('reviews.show');
Route::delete('reviews/destroy/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
Route::put('reviews/markAsReviewed/{id}', [ReviewController::class, 'markAsReviewed'])->name('reviews.markAsReviewed');

// monthly report
Route::get('monthlyreport', [ReportController::class, 'monthlyreport'])->name('monthlyreport');

Route::get('/export-search-results', [ExcelController::class, 'exportSearchResults'])->name('exportSearchResults');
Route::get('/export-items-list-site', [ExcelController::class, 'exportItemsListSite'])->name('exportItemsListSite');

Route::get('download-items-list-site-pdf', [PDFController::class, 'downloadItemsListPdf'])->name('downloadItemsListPdf');
// stock purchase requests 
Route::get('spr_lists', [StockPurchaseRequestController::class, 'spr_lists'])->name('spr_lists');
Route::get('spr_create', [StockPurchaseRequestController::class, 'index'])->name('spr_create');
Route::get('stock_purchase_cart', [StockPurchaseRequestController::class, 'stock_purchase_cart'])->name('stock_purchase_cart');
Route::get('addToStock/{id}', [StockPurchaseRequestController::class, 'addToStock'])->name('addToStock');
Route::post('spr_store', [StockPurchaseRequestController::class, 'store'])->name('spr_store');
Route::get('store_officer_spr_edit/{id}', [StockPurchaseRequestController::class, 'store_officer_spr_edit'])->name('store_officer_spr_edit');
Route::patch('update-spr_cart', [StoreRequestController::class, 'update'])->name('update-spr_cart');
Route::put('store_officer_spr_update/{id}', [StockPurchaseRequestController::class, 'store_officer_spr_update'])->name('store_officer_spr_update');
Route::put('so_spr_update/{id}', [StockPurchaseRequestController::class, 'so_spr_update'])->name('so_spr_update');
Route::put('authoriser_remarks_update/{id}', [StockPurchaseRequestController::class, 'authoriser_remarks_update'])->name('authoriser_remarks_update');

// authoriser stock purchase requests
Route::get('auth_spr_lists', [StockPurchaseRequestController::class, 'auth_spr_lists'])->name('auth_spr_lists');
Route::get('auth_spr_list_edit/{id}', [StockPurchaseRequestController::class, 'auth_spr_list_edit'])->name('auth_spr_list_edit');
Route::post('auth_spr_action', [StockPurchaseRequestController::class, 'auth_spr_action'])->name('auth_spr_action');
Route::get('auth_spr_list_view/{id}', [StockPurchaseRequestController::class, 'auth_spr_list_view'])->name('auth_spr_list_view');
Route::get('auth_spr_denied_status/{id}', [StockPurchaseRequestController::class, 'auth_spr_denied_status'])->name('auth_spr_denied_status');
Route::get('auth_spr_approved_status/{id}', [StockPurchaseRequestController::class, 'auth_spr_approved_status'])->name('auth_spr_approved_status');

// purchasing officer stock purchase requests
Route::get('po_spr_lists', [StockPurchaseRequestController::class, 'po_spr_lists'])->name('po_spr_lists');
Route::get('po_spr_list_edit/{id}', [StockPurchaseRequestController::class, 'po_spr_list_edit'])->name('po_spr_list_edit');       // taxes and levies routes 
Route::get('generate_spr_porder/{id}', [StockPurchaseRequestController::class, 'generate_spr_porder'])->name('generate_spr_porder');
Route::get('spr_purchase_order_draft/{id}', [StockPurchaseRequestController::class, 'spr_purchase_order_draft'])->name('spr_purchase_order_draft');
Route::put('spr_purchase_update/{id}', [StockPurchaseRequestController::class, 'spr_purchase_update'])->name('spr_purchase_update');
Route::put('spr_save_draft/{id}', [StockPurchaseRequestController::class, 'spr_save_draft'])->name('spr_save_draft');
Route::put('spr_purchase_update_row/{id}', [StockPurchaseRequestController::class, 'spr_purchase_update_row'])->name('spr_purchase_update_row');
Route::post('spr_porder_action', [StockPurchaseRequestController::class, 'spr_porder_action'])->name('spr_porder_action');
Route::get('spr_pos', [StockPurchaseRequestController::class, 'spr_pos'])->name('spr_pos');
Route::get('spr_pos_show/{id}', [StockPurchaseRequestController::class, 'spr_pos_show'])->name('spr_pos_show');
Route::get('spr_pos_edit/{id}', [StockPurchaseRequestController::class, 'spr_pos_edit'])->name('spr_pos_edit');
Route::delete('spr_pos_delete/{id}', [StockPurchaseRequestController::class, 'spr_pos_delete'])->name('spr_pos_delete');

Route::get('/send-bulk-email', [EmailController::class, 'showForm'])->name('send.bulk.email');
Route::post('/send-bulk-email', [EmailController::class, 'sendBulkEmail'])->name('send.bulk.email.submit');

Route::resource('taxes', TaxLeviesController::class);
Route::resource('levies', LevyController::class);
Route::resource('permissions', PermissionsController::class);
// total and taxes
Route::resource('total_taxes', TotalTaxController::class);
Route::get('ajax-autocomplete-tax', [TotalTaxController::class, 'getTax']);
Route::post('fetch_single_tax', [TotalTaxController::class, 'fetch_single_tax'])->name('fetch_single_tax');
Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Multi-tenancy routes - Super Admin only
Route::middleware(['auth'])->group(function () {
    // Super Admin Dashboard
    Route::prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\TenantController::class, 'dashboard'])->name('dashboard');
    });
    
    // Tenant management (Super Admin only)
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [App\Http\Controllers\TenantController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\TenantController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\TenantController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\TenantController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\TenantController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\TenantController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\TenantController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/create-admin', [App\Http\Controllers\TenantController::class, 'createTenantAdmin'])->name('create-admin');
        Route::post('/{id}/create-admin', [App\Http\Controllers\TenantController::class, 'storeTenantAdmin'])->name('store-admin');
    });

    // Tenant Admin routes
    Route::prefix('tenant-admin')->name('tenant-admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\TenantAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/settings', [App\Http\Controllers\TenantAdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\TenantAdminController::class, 'updateSettings'])->name('update-settings');
        
        // Site management
        Route::prefix('sites')->name('sites.')->group(function () {
            Route::get('/', [App\Http\Controllers\TenantAdminController::class, 'sites'])->name('index');
            Route::get('/create', [App\Http\Controllers\TenantAdminController::class, 'createSite'])->name('create');
            Route::post('/', [App\Http\Controllers\TenantAdminController::class, 'storeSite'])->name('store');
        });
        
        // User management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [App\Http\Controllers\TenantAdminController::class, 'users'])->name('index');
            Route::get('/create', [App\Http\Controllers\TenantAdminController::class, 'createUser'])->name('create');
            Route::post('/', [App\Http\Controllers\TenantAdminController::class, 'storeUser'])->name('store');
        });
    });
});

Route::resources([
    'roles' => RoleController::class,
    'users' => UserController::class,
    'employees' => EmployeeController::class,


]);
