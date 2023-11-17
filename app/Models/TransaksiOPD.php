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
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id')->withDefault([
            'n_kelurahan' => '-'
        ]);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id')->withDefault([
            'n_kecamatan' => '-'
        ]);
    }

    public static function queryReportDashboard($opd_id, $status, $tahun)
    {
        $date  = Carbon::now()->format('Y-m-d');

        /** Status from dashboard
         * 1. SKRD
         * 2. STS
         */

        $data = TransaksiOPD::select('id', 'id_opd', 'id_rincian_jenis_pendapatan', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'status_ttd', 'ntb', 'tgl_bayar', 'total_bayar', 'total_bayar_bjb', 'jumlah_bayar', 'status_bayar', 'chanel_bayar', 'rincian_jenis_pendapatan', 'tgl_skrd_akhir', 'tgl_skrd_awal')
            ->with(['jenis_pendapatan', 'opd', 'rincian_jenis'])
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->whereYear('tmtransaksi_opd.created_at', $tahun);

        switch ($status) {
            case '1':
                $data->where('status_bayar', 0)->where('tgl_skrd_akhir', '>=', $date);
                break;
            case '2':
                $data->where('status_bayar', 1);
                break;
            default:
                # code...
                break;
        }

        return $data->orderBy('id', 'DESC')->get();
    }

    //
    public static function queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id, $status = null, $tahun = null)
    {
        switch ($status) {
            case '1':
                return self::queryReportDashboard($opd_id, $status, $tahun);
                break;
            case '2':
                return self::queryReportDashboard($opd_id, $status, $tahun);
                break;
            default:
                $data = TransaksiOPD::select('id', 'id_opd', 'id_rincian_jenis_pendapatan', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'status_ttd', 'ntb', 'tgl_bayar', 'total_bayar', 'total_bayar_bjb', 'jumlah_bayar', 'status_bayar', 'chanel_bayar', 'rincian_jenis_pendapatan', 'tgl_skrd_akhir', 'tgl_skrd_awal', 'kelurahan_id', 'kecamatan_id')
                    ->with(['jenis_pendapatan', 'opd', 'rincian_jenis', 'kelurahan', 'kecamatan'])
                    ->when($opd_id != 0, function ($q) use ($opd_id) {
                        $q->where('id_opd', $opd_id);
                    })
                    ->when($jenis_pendapatan_id != 0, function ($q) use ($jenis_pendapatan_id) {
                        $q->where('id_jenis_pendapatan', $jenis_pendapatan_id);
                    })
                    ->when($rincian_pendapatan_id != 0, function ($q) use ($rincian_pendapatan_id) {
                        $q->where('id_rincian_jenis_pendapatan', $rincian_pendapatan_id);
                    });

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
                    }
                }

                return $data->orderBy('id', 'DESC')->get();
                break;
        }
    }

    //
    public static function queryDiskon($opd_id, $jenis_pendapatan_id, $from, $to, $status_diskon, $no_skrd)
    {
        $data = TransaksiOPD::with(['jenis_pendapatan', 'opd', 'rincian_jenis'])->where('status_bayar', 0)
            ->where('status_ttd', 0)
            ->orderBy('id', 'DESC')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })
            ->when($jenis_pendapatan_id, function ($q) use ($jenis_pendapatan_id) {
                $q->where('id_jenis_pendapatan', $jenis_pendapatan_id);
            })
            ->when($status_diskon != null, function ($q) use ($status_diskon) {
                $q->where('status_diskon', $status_diskon);
            })
            ->when($no_skrd != null, function ($q) use ($no_skrd) {
                $q->where('no_skrd', 'like', '%' . $no_skrd . '%');
            });

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
            ->orderBy('id', 'DESC')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })
            ->when($jenis_pendapatan_id != 0, function ($q) use ($jenis_pendapatan_id) {
                $q->where('id_jenis_pendapatan', $jenis_pendapatan_id);
            })
            ->when($status_denda_filter != null, function ($q) use ($status_denda_filter) {
                $q->where('status_denda', $status_denda_filter);
            })
            ->when($no_skrd != null, function ($q) use ($no_skrd) {
                $q->where('no_skrd', 'like', '%' . $no_skrd . '%');
            });

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
        $date =  Carbon::now()->format('Y-m-d');

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

    public static function checkDuplicateNoBayar($date, $opd_id, $from, $to)
    {
        $getDuplicate = TransaksiOPD::select('no_bayar')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                return $q->where('id_opd', $opd_id);
            })
            ->where('status_ttd', 0)
            ->groupBy('no_bayar')
            ->havingRaw("COUNT(no_bayar) > 1");

        if ($from != null ||  $to != null) {
            if ($from != null && $to == null) {
                $getDuplicate->whereDate('tgl_skrd_awal', $from);
            } else {
                $getDuplicate->whereBetween('tgl_skrd_awal', [$from, $to]);
            }
        } else {
            $getDuplicate->whereDate('created_at', $date);
        }
        $getDuplicate = $getDuplicate->get()->toArray();

        $data = TransaksiOPD::select('id', 'id_opd', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'tgl_skrd_akhir', 'status_ttd', 'jumlah_bayar', 'history_ttd')
            ->with('opd', 'jenis_pendapatan')
            ->whereIn('no_bayar', $getDuplicate)
            ->where('status_ttd', 0)
            ->get();

        return [$getDuplicate, $data];
    }

    //
    public static function querySTRD($from, $to, $opd_id, $no_skrd, $status_ttd, $status = null, $tahun = null)
    {
        $date = Carbon::now()->format('Y-m-d');

        $data = TransaksiOPD::select('id', 'id_opd', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'tgl_skrd_akhir', 'status_ttd', 'jumlah_bayar', 'history_ttd', 'tgl_strd_akhir')
            ->with('opd', 'jenis_pendapatan')
            ->where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '<', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })
            ->when($no_skrd != null, function ($q) use ($no_skrd) {
                $q->where('no_skrd', 'like', '%' . $no_skrd . '%');
            });

        if ($status_ttd != null) {
            if ($status_ttd == 0) {
                $data->whereIn('status_ttd', [0, 1, 2]);
            } else {
                $data->where('status_ttd', $status_ttd);
            }
        }

        switch ($status) {
            case '1':
                $data->whereYear('tmtransaksi_opd.created_at', $tahun);
                break;
            case '2':
                return $data->orderBy('id', 'DESC')->get();
                break;
            default:
                if ($from != null ||  $to != null) {
                    if ($from != null && $to == null) {
                        $data->whereDate('tgl_skrd_akhir', '<', $from);
                    } else {
                        $data->whereBetween('tgl_skrd_akhir', [$from, $to]);
                    }
                }
                break;
        }

        return $data->orderBy('id', 'DESC')->get();
    }

    //
    public static function querySTS($from, $to, $opd_id, $status_bayar, $jenis_tanggal, $no_bayar, $status)
    {
        $now = Carbon::now();
        $date = $now->format('Y-m-d');

        $data = TransaksiOPD::select('id', 'id_opd', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'status_ttd', 'ntb', 'tgl_bayar', 'total_bayar_bjb', 'status_bayar', 'chanel_bayar')
            ->with('opd', 'jenis_pendapatan')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })
            ->when($status_bayar != null, function ($q) use ($status_bayar) {
                $q->where('status_bayar', $status_bayar);
            })
            ->when($no_bayar != null, function ($q) use ($no_bayar) {
                $q->where('no_bayar', 'like', '%' . $no_bayar . '%');
            });

        switch ($status) {
            case '1':
                $data->where('tgl_bayar', $now);
                break;
            case '2':
                $data->whereDate('tgl_skrd_awal', $date);
                break;

            default:
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
                break;
        }

        return $data->orderBy('id', 'DESC')->get();
    }

    //
    public static function queryTandaTangan($belum_ttd, $from, $to, $opd_id, $no_skrd, $status_ttd, $nip)
    {
        $data = TransaksiOPD::with(['jenis_pendapatan', 'opd', 'rincian_jenis'])->whereNotIn('status_ttd', [0])
            ->when($nip, function ($q) use ($nip) {
                $q->where('nip_ttd', $nip);
            })
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })
            ->when($no_skrd != null, function ($q) use ($no_skrd) {
                $q->where('no_skrd', 'like', '%' . $no_skrd . '%');
            })
            ->when($status_ttd == 2 || $status_ttd == null, function ($q) {
                $q->whereIn('status_ttd', [0, 2, 4]);
            })
            ->when($status_ttd == 1, function ($q) {
                $q->whereIn('status_ttd', [1, 3]);
            });

        if ($belum_ttd != 1) {
            if ($from != null ||  $to != null) {
                if ($from != null && $to == null) {
                    $data->whereDate('tgl_skrd_awal', $from);
                } else {
                    $data->whereBetween('tgl_skrd_awal', [$from, $to]);
                }
            }
        }

        return $data->orderBy('id', 'ASC')->get();
    }

    //
    public static function querySearchNoSkrd($no_skrd)
    {
        $data = TransaksiOPD::select('id', 'id_opd', 'no_skrd', 'no_bayar', 'nm_wajib_pajak', 'id_jenis_pendapatan', 'tgl_skrd_awal', 'tgl_skrd_akhir', 'status_ttd', 'jumlah_bayar', 'history_ttd')
            ->where('status_bayar', 0)
            ->with('opd', 'jenis_pendapatan')
            ->where('no_skrd',  $no_skrd);

        return $data->orderBy('id', 'DESC')->get();
    }
}
