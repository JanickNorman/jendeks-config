<?php

namespace App\ZendeskExcel;

use Zendesk\API\HttpClient as ZendeskAPI;
use \Excel;
use \Cache;

class ExcelGroup extends ResourceExcel
{
   protected $headers = [
      ["No", "Name"]
   ];

   public $groups;

   protected $name = "groups";

   public function __construct(ZendeskAPI $client, $groups_response = [])
   {
      parent::__construct($client);

      $this->groups = isset($groups_response->groups) ? $groups_response->groups : [];
   }

   public function read($filepath): Array
   {
      return [];
   }

   public function setGroups($groups)
   {
      $this->groups = $groups;
   }

   protected function generateResources()
   {
      return $this->generateGroups();
   }

   protected function buildBody()
   {
      $self = $this;

      $current_group_row = $this->getStartingRow();
      $groups_num = 1;
      $next_group_row = $current_group_row + 1;
      collect($this->groups)->each(function($group) use (&$self, &$current_group_row, &$groups_num, &$next_group_row) {
         $initial_contents = [
            "A" => $groups_num,
            "B" => $group->name
         ];
         $self->setCell($initial_contents, $current_group_row);

         $current_group_row++;
         $groups_num++;
      });
   }

   private function generateGroups()
   {
      if (count($this->groups) > 0) {
         return $this;
      }

      $client = $this->client;

      // Cache ticket fields for testing purpose
      $groups_response = Cache::remember('groups_mock', 60, function() use ($client) {
         return $client->groups()->findAll(['page' => 1]);
      });
      $this->setGroups($groups_response->groups);

      $client = $this->client;
      $subdomain = $client->getSubdomain();
      $groups = Cache::remember("$subdomain.groups", 60, function() use ($client) {
         $groups = [];
         $page = 1;
         do {
            $response = $client->groups()->findAll(['page' => $page]);

            // Group ngga punya active, jadi ngga usah filter
            $active_groups = $response->groups;

            $groups = array_merge($groups, $active_groups);
            $page++;
         } while ($response->next_page !== null);
         return $groups;
      });
      $this->setGroups($groups);

      return $this;
   }
}
