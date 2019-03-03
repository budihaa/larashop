<?php

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

Auth::routes();

# Disable register routes, redirect to login when someone accessing it.
Route::match(['GET', 'POST'], '/register', function() {
    return redirect('/login');
})->name('register');

# Recycle bin
Route::get('/categories/trash', 'CategoryController@trash')->name('categories.trash');
Route::get('/categories/{id}/restore', 'CategoryController@restore')->name('categories.restore');
Route::delete('/categories/{id}/permanent-delete', 'CategoryController@permanentDelete')->name('categories.permanent-delete');

# Resource Controller
Route::resource('users', 'UserController');
Route::resource('categories', 'CategoryController');
Route::resource('books', 'BookController');

# Ajax search select2 for categories
Route::get('/ajax/categories/search', 'CategoryController@ajaxSearch')->name('categories.ajaxSearch');

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/home', 'HomeController@index')->name('home');
