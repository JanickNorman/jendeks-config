<?php
namespace App\ZendeskExcel\Formatter;

use Zendesk\API\HttpClient as ZendeskAPI;
use App\ZendeskExcel\Formatter\DisplayRepository;

class DisplayFormatter
{
   protected $defaultConditionDisplayMapper = [
      "Ticket" => [
         "group_id" => "Group",
         "assignee_id" => "Assignee",
         "requester_id" => "Requester",
         "organization_id" => "Organization",
         "current_tags" => "Tags",
         "via_id" => "Channel",
         "recipient" => "Received at",

         "brand_id" => "Brand",
         "ticket_form_id" => "Form",
         "via_subtype_id" => "Integration account",
         "schedule_id" => "Schedule",
         "within_schedule" => "Within (schedule)",

         // Only in Trigger, Automation, View
         "type" => "Type",
         "status" => "Status",
         "priority" => "Priority",
         "description_includes_word" => "Description",
         "satisfaction_score" => "Satisfaction",

         // "satisfaction_reason" => "Satisfaction Reason",
         "satisfaction_reason_code" => "Satisfaction Reason",

         // Only in Trigger
         "subject_includes_word" => "Subject text",
         "comment_includes_word" => "Comment text",
         "current_via_id" => "Update via",
         "update_type" => "Ticket",
         "comment_is_public" => "Comment",
         "ticket_is_public" => "Privacy",
         "reopens" => "Reopens",
         "replies" => "Agent replies",
         "agent_stations" => "Assignee stations",
         "group_stations" => "Group stations",
         "in_business_hours" => "Within business hours?",

         "number_of_incidents" => "Number of incidents",
         "ticket_due_date" => "Due date",

         // Only in SLA
         // "ticket_type_id" => "Type" //bentrok,
         "exact_created_at" => "Created",

         // Only in SLA and Views
         "NEW" => "Hours since created",
         "OPEN" => "Hours since open",
         "PENDING" => "Hours since pending",
         "SOLVED" => "Hours since solved",
         "CLOSED" => "Hours since closed",
         "assigned_at" => "Hours since assigned",
         "updated_at" => "Hours since updated",
         "requester_updated_at" => "Hours since requester update",
         "assignee_updated_at" => "Hours since assignee update",
         // "due_date" => "Hours since due date", In DOCUMENTATION,
         "due_at" => "Hours since due date", // In REALITY
         "until_due_date" => "Hours until due date",
         "sla_next_breach_at" => "Hours since last SLA breach",
         "until_sla_next_breach_at" => "Hours until next SLA breach",

         "HOLD" => "Hours since on-hold"
      ],
      "Requester" => [
         // Only in Trigger, Automation, View
         "requester_role" => "Role",
         "locale_id" => "Locale",

         // Only in Trigger
         "requester_twitter_followers_count" => "Number of Twitter followers",
         // "requester_twitter_status_count" => "Number of tweets", // In DOCUMENTATION
         "requester_twitter_statuses_count" => "Number of tweets", // In REALITY
         "requester_twitter_verified" => "Is verified by Twitter",

         "requester_time_zone" => "Time zone"
      ],
      "Ticket Sharing" => [
         "received_from" => "Ticket Sharing",
         "sent_to" => "Ticket Sharing",
      ],
      "Other" => [
         "role" => "Current user"
      ]
   ];

   protected $defaultActionDisplayMapper = [
      "Ticket" => [
         // Trigger, Automation, Macro
         "status" => "Status",
         "type" => "Type",
         "priority" => "Priority",
         "group_id" => "Group",
         "assignee_id" => "Assignee",
         "set_tags" => "Set tags",
         // "current_tags" => "Current tags",
         "remove_tags" => "Remove tags",

         "add_tags" => "Add tags",
         "set_schedule" => "Set schedule",
         "share_ticket" => "Share ticket with",

         // Trigger and Automation
         "satisfaction_score" => "Satisfaction",
         "cc" => "Add cc",

         // Macro
         "subject" => "Set subject",
         "comment_value_html" => "Comment/description",
         "comment_mode_is_public" => "Comment mode"
      ],
      "Notification" => [
         // Trigger and Automation
         "notification_user" => "Email user",
         "notification_group" => "Email group",
         "notification_target" => "Notify target",
         "tweet_requester" => "Tweet requester",

         "deflection" => "Answer Bot"
      ],
      "Requester" => [
         "locale_id" => "Language"
      ]
   ];

   protected $defaultConditionFieldsObjectMapper = [
      // Ticket
      "brand_id" => "brand",
      "ticket_form_id" => "ticket_form",
      'ticket_type_id' => "ticket_type",
      "group_id" => "group",
      "assignee_id" => "user",
      "requester_id" => "user",
      "organization_id" => "organization",
      "current_via_id" => "via",
      "via_subtype_id" => "channel",
      "schedule_id" => "schedule",
      "within_schedule" => "schedule",

      "via_id" => "via",

      // Ticket sharing
      "received_from" => "ticket_sharing",
      "sent_to" => "ticket_sharing",

      // Requester
      "requester_role" => "requester_role",

      // Other
      "role" => "user",
   ];

   protected $defaultActionFieldsObjectMapper = [
      "brand_id" => "brand",
      "ticket_form_id" => "ticket_form",
      "group_id" => "group",
      "assignee_id" => "user",
      "cc" => "user",
      "set_schedule" => "schedule",
      "share_ticket" => "ticket_sharing",

      "notification_user" => "user",
      "notification_group" => "group",
      "notification_target" => "target",
   ];

   public function __construct(DisplayRepository $displayRepository)
   {
      $this->displayRepository = $displayRepository;
   }

   public function rulesFieldFormatter($field)
   {
      foreach ($this->defaultConditionDisplayMapper as $type => $fields) {
         if (isset($fields[$field])) {
            return "$type: ". $fields[$field];
         }
      }

      foreach ($this->defaultActionDisplayMapper as $type => $fields) {
         if (isset($fields[$field])) {
            return "$type: ". $fields[$field];
         }
      }

      if ($this->isCustomField($field)) {
         $customFieldId = $this->retrieveCustomFieldId($field);
         return "Custom Field: " . $this->displayRepository->getTicketFieldTitle($customFieldId);
      }

      if ($this->isTicketField($field)) {
         $ticket_field_id = $this->retrieveTicketFieldId($field);
         return "Custom Field: " . $this->displayRepository->getTicketFieldTitle($ticket_field_id);
      }

      if ($this->isOrganizationField($field)) {
         $organizationFieldKey = $this->retrieveOrganizationFieldKey($field);
         return "Organization Custom Field: " . $this->displayRepository->getOrganizationFieldTitleByKey($organizationFieldKey);
      }

      if ($this->isUserField($field)) {
         $userFieldKey = $this->retrieveUserFieldKey($field);
         return "Requester Custom Field: " . $this->displayRepository->getUserFieldTitleByKey($userFieldKey);
      }

      return $field;
   }

   public function rulesValueFormatter($field, $value)
   {
      $type = $this->getFieldType($field);

      if ($type == "brand") {
         return is_numeric($value) ? $this->displayRepository->getBrandName($value) : $value;
      }

      if ($type == "group") {
         return is_numeric($value) ? $this->displayRepository->getGroupName($value) : $value;
      }

      if ($type == "organization") {
         return is_numeric($value) ? $this->displayRepository->getOrganizationName($value) : $value;
      }

      if ($type == "organization_field") {
         $organization_field_value = $this->retrieveOrganizationFieldKey($field);
         return is_numeric($value) ? $this->displayRepository->getOrganizationFieldOptionName($organization_field_value, $value) : $value;
      }

      if ($type == "requester_role") {
         return $this->displayRepository->getRequesterRoleValue($value);
      }

      if ($type == "schedule") {
         return $this->displayRepository->getScheduleName($value);
      }

      if ($type == "target") {
         return is_numeric($value) ? $this->displayRepository->getTargetTitle($value) : $value;
      }

      if ($type == "ticket_field") {
         if ($this->isCustomField($field)) {
            $custom_field_id = $this->retrieveCustomFieldId($field);
            return $this->displayRepository->getTicketFieldOptionTitleByValue($custom_field_id, $value);
         }

         if ($this->isTicketField($field)) {
            $ticket_field_id = $this->retrieveTicketFieldId($field);
            return $this->displayRepository->getTicketFieldOptionTitle($ticket_field_id, $value);
         }
      }

      if ($type == "ticket_form") {
         return $this->displayRepository->getTicketFormName($value);
      }

      if ($type == "ticket_sharing") {
         return is_numeric($value) ? $this->displayRepository->getSharingAgreementName($value) : $value;
      }

      if ($type == "ticket_type") {
         return $this->displayRepository->getTicketTypeValue($value);
      }

      if ($type == "user") {
         return is_numeric($value) ? $this->displayRepository->getUserName($value) : $value;
      }

      if ($type == "user_field") {
         $user_field_value = $this->retrieveUserFieldKey($field);
         return is_numeric($value) ? $this->displayRepository->getUserFieldOptionName($user_field_value, $value) : $value;
      }

      if ($type == "via") {
         return is_numeric($value) ? $this->displayRepository->getViaValue($value) : $value;
      }

      return $value;
   }

   public function brandValueFormatter($value)
   {
      return is_numeric($value) ? $this->displayRepository->getBrandName($value) : $value;
   }

   public function ticketFieldValueFormatter($value)
   {
      return is_numeric($value) ? $this->displayRepository->getTicketFieldTitle($value) : $value;
   }

   public function restrictionValueFormatter($field, $value)
   {
      if ($field == "Group") {
         return is_numeric($value) ? $this->displayRepository->getGroupName($value) : $value;
      }

      if ($field == "User") {
         return is_numeric($value) ? $this->displayRepository->getUserName($value) : $value;
      }

      return $value;
   }

   protected function getFieldType($field_string)
   {
      if (isset($this->defaultConditionFieldsObjectMapper[$field_string])) {
         return $this->defaultConditionFieldsObjectMapper[$field_string];
      }

      if (isset($this->defaultActionFieldsObjectMapper[$field_string])) {
         return $this->defaultActionFieldsObjectMapper[$field_string];
      }

      if ($this->isTicketField($field_string) || $this->isCustomField($field_string)) {
         return "ticket_field";
      }

      if ($this->isUserField($field_string)) {
         return "user_field";
      }

      if ($this->isOrganizationField($field_string)) {
         return "organization_field";
      }

      return null;
   }

   // Check if field string is referencing custom fields
   protected function isTicketField($field)
   {
      return substr($field, 0, 14) === "ticket_fields_";
   }

   protected function isCustomField($field)
   {
      return substr($field, 0, 14) === "custom_fields_";
   }

   protected function isUserField($field)
   {
      return substr($field, 0, 24) === "requester.custom_fields.";
   }

   protected function isOrganizationField($field)
   {
      return substr($field, 0, 27) === "organization.custom_fields.";
   }

   protected function retrieveOrganizationFieldKey($field)
   {
      return substr($field, 27);
   }

   protected function retrieveCustomFieldId($field)
   {
      if ($this->isCustomField($field)) {
         return substr($field, 14);
      }
      return $field;
   }

   protected function retrieveTicketFieldId($field)
   {
      if ($this->isTicketField($field)) {
         return substr($field, 14);
      }
      return $field;
   }

   protected function retrieveUserFieldKey($field)
   {
      return substr($field, 24);
   }

   // Check if field string is referencing some resource
   protected function isResource($field_string)
   {
      return substr($field_string, -3) === "_id";
   }
}
