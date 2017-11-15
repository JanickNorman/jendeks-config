<?php

namespace App\ZendeskExcel;

use \Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ExcelView extends ResourceExcel
{
   const HEADERS_ROW_1 = ["No", "Title", "Active", "Restriction", null, "Position", "Execution", null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, "Conditions", null, null, null];

   const HEADERS_ROW_2 = [null, null, null, null, null, null, "Group By", "Group Order", "Sort By", "Sort Order", "Group", null, null, "Sort", null, null, "Columns", null, "Fields", null, "Custom Fields", null];

   const HEADERS_ROW_3 = [null, null, null, "Type", "Id", null, null, null, null, null, "Id", "Title", "Order", "Id", "Title", "Order", "Id", "Title", "Id", "Title", "Id", "Title", "Type", "Field", "Operator", "Value"];

   const STARTING_ROW = 4;

   public $views;

   public function __construct($views_response = [])
   {
      $this->views = isset($views_response->views) ? collect($views_response->views) : [];
   }

   public function toExcel(): LaravelExcelWriter
   {
      $self = $this;

      return Excel::create("template:treesdemo1:views", function($excel)  use ($self) {
         $excel->sheet("template--views", function($sheet) use ($self) {
            $self->buildHeader($sheet);
            $self->buildBody($sheet);
         });
      });
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setViews($views)
   {
      $this->views = $views;
   }

   protected function buildHeader($sheet)
   {
      $sheet->row(1, $this::HEADERS_ROW_1);
      $sheet->row(2, $this::HEADERS_ROW_2);
      $sheet->row(3, $this::HEADERS_ROW_3);

      $sheet->mergeCells("D1:E1");
      $sheet->mergeCells("G1:V1");
      $sheet->mergeCells("W1:Z1");
      foreach (range("A","C") as $char) {
         $sheet->mergeCells($char."1:".$char."3");
      }
      $sheet->mergeCells('D1:E2');
      // $sheet->mergeCells('F1:F3');
      // foreach (range("G","J") as $char) {
      //    $sheet->mergeCells($char."1:".$char."2");
      // }
      // $sheet->mergeCells('K2:M2');
      // $sheet->mergeCells('N2:P2');
      // $sheet->mergeCells('Q2:R2');
      // $sheet->mergeCells('S2:T2');
      // $sheet->mergeCells('U2:V2');

      $style = [
         'alignment' => [
            'horizontal' => 'center',
         ],
         'font' => [
            'bold' => true
         ]
      ];
      $sheet->getStyle("A1:Z1")->applyFromArray($style);
      $sheet->getStyle("A2:Z2")->applyFromArray($style);
      $sheet->getStyle("A3:Z3")->applyFromArray($style);
   }

   protected function buildBody($sheet)
   {
      $self = $this;

      $current_view_row = self::STARTING_ROW;
      $views_num = 1;
      $next_view_row = $current_view_row + 1;
      $this->views->each(function($view) use (&$self, &$sheet, &$current_view_row, &$views_num, &$next_view_row) {
         // Initial Render
         $initial_contents = [
            "A" => $views_num,
            "B" => $view->title,
            "C" => $view->active,
            // "D" => $view->restriction,
            "F" => $view->position,
            "G" => $view->execution->group_by,
            "H" => $view->execution->group_order,
            "I" => $view->execution->sort_by,
            "J" => $view->execution->sort_order
         ];

         // Render restriction
         if ($view->restriction !== null) {
            $restriction_contents = [
               "D" => $view->restriction->type,
               "E" => $view->restriction->id
            ];
            $initial_contents = array_merge($initial_contents, $restriction_contents);
         }

         if (isset($view->execution->group) && $view->execution->group !== null) {
            $group = $view->execution->group;
            $group_contents = [
               "K" => $group->id,
               "L" => $group->title,
               "M" => $group->order
            ];
            $initial_contents = array_merge($initial_contents, $group_contents);
         }
         if (isset($view->execution->sort) && $view->execution->sort !== null) {
            $sort = $view->execution->sort;
            $sort_contents = [
               "N" => $sort->id,
               "O" => $sort->title,
               "P" => $sort->order
            ];
            $initial_contents = array_merge($initial_contents, $sort_contents);
         }
         $self->setCell($sheet, $initial_contents, $current_view_row);

         // Render columns
         $column_render_row = $current_view_row;
         foreach ($view->execution->columns as $column) {
            $contents = [
               "Q" => $column->id,
               "R" => $column->title,
            ];
            $self->setCell($sheet, $contents, $column_render_row);
            $column_render_row++;

            if ($column_render_row >= $next_view_row) {
               $next_view_row = $column_render_row;
            }
         }

         // Render fields
         $field_render_row = $current_view_row;
         foreach ($view->execution->fields as $field) {
            $contents = [
               "S" => $field->id,
               "T" => $field->title,
            ];
            $self->setCell($sheet, $contents, $field_render_row);
            $field_render_row++;

            if ($field_render_row >= $next_view_row) {
               $next_view_row = $field_render_row;
            }
         }

         // Render custom fields
         $custom_field_render_row = $current_view_row;
         foreach ($view->execution->custom_fields as $custom_field) {
            $contents = [
               "U" => $custom_field->id,
               "V" => $custom_field->title,
            ];
            $self->setCell($sheet, $contents, $custom_field_render_row);
            $custom_field_render_row++;

            if ($custom_field_render_row >= $next_view_row) {
               $next_view_row = $custom_field_render_row;
            }
         }

         // Render conditions
         $condition_render_row = $current_view_row;
         foreach ($view->conditions as $type => $conditions) {
            foreach ($conditions as $condition) {
               $contents = [
                  "W" => $type,
                  "X" => $condition->field,
                  "Y" => $condition->operator,
                  "Z" => $condition->value
               ];
               $self->setCell($sheet, $contents, $condition_render_row);
               $condition_render_row++;
            }

            if ($condition_render_row >= $next_view_row) {
               $next_view_row = $condition_render_row;
            }
         }

         $current_view_row = $next_view_row;
         $views_num++;
      });
   }
}
