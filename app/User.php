<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

// Models
use App\Models\Pengguna;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $table = 'tmusers';
    protected $fillable = ['username', 'password'];
    protected $hidden = ['password',];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id', 'user_id');
    }

    public static function checkRole($role)
    {
        if ($role == 'super-admin' || $role == 'admin-opd') {
            $readonly = '';
        } else {
            $readonly = 'readonly';
        }

        return $readonly;
    }
}
