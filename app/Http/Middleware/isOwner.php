<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;


class isOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        $owner = Role::where('name','owner')->first();

        if($user->role_id === $owner->id){
            return $next($request);
        }
        
        return response()->json(['message' => 'Hanya bisa diakses oleh Owner'], 401);
    }
}
