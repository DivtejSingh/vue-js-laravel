<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Changereseller;
use App\Models\City;
use App\Models\Plan;
use App\Models\Profile;
use App\Models\Reseller;
use App\Models\Resellerskill;
use App\Models\Skill;
use App\Models\User;
use App\Models\State;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Psy\Readline\Hoa\Console;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewAgentNotification;
use Illuminate\Support\Facades\Log;

class ResellerController extends Controller
{
    # To login reseller ( function start )
    public function resellerLogin(Request $request)
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
        $reseller = User::Where('email', $request->email)->where('user_status', '1')->first();

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Login credentials are invalid, or you are not reseller',
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

        if ($reseller == null) {
            return response([
                'success' => false,
                'message' => 'Your account has been deactivated',
            ], 400);
        } else {
            $existing_reseller = Reseller::where('user_id', $reseller->id)->first();
            $reseller->plan_id = $existing_reseller->plan_id;
        }

        //Token created, return with success response and jwt token
        return response()->json(
            [
                'success' => true,
                'token' => $token,
                'user' => $reseller,
            ],
            200
        );
    }
    # To login reseller ( function end )

    # To get state by country id for option ( function start )
    public function getstateByCountryId($id)
    {
        $state = State::where('country_id', $id)->get();
        return response()->json($state);
    }
    # To get state by country id for option ( function end )

    # To get cities by state for reseller register ( function start )
    public function getCitiesByStateId($id)
    {
        $cities = City::where('state_id', $id)->orderBy('city', 'ASC')->get();
        return response()->json($cities);
    }
    # To get cities by state for reseller register ( function end )

    # To search the data by reseller from admin panel ( function start )

    public function getCitiesById($id)
    {
        $cities = City::where('id', $id)->get();
        return response()->json($cities);
    }
 
    # To search the data by reseller from admin panel ( function start )

    
    public function resellerRegister(Request $request)
    {
        $data = $request->only(
            'name',
            'gender',
            'email',
            'password',
            'profession_id',
            'skill_id',
            'mobile_phone',
            'date_of_birth',
            'city_id',
            'address',
            'user_role',
            'user_status',
            'state_id'
        );
    
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'gender' => 'required|string|in:Male,Female',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:15',
            'profession_id' => 'required|integer',
            'skill_id' => 'required|array',
            'mobile_phone' => 'required|unique:users',
            'date_of_birth' => 'required|date',
            'city_id' => 'required|integer',
            'address' => 'required|string',
            'user_role' => 'required|in:2',
            'user_status' => 'required|in:0,1',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
    
        DB::beginTransaction();
    
        try {
            // Lock users and get the last RES number
            $lastUsername = User::where('user_role', 2)
                ->where('username', 'like', 'RES%')
                ->selectRaw("MAX(CAST(SUBSTRING(username, 4) AS UNSIGNED)) as max_number")
                ->lockForUpdate()
                ->value('max_number');
    
            $nextNumber = $lastUsername ? intval($lastUsername) + 1 : 1;
            $newUsername = 'RES' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    
            // Fallback in case of race condition
            while (User::where('username', $newUsername)->exists()) {
                $nextNumber++;
                $newUsername = 'RES' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
    
            // Optional: Safe geolocation API call
            $longitude_latitude = ['longitude' => null, 'latitude' => null];
            try {
                $longitude_latitude = apiResponse($request->address); // Define this safely
            } catch (\Exception $geoEx) {
                // Log it but don’t block registration
                \Log::warning("Geolocation failed: " . $geoEx->getMessage());
            }
    
            // Create user
            $reseller = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_role' => 3,
                'user_status' => $request->user_status,
                'username' => $newUsername,
                'mobile_phone' => $request->mobile_phone,
                'longitude' => $longitude_latitude['longitude'],
                'latitude' => $longitude_latitude['latitude'],
            ]);
    
            // Get state from city if not passed
            $state_id = $request->state_id ?? optional(City::find($request->city_id))->state_id;
    
            // Create profile
            $resellerProfile = Profile::create([
                'user_id' => $reseller->id,
                'state_id' => $state_id,
                'city_id' => $request->city_id,
                'address' => $request->address,
                'profile_status' => 1,
            ]);
    
            // Create reseller details
            $resellerDetails = Reseller::create([
                'user_id' => $reseller->id,
                'profession_id' => $request->profession_id,
                'skills_id' => implode(',', $request->skill_id),
                'reseller_dob' => $request->date_of_birth,
                'reseller_gender' => $request->gender,
                'plan_id' => 1,
            ]);
    
            DB::commit();
    
            // Send email after commit
            try {
                Mail::to('bizlxmail@gmail.com')->send(new NewAgentNotification($reseller));
            } catch (\Exception $mailEx) {
                \Log::error("Email sending failed for reseller ID {$reseller->id}: " . $mailEx->getMessage());
                // Not throwing so registration continues
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Reseller created successfully.',
                'data' => [
                    'user' => $reseller,
                    'profile' => $resellerProfile,
                    'reseller' => $resellerDetails,
                ]
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong during registration.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    # To get the data by reseller from admin panel ( function start )
    public function resellerprofileGet(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);

        $resellers = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('resellers', 'resellers.user_id', 'profiles.user_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('professions', 'professions.id', 'resellers.profession_id')
            //            ->with('allskill')
            ->leftjoin('plans', 'plans.id', 'resellers.plan_id')
            ->where('users.user_role', '2')
            ->select(
                'users.*',
                'profiles.user_avatar',
                'profiles.city_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                'profiles.address',
                'resellers.reseller_dob',
                'resellers.reseller_gender',
                'resellers.profession_id',
                'professions.profession',
                'resellers.skills_id',
                'resellers.plan_id',
            )
            ->where('users.id', $User->id)
            ->first();
        $ids = explode(",", $resellers->skills_id);
        $skillids = Skill::whereIn('id', $ids)
            ->select('id', 'skill')
            ->get();
        return response()->json(
            [
                'resellers' => $resellers,
                'skillids' => $skillids
            ]
        );
    }

    public function resellerprofileGetbyadmin(Request $request)
    {
        $User = $request->resellerId;
        $buser_id = $User;

        $resellers = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('resellers', 'resellers.user_id', 'profiles.user_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('professions', 'professions.id', 'resellers.profession_id')
            //            ->with('allskill')
            ->leftjoin('plans', 'plans.id', 'resellers.plan_id')
            ->where('users.user_role', '2')
            ->select(
                'users.*',
                'profiles.user_avatar',
                'profiles.city_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                'profiles.address',
                'resellers.reseller_dob',
                'resellers.reseller_gender',
                'resellers.profession_id',
                'professions.profession',
                'resellers.skills_id',
                'resellers.plan_id',
            )
            ->Where('users.id', '=', $buser_id)
            ->first();
        $ids = explode(",", $resellers->skills_id);
        $skillids = Skill::whereIn('id', $ids)
            ->select('id', 'skill')
            ->get();
        return response()->json(
            [
                'resellers' => $resellers,
                'skillids' => $skillids
            ]
        );
    }
    # To get the data by reseller from admin panel ( function end )

    # Update reseller dashboard profile data( Function start )
    public function updateResellerprofile(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        $buser_id = $User->id;
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'mobile_phone' => 'required',
            'address' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        } else {
            $update_reseller = User::find($buser_id);
            $update_reseller->name = $request->name;
            $update_reseller->email = $request->email;
            $update_reseller->mobile_phone = $request->mobile_phone;
            $update_reseller->update();
            Reseller::Where('user_id', $buser_id)->update(
                [
                    'profession_id' => $request->profession_id,
                    'skills_id' => implode(",", $request->skills_id),
                    'reseller_gender' => $request->reseller_gender,
                ]
            );
        }
        $update_avatar = Profile::Where('user_id', $buser_id)->first();
        if ($request->file('user_avatar')) {
            $call_function = new Profile();
            $file = $request->file('user_avatar');
            $old_file = $update_avatar->user_avatar;
            $update_avatar->user_avatar = $call_function->avatar($old_file, $file);
        }
        $update_avatar->state_id =  $request->state_id;
        $update_avatar->city_id =  $request->city_id;
        $update_avatar->address =  $request->address;
        $update_avatar->update();

        return response()->json(
            [
                'reseller_profile' => 'Reseller Profile Updated Successfully.'
            ],
            200
        );
    }


    public function updateResellerprofilebyadmin(Request $request)
    {
        $User = $request->reseller_id;
        $buser_id = $User;
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'mobile_phone' => 'required',
            'address' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        } else {
            $update_reseller = User::find($buser_id);
            $update_reseller->name = $request->name;
            $update_reseller->email = $request->email;
            $update_reseller->mobile_phone = $request->mobile_phone;
            $update_reseller->update();
            Reseller::Where('user_id', $buser_id)->update(
                [
                    'profession_id' => $request->profession_id,
                    'skills_id' => implode(",", $request->skills_id),
                    'reseller_gender' => $request->reseller_gender,
                ]
            );
        }
        $update_avatar = Profile::Where('user_id', $buser_id)->first();
        if ($request->file('user_avatar')) {
            $call_function = new Profile();
            $file = $request->file('user_avatar');
            $old_file = $update_avatar->user_avatar;
            $update_avatar->user_avatar = $call_function->avatar($old_file, $file);
        }
        $update_avatar->state_id =  $request->state_id;
        $update_avatar->city_id =  $request->city_id;
        $update_avatar->address =  $request->address;
        $update_avatar->update();

        return response()->json(
            [
                'reseller_profile' => 'Reseller Profile Updated Successfully.'
            ],
            200
        );
    }
    # Update reseller dashboard profile data( Function end )

    # Update reseller dashboard profile data( Function start )
    public function updateresellerPassword(Request $request)
    {

        $User = JWTAuth::authenticate($request->token);
        $user_id = $User->id;
        if ($User) {
            $update_password = User::find($user_id);
            $password = Hash::make($request->password);
            $update_password->password = $password;
            $update_password->update();

            return response()->json(
                [
                    'reseller' => 'Business Password Updated Successfully.'
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'reseller' => 'Business Password Not Updated!.'
                ],
                400
            );
        }
    }

    public function updateresellerPasswordbyadmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reseller_id' => 'required|integer|exists:users,id',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422); // Validation error response
        }

        $update_password = User::find($request->reseller_id);
        $update_password->password = Hash::make($request->password);
        $update_password->save();

        return response()->json([
            'reseller' => 'Business Password Updated Successfully.'
        ], 200);
    }




    # Update reseller dashboard profile data( Function end )

    # To register business by reseller ( Function start )


    public function registerReseller(Request $request)
    {
        // Authenticate user with token
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }

        // Extract data from request
        $data = $request->only(
            'business_name',
            'email',
            'username',
            'password',
            'category',
            'state',
            'city',
            'area',
            'mobile',
            'subcategories',
            'user_status',
            'country_id'
        );

        // Validate incoming data
        $validator = Validator::make($data, [
            'business_name' => 'required|string',
            'email' => 'required|email',
            'username' => 'required|string',
            'password' => 'required|string',
            'category' => 'required|integer',
            'subcategories' => 'required|array',  // Expect an array of subcategories
            'state' => 'required|integer',
            'city' => 'required|integer',
            'mobile' => 'required|string',  // Ensure mobile is a string to avoid integer length issues
            'country_id' => 'required|integer',  // Dynamically set country_id
        ]);

        // Return error if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Create new reseller user
        $save_reseller = new User();
        $save_reseller->name = $request->business_name;
        $save_reseller->email = $request->email;
        $save_reseller->username = $request->username;
        $save_reseller->mobile_phone = $request->mobile;
        $save_reseller->user_role = "1";  // Set user as reseller

        // Set user status
        $save_reseller->user_status = $request->user_status === true ? '1' : '0';

        // Hash password
        $save_reseller->password = Hash::make($request->password);

        // Get longitude and latitude from the address
        $longitude_latitude = apiResponse($request->address);
        $save_reseller->longitude = $longitude_latitude['longitude'];
        $save_reseller->latitude = $longitude_latitude['latitude'];

        // Save reseller user
        $save_reseller->save();

        // Save business details (without foreach and implode)
        $save_reseller_details = new Business();
        $save_reseller_details->business_id = $save_reseller->id;
        $save_reseller_details->cat_id = $request->category;
        $save_reseller_details->sub_cat_id = $request->subcategories[0];  // Store as JSON array
        $save_reseller_details->gst = $request->GST;
        $save_reseller_details->plan_id = 1;
        $save_reseller_details->listedby = 1;
        $save_reseller_details->listedby_reseller_id = $User->id;

        // Save business information
        $save_reseller_details->save();

        // Save profile details
        $save_profile_details = new Profile();
        $save_profile_details->city_id = $request->city;
        $save_profile_details->country_id = $request->country_id;  // Set dynamically from request
        $save_profile_details->state_id = $request->state;
        $save_profile_details->area = $request->area;
        $save_profile_details->address = $request->address;
        $save_profile_details->user_id = $save_reseller->id;

        // Save profile information
        $save_profile_details->save();

        // Return success response
        return response([
            'success' => true,
            'message' => 'Reseller registered successfully.'
        ]);
    }

    # To get the details for registered business by reseller ( function start )
    public function getBusinessRegisteredByReseller(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $get_businesses_registered_by_reseller = Business::leftjoin("subcategories", \DB::raw(
                "FIND_IN_SET(subcategories.id,businesses.sub_cat_id)"
            ), ">", \DB::raw("'0'"))
                ->leftjoin('users', 'businesses.business_id', '=', 'users.id')
                ->leftjoin('profiles', 'businesses.business_id', '=', 'profiles.user_id')
                ->leftjoin('cities', 'profiles.city_id', '=', 'cities.id')
                ->leftjoin('plans', 'businesses.plan_id', '=', 'plans.id')
                ->select(
                    "businesses.*",
                    \DB::raw("GROUP_CONCAT(subcategories.subcat_name) as subcatsname"),
                    "users.name",
                    "users.email",
                    "users.mobile_phone",
                    "users.user_status",
                    "users.created_at as user_created_at",
                    "cities.id as city_id",
                    "cities.city",
                    "plans.plan_description",
                )
                ->groupBy("businesses.id")
                ->orderBy('businesses.created_at', 'DESC')
                ->where('businesses.listedby_reseller_id', $User->id)
                ->get();
            $data1 = [];
            foreach ($get_businesses_registered_by_reseller as $businessdata) {
                $data1[] = $businessdata;
            }
            $get_businesses_registered_by_reseller_count = count($get_businesses_registered_by_reseller);
            for ($i = 0; $i < $get_businesses_registered_by_reseller_count; $i++) {
                $get_businesses_registered_by_reseller[$i]['reseller_name'] = $User->name;
                $data = $get_businesses_registered_by_reseller[$i]['created_at'] = $data1[$i]->user_created_at;
                $newtime = strtotime($data);
                $Final_date = date('d M, Y H:i a', $newtime);
                $get_businesses_registered_by_reseller[$i]['created_date'] = $Final_date;
                $expiration_date = date('d M, Y H:i a', strtotime($get_businesses_registered_by_reseller[$i]['created_at'] . ' + 365 days'));
                $get_businesses_registered_by_reseller[$i]['expires'] = $expiration_date;
            }
            return response([
                'success' => true,
                'businesses' => $get_businesses_registered_by_reseller
            ]);
        }
    }
    # To get the details for registered business by reseller ( function end )

    # To update reseller id ( function start )
    public function updateResellerId(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        $buser_id = $User->id;
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('old_reseller_id', 'new_reseller_id');
            $validator = Validator::make($data, [
                'old_reseller_id' => 'required',
                'new_reseller_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $check_existing_reseller = User::Where('user_role', '2')->Where('username', $request->new_reseller_id)->get();
                $check_existing_reseller_count = count($check_existing_reseller);
                $old_id = str_replace("RES", '', $request->old_reseller_id);
                if ($check_existing_reseller_count > 0) {
                    Business::Where('listedby', 1)->where('listedby_reseller_id', $old_id)->update(
                        [
                            'listedby_reseller_id' => $request->new_reseller_id
                        ]
                    );

                    $service = new Changereseller();
                    $service->business_id = $buser_id;
                    $service->old_reseller_id = $old_id;
                    $service->new_reseller_id = $request->new_reseller_id;
                    $service->reason_text = $request->reason_text;
                    $service->save();

                    return response([
                        'success' => true,
                        'message' => 'Reseller updated successfully'
                    ], 200);
                } else {

                    return response([
                        'success' => false,
                        'message' => 'This reseller id not exist please try another one'
                    ]);
                }
            }
        }
    }
    # To update reseller id ( function end )

    # To update reseller details ( function start )
    public function updateResellerDetails(Request $request)
    {

        $User = JWTAuth::authenticate($request->token);
        if (! $User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->all();
            $validator = Validator::make($data, [
                'business_id' => 'required|integer',
                'email' => 'required|email',
                'username' => 'required',
                'category' => 'required|integer',
                'subcategories' => 'required',
                'state' => 'required|integer',
                'city' => 'required|integer',
                'mobile' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {

                $update_reseller = User::find($request->business_id);
                $update_reseller->name = $request->business_name;
                $update_reseller->email = $request->email;
                $update_reseller->username = $request->username;
                $update_reseller->mobile_phone = $request->mobile;
                if ($request->user_status == "true") {
                    $update_reseller->user_status = '1';
                } elseif ($request->user_status == "1") {
                    $update_reseller->user_status = '1';
                } else {
                    $update_reseller->user_status = '0';
                }
                // $update_reseller->password = Hash::make($request->password);
                $longitude_latitude = apiResponse($request->Address);
                $update_reseller->longitude = $longitude_latitude['longitude'];
                $update_reseller->latitude = $longitude_latitude['latitude'];
                $update_reseller->update();

                $data = Business::Where('business_id', $request->business_id)->get();
                if ($data != null) {
                    Business::Where('business_id', $request->business_id)->delete();
                    $subcatids = $request->subcategories;
                    $subcatid = count($subcatids);
                    if ($subcatid > 1) {
                        $businesses = new Business();
                        $businesses->business_id = $request->business_id;
                        $businesses->cat_id = $request->category;
                        // $businesses->sub_cat_id = implode(",", $request->subcategories);
                        $businesses->sub_cat_id = $request->subcategories[0];  // Store as JSON array
                        $businesses->gst = $request->GST;
                        $businesses->plan_id = 1;
                        $businesses->listedby = 1;
                        $businesses->listedby_reseller_id = $User->id;
                        $businesses->save();
                        // foreach ($subcatids as $subcatid) {
                        //         $businesses = new Business();
                        //         $businesses->business_id = $request->business_id;
                        //         $businesses->cat_id = $request->category;
                        //         $businesses->sub_cat_id = $subcatid;
                        //         $businesses->gst = $request->GST;
                        //         $businesses->plan_id = 1;
                        //         $businesses->listedby = 1;
                        //         $businesses->listedby_reseller_id = $request->reseller_id;
                        //         $businesses->save();
                        // }
                    } else {
                        $businesses = new Business();
                        $businesses->business_id = $request->business_id;
                        $businesses->cat_id = $request->category;
                        // $businesses->sub_cat_id = implode(",", $request->subcategories);
                        $businesses->sub_cat_id = $request->subcategories[0];  // Store as JSON array
                        $businesses->gst = $request->GST;
                        $businesses->plan_id = 1;
                        $businesses->listedby = 1;
                        $businesses->listedby_reseller_id = $User->id;
                        $businesses->save();
                    }
                }
                Profile::where('user_id', $request->business_id)->update([
                    'city_id' => $request->city,
                    'state_id' => $request->state,
                    'address' => $request->Address,
                    'area' => $request->Area,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Business updated successfully.'
                ]);
            }
        }
    }
    # To update reseller details ( function end )

    public function getRegisteredBusinessDetails(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {
            $data = $request->only('business_id');
            $validator = Validator::make($data, [
                'business_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            } else {
                $get_businesses_registered_by_reseller = Business::leftjoin("subcategories", \DB::raw(
                    "FIND_IN_SET(subcategories.id,businesses.sub_cat_id)"
                ), ">", \DB::raw("'0'"))
                    ->leftjoin('users', 'businesses.business_id', '=', 'users.id')
                    ->leftjoin('profiles', 'businesses.business_id', '=', 'profiles.user_id')
                    ->leftjoin('states', 'profiles.state_id', '=', 'states.id')
                    ->leftjoin('cities', 'profiles.city_id', '=', 'cities.id')
                    ->leftjoin('categories', 'businesses.cat_id', '=', 'categories.id')
                    ->select(
                        "businesses.*",
                        \DB::raw("GROUP_CONCAT(subcategories.subcat_name) as subcatsname"),
                        "users.name",
                        "users.username",
                        "users.email",
                        "users.mobile_phone",
                        "profiles.address",
                        "profiles.country_id",
                        "profiles.area",
                        "users.user_status",
                        "states.id as state_id",
                        "states.state",
                        "cities.id as city_id",
                        "cities.city",
                        "categories.cat_name",
                    )
                    ->groupBy("businesses.id")
                    ->where('businesses.business_id', $request->business_id)
                    ->get();
                $ids = explode(",", $get_businesses_registered_by_reseller[0]->sub_cat_id);
                $subcats = Subcategory::whereIn('id', $ids)
                    ->select('id', 'subcat_name')
                    ->get();
                return response([
                    'success' => true,
                    'subcatids' => $subcats,
                    'registered_business' => $get_businesses_registered_by_reseller
                ]);
            }
        }
    }
    # To get the details of a specific business ( function end )

    # To get all the data for search filter ( function start )
    public function getAllInformation(Request $request)
    {
        $User = JWTAuth::authenticate($request->token);
        if (!$User) {
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        } else {

            $cities = City::select('id', 'state_id', 'city')->orderBy('city', 'ASC')->get();
            $state = State::select('id', 'state')->orderBy('state', 'ASC')->get();
            $plan = Plan::select('id', 'plan_description')->get();
            $subcategories = Subcategory::select('id', 'cat_id', 'subcat_name')->orderBy('subcat_name', 'ASC')->get();
            $businesses = User::where('user_role', '1')->where('user_status', '1')->get();
            $users = User::where('user_status', '1')->get();
            return response([
                'success' => true,
                'cities' => $cities,
                'state' => $state,
                'plan' => $plan,
                'subcategories' => $subcategories,
                'businesses' => $businesses,
                'users' => $users
            ]);
        }
    }
    # To get all the data for search filter ( function end )

    # To search the data by reseller from reseller panel ( function start )
    public function searchBusinessesByReseller(Request $request)
    {

        $User = JWTAuth::authenticate($request->token);

        $businesses = Business::query();

        $businesses->leftjoin("subcategories", \DB::raw(
            "FIND_IN_SET(subcategories.id,businesses.sub_cat_id)"
        ), ">", \DB::raw("'0'"))
            ->leftjoin('users', 'businesses.business_id', '=', 'users.id')
            ->leftjoin('profiles', 'businesses.business_id', '=', 'profiles.user_id')
            ->leftjoin('cities', 'profiles.city_id', '=', 'cities.id')
            ->leftjoin('states', 'profiles.state_id', '=', 'states.id')
            ->leftjoin('plans', 'businesses.plan_id', '=', 'plans.id')
            ->select(
                "businesses.*",
                \DB::raw("GROUP_CONCAT(subcategories.subcat_name) as subcatsname"),
                "users.name",
                "users.email",
                "users.mobile_phone",
                "users.user_status as active_status",
                "cities.id as city_id",
                "cities.city",
                "states.id as state_id",
                "plans.id as plan_id",
                "plans.plan_description",
            )
            ->groupBy("businesses.id");

        if ($request->city != "") {
            $businesses->where('city_id', '=', $request->city);
        }
        if ($request->plan != "") {
            $businesses->where('plan_id', '=', $request->plan);
        }
        if ($request->state != "") {
            $businesses->where('state_id', '=', $request->state);
        }
        if ($request->statusValue != "") {
            $businesses->where('active_status', '=', $request->statusValue);
        }
        if ($request->subcategory != "") {
            $businesses->whereRaw("find_in_set('" . $request->subcategory . "',businesses.sub_cat_id)");
        }
        // if ($request->date != "") {
        //     $businesses->where('created_at','=',$request->date);
        // }

        $businesses->where('listedby_reseller_id', $User->id);

        $searched_businesses = $businesses->get();

        $get_businesses_registered_by_reseller_count = count($searched_businesses);

        for ($i = 0; $i < $get_businesses_registered_by_reseller_count; $i++) {

            $searched_businesses[$i]['reseller_name'] = $User->name;

            $data = $searched_businesses[$i]['created_at'];

            $newtime = strtotime($data);

            $Final_date = date('d M, Y H:i a', $newtime);

            $searched_businesses[$i]['created_date'] = $Final_date;

            $expiration_date = date('d M, Y H:i a', strtotime($searched_businesses[$i]['created_at'] . ' + 365 days'));

            $searched_businesses[$i]['expires'] = $expiration_date;
        }

        return response([
            'success' => true,
            'businesses' => $searched_businesses
        ]);
    }
    # To search the data by reseller from reseller panel ( function end )

    // admin add new reseller here
    // public function adminResellerAdd(Request $request)
    // {
    //     $user = JWTAuth::authenticate($request->token);
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'reseller_gender' => 'required',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required',
    //         'reseller_dob' => 'required',
    //         'mobile_phone' => 'required|unique:users',
    //         'profession_id' => 'required|integer',
    //         'skills_id' => 'required',
    //         'state_id' => 'required|integer',
    //         'city_id' => 'required|integer',
    //         'address' => 'required',
    //         'user_status' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->messages()], 400);
    //     } else {

    //         $reseller_user = new User();
    //         $reseller_user->name = $request->name;
    //         $reseller_user->email = $request->email;
    //         $reseller_user->mobile_phone = $request->mobile_phone;
    //         $reseller_user->password = Hash::make($request->password);
    //         $reseller_user->user_role = '2';
    //         if ($request->user_status == "true") {
    //             $reseller_user->user_status = '1';
    //         } elseif ($request->user_status == 1) {
    //             $reseller_user->user_status = '1';
    //         } else {
    //             $reseller_user->user_status = '0';
    //         }
    //         $reseller_user->save();

    //         $resellerProfile =  new Profile();
    //         $resellerProfile->user_id = $reseller_user->id;
    //         $resellerProfile->address = $request->address;
    //         $resellerProfile->state_id = $request->state_id;
    //         $resellerProfile->city_id = $request->city_id;
    //         $resellerProfile->profile_status = 1;
    //         $resellerProfile->save();

    //         $reseller = new Reseller();
    //         $reseller->user_id = $reseller_user->id;
    //         $reseller->profession_id = $request->profession_id;
    //         $reseller->skills_id = implode(",", $request->skills_id);
    //         $reseller->reseller_dob = $request->reseller_dob;
    //         $reseller->reseller_gender = $request->reseller_gender;
    //         $reseller->plan_id = 1;
    //         $reseller->save();

    //         $allAddedSkill_id = $request->skills_id;
    //         for ($i = 0; $i < count($allAddedSkill_id); $i++) { // Add new skill in Resellerskill
    //             $resellerSkill = new Resellerskill();
    //             $resellerSkill->user_id = $reseller_user->id;
    //             $resellerSkill->skill_id = $allAddedSkill_id[$i];
    //             $resellerSkill->save();
    //         }
    //         # Send demails to Reseller by admin (code start)
    //         $app_url = env('RESELLER_LOGIN_URL');
    //         $data = [
    //             'name' => $request->name,
    //             'phone' => $request->mobile_phone,
    //             'email' => $request->email,
    //             'password' => $request->password,
    //             'base_url' => $app_url,
    //         ];
    //         $getadmin_email = User::Where('user_role', '=', '4')->first();
    //         $admin_email =   $getadmin_email->email;
    //         Mail::send('reseller_register_mail', $data, function ($message) use ($admin_email, $request) {
    //             $message->to($request->email)->subject('Register noted.');
    //             $message->from($admin_email);
    //         });
    //         # Send demails to Reseller by admin (code end)
    //         return response([
    //             'status' => 200,
    //             'success' => true,
    //             'message' => 'Reseller added successfully.',
    //             'data' => $reseller_user
    //         ], 200);
    //     }
    // }


    public function adminResellerAdd(Request $request)
{
    $user = JWTAuth::authenticate($request->token);
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'reseller_gender' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required',
        'reseller_dob' => 'required|date',
        'mobile_phone' => 'required|unique:users',
        'profession_id' => 'required|integer',
        'skills_id' => 'required|array',
        'state_id' => 'required|integer',
        'city_id' => 'required|integer',
        'address' => 'required',
        'user_status' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->messages()], 400);
    }

    DB::beginTransaction();
    try {
        // Generate unique username like RES001, RES002
        $lastUsername = User::where('user_role', 2)
            ->where('username', 'like', 'RES%')
            ->selectRaw("MAX(CAST(SUBSTRING(username, 4) AS UNSIGNED)) as max_number")
            ->lockForUpdate()
            ->value('max_number');

        $nextNumber = $lastUsername ? intval($lastUsername) + 1 : 1;
        $newUsername = 'RES' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Fallback in case of race condition
        while (User::where('username', $newUsername)->exists()) {
            $nextNumber++;
            $newUsername = 'RES' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        // Create user
        $reseller_user = new User();
        $reseller_user->name = $request->name;
        $reseller_user->email = $request->email;
        $reseller_user->mobile_phone = $request->mobile_phone;
        $reseller_user->password = Hash::make($request->password);
        $reseller_user->user_role = 3;
        $reseller_user->username = $newUsername;

        if ($request->user_status == "true") {
            $reseller_user->user_status = '1';
        } elseif ($request->user_status == 1) {
            $reseller_user->user_status = '1';
        } else {
            $reseller_user->user_status = '0';
        }

        $reseller_user->save();

        // Create Profile
        $resellerProfile = new Profile();
        $resellerProfile->user_id = $reseller_user->id;
        $resellerProfile->address = $request->address;
        $resellerProfile->state_id = $request->state_id;
        $resellerProfile->city_id = $request->city_id;
        $resellerProfile->profile_status = 1;
        $resellerProfile->save();

        // Create Reseller
        $reseller = new Reseller();
        $reseller->user_id = $reseller_user->id;
        $reseller->profession_id = $request->profession_id;
        $reseller->skills_id = implode(",", $request->skills_id);
        $reseller->reseller_dob = $request->reseller_dob;
        $reseller->reseller_gender = $request->reseller_gender;
        $reseller->plan_id = 1;
        $reseller->save();

        // Save reseller skills
        foreach ($request->skills_id as $skill_id) {
            $resellerSkill = new Resellerskill();
            $resellerSkill->user_id = $reseller_user->id;
            $resellerSkill->skill_id = $skill_id;
            $resellerSkill->save();
        }

        DB::commit();

        // Send email to reseller
        $app_url = env('RESELLER_LOGIN_URL');
        $data = [
            'name' => $request->name,
            'phone' => $request->mobile_phone,
            'email' => $request->email,
            'password' => $request->password,
            'base_url' => $app_url,
        ];
        $getadmin_email = User::where('user_role', 4)->first();
        $admin_email = $getadmin_email->email ?? config('mail.from.address');
        Mail::send('reseller_register_mail', $data, function ($message) use ($admin_email, $request) {
            $message->to($request->email)->subject('Register noted.');
            $message->from($admin_email);
        });

        return response([
            'status' => 200,
            'success' => true,
            'message' => 'Reseller added successfully.',
            'data' => $reseller_user
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    # To get the data by reseller from admin panel ( function start )
    public function resellerSingleGet($user_id)
    {
        $resellers = User::leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('resellers', 'resellers.user_id', 'profiles.user_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('professions', 'professions.id', 'resellers.profession_id')

            // ->leftjoin("skills", DB::raw("FIND_IN_SET(skills.id,resellers.skills_id)"),">", DB::raw("'0'"))
            ->with('allskill')
            ->leftjoin('plans', 'plans.id', 'resellers.plan_id')
            ->where('users.user_role', '2')
            ->select(
                'users.*',
                'profiles.city_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                'profiles.address',
                'resellers.reseller_dob',
                'resellers.reseller_gender',
                'resellers.profession_id',
                'professions.profession',
                'resellers.skills_id',
                'resellers.plan_id',
            )
            // ->leftjoin("documents",\DB::raw("FIND_IN_SET(documents.id,orders.file_id)").skill) as skill")
            ->where('users.id', $user_id)
            ->first();
        return response()->json(
            ['resellers' => $resellers]
        );
    }
    # To get the data by reseller from admin panel ( function end )

    # To update the data by reseller from admin panel ( function start )
    public function AdminResellerUpdate(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'name' => 'required',
            'reseller_gender' => 'required',
            'email' => 'required|email',
            'reseller_dob' => 'required',
            'mobile_phone' => 'required|integer',
            'profession_id' => 'required|integer',
            'skills_id' => 'required',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'address' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        } else {

            $reseller_user = User::find($request->user_id);
            $reseller_user->name = $request->name;
            $reseller_user->email = $request->email;
            $reseller_user->mobile_phone = $request->mobile_phone;
            if ($request->has('password')) {
                $reseller_user->password = Hash::make($request->password);
            }
            if ($request->user_status == "true") {
                $reseller_user->user_status = '1';
            } elseif ($request->user_status == '1') {
                $reseller_user->user_status = '1';
            } else {
                $reseller_user->user_status = '0';
            }
            $reseller_user->update();
            $resellerProfile =  Profile::where('user_id', $request->user_id)->first();
            $resellerProfile->address = $request->address;
            $resellerProfile->state_id = $request->state_id;
            $resellerProfile->city_id = $request->city_id;
            $resellerProfile->update();
            $reseller = Reseller::where('user_id', $request->user_id)->first();
            $reseller->profession_id = $request->profession_id;
            $reseller->skills_id = implode(",", $request->skills_id);
            $reseller->reseller_dob = $request->reseller_dob;
            $reseller->reseller_gender = $request->reseller_gender;
            $reseller->update();
            $allAddedSkill_id = $request->skills_id;
            Resellerskill::where('user_id', '=', $request->user_id)->delete(); // previous all delete
            for ($i = 0; $i < count($allAddedSkill_id); $i++) { // Add new skill in Resellerskill
                $saveResellerSkill = new Resellerskill();
                $saveResellerSkill->user_id = $request->user_id;
                $saveResellerSkill->skill_id = $allAddedSkill_id[$i];
                $saveResellerSkill->save();
            }

            return response([
                'status' => 200,
                'success' => true,
                'message' => 'Reseller updated successfully.',
                'data' => $reseller_user
            ], 200);
        }
    }
    # To update the data by reseller from admin panel ( function end )

    # To delete the data by reseller from admin panel ( function start )
    public function AdminResellerDelete(Request $request, $user_id)
    {
        $user = JWTAuth::authenticate($request->token);
        $reseller_user = User::Where('id', $request->user_id)->first();
        $reseller_business = Business::Where('business_id', $request->user_id)->first();
        $reseller_profile = Profile::Where('user_id', $request->user_id)->first();
        $resellers = Reseller::Where('user_id', $request->user_id)->first();
        $reseller_skills = Resellerskill::Where('user_id', $request->user_id)->get();
        dd($reseller_user);
    }
    # To delete the data by reseller from admin panel ( function end )

    # To search the data by reseller from admin panel ( function start )
    public function AdminResellerSearch(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);
        $resellers = User::query();
        $resellers->leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->leftjoin('resellers', 'resellers.user_id', 'profiles.user_id')
            ->leftjoin('cities', 'cities.id', 'profiles.city_id')
            ->leftjoin('states', 'states.id', 'profiles.state_id')
            ->leftjoin('professions', 'professions.id', 'resellers.profession_id')
            ->with('allskill')
            ->leftjoin("skills", DB::raw("FIND_IN_SET(skills.id,resellers.skills_id)"), 'resellers.skills_id')
            ->leftjoin('plans', 'plans.id', 'resellers.plan_id')
            ->select(
                'users.*',
                'profiles.user_avatar',
                'profiles.city_id',
                'cities.city',
                'profiles.state_id',
                'states.state',
                'resellers.reseller_dob',
                'resellers.reseller_gender',
                'resellers.profession_id',
                'professions.profession',
                'resellers.skills_id',
                'resellers.plan_id',
                'plans.plan_description',
                DB::raw('DATE(users.created_at) as added_date')
            );
        if ($request->state_id != "") {
            $resellers->where('profiles.state_id', $request->state_id);
        }
        if ($request->city_id != "") {
            $resellers->where('profiles.city_id', $request->city_id);
        }
        if($request->user_status){
            if($request->user_status == 1) {
                $resellers->where('users.user_status','=','1');
            }else{
                $resellers->where('users.user_status','=','0');
            }
        }
        $resellers->where('users.user_role', '=', '2');
        $searched_data = $resellers->get();
        if ($resellers->count() > 0) {
            return response([
                'message' => 'Reseller searched data.',
                'resellers' => $searched_data
            ]);
        }
    }
    # To search the data by reseller from admin panel ( function end )

    public function getSettings() {
        $settings = DB::table('settings')->select('reseller_reg_img')->first();
        return response()->json($settings);
    }
    
}
