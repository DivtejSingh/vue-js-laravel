<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\City;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Profile;
use App\Models\Wishlist;
use App\Models\Review;
use App\Models\Hotdeal;
use App\Models\Sale;
use App\Models\Job;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Psy\Readline\Hoa\Console;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{

    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'email', 'password', 'user_role', 'user_status', 'mobile_phone', 'city_id');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:15',
            'user_role' => 'required',
            'user_status' => 'required',
            // 'username' => 'required|string|unique:users|alpha_dash',
            'mobile_phone' => 'required|unique:users',
            'city_id' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Request is valid, create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_role' => $request->user_role,
            'user_status' => $request->user_status,
            // 'username' => $request->username,
            'mobile_phone' => $request->mobile_phone,
        ]);

        if($user){
            // if(empty($request->state_id)){ // get state_id from city table for use in profile table
            //     $city = City::find($request->city_id);
            //     $state_id = $city->state_id;
            // }else{
            //     $state_id = $request->state_id;
            // }

            // profile table
            $profile = Profile::create([
                'user_id' => $user->id,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'profile_status' => 1,
            ]);


            //User created, return success response
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => ['user'=>$user, 'profile'=>$profile]
            ], 200);
        }
    }


    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        //valid credential
        $validator = Validator::make($credentials, [
                'email' => 'required|email',
                'password' => 'required|string|min:6|max:50',
            ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $user = User::Where('email', $request->email)->first();
        if( $user == null ){
            return response([
                'success' => false,
                'message' => 'Login credentials are invalid.',
            ],400);
        } else {
            if($user->user_status == '0'){
                return response([
                    'success' => false,
                    'message' => 'Your account has been deactivated.',
                ],400);
            }
        }
        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Login credentials are invalid, or you are not a user.',
                    ], 400);
            }
        } catch (JWTException $e) {
            // return $credentials;
            return response()->json(
                [
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }
        $user = auth()->user();
        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
            'user'=> $user,
        ],200);
    }

    // public function logout(Request $request)
    // {
    //     //valid credential
    //     $validator = Validator::make($request->only('token'), [
    //         'token' => 'required'
    //     ]);

    //     //Send failed response if request is not valid
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->messages()], 200);
    //     }

    //     //Request is validated, do logout
    //     try {
    //         JWTAuth::invalidate($request->token);

    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'message' => 'User has been logged out'
    //             ]);
    //     } catch (JWTException $exception) {
    //         return response()->json(
    //             [
    //                 'success' => false,
    //                 'message' => 'Sorry, user cannot be logged out'
    //             ], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }
    public function logout(Request $request)
    {
        // Retrieve token from header if available
        $token = $request->header('Authorization');
    
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided'
            ], 400);
        }
    
        try {
            // Invalidate token
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out successfully'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed. Please try again.'
            ], 500);
        }
    }
    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

    public function userVerify(Request $request)
    {
        if($request->email){
            $verified_user = User::where('email', $request->email)->first();
            $type = "Email";
        }
        if($request->username){
            $verified_user = User::where('username', $request->username)->first();
            $type = "Username";
        }
        if($request->mobile_phone){
            $verified_user = User::where('mobile_phone', $request->mobile_phone)->first();
            $type = "Mobile Number";
        }

        if(is_null($verified_user)){
            return response()->json([
                'status' => 200,
                'message' => $type." Not Exist"
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => $type." Already Exist",
                // 'data' => $verified_user
            ]);
        }
    }

//     public function forgetPassword(Request $request){

//         $user = User::Where('email', $request->email)->first();
// //        $forgetcode = mt_rand(100000, 999999);
//         if ($user == null) {
//             return response()->json(['error' => 'Email not exist.'], 400);
//         }

//         $getadmin_email = User::Where('user_role', '=', '4')->first();
//         $admin_email =   $getadmin_email->email;
//         Mail::send(
//             'forget_email',
//             array(
//                 'forget_code'=> $request->email,
//             ),
//             function ($message) use ($admin_email, $request) {
//                 $message->from($admin_email);
//                 $message->to($request->email);
//             }
//         );

//         return response()->json(
//             [
//                 'status' => '200',
//                 'message' => 'Reset password link sent to your email address.'
//             ]);
//     }

public function forgotPassword(Request $request)
{
    $request->validate(['email'=>'required|email']);
    $status = Password::sendResetLink($request->only('email'));

    return $status == Password::RESET_LINK_SENT
         ? response()->json(['message'=>'Link sent'])
         : response()->json(['error'=>'Failed'], 400);
}

    public function resetPassword(Request $request)
    {
        $user = User::Where('email', $request->email)->first();
        $user_id = $user->id;
        $data = $request->only('password', 'email');
        $validator = Validator::make($data, [
            'email' => 'required',
            'password' => 'required|string|max:15',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{
            $update_password = User::find($user_id);
            $password = Hash::make($request->password);
            $update_password->password = $password;
            $update_password->update();

            return response()->json(['status' => '200', 'message' => 'Password has been reset.']);
        }
    }

    # To get user's wishlist (function start)
    public function Wishlists( Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }else{
            $get_wishlists = Wishlist::leftjoin('users','wishlists.business_id','=','users.id')
                            ->leftjoin('profiles','profiles.user_id','=','users.id')
                            ->select('wishlists.*','users.name','profiles.user_avatar')
                            ->where("wishlists.user_id",$User->id)
                            ->get();
            return response([
                'success' => true,
                'wishlists' => $get_wishlists
            ]);
        }
    }
    # To get user's wishlist (function end)
    function businessWishlistsGet(Request $request) {
        $user = JWTAuth::authenticate($request->token);

        $get_wishlists = Wishlist::leftjoin('users', 'users.id', 'wishlists.business_id')
            ->leftjoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
            ->leftjoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'cities.state_id')
            ->leftjoin('users as usb', 'usb.id', 'wishlists.business_id')
            ->leftjoin('hotdeals', 'hotdeals.id', 'wishlists.services_id')
            ->leftjoin('sales', 'sales.id', 'wishlists.services_id')
            ->leftjoin('jobs', 'jobs.id', 'wishlists.services_id')
            ->leftjoin('jobcategories', 'jobcategories.id', 'jobs.job_cat_id')
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
                    'hotdeals.hotdeal_slug',

                    'sales.id as sale_id',
                    'sales.sale_title',
                    'sales.sale_description',
                    'sales.normal_price',
                    'sales.sale_price',
                    'sales.saledate_from',
                    'sales.saledate_to',
                    'sales.sale_imageurl',
                    'sales.sale_slug',

                    'usb.id as buser_id',
                    'usb.name as buser_name',
                    'usb.username as buser_username',

                    'subcategories.subcat_name',

                    'jobs.id as job_id',
                    'jobs.job_title',
                    'jobs.job_description',
                    'jobs.salary_from',
                    'jobs.min_exp',
                    'jobs.job_slug',
                    'jobcategories.job_cat_name',

                    'videos.id as video_id',
                    'videos.video_title',
                    'videos.video_description',
                    'videos.video_url',
                    'videos.youtube_id',
                    'videos.video_slug',

                    'users.name',
                    'users.username',
                    'profiles.city_id',
                    'cities.city',
                    'states.state')
            ->where(["wishlists.user_id" => $user->id])
            ->get();

        return response([
            'success' => true,
            'wishlists' => $get_wishlists
        ]);
    }

    function businessWishlistsGetbyadmin(Request $request) {
        $user = $request->userId;

        $get_wishlists = Wishlist::leftjoin('users', 'users.id', 'wishlists.business_id')
            ->leftjoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
            ->leftjoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'cities.state_id')
            ->leftjoin('users as usb', 'usb.id', 'wishlists.business_id')
            ->leftjoin('hotdeals', 'hotdeals.id', 'wishlists.services_id')
            ->leftjoin('sales', 'sales.id', 'wishlists.services_id')
            ->leftjoin('jobs', 'jobs.id', 'wishlists.services_id')
            ->leftjoin('jobcategories', 'jobcategories.id', 'jobs.job_cat_id')
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
                    'hotdeals.hotdeal_slug',

                    'sales.id as sale_id',
                    'sales.sale_title',
                    'sales.sale_description',
                    'sales.normal_price',
                    'sales.sale_price',
                    'sales.saledate_from',
                    'sales.saledate_to',
                    'sales.sale_imageurl',
                    'sales.sale_slug',

                    'usb.id as buser_id',
                    'usb.name as buser_name',
                    'usb.username as buser_username',

                    'subcategories.subcat_name',

                    'jobs.id as job_id',
                    'jobs.job_title',
                    'jobs.job_description',
                    'jobs.salary_from',
                    'jobs.min_exp',
                    'jobs.job_slug',
                    'jobcategories.job_cat_name',

                    'videos.id as video_id',
                    'videos.video_title',
                    'videos.video_description',
                    'videos.video_url',
                    'videos.youtube_id',
                    'videos.video_slug',

                    'users.name',
                    'users.username',
                    'profiles.city_id',
                    'cities.city',
                    'states.state')
            ->where(["wishlists.user_id" => $user])
            ->get();
        return response([
            'success' => true,
            'wishlists' => $get_wishlists
        ]);
    }
    public function businessWishlistsDelete(Request $request, $wishlist_id){
        $user = JWTAuth::authenticate($request->token);
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
    // public function businessReviewsGet(Request $request){
    //     $user = JWTAuth::authenticate($request->token);
    //     $reviews = Review::leftjoin('users', 'users.id', 'reviews.review_user_id')
    //         ->leftjoin('profiles','profiles.user_id', 'users.id')
    //         ->select('users.id', 'users.name', 'profiles.user_avatar', 'reviews.*',
    //             DB::raw('DATE(reviews.created_at) as added_date'),
    //         )
    //         ->orderBy('reviews.rating')
    //         ->orderBy('reviews.review_user_id')
    //         ->where("reviews.review_business_id", $user->id)
    //         ->get();

    //     return response([
    //         'success' => true,
    //         'message' => "Business reviews data with rating",
    //         'reviews' => $reviews
    //     ]);

    // }
    public function businessReviewsGet(Request $request)
{
    // 1) Grab & authenticate the token from the Authorization header
    $user = JWTAuth::parseToken()->authenticate();

    // 2) Build your query
    $reviews = Review::query()
        ->where('review_business_id', $user->id)
        ->join('users', 'reviews.review_user_id', '=', 'users.id')
        ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
        ->select([
            'users.id as reviewer_id',
            'users.name',
            'profiles.user_avatar',
            'reviews.rating',
            'reviews.review_text',
            DB::raw('DATE(reviews.created_at) as added_date'),
        ])
        ->orderByDesc('reviews.rating')            // show highest ratings first?
        ->orderBy('reviews.review_user_id')        // then by user
        ->get();

    // 3) Return JSON
    return response()->json([
        'success' => true,
        'message' => 'Business reviews data with rating',
        'reviews' => $reviews,
    ]);
}
    public function businessReviewsByadmin(Request $request)
    {
        $userId = $request->userId;
        $reviews = Review::leftjoin('users', 'users.id', 'reviews.review_user_id')
            ->leftjoin('profiles', 'profiles.user_id', 'users.id')
            ->select('users.id', 'users.name', 'profiles.user_avatar', 'reviews.*',
                DB::raw('DATE(reviews.created_at) as added_date'),
            )
            ->orderBy('reviews.rating', 'desc') 
            ->orderBy('reviews.review_user_id', 'asc')
            ->where('reviews.review_user_id', $userId)
            ->get();

        return response([
            'success' => true,
            'message' => "Reviews fetched successfully.",
            'reviews' => $reviews
        ]);
    }

    # To get all the resellers by city ( function start )
    public function getResellersByCity( Request $request ){
        $get_resellers = Profile::where('city_id',$request->city_id)
                        ->leftjoin('users','users.id','=','profiles.user_id')
                        ->where('users.user_status','1')->where('users.user_role','2')
                        ->select('profiles.user_id','profiles.address','users.email','users.name','users.mobile_phone')->get();
        return response([
            'success' => true,
            'resellers' => $get_resellers
        ]);
    }

    public function getResellersuserstatus(Request $request)
    {
 
        // Find the user by ID
        $user = User::find($request->id);
   
        if ($user) {
            // Ensure user_status is an integer (casting it)
            $user_status = $request->user_status;
            // Update the user's status
            $user->update([
                'user_status' => $user_status
            ]);
    
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully.',
            ]);
        }
    
        // Return error if user is not found
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 404);
    }
    public function getBusinessuserstatus(Request $request)
    {
        // Step 1: Find the business record by the provided ID
        $business = Business::find($request->id);
    
        if ($business) {
            // Step 2: Find the user using the business_id in the businesses table
            $user = User::find($business->business_id); // Correct mapping: business_id → users.id
    
            if ($user) {
                // Step 3: Update the user's status
                $user_status = $request->user_status;
    
                $user->update([
                    'user_status' => $user_status
                ]);
    
                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'User status updated successfully.',
                ]);
            }
    
            // If the user was not found
            return response()->json([
                'success' => false,
                'message' => 'User not found for the given business.',
            ], 404);
        }
    
        // If the business was not found
        return response()->json([
            'success' => false,
            'message' => 'Business not found.',
        ], 404);
    }
    

    # To get all the resellers by city ( function end )

    // all reports ( function start )

    // public function allreports()
    // {
    //     $deals = Hotdeal::join('profiles', 'hotdeals.business_id', '=', 'profiles.user_id')
    //     ->join('users', 'profiles.user_id', '=', 'users.id')
    //     ->join('cities', 'cities.id', '=', 'profiles.city_id')
    //     ->join('states', 'states.id', '=', 'profiles.state_id')
    //     ->join('countries', 'countries.id', '=', 'profiles.country_id')
    //     ->join('businesses', 'businesses.business_id', '=', 'profiles.user_id')
    //     ->join('subcategories', 'subcategories.id', '=', 'businesses.sub_cat_id')
    //     ->select(
    //         'hotdeals.*',
    //         'profiles.country_id','countries.country',
    //         'profiles.state_id','states.state',
    //         'profiles.city_id','cities.city',
    //         'businesses.sub_cat_id','subcategories.subcat_name',
    //         'users.name'
    //     )
    //     ->where('hotdeals.hot_deal_status', 1) // ✅ Only active
    //     ->whereNull('hotdeals.deleted_at')     // ✅ Exclude soft-deleted deals
    //     ->get();

    //     $sales = Sale::join('profiles','profiles.user_id','=','sales.user_id',)
    //         ->join('users','sales.user_id','users.id')
    //         ->join('cities', 'cities.id', '=', 'profiles.city_id')
    //         ->join('states', 'states.id', '=', 'profiles.state_id')
    //         ->join('countries', 'countries.id', '=', 'profiles.country_id')
    //         ->join('businesses', 'businesses.business_id', '=', 'profiles.user_id')
    //         ->join('subcategories', 'subcategories.id', '=', 'businesses.sub_cat_id')
    //         ->select('sales.*','profiles.country_id','countries.country','profiles.state_id','states.state',
    //             'profiles.city_id','cities.city','businesses.sub_cat_id','subcategories.subcat_name','users.name')
    //         ->get();

    //     $jobs = Job::join('jobcategories','jobs.job_cat_id','jobcategories.id')
    //         ->join('profiles','profiles.user_id','=','jobs.user_id')
    //         ->join('users','users.id','=','jobs.user_id',)
    //         ->join('cities', 'cities.id', '=', 'profiles.city_id')
    //         ->join('states', 'states.id', '=', 'profiles.state_id')
    //         ->join('countries', 'countries.id', '=', 'profiles.country_id')
    //         ->select('jobs.*','profiles.country_id','countries.country','profiles.state_id','states.state',
    //             'profiles.city_id as bcity_id','cities.city','jobcategories.job_cat_name','users.name')
    //         ->get();

    //     return response()->json([
    //         'deals'=>$deals,
    //         'sales'=>$sales,
    //         'jobs'=>$jobs
    //     ],200);
    // }


    public function allreports(Request $request)
{
    $from = $request->query('from_date');
    $to = $request->query('to_date');

    $deals = Hotdeal::join('profiles', 'hotdeals.business_id', '=', 'profiles.id')
        ->join('users', 'profiles.user_id', '=', 'users.id')
        ->join('cities', 'cities.id', '=', 'profiles.city_id')
        ->join('states', 'states.id', '=', 'profiles.state_id')
        ->join('countries', 'countries.id', '=', 'profiles.country_id')
        ->join('businesses', 'businesses.business_id', '=', 'profiles.user_id')
        ->join('subcategories', 'subcategories.id', '=', 'businesses.sub_cat_id')
        ->select(
            'hotdeals.*',
            'profiles.country_id','countries.country',
            'profiles.state_id','states.state',
            'profiles.city_id','cities.city',
            'businesses.sub_cat_id','subcategories.subcat_name',
            'users.name'
        )
        ->where('hotdeals.hot_deal_status', 1)
        ->whereNull('hotdeals.deleted_at');

    if ($from && $to) {
        $deals->whereBetween(DB::raw('DATE(hotdeals.created_at)'), [$from, $to]);
    }

    $deals = $deals->get();

    $sales = Sale::join('profiles','profiles.user_id','=','sales.user_id')
        ->join('users','sales.user_id','users.id')
        ->join('cities', 'cities.id', '=', 'profiles.city_id')
        ->join('states', 'states.id', '=', 'profiles.state_id')
        ->join('countries', 'countries.id', '=', 'profiles.country_id')
        ->join('businesses', 'businesses.business_id', '=', 'profiles.user_id')
        ->join('subcategories', 'subcategories.id', '=', 'businesses.sub_cat_id')
        ->select('sales.*','profiles.country_id','countries.country','profiles.state_id','states.state',
            'profiles.city_id','cities.city','businesses.sub_cat_id','subcategories.subcat_name','users.name');

    if ($from && $to) {
        $sales->whereBetween(DB::raw('DATE(sales.created_at)'), [$from, $to]);
    }

    $sales = $sales->get();

    $jobs = Job::join('jobcategories','jobs.job_cat_id','jobcategories.id')
        ->join('profiles','profiles.user_id','=','jobs.user_id')
        ->join('users','users.id','=','jobs.user_id')
        ->join('cities', 'cities.id', '=', 'profiles.city_id')
        ->join('states', 'states.id', '=', 'profiles.state_id')
        ->join('countries', 'countries.id', '=', 'profiles.country_id')
        ->select('jobs.*','profiles.country_id','countries.country','profiles.state_id','states.state',
            'profiles.city_id as bcity_id','cities.city','jobcategories.job_cat_name','users.name');

    if ($from && $to) {
        $jobs->whereBetween(DB::raw('DATE(jobs.created_at)'), [$from, $to]);
    }

    $jobs = $jobs->get();

    return response()->json([
        'deals' => $deals,
        'sales' => $sales,
        'jobs'  => $jobs
    ], 200);
}
     // all reports ( function end )
}
