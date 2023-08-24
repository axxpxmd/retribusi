<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Models
use App\User;
use Spatie\Permission\Models\Role;

class Pengguna extends Model
{
    protected $table = 'tmpenggunas';
    protected $fillable = ['id', 'user_id', 'opd_id', 'full_name', 'nip', 'email', 'phone', 'api_key', 'url_callback', 'photo', 'nik'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function opd()
    {
        return $this->belongsTo(OPD::class, 'opd_id')->withDefault([
            'n_opd' => 'Semua'
        ]);
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

    // 
    public static function queryTable($opd_id, $role_id)
    {
        $data = Pengguna::select('id', 'user_id', 'phone', 'full_name', 'opd_id', 'photo')
            ->with(['user', 'role', 'opd', 'modelHasRole.role'])
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'tmpenggunas.user_id')
            ->whereNotIn('model_has_roles.role_id', [5])
            ->when($opd_id, function ($q) use ($opd_id) {
                return $q->where('opd_id', $opd_id);
            })
            ->when($role_id, function ($q) use ($role_id) {
                return $q->where('model_has_roles.role_id', $role_id);
            });

        return $data->orderBy('id', 'DESC')->get();
    }
}
