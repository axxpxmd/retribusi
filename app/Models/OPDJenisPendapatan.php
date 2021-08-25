<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OPDJenisPendapatan extends Model
{
    protected $table   = 'tmopd_jenis_pendapatan';
    protected $guarded = [];
    public $timestamps = false;

    public function opd()
    {
        return $this->belongsTo(OPD::class, 'id_opd');
    }

    public function jenis_pendapatan()
    {
        return $this->belongsTo(JenisPendapatan::class, 'id_jenis_pendapatan');
    }

    public function transaksi_pendapatan()
    {
        return $this->hasMany(TransaksiOPD::class, 'id_jenis_pendapatan', 'id_jenis_pendapatan');
    }
}
