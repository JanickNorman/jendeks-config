<?php

namespace App\Http\Middleware;

use Closure;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Session;

class ZendeskDestinationAuth
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
      if (Session::has('zendesk_destination_auth')) {
         $subdomain = Session::get('zendesk_destination_auth.subdomain');
         $token = Session::get('zendesk_destination_auth.token');

         $client = new ZendeskAPI("$subdomain");
         $client->setHeader("Authorization", $token);

         app()['zendesk.destination.auth'] = $client;

         return $next($request);
      }

      return redirect()->back()->withErrors('Zendesk destination is not authenticated');
    }
}
