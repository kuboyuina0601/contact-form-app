<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    // 仮ルート(admincontroller作成時まで)
    Route::get('/admin', function () {
        return view('admin.index', [
            'categories' => [],
            'tags' => \App\Models\Tag::all(),
            'contacts' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10), // お問い合わせ一覧の空データ
        ]);
    })->name('admin.index');
    //タグCRUDルート
    Route::resource('admin/tags',TagController::class)
    ->only(['store', 'edit', 'update', 'destroy'])
    ->names('admin.tags');
});