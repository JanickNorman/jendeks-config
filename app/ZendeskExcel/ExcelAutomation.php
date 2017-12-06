<?php

namespace App\ZendeskExcel;

use Zendesk\API\HttpClient as ZendeskAPI;
use \Excel;
use \Cache;

class ExcelAutomation extends ResourceExcel
{
   protected $headers = [
      ["No", "Title", "Active", "Position", "Conditions", null, null, null, "Actions", null],
      [null, null, null, null, "Type", "Field", "Operator", "Value", "Field", "Value"]
   ];

   public $automations;

   protected $name = "automations";

   public function __construct(ZendeskAPI $client, $automations_response = [])
   {
      parent::__construct($client);

      $this->automations = isset($automations_response->automations) ? $automations_response->automations : [];
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setAutomations($automations)
   {
      $this->automations = $automations;
   }

   protected function generateResources()
   {
      return $this->generateAutomations();
   }

   protected function mergeHeaderRows()
   {
      $this->sheet->mergeCells("E1:H1");
      $this->sheet->mergeCells("I1:J1");
      foreach (range("A","D") as $char) {
         $this->sheet->mergeCells($char."1:".$char."2");
      }
   }

   protected function buildBody()
   {
      $self = $this;

      $current_automation_row = $this->getStartingRow();
      $automations_num = 1;
      $next_automation_row = $current_automation_row + 1;
      collect($this->automations)->each(function($automation) use (&$self, &$current_automation_row, &$automations_num, &$next_automation_row) {
         $initial_contents = [
            "A" => $automations_num,
            "B" => $automation->title,
            "C" => $automation->active,
            "D" => $automation->position,
         ];
         $self->setCell($initial_contents, $current_automation_row);

         // Render conditions
         $condition_render_row = $current_automation_row;
         foreach ($automation->conditions as $type => $conditions) {
            foreach ($conditions as $condition) {
               $contents = [
                  "E" => $type,
                  "F" => $this->display->rulesFieldFormatter($condition->field),
                  "G" => $condition->operator,
                  "H" => $this->display->rulesValueFormatter($condition->field, $condition->value)
               ];
               $self->setCell($contents, $condition_render_row);
               $condition_render_row++;
            }

            if ($condition_render_row >= $next_automation_row) {
               $next_automation_row = $condition_render_row;
            }
         }

         // Render actions
         $action_render_row = $current_automation_row;
         foreach ($automation->actions as $action) {
            $self->setCell(["I" => $this->display->rulesFieldFormatter($action->field)], $action_render_row);

            if (is_array($action->value)) {
               foreach ($action->value as $value) {
                  $contents = [
                     "J" => $this->display->rulesValueFormatter($action->field, $value)
                  ];
                  $self->setCell($contents, $action_render_row);
                  $action_render_row++;
               }
            } else {
               $contents = [
                  "J" => $this->display->rulesValueFormatter($action->field, $action->value)
               ];
               $self->setCell($contents, $action_render_row);
               $action_render_row++;
            }

            // Get the next automation row
            if ($action_render_row >= $next_automation_row) {
               $next_automation_row = $action_render_row;
            }
         }

         $this->styleCurrentRow($current_automation_row, $next_automation_row);
         $current_automation_row = $next_automation_row;
         $automations_num++;
      });
   }

   private function generateAutomations()
   {
      if (count($this->automations) > 0) {
         return $this;
      }

      $client = $this->client;

      // // Cache ticket fields for testing purpose
      // $automations_response = Cache::remember('automations_mock', 60, function() use ($client) {
      //    return $client->automations()->findAll(['page' => 1]);
      // });
      // $this->setAutomations($automations_response->automations);

      $client = $this->client;
      $subdomain = $client->getSubdomain();
      $automations = Cache::remember("$subdomain.automations", 60, function() use ($client) {
         $automations = [];
         $page = 1;
         do {
            $response = $client->automations()->findAll(['page' => $page]);
            $automations = array_merge($automations, $response->automations);
            $page++;
         } while ($response->next_page !== null);
         return $automations;
      });
      $this->setAutomations($automations);


      return $this;
   }
}
