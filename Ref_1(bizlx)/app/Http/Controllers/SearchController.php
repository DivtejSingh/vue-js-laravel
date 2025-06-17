<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Business;
use App\Models\Subcategory;
use App\Models\Category;
use App\Models\Profile;
use App\Models\City;
use App\Models\Job;
use App\Models\Jobtype;
use App\Models\Hotdeal;
use App\Models\Video;
use App\Models\Businessfeature;
use App\Models\Jobcategory;
use App\Models\Review;
use App\Models\Sale;
use App\Models\Wishlist;
use App\Models\User;

class SearchController extends Controller
{
    public function catSearchGet()
    {
        $subcategories = Subcategory::orderBy('subcat_name', 'ASC')->get();

        $cities = City::leftjoin('states', 'cities.id','=','states.id')
            ->select('cities.*','states.state')
            ->get();

            $sortedArray = collect($cities)->sortBy(function ($item) {
                return $item['city'];
            })->values()->all();

        return response()->json(
            [
                'result' => 200,
                'message' => 'Categories with Cities',
                'subcategories' => $subcategories,
                'cities' => $sortedArray
            ]
        );
    }
    // public function catSearchGetByKeyword( Request $request )
    // {
    //     if( !empty($request->keyword) ){
    //         $subcategories = Subcategory::orderBy('subcat_name', 'ASC')
    //         ->where('subcat_name',"like", "%" . $request->keyword . "%")
    //         ->take(3)
    //         ->get();

    //         $cities = City::leftjoin('states', 'cities.id','=','states.id')
    //             ->select('cities.*','states.state')
    //             ->get();
    
    //             $sortedArray = collect($cities)->sortBy(function ($item) {
    //                 return $item['city'];
    //             })->values()->all();
    
    //         return response()->json(
    //             [
    //                 'result' => 200,
    //                 'message' => 'Categories with Cities',
    //                 'subcategories' => $subcategories,
    //                 'cities' => $sortedArray
    //             ]
    //         );
    //     }else{
    //         return response()->json([]);
    //     }
    // }


    public function catSearchGetByKeyword(Request $request)
{
    if (!empty($request->keyword)) {
        // Fetch subcategories based on the search keyword
        $subcategories = Subcategory::orderBy('subcat_name', 'ASC')
            ->where('subcat_name', 'like', "%" . $request->keyword . "%")
            ->take(3)
            ->get();

        // Fetch main categories based on the search keyword
        // $mainCategories = Category::orderBy('cat_name', 'ASC')
        //     ->where('cat_name', 'like', "%" . $request->keyword . "%")
        //     ->take(3)
        //     ->get();

        // Fetch job categories based on the search keyword
        $jobCategories = JobCategory::orderBy('job_cat_name', 'ASC')
            ->where('job_cat_name', 'like', "%" . $request->keyword . "%")
            ->take(3)
            ->get();

        // Fetch cities (unchanged, using the current logic)
        $cities = City::leftJoin('states', 'cities.state_id', '=', 'states.id')
            ->select('cities.*', 'states.state')
            ->get();

        // Sort cities alphabetically
        $sortedArray = collect($cities)->sortBy(function ($item) {
            return $item['city'];
        })->values()->all();

        // Return all the categories and cities together in the response
        return response()->json(
            [
                'result' => 200,
                'message' => 'Categories, Job Categories, and Cities',
                'subcategories' => $subcategories,
                // 'mainCategories' => $mainCategories,
                'jobCategories' => $jobCategories,
                'cities' => $sortedArray
            ]
        );
    } else {
        return response()->json([]);
    }
}

    public function searchCategoryPostGet(Request $request)
    {
        $currentDate = date('Y-m-d');
        $deals = strtolower($request->deal);
        $searched = User::query();
        $searched->leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('businesses', 'businesses.business_id', 'users.id')
            ->leftjoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id');
            if(!empty($request->selectSubCategoty) || !empty($request->selectCity) || !empty($deals)){

                if(!empty($request->selectSubCategoty)){ // sub category
                    $searchType = 'sub-category';
                    $searched->where('businesses.sub_cat_id', $request->selectSubCategoty)
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
                    );
                }
                if(!empty($request->selectCity)){ // City
                    $searchType = 'city';
                    $searched->where('profiles.city_id', $request->selectCity)
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
                    );
                }
                if($deals=="hot deals"){ // Hot deals
                    $searchType = 'hot deals';
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
                        );
                        if( $request->min_price !='' && $request->max_price !='' ){
                            if( $request->max_price != 0 ){
                                $searched->whereBetween('price_to', [$request->min_price, $request->max_price]);
                            }
                        }
                }
                if($deals=="sales"){ // Sales
                    $searchType = 'sales';
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
                        if( $request->min_price !='' && $request->max_price !='' ){
                            if( $request->max_price != 0 ){
                                $searched->whereBetween('sale_price', [$request->min_price, $request->max_price]);
                            }
                        }
                }
                if($deals=="jobs"){
                    $searchType = 'jobs';
                    $searched->leftjoin('jobs', 'jobs.user_id', 'users.id')
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
                            'jobs.job_slug',
                            'jobs.job_description',
                            'jobs.salary_from',
                            'jobs.min_exp',
                        );
                        if( $request->min_price !='' && $request->max_price !='' ){
                            if( $request->max_price != 0 ){
                                $searched->whereBetween('salary_from', [$request->min_price, $request->max_price]);
                            }
                        }
                }
                if($deals=="videos"){
                    
                    $searchType = 'videos';
                    $searched->leftjoin('videos', 'videos.user_id', 'users.id')
                    ->where('videos.video_status', '=', 1)
                    ->orderBy('video_id','DESC')
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
                        // Videos
                        'videos.id as video_id',
                        'videos.video_title',
                        'videos.video_slug',
                        'videos.video_description',
                        'videos.video_url',
                        'videos.youtube_id',
                    );
                }
                $searched->where('users.user_role', '1');
                $searched->where('users.user_status', '1');
                $searched->groupBy('users.id');
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

                $searched_data = $searched->inRandomOrder()->get();
            }
            
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
                    $videosCount = Video::where('user_id', '=', $searched_data[$i]->business_id)->where('video_status', 1)->get()->count();
                    
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
                    $searched_data[$i]['videosCount'] = $videosCount > 0 ? $videosCount : 0;
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
                    if($searched_data[$i]->hotdeal_id || $searched_data[$i]->sale_id || $searched_data[$i]->job_id || $searched_data[$i]->video_id){
                        if($searched_data[$i]->hotdeal_id){ // Hot-Deals
                            $deal_id = $searched_data[$i]->hotdeal_id;
                        }else if($searched_data[$i]->sale_id){ // sales
                            $deal_id = $searched_data[$i]->sale_id;
                        }else if($searched_data[$i]->job_id){ // Jobs
                            $deal_id = $searched_data[$i]->job_id;
                        }else if($searched_data[$i]->video_id){ // videos
                            $deal_id = $searched_data[$i]->video_id;
                        }
                        $user_added_wishlist = Wishlist::Where('services_id', $deal_id)->where('user_id', '=', $request->loggedUser_id)->first();
                        $searched_data[$i]['user_added_wishlist'] = $user_added_wishlist;
                    }
                // Get all user Wishlist of logged user - End
            }

        return response()->json(
            [
                'result' => 200,
                'message' => 'All Categories, Cities & Deals Searched data get.',
                'searchType' => $searchType,
                'searched_data' => $searched_data
            ]
        );
    }
// public function businessfeaturedGet(Request $request)
// {
//     $city_id = $request->city_id;
//     $cat_id = $request->cat_id;

//     $query = Businessfeature::with(['galleryimage', 'cities', 'categories'])
//         ->join('users', 'users.id', '=', 'businessfeatures.user_id')
//         ->join('profiles', 'profiles.user_id', '=', 'users.id')
//         // ->join('businesses', 'businesses.id', '=', 'profiles.user_id')
//         ->join('businesses', 'businesses.business_id', '=', 'users.id')

//         ->join('categories', 'categories.id', '=', 'businesses.cat_id')
//         ->join('subcategories', 'subcategories.id', '=', 'businesses.sub_cat_id')
//         ->join('states', 'states.id', '=', 'profiles.state_id')
//         ->leftJoin('businessfeature_city', 'businessfeature_city.businessfeature_id', '=', 'businessfeatures.id')
//         ->leftJoin('cities as fcities', 'fcities.id', '=', 'businessfeature_city.city_id')
//         ->leftJoin('businessfeature_category', 'businessfeature_category.businessfeature_id', '=', 'businessfeatures.id')
//         ->leftJoin('categories as fcategories', 'fcategories.id', '=', 'businessfeature_category.category_id')
//         ->select(
//             'users.id as user_id',
//             'users.name',
//             'users.username',
//             'users.email',
//             'users.mobile_phone',
//             'profiles.user_avatar',
//             'profiles.address',
//             'profiles.state_id',
//             'states.state',
//             'profiles.city_id',
//             'categories.cat_name',
//             'subcategories.subcat_name',
//             'businessfeatures.id as feature_id',
//             'businessfeatures.businessfeature_status'
//         )
//         ->where('businessfeatures.businessfeature_status', '=', 1)
//         ->where('users.user_role', '=', '1');

//     if ($city_id) {
//         $query->where(function($q) use ($city_id) {
//             $q->where('profiles.city_id', $city_id)
//               ->orWhere('businessfeature_city.city_id', $city_id);
//         });
//     }

//     if ($cat_id) {
//         $query->where(function($q) use ($cat_id) {
//             $q->where('businesses.cat_id', $cat_id)
//               ->orWhere('businessfeature_category.category_id', $cat_id);
//         });
//     }

//     $data = $query->groupBy('businessfeatures.id')->get();

//     // Map cities and categories from pivot
//     $featureIds = $data->pluck('feature_id');
//     $featuresWithRelations = Businessfeature::with(['cities', 'categories'])
//         ->whereIn('id', $featureIds)
//         ->get()
//         ->keyBy('id');

//     // $finalData = $data->map(function ($item) use ($featuresWithRelations) {
//     //     $feature = $featuresWithRelations->get($item->feature_id);
//     //     $item->featured_cities = $feature?->cities->pluck('city') ?? [];
//     //     $item->featured_categories = $feature?->categories->pluck('cat_name') ?? [];
//     //     return $item;
//     // });

//     $finalData = $data->map(function ($item) use ($featuresWithRelations) {
//         $feature = $featuresWithRelations[$item->feature_id] ?? null;
//         $item->featured_cities = optional($feature->cities)->pluck('city') ?? [];
//         $item->featured_categories = optional($feature->categories)->pluck('cat_name') ?? [];
//         return $item;
//     });
    
//     return response()->json([
//         'result' => 200,
//         'message' => 'Filtered featured businesses.',
//         'businessFeature' => $finalData,
//     ]);
// }

    
public function businessfeaturedGet(Request $request)
{
    $city_id = $request->city_id;
    $cat_id = $request->cat_id;

     $query = Businessfeature::with(['galleryimage', 'cities', 'subcategories'])
        ->join('users',    'users.id',      '=', 'businessfeatures.user_id')
        ->join('profiles', 'profiles.user_id','=', 'users.id')
        ->join('businesses','businesses.business_id','=', 'users.id')

        // ← here’s the fix:
        ->join('categories',    'categories.id',    '=', 'businesses.cat_id')
        ->join('subcategories', 'subcategories.id', '=', 'businesses.sub_cat_id')

        ->join('states', 'states.id', '=', 'profiles.state_id')
        ->leftJoin('businessfeature_city',        'businessfeature_city.businessfeature_id', '=', 'businessfeatures.id')
        ->leftJoin('cities as fcities',           'fcities.id',                          '=', 'businessfeature_city.city_id')
        ->leftJoin('businessfeature_subcategory','businessfeature_subcategory.businessfeature_id','=', 'businessfeatures.id')
        ->leftJoin('subcategories as fcategories','fcategories.id',                      '=', 'businessfeature_subcategory.subcategory_id')
        ->select(
            'users.id as user_id',
            'users.name',
            'users.username',
            'users.email',
            'users.mobile_phone',
            'profiles.user_avatar',
            'profiles.address',
            'profiles.state_id',
            'states.state',
            'profiles.city_id',
            'categories.cat_name',      // ← now comes from categories table
            'subcategories.subcat_name',
            'businessfeatures.id as feature_id',
            'businessfeatures.businessfeature_status'
        )
        ->where('businessfeatures.businessfeature_status', '=', 1)
        ->where('users.user_role', '=', '1');

    if ($city_id) {
        $query->where(function($q) use ($city_id) {
            $q->where('profiles.city_id', $city_id)
              ->orWhere('businessfeature_city.city_id', $city_id);
        });
    }

    if ($cat_id) {
        $query->where(function($q) use ($cat_id) {
            $q->where('businesses.cat_id', $cat_id)
              ->orWhere('businessfeature_subcategory.subcategory_id', $cat_id);
        });
    }

    $data = $query->groupBy('businessfeatures.id')->get();

    // Map cities and categories from pivot
    $featureIds = $data->pluck('feature_id');
    $featuresWithRelations = Businessfeature::with(['cities', 'subcategories'])
        ->whereIn('id', $featureIds)
        ->get()
        ->keyBy('id');

    // $finalData = $data->map(function ($item) use ($featuresWithRelations) {
    //     $feature = $featuresWithRelations->get($item->feature_id);
    //     $item->featured_cities = $feature?->cities->pluck('city') ?? [];
    //     $item->featured_categories = $feature?->categories->pluck('cat_name') ?? [];
    //     return $item;
    // });

    $finalData = $data->map(function ($item) use ($featuresWithRelations) {
        $feature = $featuresWithRelations[$item->feature_id] ?? null;
        $item->featured_cities = optional($feature->cities)->pluck('city') ?? [];
        $item->featured_categories = optional($feature->subcategories)->pluck('subcat_name') ?? [];
        return $item;
    });
    
    return response()->json([
        'result' => 200,
        'message' => 'Filtered featured businesses.',
        'businessFeature' => $finalData,
    ]);
}  
    

    public function jobSearchGet()
    {
        //$jobtypes = Jobtype::Where('jobtype_status', '=', 1)->get();
        $jobtypes = Jobcategory::Where('job_cat_status', '=', 1)->get();
        return response()->json(
            [
                'result' => 200,
                'message' => 'Job types with Cities',
                'jobtypes' => $jobtypes,
            ]
        );
    }

    public function getAlljobs()
    {
        $allJobs = Job::join('jobtypes', 'jobtypes.id', 'jobs.job_type_id')
            ->join('profiles', 'profiles.user_id', 'jobs.user_id')
            ->join('users', 'users.id', 'jobs.user_id')
            ->join('jobcategories', 'jobcategories.id', 'jobs.job_cat_id')
            ->join('qualifications', 'qualifications.id', 'jobs.job_qualification_id')
            ->join('cities', 'cities.id', 'profiles.city_id')
            ->join('states', 'states.id', 'profiles.state_id')
            ->select('jobtypes.id as job_id', 'users.name', 'profiles.user_avatar', 'jobtypes.job_type', 'jobcategories.job_cat_name',
                    'jobs.job_title','jobs.job_slug','profiles.city_id', 'cities.city', 'profiles.state_id', 'states.state', 'jobs.job_description', 'jobs.job_description',
                    'jobs.salary_from', 'jobs.min_exp', 'qualifications.qualification', 'jobs.job_status')->get();

            $allCities = Job::join('jobtypes', 'jobtypes.id', 'jobs.job_type_id')
                ->join('profiles', 'profiles.user_id', 'jobs.user_id')
                ->join('cities', 'cities.id', 'profiles.city_id')
                ->select('profiles.city_id', 'cities.city')->distinct('city_id')->get();

            $allJobTypes = Job::join('jobtypes', 'jobtypes.id', 'jobs.job_type_id')
                ->select('jobs.job_type_id', 'jobtypes.job_type')->distinct('job_type_id')->get();

        return response()->json(
            [
                'result' => 200,
                'message' => 'All jobs',
                'allJobs' => $allJobs,
                'allCities' => $allCities,
                'alljobTypes' => $allJobTypes,
            ]
        );
    }

    public function searchJobsCategory(Request $request)
    {
        $currentDate = date('Y-m-d');
        $searched = Job::query();
        $searched->Join('users', 'users.id', 'jobs.user_id')
            ->leftjoin('profiles', 'profiles.user_id', 'users.id')
            ->Leftjoin('businesses', 'businesses.business_id', 'users.id')
            ->Leftjoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id')
            ->leftjoin('jobcategories', 'jobcategories.id', 'jobs.job_type_id')
            ->leftjoin('cities', 'cities.id', 'jobs.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id');
            if(!empty($request->selectJobType)){ // Job type
                $searched->where('jobs.job_cat_id', $request->selectJobType);
            }
            if(!empty($request->selectCity)){ // city
                $searched->where('jobs.city_id', $request->selectCity);
            }
            // if(!empty($request->selectExperience)){ // Experience
            //     $searched->where('jobs.min_exp', $request->selectExperience);
            // }
            if (isset($request->selectExperience) && $request->selectExperience !== "") {
                $searched->where('jobs.min_exp', $request->selectExperience);
            }
            if(!empty($request->selectSubCategoty)){ // search with subCat from previous cat search page
                $searched->where('businesses.sub_cat_id', $request->selectSubCategoty);
            }
        $searched->select(
                'users.id as business_id',
                'users.name',
                'users.username',
                'profiles.id as profile_id',
                'profiles.user_avatar',
                'profiles.address',
                'profiles.city_id', 
                'cities.city',
                'profiles.state_id',
                'states.state',
                'businesses.sub_cat_id',
                'jobs.id as job_id', 
                'jobs.job_title',
                'jobs.job_slug',   
                'jobs.job_description', 
                'jobs.salary_from', 
                'jobs.min_exp',  
                'jobcategories.job_cat_name')
            ->where('jobs.job_status', '=', 1)
            ->orderBy('jobs.id', 'DESC')
            ->groupBy('users.id');

            if( $request->min_price =='' || $request->max_price =='' ){
                /*Do nothing*/
            }else{
                $searched->whereBetween('salary_from', [$request->min_price, $request->max_price])->orderBy('salary_from', 'asc');
            }

            if ($request->radius) {
                    
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
            $searched_data =  $searched->get();
        
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
                $videosCount = Video::where('user_id', '=', $searched_data[$i]->business_id)->where('video_status', 1)->get()->count();

                $searched_data[$i]['hotdealsCount'] = $hotdealsCount > 0 ? $hotdealsCount : 0;
                $searched_data[$i]['salesCount'] = $salesCount > 0 ? $salesCount : 0;
                $searched_data[$i]['jobsCount'] = $jobsCount > 0 ? $jobsCount : 0;
                $searched_data[$i]['videosCount'] = $videosCount > 0 ? $videosCount : 0;
            // counting linke Hot-deals[5], Jobs[3], Videos[5], Sales[4] -- End
            
            // Get all user Wishlist of logged user - Start
            $user_added_wishlist = Wishlist::Where('services_id', $searched_data[$i]->job_id)->where('user_id', '=', $request->loggedUser_id)->first();
            $searched_data[$i]['user_added_wishlist'] = $user_added_wishlist;
        }
        
        return response()->json(
            [
                'result' => 200,
                'message' => 'Job category with job type, city & experience',
                'searched_data' => $searched_data,
            ]
        );

    }
}
