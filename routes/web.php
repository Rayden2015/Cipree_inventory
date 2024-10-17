<?php

use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CompanyController;

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
    $properties = Property::latest()->limit(6)->get();
    return view('index', compact('properties'));
});

Auth::routes();

Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/index', [App\Http\Controllers\PagesController::class, 'index'])->name('pages.index');
Route::get('/about', [App\Http\Controllers\PagesController::class, 'about'])->name('pages.about');
Route::get('/property', [App\Http\Controllers\PagesController::class, 'property'])->name('pages.property');
Route::get('/blogs', [App\Http\Controllers\PagesController::class, 'blogs'])->name('pages.blogs');
Route::get('/contact', [App\Http\Controllers\PagesController::class, 'contact'])->name('pages.contact');
Route::get('/services', [App\Http\Controllers\PagesController::class, 'services'])->name('pages.services');
Route::get('/show/{id}', [App\Http\Controllers\PropertyController::class, 'show'])->name('property.show');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/store', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
// Route::get('/index',[App\Http\Controllers\PropertyController::class, 'index'])->name('property.index');

Route::middleware('auth')->group(function () {
    Route::resource('users',UserController::class);
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('blog', [App\Http\Controllers\BlogController::class, 'allblogs'])->name('blog.allblogs');
    // Route::get('blog',[App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/edit', [App\Http\Controllers\BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/update/{id}', [App\Http\Controllers\BlogController::class, 'update'])->name('blog.update');
    Route::post('/blog/store', [App\Http\Controllers\BlogController::class, 'store'])->name('blog.store');
    Route::delete('/blog/destroy/{id}', [App\Http\Controllers\BlogController::class, 'destroy'])->name('blog.destroy');
    Route::get('/blog/create', [App\Http\Controllers\BlogController::class, 'create'])->name('blog.create');

    Route::get('/property/edit/{id}', [App\Http\Controllers\PropertyController::class, 'edit'])->name('property.edit');
    Route::put('/update/{id}', [App\Http\Controllers\PropertyController::class, 'update'])->name('property.update');
    Route::post('/property/store', [App\Http\Controllers\PropertyController::class, 'store'])->name('property.store');
    Route::delete('/property/destroy/{id}', [App\Http\Controllers\PropertyController::class, 'destroy'])->name('property.destroy');
    Route::delete('/property/deletecover/{id}', [App\Http\Controllers\PropertyController::class, 'deletecover'])->name('property.deletecover');
    Route::get('/property/create', [App\Http\Controllers\PropertyController::class, 'create'])->name('property.create');
    Route::get('allproperties', [App\Http\Controllers\PropertyController::class, 'allproperties'])->name('property.allproperties');
    Route::post('/fetch-states', [CountryController::class, 'fetchState']);
});
