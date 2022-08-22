<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPendapatan extends Model
{
    protected $table = 'tmjenis_pendapatan';
    protected $guarded = [];
    public $timestamps = false;

    public static function queryTable()
    {
        $data =  JenisPendapatan::select('id', 'jenis_pendapatan', 'target_pendapatan');

        return $data->orderBy('id', 'DESC')->get();
    }

    public function opdJenisPendapatans()
    {
        return $this->hasMany(OPDJenisPendapatan::class, 'id_jenis_pendapatan', 'id');
    }

    public function transaksiOPDs()
    {
        return $this->hasMany(TransaksiOPD::class, 'id_jenis_pendapatan', 'id');
    }

    public function rincianJenisPendapatans()
    {
        return $this->hasMany(RincianJenisPendapatan::class, 'id_jenis_pendapatan', 'id');
    }
}
