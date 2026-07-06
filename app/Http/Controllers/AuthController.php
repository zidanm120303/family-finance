<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('pages.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = User::with(['family', 'role'])
            ->where('email', $credentials['login'])
            ->orWhere('username', $credentials['login'])
            ->first();

        if (! $user || ! $this->passwordMatches($user, $credentials['password'])) {
            return back()
                ->withErrors(['login' => 'Email, username, atau password tidak sesuai.'])
                ->onlyInput('login');
        }

        if (! $user->is_active) {
            return back()
                ->withErrors(['login' => 'Akun ini sedang nonaktif. Hubungi admin keluarga.'])
                ->onlyInput('login');
        }

        Auth::login($user, (bool) ($credentials['remember'] ?? false));
        $request->session()->regenerate();
        $user->forceFill(['last_login' => now()])->save();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function passwordMatches(User $user, string $plainPassword): bool
    {
        $passwordHash = (string) $user->password;

        if (str_starts_with($passwordHash, '$2a$')) {
            $normalizedHash = '$2y$'.substr($passwordHash, 4);

            if (! password_verify($plainPassword, $normalizedHash)) {
                return false;
            }

            $user->forceFill(['password' => Hash::make($plainPassword)])->save();

            return true;
        }

        if ((password_get_info($passwordHash)['algoName'] ?? 'unknown') !== 'bcrypt') {
            return false;
        }

        return Hash::check($plainPassword, $passwordHash);
    }
}
