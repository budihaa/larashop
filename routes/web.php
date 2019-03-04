<?php
Auth::routes();

# Disable register routes, redirect to login when someone accessing it.
Route::match(['GET', 'POST'], '/register', function() {
    return redirect('/login');
})->name('register');

##################################### Extra Routes #####################################
# Categories
Route::get('/categories/trash', 'CategoryController@trash')->name('categories.trash');
Route::get('/categories/{id}/restore', 'CategoryController@restore')->name('categories.restore');
Route::delete('/categories/{id}/permanent-delete', 'CategoryController@permanentDelete')->name('categories.permanent-delete');
Route::get('/ajax/categories/search', 'CategoryController@ajaxSearch')->name('categories.ajaxSearch'); # Ajax select2

# Books
Route::get('/books/trash', 'BookController@trash')->name('books.trash');
Route::post('/books/{id}/restore', 'BookController@restore')->name('books.restore');
Route::delete('/books/{id}/permanent-delete', 'BookController@permanentDelete')->name('books.permanent-delete');

################################## END Extra Routes ####################################

################################# Resource Controller #################################
Route::resource('users', 'UserController');
Route::resource('categories', 'CategoryController');
Route::resource('books', 'BookController');
############################### END Resource Controller ###############################

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/home', 'HomeController@index')->name('home');
