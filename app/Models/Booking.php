<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'tm_bookings';
    protected $guarded = [];

    public static function queryBooking()
    {
        $datas = Booking::all();

        return $datas;
    }
}
