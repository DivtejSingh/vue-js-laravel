<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = 'order_id';
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'total_amount',
        'wallet_discount',
        'coupon_discount',
        'status',
        'fulfillment_status',
        'user_company_address_id',
        'delivery_method_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class,'order_id','order_id');
    }

    public function userCompanyAddress()
    {
        return $this->belongsTo(UserCompanyAddress::class,'user_company_address_id','user_company_address_id');
    }

    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class,'delivery_method_id','delivery_method_id');
    }
}
