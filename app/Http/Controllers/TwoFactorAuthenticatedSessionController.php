<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;
use Laravel\Fortify\Fortify;

class TwoFactorAuthenticatedSessionController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param  \Laravel\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return mixed
     */
    public function store(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();

        if ($user->two_factor_type === 'email') {
            if ($code = $request->validRecoveryCode()) {
                $user->replaceRecoveryCode($code);
            } elseif (! $this->hasValidEmailCode($user, $request->code)) {
                return back()->withErrors(['code' => __('The provided two-factor authentication code was invalid.')]);
            }

            $this->guard->login($user, $request->remember());

            $request->session()->regenerate();

            return app(TwoFactorLoginResponse::class);
        }

        // Fallback to standard TOTP behavior by re-instantiating the request processing 
        // through the original controller logic would be hard because it's a controller.
        // Instead, we can just replicate the standard logic:
        
        // Standard TOTP validation logic (copied from Fortify essentially)
        // But Fortify's Request handles the validation automatically in some versions? 
        // No, Fortify's controller simply calls $request->hasValidCode().
        
        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);
        } elseif (! $request->hasValidCode()) {
            return back()->withErrors(['code' => __('The provided two-factor authentication code was invalid.')]);
        }

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        return app(TwoFactorLoginResponse::class);
    }

    /**
     * Determine if the user has a valid email code.
     */
    protected function hasValidEmailCode($user, $code)
    {
        if (empty($code)) {
            return false;
        }

        $cacheKey = 'two_factor_code_' . $user->id;
        $cachedCode = Cache::get($cacheKey);

        if ($cachedCode && $cachedCode === $code) {
            Cache::forget($cacheKey);
            return true;
        }

        return false;
    }
}
