<?php
namespace App\ZendeskExcel\Formatter;

use Zendesk\API\HttpClient as ZendeskAPI;
use App\ZendeskExcel\Formatter\DisplayRepository;

class SLADisplayFormatter extends DisplayFormatter
{
   protected $conditionDisplayMapper = [
      "Ticket" => [
         'ticket_type_id' => 'Type',
      ]
   ];

   public function __construct(DisplayRepository $displayRepository)
   {
      parent::__construct($displayRepository);

      // Merge parent mapper with the display mapper specific to SLA
      $this->defaultConditionDisplayMapper = array_replace_recursive($this->defaultConditionDisplayMapper, $this->conditionDisplayMapper);
   }
}
