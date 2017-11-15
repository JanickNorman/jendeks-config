<?php

namespace App\ZendeskExcel;

use \Excel;
use \Cache;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Zendesk\API\HttpClient as ZendeskAPI;

class ExcelSLA extends ResourceExcel
{
   const HEADERS_ROW_1 = ["No", "Title", "Description", "Position", "Filter", null, null, null, null, "Policy Metrics", null, null, null];

   const HEADERS_ROW_2 = [null, null, null, null, "Type", "Field", "Operator", "Value", "Priority", "Metric", "Target", "Business Hours"];

   const STARTING_ROW = 3;

   public $slas;

   public function __construct(ZendeskAPI $client, $slas_response = [])
   {
      parent::__construct($client);

      $this->slas = isset($slas_response->sla_policies) ? $slas_response->sla_policies : [];
   }

   public function toExcel(): LaravelExcelWriter
   {
      $self = $this;

      $this->generateSlas();

      return Excel::create("template:treesdemo1:slas", function($excel)  use ($self) {
         $excel->sheet("template--slas", function($sheet) use ($self) {
            $self->buildHeader($sheet);
            $self->buildBody($sheet);
         });
      });
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function generateSlas()
   {
      if (count($this->slas) > 0) {
         return $this;
      }

      $client = $this->client;
      // Cache ticket fields for testing purpose
      $slas_response = Cache::remember('slas_mock', 60, function() use ($client) {
         return $client->slaPolicies()->findAll(['page' => 1]);
      });
      $this->setSlas($slas_response->sla_policies);

      return $this;
   }

   public function setSlas($slas)
   {
      $this->slas = $slas;
   }

   protected function buildHeader($sheet)
   {
      $sheet->row(1, $this::HEADERS_ROW_1);
      $sheet->row(2, $this::HEADERS_ROW_2);

      $sheet->mergeCells("E1:H1");
      $sheet->mergeCells("I1:L1");
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
      $sheet->getStyle("A1:L1")->applyFromArray($style);
      $sheet->getStyle("A2:L2")->applyFromArray($style);
   }

   protected function buildBody($sheet)
   {
      $self = $this;

      $current_sla_row = self::STARTING_ROW;
      $slas_num = 1;
      $next_sla_row = $current_sla_row + 1;
      collect($this->slas)->each(function($sla) use (&$self, &$sheet, &$current_sla_row, &$slas_num, &$next_sla_row) {
         $initial_contents = [
            "A" => $slas_num,
            "B" => $sla->title,
            "C" => $sla->description,
            "D" => $sla->position,
         ];
         $self->setCell($sheet, $initial_contents, $current_sla_row);

         // Render filters
         $filter_render_row = $current_sla_row;
         foreach ($sla->filter as $type => $filters) {
            foreach ($filters as $filter) {
               $contents = [
                  "E" => $type,
                  "F" => $self->display->fieldFormatter($filter->field),
                  "G" => $filter->operator,
                  "H" => $self->display->valueFormatter($filter->field, $filter->value)
               ];
               $self->setCell($sheet, $contents, $filter_render_row);
               $filter_render_row++;
            }

            if ($filter_render_row >= $next_sla_row) {
               $next_sla_row = $filter_render_row;
            }
         }

         // Render policy metrics
         $policy_metric_render_row = $current_sla_row;
         foreach ($sla->policy_metrics as $policy_metric) {
            $contents = [
               "I" => $policy_metric->priority,
               "J" => $policy_metric->metric,
               "K" => $policy_metric->target,
               "L" => $policy_metric->business_hours
            ];
            $self->setCell($sheet, $contents, $policy_metric_render_row);
            $policy_metric_render_row++;

            if ($policy_metric_render_row >= $next_sla_row) {
               $next_sla_row = $policy_metric_render_row;
            }
         }

         $current_sla_row = $next_sla_row;
         $slas_num++;
      });
   }
}
