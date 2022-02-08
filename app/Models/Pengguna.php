<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Models
use App\User;
use Spatie\Permission\Models\Role;

class Pengguna extends Model
{
    protected $table = 'tmpenggunas';
    protected $fillable = ['id', 'user_id', 'opd_id', 'full_name', 'nip', 'email', 'phone', 'api_key', 'url_callback', 'photo'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function opd()
    {
        return $this->belongsTo(OPD::class, 'opd_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function modelHasRole()
    {
        return $this->belongsTo(ModelHasRoles::class, 'user_id', 'model_id');
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
