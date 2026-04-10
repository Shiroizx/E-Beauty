<?php

namespace App\Http\Controllers;

use App\Http\Support\AuthIntended;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function __construct(
        protected CaptchaService $captchaService
    ) {}
    /**
     * Display the login view.
     */
    public function showLoginForm(Request $request)
    {
        AuthIntended::putIntendedFromQuery($request);

        return view('auth.login', [
            'captcha' => $this->captchaService->challengeForForm('login'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request)
    {
        $this->captchaService->validateRequest($request, 'login');

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->isStaff()) {
                ActivityLogger::log(
                    'auth.login',
                    'Login staff: '.$user->email,
                    User::class,
                    $user->id
                );

                return redirect()->intended(route($user->staffDashboardRoute()));
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Display the registration view.
     */
    public function showRegistrationForm(Request $request)
    {
        AuthIntended::putIntendedFromQuery($request);

        return view('auth.register', [
            'captcha' => $this->captchaService->challengeForForm('register'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request)
    {
        $this->captchaService->validateRequest($request, 'register');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->intended(route('home'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Display profile page for authenticated user.
     */
    public function showProfile()
    {
        return view('profile.show', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update authenticated user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Display forgot-password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password', [
            'captcha' => $this->captchaService->challengeForForm('forgot'),
        ]);
    }

    /**
     * Send reset password link.
     */
    public function sendResetLink(Request $request)
    {
        $this->captchaService->validateRequest($request, 'forgot');

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Respons generik: cegah enumerasi email (siapa yang terdaftar).
        Password::sendResetLink($request->only('email'));

        return back()->with(
            'success',
            'Jika alamat email terdaftar di sistem kami, tautan reset sandi telah dikirim. Periksa kotak masuk atau folder spam.'
        );
    }

    /**
     * Display reset-password form.
     */
    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
            'captcha' => $this->captchaService->challengeForForm('reset'),
        ]);
    }

    /**
     * Handle reset-password submission.
     */
    public function resetPassword(Request $request)
    {
        $this->captchaService->validateRequest($request, 'reset');

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __($status));
        }

        return back()->withErrors(['email' => __($status)])->onlyInput('email');
    }
}