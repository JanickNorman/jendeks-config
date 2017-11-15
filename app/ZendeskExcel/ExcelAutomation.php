<?php

namespace App\ZendeskExcel;

use \Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ExcelAutomation extends ResourceExcel
{
   const HEADERS_ROW_1 = ["No", "Title", "Active", "Position", "Actions", null, "Conditions", null, null, null];

   const HEADERS_ROW_2 = [null, null, null, null, "Field", "Value", "Type", "Field", "Operator", "Value"];

   const STARTING_ROW = 3;

   public $automations;

   public function __construct($automations_response = [])
   {
      $this->automations = isset($automations_response->automations) ? collect($automations_response->automations) : [];
   }

   public function toExcel(): LaravelExcelWriter
   {
      $self = $this;

      return Excel::create("template:treesdemo1:automations", function($excel)  use ($self) {
         $excel->sheet("template--automations", function($sheet) use ($self) {
            $self->buildHeader($sheet);
            $self->buildBody($sheet);
         });
      });
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setAutomations($automations)
   {
      $this->automations = $automations;
   }

   protected function buildHeader($sheet)
   {
      $sheet->row(1, $this::HEADERS_ROW_1);
      $sheet->row(2, $this::HEADERS_ROW_2);

      $sheet->mergeCells("E1:F1");
      $sheet->mergeCells("G1:J1");
      foreach (range("A","D") as $char) {
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

      $current_automation_row = self::STARTING_ROW;
      $automations_num = 1;
      $next_automation_row = $current_automation_row + 1;
      $this->automations->each(function($automation) use (&$self, &$sheet, &$current_automation_row, &$automations_num, &$next_automation_row) {
         $initial_contents = [
            "A" => $automations_num,
            "B" => $automation->title,
            "C" => $automation->active,
            "D" => $automation->position,
         ];
         $self->setCell($sheet, $initial_contents, $current_automation_row);

         // Render actions
         $action_render_row = $current_automation_row;
         foreach ($automation->actions as $action) {
            $self->setCell($sheet, ["E" => $action->field], $action_render_row);

            if (is_array($action->value)) {
               foreach ($action->value as $value) {
                  $contents = [
                     "F" => $value
                  ];
                  $self->setCell($sheet, $contents, $action_render_row);
                  $action_render_row++;
               }
            } else {
               $contents = [
                  "F" => $action->value
               ];
               $self->setCell($sheet, $contents, $action_render_row);
               $action_render_row++;
            }

            // Get the next automation row
            if ($action_render_row >= $next_automation_row) {
               $next_automation_row = $action_render_row;
            }
         }

         // Render conditions
         $condition_render_row = $current_automation_row;
         foreach ($automation->conditions as $type => $conditions) {
            foreach ($conditions as $condition) {
               $contents = [
                  "G" => $type,
                  "H" => $condition->field,
                  "I" => $condition->operator,
                  "J" => $condition->value
               ];
               $self->setCell($sheet, $contents, $condition_render_row);
               $condition_render_row++;
            }

            if ($condition_render_row >= $next_automation_row) {
               $next_automation_row = $condition_render_row;
            }
         }

         $current_automation_row = $next_automation_row;
         $automations_num++;
      });
   }
}
