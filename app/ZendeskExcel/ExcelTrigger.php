<?php

namespace App\ZendeskExcel;

use Zendesk\API\HttpClient as ZendeskAPI;
use Zendesk\API\Resources\Core\Views;
use \Excel;
use \Cache;

class ExcelTrigger extends ResourceExcel
{
   protected $headers = [
      ["No", "Title", "Active", "Position", "Actions", null, "Conditions", null, null, null],
      [null, null, null, null, "Field", "Value", "Type", "Field", "Operator", "Value"]
   ];

   private $triggers = [];

   protected $name = "triggers";

   public function __construct(ZendeskAPI $client, $triggers_response = [])
   {
      parent::__construct($client);

      $this->triggers = isset($triggers_response->triggers) ? $triggers_response->triggers : [];
   }

   public static function parse($filepath)
   {
      // $triggers = &$triggers;
      //
      // $excel = Excel::load($filepath, function($reader) use (&$triggers) {
      //    $current_action = [];
      //
      //    $reader->noHeading()->skipRows(2)->limitColumns(9)->each(function($key) use (&$triggers, &$current_action) {
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

   public function read($filepath): Array
   {
      return [];
   }

   public function setTriggers($triggers)
   {
      $this->triggers = $triggers;
   }

   protected function generateResources()
   {
      return $this->generateTriggers();
   }

   protected function mergeHeaderRows()
   {
      $this->sheet->mergeCells("E1:F1");
      $this->sheet->mergeCells("G1:J1");
      foreach (range("A","D") as $char) {
         $this->sheet->mergeCells($char."1:".$char."2");
      }
   }

   protected function buildBody()
   {
      $self = $this;

      $current_trigger_row = $this->getStartingRow();
      $triggers_num = 1;
      $next_trigger_row = $current_trigger_row + 1;
      collect($this->triggers)->each(function($trigger) use (&$self, &$current_trigger_row, &$triggers_num, &$next_trigger_row) {
         $initial_contents = [
            "A" => $triggers_num,
            "B" => $trigger->title,
            "C" => $trigger->active,
            "D" => $trigger->position,
         ];
         $self->setCell($initial_contents, $current_trigger_row);

         // Render actions
         $action_render_row = $current_trigger_row;
         foreach ($trigger->actions as $action) {
            $self->setCell(["E" => $this->display->rulesFieldFormatter($action->field)], $action_render_row);

            if (is_array($action->value)) {
               foreach ($action->value as $value) {
                  $contents = [
                     "F" => $this->display->rulesValueFormatter($action->field, $value)
                  ];
                  $self->setCell($contents, $action_render_row);
                  $action_render_row++;
               }
            } else {
               $contents = [
                  "F" => $this->display->rulesValueFormatter($action->field, $action->value)
               ];
               $self->setCell($contents, $action_render_row);
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
                  "H" => $this->display->rulesFieldFormatter($condition->field),
                  "I" => $condition->operator,
                  "J" => $this->display->rulesValueFormatter($condition->field, $condition->value)
               ];
               $self->setCell($contents, $condition_render_row);
               $condition_render_row++;
            }

            if ($condition_render_row >= $next_trigger_row) {
               $next_trigger_row = $condition_render_row;
            }
         }

         $this->styleCurrentRow($current_trigger_row, $next_trigger_row);
         $current_trigger_row = $next_trigger_row;
         $triggers_num++;
      });
   }

   private function generateTriggers()
   {
      if (count($this->triggers) > 0) {
         return $this;
      }

      $client = $this->client;

      // Cache ticket fields for testing purpose
      $triggers_response = Cache::remember('triggers_mock', 60, function() use ($client) {
         return $client->triggers()->findAll(['page' => 1]);
      });
      $this->setTriggers($triggers_response->triggers);

      return $this;
   }
}
