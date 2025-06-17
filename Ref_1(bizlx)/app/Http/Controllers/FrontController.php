<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Hotdeal;
use App\Models\Job;
use App\Models\Jobcategory;
use App\Models\Pagecolumn;
use App\Models\Pagecontent;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    public function homePage()
    {
        $title = Page::where('id', 21)->value('page_title'); 
        $desc = Page::where('id', 21)->value('page_des'); 
        $ogimage = Page::where('id', 21)->value('page_ogimage'); 
        return view('welcome',compact( 'title','desc','ogimage'));
    }

    Public function comeSoon()
    {
        $title = 'Search Daily Local Hot Deals, Sales, Jobs, Videos of Shopping Stores Around You';
        return view('csoon',compact('title'));
    }

    public function categoryAll()
    {
        $title = 'All Categories';
        $categories = Category::with('subcats')
            ->orderBy('cat_name','ASC')
            ->get();
        return view('front.categories-all',compact('categories','title'));
    }

    public function jobsAllcity()
    {
        $title = 'City List of Jobs Around You';
        $citieswithjobs = DB::Table('cities')
            ->Join('jobs','jobs.city_id','=', 'cities.id')
            ->Select('cities.id','cities.city', DB::raw('COUNT(CASE WHEN jobs.job_status = 1 THEN 1 ELSE NULL END) as jobcount'))
            ->GroupBy('cities.id')
            ->get();
        return view('front.jobs-all-cities',compact('citieswithjobs','title'));
    }

    public function searchAll(Request $request)
    {
        // $alreq = $request->all();
        $title = 'Search Hot Deals, Sales, Jobs, Videos of Shopping Stores Around You';
        return view('front.searchall',[
            'dealtype' => $request->dealtype,
            'title' => $title
        ]);
    }
    public function aboutPage()
    {
        // $title = 'About Bizlx.com - Aim, Vision and Opportunities';
        // $ogimage = '/images/abc.jpg';
    
        $title = Page::where('id', 14)->value('page_title'); 
        $desc = Page::where('id', 14)->value('page_des'); 
        $ogimage = Page::where('id', 14)->value('page_ogimage'); 
  
        $content = Pagecontent::where('page_id', 14)->value('content');
    
        return view('front.about', compact( 'title','desc','ogimage','content'));
    }

    public function termsPage()
    {
        $title = Page::where('id', 18)->value('page_title'); 
        $desc = Page::where('id', 18)->value('page_des'); 
        $ogimage = Page::where('id', 18)->value('page_ogimage'); 
  
        $page = Pagecontent::where('page_id', 18)->first();
        $content = $page->content;
        $lastUpdated = \Carbon\Carbon::parse($page->updated_at)->format('jS M, Y');
    
        return view('front.terms-of-use', compact('title','desc','ogimage','content', 'lastUpdated'));
    }
    
    public function privacyPage()
    {
        $title = Page::where('id', 17)->value('page_title'); 
        $desc = Page::where('id', 17)->value('page_des'); 
        $ogimage = Page::where('id', 17)->value('page_ogimage'); 
  
        $page = Pagecontent::where('page_id', 17)->first();
        $content = $page->content;
        $lastUpdated = \Carbon\Carbon::parse($page->updated_at)->format('jS M, Y');  

        return view('front.privacy-policy',compact('title','desc','ogimage','content','lastUpdated'));
    }

    public function contactPage()
    {
        $title = Page::where('id', 27)->value('page_title'); 
        $desc = Page::where('id', 27)->value('page_des'); 
        $ogimage = Page::where('id', 27)->value('page_ogimage'); 
  
        $profile = Setting::leftjoin('locales','settings.locale_id','=','locales.id')
            ->leftjoin('timezones','settings.timezone_id','=','timezones.id')
            ->leftjoin('country_by_currency_codes','settings.currency_id','=','country_by_currency_codes.id')
            ->select('settings.*','locales.id as locale_id','locales.locale_name','timezones.id as timezone_id','timezones.timezone_name','country_by_currency_codes.id as currency_id','country_by_currency_codes.currency_code')
            ->first();
        return view('front.contact',compact('title','desc','ogimage','profile','title'));
    }

    public function blogsPage()
    {
        $title = Page::where('id', 15)->value('page_title'); 
        $desc = Page::where('id', 15)->value('page_des'); 
        $ogimage = Page::where('id', 15)->value('page_ogimage'); 
  
        return view('front.blogs',compact('title','desc','ogimage',));
    }

    public function plansPage()
    {
        $title = Page::where('id', 16)->value('page_title'); 
        $desc = Page::where('id', 16)->value('page_des'); 
        $ogimage = Page::where('id', 16)->value('page_ogimage'); 
  
        $plans =  Plan::where('plan_status', '1')->get();
        return view('front.payment-plans',compact('plans','title','desc','ogimage',));
    }

    public function bussinessloginPage()
    {
        $title = Page::where('id', 19)->value('page_title'); 
        $desc = Page::where('id', 19)->value('page_des'); 
        $ogimage = Page::where('id', 19)->value('page_ogimage'); 
  
        return view('front.business-login',compact('title','desc','ogimage',));
    }

    public function bussinessregPage()
    {
        $title = Page::where('id', 20)->value('page_title'); 
        $desc = Page::where('id', 20)->value('page_des'); 
        $ogimage = Page::where('id', 20)->value('page_ogimage'); 
  
        return view('front.business-register',compact('title','desc','ogimage',));
    }

    public function resellerloginPage()
    {
        $title = Page::where('id', 23)->value('page_title'); 
        $desc = Page::where('id', 23)->value('page_des'); 
        $ogimage = Page::where('id', 23)->value('page_ogimage'); 
  
        return view('front.reseller-login',compact('title','desc','ogimage',));
    }

    public function resellerregPage()
    {
        $title = Page::where('id', 24)->value('page_title'); 
        $desc = Page::where('id', 24)->value('page_des'); 
        $ogimage = Page::where('id', 24)->value('page_ogimage'); 
  
        return view('front.reseller-register',compact('title','desc','ogimage',));
    }

    public function userloginPage()
    {
        $title = Page::where('id', 25)->value('page_title'); 
        $desc = Page::where('id', 25)->value('page_des'); 
        $ogimage = Page::where('id', 25)->value('page_ogimage'); 
  
        return view('front.user-login',compact('title','desc','ogimage',));
    }

    public function userregPage()
    {
        $title = Page::where('id', 26)->value('page_title'); 
        $desc = Page::where('id', 26)->value('page_des'); 
        $ogimage = Page::where('id', 26)->value('page_ogimage'); 
  
        return view('front.user-register',compact('title','desc','ogimage',));
    }

    public function jobsCatcitypage(Request $request, $job_cat_slug)
    {
        $jobcatslug = $request->job_cat_slug;
        $title = 'Jobs Category'. ' '. $jobcatslug;
        $jobcategory = Jobcategory::Where('job_cat_slug',$jobcatslug)->first();

        if($jobcategory == null){
            return view('front.jobs.cat-city',compact('title','jobcatslug'));
        } else {
            $getjobcatid = $jobcategory->id;
            $getjob = DB::Table('cities')
                ->Join('jobs','jobs.city_id','=','cities.id')
                ->Join('jobcategories', 'jobcategories.id', '=','jobs.job_cat_id')
                ->Where('jobs.job_cat_id','=', $getjobcatid)
                ->Select('cities.id','cities.city', DB::raw('COUNT(CASE WHEN jobs.job_status = 1 THEN 1 ELSE NULL END) as jobcount'))
                ->GroupBy('cities.id')
                ->get();
            return view('front.jobs.cat-city',compact('title','jobcatslug','getjobcatid','getjob','jobcategory'));
        }

    }

    public function jobDetail($slug)
    {
        $jobdetail = Job::where('job_slug','=',$slug)->first();
        if($jobdetail == null){
            $title = 'Job Detail Not Found - by ';
            return view('front.jobs.detail',compact('title','jobdetail'));
        }else{
            $jobid = $jobdetail->id;
            $jobdetails = Job::join('jobtypes','jobtypes.id','jobs.job_type_id')
                ->Join('users','users.id','=','jobs.user_id')
                ->Join('galleryimages','users.id','=','galleryimages.user_id')
                ->Join('profiles','profiles.user_id','=','users.id')
                ->join('cities','cities.id','jobs.city_id')
                ->join('states','states.id','cities.state_id')
                ->join('qualifications','qualifications.id','jobs.job_qualification_id')
                ->select('jobs.*', 'users.id as business_id', 'users.mobile_phone', 'users.name','users.username',
                    'jobtypes.job_type','cities.city','states.state','qualifications.qualification',
                    'profiles.user_avatar','galleryimages.image_url')
                ->where('jobs.id', $jobid)
                ->first();
            $title = 'Job '.$jobdetails->job_title .'- Salary From  Rs. '.$jobdetails->salary_from.' - by '.$jobdetails->name . ' ';
            if(!$jobdetails->image_url){
                $ogimage = '/images/business-register.jpg';
            }
            else{
                $ogimage = $jobdetails->image_url;
            }
            $desc = $jobdetails->job_description.'- Salary From  Rs. '.$jobdetails->salary_from;
            return view('front.jobs.detail',[
                'title'=> $title,
                'ogimage'=> $ogimage,
                'desc'=>$desc,
                'jobdetail'=> $jobdetail,
                'jobdetails'=> $jobdetails,
            ]);
        }
    }

    public function saleDetail($slug)
    {
        $sslug = Sale::where('sale_slug','=',$slug)->first();
        if($sslug == null){
            $title = 'Sale - by ';
            return view('front.sales.detail',compact('title'));
        } else {
            $saleid = $sslug->id;
            $sdetail = Sale::leftJoin('profiles','profiles.user_id','sales.user_id')
                ->leftJoin('users','users.id','profiles.user_id')
                ->leftJoin('wishlists', 'wishlists.services_id', 'sales.id')
                ->select( 'sales.*','users.id as business_id', 'users.name', 'users.username', 'users.mobile_phone',
                    'profiles.city_id', 'wishlists.wishlist_status')
                ->where('sales.id', $saleid)
                // ->where('sales.saledate_to', '>=', $currentDate)
                // ->where('sales.sale_status', 1)
                ->first();
            $title = $sdetail->sale_title.' Price Rs. '.$sdetail->sale_price.' - by '.$sdetail->name.' ';
            $ogimage = $sdetail->sale_imageurl;
            $desc = $sdetail->sale_title.'-'.$sdetail->sale_description.' - Sale at Rs. '.$sdetail->sale_price.'- by '.$sdetail->name;
            return view('front.sales.detail',[
                'title'=> $title,
                'ogimage'=> $ogimage,
                'desc'=>$desc,
                'sdetail'=> $sdetail
            ]);
        }
    }

    public function videoDetail($slug)
    {
        $video = Video::where('video_slug',$slug)->first();

        if($video == null){
            $title = 'Video - by ';
            return view('front.videos.details',compact('title'));
        } else {
            $video_id = $video->id;
            $videtail = Video::leftJoin('users', 'users.id', 'videos.user_id')
                ->leftJoin('profiles', 'profiles.user_id', 'videos.user_id')
                ->leftJoin('cities', 'cities.id', 'profiles.city_id')
                ->leftJoin('states', 'states.id', 'profiles.state_id')
                ->select('videos.id as video_id', 'videos.user_id', 'videos.video_title','videos.video_slug', 'videos.video_description', 'videos.video_url',
                    'videos.youtube_id', 'videos.video_status', 'users.id as business_id', 'users.name', 'users.username', 'users.email', 'users.mobile_phone', 'profiles.city_id',
                    'cities.id as city_id', 'cities.city', 'profiles.state_id', 'states.state')
                ->where('videos.id', '=', $video_id)
                ->first();
            $title = 'Video '.$videtail->video_title.' - by ';
            return view('front.videos.details',compact('title','videtail'));
        }

    }

    public function dealDetail($slug)
    {
        $hotdeal = Hotdeal::Where('hotdeal_slug', $slug)->first();
        if($hotdeal == null){
            $title = 'Deal - by ';
            return view('front.deals.details',compact('title'));
        } else {
            $hotdealid = $hotdeal->id;
            $dealdetail = Hotdeal::join('profiles','profiles.id','=','hotdeals.business_id')
            ->join('users','users.id','profiles.user_id')
            ->with('images')
            ->join('cities','cities.id','=','profiles.city_id')
            ->select('hotdeals.*', 'users.id as business_id', 'users.name', 'users.username', 'users.mobile_phone','profiles.city_id','cities.city as city_name')
            ->where('hotdeals.id', $hotdealid)
            ->first();
        
        if ($dealdetail == null) {
            // Log the issue or handle it gracefully
            $title = 'Deal not found';
            return view('front.deals.details', compact('title'));
        }
        
            // $title = $dealdetail->hot_deal_title.' Price Rs. '.$dealdetail->price_to.'- by '.$dealdetail->username.' in '.$dealdetail->city_name ;
            $title = $dealdetail->hot_deal_title.' Price Rs. '.$dealdetail->price_to.' - by '.$dealdetail->username.' in '.$dealdetail->city_name;

            if(empty($dealdetail->images)){
                $ogimage = '/images/business-register.jpg';
            }
            else{
                $ogimage = $dealdetail->images[0]->hotdeal_img_url;
            }
            $desc = $dealdetail->hot_deal_title.'-'.$dealdetail->hot_deal_description.' Price Rs. '.$dealdetail->price_to.'- by '.$dealdetail->username;
            return view('front.deals.details',[
                'title'=> $title,
                'ogimage'=> $ogimage,
                'desc'=>$desc,
                'dealdetail'=>$dealdetail,
                ]);
        }
    }

    // public function businessPage($uname)
    // {
    //     $getuser = User::Where('username', $uname)->first();
    //     If($getuser == null){
    //         $title = $uname.' - ';
    //         return view('front.searchall',compact('uname','title'));
    //     } else {
    //         $userid = $getuser->id;
    //         $bprofile = User::Join('profiles','profiles.user_id','=','users.id')
    //             ->with('services','jobs','allreview','allvideo','galleryimage','alltarrif', 'allsale')
    //             ->join('cities','cities.id','profiles.city_id')
    //             ->join('states','states.id','cities.state_id')
    //             ->join('businesses','users.id','businesses.business_id')
    //             ->where('users.id', $userid)
    //             ->select('users.*','profiles.about', 'profiles.id as profile_id','profiles.user_id','cities.city','states.state','profiles.user_avatar','profiles.live_location', 'businesses.id as business_id')
    //             ->withcount('allreview','services', 'allsale', 'allvideo', 'jobs')
    //             ->withSum('allreview','rating')
    //             ->withAvg('allreview','rating')
    //             ->first();
    //         $coverimages = DB::Table('galleryimages')
    //             ->Where('galleryimages.image_type','=', 1)
    //             ->Where('galleryimages.user_id', $userid)
    //             ->first();
    //         $title = $bprofile->name.' - ';
    //         $desc = $bprofile->about.' - ';
    //         if($coverimages == null){
    //             $ogimage = '/images/business-register.jpg';
    //         }
    //         else{
    //             $ogimage = $coverimages->image_url;
    //         }
    //         return view('front.bussinesspage',[
    //             'uname'=>$uname,
    //             'title'=>$title,
    //             'bprofile'=>$bprofile,
    //             'desc'=>$desc,
    //             'ogimage'=>$ogimage
    //         ]);
    //     }

    // }

    public function businessPage($uname)
    {
    $getuser = User::where('username', $uname)->first();

    if (!$getuser) {
        abort(404, "User not found.");
    }

    $userid = $getuser->id;

    $bprofile = User::join('profiles', 'profiles.user_id', '=', 'users.id')
        ->with('services', 'jobs', 'allreview', 'allvideo', 'galleryimage', 'alltarrif', 'allsale')
        ->join('cities', 'cities.id', 'profiles.city_id')
        ->join('states', 'states.id', 'cities.state_id')
        ->join('businesses', 'users.id', 'businesses.business_id')
        ->where('users.id', $userid)
        ->select(
            'users.*',
            'profiles.about',
            'profiles.id as profile_id',
            'profiles.user_id',
            'cities.city',
            'states.state',
            'profiles.user_avatar',
            'profiles.live_location',
            'businesses.id as business_id'
        )
        ->withCount('allreview', 'services', 'allsale', 'allvideo', 'jobs')
        ->withSum('allreview', 'rating')
        ->withAvg('allreview', 'rating')
        ->first();

    $coverimages = DB::table('galleryimages')
        ->where('galleryimages.image_type', '=', 1)
        ->where('galleryimages.user_id', $userid)
        ->first();

    $title = $bprofile->name . ' - ';
    $desc = $bprofile->about . ' - ';
    $ogimage = $coverimages ? $coverimages->image_url : '/images/business-register.jpg';

    return view('front.bussinesspage', [
        'uname' => $uname,
        'title' => $title,
        'bprofile' => $bprofile,
        'desc' => $desc,
        'ogimage' => $ogimage
    ]);
}




}
