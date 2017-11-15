<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelMacro;

class ExcelMacrosController extends Controller
{
   public function home()
   {
      return view('excel.macros');
   }

   public function read()
   {
      $macros = ExcelGroup::parse('Template-Group.xlsx');
      return $macros;
   }

   public function download()
   {
      $client = new ZendeskAPI("treesdemo1");
      $client->setHeader('Authorization', "basic ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz");

      // Cache ticket fields for testing purpose
      $macros_response = Cache::remember('macros_mock', 60, function() use ($client) {
         return $client->macros()->findAll(['page' => 1]);
      });

      $excelMacro = new ExcelMacro($macros_response);
      // dd($excelGroup->toExcel()->getActiveSheet()->toArray());
      return $excelMacro->toExcel()->download('xlsx');
   }

   public function upload(Request $request)
   {
   }
}
