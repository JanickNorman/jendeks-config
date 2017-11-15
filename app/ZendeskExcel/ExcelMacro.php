<?php

namespace App\ZendeskExcel;

use \Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ExcelMacro extends ResourceExcel
{
   const HEADERS_ROW_1 = ["No", "Title", "Active", "Position", "Description", "Actions", null, "Restriction", null, null];

   const HEADERS_ROW_2 = [null, null, null, null, null, "Field", "Value", "Type", "Id", "Ids"];

   const STARTING_ROW = 3;

   public $macros;

   public function __construct($macros_response = [])
   {
      $this->macros = isset($macros_response->macros) ? collect($macros_response->macros) : [];
   }

   public function toExcel(): LaravelExcelWriter
   {
      $self = $this;

      return Excel::create("template:treesdemo1:macros", function($excel)  use ($self) {
         $excel->sheet("template--macros", function($sheet) use ($self) {
            $self->buildHeader($sheet);
            $self->buildBody($sheet);
         });
      });
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setMacros($macros)
   {
      $this->macros = $macros;
   }

   protected function buildHeader($sheet)
   {
      $sheet->row(1, $this::HEADERS_ROW_1);
      $sheet->row(2, $this::HEADERS_ROW_2);

      $sheet->mergeCells("F1:G1");
      $sheet->mergeCells("H1:J1");
      foreach (range("A","E") as $char) {
         $sheet->mergeCells($char."1:".$char."2");
      }

      $style = [
         'alignment' => [
            'horizontal' => 'center',
         ],
         'font' => [
            'bold' => true
         ]
      ];
      $sheet->getStyle("A1:J1")->applyFromArray($style);
      $sheet->getStyle("A2:J2")->applyFromArray($style);
   }

   protected function buildBody($sheet)
   {
      $self = $this;

      $current_macro_row = self::STARTING_ROW;
      $macros_num = 1;
      $next_macro_row = $current_macro_row + 1;
      $this->macros->each(function($macro) use (&$self, &$sheet, &$current_macro_row, &$macros_num, &$next_macro_row) {
         $initial_contents = [
            "A" => $macros_num,
            "B" => $macro->title,
            "C" => $macro->active,
            "D" => $macro->position,
            "E" => $macro->description,
         ];
         $self->setCell($sheet, $initial_contents, $current_macro_row);

         // Render actions
         $action_render_row = $current_macro_row;
         foreach ($macro->actions as $action) {
            $self->setCell($sheet, ["F" => $action->field], $action_render_row);

            if (is_array($action->value)) {
               foreach ($action->value as $value) {
                  $contents = [
                     "G" => $value
                  ];
                  $self->setCell($sheet, $contents, $action_render_row);
                  $action_render_row++;
               }
            } else {
               $contents = [
                  "G" => $action->value
               ];
               $self->setCell($sheet, $contents, $action_render_row);
               $action_render_row++;
            }

            // Get the next macro row
            if ($action_render_row >= $next_macro_row) {
               $next_macro_row = $action_render_row;
            }
         }

         if ($macro->restriction !== null) {
            // Render Restriction
            $restriction_contents = [
               "H" => $macro->restriction->type,
               "I" => $macro->restriction->id
            ];
            $this->setCell($sheet, $restriction_contents, $current_macro_row);

            if (isset($macro->restriction->ids)) {
               $restriction_ids_render_row = $current_macro_row;
               foreach ($macro->restriction->ids as $id) {
                  $self->setCell($sheet, ["J" => $id], $restriction_ids_render_row);
                  $restriction_ids_render_row++;

                  // Get the next macro row
                  if ($restriction_ids_render_row >= $next_macro_row) {
                     $next_macro_row = $restriction_ids_render_row;
                  }
               }
            }
         }

         $current_macro_row = $next_macro_row;
         $macros_num++;
      });
   }
}
