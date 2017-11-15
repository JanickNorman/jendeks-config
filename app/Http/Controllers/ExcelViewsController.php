<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelView;

class ExcelViewsController extends Controller
{
   public function home()
   {
      return view('excel.views');
   }

   public function download()
   {
      $client = new ZendeskAPI("treesdemo1");
      $client->setHeader('Authorization', "basic ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz");

      // Cache ticket fields for testing purpose
      $views_response = Cache::remember('views_mock', 60, function() use ($client) {
         return $client->views()->findAll(['page' => 1]);
      });

      $excelView = new ExcelView($views_response);
      return $excelView->toExcel()->download('xlsx');
   }

   public function upload()
   {

   }
}
