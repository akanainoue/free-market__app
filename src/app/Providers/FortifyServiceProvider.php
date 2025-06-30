<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(\App\Http\Controllers\Auth\RegisterController::class);

        // Fortify::registerView(function () {
        //     return view('auth.register');
        // });
        // Fortify::loginView(function () {
        //     return view('auth.login');
        // });


        // Fortify::authenticateUsing(function (LoginRequest $request){
        //     $credentials = $request->only('email', 'password');
        //     if (Auth::attempt($credentials)){
        //         return Auth::user();
        //     }
        //     throw ValidationException::withMessages([
        //         'email' => ['ログイン情報が登録されていません'],
        //     ]);
            // フィールド名は password でもいいが、一般的に email にまとめる
        // });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
