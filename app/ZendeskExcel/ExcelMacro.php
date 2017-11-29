<?php

namespace App\ZendeskExcel;

use Zendesk\API\HttpClient as ZendeskAPI;
use \Excel;
use \Cache;

class ExcelMacro extends ResourceExcel
{
   protected $headers = [
      ["No", "Title", "Active", "Position", "Description", "Actions", null, "Restriction", null, null],
      [null, null, null, null, null, "Field", "Value", "Type", "Id", "Ids"]
   ];

   public $macros;

   protected $name = "macros";

   public function __construct(ZendeskAPI $client, $macros_response = [])
   {
      parent::__construct($client);

      $this->macros = isset($macros_response->macros) ? $macros_response->macros : [];
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setMacros($macros)
   {
      $this->macros = $macros;
   }

   protected function generateResources()
   {
      return $this->generateMacros();
   }

   protected function mergeHeaderRows()
   {
      $this->sheet->mergeCells("F1:G1");
      $this->sheet->mergeCells("H1:J1");
      foreach (range("A","E") as $char) {
         $this->sheet->mergeCells($char."1:".$char."2");
      }
   }

   protected function buildBody()
   {
      $self = $this;

      $current_macro_row = $this->getStartingRow();
      $macros_num = 1;
      $next_macro_row = $current_macro_row + 1;
      collect($this->macros)->each(function($macro) use (&$self, &$current_macro_row, &$macros_num, &$next_macro_row) {
         $initial_contents = [
            "A" => $macros_num,
            "B" => $macro->title,
            "C" => $macro->active,
            "D" => $macro->position,
            "E" => $macro->description,
         ];
         $self->setCell($initial_contents, $current_macro_row);

         // Render actions
         $action_render_row = $current_macro_row;
         foreach ($macro->actions as $action) {
            $self->setCell(["F" => $this->display->rulesFieldFormatter($action->field)], $action_render_row);

            if (is_array($action->value)) {
               foreach ($action->value as $value) {
                  $contents = [
                     "G" => $this->display->rulesValueFormatter($action->field, $value)
                  ];
                  $self->setCell($contents, $action_render_row);
                  $action_render_row++;
               }
            } else {
               $contents = [
                  "G" => $this->display->rulesValueFormatter($action->field, $action->value)
               ];
               $self->setCell($contents, $action_render_row);
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
               "I" => $this->display->restrictionValueFormatter($macro->restriction->type, $macro->restriction->id)
            ];
            $this->setCell($restriction_contents, $current_macro_row);

            if (isset($macro->restriction->ids)) {
               $restriction_ids_render_row = $current_macro_row;
               foreach ($macro->restriction->ids as $id) {
                  $self->setCell(["J" => $this->display->restrictionValueFormatter("Group", $id)], $restriction_ids_render_row);
                  $restriction_ids_render_row++;

                  // Get the next macro row
                  if ($restriction_ids_render_row >= $next_macro_row) {
                     $next_macro_row = $restriction_ids_render_row;
                  }
               }
            }
         }

         $this->styleCurrentRow($current_macro_row, $next_macro_row);
         $current_macro_row = $next_macro_row;
         $macros_num++;
      });
   }

   private function generateMacros()
   {
      if (count($this->macros) > 0) {
         return $this;
      }

      $client = $this->client;

      // Cache ticket fields for testing purpose
      // $macros_response = Cache::remember('macros_mock', 60, function() use ($client) {
      //    return $client->macros()->findAll(['page' => 1]);
      // });
      // $this->setMacros($macros_response->macros);

      $client = $this->client;
      $subdomain = $client->getSubdomain();
      $macros = Cache::remember("$subdomain.macros", 60, function() use ($client) {
         $macros = [];
         $page = 1;
         do {
            $response = $client->macros()->findAll(['page' => $page]);
            $macros = array_merge($macros, $response->macros);
            $page++;
         } while ($response->next_page !== null);
         return $macros;
      });
      $this->setMacros($macros);

      return $this;
   }
}
