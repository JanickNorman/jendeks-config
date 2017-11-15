<?php

namespace App\ZendeskExcel;

use \Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;

class ExcelTicketField extends ResourceExcel
{
   const HEADERS_ROW_1 = ["No", "Type", "Title", "Raw Title", "Description", "Position", "Active", "Required", "Collapsed For Agents", "Regexp For Validation", "Title In Portal", "Raw Title In Portal", "Visible In Portal", "Editable In Portal", "Required In Portal", "Tag", "Removable", "Custom Field Options"];

   const HEADERS_ROW_2 = [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, "Name", "Value", "Default"];

   const STARTING_ROW = 3;

   public $ticket_fields;

   public function __construct(ZendeskAPI $client, $ticket_fields_response = [])
   {
      parent::__construct($client);

      $this->ticket_fields = isset($slas_response) ? $slas_response : [];
   }

   public function toExcel(): LaravelExcelWriter
   {
      $self = $this;

      $this->generateTicketFields();

      return Excel::create("template:treesdemo1:ticket_fields", function($excel)  use ($self) {
         $excel->sheet("template--ticket_fields", function($sheet) use ($self) {
            $self->buildHeader($sheet);
            $self->buildBody($sheet);
         });
      });
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function generateTicketFields()
   {
   }

   public function setTicketFields(array $ticket_fields)
   {
      $this->ticket_fields = collect($ticket_fields);
   }

   protected function buildHeader($sheet)
   {
      $sheet->row(1, $this::HEADERS_ROW_1);
      $sheet->row(2, $this::HEADERS_ROW_2);

      $sheet->mergeCells("R1:T1");
      foreach (range("A","Q") as $char) {
         $sheet->mergeCells($char."1:".$char."2");
      }

      $style = [
         'alignment' => [
              'horizontal' => 'center',
         ],
         'font' => [
            'bold' => true
         ]
      ];
      $sheet->getStyle("A1:T1")->applyFromArray($style);
      $sheet->getStyle("A2:T2")->applyFromArray($style);
   }

   protected function buildBody($sheet)
   {
      $self = $this;

      $current_row = self::STARTING_ROW;
      $ticket_fields_num = 1;
      collect($this->ticket_fields)->each(function($ticket_field) use (&$self, &$sheet, &$current_row, &$ticket_fields_num) {
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

               $self->setCell($sheet, $row_contents, $current_row);
               $current_row++;
            }
         } else {
            $self->setCell($sheet, $initial_contents, $current_row);
            $current_row++;
         }

         $ticket_fields_num++;
      });
      // dd('aloha');
   }
}
