<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcelViewTest extends TestCase
{
    // APInya randa ambigu
    private function viewsMock()
    {
      return [
         [
            "url" => "https://test.zendesk.com/api/v2/views/1.json",
            "id" => 1,
            "title" => "Title 1",
            "active" => true,
            "updated_at" => "2017-08-21T04:18:17Z",
            "created_at" => "2017-08-21T04:18:17Z",
            "position" => 1,
            "execution" => [
               "group_by" => "group_by_1",
               "group_order" => "asc",
               "sort_by" => "sort_by_1",
               "sort_order" => "asc",
               "group" => [
                  "id" => "status",
                  "title" => "Status",
                  "order" => "asc"
               ],
               "sort" => [
                  "id" => "updated",
                  "title" => "Updated",
                  "order" => "asc"
               ],
               "columns" => [
                  [
                     "id" => "column_id1",
                     "title" => "Column Title 1",
                  ],
                  [
                     "id" => "column_id2",
                     "title" => "Column Title 2",
                  ]
               ],
               "fields" => [
                  [
                     "id" => "field_id1",
                     "title" => "Field Title 1"
                  ],
                  [
                     "id" => "field_id2",
                     "title" => "Field Title 2"
                  ]
               ],
               "custom_fields" => [
                  [
                     "id" => 1,
                     "title" => "Custom Field Title 1",
                     "type" => "type1",
                     "url" => "https://test.zendesk.com/api/v2/ticket_fields/1.json"
                  ],
                  [
                     "id" => 2,
                     "title" => "Custom Field Title 2",
                     "type" => "type2",
                     "url" => "https://test.zendesk.com/api/v2/ticket_fields/2.json"
                  ]
               ],
            ],
            "conditions" => [
               "all" => [
                  [
                     "field" => "field_id1",
                     "operator" => "operator1",
                     "value" => "value1"
                  ],
                  [
                     "field" => "field_id2",
                     "operator" => "operator2",
                     "value" => "value2"
                  ]
               ],
               "any" => [
                  [
                     "field" => "field_id1",
                     "operator" => "operator1",
                     "value" => "value1"
                  ],
                  [
                     "field" => "field_id2",
                     "operator" => "operator2",
                     "value" => "value2"
                  ]
               ]
            ],
            "restriction" => null,
            "raw_title" => "raw_title1"
         ],
         [
            "url" => "https://test.zendesk.com/api/v2/views/2.json",
            "id" => 2,
            "title" => "Title2",
            "active" => false,
            "updated_at" => "2017-08-21T04:18:17Z",
            "created_at" => "2017-08-21T04:18:17Z",
            "position" => 2,
            "execution" => [
               "group_by" => "group_by1",
               "group_order" => "asc",
               "sort_by" => "sort_by1",
               "sort_order" => "asc",
               "group" => [
                  "id" => "status",
                  "title" => "Status",
                  "order" => "asc"
               ],
               "sort" => [
                  "id" => "updated",
                  "title" => "Updated",
                  "order" => "asc"
               ],
               "columns" => [
                  [
                     "id" => "column_id1",
                     "title" => "Column Title 1",
                  ],
                  [
                     "id" => "column_id2",
                     "title" => "Column Title 2",
                  ]
               ],
               "fields" => [
                  [
                     "id" => "field_id1",
                     "title" => "Field Title 1"
                  ],
                  [
                     "id" => "field_id2",
                     "title" => "Field Title 2"
                  ]
               ],
               "custom_fields" => [
                  [
                     "id" => 1,
                     "title" => "Custom Field Title 1",
                     "type" => "type1",
                     "url" => "https://test.zendesk.com/api/v2/ticket_fields/1.json"
                  ],
                  [
                     "id" => 2,
                     "title" => "Custom Field Title 2",
                     "type" => "type2",
                     "url" => "https://test.zendesk.com/api/v2/ticket_fields/2.json"
                  ]
               ],
            ],
            "conditions" => [
               "all" => [
                  [
                     "field" => "field_id1",
                     "operator" => "operator1",
                     "value" => "value1"
                  ],
                  [
                     "field" => "field_id2",
                     "operator" => "operator2",
                     "value" => "value2"
                  ]
               ],
               "any" => [
                  [
                     "field" => "field_id1",
                     "operator" => "operator1",
                     "value" => "value1"
                  ],
                  [
                     "field" => "field_id2",
                     "operator" => "operator2",
                     "value" => "value2"
                  ]
               ]
            ],
            "restriction" => [
               "type" => "User",
               "id" => 2
            ],
            "raw_title" => "raw_title2"
         ]
      ];
   }
}
