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
    // Invoices routes
    Route::resource('invoices', 'InvoiceController');
    Route::get('invoices/edit/{id}', 'InvoiceController@edit');

    // Sections routes
    Route::resource('sections', 'SectionController');
    Route::resource('products', 'ProductController');


    // Attachments routes
    Route::post('attachments/destroy', 'AttachmentsInvoiceController@destroy');
    Route::post('attachments/store', 'AttachmentsInvoiceController@store');
    Route::get('attachments/{invoice_number}/{file_name}', 'AttachmentsInvoiceController@show');
    Route::get('attachments/download/{invoice_number}/{file_name}', 'AttachmentsInvoiceController@download');
});


// Admin handling
Route::get('/{page}', 'AdminController@index');
