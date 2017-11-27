<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelMacro;

class ExcelMacrosController extends Controller
{
   protected $type = "macros";

   public function read()
   {
      $macros = ExcelGroup::parse('Template-Group.xlsx');
      return $macros;
   }

   public function download()
   {
      $client = app('zendesk.source.auth');
      $excelMacro = new ExcelMacro($client);
      return $excelMacro->toExcel()->download('xlsx');
   }

   public function upload(Request $request)
   {
   }
}
