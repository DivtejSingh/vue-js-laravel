<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Job;
use App\Models\Video;
use App\Models\Hotdeal;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function getSales(){
        $sales = Sale::join('users','users.id','sales.user_id')
            ->join('profiles','profiles.user_id','sales.user_id')
            ->join('cities','cities.id','profiles.city_id')
            ->join('states','states.id','cities.state_id')
            ->select('sales.*','users.name','users.username','users.mobile_phone','profiles.city_id','cities.city','states.state')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Sales List',
            'sales' => $sales,
            ]);
    }

    public function getSaledetail($sale_slug, $loggedUser_id){
        date_default_timezone_set('Asia/Kolkata');
        $currentDate = date('Y-m-d');
        $sale1 = Sale::where('sale_slug',$sale_slug)->first();
        $sale_id = $sale1->id;
        $user_sale_wishlist = Wishlist::Where('services_id',$sale_id)->where('user_id', '=', $loggedUser_id)->first();

        if($user_sale_wishlist==null){
            $user_sale_wishlist = null;
        }

        if($sale1 == null){
            return response()->json(['status' => 400, 'message' => 'No Sale Found']);
        } else{
            $saleid = $sale1->id;
            $sale = Sale::leftJoin('profiles','profiles.user_id','sales.user_id')
                ->leftJoin('users','users.id','profiles.user_id')
                ->join('cities','cities.id','profiles.city_id')
                ->join('states','states.id','cities.state_id')
                ->join('countries','countries.id','states.country_id')
                ->with('salesImages')
                ->leftJoin('wishlists', 'wishlists.services_id', 'sales.id')
                ->select( 'sales.*','users.id as business_id', 'users.name', 'users.username', 'users.mobile_phone',
                    'profiles.city_id','cities.city','states.state','countries.country','countries.phonecode', 'wishlists.wishlist_status')
                ->where('sales.id', $saleid)
                // ->where('sales.saledate_to', '>=', $currentDate)
                // ->where('sales.sale_status', 1)
                ->first();
                $sale->saledate_from = date("d-m-Y", strtotime($sale->saledate_from));
                $sale->saledate_to = date("d-m-Y", strtotime($sale->saledate_to));
            $sale['user_sale_wishlist'] = $user_sale_wishlist;

            return response()->json([
                'status' => 200,
                'message' => 'Sale Detail',
                'sdetail' => $sale,
            ]);
        }
    }

    public function updatesql()
    {
        $jobs = Job::get();
        
        $sales = Sale::get();

        $videos = Video::get();

        $hotdeals = Hotdeal::get();

        foreach($jobs as $job_title)
        {
            $job = Job::find($job_title->id);
            $job->job_slug = strtolower(Str::slug($job_title->job_title,'-')).'-'.Str::random(4);
            $job->update();

        }

        foreach($sales as $job_title)
        {
            $job = Sale::find($job_title->id);
            $job->sale_slug = strtolower(Str::slug($job_title->sale_title,'-')).'-'.Str::random(4);
            $job->update();

        }

        foreach($hotdeals as $job_title)
        {
            $job = Hotdeal::find($job_title->id);
            $job->hotdeal_slug = strtolower(Str::slug($job_title->hot_deal_title,'-')).'-'.Str::random(4);
            $job->update();

        }

        foreach($videos as $job_title)
        {
            $job = Video::find($job_title->id);
            $job->video_slug = strtolower(Str::slug($job_title->video_title,'-')).'-'.Str::random(4);
            $job->update();

        }
    }
}
