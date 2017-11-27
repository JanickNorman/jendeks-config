<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelGroup;

class ExcelGroupsController extends Controller
{
   protected $type = "groups";

   public function download()
   {
      $client = app('zendesk.source.auth');
      $excelGroup = new ExcelGroup($client);
      return $excelGroup->toExcel()->download('xlsx');
   }

   public function upload(Request $request)
   {
   }
}
