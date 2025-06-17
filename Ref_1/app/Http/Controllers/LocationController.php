<?php

namespace App\Http\Controllers;
use App\Models\City;
use App\Models\Country;
use App\Models\Qualification;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Country::with('states.cities')->get();

        $locate = DB::table('countries')
            ->leftJoin('states','states.country_id', 'countries.id')
            ->leftJoin('cities','cities.state_id', 'states.id')
            ->get();
        return view('sadmin.locations', compact('locations','locate'));
    }
    
    public function getCountries()
    {
        $country = Country::Where('country_status', 1)->orderBy('country','ASC')->get();
        return response()->json([
            'status' => 200,
            'countries' => $country
        ]);
    }
    public function registerGetState($country_id)
    {
        $states = State::where('country_id', $country_id)->Where('state_status', 1)->orderBy('state', 'ASC')->get();
        return response()->json($states);
    }
    public function getLocations()
    {
        $location = Country::with('states.cities')->get();
        return response()->json(
            ['locations' => $location]
        );
    }
    public function getCities()
    {
        $location = City::Where('city_status', '=', 1)->get();
        return response()->json(
            ['cities' => $location]
        );
    }
    public function getQualification()
    {
        $qualification = Qualification::Where('qualification_status', '=', 1)->get();
        return response()->json(
            ['qualification' => $qualification]
        );
    }

    public function getStates()
    {
        $location = DB::table('countries')
            ->Join('states','states.country_id', 'countries.id')
            ->get();
        return response()->json(
            ['locations' => $location]
        );
    }

    public function getStateWithCity()
    {
        $location = DB::table('countries')
            ->Join('states','states.country_id', 'countries.id')
            ->Join('cities','cities.state_id', 'states.id')
            ->select('countries.id as country_id', 'countries.country', 'countries.country_status', 'countries.phonecode', 'states.id as state_id',
                'states.state', 'cities.id', 'cities.id as city_id', 'cities.city', 'cities.city_status')
            ->get();
        return response()->json(
            ['locations' => $location]
        );
    }
    public function getStateWithCityByKeyword( Request $request )
    {
        if( !empty($request->keyword) ){
            $location = DB::table('countries')
            ->Join('states','states.country_id', 'countries.id')
            ->Join('cities','cities.state_id', 'states.id')
            ->where('cities.city',"like", "%" . $request->keyword . "%")
            ->select('countries.id as country_id', 'countries.country', 'countries.country_status', 'countries.phonecode', 'states.id as state_id',
                'states.state', 'cities.id', 'cities.id as city_id', 'cities.city', 'cities.city_status')
            ->take(3)
            ->get();
            return response()->json(
                ['locations' => $location]
            );
        }else{
            return response()->json([]);
        }
        
    }

    public function addCity(Request $request){

        $credentials = $request->only('stateId', 'cityname', 'city_status');

        //valid credential
        $validator = Validator::make($credentials, [
            'stateId' => 'required',
            'cityname' => 'required',
            'city_status' => 'required'
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{
            $addcity = new City();
            $addcity->state_id = $request->stateId;
            $addcity->city = $request->cityname;
            $addcity->city_status = $request->city_status;
            $save_city = $addcity->save();
            if ($save_city == true) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => "City added successfully",
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
    public function editCity(Request $request)
    {
        $update_city = City::find($request->id);
        $update_city->state_id = $request->state_id;
        $update_city->city = $request->cityname;
        
        if( $request->city_status == "true" ){
            $update_city->city_status = 1;
        }elseif( $request->city_status == "1" ){
            $update_city->city_status = 1;
        }else{
            $update_city->city_status = 0;
        }
        $city_updata = $update_city->update();
        if ($city_updata == true) {
            return response(
                [
                    'success' => 200,
                    'message' => 'City Update Successfully.'
                ]);
        }else{
            return response()->json(
                [
                    'status' => 400,
                    'message' => "Something went worng."
                ]);
        }
    }
    public function deleteCity( Request $request){

            $data = $request->only('city_id');
            $validator = Validator::make($data, [
                'city_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $cities = City::find($request->city_id);
                if( $cities == null ){
                    return response(
                        [
                            'success' => false,
                            'message' => 'City does not exist please check again!'
                        ],400);
                }else{
                    City::where('id',$request->city_id)->delete();
                    return response(
                        [
                            'success' => true,
                            'message' => 'City deleted successfully'
                        ]);
                  }
                }
            }

    # To delete the multiple Cities by admin ( function start )
    public function deleteMultiplecities( Request $request ){
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
                    $delete_reviews= City::find($id);
                    $delete_reviews->delete();
                }
                return response([
                    'success' => true,
                    'message' => 'Cities deleted successfully.'
                ]);

            }
        }
    }
    # To get the multiple Country by admin ( function start )
    public function getCountry(Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }else{
            $country = Country::orderBy('created_at', 'DESC')->get();
            return response()->json([
                'status' => 200,
                'countries' => $country
            ]);
        }
    }
    # To get the multiple Country by admin ( function end )

    # To add the multiple Country by admin ( function start )
    public function addCountry(Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'country' => 'required|unique:countries|string',
                'sortname' => 'required|unique:countries|string',
                'phonecode' => 'required|unique:countries|integer'
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }else{
                $add_country = new Country();
                    $add_country->country = $request->country;
                    $add_country->sortname = $request->sortname;
                    $add_country->phonecode = $request->phonecode;
                    $add_country->country_status = 1;
                $save_country = $add_country->save();
                if($save_country == true){
                    return response()->json([
                        'status' => 200,
                        'message' => "Country added successfully.",
                        'data' => $add_country
                    ]);
                }else{
                    return response()->json([
                        'status' => 400,
                        'message' => "Something went worng."
                    ]);
                }
            }
        }
    }
    # To add the multiple Country by admin ( function end )
    
    # To edit the multiple Country by admin ( function start )
    public function editCountry(Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'country_id' => 'required|integer',
                'country' => 'required|string',
                'sortname' => 'required|string',
                'phonecode' => 'required|integer'
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }else{
                $country = Country::find($request->country_id);
                $country->country = $request->country;
                $country->sortname = $request->sortname;
                $country->phonecode = $request->phonecode;
                if( $request->country_status == "true" ){
                    $country->country_status = 1;
                }elseif( $request->country_status == "1" ){
                    $country->country_status = 1;
                }else{
                    $country->country_status = 0;
                }

                $country_update = $country->update();
                if ($country_update == true) {
                    return response([
                        'success' => 200,
                        'message' => 'Country updated Successfully.',
                        'data' => $country
                    ]);
                }else{
                    return response()->json([
                        'status' => 400,
                        'message' => "Something went worng."
                    ]);
                }

            }
        }
    }
    # To edit the multiple Country by admin ( function end )
    
    # To single delete the multiple Country by admin ( function start )
    public function deleteCountry(Request $request){
        $User = JWTAuth::authenticate($request->token);
        if(!$User){
            return response([
                'success' => false,
                'message' => 'User not found !'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'country_id' => 'required|integer',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors());
            }else{
                $country = Country::find($request->country_id);
                $country_delete = $country->delete();
                if ($country_delete == true) {
                    return response([
                        'success' => 200,
                        'message' => 'Country deleted successfully.',
                        'data' => $country
                    ]);
                }else{
                    return response()->json([
                        'status' => 400,
                        'message' => "Something went worng."
                    ]);
                }
            }
        }
    }
    # To single delete the multiple Country by admin ( function end )

    # To delete the multiple Cities by admin ( function end )
    public function addState(Request $request){

        $credentials = $request->only('statename');

        //valid credential
        $validator = Validator::make($credentials, [
            'statename' => 'required',
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }else{
            $addstate = new State();
            $addstate->country_id = $request->countryId;
            $addstate->state = $request->statename;
            $addstate->state_status = $request->state_status;
            $save_state = $addstate->save();
            if ($save_state == true) {
                return response()->json([
                    'status' => 200,
                    'message' => "State added successfully.",
                ]);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => "Something went worng."
                ]);
            }
        }
    }
    public function editState(Request $request)
    {
        $update_state = State::find($request->id);
        $update_state->country_id = $request->country_id;
        $update_state->state = $request->statename;
        if( $request->state_status == "true" ){
            $update_state->state_status = 1;
        }elseif( $request->state_status == "1" ){
            $update_state->state_status = 1;
        }else{
            $update_state->state_status = 0;
        }
        $state_update = $update_state->update();
        if ($state_update == true) {
            return response([
                'success' => 200,
                'message' => 'State Update Successfully.'
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => "Something went worng."
            ]);
        }
    }
    public function deleteState( Request $request){

            $data = $request->only('state_id');
            $validator = Validator::make($data, [
                'state_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            else{
                $cities = State::find($request->state_id);
                if( $cities == null ){
                    return response(
                        [
                            'success' => false,
                            'message' => 'State does not exist please check again!'
                        ],400);
                }else{
                    State::where('id',$request->state_id)->delete();
                    return response(
                        [
                            'success' => true,
                            'message' => 'State deleted successfully'
                        ]);
                  }
                }
            }

    # To delete the multiple States by admin ( function start )
    public function deleteMultiplestates( Request $request ){
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
                    $delete_states = State::find($id);
                    $delete_states->delete();
                }
                return response([
                                    'success' => true,
                                    'message' => 'States deleted successfully.'
                                ]);

            }
        }
    }
    # To delete the multiple States by admin ( function end )
}
