<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Business;
use App\Models\User;
use App\Models\Profile;
use App\Models\City;
use App\Models\Subcategory;
use App\Models\Review;
use App\Models\Hotdeal;
use App\Models\Hotdealimage;
use App\Models\Plan;
use App\Models\Sale;
use App\Models\Saleimage;
use App\Models\Job;
use App\Models\Jobtype;
use App\Models\Qualification;
use App\Models\Wishlist;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Intervention\Image\Facades\Image;
use File;

class UserController extends Controller
{
    public function index()
    {
        $user = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->select('users.*', 'cities.city',DB::raw('Date(users.created_at) as created_date'))
            ->where('users.user_role','0')
            // ->where('users.user_status','1')
            ->get();
        return response()->json(
            ['user' => $user]
        );
    }
    public function getuserdata(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);
        $user_id = $user->id;
        $user = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->select('users.*','profiles.*', 'cities.city')
            ->where('users.id', $user_id)
            ->first();
        return response()->json(
            ['user' => $user]
        );
    }
    public function adminUserUpdate(Request $req)
    {
        $user = JWTAuth::authenticate($req->token);
        $validator = Validator::make($req->all(), [
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email',
           
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        try {
            $user = User::find($req->user_id);
            $user->name = $req->name;
            $user->email = $req->email;
            // $user->username = $req->username;
            if ($req->user_status == "true") {
                $user->user_status = '1';
            } elseif($req->user_status == "1") {
                $user->user_status = '1';
            }else{
                $user->user_status = '0';
            }
            $user->update();
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data' => $user
            ], 200);
        }catch (JWTexception $e) {// handle the exception
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ]);
        }
    }
    public function subAdmins()
    {
        $subadmins = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->where('user_role', '3')
            ->select('users.*', 'profiles.city_id', 'cities.city',DB::raw('Date(users.created_at) as created_date'))
            ->get();
        return response()->json(
            ['subadmins' => $subadmins]
        );
    }
    public function AdminAddsubAdmin(Request $request){
        $user = JWTAuth::authenticate($request->token);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'mobile_phone' => 'required|integer',
            'city_id' => 'required|integer',
            'username' => 'required|string|unique:users',
            'password' => 'required',
            'user_role' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->messages()], 200);
        }else{
            $subAdmin = new User();
                $subAdmin->name = $request->name;
                $subAdmin->email = $request->email;
                $subAdmin->mobile_phone = $request->mobile_phone;
                $subAdmin->username = $request->username;
                $subAdmin->password = Hash::make($request->password);
                $subAdmin->user_role = $request->user_role;
                $subAdmin->user_status = $request->user_status;
                $sabAdmin_save = $subAdmin->save();

            $city = City::where('id', $request->city_id)->first();
            $profile = new Profile();
                $profile->user_id = $subAdmin->id;
                $profile->state_id = $city->state_id;
                $profile->city_id = $request->city_id;
                $profile->profile_status = '1';
                $profile_save = $profile->save();

            if($sabAdmin_save==true && $profile_save==true) {
                return response()->json([
                    'status' => 200,
                    'message' => "Sub-admin added successfully.",
                    'data' => $subAdmin.$profile
                ]);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => "Something went worng."
                ]);
            }
        }
    }
    public function subAdminUpdate(Request $req)
    {
        $user = JWTAuth::authenticate($req->token);
        $validator = Validator::make($req->all(), [
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile_phone' => 'required|integer',
            'city_id' => 'required|integer',
            'username' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->messages()], 200);
        }
        try {
            $user = User::find($req->user_id);
            $user->name = $req->name;
            $user->email = $req->email;
            $user->username = $req->username;
            $user->mobile_phone  = $req->mobile_phone;
            if ($req->user_status == "true") {
                $user->user_status = '1';
            } elseif($req->user_status == "1") {
                $user->user_status = '1';
            }else{
                $user->user_status = '0';
            }
            $user->update();
            $city = City::where('id', $req->city_id)->first();
            $profile = Profile::where('user_id', $req->user_id)->first();
            $profile->state_id = $city->state_id;
            $profile->city_id = $req->city_id;
            $profile->profile_status = '1';
            $profile->update();

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Sub-Admin updated successfully.',
                'data' => ['user'=>$user, 'user_profile'=>$profile]
            ]);

        }catch (JWTexception $e) {// handle the exception
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ]);
        }
    }
    public function AdminDeletesubAdmin(Request $req, $user_id){
        $user = JWTAuth::authenticate($req->token);
        $user = User::where('id', $user_id)->first();
        $profile = Profile::where('user_id', $user_id)->first();

        if($profile->user_avatar!=null){
            if(Storage::disk('s3')->exists($profile->user_avatar)){
                Storage::disk('s3')->delete($profile->user_avatar);
            }
            if (File::exists(public_path().$profile->user_avatar)) {
                unlink(public_path().$profile->user_avatar);
            }
        }
        if($user->delete()==true && $profile->delete()==true) {
            return response()->json([
                'status' => 200,
                'message' => "Sub-admin deleted successfully.",
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => "Something went worng."
            ]);
        }
    }
    public function resellers()
    {
        $resellers = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
                ->leftjoin('resellers', 'resellers.user_id', 'profiles.user_id')
                ->leftjoin('cities', 'cities.id', 'profiles.city_id')
                ->leftjoin('states', 'states.id', 'profiles.state_id')
                ->leftjoin('professions', 'professions.id', 'resellers.profession_id')
                ->with('allskill')
                // ->leftjoin("skills", DB::raw("FIND_IN_SET(skills.id,resellers.skills_id)"),'resellers.skills_id')
                ->leftjoin('plans', 'plans.id', 'resellers.plan_id')
                ->where('users.user_role', '2')
                ->select('users.*', 'profiles.user_avatar', 'profiles.city_id', 'cities.city', 'profiles.state_id', 'states.state',
                            'resellers.reseller_dob',
                            'resellers.reseller_gender',
                            'resellers.profession_id',
                            'professions.profession',
                            'resellers.skills_id',
                            'resellers.plan_id',
                            'plans.plan_description',
                            DB::raw('DATE(users.created_at) as added_date')
                        )
                ->orderBy('users.id', 'DESC')
                ->get();
        return response()->json(
            ['resellers' => $resellers]
        );
    }
    public function businesses()
    {
        // $businesses = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
        //     ->leftJoin('businesses', 'businesses.business_id', 'users.id')
        //     ->leftjoin("subcategories", \DB::raw("FIND_IN_SET(subcategories.id, businesses.sub_cat_id)"),">",\DB::raw("'0'"))
        //     ->leftJoin('states', 'states.id', 'profiles.state_id')
        //     ->leftJoin('cities', 'cities.id', 'profiles.city_id')
        //     ->leftJoin('categories', 'categories.id', 'businesses.cat_id')
        //     // ->leftJoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id')
        //     ->leftJoin('plans', 'plans.id', 'businesses.plan_id')
        //     ->select('users.*', 'businesses.id as businesses_id',
        //         'businesses.cat_id', 'categories.cat_name',
        //         'businesses.sub_cat_id', \DB::raw("GROUP_CONCAT(subcategories.subcat_name) as subcatsname"),
        //         'businesses.plan_id', 'plans.plan_description', 'plans.plan_expiry',
        //         'businesses.listedby',
        //         'businesses.listedby_reseller_id',
        //         'profiles.city_id', 'cities.city', 'profiles.state_id',
        //         'states.state', DB::raw('DATE(users.created_at) as added_date'))
        //     ->where('users.user_role', '=', '1')
        //     ->get();
        $businesses = Business::leftjoin("subcategories",\DB::raw("FIND_IN_SET(subcategories.id,businesses.sub_cat_id)"),">",\DB::raw("'0'"))
            ->leftjoin('users', 'businesses.business_id', '=', 'users.id')
            ->leftjoin('profiles','businesses.business_id','=','profiles.user_id')
            ->leftJoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('cities', 'profiles.city_id', '=', 'cities.id')
            ->leftJoin('plans', 'plans.id', 'businesses.plan_id')
            ->select(
                "businesses.*",
                \DB::raw("GROUP_CONCAT(subcategories.subcat_name) as subcatsname"),
                "users.name",
                "users.email",
                "users.mobile_phone",
                "users.user_status",
                'profiles.id as profile_id',
                'profiles.state_id',
                'states.state',
                "cities.id as city_id",
                "cities.city",
                'businesses.plan_id', 'plans.plan_description', 'plans.plan_expiry',
                DB::raw('DATE(users.created_at) as added_date')
            )
            ->groupBy("businesses.id")
            ->orderBy('added_date','DESC')
            ->where('users.user_role', '=', '1')
            ->get();
            for ($i=0; $i < $businesses->count(); $i++) {
                if($businesses[$i]->listedby==1){
                    if($businesses[$i]->listedby_reseller_id){
                        $resellers = User::where('id', '=', $businesses[$i]->listedby_reseller_id)->where('user_role', '=', '2')->first();
                        if($resellers!=null){
                            $businesses[$i]['reseller_name'] = $resellers->name;
                        }else{
                            $businesses[$i]['reseller_name'] = '';
                        }
                    }
                }else{
                    $businesses[$i]['reseller_name'] = '';
                }
            }
        return response()->json(
            ['businesses' => $businesses]
        );
    }
    public function businessesbyadmin(Request $request)
    {
        $businesses = Business::leftjoin("subcategories",\DB::raw("FIND_IN_SET(subcategories.id,businesses.sub_cat_id)"),">",\DB::raw("'0'"))
            ->leftjoin('users', 'businesses.business_id', '=', 'users.id')
            ->leftjoin('profiles','businesses.business_id','=','profiles.user_id')
            ->leftJoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('cities', 'profiles.city_id', '=', 'cities.id')
            ->leftJoin('plans', 'plans.id', 'businesses.plan_id')
            ->where('businesses.listedby_reseller_id',$request->id)
            ->where('businesses.listedby',1)
            ->select(
                "businesses.*",
                \DB::raw("GROUP_CONCAT(subcategories.subcat_name) as subcatsname"),
                "users.name",
                "users.email",
                "users.mobile_phone",
                "users.user_status",
                'profiles.id as profile_id',
                'profiles.state_id',
                'states.state',
                "cities.id as city_id",
                "cities.city",
                'businesses.plan_id', 'plans.plan_description', 'plans.plan_expiry',
                DB::raw('DATE(users.created_at) as added_date')
            )
            ->groupBy("businesses.id")
            ->orderBy('added_date','DESC')
            ->where('users.user_role', '=', '1')
            ->get();
            for ($i=0; $i < $businesses->count(); $i++) {
                if($businesses[$i]->listedby==1){
                    if($businesses[$i]->listedby_reseller_id){
                        $resellers = User::where('id', '=', $businesses[$i]->listedby_reseller_id)->where('user_role', '=', '2')->first();
                        if($resellers!=null){
                            $businesses[$i]['reseller_name'] = $resellers->name;
                        }else{
                            $businesses[$i]['reseller_name'] = '';
                        }
                    }
                }else{
                    $businesses[$i]['reseller_name'] = '';
                }
            }
        return response()->json(
            ['businesses' => $businesses]
        );
    }
    public function businessesUpdate(Request $request)
    {
        $profile = new Profile();
            $profile->user_id = $request->id;
            $city = City::find($request->city_id);
            $profile->state_id = $city->state_id;
            $profile->city_id = $request->city_id;

        $business = new Business();
            $business->business_id = $request->id;
            $subcategory = Subcategory::find($request->category_id);
            $business->cat_id = $subcategory->cat_id;
            $business->sub_cat_id = $request->category_id;
            $business->listedby = 0;
            $business->plan_id = 1;

        $save_business = $profile->save() && $business->save();

        if ($save_business==true) {
            return response()->json([
                'status' => 200,
                'message' => "Profile & Businesses add successfully.",
                'data' => $profile
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => "Something went worng."
            ]);
        }
    }
    public function businesstHotDealsGet(Request $request){
        $user = JWTAuth::authenticate($request->token);
        $userData = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('plans', 'plans.id', 'businesses.plan_id')
            ->select('users.id as user_id',
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
            ->where('users.id', $user->id)
            ->first();

        $hotDeals = Hotdeal::with('hotdealsImages')
            ->Join('profiles', 'profiles.id', 'hotdeals.business_id')
            ->join('cities', 'cities.id', 'profiles.city_id')
            ->join('states', 'states.id', 'profiles.state_id')
            ->select('hotdeals.id', 'profiles.id as profile_id', 'hotdeals.hot_deal_title', 'hotdeals.hot_deal_description',
                    'hotdeals.price_from','hotdeals.hotdeal_slug','hotdeals.price_to', 'hotdeals.date_from', 'hotdeals.date_to', 'hotdeals.hot_deal_status',
                    'profiles.user_avatar', 'profiles.address', 'profiles.city_id','cities.city', 'profiles.state_id', 'states.state')
            ->withCount('hotdealsImages')
            ->where('hotdeals.business_id', $userData->profile_id)
            ->orderBy('hotdeals.id', 'DESC')
            ->get();

        return response()->json([
                'status' => 200,
                'message' => "Busines hot deals with image.",
                'user' => $userData,
                'hotDeals' => $hotDeals,
        ]);
    }

    # by admin business deals get function start
    public function businesstHotDealsGetbyAdmin(Request $request){
        $user = $request->user_id;
        $userData = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('plans', 'plans.id', 'businesses.plan_id')
            ->select('users.id as user_id',
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
        $hotDeals = Hotdeal::with('hotdealsImages')
            ->Join('profiles', 'profiles.id', 'hotdeals.business_id')
            ->join('cities', 'cities.id', 'profiles.city_id')
            ->join('states', 'states.id', 'profiles.state_id')
            ->select('hotdeals.id', 'profiles.id as profile_id', 'hotdeals.hot_deal_title', 'hotdeals.hot_deal_description',
                    'hotdeals.price_from','hotdeals.hotdeal_slug','hotdeals.price_to', 'hotdeals.date_from', 'hotdeals.date_to', 'hotdeals.hot_deal_status',
                    'profiles.user_avatar', 'profiles.address', 'profiles.city_id','cities.city', 'profiles.state_id', 'states.state')
            ->withCount('hotdealsImages')
            ->where('hotdeals.business_id', $userData->profile_id)
            ->orderBy('hotdeals.id', 'DESC')
            ->get();

        return response()->json([
                'status' => 200,
                'message' => "Busines hot deals with image.",
                'user' => $userData,
                'hotDeals' => $hotDeals,
        ]);
    }
    # by admin business deals get function start

    public function businessesHotdealsAdd(Request $request){
        $user = JWTAuth::authenticate($request->token);
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
                                    $real_name = 'hotdeal'.hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                    $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                                $real_name = 'hotdeal'.hexdec(rand(11111,99999)).'.'."png";
                                $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                                    $real_name = 'hotdeal'.hexdec(rand(11111,99999)).'.'."png";
                                    $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                            $path = public_path().$destinationPath;
                            if($countImg <= $userPlan->images){
                                $real_name = 'hotdeal'.hexdec(rand(11111,99999)).'.'."png";
                                $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                    if(Storage::disk('s3')->exists($hotDealImages->hotdeal_img_url)){
                        Storage::disk('s3')->delete($hotDealImages->hotdeal_img_url);
                    }
                    $file_path = public_path().$hotDealImages->hotdeal_img_url;
                    if (File::exists($file_path)) {
                        unlink($file_path);
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

    #add business hot deals by admin function start
    public function businessesHotdealsAddbyAdmin(Request $request){
        $user = $request->user_id;
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
                                    $real_name = 'hotdeal'.hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                    $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                                $real_name = 'hotdeal'.hexdec(rand(11111,99999)).'.'."png";
                                $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                                    $real_name = 'hotdeal'.hexdec(rand(11111,99999)).'.'."png";
                                    $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                            $path = public_path().$destinationPath;
                            if($countImg <= $userPlan->images){
                                $real_name = 'hotdeal'.hexdec(rand(11111,99999)).'.'."png";
                                $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                    if(Storage::disk('s3')->exists($hotDealImages->hotdeal_img_url)){
                        Storage::disk('s3')->delete($hotDealImages->hotdeal_img_url);
                    }
                    $file_path = public_path().$hotDealImages->hotdeal_img_url;
                    if (File::exists($file_path)) {
                        unlink($file_path);
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
    #add business hot deals by admin function end
    public function businessesSalesGet(Request $request) {
        $user = JWTAuth::authenticate($request->token);
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
            ->where('users.id', $user->id)
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
                        'sales.saledate_from',
                        'sales.saledate_to',
                        'sales.sale_status',
                        'sales.sale_slug',
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

    public function businessesSalesGetbyAdmin(Request $request) {
    $user = $request->user_id;
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
                    'sales.saledate_from',
                    'sales.saledate_to',
                    'sales.sale_status',
                    'sales.sale_slug',
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

    public function businessesSalesSubmit(Request $request){
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
                    $real_name = 'sale'.hexdec(rand(11111,99999)) . '.' . "png";
                    $img = Image::make($request->sale_imageurl[0]->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                                    $real_name = 'sale'.hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                    $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                $path = public_path().$destinationPath;
                    if (!File::exists($path)) {
                        File::makeDirectory($path, $mode = 0777, true, true);
                    }
                $real_name = 'sale'.hexdec(rand(11111,99999)) . '.' . "png";
                $img = Image::make($request->sale_imageurl[0]->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                                $real_name = 'sale'.hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                        if(Storage::disk('s3')->exists($saleImage->sale_img_url)){
                            Storage::disk('s3')->delete($saleImage->sale_img_url);
                        }
                        if (File::exists(public_path().$saleImage->sale_img_url)) {
                            unlink(public_path().$saleImage->sale_img_url);
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
                                    $real_name = 'sale'.hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                    $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                                $real_name = hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                $img = Image::make($file->getRealPath())->resize('636','350', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
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
                    if(Storage::disk('s3')->exists($saleImages->sale_img_url)){
                        Storage::disk('s3')->delete($saleImages->sale_img_url);
                    }
                }
                if(Storage::disk('s3')->exists($sale->sale_imageurl)){
                    Storage::disk('s3')->delete($sale->sale_imageurl);
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
    public function jobTypeGet(Request $request){
        $user = JWTAuth::authenticate($request->token);
        $jobTypes = Jobtype::Where('jobtype_status', 1)->get();
        return response()->json(
            ['jobtypes' => $jobTypes]
        );
    }
    public function getcitiesGet(Request $request){
        $user = JWTAuth::authenticate($request->token);
        $cities = City::Where('city_status', 1)->get();
        return response()->json(
            ['cities' => $cities]
        );
    }
    public function qualificationGet(Request $request){
        $user = JWTAuth::authenticate($request->token);
        $qualifications = Qualification::Where('qualification_status', 1)->get();
        return response()->json(
            ['qualifications' => $qualifications]
        );
    }
    public function businesstJobsGet(Request $request){
        $user = JWTAuth::authenticate($request->token);
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
            ->where('users.id', $user->id)
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
                ->where('jobs.user_id', '=', $user->id)
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

    #by admin function start
    public function businesstJobsGetbyAdmin(Request $request){
        $user = $request->user_id;
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
                ->where('jobs.user_id', '=', $user)
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
    #by admin function end
    # To delete the user by admin ( function start )
    public function deleteUserByAdmin( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }else{
            $data = $request->only('id');
            $validator = Validator::make($data, [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $delete_user = User::find($request->id);
                if( $delete_user == null){
                    return response([
                        'success' => false,
                        'message' => 'No data found related to this id.'
                    ],400);
                }else{
                    $delete_user->delete();
                }
                return response([
                    'success' => true,
                    'message' => 'User deleted successfully.'
                ]);

            }
        }
    }
    # To delete the user by admin ( function end )

    # To change the multiple users' status ( function start )
    public function deleteMultipleUsers( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if( $User ){
            if( $request->ids ){
                foreach( $request->ids as $id ){
                    $user = User::find($id);
                    $user->user_status = '0';
                    $user->update();
                }
                return response([
                    'success' => true,
                    'message' => 'Users deleted successfully.'
                ]);
            }else{
                return response([
                    'success' => false,
                    'message' => 'Ids not found!'
                ]);
            }
        }else{
            return response([
                'success' => false,
                'message' => 'User not found!'
            ],401);
        }
    }
    # To change the multiple users' status ( function end )

    # To get user profile data ( function start )
    public function getUserprofiles( Request $request )
    {
        $User = JWTAuth::authenticate($request->token);
        if($User){
            $userprofile = User::Leftjoin('profiles','profiles.user_id', '=', 'users.id')
                ->Leftjoin('countries','profiles.country_id', '=', 'countries.id')
                ->Leftjoin('states','profiles.state_id', '=', 'states.id')
                ->Leftjoin('cities','profiles.city_id', '=', 'cities.id')
                ->Select('users.*', 'profiles.user_avatar','countries.id as country_id','countries.country','states.id as state_id','states.state','cities.id as city_id','cities.city')
                ->Where('users.id',$User->id)
                ->get();
            return response()->json(
                [
                    'status' => 200,
                    'userprofile' => $userprofile
                ],200);
        }else{
            return response()->json(
                [
                    'status' => 400,
                    'user' => 'User not found'
                ],400);
        }

    }
    # To get user profile data ( function end )

    # Update User dashboard profile data( Function start )
    public function updateUserprofiles(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        $buser_id = $User->id;
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required',
            'mobile_phone' => 'required',
            // 'username' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        } else {
            $update_reseller = User::find($buser_id);
            $update_reseller->name = $request->name;
            $update_reseller->email = $request->email;
            $update_reseller->mobile_phone = $request->mobile_phone;
            // $update_reseller->username = $request->username;
            $update_reseller->update();

            Profile::where('user_id',$buser_id)->update([
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
            ]);

            $update_avatar = Profile::Where('user_id', $buser_id)->first();
            if($request->file('user_avatar')){
                $call_function = new Profile();
                $file = $request->file('user_avatar');
                $old_file = $update_avatar->user_avatar;
                $update_avatar->user_avatar = $call_function->avatar($old_file,$file);
                $update_avatar->update();
            }
        }
        return response()->json(
            [
                'user_profile' => 'User Profile Updated Successfully.'
            ],
            200
        );
    }
    # Update User dashboard profile data( Function end )

    # Update User dashboard password change data( Function start )
    public function updateUserPasswords(Request $request){

        $User = JWTAuth::authenticate($request->token);
        $user_id = $User->id;
        if($User){
            $update_password = User::find($user_id);
            $password = Hash::make($request->password);
            $update_password->password = $password;
            $update_password->update();

            return response()->json(
                [
                    'user_password' => 'User Password Updated Successfully.'
                ],200);
        }
        else{
            return response()->json(
                [
                    'user_password' => 'User Password Not Updated!.'
                ],400);
        }
    }
    # Update User dashboard password change data( Function end )

    # Get user reviews data( Function start )
    public function getUserreviews(Request $request){

        $User = JWTAuth::authenticate($request->token);
        $reviews = Review::leftjoin('users', 'users.id', 'reviews.review_business_id')
            ->leftjoin('profiles','profiles.user_id', 'users.id')
            ->with('allReviewImages')
            ->select('users.id', 'users.name', 'profiles.user_avatar', 'reviews.*',
                     DB::raw('DATE(reviews.created_at) as added_date'),
            )
            ->orderBy('reviews.rating')
            ->orderBy('reviews.review_business_id')
            ->where("reviews.review_user_id", $User->id)
            ->get();
            return response()->json(
                [
                    'user_reviews' => $reviews
                ],200);
        }

        public function getUserreviewsbusinessbyadmin(Request $request){

            $User = $request->user_id;
            $reviews = Review::leftjoin('users', 'users.id', 'reviews.review_business_id')
                ->leftjoin('profiles','profiles.user_id', 'users.id')
                ->with('allReviewImages')
                ->select('users.id', 'users.name', 'profiles.user_avatar', 'reviews.*',
                         DB::raw('DATE(reviews.created_at) as added_date'),
                )
                ->orderBy('reviews.rating')
                ->orderBy('reviews.review_business_id')
                ->where("reviews.review_user_id", $User)
                ->get();
                return response()->json(
                    [
                        'user_reviews' => $reviews
                    ],200);
            }
        public function getUserreviewsbyadmin(Request $request){

            $User =$request->reseller_id;
            $reviews = Review::leftjoin('users', 'users.id', 'reviews.review_business_id')
                ->leftjoin('profiles','profiles.user_id', 'users.id')
                ->with('allReviewImages')
                ->select('users.id', 'users.name', 'profiles.user_avatar', 'reviews.*',
                         DB::raw('DATE(reviews.created_at) as added_date'),
                )
                ->orderBy('reviews.rating')
                ->orderBy('reviews.review_business_id')
                ->where("reviews.review_user_id", $User)
                ->get();
                return response()->json(
                    [
                        'user_reviews' => $reviews
                    ],200);
            }
    # Get user reviews data( Function end )

    #send email admin and business after apply job (function start)
    public function applyJob(Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                                'success' => false,
                                'message' => 'User not valid!',
                            ],401);
        }else{
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                # To send email to admin who filled form (code start)
                $data = [
                    'name'=> $request->name,
                    'phone'=> $request->phone,
                    'experience'=> $request->experience,
                    'qualification'=> $request->qualification,
                    'job_name'=> $request->job_name,
                ];
                $getadmin_email = User::Where('user_role', '=', '4')->first();
                $admin_email =   $getadmin_email->email;
                Mail::send('jobapply', $data, function($message) use ($admin_email, $request) {
                    $message->to($admin_email)->subject('Job Apply.');
                    $message->from($request->email);
                });
                # To send email to user who filled form (code end)

                # To send email to business who filled form ( code start)
                $get_business_user_email = User::find($request->business_user_id);
                $business_email = $get_business_user_email->email;

                if( $business_email == null){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Business user email not found!.'
                        ],404);
                }else{
                    $data = [
                        'businessName'=> $get_business_user_email->name,
                        'username'=> $request->name,
                        'name'=> $request->name,
                        'phone'=> $request->phone,
                        'experience'=> $request->experience,
                        'qualification'=> $request->qualification,
                        'job_name'=> $request->job_name,
                    ];
                    Mail::send('businesstemplate1', $data, function($message) use ($business_email,$request) {
                        $message->to( $business_email)->subject('Job Apply.');
                        $message->from($request->email);
                    });
                }
                # To send email to business who filled form( code end)
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Job apply successfully.'
                    ],200);
            }
        }

    }
    #send email admin and business after apply job (function end)

    #send email admin after contact form fill (function start)
    // public function contactUs(Request $request){

    //         $data = $request->all();
    //         $validator = Validator::make($data, [
    //             'name' => 'required',
    //             'email' => 'required|email',
    //             'address' => 'required',
    //             'city' => 'required',
    //             'description' => 'required',
    //             'enquiry' => 'required',
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json(['error' => $validator->messages()], 400);
    //         }else{
    //             # To send email to admin who filled form (code start)
    //             $data = [
    //                 'name'=> $request->name,
    //                 'email'=> $request->email,
    //                 'address'=> $request->address,
    //                 'city'=> $request->city,
    //                 'description'=> $request->description,
    //                 'enquiry'=> $request->enquiry,
    //             ];
    //             $getadmin_email = User::Where('user_role', '=', '4')->first();
    //             $admin_email =   $getadmin_email->email;
    //             Mail::send('businesstemplate2', $data, function($message) use ($admin_email, $request) {
    //                 $message->to($admin_email)->subject($request->enquiry);
    //                 $message->from($request->email);
    //             });
    //             # To send email to admin who filled form (code end)
    //             return response()->json(
    //                 [
    //                     'success' => true,
    //                     'message' => 'Form submit successfully.'
    //                 ],200);
    //         }
    // }

    public function contactUs(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'address' => 'required',
        'city' => 'required',
        'description' => 'required',
        'enquiry' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->messages()], 400);
    }

    // Prepare the data for the email view
    $data = [
        'name'        => $request->name,
        'email'       => $request->email,
        'address'     => $request->address,
        'city'        => $request->city,
        'description' => $request->description,
        'enquiry'     => $request->enquiry,
    ];

    // Send the email
    Mail::send('businesstemplate2', $data, function ($message) use ($request) {
        $message->to('bizlxapp@gmail.com') // Admin inbox
                ->subject('Enquiry from: ' . $request->name . ' (' . $request->email . ')')
                ->replyTo($request->email, $request->name); // So admin can reply to user
    });
    
    

    return response()->json([
        'success' => true,
        'message' => 'Form submitted successfully.'
    ], 200);
}

    #send email admin after contact form fill (function end)
}
