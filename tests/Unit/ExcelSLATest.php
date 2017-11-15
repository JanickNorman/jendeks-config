<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcelSLATest extends TestCase
{
    private function slaMocks()
    {
      return [
         [
            "url" => "https://test.zendesk.com/api/v2/slas/policies/1.json",
            "id" => 1,
            "title" => "Title 1",
            "description" => "Description 1",
            "position" => 1,
            "filter" => [
               "all" => [
                  [
                     "field" => "all_1_field_1",
                     "operator" => "all_1_operator_1",
                     "value" => 11
                  ],
                  [
                     "field" => "all_1_field_2",
                     "operator" => "all_1_operator_2",
                     "value" => 12
                  ]
               ],
               "any" => [
                  [
                     "field" => "any_1_field_1",
                     "operator" => "any_1_operator_1",
                     "value" => 21
                  ],
                  [
                     "field" => "any_2_field_2",
                     "operator" => "any_2_operator_2",
                     "value" => 22
                  ]
               ]
            ],
            "policy_metrics" => [
               [
                  "priority" => "normal",
                  "metric" => "metric_1",
                  "target" => 1,
                  "business_hours" => true
               ],
               [
                  "priority" => "high",
                  "metric" => "metric_2",
                  "target" => 2,
                  "business_hours" => true
               ]
            ],
            "created_at" => "2016-06-17T04:16:03Z",
            "updated_at" => "2016-06-17T04:16:03Z"
         ],
         [

         ]
      ];
   }
}
