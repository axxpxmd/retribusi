<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDelete extends Model
{
    protected $table = 'tmtransaksi_opd_del';
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

    public static function queryTable($from, $to, $opd_id, $no_skrd)
    {
        $data = TransaksiDelete::select('id', 'id_opd', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'tgl_skrd_akhir', 'status_ttd', 'jumlah_bayar', 'history_ttd')
            ->with('opd', 'jenis_pendapatan')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                return $q->where('id_opd', $opd_id);
            })
            ->when($no_skrd != null, function ($q) use ($no_skrd) {
                return $q->where('no_skrd', 'like', '%' . $no_skrd . '%');
            });

        if ($from != null ||  $to != null) {
            if ($from != null && $to == null) {
                $data->whereDate('tgl_skrd_awal', $from);
            } else {
                $data->whereBetween('tgl_skrd_awal', [$from, $to]);
            }
        }

        return $data->orderBy('id', 'DESC')->get();
    }
}
