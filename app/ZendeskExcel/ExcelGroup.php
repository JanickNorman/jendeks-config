<?php

namespace App\ZendeskExcel;

use \Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ExcelGroup extends ResourceExcel
{
   const HEADERS_ROW_1 = ["No", "Name"];

   const STARTING_ROW = 2;

   public $groups;

   public function __construct($groups_response = [])
   {
      $this->groups = isset($groups_response->groups) ? collect($groups_response->groups) : [];
   }

   public function toExcel(): LaravelExcelWriter
   {
      $self = $this;

      return Excel::create("template:treesdemo1:groups", function($excel)  use ($self) {
         $excel->sheet("template--groups", function($sheet) use ($self) {
            $self->buildHeader($sheet);
            $self->buildBody($sheet);
         });
      });
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setGroups($groups)
   {
      $this->groups = $groups;
   }

   protected function buildHeader($sheet)
   {
      $sheet->row(1, $this::HEADERS_ROW_1);

      $style = [
         'alignment' => [
            'horizontal' => 'center',
         ],
         'font' => [
            'bold' => true
         ]
      ];
      $sheet->getStyle("A1:B1")->applyFromArray($style);
   }

   protected function buildBody($sheet)
   {
      $self = $this;

      $current_group_row = self::STARTING_ROW;
      $groups_num = 1;
      $next_group_row = $current_group_row + 1;
      $this->groups->each(function($group) use (&$self, &$sheet, &$current_group_row, &$groups_num, &$next_group_row) {
         $initial_contents = [
            "A" => $groups_num,
            "B" => $group->name
         ];
         $self->setCell($sheet, $initial_contents, $current_group_row);

         $current_group_row++;
         $groups_num++;
      });
   }
}
