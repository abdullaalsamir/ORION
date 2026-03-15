<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\ConcernController;
use App\Http\Controllers\Admin\CsrController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\VideoGalleryController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BoardDirectorController;
use App\Http\Controllers\Admin\LeadershipController;
use App\Http\Controllers\Admin\MedicalJournalController;
use App\Http\Controllers\Admin\PriceSensitiveInformationController;
use App\Http\Controllers\Admin\HalfYearlyReportsController;
use App\Http\Controllers\Admin\QuarterlyReportsController;
use App\Http\Controllers\Admin\AnnualReportsController;
use App\Http\Controllers\Admin\ConnectController;
use App\Http\Controllers\Admin\CareerController;
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
        Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

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

        Route::prefix('products-actions')->name('products.')->group(function () {
            Route::get('/fetch/{generic}', [ProductController::class, 'fetchProducts'])->name('fetch');
            Route::post('/generic-store', [ProductController::class, 'storeGeneric'])->name('generic.store');
            Route::put('/generic-update/{generic}', [ProductController::class, 'updateGeneric'])->name('generic.update');
            Route::delete('/generic-delete/{generic}', [ProductController::class, 'deleteGeneric'])->name('generic.delete');
            Route::post('/product-store/{generic}', [ProductController::class, 'storeProduct'])->name('store');
            Route::put('/product-update/{product}', [ProductController::class, 'updateProduct'])->name('update');
            Route::delete('/product-delete/{product}', [ProductController::class, 'deleteProduct'])->name('delete');
        });

        Route::prefix('scholarship-actions')->name('scholarship.')->group(function () {
            Route::post('/store', [ScholarshipController::class, 'store'])->name('store');
            Route::put('/{scholarship}', [ScholarshipController::class, 'update'])->name('update');
            Route::delete('/{scholarship}', [ScholarshipController::class, 'delete'])->name('delete');
            Route::post('/update-order', [ScholarshipController::class, 'updateOrder'])->name('update-order');
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

        Route::prefix('csr-actions')->name('csr.')->group(function () {
            Route::post('/store', [CsrController::class, 'store'])->name('store');
            Route::put('/{csrItem}', [CsrController::class, 'update'])->name('update');
            Route::delete('/{csrItem}', [CsrController::class, 'delete'])->name('delete');
            Route::post('/update-order', [CsrController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('news-actions')->name('news.')->group(function () {
            Route::post('/store', [NewsController::class, 'store'])->name('store');
            Route::put('/{newsItem}', [NewsController::class, 'update'])->name('update');
            Route::delete('/{newsItem}', [NewsController::class, 'delete'])->name('delete');
            Route::post('/update-order', [NewsController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('video-gallery-actions')->name('video-gallery.')->group(function () {
            Route::post('/store', [VideoGalleryController::class, 'store'])->name('store');
            Route::put('/{videoGallery}', [VideoGalleryController::class, 'update'])->name('update');
            Route::delete('/{videoGallery}', [VideoGalleryController::class, 'delete'])->name('delete');
            Route::post('/update-order', [VideoGalleryController::class, 'updateOrder'])->name('update-order');
        });

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

        Route::prefix('journal-actions')->name('medical-journals.')->group(function () {
            Route::post('/store', [MedicalJournalController::class, 'store'])->name('store');
            Route::put('/{medicalJournal}', [MedicalJournalController::class, 'update'])->name('update');
            Route::delete('/{medicalJournal}', [MedicalJournalController::class, 'delete'])->name('delete');
            Route::post('/update-order', [MedicalJournalController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('annual-reports-actions')->name('annual-reports.')->group(function () {
            Route::post('/store', [AnnualReportsController::class, 'store'])->name('store');
            Route::put('/{annualReports}', [AnnualReportsController::class, 'update'])->name('update');
            Route::delete('/{annualReports}', [AnnualReportsController::class, 'delete'])->name('delete');
            Route::post('/update-order', [AnnualReportsController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('quarterly-reports-actions')->name('quarterly-reports.')->group(function () {
            Route::post('/store', [QuarterlyReportsController::class, 'store'])->name('store');
            Route::put('/{quarterlyReports}', [QuarterlyReportsController::class, 'update'])->name('update');
            Route::delete('/{quarterlyReports}', [QuarterlyReportsController::class, 'delete'])->name('delete');
            Route::post('/update-order', [QuarterlyReportsController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('half-yearly-reports-actions')->name('half-yearly-reports.')->group(function () {
            Route::post('/store', [HalfYearlyReportsController::class, 'store'])->name('store');
            Route::put('/{halfYearlyReports}', [HalfYearlyReportsController::class, 'update'])->name('update');
            Route::delete('/{halfYearlyReports}', [HalfYearlyReportsController::class, 'delete'])->name('delete');
            Route::post('/update-order', [HalfYearlyReportsController::class, 'updateOrder'])->name('update-order');
        });

        Route::prefix('price-sensitive-information-actions')->name('price-sensitive-information.')->group(function () {
            Route::post('/store', [PriceSensitiveInformationController::class, 'store'])->name('store');
            Route::put('/{priceSensitiveInformation}', [PriceSensitiveInformationController::class, 'update'])->name('update');
            Route::delete('/{priceSensitiveInformation}', [PriceSensitiveInformationController::class, 'delete'])->name('delete');
            Route::post('/update-order', [PriceSensitiveInformationController::class, 'updateOrder'])->name('update-order');
        });

        Route::delete('connect-actions/{connect}', [ConnectController::class, 'delete'])
            ->name('connect.delete');

        Route::prefix('career')->name('career.')->group(function () {
            Route::get('/', [CareerController::class, 'index'])->name('index');
            Route::post('/store', [CareerController::class, 'store'])->name('store');
            Route::put('/{career}', [CareerController::class, 'update'])->name('update');
            Route::delete('/{career}', [CareerController::class, 'delete'])->name('delete');
            Route::post('/update-order', [CareerController::class, 'updateOrder'])->name('update-order');
        });

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


Route::get('products/{generic}/{product}', [ProductController::class, 'frontendShow'])
    ->where('generic', '^[a-zA-Z0-9-]+$')
    ->where('product', '^[a-zA-Z0-9-]+$');

Route::get('sliders/{filename}', [SliderController::class, 'serveSliderImage'])
    ->where('filename', '^[0-9]+\.webp$');

Route::get(
    'products/{genericSlug}/{filename}',
    [ProductController::class, 'serveProductImage']
)->where('filename', '.*\.webp$');

Route::get('/career/{filename}', [CareerController::class, 'serveCareerImage'])
    ->where('filename', '.*\.webp$')
    ->name('career.image');

Route::get('{path}/{filename}', [NewsController::class, 'serveNewsPdf'])
    ->where('path', '.*news-and-announcements')
    ->where('filename', '.*\.pdf$');

Route::get('video-gallery-files/videos/{filename}', [VideoGalleryController::class, 'serveVideo'])
    ->where('filename', '.*');

Route::get('video-gallery-files/thumbnails/{filename}', [VideoGalleryController::class, 'serveThumbnail'])
    ->where('filename', '.*\.webp$');

Route::get('{path}/{year}/{filename}', [MedicalJournalController::class, 'servePdf'])
    ->where('path', '.*')
    ->where('year', '^[0-9]{4}$')
    ->where('filename', '.*\.pdf$');

Route::get('{path}/{filename}', [PriceSensitiveInformationController::class, 'servePdf'])
    ->where('path', '.*price-sensitive-information')
    ->where('filename', '.*\.pdf$');

Route::get('{path}/{filename}', [HalfYearlyReportsController::class, 'servePdf'])
    ->where('path', '.*half-yearly-reports')
    ->where('filename', '.*\.pdf$');

Route::get('{path}/{filename}', [QuarterlyReportsController::class, 'servePdf'])
    ->where('path', '.*quarterly-reports')
    ->where('filename', '.*\.pdf$');

Route::get('{path}/{filename}', [AnnualReportsController::class, 'servePdf'])
    ->where('path', '.*annual-reports')
    ->where('filename', '.*\.pdf$');

Route::get('{path}/{filename}', [PageController::class, 'image'])
    ->where('path', '.*')
    ->where('filename', '^[a-zA-Z0-9-]+\.webp$');

Route::post('/connect', [ConnectController::class, 'store'])
    ->name('connect.submit');

Route::get('/career', [CareerController::class, 'careerIndex'])->name('career.index');
Route::get('/career/{slug}', [CareerController::class, 'careerShow'])->name('career.show');
Route::post('/career/{slug}/apply', [CareerController::class, 'submitApplication'])->name('career.apply');

Route::get('/sitemap', [PageController::class, 'sitemap'])->name('sitemap');

Route::get('{slug}', [PageController::class, 'page'])
    ->where('slug', '.*');