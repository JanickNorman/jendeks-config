<?php

namespace App\ZendeskExcel;

use \Excel;
use \Cache;
use Zendesk\API\HttpClient as ZendeskAPI;

class ExcelSLA extends ResourceExcel
{
   protected $name = "sla";

   protected $headers = [
      ["No", "Title", "Description", "Position", "Filter", null, null, null, "Policy Metrics", null, null, null],
      [null, null, null, null, "Type", "Field", "Operator", "Value", "Priority", "Metric", "Target", "Business Hours"]
   ];

   protected $sheet;

   public $slas;

   public function __construct(ZendeskAPI $client, $slas_response = [])
   {
      parent::__construct($client);

      $this->slas = isset($slas_response->sla_policies) ? $slas_response->sla_policies : [];
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setSlas($slas)
   {
      $this->slas = $slas;
   }

   protected function generateResources()
   {
      return $this->generateSlas();
   }


   protected function mergeHeaderRows()
   {
      $this->sheet->mergeCells("E1:H1");
      $this->sheet->mergeCells("I1:L1");
      foreach (range("A","D") as $char) {
         $this->sheet->mergeCells($char."1:".$char."2");
      }
   }

   protected function buildBody()
   {
      $self = $this;

      $current_sla_row = $this->getStartingRow();
      $slas_num = 1;
      $next_sla_row = $current_sla_row + 1;
      collect($this->slas)->each(function($sla) use (&$self, &$sheet, &$current_sla_row, &$slas_num, &$next_sla_row) {
         $initial_contents = [
            "A" => $slas_num,
            "B" => $sla->title,
            "C" => $sla->description,
            "D" => $sla->position,
         ];
         $self->setCell($initial_contents, $current_sla_row);

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
               $self->setCell($contents, $filter_render_row);
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
            $self->setCell($contents, $policy_metric_render_row);
            $policy_metric_render_row++;

            if ($policy_metric_render_row >= $next_sla_row) {
               $next_sla_row = $policy_metric_render_row;
            }
         }

         $this->styleCurrentRow($current_sla_row, $next_sla_row);
         $current_sla_row = $next_sla_row;
         $slas_num++;
      });
   }

   private function generateSlas()
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
}
