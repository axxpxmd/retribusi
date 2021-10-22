<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Models
use App\User;

class TtdOPD extends Model
{
    protected $table   = 'tr_ttd_opds';
    protected $guarded = [];

    public function opd()
    {
        return $this->belongsTo(OPD::class, 'id_opd');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
