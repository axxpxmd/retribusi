<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class TransaksiOPD extends Model
{
    protected $table = 'tmtransaksi_opd';
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

    // 
    public static function queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis)
    {
        $data = TransaksiOPD::orderBy('id', 'DESC');

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($jenis_pendapatan_id) {
            $data->where('id_jenis_pendapatan', $jenis_pendapatan_id);
        }

        if ($status_bayar != null) {
            $data->where('status_bayar', $status_bayar);
        }

        if ($jenis == 1 || $jenis == 0) {
            if ($from != null || $to != null) {
                if ($from != null && $to == null) {
                    $data->whereDate('tgl_skrd_awal', $from);
                } else {
                    $data->whereBetween('tgl_skrd_awal', [$from, $to]);
                }
            }
        } elseif ($jenis == 2) {
            if ($from != null || $to != null) {
                if ($from != null && $to == null) {
                    $data->whereDate('tgl_bayar', $from);
                } else {
                    $data->whereBetween('tgl_bayar', [$from, $to]);
                }
            }
        }

        return $data->get();
    }

    // 
    public static function queryDiskon($opd_id, $jenis_pendapatan_id, $from, $to, $status_diskon, $no_skrd)
    {
        $data = TransaksiOPD::orderBy('id', 'DESC')->where('status_bayar', 0);

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($jenis_pendapatan_id) {
            $data->where('id_jenis_pendapatan', $jenis_pendapatan_id);
        }

        if ($status_diskon != null) {
            $data->where('status_diskon', $status_diskon);
        }

        if ($no_skrd != null) {
            $data->where('no_skrd', 'like', '%' . $no_skrd . '%');
        }

        if ($from != null ||  $to != null) {
            if ($from != null && $to == null) {
                $data->whereDate('tgl_skrd_awal', $from);
            } else {
                $data->whereBetween('tgl_skrd_awal', [$from, $to]);
            }
        }

        return $data->get();
    }

    // 
    public static function queryDenda($opd_id, $jenis_pendapatan_id, $from, $to, $status_denda_filter, $no_skrd)
    {
        $data = TransaksiOPD::orderBy('id', 'DESC')->where('status_bayar', 0);

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($jenis_pendapatan_id) {
            $data->where('id_jenis_pendapatan', $jenis_pendapatan_id);
        }

        if ($status_denda_filter != null) {
            $data->where('status_denda', $status_denda_filter);
        }

        if ($no_skrd != null) {
            $data->where('no_skrd', 'like', '%' . $no_skrd . '%');
        }

        if ($from != null ||  $to != null) {
            if ($from != null && $to == null) {
                $data->whereDate('tgl_skrd_awal', $from);
            } else {
                $data->whereBetween('tgl_skrd_awal', [$from, $to]);
            }
        }

        return $data->get();
    }

    // 
    public static function querySKRD($from, $to, $opd_id)
    {
        $data = TransaksiOPD::orderBy('id', 'DESC');

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($from != null ||  $to != null) {
            if ($from != null && $to == null) {
                $data->whereDate('tgl_skrd_awal', $from);
            } else {
                $data->whereBetween('tgl_skrd_awal', [$from, $to]);
            }
        }

        return $data->get();
    }

    // 
    public static function querySTS($from, $to, $opd_id, $status_bayar, $jenis_tanggal)
    {
        $data = TransaksiOPD::orderBy('id', 'DESC');

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($status_bayar != null) {
            $data->where('status_bayar', $status_bayar);
        }

        if ($jenis_tanggal == 1) {
            if ($from != null || $to != null) {
                if ($from != null && $to == null) {
                    $data->whereDate('tgl_skrd_awal', $from);
                } else {
                    $data->whereBetween('tgl_skrd_awal', [$from, $to]);
                }
            }
        } elseif ($jenis_tanggal == 2) {
            if ($from != null || $to != null) {
                if ($from != null && $to == null) {
                    $data->whereDate('tgl_bayar', $from);
                } else {
                    $data->whereBetween('tgl_bayar', [$from, $to]);
                }
            }
        }

        return $data->get();
    }
}
