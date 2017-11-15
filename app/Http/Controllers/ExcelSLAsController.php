<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelSLA;

class ExcelSLAsController extends Controller
{
   public function home()
   {
      return view('excel.slas');
   }

   public function download()
   {
      $client = new ZendeskAPI("treesdemo1");
      $client->setHeader('Authorization', "basic ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz");

      $excelSLA = new ExcelSLA($client);
      return $excelSLA->toExcel()->download('xlsx');
   }

   public function upload()
   {
      
   }
}
