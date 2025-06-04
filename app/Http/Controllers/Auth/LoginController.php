<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the post-login redirect path based on user role.
     *
     * @return string
     */
    public function redirectTo()
    {
        $user = auth()->user();

        // Redirect berdasarkan role
        if ($user->hasRole('petugas_pusat')) {
            return route('admin.dashboard');
        } elseif ($user->hasRole('petugas_kebersihan')) {
            return route('petugas.dashboard');
        } elseif ($user->hasRole('masyarakat')) {
            return route('user.dashboard');
        }

        // Default redirect jika tidak ada role yang cocok
        return '/dashboard';
    }

    /**
     * The user has been authenticated.
     * Method ini dipanggil setelah user berhasil login
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Optional: Log activity atau set session data
        session(['last_login' => now()]);

        // Optional: Set flash message welcome
        $roleName = $user->roles->first()?->name ?? 'user';
        $roleDisplay = ucwords(str_replace('_', ' ', $roleName));

        session()->flash('success', "Selamat datang, {$user->name}! Anda login sebagai {$roleDisplay}.");

        // Return redirect akan menggunakan redirectTo() method
        return redirect()->intended($this->redirectTo());
    }

    /**
     * Handle logout and redirect
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function loggedOut(Request $request)
    {
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Override login method untuk custom validation messages
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the failed login response instance with custom message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()
            ->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => 'Email atau password salah. Silakan coba lagi.',
            ]);
    }

    /**
     * Validate the user login request dengan custom messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate(
            [
                $this->username() => 'required|email',
                'password' => 'required|string',
            ],
            [
                'email.required' => 'Email harus diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Password harus diisi.',
            ],
        );
    }
}
