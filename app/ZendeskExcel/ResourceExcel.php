<?php

namespace App\ZendeskExcel;

use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use App\ZendeskExcel\Formatter\DisplayFormatter;
use App\ZendeskExcel\Formatter\DisplayRepository;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Excel;

abstract class ResourceExcel
{
   protected $name = "";

   protected $client;

   protected $display;

   protected $sheet;

   protected $headers = [[]];

   public function __construct(ZendeskAPI $client)
   {
      $this->client = $client;
      $this->display = new DisplayFormatter(new DisplayRepository($client));
   }

   public function toExcel(): LaravelExcelWriter
   {
      $self = $this;

      $this->generateResources();

      return Excel::create("template:treesdemo1:$this->name", function($excel)  use ($self) {
         $excel->sheet("template--$self->name", function($sheet) use ($self) {
            $self->sheet = $sheet;

            $self->buildHeader();
            $self->buildBody();
         });
      });
   }

   abstract public function read($filepath): Array;

   abstract protected function generateResources();

   protected function buildHeader()
   {
      foreach ($this->headers as $key => $header) {
         $this->sheet->row((int) $key + 1, $header);
      }

      $this->mergeHeaderRows();

      $style = [
         'alignment' => [
            'horizontal' => 'center',
         ],
         'font' => [
            'bold' => true
         ]
      ];

      $highestColumn = $this->getHighestColumn();
      $this->sheet->setBorder("A1:$highestColumn".count($this->headers), 'thin');
      foreach ($this->headers as $key => $header) {
         $row_num = (int) $key + 1;
         $this->sheet->getStyle("A$row_num:".$highestColumn."$row_num")->applyFromArray($style);

      }
   }

   abstract protected function buildBody();

   protected function mergeHeaderRows()
   {
      //
   }

   protected function getStartingRow()
   {
      return (int) count($this->headers) + 1;
   }

   protected function setCell(array $contents, $row_num)
   {
      foreach ($contents as $column => $value) {
         $this->sheet->setCellValue($column.$row_num, $value);
      }
   }

   protected function getHighestColumn()
   {
      return $this->toAlphabet(array_reduce($this->headers, function ($a, $b) {
          return count($a) > count($b) ? count($a) : count($b) ;
      }));
   }

   protected function styleCurrentRow($current_row, $next_row)
   {
      $upper_row = $current_row;
      $bottom_row = $next_row - 1;
      $right_row = $this->getHighestColumn();
      $this->sheet->getStyle("A$upper_row:$right_row$bottom_row")->applyFromArray(
         array(
           'borders' => array(
             'outline' => array(
               'style' => \PHPExcel_Style_Border::BORDER_THIN,
             )
           )
         )
      );
   }

   private function toAlphabet($num)
   {
      return chr(substr("000".($num+64),-3));
   }
}
