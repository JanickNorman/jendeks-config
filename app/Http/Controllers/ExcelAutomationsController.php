<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelAutomation;

class ExcelAutomationsController extends Controller
{
   protected $type = "automations";

   public function download()
   {
      $client = app('zendesk.source.auth');
      $excelAutomation = new ExcelAutomation($client);
      return $excelAutomation->toExcel()->download('xlsx');
   }

   public function upload()
   {

   }
}
