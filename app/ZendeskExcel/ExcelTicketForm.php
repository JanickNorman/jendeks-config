<?php

namespace App\ZendeskExcel;

use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Excel;
use \Cache;

class ExcelTicketForm extends ResourceExcel
{
   protected $headers = [
      ["No", "Name", "Raw Name", "Display Name", "Raw Display Name", "Position", "Active", "End User Visible", "Default", "Ticket Field", "In All Brand", "Restricted Brand"]
   ];

   public $ticket_forms;

   protected $name = "ticket forms";

   public function __construct(ZendeskAPI $client, $ticket_forms_response = [])
   {
      parent::__construct($client);

      $this->ticket_forms = isset($ticket_forms_response->ticket_forms) ? collect($ticket_forms_response->ticket_forms) : [];
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setTicketForms($ticket_forms)
   {
      $this->ticket_forms = $ticket_forms;
   }

   protected function generateResources()
   {
      return $this->generateTicketForms();
   }

   protected function buildBody()
   {
      $self = $this;

      $current_ticket_form_row = $this->getStartingRow();
      $ticket_forms_num = 1;
      $next_ticket_form_row = $current_ticket_form_row + 1;
      collect($this->ticket_forms)->each(function($ticket_form) use (&$self, &$current_ticket_form_row, &$ticket_forms_num, &$next_ticket_form_row) {
         $initial_contents = [
            "A" => $ticket_forms_num,
            "B" => $ticket_form->name,
            "C" => $ticket_form->raw_name,
            "D" => $ticket_form->display_name,
            "E" => $ticket_form->raw_display_name,
            "F" => $ticket_form->position,
            "G" => $ticket_form->active,
            "H" => $ticket_form->end_user_visible,
            "I" => $ticket_form->default,
            // "J" => $ticket_form->ticket_field_ids,
            "K" => $ticket_form->in_all_brands,
            // "M" => $ticket_form->restricted_brand_ids,
         ];
         $self->setCell($initial_contents, $current_ticket_form_row);

         // Render ticket field ids
         $ticket_field_render_row = $current_ticket_form_row;
         foreach ($ticket_form->ticket_field_ids as $ticket_field_id) {
            $contents = [
               "J" => $this->display->ticketFieldValueFormatter($ticket_field_id),
            ];
            $self->setCell($contents, $ticket_field_render_row);
            $ticket_field_render_row++;

            if ($ticket_field_render_row >= $next_ticket_form_row) {
               $next_ticket_form_row = $ticket_field_render_row;
            }
         }

         // Render restricted brand ids
         $restricted_brand_render_row = $current_ticket_form_row;
         foreach ($ticket_form->restricted_brand_ids as $restricted_brand_id) {
            $contents = [
               "L" => $this->display->brandValueFormatter($restricted_brand_id),
            ];
            $self->setCell($contents, $restricted_brand_render_row);
            $ticket_field_render_row++;

            if ($restricted_brand_render_row >= $next_ticket_form_row) {
               $next_ticket_form_row = $restricted_brand_render_row;
            }
         }

         $this->styleCurrentRow($current_ticket_form_row, $next_ticket_form_row);
         $current_ticket_form_row = $next_ticket_form_row;
         $ticket_forms_num++;
      });
   }

   private function generateTicketForms()
   {
      if (count($this->ticket_forms) > 0) {
         return $this;
      }

      $client = $this->client;

      // Cache ticket fields for testing purpose
      // $ticket_forms_response = Cache::remember('ticket_forms_mock', 60, function() use ($client) {
      //    return $client->get('api/v2/ticket_forms');
      // });
      // $this->setTicketForms($ticket_forms_response->ticket_forms);

      $client = $this->client;
      $subdomain = $client->getSubdomain();
      $ticket_forms = Cache::remember("$subdomain.ticket_forms", 60, function() use ($client) {
         $ticket_forms = [];
         $page = 1;
         do {
            $response = $client->get("api/v2/ticket_forms?page=$page");
            $ticket_forms = array_merge($ticket_forms, $response->ticket_forms);
            $page++;
         } while ($response->next_page !== null);
         return $ticket_forms;
      });
      $this->setTicketForms($ticket_forms);

      return $this;
   }
}
