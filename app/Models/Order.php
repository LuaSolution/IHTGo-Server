<?php

namespace App\Models;

use App\Helpers\Business;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Order extends BaseModel
{

    protected $fillable = [
        'code', 'name', 'car_type', 'total_price', 'payment_type', 'user_id', 'status', 'is_payment', 'car_option', 'is_admin', 'coupon_code'
    ];

    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::creating(function ($order){
            $order->code = static::generateOrderCode();
        });
    }

    /**
     * @return string
     */
    public static function generateOrderCode()
    {
        $countRecordToday = Order::whereDate('created_at',  Carbon::now()->toDateString())->count();
        $countRecordToday = (int) $countRecordToday + 1;
        do{
            $orderCode = sprintf("IHT%s%'.03d", date('Ymd'), $countRecordToday);
            $countRecordToday++;
        }while (Order::where('code', $orderCode)->first());
        return $orderCode;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function detail()
    {
        return $this->hasOne(OrderDetail::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(Image::class, 'service_id', 'id')->where(['type' => Business::IMAGE_UPLOAD_TYPE_ORDER])->select(['id', 'type']);
    }

    public function driverDevice($orderID)
    {
        $token = DB::table('deliveries as d')
            ->join('drivers as dr', 'd.id', '=', 'd.driver_id')
            ->join('devices as de', 'de.user_id', '=', 'dr.user_id')
            ->where(['d.order_id' => $orderID])
            ->value('fcm');
        return $token;
    }

    public function route()
    {
        return $this->hasMany(OrderDelivery::class);
    }

    public function receive()
    {
        return $this->hasMany(OrderReceive::class);
    }
}