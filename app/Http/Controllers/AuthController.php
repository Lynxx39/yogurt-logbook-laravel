<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {

    public function showLogin() {
        if (session('user')) return redirect('/');
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Username atau password salah.'])->withInput();
        }

        session(['user' => [
            'id'         => $user->id,
            'name'       => $user->name,
            'username'   => $user->username,
            'role'       => $user->role,
            'group_name' => $user->group_name,
        ]]);

        return redirect($user->role === 'guru' ? '/teacher' : '/student');
    }

    public function showRegister() {
        if (session('user')) return redirect('/');
        return view('auth.login', ['tab' => 'register']);
    }

    public function register(Request $request) {
        $request->validate([
            'name'       => 'required|string|max:100',
            'group_name' => 'required|string|max:100',
            'username'   => 'required|string|max:50|unique:users,username',
            'password'   => 'required|string|min:4',
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'password.min'    => 'Password minimal 4 karakter.',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'role'       => 'siswa',
            'group_name' => $request->group_name,
        ]);

        // Create empty logbook for student
        Logbook::create(['user_id' => $user->id]);

        session(['user' => [
            'id'         => $user->id,
            'name'       => $user->name,
            'username'   => $user->username,
            'role'       => $user->role,
            'group_name' => $user->group_name,
        ]]);

        return redirect('/student')->with('success', 'Akun berhasil dibuat!');
    }

    public function logout(Request $request) {
        $request->session()->flush();
        return redirect('/login');
    }
}
