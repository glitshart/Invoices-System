<?php

use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('auth.login');
});

// Auth::routes(['register' => false]);
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Separated routes
    Route::resource('sections', 'SectionController');
    Route::resource('products', 'ProductController');

    // Invoices routes
    Route::group(['prefix' => 'invoices'], function () {
        // Invoices routes
        Route::get('/', 'InvoiceController@index');
        Route::get('create', 'InvoiceController@create');
        Route::get('print/{id}', 'InvoiceController@print');
        Route::get('show/{id}', 'InvoiceController@show');
        Route::delete('destroy', 'InvoiceController@destroy');
        Route::post('store', 'InvoiceController@store');
        Route::get('paid', 'InvoiceController@paid');
        Route::get('unpaid', 'InvoiceController@unpaid');
        Route::get('partial', 'InvoiceController@partial');
        Route::get('edit/{id}', 'InvoiceController@edit');
        Route::get('status/{id}', 'InvoiceController@status')->name('invoices/status');
        Route::post('status_update/{id}', 'InvoiceController@status_update')->name('invoices/status_update');

        // Archives routes
        Route::get('archive', 'InvoiceController@archives');
        Route::patch('unarchive', 'InvoiceController@unarchive');
    });

    // Attachments routes
    Route::group(['prefix' => 'attachments'], function () {
        Route::post('/destroy', 'AttachmentsInvoiceController@destroy');
        Route::post('/store', 'AttachmentsInvoiceController@store');
        Route::get('/{invoice_number}/{file_name}', 'AttachmentsInvoiceController@show');
        Route::get('/download/{invoice_number}/{file_name}', 'AttachmentsInvoiceController@download');
    });
});

// Admin handling
Route::get('/{page}', 'AdminController@index');
