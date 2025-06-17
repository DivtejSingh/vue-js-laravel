<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Changereseller;
use App\Models\Galleryimage;
use App\Models\Plan;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Service;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Hotdeal;
use App\Models\Sale;
use App\Models\Saleimage;
use App\Models\Job;
use App\Models\Hotdealimage;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use File;
use DateTime;
use DateInterval;
use Tymon\JWTAuth\Facades\JWTAuth;

class ResellerBusinessController extends Controller
{

    public function userGalleryImagesbyadmin(Request $request)
    {
        $userId = $request->userId;
        $get_gallery_images = Galleryimage::where('user_id', $userId)->where('image_type', 0)->get();
        return response([
            'success' => true,
            'gallery_images' => $get_gallery_images
        ]);
        
    }
    # To Login reseller as a  business (function start)
    public function resellerbusinessLogin(Request $request)

    {
        $business = User::Where('email', $request->email)->where('user_status','1')->first();
        //Request is validated
        //Crean token
        if( $business == null ){
            return response([
                                'success' => false,
                                'message' => 'Your account has been deactivated',
                            ],400);
        }else{
            $existing_business = Business::Where('business_id',$business->id)->first();
            $business->plan_id = $existing_business->plan_id;
        }

        //Token created, return with success response and jwt token
        return response()->json(
            [
                'success' => true,
                'user'=> $business,
            ],200);
    }
    # To Login reseller as a  business (function end)

    # To get business profile by reseller (function start)
    public function ResellerBusinessProfileGet(Request $request)
{
    $buserid = $request->userId;

    $buser1 = DB::table('users')
        ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
        ->leftJoin('businesses', 'businesses.business_id', '=', 'users.id')
        ->leftJoin('categories', 'categories.id', '=', 'businesses.cat_id')
        ->leftJoin('subcategories', 'subcategories.id', '=', 'businesses.sub_cat_id')
        ->join('cities', 'cities.id', '=', 'profiles.city_id')
        ->join('states', 'states.id', '=', 'cities.state_id')
        ->leftJoin('services', 'services.service_user_id', '=', 'users.id')
        ->where('users.id', '=', $buserid)
        ->select(
            'users.id', 'users.name', 'users.email', 'users.username', 'users.mobile_phone',
            'users.password', 'profiles.user_avatar', 'profiles.about', 'profiles.address',
            'profiles.country_id', 'businesses.business_id', 'businesses.gst',
            'businesses.listedby', 'businesses.listedby_reseller_id',
            'categories.id AS cat_id', 'categories.cat_name',
            'subcategories.id AS subcat_id', 'subcategories.subcat_name',
            'cities.id AS city_id', 'cities.city',
            'states.id AS state_id', 'states.state',
            'services.service_user_id', 'services.service_text',
            'businesses.sub_cat_id'
        )
        ->first();

    if (!$buser1) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $buser1->service_text = json_decode($buser1->service_text);

    $ids = explode(",", $buser1->sub_cat_id);
    $subcats = DB::table('subcategories')
        ->whereIn('id', $ids)
        ->select('id', 'subcat_name')
        ->get();

    // Get reseller data
    $get_reseller_data = DB::table('users')
        ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
        ->join('cities', 'cities.id', '=', 'profiles.city_id')
        ->select(
            'users.id', 'users.name', 'users.email',  'users.username','users.mobile_phone',
            'profiles.id as profile_id', 'profiles.address',
            'cities.id as city_id', 'cities.city'
        )
        ->where('users.id', $buser1->listedby_reseller_id)
        ->get()
        ->toArray();

    foreach ($get_reseller_data as $user) {
        $get_reseller_data[0]->id = 'RES' . $user->id;
    }

    // 🌟 Get business feature status
    $feature = DB::table('businessfeatures')
        ->where('user_id', $buserid)
        ->where('businessfeature_status', 1)
        ->first();

    $featureStatus = $feature ? 1 : 0;

    // 🌟 Featured Cities
    $featuredCities = [];
    if ($feature) {
        $cityIds = DB::table('businessfeature_city')
            ->where('businessfeature_id', $feature->id)
            ->pluck('city_id');

        $featuredCities = DB::table('cities')
            ->whereIn('id', $cityIds)
            ->select('id', 'city')
            ->get();
    }

    // 🌟 Featured Categories
    $featuredCategories = [];
    if ($feature) {
        $catIds = DB::table('businessfeature_category')
            ->where('businessfeature_id', $feature->id)
            ->pluck('category_id');

        $featuredCategories = DB::table('categories')
            ->whereIn('id', $catIds)
            ->select('id', 'cat_name')
            ->get();
    }

    return response()->json([
        'buser' => $buser1,
        'subcatids' => $subcats,
        'reseller_data' => $get_reseller_data,
        'businessfeature_status' => $featureStatus,
        'feature_cities' => $featuredCities,
        'feature_categories' => $featuredCategories,
    ]);
}

    # To get business profile by reseller (function end)

    # To update business profile by reseller (function start)
   
    
    public function updateBprofilebyreseller(Request $request)
    {
        $data = $request->only('name', 'username', 'email', 'mobile_phone', 'userId');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'username' => 'required|string|alpha_dash',
            'email' => 'required|email',
            'mobile_phone' => 'required',
            'userId' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
    
        // Update user
        $update_business = User::find($request->userId);
        $update_business->name = $request->name;
        $update_business->email = $request->email;
        $update_business->username = $request->username;
        $update_business->mobile_phone = $request->mobile_phone;
        $update_business->update();
    
        // Update avatar
        $update_avatar = Profile::where('user_id', $request->userId)->first();
        if ($request->file('user_avatar')) {
            $call_function = new Profile();
            $file = $request->file('user_avatar');
            $old_file = $update_avatar->user_avatar;
            $update_avatar->user_avatar = $call_function->avatar($old_file, $file);
            $update_avatar->update();
        }
    
        // Handle featured toggle
        if ($request->has('is_featured') && $request->is_featured) {
            // Insert or update feature record
            $feature = DB::table('businessfeatures')->updateOrInsert(
                ['user_id' => $request->userId],
                ['businessfeature_status' => 1]
            );
    
            $featureId = DB::table('businessfeatures')->where('user_id', $request->userId)->value('id');
    
            // Sync cities
            DB::table('businessfeature_city')->where('businessfeature_id', $featureId)->delete();
            if (is_array($request->feature_cities)) {
                foreach ($request->feature_cities as $cityId) {
                    DB::table('businessfeature_city')->insert([
                        'businessfeature_id' => $featureId,
                        'city_id' => $cityId
                    ]);
                }
            }
    
            // Sync categories
            DB::table('businessfeature_category')->where('businessfeature_id', $featureId)->delete();
            if (is_array($request->feature_categories)) {
                foreach ($request->feature_categories as $catId) {
                    DB::table('businessfeature_category')->insert([
                        'businessfeature_id' => $featureId,
                        'category_id' => $catId
                    ]);
                }
            }
        } else {
            // Toggle off
            DB::table('businessfeatures')->where('user_id', $request->userId)
                ->update(['businessfeature_status' => 0]);
        }
    
        return response()->json([
            'buser' => 'Business Profile Updated Successfully.'
        ], 200);
    }
    
    # To update business profile by reseller (function end)

    # To update business profile2 by reseller (function start)
    public function updateBprofile2byreseller(Request $request){
        $data = $request->only('cat_id', 'subcat_id', 'state_id', 'city_id','mobile_phone', 'address');
        $validator = Validator::make($data, [
            'cat_id' => 'required',
            'subcat_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'mobile_phone' => 'required',
            'address' => 'required|string',
            // 'about' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{
            $update_business = User::find($request->userId);
            $update_business->mobile_phone = $request->mobile_phone;
            $longitude_latitude = apiResponse($request->address);
            $update_business->longitude = $longitude_latitude['longitude'];
            $update_business->latitude = $longitude_latitude['latitude'];
            $update_business->update();
            $subcatIds = explode(',', $request->subcat_id);
            Profile::Where('user_id', $request->userId)->update(
                [
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'about' => $request->about,
                    'address' => $request->address,
                ]);
            Business::Where('business_id', $request->userId)->update(
                [
                    'cat_id' => $request->cat_id,
                    'sub_cat_id' => implode(",", explode(',', $request->subcat_id)),
                    'gst' => $request->gst,
                ]);

            $Services = Service::where('service_user_id', $request->userId)->first();
              if (is_array($request->service_text)) {
                $array_service_text = $request->service_text;
              }else{
                $array_service_text = explode(',', $request->service_text);
              }

            if($Services == null){
                $service = new Service();
                $service->service_user_id = $request->userId;
                $service->service_text = $array_service_text;
                $service->save();
            }else{
                Service::Where('service_user_id', $request->userId)->update(
                    [
                        'service_text' => $array_service_text,
                    ]);

            }
            return response()->json(
                [
                    'buser' => 'Business Profile Updated Successfully.'
                ],200);
        }
    }
    # To update business profile2 by reseller (function end)

    # To update business password by reseller (function start)
    public function updatePasswordbyreseller(Request $request){
        if($request->userId){
            $update_password = User::find($request->userId);
            $password = Hash::make($request->password);
            $update_password->password = $password;
            $update_password->update();

            return response()->json(
                [
                    'buser' => 'Business Password Updated Successfully.'
                ],200);
        }
        else{
            return response()->json(
                [
                    'buser' => 'Business Password Not Updated!.'
                ],400);
        }
    }
    # To update business password by reseller (function end)

    # To update business reseller by reseller (function start)
    public function updateResellerIdByreseller(Request $request)
    {
        $buser_id = $request->userId;
        if (!$buser_id) {
            return response([
                                'success' => false,
                                'message' => 'User not found !'
                            ]);
        } else {
            $data = $request->only('old_reseller_id', 'new_reseller_id');
            $validator = Validator::make($data, [
                'old_reseller_id' => 'required',
                'new_reseller_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $check_existing_reseller = User::Where('user_role', '2')->Where('id', $request->new_reseller_id)->get();
                $check_existing_reseller_count = count($check_existing_reseller);
                $old_id = str_replace("RES", '', $request->old_reseller_id);
                if ($check_existing_reseller_count > 0) {
                    Business::Where('listedby', 1)->where('listedby_reseller_id', $old_id)->update(
                        [
                            'listedby_reseller_id' => $request->new_reseller_id
                        ]);

                    $service = new Changereseller();
                    $service->business_id = $buser_id;
                    $service->old_reseller_id = $old_id;
                    $service->new_reseller_id = $request->new_reseller_id;
                    $service->reason_text = $request->reason_text;
                    $service->save();

                    return response([
                                        'success' => true,
                                        'message' => 'Reseller updated successfully'
                                    ], 200);
                } else {

                    return response([
                                        'success' => false,
                                        'message' => 'This reseller id not exist please try another one'
                                    ]);
                }
            }
        }
    }
    # To update business reseller by reseller (function end)

    # To save business cover image by reseller (function start)
    public function saveCoverimagesByreseller(Request $request)
    {
        $user_id = $request->userId;
        $get_gallery_data = Galleryimage::Where('user_id', $user_id)->Where('image_type', '1')->get();
        $get_gallery_data_count = count($get_gallery_data);
        $get_coverimages_count = 3;

        if( $get_gallery_data_count < $get_coverimages_count ){
            if ($request->file('image_url')) {
                $destinationPath = '/images/cover_images';
                $path = public_path().$destinationPath;
                $file = $request->file('image_url');
                if ( ! File::exists($path)) {
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $real_name = time().hexdec(rand(11111, 99999)).'.'.$file->getClientOriginalExtension(); // type change
                $img = Image::make($file)->resize(1280, null,function($constraint){
                    $constraint->aspectRatio();
                    $constraint->upsize();
                    }); // resize
                $final_path = $destinationPath."/".$real_name;
                Storage::disk('s3')->put($final_path,$img->stream()->__toString());
            }
            $cover_image = new Galleryimage;
            $cover_image->user_id = $user_id;
            $cover_image->image_title = $request->image_title;
            $cover_image->image_type = $request->image_type;
            $cover_image->image_description = $request->image_description;
            $cover_image->image_url = $final_path;
            $cover_image->save();
            return response()->json(
                [
                    'message' => "Cover Image Added Successfully.",
                ],
                200
            );
        }else{
            return response()->json(
                [
                    'message' => "You Are Max 3 Cover Images Can Be Loaded.",
                ]);
        }
    }
    # To save business cover image by reseller (function end)

    # To get business cover image by reseller (function start)
    public function coverImageByreseller(Request $request){
        $User = $request->userId;
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ]);
        }else{
            $get_cover_image = Galleryimage::where('user_id', $User)->where('image_type',1)->get();
            return response([
                'success' => true,
                'cover_image' => $get_cover_image
            ]);
        }
    }
    # To get business cover image by reseller(function end)

    // # To save business gallery image by reseller (function start)
    // public function saveGalleryimagesByreseller(Request $request)
    // {
    //     $user_id = $request->userId;
    //     $business_data = Business::Where('business_id',$user_id)->first();
    //     $user_plan_id = $business_data->plan_id;
    //     $business_plan = Plan::Where('id',$user_plan_id)->first();
    //     $get_gallery_data = Galleryimage::Where('user_id',$user_id)->Where('image_type', '0')->get();

    //     if(count($get_gallery_data) > 0){
    //         if($business_plan->images > $get_gallery_data->count()){

    //             if($request->file('image_url')) {

    //                 $destinationPath = '/images/gallery_images';
    //                 $path = public_path().$destinationPath;
    //                 $file = $request->file('image_url');
    //                 $real_name = time().hexdec(rand(11111, 99999)).'.'.$file->getClientOriginalExtension(); // type change
    //                 $img = Image::make($file)->resize(1280, null,function($constraint){
    //                     $constraint->aspectRatio();
    //                     $constraint->upsize();
    //                     }); // resize
    //                 $final_path = $destinationPath."/".$real_name;
    //                 Storage::disk('s3')->put($final_path,$img->stream()->__toString());
    //             }
                // $cover_image = new Galleryimage;
                // $cover_image->user_id = $user_id;
                // $cover_image->image_title = $request->image_title;
                // $cover_image->price = $request->price;
                // $cover_image->image_type = $request->image_type;
                // $cover_image->image_description = $request->image_description;
                // $cover_image->image_url = $final_path;
                // $cover_image->save();
    //             return response()->json(
    //                 [
    //                     'message' => "Gallery Image Added Successfully.",
    //                 ],200);
    //         }else{
    //             return response()->json(
    //                 [
    //                     'message' => "You have already added " .$get_gallery_data->count().' Gallery Images',
    //                 ],200);
    //         }
    //     }else{
    //         if($request->file('image_url')) {

    //             $destinationPath = '/images/gallery_images';
    //             $path = public_path().$destinationPath;
    //             $file = $request->file('image_url');
    //             $real_name = time().hexdec(rand(11111, 99999)).'.'.$file->getClientOriginalExtension(); // type change
    //             $img = Image::make($file)->resize(1280, null,function($constraint){
    //                 $constraint->aspectRatio();
    //                 $constraint->upsize();
    //                 }); // resize
    //             $final_path = $destinationPath."/".$real_name;
    //             Storage::disk('s3')->put($final_path,$img->stream()->__toString());
    //         }
    //         $cover_image = new Galleryimage;
    //         $cover_image->user_id = $user_id;
    //         $cover_image->image_title = $request->image_title;
    //         $cover_image->image_type = $request->image_type;
    //         $cover_image->price = $request->price;
    //         $cover_image->image_description = $request->image_description;
    //         $cover_image->image_url = $final_path;
    //         $cover_image->save();
    //         return response()->json(
    //             [
    //                 'message' => "Gallery Image Added Successfully.",
    //             ],200);
    //     }
    // }

    public function saveGalleryimagesByreseller(Request $request)
    {
        $user_id = $request->userId;
        $get_gallery_data = Galleryimage::Where('user_id', $user_id)->Where('image_type', '0')->get();
        $get_gallery_data_count = count($get_gallery_data);
        $get_coverimages_count = 5;

        if( $get_gallery_data_count < $get_coverimages_count ){
            if ($request->file('image_url')) {
                $destinationPath = '/images/gallery_images';
                $path = public_path().$destinationPath;
                $file = $request->file('image_url');
                if ( ! File::exists($path)) {
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $real_name = time().hexdec(rand(11111, 99999)).'.'.$file->getClientOriginalExtension(); // type change
                $img = Image::make($file)->resize(1280, null,function($constraint){
                    $constraint->aspectRatio();
                    $constraint->upsize();
                    }); // resize
                $final_path = $destinationPath."/".$real_name;
                Storage::disk('s3')->put($final_path,$img->stream()->__toString());
            }
            $cover_image = new Galleryimage;
            $cover_image->user_id = $user_id;
            $cover_image->image_title = $request->image_title;
            $cover_image->price = $request->price;
            $cover_image->image_type = $request->image_type;
            $cover_image->image_description = $request->image_description;
            $cover_image->image_url = $final_path;
            $cover_image->save();
            return response()->json(
                [
                    'message' => "gallery Image Added Successfully.",
                ],
                200
            );
        }else{
            return response()->json(
                [
                    'message' => "You Are Max 3 Cover Images Can Be Loaded.",
                ]);
        }
    }
    # To save business gallery image by reseller (function end)

    # To get business gallery images by reseller (function start)
    public function userGalleryImagesbyreseller(Request $request){
        $User = $request->userId;
        if(!$User){
            return response([
                                'success' => false,
                                'message' => 'User not valid!'
                            ]);
        }else{
            $get_gallery_images = Galleryimage::where('user_id',$User)->where('image_type',0)->get();
            return response([
                                'success' => true,
                                'gallery_images' => $get_gallery_images
                            ]);
        }
    }
    # To get business gallery images by reseller (function end)

    # To get business gallery images by reseller (function start)
    function businessWishlistsGetbyreseller(Request $request) {
        $User = $request->userId;
        $get_wishlists = Wishlist::leftjoin('users', 'users.id', 'wishlists.user_id')
            ->leftjoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('hotdeals', 'hotdeals.id', 'wishlists.services_id')
            ->leftjoin('sales', 'sales.id', 'wishlists.services_id')
            ->leftjoin('jobs', 'jobs.id', 'wishlists.services_id')
            ->leftjoin('videos', 'videos.id', 'wishlists.services_id')
            ->orderBy('wishlists.wishlist_type', 'ASC')
            ->with('hotdealsImages')
            ->select(
                'profiles.user_avatar',
                'wishlists.id as wishlist_id',
                'wishlists.wishlist_type',
                'wishlists.services_id',
                'hotdeals.id',
                'hotdeals.hot_deal_title',
                'hotdeals.hot_deal_description',
                'hotdeals.price_from',
                'hotdeals.price_to',

                'sales.id as sale_id',
                'sales.sale_title',
                'sales.sale_description',
                'sales.normal_price',
                'sales.sale_price',
                'sales.saledate_from',
                'sales.saledate_to',
                'sales.sale_imageurl',

                'jobs.id as job_id',
                'jobs.job_title',
                'jobs.job_description',
                'jobs.salary_from',
                'jobs.min_exp',

                'videos.id as video_id',
                'videos.video_title',
                'videos.video_description',
                'videos.video_url',
                'videos.youtube_id',

                'users.name',
                'profiles.city_id',
                'cities.city')
            ->where(["wishlists.user_id" => $User])
            ->get();


        return response([
                            'success' => true,
                            'wishlists' => $get_wishlists
                        ]);
    }
    # To get business gallery images by reseller (function end)

    # To remove business wishlist by reseller (function start)
    public function businessWishlistsremove(Request $request, $wishlist_id){
        $wishlist = Wishlist::where('id', $wishlist_id)->delete();
        if($wishlist==true){
            return response()->json(
                [
                    'status' => '200',
                    'message' => 'Wishlist deleted successfully.'
                ]
            );
        }
    }
    # To remove business wishlist by reseller (function end)

    # To get business reviews by reseller (function start)
    public function businessReviewsGetbyreseller(Request $request){
        $user = $request->userId;
        $reviews = Review::leftjoin('users', 'users.id', 'reviews.review_user_id')
            ->leftjoin('profiles','profiles.user_id', 'users.id')
            ->select('users.id', 'users.name', 'profiles.user_avatar', 'reviews.*',
                     DB::raw('DATE(reviews.created_at) as added_date'),
            )
            ->orderBy('reviews.rating')
            ->orderBy('reviews.review_user_id')
            ->where("reviews.review_business_id", $user)
            ->get();
        return response(
            [
                'success' => true,
                'message' => "Business reviews data with rating",
                'reviews' => $reviews
            ]);

    }
    # To get business reviews by reseller (function end)

    #To get business billing by reseller (function start)
    public function getBussinessBillingPlansbyreseller(Request $request)
    {
        $user = $request->userId;
        $active_user_plan = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
            ->select(
                'users.id as user_id',
                'users.name',
                'businesses.plan_id')
            ->where('users.id', $user)
            ->first();
        $plans =  Plan::where('plan_status', '1')->get();

        return response()->json(
            [
                'status' => true,
                'active_user_plan' => $active_user_plan,
                'plans' => $plans
            ]);
    }
    # To get business billing by reseller (function end)

    #To get business billing active by reseller (function start)
    public function getBussinessactivePlansbyreseller(Request $request)
    {
        $user = $request->userId;
        $active_plan = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
            ->leftjoin('plans', 'plans.id', 'businesses.plan_id')
            ->select(
                'users.*',
                'plans.id as plan_id',
                'plans.plan_description',
                'plans.plan_expiry',
                'businesses.business_id',
                'businesses.plan_id',)
            ->where('users.id', $user)
            ->first();
        $data = $active_plan->created_at;
        $date = new DateTime($data);
        // Add 365 days to the date
        $date->add(new DateInterval('P' . $active_plan->plan_expiry . 'D'));
        // Get the resulting date in the desired format
        $resultDateString = $date->format('d M, Y H:i a');
        // Output the result
        $expiration_date = $resultDateString;
        return response()->json(
            [
                'status' => true,
                'active_plan' => $active_plan,
                'expiration_date' => $expiration_date,
            ]);
    }
    # To get business billing active by reseller (function end)

    # To get business hot-deals by reseller (function start)

    public function businesstHotDealsGetByReseller(Request $request){
        $user = $request->userId;
        $userData = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('plans', 'plans.id', 'businesses.plan_id')
            ->select(
                    'users.id as user_id',
                    'users.name',
                    'businesses.sub_cat_id',
                    'businesses.plan_id',
                    'businesses.listedby',
                    'businesses.listedby_reseller_id',
                    'profiles.id as profile_id',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                    'plans.*'
            )
            ->where('users.id', $user)
            ->first();

        $hotDeals = Hotdeal::with('hotdealsImages')
                    ->Join('profiles', 'profiles.id', 'hotdeals.business_id')
                    ->join('cities', 'cities.id', 'profiles.city_id')
                    ->join('states', 'states.id', 'profiles.state_id')
                    ->select('hotdeals.id', 'profiles.id as profile_id', 'hotdeals.hot_deal_title', 'hotdeals.hot_deal_description',
                            'hotdeals.price_from','hotdeals.hotdeal_slug', 'hotdeals.price_to', 'hotdeals.date_from', 'hotdeals.date_to', 'hotdeals.hot_deal_status',
                            'profiles.user_avatar', 'profiles.address', 'profiles.city_id','cities.city', 'profiles.state_id',
                            'states.state')
                    ->withCount('hotdealsImages')
                    ->where('hotdeals.business_id', $userData->profile_id)
                    ->get();

        return response()->json([
                'status' => 200,
                'message' => "Busines hot deals with image.",
                'user' => $userData,
                'hotDeals' => $hotDeals,
        ]);
    }
    # To get business hot-deals by reseller (function end)

    # to add, Edit, Delete hot-deal by reseller (function start)
    public function businessesHotdealsAddByReseller(Request $request){
        // $user = JWTAuth::authenticate($request->token);
        if($request->services=='hot_deals_add'){ // add new
            $userPlan = Plan::Where('id', $request->plan_id)->first();
            $validator = Validator::make($request->all(), [
                'profile_id' => 'required|integer',
                'plan_id' => 'required|integer',
                'hot_deal_title' => 'required|string',
                'hot_deal_description' => 'required|string',
                'price_from' => 'required|integer',
                'price_to' => 'required|integer',
                'date_from' => 'required',
                'date_to' => 'required',
                'hotdeal_img' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            $addedHotDels = Hotdeal::Where('business_id', $request->profile_id)->get();
            if( count($addedHotDels)>0 ){ // all sale add after first time
                if($userPlan->hot_deals > $addedHotDels->count()){ // check how many added hot dels
                    $hotdeal = new Hotdeal();
                    $hotdeal->business_id = $request->profile_id;
                    $hotdeal->hot_deal_title = $request->hot_deal_title;
                    $hotdeal->hotdeal_slug = strtolower(Str::slug($request->hot_deal_title,'-')).'-'.Str::random(4);
                    $hotdeal->hot_deal_description = $request->hot_deal_description;
                    $hotdeal->price_from = $request->price_from;
                    $hotdeal->price_to = $request->price_to;
                    $hotdeal->date_from = $request->date_from;
                    $hotdeal->date_to = $request->date_to;
                    $hotdeal->hot_deal_status = ($request->hot_deal_status==1) ? (1) : (0);
                    $save_hotdeal = $hotdeal->save();
                    if ($save_hotdeal==true) {
                        if($request->hasfile('hotdeal_img')){
                            $countImg = 0;
                            $destinationPath = '/images/hotdealimages';
                            foreach($request->hotdeal_img as $file){ // file upload from model
                                $countImg++;
                                if($countImg <= $userPlan->images){
                                    $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                    $img = Image::make($file)->resize(1280, null,function($constraint){
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                        }); // resize
                                    $final_path = $destinationPath."/".$real_name;
                                    Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                                    $hotdealimage = new Hotdealimage;
                                    $hotdealimage->hotdeal_id = $hotdeal->id;
                                    $hotdealimage->hotdeal_img_url = $final_path;
                                    $hotdealimage->image_status = 1;
                                    $hotdealimage->save();
                                }
                            }
                        }
                        return response()->json([
                            'status' => 200,
                            'message' => "Hot deal added successfully.",
                            'data' => $hotdeal
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 200,
                        'message' => "You have already added ".$addedHotDels->count()." hot deals",
                        'plan' => $addedHotDels
                    ]);
                }
            }else{ // first hot deal add here
                $hotdeal = new Hotdeal();
                $hotdeal->business_id = $request->profile_id;
                $hotdeal->hot_deal_title = $request->hot_deal_title;
                $hotdeal->hotdeal_slug = strtolower(Str::slug($request->hot_deal_title,'-')).'-'.Str::random(4);
                $hotdeal->hot_deal_description = $request->hot_deal_description;
                $hotdeal->price_from = $request->price_from;
                $hotdeal->price_to = $request->price_to;
                $hotdeal->date_from = $request->date_from;
                $hotdeal->date_to = $request->date_to;
                $hotdeal->hot_deal_status = ($request->hot_deal_status==1) ? (1) : (0);
                $save_hotdeal = $hotdeal->save();
                if ($save_hotdeal==true) {
                    if($request->hasfile('hotdeal_img')){
                        $destinationPath = '/images/hotdealimages';
                        $countImg = 0;
                        foreach($request->hotdeal_img as $file){ // file upload from model
                            $countImg++;
                            if($countImg <= $userPlan->images){
                                $real_name = hexdec(rand(11111,99999)).'.'."png";
                                $img = Image::make($file)->resize(636, 350,function($constraint){
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                                $final_path = $destinationPath."/".$real_name;
                                Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                                $hotdealimage = new Hotdealimage;
                                $hotdealimage->hotdeal_id = $hotdeal->id;
                                $hotdealimage->hotdeal_img_url = $final_path;
                                $hotdealimage->image_status = 1;
                                $hotdealimage->save();
                            }
                        }
                    }
                    return response()->json([
                        'status' => 200,
                        'message' => "Hot deal added successfully.",
                        'data' => $hotdeal
                    ]);
                }
            }
        }else if($request->services=='hot_deals_update'){ // update
            $validator = Validator::make($request->all(), [
                'hotdel_id' => 'required|integer',
                'profile_id' => 'required|integer',
                'plan_id' => 'required|integer',
                'hot_deal_title' => 'required|string',
                'hot_deal_description' => 'required|string',
                'price_from' => 'required|integer',
                'price_to' => 'required|integer',
                'date_from' => 'required',
                'date_to' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            $hotdeal = Hotdeal::find($request->hotdel_id);
            $hotdeal->business_id = $request->profile_id;
            $hotdeal->hot_deal_title = $request->hot_deal_title;
            $hotdeal->hotdeal_slug = strtolower(Str::slug($request->hot_deal_title,'-')).'-'.Str::random(4);
            $hotdeal->hot_deal_description = $request->hot_deal_description;
            $hotdeal->price_from = $request->price_from;
            $hotdeal->price_to = $request->price_to;
            $hotdeal->date_from = $request->date_from;
            $hotdeal->date_to = $request->date_to;
                if( $request->hot_deal_status == "true" ){
                    $hotdeal->hot_deal_status = 1;
                }elseif( $request->hot_deal_status == "1" ){
                    $hotdeal->hot_deal_status = 1;
                }else{
                    $hotdeal->hot_deal_status = 0;
                }
            $update_hotdeal = $hotdeal->update();
            if ($update_hotdeal==true) {
                $userPlan = Plan::Where('id', $request->plan_id)->first();
                // delete selected previous uploaded image
                if($request->oldDeleted_HotDeals_id){
                    foreach($request->oldDeleted_HotDeals_id as $old_hotDealImage_id){
                        $hotdealimage = Hotdealimage::where('id', $old_hotDealImage_id)->first();
                        if (File::exists(public_path().$hotdealimage->hotdeal_img_url)) {
                            unlink(public_path().$hotdealimage->hotdeal_img_url);
                        }
                        if(Storage::disk('s3')->exists($hotdealimage->hotdeal_img_url)){
                            Storage::disk('s3')->delete($hotdealimage->hotdeal_img_url);
                        }
                        $hotdealimage->delete();
                    }
                }
                if($request->hasfile('hotdeal_img')){
                    $addedHotdealimage = Hotdealimage::Where('hotdeal_id', $request->hotdel_id)->get();
                    if( count($addedHotdealimage)>0 ){
                        if($userPlan->images > $addedHotdealimage->count()){ // check how many added hot dels
                            $countImg = $addedHotdealimage->count();
                            foreach($request->hotdeal_img as $file){ // file upload from model
                                $countImg++;
                                $destinationPath = '/images/hotdealimages';
                                if($countImg <= $userPlan->images){
                                    $real_name = time().hexdec(rand(11111,99999)).'.'."png";
                                    $img = Image::make($file)->resize(1280, null,function($constraint){
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                    $final_path = $destinationPath."/".$real_name;
                                    Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                                    $hotdealimage = new Hotdealimage;
                                    $hotdealimage->hotdeal_id = $hotdeal->id;
                                    $hotdealimage->hotdeal_img_url = $final_path;
                                    $hotdealimage->image_status = 1;
                                    $hotdealimage->save();
                                }
                            }
                        }else{
                            return response()->json([
                                'status' => 200,
                                'message' => "You have already added ".$addedHotdealimage->count()." images.",
                                'plan' => $addedHotdealimage
                            ]);
                        }
                    }else{
                        $countImg = 0;
                        foreach($request->hotdeal_img as $file){ // file upload from model
                            $destinationPath = '/images/hotdealimages';
                            if($countImg <= $userPlan->images){
                                $extension = $file->getClientOriginalExtension();
                                $real_name = time().hexdec(rand(11111, 99999)) . '.' . $extension;
                                $img = Image::make($file)->resize(1280, null,function($constraint){
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                    });
                                $final_path = $destinationPath .'/'. $real_name;
                                Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                                $hotdealimage = new Hotdealimage;
                                $hotdealimage->hotdeal_id = $hotdeal->id;
                                $hotdealimage->hotdeal_img_url = $final_path;
                                $hotdealimage->image_status = 1;
                                $hotdealimage->save();
                            }
                        }
                    }
                }
                return response()->json([
                    'status' => 200,
                    'message' => "Hot deal updated successfully.",
                    'data' => $hotdeal
                ]);
            }
        }else if($request->services=='hot_deals_delete'){ // Delete
            $hotdeal = Hotdeal::where('id', $request->hotdel_id)->first();
            Wishlist::where('services_id', $hotdeal->id)->delete();
            if($hotdeal){
                $addedHotdealimage = Hotdealimage::Where('hotdeal_id', $hotdeal->id)->get();
                foreach($addedHotdealimage as $hotDealImages){
                    $file_path = public_path().$hotDealImages->hotdeal_img_url;
                    if (File::exists($file_path)) {
                        unlink($file_path);
                    }
                    if(Storage::disk('s3')->exists($hotDealImages->hotdeal_img_url)){
                        Storage::disk('s3')->delete($hotDealImages->hotdeal_img_url);
                    }
                }
                $hotdeal->delete();
                return response()->json([
                    'status' => 200,
                    'message' => "Hot deal deleted successfully.",
                ]);
            }else{
                return response()->json([
                    'status' => 200,
                    'message' => "Hot deal ID not exit.",
                ]);
            }
        }
    }
    # to add, Edit, Delete hot-deal by reseller (function end)

    # to get sales by reseller (function start)
    public function businessesSalesGetByReseller(Request $request) {
        $user = $request->userId;
        $userData = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('plans', 'plans.id', 'businesses.plan_id')
            ->select(
                    'users.id as user_id',
                    'users.name',
                    'businesses.sub_cat_id',
                    'businesses.plan_id',
                    'businesses.listedby',
                    'businesses.listedby_reseller_id',
                    'profiles.id as profile_id',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                    'plans.*')
                    ->where('users.id', $user)
                    ->first();

                    $sales = Sale::with('salesImages')
                    ->Join('profiles', 'profiles.user_id', 'sales.user_id')
                    ->join('cities', 'cities.id', 'profiles.city_id')
                    ->join('states', 'states.id', 'profiles.state_id')
                    ->select('sales.id',
                    'profiles.user_id',
                    'sales.sale_imageurl',
                    'sales.sale_title',
                    'sales.sale_description',
                    'sales.normal_price',
                    'sales.sale_price',
                    'sales.sale_slug',
                    'sales.saledate_from',
                        'sales.saledate_to',
                        'sales.sale_status',
                        'profiles.user_avatar',
                        'profiles.address',
                        'profiles.city_id',
                        'cities.city',
                        'profiles.state_id',
                        'states.state')
                        ->withCount('salesImages')
                        ->where('sales.user_id', $userData->user_id)
                        ->orderBy('sales.id', 'DESC')
                        ->get();

                        return response()->json([
                'status' => 200,
                'message' => "All sales data of busines.",
                'user' => $userData,
                'sales' => $sales,
            ]);
        }
        # to get sales by reseller (function end)

        # to add, Edit, Delete sales by reseller (function start)
        public function businessesSalesSubmitByReseller(Request $request){
            $userPlan = Plan::Where('id', $request->plan_id)->first();
            $destinationPath = '/images/salesimages';
            $path = public_path().$destinationPath;
            if($request->services=='sale_add'){ // add new

                $validator = Validator::make($request->all(), [
                    'user_id' => 'required|integer',
                    'plan_id' => 'required|integer',
                    'sale_title' => 'required|string',
                    'sale_description' => 'required|string',
                    'normal_price' => 'required|integer',
                    'sale_price' => 'required|integer',
                    'saledate_from' => 'required',
                    'saledate_to' => 'required',
                    'sale_imageurl' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->messages()], 400);
                }
                $addedSales = Sale::Where('user_id', $request->user_id)->get();
                if( count($addedSales)>0 ){ // all sale add after first time
                    if($userPlan->sale > $addedSales->count()){ // check how many added sale in sale table
                        $sale = new Sale();
                        $sale->user_id = $request->user_id;
                        $sale->sale_title = $request->sale_title;
                        $sale->sale_slug = strtolower(Str::slug($request->sale_title,'-')).'-'.Str::random(4);
                        $sale->sale_description = $request->sale_description;
                        $sale->normal_price = $request->normal_price;
                        $sale->sale_price = $request->sale_price;
                        $sale->saledate_from = $request->saledate_from;
                        $sale->saledate_to = $request->saledate_to;
                        // first image upload from multiple uploads
                        $real_name = time().hexdec(rand(11111,99999)).'.'."png";
                        $img = Image::make($request->sale_imageurl[0])->resize(1280, null,function($constraint){
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $fpath = $destinationPath."/".$real_name;
                        Storage::disk('s3')->put($fpath,$img->stream()->__toString());
                        $sale->sale_imageurl = $fpath;
                            $sale->sale_status = ($request->sale_status==1) ? (1) : (0);
                        $save_sale = $sale->save();
                        if ($save_sale==true) {
                            if($request->hasfile('sale_imageurl')){
                                $countImg = 0;
                                foreach($request->sale_imageurl as $file){ // file upload from model
                                    $countImg++;
                                    if($countImg <= $userPlan->images){
                                        $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                        $img = Image::make($file)->resize(1280, null,function($constraint){
                                            $constraint->aspectRatio();
                                            $constraint->upsize();
                                        });
                                        $final_path = $destinationPath."/".$real_name;
                                        Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                                        $hotdealimage = new Saleimage();
                                        $hotdealimage->sale_id = $sale->id;
                                        $hotdealimage->sale_img_url = $final_path;
                                        $hotdealimage->image_status = 1;
                                        $hotdealimage->save();
                                    }
                                }
                            }
                            return response()->json([
                                'status' => 200,
                                'message' => "Sale added successfully.",
                                'data' => $sale
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 200,
                            'message' => "You have already added ".$addedSales->count()." Sales",
                            'plan' => $addedSales
                        ]);
                    }
                }else{ // first sale add here
                    $sale = new Sale();
                    $sale->user_id = $request->user_id;
                    $sale->sale_title = $request->sale_title;
                    $sale->sale_slug = strtolower(Str::slug($request->sale_title,'-')).'-'.Str::random(4);
                    $sale->sale_description = $request->sale_description;
                    $sale->normal_price = $request->normal_price;
                    $sale->sale_price = $request->sale_price;
                    $sale->saledate_from = $request->saledate_from;
                    $sale->saledate_to = $request->saledate_to;
                    // first image upload from multiple uploads
                    $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
                    $img = Image::make($request->sale_imageurl[0])->resize(1280, null,function($constraint){
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $fpath =  $destinationPath."/".$real_name;
                    Storage::disk('s3')->put($fpath,$img->stream()->__toString());
                    $sale->sale_imageurl = $fpath;
                    $sale->sale_status = ($request->sale_status==1) ? (1) : (0);
                    $save_sale = $sale->save();
                    if ($save_sale==true) {
                        if($request->hasfile('sale_imageurl')){
                            $countImg = 0;
                            foreach($request->sale_imageurl as $file){ // file upload from model
                                $countImg++;
                                if($countImg <= $userPlan->images){
                                    $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                    $img = Image::make($file)->resize(1280, null,function($constraint){
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                    $final_path = $destinationPath."/".$real_name;
                                    Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                                    $hotdealimage = new Saleimage();
                                    $hotdealimage->sale_id = $sale->id;
                                    $hotdealimage->sale_img_url = $final_path;
                                    $hotdealimage->image_status = 1;
                                    $hotdealimage->save();
                                }
                            }
                        }
                        return response()->json([
                            'status' => 200,
                            'message' => "Sale added successfully.",
                            'data' => $sale
                        ]);
                    }
                }
            }else if($request->services=='sale_update'){
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required|integer',
                    'sale_id' => 'required|integer',
                    'plan_id' => 'required|integer',
                    'sale_title' => 'required|string',
                    'sale_description' => 'required|string',
                    'normal_price' => 'required|integer',
                    'sale_price' => 'required|integer',
                    'saledate_from' => 'required',
                    'saledate_to' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->messages()], 400);
                }
                $sale = Sale::find($request->sale_id);
                $sale->user_id = $request->user_id;
                $sale->sale_title = $request->sale_title;
                $sale->sale_slug = strtolower(Str::slug($request->sale_title,'-')).'-'.Str::random(4);
                $sale->sale_description = $request->sale_description;
                $sale->normal_price = $request->normal_price;
                $sale->sale_price = $request->sale_price;
                $sale->saledate_from = $request->saledate_from;
                $sale->saledate_to = $request->saledate_to;
                    if( $request->sale_status == "true" ){
                        $sale->sale_status = 1;
                    }elseif( $request->sale_status == "1" ){
                        $sale->sale_status = 1;
                    }else{
                        $sale->sale_status = 0;
                    }
                $save_sale = $sale->update();
                if ($save_sale==true) {
                    if($request->oldDeleted_sales_image_id){
                        foreach($request->oldDeleted_sales_image_id as $old_saleImage_id){
                            $saleImage = Saleimage::where('id', $old_saleImage_id)->first();
                            if (File::exists(public_path().$saleImage->sale_img_url)) {
                                unlink(public_path().$saleImage->sale_img_url);
                            }
                            If(Storage::disk('s3')->exists($saleImage->sale_img_url)){
                                Storage::disk('s3')->delete($saleImage->sale_img_url);
                            }
                            $saleImage->delete();
                        }
                    }
                    if($request->hasfile('sale_imageurl')){
                        $addedSalesimage = Saleimage::Where('sale_id', $request->sale_id)->get();
                        if( count($addedSalesimage)>0 ){
                            if($userPlan->images > $addedSalesimage->count()){ // check how many added hot dels
                                $countImg = $addedSalesimage->count();
                                foreach($request->sale_imageurl as $file){ // file upload from model
                                    $countImg++;

                                    if($countImg <= $userPlan->images){
                                        $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                        $img = Image::make($file)->resize(1280, null,function($constraint){
                                            $constraint->aspectRatio();
                                            $constraint->upsize();
                                        });
                                        $final_path = $destinationPath."/".$real_name;
                                        Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                                        $hotdealimage = new Saleimage();
                                        $hotdealimage->sale_id = $sale->id;
                                        $hotdealimage->sale_img_url = $final_path;
                                        $hotdealimage->image_status = 1;
                                        $hotdealimage->save();
                                    }
                                }
                            }else{
                                return response()->json([
                                    'status' => 200,
                                    'message' => "You have already added ".$addedSalesimage->count()." images.",
                                    'plan' => $addedSalesimage
                                ]);
                            }
                        }else{
                            $countImg = 0;
                            foreach($request->sale_imageurl as $file){ // file upload from model
                                $countImg++;

                                if($countImg <= $userPlan->images){
                                    $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                    $img = Image::make($file)->resize(1280, null,function($constraint){
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                    $final_path = $destinationPath."/".$real_name;
                                    Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                                    $hotdealimage = new Saleimage();
                                    $hotdealimage->sale_id = $sale->id;
                                    $hotdealimage->sale_img_url = $final_path;
                                    $hotdealimage->image_status = 1;
                                    $hotdealimage->save();
                                }
                            }
                        }
                    }

                    return response()->json([
                        'status' => 200,
                        'message' => "Sale updated successfully.",
                        'data' => $sale
                    ]);
                }
            }else if($request->services=='sale_delete'){
                $sale = Sale::where('id', $request->sale_id)->first();

                if($sale){
                    $addedSalesImages = Saleimage::Where('sale_id', $sale->id)->get();
                    foreach($addedSalesImages as $saleImages){ // all sale Image delete
                        $file_path = public_path().$saleImages->sale_img_url;
                        if (File::exists($file_path)) {
                            unlink($file_path);
                        }
                        if(Storage::disk('s3')->exists($saleImages->sale_img_url)){
                            Storage::disk('s3')->delete($saleImages->sale_img_url);
                        }
                    }
                    $file_path = public_path().$sale->sale_imageurl;
                    if (File::exists($file_path)) { // Single sale image delete
                        unlink($file_path);
                    }
                    if(Storage::disk('s3')->exists($sale->sale_img_url)){
                        Storage::disk('s3')->delete($sale->sale_img_url);
                    }
                    $sale->delete();
                    return response()->json([
                        'status' => 200,
                        'message' => "Sale deleted successfully with image.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 200,
                        'message' => "Sale ID not exit.",
                    ]);
                }

            }
        }
        # to add, Edit, Delete sales by reseller (function end)

        # to get jobs by reseller (function start)
        public function businesstJobsGetByReseller(Request $request){
            $userId = $request->userId;
            $userData = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
                ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
                ->leftjoin('cities', 'cities.id', 'profiles.city_id')
                ->leftjoin('states', 'states.id', 'profiles.state_id')
                ->leftjoin('plans', 'plans.id', 'businesses.plan_id')
                ->select(
                        'users.id as user_id',
                        'users.name',
                        'businesses.sub_cat_id',
                        'businesses.plan_id',
                        'businesses.listedby',
                        'businesses.listedby_reseller_id',
                        'profiles.id as profile_id',
                        'profiles.city_id',
                        'cities.city',
                        'profiles.state_id',
                        'states.state',
                        'plans.*')
                ->where('users.id', $userId)
                ->first();

                $jobs = Job::Join('businesses','businesses.business_id', 'jobs.user_id')
                    ->join('jobtypes', 'jobtypes.id', 'jobs.job_type_id')
                    ->join('jobcategories', 'jobcategories.id', 'jobs.job_cat_id')
                    ->join('qualifications', 'qualifications.id', 'jobs.job_qualification_id')
                    ->leftJoin('cities', 'cities.id', 'jobs.city_id')
                    ->select('jobs.id as job_id',
                        'jobs.job_type_id',
                        'jobtypes.job_type',
                        'jobs.job_cat_id',
                        'jobcategories.job_cat_name',
                        'jobs.job_title',
                        'jobs.job_slug',
                        'jobs.city_id',
                        'cities.city',
                        'jobs.job_description', 'jobs.job_description',
                        'jobs.salary_from', 'jobs.min_exp',
                        'jobs.job_qualification_id',
                        'qualifications.qualification',
                        'jobs.job_status' )
                    ->where('jobs.user_id', '=', $userId)
                    ->orderBy('job_id', 'DESC')
                    ->distinct()
                    ->get();
                $allJobs = $jobs;

                return response()->json([
                    'status' => 200,
                    'message' => "All sales data of busines.",
                    'user' => $userData,
                    'jobs' => $allJobs,
                ]);
        }
        # to get jobs by reseller (function end)

        # To add job (function start)
        public function addJobByReseller( Request $request){
            $user_id = $request->user_id;
            $plan_id = $request->plan_id;
            $data = $request->only('job_title','job_cat_id','job_type_id','job_qualification_id','salary_from','min_exp','job_description');
            $validator = Validator::make($data, [
                'job_title' => 'required',
                'job_cat_id' => 'required',
                'job_type_id' => 'required',
                'job_qualification_id' => 'required',
//                'city_id' => 'required',
                'salary_from' => 'required',
                'min_exp' => 'required',
                'job_description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }else{
                // $business_data = Business::Where('business_id', $user_id)->first();
                // $user_plan_id = $business_data->plan_id;
                $business_plan = Plan::Where('id', $plan_id)->first();
                $city_id = Profile::Where('user_id', $user_id)->first()->city_id;
                $job_data = Job::Where('user_id', $user_id)->get();
                if (count($job_data) > 0) {
                    if (count($job_data) == $business_plan->jobs) {
                        return response()->json(
                            [
                                'success' => false,
                                'message' => "You have already added " . $job_data->count() . ' jobs',
                            ],
                            200
                        );
                    } else {
                        $job = new Job();
                        $job->user_id = $user_id;
                        $job->job_title = $request->job_title;
                        $job->job_slug = strtolower(Str::slug($request->job_title,'-')).'-'.Str::random(4);
                        $job->job_cat_id = $request->job_cat_id;
                        $job->job_type_id = $request->job_type_id;
                        $job->job_qualification_id = $request->job_qualification_id;
                        $job->city_id = $city_id;
                        $job->salary_from = $request->salary_from;
                        $job->min_exp = $request->min_exp;
                        $job->job_description = $request->job_description;
                        $job->job_status = ($request->job_status==1) ? (1) : (0);
                        $job->save();
                        return response()->json(
                            [
                                'success' => true,
                                'message' => "Job Added Successfully.",
                            ],200
                        );
                    }
                } else {
                    $job = new Job();
                        $job->user_id = $user_id;
                        $job->job_title = $request->job_title;
                        $job->job_slug = strtolower(Str::slug($request->job_title,'-')).'-'.Str::random(4);
                        $job->job_cat_id = $request->job_cat_id;
                        $job->job_type_id = $request->job_type_id;
                        $job->job_qualification_id = $request->job_qualification_id;
                        $job->city_id = $city_id;
                        $job->salary_from = $request->salary_from;
                        $job->min_exp = $request->min_exp;
                        $job->job_description = $request->job_description;
                        $job->job_status = ($request->job_status==1) ? (1) : (0);
                        $job->save();
                    return response()->json(
                        [
                            'success' => true,
                            'message' => "Job Added Successfully.",
                        ],200
                    );
                }
            }

        }
        # To add job (function end)

        # To update job (function start)
        public function updateJobByReseller( Request $request){
            $user_id = $request->user_id;
            $data = $request->only('job_id','job_title','job_cat_id','job_type_id','job_qualification_id','city_id','salary_from','min_exp','job_description');
            $validator = Validator::make($data, [
                'job_id' => 'required',
                'job_title' => 'required',
                'job_cat_id' => 'required',
                'job_type_id' => 'required',
                'job_qualification_id' => 'required',
                'city_id' => 'required',
                'salary_from' => 'required',
                'min_exp' => 'required',
                'job_description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }else{
                $job = Job::find($request->job_id);
                $job->job_title = $request->job_title;
                $job->job_slug = strtolower(Str::slug($request->job_title,'-')).'-'.Str::random(4);
                $job->job_cat_id = $request->job_cat_id;
                $job->job_type_id = $request->job_type_id;
                $job->job_qualification_id = $request->job_qualification_id;
                $job->city_id = $request->city_id;
                $job->salary_from = $request->salary_from;
                $job->min_exp = $request->min_exp;
                $job->job_description = $request->job_description;
                    if( $request->job_status == "true" ){
                        $job->job_status = 1;
                    }elseif( $request->job_status == "1" ){
                        $job->job_status = 1;
                    }else{
                        $job->job_status = 0;
                    }
                $job->update();
                return response()->json(
                    [
                        'success' => true,
                        'message' => "Job updated Successfully.",
                    ],
                    200
                );
            }

        }
        # To update job (function end)

        # To delete the specific job ( function start)
        public function deleteJobByReseller( Request $request){
            $data = $request->only('job_id');
            $validator = Validator::make($data, [
                'job_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }else{
                $jobs = Job::find($request->job_id);
                if( $jobs == null ){
                    return response([
                        'success' => false,
                        'message' => 'Job does not exist please check again!'
                    ],404);
                }else{
                    Job::where('id',$request->job_id)->delete();
                    return response([
                        'success' => true,
                        'message' => 'Job deleted successfully'
                    ]);
                }
            }
        }
        # To delete the specific job ( function end)

        # To get the all Videos ( function start)
        public function businessVideosGetByReseller(Request $request){
            $user_id = $request->userId;
            $userData = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
                ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
                ->leftjoin('cities', 'cities.id', 'profiles.city_id')
                ->leftjoin('states', 'states.id', 'profiles.state_id')
                ->leftjoin('plans', 'plans.id', 'businesses.plan_id')
                ->select(
                        'users.id as user_id',
                        'users.name',
                        'businesses.sub_cat_id',
                        'businesses.plan_id',
                        'businesses.listedby',
                        'businesses.listedby_reseller_id',
                        'profiles.id as profile_id',
                        'profiles.city_id',
                        'cities.city',
                        'profiles.state_id',
                        'states.state',
                        'plans.*')
                ->where('users.id', $user_id)
                ->first();

            $allVideos = Video::Join('businesses', 'businesses.business_id', 'videos.user_id')
                ->select('videos.id as video_id',
                    'videos.user_id',
                    'videos.video_title',
                    'videos.video_description',
                    'videos.video_url',
                    'videos.video_slug',
                    'videos.youtube_id',
                    'videos.video_status')
                ->where('videos.user_id', $userData->user_id)
                ->orderBy('video_id', 'DESC')
                ->distinct()
                ->get();
            return response()->json([
                'status' => 200,
                'message' => "All videos data of busines.",
                'user' => $userData,
                'allVideos' => $allVideos,
            ]);
        }
        # To get the all Videos ( function end)

        # To add video (function start)
        public function addVideoByReseller(Request $request)
        {
            $user_id = $request->user_id;
            $plan_id = $request->plan_id;
            $data = $request->only('video_title','video_url','youtube_link');
            $validator = Validator::make($data, [
                'video_title' => 'required|string',
                'video_url' => 'required|string',
                'youtube_link' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {

                $business_plan = Plan::Where('id', $plan_id)->first();
                $video_data = Video::Where('user_id', $user_id)->get();

                if (count($video_data) > 0) {
                    if (count($video_data) == $business_plan->video) {
                        return response()->json(
                            [
                                'message' => "You have already added " . $video_data->count() . ' videos',
                            ],200
                        );
                    } else {
                        $video = new Video();
                        $video->user_id = $user_id;
                        $video->video_title = $request->video_title;
                        $video->video_slug = strtolower(Str::slug($request->video_title,'-')).'-'.Str::random(4);
                        $video->video_description = $request->video_description;
                        $video->video_url = $request->video_url;
                        $link = $request->youtube_link;
                        $video_id = explode("?v=", $link);
                        $final_id = isset($video_id[1]) ? $video_id[1] : null;
                        $video->youtube_id = $final_id;
                        $video->save();
                        return response()->json(
                            [
                                'message' => "Video Added Successfully.",
                            ],200
                        );
                    }
                } else {
                    $video = new Video();
                    $video->user_id = $user_id;
                    $video->video_title = $request->video_title;
                    $video->video_slug = strtolower(Str::slug($request->video_title,'-')).'-'.Str::random(4);
                    $video->video_description = $request->video_description;
                    $video->video_url = $request->video_url;
                    $link = $request->youtube_link;
                    $video_id = explode("?v=", $link);
                    $final_id = isset($video_id[1]) ? $video_id[1] : null;
                    $video->youtube_id = $final_id;
                    $video->save();
                    return response()->json(
                        [
                            'message' => "Video Added Successfully.",
                        ],200
                    );
                }
            }
        }
        # To add video (function end)

        # To update video (function start)
        public function updateVideoByReseller(Request $request)
        {
            $user_id = $request->user_id;
            $data = $request->only('video_id','video_title','video_url','youtube_link');
            $validator = Validator::make($data, [
                'video_id' => 'required|integer',
                'video_title' => 'required|string',
                'video_url' => 'required|string',
                'youtube_link' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $video = Video::find($request->video_id);
                $video->video_title = $request->video_title;
                $video->video_slug = strtolower(Str::slug($request->video_title,'-')).'-'.Str::random(4);
                $video->video_description = $request->video_description;
                $video->video_url = $request->video_url;
                $link = $request->youtube_link;
                $video_id = explode("?v=", $link);
                $final_id = isset($video_id[1]) ? $video_id[1] : null;
                $video->youtube_id = $final_id;

                if ($request->video_status == "true" || $request->video_status == "1") {
                    $video->video_status = 1;
                } else {
                    $video->video_status = 0;
                }
                $video->update();
                return response()->json(
                    [
                        'message' => "Video updated Successfully.",
                    ],200
                );
            }
        }
        # To update video (function end)

        # To delete video (function start)
        public function deleteVideoByReseller(Request $request)
        {
            $data = $request->only('video_id');
            $validator = Validator::make($data, [
                'video_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $video = Video::find($request->video_id);
                if (public_path() . '/' . $video->video_url) {
                    File::delete(public_path() . '/' . $video->video_url);
                }
                $video->delete();
                return response()->json(
                    [
                        'message' => "Video deleted Successfully.",
                    ],200
                );
            }
        }
        # To delete video (function end)



    }
