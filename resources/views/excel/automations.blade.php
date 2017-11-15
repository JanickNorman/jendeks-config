@extends('app')

@section('zendesk_config_name', 'Automations')

@section('content')
{!! Session::get('zendesk_automations_excel')!!}
@endsection

@section('javascript')
<script>
// Mock
var automationsSelectList = [
   {
      title: "Close ticket 4 days after status is set to solved no exist",
      active: true,
      actions: [
         {
         field: "status",
         value: "closed"
         }
      ],
      conditions: {
         all: [
            {
               field: "status",
               operator: "is",
               value: "solved"
            },
            {
               field: "SOLVED",
               operator: "greater_than",
               value: "96"
            }
         ],
         any: [ ]
      },
      position: 0,
      raw_title: "Close ticket 4 days after status is set to solved no exist"
   },
   {
      title: "Pending notification 24 hours no exist",
      active: false,
      actions: [
         {
            field: "notification_user",
            value: [
               'requester_id',
               '[@{{ticket.account}}] Pending request: @{{ticket.title}}',
               'This is an email to remind you that your request (#@{{ticket.id}}) is pending and awaits your feedback @{{ticket.comments_formatted}}'
            ]
         }
      ],
      conditions: {
         all: [
            {
               field: "PENDING",
               operator: "is",
               value: "24"
            }
         ],
         any: [ ]
      },
      position: 9998,
      raw_title: "Pending notification 24 hours no exist"
   }
];

/*=====AUTOMATIONS=====*/
if (automationsSelectList.length > 0) {
 $.ajax(getTicketFields_dest()).then(
    function(ticketFieldsDestData){
      $.ajax(getAutomations_dest()).then(
        function(automationsDest){
          var processCounter = 0;
          var counterArray = [];
          for (var i=0; i<automationsSelectList.length; i++) {
            var automationsExist = false;
            var processCounter = 0;
            for (var td=0; td<automationsDest.automations.length; td++) {
              if (automationsSelectList[i].title == automationsDest.automations[td].title) {
                automationsExist = true;
              }
            }
            if (!automationsExist) {
              (function(counterI){
                if (automationsSelectList[i].actions.length > 0) {
                  var actionCounter = 0;
                  for (var a=0; a<automationsSelectList[i].actions.length; a++) {
                    (function(counterA){
                      if (automationsSelectList[i].actions[a].field == 'notification_user') {
                        if (isNumeric(automationsSelectList[i].actions[a].value[0])) {

                          $.ajax(getUsers(automationsSelectList[i].actions[a].value[0])).then(
                            function(user){
                              if (user.user.email !== null) {
                                $.ajax(srcUserByEmail_dest(user.user.email)).then(
                                  function(srcUser){
                                    if (srcUser.results.length > 0) {
                                      counterArray.push(counterI);
                                      console.log(counterArray);
                                      console.log('user found');
                                      automationsSelectList[counterI].actions[counterA].value[0] = srcUser.results[0].id;
                                      var caCounter = 0;
                                      for (c in counterArray) {
                                        var aCounter = automationsSelectList[counterI].actions.length;
                                        var alCounter = automationsSelectList[counterI].conditions.all.length;
                                        var anCounter = automationsSelectList[counterI].conditions.any.length;
                                        if (counterArray[c] == counterI) {
                                          caCounter++;
                                          if (caCounter == aCounter + alCounter + anCounter) {
                                            doCreateAutomations(automationsSelectList[counterI]);
                                          }
                                        }
                                      }
                                      // actionCounter++;
                                      // if (counterA == automationsSelectList[counterI].actions.length-1) {
                                      //   processCounter++
                                      //   if (processCounter == 3) {
                                      //     doCreateAutomations(automationsSelectList[counterI]);
                                      //   }
                                      // }
                                    } else {
                                      console.log('===== user not found =====');
                                      updateProgress('Automations', '<b>' + automationsSelectList[counterI].title + '</b> Error, Users not found: ' + user.user.name);
                                    }
                                  },
                                  function(srcUserError){
                                    console.log('===== srcUserError =====');
                                    console.log(srcUserError);
                                  });
                              }
                            },
                            function(userError){
                              console.log('===== userError =====');
                              console.log(userError);
                            });
                        } else {
                          counterArray.push(counterI);
                          var caCounter = 0;
                          for (c in counterArray) {
                            var aCounter = automationsSelectList[counterI].actions.length;
                            var alCounter = automationsSelectList[counterI].conditions.all.length;
                            var anCounter = automationsSelectList[counterI].conditions.any.length;
                            if (counterArray[c] == counterI) {
                              caCounter++;
                              if (caCounter == aCounter + alCounter + anCounter) {
                                doCreateAutomations(automationsSelectList[counterI]);
                              }
                            }
                          }
                          // actionCounter++;
                          // if (counterA == automationsSelectList[counterI].actions.length-1) {
                          //   processCounter++
                          //   if (processCounter == 3) {
                          //     doCreateAutomations(automationsSelectList[counterI]);
                          //   }
                          // }
                        }
                      } else if (automationsSelectList[i].actions[a].field == 'notification_group') {
                        if (isNumeric(automationsSelectList[i].actions[a].value[0])) {
                          $.ajax(getGroups(automationsSelectList[i].actions[a].value[0])).then(
                            function(group){
                              $.ajax(srcGroups_dest(group.group.name)).then(
                                function(srcGroup){
                                  if (srcGroup.results.length > 0) {
                                    counterArray.push(counterI);
                                    console.log(counterArray);
                                    automationsSelectList[counterI].actions[counterA].value[0] = srcGroup.results[0].id;
                                    var caCounter = 0;
                                    for (c in counterArray) {
                                      var aCounter = automationsSelectList[counterI].actions.length;
                                      var alCounter = automationsSelectList[counterI].conditions.all.length;
                                      var anCounter = automationsSelectList[counterI].conditions.any.length;
                                      if (counterArray[c] == counterI) {
                                        caCounter++;
                                        if (caCounter == aCounter + alCounter + anCounter) {
                                          doCreateAutomations(automationsSelectList[counterI]);
                                        }
                                      }
                                    }
                                    // actionCounter++;
                                    // if (counterA == automationsSelectList[counterI].actions.length-1) {
                                    //   processCounter++
                                    //   if (processCounter == 3) {
                                    //     doCreateAutomations(automationsSelectList[counterI]);
                                    //   }
                                    // }
                                  } else {
                                    console.log('===== group not found =====');
                                    updateProgress('Automations', '<b>' + automationsSelectList[counterI].title + '</b> Error, Group not found: ' + group.group.name);
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
                          counterArray.push(counterI);
                          var caCounter = 0;
                          for (c in counterArray) {
                            var aCounter = automationsSelectList[counterI].actions.length;
                            var alCounter = automationsSelectList[counterI].conditions.all.length;
                            var anCounter = automationsSelectList[counterI].conditions.any.length;
                            if (counterArray[c] == counterI) {
                              caCounter++;
                              if (caCounter == aCounter + alCounter + anCounter) {
                                doCreateAutomations(automationsSelectList[counterI]);
                              }
                            }
                          }
                          // actionCounter++;
                          // if (counterA == automationsSelectList[counterI].actions.length-1) {
                          //   processCounter++
                          //   if (processCounter == 3) {
                          //     doCreateAutomations(automationsSelectList[counterI]);
                          //   }
                          // }
                        }
                      } else if (automationsSelectList[i].actions[a].field.includes('custom_fields_')) {
                        var ticketId = automationsSelectList[i].actions[a].field.split('_');
                        $.ajax(getTicketFieldsbyId(ticketId[2])).then(
                          function(ticketField){
                            var ticketFieldFound = false;
                            var ticketDestId = '';
                            for (var t=0; t<ticketFieldsDestData.ticket_fields.length; t++) {
                              if (ticketField.ticket_field.title == ticketFieldsDestData.ticket_fields[t].title) {
                                ticketFieldFound = true;
                                ticketDestId = ticketFieldsDestData.ticket_fields[t].id;
                              }
                            }
                            if (ticketFieldFound) {
                              counterArray.push(counterI);
                              console.log(counterArray);
                              automationsSelectList[counterI].actions[counterA].field = 'custom_fields_' + ticketDestId;
                              var caCounter = 0;
                              for (c in counterArray) {
                                var aCounter = automationsSelectList[counterI].actions.length;
                                var alCounter = automationsSelectList[counterI].conditions.all.length;
                                var anCounter = automationsSelectList[counterI].conditions.any.length;
                                if (counterArray[c] == counterI) {
                                  caCounter++;
                                  if (caCounter == aCounter + alCounter + anCounter) {
                                    doCreateAutomations(automationsSelectList[counterI]);
                                  }
                                }
                              }
                              // actionCounter++;
                              // if (counterA == automationsSelectList[counterI].actions.length-1) {
                              //   processCounter++
                              //   if (processCounter == 3) {
                              //     doCreateAutomations(automationsSelectList[counterI]);
                              //   }
                              // }
                            } else {
                              console.log('===== ticket field not found =====');
                              updateProgress('Automations', '<b>' + automationsSelectList[counterI].title + '</b> Error, Ticket Field not found: ' + ticketField.ticket_field.title);
                            }
                          },
                          function(ticketFieldError){
                            console.log('===== ticketFieldError =====');
                            console.log(ticketFieldError);
                          });
                      } else {
                        var caCounter = 0;
                        for (c in counterArray) {
                          var aCounter = automationsSelectList[counterI].actions.length;
                          var alCounter = automationsSelectList[counterI].conditions.all.length;
                          var anCounter = automationsSelectList[counterI].conditions.any.length;
                          if (counterArray[c] == counterI) {
                            caCounter++;
                            if (caCounter == aCounter + alCounter + anCounter) {
                              doCreateAutomations(automationsSelectList[counterI]);
                            }
                          }
                        }
                        // actionCounter++;
                        // if (counterA == automationsSelectList[counterI].actions.length-1) {
                        //   processCounter++
                        //   if (processCounter == 3) {
                        //     doCreateAutomations(automationsSelectList[counterI]);
                        //   }
                        // }
                      }
                    })(a);
                  }
                } else {
                  counterArray.push(counterI);
                  var caCounter = 0;
                  for (c in counterArray) {
                    var aCounter = automationsSelectList[counterI].actions.length;
                    var alCounter = automationsSelectList[counterI].conditions.all.length;
                    var anCounter = automationsSelectList[counterI].conditions.any.length;
                    if (counterArray[c] == counterI) {
                      caCounter++;
                      if (caCounter == aCounter + alCounter + anCounter) {
                        doCreateAutomations(automationsSelectList[counterI]);
                      }
                    }
                  }
                  // processCounter++;
                  // if (processCounter == 3) {
                  //   doCreateAutomations(automationsSelectList[counterI]);
                  // }
                }

                if (automationsSelectList[i].conditions.all.length > 0) {
                  counterArray.push(counterI);
                  var caCounter = 0;
                  for (c in counterArray) {
                    var aCounter = automationsSelectList[counterI].actions.length;
                    var alCounter = automationsSelectList[counterI].conditions.all.length;
                    var anCounter = automationsSelectList[counterI].conditions.any.length;
                    if (counterArray[c] == counterI) {
                      caCounter++;
                      if (caCounter == aCounter + alCounter + anCounter) {
                        doCreateAutomations(automationsSelectList[counterI]);
                      }
                    }
                  }
                  // processCounter++;
                  // if (processCounter == 3) {
                  //   doCreateAutomations(automationsSelectList[counterI]);
                  // }
                } else {
                  counterArray.push(counterI);
                  var caCounter = 0;
                  for (c in counterArray) {
                    var aCounter = automationsSelectList[counterI].actions.length;
                    var alCounter = automationsSelectList[counterI].conditions.all.length;
                    var anCounter = automationsSelectList[counterI].conditions.any.length;
                    if (counterArray[c] == counterI) {
                      caCounter++;
                      if (caCounter == aCounter + alCounter + anCounter) {
                        doCreateAutomations(automationsSelectList[counterI]);
                      }
                    }
                  }
                  // processCounter++;
                  // if (processCounter == 3) {
                  //   doCreateAutomations(automationsSelectList[counterI]);
                  // }
                }

                if (automationsSelectList[i].conditions.any.length > 0) {
                  counterArray.push(counterI);
                  var caCounter = 0;
                  for (c in counterArray) {
                    var aCounter = automationsSelectList[counterI].actions.length;
                    var alCounter = automationsSelectList[counterI].conditions.all.length;
                    var anCounter = automationsSelectList[counterI].conditions.any.length;
                    if (counterArray[c] == counterI) {
                      caCounter++;
                      if (caCounter == aCounter + alCounter + anCounter) {
                        doCreateAutomations(automationsSelectList[counterI]);
                      }
                    }
                  }
                  // processCounter++;
                  // if (processCounter == 3) {
                  //   doCreateAutomations(automationsSelectList[counterI]);
                  // }
                } else {
                  counterArray.push(counterI);
                  var caCounter = 0;
                  for (c in counterArray) {
                    var aCounter = automationsSelectList[counterI].actions.length;
                    var alCounter = automationsSelectList[counterI].conditions.all.length;
                    var anCounter = automationsSelectList[counterI].conditions.any.length;
                    if (counterArray[c] == counterI) {
                      caCounter++;
                      if (caCounter == aCounter + alCounter + anCounter) {
                        doCreateAutomations(automationsSelectList[counterI]);
                      }
                    }
                  }
                  // processCounter++;
                  // if (processCounter == 3) {
                  //   doCreateAutomations(automationsSelectList[counterI]);
                  // }
                }
              })(i);
            } else {
              console.log('automations exist');
            }
            // console.log(automationsSelectList[i]);
            // if (automationsSelectList[i].conditions.all.length > 0){
            //   (function(counterI){
            //     for (var cll=0; cll<automationsSelectList[i].conditions.all.length; cll++) {
            //       if (automationsSelectList[i].conditions.all[cll].field.includes('group_id')) {
            //         (function(counterJ){
            //           $.ajax(getGroups(automationsSelectList[i].conditions.all[cll].value)).then(
            //             function(groupData){
            //               if (groupData.group.name !== null) {
            //                 $.ajax(srcGroups_dest(groupData.group.name)).then(
            //                   function(srcGroupDataDest){
            //                     automationsSelectList[counterI].conditions.all[counterJ].value = srcGroupDataDest.results[0].id;
            //                   },
            //                   function(srcGroupDataDestError){
            //                     console.log('=== FAILED SEARCH GROUP ===');
            //                     console.log(srcGroupDataDestError);
            //                   });
            //               }
            //             },
            //             function(groupError){
            //               console.log('=== FAILED GET GROUP');
            //               console.log(groupError);
            //             });
            //         })(cll);
            //       }
            //     }
            //   })(i);
            // }
            // if (automationsSelectList[i].conditions.any.length > 0) {
            //   (function(counterI){
            //     for (var cll=0; cll<automationsSelectList[i].conditions.any.length; cll++) {
            //       if (automationsSelectList[i].conditions.any[cll].field.includes('group_id')) {
            //         (function(counterJ){
            //           $.ajax(getGroups(automationsSelectList[i].conditions.any[cll].value)).then(
            //             function(groupData){
            //               if (groupData.group.name !== null) {
            //                 $.ajax(srcGroups_dest(groupData.group.name)).then(
            //                   function(srcGroupDataDest){
            //                     automationsSelectList[counterI].conditions.any[counterJ].value = srcGroupDataDest.results[0].id;
            //                   },
            //                   function(srcGroupDataDestError){
            //                     console.log('=== FAILED SEARCH GROUP ===');
            //                     console.log(srcGroupDataDestError);
            //                   });
            //               }
            //             },
            //             function(groupError){
            //               console.log('=== FAILED GET GROUP');
            //               console.log(groupError);
            //             });
            //         })(cll);
            //       }
            //     }
            //   })(i);
            // }
            // if (automationsSelectList[i].actions.length > 0) {
            //   (function(counterI){
            //     var actionCounter = 0;
            //     for (var j=0; j<automationsSelectList[i].actions.length; j++) {
            //       (function(counterA){
            //         if (automationsSelectList[i].actions[j].field == 'notification_user') {
            //           if (isNumeric(automationsSelectList[i].actions[j].value[0])){
            //             (function(counterJ){
            //               $.ajax(getUsers(automationsSelectList[counterI].actions[j].value[0])).then(
            //                 function(usersData){
            //                   if (usersData.user.email !== null) {
            //                     $.ajax(srcUserByEmail_dest(usersData.user.email)).then(
            //                       function(srcUserData){
            //                         if (srcUserData.results.length > 0) {
            //                           actionCounter++;
            //                           console.log('notif_user: ' + counterA);
            //                           var userId = srcUserData.results[0].id;
            //                           automationsSelectList[counterI].actions[counterJ].value[0] = userId;
            //                           console.log('USER FOUND');
            //                           if (actionCounter == automationsSelectList[counterI].actions.length) {
            //                             console.log('AUTOMATIONS FINISH');
            //                             console.log(automationsSelectList[counterI]);
            //                           }
            //                         } else {
            //                           console.log('=== FAILED GET USER, USER DOESNT EXIST ===');
            //                         }
            //                       },
            //                       function(srcUserDataError){
            //                         console.log('=== FAILED SEARCH USERS ===');
            //                         console.log(srcUserDataError);
            //                       });
            //                   }
            //                 },
            //                 function(usersError){
            //                   console.log('=== FAILED GET USERS ===');
            //                   console.log(usersError);
            //                 });
            //             })(j);
            //           } else {
            //             actionCounter++;
            //             console.log('notif_user no id: ' + counterA);
            //             if (actionCounter == automationsSelectList[counterI].actions.length) {
            //               console.log('AUTOMATIONS NO USERS ID');
            //               console.log(automationsSelectList[counterI]);
            //             }
            //           }
            //         }
            //         if (automationsSelectList[i].actions[j].field == 'notification_group') {
            //           if (isNumeric(automationsSelectList[i].actions[j].value[0])){
            //             (function(counterJ){
            //               $.ajax(getGroups(automationsSelectList[counterI].actions[j].value[0])).then(
            //                 function(groupData){
            //                   if (groupData.group.name !== null) {
            //                     $.ajax(srcGroups_dest(groupData.group.name)).then(
            //                       function(srcGroupData){
            //                         if (srcGroupData.results.length > 0) {
            //                           actionCounter++;
            //                           console.log('notif_group: ' + counterA);
            //                           var userId = srcGroupData.results[0].id;
            //                           automationsSelectList[counterI].actions[counterJ].value[0] = userId;
            //                           console.log('GROUP FOUND');
            //                           if (actionCounter == automationsSelectList[counterI].actions.length) {
            //                             console.log('AUTOMATIONS FINISH');
            //                             console.log(automationsSelectList[counterI]);
            //                           }
            //                         } else {
            //                           console.log('=== FAILED GET GROUP, GROUP DOESNT EXIST ===');
            //                         }
            //                       },
            //                       function(srcGroupDataError){
            //                         console.log('=== FAILED SEARCH GROUP ===');
            //                         console.log(srcUserDataError);
            //                       });
            //                   }
            //                 },
            //                 function(groupDataError){
            //                   console.log('=== FAILED GET GROUP ===');
            //                   console.log(groupDataError);
            //                 });
            //             })(j);
            //           } else {
            //             actionCounter++;
            //             console.log('notif_group no id: ' + counterA);
            //             if (actionCounter == automationsSelectList[counterI].actions.length) {
            //               console.log('AUTOMATIONS NO GROUPS ID');
            //               console.log(automationsSelectList[counterI]);
            //             }
            //           }
            //         }
            //         if (automationsSelectList[i].actions[j].field.includes('custom_fields_')) {
            //           var ticketFieldsId = automationsSelectList[i].actions[j].field.split('_');
            //           (function(counterJ){
            //             $.ajax(getTicketFieldsbyId(ticketFieldsId[2])).then(
            //               function(ticketFieldsData){
            //                 for (var x=0; x<ticketFieldsDestData.ticket_fields.length; x++){
            //                   if (ticketFieldsData.ticket_field.title == ticketFieldsDestData.ticket_fields[x].title){
            //                     actionCounter++;
            //                     automationsSelectList[counterI].actions[counterJ].field = 'custom_fields_' + ticketFieldsDestData.ticket_fields[x].id;
            //                   }
            //                 }
            //                 if (actionCounter == automationsSelectList[counterI].actions.length) {
            //                   console.log('AUTOMATIONS FINISH');
            //                   console.log(automationsSelectList[counterI]);
            //                 }
            //               },
            //               function(ticketFieldsError){
            //                 console.log('=== FAILED GET TICKET FIELDS ===');
            //                 console.log(ticketFieldsError);
            //               });
            //           })(j);
            //         }
            //       })(j);
            //     }
            //   })(i);
            // } else {
            //   console.log('automations has no actions');
            // }
          }
        },
        function(automationsDestError){
          console.log('automationsDestError');
          console.log(automationsDestError);
        });

    },
    function(ticketFieldsDestError){
      console.log('===== FAILED GET TICKET DEST DATA =====');
      console.log(ticketFieldsDestError);
    });

}
</script>
@endsection
