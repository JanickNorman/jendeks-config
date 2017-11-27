<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ZendeskExcel\ExcelTicketForm;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;

class ExcelTicketFormsController extends Controller
{
   protected $type = "ticketforms";

   public function download()
   {
      $client = app('zendesk.source.auth');
      $excelTicketForm = new ExcelTicketForm($client);
      return $excelTicketForm->toExcel()->download('xlsx');
   }

   public function upload()
   {

   }
}
