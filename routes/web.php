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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/boss', function () {
    return 'you are the boss';
})->middleware(['auth', 'auth.boss'])->name('boss');

Route::namespace('Boss')->prefix('boss')->middleware(['auth', 'auth.boss'])->name('boss.')->group(function () {
    Route::resource('/users', 'UserController', [
        'except' => ['show', 'create', 'store'],
    ]);
});
