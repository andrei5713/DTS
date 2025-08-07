<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DepartmentHeadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isDepartmentHead()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Department Head access required.'], 403);
            }
            return redirect()->route('login')->with('error', 'Department Head access required.');
        }

        return $next($request);
    }
}
