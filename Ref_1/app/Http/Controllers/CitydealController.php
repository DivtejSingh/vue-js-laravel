<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\User;
use App\Models\Citydeal;
use App\Models\Hotdeal;
use App\Models\Hotdealimage;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Wishlist;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CitydealController extends Controller
{
    public function getCitydeals(){
        $currentDate = date('Y-m-d');
        $citydeals = Citydeal::leftJoin('cities', 'cities.id', 'citydeals.city_id')
        ->leftJoin('profiles', 'profiles.city_id', 'citydeals.city_id')
        ->leftJoin('users', 'users.id', 'profiles.user_id')
        ->leftJoin('hotdeals', 'hotdeals.business_id', 'profiles.id')
        ->select('citydeals.*','hotdeals.*','cities.city')
        ->selectRaw('COUNT(profiles.city_id) AS listcount')
        ->selectRaw('SUM(CASE WHEN hotdeals.date_from <= ? AND hotdeals.date_to >= ? THEN 1 ELSE 0 END) AS dealcount', [$currentDate, $currentDate])
        ->groupBy('citydeals.city_id')
        ->where('citydeals.citydeal_status', '=', 1)
        ->where('users.user_status', '=', '1')
        ->where('users.user_role', '=', '1')
        ->get();
//        for($i=0; $i < count($citydeals); $i++) {
//            $hotdealsCount = Hotdeal::Where('business_id', '=', $citydeals[$i]->business_id)
//                ->where('date_from', '<=', $currentDate)
//                ->where('date_to', '>=', $currentDate)
//                ->where('hot_deal_status', '=', 1)
//                ->get()->count();
//            $citydeals[$i]['dealcount'] = $hotdealsCount;
//        }
        return response()->json(
            [
             'status' => 200,
             'message' => 'All Citydeals List',
             'citydeals' => $citydeals,
            ]);
    }
    public function admingetCitydeals(){
        $citydeals = Citydeal::Join('cities','cities.id','citydeals.city_id')
            ->select('citydeals.*','cities.city')
            ->get();
        return response()->json(
            [
             'status' => 200,
             'message' => 'All Citydeals List',
             'citydeals' => $citydeals,
            ]);
    }

    public function getCitylistings( Request $request){

        $cities = City::Where('city',$request->city)->first();
        if ($cities == null){
            return response()->json(['status' => 400, 'message' => 'No Listings Found In City']);
        } else{

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

                $city = $cities->city;
                $citylistings = Profile::leftjoin('users','users.id','profiles.user_id')
                    ->leftjoin('cities','cities.id','profiles.city_id')
                    ->leftjoin('states','states.id','profiles.state_id')
                    ->leftjoin('businesses','businesses.business_id','profiles.user_id')
                    ->leftjoin('subcategories','subcategories.id','businesses.sub_cat_id')
                    ->select('profiles.*',
                    DB::raw("users.name,users.id As business_id,users.username As business_uname,users.mobile_phone,( 3959 * acos( cos( radians(" . $lat . ") ) *cos( radians( latitude ) )* cos( radians( longitude ) - radians(" . $lng . ")) + sin( radians(" . $lat . ") ) *sin( radians( latitude ) ) )) AS distance"),
                    'cities.city','states.state','businesses.sub_cat_id','businesses.business_id','subcategories.subcat_name')
                    ->having("distance", "<", $radius)
                    ->groupBy('businesses.business_id')
                    ->where('cities.city',$city)
                    ->where('users.user_status', '1')
                    ->Where('users.user_role','=', '1')
                    ->orderBy("distance")
                    ->inRandomOrder()
                    ->get();

                foreach( $citylistings as $cities ){
                    $reviews = Review::where('review_business_id',$cities->business_id)->get();
                    $reviews_count = count($reviews);
                    if( $reviews_count > 0 ){
                        $sum = Review::where('review_business_id',$cities->business_id)->sum('rating');
                        $cities->rating = round($sum / $reviews_count,1);
                    }else{
                        $cities->rating = 0;
                    }
                }
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'City Listings',
                    'citylistings' => $citylistings,
                ]);

            }else{
                $city = $cities->city;
                $citylistings = Profile::leftjoin('users','users.id','profiles.user_id')
                    ->leftjoin('cities','cities.id','profiles.city_id')
                    ->leftjoin('states','states.id','profiles.state_id')
                    ->leftjoin('businesses','businesses.business_id','profiles.user_id')
                    ->leftjoin('subcategories','subcategories.id','businesses.sub_cat_id')
                    ->select('profiles.*','users.name','users.id As business_id','users.username As business_uname','users.mobile_phone','cities.city','states.state','businesses.sub_cat_id','businesses.business_id','subcategories.subcat_name')
                    ->groupBy('businesses.business_id')
                    ->where('cities.city',$city)
                    ->where('users.user_status', '1')
                    ->Where('users.user_role','=', '1')
                    ->inRandomOrder()
                    ->get();

                    foreach( $citylistings as $cities ){
                        $reviews = Review::where('review_business_id',$cities->business_id)->get();
                        $reviews_count = count($reviews);
                        if( $reviews_count > 0 ){
                            $sum = Review::where('review_business_id',$cities->business_id)->sum('rating');
                            $cities->rating = round($sum / $reviews_count,1);
                        }else{
                            $cities->rating = 0;
                        }
                    }
                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'City Listings',
                        'citylistings' => $citylistings,
                    ]);
            }
        }

    }

    public function getCitydealslist(Request $request)
    {
        $cities = City::Where('city', $request->city)->first();
        $currentDate = date('Y-m-d');
        if ($cities == null) {
            return response()->json(['status' => 400, 'message' => 'No Deals Found In City']);
        } else {
            if ($request->radius != 0) {
                $radius = $request->radius;

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'http://ip-api.com/json/'.$request->ip,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ]);

                $response = curl_exec($curl);

                curl_close($curl);
                $data = json_decode($response, true);

                if (isset($data['status'])) {
                    $lng = '76.7889';
                    $lat = '30.7339';
                } else {
                    $lng = $data['lon'];
                    $lat = $data['lat'];
                }
                $city = $cities->city;
                $deals = Hotdeal::query();

                $deals->leftjoin('profiles', 'profiles.id', 'hotdeals.business_id')
                    ->leftjoin('cities', 'cities.id', 'profiles.city_id')
                    ->leftjoin('states', 'states.id', 'profiles.state_id')
                    ->leftjoin('users', 'users.id', 'profiles.user_id')
                    ->leftjoin('businesses', 'businesses.business_id', 'users.id')
                    ->leftjoin('subcategories', 'businesses.sub_cat_id', 'subcategories.id')
                    ->select('hotdeals.*',
                             DB::raw("users.name,users.id As business_id,users.username As business_uname,users.mobile_phone,( 3959 * acos( cos( radians(" . $lat . ") ) *cos( radians( latitude ) )* cos( radians( longitude ) - radians(" . $lng . ")) + sin( radians(" . $lat . ") ) *sin( radians( latitude ) ) )) AS distance"),
                             'profiles.city_id', 'cities.city', 'profiles.state_id', 'states.state','profiles.user_avatar', 'profiles.address', 'subcategories.subcat_name', 'businesses.sub_cat_id')
                    ->having("distance", "<", $radius)
//                    ->groupBy('hotdeals.business_id')
                    ->where('cities.city', $city)
                    ->where('hotdeals.date_from', '<=', $currentDate)
                    ->where('hotdeals.date_to', '>=', $currentDate)
                    ->where('hotdeals.hot_deal_status', '=', 1);

                    if( $request->min_price !='' && $request->max_price !='' ){
                        if( $request->max_price != 0 ){
                            $deals->whereBetween('price_to', [$request->min_price, $request->max_price]);
                        }
                    }
                    $dealslist = $deals->inRandomOrder()->get();
                foreach ($dealslist as $deal) {
                    $get_hotdeal_image = Hotdealimage::where('hotdeal_id', $deal->id)->first();
                    if ($get_hotdeal_image != null) {
                        $deal->hotdeal_image_url = $get_hotdeal_image->hotdeal_img_url;
                    } else {
                        $deal->hotdeal_image_url = '';
                    }
                    $user_added_wishlist = Wishlist::Where('services_id', $deal->id)->where('user_id', '=', $request->loggedUser_id)->first();
                    $deal->user_added_wishlist = $user_added_wishlist;
                    $deal->date_from = date("d-m-Y", strtotime($deal->date_from));
                    $deal->date_to = date("d-m-Y", strtotime($deal->date_to));
                }
                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'City Deal List',
                        'dealslist' => $dealslist,
                    ]);
            } else {
                $city = $cities->city;
                $deals = Hotdeal::query();
                $deals->leftjoin('profiles', 'profiles.id', 'hotdeals.business_id')
                    ->leftjoin('cities', 'cities.id', 'profiles.city_id')
                    ->leftjoin('states', 'states.id', 'profiles.state_id')->leftjoin('users', 'users.id', 'profiles.user_id')
                    ->leftjoin('businesses', 'businesses.business_id', 'users.id')
                    ->leftjoin('subcategories', 'businesses.sub_cat_id', 'subcategories.id')
                    ->select('hotdeals.*', 'profiles.city_id', 'cities.city', 'profiles.state_id', 'states.state', 'profiles.user_avatar', 'profiles.address', 'users.mobile_phone', 'users.name', 'subcategories.subcat_name', 'businesses.sub_cat_id')
//                    ->groupBy('hotdeals.business_id')
                    ->where('cities.city', $city)
                    ->where('hotdeals.date_from', '<=', $currentDate)
                    ->where('hotdeals.date_to', '>=', $currentDate)
                    ->where('hotdeals.hot_deal_status', '=', 1);

                    if( $request->min_price !='' && $request->max_price !='' ){
                        if( $request->max_price != 0 ){
                            $deals->whereBetween('price_to', [$request->min_price, $request->max_price]);
                        }
                    }

                    $dealslist = $deals->inRandomOrder()->get();

                foreach ($dealslist as $deal) {
                    $get_hotdeal_image = Hotdealimage::where('hotdeal_id', $deal->id)->first();
                    if ($get_hotdeal_image != null) {
                        $deal->hotdeal_image_url = $get_hotdeal_image->hotdeal_img_url;
                    } else {
                        $deal->hotdeal_image_url = '';
                    }
                    $user_added_wishlist = Wishlist::Where('services_id', $deal->id)->where('user_id', '=', $request->loggedUser_id)->first();
                    $deal->user_added_wishlist = $user_added_wishlist;
                    $deal->date_from = date("d-m-Y", strtotime($deal->date_from));
                    $deal->date_to = date("d-m-Y", strtotime($deal->date_to));
                }
                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'City Deal List',
                        'dealslist' => $dealslist,
                    ]);
            }
        }
    }

    public function getDealdetail($hotdeal_slug, $loggedUser_id){
        $hotdeal = Hotdeal::Where('hotdeal_slug', $hotdeal_slug)->first();
        $hotDeal_id = $hotdeal->id;
        $user_hotdeal_wishlist = Wishlist::Where('services_id',$hotDeal_id)->where('user_id', '=', $loggedUser_id)->first();
        if($user_hotdeal_wishlist==null){
            $user_hotdeal_wishlist = null;
        }
        date_default_timezone_set('Asia/Kolkata');
        $currentDate = date('Y-m-d');
        if ($hotdeal == null){
            return response()->json(['status' => 400, 'message' => 'No Deals Found']);
        } else{
            $hotdealid = $hotdeal->id;
            $hotdeals = Hotdeal::join('profiles','profiles.id','=','hotdeals.business_id')
                ->join('users','users.id','profiles.user_id')
                ->join('cities','cities.id','profiles.city_id')
                ->join('states','states.id','cities.state_id')
                ->join('countries','countries.id','states.country_id')
                ->with('images')
                ->select('hotdeals.*', 'users.id as business_id', 'users.name', 'users.username', 'users.mobile_phone','profiles.city_id',
                'cities.city','states.state','countries.country','countries.phonecode')
                ->where('hotdeals.id', $hotdealid)
                // ->where('hotdeals.date_to', '>=', $currentDate)
                ->first();
            $hotdeals['user_hotdeal_wishlist'] = $user_hotdeal_wishlist;

            $cityid = $hotdeals->city_id;
            $dealslider = Hotdeal::leftJoin('profiles','profiles.id','=','hotdeals.business_id')
                ->with('hotdealSingleImage')
                ->leftJoin('cities','cities.id','=','profiles.city_id')
                ->leftJoin('states','states.id','=','cities.state_id')
                ->join('users','users.id','profiles.user_id')
                ->select('hotdeals.*', 'profiles.city_id', 'cities.city', 'states.state','users.name','users.id as dealby_id','users.username')
                ->where('profiles.city_id', $cityid)
                ->where('hotdeals.id', '!=', $hotdeals->id)
                ->distinct()
                // ->where('hotdeals.hot_deal_status', 1)
                // ->where('hotdeals.date_from', '<=', $currentDate)
                // ->where('hotdeals.date_to', '>=', $currentDate)
                ->get();
                for($i = 0; $i<$dealslider->count(); $i++){
                    $user_hotdeal_wishlist = Wishlist::Where('services_id', $dealslider[$i]->id)->where('user_id', '=', $loggedUser_id)->first();
                    $dealslider[$i]['user_hotdeal_wishlist'] = $user_hotdeal_wishlist;
                    $dealslider[$i]['date_from'] = date("d-m-Y", strtotime($dealslider[$i]['date_from']));
                    $dealslider[$i]['date_to'] = date("d-m-Y", strtotime($dealslider[$i]['date_to']));
                }
                // $newDate = $hotdeals->date_from->format('d-m-Y');
                $hotdeals->date_from = date("d-m-Y", strtotime($hotdeals->date_from));

                $hotdeals->date_to = date("d-m-Y", strtotime($hotdeals->date_to));
            return response()->json([
                'status' => 200,
                'message' => 'Hotdeal Detail',
                'hotdeal' => $hotdeals,
                'nearby' => $dealslider,
            ]);
        }
    }

    public function citydealAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|unique:citydeals',
            'citydeal_status' => 'required',
            'city_img_url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }else{
            $citydeal = new Citydeal();
            $citydeal->city_id = $request->city_id;
            $file = $request->file('city_img_url'); // File Upload by model
            $citydeal->city_img_url = $citydeal->add_image($file);
            $citydeal->citydeal_status = $request->citydeal_status;
            $save_citydeal= $citydeal->save();
            if ($save_citydeal == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "City deal successfully.",
                    ]);
            }else{
                return response()->json(
                    [
                        'status' => 400,
                        'message' => "Something went wrong."
                    ]);
            }
        }
    }

    public function citydealEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required',
            'citydeal_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{

            $edit_citydeal = Citydeal::find($id);
            $edit_citydeal->city_id = $request->city_id;

            if($request->file('city_img_url')){ // file upload from model
                $file = $request->file('city_img_url');
                $edit_citydeal->city_img_url = $edit_citydeal->edit_image($edit_citydeal->city_img_url, $file);
            }
            if( $request->citydeal_status == "true" ){
                $edit_citydeal->citydeal_status = 1;
            }elseif( $request->citydeal_status == "1" ){
                $edit_citydeal->citydeal_status = 1;
            }else{
                $edit_citydeal->citydeal_status = 0;
            }

            $update_citydeal= $edit_citydeal->update();

            if ($update_citydeal == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "City deal updated successfully.",
                    ]);
            }else{
                return response()->json(
                    [
                        'status' => 400,
                        'message' => "Something went worng."
                    ]);
            }
        }
    }

    # To delete the specific Topads by admin ( function start)
    public function deletecitydeal( Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                                'success' => false,
                                'message' => 'User not valid!'
                            ],401);
        }else{
            $data = $request->only('city_id');
            $validator = Validator::make($data, [
                'city_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $topads = Citydeal::find($request->city_id);
                if( $topads == null ){
                    return response(
                        [
                            'success' => false,
                            'message' => 'City deal does not exist please check again!'
                        ],404);
                }else{
                    $topads = Citydeal::where('id',$request->city_id)->first();
                }
                if($topads){
                    $file_path = public_path().$topads->city_img_url;
                    if (File::exists($file_path)) {
                        unlink($file_path);
                    }
                    if(Storage::disk('s3')->exists($topads->city_img_url)){
                        Storage::disk('s3')->delete($topads->city_img_url);
                    }
                    $topads->delete();
                    return response(
                        [
                            'success' => true,
                            'message' => 'City deal deleted successfully'
                        ]);
                }
            }

        }
    }
    # To delete the specific jobcategory by admin  ( function end)
}
