<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;

class ExcelBrandsController extends Controller
{
   public function read()
   {
      $client = new ZendeskAPI('treesdemo1');
      $client->setHeader('Authorization', "basic ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz");
      // $client->setAuth("basic", ["username" => "eldien.hasmanto@treessolutions.com", "token" => "ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz"]);

      $tickets = $client->tickets()->findAll(['page' => 1]);
      return $tickets->tickets;
   }
}
