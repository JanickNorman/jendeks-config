<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use Zendesk\API\Exceptions\ApiResponseException;
use \Session;
use \Cache;

class ZendeskLoginController extends Controller
{
   public function sourceLogin(Request $request)
   {
      Session::forget('zendesk_source_auth');

      $this->validate($request, [
         'source_subdomain' => 'required',
         'source_username' => 'required',
         'source_password' => 'required'
      ]);

      try {
         $client = new ZendeskAPI("$request->source_subdomain");
         $client->setHeader('Authorization', "basic ".base64_encode("$request->source_username:$request->source_password"));

         Cache::flush();

         // WARNING!! This is a very hackable way to test if th euser enter a valid username and password for a given subdomain
         $client->triggers()->findAll(['page' => 1]);
      } catch (ApiResponseException $e) {
         return redirect()->back()->withErrors($e->getErrorDetails());
      }

      $credentials = [
         "subdomain" => $request->source_subdomain,
         "token" => "basic ".base64_encode("$request->source_username:$request->source_password")
      ];
      Session::put('zendesk_source_auth', $credentials, 60);

      return redirect()->back()->withSuccess('success login to subdomain: '."<b>$request->source_subdomain</b>.zendesk.com");
   }
}
