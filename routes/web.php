<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LevyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartsController;
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
use App\Http\Controllers\TaxLeviesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthoriserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\StoreRequestController;
use App\Http\Controllers\EmailController;
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
Route::get('search_users', [\App\Http\Controllers\UserController::class, 'searchUsers'])->name('search.users');
Route::resource('company', CompanyController::class);
Route::resource('suppliers', SupplierController::class);
Route::get('supplier_search', [\App\Http\Controllers\SupplierController::class, 'supplier_search'])->name('supplier_search');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

//myaccount routes
Route::get('myaccounts', [\App\Http\Controllers\MyAccountController::class, 'index'])->name('myaccounts.index');
Route::put('myaccounts/update/{id}', [\App\Http\Controllers\MyAccountController::class, 'update'])->name('myaccounts.update');
Route::put('myaccounts/changepassword', [\App\Http\Controllers\MyAccountController::class, 'changepassword'])->name('myaccounts.changepassword');

//item
Route::resource('items', ItemController::class);
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
Route::post('inventory_action', [\App\Http\Controllers\InventoryController::class, 'inventory_action'])->name('inventories.action');
Route::post('update_inventory_item/{id}', [\App\Http\Controllers\InventoryController::class, 'update_inventory_item'])->name('inventories.update_inventory_item');
Route::get('ajax-autocomplete-category', [InventoryController::class, 'selectCategory']);
Route::get('ajax-autocomplete-deliveredby', [InventoryController::class, 'selectDeliveredBy']);
Route::get('inventory_search', [\App\Http\Controllers\InventoryController::class, 'inventory_search'])->name('inventory_search');
Route::get('out_of_stock_search', [\App\Http\Controllers\InventoryController::class, 'out_of_stock_search'])->name('out_of_stock_search');
Route::get('inventory_home_search', [\App\Http\Controllers\InventoryController::class, 'inventory_home_search'])->name('inventory_home_search');
Route::get('inventory_history_search', [\App\Http\Controllers\InventoryController::class, 'inventory_history_search'])->name('inventory_history_search');
Route::get('inventory_history_date_search', [\App\Http\Controllers\InventoryController::class, 'inventory_history_date_search'])->name('inventory_history_date_search');
Route::get('out_of_stock', [\App\Http\Controllers\InventoryController::class, 'out_of_stock'])->name('inventories.out_of_stock');

Route::get('ajax-autocomplete-item', [InventoryController::class, 'getItem']);
Route::get('ajax-autocomplete-items', [InventoryController::class, 'getItems']);
Route::get('ajax-autocomplete-location', [InventoryController::class, 'getLocation']);
Route::get('ajax-autocomplete-selectlocation', [InventoryController::class, 'selectLocation']);
Route::get('inventory_item_history', [\App\Http\Controllers\InventoryController::class, 'inventory_item_history'])->name('inventories.inventory_item_history');
Route::get('inventory_history_show/{id}', [\App\Http\Controllers\InventoryController::class, 'inventory_history_show'])->name('inventories.inventory_history_show');
Route::get('inventory_history_edit/{id}', [\App\Http\Controllers\InventoryController::class, 'inventory_history_edit'])->name('inventories.inventory_history_edit');
Route::put('inventory_history_update/{id}', [\App\Http\Controllers\InventoryController::class, 'inventory_history_update'])->name('inventories.inventory_history_update');
Route::post('update_inventory_history/{id}', [\App\Http\Controllers\InventoryController::class, 'update_inventory_history'])->name('inventories.update_inventory_history');
Route::delete('inventory_history_destroy/{id}', [\App\Http\Controllers\InventoryController::class, 'inventory_history_destroy'])->name('inventories.inventory_history_destroy');
Route::get('generateinventoryPDF/{id}', [\App\Http\Controllers\InventoryController::class, 'generateinventoryPDF'])->name('inventories.generateinventoryPDF');
Route::post('check_waybill', [\App\Http\Controllers\InventoryController::class, 'check_waybill'])->name('check_waybill');
Route::post('check_po_number', [\App\Http\Controllers\InventoryController::class, 'check_po_number'])->name('check_po_number');
Route::post('check_invoice_number', [\App\Http\Controllers\InventoryController::class, 'check_invoice_number'])->name('check_invoice_number');
// Route::post('/check_superior',['uses'=>'PagesController@checkEmail']);
// Route::post('sendProductNotification', [UserController::class, 'sendProductNotification'])->name('stores.sendproductnotification');
//categories
Route::resource('categories', CategoryController::class);
// sites
Route::resource('sites', SiteController::class);
// endusers
Route::resource('endusers', EnduserController::class);
Route::get('endusersearch', [\App\Http\Controllers\EnduserController::class, 'search'])->name('endusers.search');
Route::get('endusersort', [\App\Http\Controllers\EnduserController::class, 'endusersort'])->name('endusersort');
Route::get('enduser_show/{id}', [\App\Http\Controllers\EnduserController::class, 'show'])->name('endusers.show');
// purchases routes
Route::resource('purchases', PurchaseController::class);
Route::get('generate_order/{id}', [\App\Http\Controllers\PurchaseController::class, 'generate_order'])->name('purchases.generate_order');
Route::get('purchase_order_draft/{id}', [\App\Http\Controllers\PurchaseController::class, 'purchase_order_draft'])->name('purchases.purchase_order_draft');
Route::post('savelevytax_porder/{id}', [\App\Http\Controllers\PurchaseController::class, 'savelevytax_porder'])->name('savelevytax_porder');
Route::get('purchase_list', [\App\Http\Controllers\PurchaseController::class, 'purchase_list'])->name('purchases.purchase_list');
Route::get('purchase_edit/{id}', [\App\Http\Controllers\PurchaseController::class, 'purchase_edit'])->name('purchases.purchase_edit');
Route::put('purchase_update/{id}', [\App\Http\Controllers\PurchaseController::class, 'purchase_update'])->name('purchases.purchase_update');
Route::put('purchase_update_row/{id}', [\App\Http\Controllers\PurchaseController::class, 'purchase_update_row'])->name('purchases.purchase_update_row');
Route::delete('purchase_destroy/{id}', [\App\Http\Controllers\PurchaseController::class, 'purchase_destroy'])->name('purchases.purchase_destroy');
Route::get('showlist/{id}', [\App\Http\Controllers\PurchaseController::class, 'showlist'])->name('purchases.showlist');
Route::get('editlist/{id}', [\App\Http\Controllers\PurchaseController::class, 'editlist'])->name('purchases.editlist');
Route::post('purchaselist_update', [\App\Http\Controllers\PurchaseController::class, 'purchaselist_update'])->name('purchases.purchaselist_update');
Route::post('action', [\App\Http\Controllers\PurchaseController::class, 'action'])->name('purchases.action');
Route::get('requested', [\App\Http\Controllers\PurchaseController::class, 'requested'])->name('purchases.requested');
Route::get('initiated', [\App\Http\Controllers\PurchaseController::class, 'initiated'])->name('purchases.initiated');
Route::get('approved', [\App\Http\Controllers\PurchaseController::class, 'approved'])->name('purchases.approved');
Route::get('ordered', [\App\Http\Controllers\PurchaseController::class, 'ordered'])->name('purchases.ordered');
Route::get('delivered', [\App\Http\Controllers\PurchaseController::class, 'delivered'])->name('purchases.delivered');

Route::get('req_all', [\App\Http\Controllers\PurchaseController::class, 'req_all'])->name('purchases.req_all');
Route::get('all_requests', [\App\Http\Controllers\PurchaseController::class, 'all_requests'])->name('purchases.all_requests');
Route::get('all_initiates', [\App\Http\Controllers\PurchaseController::class, 'all_initiates'])->name('purchases.all_initiates');
Route::get('all_approves', [\App\Http\Controllers\PurchaseController::class, 'all_approves'])->name('purchases.all_approves');
Route::get('all_orders', [\App\Http\Controllers\PurchaseController::class, 'all_orders'])->name('purchases.all_orders');
Route::get('all_delivers', [\App\Http\Controllers\PurchaseController::class, 'all_delivers'])->name('purchases.all_delivers');
Route::get('generatePDF/{id}', [\App\Http\Controllers\PurchaseController::class, 'generatePDF'])->name('purchases.generatePDF');
Route::get('generatePurchaseOrderPDF/{id}', [\App\Http\Controllers\PurchaseController::class, 'generatePurchaseOrderPDF'])->name('purchases.generatePurchaseOrderPDF');
Route::get('ajax-autocomplete-part', [PurchaseController::class, 'selectPart']);
Route::get('ajax-autocomplete-search', [PurchaseController::class, 'selectSearch']);
Route::get('ajax-autocomplete-enduser', [PurchaseController::class, 'selectEnduser']);
Route::get('ajax-autocomplete-requester', [PurchaseController::class, 'selectRequester']);
Route::get('ajax-autocomplete-tax', [PurchaseController::class, 'selectTax']);
Route::get('ajax-autocomplete-levy', [PurchaseController::class, 'selectLevy']);
Route::get('po_all_requests', [\App\Http\Controllers\PurchaseController::class, 'po_all_requests'])->name('purchases.po_all_requests');
Route::put('save_draft/{id}', [\App\Http\Controllers\PurchaseController::class, 'save_draft'])->name('save_draft');
Route::get('drafts', [\App\Http\Controllers\PurchaseController::class, 'drafts'])->name('purchases.drafts');


//storerequest routes
Route::get('request_search', [\App\Http\Controllers\StoreRequestController::class, 'request_search'])->name('stores.request_search');
Route::get('requester_search', [\App\Http\Controllers\StoreRequestController::class, 'requester_search'])->name('stores.requester_search');
Route::get('ajax-autocomplete-locations', [StoreRequestController::class, 'selectLocations']);
Route::get('cart', [StoreRequestController::class, 'cart'])->name('cart');
Route::get('add-to-cart/{id}', [StoreRequestController::class, 'addToCart'])->name('add.to.cart');
Route::patch('update-cart', [StoreRequestController::class, 'update'])->name('update.cart');
Route::delete('remove-from-cart', [StoreRequestController::class, 'remove'])->name('remove.from.cart');
Route::delete('sorders/destroy/{id}', [StoreRequestController::class, 'destroy'])->name('sorders.destroy');
Route::delete('requester_store_delete/{id}', [\App\Http\Controllers\StoreRequestController::class, 'requester_store_delete'])->name('stores.requester_store_delete');
Route::post('sorders/store/', [\App\Http\Controllers\StoreRequestController::class, 'store'])->name('sorders.store');
Route::get('store_lists/', [\App\Http\Controllers\StoreRequestController::class, 'store_lists'])->name('sorders.store_lists');
Route::get('store_list_view/{id}', [\App\Http\Controllers\StoreRequestController::class, 'store_list_view'])->name('sorders.store_list_view');
Route::get('requester_store_list_view/{id}', [\App\Http\Controllers\StoreRequestController::class, 'requester_store_list_view'])->name('sorders.requester_store_list_view');
Route::get('generatesorderPDF/{id}', [\App\Http\Controllers\StoreRequestController::class, 'generatesorderPDF'])->name('sorders.generatesorderPDF');
Route::get('authoriser_store_list_view_dash/{id}', [\App\Http\Controllers\StoreRequestController::class, 'authoriser_store_list_view_dash'])->name('sorders.authoriser_store_list_view_dash');
Route::get('store_list_edit/{id}', [\App\Http\Controllers\StoreRequestController::class, 'store_list_edit'])->name('sorders.store_list_edit');
Route::put('store_list_update/{id}', [\App\Http\Controllers\StoreRequestController::class, 'store_list_update'])->name('stores.store_list_update');
Route::put('sorder_update/{id}', [\App\Http\Controllers\StoreRequestController::class, 'sorder_update'])->name('stores.sorder_update');
Route::put('requester_sorder_update/{id}', [\App\Http\Controllers\StoreRequestController::class, 'requester_sorder_update'])->name('stores.requester_sorder_update');
Route::post('stores_action', [\App\Http\Controllers\StoreRequestController::class, 'stores_action'])->name('stores.action');
Route::get('stores/approved_status/{id}', [\App\Http\Controllers\StoreRequestController::class, 'approved_status'])->name('stores.approved_status');
Route::get('stores/depart_auth_approved_status/{id}', [\App\Http\Controllers\StoreRequestController::class, 'depart_auth_approved_status'])->name('stores.depart_auth_approved_status');
Route::get('stores/denied_status/{id}', [\App\Http\Controllers\StoreRequestController::class, 'denied_status'])->name('stores.denied_status');
Route::get('stores/depart_auth_denied_status/{id}', [\App\Http\Controllers\StoreRequestController::class, 'depart_auth_denied_status'])->name('stores.depart_auth_denied_status');
Route::get('store_officer_lists', [\App\Http\Controllers\StoreRequestController::class, 'store_officer_lists'])->name('stores.store_officer_lists');
Route::get('received_history_page', [\App\Http\Controllers\StoreRequestController::class, 'received_history_page'])->name('stores.received_history_page');

Route::get('store_officer_list_search', [\App\Http\Controllers\StoreRequestController::class, 'store_officer_list_search'])->name('stores.store_officer_list_search');
Route::get('store_requester_lists', [\App\Http\Controllers\StoreRequestController::class, 'store_requester_lists'])->name('stores.store_requester_lists');
Route::get('store_officer_edit/{id}', [\App\Http\Controllers\StoreRequestController::class, 'store_officer_edit'])->name('stores.store_officer_edit');
Route::put('update_manual_remarks/{id}', [\App\Http\Controllers\StoreRequestController::class, 'update_manual_remarks'])->name('stores.update_manual_remarks');
Route::get('requester_edit/{id}', [\App\Http\Controllers\StoreRequestController::class, 'requester_edit'])->name('stores.requester_edit');
Route::put('store_officer_update/{id}', [\App\Http\Controllers\StoreRequestController::class, 'store_officer_update'])->name('stores.store_officer_update');
Route::put('requester_store_update/{id}', [\App\Http\Controllers\StoreRequestController::class, 'requester_store_update'])->name('stores.requester_store_update');
Route::get('supply_history', [\App\Http\Controllers\StoreRequestController::class, 'supply_history'])->name('stores.supply_history');
Route::get('supply_history_search', [\App\Http\Controllers\StoreRequestController::class, 'supply_history_search'])->name('stores.supply_history_search');
Route::get('supply_history_search_item', [\App\Http\Controllers\StoreRequestController::class, 'supply_history_search_item'])->name('stores.supply_history_search_item');
Route::get('requester_store_lists', [\App\Http\Controllers\StoreRequestController::class, 'requester_store_lists'])->name('stores.requester_store_lists');
Route::get('fetch_single_enduser/{id}', [\App\Http\Controllers\StoreRequestController::class, 'fetch_single_enduser'])->name('fetch_single_enduser');
Route::get('fetch_single_enduser1/{id}', [\App\Http\Controllers\StoreRequestController::class, 'fetch_single_enduser1'])->name('fetch_single_enduser1');
Route::delete('sorderpart_delete/{id}', [\App\Http\Controllers\StoreRequestController::class, 'sorderpart_delete'])->name('sorderpart_delete');
Route::delete('/sorderpart/delete/{id}', [\App\Http\Controllers\StoreRequestController::class, 'deleteSorderPart'])->name('sorderpart_delete');


// authoriser module
Route::get('authorise_req_all', [\App\Http\Controllers\AuthoriserController::class, 'req_all'])->name('authorise.req_all');
Route::get('authorise_all_requests', [\App\Http\Controllers\AuthoriserController::class, 'all_requests'])->name('authorise.all_requests');
Route::get('authorise_all_initiates', [\App\Http\Controllers\AuthoriserController::class, 'all_initiates'])->name('authorise.all_initiates');
Route::get('authorise_all_approves', [\App\Http\Controllers\AuthoriserController::class, 'all_approves'])->name('authorise.all_approves');
Route::get('authorise_all_orders', [\App\Http\Controllers\AuthoriserController::class, 'all_orders'])->name('authorise.all_orders');
Route::get('authorise_all_delivers', [\App\Http\Controllers\AuthoriserController::class, 'all_delivers'])->name('authorise.all_delivers');
Route::get('approved_status/{id}', [\App\Http\Controllers\AuthoriserController::class, 'approved_status'])->name('authorise.approved_status');
Route::get('depart_auth_approved_status/{id}', [\App\Http\Controllers\AuthoriserController::class, 'depart_auth_approved_status'])->name('depart_auth_authorise.approved_status');
Route::get('denied_status/{id}', [\App\Http\Controllers\AuthoriserController::class, 'denied_status'])->name('authorise.denied_status');

Route::get('depart_auth_denied_status/{id}', [\App\Http\Controllers\AuthoriserController::class, 'depart_auth_denied_status'])->name('depart_auth_authorise.denied_status');
//order
Route::resource('orders', OrderController::class);
Route::post('orders_action/{id}', [\App\Http\Controllers\OrderController::class, 'orders_action'])->name('orders.action');
Route::get('orders/view/{id}', [\App\Http\Controllers\OrderController::class, 'view'])->name('orders.view');
Route::get('admincreate', [\App\Http\Controllers\OrderController::class, 'admincreate'])->name('orders.admincreate');
Route::post('orderstore', [\App\Http\Controllers\OrderController::class, 'orderstore'])->name('orders.orderstore');
Route::put('orders_update/{id}', [\App\Http\Controllers\OrderController::class, 'update'])->name('orders.update');
// notification
Route::get('read/{id}', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notification.read');

Route::post('fetch_single_product', [\App\Http\Controllers\InventoryController::class, 'fetch_single_product'])->name('fetch_single_product');

//SMS routes
Route::post('/sms/send/{to}/{content}', [SmsController::class, 'sendSms']);

//dashboard tab navigations
Route::get('pending_po_approvals', [\App\Http\Controllers\DashboardNavigationController::class, 'pending_po_approvals'])->name('dashboard.pending_po_approvals');
Route::get('approved_request', [\App\Http\Controllers\DashboardNavigationController::class, 'approved_request'])->name('dashboard.approved_request');
Route::get('approved_pos', [\App\Http\Controllers\DashboardNavigationController::class, 'approved_pos'])->name('dashboard.approved_pos');
Route::get('processed_request', [\App\Http\Controllers\DashboardNavigationController::class, 'processed_request'])->name('dashboard.processed_request');
Route::get('pending_request_approvals', [\App\Http\Controllers\DashboardNavigationController::class, 'pending_request_approvals'])->name('dashboard.pending_request_approvals');
Route::get('pending_stock_approvals', [\App\Http\Controllers\DashboardNavigationController::class, 'pending_stock_approvals'])->name('dashboard.pending_stock_approvals');
Route::get('processed_pos', [\App\Http\Controllers\DashboardNavigationController::class, 'processed_pos'])->name('dashboard.processed_pos');
Route::get('reorder_level', [\App\Http\Controllers\DashboardNavigationController::class, 'reorder_level'])->name('dashboard.reorder_level');
Route::get('out_of_stock', [\App\Http\Controllers\DashboardNavigationController::class, 'out_of_stock'])->name('dashboard.out_of_stock');
Route::get('out_of_stock_view/{id}', [\App\Http\Controllers\DashboardNavigationController::class, 'out_of_stock_view'])->name('dashboard.out_of_stock_view');
Route::get('low_stock_view/{id}', [\App\Http\Controllers\DashboardNavigationController::class, 'low_stock_view'])->name('dashboard.low_stock_view');
Route::get('stock_request_pending', [\App\Http\Controllers\DashboardNavigationController::class, 'stock_request_pending'])->name('dashboard.stock_request_pending');
Route::get('sofficer_stock_request_pending', [\App\Http\Controllers\DashboardNavigationController::class, 'sofficer_stock_request_pending'])->name('dashboard.sofficer_stock_request_pending');
Route::get('rfi_pending_approval', [\App\Http\Controllers\DashboardNavigationController::class, 'rfi_pending_approval'])->name('dashboard.rfi_pending_approval');
Route::get('rfi_approved_requests', [\App\Http\Controllers\DashboardNavigationController::class, 'rfi_approved_requests'])->name('dashboard.rfi_approved_requests');
Route::get('rfi_processed_requests', [\App\Http\Controllers\DashboardNavigationController::class, 'rfi_processed_requests'])->name('dashboard.rfi_processed_requests');
Route::get('rfi_denied', [\App\Http\Controllers\DashboardNavigationController::class, 'rfi_denied'])->name('dashboard.rfi_denied');
Route::get('po_total_value_of_approved_pos_mtd', [\App\Http\Controllers\DashboardNavigationController::class, 'po_total_value_of_approved_pos_mtd'])->name('dashboard.po_total_value_of_approved_pos_mtd');
Route::get('po_total_value_of_supplied_pos_mtd', [\App\Http\Controllers\DashboardNavigationController::class, 'po_total_value_of_supplied_pos_mtd'])->name('dashboard.po_total_value_of_supplied_pos_mtd');
Route::get('po_total_value_of_pending_pos_mtd', [\App\Http\Controllers\DashboardNavigationController::class, 'po_total_value_of_pending_pos_mtd'])->name('dashboard.po_total_value_of_pending_pos_mtd');
Route::get('po_approved_stock_requests', [\App\Http\Controllers\DashboardNavigationController::class, 'po_approved_stock_requests'])->name('dashboard.po_approved_stock_requests');
Route::get('po_approved_direct_requests', [\App\Http\Controllers\DashboardNavigationController::class, 'po_approved_direct_requests'])->name('dashboard.po_approved_direct_requests');
Route::get('po_approved_pos', [\App\Http\Controllers\DashboardNavigationController::class, 'po_approved_pos'])->name('dashboard.po_approved_pos');
Route::get('po_denied_requests', [\App\Http\Controllers\DashboardNavigationController::class, 'po_denied_requests'])->name('dashboard.po_denied_requests');
Route::get('active_user_accounts', [\App\Http\Controllers\DashboardNavigationController::class, 'active_user_accounts'])->name('dashboard.active_user_accounts');
Route::get('disabled_user_accounts', [\App\Http\Controllers\DashboardNavigationController::class, 'disabled_user_accounts'])->name('dashboard.disabled_user_accounts');
Route::get('active_endusers', [\App\Http\Controllers\DashboardNavigationController::class, 'active_endusers'])->name('dashboard.active_endusers');
Route::get('disabled_endusers', [\App\Http\Controllers\DashboardNavigationController::class, 'disabled_endusers'])->name('dashboard.disabled_endusers');
Route::get('departments', [\App\Http\Controllers\DashboardNavigationController::class, 'departments'])->name('dashboard.departments');
Route::get('sections', [\App\Http\Controllers\DashboardNavigationController::class, 'sections'])->name('dashboard.sections');
Route::get('dpr_pending_approval', [\App\Http\Controllers\DashboardNavigationController::class, 'dpr_pending_approval'])->name('dashboard.dpr_pending_approval');
Route::get('dpr_approved', [\App\Http\Controllers\DashboardNavigationController::class, 'dpr_approved'])->name('dashboard.dpr_approved');
Route::get('dpr_processed', [\App\Http\Controllers\DashboardNavigationController::class, 'dpr_processed'])->name('dashboard.dpr_processed');
Route::get('dpr_denied', [\App\Http\Controllers\DashboardNavigationController::class, 'dpr_denied'])->name('dashboard.dpr_denied');

Route::get('reorder_level_search', [\App\Http\Controllers\DashboardNavigationController::class, 'reorder_level_search'])->name('reorder_level_search');

Route::get('items_list_site', [\App\Http\Controllers\DashboardNavigationController::class, 'items_list_site'])->name('items_list_site');

// Route::post('feedback', 'FeedbackController@store')->name('feedback.store');

// Review controller routes
Route::get('reviews', [\App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
// Route::get('images', [\App\Http\Controllers\ReviewController::class, 'images'])->name('reviews.images');
Route::get('reviews/show/{id}', [\App\Http\Controllers\ReviewController::class, 'show'])->name('reviews.show');
Route::delete('reviews/destroy/{id}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
Route::put('reviews/markAsReviewed/{id}', [\App\Http\Controllers\ReviewController::class, 'markAsReviewed'])->name('reviews.markAsReviewed');

// monthly report
Route::get('monthlyreport', [\App\Http\Controllers\ReportController::class, 'monthlyreport'])->name('monthlyreport');

Route::get('/export-search-results', [\App\Http\Controllers\ExcelController::class, 'exportSearchResults'])->name('exportSearchResults');
Route::get('/export-items-list-site', [\App\Http\Controllers\ExcelController::class, 'exportItemsListSite'])->name('exportItemsListSite');

Route::get('download-items-list-site-pdf', [\App\Http\Controllers\PDFController::class, 'downloadItemsListPdf'])->name('downloadItemsListPdf');
// stock purchase requests 
Route::get('spr_lists', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_lists'])->name('spr_lists');
Route::get('spr_create', [\App\Http\Controllers\StockPurchaseRequestController::class, 'index'])->name('spr_create');
Route::get('stock_purchase_cart', [\App\Http\Controllers\StockPurchaseRequestController::class, 'stock_purchase_cart'])->name('stock_purchase_cart');
Route::get('addToStock/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'addToStock'])->name('addToStock');
Route::post('spr_store', [\App\Http\Controllers\StockPurchaseRequestController::class, 'store'])->name('spr_store');
Route::get('store_officer_spr_edit/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'store_officer_spr_edit'])->name('store_officer_spr_edit');
Route::patch('update-spr_cart', [StoreRequestController::class, 'update'])->name('update-spr_cart');
Route::put('store_officer_spr_update/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'store_officer_spr_update'])->name('store_officer_spr_update');
Route::put('so_spr_update/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'so_spr_update'])->name('so_spr_update');
Route::put('authoriser_remarks_update/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'authoriser_remarks_update'])->name('authoriser_remarks_update');

// authoriser stock purchase requests
Route::get('auth_spr_lists', [\App\Http\Controllers\StockPurchaseRequestController::class, 'auth_spr_lists'])->name('auth_spr_lists');
Route::get('auth_spr_list_edit/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'auth_spr_list_edit'])->name('auth_spr_list_edit');
Route::post('auth_spr_action', [\App\Http\Controllers\StockPurchaseRequestController::class, 'auth_spr_action'])->name('auth_spr_action');
Route::get('auth_spr_list_view/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'auth_spr_list_view'])->name('auth_spr_list_view');
Route::get('auth_spr_denied_status/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'auth_spr_denied_status'])->name('auth_spr_denied_status');
Route::get('auth_spr_approved_status/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'auth_spr_approved_status'])->name('auth_spr_approved_status');

// purchasing officer stock purchase requests
Route::get('po_spr_lists', [\App\Http\Controllers\StockPurchaseRequestController::class, 'po_spr_lists'])->name('po_spr_lists');
Route::get('po_spr_list_edit/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'po_spr_list_edit'])->name('po_spr_list_edit');       // taxes and levies routes 
Route::get('generate_spr_porder/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'generate_spr_porder'])->name('generate_spr_porder');
Route::get('spr_purchase_order_draft/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_purchase_order_draft'])->name('spr_purchase_order_draft');
Route::put('spr_purchase_update/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_purchase_update'])->name('spr_purchase_update');
Route::put('spr_save_draft/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_save_draft'])->name('spr_save_draft');
Route::put('spr_purchase_update_row/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_purchase_update_row'])->name('spr_purchase_update_row');
Route::post('spr_porder_action', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_porder_action'])->name('spr_porder_action');
Route::get('spr_pos', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_pos'])->name('spr_pos');
Route::get('spr_pos_show/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_pos_show'])->name('spr_pos_show');
Route::get('spr_pos_edit/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_pos_edit'])->name('spr_pos_edit');
Route::delete('spr_pos_delete/{id}', [\App\Http\Controllers\StockPurchaseRequestController::class, 'spr_pos_delete'])->name('spr_pos_delete');

Route::get('/send-bulk-email', [EmailController::class, 'showForm'])->name('send.bulk.email');
Route::post('/send-bulk-email', [EmailController::class, 'sendBulkEmail'])->name('send.bulk.email.submit');

Route::resource('taxes', TaxLeviesController::class);
Route::resource('levies', LevyController::class);
Route::resource('permissions', PermissionsController::class);
// total and taxes
Route::resource('total_taxes', TotalTaxController::class);
Route::get('ajax-autocomplete-tax', [TotalTaxController::class, 'getTax']);
Route::post('fetch_single_tax', [\App\Http\Controllers\TotalTaxController::class, 'fetch_single_tax'])->name('fetch_single_tax');
Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resources([
    'roles' => RoleController::class,
    'users' => UserController::class,
    'employees' => EmployeeController::class,


]);
