<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CitydealController;
use App\Http\Controllers\TopadsController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\CatsliderController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ResellerBusinessController;
use App\Http\Controllers\SadminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\VerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
// });

Route::get('/verify-email/{token}', [VerificationController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/settings', [ResellerController::class, 'getSettings']);
Route::get('/setting', [BusinessController::class, 'getSetting']);


 Route::post('/user/register',[ApiController::class, 'register']);
 Route::post('/user/login',[ApiController::class, 'authenticate'])->name('user.login');

 Route::post('admin/login',[AdminController::class, 'adminLogin']);
 Route::post('subadmin/login',[AdminController::class, 'subadminLogin']);

 Route::post('/business/login',[BusinessController::class, 'businessLogin']);
 Route::post('/business/register',[BusinessController::class, 'businessRegister']);
 Route::post('/verify-otp', [BusinessController::class, 'verifyOtp']);
 Route::post('/resend-otp', [BusinessController::class, 'resendOtp']);


 Route::post('reseller/register', [ResellerController::class, 'resellerRegister']);
 Route::post('/reseller/login',[ResellerController::class, 'resellerLogin']);

 Route::post('/user/verify', [ApiController::class, 'userVerify']);
 Route::post('forgetpassword',[ApiController::class, 'forgetPassword']);
 Route::post('resetpassword',[ApiController::class, 'resetPassword']);
 Route::get('getstate/{id}', [ResellerController::class, 'getstateByCountryId']);
 Route::get('states/{id}', [ResellerController::class, 'getCitiesByStateId']);
 Route::get('cities/{id}', [ResellerController::class, 'getCitiesById']);
//old   Route::get('subcategories/{id}', [CategoryController::class, 'getsubCatBycatId']);
 Route::get('subcategories', [CategoryController::class, 'getsubCatBycatId']);
 Route::get('keyword/subcategories', [CategoryController::class, 'getSubcategoriesByKeyword']);
 Route::post('/categoryedit', [CategoryController::class, 'editCategory']);
 Route::get('/category',[CategoryController::class,'getCategories']);
 Route::get('/maincategory',[CategoryController::class,'getMainCategories']);
 Route::post('category/add/', [CategoryController::class, 'categoryAdd']);
 Route::delete('/category/delete', [CategoryController::class, 'deleteCategory']);
 Route::post('admin/multiple/category/delete', [CategoryController::class, 'adminDeleteMultipleCategory']);

 Route::get('subcategory',[CategoryController::class,'getSubCategories']);
 Route::get('subcategories/{id}',[CategoryController::class,'getSubCategoriesbyID']);
 Route::get('jobcategories/{id}',[CategoryController::class,'getJobCategoriesbyID']);
 Route::get('admin/allcat/drop',[CategoryController::class,'adminAllCatDrop']);
 Route::post('admin/subcategory/add',[CategoryController::class,'adminSubcatAdd']);
 Route::post('subcategory/edit/{id}',[CategoryController::class,'editSubCategories']);
 Route::get('admin/subcategory/delete/{id}',[CategoryController::class,'adminSubcategoryDelete']);
 Route::post('admin/multiple/subcategory/delete',[CategoryController::class,'adminMultipleSubcategoryDelete']);

 Route::get('/populars',[CategoryController::class,'getPopularCategories']);
 Route::get('/popular/cat/cities/{subCat_id}',[CategoryController::class,'getPopularCities']);
 Route::get('/popular/cat/cities/business/{city_id}/{sub_cat_id}',[CategoryController::class,'getPopularCitiesBusiness']);
 Route::get('category/city/search',[CategoryController::class,'getCitiesByRadius']);

 Route::get('countries', [LocationController::class, 'getCountries']);
 Route::get('get/states/{country_id}',[LocationController::class,'registerGetState']);

 Route::get('/locations',[LocationController::class,'getLocations']);
 Route::get('/allstates',[LocationController::class,'getStates']);
 Route::get('getallcities',[LocationController::class,'getCities']);
 Route::get('qualification',[LocationController::class,'getQualification']);

 Route::get('allcities',[LocationController::class,'getStateWithCity']);
 Route::get('keyword/allcities',[LocationController::class,'getStateWithCityByKeyword']);

 Route::get('businesses/profile/{uname}/{loggedUser_id}',[BusinessController::class,'getbusinessProfile']);
 Route::get('resellers/list',[ApiController::class,'getResellersByCity']);
 Route::post('resellers/userstatus', [ApiController::class, 'getResellersuserstatus']);
 Route::post('business/userstatus', [ApiController::class, 'getBusinessuserstatus']);
 
// business dashboard all api's by reseller (start)
 Route::post('reseller/business/login',[ResellerBusinessController::class, 'resellerbusinessLogin']);
 Route::post('reseller/business/profile', [ResellerBusinessController::class, 'ResellerBusinessProfileGet']);
 Route::post('reseller/update/business/profile', [ResellerBusinessController::class, 'updateBprofilebyreseller']);
 Route::post('reseller/update/business/profile2', [ResellerBusinessController::class, 'updateBprofile2byreseller']);

 Route::post('reseller/update/business/password', [ResellerBusinessController::class, 'updatePasswordbyreseller']);

 Route::post('reseller/update/reseller/id', [ResellerBusinessController::class, 'updateResellerIdByreseller']);
 Route::post('reseller/add/business/cover_images', [ResellerBusinessController::class, 'saveCoverimagesByreseller']);
 Route::post('reseller/add/business/gallery_images', [ResellerBusinessController::class, 'saveGalleryimagesByreseller']);
 Route::post('reseller/cover/image', [ResellerBusinessController::class, 'coverImageByreseller']);
 Route::post('reseller/gallery/images', [ResellerBusinessController::class, 'userGalleryImagesbyadmin']);
 Route::post('admin/gallery/images', [ResellerBusinessController::class, 'userGalleryImagesbyreseller']);
 Route::post('resller/update/image', [ResellerBusinessController::class, 'UpdateImagesDetailsbyreseller']);
 Route::post('resller/delete/image', [ResellerBusinessController::class, 'deleteImagebyreseller']);
 Route::post('businesses/wishlists/getbyreseller', [ResellerBusinessController::class, 'businessWishlistsGetbyreseller']);
 Route::get('businesses/wishlists/remove/{wishlist_id}', [ResellerBusinessController::class, 'businessWishlistsremove']);
 Route::post('businesses/reviews/getbyreseller', [ResellerBusinessController::class, 'businessReviewsGetbyreseller']);
 Route::post('businesses/billing/planbyreseller', [ResellerBusinessController::class, 'getBussinessBillingPlansbyreseller']);
 Route::post('businesses/active/planbyreseller', [ResellerBusinessController::class, 'getBussinessactivePlansbyreseller']);
 Route::post('businesses/hotdeals/getbyReseller',[ResellerBusinessController::class,'businesstHotDealsGetByReseller']);
 Route::post('businesses/hotdeals/addbyReseller', [ResellerBusinessController::class, 'businessesHotdealsAddByReseller']);
 Route::post('businesses/sales/getbyReseller', [ResellerBusinessController::class, 'businessesSalesGetByReseller']);
 Route::post('businesses/sales/submitbyReseller', [ResellerBusinessController::class, 'businessesSalesSubmitByReseller']);
 Route::post('businesses/jobs/getbyReseller',[ResellerBusinessController::class,'businesstJobsGetByReseller']);
 Route::post('add/jobbyReseller', [ResellerBusinessController::class, 'addJobByReseller']);
 Route::post('update/jobbyReseller', [ResellerBusinessController::class, 'updateJobByReseller']);
 Route::post('delete/jobbyReseller', [ResellerBusinessController::class, 'deleteJobByReseller']);
 Route::post('businesses/videos/getbyReseller', [ResellerBusinessController::class, 'businessVideosGetByReseller']);
 Route::post('add/videobyReseller', [ResellerBusinessController::class, 'addVideoByReseller']);
 Route::post('update/videobyReseller', [ResellerBusinessController::class, 'updateVideoByReseller']);
 Route::post('delete/videobyReseller', [ResellerBusinessController::class, 'deleteVideoByReseller']);
// business dashboard all api's by reseller (end)

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('/business/profile', [BusinessController::class, 'getBprofile']);
    Route::post('/business/profile1', [BusinessController::class, 'getBprofile1']);
    Route::post('/update/business/profile', [BusinessController::class, 'updateBprofile']);
    Route::post('update/business/updateBprofileByAdmin', [BusinessController::class, 'updateBprofileByAdmin']);
    Route::get('/myprofile', [ApiController::class, 'myprofile']);
    Route::post('/inquirey', [BusinessController::class, 'sendInquiryInfo']);
    Route::post('/add/wishlist', [BusinessController::class, 'addWishlist']);
    Route::post('/jobapply', [UserController::class, 'applyJob']);
    Route::post('/remove/wishlist', [BusinessController::class, 'removeFromWishlist']);
    // Route::post('/add/coverimage', [BusinessController::class, 'addCoverImage']);
    Route::get('/gallery/images', [BusinessController::class, 'userGalleryImages']);
    Route::post('/gallery/imagesbyadmin', [BusinessController::class, 'userGalleryImagesbyadmin']);
    Route::get('/cover/image', [BusinessController::class, 'coverImage']);
    Route::post('/cover/coverImagebyadmin', [BusinessController::class, 'coverImagebyadmin']);

    Route::post('update/business/profile2', [BusinessController::class, 'updateBprofile2']);
    Route::post('update/business/updateBprofile2byadmin', [BusinessController::class, 'updateBprofile2byadmin']);

    Route::post('update/business/password', [BusinessController::class, 'updatePassword']);
    Route::post('add/business/cover_images', [BusinessController::class, 'saveCoverimages']);
    Route::post('add/business/cover_images1', [BusinessController::class, 'saveCoverimages1']);
    Route::post('add/business/gallery_images', [BusinessController::class, 'saveGalleryimages']);
    Route::post('add/business/gallery_images1', [BusinessController::class, 'saveGalleryimages1']);
    Route::post('update/image', [BusinessController::class, 'UpdateImagesDetails']);
    Route::post('delete/image', [BusinessController::class, 'deleteImage']);
    Route::get('user/wishlists', [ApiController::class, 'Wishlists']);
    Route::post('add/video', [VideoController::class, 'addVideo']);
    Route::post('add/videobyadmin', [VideoController::class, 'addVideobyAdmin']);
    Route::post('update/video', [VideoController::class, 'updateVideo']);
    Route::post('update/videobyadmin', [VideoController::class, 'updateVideobyAdmin']);
    Route::post('delete/video', [VideoController::class, 'deleteVideo']);
    Route::post('add/business/review', [ReviewController::class, 'addReview']);
    Route::post('edit/business/review', [ReviewController::class, 'editReview']);
    Route::post('add/job', [JobController::class, 'addJob']);
    Route::post('add/jobbyadmin', [JobController::class, 'addJobbyAdmin']);
    Route::post('update/job', [JobController::class, 'updateJob']);
    Route::post('update/jobbyadmin', [JobController::class, 'updateJobbyAdmin']);
    Route::get('jobs', [JobController::class, 'jobsList']);
    Route::post('delete/job', [JobController::class, 'deleteJob']);
    Route::post('delete/jobcategory', [JobCategoryController::class, 'deleteJobcategory']);
    Route::post('delete/multijobcategory', [JobCategoryController::class, 'deleteMultiplejobcat']);
    Route::post('register/reseller', [ResellerController::class, 'registerReseller']);
    Route::get('registered/business/list', [ResellerController::class, 'getBusinessRegisteredByReseller']);
    Route::post('update/reseller/id', [ResellerController::class, 'updateResellerId']);
    Route::get('get/business/details', [ResellerController::class, 'getRegisteredBusinessDetails']);
    Route::post('update/reseller/details', [ResellerController::class, 'updateResellerDetails']);
    Route::get('get/information', [ResellerController::class, 'getAllInformation']);
    Route::post('search/business', [ResellerController::class, 'searchBusinessesByReseller']);
    Route::post('search/reviews', [ReviewController::class, 'searchReviewsData']);
    Route::get('categories/subcategories', [AdminController::class, 'categoriesData']);
    Route::post('gallery/upload', [AdminController::class, 'upload']);
    Route::post('add/tab', [AdminController::class, 'addTab']);
    Route::get('tabs', [AdminController::class, 'Tabs']);
    Route::post('add/city', [LocationController::class, 'addCity']);
    Route::post('edit/city', [LocationController::class, 'editCity']);
    Route::post('delete/city', [LocationController::class, 'deleteCity']);
    Route::post('delete/multicities', [LocationController::class, 'deleteMultiplecities']);

    Route::get('admin/get/country', [LocationController::class, 'getCountries']);
    Route::post('admin/add/country', [LocationController::class, 'addCountry']);
    Route::post('admin/edit/country', [LocationController::class, 'editCountry']);
    Route::post('admin/delete/country', [LocationController::class, 'deleteCountry']);


    Route::post('add/state', [LocationController::class, 'addState']);
    Route::post('edit/state', [LocationController::class, 'editState']);
    Route::post('delete/state', [LocationController::class, 'deleteState']);
    Route::post('delete/multistates', [LocationController::class, 'deleteMultiplestates']);
    Route::post('update/tab', [AdminController::class, 'updateTab']);
    Route::post('delete/tab', [AdminController::class, 'deleteTab']);
    Route::post('delete/multiple/tabs', [AdminController::class, 'deleteMultipleTabs']);
    Route::get('get/subcatids', [AdminController::class, 'getSubIds']);
    Route::get('admin/jobtypes', [JobCategoryController::class, 'getjobtypes']);
    Route::post('admin/add/jobtypes', [JobCategoryController::class, 'addjobtypes']);
    Route::post('admin/edit/jobtypes/{id}', [JobCategoryController::class, 'editjobtypes']);
    Route::post('admin/delete/jobtypes', [JobCategoryController::class, 'deletejobtypes']);
    Route::get('admin/job/qualification', [JobCategoryController::class, 'getjobqualification']);
    Route::post('admin/add/job/qualification', [JobCategoryController::class, 'addjobqualification']);
    Route::post('admin/edit/job/qualification/{id}', [JobCategoryController::class, 'editjobqualification']);
    Route::post('admin/delete/job/qualification', [JobCategoryController::class, 'deletejobqalification']);
    Route::get('admin/jobcategory', [JobCategoryController::class, 'adminjobcategoryGet']);
    Route::get('admin/catsliders',[CatsliderController::class,'admingetCatsliders']);
    Route::post('admin/add/catsliders',[CatsliderController::class,'adminaddCatsliders']);
    Route::post('admin/edit/catsliders/{id}',[CatsliderController::class,'admineditCatsliders']);
    Route::post('admin/delete/catsliders',[CatsliderController::class,'admindeleteCatsliders']);
    Route::get('admin/subcategories', [CategoryController::class, 'admingetSubCategories']);
    Route::post('admin/add/topads', [TopadsController::class, 'topadsAdd']);
    Route::post('admin/edit/topads/{id}', [TopadsController::class, 'topadsEdit']);
    Route::post('admin/delete/topads', [TopadsController::class, 'deletetopads']);
    Route::get('admin/get/topads', [TopadsController::class, 'admingetTopads']);
    Route::post('update/editor/array', [BusinessController::class, 'updateEditorArray']);
    Route::post('update/editor/updateEditorArraybyadmin', [BusinessController::class, 'updateEditorArraybyadmin']);
    Route::get('get/columns', [AdminController::class, 'colummns']);
    Route::post('save/page', [AdminController::class, 'savePage']);
    Route::get('all/pages', [AdminController::class, 'allPages']);
    Route::post('delete/page', [AdminController::class, 'deletePage']);
    Route::get('get/page/detail', [AdminController::class, 'getPageDetail']);
    Route::post('update/page', [AdminController::class, 'updatePageDetails']);
    Route::post('delete/multiple/pages', [AdminController::class, 'deleteMultiplePages']);
    Route::get('get/pages', [AdminController::class, 'getPages']);
    Route::get('page/content', [AdminController::class, 'getPageContent']);
    Route::post('update/page/content', [AdminController::class, 'updatePageContent']);
    Route::get('get/business/namecity', [BusinessController::class, 'getbusinessname']);
    Route::get('admin/home/cityslider',[CitydealController::class,'admingetCitydeals']);
    Route::post('admin/add/citydeal', [CitydealController::class, 'citydealAdd']);
    Route::post('admin/edit/citydeal/{id}', [CitydealController::class, 'citydealEdit']);
    Route::post('admin/delete/citydeal', [CitydealController::class, 'deletecitydeal']);
    Route::get('admin/Profile',[AdminController::class,'getadminprofile']);
    Route::post('admin/Profile/update',[AdminController::class,'adminUpdateprofile']);
    Route::post('admin/password/update',[AdminController::class,'updateadminPassword']);
    Route::get('admin/plans',[PlanController::class,'admingetPlans']);
    Route::post('admin/add/plan',[PlanController::class,'adminAddplan']);
    Route::get('get/plan/details', [PlanController::class, 'getplansbyid']);
    Route::post('admin/edit/plan/{id}', [PlanController::class, 'adminEditplan']);
    Route::post('admin/delete/plan', [PlanController::class, 'adminDeleteplan']);
    Route::get('locales/timezones/currencies', [AdminController::class, 'getLocalesTimezonesAndCurrencies']);
    Route::post('update/profile/details', [AdminController::class, 'updateSuperAdminProfileDetails']);
    Route::post('delete/multiple/users', [UserController::class, 'deleteMultipleUsers']);
    Route::get('job/search', [JobController::class, 'JobSearch']);
    Route::get('reseller/profile', [ResellerController::class, 'resellerprofileGet']);
    Route::post('reseller/profile1', [ResellerController::class, 'resellerprofileGetbyadmin']);

    Route::post('update/reseller/profile', [ResellerController::class, 'updateResellerprofile']);
    Route::post('update/reseller/updateResellerprofilebyadmin', [ResellerController::class, 'updateResellerprofilebyadmin']);

    Route::post('update/reseller/password', [ResellerController::class, 'updateresellerPassword']);
    Route::post('update/reseller/updateresellerPasswordbyadmin', [ResellerController::class, 'updateresellerPasswordbyadmin']);


    Route::get('user/profile', [UserController::class, 'getUserprofiles']);
    Route::post('update/user/profile', [UserController::class, 'updateUserprofiles']);
    Route::post('update/user/password', [UserController::class, 'updateUserPasswords']);
    Route::get('user/review', [UserController::class, 'getUserreviews']);
    Route::post('user/reviewbyadmin', [UserController::class, 'getUserreviewsbusinessbyadmin']);
    Route::post('user/getUserReviewsByAdmin', [UserController::class, 'getUserReviewsByAdmin']);  # Reviews


    Route::get('businesses/active/plan', [PlanController::class, 'getBussinessactivePlans']);
    Route::get('businesses/active/planbyadmin', [PlanController::class, 'getBussinessactivePlansbyAdmin']);
    Route::get('/businesses',[UserController::class,'businesses']);
    Route::get('/businessesbyadmin/{id}',[UserController::class,'businessesbyadmin']);
});
 Route::get('profile/details', [AdminController::class, 'getSuperAdminDetails']);
 Route::get('businesses/list',[TopadsController::class,'getallBusiness']);

 Route::get('/jobcategory', [JobCategoryController::class, 'jobcategoryGet']);
 Route::get('/keyword/jobcategory', [JobCategoryController::class, 'jobcategoryGetByKeyword']);

 Route::get('/jobcategory/allcities', [JobCategoryController::class, 'getAllcities']);
 Route::get('/jobcategory/bycity/{city}', [JobCategoryController::class, 'getjobCatbycity']);
 Route::get('/jobcategory/{job_cat_slug}', [JobCategoryController::class, 'getJobbyid']);
 Route::get('jobcategory/job-detail/{job_slug}/{loggedUser_id}', [JobCategoryController::class, 'getJobdetail']);
 Route::get('/jobs/{job_cat_slug}/{city}', [JobCategoryController::class, 'jobCatwithcity']);
 Route::post('/jobcategory/add', [JobCategoryController::class, 'jobcategoryAdd']);
 Route::post('/jobcategory/edit/{id}', [JobCategoryController::class, 'jobcategoryEdit']);

 Route::get('/catsearch', [SearchController::class, 'catSearchGet']);
 Route::get('/keyword/catsearch', [SearchController::class, 'catSearchGetByKeyword']);

 Route::post('search/category', [SearchController::class, 'searchCategoryPostGet']);
 Route::get('/business/featured', [SearchController::class, 'businessfeaturedGet']);
 Route::post('/api/business/feature-toggle', [BusinessController::class, 'toggleFeaturedBusiness']);

 Route::get('/jobsearch', [SearchController::class, 'jobSearchGet']);
 Route::post('/search/jobs/category', [SearchController::class, 'searchJobsCategory']);
 Route::get('/getalljobs', [SearchController::class, 'getAlljobs']);

 Route::get('/video/all', [VideoController::class, 'getVideoAll']);

 Route::get('video/detail/{video_id}/{loggedUser_id}',[VideoController::class,'getVideoDetail']);

 Route::get('/professions',[ProfessionController::class, 'getProfessions']);
 Route::get('/skills',[SkillController::class,'getSkills']);
 Route::get('plans',[PlanController::class,'getPlans']);

 Route::get('/home/citydeal',[CitydealController::class,'getCitydeals']);
 Route::get('/home/topads',[TopadsController::class,'getTopads']);
 Route::get('/home/catsliders/{loggedUser_id}',[CatsliderController::class,'getCatsliders']);


 Route::get('citylistings',[CitydealController::class,'getCitylistings']);
 Route::get('citydealslist',[CitydealController::class,'getCitydealslist']);

//  Route::get('citylistings/{city}',[CitydealController::class,'getCitylistings']);
//  Route::get('citydealslist/{city}',[CitydealController::class,'getCitydealslist']);

 Route::get('deals/details/{hotDeal_slug}/{loggedUser_id}',[CitydealController::class,'getDealdetail']);

 Route::get('allsales',[SaleController::class,'getSales']);
 Route::get('sales/detail/{sale_id}/{loggedUser_id}',[SaleController::class,'getSaledetail']);

 //get single businesss hot-deals, jobs, sales and review data (start)
 Route::get('businesss/jobs/{business}',[BusinessController::class,'getBusienssjobs']);
 Route::get('businesss/hotdeals/{business}',[BusinessController::class,'getBusiensshotdeals']);
 Route::get('businesss/sales/{business}',[BusinessController::class,'getBusienssSales']);
 Route::get('businesss/reviews/{business}',[BusinessController::class,'getBusienssReviews']);
 //get single businesss hot-deals, jobs, sales and review data (end)


 Route::group(['middleware' => ['jwt.verify']], function(){
    Route::get('user',[UserController::class,'index']);
    Route::get('userdata',[UserController::class,'getuserdata']);
    Route::post('admin/user/update', [UserController::class, 'adminUserUpdate']);
    Route::get('admin/reviews',[ReviewController::class,'adminReviewslist']);
    Route::post('admin/reviews/update', [ReviewController::class,'adminReviewUpdate']);
    Route::get('subadmins',[UserController::class,'subAdmins']);
    Route::post('admin/add/subadmin',[UserController::class,'AdminAddsubAdmin']);
    Route::post('admin/subadmin/update', [UserController::class, 'subAdminUpdate']);
    Route::get('admin/delete/subadmin/{user_id}',[UserController::class,'AdminDeletesubAdmin']);
    Route::get('resellers',[UserController::class,'resellers']);

    Route::post('admin/reseller/add', [ResellerController::class, 'adminResellerAdd']);

    Route::get('admin/reseller/single/get/{user_id}',[ResellerController::class,'resellerSingleGet']);
    Route::post('admin/reseller/update', [ResellerController::class, 'AdminResellerUpdate']);
    Route::get('admin/reseller/delete/{user_id}', [ResellerController::class, 'AdminResellerDelete']);

    Route::post('admin/reseller/search', [ResellerController::class, 'AdminResellerSearch']);
    Route::get('admin/particularbusinesses/single/get/{business_id}', [BusinessController::class,'singelegetbusiness']);
    Route::get('admin/businesses/single/get/{business_id}', [BusinessController::class,'businessSingleGet']);
    Route::post('admin/businesses/update', [BusinessController::class,'AdminBusinessesUpdate']);
    Route::post('admin/businessesstatecity/update', [BusinessController::class,'AdminBusinessesstatecityUpdate']);
    Route::post('admin/business/search', [BusinessController::class, 'AdminBusinessSearch']);
    Route::get('businesses/hotdeals/get',[UserController::class,'businesstHotDealsGet']);
    Route::post('businesses/hotdeals/getbyadmin',[UserController::class,'businesstHotDealsGetbyAdmin']);
    Route::post('businesses/hotdeals/add', [UserController::class, 'businessesHotdealsAdd']);
    Route::post('businesses/hotdeals/addbyadmin', [UserController::class, 'businessesHotdealsAddbyAdmin']);
    Route::get('businesses/sales/get', [UserController::class, 'businessesSalesGet']);
    Route::post('businesses/sales/getbyadmin',[UserController::class,'businessesSalesGetbyAdmin']);
    Route::post('businesses/sales/submit', [UserController::class, 'businessesSalesSubmit']);
    // Route::post('businesses/sales/submitbyadmin', [UserController::class, 'businessesSalesSubmitbyAdmin']);
    Route::get('jobtypes', [UserController::class, 'jobTypeGet']);
    Route::get('getcities', [UserController::class, 'getcitiesGet']);
    Route::get('qualifications', [UserController::class, 'qualificationGet']);
    Route::get('businesses/jobs/get',[UserController::class,'businesstJobsGet']);
    Route::post('businesses/jobs/getbyadmin',[UserController::class,'businesstJobsGetbyAdmin']);
    Route::get('businesses/videos/get', [VideoController::class, 'businessVideosGet']);
    Route::post('businesses/videos/getbyadmin', [VideoController::class, 'businessVideosGetbyAdmin']);
    Route::get('businesses/wishlists/get', [ApiController::class, 'businessWishlistsGet']);
    Route::post('businesses/wishlists/getbyadmin', [ApiController::class, 'businessWishlistsGetbyadmin']);
    Route::get('businesses/wishlists/delete/{wishlist_id}', [ApiController::class, 'businessWishlistsDelete']);
    Route::get('businesses/reviews/get', [ApiController::class, 'businessReviewsGet']);
    Route::post('businesses/reviews/getbyadmin', [ApiController::class, 'businessReviewsByadmin']);


    Route::post('admin/user/delete', [UserController::class, 'deleteUserByAdmin']);
    Route::post('admin/review/delete', [ReviewController::class, 'deleteReviewByAdmin']);
    Route::post('admin/delete/multiple/reviews', [ReviewController::class, 'deleteMultipleReviews']);
    Route::get('businesses/billing/plan',[PlanController::class,'getBussinessBillingPlans']);
    Route::get('embed/code',[BusinessController::class,'makeEmbedCode'])->name('business.show');
    Route::get('radar/search',[CommonController::class,'radarSearch']);
});

Route::get('footer/details', [AdminController::class, 'getFooterDetails']);

# To save the timezones from external api ( route start )
Route::get("save/timezones",[CommonController::class,'saveTimeZones']);
# To save the timezones from external api ( route end )

# To search nearby location
Route::post('find/nearby/locations',[CommonController::class,'findBusinessesNearBy']);

# Contact us route
Route::post('/contact', [UserController::class, 'contactUs']);
Route::get('filter/data',[CommonController::class,'filterHotdealsAndSales']);
Route::get('filter/data/copy',[CommonController::class,'filterHotdealsAndSalesCopy']);
Route::get('citybycountry',[CommonController::class,'filterCities']);
Route::get('keyword/citybycountry',[CommonController::class,'filterCitiesByKeyword']);
Route::get('reports',[ApiController::class,'allreports']);
Route::get('deal/reports',[AdminController::class,'dealReports']);

# Business route
Route::get('/business/{business_id}', [HomeController::class, 'getBusiness']);



Route::get('/api/business/{id}', [BusinessController::class, 'getBusiness']);
