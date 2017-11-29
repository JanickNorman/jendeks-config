<?php

namespace App\ZendeskExcel;

use Zendesk\API\HttpClient as ZendeskAPI;
use App\ZendeskExcel\Formatter\DisplayRepository;
use App\ZendeskExcel\Formatter\SLADisplayFormatter;
use \Excel;
use \Cache;

class ExcelSLA extends ResourceExcel
{
   protected $headers = [
      ["No", "Title", "Description", "Position", "Filter", null, null, null, "Policy Metrics", null, null, null],
      [null, null, null, null, "Type", "Field", "Operator", "Value", "Priority", "Metric", "Target", "Business Hours"]
   ];

   protected $sheet;

   public $slas;

   protected $name = "slas";

   public function __construct(ZendeskAPI $client, $slas_response = [])
   {
      parent::__construct($client);

      $this->slas = isset($slas_response->sla_policies) ? $slas_response->sla_policies : [];
      $this->display = new SLADisplayFormatter(new DisplayRepository($client));
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
                  "F" => $self->display->rulesFieldFormatter($filter->field),
                  "G" => $filter->operator,
                  "H" => $self->display->rulesValueFormatter($filter->field, $filter->value)
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
      $subdomain = $client->getSubdomain();
      $sla_policies = Cache::remember("$subdomain.slas", 60, function() use ($client) {
         $sla_policies = [];
         $page = 1;
         do {
            $response = $client->slaPolicies()->findAll(['page' => $page]);
            $sla_policies = array_merge($sla_policies, $response->sla_policies);
            $page++;
         } while ($response->next_page !== null);
         return $sla_policies;
      });
      $this->setSlas($sla_policies);

      return $this;
   }
}
