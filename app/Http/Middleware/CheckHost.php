<?php

namespace App\Http\Middleware;

use Closure;
use Log;

class CheckHost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            $error = 1;
            $domains = array('127.0.0.1','localhost');
            foreach($domains as $domain)
            {
                if (strpos($_SERVER['SERVER_NAME'], $domain) !== false)
                {
                    $error=0;
                    break;
                }
            }
            if ($error>0)
            {
                return response()->view('error.denied');
            }else{
                return $next($request);
            }

        }catch(\Exception $e){
            Log::error('TrustedHosts-handle: '.$e->getMessage());         
            return view('error.home');
        }
    
        // $allowedHosts = ['127.0.0.1','localhost', 'edmshry.nic.in', '164.100.200.54']; // Not a domain yet

        // if (!in_array($request->getHost(), $allowedHosts)) {
        //     abort(403);
        // }

        // return $next($request);
    
    }
}