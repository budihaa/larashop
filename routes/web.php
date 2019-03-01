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
Route::match(['GET', 'POST'], '/register', function(){
    return redirect('/login');
})->name('register');

# Resource Controller
Route::resource('users', 'UserController');

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/home', 'HomeController@index')->name('home');
