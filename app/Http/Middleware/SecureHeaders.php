<?php
namespace App\Http\Middleware;
use Closure;

class SecureHeaders
{
    
    private $unwantedHeaderList = [
        'X-Powered-By',
        'Server',
    ];
    public function handle($request, Closure $next)
    {
        $this->removeUnwantedHeaders($this->unwantedHeaderList);
        $response = $next($request);
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        // $response->headers->set('Content-Security-Policy', "default-src 'self' 'unsafe-inline' 'unsafe-eval' code.ionicframework.com fonts.googleapis.com fonts.gstatic.com cdn.datatables.net cdn.jsdelivr.net cdnjs.cloudflare.com ckeditor.com");
        // $response->headers->set('Access-Control-Allow-Methods', 'GET,POST');
        // $response->headers->set('Access-Control-Allow-Headers', 'Content-Type,Authorization,X-Requested-With,X-CSRF-Token');
        return $response;
    }
    private function removeUnwantedHeaders($headerList) 
    {
        foreach ($headerList as $header)
            header_remove($header);
    }
}
?>