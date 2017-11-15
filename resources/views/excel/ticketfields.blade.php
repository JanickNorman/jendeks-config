@extends('app')

@section('zendesk_config_name', 'Ticket Fields')

@section('content')
{!! Session::get('zendesk_ticketfields_excel')!!}
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">No</th>
      <th scope="col">Title</th>
      <th scope="col">View full json</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>Mark</td>
      <td>Otto</td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
    </tr>
  </tbody>
</table>
@endsection

@section('javascript')
   <script>
   // Mocking
   var ticketFieldsSelectList = [
      {
         title: "testes",
         type: "text"
      },
      {
         title: "testes1",
         type: "text"
      }
   ];
   if (ticketFieldsSelectList.length > 0) {
           var tfCounter = 0;
           $.ajax(getTicketFields_dest()).then(
             function (data){
               // console.log('get ticket_fields dest');
               for (var i=0; i<ticketFieldsSelectList.length; i++) {
                 var ticketFieldsExist = false;
                 for (var j = 0; j< data.ticket_fields.length; j++) {
                   if (ticketFieldsSelectList[i].title == data.ticket_fields[j].title) {
                     ticketFieldsExist = true;
                   }
                 }
                 (function(counterI){
                   if (!ticketFieldsExist) {
                     // console.log('ticket_fields notExist');
                     var ticketData = new Array({ticket_field:ticketFieldsSelectList[i]});
                     $.ajax(createTicketFields(JSON.stringify(ticketData[0]))).then(
                       function (createData){
                         tfCounter++;
                         console.log('create success');
                         // if (tfCounter == ticketFieldsSelectList.length) {
                         //   console.log('its DONE');
                         // }
                       },
                       function (errorCreateData){
                         tfCounter++;
                         // if (tfCounter == ticketFieldsSelectList.length) {
                         //   console.log('its DONE');
                         // }
                         console.log('===== error create ticket_fields dest ======');
                         console.log(errorCreateData);
                         updateProgress('Ticket Fields', 'Error Create Ticket: ' + ticketFieldsSelectList[counterI].title);
                       });
                   } else {
                     tfCounter++;
                     // if (tfCounter == ticketFieldsSelectList.length) {
                     //   console.log('its DONE');
                     // }
                     console.log('ticket_fields Exist');
                   }
                 })(i);
               }
             },
             function (errorData){
               console.log('===== errorData get ticket_fields list dest =====');
               console.log(errorData);
             });
   }
   </script>

@endsection
