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

namespace App\Http\Controllers\Profile;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

// Model
use App\User;
use App\Models\Pengguna;

class ProfileController extends Controller
{
    protected $view  = 'pages.profile.';
    protected $title = 'Profile';
    protected $route = 'profile.';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $user_id = Auth::user()->id;
        $data    = Pengguna::where('user_id', $user_id)->first();

        return view($this->view . 'index', compact(
            'route',
            'title',
            'data'
        ));
    }

    public function update()
    {
        // 
    }

    public function editPassword($id)
    {
        $route  = $this->route;
        $title  = $this->title;

        $userId = $id;
        $data = User::find($id);

        return view($this->view . 'edit-password', compact(
            'route',
            'title',
            'userId',
            'data'
        ));
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        $password = $request->password;

        User::where('id', $id)->update([
            'password' => Hash::make($password)
        ]);

        return response()->json([
            'message' => 'Password berhasil diperbaharui.'
        ]);
    }
}
