<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class OPD extends Model
{
    protected $table = 'tmopds';
    protected $guarded = [];

    public function transaksi_opd()
    {
        return $this->hasMany(TransaksiOPD::class, 'id_opd', 'id')->whereYear('created_at', Carbon::now()->format('Y'));
    }

    public function getApiKey()
    {
        return $this->hasOne(Pengguna::class, 'opd_id', 'id')->whereNotNull('api_key')->withDefault([
            'api_key' => ''
        ]);
    }

    public function countJenisPendapatan()
    {
        return $this->hasMany(OPDJenisPendapatan::class, 'id_opd', 'id');
    }

    public function countPenandaTangan()
    {
        return $this->hasMany(TtdOPD::class, 'id_opd', 'id');
    }

    public static function getAll($opdArray, $opd_id)
    {
        $role  = Auth::user()->pengguna->modelHasRole->role->name;

        $data = OPD::select('id', 'n_opd')->whereIn('id', $opdArray)
            ->when($opd_id != 0 && $role != 'super-admin' && $role != 'admin-bjb' , function ($q) use ($opd_id) {
                return $q->where('id', $opd_id);
            })->get();

        return $data;
    }
}
