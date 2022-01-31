<?php

use Illuminate\Http\Request;
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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signUp');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});


//Categories
Route::resource("categories","CategoryController", array("as" => "api"))->middleware('auth:api');
Route::get('categories', 'CategoryController@categories');


//Countries
Route::resource("countries","CountryController", array("as" => "api"))->middleware('auth:api');
Route::get('countries/{id}/states', 'CountryController@states');

//Customers
Route::resource("customers","CustomerController", array("as" => "api"))->middleware('auth:api');
Route::get('customers', 'CustomerController@customers');
Route::get('customers/{id}/photos', 'CustomerController@photos');

//Photos
Route::resource("photos","PhotoController", array("as" => "api"))->middleware('auth:api');
Route::get('photos', 'PhotoController@photos');

//Products
Route::resource("products","ProductController", array("as" => "api"))->middleware('auth:api');
Route::get('products', 'ProductController@products');

//Receivables
Route::resource("receivables","ReceivableController", array("as" => "api"))->middleware('auth:api');
Route::get('receivables', 'ReceivableController@receivables');


//Sales
Route::resource("sales","SaleController", array("as" => "api"))->middleware('auth:api');

//ServiceOrder
Route::resource("service_orders","ServiceOrderController", array("as" => "api"))->middleware('auth:api');

//Services
Route::resource("services","ServiceController", array("as" => "api"))->middleware('auth:api');
Route::get('services', 'ServiceController@services');

//States
Route::resource("states","StateController", array("as" => "api"))->middleware('auth:api');

//Subscribers
Route::resource("subscribers","SubscriberController", array("as" => "api"))->middleware('auth:api');
Route::get('subscribers/{id}/customers', 'SubscriberController@customers');
Route::get('subscribers', 'SubscriberController@subscribers');
Route::get('subscribers/{id}/products', 'SubscriberController@products');
Route::get('subscribers/{id}/services', 'SubscriberController@services');
Route::get('subscribers/{id}/suppliers', 'SubscriberController@suppliers');
Route::get('subscribers/{id}/sales', 'SubscriberController@sales');

//Suppliers
Route::resource("suppliers","SupplierController", array("as" => "api"))->middleware('auth:api');
Route::get('suppliers', 'SupplierController@suppliers');

//Units
Route::resource("units","UnitController", array("as" => "api"))->middleware('auth:api');
Route::get('units', 'UnitController@units');

//Users
Route::post('users/{id}/signature', 'UserController@signature');
Route::post('users/{id}/avatar', 'UserController@avatar');

//Test
Route::post('test_request', 'ToolController@test_request');
Route::post('test_var_dump', 'ToolController@test_var_dump');



