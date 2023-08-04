<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Setting;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Jobs\SendConfirmationEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('panel.authentication.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {

        $settings = Setting::first();
        if ((bool)$settings->login_without_confirmation == false) {
            $user = User::where('email', $request->email)->first();
            if ($user->email_confirmed != 1 and $user->type != 'admin') {
                dispatch(new SendConfirmationEmail($user));
                $data = array(
                    'errors' => ['We have sent you an email for account confirmation. Please confirm your account to continue. Please also check your spam folder'],
                    'type' => 'confirmation',
                );
                return response()->json($data, 401);
            }
        }

        $request->authenticate();


        $request->session()->regenerate();

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
