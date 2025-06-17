<?php

namespace App\Http\Controllers;

use App\Models\Tab;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use DateTime;
use DateInterval;

class PlanController extends Controller
{
    public function getPlans()
    {
        $plans =  Plan::where('plan_status', '1')->get();
        return response()->json(
            [
               'status' => true,
               'plans' => $plans
            ]);
    }
    public function admingetPlans()
    {
        $plans =  Plan::orderBy('created_at', 'desc')->get();
        return response()->json(
            [
               'status' => true,
               'plans' => $plans
            ]);
    }
    #To get business billing by reseller (function start)
    public function getBussinessBillingPlans(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);
        $active_user_plan = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
                            ->leftjoin('businesses', 'businesses.business_id', 'profiles.user_id')
                            ->select(
                                'users.id as user_id',
                                'users.name',
                                'businesses.plan_id')
                            ->where('users.id', $user->id)
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
    public function getBussinessactivePlans(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);
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
            ->where('users.id', $user->id)
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

    #To get business billing active by admin (function start)
    public function getBussinessactivePlansbyAdmin(Request $request)
    {
        $user = $request->user_id;
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
    # To get business billing active by admin (function end)

    # To save the plan by admin ( function start )
    public function adminAddplan( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                                'success' => false,
                                'message' => 'User not found !'
                            ]);
        } else {
            $data = $request->all();
            $validator = Validator::make($data, [
                'plan_description' => 'required',
                'plan_expiry' => 'required',
                'plan_price' => 'required',
                'images' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $add_plan = new Plan();
                $add_plan->plan_description = $request->plan_description;
                $add_plan->plan_expiry = $request->plan_expiry;
                $add_plan->plan_price = $request->plan_price;
                $add_plan->plan_status = $request->plan_status;
                $add_plan->images = $request->images;
                if($request->hot_deals != null){
                    $add_plan->hot_deals = $request->hot_deals;
                }else{
                    $add_plan->hot_deals = 0;
                }
                if($request->jobs != null){
                    $add_plan->jobs = $request->jobs;
                }else{
                    $add_plan->jobs = 0;
                }
                if($request->sale != null){
                    $add_plan->sale = $request->sale;
                }else{
                    $add_plan->sale = 0;
                }
                if($request->video != null){
                    $add_plan->video = $request->video;
                }else{
                    $add_plan->video = 0;
                }
                if( $request->featured_city === "true" ){
                    $add_plan->featured_city = 1;
                }elseif( $request->featured_city == "1" ){
                    $add_plan->featured_city = 1;
                }else {
                    $add_plan->featured_city = 0;
                }
                if( $request->featured_category === "true" ){
                    $add_plan->featured_category = 1;
                }elseif( $request->featured_category == "1" ){
                    $add_plan->featured_category = 1;
                }else {
                    $add_plan->featured_category = 0;
                }
                if( $request->home_page_banner === "true" ){
                    $add_plan->home_page_banner = 1;
                }elseif( $request->home_page_banner == "1" ){
                    $add_plan->home_page_banner = 1;
                }else {
                    $add_plan->home_page_banner = 0;
                }
                if( $request->contact === "true" ){
                    $add_plan->contact = 1;
                }elseif( $request->contact == "1" ){
                    $add_plan->contact = 1;
                }else {
                    $add_plan->contact = 0;
                }
                if( $request->services === "true" ){
                    $add_plan->services = 1;
                }elseif( $request->services == "1" ){
                    $add_plan->services = 1;
                }else {
                    $add_plan->services = 0;
                }
                if( $request->reviews === "true" ){
                    $add_plan->reviews = 1;
                }elseif( $request->reviews == "1" ){
                    $add_plan->reviews = 1;
                }else {
                    $add_plan->reviews = 0;
                }
                if( $request->about === "true" ){
                    $add_plan->about = 1;
                }elseif( $request->about == "1" ){
                    $add_plan->about = 1;
                }else {
                    $add_plan->about = 0;
                }
                $add_plan->save();
                return response(
                    [
                        'success' => true,
                        'message' => 'Plan Created successfully.',
                    ]);
            }
        }
    }
    # To save the plan by admin( function end ) # To save the plan by admin ( function start )
    public function adminEditplan( Request $request, $id){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response(
                [
                    'success' => false,
                    'message' => 'User not found !'
                ]);
        } else {
                $edit_plan = Plan::find($id);
                $edit_plan->plan_description = $request->plan_description;
                $edit_plan->plan_expiry = $request->plan_expiry;
                $edit_plan->plan_price = $request->plan_price;
                $edit_plan->images = $request->images;
                if($request->hot_deals != null){
                    $edit_plan->hot_deals = $request->hot_deals;
                }else{
                    $edit_plan->hot_deals = 0;
                }
                if($request->jobs != null){
                    $edit_plan->jobs = $request->jobs;
                }else{
                    $edit_plan->jobs = 0;
                }
                if($request->sale != null){
                    $edit_plan->sale = $request->sale;
                }else{
                    $edit_plan->sale = 0;
                }
                if($request->video != null){
                    $edit_plan->video = $request->video;
                }else{
                    $edit_plan->video = 0;
                }
                if( $request->plan_status == "true" ){
                    $edit_plan->plan_status = '1';
                }elseif( $request->plan_status == "1" ){
                    $edit_plan->plan_status = '1';
                }else {
                    $edit_plan->plan_status = '0';
                }
                if( $request->featured_city == "true" ){
                    $edit_plan->featured_city = 1;
                }elseif( $request->featured_city == "1" ){
                    $edit_plan->featured_city = 1;
                }else {
                    $edit_plan->featured_city = 0;
                }
                if( $request->featured_category == "true" ){
                    $edit_plan->featured_category = 1;
                }elseif( $request->featured_category == "1" ){
                    $edit_plan->featured_category = 1;
                }else {
                    $edit_plan->featured_category = 0;
                }
                if( $request->home_page_banner == "true" ){
                    $edit_plan->home_page_banner = 1;
                }elseif( $request->home_page_banner == "1" ){
                    $edit_plan->home_page_banner = 1;
                }else {
                    $edit_plan->home_page_banner = 0;
                }
                if( $request->contact == "true" ){
                    $edit_plan->contact = 1;
                }elseif( $request->contact == "1" ){
                    $edit_plan->contact = 1;
                }else {
                    $edit_plan->contact = 0;
                }
                if( $request->services == "true" ){
                    $edit_plan->services = 1;
                }elseif( $request->services == "1" ){
                    $edit_plan->services = 1;
                }else {
                    $edit_plan->services = 0;
                }
                if( $request->reviews == "true" ){
                    $edit_plan->reviews = 1;
                }elseif( $request->reviews == "1" ){
                    $edit_plan->reviews = 1;
                }else {
                    $edit_plan->reviews = 0;
                }
                if( $request->about == "true" ){
                    $edit_plan->about = 1;
                }elseif( $request->about == "1" ){
                    $edit_plan->about = 1;
                }else {
                    $edit_plan->about = 0;
                }
                 $edit_plan->update();
                return response(
                    [
                        'success' => true,
                        'message' => 'Plan Updated successfully.',
                    ]);
            }
        }
    # To save the plan by admin( function end )

    # To get the details of a specific plans ( function start )
    public function getplansbyid( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response(
                [
                    'success' => false,
                    'message' => 'User not found !'
                ]);
        } else {
            $data = $request->only('plan_id');
            $validator = Validator::make($data, [
                'plan_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $plandata = Plan::where('plans.id', $request->plan_id)->first();
                return response(
                    [
                        'success' => true,
                        'planbyid' => $plandata,
                    ]);
            }
        }
    }
    # To get the details of a specific plans ( function end )

    # To delete the specific plan by admin ( function start)
    public function adminDeleteplan( Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response(
                [
                    'success' => false,
                    'message' => 'User not valid!'
                ],400);
        }else{
            $data = $request->only('plan_id');
            $validator = Validator::make($data, [
                'plan_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $plans = Plan::find($request->plan_id);
                if( $plans == null ){
                    return response(
                        [
                            'success' => false,
                            'message' => 'Plan does not exist please check again!'
                        ],404);
                }else{
                    Plan::where('id',$request->plan_id)->delete();
                    return response(
                        [
                            'success' => true,
                            'message' => 'Plan deleted successfully'
                        ]);
                }


                }
            }
        }
    # To delete the specific plan by admin  ( function end)
}
