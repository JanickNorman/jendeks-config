<?php

namespace App\ZendeskExcel\Formatter;

use Zendesk\API\HttpClient as ZendeskAPI;
use \Cache;
use \Exception;

class DisplayRepository
{
   protected $client;

   protected $via_values = [
      0 => "web_form",
      4 => 'mail',
      29 => 'chat',
      30 => 'twitter',
      26 => 'twitter_dm',
      23 => 'twitter_favorite',
      33 => 'voicemail',
      34 => 'phone_call_inbound',
      35 => 'phone_call_outbound',
      44 => 'api_voicemail',
      45 => 'api_phone_call_inbound',
      46 => 'api_phone_call_outbound',
      57 => 'sms',
      16 => 'get_satisfaction',
      48 => 'web_widget',
      49 => 'mobile_sdk',
      56 => 'mobile',
      5 => 'helpcenter',
      8 => 'rule',
      27 => 'closed_ticket',
      31 => 'ticket_sharing',
      38 => 'facebook_post',
      41 => 'facebook_message',
      54 => 'satisfaction_prediction',
      55 => 'any_channel'
   ];

   protected $requester_role_values = [
      0 => 'end-user',
      2 => 'admin',
      4 => 'agent'
   ];

   protected $ticket_type_values = [
      1 => "question",
      2 => 'incident',
      3 => 'problem',
      4 => 'task'
   ];

   public function __construct(ZendeskAPI $client)
   {
      $this->client = $client;
   }

   public function getBrandName($id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $brand = Cache::remember("$subdomain.brands.$id", 60, function() use ($self, $id) {
         try {
            $response = $self->client->brands()->find($id);
            return $response->brand;
         } catch (Exception $e) {
            return null;
         }
      });

      return isset($brand->name) ? $brand->name : $id;
   }

   public function getGroupName($id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $group = Cache::remember("$subdomain.groups.$id", 60, function() use ($self, $id) {
         try {
            $response = $self->client->groups()->find($id);
            return $response->group;
         } catch (Exception $e) {
            return null;
         }
      });
      return isset($group->name) ? $group->name : $id;
   }

   public function getOrganizationName($id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $organization = Cache::remember("$subdomain.organizations.$id", 60, function() use ($self, $id) {
         try {
            $response = $self->client->organizations()->find($id);
            return $response->organization;
         } catch (Exception $e) {
            return null;
         }
      });
      return isset($organization->name) ? $organization->name : $id;
   }

   public function getOrganizationFieldTitleByKey($key)
   {
      $organizationField = $this->getOrganizationFieldByKey($key);
      return isset($organizationField->title) ? $organizationField->title : $key;
   }

   public function getOrganizationFieldOptionName($key, $id)
   {
      $organizationField = $this->getOrganizationFieldByKey($key);
      if (isset($organizationField->custom_field_options)) {
         foreach ($organizationField->custom_field_options as $custom_field_option) {
            if ($custom_field_option->id == $id) {
               return $custom_field_option->name;
            }
         }
      }

      return $id;
   }

   public function getRequesterRoleValue($value) {
      return isset($this->requester_role_values[$value]) ? $this->requester_role_values[$value] : $value;
   }

   public function getScheduleName($id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $schedule = Cache::remember("$subdomain.schedules.$id", 60, function() use ($self, $id) {
         try {
            $response = $self->client->get("business_hours/schedules/$id");
            return $response->schedule;
         } catch (Exception $e) {
            return null;
         }
      });
      return isset($schedule->name) ? $schedule->name : $id;
   }

   public function getTargetTitle($id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $target = Cache::remember("$subdomain.targets.$id", 60, function() use ($self, $id) {
         try {
            $response = $self->client->targets()->find($id);
            return $response->target;
         } catch (Exception $e) {
            return null;
         }
      });

      return isset($target->title) ? $target->title : $id;
   }

   public function getTicketFieldTitle($id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $ticket_field = Cache::remember("$subdomain.ticket_fields.$id", 60, function() use ($self, $id) {
         try {
            $response = $self->client->ticketFields()->find($id);
            return $response->ticket_field;
         } catch (Exception $e) {
            return null;
         }
      });

      return isset($ticket_field->title) ? $ticket_field->title : $id;
   }

   public function getTicketFieldOptionTitle($custom_field_id, $id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $ticket_field = Cache::remember("$subdomain.ticket_fields.$custom_field_id", 60, function() use ($self, $custom_field_id) {
         try {
            $response = $self->client->ticketFields()->find($id);
            return $response->ticket_field;
         } catch (Exception $e) {
            return null;
         }
      });

      if (isset($ticket_field->custom_field_options)) {
         foreach ($ticket_field->custom_field_options as $custom_field_option) {
            if (isset($custom_field_option->id) && $custom_field_option->id == $id) {
               return $custom_field_option->name;
            }
         }
      }
      return $id;
   }

   public function getTicketFieldOptionTitleByValue($custom_field_id, $value)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $ticket_field = Cache::remember("$subdomain.ticket_fields.$custom_field_id", 60, function() use ($self, $custom_field_id) {
         try {
            $response = $self->client->ticketFields()->find($id);
            return $response->ticket_field;
         } catch (Exception $e) {
            return null;
         }
      });

      if (isset($ticket_field->custom_field_options)) {
         foreach ($ticket_field->custom_field_options as $custom_field_option) {
            if (isset($custom_field_option->value) && $custom_field_option->value == $value) {
               return $custom_field_option->name;
            }
         }
      }
      return $value;
   }

   public function getTicketFormName($id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $ticket_form = Cache::remember("$subdomain.ticket_forms.$id", 60, function() use ($self, $id) {
         try {
            $response = $self->client->get("ticket_forms/$id");
            return $response->ticket_form;
         } catch (Exception $e) {
            return null;
         }
      });
      return isset($ticket_form->name) ? $ticket_form->name : $id;
   }

   public function getTicketTypeValue($value) {
      return isset($this->ticket_type_values[$value]) ? $this->ticket_type_values[$value] : $value;
   }
   public function getSharingAgreementName($id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $sharing_agreement = Cache::remember("$subdomain.sharing_agreements.$id", 60, function() use ($self, $id) {
         try {
            $response = $self->client->get("sharing_agreements/$id");
            return $response->sharing_agreement;
         } catch (Exception $e) {
            return null;
         }
      });
      return isset($sharing_agreement->name) ? $sharing_agreement->name : $id;
   }

   public function getUserName($id)
   {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      $user = Cache::remember("$subdomain.users.$id", 60, function() use ($self, $id) {
         try {
            $response = $self->client->users()->find($id);
            return $response->user;
         } catch (Exception $e) {
            return null;
         }
      });
      return isset($user->name) ? $user->name : $id;
   }

   public function getUserFieldTitleByKey($key)
   {
      $userField = $this->getUserFieldByKey($key);
      return isset($userField->title) ? $userField->title : $key;
   }

   public function getUserFieldOptionName($key, $id)
   {
      $userField = $this->getUserFieldByKey($key);
      if (isset($userField->custom_field_options)) {
         foreach ($userField->custom_field_options as $custom_field_option) {
            if ($custom_field_option->id == $id) {
               return $custom_field_option->name;
            }
         }
      }

      return $id;
   }

   public function getViaValue($id)
   {
      return isset($this->via_values[$id]) ? $this->via_values[$id] : $id;
   }

   private function getOrganizationFieldByKey($key) {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      // Check if the user field is already cached, retrieve if it has
      $organizationField = Cache::get("$subdomain.organization_fields.$key");
      if ($organizationField) {
         return $organizationField;
      }

      // Cache all organization fields, for easier retrieval later
      $organizationFields = Cache::remember("$subdomain.organization_fields", 60, function() use ($self) {
         try {
            $response = $self->client->organizationFields()->findAll();
            return $response->organization_fields;
         } catch (Exception $e) {
            return null;
         }
      });

      // If organizationField unable to be cached, just return null
      if (!$organizationFields) {
         return null;
      }

      // Iterate all the fields, and cache each field with
      foreach ($organizationFields as $organizationField) {
         $organizationField = Cache::remember("$subdomain.organization_fields.$organizationField->key", 60, function() use ($self, $organizationField) {
               return $organizationField;
         });
         if ($organizationField->key == $key) {
            return $organizationField;
         }
      }

      return null;
   }

   private function getUserFieldByKey($key) {
      $self = $this;
      $subdomain = $this->client->getSubdomain();

      // Check if the user field is already cached, retrieve if it has
      $userField = Cache::get("$subdomain.user_fields.$key");
      if ($userField) {
         return $userField;
      }

      // Cache all user fields, for easier retrieval later
      $userFields = Cache::remember("$subdomain.user_fields", 60, function() use ($self) {
         try {
            $response = $self->client->userFields()->findAll();
            return $response->user_fields;
         } catch (Exception $e) {
            return null;
         }
      });

      // If userField unable to be cached, just return null
      if (!$userFields) {
         return null;
      }

      // Iterate all the fields, and cache each field with
      foreach ($userFields as $userField) {
         $userField = Cache::remember("$subdomain.user_fields.$userField->key", 60, function() use ($self, $userField) {
               return $userField;
         });
         if ($userField->key == $key) {
            return $userField;
         }
      }

      return null;
   }
}
