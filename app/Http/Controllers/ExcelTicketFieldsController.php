<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;
use Zendesk\API\Exceptions\ApiResponseException;
use App\ZendeskExcel\ExcelTicketField;
use \Excel;
use \Cache;

class ExcelTicketFieldsController extends Controller
{
   public function home()
   {
      return view('excel.ticketfields');
   }

   public function show()
   {

   }

   public function download()
   {
      $client = new ZendeskAPI("treesdemo1");
      $client->setHeader('Authorization', "basic ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz");

      // Cache ticket fields for testing purpose
      $ticket_fields_response = Cache::remember('ticket_fields_mock', 60, function() use ($client) {
         return $client->ticketFields()->findAll(['page' => 1]);
      });

      $excelTicketField = new ExcelTicketField($ticket_fields_response);
      // dd($excelTicketField->toExcel()->getActiveSheet()->toArray());
      return $excelTicketField->toExcel()->download('xlsx');
   }

   public function upload(Request $request)
   {
      $this->validate($request,[
          'resource-excel-file' => 'required|file',
      ]);

      return back()->with('zendesk_ticketfields_excel', json_encode(['arriba', 'cartel']));
   }
}
