<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelTrigger;

class ExcelTriggersController extends Controller
{
   public function home()
   {
      return view('excel.triggers');
   }

   public function read()
   {
      $triggers = ExcelTrigger::parse('Template-Trigger.xlsx');
      return $triggers;
   }

   public function download()
   {
      $client = new ZendeskAPI("treesdemo1");
      $client->setHeader('Authorization', "basic ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz");

      // Cache ticket fields for testing purpose
      $trigger_response = Cache::remember('trigger_mock', 60, function() use ($client) {
         return $client->triggers()->findAll(['page' => 1]);
      });

      $excelTrigger = new ExcelTrigger($trigger_response);
      // dd($excelTrigger->toExcel()->getActiveSheet()->toArray());
      return $excelTrigger->toExcel()->download('xlsx');
   }

   public function upload(Request $request)
   {
      $this->validate($request,[
          'resource-excel-file' => 'required|file',
      ]);

      $triggers = ExcelTrigger::parse($request->file('resource-excel-file')->getRealPath());
      return back()->with('triggers', json_encode($triggers));
   }
}
