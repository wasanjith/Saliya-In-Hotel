<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePOS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('AuthenticatePOS middleware called', [
            'url' => $request->url(),
            'authenticated' => Auth::check(),
            'user_id' => Auth::id()
        ]);

        if (!Auth::check()) {
            Log::warning('User not authenticated, redirecting to login', [
                'url' => $request->url(),
                'ip' => $request->ip()
            ]);
            return redirect('/login');
        }

        Log::info('User authenticated, proceeding to POS', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'url' => $request->url()
        ]);

        return $next($request);
    }
}
