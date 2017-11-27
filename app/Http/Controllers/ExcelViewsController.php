<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelView;

class ExcelViewsController extends Controller
{
   protected $type = "views";

   public function download()
   {
      $client = app('zendesk.source.auth');
      $excelView = new ExcelView($client);
      return $excelView->toExcel()->download('xlsx');
   }

   public function upload()
   {

   }
}
