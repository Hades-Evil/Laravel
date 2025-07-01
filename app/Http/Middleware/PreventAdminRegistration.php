<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->is('register')) {

            $utype = $request->input('utype');
            if ($utype === 'ADM') {
                return redirect()->back()->withErrors([
                    'utype' => 'Admin registration is not allowed. Contact system administrator.'
                ]);
            }
            
            $request->merge(['utype' => 'USR']);
        }
        
        return $next($request);
    }
}
