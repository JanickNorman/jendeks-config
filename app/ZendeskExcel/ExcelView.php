<?php

namespace App\ZendeskExcel;

use Zendesk\API\HttpClient as ZendeskAPI;
use \Excel;
use \Cache;

class ExcelView extends ResourceExcel
{
   protected $headers = [
      ["No", "Title", "Active", "Restriction", null, "Position", "Output", null, null, null, null, "Condition", null, null],
      [null, null, null, "Type", "Name", null, "Columns", "Group By", "Group Order", "Sort By", "Sort Order", "Type", "Field", "Operator", "Value",]
   ];

   public $views;

   public function __construct(ZendeskAPI $client, $views_response = [])
   {
      parent::__construct($client);

      $this->views = isset($views_response->views) ? $views_response->views : [];
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setViews($views)
   {
      $this->views = $views;
   }

   protected function generateResources()
   {
      return $this->generateViews();
   }

   protected function mergeHeaderRows()
   {
      foreach (range("A","C") as $char) {
         $this->sheet->mergeCells($char."1:".$char."2");
      }
      $this->sheet->mergeCells('F1:F2');

      $this->sheet->mergeCells('D1:E1');
      $this->sheet->mergeCells('G1:K1');
      $this->sheet->mergeCells('L1:O1');
      // $this->sheet->mergeCells('J1:N1');
      // $this->sheet->mergeCells('O1:P1');
   }

   protected function buildBody()
   {
      $self = $this;

      $current_view_row = $this->getStartingRow();
      $views_num = 1;
      $next_view_row = $current_view_row + 1;
      collect($this->views)->each(function($view) use (&$self, &$current_view_row, &$views_num, &$next_view_row) {
         // Initial contents declaration
         $initial_contents = [
            "A" => $views_num,
            "B" => $view->title,
            "C" => $view->active,
            "F" => $view->position
         ];

         // Merge restriction if exist and render with initial contents
         if ($view->restriction !== null) {
            $restriction_contents = [
               "D" => $view->restriction->type,
               "E" => $this->display->restrictionValueFormatter($view->restriction->type, $view->restriction->id)
            ];
            $initial_contents = array_merge($initial_contents, $restriction_contents);
         }
         $self->setCell($initial_contents, $current_view_row);

         // Render columns
         $column_render_row = $current_view_row;
         foreach ($view->execution->columns as $column) {
            $contents = [
               "G" => $column->title,
            ];
            $self->setCell($contents, $column_render_row);
            $column_render_row++;

            if ($column_render_row >= $next_view_row) {
               $next_view_row = $column_render_row;
            }
         }

         // Render other execution parameters
         $execution_contents = [
            "H" => isset($view->execution->group->title) ? $view->execution->group->title : null,
            "I" => $view->execution->group_order,
            "J" => isset($view->execution->sort->title) ? $view->execution->sort->title : null ,
            "K" => $view->execution->sort_order,
         ];
         $self->setCell($execution_contents, $current_view_row);

         // Render conditions
         $condition_render_row = $current_view_row;
         foreach ($view->conditions as $type => $conditions) {
            foreach ($conditions as $condition) {
               $contents = [
                  "L" => $type,
                  "M" => $this->display->rulesFieldFormatter($condition->field),
                  "N" => $condition->operator,
                  "O" => $this->display->rulesValueFormatter($condition->field, $condition->value)
               ];
               $self->setCell($contents, $condition_render_row);
               $condition_render_row++;
            }

            if ($condition_render_row >= $next_view_row) {
               $next_view_row = $condition_render_row;
            }
         }

         $this->styleCurrentRow($current_view_row, $next_view_row);         
         $current_view_row = $next_view_row;
         $views_num++;
      });


   }

   private function generateViews()
   {
      if (count($this->views) > 0) {
         return $this;
      }

      $client = $this->client;

      // Cache ticket fields for testing purpose
      $views_response = Cache::remember('views_mock', 60, function() use ($client) {
         return $client->views()->findAll(['page' => 1]);
      });
      $this->setViews($views_response->views);

      return $this;
   }
}
