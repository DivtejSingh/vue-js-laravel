<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;

use App\Models\Business;
use App\Models\Category;
use App\Models\City;
use App\Models\CountryByCurrencyCode;
use App\Models\Hotdeal;
use App\Models\Job;
use App\Models\Page;
use App\Models\Pagecolumn;
use App\Models\Pagecontent;
use App\Models\Review;
use App\Models\Profile;
use App\Models\Sale;
use App\Models\Subcategory;
use App\Models\Tab;
use App\Models\Timezone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Locale;
use App\Models\Setting;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use File;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;

use App\Models\Galleryimage;
class AdminController extends Controller
{
    // Super Admin login(function start)
    public function adminloginPage()
    {
        $title = 'Admin Login';
        return view('admin.login',compact('title'));
    }
    public function userGalleryImagesbyreseller(Request $request){
        $User = $request->userId;
        if(!$User){
            return response([
                                'success' => false,
                                'message' => 'User not valid!'
                            ]);
        }else{
            $get_gallery_images = Galleryimage::where('user_id',$User)->where('image_type',0)->get();
            return response([
                                'success' => true,
                                'gallery_images' => $get_gallery_images
                            ]);
        }
    }
    public function upload(Request $request): JsonResponse
    {
        // 1) validate
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'image'   => 'required|image|mimes:jpeg,png,jpg,webp',
            'title'   => 'required|string|max:255',
            'price'   => 'required|integer|min:0',
        ]);

        // enforce “max 5 gallery images” per user
        $existing = Galleryimage::where('user_id', $data['user_id'])
                                ->where('image_type', 0)
                                ->count();

        if ($existing >= 5) {
            return response()->json([
                'message' => 'You are allowed up to 5 gallery images only.',
            ], 422);
        }

        // 2) process the upload
        $file = $request->file('image');

        $img = Image::make($file)
                    ->resize(1280, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->stream(); // Intervention Image stream

        // unique file name
        $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path     = "images/gallery_images/{$filename}";

        // 3) send to S3 (no ACL header!)
        Storage::disk('s3')->put($path, $img->__toString());

        // 4) save the record
        Galleryimage::create([
            'user_id'          => $data['user_id'],
            'image_type'       => 0,
            'image_url'        => $path,
            'image_title'      => $data['title'],
            'price'            => $data['price'],
        ]);

        return response()->json([
            'message' => 'Gallery image added successfully.',
        ], 201);
    }

        
    
    public function adminLogin(Request $request)
    {
        $credentials = $request->only('email', 'password', 'user_role');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50',
            'user_role' => 'required'
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $admin = User::Where('email', $request->email)->first();
        //Request is validated
        //Crean token
        try {
            if ( ! $token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Login credentials are invalid, or you are not a admin',
                    ],
                    400
                );
            }
        } catch (JWTException $e) {
            // return $credentials;
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Could not create token.',
                ],
                500
            );
        }

        //Token created, return with success response and jwt token
        return response()->json(
            [
                'success' => true,
                'token' => $token,
                'user' => $admin,
            ],
            200
        );
    }
    // Super Admin login(function end)

    // Sub Admin login(function start)
    public function subadminLogin(Request $request)
    {
        $credentials = $request->only('email', 'password', 'user_role');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50',
            'user_role' => 'required'
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $subadmin = User::Where('email', $request->email)->first();
        //Request is validated
        //Crean token
        try {
            if ( ! $token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Login credentials are invalid, or you are not a admin',
                    ],
                    400
                );
            }
        } catch (JWTException $e) {
            // return $credentials;
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Could not create token.',
                ],
                500
            );
        }

        //Token created, return with success response and jwt token
        return response()->json(
            [
                'success' => true,
                'token' => $token,
                'user' => $subadmin,
            ],
            200
        );
    }
    // Sub Admin login(function end)

    # To get required information for tabs selction dropdowns ( function start )
    public function categoriesData( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }else{
            $categories = Category::where('cat_status','1')->select('id','cat_name')->get();
            $sub_categories = Subcategory::where('subcat_status','1')->select('id','cat_id','subcat_name')->get();
            return response([
                'success' => true,
                'categories' =>$categories,
                'subcategories' => $sub_categories
            ]);
        }
    }
    # To get required information for tabs selction dropdowns ( function end )

    # To save the tab details ( function start )
    public function addTab( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('title', 'category','subcategory');
            $validator = Validator::make($data, [
                'title' => 'required|string',
                'category' => 'required|integer',
                'subcategory' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $add_tab = new Tab();
                $add_tab->title = $request->title;
                $add_tab->cat_id = $request->category;
                $add_tab->sub_cat_id = implode(",",$request->subcategory);
                $add_tab->save();
                return response([
                    'success' => true,
                    'message' => 'Tab saved successfully.',
                ]);
            }
        }
    }
    # To save the tab details ( function end )

    # To get all the tabs data ( function start )
    public function Tabs( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $tabs = Tab::leftjoin('categories','tabs.cat_id','=','categories.id')
            ->select(
                'tabs.*',
                'categories.id as cat_id',
                'categories.cat_name',
                )->get();

                $tabs_count = count($tabs);
                for ( $i = 0; $i < $tabs_count; $i++ ){
                    $subcat_names = [];
                    $exploded_ids = explode(",",$tabs[$i]['sub_cat_id']);
                    foreach ( $exploded_ids as $id ){
                        $subcat_data = Subcategory::find($id);
                        $subcat_names[] = $subcat_data->subcat_name;
                    }
                    $tabs[$i]['subcatnames'] = implode(",",$subcat_names);
                }
            return response([
                'success' =>true,
                'tabs' => $tabs
            ]);
        }
    }
    # To get all the tabs data ( function end )

    # To update the tab details ( function start )
    public function updateTab( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('id','title', 'cat_id');
            $validator = Validator::make($data, [
                'id' => 'required|integer',
                'title' => 'required|string',
                'cat_id' => 'required|integer',
                // 'subcat_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $update_tab = Tab::find($request->id);
                if( $update_tab != null ){
                    $update_tab->sub_cat_id = null;
                    $update_tab->update();
                }
                $update_tab->title = $request->title;
                $update_tab->cat_id = $request->cat_id;
                $update_tab->sub_cat_id = implode(",",$request->subcat_id);

                if( $request->tab_status == "true" ){
                    $update_tab->tab_status = '1';
                }elseif( $request->tab_status == 1 ){
                    $update_tab->tab_status = '1';
                }else{
                    $update_tab->tab_status = '0';
                }
                $update_tab->update();
                return response([
                    'success' => true,
                    'message' => 'Tab updated successfully.'
                    // 'message' => $request->all()
                ]);
            }
        }
    }
    # To update the tab details ( function end )

    # To delete the tab ( function start )
    public function deleteTab( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('id');
            $validator = Validator::make($data, [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $delete_tab = Tab::find($request->id);
                $delete_tab->delete();
                return response([
                    'success' => true,
                    'message' => 'Tab deleted successfully.'
                ]);
            }
        }
    }
    # To delete the tab ( function end )

    # To delete the multiple tabs ( function start )
    public function deleteMultipleTabs( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('ids');
            $validator = Validator::make($data, [
                'ids' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                Tab::destroy($request->ids);
                return response([
                    'success' => true,
                    'message' => 'Tabs deleted successfully.'
                ]);
            }
        }
    }
    # To delete multiple tabs ( function end )

    # To get subcategory ids ( function start )
    public function getSubIds( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('id');
            $validator = Validator::make($data, [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $tab = Tab::find($request->id);
                $ids = explode(",",$tab->sub_cat_id);
                $subcats = Subcategory::whereIn('id',$ids)
                ->select('id','subcat_name')
                ->get();
                return response([
                    'success' => true,
                    'subcatids' => $subcats
                ]);
            }
        }
    }
    # To get subcategory ids ( function end )

    # To get the columns information ( function start )
    public function colummns( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $columns = Pagecolumn::all();
            return response([
                'success' => true,
                'columns' => $columns
            ]);
        }
    }
    # To get the columns information ( function end )
    public function savePage(Request $request) {
        $User = JWTAuth::authenticate($request->token);
    
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }
    
        $save = new Page();
        $save->column_id = $request->column_id;
        $save->page_name = $request->page_name;
        $save->page_link = $request->page_link;
    
        $save->status = ($request->status === true || $request->status === '1') ? '1' : '0';
        $save->page_status = ($request->page_status === true || $request->page_status === '1') ? '1' : '0';
    
        $save->save();
    
        // Save content to `pagecontents` table
        DB::table('pagecontents')->insert([
            'page_id' => $save->id,
            'content' => $request->content ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return response([
            'success' => true,
            'message' => 'Page saved successfully'
        ]);
    }
    
    # To save the details of page ( function start )
    // public function savePage( Request $request ){
    //     $User = JWTAuth::authenticate($request->token);
    //     if (!$User) {
    //         return response([
    //             'success' => false,
    //             'message' => 'User not found !'
    //         ]);
    //     } else {
    //         $save = new Page();
    //         $save->column_id = $request->column_id;
    //         $save->page_name = $request->page_name;
    //         $save->page_link = $request->page_link;

    //         if( $request->status == true ){
    //             $save->status = '1';
    //         }elseif( $request->status == '1' ){
    //             $save->status = '1';
    //         }else{
    //             $save->status = '0';
    //         }

    //         if( $request->page_status == true ){
    //             $save->page_status = '1';
    //         }elseif( $request->page_status == '1' ){
    //             $save->page_status = '1';
    //         }else{
    //             $save->page_status = '0';
    //         }
    //         $save->save();
    //         return response([
    //             'success' => true,
    //             'message' => 'Page saved successfully'
    //         ]);
    //     }
    // }
    # To save the details of page ( function end )

    # To update the details of page ( function start )
    // public function updatePageDetails( Request $request ){
    //     $User = JWTAuth::authenticate($request->token);
    //     if (!$User) {
    //         return response([
    //             'success' => false,
    //             'message' => 'User not found !'
    //         ]);
    //     } else {
    //         $update = Page::find($request->page_id);
    //         $update->column_id = $request->column_id;
    //         $update->page_name = $request->page_name;
    //         $update->page_link = $request->page_link;

    //         if( $request->status == true ){
    //             $update->status = '1';
    //         }elseif( $request->status == '1' ){
    //             $update->status = '1';
    //         }else{
    //             $update->status = '0';
    //         }

    //         if( $request->page_status == true ){
    //             $update->page_status = '1';
    //         }elseif( $request->page_status == '1' ){
    //             $update->page_status = '1';
    //         }else{
    //             $update->page_status = '0';
    //         }
    //         $update->update();
    //         return response([
    //             'success' => true,
    //             'message' => 'Page updated successfully'
    //         ]);
    //     }
    // }
    # To save the details of page ( function end )
    public function updatePageDetails(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }
    
        $update = Page::find($request->id);
        if (!$update) {
            return response([
                'success' => false,
                'message' => 'Page not found!'
            ]);
        }
    
        $update->column_id = $request->column_id;
        $update->page_name = $request->page_name;
        $update->page_link = $request->page_link;
        $update->page_title = $request->page_title;
        $update->page_des = $request->page_des;
        // $update->page_ogimage = $request->page_ogimage;

        if ($request->hasfile('page_ogimage')) {

            $destinationPath = '/images/og_images/';
            $path = public_path().$destinationPath;
            if(!File::exists($path)) {
                File::makeDirectory($path, 0777, true); //make dir
            }

            if ($update->page_ogimage != null) {
                if(Storage::disk('s3')->exists($destinationPath.$update->page_ogimage)){
                    Storage::disk('s3')->delete($destinationPath.$update->page_ogimage);
                }
                if (file_exists(public_path() . '/' . $update->page_ogimage)) {
                    unlink(public_path() . '/' . $update->page_ogimage);
                }

                $file = $request->page_ogimage;
                $real_name = uniqid().'.'.$file->getClientOriginalExtension(); // type change
                $img = Image::make($file->getRealPath())->resize('1200','630', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
                $final_path = $destinationPath.$real_name;
                Storage::disk('s3')->put($final_path, $img->stream()->__toString());
                $update->page_ogimage = $final_path;

            }else{
                $file = $request->page_ogimage;
                $real_name = uniqid().'.'.$file->getClientOriginalExtension(); // type change
                $img = Image::make($file->getRealPath())->resize('1200','630', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
                $final_path = $destinationPath.$real_name;
                Storage::disk('s3')->put($final_path, $img->stream()->__toString());
                $update->page_ogimage = $final_path;
            }
        }
        $update->status = ($request->status == true || $request->status == '1') ? '1' : '0';
        $update->page_status = ($request->page_status == true || $request->page_status == '1') ? '1' : '0';
        $update->update();
    
        // Update or Insert into pagecontents table
        $existingContent = DB::table('pagecontents')->where('page_id', $update->id)->first();
    
        if ($existingContent) {
            // Update
            DB::table('pagecontents')->where('page_id', $update->id)->update([
                'content' => $request->content ?? '',
                'updated_at' => now()
            ]);
        } else {
            // Insert
            DB::table('pagecontents')->insert([
                'page_id' => $update->id,
                'content' => $request->content ?? '',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    
        return response([
            'success' => true,
            'message' => 'Page updated successfully'
        ]);
    }
    

    # To get all saved pages list ( function start )
    public function allPages( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $pages = Page::all();
            return response([
                'success' => true,
                'pages' => $pages
            ]);
        }
    }
    # To get all saved pages list ( function end )

    # To delete the page ( function start )
    public function deletePage( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('id');
            $validator = Validator::make($data, [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $page = Page::find($request->id);
                $page->delete();
                return response([
                    'success' => true,
                    'message' => 'Page deleted successfully.'
                ]);
            }
        }
    }
    # To delete the page ( function end )

    # To update the page details ( function start )
    public function getPageDetail( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('id');
            $validator = Validator::make($data, [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $page = Page::leftjoin('pagecolumns','pages.column_id','=','pagecolumns.id')
                ->select('pages.*','pagecolumns.columns_name')
                ->where('pages.id',$request->id)
                ->first();
                return response([
                    'success' => true,
                    'page' => $page
                ]);
            }
        }
    }
    # To update the page details ( function end )

    # To delete multiple pages ( function start )
    public function deleteMultiplePages( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('ids');
            $validator = Validator::make($data, [
                'ids' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                Page::destroy($request->ids);
                return response([
                    'success' => true,
                    'message' => 'Pages deleted successfully.'
                ]);
            }
        }
    }
    # To delete multiple pages ( function end )

    # To get all footer details ( function start )
    public function getFooterDetails( Request $request ){
        $footer_details = Pagecolumn::with('pages')->get();
        return response([
            'success' => true,
            'footer_details' => $footer_details
        ]);
    }
    # To get all footer details ( function end )

    # To get all the pages details ( function start )
    public function getPages( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $pages = Page::all();
            return response([
                'success' => true,
                'pages' => $pages
            ]);
        }
    }
    # To get all the pages details ( function end )

    # To get page content ( function start )
    public function getPageContent(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found!'
            ]);
        } else {
            // Get the 'id' from the query string
            $data = $request->only('id');
            $validator = Validator::make($data, [
                'id' => 'required|integer'
            ]);
            
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $pageContent = Pagecontent::where('page_id', $request->id)->first();
                if ($pageContent == null) {
                    $final_content = ''; // If no content found, return an empty string
                } else {
                    $final_content = $pageContent->content; // Get the content from the database
                }
                return response([
                    'success' => true,
                    'content' => $final_content
                ]);
            }
        }
    }
    
    # To get page content ( function end )

    # To update page content ( function start )
    public function updatePageContent( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('id');
            $validator = Validator::make($data, [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $pageContent = Pagecontent::where('page_id',$request->id)->first();
                if( $pageContent == null ){
                    $save_content = new Pagecontent();
                    $save_content->page_id = $request->id;
                    $save_content->content = $request->content;
                    $save_content->save();
                    return response([
                        'success' => true,
                        'message' => 'Page content saved successfully',
                        'content' => $pageContent
                    ]);
                }else{
                        $pageContent = Pagecontent::where('page_id',$request->id)->first();

                        Pagecontent::where('page_id',$request->id)->update([
                            'content' => $request->content
                        ]);
                        return response([
                            'success' => true,
                            'message' => 'Page content updated successfully.',
                            'content' => $pageContent
                        ]);
                }


            }
        }
    }
    # To update page content ( function end )

    # To get Admin Profile ( function start )
    public function getadminprofile(Request $request) {
        try {
            // Authenticate the user
            $User = JWTAuth::authenticate($request->token);
            if (!$User) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }
    
            // Fetch admin profile where user_role = 4
            $buser1 = User::leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
                ->select('users.*', 'profiles.user_avatar')
                ->where('users.id', $User->id)  // Ensure you only fetch the logged-in user
                ->where('users.user_role', '4')
                ->first(); // Fetch the first record
    
            // Check if user with role=4 exists
            if (!$buser1) {
                return response()->json(['message' => 'Admin not found or not authorized'], 403);
            }
    
            return response()->json([
                'message' => 'Admin profile found',
                'Adminprofile' => $buser1,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching profile',
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    # To get Admin Profile ( function end )

    # To get Admin Profile Update ( function start )
    public function adminUpdateprofile(Request $request){

        $User = JWTAuth::authenticate($request->token);
        $buser_id = $User->id;
        $data = $request->only('name', 'email');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{
            $update_admin = User::find($buser_id);
            $update_admin->name = $request->name;
            $update_admin->email = $request->email;
            $update_admin->update();

            $update_adminavatar = Profile::Where('user_id', $buser_id)->first();
            if($request->file('user_avatar')){
                $call_function = new Profile();
                $file = $request->file('user_avatar');
                $old_file = $update_adminavatar->user_avatar;
                $update_adminavatar->user_avatar = $call_function->avatar($old_file,$file);
                $update_adminavatar->update();
            }
            return response()->json(
                [
                    'adminupdateprofile' => 'Admin Profile Updated Successfully.'
                ],200);
        }
    }
    # To get Admin Profile Update ( function end )

    public function updateadminPassword(Request $request){

        $User = JWTAuth::authenticate($request->token);
        $user_id = $User->id;
        if($User){
            $update_password = User::find($user_id);
            $password = Hash::make($request->password);
            $update_password->password = $password;
            $update_password->update();

            return response()->json(
                [
                    'adminpassword' => 'Admin Password Updated Successfully.'
                ],200);
        }
        else{
            return response()->json(
                [
                    'adminpassword' => 'Admin Password Not Updated!.'
                ],400);
        }
    }

    # To get all the details of locales , timezones and currency code ( function start )
    public function getLocalesTimezonesAndCurrencies( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if($User){
            $locales = Locale::all();
            $timezones = Timezone::all();
            $currencies = CountryByCurrencyCode::all();
            return response([
                'success' => true,
                'locales' => $locales,
                'timezones' => $timezones,
                'currencies' => $currencies,
            ]);
        }
        else{
            return response([
                'success' => false,
                'message' => 'User invalid!'
            ]);
        }
    }
    # To get all the details of locales , timezones and currency code ( function end )

    # To get profile details of superAdmin ( function start )
    public function getSuperAdminDetails( Request $request ){
            $get_super_admin_details = Setting::leftjoin('locales','settings.locale_id','=','locales.id')
            ->leftjoin('timezones','settings.timezone_id','=','timezones.id')
            ->leftjoin('country_by_currency_codes','settings.currency_id','=','country_by_currency_codes.id')
            ->select('settings.*','locales.id as locale_id','locales.locale_name','timezones.id as timezone_id','timezones.timezone_name','country_by_currency_codes.id as currency_id','country_by_currency_codes.currency_code')
            ->first();
            return response([
                'success' => true,
                'profile_details' => $get_super_admin_details
            ]);
        }
    # To get profile details of superAdmin ( function end )

    # To update the super admin profile details ( function start )
    public function updateSuperAdminProfileDetails( Request $request ){
        $User = JWTAuth::authenticate($request->token);
        if($User){
            $update = Setting::find($request->id);
            if( $request->locale_id ){
                $update->locale_id = $request->locale_id;
            }

            if( $request->timezone_id ){
                $update->timezone_id = $request->timezone_id;
            }

            if( $request->currency_id ){
                $update->currency_id = $request->currency_id;
            }


            # If request has files then ( code start )

            if ($request->hasfile('business_reg_img')) {

                $destinationPath = '/images/registration_images/';
                $path = public_path().$destinationPath;
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true); //make dir
                }

                if ($update->business_reg_img != null) {
                    if(Storage::disk('s3')->exists($destinationPath.$update->business_reg_img)){
                        Storage::disk('s3')->delete($destinationPath.$update->business_reg_img);
                    }
                    if (file_exists(public_path() . '/' . $update->business_reg_img)) {
                        unlink(public_path() . '/' . $update->business_reg_img);
                    }

                    $file = $request->business_reg_img;
                    $real_name = uniqid().'.'.$file->getClientOriginalExtension(); // type change
                    $img = Image::make($file->getRealPath())->resize('1280','null', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
                    $final_path = $destinationPath.$real_name;
                    Storage::disk('s3')->put($final_path, $img->stream()->__toString());
                    $update->business_reg_img = $final_path;

                }else{
                    $file = $request->business_reg_img;
                    $real_name = uniqid().'.'.$file->getClientOriginalExtension(); // type change
                    $img = Image::make($file->getRealPath())->resize('1280','null', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
                    $final_path = $destinationPath.$real_name;
                    Storage::disk('s3')->put($final_path, $img->stream()->__toString());
                    $update->business_reg_img = $final_path;
                }
            }

            if ($request->hasfile('reseller_reg_img')) {

                $destinationPath = '/images/registration_images/';
                $path = public_path().$destinationPath;
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true); //make dir
                }

                if ($update->reseller_reg_img != null) {
                    if(Storage::disk('s3')->exists($destinationPath.$update->reseller_reg_img)){
                        Storage::disk('s3')->delete($destinationPath.$update->reseller_reg_img);
                    }
                    if (file_exists(public_path() . '/' . $update->reseller_reg_img)) {
                        unlink(public_path() . '/' . $update->reseller_reg_img);
                    }
                    $file = $request->reseller_reg_img;
                    $real_name = uniqid().'.'.$file->getClientOriginalExtension(); // type change
                    $img = Image::make($file->getRealPath())->resize('1280','null', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
                    $final_path = $destinationPath.$real_name;
                    Storage::disk('s3')->put($final_path, $img->stream()->__toString());
                    $update->reseller_reg_img = $final_path;

                }else{
                    $file = $request->reseller_reg_img;
                    $real_name = uniqid().'.'.$file->getClientOriginalExtension(); // type change
                    $img = Image::make($file->getRealPath())->resize('1280','null', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
                    $final_path = $destinationPath.$real_name;
                    Storage::disk('s3')->put($final_path, $img->stream()->__toString());
                    $update->reseller_reg_img = $final_path;
                }
            }
            # If request has files then ( code end )

            if( $request->address ){
                $update->address = $request->address;
            }
            if( $request->contact_number ){
                $update->contact_number = $request->contact_number;
            }
            if( $request->business_status ){
                if( $request->business_status == true ){
                    $update->business_status = '1';
                }elseif( $request->business_status == '1' ){
                    $update->business_status = '1';
                }else{
                    $update->business_status = '0';
                }
            }

            if( $request->reseller_status ){
                if( $request->reseller_status == true ){
                    $update->reseller_status = '1';
                }elseif( $request->reseller_status == '1' ){
                    $update->reseller_status = '1';
                }else{
                    $update->reseller_status = '0';
                }
            }

            $update->update();
            return response([
                'success' => true,
                'message' => 'Details updated successfully.'
            ]);
        }
        else{
            return response([
                'success' => false,
                'message' => 'User invalid!'
            ]);
        }
    }
    # To update the super admin profile details ( function end )

    public function dealReports(Request $request)
    {
        $currentDate = date('Y-m-d');
        $type = strtolower($request->type);
        $searchType = null;
        $maxPrice = null;
        $allwords = $request->searchy;
        $words = explode(' ', $allwords);
        $searchy = array_filter($words);
        $country_id = 1;
        $searched = User::query();
        $searched->leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('countries','countries.id','states.country_id')
            ->leftjoin('businesses', 'businesses.business_id', 'users.id')
            ->leftjoin('galleryimages', 'galleryimages.user_id', 'users.id')
            ->leftjoin('subcategories', 'subcategories.id', 'businesses.sub_cat_id');
        if(!empty($request->subcat_id)){ // sub category
            $searchType = 'sub-category';
            $searched->where('businesses.sub_cat_id', $request->subcat_id)
                ->select(
                    'users.id as business_id',
                    'profiles.id as profile_id',
                    'users.name',
                    'users.username',
                    'users.mobile_phone',
                    'profiles.user_avatar',
                    'profiles.address',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                    'countries.id as country_id',
                    'countries.country',
                    'businesses.sub_cat_id',
                    'subcategories.subcat_name',
                    DB::raw("DATE_FORMAT(users.created_at, '%d-%m-%Y') as added_date"),
                );
        }
        if(!empty($request->city_id)){ // City
            $searchType = 'city';
            $searched->where('profiles.city_id', $request->city_id)
                ->select(
                    'users.id as business_id',
                    'profiles.id as profile_id',
                    'users.name',
                    'users.username',
                    'users.mobile_phone',
                    'profiles.user_avatar',
                    'profiles.address',
                    'profiles.city_id',
                    'cities.city',
                    'countries.id as country_id',
                    'countries.country',
                    'profiles.state_id',
                    'states.state',
                    'businesses.sub_cat_id',
                    'subcategories.subcat_name',
                    DB::raw("DATE_FORMAT(users.created_at, '%d-%m-%Y') as added_date"),
                );
        }
        if($type == 0){
            $searchType = 0;
            $searched->leftjoin('hotdeals', 'hotdeals.business_id', 'profiles.id')
                ->with('hotdealSingleImage')
                ->where('date_from', '<=', $currentDate)
                ->where('date_to', '>=', $currentDate)
                ->where('hot_deal_status', '=', 1)
                ->select(
                    'users.id as business_id',
                    'profiles.id as profile_id',
                    'users.name',
                    'users.username',
                    'users.mobile_phone',
                    'profiles.user_avatar',
                    'profiles.address',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                    'countries.id as country_id',
                    'countries.country',
                    'businesses.sub_cat_id',
                    'subcategories.subcat_name',
                    DB::raw("DATE_FORMAT(users.created_at, '%d-%m-%Y') as added_date"),
                    // hot deals
                    'hotdeals.id',
                    'hotdeals.id as hotdeal_id',
                    'hotdeals.hotdeal_slug',
                    'hotdeals.hot_deal_title',
                    'hotdeals.hot_deal_description',
                    'hotdeals.price_from',
                    'hotdeals.price_to',
                    'hotdeals.date_from',
                    'hotdeals.date_to',
                    'hotdeals.hot_deal_status',
                )
                ->where(function ($query) use($searchy){
                    foreach ($searchy as $sl){
                        $query->orWhere('hotdeals.hot_deal_title', 'like', '%' . $sl . '%')
                            ->orWhere('hotdeals.hot_deal_description', 'like', '%' . $sl . '%');
                    }
                });
            $maxPrice = Hotdeal::max('price_to');
            if( empty($request->min_price) && !empty($request->max_price) ){
                $searched->whereBetween('price_to', [0, $request->max_price]);
            }
            if( !empty($request->min_price) && empty($request->max_price) ){
                $searched->whereBetween('price_to', [$request->min_price, $maxPrice]);
            }
            if( !empty($request->min_price) && !empty($request->max_price) ){
                $searched->whereBetween('price_to', [$request->min_price, $request->max_price]);
            }
        }
        if($type == 1){ // Sales
            $searchType = 1;
            $searched->leftjoin('sales', 'sales.user_id', 'users.id')
                ->where('sales.saledate_from', '<=', $currentDate)
                ->where('sales.saledate_to', '>=', $currentDate)
                ->where('sales.sale_status', '=', 1)
                ->select(
                    'users.id as business_id',
                    'profiles.id as profile_id',
                    'users.name',
                    'users.username',
                    'users.mobile_phone',
                    'profiles.user_avatar',
                    'profiles.address',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                    'countries.id as country_id',
                    'countries.country',
                    'businesses.sub_cat_id',
                    'subcategories.subcat_name',
                    DB::raw("DATE_FORMAT(users.created_at, '%d-%m-%Y') as added_date"),
                    // Sales
                    'sales.id as sale_id',
                    'sales.sale_title',
                    'sales.sale_slug',
                    'sales.sale_description',
                    'sales.normal_price',
                    'sales.sale_price',
                    'sales.saledate_from',
                    'sales.saledate_to',
                    'sales.sale_imageurl',
                    'sales.sale_status',
                )
                ->
                where(function ($query) use($searchy){
                    foreach ($searchy as $sl){
                        $query->orWhere('sales.sale_title', 'like', '%' . $sl . '%')
                            ->orWhere('sales.sale_description', 'like', '%' . $sl . '%');
                    }
                });
            $maxPrice = Sale::max('sale_price');
            if( empty($request->min_price) && !empty($request->max_price) ){
                $searched->whereBetween('sale_price', [0, $request->max_price]);
            }
            if( !empty($request->min_price) && empty($request->max_price) ){
                $searched->whereBetween('sale_price', [$request->min_price, $maxPrice]);
            }
            if( !empty($request->min_price) && !empty($request->max_price) ){
                $searched->whereBetween('sale_price', [$request->min_price, $request->max_price]);
            }
        }
        if($type==2){
            $searchType = 2;
            $searched->leftjoin('jobs', 'jobs.user_id', 'users.id')
                ->join('jobcategories', 'jobcategories.id', 'jobs.job_cat_id')
                ->where('jobs.job_status', '=', 1)
                ->orderBy('job_id','DESC')
                ->select(
                    'users.id as business_id',
                    'profiles.id as profile_id',
                    'users.name',
                    'users.username',
                    'users.mobile_phone',
                    'profiles.user_avatar',
                    'profiles.address',
                    'profiles.city_id',
                    'cities.city',
                    'profiles.state_id',
                    'states.state',
                    'businesses.sub_cat_id',
                    'subcategories.subcat_name',
                    DB::raw("DATE_FORMAT(users.created_at, '%d-%m-%Y') as added_date"),
                    // jobs
                    'jobs.id as job_id',
                    'jobs.job_title',
                    'jobs.job_cat_id',
                    'jobcategories.job_cat_name',
                    'jobs.job_slug',
                    'jobs.job_description',
                    'jobs.salary_from',
                    'jobs.min_exp',
                    'jobs.job_status',
                )
                ->where(function ($query) use($searchy){
                    foreach ($searchy as $sl){
                        $query->orWhere('jobs.job_title', 'like', '%' . $sl . '%')
                            ->orWhere('job_description', 'like', '%' . $sl . '%');
                    }
                });
            if(!empty($request->job_cat_id)){
                $searched->where('jobs.job_cat_id', $request->job_cat_id);
            }
            $maxPrice = Job::max('salary_from');
            if( empty($request->min_price) && !empty($request->max_price) ){
                $searched->whereBetween('salary_from', [0, $request->max_price]);
            }
            if( !empty($request->min_price) && empty($request->max_price) ){
                $searched->whereBetween('salary_from', [$request->min_price, $maxPrice]);
            }
            if( !empty($request->min_price) && !empty($request->max_price) ){
                $searched->whereBetween('salary_from', [$request->min_price, $request->max_price]);
            }
        }
        if($type == 3){
            $searchType = 3;
            $searched->select('users.id as business_id',
                'profiles.id as profile_id',
                'users.name',
                'users.username',
                'users.mobile_phone',
                'profiles.user_avatar',
                'profiles.address',
                'profiles.city_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                'galleryimages.image_url as mainimage',
                'businesses.sub_cat_id','subcategories.subcat_name',    'users.created_at',
                'users.updated_at')
                ->where('users.name', 'like', '%' . $allwords . '%');
        }
        $searched->where('users.user_role', '1');
        $searched->where('users.user_status', '1');
        $searched->groupBy('users.id');
        if(!empty($request->country_id)){
            $searched->where('countries.id','=',$request->country_id);
        }else{
            $searched->where('countries.id','=', $country_id);
        }
        $alldata = $searched->get();

        $acounts = [
            'jobcount'=> Job::count(),
            'salecount' => Sale::count(),
            'hotdcount' => Hotdeal::count(),
            'buscount' => Business::count(),
        ];
        $cbcount = Profile::Join('businesses','businesses.business_id','=','profiles.user_id')
         ->join('countries','countries.id','=','profiles.country_id')
         ->select('profiles.country_id','countries.country',DB::raw('count(*) as businesscountbycountry'))
         ->groupBy('profiles.country_id')
         ->get();
        foreach ($cbcount as $cid){

            $cid->dealscountbycountry = Profile::join('hotdeals','hotdeals.business_id','=','profiles.id')
                ->where('profiles.country_id','=',$cid->country_id)
                ->select('profiles.country_id',DB::raw('count(*) as dealscountbycountry'))
                ->first()->dealscountbycountry;

            $cid->salescountbycountry = Profile::join('sales','sales.user_id','=','profiles.user_id')
                ->where('profiles.country_id','=',$cid->country_id)
                ->select('profiles.country_id',DB::raw('count(*) as salescountbycountry'))
                ->first()->salescountbycountry;

            $cid->jobscountbycountry = Profile::join('jobs','jobs.user_id','=','profiles.user_id')
                ->where('profiles.country_id','=',$cid->country_id)
                ->select('profiles.country_id',DB::raw('count(*) as jobscountbycountry'))
                ->first()->jobscountbycountry;

            $bstatecount1 = Profile::Join('businesses','businesses.business_id','=','profiles.user_id')
                ->join('states','states.id','=','profiles.state_id')
                ->join('countries','countries.id','=','states.country_id')
                ->where('countries.id','=',$cid->country_id)
                ->select('profiles.state_id','states.country_id','states.state',DB::raw('count(*) as businesscountbystate'))
                ->groupBy('profiles.state_id')
                ->orderBy('businesscountbystate','Desc')
                ->take(1)
                ->get();

            $cid->bystate = $bstatecount1;
            foreach ($bstatecount1 as $stid){
                $stid3 = Profile::join('hotdeals','hotdeals.business_id','=','profiles.id')
                    ->where('profiles.state_id','=',$stid->state_id)
                    ->select('profiles.state_id',DB::raw('count(*) as dealscountbystate'))
                    ->groupBy('profiles.state_id')
                    ->first();
                if($stid3 != null){
                    $stid->dealscountbystate = $stid3->dealscountbystate;
                } else {
                    $stid->dealscountbystate = 0;
                }

                $stid2 = Profile::join('sales','sales.user_id','=','profiles.user_id')
                    ->where('profiles.state_id','=',$stid->state_id)
                    ->select('profiles.state_id',DB::raw('count(*) as salescountbystate'))
                    ->groupBy('profiles.state_id')
                    ->first();
                if($stid2 != null){
                    $stid->salescountbystate = $stid2->salescountbystate;
                } else {
                    $stid->salescountbystate = 0;
                }

                $stid1 = Profile::join('jobs','jobs.user_id','=','profiles.user_id')
                    ->where('profiles.state_id','=',$stid->state_id)
                    ->select('profiles.state_id',DB::raw('count(*) as jobscountbystate'))
                    ->groupBy('profiles.state_id')
                    ->first();
                if($stid1 != null){
                    $stid->jobscountbystate = $stid1->jobscountbystate;
                } else {
                    $stid->jobscountbystate = 0;
                }



//                dd($stid->jobscountbystate);
//                $bcount1 = Profile::Join('businesses','businesses.business_id','=','profiles.user_id')
//                    ->join('cities','cities.id','=','profiles.city_id')
//                    ->join('states','states.id','=','cities.state_id')
//                    ->where('states.id','=',$stid->state_id)
//                    ->select('profiles.city_id','cities.city',DB::raw('count(*) as businesscount'))
//                    ->groupBy('profiles.city_id')
//                    ->orderBy('businesscount','Desc')
//                    ->take(10)
//                    ->get();
//                $stid->bycity = $bcount1;
            }
        }

         $bcount = Profile::Join('businesses','businesses.business_id','=','profiles.user_id')
         ->join('cities','cities.id','=','profiles.city_id')
         ->select('profiles.city_id','cities.city',DB::raw('count(*) as businesscount'))
         ->groupBy('profiles.city_id')
         ->orderBy('businesscount','Desc')
         ->take(10)
         ->get();

         $bstatecount = Profile::Join('businesses','businesses.business_id','=','profiles.user_id')
             ->join('states','states.id','=','profiles.state_id')
             ->join('countries','countries.id','states.country_id')
             ->select('profiles.state_id','states.country_id','states.state',DB::raw('count(*) as businessstatecount'))
             ->groupBy('profiles.state_id')
             ->orderBy('businessstatecount','Desc')
             ->take(10)
             ->get();

         $city_count = Profile::Join('hotdeals','hotdeals.business_id','=','profiles.id')
         ->join('cities','cities.id','=','profiles.city_id')
         ->select('profiles.city_id','cities.city',DB::raw('count(*) as citycount'))
         ->groupBy('profiles.city_id')
         ->orderBy('citycount','Desc')
         ->take(10)
         ->get();

         $cstatecount =Profile::Join('hotdeals','hotdeals.business_id','=','profiles.id')
         ->join('states','states.id','=','profiles.state_id')
         ->select('profiles.state_id','states.state',DB::raw('count(*) as citystatecount'))
         ->groupBy('profiles.state_id')
         ->orderBy('citystatecount','Desc')
         ->take(10)
         ->get();

         $jcity_count = Profile::Join('jobs','jobs.user_id','=','profiles.user_id')
         ->join('cities','cities.id','=','profiles.city_id')
         ->select('profiles.city_id','cities.city',DB::raw('count(*) as jcitycount'))
         ->groupBy('profiles.city_id')
         ->orderBy('jcitycount','Desc')
         ->take(10)
         ->get();

         $jobstate_count = Profile::Join('jobs','jobs.user_id','=','profiles.user_id')
         ->join('states','states.id','=','profiles.state_id')
         ->select('profiles.state_id','states.state',DB::raw('count(*) as jobstatecount'))
         ->groupBy('profiles.state_id')
         ->orderBy('jobstatecount','Desc')
         ->take(10)
         ->get();

         $scity_count = Profile::Join('sales','sales.user_id','=','profiles.user_id')
         ->join('cities','cities.id','=','profiles.city_id')
         ->select('profiles.city_id','cities.city',DB::raw('count(*) as scitycount'))
         ->groupBy('profiles.city_id')
         ->orderBy('scitycount','Desc')
         ->take(10)
         ->get();

         $salestate_count = Profile::Join('sales','sales.user_id','=','profiles.user_id')
         ->join('states','states.id','=','profiles.state_id')
         ->select('profiles.state_id','states.state',DB::raw('count(*) as salestatecount'))
         ->groupBy('profiles.state_id')
         ->orderBy('salestatecount','Desc')
         ->take(10)
         ->get();

        $alls = Profile::with('jobs','sales','deals')
            ->Join('users','users.id','=','profiles.user_id')
            ->Join('businesses','businesses.business_id','=','profiles.user_id')
            ->join('cities','cities.id','=','profiles.city_id')
            ->join('states','states.id','=','profiles.state_id')
            ->join('countries','countries.id','=','profiles.country_id')
            ->join('subcategories','subcategories.id','=','businesses.sub_cat_id')
            ->select('profiles.*','users.name','users.username','businesses.cat_id','businesses.sub_cat_id',
                'subcategories.subcat_name','cities.city','states.state','countries.country',  'users.created_at',
                'users.updated_at')
            ->withCount('jobs','sales','deals')
            ->get();
        return response()->json([
            'status'=>true,
            'message'=>"All Listings",
            'type'=>$type,
            'alldata'=>$alldata,
            'allps'=>$alls,
            'acounts'=>$acounts,
            'cbcount'=>$cbcount,
            'bcount'=>$bcount,
            'city_count'=>$city_count,
            'jcity_count'=>$jcity_count,
            'scity_count'=>$scity_count,
            'bstatecount'=>$bstatecount,
            'cstatecount'=>$cstatecount,
            'jobstate_count'=>$jobstate_count,
            'salestate_count'=>$salestate_count
        ],200);
    }
}
