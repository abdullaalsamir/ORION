<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\BoardDirectorController;
use App\Http\Controllers\Admin\LeadershipController;
use App\Http\Controllers\Admin\ConcernController;
use App\Http\Controllers\Admin\VideoGalleryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\CsrController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\ConnectController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\SettingsController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin', 'no.cache'])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/menus', [MenuController::class, 'index'])->name('menus');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'delete'])->name('menus.delete');
        Route::post('/menus/update-order', [MenuController::class, 'updateOrder'])->name('menus.update-order');

        Route::get('/pages', [MenuController::class, 'pages'])->name('pages');
        Route::put('/pages/{menu}', [MenuController::class, 'updatePage'])->name('pages.update');

        Route::get('/banners', [BannerController::class, 'index'])->name('banners');
        Route::get('/banners/fetch/{menu}', [BannerController::class, 'getBanners'])->name('banners.fetch');
        Route::get('/banners/get-for-editor/{menu}', [BannerController::class, 'getBannersForEditor']);
        Route::post('/banners/upload/{menu}', [BannerController::class, 'store'])->name('banners.upload');
        Route::match(['post', 'put'], '/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{banner}', [BannerController::class, 'delete'])->name('banners.delete');

        Route::get('/sliders', [SliderController::class, 'index'])->name('sliders.index');
        Route::post('/sliders', [SliderController::class, 'store'])->name('sliders.store');
        Route::put('/sliders/{slider}', [SliderController::class, 'update'])->name('sliders.update');
        Route::delete('/sliders/{slider}', [SliderController::class, 'delete'])->name('sliders.delete');
        Route::post('/sliders/update-order', [SliderController::class, 'updateOrder'])->name('sliders.update-order');

        Route::prefix('director-actions')->name('directors.')->group(function () {
            Route::post('/store', [BoardDirectorController::class, 'store'])->name('store');
            Route::put('/{boardDirector}', [BoardDirectorController::class, 'update'])->name('update');
            Route::delete('/{boardDirector}', [BoardDirectorController::class, 'delete'])->name('delete');
            Route::post('/update-order', [BoardDirectorController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('leadership-actions')->name('leadership.')->group(function () {
            Route::post('/store', [LeadershipController::class, 'store'])->name('store');
            Route::put('/{leadership}', [LeadershipController::class, 'update'])->name('update');
            Route::delete('/{leadership}', [LeadershipController::class, 'delete'])->name('delete');
            Route::post('/update-order', [LeadershipController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('concern-actions')->name('concerns.')->group(function () {
            Route::get('/fetch/{menu}', [ConcernController::class, 'fetch'])->name('fetch');
            Route::post('/update-info/{menu}', [ConcernController::class, 'updateInfo'])->name('update-info');
            Route::post('/update-redirect/{menu}', [ConcernController::class, 'updateRedirect'])->name('update-redirect');
            Route::post('/update-description/{menu}', [ConcernController::class, 'updateDescription'])->name('update-description');
            Route::post('/upload-cover/{menu}', [ConcernController::class, 'uploadCover'])->name('upload-cover');
            Route::delete('/delete-cover/{menu}', [ConcernController::class, 'deleteCover'])->name('delete-cover');
            Route::post('/upload-gallery/{menu}', [ConcernController::class, 'uploadGallery'])->name('upload-gallery');
            Route::post('/replace-gallery/{concernGallery}', [ConcernController::class, 'replaceGallery'])->name('replace-gallery');
            Route::delete('/delete-gallery/{concernGallery}', [ConcernController::class, 'deleteGallery'])->name('delete-gallery');
            Route::post('/update-gallery-order', [ConcernController::class, 'updateGalleryOrder'])->name('update-gallery-order');
        });

        Route::prefix('video-gallery-actions')->name('video-gallery.')->group(function () {
            Route::post('/store', [VideoGalleryController::class, 'store'])->name('store');
            Route::put('/{videoGallery}', [VideoGalleryController::class, 'update'])->name('update');
            Route::delete('/{videoGallery}', [VideoGalleryController::class, 'delete'])->name('delete');
            Route::post('/update-order', [VideoGalleryController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('news-actions')->name('news.')->group(function () {
            Route::post('/store', [NewsController::class, 'store'])->name('store');
            Route::put('/{newsItem}', [NewsController::class, 'update'])->name('update');
            Route::delete('/{newsItem}', [NewsController::class, 'delete'])->name('delete');
            Route::post('/update-order', [NewsController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('csr-actions')->name('csr.')->group(function () {
            Route::post('/store', [CsrController::class, 'store'])->name('store');
            Route::put('/{csrItem}', [CsrController::class, 'update'])->name('update');
            Route::delete('/{csrItem}', [CsrController::class, 'delete'])->name('delete');
            Route::post('/update-order', [CsrController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('career')->name('career.')->group(function () {
            Route::get('/', [CareerController::class, 'index'])->name('index');
            Route::post('/store', [CareerController::class, 'store'])->name('store');
            Route::put('/{career}', [CareerController::class, 'update'])->name('update');
            Route::delete('/{career}', [CareerController::class, 'delete'])->name('delete');
            Route::post('/update-order', [CareerController::class, 'updateOrder'])->name('update-order');
        });

        Route::delete('connect-actions/{connect}', [ConnectController::class, 'delete'])
            ->name('connect.delete');

        Route::get('/footer', [FooterController::class, 'index'])->name('footer');
        Route::post('/footer/update', [FooterController::class, 'update'])->name('footer.update');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::put('/update-assets', [SettingsController::class, 'updateAssets'])->name('update-assets');
            Route::put('/update-credentials', [SettingsController::class, 'updateCredentials'])->name('update-credentials');
        });

        Route::get('/{slug}', [MenuController::class, 'showMultifunctional'])->name('multifunctional');
    });

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/career', [CareerController::class, 'careerIndex'])->name('career.index');
Route::get('/career/{slug}', [CareerController::class, 'careerShow'])->name('career.show');
Route::post('/career/{slug}/apply', [CareerController::class, 'submitApplication'])->name('career.apply');

Route::post('/connect', [ConnectController::class, 'store'])
    ->name('connect.submit');

Route::get('/sitemap', [PageController::class, 'sitemap'])->name('sitemap');

Route::get('{slug}', [PageController::class, 'page'])
    ->where('slug', '.*');