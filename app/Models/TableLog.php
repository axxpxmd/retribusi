<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableLog extends Model
{
    protected $table = 'logs';
    protected $guarded = [];

    public function dataRetribusi()
    {
        return $this->belongsTo(TransaksiOPD::class, 'id_retribusi', 'id');
    }

    public static function queryTable($channel_bayar, $from, $to, $status)
    {
        $from = $from . ' ' . '00:00:01';
        $to = $to . ' ' . '23:59:59';

        $data = TableLog::select('id', 'no_bayar', 'id_retribusi', 'ntb', 'waktu', 'jenis', 'waktu', 'status')
            ->whereBetween('waktu', [$from, $to])
            ->when($status != 0, function ($q) use ($status) {
                $q->where('status', $status);
            });

        if ($channel_bayar != 0) {
            switch ($channel_bayar) {
                case "1":
                    $data->where('jenis', 'like', '%Virtual Account%');
                    break;
                case 2:
                    $data->where('jenis', 'like', '%ATM%');
                    break;
                case 3;
                    $data->where('jenis', 'like', '%MOBIL%');
                    break;
                case 4;
                    $data->where('jenis', 'like', '%TELLER%');
                    break;
                case 5;
                    $data->where('jenis', 'like', '%QRIS%');
                    break;
                case 6;
                    $data->where('jenis', 'like', '%Bendahara%');
                    break;
                case 7;
                    $data->where('jenis', 'like', '%Transfer RKUD%');
                    break;
                case 8;
                    $data->where('jenis', 'like', '%RTGS/SKN%');
                    break;
                case 9;
                    $data->where('jenis', 'like', '%Lainnya%');
                    break;
                default:
                    $data->where('jenis', 'like', '%Lainnya%');
                    break;
            }
        }

        return $data->orderBy('logs.id', 'DESC')->get();
    }
}
