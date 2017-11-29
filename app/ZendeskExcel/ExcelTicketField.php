<?php

namespace App\ZendeskExcel;

use \Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;

class ExcelTicketField extends ResourceExcel
{
   protected $headers = [
      ["No", "Type", "Title", "Raw Title", "Description", "Position", "Active", "Required", "Collapsed For Agents", "Regexp For Validation", "Title In Portal", "Raw Title In Portal", "Visible In Portal", "Editable In Portal", "Required In Portal", "Tag", "Removable", "Custom Field Options"],
      [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, "Name", "Value", "Default"]
   ];

   public $ticket_fields;

   protected $name = "ticket fields";

   public function __construct(ZendeskAPI $client, $ticket_fields_response = [])
   {
      parent::__construct($client);

      $this->ticket_fields = isset($ticket_fields_response) ? $ticket_fields_response : [];
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setTicketFields(array $ticket_fields)
   {
      $this->ticket_fields = $ticket_fields;
   }

   protected function generateResources()
   {
      return $this->generateTicketFields();
   }

   protected function mergeHeaderRows()
   {
      $this->sheet->mergeCells("R1:T1");
      foreach (range("A","Q") as $char) {
         $this->sheet->mergeCells($char."1:".$char."2");
      }
   }

   protected function buildBody()
   {
      $self = $this;

      $current_row = $this->getStartingRow();
      $ticket_fields_num = 1;
      collect($this->ticket_fields)->each(function($ticket_field) use (&$self, &$current_row, &$ticket_fields_num) {
         $initial_row = $current_row;

         $initial_contents = [
                  "A" => $ticket_fields_num,
                  "B" => $ticket_field->type,
                  "C" => $ticket_field->title,
                  "D" => $ticket_field->raw_title,
                  "E" => $ticket_field->description,
                  "F" => $ticket_field->position,
                  "G" => $ticket_field->active,
                  "H" => $ticket_field->required,
                  "I" => $ticket_field->collapsed_for_agents,
                  "J" => $ticket_field->regexp_for_validation,
                  "K" => $ticket_field->title_in_portal,
                  "L" => $ticket_field->raw_title_in_portal,
                  "M" => $ticket_field->visible_in_portal,
                  "N" => $ticket_field->editable_in_portal,
                  "O" => $ticket_field->required_in_portal,
                  "P" => $ticket_field->tag,
                  "Q" => $ticket_field->removable,
               ];

         // Append to new row if have custom field options
         if (isset($ticket_field->custom_field_options) && count($ticket_field->custom_field_options) > 0) {
            $custom_field_options = $ticket_field->custom_field_options;
            foreach ($custom_field_options as $key => $custom_field_option) {
               $custom_field_option_contents = [
                  "R" => $custom_field_option->name,
                  "S" => $custom_field_option->value,
                  "T" => $custom_field_option->default,
               ];

               $row_contents = [];
               if ($key < 1) {
                  $row_contents = array_merge($initial_contents, $custom_field_option_contents);
               } else {
                  $row_contents = $custom_field_option_contents;
               }

               $self->setCell($row_contents, $current_row);
               $current_row++;
            }
         } else {
            $self->setCell($initial_contents, $current_row);
            $current_row++;
         }

         $last_row = $current_row;
         $this->styleCurrentRow($initial_row, $last_row);
         $ticket_fields_num++;
      });
      // dd('aloha');
   }

   private function generateTicketFields()
   {
      if (count($this->ticket_fields) > 0) {
         return $this;
      }

      $client = $this->client;

      // Cache ticket fields for testing purpose
      // $ticket_fields_response = Cache::remember('ticket_fields_mock', 60, function() use ($client) {
      //    return $client->ticketFields()->findAll(['page' => 1]);
      // });
      // $this->setTicketFields($ticket_fields_response->ticket_fields);
      //
      $client = $this->client;
      $subdomain = $client->getSubdomain();
      $ticket_fields = Cache::remember("$subdomain.ticket_fields", 60, function() use ($client) {
         $ticket_fields = [];
         $page = 1;
         do {
            $response = $client->ticketFields()->findAll(['page' => $page]);
            $ticket_fields = array_merge($ticket_fields, $response->ticket_fields);
            $page++;
         } while ($response->next_page !== null);
         return $ticket_fields;
      });
      $this->setTicketFields($ticket_fields);

      return $this;
   }
}
