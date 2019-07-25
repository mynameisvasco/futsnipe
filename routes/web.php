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

//Auth routes
Auth::routes();

//Page routes
Route::get('/', function () {
    return redirect('/dashboard');
});
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/items', 'ItemsController@index')->name('items');
Route::get('/transactions', 'TransactionsController@index')->name('transactions');
Route::get('/configurations', 'ConfigurationsController@index')->name('configurations');
Route::get('/accounts', 'AccountsController@index')->name('accounts');

//Items routes
Route::post('/items/store', 'ItemsController@store')->name('items.store');
Route::get('/items/players', 'ItemsController@players')->name('items.players');
Route::get('/items/player_cards/{assetId}', 'ItemsController@playerCards')->name('items.player_cards');
Route::get('/items/{id}/delete', 'ItemsController@delete')->name('items.delete');
Route::get('/items/nationalities', 'ItemsController@nationalities')->name('items.nationalities');
Route::post('/items/card/generate', 'ItemsController@generateCard')->name('items.card.generate');

//Account Routes
Route::post('/accounts/new', 'AccountsController@store')->name('accounts.store');
Route::get('/accounts/{id}/refresh', 'AccountsController@refresh')->name('accounts.refresh');
Route::get('/accounts/{id}/stop', 'AccountsController@stop')->name('accounts.stop');

//Transactions

//Configurations
Route::post('/configurations/{id}/save', 'ConfigurationsController@update')->name('configurations.update');
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
