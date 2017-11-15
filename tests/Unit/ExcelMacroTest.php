<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcelMacroTest extends TestCase
{
    private function macrosMock()
    {
      return [
         [
            "url" => "https://test.zendesk.com/api/v2/macros/1.json",
            "id" => 1,
            "title" => "Title 1",
            "active" => true,
            "updated_at" => "2016-09-28T03:00:24Z",
            "created_at" => "2016-09-28T03:00:24Z",
            "position" => 1,
            "description" => "description 1",
            "actions" => [
               [
                  "field" => "field_id_2",
                  "value" => "value_2"
               ],
               [
                  "field" => "field_id_2",
                  "value" => "value_2"
               ]
            ],
            "restriction" => [
               "type" => "Group",
               "id" => 11,
               "ids" => [
                  111,222,333
               ]
            ]
         ],
         [
            "url" => "https://test.zendesk.com/api/v2/macros/2.json",
            "id" => 2,
            "title" => "Title 2",
            "active" => false,
            "updated_at" => "2016-09-28T03:00:24Z",
            "created_at" => "2016-09-28T03:00:24Z",
            "position" => 2,
            "description" => "description 2",
            "actions" => [
               [
                  "field" => "field_id_2",
                  "value" => [
                     "value_1_array_1",
                     "value_1_array_2"
                  ]
               ],
               [
                  "field" => "field_id_2",
                  "value" => [
                     "value_2_array_1",
                     "value_2_array_2"
                  ]
               ]
            ],
            "restriction" => [
               "type" => "Group",
               "id" => 22,
               "ids" => [
                  444,555,666
               ]
            ]
         ]
      ];
   }
}
