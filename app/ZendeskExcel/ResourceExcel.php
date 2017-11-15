<?php

namespace App\ZendeskExcel;

use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use App\ZendeskExcel\Formatter\DisplayFormatter;
use App\ZendeskExcel\Formatter\DisplayFactory;
use Zendesk\API\HttpClient as ZendeskAPI;

abstract class ResourceExcel
{
   protected $client;

   protected $display;

   public function __construct(ZendeskAPI $client)
   {
      $this->client = $client;
      $this->display = new DisplayFormatter(new DisplayFactory($client));
   }

   abstract public function toExcel(): LaravelExcelWriter;

   abstract public function read($filepath): Array;

   protected function setCell($sheet, array $contents, $row_num)
   {
      foreach ($contents as $column => $value) {
         $sheet->setCellValue($column.$row_num, $value);
      }
   }
}
