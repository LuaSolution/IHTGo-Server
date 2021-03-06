<?php

namespace App\Models;

use App\Helpers\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dept extends Model
{
    protected $fillable = [
      'company_id', 'status', 'from', 'to', 'money'
    ];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::creating(function ($dept){
            $dept->money = static::getMoney($dept->company_id, $dept->from, $dept->to);
        });
    }

    public static function getMoney($companyID, $from, $to)
    {
        $result = DB::table('customers as c')
            ->leftJoin('orders as o', 'o.user_id', '=', 'c.user_id')
            ->where(['c.company_id' => $companyID, 'o.status' => Business::ORDER_DELIVERY_DONE])
            ->whereBetween('o.created_at', [$from, $to])
            ->sum('o.total_price');
        return $result;
    }
}
