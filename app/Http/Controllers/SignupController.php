<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SignupController extends Controller
{
    public function create()
    {
        return view('signup');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'username' => ['required', 'alpha_dash', 'max:60', Rule::unique('users')],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);

        $user = DB::transaction(function () use ($data) {
            $firstAccount = ! User::query()->lockForUpdate()->exists();

            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => $firstAccount ? 'admin' : 'analis',
                'active' => true,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();
        Audit::record('signup', 'users', $user->id, null, ['role' => $user->role]);

        return redirect()->route('dashboard')->with('success', $user->role === 'admin'
            ? 'Akun admin pertama berhasil dibuat.'
            : 'Akun Anda berhasil dibuat.');
    }
}
