<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class GetUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        $response = Http::withHeaders([
            "Accept" => "application/json",
            "Authorization" => $header
        ])->get('http://127.0.0.1:8000/api/user');
        if($response->status() !== 200){
            return \response()->json([
                'message' => $response
            ], $response->status());
        }
        $request->merge(['user' => $response->json()]);
        return $next($request);
    }
}
