<?php

namespace App\Http\Controllers;

use App\Models\Catslider;
use App\Models\Galleryimage;
use App\Models\Hotdealimage;
use App\Models\Qualification;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class CatsliderController extends Controller
{
    # Get category slider all data for Home Page (function start)
    public function getCatsliders($loggedUser_id)
    {
        $catslider = Catslider::with('info', 'hotdeal', 'sales', 'job', 'video')
            ->Join( 'subcategories', 'subcategories.id', 'catsliders.subcat_id')
            ->Where('catslider_status', '=', 1)
            ->Select('catsliders.*', 'subcategories.subcat_name')
            ->get();

        foreach( $catslider as $slider ){
            foreach( $slider->info as $info ){
                $gimage = Galleryimage::where('galleryimages.user_id', '=',$info->business_id)->first();
                if(isset($gimage->image_url)){
                    $info->mainimage = $gimage->image_url;
                }else{
                    $info->mainimage = null;
                }
                $reviews = Review::where('review_business_id',$info->business_id)->get();
                $reviews_count = count($reviews);
                if( $reviews_count > 0 ){
                    $sum = Review::where('review_business_id',$info->business_id)->sum('rating');
                    $info->rating = round($sum / $reviews_count,1);
                }else{
                    $info->rating = 0;
                }

                $firstLetter = strtoupper(substr($info->name, 0, 1));
                $spacePosition = strpos($info->name, ' ');

                if ($spacePosition !== false) {
                    $secondLetter = strtoupper(substr($info->name, $spacePosition + 1, 1));
                } else {
                    $secondLetter = '';
                }
                $info->first_last_letter = $firstLetter.$secondLetter;

            }

            foreach ( $slider->hotdeal as $hotdeal ){ // Images & Hot-deals wishlist
                $get_hotdeal_image = Hotdealimage::where('hotdeal_id',$hotdeal->id)->first();
                if( $get_hotdeal_image != null ){
                    $hotdeal->hotdeal_image_url = $get_hotdeal_image->hotdeal_img_url;
                }else{
                    $hotdeal->hotdeal_image_url = '';
                }
                $hotdeal->user_added_wishlist = Wishlist::Where('services_id', $hotdeal->id)->where('user_id', $loggedUser_id)->first();
                $hotdeal->date_from = date("d-m-Y", strtotime($hotdeal->date_from));
                $hotdeal->date_to = date("d-m-Y", strtotime($hotdeal->date_to));

                $firstLetter = strtoupper(substr($hotdeal->name, 0, 1));
                $spacePosition = strpos($hotdeal->name, ' ');

                if ($spacePosition !== false) {
                    $secondLetter = strtoupper(substr($hotdeal->name, $spacePosition + 1, 1));
                } else {
                    $secondLetter = '';
                }
                $hotdeal->first_last_letter = $firstLetter.$secondLetter;

            }
            foreach ( $slider->sales as $sales ){ // Sales wishlist
                $sales->user_added_wishlist = Wishlist::Where('services_id', $sales->id)->where('user_id', $loggedUser_id)->first();

                $firstLetter = strtoupper(substr($sales->name, 0, 1));
                $spacePosition = strpos($sales->name, ' ');

                if ($spacePosition !== false) {
                    $secondLetter = strtoupper(substr($sales->name, $spacePosition + 1, 1));
                } else {
                    $secondLetter = '';
                }
                $sales->first_last_letter = $firstLetter.$secondLetter;

                # change date format
                $sales->saledate_from = date("d-m-Y", strtotime($sales->saledate_from));
                $sales->saledate_to = date("d-m-Y", strtotime($sales->saledate_to));
            }
            foreach ( $slider->job as $job ){ // Jobs wishlist
                $gimage = Galleryimage::where('galleryimages.user_id', '=',$job->business_id)->first();
                if(isset($gimage->image_url)){
                    $job->mainimage = $gimage->image_url;
                }else{
                    $job->mainimage = null;
                }

                $job->user_added_wishlist = Wishlist::Where('services_id', $job->id)->where('user_id', $loggedUser_id)->first();

                $firstLetter = strtoupper(substr($job->name, 0, 1));
                $spacePosition = strpos($job->name, ' ');

                if ($spacePosition !== false) {
                    $secondLetter = strtoupper(substr($job->name, $spacePosition + 1, 1));
                } else {
                    $secondLetter = '';
                }
                $job->first_last_letter = $firstLetter.$secondLetter;
            }
            foreach ( $slider->video as $video ){ // Videos wishlist
                $video->user_added_wishlist = Wishlist::Where('services_id', $video->id)->where('user_id', $loggedUser_id)->first();
            }
        }

        return response()->json(
            [
                'status' => 200,
                'message' => 'Cat Sliders List',
                'catsliders' => $catslider
            ]
        );
    }
    # Get category slider all data for Home Page (function end)

    # Get category slider for super admin (function start)
    public function admingetCatsliders()
    {
        $catslider = Catslider::Join('subcategories', 'subcategories.id', 'catsliders.subcat_id')->Select(
                'catsliders.*',
                'subcategories.subcat_name'
            )->get();
        if ($catslider != null) {
            return response(
                [
                    'success' => true,
                    'catsliders' => $catslider
                ],
                200
            );
        } else {
            return response(
                [
                    'success' => false,
                    'message' => 'Home Category Slider Not Found'
                ],
                400
            );
        }
    }
    # Get category slider for super admin (function end)

    # Add category slider for super admin (function start)
    public function adminaddCatsliders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subcat_id' => 'required|unique:catsliders',
            'catslider_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        } else {
            $catslider = new Catslider();
            $catslider->subcat_id = $request->subcat_id;
            $catslider->catslider_status = $request->catslider_status;
            $save_catslider = $catslider->save();
            if ($save_catslider == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "Category Slider added successfully.",
                    ]
                );
            } else {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => "Something went wrong."
                    ]
                );
            }
        }
    }
    # Add category slider for super admin (function end)

    # Edit category slider for super admin (function start)
    public function admineditCatsliders(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'subcat_id' => 'required',
            'catslider_status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{
            $edit_catslider = Catslider::Where('id', $id)->first();
            $edit_catslider->subcat_id = $request->subcat_id;
            if( $request->catslider_status == "true" ){
                $edit_catslider->catslider_status = 1;
            }elseif( $request->catslider_status == 1 ){
                $edit_catslider->catslider_status = 1;
            }else{
                $edit_catslider->catslider_status = 0;
            }
            $save_catslider = $edit_catslider->update();
            if ($save_catslider == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "Category Slider Update successfully.",
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
    # Edit category slider for super admin (function end)

    # # Delete category slider for super admin (function start)
    public function admindeleteCatsliders(Request $request){

        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response(
                [
                    'success' => false,
                    'message' => 'User not valid!'
                ],400);
        }else{
            $data = $request->only('catid');
            $validator = Validator::make($data, [
                'catid' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $jobqualification = Catslider::find($request->catid);
                if( $jobqualification == null ){
                    return response([
                                        'success' => false,
                                        'message' => 'Category slider does not exist please check !'
                                    ],400);
                }else{
                    Catslider::Where('id',$request->catid)->delete();
                    return response(
                        [
                            'success' => true,
                            'message' => 'Category Slider delete successfully'
                        ]);
                }
            }

        }
    }
    # Delete category slider for super admin (function end)
}
