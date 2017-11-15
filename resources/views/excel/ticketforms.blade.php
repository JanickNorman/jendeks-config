@extends('app')

@section('zendesk_config_name', 'Ticket Forms')

@section('content')
   {!! Session::get('zendesk_ticketforms_excel')!!}
@endsection

@section('javascript')
<script>
// Mocking
var ticketFormsSelectList = [
   {
      name: "Ticket Form - HR Helpdesk tiga",
      raw_name: "Ticket Form - HR Helpdesk tiga",
      display_name: "Form",
      raw_display_name: "Form",
      end_user_visible: true,
      position: 0,
      ticket_field_ids: [33034688,33034708],
      active: true,
      default: false,
      in_all_brands: false,
      restricted_brand_ids: [
         1376947
      ]
   },
   {
      name: "Ticket Form - HR Helpdesk dua",
      raw_name: "Ticket Form - HR Helpdesk dua",
      display_name: "Form",
      raw_display_name: "Form",
      end_user_visible: true,
      position: 0,
      ticket_field_ids: [33034688],
      active: true,
      default: false,
      in_all_brands: false,
      restricted_brand_ids: [
         1376947
      ]
   },
];

 /*  Migrate Ticket Form  */
 /*=====TICKET FORMS=====*/
 if (ticketFormsSelectList.length > 0) {
  $.ajax(getTicketFields()).then(
     function(ticketFieldsData){
       $.ajax(getTicketFields_dest()).then(
         function(ticketFieldsDestData){
          $.ajax(getTicketForms_dest()).then(
             function(ticketFormsDestData){
               for (var i=0; i<ticketFormsSelectList.length; i++){
                 var isExist = false;
                 for (var j=0; j<ticketFormsDestData.ticket_forms.length; j++) {
                   if (ticketFormsSelectList[i].name == ticketFormsDestData.ticket_forms[j].name) {
                    isExist = true;
                   }
                 }
                 if (!isExist) {
                   var ticketFieldsExist = false;
                   var ticketFieldsCounter = 0;
                   var ticketFieldsFrom = [];
                   var newTicketIds = [];
                   var ticketFieldsError = false;

                   for (var tf=0; tf<ticketFormsSelectList[i].ticket_field_ids.length; tf++){
                    ticketFieldsFrom = [];
                    ticketFieldsExist = false;
                    for (var tfFrom=0; tfFrom<ticketFieldsData.ticket_fields.length; tfFrom++) {
                       if (ticketFormsSelectList[i].ticket_field_ids[tf] == ticketFieldsData.ticket_fields[tfFrom].id) {
                         ticketFieldsFrom = ticketFieldsData.ticket_fields[tfFrom];
                       }
                    }
                    console.log(ticketFieldsFrom);
                    for (var tfDest=0; tfDest<ticketFieldsDestData.ticket_fields.length; tfDest++){
                       if (ticketFieldsFrom.title == ticketFieldsDestData.ticket_fields[tfDest].title) {
                         ticketFieldsExist = true;
                         newTicketIds.push(ticketFieldsDestData.ticket_fields[tfDest].id);
                       }
                    }
                    if (ticketFieldsExist) {
                       ticketFieldsCounter++;
                    } else {
                       ticketFieldsError = true;
                       console.log('===== have to create some ticket fields =====');
                       updateProgress('Ticket Forms', '<b>' + ticketFormsSelectList[i].name + '</b> Error. Some ticket fields not exist: ' + ticketFieldsFrom.title);
                    }
                    ticketFieldsExist = false;
                   }
                   // if (ticketFieldsError) {
                   //   updateProgress('Ticket Forms', '<b>' + ticketFormsSelectList[i].name + '</b> Error. Some ticket fields not exist: ' + ticketFieldsFrom.title);
                   //   errorMigrate.push({
                   //     name: ticketFormsSelectList[i].name,
                   //     error: 'some ticket fields not exist'
                   //   });
                   // }
                   (function(counterI){
                    if (ticketFieldsCounter == ticketFormsSelectList[i].ticket_field_ids.length) {
                       ticketFormsSelectList[i].ticket_field_ids = newTicketIds;
                       console.log('===== done processing ticket fields ======');
                       console.log(ticketFormsSelectList[i]);
                       var ticketForms = new Array({ticket_form:ticketFormsSelectList[i]});
                       console.log(ticketForms);
                       $.ajax(createTicketForms(JSON.stringify(ticketForms[0]))).then(
                         function(createData){
                           console.log('===== CREATE SUCCESS =====');
                           console.log(createData);
                         },
                         function(createError){
                           console.log('===== createError =====');
                           console.log(createError);
                           errorMigrate.push({
                             name: ticketFormsSelectList[counterI].name,
                             error: createError
                           });
                           updateProgress('Ticket Forms', '<b>' + 'Error when create ticket forms: ' + ticketFormsSelectList[counterI].name);
                         });
                       newTicketIds = [];
                    }
                   })(i);
                 } else {
                   console.log('===== ticket forms is exist =====');
                 }
               }
             },
             function(ticketFormsDestError){

             });
         },
         function(ticketFieldsDestError){
          console.log('ticketFieldsDestError');
          console.log(ticketFieldsDestError);
         });
     },
     function(ticketFieldsError){
       console.log('ticketFieldsError');
       console.log(ticketFieldsError);
     });
  /*var ticketFormsCounter = -1;
  $.ajax(getTicketFields_dest()).then(
     function(tfDestData){
       $.ajax(getTicketForms_dest()).then(
         function (data) {
          for (var i=0; i<ticketFormsSelectList.length; i++) {
             var newTicketIds = [];
             var ticketFormExist = false;
             for (var j=0; j<data.ticket_forms.length; j++) {
               if (ticketFormsSelectList[i].name == data.ticket_forms[j].name) {
                 ticketFormExist = true;
               }
             }
             if (!ticketFormExist) {
               console.log('ticket_forms is notExist');
               console.log('get ticket_fields info');
               var ticketFieldsCount = 0;
               for (var k=0; k<ticketFormsSelectList[i].ticket_field_ids.length; k++) {
                 $.ajax(getTicketFieldsbyId(ticketFormsSelectList[i].ticket_field_ids[k])).then(
                   function(tfDataFrom){
                    var ticket_fieldsExist = false;
                    var ticketId = 0;
                    for (var l=0; l<tfDestData.ticket_fields.length; l++){
                       if (tfDataFrom.ticket_field.title == tfDestData.ticket_fields[l].title){
                         ticket_fieldsExist = true;
                         ticketId = tfDestData.ticket_fields[l].id;
                       }
                    }
                    if (!ticket_fieldsExist) {
                       console.log('ticket_fields is notExist');
                       console.log('creating ticket_fields');
                       var newTicketFields = new Array({ticket_field:tfDataFrom.ticket_field})
                       $.ajax(createTicketFields(JSON.stringify(newTicketFields[0]))).then(
                         function (createTfData){
                           if (ticketFieldsCount==0) {
                             ticketFormsCounter++;
                           }
                           newTicketIds.push(createTfData.ticket_field.id);
                           ticketFieldsCount++;
                           if (ticketFieldsCount == ticketFormsSelectList[ticketFormsCounter].ticket_field_ids.length) {
                             console.log('ticket fields done');
                             ticketFormsSelectList[ticketFormsCounter].ticket_field_ids = newTicketIds;
                             var ticketForms = new Array({ticket_form:ticketFormsSelectList[ticketFormsCounter]})
                             $.ajax(createTicketForms(JSON.stringify(ticketForms[0]))).then(
                               function (createFormData){
                                 console.log(createFormData);
                               },
                               function (errorCreateFormData) {
                                 console.log('===== error create ticket_forms dest =====');
                                 console.log(errorCreateFormData);
                               });
                             newTicketIds = [];
                           }
                         },
                         function (errorCreateTfData) {
                           console.log('===== error create ticket_fields dest ======');
                           console.log(errorCreateTfData);
                         });
                    } else {
                       if (ticketFieldsCount==0) {
                         ticketFormsCounter++;
                       }
                       ticketFieldsCount++;
                       newTicketIds.push(ticketId);
                       console.log('ticket_fields isExist');
                       if (ticketFieldsCount == ticketFormsSelectList[ticketFormsCounter].ticket_field_ids.length) {
                         console.log('ticket fields done');
                         ticketFormsSelectList[ticketFormsCounter].ticket_field_ids = newTicketIds;
                         var ticketForms = new Array({ticket_form:ticketFormsSelectList[ticketFormsCounter]});
                         $.ajax(createTicketForms(JSON.stringify(ticketForms[0]))).then(
                           function (createFormData){
                             console.log(createFormData);
                           },
                           function (errorCreateFormData) {
                             console.log('===== error create ticket_forms dest =====');
                             console.log(errorCreateFormData);
                           });
                         newTicketIds = [];
                       }
                    }
                   },
                   function(errorTfDataFrom){
                    console.log('====== error data get ticket_fields info ======');
                    console.log(errorTfDataFrom);
                   });
               }
             } else {
               console.log('ticket_forms Exist');
             }
          }
         },
         function (errorData){
          console.log('===== error get ticket_forms dest ======');
          console.log(errorData);
         });
     },
     function (errorTfDestData){
     });
  */
 }

</script>
@endsection
