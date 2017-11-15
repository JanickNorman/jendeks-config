<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelGroup;

class ExcelGroupsController extends Controller
{
   public function home()
   {
      return view('excel.groups');
   }

   public function read()
   {
   }

   public function download()
   {
      $client = new ZendeskAPI("treesdemo1");
      $client->setHeader('Authorization', "basic ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz");

      // Cache ticket fields for testing purpose
      $groups_response = Cache::remember('groups_mock', 60, function() use ($client) {
         return $client->groups()->findAll(['page' => 1]);
      });

      $excelGroup = new ExcelGroup($groups_response);
      // dd($excelGroup->toExcel()->getActiveSheet()->toArray());
      return $excelGroup->toExcel()->download('xlsx');
   }

   public function upload(Request $request)
   {
   }
}
