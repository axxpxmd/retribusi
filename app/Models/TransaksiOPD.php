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
        $data = TransaksiOPD::with(['jenis_pendapatan', 'opd', 'rincian_jenis'])->orderBy('id', 'DESC');

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
            $from = $from . ' ' . '00:00:01';
            $to = $to . ' ' . '23:59:59';

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
        $data = TransaksiOPD::with(['jenis_pendapatan', 'opd', 'rincian_jenis'])->where('status_bayar', 0)
            ->where('status_ttd', 0)
            ->orderBy('id', 'DESC');

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
        $data = TransaksiOPD::with(['jenis_pendapatan', 'opd', 'rincian_jenis'])->where('status_bayar', 0)
            ->where('status_ttd', 0)
            ->orderBy('id', 'DESC');

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
    public static function querySKRD($from, $to, $opd_id, $no_skrd, $status_ttd)
    {
        $now = Carbon::now();
        $date = $now->format('Y-m-d');

        $data = TransaksiOPD::select('id', 'id_opd', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'tgl_skrd_akhir', 'status_ttd', 'jumlah_bayar')
            ->with('opd', 'jenis_pendapatan')->where('status_bayar', 0)->where('tgl_skrd_akhir', '>=', $date);

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($no_skrd != null) {
            $data->where('no_skrd', 'like', '%' . $no_skrd . '%');
        }

        if ($status_ttd != null) {
            $data->where('status_ttd', $status_ttd);
        }

        if ($from != null ||  $to != null) {
            if ($from != null && $to == null) {
                $data->whereDate('tgl_skrd_awal', $from);
            } else {
                $data->whereBetween('tgl_skrd_awal', [$from, $to]);
            }
        }

        return $data->orderBy('id', 'DESC')->get();
    }

    // 
    public static function querySTRD($from, $to, $opd_id, $no_skrd, $status_ttd)
    {
        $now = Carbon::now();
        $date = $now->format('Y-m-d');

        $data = TransaksiOPD::with('opd', 'jenis_pendapatan')->where('status_bayar', 0)->where('tgl_skrd_akhir', '<', $date);

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($no_skrd != null) {
            $data->where('no_skrd', 'like', '%' . $no_skrd . '%');
        }

        if ($status_ttd != null) {
            $data->where('status_ttd', $status_ttd);
        }

        if ($from != null ||  $to != null) {
            if ($from != null && $to == null) {
                $data->whereDate('tgl_skrd_akhir', '<', $from);
            } else {
                $data->whereBetween('tgl_skrd_akhir', [$from, $to]);
            }
        }

        return $data->orderBy('id', 'DESC')->get();
    }

    // 
    public static function querySTS($from, $to, $opd_id, $status_bayar, $jenis_tanggal, $no_bayar)
    {
        $data = TransaksiOPD::select('id', 'id_opd', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'status_ttd', 'ntb', 'tgl_bayar', 'total_bayar_bjb', 'status_bayar')
            ->with('opd', 'jenis_pendapatan');

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($status_bayar != null) {
            $data->where('status_bayar', $status_bayar);
        }

        if ($no_bayar != null) {
            $data->where('no_bayar', 'like', '%' . $no_bayar . '%');
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
            $from = $from . ' ' . '00:00:01';
            $to = $to . ' ' . '23:59:59';

            if ($from != null || $to != null) {
                if ($from != null && $to == null) {
                    $data->whereDate('tgl_bayar', $from);
                } else {
                    $data->whereBetween('tgl_bayar', [$from, $to]);
                }
            }
        }

        return $data->orderBy('id', 'DESC')->get();
    }

    // 
    public static function queryTandaTangan($from, $to, $opd_id, $no_skrd, $status_ttd)
    {
        $data = TransaksiOPD::with(['jenis_pendapatan', 'opd', 'rincian_jenis'])->whereIn('status_ttd', [1, 2, 3, 4])
            ->orderBy('id', 'DESC');

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($no_skrd != null) {
            $data->where('no_skrd', 'like', '%' . $no_skrd . '%');
        }

        if ($status_ttd == 2) {
            $data->whereIn('status_ttd', [0, 2, 4]);
        }

        if ($status_ttd == 1) {
            $data->whereIn('status_ttd', [1, 3]);
        }

        if ($status_ttd == null) {
            $data->whereIn('status_ttd', [0, 2, 4]);
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
}
