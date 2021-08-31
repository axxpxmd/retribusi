<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of welcome
 *
 * @author Asip Hamdi
 * Github : axxpxmd
 */

namespace App\Http\Controllers\Pengguna;

use Auth;
use DataTables;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

// Model
use App\User;
use App\Models\OPD;
use App\Models\Pengguna;
use App\Models\OPDJenisPendapatan;
use Spatie\Permission\Models\Role;

class PenggunaController extends Controller
{
    protected $route = 'pengguna.';
    protected $view  = 'pages.pengguna.';
    protected $path  = 'images/ava/';
    protected $title = 'Pengguna';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;
        $path  = $this->path;

        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $roles = Role::select('id', 'name')->get();
        $opds = OPD::select('id', 'n_opd')->whereIn('id', $opdArray)->get();

        return view($this->view . 'index', compact(
            'route',
            'title',
            'path',
            'opds',
            'roles'
        ));
    }

    public function api(Request $request)
    {
        $opd_id = $request->opdId;

        $pengguna = Pengguna::whereNotIn('id', [7])->orderBy('id', 'DESC')->get();

        if ($opd_id != 0) {
            $pengguna = Pengguna::where('opd_id', $opd_id)->whereNotIn('id', [7])->orderBy('id', 'DESC')->get();
        }

        return DataTables::of($pengguna)
            ->addColumn('action', function ($p) {
                return "
                <a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus Permission'><i class='icon icon-remove'></i></a>
                <a href='#' onclick='show(" . $p->id . ")' title='show data'><i class='icon icon-eye3 mr-1'></i></a>";
            })
            ->editColumn('full_name', function ($p) {
                return "<a href='" . route($this->route . 'edit', $p->id) . "' class='text-primary' title='Show Data'>" . $p->full_name . "</a>";
            })
            ->editColumn('opd', function ($p) {
                if ($p->opd == null) {
                    return '-';
                } else {
                    return $p->opd->n_opd;
                }
            })
            ->editColumn('role', function ($p) {
                return $p->role->name;
            })
            ->editColumn('user_id', function ($p) {
                return $p->user->username;
            })
            ->editColumn('photo',  function ($p) {
                return "<img width='50' class='img-fluid mx-auto d-block rounded-circle img-circular' alt='foto' src='" . $this->path . $p->photo . "'>";
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'full_name', 'photo'])
            ->toJson();
    }

    public function show($id)
    {
        $pengguna = Pengguna::getDataPengguna($id);

        return $pengguna;
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'  => 'required|max:50|unique:tmusers,username',
            'password'  => 'required|min:8',
            'full_name' => 'required|max:100',
            'email'   => 'required|max:100|email|unique:tmpenggunas,email',
            'opd_id'  => 'required',
            'role_id' => 'required',
            'phone'   => 'required|max:20'
        ]);

        // Get Data
        $path = 'app\User';
        $username  = $request->username;
        $password  = $request->password;
        $full_name = $request->full_name;
        $email   = $request->email;
        $phone   = $request->phone;
        $opd_id  = $request->opd_id;
        $role_id = $request->role_id;

        /* Tahapan : 
         * 1. tmusers
         * 2. tmpenggunas
         * 3. model_has_roles
         */

        // Tahap 1
        $user = new User();
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->save();

        // Tahap 2
        $pengguna = new Pengguna();
        $pengguna->user_id = $user->id;
        $pengguna->opd_id  = $opd_id;
        $pengguna->role_id = $role_id;
        $pengguna->full_name = $full_name;
        $pengguna->email = $email;
        $pengguna->phone = $phone;
        $pengguna->photo = 'default.png';
        $pengguna->save();

        // Tahap 3
        $model_has_role = new ModelHasRoles();
        $model_has_role->role_id    = $role_id;
        $model_has_role->model_type = $path;
        $model_has_role->model_id   = $user->id;
        $model_has_role->save();

        return response()->json([
            'message' => "Data " . $this->title . " berhasil tersimpan."
        ]);
    }

    public function edit($id)
    {
        $route = $this->route;
        $title = $this->title;
        $path  = $this->path;

        $pengguna = Pengguna::find($id);

        $roles = Role::select('id', 'name')->get();
        $opds = OPD::select('id', 'n_opd')->get();

        return view($this->view . 'edit', compact(
            'roles',
            'route',
            'title',
            'path',
            'pengguna',
            'opds'
        ));
    }

    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::find($id);
        $user_id = $pengguna->user_id;

        // Validation
        $request->validate([
            'username'  => 'required|max:50|unique:tmusers,username,' . $user_id,
            'full_name' => 'required|max:100',
            'email' => 'required|max:100|email|unique:tmpenggunas,email,' . $id,
            'phone' => 'required|max:20',
            'opd_id'  => 'required',
            'role_id' => 'required'
        ]);

        // Get Data
        $username  = $request->username;
        $full_name = $request->full_name;
        $email   = $request->email;
        $phone   = $request->phone;
        $opd_id  = $request->opd_id;
        $role_id = $request->role_id;

        /* Tahapan : 
         * 1. tmusers
         * 2. tmpenggunas
         */

        // Tahap 1
        User::where('id', $user_id)->update([
            'username' => $username
        ]);

        // Tahap 2
        $pengguna->update([
            'full_name' => $full_name,
            'email'   => $email,
            'phone'   => $phone,
            'opd_id'  => $opd_id,
            'role_id' => $role_id
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function destroy($id)
    {
        /* Tahapan : 
         * 1. admin_details
         * 2. admins
         */

        // Tahap 1
        $pengguna = Pengguna::findOrFail($id);

        if ($pengguna->photo != 'default.png') {
            // Proses Delete Foto
            $exist = $pengguna->photo;
            $path  = "images/ava/" . $exist;
            \File::delete(public_path($path));
        }
        $pengguna->delete();

        // Tahap 2
        User::whereid($pengguna->user_id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
