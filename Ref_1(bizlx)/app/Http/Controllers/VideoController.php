<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Galleryimage;
use App\Models\Profile;
use App\Models\Video;
use App\Models\Hotdeal;
use App\Models\Review;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Plan;
use App\Models\Wishlist;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use File;
use Psy\Readline\Hoa\Console;

class VideoController extends Controller
{
    public function getVideoAll(Request $request)
    {
        $videos = DB::table('videos')
        ->leftJoin('users', 'users.id', '=', 'videos.user_id') // Join with users table
        ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id') // Join with profiles table
        ->leftJoin('cities', 'cities.id', '=', 'profiles.city_id') // Join with cities table
        ->leftJoin('states', 'states.id', '=', 'profiles.state_id') // Join with states table
        ->leftjoin('countries','countries.id','states.country_id')
        ->leftJoin('businesses', 'businesses.business_id', '=', 'users.id') // Join with businesses table
        ->leftJoin('subcategories', 'subcategories.id', '=', 'businesses.sub_cat_id') // Join with subcategories table
        ->where('videos.video_status', '=', 1); // Only active videos

    // Add condition for subcategory if subcat_id is provided
    if ($request->subcat_id && $request->subcat_id != null) {
        $videos->where('businesses.sub_cat_id', $request->subcat_id);
    }

    // Add condition for city if city_id is provided
    if ($request->city_id && $request->city_id != null) {
        $videos->where('profiles.city_id', $request->city_id);
    }

    // Fetch videos
    $videos = $videos->orderBy('videos.id', 'DESC') // Order by video id in descending order
        ->select(
            'users.id as business_id',
            'profiles.id as profile_id',
            'users.name',
            'users.username',
            'users.mobile_phone',
            'countries.phonecode',
            'profiles.user_avatar',
            'profiles.address',
            'profiles.city_id',
            'cities.city',
            'profiles.state_id',
            'states.state',
            'businesses.sub_cat_id',
            'subcategories.subcat_name',
            DB::raw('DATE(users.created_at) as added_date'),
            // Video data
            'videos.id as video_id',
            'videos.video_title',
            'videos.video_slug',
            'videos.video_description',
            'videos.video_url',
            'videos.youtube_id'
        )
        ->get();

        // Process videos to add the thumbnail URL
        $videosWithThumbnails = $videos->map(function ($video) {
            $video->thumbnail_url = "https://img.youtube.com/vi/{$video->youtube_id}/hqdefault.jpg"; // High-quality thumbnail
            return $video;
        });


        // Return the data as a JSON response
        return response()->json([
            'result' => 200,
            'message' => 'All video and related user data fetched successfully.',
            'videos' => $videos,
            'videosWithThumbnails' => $videosWithThumbnails // Including processed video thumbnails
        ]);
    }


    public function getVideoDetail($video_slug, $loggedUser_id)
    {
        $video = Video::where('video_slug',$video_slug)->first();

        $video_id = $video->id;
        $user_video_wishlist = Wishlist::Where('services_id', $video_id)->where('user_id', '=', $loggedUser_id)->first();
        if($user_video_wishlist==null){
            $user_video_wishlist = null;
        }

        $videoDetails = Video::leftJoin('users', 'users.id', 'videos.user_id')
            ->leftJoin('profiles', 'profiles.user_id', 'videos.user_id')
            ->leftJoin('cities', 'cities.id', 'profiles.city_id')
            ->leftJoin('states', 'states.id', 'profiles.state_id')
            ->join('countries','countries.id','states.country_id')
            ->select('videos.id as video_id', 'videos.user_id', 'videos.video_title','videos.video_slug', 'videos.video_description', 'videos.video_url',
                    'videos.youtube_id', 'videos.video_status', 'users.id as business_id', 'users.name', 'users.username', 'users.email', 'users.mobile_phone', 'profiles.city_id',
                    'cities.id as city_id', 'cities.city', 'profiles.state_id', 'states.state','countries.country','countries.phonecode')
            ->where('videos.id', '=', $video_id)
            ->first();
        $videoDetails['user_video_wishlist'] = $user_video_wishlist;
            $videoReview = Review::select('review_business_id', 'rating')->where('review_business_id', '=', $videoDetails->user_id)->get();
            $videoDetails['total_reviews'] = $videoReview->count();
            $videoDetails['total_ratings'] = $videoReview->sum('rating');
            if ($videoDetails['total_ratings'] > 0) {
                $videoDetails['rating_avrage'] = round($videoDetails['total_ratings'] / $videoDetails['total_reviews']);
            } else {
                $videoDetails['rating_avrage'] = 0;
            }

        $allRelatedVideo = Profile::Join('users', 'users.id', 'profiles.user_id')
            ->Join('videos', 'videos.user_id', 'profiles.user_id')
            ->join('cities', 'cities.id', 'profiles.city_id')
            ->join('states', 'states.id', 'profiles.state_id')
            ->select('videos.id as video_id', 'videos.user_id', 'videos.video_title','videos.video_slug', 'videos.video_description', 'videos.video_url',
                'videos.youtube_id', 'videos.video_status', 'users.name', 'users.username', 'users.email', 'users.mobile_phone', 'profiles.address', 'profiles.city_id',
                'cities.city', 'profiles.state_id', 'states.state')
            ->where('profiles.city_id', '=', $videoDetails->city_id)
            ->where('videos.id', '!=', $videoDetails->video_id)
            // ->groupBy('profiles.user_id')
            ->get();

            for($i=0; $i < count($allRelatedVideo); $i++) { // Review, Rating & rating avrage
                $user_video_wishlist = Wishlist::Where('services_id', $allRelatedVideo[$i]->video_id)->where('user_id', '=', $loggedUser_id)->first();
                if($user_video_wishlist==null){
                    $allRelatedVideo[$i]['user_video_wishlist'] = null;
                }else{
                    $allRelatedVideo[$i]['user_video_wishlist'] = $user_video_wishlist;
                }

                $allReviews = Review::select('review_business_id', 'rating')->where('review_business_id', '=', $allRelatedVideo[$i]->user_id)->get();
                $allRelatedVideo[$i]['total_reviews'] = $allReviews->count();
                $allRelatedVideo[$i]['total_ratings'] = $allReviews->sum('rating');

                if ($allRelatedVideo[$i]['total_ratings'] > 0) {
                    $allRelatedVideo[$i]['rating_avrage'] = round($allRelatedVideo[$i]['total_ratings'] / $allRelatedVideo[$i]['total_reviews']);
                } else {
                    $allRelatedVideo[$i]['rating_avrage'] = 0;
                }
            }

        return response()->json(
            [
                'result' => 200,
                'message' => 'Video profile data get with related hot deals.',
                'videoProfile' => $videoDetails,
                'nearByHotDealsVideos' => $allRelatedVideo,
            ]
        );
    }

    # To add video (function start)
    public function addVideo(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('video_title','video_url','youtube_link');
            $validator = Validator::make($data, [
                'video_title' => 'required|string',
                'video_url' => 'required|string',
                'youtube_link' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $user_id = $User->id;
                $business_data = Business::Where('business_id', $user_id)->first();
                $user_plan_id = $business_data->plan_id;
                $business_plan = Plan::Where('id', $user_plan_id)->first();
                $video_data = Video::Where('user_id', $user_id)->get();
                // dd($business_plan->video,count($video_data));

                if (count($video_data) > 0) {
                    if (count($video_data) == $business_plan->video) {
                        return response()->json(
                            [
                                'message' => "You have already added " . $video_data->count() . ' videos',
                            ],
                            200
                        );
                    } else {
                        $video = new Video();
                        $video->user_id = $user_id;
                        $video->video_title = $request->video_title;
                        $video->video_slug = strtolower(Str::slug($request->video_title,'-')).'-'.Str::random(4);
                        $video->video_description = $request->video_description;
                        $video->video_url = $request->video_url;
                        $link = $request->youtube_link;
                        $video_id = explode("?v=", $link);
                        $final_id = $video_id[1];
                        $video->youtube_id = $final_id;
                        $video->save();
                        return response()->json(
                            [
                                'message' => "Video Added Successfully.",
                            ],
                            200
                        );
                    }
                } else {
                    $video = new Video();
                    $video->user_id = $user_id;
                    $video->video_title = $request->video_title;
                    $video->video_slug = strtolower(Str::slug($request->video_title,'-')).'-'.Str::random(4);
                    $video->video_description = $request->video_description;
                    $video->video_url = $request->video_url;
                    $link = $request->youtube_link;
                    $video_id = explode("?v=", $link);
                    $final_id = $video_id[1];
                    $video->youtube_id = $final_id;
                    $video->save();
                    return response()->json(
                        [
                            'message' => "Video Added Successfully.",
                        ],
                        200
                    );
                }
            }
        }
    }
    # To add video (function end)

    # To add video by admin(function start)
    public function addVideobyAdmin(Request $request)
    {
        $User = $request->user_id;
        $data = $request->only('video_title','video_url','youtube_link');
        $validator = Validator::make($data, [
            'video_title' => 'required|string',
            'video_url' => 'required|string',
            'youtube_link' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        } else {
            $user_id = $User;
            $business_data = Business::Where('business_id', $user_id)->first();
            $user_plan_id = $business_data->plan_id;
            $business_plan = Plan::Where('id', $user_plan_id)->first();
            $video_data = Video::Where('user_id', $user_id)->get();
            // dd($business_plan->video,count($video_data));

            if (count($video_data) > 0) {
                if (count($video_data) == $business_plan->video) {
                    return response()->json(
                        [
                            'message' => "You have already added " . $video_data->count() . ' videos',
                        ],
                        200
                    );
                } else {
                    $video = new Video();
                    $video->user_id = $user_id;
                    $video->video_title = $request->video_title;
                    $video->video_slug = strtolower(Str::slug($request->video_title,'-')).'-'.Str::random(4);
                    $video->video_description = $request->video_description;
                    $video->video_url = $request->video_url;
                    $link = $request->youtube_link;
                    $video_id = explode("?v=", $link);
                    $final_id = $video_id[1];
                    $video->youtube_id = $final_id;
                    $video->save();
                    return response()->json(
                        [
                            'message' => "Video Added Successfully.",
                        ],
                        200
                    );
                }
            } else {
                $video = new Video();
                $video->user_id = $user_id;
                $video->video_title = $request->video_title;
                $video->video_slug = strtolower(Str::slug($request->video_title,'-')).'-'.Str::random(4);
                $video->video_description = $request->video_description;
                $video->video_url = $request->video_url;
                $link = $request->youtube_link;
                $video_id = explode("?v=", $link);
                $final_id = $video_id[1];
                $video->youtube_id = $final_id;
                $video->save();
                return response()->json(
                    [
                        'message' => "Video Added Successfully.",
                    ],
                    200
                );
            }
        }
    }
    # To add video  by admin(function end)

    # To update video (function start)
    public function updateVideo(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('video_id','video_title','video_url','youtube_link');
            $validator = Validator::make($data, [
                'video_id' => 'required|integer',
                'video_title' => 'required|string',
                'video_url' => 'required|string',
                'youtube_link' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $user_id = $User->id;
                $video = Video::find($request->video_id);
                $video->user_id = $user_id;
                $video->video_title = $request->video_title;
                $video->video_slug = strtolower(Str::slug($request->video_title,'-')).'-'.Str::random(4);
                $video->video_description = $request->video_description;
                $video->video_url = $request->video_url;
                $link = $request->youtube_link;
                $video_id = explode("?v=", $link);
                $final_id = $video_id[1];
                $video->youtube_id = $final_id;
                    if( $request->video_status == "true" ){
                        $video->video_status = 1;
                    }elseif( $request->video_status == "1" ){
                        $video->video_status = 1;
                    }else{
                        $video->video_status = 0;
                    }
                $video->update();
                return response()->json(
                    [
                        'message' => "Video updated Successfully.",
                    ],
                    200
                );
            }
        }
    }
    # To update video (function end)

    # To update video (function start)
    public function updateVideobyAdmin(Request $request)
    {
        $User = $request->user_id;
            $data = $request->only('video_id','video_title','video_url','youtube_link');
            $validator = Validator::make($data, [
                'video_id' => 'required|integer',
                'video_title' => 'required|string',
                'video_url' => 'required|string',
                'youtube_link' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $user_id = $User;
                $video = Video::find($request->video_id);
                $video->user_id = $user_id;
                $video->video_title = $request->video_title;
                $video->video_slug = strtolower(Str::slug($request->video_title,'-')).'-'.Str::random(4);
                $video->video_description = $request->video_description;
                $video->video_url = $request->video_url;
                $link = $request->youtube_link;
                $video_id = explode("?v=", $link);
                $final_id = $video_id[1];
                $video->youtube_id = $final_id;
                    if( $request->video_status == "true" ){
                        $video->video_status = 1;
                    }elseif( $request->video_status == "1" ){
                        $video->video_status = 1;
                    }else{
                        $video->video_status = 0;
                    }
                $video->update();
                return response()->json(
                    [
                        'message' => "Video updated Successfully.",
                    ],
                    200
                );
            }
    }
    # To update video (function end)

    # To delete video (function start)
    public function deleteVideo(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('video_id');
            $validator = Validator::make($data, [
                'video_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $video = Video::find($request->video_id);
                if (public_path() . '/' . $video->video_url) {
                    File::delete(public_path() . '/' . $video->video_url);
                }
                $video->delete();
                return response()->json(
                    [
                        'message' => "Video deleted Successfully.",
                    ],
                    200
                );
            }
        }
    }
    # To delete video (function end)

    public function businessVideosGet(Request $request){
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

        $allVideos = Video::Join('businesses', 'businesses.business_id', 'videos.user_id')
            ->select('videos.id as video_id',
                'videos.user_id',
                'videos.video_title',
                'videos.video_description',
                'videos.video_url',
                'videos.youtube_id',
                'videos.video_slug',
                'videos.video_status')
            ->where('videos.user_id', $userData->user_id)
            ->orderBy('video_id', 'DESC')
            ->distinct()
            ->get();
        return response()->json([
            'status' => 200,
            'message' => "All videos data of busines.",
            'user' => $userData,
            'allVideos' => $allVideos,
        ]);

    }

    public function businessVideosGetbyAdmin(Request $request){
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

        $allVideos = Video::Join('businesses', 'businesses.business_id', 'videos.user_id')
            ->select('videos.id as video_id',
                'videos.user_id',
                'videos.video_title',
                'videos.video_description',
                'videos.video_url',
                'videos.youtube_id',
                'videos.video_slug',
                'videos.video_status')
            ->where('videos.user_id', $userData->user_id)
            ->orderBy('video_id', 'DESC')
            ->distinct()
            ->get();
        return response()->json([
            'status' => 200,
            'message' => "All videos data of busines.",
            'user' => $userData,
            'allVideos' => $allVideos,
        ]);
    }

}
