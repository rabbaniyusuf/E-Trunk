<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   public function login(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get authenticated user
        $user = Auth::user();

        // Set welcome message dengan role
        $roleName = $user->roles->first()?->name ?? 'user';
        $roleDisplay = $this->getRoleDisplayName($roleName);

        session()->flash('success', "Selamat datang, {$user->name}! Anda login sebagai {$roleDisplay}.");

        // Redirect berdasarkan role
        $redirectUrl = $this->getRedirectUrlByRole($user);

        return redirect()->intended($redirectUrl);
    }

    /**
     * Display the registration view.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }

    /**
     * Display the password reset link request view.
     */
    // public function showForgotPassword()
    // {
    //     return view('auth.forgot-password');
    // }

    /**
     * Handle an incoming password reset link request.
     */
    // public function sendResetLink(Request $request): RedirectResponse
    // {
    //     $request->validate(['email' => 'required|email']);

    //     $status = Password::sendResetLink($request->only('email'));

    //     return $status === Password::RESET_LINK_SENT ? back()->with(['status' => __($status)]) : back()->withErrors(['email' => __($status)]);
    // }

    /**
     * Display the password reset view.
     */
    // public function showResetPassword(string $token): View
    // {
    //     return view('auth.reset-password', ['token' => $token]);
    // }

    /**
     * Handle an incoming new password request.
     */
    // public function resetPassword(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'token' => 'required',
    //         'email' => 'required|email',
    //         'password' => 'required|min:8|confirmed',
    //     ]);

    //     $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
    //         $user
    //             ->forceFill([
    //                 'password' => Hash::make($password),
    //             ])
    //             ->setRememberToken(Str::random(60));

    //         $user->save();

    //         event(new PasswordReset($user));
    //     });

    //     return $status === Password::PASSWORD_RESET ? redirect()->route('login')->with('status', __($status)) : back()->withErrors(['email' => [__($status)]]);
    // }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

     private function getRedirectUrlByRole(User $user): string
    {
        if ($user->hasRole('petugas_pusat')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('petugas_kebersihan')) {
            return route('petugas.dashboard');
        }

        if ($user->hasRole('masyarakat')) {
            return route('user.dashboard');
        }

        // Default fallback
        return route('home');
    }

     private function getRoleDisplayName(string $roleName): string
    {
        $roleNames = [
            'petugas_pusat' => 'Petugas Pusat',
            'petugas_kebersihan' => 'Petugas Kebersihan',
            'masyarakat' => 'Masyarakat',
        ];

        return $roleNames[$roleName] ?? 'User';
    }
}