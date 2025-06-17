<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Job;
use App\Models\Jobcategory;
use App\Models\Jobtype;
use App\Models\Qualification;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use File;

class JobCategoryController extends Controller
{

    public function jobcategoryGet()
    {
        $jobcategory = Jobcategory::Where('job_cat_feature', '=', 1)->Where('job_cat_status', '=', 1)->orderBy('job_cat_name', 'ASC')->get();
        return response()->json(
            ['jobcategories' => $jobcategory]
        );
    }
    public function jobcategoryGetByKeyword( Request $request )
    {
        if( !empty($request->keyword) ){
            $jobcategory = Jobcategory::Where('job_cat_feature', '=', 1)
            ->Where('job_cat_status', '=', 1)
            ->where('job_cat_name',"like", "%" . $request->keyword . "%")
            ->orderBy('job_cat_name', 'ASC')
            ->take(3)
            ->get();

            return response()->json(
                ['jobcategories' => $jobcategory]
            );
        }else{
            return response()->json([]);
        }
    }
    public function adminjobcategoryGet()
    {
        $jobcategory = Jobcategory::orderBy('job_cat_sort', 'ASC')->orderBy('created_at', 'DESC')->get();
        return response()->json(
            ['jobcategories' => $jobcategory]
        );
    }

    public function getJobbyid($job_cat_slug)
    {
        $jobcategory = Jobcategory::Where('job_cat_slug',$job_cat_slug)->first();

        if($jobcategory == null){
            return response()->json(
                ['message' => 'Job Category Not Found'], 400);

        }else{
            $getjobcatid = $jobcategory->id;
            $getjob = DB::Table('cities')
                ->Join('jobs','jobs.city_id','=','cities.id')
                ->Join('jobcategories', 'jobcategories.id', '=','jobs.job_cat_id')
                ->Where('jobs.job_cat_id','=', $getjobcatid)
                ->Select('cities.id','cities.city', DB::raw('COUNT(CASE WHEN jobs.job_status = 1 THEN 1 ELSE NULL END) as jobcount'))
                ->GroupBy('cities.id')
                ->get();
            return response()->json(
                [
                    'status' => 200,
                    'jobcatid' => $jobcategory,
                    'jobs' => $getjob
                ]
            );
        }
    }

    public function jobCatwithcity($job_cat_slug, $city)
    {
        $jobcategory = Jobcategory::Where('job_cat_slug',$job_cat_slug)->first();
        if ($jobcategory == null) {
            return response()->json(
                ['message' => 'Job List Not Found'], 400);
        }else{
            $getjobcatid = $jobcategory->id;
            $cities = City::Where('city',$city)->first();
            $cityid = $cities->id;
            $joblist = Job::Join('users','users.id','jobs.user_id')
                ->join('profiles','profiles.user_id','users.id')
                ->join('cities','cities.id','jobs.city_id')
                ->join('states','states.id','cities.state_id')
                ->where('cities.id', $cityid)
                ->where('jobs.job_cat_id', $getjobcatid)
                ->select('jobs.*','users.name','users.username','users.mobile_phone','cities.city','states.state','profiles.user_avatar')
                ->get();

            return response()->json(
                [
                    'status' => 200,
                    'joblists' => $joblist,
                ]
            );
        }
    }

    public function getAllcities()
    {
        $getcities = DB::Table('cities')
            ->Join('jobs','jobs.city_id','=', 'cities.id')
            ->Select('cities.id','cities.city', DB::raw('COUNT(CASE WHEN jobs.job_status = 1 THEN 1 ELSE NULL END) as jobcount'))
            ->GroupBy('cities.id')
            ->get();

        if (!$getcities) {
            return response()->json(
                ['message' => 'Cities Not Found'], 400);
        }
        return response()->json(
            [
                'status' => 200,
                'allcities' => $getcities,
            ]
        );
    }

    public function getjobCatbycity($city)
    {
        $cities = City::Where('city',$city)->first();
        if ($cities == null) {
            return response()->json(
                ['message' => 'Job Category Not Found'], 400);
        }else{
            $cityid = $cities->id;
            $getjobcat = DB::Table('jobcategories')
                ->Join('jobs','jobs.job_cat_id','=','jobcategories.id')
                ->Join('cities','cities.id', 'jobs.city_id')
                ->Select('jobcategories.id','jobcategories.job_cat_name','jobcategories.job_img_url','jobcategories.job_cat_slug','cities.city',
                 DB::raw('COUNT(CASE WHEN jobcategories.job_cat_status = 1 THEN 1 ELSE NULL END) as jobcount')
                 )
                ->Where('cities.id',$cityid)
                ->GroupBy('jobcategories.id')
                ->get();

            return response()->json(
                [
                    'status' => 200,
                    'jobcatbycity' => $getjobcat,
                ]
            );
        }
    }

    public function getJobdetail($job_slug, $loggedUser_id)
    {
        $jobs = Job::Where('job_slug', $job_slug)->first();
        $jobid = $jobs->id;
        if ($jobs == null) {
            return response()->json(
                ['message' => 'Job Profile Not Found'], 400);
        }else{
            $jobid = $jobs->id;
            $getjobsbyid = Job::join('jobtypes','jobtypes.id','jobs.job_type_id')
                ->Join('users','users.id','=','jobs.user_id')
                ->Join('galleryimages','users.id','=','galleryimages.user_id')
                ->Join('profiles','profiles.user_id','=','users.id')
                ->join('cities','cities.id','jobs.city_id')
                ->join('states','states.id','cities.state_id')
                ->join('countries','countries.id','states.country_id')
                ->join('qualifications','qualifications.id','jobs.job_qualification_id')
                ->select('jobs.*', 'users.id as business_id', 'users.mobile_phone', 'users.name','users.username','jobtypes.job_type',
                    'cities.city','states.state','countries.country','countries.phonecode',
                    'qualifications.qualification','profiles.user_avatar','galleryimages.image_url')
                ->where('jobs.id', $jobid)
                ->first();

            $user_job_wishlist = Wishlist::Where('services_id', $getjobsbyid->id)->where('user_id', '=', $loggedUser_id)->first();
            if($user_job_wishlist==null){
                $user_job_wishlist = null;
            }
            $getjobsbyid['user_job_wishlist'] = $user_job_wishlist;

            return response()->json(
                [
                    'status' => 200,
                    'jobdetails' => $getjobsbyid,
                ]
            );
        }
    }

    public function jobcategoryAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_cat_name' => 'required|unique:jobcategories',
            'job_img_url' => 'required',
            'job_cat_sort' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }else{
            $jobcategory = new Jobcategory;
            $jobcategory->job_cat_name = $request->job_cat_name;

            $file = $request->file('job_img_url'); // File Upload by model
            $jobcategory->job_img_url = $jobcategory->add_image($file);

            $jobcategory->job_cat_slug = strtolower(Str::slug($request->job_cat_name, '-'));
            $jobcategory->job_cat_feature = ($request->job_cat_feature == "true") ? "1" : "0";
            $jobcategory->job_cat_sort = $request->job_cat_sort;
            $jobcategory->job_cat_status = ($request->job_cat_status == "true") ? "1" : "0";
            $save_jobcategory = $jobcategory->save();
            if ($save_jobcategory==true) {
                return response()->json([
                    'status' => 200,
                    'message' => "Job category added successfully.",
                    'data' => $jobcategory
                ]);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => "Something went worng."
                ]);
            }
        }
    }

    public function jobcategoryEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'job_cat_name' => 'required',
            'job_cat_sort' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{

            $edit_jobcategory = Jobcategory::find($id);
            $edit_jobcategory->job_cat_name = $request->job_cat_name;

                if($request->file('job_img_url')){ // file upload from model
                    $file = $request->file('job_img_url');
                    $edit_jobcategory->job_img_url = $edit_jobcategory->edit_image($edit_jobcategory->job_img_url, $file);
                }

            $edit_jobcategory->job_cat_slug = strtolower(Str::slug($request->job_cat_name, '-'));
            $edit_jobcategory->job_cat_sort = $request->job_cat_sort;

            if( $request->job_cat_feature == "true" ){
                $edit_jobcategory->job_cat_feature = 1;
            }elseif( $request->job_cat_feature == "1" ){
                $edit_jobcategory->job_cat_feature = 1;
            }else{
                $edit_jobcategory->job_cat_feature = 0;
            }

            if( $request->job_cat_status == "true" ){
                $edit_jobcategory->job_cat_status = 1;
            }elseif( $request->job_cat_status == "1" ){
                $edit_jobcategory->job_cat_status = 1;
            }else{
                $edit_jobcategory->job_cat_status = 0;
            }

            $update_jobcategory = $edit_jobcategory->update();

            if ($update_jobcategory==true) {
                return response()->json([
                    'status' => 200,
                    'message' => "Job category updated successfully.",
                    'data' => $edit_jobcategory
                ]);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => "Something went worng."
                ]);
            }
        }
    }
    # To delete the specific jobcategory by admin ( function start)
    public function deleteJobcategory( Request $request){
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
                $jobs = Jobcategory::find($request->job_id);
                if( $jobs == null ){
                    return response([
                                        'success' => false,
                                        'message' => 'JobCategory does not exist please check again!'
                                    ],404);
                }else{
                   $jobcategory = Jobcategory::where('id',$request->job_id)->first();
                }
                    if($jobcategory){
                            $file_path = public_path().$jobcategory->job_img_url;
                            if (File::exists($file_path)) {
                                unlink($file_path);
                            }
                            if(Storage::disk('s3')->exists($jobcategory->job_img_url)){
                                Storage::disk('s3')->delete($jobcategory->job_img_url);
                            }
                           $jobcategory->delete();
                    return response([
                                        'success' => true,
                                        'message' => 'JobCategory deleted successfully'
                                    ]);
                }
            }

        }
    }
    # To delete the specific jobcategory by admin  ( function end)

    # To delete the multiple jobcategories by admin ( function start )

    public function deleteMultiplejobcat( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                                'success' => false,
                                'message' => 'User not found !'
                            ]);
        }else{
            $data = $request->only('ids');
            $validator = Validator::make($data, [
                'ids' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                foreach( $request->ids as $id ){
                    $delete_jobcats= Jobcategory::find($id);
                    $file_path = public_path().$delete_jobcats->job_img_url;
                    if (File::exists($file_path)) {
                        unlink($file_path);
                    }
                    if(Storage::disk('s3')->exists($delete_jobcats->job_img_url)){
                        Storage::disk('s3')->delete($delete_jobcats->job_img_url);
                    }
                    $delete_jobcats->delete();
                }
                return response(
                    [
                        'success' => true,
                        'message' => 'Job Categories deleted successfully.'
                    ]);
            }
        }
    }
    # To delete the multiple jobcategories by admin ( function end )

    # Get job types for super admin (function start)
    public function getjobtypes(){
        $getjobtypes = Jobtype::get();
        if($getjobtypes != null){
            return response(
                [
                    'success' => true,
                    'jobtypes' => $getjobtypes
                ],200);
        }else{
            return response(
                [
                    'success' => false,
                    'message' => 'Job Types Not Found'
                ],400);
        }
    }
    # Get job types for super admin (function end)

    # Add job types for super admin (function start)
    public function addjobtypes(Request $request){
        $validator = Validator::make($request->all(), [
            'job_type' => 'required|unique:jobtypes',
            'jobtype_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }else{
            $jobtytpe = new Jobtype();
            $jobtytpe->job_type = $request->job_type;
            $jobtytpe->jobtype_status = $request->jobtype_status;
            $save_jobcategory = $jobtytpe->save();
            if ($save_jobcategory==true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "Job Type added successfully.",
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
    # Add job types for super admin (function end)

    # Edit job types for super admin (function start)
    public function editjobtypes(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'jobtype_status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{
            $edit_jobtype = Jobtype::Where('id', $id)->first();
            $edit_jobtype->job_type = $request->job_type;
            if( $request->jobtype_status == "true" ){
                $edit_jobtype->jobtype_status = 1;
            }elseif( $request->jobtype_status == 1 ){
                $edit_jobtype->jobtype_status = 1;
            }else{
                $edit_jobtype->jobtype_status = 0;
            }
            $save_jobtype = $edit_jobtype->update();
            if ($save_jobtype == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "Job Type Update successfully.",
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
    # Edit job types for super admin (function end)

    # Delete job types for super admin (function start)
    public function deletejobtypes(Request $request){

        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response(
                [
                    'success' => false,
                    'message' => 'User not valid!'
                ],400);
        }else{
            $data = $request->only('jobtype_id');
            $validator = Validator::make($data, [
                'jobtype_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $jobtype = Jobtype::find($request->jobtype_id);
                if( $jobtype == null ){
                    return response([
                                        'success' => false,
                                        'message' => 'Job Type does not exist please check again!'
                                    ],400);
                }else{
                    Jobtype::Where('id',$request->jobtype_id)->delete();
                     return response(
                        [
                            'success' => true,
                            'message' => 'Job Type delete successfully'
                        ]);
                }
            }

        }
    }
    # Delete job types for super admin (function end)

    # Get job Qualification for super admin (function start)
    public function getjobqualification(){
        $getjobqualification = Qualification::get();
        if($getjobqualification != null){
            return response(
                [
                    'success' => true,
                    'jobqualification' => $getjobqualification
                ],200);
        }else{
            return response(
                [
                    'success' => false,
                    'message' => 'Job Qualification Types Not Found'
                ],400);
        }
    }
    # Get job Qualification for super admin (function end)

    # Add job Qualification for super admin (function start)
    public function addjobqualification(Request $request){
        $validator = Validator::make($request->all(), [
            'qualification' => 'required|unique:qualifications',
            'qualification_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }else{
            $jobqualification = new Qualification();
            $jobqualification->qualification = $request->qualification;
            $jobqualification->qualification_status = $request->qualification_status;
            $save_jobqualification = $jobqualification->save();
            if ($save_jobqualification == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "Job Qualification added successfully.",
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
    # Add job Qualification for super admin (function end)

    # Edit job Qualification for super admin (function start)
    public function editjobqualification(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'qualification_status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{
            $edit_qualification = Qualification::Where('id', $id)->first();
            $edit_qualification->qualification = $request->qualification;
            if( $request->qualification_status == "true" ){
                $edit_qualification->qualification_status = 1;
            }elseif( $request->qualification_status == 1 ){
                $edit_qualification->qualification_status = 1;
            }else{
                $edit_qualification->qualification_status = 0;
            }
            $save_qualification = $edit_qualification->update();
            if ($save_qualification == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "Job Qualification Update successfully.",
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
    # Edit job Qualification for super admin (function end)

    # Delete job Qualification for super admin (function start)
    public function deletejobqalification(Request $request){

        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response(
                [
                    'success' => false,
                    'message' => 'User not valid!'
                ],400);
        }else{
            $data = $request->only('qualification_id');
            $validator = Validator::make($data, [
                'qualification_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $jobqualification = Qualification::find($request->qualification_id);
                if( $jobqualification == null ){
                    return response([
                                        'success' => false,
                                        'message' => 'Job qualification does not exist please check again!'
                                    ],400);
                }else{
                    Qualification::Where('id',$request->qualification_id)->delete();
                    return response(
                        [
                            'success' => true,
                            'message' => 'Job Qualification delete successfully'
                        ]);
                }
            }

        }
    }
    # Delete job Qualification for super admin (function end)
}
