<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcelTriggerTest extends TestCase
{
    private function triggersMock()
    {
      return [
         [
            "url" => "https://test.zendesk.com/api/v2/triggers/1.json",
            "id" => 1,
            "title" => "Title 1",
            "active" => true,
            "updated_at" => "2017-07-05T10:25:34Z",
            "created_at" => "2017-07-05T10:25:34Z",
            "actions" => [
               [
                  "field" => "action_field_1",
                  "value" => [
                     "action_1_value_array_1",
                     "action_1_value_array_2"
                  ]
               ],
               [
                  "field" => "action_field_2",
                  "value" => [
                     "action_2_value_array_1",
                     "action_2_value_array_2"
                  ]
               ]
            ],
            "conditions" => [
               "all" => [
                  [
                     "field" => "condition_all_1_field_1",
                     "operator" => "condition_all_1_operator_1",
                     "value" => "condition_all_1_value1"
                  ],
                  [
                     "field" => "condition_all_2_field2",
                     "operator" => "condition_all_2_operator2",
                     "value" => "condition_all_2_value2"
                  ]
               ],
               "any" => [
                  [
                     "field" => "condition_any_1_field_1",
                     "operator" => "condition_any_1_operator_1",
                     "value" => "condition_any_1_value_1"
                  ],
                  [
                     "field" => "condition_any_2_field_2",
                     "operator" => "condition_any_2_operator_2",
                     "value" => "condition_any_2_value_2"
                  ]
               ]
            ],
            "description" => "description_1",
            "position" => 1,
            "raw_title" => "Raw Title 1"
         ],
         [
            "url" => "https://test.zendesk.com/api/v2/triggers/2.json",
            "id" => 2,
            "title" => "Title 2",
            "active" => true,
            "updated_at" => "2017-07-05T10:25:34Z",
            "created_at" => "2017-07-05T10:25:34Z",
            "actions" => [
               [
                  "field" => "action_field_1",
                  "value" => [
                     "action_1_value_array_1",
                     "action_1_value_array_2"
                  ]
               ],
               [
                  "field" => "action_field_2",
                  "value" => [
                     "action_2_value_array_1",
                     "action_2_value_array_2"
                  ]
               ]
            ],
            "conditions" => [
               "all" => [
                  [
                     "field" => "condition_all_1_field_1",
                     "operator" => "condition_all_1_operator_1",
                     "value" => "condition_all_1_value1"
                  ],
                  [
                     "field" => "condition_all_2_field2",
                     "operator" => "condition_all_2_operator2",
                     "value" => "condition_all_2_value2"
                  ]
               ],
               "any" => [
                  [
                     "field" => "condition_any_1_field_1",
                     "operator" => "condition_any_1_operator_1",
                     "value" => "condition_any_1_value_1"
                  ],
                  [
                     "field" => "condition_any_2_field_2",
                     "operator" => "condition_any_2_operator_2",
                     "value" => "condition_any_2_value_2"
                  ]
               ]
            ],
            "description" => "description_2",
            "position" => 2,
            "raw_title" => "Raw Title 2"
         ],
      ];
   }
}
