@extends('app')

@section('zendesk_config_name', 'Macros')

@section('content')
{!! Session::get('zendesk_macros_excel')!!}
@endsection

@section('javascript')
<script>
var macrosSelectList = [
   {
      title: "Assign Incident::HQ",
      active: true,
      position: 8,
      description: null,
      actions: [
         {
            field: "group_id",
            value: "28706967"
         },
         {
            field: "status",
            value: "open"
         }
      ],
      restriction: {
         type: "Group",
         id: 28737408,
         ids: [
            28737408
         ]
      }
   },
   {
      title: "Assign to HR Contact Center",
      active: true,
      position: 32,
      description: null,
      actions: [
         {
            field: "status",
            value: "open"
         },
         {
            field: "comment_mode_is_public",
            value: "false"
         },
         {
            field: "comment_value",
            value: [
               "channel:all",
               "Mohon bantuannya untuk hal berikut ini. Terima kasih @{{current_user.name}}"
            ]
         },
         {
            field: "group_id",
            value: "29580608"
         }
      ],
      restriction: {
         type: "Group",
         id: 29559947,
         ids: [
            29559947
         ]
      }
   },
}
];

/*=====MACROS=====*/
   if (macrosSelectList.length > 0) {
      $.ajax(getAllBrandsDest()).then(
         function(brandsDest){
           $.ajax(getTicketFields_dest()).then(
             function(ticketFieldsDest){
               $.ajax(getMacrosDest()).then(
                 function(macrosDest){
                   for (var i=0; i<macrosSelectList.length; i++) {
                     (function(counterI){
                       var macrosExist = false;
                       for (var m=0; m<macrosDest.macros.length; m++) {
                        if (macrosSelectList[i].title == macrosDest.macros[m].title) {
                           macrosExist = true;
                        }
                       }
                       if (!macrosExist) {
                        var processCounter = 0;
                        if (macrosSelectList[i].actions.length > 0) {
                           var actionCounter = 0;
                           for (var a=0; a<macrosSelectList[i].actions.length; a++) {
                             (function(counterA){
                              if (macrosSelectList[i].actions[a].field == 'group_id') {
                                 if (isNumeric(macrosSelectList[i].actions[a].value)) {
                                   $.ajax(getGroups(macrosSelectList[i].actions[a].value)).then(
                                     function(group){
                                       $.ajax(srcGroups_dest(group.group.name)).then(
                                         function(srcGroup){
                                           console.log(srcGroup);
                                           if (srcGroup.results.length > 0) {
                                             macrosSelectList[counterI].actions[counterA].value = ''+ srcGroup.results[0].id + '';
                                             actionCounter++;
                                             if (actionCounter == macrosSelectList[counterI].actions.length) {
                                               processCounter++;
                                               if (processCounter == 2) {
                                                 doCreateMacros(macrosSelectList[counterI]);
                                               }
                                             }
                                           } else {
                                             console.log('===== group not found =====');
                                             updateProgress('Macros', '<b>' + macrosSelectList[counterI].title + '</b> Error, Group not found: ' + group.group.name);
                                           }
                                         },
                                         function(srcGroupError){
                                           console.log('===== srcGroupError =====');
                                           console.log(srcGroupError);
                                         });
                                     },
                                     function(groupError){
                                       console.log('===== groupError =====');
                                       console.log(groupError);
                                     });
                                 } else {
                                   /*GROUP NOT NUMERIC*/
                                   actionCounter++;
                                   if (actionCounter == macrosSelectList[counterI].actions.length) {
                                     processCounter++;
                                     if (processCounter == 2) {
                                       doCreateMacros(macrosSelectList[counterI]);
                                     }
                                   }
                                 }
                              } else if (macrosSelectList[i].actions[a].field.includes('custom_fields_')) {
                                 var ticketId = macrosSelectList[i].actions[a].field.split('_');
                                   $.ajax(getTicketFieldsbyId(ticketId[2])).then(
                                     function(ticketField){
                                       var ticketFieldFound = false;
                                       for (var t=0; t<ticketFieldsDest.ticket_fields.length; t++) {
                                         if (ticketField.ticket_field.title == ticketFieldsDest.ticket_fields[t].title) {
                                           console.log('ticket_fields found with id: ' + ticketFieldsDest.ticket_fields[t].id);
                                           actionCounter++
                                           ticketFieldFound = true;
                                           macrosSelectList[counterI].actions[counterA].field = 'custom_fields_' + ticketFieldsDest.ticket_fields[t].id;
                                           if (actionCounter == macrosSelectList[counterI].actions.length) {
                                             processCounter++;
                                             if (processCounter == 2) {
                                               doCreateMacros(macrosSelectList[counterI]);
                                             }
                                           }
                                         }
                                       }
                                       if (!ticketFieldFound) {
                                         console.log('===== ticket field not found =====');
                                         updateProgress('Views', '<b>' + macrosSelectList[counterI].title + '</b> Error, Ticket field not found: ' + ticketField.ticket_field.title);
                                       }
                                     },
                                     function(ticketFieldError){
                                       console.log('===== ticketFieldError =====');
                                       console.log(ticketFieldError);
                                     });
                              } else if (macrosSelectList[i].actions[a].field == 'brand_id') {
                                 $.ajax(getBrands(macrosSelectList[i].actions[a].value)).then(
                                   function(brands){
                                     var brandsIsFound = false;
                                     for (var br=0; br<brandsDest.brands.length; br++) {
                                       if (brands.brand.name == brandsDest.brands[br].name) {
                                         brandsIsFound = true;
                                         console.log('brands found');
                                         actionCounter++;
                                         macrosSelectList[counterI].action[counterA].value = brandsDest.brands[br].id;
                                         if (actionCounter == macrosSelectList[counterI].actions.length) {
                                           processCounter++;
                                           if (processCounter == 2) {
                                             doCreateMacros(macrosSelectList[counterI]);
                                           }
                                         }
                                       }
                                     }
                                     if (!brandsIsFound) {
                                     console.log('===== brands not found =====');
                                       updateProgress('Macros', '<b>' + macrosSelectList[counterI].title + '</b> Error. Brands not exist: ' + brands.brand.name);
                                     }
                                   },
                                   function(brandsError){
                                     console.log('===== brandsError =====');
                                     console.log(brandsError);
                                   });
                              } else if (macrosSelectList[i].actions[a].field == 'cc' || macrosSelectList[i].actions[a].field == 'assignee_id') {
                                 if (isNumeric(macrosSelectList[i].actions[a].value)){
                                   $.ajax(getUsers(macrosSelectList[i].actions[a].value)).then(
                                     function(user){
                                       if (user.user.email !== null) {
                                         $.ajax(srcUserByEmail_dest(user.user.email)).then(
                                           function(srcUser){
                                             if (srcUser.results.length > 0) {
                                               macrosSelectList[counterI].actions[counterA].value = srcUser.results[0].id;
                                               actionCounter++;
                                               if (actionCounter == macrosSelectList[counterI].actions.length) {
                                                 processCounter++;
                                                 if (processCounter == 2) {
                                                   doCreateMacros(macrosSelectList[counterI]);
                                                 }
                                               }
                                             } else {
                                               updateProgress('Macros', '<b>' + macrosSelectList[counterI].title + '</b> Error. Cannot found users: ' + user.user.name);
                                             }
                                           },
                                           function(srcUserError){
                                             console.log('===== srcUserError =====');
                                             console.log(srcUserError);
                                           });
                                       } else {
                                         updateProgress('Macros', '<b>' + macrosSelectList[counterI].title + '</b> Error. Cannot found email users: ' + user.user.name);
                                       }
                                     },
                                     function(userError){
                                       console.log('===== userError =====');
                                       console.log(userError);
                                     });
                                 } else {
                                   /*CC NOT ID*/
                                   actionCounter++;
                                   if (actionCounter == macrosSelectList[counterI].actions.length) {
                                     processCounter++;
                                     if (processCounter == 2) {
                                       doCreateMacros(macrosSelectList[counterI]);
                                     }
                                   }
                                 }
                              } else {
                                 actionCounter++;
                                 if (actionCounter == macrosSelectList[counterI].actions.length) {
                                   processCounter++;
                                   if (processCounter == 2) {
                                     doCreateMacros(macrosSelectList[counterI]);
                                   }
                                 }
                              }
                             })(a);
                           }
                        } else {
                           console.log('action null.. proceed creating macros');
                        }
                        if (macrosSelectList[i].restriction !== null) {
                           if (macrosSelectList[i].restriction.type == 'Group') {
                             if (isNumeric(macrosSelectList[i].restriction.id)) {
                              if (macrosSelectList[i].restriction.ids.length > 0) {
                                 var idsCounter = 0;
                                 for (var r=0; r<macrosSelectList[i].restriction.ids.length; r++) {
                                   (function(counterR){
                                     $.ajax(getGroups(macrosSelectList[i].restriction.ids[r])).then(
                                       function(group){
                                         $.ajax(srcGroups_dest(group.group.name)).then(
                                           function(srcGroup){
                                             console.log(srcGroup);
                                             if (srcGroup.results.length > 0) {
                                               idsCounter++;
                                               if (macrosSelectList[counterI].restriction.ids[counterR] == macrosSelectList[counterI].restriction.id) {
                                                 macrosSelectList[counterI].restriction.id = srcGroup.results[0].id;
                                                 macrosSelectList[counterI].restriction.ids[counterR] = srcGroup.results[0].id;
                                               } else {
                                                 macrosSelectList[counterI].restriction.ids[counterR] = srcGroup.results[0].id;
                                               }
                                               if (idsCounter == macrosSelectList[counterI].restriction.ids.length) {
                                                 processCounter++;
                                                 if (processCounter == 2) {
                                                   doCreateMacros(macrosSelectList[counterI]);
                                                 }
                                               }
                                             } else {
                                               console.log('===== group not found =====');
                                               updateProgress('Macros', '<b>' + macrosSelectList[counterI].title + '</b> Error, Group not found: ' + group.group.name);
                                             }
                                           },
                                           function(srcGroupError){
                                             console.log('===== srcGroupError =====');
                                             console.log(srcGroupError);
                                           });
                                       },
                                       function(groupError){
                                         console.log('===== groupError =====');
                                         console.log(groupError);
                                       });
                                   })(r);
                                 }
                              }
                             } else {
                              /*GROUP NOT ID*/
                             }
                           }
                        } else {
                           processCounter++;
                           if (processCounter == 2) {
                             doCreateMacros(macrosSelectList[counterI]);
                           }
                        }
                       } else {
                        console.log('===== macros exist =====');
                       }
                     })(i);
                   }
                 },
                 function(macrosDestError){
                   console.log('===== macrosDestError =====');
                   console.log(macrosDestError);
                 });
             },
             function(ticketFieldsDestError){
               console.log('===== ticketFieldsDestError =====');
               console.log(ticketFieldsDestError);
             });
         },
         function(brandsDestError){
           console.log('===== brandsDestError =====');
           console.log(brandsDestError);
         });
     }
   }
</script>
@endsection
