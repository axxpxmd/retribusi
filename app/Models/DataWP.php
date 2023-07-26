<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataWP extends Model
{
    protected $table = 'tmdata_wp';
    protected $guarded = [];

    public function jenis_pendapatan()
    {
        return $this->belongsTo(JenisPendapatan::class, 'id_jenis_pendapatan');
    }

    public function rincian_jenis()
    {
        return $this->belongsTo(RincianJenisPendapatan::class, 'id_rincian_jenis_pendapatan');
    }

    public function opd()
    {
        return $this->belongsTo(OPD::class, 'id_opd');
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function riwayatRetribusi($data)
    {
        return TransaksiOPD::where('id_opd', $data->id_opd)
            ->where('id_jenis_pendapatan', $data->id_jenis_pendapatan)
            ->where('id_rincian_jenis_pendapatan', $data->id_rincian_jenis_pendapatan)
            ->where('kecamatan_id', $data->kecamatan_id)
            ->where('kelurahan_id', $data->kelurahan_id)
            ->where('nm_wajib_pajak', $data->nm_wajib_pajak)
            ->orderBy('tgl_skrd_awal', 'ASC')
            ->get();
    }

    public static function queryTable($opd_id, $jenis_pendapatan_id)
    {
        $data = DataWP::select('id', 'id_opd', 'id_jenis_pendapatan', 'nm_wajib_pajak', 'no_telp', 'email')
            ->with(['jenis_pendapatan', 'opd'])
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })
            ->when($jenis_pendapatan_id, function ($q) use ($jenis_pendapatan_id) {
                $q->where('id_jenis_pendapatan', $jenis_pendapatan_id);
            })
            ->orderBy('id', 'DESC');

        return $data->get();
    }
}
