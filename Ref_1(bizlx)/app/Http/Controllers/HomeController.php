<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function userWishlist()
    {
        return view('userpanel.wishlist');
    }

    public function userReviews()
    {
        return view('userpanel.reviews');
    }

    public function businessDashboard()
    {
        return view('businesspanel.dashboard');
    }

    public function businessWishlist()
    {
        return view('businesspanel.wishlist');
    }

    public function businessReviews()
    {
        return view('businesspanel.reviews');
    }

    public function businessCoverimage()
    {
        return view('businesspanel.coverimage');
    }

    public function businessGalleryimage()
    {
        return view('businesspanel.galleryimage');
    }

    public function businessReviewsbybusiness()
    {
        return view('businesspanel.reviewbybusiness');
    }

    public function businessBilling()
    {
        return view('businesspanel.billing');
    }

    public function businessHotdeals()
    {
        return view('businesspanel.deals');
    }

    public function resellerDashboard()
    {
        return view('resellerpanel.dashboard');
    }

    public function resellerWishlist()
    {
        return view('resellerpanel.wishlist');
    }

    public function resellerReviews()
    {
        return view('resellerpanel.reviews');
    }

    public function resellerPayment()
    {
        return view('resellerpanel.payment');
    }

    public function resellerProfile()
    {
        return view('resellerpanel.profile');
    }

    public function adminDashboard()
    {
        return view('admin.dashboard');
    }
    public function publicUsers()
    {
        return view('admin.users.public');
    }

    public function subAdmins()
    {
        return view('admin.users.subadmin');
    }

    public function adminBusinesses()
    {
        return view('admin.users.business');
    }
    # Nuew Route for edit
    public function editBusiness()
    {
        // Retrieve the business by its ID
        // $business = Business::findOrFail($business_id);

        // Pass the entire business object to the Blade view
        return view('admin.businessedit');
    }
    public function editBusinessCoverImage()
    {

        return view('admin.businesscoverimageedit');
    }
    public function editBusinessGalleryImage()
    {

        return view('admin.businessgalleryimageedit');
    }
    public function editreseller($id)
    {
        return view('admin.resellereditbyadmin', ['resellerId' => $id]);
    }
    public function adminResellerReviews($id)
    {
        // Logic to fetch reseller reviews based on ID
        return view('admin.resellerreviews');
    }

    public function adminResellerwishlist($id)
    {
        return view('admin.resellerwishlistedit');
    }

    public function adminResellerpayment($id)
    {
        return view('admin.resellerpayment');
    }

    public function adminBusinessesdeals($id)
    {
        $deal = Business::find($id);
        return view('admin.users.businessdeals', ['deal' => $deal]);
    }

    public function adminBilling()
    {
        return view('admin.billing');
    }

    public function adminResellers()
    {
        return view('admin.users.reseller');
    }

    public function adminResellersBusinessList($id)
    {
        return view('admin.resellerbusinesslist', ['resellerId' => $id]);
    }

    public function adminMaincats()
    {
        return view('admin.categories.main');
    }

    public function adminSubcats()
    {
        return view('admin.categories.subcat');
    }

    public function adminJobcats()
    {
        return view('admin.jobs.categories');
    }

    public function adminJobtypes()
    {
        return view('admin.jobs.types');
    }

    public function adminJobqualifications()
    {
        return view('admin.jobs.qualifications');
    }

    public function adminCountries()
    {
        return view('admin.locations.country');
    }

    public function adminStates()
    {
        return view('admin.locations.state');
    }

    public function adminCities()
    {
        return view('admin.locations.city');
    }

    public function adminReviews()
    {
        return view('admin.reviews');
    }

    public function adminReports()
    {
        return view('admin.reports');
    }

    public function adminAds()
    {
        return view('admin.ads');
    }

    public function adminCitydeals()
    {
        return view('admin.citydeals');
    }

    public function adminCatslider()
    {
        return view('admin.catslider');
    }

    public function adminProfile()
    {
        return view('admin.profile');
    }

    public function adminPlans()
    {
        return view('admin.plans');
    }

    public function adminRegistrationimage()
    {
        return view('admin.registrationimage');
    }

    public function adminContactphone()
    {
        return view('admin.contactphone');
    }

    public function adminPages()
    {
        return view('admin.pages');
    }

    public function rbussEdit($id)
    {
        $business_id = Business::where('business_id','=',$id)->first()->business_id;
        return view('resellerpanel.edit-business',compact('business_id'));
    }

    public function rbusinessProfile()
    {
        return view('rbusiness.dashboard');
    }

    public function rbusinessCoverimage()
    {
        return view('rbusiness.coverimage');
    }

    public function rbusinessGalleryimage()
    {
        return view('rbusiness.galleryimage');

    }

    public function rbusinessHotdeals()
    {
        return view('rbusiness.deals');
    }

    public function rbusinessReview()
    {
        return view('rbusiness.reviews');
    }

}
