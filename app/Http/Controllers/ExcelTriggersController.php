<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelTrigger;

class ExcelTriggersController extends Controller
{
   protected $type = "triggers";

   public function read()
   {
      $triggers = ExcelTrigger::parse('Template-Trigger.xlsx');
      return $triggers;
   }

   public function download()
   {
      $client = app('zendesk.source.auth');
      $excelTrigger = new ExcelTrigger($client);
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
