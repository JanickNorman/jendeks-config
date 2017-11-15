<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcelTicketFormTest extends TestCase
{
   private function ticketFormsMock()
   {
      return [
         [
            "url" => "https://test.zendesk.com/api/v2/ticket_forms.json",
            "name" => "name1",
            "raw_name" => "raw_name1",
            "display_name" => "display_name1",
            "raw_display_name" => "raw_display_name1",
            "position" => 1,
            "active" => true,
            "end_user_visible" => true,
            "default" => true,
            "ticket_field_ids" => [
               11,22,33
            ],
            "in_all_brands" => true,
            "restricted_brand_ids" => [
               99
            ]
         ],
         [
            "name" => "name_2",
            "raw_name" => "raw_name_2",
            "display_name" => "display_name_2",
            "raw_display_name" => "raw_display_name_2",
            "position" => 2,
            "active" => true,
            "end_user_visible" => true,
            "default" => true,
            "ticket_field_ids" => [
               44,55,66
            ],
            "in_all_brands" => false,
            "restricted_brand_ids" => [
               88
            ]
         ]
      ];
   }
}
