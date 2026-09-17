<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Audit;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        config(['fortify.guard' => 'web', 'fortify.username' => 'email', 'fortify.home' => '/', 'fortify.views' => true, 'fortify.features' => [], 'fortify.limiters.login' => 'login']);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(fn () => view('login'));
        Fortify::authenticateUsing(function ($request) {
            $user = User::where('email', $request->email)->orWhere('username', $request->email)->first();

            return $user && $user->active && Hash::check($request->password, $user->password) ? $user : null;
        });
        RateLimiter::for('login', fn ($request) => Limit::perMinute(5)
            ->by(strtolower($request->email).'|'.$request->ip())
            ->response(function ($request, array $headers) {
                $message = __('auth.throttle', ['seconds' => $headers['Retry-After']]);

                if ($request->expectsJson()) {
                    return response()->json(['message' => $message, 'errors' => ['email' => [$message]]], 429, $headers);
                }

                return redirect()->route('login')
                    ->withErrors(['email' => $message])
                    ->withInput($request->only('email'))
                    ->withHeaders($headers);
            }));
        Event::listen(Login::class, fn ($event) => Audit::record('login', 'users', $event->user->id));
    }
}
