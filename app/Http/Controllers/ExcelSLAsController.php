<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelSLA;

class ExcelSLAsController extends Controller
{
   protected $type = "slas";

   public function download()
   {
      $client = app('zendesk.source.auth');
      $excelSLA = new ExcelSLA($client);
      return $excelSLA->toExcel()->download('xlsx');
   }

   public function upload()
   {

   }
}
