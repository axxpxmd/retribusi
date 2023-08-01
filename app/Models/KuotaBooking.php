<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class KuotaBooking extends Model
{
    protected $table = 'tm_kuota_bookings';
    protected $guarded = [];

    public static function queryKuotaBooking()
    {
        $role  = Auth::user()->pengguna->modelHasRole->role->name;
        $opd_id = Auth::user()->pengguna->opd_id;

        $datas = KuotaBooking::when($opd_id != 0 && $role != 'super-admin' && $role != 'admin-bjb' , function ($q) use ($opd_id) {
            return $q->where('id', $opd_id);
        })->get();

        return $datas;
    }

    public function opd()
    {
        return $this->belongsTo(OPD::class, 'id_opd');
    }
}
