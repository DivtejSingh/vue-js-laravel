<?php

namespace App\Http\Controllers;

use App\Models\Topad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use File;

class TopadsController extends Controller
{
    public function getTopads(){
        $topads = Topad::join('users','users.id','topads.business_id')
            ->leftjoin('profiles','users.id','=','profiles.user_id')
            ->leftjoin('states','profiles.state_id','=','states.id')
            ->leftjoin('cities','profiles.city_id','=','cities.id')
            ->where('ad_status', '=', 1)
            ->where('users.user_role','1')
            ->select('topads.*','users.id as user_id','users.name as business_name','users.username as business_uname','states.state','cities.city')
            ->get();

        return response()->json(
            [
                'status' => true,
                'message'=> 'Topads List',
                'topads' => $topads
            ]);
    }
    public function admingetTopads(){
        $topads = Topad::join('users','users.id','topads.business_id')
            ->where('users.user_role','1')
            ->select('topads.*','users.id as user_id','users.name as business_name','users.username as business_uname')
            ->get();

        return response()->json(
            [
                'status' => true,
                'message'=> 'Topads List',
                'topads' => $topads
            ]);
    }

    public function getallBusiness(){
        $Business = User::where('user_role', '=', '1')->get();
        if($Business != null){
            return response()->json(
                [
                    'status' => true,
                    'message'=> 'All Business List',
                    'business' => $Business
                ],200);
        }else{
            return response()->json(
                [
                    'status' => false,
                    'message'=> 'Business List Not Found',
                ],400);
        }
    }

    public function topadsAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|unique:topads',
            'ad_status' => 'required',
            'ad_img_url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }else{
            $topadss = new Topad();
            $topadss->business_id = $request->business_id;
            $file = $request->file('ad_img_url'); // File Upload by model
            $topadss->ad_img_url = $topadss->add_image($file);
            $topadss->ad_status = $request->ad_status;
            $save_topadss = $topadss->save();
            if ($save_topadss == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "Top Ads added successfully.",
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

    public function topadsEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'ad_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{

            $edit_topads = Topad::find($id);
            $edit_topads->business_id = $request->business_id;

            if($request->file('ad_img_url')){ // file upload from model
                $file = $request->file('ad_img_url');
                $edit_topads->ad_img_url = $edit_topads->edit_image($edit_topads->ad_img_url, $file);
            }
            if( $request->ad_status == "true" ){
                $edit_topads->ad_status = 1;
            }elseif( $request->ad_status == "1" ){
                $edit_topads->ad_status = 1;
            }else{
                $edit_topads->ad_status = 0;
            }

            $update_topads = $edit_topads->update();

            if ($update_topads == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "Top Ads updated successfully.",
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
    public function deletetopads( Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                                'success' => false,
                                'message' => 'User not valid!'
                            ],401);
        }else{
            $data = $request->only('topads_id');
            $validator = Validator::make($data, [
                'topads_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $topads = Topad::find($request->topads_id);
                if( $topads == null ){
                    return response(
                        [
                            'success' => false,
                            'message' => 'Topads does not exist please check again!'
                        ],404);
                }else{
                    $topads = Topad::where('id',$request->topads_id)->first();
                }
                if($topads){
                    $file_path = public_path().$topads->ad_img_url;
                    if (File::exists($file_path)) {
                        unlink($file_path);
                    }
                    if(Storage::disk('s3')->exists($topads->ad_img_url)){
                        Storage::disk('s3')->delete($topads->ad_img_url);
                    }
                    $topads->delete();
                    return response(
                        [
                            'success' => true,
                            'message' => 'Top ads deleted successfully'
                        ]);
                }
            }

        }
    }
    # To delete the specific Topads by admin  ( function end)
}
