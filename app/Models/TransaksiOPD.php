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

    public static function checkStatusTTD($status_ttd)
    {
        if ($status_ttd == 1 || $status_ttd == 3) {
            $status_ttd = true;
        } else {
            $status_ttd = false;
        }

        return $status_ttd;
    }

    public static function checkDenda($denda)
    {
        if ($denda == 0 || $denda == null) {
            $status_denda = 0;
        } else {
            $status_denda = 1;
        }

        return $status_denda;
    }

    public static function getDiffDate($tgl_jatuh_tempo)
    {
        $startDate = Carbon::parse($tgl_jatuh_tempo . ' 23:59:59');
        $endDate   = Carbon::now();

        $dayDiff = $startDate->diffInDays($endDate);
        $monthDiff = $startDate->diffInMonths()($endDate);

        return [$dayDiff, $monthDiff];
    }

    // 
    public static function queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id)
    {
        $data = TransaksiOPD::select('id', 'id_opd', 'id_rincian_jenis_pendapatan', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'status_ttd', 'ntb', 'tgl_bayar', 'total_bayar', 'total_bayar_bjb', 'jumlah_bayar', 'status_bayar', 'chanel_bayar', 'rincian_jenis_pendapatan')
            ->with(['jenis_pendapatan', 'opd', 'rincian_jenis'])->orderBy('id', 'DESC');

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($jenis_pendapatan_id != 0) {
            $data->where('id_jenis_pendapatan', $jenis_pendapatan_id);
        }

        if ($rincian_pendapatan_id != 0) {
            $data->where('id_rincian_jenis_pendapatan', $rincian_pendapatan_id);
        }

        if ($jenis == 1 || $jenis == 0) {
            if ($status_bayar != 0 || $status_bayar != null) {
                $data->where('status_bayar', $status_bayar);
            }

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

            $data->where('status_bayar', 1);

            if ($from != null || $to != null) {
                if ($from != null && $to == null) {
                    $data->whereDate('tgl_bayar', $from);
                } else {
                    $data->whereBetween('tgl_bayar', [$from, $to]);
                }
            }
        }

        /**
         * 1. VA
         * 2. ATM
         * 3. BJB Mobile
         * 4. Teller
         * 5. QRIS
         * 6. Bendahara OPD
         * 7. Transfer RKUD
         * 8. RTGS/SKN
         * 9. Lainnya
         */

        if ($channel_bayar != 0) {
            switch ($channel_bayar) {
                case "1":
                    $data->where('chanel_bayar', 'like', '%Virtual Account%');
                    break;
                case 2:
                    $data->where('chanel_bayar', 'like', '%ATM%');
                    break;
                case 3;
                    $data->where('chanel_bayar', 'like', '%MOBIL%');
                    break;
                case 4;
                    $data->where('chanel_bayar', 'like', '%TELLER%');
                    break;
                case 5;
                    $data->where('chanel_bayar', 'like', '%QRIS%');
                    break;
                case 6;
                    $data->where('chanel_bayar', 'like', '%Bendahara%');
                    break;
                case 7;
                    $data->where('chanel_bayar', 'like', '%Transfer RKUD%');
                    break;
                case 8;
                    $data->where('chanel_bayar', 'like', '%RTGS/SKN%');
                    break;
                case 9;
                    $data->where('chanel_bayar', 'like', '%Lainnya%');
                    break;
                default:
                    $data->where('chanel_bayar', 'like', '%Lainnya%');
                    break;
                    break;
            }
        }

        return $data->get();
    }

    // 
    public static function queryReportCetak($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id)
    {
        $data = TransaksiOPD::select('id', 'id_opd', 'id_rincian_jenis_pendapatan', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'status_ttd', 'ntb', 'tgl_bayar', 'total_bayar', 'total_bayar_bjb', 'jumlah_bayar', 'status_bayar', 'chanel_bayar')
            ->with(['jenis_pendapatan']);

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($jenis_pendapatan_id != 0) {
            $data->where('id_jenis_pendapatan', $jenis_pendapatan_id);
        }

        if ($rincian_pendapatan_id != 0) {
            $data->where('id_rincian_jenis_pendapatan', $rincian_pendapatan_id);
        }

        if ($jenis == 1 || $jenis == 0) {
            if ($status_bayar != 0 || $status_bayar != null) {
                $data->where('status_bayar', $status_bayar);
            }

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

        /**
         * 1. VA
         * 2. ATM
         * 3. BJB Mobile
         * 4. Teller
         * 5. QRIS
         * 6. Bendahara OPD
         * 7. Transfer RKUD
         * 8. RTGS/SKN
         * 9. Lainnya
         */

        if ($channel_bayar != 0) {
            switch ($channel_bayar) {
                case "1":
                    $data->where('chanel_bayar', 'like', '%Virtual Account%');
                    break;
                case 2:
                    $data->where('chanel_bayar', 'like', '%ATM%');
                    break;
                case 3;
                    $data->where('chanel_bayar', 'like', '%MOBIL%');
                    break;
                case 4;
                    $data->where('chanel_bayar', 'like', '%TELLER%');
                    break;
                case 5;
                    $data->where('chanel_bayar', 'like', '%QRIS%');
                    break;
                case 6;
                    $data->where('chanel_bayar', 'like', '%Bendahara%');
                    break;
                case 7;
                    $data->where('chanel_bayar', 'like', '%Transfer RKUD%');
                    break;
                case 8;
                    $data->where('chanel_bayar', 'like', '%RTGS/SKN%');
                    break;
                case 9;
                    $data->where('chanel_bayar', 'like', '%Lainnya%');
                    break;
                default:
                    $data->where('chanel_bayar', 'like', '%Lainnya%');
                    break;
                    break;
            }
        }

        return $data->orderBy('id', 'DESC')->get();
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

    //* Query get data SKRD
    public static function querySKRD($from, $to, $opd_id, $no_skrd, $status_ttd)
    {
        $now = Carbon::now();
        $date = $now->format('Y-m-d');

        $data = TransaksiOPD::select('id', 'id_opd', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'tgl_skrd_akhir', 'status_ttd', 'jumlah_bayar', 'history_ttd')
            ->with('opd', 'jenis_pendapatan')
            ->where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '>=', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                return $q->where('id_opd', $opd_id);
            })
            ->when($no_skrd != null, function ($q) use ($no_skrd) {
                return $q->where('no_skrd', 'like', '%' . $no_skrd . '%');
            })
            ->when($status_ttd != null, function ($q) use ($status_ttd) {
                return $q->where('status_ttd', $status_ttd);
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
    public static function querySTS($from, $to, $opd_id, $status_bayar, $jenis_tanggal, $no_bayar, $channel_bayar)
    {
        $now = Carbon::now();
        $date = $now->format('Y-m-d');

        $data = TransaksiOPD::select('id', 'id_opd', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'status_ttd', 'ntb', 'tgl_bayar', 'total_bayar_bjb', 'status_bayar', 'chanel_bayar')
            ->with('opd', 'jenis_pendapatan');

        if ($opd_id != 0) {
            $data->where('id_opd', $opd_id);
        }

        if ($status_bayar != null) {
            $data->where('status_bayar', $status_bayar);
        }

        if ($channel_bayar != 0) {
            switch ($channel_bayar) {
                case "1":
                    $metode_bayar = 'BJB Virtual Account';
                    $data->where('chanel_bayar', $metode_bayar);
                    break;
                case 2:
                    $metode_bayar = 'ATM BJB';
                    $data->where('chanel_bayar', $metode_bayar);
                    break;
                case 3;
                    $metode_bayar = '';
                    $data->where('chanel_bayar', 'like', '%QRIS%');
                    break;
                default:
                    // 
                    break;
            }
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
    public static function queryTandaTangan($belum_ttd, $from, $to, $opd_id, $no_skrd, $status_ttd)
    {
        $data = TransaksiOPD::with(['jenis_pendapatan', 'opd', 'rincian_jenis'])->orderBy('id', 'ASC')->whereNotIn('status_ttd', [0]);

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

        if ($belum_ttd != 1) {
            if ($from != null ||  $to != null) {
                if ($from != null && $to == null) {
                    $data->whereDate('tgl_skrd_awal', $from);
                } else {
                    $data->whereBetween('tgl_skrd_awal', [$from, $to]);
                }
            }
        }

        return $data->get();
    }
}
