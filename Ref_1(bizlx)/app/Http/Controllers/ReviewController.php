<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Intervention\Image\Facades\Image;
use DB;
use Illuminate\Support\Facades\File;

class ReviewController extends Controller
{
    public function adminReviewslist()
    {
        $reviews = Review::join('users','users.id','reviews.review_business_id')
            ->join('users as ur','ur.id','reviews.review_user_id')
            ->join('profiles','profiles.user_id','users.id')
            ->join('cities','cities.id','profiles.city_id')
            ->select('reviews.id','reviews.review_user_id','reviews.review_business_id','reviews.rating',
                     'reviews.review_text','reviews.created_at',
                     'ur.name as reviewby_name',\DB::raw("DATE_FORMAT(reviews.created_at, '%Y-%m-%d') as formatted_created_at"),
                     'users.name as bname','users.mobile_phone as bphone','profiles.city_id','cities.city')
            ->get();

        return response()->json(
            [
                'status' => 200,
                'message' => 'Admin Reviews List',
                'reviews' => $reviews
            ],200
        );
    }
    public function adminReviewUpdate(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);

        $validator = Validator::make($request->all(), [
            'review_id' => 'required|integer',
            'review_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        try {
            $review = Review::find($request->review_id);
            $review->review_text = $request->review_text;
            $review->update();

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Review updated successfully.',
                'data' => $review
            ]);

        }catch (JWTexception $e) {//code to handle the exception
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ]);
        }



    }

    # To save the user review details ( function start )
    public function addReview( Request $request ){
        $User = JWTAuth::authenticate($request->token);

        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not valid!'
            ]);
        }else{
            $logged_user_id = $User->id;
            $validator = Validator::make($request->all(), [
                'review_user_id' => 'required|integer',
                'review_business_id' => 'required|integer',
                'rating' => 'required',
                'description' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }else{
                if($logged_user_id==$request->review_business_id){
                    $message = "Business can't add self review";
                }else{
                    $added_review = Review::Where('review_user_id', $request->review_user_id)->Where('review_business_id', $request->review_business_id)->first();

                    if($added_review==null){
                        $add_review = new Review();
                        $add_review->review_user_id = $request->review_user_id;
                        $add_review->review_business_id = $request->review_business_id;
                        $add_review->rating = $request->rating;
                        $add_review->review_text = $request->description;
                        $review_save = $add_review->save();
                        if($review_save == true){

                            if($request->hasfile('review_imgs')){ // check images upload or not
                                $destinationPath = '/images/review_images';
                                $path = public_path().$destinationPath;
                                foreach($request->review_imgs as $file){ // file upload from model
                                    $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
                                    $img = Image::make($file)->resize(400, 200,function($constraint){
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                        }); // resize
                                    $final_path = $destinationPath."/".$real_name;
                                    Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                                    $reviewImage = new ReviewImage;
                                    $reviewImage->review_id = $add_review->id;
                                    $reviewImage->review_image_url = $final_path;
                                    $reviewImage->save();
                                }
                            }
                            $message = "Review added successfully.";
                            $review_data = $add_review;
                        }else{
                            $message = "Something is wrong.";
                        }
                    }else{
                        $message = "You have already reviewed.";
                        $review_data = $added_review;
                    }
                }
            }
            return response([
                'success' => true,
                'message' => $message,
                'data' => $review_data
            ]);
        }
    }
    # To save the user review details ( function end )

    // Edit review by user -- start
    public function editReview(Request $request){
        $User = JWTAuth::authenticate($request->token);
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|integer',
            'review_user_id' => 'required|integer',
            'review_business_id' => 'required|integer',
            'rating' => 'required',
            'review_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{
            $edit_review = Review::find($request->review_id);
            $edit_review->review_user_id = $request->review_user_id;
            $edit_review->review_business_id = $request->review_business_id;
            $edit_review->rating = $request->rating;
            $edit_review->review_text = $request->review_text;
            $review_update = $edit_review->update();
            if($review_update == true){
                if($request->deleteReviewImages_id){ // check delete id array value exit or not
                    for($i=0; $i<count($request->deleteReviewImages_id); $i++){ // Image exit or not in folder
                        $reviewImages = ReviewImage::find($request->deleteReviewImages_id[$i]);
                        $path = public_path().'/'.$reviewImages->review_image_url;
                        if (File::exists($path)) {
                            unlink($path);
                        }
                        If(Storage::disk('s3')->exists($reviewImages->review_image_url)){
                            Storage::disk('s3')->exists($reviewImages->review_image_url);
                        }
                        $reviewImages->delete();
                    }
                }
                if($request->hasfile('review_imgs')){ // check images upload or not
                    $destinationPath = '/images/review_images';
                    foreach($request->review_imgs as $file){ // file upload from model
                        $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
                        $img = Image::make($file)->resize(1280, null,function($constraint){
                            $constraint->aspectRatio();
                            $constraint->upsize();
                            }); // resize
                        $final_path = $destinationPath."/".$real_name;
                        Storage::disk('s3')->put($final_path,$img->stream()->__toString());
                        $reviewImage = new ReviewImage;
                        $reviewImage->review_id = $request->review_id;
                        $reviewImage->review_image_url = $final_path;
                        $reviewImage->save();
                    }
                }
                $message = "Review updated successfully.";
            }else{
                $message = "Somethind is wrong.";
            }
            return response([
                'success' => true,
                'message' => $message,
                'data' => $review_update
            ]);
        }

    }
    // Edit review by user -- end

    # To delete the review by admin ( function start )
    public function deleteReviewByAdmin( Request $request ){
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
                $delete_review = Review::find($request->id);
                if( $delete_review == null){
                    return response([
                        'success' => false,
                        'message' => 'No data found related to this id.'
                    ],400);
                }else{
                    $delete_review->delete();
                }
                return response([
                    'success' => true,
                    'message' => 'Review deleted successfully.'
                ]);
            }
        }
    }
    # To delete the review by admin ( function end )

    # To delete the multiple reviews by admin ( function start )
    public function deleteMultipleReviews( Request $request ){
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
                    $delete_reviews= Review::find($id);
                    $delete_reviews->delete();
                }
                return response([
                    'success' => true,
                    'message' => 'Reviews deleted successfully.'
                ]);

            }
        }
    }
    # To delete the multiple reviews by admin ( function end )

        # To search the reviews data ( function start )
        public function searchReviewsData( Request $request ){

            $reviews = Review::query();

            $reviews->join('users','users.id','reviews.review_business_id')
            ->join('users as ur','ur.id','reviews.review_user_id')
            ->join('profiles','profiles.user_id','users.id')
            ->join('cities','cities.id','profiles.city_id')
            ->select('reviews.id',
                    'reviews.review_user_id',
                    'reviews.review_business_id',
                    'reviews.rating',
                     'reviews.review_text',
                     \DB::raw("DATE_FORMAT(reviews.created_at, '%Y-%m-%d') as formatted_created_at"),
                     'reviews.created_at',
                     'ur.name as reviewby_name',
                     'users.name as bname',
                     'users.mobile_phone as bphone',
                     'profiles.city_id',
                     'cities.city',
                    );

            if ($request->city != "") {
                $reviews->where('profiles.city_id',$request->city);
            }
            if ($request->business != "") {
                $reviews->where('review_business_id','=',$request->business);
            }
            if ($request->user != "") {
                $reviews->where('review_user_id','=',$request->user);
            }
            if ($request->rating != "") {
                $reviews->where('rating','LIKE',$request->rating.'%');
            }
            if( $request->date1 && $request->date2){
                $reviews->whereBetween(DB::raw("DATE_FORMAT(reviews.created_at, '%Y-%m-%d')"), [$request->date1, $request->date2]);
            }elseif( $request->date1 != "" ){
                $reviews->where(DB::raw("DATE_FORMAT(reviews.created_at, '%Y-%m-%d')"),$request->date1);
            }elseif( $request->date2 != "" ){
                $reviews->where(DB::raw("DATE_FORMAT(reviews.created_at, '%Y-%m-%d')"),$request->date2);
            }

            $searched_data = $reviews->get();

            return response()->json(
                [
                    'status' => 200,
                    'reviews' => $searched_data
                ],200
            );
        }
        # To search the reviews data ( function end )
}
