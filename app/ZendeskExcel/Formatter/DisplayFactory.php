<?php

namespace App\ZendeskExcel\Formatter;

use \Cache;
use Zendesk\API\HttpClient as ZendeskAPI;

class DisplayFactory
{
   protected $client;

   public function __construct(ZendeskAPI $client)
   {
      $this->client = $client;
   }

   public function getResourceName($id, $type) {
      switch ($type) {
         case "brand":
            return $this->getBrand($id);
            break;
         case "ticket_field":
            return $this->getTicketField($id);
            break;
         case "ticket_form":
            return $this->getTicketForm($id);
            break;
         case "user" || "assignee":
            return $this->getUser($id);
            break;
         case "custom_field":
            return $this->getCustomField($id);
            break;
         case "organization":
            return $this->getOrganization($id);
            break;
         case "via":
            return $this->getVia($id);
            break;
         case "group":
            return $this->getGroup($id);
            break;
         default:
            return $id;
            break;
      }
   }

   protected function getUser($id)
   {
      return "";
   }

   protected function getBrand($id)
   {
      return "";
   }

   protected function getTicketField($id)
   {
      return "";
   }

   protected function getTicketForm($id)
   {
      return "";
   }

   protected function getCustomField($id)
   {
      return "";
   }

   protected function getVia($id)
   {
      return "";
   }
}
