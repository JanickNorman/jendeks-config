<?php

namespace App\Http\Middleware;

use Closure;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use \Exception;
use \Session;

class ZendeskSourceAuth
{

   protected $client;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if (Session::has('zendesk_source_auth')) {
         $subdomain = Session::get('zendesk_source_auth.subdomain');
         $token = Session::get('zendesk_source_auth.token');

         $this->client = new ZendeskAPI("$subdomain");
         $this->client->setHeader("Authorization", $token);

         Cache::flush();

         app()['zendesk.source.auth'] = $this->client;

         $this->cacheResources();

         // $response = $this->client->brands()->find("11234");

         return $next($request);
      }

      return redirect()->back()->withErrors('Zendesk source is not authenticated');
    }

    private function cacheResources()
    {
      // $subdomain = $this->client->getSubdomain();
      //
      // // Cache Ticket Fields
      // if (!Cache::get("$subdomain.ticket_fields")){
      //    try {
      //       $ticket_fields = $this->client->get('api/v2/ticket_fields')->ticket_fields;
      //
      //       foreach ($ticket_fields as $ticket_field)
      //          Cache::remember("$subdomain.ticket_fields.")
      //       }
      //
      //    } catch (Exception $e) {
      //
      //    }
      // }
      //
      // // Cache Ticket Fields options

   }
}
