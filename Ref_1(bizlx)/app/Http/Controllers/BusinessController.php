<?php

namespace App\Http\Controllers;
use App\Models\Business;
use App\Models\Businessfeature;
use App\Models\Category;
use App\Models\City;
use App\Models\Galleryimage;
use App\Models\Hotdeal;
use App\Models\Hotdealimage;
use App\Models\Job;
use App\Models\Plan;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Sale;
use App\Models\Service;
use App\Models\Subcategory;
use App\Models\Tab;
use App\Models\Tabcontent;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BusinessEmailVerification;
use Carbon\Carbon;



use File;
use App\Mail\NewBusinessNotification;
class BusinessController extends Controller
{
    // Login your business
    // public function businessLogin(Request $request)

    // {
    //     $credentials = $request->only('email', 'password', 'user_role');

    //     //valid credential
    //     $validator = Validator::make($credentials, [
    //         'email' => 'required|email',
    //         'password' => 'required|string|min:6|max:50',
    //         'user_role' => 'required'
    //     ]);
    //     //Send failed response if request is not valid
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->messages()], 400);
    //     }
    //     $business = User::Where('email', $request->email)->where('user_status', '1')->first();
    //     //Request is validated
    //     //Crean token
    //     try {
    //         if (! $token = JWTAuth::attempt($credentials)) {
    //             return response()->json(
    //                 [
    //                     'success' => false,
    //                     'message' => 'Login credentials are invalid, or you are not business',
    //                 ],
    //                 400
    //             );
    //         }
    //     } catch (JWTException $e) {
    //         // return $credentials;
    //         return response()->json(
    //             [
    //                 'success' => false,
    //                 'message' => 'Could not create token.',
    //             ],
    //             500
    //         );
    //     }

    //     if ($business == null) {
    //         return response([
    //             'success' => false,
    //             'message' => 'Your account has been deactivated',
    //         ], 400);
    //     } else {
    //         $existing_business = Business::where('business_id', $business->id)->first();
    //         $business->plan_id = $existing_business->plan_id;
    //     }

    //     //Token created, return with success response and jwt token
    //     return response()->json(
    //         [
    //             'success' => true,
    //             'token' => $token,
    //             'user' => $business,
    //         ],
    //         200
    //     );
    // }


    public function businessLogin(Request $request)
{
    $credentials = $request->only('email', 'password', 'user_role');

    $validator = Validator::make($credentials, [
        'email' => 'required|email',
        'password' => 'required|string|min:6|max:50',
        'user_role' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->messages()], 400);
    }

    $business = User::where('email', $request->email)
        ->where('user_status', '1')
        ->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Your account has been deactivated or does not exist.',
        ], 400);
    }

    // 🔒 Check if email is verified
    if (is_null($business->email_verified_at)) {
        return response()->json([
            'success' => false,
            'message' => 'Please verify your email before logging in.',
        ], 403);
    }

    try {
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Login credentials are invalid or you are not a business.',
            ], 400);
        }
    } catch (JWTException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Could not create token.',
        ], 500);
    }

    $existing_business = Business::where('business_id', $business->id)->first();
    $business->plan_id = $existing_business ? $existing_business->plan_id : null;
    

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => $business,
    ], 200);
}

    // Register your business
    // public function businessRegister(Request $request)
    // {
    //     //Validate data
    //     $data = $request->only('username', 'subcategory_id',  'email', 'password', 'mobile_phone', 'city_id', 'address', 'user_role', 'user_status');
    //     $validator = Validator::make($data, [
    //         'username' => 'required|unique:users',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|string|min:8|max:15',
    //         'subcategory_id' => 'required',
    //         // 'cat_id' => 'required',
    //         'mobile_phone' => 'required|unique:users',
    //         'city_id' => 'required',
    //         'address' => 'required',
    //         'user_role' => 'required',
    //         'user_status' => 'required',
    //     ]);

    //     //Send failed response if request is not valid
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->messages()], 400);
    //     } else {
    //         //Request is valid, create new user
    //         $longitude_latitude = apiResponse($request->address);

    //         $business = User::create(
    //             [
    //                 'name' => $request->name,
    //                 'email' => $request->email,
    //                 'password' => bcrypt($request->password),
    //                 'user_role' => $request->user_role,
    //                 'user_status' => $request->user_status,
    //                 'username' => $request->username,
    //                 'mobile_phone' => $request->mobile_phone,
    //                 'longitude' => $longitude_latitude['longitude'],
    //                 'latitude' => $longitude_latitude['latitude'],
    //             ]
    //         );
    //         if ($business) {
    //             if (empty($request->state_id)) { // get state_id from city table for use in profile table
                    
    //                 $city = City::find($request->city_id);
    //                 $state_id = $city->state_id;
    //             } else {
    //                 $state_id = $request->state_id;
    //             }
    //             // profile table
    //             $businessprofile = Profile::create(
    //                 [
    //                     'user_id' => $business->id,
    //                     'state_id' => $state_id,
    //                     'city_id' => $request->city_id,
    //                     'address' => $request->address,
    //                     'profile_status' => 1,
    //                 ]
    //             );
    //             $subcatids = $request->subcategory_id;
    //             if ($subcatids) {
    //                 $businesses = new Business();
    //                 // $businesses->sub_cat_id = implode(",", $request->subcategory_id);
    //                 $businesses->sub_cat_id = $request->subcategory_id;
    //                 $businesses->business_id = $business->id;
    //                 $get_cat_id = Subcategory::find($request->subcategory_id);
    //                 $businesses->cat_id = $get_cat_id->cat_id;
    //                 $businesses->plan_id = 1;
    //                 $businesses->listedby_reseller_id = $request->refferal_code;
    //                 $businesses->save();
    //                 // foreach ($subcatids as $subcatid) {
    //                 //     $businesses = new Business();
    //                 //     $businesses->sub_cat_id = $subcatid;
    //                 //     $businesses->business_id = $business->id;
    //                 //     $businesses->cat_id = $request->cat_id;
    //                 //     $businesses->plan_id = 1;
    //                 //     $businesses->save();
    //                 // }
    //             }
    //             return response()->json(
    //                 [
    //                     'success' => true,
    //                     'message' => 'Business created successfully',
    //                     'data' => ['user' => $business, 'profile' => $businessprofile, 'reseller' => $businesses]
    //                 ],
    //                 200
    //             );
    //         }
    //     }
    // }
    public function getSetting() {
        $settings = DB::table('settings')->select('business_reg_img')->first();
        return response()->json($settings);
    }
    
// public function businessRegister(Request $request)
// {
//     $data = $request->only('username', 'subcategory_id', 'email', 'password', 'mobile_phone', 'city_id', 'address', 'user_role', 'user_status');
//     $validator = Validator::make($data, [
//         'username' => 'required|unique:users',
//         'email' => 'required|email',
//         'password' => 'required|string|min:8|max:15',
//         'subcategory_id' => 'required',
//         'mobile_phone' => 'required',
//         'city_id' => 'required',
//         'address' => 'required',
//         'user_role' => 'required',
//         'user_status' => 'required',
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['error' => $validator->messages()], 400);
//     }

//     $longitude_latitude = apiResponse($request->address);

//     $business = User::create([
//         'name' => $request->name,
//         'email' => $request->email,
//         'password' => bcrypt($request->password),
//         'user_role' => $request->user_role,
//         'user_status' => $request->user_status,
//         'username' => $request->username,
//         'mobile_phone' => $request->mobile_phone,
//         'longitude' => $longitude_latitude['longitude'],
//         'latitude' => $longitude_latitude['latitude'],
//     ]);

//     if ($business) {
//         // Get State ID if not provided
//         $state_id = $request->state_id ?: City::find($request->city_id)->state_id;

//         Profile::create([
//             'user_id' => $business->id,
//             'state_id' => $state_id,
//             'city_id' => $request->city_id,
//             'address' => $request->address,
//             'profile_status' => 1,
//         ]);

//         if ($request->subcategory_id) {
//             $get_cat_id = Subcategory::find($request->subcategory_id);
//             Business::create([
//                 'sub_cat_id' => $request->subcategory_id,
//                 'business_id' => $business->id,
//                 'cat_id' => $get_cat_id->cat_id,
//                 'plan_id' => 1,
//                 'listedby_reseller_id' => $request->refferal_code,
//             ]);
//         }

//         // Send Email to Admin
//         Mail::to('bizlxapp@gmail.com')->send(new NewBusinessNotification($business));

//         return response()->json([
//             'success' => true,
//             'message' => 'Business created successfully. Notification sent to admin.',
//         ], 200);
//     }
// }



public function businessRegister(Request $request)
{
    $data = $request->only('username', 'subcategory_id', 'email', 'password', 'mobile_phone', 'city_id', 'address', 'user_role', 'user_status','area');

    $validator = Validator::make($data, [
        'username' => 'required|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8|max:15',
        'subcategory_id' => 'required',
        'mobile_phone' => 'required',
        'city_id' => 'required',
        
        'address' => 'required',
        'user_role' => 'required',
        'user_status' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->messages()], 400);
    }

    $longitude_latitude = apiResponse($request->address);

    $otp = rand(100000, 999999);

    $business = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'user_role' => $request->user_role,
        'user_status' => $request->user_status,
        'username' => $request->username,
        'mobile_phone' => $request->mobile_phone,
        'longitude' => $longitude_latitude['longitude'],
        'latitude' => $longitude_latitude['latitude'],
        'otp_code' => $otp,
        'otp_expires_at' => Carbon::now()->addMinutes(15),
        'email_verified_at' => false,
    ]);

    if ($business) {
        $state_id = $request->state_id ?: City::find($request->city_id)->state_id;

        Profile::create([
            'user_id' => $business->id,
            'state_id' => $state_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'area' => $request->area,
            'profile_status' => 1,
        ]);
        

        if ($request->subcategory_id) {
            $get_cat_id = Subcategory::find($request->subcategory_id);
            Business::create([
                'sub_cat_id' => $request->subcategory_id,
                'business_id' => $business->id,
                'cat_id' => $get_cat_id->cat_id,
                'plan_id' => 1,
                'listedby_reseller_id' => $request->refferal_code,
            ]);
        }

        // Send mail to user for OTP
        // Mail::to($business->email)->send(new BusinessEmailVerification($otp));

        // Send notification to admin
        Mail::to('bizlxapp@gmail.com')->send(new NewBusinessNotification($business));

        return response()->json([
            'success' => true,
            'message' => 'Business created successfully. OTP sent for verification.',
        ], 200);
    }
}

public function verifyOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'otp' => 'required|digits:6',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->messages()], 400);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || $user->otp_code !== $request->otp) {
        return response()->json(['error' => 'Invalid OTP.'], 401);
    }

    if (now()->gt($user->otp_expires_at)) {
        return response()->json(['error' => 'OTP has expired.'], 401);
    }

    $user->email_verified_at = true;
    $user->otp_code = null;
    $user->otp_expires_at = null;
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Email verified successfully. You may now log in.',
    ], 200);
}

public function resendOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->messages()], 400);
    }

    $user = User::where('email', $request->email)->first();

    if ($user->email_verified) {
        return response()->json(['message' => 'Email already verified.'], 200);
    }

    $otp = rand(100000, 999999);
    $user->otp_code = $otp;
    $user->otp_expires_at = now()->addMinutes(15);
    $user->save();

    Mail::to($user->email)->send(new BusinessEmailVerification($otp));

    return response()->json([
        'success' => true,
        'message' => 'OTP resent successfully. Please check your email.',
    ]);
}

    public function getbusinessProfile($uname, $loggedUser_id)
    {
        $getuser = User::Where('username', $uname)->first();
        $id = $getuser->id;
        if ($getuser == null) {
            return response()->json(
                ['message' => 'Business Profile Not Found'],
                400
            );
        } else {
            date_default_timezone_set('Asia/Kolkata');
            $currentDate = date('Y-m-d');
            $userid = $getuser->id;
            $getbusinessprofile = User::Join('profiles', 'profiles.user_id', '=', 'users.id')
                ->with('services', 'jobs', 'allreview', 'allvideo', 'galleryimage', 'alltarrif', 'allsale')
                ->join('cities', 'cities.id', 'profiles.city_id')
                ->join('states', 'states.id', 'cities.state_id')
                ->join('countries', 'countries.id', 'states.country_id')
                ->join('businesses', 'users.id', 'businesses.business_id')
                ->where('users.id', $userid)
                ->select(
                    'users.*',
                    'profiles.about',
                    'profiles.id as profile_id',
                    'profiles.user_id',
                    'profiles.area',
                    'cities.city',
                    'countries.country',
                    'countries.phonecode',
                    'states.state',
                    'profiles.user_avatar',
                    'profiles.live_location',
                    'businesses.id as business_id'
                )
                ->withcount('allreview', 'services', 'allsale', 'allvideo', 'jobs')
                ->withSum('allreview', 'rating')
                ->withAvg('allreview', 'rating')
                ->first();
            $firstLetter = strtoupper(substr($getbusinessprofile->name, 0, 1));
            $spacePosition = strpos($getbusinessprofile->name, ' ');

            if ($spacePosition !== false) {
                $secondLetter = strtoupper(substr($getbusinessprofile->name, $spacePosition + 1, 1));
            } else {
                $secondLetter = '';
            }
            $getbusinessprofile->first_last_letter = $firstLetter . $secondLetter;
            // Check user review added or not
            if ($userid == $loggedUser_id) {
                $added_review = "Business can't add self review.";
            } else {
                $added_review = Review::Where('review_user_id', $loggedUser_id)->Where('review_business_id', $userid)->first();
            }

            $user_business_wishlist = Wishlist::Where('business_id', $getbusinessprofile->id)->where('user_id', '=', $loggedUser_id)->first();
            $getbusinessprofile['user_business_wishlist'] = $user_business_wishlist;

            $getbusinessprofile['user_added_reviewed'] = $added_review;
            // get all reviews images
            if (count($getbusinessprofile['allreview']) > 0) {
                for ($i = 0; $i < count($getbusinessprofile['allreview']); $i++) {
                    $review_id = $getbusinessprofile['allreview'][$i]['id'];
                    $allReviewImages = ReviewImage::Where('review_id', $review_id)->Select('id', 'review_id', 'review_image_url')->get();
                    $getbusinessprofile['allreview'][$i]['review_images'] = $allReviewImages;
                }
            }

            for ($i = 0; $i < count($getbusinessprofile['jobs']); $i++) { // get wishlisted of loggedin user of Jobs services
                $user_hotdeal_wishlist = Wishlist::Where('services_id', $getbusinessprofile['jobs'][$i]->id)->where('user_id', '=', $loggedUser_id)->first();
                $getbusinessprofile['jobs'][$i]['user_jobs_wishlist'] = $user_hotdeal_wishlist;

                $firstLetter = strtoupper(substr($getbusinessprofile['jobs'][$i]['name'], 0, 1));
                $spacePosition = strpos($getbusinessprofile['jobs'][$i]['name'], ' ');

                if ($spacePosition !== false) {
                    $secondLetter = strtoupper(substr($getbusinessprofile['jobs'][$i]['name'], $spacePosition + 1, 1));
                } else {
                    $secondLetter = '';
                }
                $getbusinessprofile['jobs'][$i]['first_last_letter'] = $firstLetter . $secondLetter;
            }
            for ($i = 0; $i < count($getbusinessprofile['allsale']); $i++) { // get wishlisted of loggedin user of Sales services
                $user_hotdeal_wishlist = Wishlist::Where('services_id', $getbusinessprofile['allsale'][$i]->id)->where('user_id', '=', $loggedUser_id)->first();
                $getbusinessprofile['allsale'][$i]['saledate_to'] = date("d-m-Y", strtotime($getbusinessprofile['allsale'][$i]['saledate_to']));
                $getbusinessprofile['allsale'][$i]['user_sale_wishlist'] = $user_hotdeal_wishlist;
            }

            $hotDeals =  Hotdeal::leftJoin('profiles', 'profiles.id', 'hotdeals.business_id')
                ->leftJoin('cities', 'cities.id', 'profiles.city_id')
                ->leftJoin('states', 'states.id', 'profiles.state_id')
                ->with('hotdealsImages')
                ->select(
                    'hotdeals.id',
                    'hotdeals.business_id as user_id',
                    'hotdeals.hot_deal_title',
                    'hotdeals.hotdeal_slug',
                    'hotdeals.hot_deal_description',
                    'hotdeals.price_from',
                    'hotdeals.price_to',
                    'hotdeals.date_from',
                    'hotdeals.date_to',
                    'hotdeals.hot_deal_status',
                    'profiles.user_avatar',
                    'profiles.address',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                )
                ->where('user_id', '=', $getbusinessprofile->user_id)
                ->where('hotdeals.date_from', '<=', $currentDate)
                ->where('hotdeals.date_to', '>=', $currentDate)
                ->where('hotdeals.hot_deal_status', '=', 1)
                ->orderBy('hotdeals.id', 'DESC')
                ->distinct()
                ->get();

            for ($i = 0; $i < count($hotDeals); $i++) { // get wishlisted of loggedin user of Hot-Deals services
                $user_hotdeal_wishlist = Wishlist::Where('services_id', $hotDeals[$i]->id)->where('user_id', '=', $loggedUser_id)->first();
                $hotDeals[$i]['user_hotdeal_wishlist'] = $user_hotdeal_wishlist;
                $hotDeals[$i]['date_from'] = date("d-m-Y", strtotime($hotDeals[$i]['date_from']));
                $hotDeals[$i]['date_to'] = date("d-m-Y", strtotime($hotDeals[$i]['date_to']));
            }
        }
        $coverimages = DB::Table('galleryimages')
            ->Where('galleryimages.image_type', '=', 1)
            ->Where('galleryimages.user_id', $userid)
            ->get();
        $count1 = DB::Table('reviews')
            ->Join('users', 'users.id', '=', 'reviews.review_business_id')
            //                ->Where('rating', '=',1)
            ->whereIn('rating', [1, 1.5])
            ->Where('users.id', $userid)
            ->count();
        $count2 = DB::Table('reviews')
            ->Join('users', 'users.id', '=', 'reviews.review_business_id')
            //                ->Where('rating', '=',2)
            ->whereIn('rating', [2, 2.5])
            ->Where('users.id', $userid)
            ->count();
        $count3 = DB::Table('reviews')
            ->Join('users', 'users.id', '=', 'reviews.review_business_id')
            //                ->Where('rating', '=',3)
            ->whereIn('rating', [3, 3.5])
            ->Where('users.id', $userid)
            ->count();
        $count4 = DB::Table('reviews')
            ->Join('users', 'users.id', '=', 'reviews.review_business_id')
            //                ->Where('rating', '=',4)
            ->whereIn('rating', [4, 4.5])
            ->Where('users.id', $userid)
            ->count();
        $count5 = DB::Table('reviews')
            ->Join('users', 'users.id', '=', 'reviews.review_business_id')
            ->Where('rating', '=', 5)
            ->Where('users.id', $userid)
            ->count();

        $getbusinessprofile->allreview_avg_rating = round($getbusinessprofile->allreview_avg_rating, 1);

        // # Get cover images for business slider (code start)
        // $cover_images = Galleryimage::where('user_id',$userid)->where('image_type',1)->get();
        // # Get cover images for business slider (code end)

        # Get business details ( code start )
        $tabs_array = null;
        $business_data = Business::where('business_id', $id)->get();
        $business_data_count = count($business_data);
        $exploded_business_subcat_ids = [];
        if ($business_data_count > 0) {
            for ($i = 0; $i < $business_data_count; $i++) {
                $exploded_business_subcat_ids[] = explode(",", $business_data[$i]['sub_cat_id']);
            }
            $get_all_tabs = Tab::all();
            $get_all_tabs_count = count($get_all_tabs);
            if ($get_all_tabs_count > 0) {
                $exploded_tabs_subcat_ids = [];
                foreach ($get_all_tabs as $tab) {
                    $exploded_tabs_subcat_ids[] = explode(",", $tab->sub_cat_id);
                }

                foreach ($exploded_business_subcat_ids[0] as $business_subcat_id) {
                    foreach ($exploded_tabs_subcat_ids[0] as $tab_subcat_id) {
                        if ($business_subcat_id == $tab_subcat_id) {

                            $tabs_array = Tab::whereRaw("find_in_set($tab_subcat_id,sub_cat_id)")->get();
                        }
                    }
                }
            }
        }
        # Get business details ( code end )
        # To add the tabs content by comparing their ids ( code start )
        $business_tab_ids = [];
        $business_tabs_info = Tabcontent::where('business_id', $id)->get();
        $business_tabs_info_count = count($business_tabs_info);
        if ($business_tabs_info_count > 0) {
            foreach ($business_tabs_info as $business_tabs) {
                $business_tab_ids[] = $business_tabs->tab_id;
            }
        }
        if ($tabs_array != null) {
            $tabs_array_count = count($tabs_array);

            for ($i = 0; $i < $tabs_array_count; $i++) {
                if (in_array($tabs_array[$i]['id'], $business_tab_ids)) {
                    $data = Tabcontent::where('business_id', $id)->where('tab_id', $tabs_array[$i]['id'])->first();
                    $tabs_array[$i]['tab_content'] = $data->tab_content;
                }
            }
        }
        # To add the tabs content by comparing their ids ( code end )

        if ($tabs_array == null) {
            $final_array = [];
        } else {
            $final_array = $tabs_array;
        }

        $jobs = "<iframe src='https://bizlx.com/jobsby/business/$uname' width='100%' height='400px'></iframe>";

        $hotdeal = "<iframe src='https://bizlx.com/hotdealsby/business/$uname' width='100%' height='400px'></iframe>";

        $sales = "<iframe src='https://bizlx.com/salesby/business/$uname' width='100%' height='400px'></iframe>";

        $reviews = "<iframe src='https:/bizlx.com/reviewsby/business/$uname' width='100%' height='400px'></iframe>";

        return response()->json(
            [
                'status' => 200,
                'businessprofile' => $getbusinessprofile,
                "jobs" => $jobs,
                "hotdeal" => $hotdeal,
                "sales" => $sales,
                "reviews" => $reviews,
                'allhotdeal' => $hotDeals,
                'coverimages' => $coverimages,
                'count1' => $count1,
                'count2' => $count2,
                'count3' => $count3,
                'count4' => $count4,
                'count5' => $count5,
                'tabs' => $final_array,
            ]
        );
    }

    public function getBprofile(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        $buser_id = $User->id;
    
        $buser1 = User::LeftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->LeftJoin('businesses', 'businesses.business_id', 'users.id')
            ->LeftJoin('categories', 'categories.id', 'businesses.cat_id')
            ->LeftJoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id')
            ->Join('countries', 'countries.id', '=', 'profiles.country_id')
            ->Join('cities', 'cities.id', '=', 'profiles.city_id')
            ->Join('states', 'states.id', '=', 'cities.state_id')
            ->LeftJoin('services', 'services.service_user_id', '=', 'users.id')
            ->Where('users.id', '=', $buser_id)
            ->Select(
                'users.id', 'users.name', 'users.email', 'users.username', 'users.mobile_phone', 'users.password',
                'profiles.user_avatar', 'profiles.about', 'profiles.address','profiles.area','profiles.live_location',
                'businesses.business_id', 'businesses.gst', 'businesses.listedby', 'businesses.listedby_reseller_id',
                'categories.id As cat_id', 'categories.cat_name',
                'subcategories.id As subcat_id', 'subcategories.subcat_name',
                'countries.id AS country_id', 'countries.country',
                'cities.id As city_id', 'cities.city',
                'states.id As state_id', 'states.state',
                'services.service_user_id', 'services.service_text',
                'businesses.sub_cat_id', 'businesses.id as buserid'
            )
            ->first();
    
        // Service Text Decode
        $buser1->service_text = json_decode($buser1->service_text);
    
        // Subcategories List
        $ids = explode(",", $buser1->sub_cat_id);
        $subcats = Subcategory::whereIn('id', $ids)->select('id', 'subcat_name')->get();
    
        // Reseller Data
        $get_reseller_data = User::leftjoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->Join('cities', 'cities.id', '=', 'profiles.city_id')
            ->select('users.id', 'users.name', 'users.email', 'users.mobile_phone', 'profiles.id as profile_id', 'profiles.address', 'cities.id as city_id', 'cities.city')
            ->where('users.id', $buser1->listedby_reseller_id)
            ->get()->toArray();
    
        foreach ($get_reseller_data as $user) {
            $get_reseller_data[0]['id'] = 'RES' . $user['id'];
        }
    
        // ✅ Get Business Feature Info
        // $businessFeature = Businessfeature::with(['cities:id,city', 'categories:id,cat_name'])
        //     ->where('user_id', $buser_id)
        //     ->first();
    
        // $featureCities = $businessFeature ? $businessFeature->cities->map(function ($city) {
        //     return ['id' => $city->id, 'city' => $city->city];
        // }) : [];
    
        // $featureCategories = $businessFeature ? $businessFeature->categories->map(function ($cat) {
        //     return ['id' => $cat->id, 'cat_name' => $cat->cat_name];
        // }) : [];
    
        // $businessfeatureStatus = $businessFeature ? $businessFeature->businessfeature_status : 0;
    
        // Tabs Section
        $tabs_array = null;
        $business_data = Business::where('business_id', $buser_id)->get();
        $exploded_business_subcat_ids = [];
    
        if ($business_data->count() > 0) {
            foreach ($business_data as $item) {
                $exploded_business_subcat_ids[] = explode(",", $item['sub_cat_id']);
            }
    
            $get_all_tabs = Tab::all();
            foreach ($exploded_business_subcat_ids[0] as $business_subcat_id) {
                foreach ($get_all_tabs as $tab) {
                    $tab_subcat_ids = explode(",", $tab->sub_cat_id);
                    if (in_array($business_subcat_id, $tab_subcat_ids)) {
                        $tabs_array = Tab::whereRaw("find_in_set($business_subcat_id,sub_cat_id)")->get();
                    }
                }
            }
        }
    
        $business_tabs_info = Tabcontent::where('business_id', $buser_id)->get();
        $business_tab_ids = $business_tabs_info->pluck('tab_id')->toArray();
    
        if ($tabs_array != null) {
            foreach ($tabs_array as $i => $tab) {
                if (in_array($tab['id'], $business_tab_ids)) {
                    $data = $business_tabs_info->where('tab_id', $tab['id'])->first();
                    $tabs_array[$i]['tab_content'] = $data->tab_content;
                }
            }
        }
    
        $final_array = $tabs_array ?? [];
    
        if ($User) {
            return response()->json([
                'buser' => $buser1,
                'subcatids' => $subcats,
                'reseller_data' => $get_reseller_data,
                'tabs' => $final_array,
                // 'feature_cities' => $featureCities,
                // 'feature_categories' => $featureCategories,
                // 'businessfeature_status' => $businessfeatureStatus,
            ], 200);
        } else {
            return response()->json(['message' => 'token required'], 400);
        }
    }
    
    public function getBprofile1(Request $request)
    {
        $buser_id = $request->bussine_id;
    
        $buser1 = User::LeftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->LeftJoin('businesses', 'businesses.business_id', '=', 'users.id')
            ->LeftJoin('categories', 'categories.id', '=', 'businesses.cat_id')
            ->LeftJoin('subcategories', 'subcategories.id', '=', 'businesses.sub_cat_id')
            ->Join('countries', 'countries.id', '=', 'profiles.country_id')
            ->Join('cities', 'cities.id', '=', 'profiles.city_id')
            ->Join('states', 'states.id', '=', 'cities.state_id')
            ->LeftJoin('services', 'services.service_user_id', '=', 'users.id')
            ->Where('users.id', '=', $buser_id)
            ->Select(
                'users.id',
                'users.name',
                'users.email',
                'users.username',
                'users.mobile_phone',
                'users.password',
                'profiles.user_avatar',
                'profiles.area',
                'profiles.about',
                'profiles.address',
                'profiles.live_location',
                'businesses.business_id',
                'businesses.gst',
                'businesses.listedby',
                'businesses.listedby_reseller_id',
                'categories.id As cat_id',
                'categories.cat_name',
                'subcategories.id As subcat_id',
                'subcategories.subcat_name',
                'countries.id AS country_id',
                'countries.country',
                'cities.id As city_id',
                'cities.city',
                'states.id As state_id',
                'states.state',
                'services.service_text',
                'businesses.sub_cat_id',
                'businesses.id as buserid'
            )
            ->first();
    
        $buser1->service_text = json_decode($buser1->service_text);
    
        // Decode subcats
        $ids = explode(",", $buser1->sub_cat_id);
        $subcats = Subcategory::whereIn('id', $ids)->select('id', 'subcat_name')->get();
    
        // Reseller data
        $get_reseller_data = User::leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->Join('cities', 'cities.id', '=', 'profiles.city_id')
            ->select('users.id', 'users.name', 'users.email',  'users.username','users.mobile_phone', 'profiles.id as profile_id', 'profiles.address', 'cities.id as city_id', 'cities.city')
            ->where('users.id', $buser1->listedby_reseller_id)
            ->get()
            ->toArray();
    
        foreach ($get_reseller_data as $user) {
            $get_reseller_data[0]['id'] = 'RES' . $user['id'];
        }
    
        // Tabs logic
        $tabs_array = [];
        $business_data = Business::where('business_id', $buser_id)->get();
        $business_data_count = count($business_data);
    
        if ($business_data_count > 0) {
            $exploded_business_subcat_ids = explode(",", $business_data[0]->sub_cat_id);
            $get_all_tabs = Tab::all();
    
            foreach ($exploded_business_subcat_ids as $business_subcat_id) {
                foreach ($get_all_tabs as $tab) {
                    if (in_array($business_subcat_id, explode(",", $tab->sub_cat_id))) {
                        $tabs_array[] = $tab;
                    }
                }
            }
    
            // Attach tab content
            foreach ($tabs_array as &$tab) {
                $tabContent = Tabcontent::where('business_id', $buser_id)
                    ->where('tab_id', $tab->id)
                    ->first();
                $tab->tab_content = $tabContent ? $tabContent->tab_content : '';
            }
        }
    
        // 🔥 Featured cities & categories
        $businessFeature = DB::table('businessfeatures')
            ->where('user_id', $buser_id)
            ->where('businessfeature_status', 1)
            ->first();
            $businessfeatureStatus = $businessFeature ? $businessFeature->businessfeature_status : 0;
    
        $featuredCityIds = [];
        $featuredCities = [];
        $featuredCategoryIds = [];
        $featuredCategories = [];
    
        if ($businessFeature) {
            $featureId = $businessFeature->id;
    
            $featuredCityIds = DB::table('businessfeature_city')
                ->where('businessfeature_id', $featureId)
                ->pluck('city_id');
    
            $featuredCities = DB::table('cities')
                ->whereIn('id', $featuredCityIds)
                ->pluck('city');
    
            $featuredCategoryIds = DB::table('businessfeature_subcategory')
                ->where('businessfeature_id', $featureId)
                ->pluck('subcategory_id');
    
            $featuredCategories = DB::table('categories')
                ->whereIn('id', $featuredCategoryIds)
                ->pluck('cat_name');
        }
    
        return response()->json([
            'buser' => $buser1,
            'subcatids' => $subcats,
            'reseller_data' => $get_reseller_data,
            'tabs' => $tabs_array,
            'featured_cities' => $featuredCities,
            'featured_categories' => $featuredCategories,
            'featured_cities_ids' => $featuredCityIds,
            'featured_categories_ids' => $featuredCategoryIds,
                            'businessfeature_status' => $businessfeatureStatus,

        ]);
    }
    
    // public function updateBprofile(Request $request)
    // {

    //     $User = JWTAuth::authenticate($request->token);
    //     $buser_id = $User->id;
    //     $data = $request->only('name', 'username', 'email', 'mobile_phone');
    //     $validator = Validator::make($data, [
    //         'name' => 'required|string',
    //         'username' => 'required|string|alpha_dash',
    //         'email' => 'required|email',
    //         'mobile_phone' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->messages()], 400);
    //     } else {
    //         $update_business = User::find($buser_id);
    //         $update_business->name = $request->name;
    //         $update_business->email = $request->email;
    //         $update_business->username = $request->username;
    //         $update_business->mobile_phone = $request->mobile_phone;
    //         $update_business->update();

    //         $update_avatar = Profile::Where('user_id', $buser_id)->first();
    //         if ($request->file('user_avatar')) {
    //             $call_function = new Profile();
    //             $file = $request->file('user_avatar');
    //             $old_file = $update_avatar->user_avatar;
    //             $update_avatar->user_avatar = $call_function->avatar($old_file, $file);
    //             $update_avatar->update();
    //         }
    //         return response()->json(
    //             [
    //                 'buser' => 'Business Profile Updated Successfully.'
    //             ],
    //             200
    //         );
    //     }
    // }   


    
    public function updateBprofile(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        $buser_id = $User->id;
    
        $data = $request->only('name', 'username', 'email', 'mobile_phone');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'username' => 'required|string|alpha_dash',
            'email' => 'required|email',
            'mobile_phone' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
    
        DB::beginTransaction();
        try {
            // Update user info
            $user = User::find($buser_id);
            $user->update($data);
    
            // Update avatar
            $profile = Profile::where('user_id', $buser_id)->first();
            if ($request->hasFile('user_avatar')) {
                $oldFile = $profile->user_avatar;
                $profile->user_avatar = (new Profile())->avatar($oldFile, $request->file('user_avatar'));
                $profile->save();
            }
    
            // ✅ FEATURE AD TOGGLE
            // ✅ FEATURE AD TOGGLE LOGIC
// if ($request->has('is_featured') && $request->is_featured == 'true') {
//     $feature = Businessfeature::updateOrCreate(
//         ['user_id' => $buser_id],
//         ['businessfeature_status' => 1]
//     );

//     // Reload the feature to ensure it's fresh with relationships
//     $feature = Businessfeature::find($feature->id);

//     // Decode city and category arrays
//     $featureCities = json_decode($request->feature_cities, true) ?? [];
//     $featureCategories = json_decode($request->feature_categories, true) ?? [];

//     // Sync relations
//     $feature->cities()->sync($featureCities);
//     $feature->categories()->sync($featureCategories);

// } else {
//     // ✅ Set status to 0 instead of deleting
//     $feature = Businessfeature::updateOrCreate(
//         ['user_id' => $buser_id],
//         ['businessfeature_status' => 0]
//     );

//     // Optionally detach cities/categories when disabled
//     $feature->cities()->detach();
//     $feature->categories()->detach();
// }

    
            DB::commit();
            return response()->json(['buser' => 'Business Profile Updated Successfully.'], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Something went wrong.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function updateBprofileByAdmin(Request $request)
    // {

    //     $buser_id = $request->bussine_id;
    //         $update_business = User::find($buser_id);
    //         $update_business->name = $request->name;
    //         $update_business->email = $request->email;
    //         $update_business->username = $request->username;
    //         $update_business->mobile_phone = $request->mobile_phone;
    //         $update_business->update();

    //         $update_avatar = Profile::Where('user_id', $buser_id)->first();
    //         if ($request->file('user_avatar')) {
    //             $call_function = new Profile();
    //             $file = $request->file('user_avatar');
    //             $old_file = $update_avatar->user_avatar;
    //             $update_avatar->user_avatar = $call_function->avatar($old_file, $file);
    //             $update_avatar->update();
    //         }
    //         return response()->json(
    //             [
    //                 'buser' => 'Admin Business Profile Updated Successfully.'
    //             ],
    //             200
    //         );
    // }



public function updateBprofileByAdmin(Request $request)
{
    $buser_id = $request->bussine_id;

    // 1. Update User Details
    $update_business = User::find($buser_id);
    $update_business->name = $request->name;
    $update_business->email = $request->email;
    $update_business->username = $request->username;
    $update_business->mobile_phone = $request->mobile_phone;
    $update_business->update();

    // 2. Update Avatar
    $update_avatar = Profile::where('user_id', $buser_id)->first();
    if ($request->file('user_avatar')) {
        $call_function = new Profile();
        $file = $request->file('user_avatar');
        $old_file = $update_avatar->user_avatar;
        $update_avatar->user_avatar = $call_function->avatar($old_file, $file);
        $update_avatar->update();
    }

    // 3. Handle Featured Toggle
    $is_featured = $request->is_featured === 'true' || $request->is_featured === true ? 1 : 0;

    // 4. Update or Insert main feature record
    $featureId = DB::table('businessfeatures')->updateOrInsert(
        ['user_id' => $buser_id],
        ['businessfeature_status' => $is_featured, 'updated_at' => now(), 'created_at' => now()]
    );

    // 🔐 Now get the actual feature ID (important)
    $featureId = DB::table('businessfeatures')->where('user_id', $buser_id)->value('id');

    // 🔄 ONLY update cities/categories if toggle is ON
    if ($is_featured) {
        // Cities
        if (is_array($request->feature_cities)) {
            DB::table('businessfeature_city')->where('businessfeature_id', $featureId)->delete();
            foreach ($request->feature_cities as $cityId) {
                DB::table('businessfeature_city')->insert([
                    'businessfeature_id' => $featureId,
                    'city_id' => $cityId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Categories
       // after you’ve upserted businessfeatures and have $featureId...

if ($is_featured && is_array($request->feature_categories)) {
    // clear old picks
    DB::table('businessfeature_subcategory')
      ->where('businessfeature_id', $featureId)
      ->delete();

    // insert each picked subcategory
    foreach ($request->feature_categories as $subcatId) {
        // you can optionally validate it exists:
        if (Subcategory::where('id', $subcatId)->exists()) {
            DB::table('businessfeature_subcategory')->insert([
                'businessfeature_id' => $featureId,
                'subcategory_id'     => $subcatId,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }
}
    }

    return response()->json([
        'buser' => 'Admin Business Profile Updated Successfully.',
        'featured_status' => $is_featured,
        'saved_feature_id' => $featureId
    ], 200);
}

    
    
    
    


    public function sendInquiryInfo(Request $request)
    {

        $User = JWTAuth::authenticate($request->token);
        $get_business_user_email = User::find($request->business_user_id);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not valid!',
            ], 401);
        } else {
            $data = $request->only('name', 'email', 'phone');
            $validator = Validator::make($data, [
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                # To send email to user who filled form (code start)
                $data = [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'businessName' => $get_business_user_email->name,
                ];
                $getadmin_email = User::Where('user_role', '=', '4')->first();
                $admin_email =   $getadmin_email->email;
                Mail::send('inquiry', $data, function ($message) use ($admin_email, $request) {
                    $message->to($admin_email)->subject('Inquiry noted.');
                    $message->from($request->email);
                });
                # To send email to user who filled form (code end)

                # To send email to business ( code start)
                $get_business_user_email = User::find($request->business_user_id);
                $business_email = $get_business_user_email->email;
                if ($business_email == null) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Business user email not found!.'
                        ],
                        404
                    );
                } else {
                    $data = [
                        'businessName' => $get_business_user_email->name,
                        'username' => $request->name,
                        'description' => $request->textarea
                    ];
                    Mail::send('businesstemplate', $data, function ($message) use ($business_email, $request) {
                        $message->to($business_email)->subject('Inquiry noted.');
                        $message->from($request->email);
                    });
                }
                # To send email to business ( code end)
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Inquiry sent successfully.'
                    ],
                    200
                );
            }
        }
    }

    # To add to wishlist (function start)
    public function addWishlist(Request $request)
    {
        $token = $request->header('Authorization');
    
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token is required'
            ], 400);
        }
    
        $User = JWTAuth::authenticate($token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ]);
        } else {
            $data = $request->only('business_id', 'wishlist_type', 'services_id');
            $validator = Validator::make($data, [
                'business_id' => 'required',
                'wishlist_type' => 'required',
                'services_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $existing_wishlist = Wishlist::where('user_id', $User->id)->where("business_id", $request->business_id)->where("wishlist_type", $request->wishlist_type)->where("services_id", $request->services_id)->first();
                if ($existing_wishlist == null) {
                    $add_to_wishlist = new Wishlist();
                    $add_to_wishlist->user_id = $User->id;
                    $add_to_wishlist->business_id = $request->business_id;
                    $add_to_wishlist->wishlist_type = $request->wishlist_type;
                    $add_to_wishlist->services_id = $request->services_id;
                    $add_to_wishlist->save();
                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Wishlist added succesfully!',
                            'business' => $add_to_wishlist
                        ],
                        200
                    );
                } else {
                    $existing_wishlist_remove = $existing_wishlist->delete();
                    if ($existing_wishlist_remove) {
                        return response([
                            'success' => true,
                            'message' => 'Wishlist removed succesfully!'
                        ], 200);
                    }
                }
            }
        }
    }
    # To add to wishlist (function end)

    # To remove from wishlist (function start)
    public function removeFromWishlist(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ]);
        } else {
            $data = $request->only('business_id');
            $validator = Validator::make($data, [
                'business_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $existing_wishlist = Wishlist::where('user_id', $User->id)->where("business_id", $request->business_id)->first();
                if ($existing_wishlist == null) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'You have entered a wrong business_id or business already removed from wishlist !',
                        ],
                        400
                    );
                } else {
                    Wishlist::where('user_id', $User->id)->where("business_id", $request->business_id)->delete();
                    return response([
                        'success' => true,
                        'message' => 'Business removed from wishlist succesfully.'
                    ], 200);
                }
            }
        }
    }
    # To remove from wishlist (function end)

    # To get user's gallery images (function start)
    public function userGalleryImages(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ]);
        } else {
            $get_gallery_images = Galleryimage::where('user_id', $User->id)->where('image_type', 0)->get();
            return response([
                'success' => true,
                'gallery_images' => $get_gallery_images
            ]);
        }
    }

    public function userGalleryImagesbyadmin(Request $request)
    {
        $userId = $request->userId;
        $get_gallery_images = Galleryimage::where('user_id', $userId)->where('image_type', 0)->get();
        return response([
            'success' => true,
            'gallery_images' => $get_gallery_images
        ]);
        
    }
    # To get user's gallery images (function end)

    # To get user's cover image (function start)
    public function coverImage(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ]);
        } else {

            $get_cover_image = Galleryimage::where('user_id', $User->id)->where('image_type', 1)->get();
            return response([
                'success' => true,
                'cover_image' => $get_cover_image
            ]);
        }
    }

    public function coverImagebyadmin(Request $request)
    {

        // Retrieve the user ID from the request
        $userId = $request->userId;
        $get_cover_image = Galleryimage::where('user_id', $userId)->where('image_type', 1)->get();
        return response()->json([
            'success' => true,
            'cover_image' => $get_cover_image,
        ]);
    }


    # To get user's cover image (function end)

    # Update business dashboard profile info business information profile data
    public function updateBprofile2(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        $buser_id = $User->id;
        $data = $request->all();

        // Validate request data
        $validator = Validator::make($data, [
            'cat_id' => 'required',
            'subcat_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'mobile_phone' => 'required',
            'address' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        } else {
            // Update user details
            $update_business = User::find($buser_id);
            $update_business->mobile_phone = $request->mobile_phone;
            $longitude_latitude = apiResponse($request->address);
            $update_business->longitude = $longitude_latitude['longitude'];
            $update_business->latitude = $longitude_latitude['latitude'];
            $update_business->save(); // Use save() instead of update()

            // Update or create business details
            Business::where('business_id', $buser_id)->delete();

            $subcat_ids = is_array($request->subcat_id) ? $request->subcat_id : [$request->subcat_id];

            foreach ($subcat_ids as $subcat_id) {
                $businesses = new Business();
                $businesses->sub_cat_id = $subcat_id;
                $businesses->business_id = $buser_id;
                $businesses->cat_id = $request->cat_id;
                $businesses->plan_id = 1;
                $businesses->gst = $request->gst;
                $businesses->save();
            }

            // Update profile details
            Profile::where('user_id', $buser_id)->update([
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'area' => $request->area,
                'live_location' => $request->live_location,
                'about' => $request->about,
                'address' => $request->address,
            ]);

            // Update or create service details
            $service = Service::where('service_user_id', $buser_id)->first();
            if ($service === null) {
                $service = new Service();
                $service->service_user_id = $buser_id;
            }
              // Filter out null values from the service_text array
            $service_text = array_filter($request->service_text, function ($value) {
                return !is_null($value);
            });
            // Save the filtered service_text array as a JSON-encoded string
            $service->service_text = $service_text;
            $service->save();
            // $service->service_text = $request->service_text;
            // $service->save();

            return response()->json([
                'buser' => 'Business Profile Updated Successfully.'
            ], 200);
        }
    }

    public function updateBprofile2byadmin(Request $request)
    {
        $buser_id = $request->bussine_id;
        $data = $request->all();

        // Validate request data
        $validator = Validator::make($data, [
            'cat_id' => 'required',
            'subcat_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'mobile_phone' => 'required',
            'address' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        } else {
            // Update user details
            $update_business = User::find($buser_id);
            $update_business->mobile_phone = $request->mobile_phone;
            $longitude_latitude = apiResponse($request->address);
            $update_business->longitude = $longitude_latitude['longitude'];
            $update_business->latitude = $longitude_latitude['latitude'];
            $update_business->save(); // Use save() instead of update()

            // Update or create business details
            Business::where('business_id', $buser_id)->delete();

            $subcat_ids = is_array($request->subcat_id) ? $request->subcat_id : [$request->subcat_id];

            foreach ($subcat_ids as $subcat_id) {
                $businesses = new Business();
                $businesses->sub_cat_id = $subcat_id;
                $businesses->business_id = $buser_id;
                $businesses->cat_id = $request->cat_id;
                $businesses->plan_id = 1;
                $businesses->gst = $request->gst;
                $businesses->save();
            }

            // Update profile details
            Profile::where('user_id', $buser_id)->update([
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'live_location' => $request->live_location,
                'about' => $request->about,
                'address' => $request->address,
                'area' => $request->area,
            ]);

            // Update or create service details
            $service = Service::where('service_user_id', $buser_id)->first();
            if ($service === null) {
                $service = new Service();
                $service->service_user_id = $buser_id;
            }
            // Filter out null values from the service_text array
            $service_text = array_filter($request->service_text, function ($value) {
                return !is_null($value);
            });
            // Save the filtered service_text array as a JSON-encoded string
            $service->service_text = $service_text;
            $service->save();
            // $service->service_text = $request->service_text;
            // $service->save();

            return response()->json([
                'buser' => 'Business Profile Updated Successfully.'
            ], 200);
        }
    }

    # update business dashboard profile info business changes password api
    public function updatePassword(Request $request)
    {

        $User = JWTAuth::authenticate($request->token);
        $user_id = $User->id;
        if ($User) {
            $update_password = User::find($user_id);
            $password = Hash::make($request->password);
            $update_password->password = $password;
            $update_password->update();

            return response()->json(
                [
                    'buser' => 'Business Password Updated Successfully.'
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'buser' => 'Business Password Not Updated!.'
                ],
                400
            );
        }
    }
    // save business cover images api

    public function saveCoverimages(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        $user_id = $User->id;
        $get_gallery_data = Galleryimage::Where('user_id', $user_id)->Where('image_type', '1')->get();
        $get_gallery_data_count = count($get_gallery_data);
        $get_coverimages_count = 3;

        if ($get_gallery_data_count < $get_coverimages_count) {
            if ($request->file('image_url')) {
                $destinationPath = '/images/cover_images';
                $file = $request->file('image_url');
                $real_name = time() . hexdec(rand(11111, 99999)) . '.' . $file->getClientOriginalExtension(); // type change
                $img = Image::make($file->getRealPath())->resize('1280', 'null', function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $final_path = $destinationPath . "/" . $real_name;
                Storage::disk('s3')->put($final_path, $img->stream()->__toString());
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
        } else {
            return response()->json(
                [
                    'message' => "You Are Max 3 Cover Images Can Be Loaded.",
                ]
            );
        }
    }
    public function saveCoverimages1(Request $request)
    {
        // $User = JWTAuth::authenticate($request->token);
        $user_id = $request->user_id;
        $get_gallery_data = Galleryimage::Where('user_id', $user_id)->Where('image_type', '1')->get();
        $get_gallery_data_count = count($get_gallery_data);
        $get_coverimages_count = 3;

        if ($get_gallery_data_count < $get_coverimages_count) {
            if ($request->file('image_url')) {
                $destinationPath = '/images/cover_images';
                $file = $request->file('image_url');
                $real_name = time() . hexdec(rand(11111, 99999)) . '.' . $file->getClientOriginalExtension(); // type change
                $img = Image::make($file->getRealPath())->resize('1280', 'null', function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $final_path = $destinationPath . "/" . $real_name;
                Storage::disk('s3')->put($final_path, $img->stream()->__toString());
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
        } else {
            return response()->json(
                [
                    'message' => "You Are Max 3 Cover Images Can Be Loaded.",
                ]
            );
        }
    }
    public function saveGalleryimages(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        $user_id = $User->id;
        $business_data = Business::Where('business_id', $user_id)->first();
        $user_plan_id = $business_data->plan_id=1;
        $business_plan = Plan::Where('id', $user_plan_id)->first();
        $get_gallery_data = Galleryimage::Where('user_id', $user_id)->Where('image_type', '0')->get();

        if (count($get_gallery_data) > 0) {
            if ($business_plan->images > $get_gallery_data->count()) {

                if ($request->file('image_url')) {

                    $destinationPath = '/images/gallery_images';
                    $file = $request->file('image_url');
                    $real_name = time() . hexdec(rand(11111, 99999)) . '.' . $file->getClientOriginalExtension(); // type change
                    $img = Image::make($file->getRealPath())->resize('1280', 'null', function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $final_path = $destinationPath . "/" . $real_name;
                    Storage::disk('s3')->put($final_path, $img->stream()->__toString());
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
                        'message' => "Gallery Image Added Successfully.",
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => "You have already added " . $get_gallery_data->count() . ' Gallery Images',
                    ],
                    200
                );
            }
        } else {
            if ($request->file('image_url')) {

                $destinationPath = '/images/gallery_images';
                $file = $request->file('image_url');
                $real_name = time() . hexdec(rand(11111, 99999)) . '.' . $file->getClientOriginalExtension(); // type change
                $img = Image::make($file->getRealPath())->resize('1280', 'null', function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $final_path = $destinationPath . "/" . $real_name;
                Storage::disk('s3')->put($final_path, $img->stream()->__toString());
            }
            $cover_image = new Galleryimage;
            $cover_image->user_id = $user_id;
            $cover_image->image_title = $request->image_title;
            $cover_image->image_type = $request->image_type;
            $cover_image->price = $request->price;
            $cover_image->image_description = $request->image_description;
            $cover_image->image_url = $final_path;
            $cover_image->save();
            return response()->json(
                [
                    'message' => "Gallery Image Added Successfully.",
                ],
                200
            );
        }
    }

    public function saveGalleryimages1(Request $request)
    {

        $user_id = $request->user_id;
        $business_data = Business::Where('business_id', $user_id)->first();
        $user_plan_id = $business_data->plan_id;
        $business_plan = Plan::Where('id', $user_plan_id)->first();
        $get_gallery_data = Galleryimage::Where('user_id', $user_id)->Where('image_type', '0')->get();

        if (count($get_gallery_data) > 0) {
            if ($business_plan->images > $get_gallery_data->count()) {

                if ($request->file('image_url')) {

                    $destinationPath = '/images/gallery_images';
                    $file = $request->file('image_url');
                    $real_name = time() . hexdec(rand(11111, 99999)) . '.' . $file->getClientOriginalExtension(); // type change
                    $img = Image::make($file->getRealPath())->resize('1280', 'null', function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $final_path = $destinationPath . "/" . $real_name;
                    Storage::disk('s3')->put($final_path, $img->stream()->__toString());
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
                        'message' => "Gallery Image Added Successfully.",
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => "You have already added " . $get_gallery_data->count() . ' Gallery Images',
                    ],
                    200
                );
            }
        } else {
            if ($request->file('image_url')) {

                $destinationPath = '/images/gallery_images';
                $file = $request->file('image_url');
                $real_name = time() . hexdec(rand(11111, 99999)) . '.' . $file->getClientOriginalExtension(); // type change
                $img = Image::make($file->getRealPath())->resize('1280', 'null', function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $final_path = $destinationPath . "/" . $real_name;
                Storage::disk('s3')->put($final_path, $img->stream()->__toString());
            }
            $cover_image = new Galleryimage;
            $cover_image->user_id = $user_id;
            $cover_image->image_title = $request->image_title;
            $cover_image->image_type = $request->image_type;
            $cover_image->price = $request->price;
            $cover_image->image_description = $request->image_description;
            $cover_image->image_url = $final_path;
            $cover_image->save();
            return response()->json(
                [
                    'message' => "Gallery Image Added Successfully.",
                ],
                200
            );
        }
    }
    # To Update the images details (function start)
    // public function UpdateImagesDetails(Request $request)
    // {
    //     $User = JWTAuth::authenticate($request->token);
    //     if (!$User) {
    //         return response([
    //             'success' => false,
    //             'message' => 'User not found !'
    //         ]);
    //     } else {
    //         $data = $request->only('id', 'title');
    //         $validator = Validator::make($data, [
    //             'id' => 'required|integer',
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json(['error' => $validator->messages()], 400);
    //         } else {
    //             $update_image_details = Galleryimage::find($request->id);
    //             $update_image_details->image_title = $request->title;
    //             $update_image_details->price = $request->price;
    //             $update_image_details->image_description = $request->description;
    //             $update_image_details->update();
    //             return response([
    //                 'success' => true,
    //                 'message' => 'Image details updated successfully.'
    //             ]);
    //         }
    //     }
    // }


    public function UpdateImagesDetails(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found!'
            ]);
        }
    
        $data = $request->only('id', 'title', 'description', 'price', 'image');
        $validator = Validator::make($data, [
            'id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
    
        $update_image_details = Galleryimage::find($request->id);
        if (!$update_image_details) {
            return response()->json(['error' => 'Image not found.'], 404);
        }
    
        $update_image_details->image_title = $request->title;
        $update_image_details->price = $request->price;
        $update_image_details->image_description = $request->description;
    
        // Check and upload the image if provided
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $real_name = time() . hexdec(rand(11111, 99999)) . '.' . $file->getClientOriginalExtension();
    
            // Resize image using Intervention Image
            $img = Image::make($file)->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
    
            // Upload to S3
            $destinationPath = '/images/cover_images';
            $final_path = $destinationPath . "/" . $real_name;
            Storage::disk('s3')->put($final_path, $img->stream()->__toString());
    
            $update_image_details->image_url = $final_path;
        }
    
        $update_image_details->save();
    
        return response([
            'success' => true,
            'message' => 'Image details updated successfully.',
            'image_url' => $update_image_details->image_url
        ]);
    }
    

    # To Update the images details (function end)

    # To delete the image details (function start)
    public function deleteImage(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('id');
            $validator = Validator::make($data, [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $delete_image = Galleryimage::find($request->id);
                if ($delete_image == null) {
                    return response([
                        'success' => false,
                        'message' => 'No data found related to this id.'
                    ], 400);
                } else {
                    if (Storage::disk('s3')->exists($delete_image->image_url)) {
                        Storage::disk('s3')->delete($delete_image->image_url);
                    }
                    $delete_image->delete();
                }
                return response([
                    'success' => true,
                    'message' => 'Image deleted successfully.'
                ]);
            }
        }
    }
    # To delete the image details (function end)

    # To update the business profile editors data ( function start )
    public function updateEditorArray(Request $request)
    {

        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            if ($request->tabids) {
                $request_ids_array_count = count($request->tabids);
                if ($request_ids_array_count > 0) {

                    $combined_array = array_combine($request->tabids, $request->items);

                    foreach ($combined_array as $key => $value) {

                        $existing_tab_id_data = Tabcontent::where('business_id', $User->id)->where('tab_id', $key)->get();

                        $existing_tab_id_data_count = count($existing_tab_id_data);

                        if ($existing_tab_id_data_count > 0) {
                            Tabcontent::where('business_id', $User->id)->where('tab_id', $key)->update([
                                'tab_content' => $value
                            ]);
                        } else {
                            $save_tab_details = new Tabcontent();
                            $save_tab_details->business_id = $User->id;
                            $save_tab_details->tab_id = $key;
                            $save_tab_details->tab_content = $value;
                            $save_tab_details->save();
                        }
                    }
                }
                return response([
                    'success' => true,
                    'message' => 'Editors data updated successfully'
                ]);
            }
        }
    }


    public function updateEditorArraybyadmin(Request $request)
    {

        $User = $request;
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            if ($request->tabids) {
                $request_ids_array_count = count($request->tabids);
                if ($request_ids_array_count > 0) {

                    $combined_array = array_combine($request->tabids, $request->items);

                    foreach ($combined_array as $key => $value) {

                        $existing_tab_id_data = Tabcontent::where('business_id', $User->id)->where('tab_id', $key)->get();

                        $existing_tab_id_data_count = count($existing_tab_id_data);

                        if ($existing_tab_id_data_count > 0) {
                            Tabcontent::where('business_id', $User->id)->where('tab_id', $key)->update([
                                'tab_content' => $value
                            ]);
                        } else {
                            if($value != "" || $value != null){
                                $save_tab_details = new Tabcontent();
                                $save_tab_details->business_id = $User->id;
                                $save_tab_details->tab_id = $key;
                                $save_tab_details->tab_content = $value;
                                $save_tab_details->save();
                            }
                        }
                    }
                }
                return response([
                    'success' => true,
                    'message' => 'Editors data updated successfully'
                ]);
            }
        }
    }
    # To update the business profile editors data ( function end )

    # To get business name and city for business main cover image (function start)
    public function getbusinessname(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ], 400);
        } else {

            $get_bnamecity = User::Join('profiles', 'profiles.user_id', 'users.id')
                ->leftjoin('cities', 'cities.id', 'profiles.city_id')
                ->select('users.name as business_name', 'cities.city as city_name')
                ->where('users.id', $User->id)
                ->first();
            return response(
                [
                    'success' => true,
                    'businessdata' => $get_bnamecity
                ],
                200
            );
        }
    }
    # To get business name and city for business main cover image (function end)

    // Admin multiple search of business
    public function AdminBusinessSearch(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);

        $businesses = User::query();
        $businesses->leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftJoin('businesses', 'businesses.business_id', 'users.id')
            ->leftJoin('states', 'states.id', 'profiles.state_id')
            ->leftJoin('cities', 'cities.id', 'profiles.city_id')
            ->leftJoin('categories', 'categories.id', 'businesses.cat_id')
            ->leftJoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id')
            ->leftJoin('plans', 'plans.id', 'businesses.plan_id')
            ->select(
                'users.*',
                'businesses.id as businesses_id',
                'businesses.cat_id',
                'categories.cat_name',
                'businesses.sub_cat_id',
                'subcategories.subcat_name',
                'businesses.plan_id',
                'plans.plan_description',
                'plans.plan_expiry',
                'businesses.listedby',
                'businesses.listedby_reseller_id',
                'profiles.city_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                DB::raw('DATE(users.created_at) as added_date')
            );
            if($request->reseller_id){
                $businesses->where('businesses.listedby_reseller_id', $request->reseller_id);
                $businesses->where('businesses.listedby', 1);
            }
            if($request->state_id != "") {
                $businesses->where('profiles.state_id', $request->state_id);
            }
            if($request->city_id != "") {
                $businesses->where('profiles.city_id', $request->city_id);
            }
            if($request->user_status){
                if($request->user_status == 1) {
                    $businesses->where('users.user_status','=','1');
                }else{
                    $businesses->where('users.user_status','=','0');
                }
            }
            if($request->plans_id != "") {
                $businesses->where('businesses.plan_id', '=', $request->plans_id);
            }
            if($request->subcat_id != "") {
                $businesses->where('businesses.sub_cat_id', '=', $request->subcat_id);
            }
            if($request->expired_date != "") {
                $businesses->where('added_date', '=', $request->expired_date);
            }
            $businesses->where('users.user_role', '=', '1');
            $searched_data = $businesses->get();
            for ($i=0; $i < $searched_data->count(); $i++) {
                if($searched_data[$i]->listedby==1){
                    if($searched_data[$i]->listedby_reseller_id){
                        $resellers = User::where('id', '=', $searched_data[$i]->listedby_reseller_id)->where('user_role', '=', '2')->first();
                        if($resellers!=null){
                            $searched_data[$i]['reseller_name'] = $resellers->name;
                        }else{
                            $searched_data[$i]['reseller_name'] = '';
                        }
                    }
                }else{
                    $searched_data[$i]['reseller_name'] = '';
                }
            }
        if ($request->state_id != "") {
            $businesses->where('profiles.state_id', $request->state_id);
        }
        if ($request->city_id != "") {
            $businesses->where('profiles.city_id', $request->city_id);
        }
        if ($request->user_status) {
            if ($request->user_status == 1) {
                $businesses->where('users.user_status', '=', '1');
            } else {
                $businesses->where('users.user_status', '=', '0');
            }
        }
        if ($request->plans_id != "") {
            $businesses->where('businesses.plan_id', '=', $request->plans_id);
        }
        if ($request->subcat_id != "") {
            $businesses->where('businesses.sub_cat_id', '=', $request->subcat_id);
        }
        if ($request->expired_date != "") {
            $businesses->where('added_date', '=', $request->expired_date);
        }
        $businesses->where('users.user_role', '=', '1');
        $searched_data = $businesses->get();
        for ($i = 0; $i < $searched_data->count(); $i++) {
            if ($searched_data[$i]->listedby == 1) {
                if ($searched_data[$i]->listedby_reseller_id) {
                    $resellers = User::where('id', '=', $searched_data[$i]->listedby_reseller_id)->where('user_role', '=', '2')->first();
                    if ($resellers != null) {
                        $searched_data[$i]['reseller_name'] = $resellers->name;
                    } else {
                        $searched_data[$i]['reseller_name'] = '';
                    }
                }
            } else {
                $searched_data[$i]['reseller_name'] = '';
            }
        }

        return response()->json(
            ['businesses' => $searched_data]
        );
    }

    public function singelegetbusiness(Request $request, $business_id)
    {
        $User = JWTAuth::authenticate($request->token);

        $businesses = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftJoin('businesses', 'businesses.business_id', 'users.id')
            ->leftJoin('states', 'states.id', 'profiles.state_id')
            ->leftJoin('cities', 'cities.id', 'profiles.city_id')
            ->leftJoin('categories', 'categories.id', 'businesses.cat_id')
            ->leftJoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id')
            ->leftJoin('plans', 'plans.id', 'businesses.plan_id')
            ->select(
                'users.*',
                'businesses.id as businesses_id',
                'businesses.cat_id',
                'categories.cat_name',
                'businesses.sub_cat_id',
                'subcategories.subcat_name',
                'businesses.plan_id',
                'plans.plan_description',
                'plans.plan_expiry',
                'businesses.listedby',
                'businesses.listedby_reseller_id',
                'profiles.city_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                DB::raw('DATE(users.created_at) as added_date')
            )->where('businesses.id', '=', $business_id)
            ->where('users.user_role', '=', '1')
            ->first();
        if ($businesses->listedby == 1) {
            if ($businesses->listedby_reseller_id) {
                $resellers = User::where('id', '=', $businesses->listedby_reseller_id)->where('user_role', '=', '2')->first();
                if ($resellers != null) {
                    $businesses['reseller_name'] = $resellers->name;
                } else {
                    $businesses['reseller_name'] = '';
                }
            }
        } else {
            $businesses['reseller_name'] = '';
        }
        return response()->json(
            ['businesses' => $businesses]
        );
    }

    public function businessSingleGet(Request $request, $business_id)
    {

        $User = JWTAuth::authenticate($request->token);

        $businesses = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftJoin('businesses', 'businesses.business_id', 'users.id')
            ->leftJoin('states', 'states.id', 'profiles.state_id')
            ->leftJoin('cities', 'cities.id', 'profiles.city_id')
            ->leftJoin('categories', 'categories.id', 'businesses.cat_id')
            ->leftJoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id')
            ->leftJoin('plans', 'plans.id', 'businesses.plan_id')
            ->select(
                'users.*',
                'businesses.id as businesses_id',
                'businesses.cat_id',
                'categories.cat_name',
                'businesses.sub_cat_id',
                'subcategories.subcat_name',
                'businesses.plan_id',
                'plans.plan_description',
                'plans.plan_expiry',
                'businesses.listedby',
                'businesses.listedby_reseller_id',
                'profiles.city_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                DB::raw('DATE(users.created_at) as added_date')
            )->where('users.id', '=', $business_id)
            ->where('users.user_role', '=', '1')
            ->first();
        if ($businesses->listedby == 1) {
            if ($businesses->listedby_reseller_id) {
                $resellers = User::where('id', '=', $businesses->listedby_reseller_id)->where('user_role', '=', '2')->first();
                if ($resellers != null) {
                    $businesses['reseller_name'] = $resellers->name;
                } else {
                    $businesses['reseller_name'] = '';
                }
            }
        } else {
            $businesses['reseller_name'] = '';
        }
        return response()->json(
            ['businesses' => $businesses]
        );
    }

    // state city sub-category update function start

    public function AdminBusinessesstatecityUpdate(Request $request)
    {

        $User = JWTAuth::authenticate($request->token);
        $statecitydata = Profile::find($request->profile_id);
        $statecitydata->state_id = $request->state_id;
        $statecitydata->city_id = $request->city_id;
        $statecitydata->update();

        $sub_cat = Business::find($request->business_id);
        $sub_cat->sub_cat_id = $request->subcatid;
        $sub_cat->update();

        return response()->json([
            'status' => 200,
            'message' => "State City Sub-category update successfully. ",
            // 'data' => $statecitydata
        ]);
    }

    // state city sub-category update function end

    public function AdminBusinessesUpdate(Request $request)
    {

        $User = JWTAuth::authenticate($request->token);
        $userData = User::find($request->user_id);
        $userData->name = $request->name;
        $userData->email = $request->email;
        if ($request->user_status == 1) {
            $userData->user_status = '1';
        } else {
            $userData->user_status = '0';
        }
        if ($request->business_feature == 1) {
            $userData->business_feature = 1;
        } else {
            $userData->business_feature = 0;
        }
        $userData->update();

        $businessData = Business::find($request->business_id);
        $businessData->plan_id = $request->plan_id;
        $businessData->update();
        if ($request->business_feature == true) {
            $save_reseller = new Businessfeature();
            $save_reseller->user_id = $request->user_id;
            $save_reseller->businessfeature_status = 1;
            $save_reseller->save();
        } elseif ($request->business_feature == false) {
            Businessfeature::Where('user_id', '=', $request->user_id)->delete();
        }
        return response()->json([
            'status' => 200,
            'message' => "Business update successfully. ",
            'data' => $userData
        ]);
    }
    # To make embeded code ( function start )
    public function makeEmbedCode(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('id');
            $validator = Validator::make($data, [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                // $post = Business::findOrFail($request->id);
                // Generate the embed code using the post data
                $embedCode = "<iframe src='" . route('business.show', $request->id) . "'></iframe>";
                return response([
                    'success' => true,
                    'embed_code' => $embedCode
                ]);
            }
        }
    }
    # To make embeded code ( function end )

    # To get business jobs for embeded code ( function start )
    public function getBusienssjobs($business)
    {
        $business_detail = User::where('username', $business)->first();
        $business_id = $business_detail->id;
        $businessjobs = Job::leftjoin('users', 'users.id', 'jobs.user_id')
            ->leftjoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->select('jobs.*', 'users.name', 'users.id As business_id', 'users.username As business_uname', 'cities.city', 'states.state', 'profiles.user_avatar')
            ->where('jobs.user_id', $business_id)
            ->where('users.user_status', '1')
            ->OrderBy('jobs.id', 'DESC')
            ->get();
        return response()->json(
            [
                'status' => 200,
                'message' => 'Business job Listings',
                'businessjobs' => $businessjobs,
            ]
        );
    }
    # To get business jobs for embeded code ( function end )

    # To get business hotdeals for embeded code ( function start )
    public function getBusiensshotdeals($business)
    {
        $currentDate = date('Y-m-d');
        $business_detail = User::where('username', $business)->first();
        $business_id = $business_detail->id;
        $getuser = User::Where('id', $business_id)->first();
        $businesshotdeals = Hotdeal::leftjoin('profiles', 'profiles.id', 'hotdeals.business_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('users', 'users.id', 'profiles.user_id')
            ->select(
                'hotdeals.*',
                'profiles.city_id',
                'profiles.user_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                'profiles.user_avatar',
                'profiles.address',
                'users.mobile_phone',
                'users.name',
                'users.id as business_id',
                'users.username'
            )
            ->where('users.id', '=', $getuser->id)
            ->where('hotdeals.date_from', '<=', $currentDate)
            ->where('hotdeals.date_to', '>=', $currentDate)
            ->where('hotdeals.hot_deal_status', '=', 1)
            ->OrderBy('hotdeals.id', 'DESC')
            ->get();

        foreach ($businesshotdeals as $deal) {
            $get_hotdeal_image = Hotdealimage::Where('hotdeal_id', $deal->id)->first();
            if ($get_hotdeal_image != null) {
                $deal->hotdeal_image_url = $get_hotdeal_image->hotdeal_img_url;
            } else {
                $deal->hotdeal_image_url = '';
            }
        }
        return response()->json(
            [
                'status' => 200,
                'message' => 'Business Hot deals Listings',
                'businesshotdeals' => $businesshotdeals,
            ]
        );
    }
    # To get business hotdeals for embeded code ( function end )

    # To get business hotdeals for embeded code ( function start )
    public function getBusienssSales($business)
    {
        $currentDate = date('Y-m-d');
        $business_detail = User::where('username', $business)->first();
        $business_id = $business_detail->id;
        $businesssales = Sale::leftjoin('users', 'users.id', '=', 'sales.user_id')
            ->leftjoin('profiles', 'profiles.user_id', '=', 'sales.user_id')
            ->leftjoin('cities', 'cities.id', '=', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'cities.state_id')
            ->Select('sales.*', 'cities.city', 'states.state', 'users.id as business_id', 'users.name', 'users.username', 'users.mobile_phone')
            ->Where('sales.saledate_from', '<=', $currentDate)
            ->Where('sales.saledate_to', '>=', $currentDate)
            ->Where('sales.sale_status', '=', 1)
            ->Where('sales.user_id', '=', $business_id)
            ->OrderBy('sales.id', 'DESC')
            ->get();
        return response()->json(
            [
                'status' => 200,
                'message' => 'Business Sales Listings',
                'businesssales' => $businesssales,
            ]
        );
    }
    # To get business hotdeals for embeded code ( function end )

    # To get business reviews for embeded code ( function start )
    public function getBusienssReviews($business)
    {
        $business_detail = User::where('username', $business)->first();
        $business_id = $business_detail->id;
        $businessreviews = Review::leftjoin('users', 'users.id', '=', 'reviews.review_user_id')
            ->leftjoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->Select(
                'reviews.*',
                'users.name',
                'profiles.user_avatar',
                DB::raw('DATE(reviews.created_at) as added_date')
            )
            ->Where('users.user_status', '=', '1')
            ->Where('reviews.review_business_id', '=', $business_id)
            ->get();
        return response()->json(
            [
                'status' => 200,
                'message' => 'Business Reviews Listings',
                'businessreviews' => $businessreviews,
            ]
        );
    }
    # To get business reviews for embeded code ( function end )




    public function toggleFeaturedBusiness(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'businessfeature_status' => 'required|in:0,1',
            'cities' => 'nullable|array',
            'categories' => 'nullable|array',
        ]);

        $feature = Businessfeature::updateOrCreate(
            ['user_id' => $request->user_id],
            ['businessfeature_status' => $request->businessfeature_status]
        );

        $feature->cities()->sync($request->cities ?? []);
        $feature->categories()->sync($request->categories ?? []);

        return response()->json([
            'message' => 'Featured business status updated successfully.',
            'status' => 200
        ]);
    }

}
