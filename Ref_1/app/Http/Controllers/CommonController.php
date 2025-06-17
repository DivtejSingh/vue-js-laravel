<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Country;
use App\Models\Customer_businesse;
use App\Models\Hotdeal;
use App\Models\Job;
use App\Models\Review;
use App\Models\Sale;
use App\Models\State;
use App\Models\Timezone;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Location;
use App\Models\Jobcategory;  

class CommonController extends Controller
{
    public function saveTimeZones ( Request $request ){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://worldtimeapi.org/api/timezone',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, TRUE);

        # To save the timezones into database table ( i.e - timezones table ) ( code start )
        foreach( $data as $timezone ){
            $save_timezone = new Timezone();
            $save_timezone->timezone_name = $timezone;
            $save_timezone->save();
        }
        return response([
            'success'=>true,
            'message'=>'Timezones saved successfully'
        ]);
        # To save the timezones into database table ( i.e - timezones table ) ( code end )
    }

    # To check the longitude and latitude and filter the records ( function start )
    public function radarSearch( Request $request ){

        $customers = Customer_businesse::where('address','!=',null)->where('address','!=','null')->get();

        foreach ( $customers as $customer ){

            $url = 'https://geokeo.com/geocode/v1/search.php?q='.urlencode($customer->address).'&api=aabf1e32bb4f2321a163dc0f3ebae589';
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response, TRUE);
            if(isset($data['results'])){
                $latitude = '';
                $longitude = '';
                for( $i = 0; $i < 1; $i++ ){
                    $latitude = $data['results'][$i]['geometry']['viewport']['northeast']['lat'];
                    $longitude = $data['results'][$i]['geometry']['viewport']['northeast']['lng'];
                    $existing_user = User::where('name',$customer->name)->get();
                    $existing_user_count = count($existing_user);
                    if( $existing_user_count > 0){
                        User::where('id',$existing_user[0]['id'])->update([
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                        ]);
                    }
                }
                Customer_businesse::where('id',$customer->id)->delete();
            }

        }
        return response([
            'success' => true,
            'message' => 'Longitude and latitude updated successfully!'
        ]);
    }
    # To check the longitude and latitude and filter the records ( function end )

    # To search the businesses from the current location using radius with longitude and latitude ( function start )
    public function findBusinessesNearBy( Request $request ){
         /*
         * using eloquent approach, make sure to replace the "User" with your actual model name
         * replace 6371000 with 6371 for kilometer and 3956 for miles
         */

        $data = $request->only('radius');
        $validator = Validator::make($data, [
            'radius' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{

            # To get current user IP ( code start )
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_FORWARDED'])) {
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            } else if (isset($_SERVER['REMOTE_ADDR'])) {
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            } else {
                $ipaddress = 'UNKNOWN';
            }

            $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?$ipaddress"));
            if( isset( $geo )){
                $lng = $geo['geoplugin_longitude'];
                $lat = $geo['geoplugin_latitude'];
            }

            # To get current user IP ( code end )

            $radius = $request->radius;
            $businesses = User::LeftJoin('profiles','profiles.user_id','=','users.id')
            ->select(\DB::raw("users.*,( 3959 * acos( cos( radians(" . $lat . ") ) *cos( radians( latitude ) )* cos( radians( longitude ) - radians(" . $lng . ")) + sin( radians(" . $lat . ") ) *sin( radians( latitude ) ) )) AS distance"),'profiles.address')
                    ->having("distance", "<", $radius)
                    ->orderBy("distance")
                    ->get();

            return response([
                'success' => true,
                'businesses' => $businesses
            ]);
        }
    }
    # To search the businesses from the current location using radius with longitude and latitude ( function end )

    // # To filter the data for all the hotdeals and sales ( function start )
    // public function filterHotdealsAndSales( Request $request ){
    //     $currentDate = date('Y-m-d');
    //     $type = strtolower($request->type);
    //     $searchType = null;
    //     $maxPrice = null;
    //     $allwords = $request->searchy;
    //     $words = explode(' ', $allwords);
    //     $searchy = array_filter($words);
    //     $country_id = 1;
    //     $searched = User::query();
    //     $searched->leftJoin('profiles', 'profiles.user_id', 'users.id')
    //         ->leftjoin('cities', 'cities.id', 'profiles.city_id')
    //         ->leftjoin('states', 'states.id', 'profiles.state_id')
    //         ->leftjoin('countries','countries.id','states.country_id')
    //         ->leftjoin('businesses', 'businesses.business_id', 'users.id')
    //         ->leftjoin('galleryimages', 'galleryimages.user_id', 'users.id')
    //         ->leftjoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id');
    //     if(!empty($request->subcat_id)){ // sub category
    //         $searchType = 'sub-category';
    //         $searched->where('businesses.sub_cat_id', $request->subcat_id)
    //         ->select(
    //             'users.id as business_id',
    //             'profiles.id as profile_id',
    //             'users.name',
    //             'users.username',
    //             'users.mobile_phone',
    //             'profiles.user_avatar',
    //             'profiles.address',
    //             'profiles.area',
    //             'profiles.city_id',
    //             'cities.city',
    //             'profiles.state_id',
    //             'states.state',
    //             'countries.id as country_id',
    //             'countries.country',
    //             'countries.phonecode',
    //             'businesses.sub_cat_id',
    //             'subcategories.subcat_name',
    //             DB::raw('DATE(users.created_at) as added_date'),
    //         );
    //     }
    //     if(!empty($request->city_id)){ // City
    //         $searchType = 'city';
    //         $searched->where('profiles.city_id', $request->city_id)
    //         ->select(
    //             'users.id as business_id',
    //             'profiles.id as profile_id',
    //             'users.name',
    //             'users.username',
    //             'users.mobile_phone',
    //             'profiles.user_avatar',
    //             'profiles.address',
    //             'profiles.city_id',
    //             'profiles.area',
    //             'cities.city',
    //             'countries.id as country_id',
    //             'countries.country',
    //             'countries.phonecode',
    //             'profiles.state_id',
    //             'states.state',
    //             'businesses.sub_cat_id',
    //             'subcategories.subcat_name',
    //             DB::raw('DATE(users.created_at) as added_date'),
    //         );
    //     }
    //     if($type == 0){
    //         $searchType = 0;
    //         $searched->leftjoin('hotdeals', 'hotdeals.business_id', 'profiles.id')
    //             ->with('hotdealSingleImage')
    //             ->where('date_from', '<=', $currentDate)
    //             ->where('date_to', '>=', $currentDate)
    //             ->where('hot_deal_status', '=', 1)
    //             ->select(
    //                 'users.id as business_id',
    //                 'profiles.id as profile_id',
    //                 'users.name',
    //                 'users.username',
    //                 'users.mobile_phone',
    //             'profiles.area',
    //                 'profiles.user_avatar',
    //                 'profiles.address',
    //                 'profiles.city_id',
    //                 'cities.city',
    //                 'profiles.state_id',
    //                 'states.state',
    //                 'countries.id as country_id',
    //                 'countries.country',
    //                 'countries.phonecode',
    //                 'businesses.sub_cat_id',
    //                 'subcategories.subcat_name',
    //                 DB::raw('DATE(users.created_at) as added_date'),
    //                 // hot deals
    //                 'hotdeals.id',
    //                 'hotdeals.id as hotdeal_id',
    //                 'hotdeals.hotdeal_slug',
    //                 'hotdeals.hot_deal_title',
    //                 'hotdeals.hot_deal_description',
    //                 'hotdeals.price_from',
    //                 'hotdeals.price_to',
    //                 'hotdeals.date_from',
    //                 'hotdeals.date_to',
    //             )
    //             ->where(function ($query) use($searchy){
    //                 foreach ($searchy as $sl){
    //                     $query->orWhere('hotdeals.hot_deal_title', 'like', '%' . $sl . '%')
    //                         ->orWhere('hotdeals.hot_deal_description', 'like', '%' . $sl . '%');
    //                 }
    //             });
    //             $maxPrice = Hotdeal::max('price_to');
    //             if( empty($request->min_price) && !empty($request->max_price) ){
    //                 $searched->whereBetween('price_to', [0, $request->max_price]);
    //             }
    //             if( !empty($request->min_price) && empty($request->max_price) ){
    //                 $searched->whereBetween('price_to', [$request->min_price, $maxPrice]);
    //             }
    //             if( !empty($request->min_price) && !empty($request->max_price) ){
    //                 $searched->whereBetween('price_to', [$request->min_price, $request->max_price]);
    //             }
    //     }
    //     if($type == 1){ // Sales
    //         $searchType = 1;
    //         $searched->leftjoin('sales', 'sales.user_id', 'users.id')
    //             ->where('sales.saledate_from', '<=', $currentDate)
    //             ->where('sales.saledate_to', '>=', $currentDate)
    //             ->where('sales.sale_status', '=', 1)
    //             ->select(
    //                 'users.id as business_id',
    //                 'profiles.id as profile_id',
    //                 'users.name',
    //                 'users.username',
    //                 'users.mobile_phone',
    //                 'profiles.user_avatar',
    //                 'profiles.address',
    //                 'profiles.city_id',
    //                 'cities.city',
    //                 'profiles.state_id',
    //                 'states.state',
    //             'profiles.area',
    //                 'countries.id as country_id',
    //                 'countries.country',
    //                 'countries.phonecode',
    //                 'businesses.sub_cat_id',
    //                 'subcategories.subcat_name',
    //                 DB::raw('DATE(users.created_at) as added_date'),
    //                 // Sales
    //                 'sales.id as sale_id',
    //                 'sales.sale_title',
    //                 'sales.sale_slug',
    //                 'sales.sale_description',
    //                 'sales.normal_price',
    //                 'sales.sale_price',
    //                 'sales.saledate_from',
    //                 'sales.saledate_to',
    //                 'sales.sale_imageurl',
    //             )
    //             ->
    //             where(function ($query) use($searchy){
    //                 foreach ($searchy as $sl){
    //                     $query->orWhere('sales.sale_title', 'like', '%' . $sl . '%')
    //                         ->orWhere('sales.sale_description', 'like', '%' . $sl . '%');
    //                 }
    //             });
    //             $maxPrice = Sale::max('sale_price');
    //             if( empty($request->min_price) && !empty($request->max_price) ){
    //                 $searched->whereBetween('sale_price', [0, $request->max_price]);
    //             }
    //             if( !empty($request->min_price) && empty($request->max_price) ){
    //                 $searched->whereBetween('sale_price', [$request->min_price, $maxPrice]);
    //             }
    //             if( !empty($request->min_price) && !empty($request->max_price) ){
    //                 $searched->whereBetween('sale_price', [$request->min_price, $request->max_price]);
    //             }
    //     }
    //     if($type==2){
    //         $searchType = 2;
    //         $searched->leftjoin('jobs', 'jobs.user_id', 'users.id')
    //             ->join('jobcategories', 'jobcategories.id', 'jobs.job_cat_id')
    //             ->where('jobs.job_status', '=', 1)
    //             ->orderBy('job_id','DESC')
    //             ->select(
    //                 'users.id as business_id',
    //                 'profiles.id as profile_id',
    //                 'users.name',
    //                 'users.username',
    //                 'users.mobile_phone',
    //                 'countries.phonecode',
    //                 'profiles.user_avatar',
    //             'profiles.area',
    //                 'profiles.address',
    //                 'profiles.city_id',
    //                 'cities.city',
    //                 'profiles.state_id',
    //                 'states.state',
    //                 'businesses.sub_cat_id',
    //                 'subcategories.subcat_name',
    //                 'galleryimages.image_url as mainimage',
    //                 DB::raw('DATE(users.created_at) as added_date'),
    //                 // jobs
    //                 'jobs.id as job_id',
    //                 'jobs.job_title',
    //                 'jobs.job_cat_id',
    //                 'jobcategories.job_cat_name',
    //                 'jobs.job_slug',
    //                 'jobs.job_description',
    //                 'jobs.salary_from',
    //                 'jobs.min_exp',
    //             )
    //             ->where(function ($query) use($searchy){
    //                 foreach ($searchy as $sl){
    //                     $query->orWhere('jobs.job_title', 'like', '%' . $sl . '%')
    //                         ->orWhere('job_description', 'like', '%' . $sl . '%');
    //                 }
    //             });
    //             if(!empty($request->job_cat_id)){
    //                 $searched->where('jobs.job_cat_id', $request->job_cat_id);
    //             }
    //             $maxPrice = Job::max('salary_from');
    //             if( empty($request->min_price) && !empty($request->max_price) ){
    //                 $searched->whereBetween('salary_from', [0, $request->max_price]);
    //             }
    //             if( !empty($request->min_price) && empty($request->max_price) ){
    //                 $searched->whereBetween('salary_from', [$request->min_price, $maxPrice]);
    //             }
    //             if( !empty($request->min_price) && !empty($request->max_price) ){
    //                 $searched->whereBetween('salary_from', [$request->min_price, $request->max_price]);
    //             }
    //     }
    //     if($type == 3){
    //         $searchType = 3;
    //         $searched->select('users.id as business_id',
    //                           'profiles.id as profile_id',
    //                           'users.name',
    //                           'users.username',
    //                           'users.mobile_phone',
    //                           'profiles.user_avatar',
    //                           'profiles.address',
    //                           'profiles.city_id',
    //             'profiles.area',
    //                           'cities.city',
    //                           'countries.phonecode',
    //                           'profiles.state_id',
    //                           'states.state',
    //                           'galleryimages.image_url as mainimage',
    //                           'businesses.sub_cat_id','subcategories.subcat_name')
    //             ->where('users.name', 'like', '%' . $allwords . '%');
    //     }
    //     if ($request->radius != 0) {

    //         $radius = $request->radius;
    //         $curl = curl_init();
    //         curl_setopt_array($curl, array(
    //         CURLOPT_URL => 'http://ip-api.com/json/'.$request->ip,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'GET',
    //         ));

    //         $response = curl_exec($curl);

    //         curl_close($curl);
    //         $data = json_decode($response,TRUE);

    //         if( isset($data['status'])){
    //             $lng = '76.7889';
    //             $lat = '30.7339';
    //         }else{
    //             $lng = $data['lon'];
    //             $lat = $data['lat'];
    //         }

    //         $searched->selectRaw("( 3959 * acos( cos( radians(" . $lat . ") ) *cos( radians( latitude ) )* cos( radians( longitude ) - radians(" . $lng . ")) + sin( radians(" . $lat . ") ) *sin( radians( latitude ) ) )) AS distance")
    //         ->having("distance", "<", $radius)
    //         ->orderBy("distance");
    //     }
    //     if( !empty($request->country_id) ){
    //         $searched->where('countries.id','=',$request->country_id);
    //     }else{
    //         $searched->where('countries.id','=', $country_id);
    //     }
    //     $searched->where('users.user_role', '1');
    //     $searched->where('users.user_status', '1');
    //     $searched->groupBy('users.id');
    //     $searched_data = $searched->inRandomOrder()->get();
    //     for($i=0; $i < count($searched_data); $i++) {

    //         // counting linke Hot-deals[5], Jobs[3], Videos[5], Sales[4] -- Start
    //             $hotdealsCount = Hotdeal::where('business_id', '=', $searched_data[$i]->profile_id)
    //                     ->where('date_from', '<=', $currentDate)
    //                     ->where('date_to', '>=', $currentDate)
    //                     ->where('hot_deal_status', '=', 1)
    //                     ->count();
    //             $salesCount = Sale::where('user_id', '=', $searched_data[$i]->business_id)
    //                     ->where('saledate_from', '<=', $currentDate)
    //                     ->where('saledate_to', '>=', $currentDate)
    //                     ->where('sale_status', '=', 1)
    //                     ->get()->count();
    //             $jobsCount = Job::where('user_id', '=', $searched_data[$i]->business_id)->where('job_status', 1)->get()->count();
    //             // $videosCount = Video::where('user_id', '=', $searched_data[$i]->business_id)->where('video_status', 1)->get()->count();

    //             if( $searched_data[$i]['saledate_from'] ){
    //                 $searched_data[$i]['saledate_from'] = date("d-m-Y", strtotime($searched_data[$i]['saledate_from']));
    //                 $searched_data[$i]['saledate_to'] = date("d-m-Y", strtotime($searched_data[$i]['saledate_to']));
    //             }else{

    //                 $format_date_from = date("d-m-Y", strtotime($searched_data[$i]['date_from']));
    //                 $searched_data[$i]['date_from'] = $format_date_from;
    //                 $searched_data[$i]['date_to'] = date("d-m-Y", strtotime($searched_data[$i]['date_to']));
    //             }

    //             if( $searched_data[$i]['name'] ){
    //                 $firstLetter = strtoupper(substr($searched_data[$i]['name'], 0, 1));
    //                 $spacePosition = strpos($searched_data[$i]['name'], ' ');

    //                 if ($spacePosition !== false) {
    //                     $secondLetter = strtoupper(substr($searched_data[$i]['name'], $spacePosition + 1, 1));
    //                 } else {
    //                     $secondLetter = '';
    //                 }
    //                 $searched_data[$i]['first_last_letter'] = $firstLetter.$secondLetter;
    //             }

    //             $searched_data[$i]['hotdealsCount'] = $hotdealsCount > 0 ? $hotdealsCount : 0;
    //             $searched_data[$i]['salesCount'] = $salesCount > 0 ? $salesCount : 0;
    //             $searched_data[$i]['jobsCount'] = $jobsCount > 0 ? $jobsCount : 0;
    //             // $searched_data[$i]['videosCount'] = $videosCount > 0 ? $videosCount : 0;
    //         // counting linke Hot-deals[5], Jobs[3], Videos[5], Sales[4] -- End

    //         // Get all review & rating with rating avrage of business - Start
    //             $allReviews = Review::select('id', 'rating')->where('review_business_id', '=', $searched_data[$i]->business_id)->get();
    //             $searched_data[$i]['total_reviews'] = $allReviews->count();
    //             $searched_data[$i]['total_ratings'] = $allReviews->sum('rating');
    //             if ($searched_data[$i]['total_ratings'] > 0) {
    //                 $searched_data[$i]['rating_avrage'] = round($searched_data[$i]['total_ratings'] / $searched_data[$i]['total_reviews'],1);
    //             } else {
    //                 $searched_data[$i]['rating_avrage'] = 0;
    //             }
    //         // Get all review & rating with rating avrage of business - End

    //         // Get all user Wishlist of logged user - Start
    //             if($searched_data[$i]->hotdeal_id || $searched_data[$i]->sale_id || $searched_data[$i]->job_id || $searched_data[$i]->video_id){
    //                 if($searched_data[$i]->hotdeal_id){ // Hot-Deals
    //                     $deal_id = $searched_data[$i]->hotdeal_id;
    //                 }else if($searched_data[$i]->sale_id){ // sales
    //                     $deal_id = $searched_data[$i]->sale_id;
    //                 }else if($searched_data[$i]->job_id){ // Jobs
    //                     $deal_id = $searched_data[$i]->job_id;
    //                 }else if($searched_data[$i]->video_id){ // videos
    //                     $deal_id = $searched_data[$i]->video_id;
    //                 }
    //                 $user_added_wishlist = Wishlist::Where('services_id', $deal_id)->where('user_id', '=', $request->loggedUser_id)->first();
    //                 $searched_data[$i]['user_added_wishlist'] = $user_added_wishlist;
    //             }
    //         // Get all user Wishlist of logged user - End
    //     }
    //     return response([
    //         'success' => 'success',
    //         'result' => 200,
    //         'message' => 'All Categories, Cities & Deals Searched data get.',
    //         'searchType' => $searchType,
    //         'maxPrice' => $maxPrice,
    //         'filter_data' => $searched_data
    //     ]);
    // }

    public function filterHotdealsAndSales(Request $request)
    {
        $today   = date('Y-m-d');
        $type    = (int)$request->input('type',3);
        $country = $request->input('country_id',1);
        $city    = $request->input('city_id');
        $area    = $request->input('area');
        $subcat  = $request->input('subcat_id');
        $cat     = $request->input('cat_id');
        $jobcat  = $request->input('job_cat_id');
        $searchy = array_filter(explode(' ',$request->input('searchy','')));

        // always return jobCategories for the sidebar
        $jobCategoriesList = Jobcategory::orderBy('job_cat_name')
            ->get(['id','job_cat_name']);

        $q = User::query()
            ->leftJoin('profiles','profiles.user_id','users.id')
            ->leftJoin('cities','cities.id','profiles.city_id')
            ->leftJoin('states','states.id','profiles.state_id')
            ->leftJoin('countries','countries.id','states.country_id')
            ->leftJoin('businesses','businesses.business_id','users.id')
            ->leftJoin('subcategories','subcategories.id','businesses.sub_cat_id')
            ->leftJoin('galleryimages','galleryimages.user_id','users.id')
            ->select([
                'users.id as business_id',
                'profiles.id as profile_id',
                'users.name','users.username','users.mobile_phone',
                'profiles.user_avatar','profiles.address','profiles.area',
                'profiles.city_id','cities.city','profiles.state_id','states.state',
                'countries.id as country_id','countries.country','countries.phonecode',
                'businesses.sub_cat_id','subcategories.subcat_name',
                'galleryimages.image_url as mainimage',
            ]);

        // city/area / keyword
        if ($city) $q->where('profiles.city_id',$city);
        if ($area) $q->where('profiles.area',$area);
        if ($searchy) {
            $q->where(function($w) use($searchy){
                foreach($searchy as $wrd){
                    $w->orWhere('users.name','like',"%{$wrd}%")
                      ->orWhere('profiles.address','like',"%{$wrd}%");
                }
            });
        }

        // EXACTLY ONE of subcat / cat / jobcat
        if ($subcat)       $q->where('businesses.sub_cat_id',$subcat);
        elseif ($cat)      $q->where('subcategories.cat_id',$cat);
        // jobcat deferred into jobs block

        // type‐specific
        switch($type){
          case 0: // Hot Deals
            $q->leftJoin('hotdeals','hotdeals.business_id','profiles.id')
              ->where('hotdeals.hot_deal_status',1)
              ->where('hotdeals.date_from','<=',$today)
              ->where('hotdeals.date_to','>=',$today)
              ->when($request->filled('min_price'), function($q) use($request){
                $q->where('hotdeals.price_to','>=',(int)$request->min_price);
            })
            ->when($request->filled('max_price'), function($q) use($request){
                $q->where('hotdeals.price_to','<=',(int)$request->max_price);
            })
              ->addSelect([
                'hotdeals.id as hotdeal_id','hotdeals.hotdeal_slug',
                'hotdeals.hot_deal_title','hotdeals.price_from',
                'hotdeals.price_to'
              ]);
            break;

          case 1: // Sales
            $q->leftJoin('sales','sales.user_id','users.id')
              ->where('sales.sale_status',1)
              ->where('sales.saledate_from','<=',$today)
              ->where('sales.saledate_to','>=',$today)
              ->when($request->filled('min_price'), function($q) use($request){
                $q->where('sales.sale_price','>=',(int)$request->min_price);
              })
              ->when($request->filled('max_price'), function($q) use($request){
                $q->where('sales.sale_price','<=',(int)$request->max_price);
              })
              ->addSelect([
                'sales.id as sale_id','sales.sale_slug',
                'sales.sale_title','sales.normal_price',
                'sales.sale_price','sales.sale_imageurl',
              ]);
            break;

          case 2: // Jobs
            $q->leftJoin('jobs','jobs.user_id','users.id')
              ->where('jobs.job_status',1)
              ->when($jobcat, function($q) use ($jobcat) {
                $q->where('jobs.job_cat_id', $jobcat);
            })
               // ← AND HERE:
      ->when($request->filled('min_price'), function($q) use($request){
        $q->where('jobs.salary_from','>=',(int)$request->min_price);
      })
      ->when($request->filled('max_price'), function($q) use($request){
        $q->where('jobs.salary_from','<=',(int)$request->max_price);
      })
              ->leftJoin('jobcategories','jobcategories.id','jobs.job_cat_id')
              ->addSelect([
                'jobs.id as job_id','jobs.job_slug','jobs.job_title',
                'jobs.salary_from','jobs.min_exp',
                'jobs.job_cat_id','jobcategories.job_cat_name'
              ]);
            break;

          default:
            // Listing (type 3 & 4), no extra joins
        }

        $results = $q
          ->where('countries.id',$country)
          ->where('users.user_role','1')
          ->where('users.user_status','1')
          ->groupBy('users.id')
          ->inRandomOrder()
          ->get();

        return response()->json([
          'success'       => 'success',
          'result'        => 200,
          'message'       => 'Filtered data retrieved.',
          'searchType'    => $type,
          'jobCategories' => $jobCategoriesList,
          'filter_data'   => $results,
        ]);
    }

    
    
    

    # To filter the data for all the hotdeals and sales ( function end )

    # To filter the data for all the hotdeals and sales ( function start )
    public function filterHotdealsAndSalesCopy( Request $request ){
        $currentDate = date('Y-m-d');
        $type = strtolower($request->type);
        $searchType = null;
        $allwords = $request->searchy;
        $words = explode(' ', $allwords);
        $searchy = array_filter($words);
        $maxPrice = null;
        $country_id = 1;
        $searched = User::query();
        $searched->leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('countries','countries.id','states.country_id')
            ->leftjoin('businesses', 'businesses.business_id', 'users.id')
            ->leftjoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id');
        if(!empty($request->subcat_id)){ // sub category
            $searchType = 'sub-category';
            $searched->where('businesses.sub_cat_id', $request->subcat_id)
            ->select(
                'users.id as business_id',
                'profiles.id as profile_id',
                'users.name',
                'users.username',
                'users.mobile_phone',
                'profiles.user_avatar',
                'profiles.address',
                'profiles.city_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                'countries.id as country_id',
                'countries.country',
                'businesses.sub_cat_id',
                'subcategories.subcat_name',
                DB::raw('DATE(users.created_at) as added_date'),
            );
        }
        if(!empty($request->city_id)){ // City
            $searchType = 'city';
            $searched->where('profiles.city_id', $request->city_id)
            ->select(
                'users.id as business_id',
                'profiles.id as profile_id',
                'users.name',
                'users.username',
                'users.mobile_phone',
                'profiles.user_avatar',
                'profiles.address',
                'profiles.city_id',
                'cities.city',
                'countries.id as country_id',
                'countries.country',
                'profiles.state_id',
                'states.state',
                'businesses.sub_cat_id',
                'subcategories.subcat_name',
                DB::raw('DATE(users.created_at) as added_date'),
            );
        }
        if($type == 0){
            $searchType = 0;
            $searched->leftjoin('hotdeals', 'hotdeals.business_id', 'profiles.id')
                ->with('hotdealSingleImage')
                ->where('date_from', '<=', $currentDate)
                ->where('date_to', '>=', $currentDate)
                ->where('hot_deal_status', '=', 1)
                ->select(
                    'users.id as business_id',
                    'profiles.id as profile_id',
                    'users.name',
                    'users.username',
                    'users.mobile_phone',
                    'profiles.user_avatar',
                    'profiles.address',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                    'countries.id as country_id',
                    'countries.country',
                    'businesses.sub_cat_id',
                    'subcategories.subcat_name',
                    DB::raw('DATE(users.created_at) as added_date'),
                    // hot deals
                    'hotdeals.id',
                    'hotdeals.id as hotdeal_id',
                    'hotdeals.hotdeal_slug',
                    'hotdeals.hot_deal_title',
                    'hotdeals.hot_deal_description',
                    'hotdeals.price_from',
                    'hotdeals.price_to',
                    'hotdeals.date_from',
                    'hotdeals.date_to',
                )->where(function ($query) use($searchy){
                    foreach ($searchy as $sl){
                        $query->orWhere('hotdeals.hot_deal_title', 'like', '%' . $sl . '%')
                            ->orWhere('hotdeals.hot_deal_description', 'like', '%' . $sl . '%');
                    }
                });

                $maxPrice = Hotdeal::max('price_to');
                if( empty($request->min_price) && !empty($request->max_price) ){
                    $searched->whereBetween('price_to', [0, $request->max_price]);
                }
                if( !empty($request->min_price) && empty($request->max_price) ){
                    $searched->whereBetween('price_to', [$request->min_price, $maxPrice]);
                }
                if( !empty($request->min_price) && !empty($request->max_price) ){
                    $searched->whereBetween('price_to', [$request->min_price, $request->max_price]);
                }
        }
        if($type == 1){ // Sales
            $searchType = 1;
            $searched->leftjoin('sales', 'sales.user_id', 'users.id')
                ->where('sales.saledate_from', '<=', $currentDate)
                ->where('sales.saledate_to', '>=', $currentDate)
                ->where('sales.sale_status', '=', 1)
                ->select(
                    'users.id as business_id',
                    'profiles.id as profile_id',
                    'users.name',
                    'users.username',
                    'users.mobile_phone',
                    'profiles.user_avatar',
                    'profiles.address',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                    'countries.id as country_id',
                    'countries.country',
                    'businesses.sub_cat_id',
                    'subcategories.subcat_name',
                    DB::raw('DATE(users.created_at) as added_date'),
                    // Sales
                    'sales.id as sale_id',
                    'sales.sale_title',
                    'sales.sale_slug',
                    'sales.sale_description',
                    'sales.normal_price',
                    'sales.sale_price',
                    'sales.saledate_from',
                    'sales.saledate_to',
                    'sales.sale_imageurl',
                );
                $maxPrice = Sale::max('sale_price');
                if( empty($request->min_price) && !empty($request->max_price) ){
                    $searched->whereBetween('sale_price', [0, $request->max_price]);
                }
                if( !empty($request->min_price) && empty($request->max_price) ){
                    $searched->whereBetween('sale_price', [$request->min_price, $maxPrice]);
                }
                if( !empty($request->min_price) && !empty($request->max_price) ){
                    $searched->whereBetween('sale_price', [$request->min_price, $request->max_price]);
                }
        }
        if($type==2){
            $searchType = 2;
            $searched->leftjoin('jobs', 'jobs.user_id', 'users.id')
                ->leftjoin('jobcategories', 'jobcategories.id', 'jobs.job_cat_id')
                ->where('jobs.job_status', '=', 1)
                ->orderBy('job_id','DESC')
                ->select(
                    'users.id as business_id',
                    'profiles.id as profile_id',
                    'users.name',
                    'users.username',
                    'users.mobile_phone',
                    'profiles.user_avatar',
                    'profiles.address',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                    'businesses.sub_cat_id',
                    'subcategories.subcat_name',
                    DB::raw('DATE(users.created_at) as added_date'),
                    // jobs
                    'jobs.id as job_id',
                    'jobs.job_title',
                    'jobs.job_cat_id',
                    'jobcategories.job_cat_name',
                    'jobs.job_slug',
                    'jobs.job_description',
                    'jobs.salary_from',
                    'jobs.min_exp',
                );

                if(!empty($request->job_cat_id)){
                    $searched->where('jobs.job_cat_id', $request->job_cat_id);
                }
                $maxPrice = Job::max('salary_from');
                if( empty($request->min_price) && !empty($request->max_price) ){
                    $searched->whereBetween('salary_from', [0, $request->max_price]);
                }
                if( !empty($request->min_price) && empty($request->max_price) ){
                    $searched->whereBetween('salary_from', [$request->min_price, $maxPrice]);
                }
                if( !empty($request->min_price) && !empty($request->max_price) ){
                    $searched->whereBetween('salary_from', [$request->min_price, $request->max_price]);
                }
        }
        if ($request->radius != 0) {

            $radius = $request->radius;
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://ip-api.com/json/'.$request->ip,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $data = json_decode($response,TRUE);

            if( isset($data['status'])){
                $lng = '76.7889';
                $lat = '30.7339';
            }else{
                $lng = $data['lon'];
                $lat = $data['lat'];
            }

            $searched->selectRaw("( 3959 * acos( cos( radians(" . $lat . ") ) *cos( radians( latitude ) )* cos( radians( longitude ) - radians(" . $lng . ")) + sin( radians(" . $lat . ") ) *sin( radians( latitude ) ) )) AS distance")
            ->having("distance", "<", $radius)
            ->orderBy("distance");
        }
        if( !empty($request->country_id) ){
            $searched->where('countries.id','=',$request->country_id);
        }else{
            $searched->where('countries.id','=', $country_id);
        }
        $searched->where('users.user_role', '1');
        $searched->where('users.user_status', '1');
        $searched->groupBy('users.id');
        $searched_data = $searched->inRandomOrder()->get();
        for($i=0; $i < count($searched_data); $i++) {

            // counting linke Hot-deals[5], Jobs[3], Videos[5], Sales[4] -- Start
                $hotdealsCount = Hotdeal::where('business_id', '=', $searched_data[$i]->profile_id)
                        ->where('date_from', '<=', $currentDate)
                        ->where('date_to', '>=', $currentDate)
                        ->where('hot_deal_status', '=', 1)
                        ->get()->count();
                $salesCount = Sale::where('user_id', '=', $searched_data[$i]->business_id)
                        ->where('saledate_from', '<=', $currentDate)
                        ->where('saledate_to', '>=', $currentDate)
                        ->where('sale_status', '=', 1)
                        ->get()->count();
                $jobsCount = Job::where('user_id', '=', $searched_data[$i]->business_id)->where('job_status', 1)->get()->count();
                // $videosCount = Video::where('user_id', '=', $searched_data[$i]->business_id)->where('video_status', 1)->get()->count();

                if( $searched_data[$i]['saledate_from'] ){
                    $searched_data[$i]['saledate_from'] = date("d-m-Y", strtotime($searched_data[$i]['saledate_from']));
                    $searched_data[$i]['saledate_to'] = date("d-m-Y", strtotime($searched_data[$i]['saledate_to']));
                }else{

                    $format_date_from = date("d-m-Y", strtotime($searched_data[$i]['date_from']));
                    $searched_data[$i]['date_from'] = $format_date_from;
                    $searched_data[$i]['date_to'] = date("d-m-Y", strtotime($searched_data[$i]['date_to']));
                }

                if( $searched_data[$i]['name'] ){
                    $firstLetter = strtoupper(substr($searched_data[$i]['name'], 0, 1));
                    $spacePosition = strpos($searched_data[$i]['name'], ' ');

                    if ($spacePosition !== false) {
                        $secondLetter = strtoupper(substr($searched_data[$i]['name'], $spacePosition + 1, 1));
                    } else {
                        $secondLetter = '';
                    }
                    $searched_data[$i]['first_last_letter'] = $firstLetter.$secondLetter;
                }

                $searched_data[$i]['hotdealsCount'] = $hotdealsCount > 0 ? $hotdealsCount : 0;
                $searched_data[$i]['salesCount'] = $salesCount > 0 ? $salesCount : 0;
                $searched_data[$i]['jobsCount'] = $jobsCount > 0 ? $jobsCount : 0;
                // $searched_data[$i]['videosCount'] = $videosCount > 0 ? $videosCount : 0;
            // counting linke Hot-deals[5], Jobs[3], Videos[5], Sales[4] -- End

            // Get all review & rating with rating avrage of business - Start
                $allReviews = Review::select('id', 'rating')->where('review_business_id', '=', $searched_data[$i]->business_id)->get();
                $searched_data[$i]['total_reviews'] = $allReviews->count();
                $searched_data[$i]['total_ratings'] = $allReviews->sum('rating');
                if ($searched_data[$i]['total_ratings'] > 0) {
                    $searched_data[$i]['rating_avrage'] = round($searched_data[$i]['total_ratings'] / $searched_data[$i]['total_reviews'],1);
                } else {
                    $searched_data[$i]['rating_avrage'] = 0;
                }
            // Get all review & rating with rating avrage of business - End

            // Get all user Wishlist of logged user - Start
                if($searched_data[$i]->hotdeal_id || $searched_data[$i]->sale_id || $searched_data[$i]->job_id){
                    if($searched_data[$i]->hotdeal_id){ // Hot-Deals
                        $deal_id = $searched_data[$i]->hotdeal_id;
                    }else if($searched_data[$i]->sale_id){ // sales
                        $deal_id = $searched_data[$i]->sale_id;
                    }else if($searched_data[$i]->job_id){ // Jobs
                        $deal_id = $searched_data[$i]->job_id;
                    }
                    $user_added_wishlist = Wishlist::Where('services_id', $deal_id)->where('user_id', '=', $request->loggedUser_id)->first();
                    $searched_data[$i]['user_added_wishlist'] = $user_added_wishlist;
                }
            // Get all user Wishlist of logged user - End

        }
        $searched_data = json_decode(json_encode($searched_data));

        foreach ($searched_data as $item) {
            if ($item->hotdeal_single_image === null) {
                $item->hotdeal_single_image = (object)[
                    'id' => "",
                    'hotdeal_id' => "",
                    'hotdeal_img_url' => "",
                    'image_status' => "",
                    'created_at' => "",
                    'updated_at' => "",
                    'deleted_at' => ""
                ];
            } else {
                $item->hotdeal_single_image = (object)$item->hotdeal_single_image;
            }
        }
        return response([
            'success' => 'success',
            'result' => 200,
            'message' => 'All Categories, Cities & Deals Searched data get.',
            'searchType' => $searchType,
            'maxPrice' => $maxPrice,
            'filter_data' => $searched_data
        ]);
    }
    # To filter the data for all the hotdeals and sales ( function end )

    # To filter the cities by country id ( function start )
    public function filterCities( Request $request ){
        if(!empty($request->country_id)){
            $country_data = Country::join('states','states.country_id','=','countries.id')
            ->join('cities','cities.state_id','=','states.id')
            ->where('countries.id',$request->country_id)
            ->select('states.id as state_id','states.state','cities.id as city_id','cities.city')
            ->get();
            return response([
                'success' => true,
                'country_id' => $request->country_id,
                'cities' => $country_data
            ]);
        }else{
            return response([
                'success' => false,
                'message' => 'country_id is required to get the cities!'
            ],401);
        }
    }
    # To filter the cities by country id ( function end )

    # To filter the cities by country id and keyword ( function start )
    // public function filterCitiesByKeyword( Request $request ){

    //     if(!empty($request->country_id) && !empty($request->keyword) ){
    //         $country_data = Country::join('states','states.country_id','=','countries.id')
    //         ->join('cities','cities.state_id','=','states.id')
    //         ->where('countries.id',$request->country_id)
    //         ->where('cities.city',"like", "%" . $request->keyword . "%")
    //         ->take(3)
    //         ->select('states.id as state_id','states.state','cities.id as city_id','cities.city')
    //         ->get();
    //         return response([
    //             'success' => true,
    //             'country_id' => $request->country_id,
    //             'cities' => $country_data
    //         ]);
    //     }else{
    //         return response([
    //             'success' => false,
    //             'message' => 'country_id is required to get the cities!'
    //         ],401);
    //     }
    // }

    public function filterCitiesByKeyword(Request $request)
    {
        $request->validate([
          'country_id' => 'required|integer|exists:countries,id',
          'keyword'    => 'nullable|string|max:100',
        ]);
    
        // build the base query
        $q = Country::join('states', 'states.country_id', '=', 'countries.id')
                    ->join('cities', 'cities.state_id',   '=', 'states.id')
                    ->where('countries.id', $request->country_id);
    
        // only filter by keyword if one was provided
        if ($request->filled('keyword')) {
            $q->where('cities.city', 'like', '%' . $request->keyword . '%');
        }
    
        // take at most 3 and select just the fields we need
        $cities = $q
            ->select([
                'states.id   as state_id',
                'states.state',
                'cities.id   as city_id',
                'cities.city',
            ])
            ->get();
    
        return response()->json([
          'success'    => true,
          'country_id' => (int)$request->country_id,
          'cities'     => $cities,
        ]);
    }
    

    # To filter the cities by country id and keyword ( function end )
}
