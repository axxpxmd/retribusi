<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $table = 'tmpenggunas';
    protected $fillable = ['id', 'user_id', 'opd_id', 'full_name', 'email', 'phone', 'photo'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function opd()
    {
        return $this->belongsTo(OPD::class, 'opd_id');
    }

    /**
     * QUERY
     */

    //  
    public static function getDataPengguna($id)
    {
        $data = Pengguna::join('tmusers', 'tmusers.id', '=', 'tmpenggunas.user_id')
            ->where('tmpenggunas.id', $id)
            ->first();

        return $data;
    }
}
