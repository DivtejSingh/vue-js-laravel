<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\JobCategory;
use App\Models\Business;
use App\Models\City;
use App\Models\User;
use App\Models\Review;
use App\Models\Hotdeal;
use App\Models\Job;
use App\Models\Video;
use App\Models\Sale;
use File;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('subcats')->get();
        return view('sadmin.categories',compact('categories'));
    }

    public function getCategories()
    {
        $categories = Category::with('subcats')->orderBy('cat_name','ASC')->get();
        return response()->json(
            ['categories' => $categories]
        );
    }
    public function getMainCategories()
    {
        $maincategories = Category::get();
        return response()->json(
            ['maincategories' => $maincategories]
        );
    }
    public function getsubCatBycatId( Request $request )
    {
        if( $request->id ){
            $subcats = Subcategory::where('cat_id',$request->id)
            ->where('subcat_status',1)
            ->orderBy('subcat_name', 'ASC')
            ->get();
            return response()->json($subcats);
        }else{
            $subcats = Subcategory::where('subcat_status',1)
            ->orderBy('subcat_name', 'ASC')
            ->get();
            return response()->json($subcats);
        }
    }
    public function getSubcategoriesByKeyword( Request $request )
    {
        if( !empty($request->keyword) ){
            $subcats = Subcategory::where('subcat_status',1)
            ->where('subcat_name',"like", "%" . $request->keyword . "%")
            ->orderBy('subcat_name', 'ASC')
            ->take(3)
            ->get();
            return response()->json($subcats);
        }else{
            return response()->json([]);
        }
    }
    // public function getSubcategoriesByKeyword(Request $request)
    // {
    //     if (!empty($request->keyword)) {
    //         // Fetch subcategories based on the search keyword
    //         $subcats = Subcategory::where('subcat_status', 1)
    //             ->where('subcat_name', "like", "%" . $request->keyword . "%")
    //             ->orderBy('subcat_name', 'ASC')
    //             ->take(3)
    //             ->get();
    
    //         // Fetch main categories based on the search keyword
    //         $mainCategories = Category::where('cat_status', 1)
    //             ->where('cat_name', "like", "%" . $request->keyword . "%")
    //             ->orderBy('cat_name', 'ASC')
    //             ->take(3)
    //             ->get();
    
    //         // Fetch job categories based on the search keyword
    //         $jobCategories = JobCategory::where('job_cat_status', 1)
    //             ->where('job_cat_name', "like", "%" . $request->keyword . "%")
    //             ->orderBy('job_cat_name', 'ASC')
    //             ->take(3)
    //             ->get();
    
    //         // Fetch cities (unchanged, using the current logic)
    //         $cities = City::leftJoin('states', 'cities.state_id', '=', 'states.id')
    //             ->select('cities.*', 'states.state')
    //             ->get();
    
    //         // Sort cities alphabetically
    //         $sortedArray = collect($cities)->sortBy(function ($item) {
    //             return $item['city'];
    //         })->values()->all();
    
    //         // Return all the categories, job categories, and cities together in the response
    //         return response()->json([
    //             'result' => 200,
    //             'message' => 'Categories, Job Categories, and Cities',
    //             'subcategories' => $subcats,
    //             'mainCategories' => $mainCategories,
    //             'jobCategories' => $jobCategories,
    //             'cities' => $sortedArray
    //         ]);
    //     } else {
    //         return response()->json([]);
    //     }
    // }
    
    public function getSubCategories()
    {
        $subcategories = DB::table('categories')
            ->Join('subcategories','subcategories.cat_id','categories.id')
            ->get();
        return response()->json(
            ['subcategories' => $subcategories]
        );
    }
    public function getSubCategoriesbyID($id)
    {
        $subcategory = DB::table('subcategories')
            ->where('id', $id)
            ->first();
    
        return response()->json($subcategory);
    }
    
    public function getJobCategoriesbyID($id)
    {
        $jobcategories = DB::table('jobcategories')
            ->where('id', $id)
            ->first();
    
        return response()->json($jobcategories);
    }
    
    public function admingetSubCategories()
    {
        $subcategories = Subcategory::get();
        return response()->json(
            ['subcategories' => $subcategories]
        );
    }
    public function adminAllCatDrop()
    {
        $maincategories = Category::Where('cat_status', '1')->get();
        return response()->json(
            ['maincategories_drop' => $maincategories]
        );
    }
    public function adminSubcatAdd(Request $request){

        $subCat = new Subcategory();
        $subCat->cat_id = $request->cat_id;
        $subCat->subcat_name = $request->subcat_name;
            $file = $request->file('new_subCat_img_url');
        $subCat->subcat_img_url = $subCat->add_image($file);
        $subCat->subcat_slug = strtolower(Str::slug($request->subcat_name, '-'));
            if( $request->subcat_feature == ''){
                $subCat->subcat_feature = 0;
            }elseif( $request->subcat_feature == 1 ){
                $subCat->subcat_feature = 1;
            }else{
                $subCat->subcat_feature = 0;
            }
        $subCat->subcat_status = $request->subcat_status;
        $save_SubCategory = $subCat->save();
        if ($save_SubCategory==true) {
            return response()->json([
                'status' => 200,
                'message' => "Sub-Category added successfully.",
                'data' => $subCat
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => "Something went worng."
            ]);
        }
    }
    public function editSubCategories(Request $request, $id)
    {
        $edit_subCategory = Subcategory::find($id);
        $edit_subCategory->cat_id = $request->cat_id;
        $edit_subCategory->subcat_name = $request->subcat_name;
            if($request->new_subCat_img_url){ // file upload from model
                $file = $request->file('new_subCat_img_url');
                $edit_subCategory->subcat_img_url = $edit_subCategory->edit_image($edit_subCategory->subcat_img_url, $file);
            }
        $edit_subCategory->subcat_slug = strtolower(Str::slug($request->subcat_name, '-'));
        $edit_subCategory->subcat_feature = $request->subcat_feature;
            if( $request->subcat_status == "true" ){
                $edit_subCategory->subcat_status = 1;
            }elseif( $request->subcat_status == "1" ){
                $edit_subCategory->subcat_status = 1;
            }else{
                $edit_subCategory->subcat_status = 0;
            }
        $update_subcategory = $edit_subCategory->update();
        if ($update_subcategory==true) {
            return response()->json([
                'status' => 200,
                'message' => "SubCategory updated successfully.",
                'data' => $edit_subCategory
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => "Something went worng."
            ]);
        }
    }
    // Single sub-cat delete
    public function adminSubcategoryDelete($subCat_id){
        $delete_subCategory = Subcategory::where('id', $subCat_id)->first();
        if($delete_subCategory){
            if (File::exists(public_path().$delete_subCategory->subcat_img_url)) {
                unlink(public_path().$delete_subCategory->subcat_img_url);
            }
            if(Storage::disk('s3')->exists($delete_subCategory->subcat_img_url)){
                Storage::disk('s3')->delete($delete_subCategory->subcat_img_url);
            }
            $delete = $delete_subCategory->delete();
            if ($delete==true) {
                return response()->json([
                    'status' => 200,
                    'message' => "Sub-Category deleted successfully.",
                    'data' => $delete_subCategory
                ]);
            }
        }else{
            return response()->json([
                'status' => 400,
                'message' => "Something went worng."
            ]);
        }
    }
    // multiple sub-cat delete
    public function adminMultipleSubcategoryDelete(Request $request){
        foreach ($request->ids as $subCat_id) {
            $delete_subCat = Subcategory::Where('id', $subCat_id)->first();
            if (File::exists(public_path().$delete_subCat->subcat_img_url)) {
                unlink(public_path().$delete_subCat->subcat_img_url);
            }
            if(Storage::disk('s3')->exists($delete_subCat->subcat_img_url)){
                Storage::disk('s3')->delete($delete_subCat->subcat_img_url);
            }
            $delete_subCat->delete();
        }
        return response()->json([
            'status' => 200,
            'message' => "Sub-Category deleted successfully."
        ]);
    }

    public function getPopularCategories()
    {
        $popsubcats = Cache::remember('popular_subcategories', 60, function() {
            return DB::table('subcategories')
                ->where('subcategories.subcat_feature', 1)
                ->where('subcategories.subcat_status', 1)
                ->select('id', 'subcat_name', 'subcat_img_url', 'subcat_slug', 'subcat_feature', 'subcat_status')
                ->get();
        });
        return response()->json(
            ['populars' => $popsubcats],200);
    }

    public function getPopularCities($subCat_id)
    {
        $allCities = Business::join('profiles', 'profiles.user_id', 'businesses.business_id')
            ->join('cities', 'cities.id', 'profiles.city_id')
            ->where('businesses.sub_cat_id', '=', $subCat_id)
            ->Select('businesses.sub_cat_id', 'profiles.city_id', 'cities.city')
            ->distinct('profiles.city_id')
            ->orderBy('city','ASC')
            ->get();

        return response()->json(
            ['popularCatCities' => $allCities],200
        );
    }
    public function getPopularCitiesBusiness($city_id, $sub_cat_id)
    {

        $cityDetails = Subcategory::Join('businesses', 'businesses.sub_cat_id', 'subcategories.id')
            ->Join('profiles', 'profiles.user_id', 'businesses.business_id')
            ->join('cities', 'cities.id', 'profiles.city_id')
            ->Select('subcategories.id as subcat_id', 'subcategories.subcat_name', 'profiles.city_id', 'cities.city')
            ->Where('subcategories.id', '=', $sub_cat_id)->first();

        $businessList = Business::Join('subcategories','businesses.sub_cat_id', 'subcategories.id')
            ->Join('profiles', 'profiles.user_id','businesses.business_id')
            ->Join('users', 'users.id', 'profiles.user_id')
            ->join('cities', 'cities.id', 'profiles.city_id')
            ->join('states', 'states.id', 'profiles.state_id')
            ->select(['businesses.business_id', 'businesses.sub_cat_id', 'subcategories.subcat_name','profiles.city_id',
                    'cities.city','profiles.state_id','states.state','profiles.address', 'profiles.user_id','users.name','users.email','users.username',
                    'users.mobile_phone','users.user_role', 'profiles.user_avatar'])
            ->where('profiles.city_id', '=', $city_id)
            ->where('businesses.sub_cat_id', '=', $sub_cat_id)
            ->inRandomOrder()
            ->get();

            for($i=0; $i < count($businessList); $i++) { // count linke Hot deals[5], Jobs[3], Videos[5], Sales[4] &
                // count linke Hot deals[5], Jobs[3], Videos[5], Sales[4]
                $hotdealsCount = Hotdeal::where('business_id', '=', $businessList[$i]->user_id)->get()->count();
                $jobsCount = Job::where('user_id', '=', $businessList[$i]->user_id)->get()->count();
                $videosCount = Video::where('user_id', '=', $businessList[$i]->user_id)->get()->count();
                $salesCount = Sale::where('user_id', '=', $businessList[$i]->user_id)->get()->count();
                $businessList[$i]['hotdealsCount'] = $hotdealsCount > 0 ? $hotdealsCount : 0;
                $businessList[$i]['jobsCount'] = $jobsCount > 0 ? $jobsCount : 0;
                $businessList[$i]['videosCount'] = $videosCount > 0 ? $videosCount : 0;
                $businessList[$i]['salesCount'] = $salesCount > 0 ? $salesCount : 0;
                // rating
                $allReviews = Review::select('review_business_id', 'rating')->where('review_business_id', '=', $businessList[$i]->user_id)->get();
                $businessList[$i]['total_reviews'] = $allReviews->count();
                $businessList[$i]['total_ratings'] = $allReviews->sum('rating');
                if ($businessList[$i]['total_ratings'] > 0) {
                    $businessList[$i]['rating_avrage'] = round($businessList[$i]['total_ratings'] / $businessList[$i]['total_reviews'],1);
                } else {
                    $businessList[$i]['rating_avrage'] = 0;
                }
            }
        return response()->json(
            [
                'status' => 200,
                'message' => "Popular category of city.",
                'cityDetails' => $cityDetails,
                'businessList' => $businessList,
            ]
        );
    }

    # To search the cities by radius ( function start )
    public function getCitiesByRadius( Request $request ){

        $sub_cat_id = $request->sub_cat_id;
        $city_id =  $request->city_id;

        $cityDetails = Subcategory::leftjoin('businesses', 'businesses.sub_cat_id', 'subcategories.id')
            ->leftjoin('profiles', 'profiles.user_id', 'businesses.business_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->Select('subcategories.id as subcat_id', 'subcategories.subcat_name', 'profiles.city_id', 'cities.city')
            ->Where('subcategories.id', '=', $sub_cat_id)->first();

            $searchBusiness = Business::query();

            $searchBusiness->leftjoin('subcategories','businesses.sub_cat_id', 'subcategories.id')
            ->leftjoin('profiles', 'profiles.user_id','businesses.business_id')
            ->leftjoin('users', 'users.id', 'profiles.user_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id');

            $searchBusiness->select(['businesses.business_id', 'businesses.sub_cat_id', 'subcategories.subcat_name','profiles.city_id',
                    'cities.city','profiles.state_id','states.state','profiles.address', 'profiles.user_id','users.name','users.username','users.email',
                    'users.mobile_phone','users.user_role', 'profiles.user_avatar']);
            if( $request->radius ){
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

                    $searchBusiness->selectRaw("( 3959 * acos( cos( radians(" . $lat . ") ) *cos( radians( latitude ) )* cos( radians( longitude ) - radians(" . $lng . ")) + sin( radians(" . $lat . ") ) *sin( radians( latitude ) ) )) AS distance")
                    ->having("distance", "<", $radius)
                    ->orderBy("distance");
            }
            $searchBusiness->where('profiles.city_id', '=', $city_id)
            ->where('businesses.sub_cat_id', '=', $sub_cat_id);

            $businessList =  $searchBusiness->get();

            for($i=0; $i < count($businessList); $i++) { // count linke Hot deals[5], Jobs[3], Videos[5], Sales[4] &
                // count linke Hot deals[5], Jobs[3], Videos[5], Sales[4]
                $hotdealsCount = Hotdeal::where('business_id', '=', $businessList[$i]->user_id)->get()->count();
                $jobsCount = Job::where('user_id', '=', $businessList[$i]->user_id)->get()->count();
                $videosCount = Video::where('user_id', '=', $businessList[$i]->user_id)->get()->count();
                $salesCount = Sale::where('user_id', '=', $businessList[$i]->user_id)->get()->count();
                $businessList[$i]['hotdealsCount'] = $hotdealsCount > 0 ? $hotdealsCount : 0;
                $businessList[$i]['jobsCount'] = $jobsCount > 0 ? $jobsCount : 0;
                $businessList[$i]['videosCount'] = $videosCount > 0 ? $videosCount : 0;
                $businessList[$i]['salesCount'] = $salesCount > 0 ? $salesCount : 0;
                // rating
                $allReviews = Review::select('review_business_id', 'rating')->where('review_business_id', '=', $businessList[$i]->user_id)->get();
                $businessList[$i]['total_reviews'] = $allReviews->count();
                $businessList[$i]['total_ratings'] = $allReviews->sum('rating');
                if ($businessList[$i]['total_ratings'] > 0) {
                    $businessList[$i]['rating_avrage'] = round($businessList[$i]['total_ratings'] / $businessList[$i]['total_reviews'],1);
                } else {
                    $businessList[$i]['rating_avrage'] = 0;
                }
            }
        return response()->json(
            [
                'status' => 200,
                'message' => "Popular category of city.",
                'cityDetails' => $cityDetails,
                'businessList' => $businessList,
            ]
        );
    }
    # To search the cities by radius ( function end )

    public function categoryAdd(Request $request)
    {
        $category = new Category();
        $category->cat_name = $request->cat_name;
            $file = $request->file('selectedFile');
        $category->cat_img_url = $category->add_image($file);
        $category->cat_slug = strtolower(Str::slug($request->cat_name, '-'));
        $category->cat_feature = $request->cat_feature;
        $category->cat_sort = $request->cat_sort;
        $category->cat_status = '1';
        $save_category = $category->save();
        if ($save_category==true) {
            return response()->json([
                'status' => 200,
                'message' => "Category added successfully.",
                'data' => $category
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => "Something went worng."
            ]);
        }
    }

    public function editCategory(Request $request)
    {
        $edit_category = Category::find($request->cat_id);
        $edit_category->cat_name = $request->cat_name;
        if($request->file('cat_img_url')){
            $file = $request->file('cat_img_url');
            $edit_category->cat_img_url = $edit_category->edit_image($edit_category->cat_img_url, $file);
        }
        $edit_category->cat_slug = strtolower(Str::slug($request->cat_name, '-'));
        $edit_category->cat_feature = $request->cat_feature;
        $edit_category->cat_sort = $request->cat_sort;
        if( $request->cat_status == '1' ){
            $edit_category->cat_status = '1';
        }else{
            $edit_category->cat_status = '0';
        }

        $edit_category->update();
        return response()->json([
            'status' => 200,
            'message' => "Category updated successfully.",
            'data' => $edit_category
        ]);
    }

    // delete category
    public function deleteCategory(Request $request)
    {
        $delete_category = Category::find($request->id);
        if(!empty($delete_category->cat_img_url)){
            $delete_category->delete_image($delete_category->cat_img_url);
        }
        $delete = $delete_category->delete();
        if ($delete==true) {
            return response()->json([
                'status' => 200,
                'message' => "Category deleted successfully.",
                'data' => $delete_category
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => "Something went worng."
            ]);
        }
    }
    public function adminDeleteMultipleCategory(Request $request){
        foreach ($request->ids as $cat_id) {
            $deleteCat = Category::Where('id', $cat_id)->first();
            if (File::exists(public_path().$deleteCat->cat_img_url)) {
                unlink(public_path().$deleteCat->cat_img_url);
            }
            if(Storage::disk('s3')->exists($deleteCat->cat_img_url)){
                Storage::disk('s3')->delete($deleteCat->cat_img_url);
            }
            $deleteCat->delete();
        }
        return response()->json([
            'status' => 200,
            'message' => "Category deleted successfully."
        ]);
    }






}
