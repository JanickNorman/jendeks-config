<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use App\ZendeskExcel\ExcelTicketForm;

class ExcelTicketFormsController extends Controller
{
   public function home()
   {
      return view('excel.ticketforms');
   }

   public function download()
   {
      $client = new ZendeskAPI("treesdemo1");
      $client->setHeader('Authorization', "basic ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz");

      // Cache ticket fields for testing purpose
      $ticket_forms_response = Cache::remember('ticket_forms_mock', 60, function() use ($client) {
         return $client->get('api/v2/ticket_forms');
      });
      $excelTicketForm = new ExcelTicketForm($ticket_forms_response);
      // dd($excelTicketForm->toExcel()->getActiveSheet()->toArray());
      return $excelTicketForm->toExcel()->download('xlsx');

   }

   public function upload()
   {

   }
}
