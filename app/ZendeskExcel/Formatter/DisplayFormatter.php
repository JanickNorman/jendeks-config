<?php
namespace App\ZendeskExcel\Formatter;

use Zendesk\API\HttpClient as ZendeskAPI;
use App\ZendeskExcel\Formatter\DisplayFactory;

class DisplayFormatter
{
   // const FIELD_TYPE_MAPPER = [
   //    "assignee" => "user",
   //    "via" => "via"
   // ];

   public function __construct(DisplayFactory $displayFactory)
   {
      $this->displayFactory = $displayFactory;
   }

   public function resourceFormatter($id, $type)
   {
      return $this->displayFactory->getResourceName($id, $type);
   }

   public function fieldFormatter($field_string)
   {
      // Check if field string is referencing custom fields
      if ($this->isTicketField($field_string)) {
         return "ticket_field: " . $field_string;
      }

      if ($this->isResource($field_string)) {
         return substr($field_string, 0, -3);
      }

      return $field_string;
   }

   public function valueFormatter($field_string, $value_string)
   {
      $type = $this->getFieldType($field_string);
      return $this->displayFactory->getResourceName(FIELD_TYPE_MAPPER[$type], $value_string);
   }

   protected function getFieldType($field_string)
   {
      if ($this->isTicketField($field_string)) {
         return "ticket_field";
      }

      if ($this->isCustomField($field_string)) {
         return "custom_field";
      }

      if ($this->isResource($field_string)) {
         return substr($field_string, 0, -3);
      }

      return "field";
   }

   // Check if field string is referencing custom fields
   protected function isTicketField($field_string)
   {
      return substr($field_string, 0, 14) === "ticket_fields_";
   }

   protected function isCustomField($field_string)
   {
      return substr($field_string, 0, 14) === "custom_fields_";
   }

   // Check if field string is referencing some resource
   protected function isResource($field_string)
   {
      return substr($field_string, -3) === "_id";
   }
}
