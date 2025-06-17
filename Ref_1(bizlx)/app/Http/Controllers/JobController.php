<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Job;
use App\Models\Plan;
use App\Models\Profession;
use App\Models\Profile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class JobController extends Controller
{

    # To add job (function start)
    public function addJob( Request $request){

        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ]);
        }else{
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
            }
            else{
                $user_id = $User->id;
                $business_data = Business::Where('business_id', $user_id)->first();
                $user_plan_id = $business_data->plan_id;
                $city_id = Profile::Where('user_id', $user_id)->first()->city_id;
                $business_plan = Plan::Where('id', $user_plan_id)->first();
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
                            ],
                            200
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
                        ],
                        200
                    );
                }
            }
        }
    }
    # To add job (function end)

    # To add job by admin (function start)
    public function addJobbyAdmin( Request $request){

        $User = $request->user_id;
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ]);
        }else{
            $data = $request->only('job_title','job_cat_id','job_type_id','job_qualification_id','salary_from','min_exp','job_description');
            $validator = Validator::make($data, [
                'job_title' => 'required',
                'job_cat_id' => 'required',
                'job_type_id' => 'required',
                'job_qualification_id' => 'required',
                // 'city_id' => 'required',
                'salary_from' => 'required',
                'min_exp' => 'required',
                'job_description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $user_id = $User;
                $business_data = Business::Where('business_id', $user_id)->first();
                $user_plan_id = $business_data->plan_id;
                $city_id = Profile::Where('user_id', $user_id)->first()->city_id;
                $business_plan = Plan::Where('id', $user_plan_id)->first();
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
                            ],
                            200
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
                        ],
                        200
                    );
                }
            }
        }
    }
    # To add job by admin (function end)
    # To update job (function start)
    public function updateJob( Request $request){

        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ]);
        }else{
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
            }
            else{
                $user_id = $User->id;
                $job = Job::find($request->job_id);
                $job->user_id = $user_id;
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
    }
    # To update job (function end)

    # To update job by admin(function start)
    public function updateJobbyAdmin( Request $request)
    {
        $User = $request->user_id;
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
        }
        else{
            $user_id = $User;
            $job = Job::find($request->job_id);
            $job->user_id = $user_id;
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

    # To get all the jobs list ( function start )
    public function jobsList( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ],401);
        }else{
            $jobs = Job::where('user_id',$User->id)->get();
            return response([
                'success' => true,
                'jobs' => $jobs
            ]);
        }
    }
    # To get all the jobs list ( function end )

    # To delete the specific job ( function start)
    public function deleteJob( Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ],401);
        }else{
            $data = $request->only('job_id');
            $validator = Validator::make($data, [
                'job_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
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
    }
    # To delete the specific job ( function end)
}
