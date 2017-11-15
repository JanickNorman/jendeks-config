<?php

namespace App\ZendeskExcel;

use \Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ExcelTicketForm extends ResourceExcel
{
   const HEADERS_ROW_1 = ["No", "Name", "Raw Name", "Display Name", "Raw Display Name", "Position", "Active", "End User Visible", "Default", "Ticket Field Ids", "In All Brand", "Restricted Brand Ids"];

   const STARTING_ROW = 2;

   public $ticket_forms;

   public function __construct($ticket_forms_response = [])
   {
      $this->ticket_forms = isset($ticket_forms_response->ticket_forms) ? collect($ticket_forms_response->ticket_forms) : [];
   }

   public function toExcel(): LaravelExcelWriter
   {
      $self = $this;

      return Excel::create("template:treesdemo1:ticket_forms", function($excel)  use ($self) {
         $excel->sheet("template--ticket_forms", function($sheet) use ($self) {
            $self->buildHeader($sheet);
            $self->buildBody($sheet);
         });
      });
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setTicketForms($ticket_forms)
   {
      $this->ticket_forms = $ticket_forms;
   }

   protected function buildHeader($sheet)
   {
      $sheet->row(1, $this::HEADERS_ROW_1);

      $style = [
         'alignment' => [
            'horizontal' => 'center',
         ],
         'font' => [
            'bold' => true
         ]
      ];
      $sheet->getStyle("A1:L1")->applyFromArray($style);
   }

   protected function buildBody($sheet)
   {
      $self = $this;

      $current_ticket_form_row = self::STARTING_ROW;
      $ticket_forms_num = 1;
      $next_ticket_form_row = $current_ticket_form_row + 1;
      $this->ticket_forms->each(function($ticket_form) use (&$self, &$sheet, &$current_ticket_form_row, &$ticket_forms_num, &$next_ticket_form_row) {
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
         $self->setCell($sheet, $initial_contents, $current_ticket_form_row);

         // Render ticket field ids
         $ticket_field_render_row = $current_ticket_form_row;
         foreach ($ticket_form->ticket_field_ids as $ticket_field_id) {
            $contents = [
               "J" => $ticket_field_id,
            ];
            $self->setCell($sheet, $contents, $ticket_field_render_row);
            $ticket_field_render_row++;

            if ($ticket_field_render_row >= $next_ticket_form_row) {
               $next_ticket_form_row = $ticket_field_render_row;
            }
         }

         // Render restricted brand ids
         $restricted_brand_render_row = $current_ticket_form_row;
         foreach ($ticket_form->restricted_brand_ids as $restricted_brand_id) {
            $contents = [
               "L" => $restricted_brand_id,
            ];
            $self->setCell($sheet, $contents, $restricted_brand_render_row);
            $ticket_field_render_row++;

            if ($restricted_brand_render_row >= $next_ticket_form_row) {
               $next_ticket_form_row = $restricted_brand_render_row;
            }
         }

         $current_ticket_form_row = $next_ticket_form_row;
         $ticket_forms_num++;
      });
   }
}
