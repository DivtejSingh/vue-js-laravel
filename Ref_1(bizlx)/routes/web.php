<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SadminController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\FrontController;

// new add
use App\Http\Controllers\BusinessController;


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

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/',[FrontController::class,'homePage'])->name('homepage');
Route::get('/csoon',[FrontController::class,'comeSoon'])->name('csoon');
Auth::routes();

Route::get('/about',[FrontController::class,'aboutPage'])->name('front.about');
Route::get('/contact',[FrontController::class,'contactPage'])->name('front.contact');
Route::get('/blog',[FrontController::class,'blogsPage'])->name('front.blogs');
Route::get('/payment/plans',[FrontController::class,'plansPage'])->name('front.plans');
Route::get('/privacy-policy',[FrontController::class,'privacyPage'])->name('front.privacy');
Route::get('/terms-of-use',[FrontController::class,'termsPage'])->name('front.terms');


Route::get('/category/all',[FrontController::class,'categoryAll'])->name('front.categoryall');
Route::get('/jobs/all-cities',[FrontController::class,'jobsAllcity'])->name('front.jobsallcity');
Route::get('/jobs/cat-city/{job_cat_slug}',[FrontController::class,'jobsCatcitypage'])->name('front.jobscatcity');
Route::get('/searchall',[FrontController::class,'searchAll'])->name('front.searchall');
Route::get('/jobs/detail/{slug}',[FrontController::class,'jobDetail'])->name('job.detail');
Route::get('/sales/detail/{slug}',[FrontController::class,'saleDetail'])->name('sale.detail');
Route::get('/videos/detail/{slug}',[FrontController::class,'videoDetail'])->name('video.detail');
Route::get('/deals/detail/{slug}',[FrontController::class,'dealDetail'])->name('deal.detail');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'business'], function (){
    Route::get('/login',[FrontController::class,'bussinessloginPage'])->name('front.businesslogin');
    Route::get('/register',[FrontController::class,'bussinessregPage'])->name('front.businessreg');

    Route::get('/profile', [HomeController::class,'businessDashboard'])->name('bussiness.dashboard');

    Route::get('/wishlist', [HomeController::class, 'businessWishlist'])->name('business.wishlist');
    Route::get('/reviews', [HomeController::class, 'businessReviews'])->name('business.reviews');
    Route::get('/main-cover-image',[HomeController::class,'businessCoverimage'])->name('business.coverimage');
    Route::get('/gallery-images',[HomeController::class,'businessGalleryimage'])->name('business.galleryimage');
    Route::get('/bybusiness',[HomeController::class,'businessReviewsbybusiness'])->name('business.reviewbybusiness');
    Route::get('/billing', [HomeController::class, 'businessBilling'])->name('business.billing');
    Route::get('/deals',[HomeController::class,'businessHotdeals'])->name('business.deals');
});

Route::group(['prefix' => 'reseller'], function (){
    Route::get('/login',[FrontController::class,'resellerloginPage'])->name('front.resellerlogin');
    Route::get('/register',[FrontController::class,'resellerregPage'])->name('front.resellerreg');
    Route::get('/business-list',[HomeController::class,'resellerDashboard'])->name('reseller.dashboard');
    Route::get('/wishlist', [HomeController::class, 'resellerWishlist'])->name('reseller.wishlist');
    Route::get('/reviews', [HomeController::class, 'resellerReviews'])->name('reseller.reviews');
    Route::get('/payment', [HomeController::class, 'resellerPayment'])->name('reseller.payment');
    Route::get('/profile', [HomeController::class, 'resellerProfile'])->name('reseller.profile');
    Route::get('/business/profile',[HomeController::class,'rbusinessProfile'])->name('rbusiness.dashboard');
    Route::get('/business/main-cover-image',[HomeController::class,'rbusinessCoverimage'])->name('rbusiness.coverimage');
    Route::get('/business/gallery-images',[HomeController::class,'rbusinessGalleryimage'])->name('rbusiness.galleryimage');
    Route::get('/business/deals',[HomeController::class,'rbusinessHotdeals'])->name('rbusiness.deals');
    Route::get('/business/reviews',[HomeController::class,'rbusinessReview'])->name('rbusiness.reviews');
    // Route::post('/update/business/profile2', [ResellerBusinessController::class, 'updateProfile2byreseller']);


    Route::get('/business-list/edit/{id}', [HomeController::class, 'rbussEdit'])->name('rbussiness.edit');

});

Route::group(['prefix' => 'user'], function (){
    Route::get('/login',[FrontController::class,'userloginPage'])->name('front.userlogin');
    Route::get('/register',[FrontController::class,'userregPage'])->name('front.userreg');
    Route::post('/logout', [LoginController::class, 'userLogout'])->name('user.logout');
    Route::get('/wishlist',[HomeController::class,'userWishlist'])->name('user.wishlist');
    Route::get('/reviews',[HomeController::class,'userReviews'])->name('user.reviews');
});

Route::group(['prefix' => 'admin'], function (){
    Route::get('/login',[AdminController::class,'adminloginPage'])->name('admin.login');
    Route::get('/dashboard',[HomeController::class,'adminDashboard'])->name('admin.dashboard');
    Route::get('/publicusers',[HomeController::class,'publicUsers'])->name('admin.publicusers');
    Route::get('/subadmins',[HomeController::class,'subAdmins'])->name('admin.subadmins');

    Route::get('/businesses',[HomeController::class,'adminBusinesses'])->name('admin.businesses');
    Route::get('/businesses/edit/{id}', [HomeController::class, 'editBusiness'])->name('business.edit');  # Business edit by admin
    Route::get('/reseller/edit/{id}', [HomeController::class, 'editreseller'])->name('reseller.edit'); # Reseller edit by admin
    Route::get('/businesses/main-cover-image/{id}', [HomeController::class, 'editBusinessCoverImage'])->name('businesscoverimage.edit');  # Business edit by admin
    Route::get('/businesses/gallery-image/{id}', [HomeController::class, 'editBusinessGalleryImage'])->name('businessgalleryimage.edit');  # Business edit by admin

    Route::get('/businesses-deals/{id}',[HomeController::class,'adminBusinessesdeals'])->name('admin.businessesdeals');
    Route::get('/businesses/billing/{id}',[HomeController::class,'adminBilling'])->name('admin.businessesbilling');
    Route::get('/reseller/reviews/{id}', [HomeController::class, 'adminResellerReviews'])->name('admin.resellerreviews');
    Route::get('/reseller/wishlist/{id}', [HomeController::class, 'adminResellerwishlist'])->name('admin.resellerwishlistedit');
    Route::get('/reseller/payment/{id}', [HomeController::class, 'adminResellerpayment'])->name('admin.resellerpayment');


    Route::get('/reseller/businesslist/{id}', [HomeController::class, 'adminResellersBusinessList'])->name('admin.resellersbusinesslist');
    Route::get('/resellers',[HomeController::class,'adminResellers'])->name('admin.resellers');
    Route::get('/maincategories',[HomeController::class,'adminMaincats'])->name('admin.maincategories');
    Route::get('/subcategories',[HomeController::class,'adminSubcats'])->name('admin.subcategories');
    Route::get('/jobcategories',[HomeController::class,'adminJobcats'])->name('admin.jobcategories');
    Route::get('/jobtypes',[HomeController::class,'adminJobtypes'])->name('admin.jobtypes');
    Route::get('/jobqualifications',[HomeController::class,'adminJobqualifications'])->name('admin.jobqualifications');
    Route::get('/country',[HomeController::class,'adminCountries'])->name('admin.country');
    Route::get('/state',[HomeController::class,'adminStates'])->name('admin.state');
    Route::get('/city',[HomeController::class,'adminCities'])->name('admin.city');
    Route::get('/reviews',[HomeController::class,'adminReviews'])->name('admin.reviews');
    Route::get('/reports',[HomeController::class,'adminReports'])->name('admin.reports');
    Route::get('/ads',[HomeController::class,'adminAds'])->name('admin.ads');
    Route::get('/citydeals',[HomeController::class,'adminCitydeals'])->name('admin.citydeals');
    Route::get('/catslider',[HomeController::class,'adminCatslider'])->name('admin.catslider');
    Route::get('/profile',[HomeController::class,'adminProfile'])->name('admin.profile');
    Route::get('/plans',[HomeController::class,'adminPlans'])->name('admin.plans');
    Route::get('/registration/image',[HomeController::class,'adminRegistrationimage'])->name('admin.registrationimage');
    Route::get('/contact/phone',[HomeController::class,'adminContactphone'])->name('admin.contactphone');
    Route::get('/pages',[HomeController::class,'adminPages'])->name('admin.pages');
});

Route::get('/{uname}',[FrontController::class,'businessPage'])->name('bussinesspage');
//Route::get('/{uname}',function ($uname){return view('share.businessprofile',['uname' => $uname]);});
//Route::get('/deals/detail/{id}',function ($id){return view('share.dealdetails',['id' => $id]);});
//Route::get('/jobs/detail/{id}',function ($id){return view('share.jobdetail',['id' => $id]);});
//Route::get('/sales/detail/{id}',function ($id){return view('share.saledetail',['id' => $id]);});
//Route::get('/videos/detail/{id}',function ($id){return view('share.videodetail',['id' => $id]);});
Route::get('radar/search',[CommonController::class,'radarSearch']);



Route::get('/updateslug',[SaleController::class,'updatesql']);

Route::group(['prefix' => 'sadmin'], function (){
    Route::group(['middleware' => 'sadmin.guest'], function (){
        Route::view('/login','sadmin.login')->name('sadmin.login');
        Route::post('/login',[SadminController::class,'authenticate'])->name('sadmin.auth');
    });
    Route::group(['middleware' => 'sadmin.auth'], function (){
        Route::get('/dashboard',[SadminController::class,'dashboard'])->name('sadmin.dashboard');
        Route::get('/logout',[SadminController::class,'logout'])->name('sadmin.logout');
        Route::get('/category',[CategoryController::class,'index'])->name('sadmin.category');
        Route::get('/location',[LocationController::class,'index'])->name('sadmin.location');
    });


});
