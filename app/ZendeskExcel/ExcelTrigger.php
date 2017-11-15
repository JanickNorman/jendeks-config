<?php

namespace App\ZendeskExcel;

use \Validator;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use \Excel as Excel;

class ExcelTrigger extends ResourceExcel
{
   const HEADERS_ROW_1 = ["No", "Title", "Active", "Position", "Actions", null, "Conditions", null, null, null];

   const HEADERS_ROW_2 = [null, null, null, null, "Field", "Value", "Type", "Field", "Operator", "Value"];

   const STARTING_ROW = 3;

   private $triggers = [];

   public function __construct($triggers_response = [])
   {
      $this->triggers = isset($triggers_response->triggers) ? collect($triggers_response->triggers) : [];
   }

   public static function parse($filepath)
   {
      // $triggers = &$triggers;
      //
      // $excel = Excel::load($filepath, function($reader) use (&$triggers) {
      //    $current_action = [];
      //
      //    $reader->noHeading()->skipRows(2)->limitColumns(9)->each(function($sheet, $key) use (&$triggers, &$current_action) {
      //       $sheet = $sheet->toArray();
      //
      //       $columns['title'] = $sheet[0];
      //       $columns['active'] = $sheet[1];
      //       $columns['position'] = (int) $sheet[2];
      //       $columns['actions_field'] = $sheet[3];
      //       $columns['actions_value'] = $sheet[4];
      //       $columns['conditions_type'] = $sheet[5] ?: "all";
      //       $columns['conditions_field'] = $sheet[6];
      //       $columns['conditions_operator'] = $sheet[7];
      //       $columns['conditions_value'] = $sheet[8];
      //
      //       // Initialize new trigger if row indicating a new valid trigger is detected
      //       if ($columns['title'] != null) {
      //          // Check row is valid
      //          if ($columns['actions_field'] == null) {
      //             throw new \Exception("Could not parse trigger-template excel", 1);
      //          }
      //
      //          $trigger['title'] = $columns['title'];
      //          $trigger['active'] = $columns['active'];
      //          $trigger['actions'] = [];
      //          $trigger['conditions'] = [
      //             "all" => [],
      //             "any" => []
      //          ];
      //          $trigger['description'] = "";
      //          $trigger['position'] = $columns['position'];
      //          $trigger['raw_title'] = "";
      //
      //          $triggers[] = $trigger;
      //       }
      //       $total_elements = count($triggers) - 1;
      //       $current_trigger = &$triggers[$total_elements];
      //
      //       // Initialize new action if new action is detected
      //       if ($columns['actions_field'] !== null) {
      //          $action['field'] = $columns['actions_field'];
      //          $action['value'] = $columns['actions_value'];
      //          $current_trigger['actions'][] = $action;
      //       }
      //       $total_action_elements = count($current_trigger['actions']) - 1;
      //       $current_action = &$current_trigger['actions'][$total_action_elements];
      //
      //       // Manipulate the action field
      //       if ($columns['actions_field'] == null && $columns['actions_value']) {
      //          $current_action_value = $current_action['value'];
      //          if (is_string($current_action_value) || is_null($current_action_value)) {
      //             $current_action['value'] = [];
      //             $current_action['value'][] = $current_action_value;
      //          }
      //          $current_action['value'][] = $columns['actions_value'];
      //       }
      //
      //       // Manipulate the condition field; Here we check if the condition is a valid one, then push to the current trigger
      //       if ($columns['conditions_field'] !== null && $columns['conditions_operator'] !== null && $columns['conditions_value'] !== null) {
      //          $condition['field'] = $columns['conditions_field'];
      //          $condition['operator'] = $columns['conditions_operator'];
      //          $condition['value'] = $columns['conditions_value'];
      //          $current_trigger['conditions'][$columns['conditions_type']][] = $condition;
      //       }
      //    });
      // });
      // return $triggers;
   }

   public function toExcel(): LaravelExcelWriter
   {
      $self = $this;

      return Excel::create("template:treesdemo1:triggers", function($excel)  use ($self) {
         $excel->sheet("template--triggers", function($sheet) use ($self) {
            $self->buildHeader($sheet);
            $self->buildBody($sheet);
         });
      });
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setTriggers($triggers)
   {
      $this->triggers = $triggers;
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

      $current_trigger_row = self::STARTING_ROW;
      $triggers_num = 1;
      $next_trigger_row = $current_trigger_row + 1;
      $this->triggers->each(function($trigger) use (&$self, &$sheet, &$current_trigger_row, &$triggers_num, &$next_trigger_row) {
         $initial_contents = [
            "A" => $triggers_num,
            "B" => $trigger->title,
            "C" => $trigger->active,
            "D" => $trigger->position,
         ];
         $self->setCell($sheet, $initial_contents, $current_trigger_row);

         // Render actions
         $action_render_row = $current_trigger_row;
         foreach ($trigger->actions as $action) {
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

            // Get the next trigger row
            if ($action_render_row >= $next_trigger_row) {
               $next_trigger_row = $action_render_row;
            }
         }

         // Render conditions
         $condition_render_row = $current_trigger_row;
         foreach ($trigger->conditions as $type => $conditions) {
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

            if ($condition_render_row >= $next_trigger_row) {
               $next_trigger_row = $condition_render_row;
            }
         }

         $current_trigger_row = $next_trigger_row;
         $triggers_num++;
      });
   }
}
