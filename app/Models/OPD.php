<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OPD extends Model
{
    protected $table = 'tmopds';
    protected $guarded = [];

    public function transaksi_opd()
    {
        return $this->hasMany(TransaksiOPD::class, 'id_opd', 'id');
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
}
