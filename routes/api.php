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

Route::get('/', function () {
    return response()->json([
        'domain' => 'Book Car Api',
        'method' => 'GET',
    ], 200);
});
Route::post('/', function () {
    return response()->json([
        'domain' => 'Book Car Api',
        'method' => 'POST',
    ], 200);
});

Route::post('/v1/image/store', 'ImageController@store')->name('api.image.store');
Route::get('/v1/image/show/{id?}/{type?}', 'ImageController@show')->name('api.image.show');
Route::post('/v1/image/delete/{id?}', 'ImageController@delete')->name('api.image.delete');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::group(['prefix' => 'v1/customer'], function () {
//    Route::post('login', 'AuthController@authenticate')->name('api.customer.login');
//    Route::post('register', 'API/v1/AuthController@register')->name('api.customer.register');
//});

Route::namespace('Api\v1')->group(function (){

    Route::group(['prefix' => 'v1'], function () {

        //Master Data
        Route::group(['prefix' => 'data'], function () {
            Route::get('province', 'DataController@province')->name('api.data.province');
            Route::get('district', 'DataController@district')->name('api.data.district');
            Route::get('binhduong', 'DataController@districtBinhDuong')->name('api.data.districtBinhDuong');
            Route::get('other', 'DataController@other')->name('api.data.other');
            Route::get('type-car', 'DataController@typeCar')->name('api.data.typeCar');
            //Route::get('binhduong-all', 'DataController@districtBinhDuongAll')->name('api.data.districtBinhDuongAll');
        });

        // Customer
        Route::group(['prefix' => 'customer'], function () {

            // social
            Route::post('social', 'AuthController@social')->name('api.customer.social');

            Route::post('login', 'AuthController@authenticate')->name('api.customer.login');
            Route::post('register', 'AuthController@register')->name('api.customer.register');
            Route::post('activated', 'AuthController@activated')->name('api.customer.activated');
            Route::post('profile', 'AuthController@profile')->middleware('jwt')->name('api.customer.profile');
            Route::post('change-password', 'AuthController@changePassword')->middleware('jwt')->name('api.customer.profile');
            Route::post('reset-password', 'AuthController@resetPassword')->name('api.customer.reset_password');
            Route::post('password/reset', 'AuthController@resetPasswordByPhone')->name('api.customer.reset_password_by_phone');
        });
        Route::post('driver/login', 'AuthController@driverLogin')->name('api.driver.login');

        Route::group(['middleware' => 'jwt'], function () {
            Route::post('evaluate', 'EvaluateController@store')->name('api.evaluate.store');

            Route::post('device', 'DataController@device')->name('api.data.device');

            // Order
            Route::group(['prefix' => 'order'], function () {
                Route::post('money', 'OrderController@getPayment')->name('api.order.payment');
                Route::post('', 'OrderController@store')->name('api.order.store');
                Route::get('{id?}', 'OrderController@detail')->name('api.order.detail');
                Route::post('list', 'OrderController@listOrder')->name('api.order.list');
                Route::post('update/{id?}', 'OrderController@update')->name('api.order.update');
                Route::post('delete/{id?}', 'OrderController@delete')->name('api.order.delete');
            });

            // Driver
            Route::group(['prefix' => 'driver'], function () {
                Route::post('orders', 'DriverController@orders')->name('api.driver.orders');
                Route::put('order/{id?}', 'DriverController@statusOrder')->name('api.driver.orderStatus');
                Route::post('profile', 'AuthController@profile')->name('api.driver.info');
                Route::post('location', 'DriverController@location')->name('api.driver.location');
                Route::get('location/{id?}', 'DriverController@getLocation')->name('api.driver.getLocation');
                Route::post('/update/order/{id?}', 'OrderController@updateByDriver')->name('driver.update.order');
            });

            Route::post('msg', 'ChatController@sendMsg')->name('api.msg.sendMsg');
        });
        // Chat
        Route::group(['prefix' => 'chat'], function () {
            Route::post('user', 'ChatkitController@createUser')->name('api.chat.user');
            Route::get('contacts/{id?}', 'ChatkitController@getContact')->name('api.chat.getContact');
            Route::post('contacts', 'ChatkitController@postContact')->name('api.chat.postContact')->middleware('jwt');
            Route::post('token/{id?}', 'ChatkitController@getToken')->name('api.chat.getToken');
            Route::post('message/{id?}', 'ChatkitController@message')->name('api.chat.message');
            Route::post('get/message', 'ChatkitController@getMessage')->name('api.chat.getMessage');
        });
    });
});

Route::post('noti', 'Api\v1\DataController@noti')->name('api.data.noti');