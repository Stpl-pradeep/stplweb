<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\BannersController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenusController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\DocumentController;

use App\Http\Controllers\Users\PageController;
use App\Http\Controllers\Users\BlogsController;
use App\Http\Controllers\Users\ContactController;
use App\Http\Controllers\Users\FrontServicesController;
use Illuminate\Support\Facades\Auth;

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

// Route::get('/', function () {
//     return view('home');
// });

Auth::routes();


Route::get('login', function () {
    return redirect('/');
})->name('login');
// Start user auth

Route::get('/', [HomeController::class, 'Home'])->name('home');



// End user auth

Route::group(['prefix' => 'admin'], function () {

    Route::group(['middleware' => 'admin.guest'], function () {
        Route::view('login', 'admin.login')->name('admin.login');
        Route::post('login', [AdminController::class, 'login'])->name('admin.auth');
    });

    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.home');
        Route::post('logout', [AdminController::class, 'logout'])->name('admin.logout');

        Route::get('change-password', [AdminController::class, 'changePassword'])->name('change.password');
        Route::post('update-change-password', [AdminController::class, 'updateChangePass'])->name('update.change.password');

        Route::post('pages/status', [PagesController::class, 'pagesstatus'])->name('pages.status');
        Route::post('menus/status', [MenusController::class, 'menustatus'])->name('menus.status');
        Route::post('banner/status', [BannersController::class, 'bannerstatus'])->name('banner.status');
        Route::post('blog/status', [BlogController::class, 'blogstatus'])->name('blog.status');
        Route::post('category/status', [CategoryController::class, 'Categorystatus'])->name('category.status');
       
        // Route::resource('/cards', CardController::class);

        // Start Menu search
        Route::get('search/menu/list', [MenusController::class, 'SearchMenuist'])->name('search.menu.list');
        // End menu search

        // Start Page search
        Route::get('search/pages/list', [PagesController::class, 'SearchPageist'])->name('search.page.list');
        // End Page search

        // Start blog search
        Route::get('search/blog/list', [BlogController::class, 'SearchBloglist'])->name('search.blog.list');
        // End blog search

        // Search Category
        Route::get('search/category', [CategoryController::class, 'SearchCategory'])->name('search.category');
        // Search Category

        //   Start Support Route
        Route::get('support/list', [UserController::class, 'supportContactList'])->name('support.list');
        // End Support Route

        // Start Service Route Status Update
        Route::post('service/status', [ServiceController::class, 'serviceStatus'])->name('service.status');
       // End Service Route Status Update   
        
        Route::resources([
            '/gallery' => GalleryController::class,
            '/document' => DocumentController::class,
            '/category' => CategoryController::class,
            '/service' => ServiceController::class,
            '/plans' => PlanController::class,
            '/pages' => PagesController::class,
            '/menus' => MenusController::class,
            '/banner' => BannersController::class,
            '/settings' => SiteSettingsController::class,
            '/blog' => BlogController::class,
        ]);


    });
});

// End Admin Route

Route::group(['middleware' => 'auth'], function () {

});

// Start main website Pages Route
Route::get('page/{slug}', [PageController::class, 'Pages'])->name('page');
Route::get('allgallery', [PageController::class, 'galleryPage'])->name('allgallery');
Route::get('alldocument', [PageController::class, 'documentPage'])->name('alldocument');
// End main website Pages Route

// Start BLogs Controller
Route::get('post/{slug?}', [BlogsController::class, 'BlogList'])->name('post');
Route::get('single/{slug}', [BlogsController::class, 'BlogDetail'])->name('blog.detail');
// End BLogs Controller

// Start Contact Route
Route::get('contact/support', [ContactController::class, 'supportContact'])->name('contact.support');
Route::get('documentddd', [ContactController::class, 'getDocument'])->name('document');
Route::post('save/contact/support', [ContactController::class, 'saveSupportContact'])->name('save.contact.support');
// End Contact Route

Route::resources([
     '/services' => FrontServicesController::class
]);