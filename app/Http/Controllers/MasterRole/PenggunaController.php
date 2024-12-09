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

namespace App\Http\Controllers\MasterRole;

use DataTables;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Stevebauman\Purify\Facades\Purify;

// Model
use App\User;
use App\Models\OPD;
use App\Models\TtdOPD;
use App\Models\Pengguna;
use App\Models\ModelHasRoles;
use Spatie\Permission\Models\Role;
use App\Models\OPDJenisPendapatan;

class PenggunaController extends Controller
{
    protected $route = 'master-role.pengguna.';
    protected $view  = 'pages.masterRole.pengguna.';
    protected $path  = '../images/ava/';
    protected $title = 'Pengguna';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:Pengguna']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;
        $path  = $this->path;

        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $roles = Role::select('id', 'name')->whereNotIn('id', [5])->get();
        // $opds = OPD::select('id', 'n_opd')->whereIn('id', $opdArray)->get();
        $opds = OPD::select('id', 'n_opd')->get();

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
        $opd_id  = $request->opd_id;
        $role_id = $request->role_id;

        $data = Pengguna::queryTable($opd_id, $role_id);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                return "
                <a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus Permission'><i class='icon icon-remove'></i></a>
                <a href='#' onclick='show(" . $p->id . ")' title='show data'><i class='icon icon-eye3 mr-1'></i></a>";
            })
            ->editColumn('full_name', function ($p) {
                return "<a href='" . route($this->route . 'edit', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->full_name . "</a>";
            })
            ->editColumn('opd', function ($p) {
                if ($p->opd == null) {
                    return '-';
                } else {
                    return $p->opd->n_opd;
                }
            })
            ->editColumn('role', function ($p) {
                if ($p->modelHasRole == null) {
                    return '-';
                } else {
                    return $p->modelHasRole->role->name;
                }
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
            'full_name' => 'required|string|max:100',
            'email'   => 'required|max:100|string|email|unique:tmpenggunas,email',
            'role_id' => 'required',
            'phone'   => 'required|max:20',
        ]);

        //TODO: Validation Penanda Tangan
        if ($request->role_id == 11)
            $request->validate([
                'nip'    => 'required|numeric|digits:18|unique:tmpenggunas,nip',
                'nik'    => 'required|numeric|digits:16|unique:tmpenggunas,nik',
                'opd_id' => 'required'
            ]);

        //TODO: Validation untuk akun OPD
        if ($request->role_id != 7)
            $request->validate([
                'opd_id' => 'required'
            ]);

        //TODO: Validation untuk User API
        if ($request->role_id == 12) {
            $checkApiKey = Pengguna::where('opd_id', $request->opd_id)->whereNotNull('api_key')->first();

            if ($checkApiKey) {
                return response()->json([
                    'message' => "OPD sudah memiliki API Key."
                ], 422);
            }
        }

        //TODO: Check opd_id
        if ($request->role_id == 7) {
            $opd_id = 0;
        } else {
            $opd_id = $request->opd_id;
        }

        /* Tahapan :
         * 1. tmusers
         * 2. tmpenggunas
         * 3. model_has_roles
         */

        // Tahap 1
        $username  = $request->username;
        $password  = $request->password;

        $user = new User();
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->save();

        //* Role User API
        if ($request->role_id == 12) {
            $api_key = md5($user->id . $username . $user->created_at . $opd_id);
            $url_callback = $request->url_callback;
        } else {
            $api_key = '';
            $url_callback = '';
        }

        // Tahap 2
        $dataPengguna = [
            'user_id' => $user->id,
            'opd_id'  => $opd_id,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'photo'     => 'default.png',
            'nip'       => $request->nip,
            'nik'       => $request->nik,
            'api_key'   => $api_key,
            'url_callback' => $url_callback
        ];

        Pengguna::create($dataPengguna);

        // Tahap 3
        $path = 'app\User';
        $role_id = $request->role_id;

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
        $id    = Crypt::decrypt($id);
        $route = $this->route;
        $title = $this->title;
        $path  = 'images/ava/';

        $pengguna = Pengguna::find($id);

        $roles = Role::select('id', 'name')->whereNotIn('id', [5])->get();
        $opds  = OPD::select('id', 'n_opd')->get();

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
        $id = Crypt::decrypt($id);
        $pengguna = Pengguna::find($id);
        $user_id  = $pengguna->user_id;

        //* Handle XSS
        $input = $request->all();
        $cleanText = Purify::clean($input);

        if (!$cleanText['full_name']) {
            return response()->json([
                'message' => 'Karakter dilarang!. Cek kembali pada inputan, terdapat karakter yang dilarang.'
            ], 422);
        }

        //* Validation
        $request->validate([
            'username'  => 'required|max:50|unique:tmusers,username,' . $user_id,
            'full_name' => 'required|max:100',
            'email' => 'required|max:100|email|unique:tmpenggunas,email,' . $id,
            'phone' => 'required|max:20',
            'role_id' => 'required'
        ]);

        //TODO: Validation Penanda Tangan
        if ($request->role_id == 11)
            $request->validate([
                'nip'    => 'required|numeric|digits:18|unique:tmpenggunas,nip,' . $id,
                'nik'    => 'required|numeric|digits:16|unique:tmpenggunas,nik,' . $id,
                'opd_id' => 'required'
            ]);

        //TODO: Validation untuk akun OPD
        if ($request->role_id != 7)
            $request->validate([
                'opd_id' => 'required'
            ]);

        //TODO: Check opd_id
        if ($request->role_id == 7) {
            $opd_id = 0;
        } else {
            $opd_id = $request->opd_id;
        }

        //* Get Data
        $username  = $request->username;
        $full_name = $cleanText['full_name'];
        $email   = $request->email;
        $phone   = $request->phone;
        $role_id = $request->role_id;
        $nip     = $request->nip;
        $nik     = $request->nik;

        /* Tahapan :
         * 1. tmusers
         * 2. tmpenggunas
         * 3. model_has_roles
         */

        // Tahap 1
        User::where('id', $user_id)->update([
            'username' => $username
        ]);

        // Tahap 2
        $pengguna->update([
            'full_name' => $full_name,
            'email'  => $email,
            'phone'  => $phone,
            'opd_id' => $opd_id,
            'nip'    => $nip,
            'nik'    => $nik
        ]);

        // Tahap 3
        $model_has_role = ModelHasRoles::where('model_id', $user_id);
        $model_has_role->update([
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
         * 3. tr_ttd_opds
         */

        // Tahap 1
        $pengguna = Pengguna::findOrFail($id);

        if ($pengguna->photo != 'default.png') {
            // Proses Delete Foto
            $exist = $pengguna->photo;
            $path  = $this->path . $exist;
            \File::delete(public_path($path));
        }
        $pengguna->delete();

        // Tahap 2
        User::whereid($pengguna->user_id)->delete();

        // Tahap 3
        TtdOPD::where('user_id', $pengguna->user_id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
