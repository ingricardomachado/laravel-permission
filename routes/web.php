<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Auth::routes();

//Home
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');

//Img
Route::get('user_avatar/{id}', 'ImgController@showUserAvatar');
Route::get('user_signature/{id}', 'ImgController@showUserSignature');
Route::get('app_logo', 'ImgController@showAppLogo');
Route::get('subscriber_logo/{id}', 'ImgController@showSubscriberLogo');
Route::get('subscriber_stamp/{id}', 'ImgController@showSubscriberStamp');

//Categories
Route::resource("categories","CategoryController");
Route::post('categories.datatable', 'CategoryController@datatable')->name('categories.datatable');
Route::get('categories.load/{id}', 'CategoryController@load')->name('categories.load');
Route::get('categories.status/{id}', 'CategoryController@status')->name('categories.status');
Route::get('categories.rpt_categories', 'CategoryController@rpt_categories')->name('categories.rpt_categories');

//Contacts
Route::resource("contacts", "ContactController");
Route::post('contacts.datatable', 'ContactController@datatable')->name('contacts.datatable');
Route::get('contacts.load/{id}', 'ContactController@load')->name('contacts.load');
Route::get('contacts.status/{id}', 'ContactController@status')->name('contacts.status');
Route::get('contacts.rpt_contacts', 'ContactController@rpt_contacts')->name('contacts.rpt_contacts');
Route::get('contacts.xls_contacts', 'ContactController@xls_contacts')->name('contacts.xls_contacts');


//Customers
Route::resource("customers","CustomerController");
Route::post('customers.datatable', 'CustomerController@datatable')->name('customers.datatable');
Route::get('customers.load/{id}', 'CustomerController@load')->name('customers.load');
Route::post('customers.load_contacts', 'CustomerController@load_contacts')->name('customers.load_contacts');

Route::get('customers.status/{id}', 'CustomerController@status')->name('customers.status');
Route::get('customers.revoke/{id}', 'CustomerController@revoke')->name('customers.revoke');
Route::get('customers.rpt_customers', 'CustomerController@rpt_customers')->name('customers.rpt_customers');
Route::get('customers.xls_customers', 'CustomerController@xls_customers')->name('customers.xls_customers');


//CustomerContact
//Route::resource("customer_contacts","CustomerContactController");
//Route::get('customer_contacts.load/{customer}', 'CustomerContactController@load')->name('customer_contacts.load');

//CustomerDocument
Route::resource("customer_documents","CustomerDocumentController");
Route::get('customer_documents.download/{id}', 'CustomerDocumentController@download')->name('customer_documents.download');

//Employees
Route::resource("employees", "EmployeeController");
Route::post('employees.datatable', 'EmployeeController@datatable')->name('employees.datatable');
Route::get('employees.load/{id}', 'EmployeeController@load')->name('employees.load');
Route::get('employees.status/{id}', 'EmployeeController@status')->name('employees.status');
Route::get('employees.rpt_employees', 'EmployeeController@rpt_employees')->name('employees.rpt_employees');
Route::get('employees.xls_employees', 'EmployeeController@xls_employees')->name('employees.xls_employees');

//Products
Route::resource("products","ProductController");
Route::post('products.datatable', 'ProductController@datatable')->name('products.datatable');
Route::get('products.load/{id}', 'ProductController@load')->name('products.load');
Route::get('products.status/{id}', 'ProductController@status')->name('products.status');
Route::get('products.rpt_products', 'ProductController@rpt_products')->name('products.rpt_products');
Route::get('products.xls_products', 'ProductController@xls_products')->name('products.xls_products');
Route::get('products.gallery/{id}', 'ProductController@gallery')->name('products.gallery');


//ProductDocument
Route::resource("product_documents","ProductDocumentController");
Route::get('product_documents.download/{id}', 'ProductDocumentController@download')->name('product_documents.download');


//ProductPhoto
Route::resource("product_photo","ProductPhotoController");
Route::get('product_photo.load/{id}', 'ProductPhotoController@load')->name('product_photo.load');
Route::get('product_photo_thumbnail/{id}', 'ProductPhotoController@thumbnail')->name('product_photo_thumbnail');


//Purchases
Route::resource('purchases', 'PurchaseController');
Route::post('purchases.datatable', 'PurchaseController@datatable')->name('purchases.datatable');
Route::get('purchases.load/{id}/{type}', 'PurchaseController@load')->name('purchases.load');
Route::post('purchases.load_items', 'PurchaseController@load_items')->name('purchases.load_items');
Route::get('purchases.rpt_purchase/{id}', 'PurchaseController@rpt_purchase')->name('purchases.rpt_purchase');
Route::get('purchases.download_purchase/{id}', 'PurchaseController@download_purchase')->name('purchases.download_purchase');
Route::get('purchases.load_send_modal/{id}', 'PurchaseController@load_send_modal')->name('purchases.load_send_modal');
Route::post('purchases.send_email/{id}/{to}', 'PurchaseController@send_email')->name('purchases.send_email');
Route::get('purchases.load_convert_modal/{id}', 'PurchaseController@load_convert_modal')->name('purchases.load_convert_modal');
Route::post('purchases.convert/{id}', 'PurchaseController@convert')->name('purchases.convert');

Route::get('purchases.settings', 'PurchaseController@settings')->name('purchases.settings');
Route::post('purchases.update_settings/{id}', 'PurchaseController@update_settings')->name('purchases.update_settings');

Route::get('purchases/{id}/products', 'PurchaseController@products');


//Orders
Route::get('orders', 'PurchaseController@orders')->name('orders');


//Sales
Route::resource('sales', 'SaleController');
Route::post('sales.datatable', 'SaleController@datatable')->name('sales.datatable');
Route::get('sales.load/{id}/{type}', 'SaleController@load')->name('sales.load');
Route::post('sales.load_items', 'SaleController@load_items')->name('sales.load_items');
Route::get('sales.rpt_sale/{id}', 'SaleController@rpt_sale')->name('sales.rpt_sale');
Route::get('sales.download_sale/{id}', 'SaleController@download_sale')->name('sales.download_sale');
Route::get('sales.load_send_modal/{id}', 'SaleController@load_send_modal')->name('sales.load_send_modal');
Route::post('sales.send_email/{id}/{to}', 'SaleController@send_email')->name('sales.send_email');
Route::get('sales.load_convert_modal/{id}', 'SaleController@load_convert_modal')->name('sales.load_convert_modal');
Route::post('sales.convert/{id}', 'SaleController@convert')->name('sales.convert');

Route::get('sales.settings', 'SaleController@settings')->name('sales.settings');
Route::post('sales.update_settings/{id}', 'SaleController@update_settings')->name('sales.update_settings');
Route::get('sales.rpt_sales/{type}', 'SaleController@rpt_sales')->name('sales.rpt_sales');
Route::get('sales.xls_sales/{type}', 'SaleController@xls_sales')->name('sales.xls_sales');


//Budgets
Route::get('budgets', 'SaleController@budgets')->name('budgets');

//Photos
Route::get('photo/{id}', 'PhotoController@photo');
Route::get('photo_thumbnail/{id}', 'PhotoController@thumbnail');

//Receivables
Route::resource("receivables","ReceivableController");
Route::post('receivables.datatable', 'ReceivableController@datatable')->name('receivables.datatable');
Route::get('receivables.load/{id}', 'ReceivableController@load')->name('receivables.load');
Route::get('receivables.status/{id}', 'ReceivableController@status')->name('receivables.status');
Route::get('receivables.rpt_receivables', 'ReceivableController@rpt_receivables')->name('receivables.rpt_receivables');
Route::get('receivables.xls_receivables', 'ReceivableController@xls_receivables')->name('receivables.xls_receivables');

//Services
Route::resource("services","ServiceController");
Route::post('services.datatable', 'ServiceController@datatable')->name('services.datatable');
Route::get('services.load/{id}', 'ServiceController@load')->name('services.load');
Route::get('services.status/{id}', 'ServiceController@status')->name('services.status');
Route::get('services.rpt_services', 'ServiceController@rpt_services')->name('services.rpt_services');
Route::get('services.xls_services', 'ServiceController@xls_services')->name('services.xls_services');


//ServiceOrders
Route::resource("service_orders","ServiceOrderController");
Route::post('service_orders.datatable', 'ServiceOrderController@datatable')->name('service_orders.datatable');
Route::get('service_orders.download_file/{id}', ['as' => 'service_orders.download_file', 'uses' => 'ServiceOrderController@download_file']);
Route::get('service_orders.rpt_service_orders', 'ServiceOrderController@rpt_service_orders')->name('service_orders.rpt_service_orders');
Route::get('service_orders.xls_service_orders', 'ServiceOrderController@xls_service_orders')->name('service_orders.xls_service_orders');


//Roles
//Route::get('sync_permissions', 'RoleController@sync_permissions')->name('sync_permissions');
//Route::get('assign_role', 'RoleController@assign_role')->name('assign_role');


//Route::get('settings.app', ['as' => 'settings.app', 'uses' => 'SettingController@app']);
Route::post('settings.update_app', ['as' => 'settings.update_app', 'uses' => 'SettingController@update_app']);

//Setting
Route::resource("settings","SettingController");
Route::get('settings.app', 'SettingController@app')->name('settings.app');

//Subscribers
Route::resource("subscribers","SubscriberController");
Route::post('subscribers.datatable', 'SubscriberController@datatable')->name('subscribers.datatable');
Route::get('subscribers.load/{id}', 'SubscriberController@load')->name('subscribers.load');
Route::get('subscribers.status/{id}', 'SubscriberController@status')->name('subscribers.status');
Route::get('subscribers.index_demo', 'SubscriberController@index_demo')->name('subscribers.index_demo');
Route::post('subscribers.demo/{id}', 'SubscriberController@demo')->name('subscribers.demo');
Route::get('subscribers.revoke/{id}', 'SubscriberController@revoke')->name('subscribers.revoke');
Route::get('subscribers.rpt_subscribers/{demo}', 'SubscriberController@rpt_subscribers')->name('subscribers.rpt_subscribers');
Route::get('subscribers.manage/{id}', 'SubscriberController@manage')->name('subscribers.manage');
Route::post('subscribers.return_sam', ['as' => 'subscribers.return_sam', 'uses' => 'SubscriberController@return_sam']);
Route::post('subscribers.full_register/{id}', 'SubscriberController@full_register')->name('subscribers.full_register');

Route::get('subscribers/{id}/customers', 'SubscriberController@customers');
Route::get('subscribers/{id}/suppliers', 'SubscriberController@suppliers');


//Suppliers
Route::resource("suppliers", "SupplierController");
Route::post('suppliers.datatable', 'SupplierController@datatable')->name('suppliers.datatable');
Route::get('suppliers.load/{id}', 'SupplierController@load')->name('suppliers.load');
Route::post('suppliers.load_contacts', 'SupplierController@load_contacts')->name('suppliers.load_contacts');
Route::get('suppliers.status/{id}', 'SupplierController@status')->name('suppliers.status');
Route::get('suppliers.rpt_suppliers', 'SupplierController@rpt_suppliers')->name('suppliers.rpt_suppliers');
Route::get('suppliers.xls_suppliers', 'SupplierController@xls_suppliers')->name('suppliers.xls_suppliers');


//SupplierContact
Route::resource("supplier_contacts","SupplierContactController");
Route::get('supplier_contacts.load/{supplier}/{id}', 'SupplierContactController@load')->name('supplier_contacts.load');

//SupplierDocument
Route::resource("supplier_documents","SupplierDocumentController");
Route::get('supplier_documents.download/{id}', 'SupplierDocumentController@download')->name('supplier_documents.download');

//Targets
Route::resource("targets","TargetController");
Route::post('targets.datatable', 'TargetController@datatable')->name('targets.datatable');
Route::get('targets.load/{id}', 'TargetController@load')->name('targets.load');
Route::get('targets.status/{id}', 'TargetController@status')->name('targets.status');
Route::get('targets.rpt_targets', 'TargetController@rpt_targets')->name('targets.rpt_targets');

//Tool
Route::get('countries/{id}/states', 'ToolController@states');
Route::post('verify_email', 'ToolController@verify_email');
Route::get('products_services', 'ToolController@products_services');


//Units
Route::resource("units","UnitController");
Route::post('units.datatable', 'UnitController@datatable')->name('units.datatable');
Route::get('units.load/{id}', 'UnitController@load')->name('units.load');
Route::get('units.status/{id}', 'UnitController@status')->name('units.status');
Route::get('units.rpt_units', 'UnitController@rpt_units')->name('units.rpt_units');

//Fix
Route::get('create_roles', 'FixController@create_roles');
Route::get('assign_roles', 'FixController@assign_roles');
Route::get('test_methods/{id}', 'FixController@test_methods');
Route::get('read_projects', 'FixController@read_projects');



//Socialite
Route::get('login/github', 'Auth\LoginController@redirectToProvider');
Route::get('login/github/callback', 'Auth\LoginController@handleProviderCallback');
