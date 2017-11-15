<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelAutomation;

class ExcelAutomationsController extends Controller
{
   public function home()
   {
      return view('excel.automations');
   }

   public function download()
   {
      $client = new ZendeskAPI("treesdemo1");
      $client->setHeader('Authorization', "basic ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz");

      // Cache ticket fields for testing purpose
      $automations_response = Cache::remember('automations_mock', 60, function() use ($client) {
         return $client->automations()->findAll(['page' => 1]);
      });
      $excelAutomation = new ExcelAutomation($automations_response);
      // dd($excelAutomation->toExcel()->getActiveSheet()->toArray());
      return $excelAutomation->toExcel()->download('xlsx');
   }

   public function upload()
   {

   }
}
