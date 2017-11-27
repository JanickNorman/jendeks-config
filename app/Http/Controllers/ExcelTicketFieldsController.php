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
   protected $type = "ticketfields";

   public function download()
   {
      $client = app('zendesk.source.auth');
      $excelTicketField = new ExcelTicketField($client);
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
