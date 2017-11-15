<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcelGroupTest extends TestCase
{
    private function groupsMock()
    {
      return [
         [
            "url" => "https://test.zendesk.com/api/v2/groups/1.json",
            "id" => 1,
            "name" => "Name 1",
            "deleted" => true,
            "created_at" => "2016-06-23T04:06:44Z",
            "updated_at" => "2016-06-23T04:06:44Z"
         ],
         [
            "url" => "https://test.zendesk.com/api/v2/groups/2.json",
            "id" => 2,
            "name" => "Name 2",
            "deleted" => true,
            "created_at" => "2016-06-23T04:06:44Z",
            "updated_at" => "2016-06-23T04:06:44Z"
         ]
      ];
   }
}
