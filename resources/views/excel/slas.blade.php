@extends('app')

@section('zendesk_config_name', 'SLAs')

@section('content')
{!! Session::get('zendesk_slas_excel')!!}
@endsection

@section('javascript')
<script>
// Mock
var slaSelectList = [
   {
      title: "General",
      description: "General",
      position: 1,
      filter: {
         all: [ ],
         any: [ ]
      },
      policy_metrics: [
         {
            priority: "normal",
            metric: "requester_wait_time",
            target: 1,
            business_hours: true
         },
         {
            priority: "high",
            metric: "requester_wait_time",
            target: 480,
            business_hours: true
         },
         {
            priority: "normal",
            metric: "next_reply_time",
            target: 1,
            business_hours: true
         },
         {
            priority: "high",
            metric: "next_reply_time",
            target: 120,
            business_hours: true
         },
         {
            priority: "normal",
            metric: "first_reply_time",
            target: 1,
            business_hours: true
         },
         {
            priority: "high",
            metric: "first_reply_time",
            target: 120,
            business_hours: true
         },
         {
            priority: "normal",
            metric: "agent_work_time",
            target: 1,
            business_hours: true
         },
         {
            priority: "high",
            metric: "agent_work_time",
            target: 480,
            business_hours: true
         }
      ],
   },
   {
      title: "Testing SLA",
      description: null,
      position: 4,
      filter: {
         all: [
            {
               field: "brand_id",
               operator: "is",
               value: 1208428
            },
            {
               field: "ticket_form_id",
               operator: "is",
               value: 645528
            },
            {
               field: "ticket_type_id",
               operator: "is",
               value: 1
            },
            {
               field: "group_id",
               operator: "is_not",
               value: 29580608
            },
            {
               field: "organization_id",
               operator: "is",
               value: 15370217648
            }
         ],
         any: [
            {
               field: "via_id",
               operator: "is",
               value: 0
            },
            {
               field: "exact_created_at",
               operator: "less_than",
               value: "2017-10-01 00:00"
            },
            {
               field: "ticket_fields_33198848",
               operator: "is",
               value: 55157568
            }
         ]
      },
      policy_metrics: [
         {
            priority: "low",
            metric: "first_reply_time",
            target: 60,
            business_hours: false
         },
         {
            priority: "normal",
            metric: "first_reply_time",
            target: 60,
            business_hours: false
         },
         {
            priority: "high",
            metric: "first_reply_time",
            target: 60,
            business_hours: false
         },
         {
            priority: "urgent",
            metric: "first_reply_time",
            target: 60,
            business_hours: false
         }
      ],
   }
]
/*=====SLA======*/
if (slaSelectList.length > 0) {
 $.ajax(getTicketFields_dest()).then(
    function(ticketFieldDestData){
      $.ajax(getAllBrandsDest()).then(
        function(brandsDest){
          $.ajax(getTicketForms_dest()).then(
            function(ticketFormsDest){
              var counterArray = [];
              for (var i=0; i<slaSelectList.length; i++) {
                var filterFinish = 0;
                (function(counterI){
                  if (slaSelectList[i].filter.all.length >0) {
                    var filterAllCounter = 0;
                    for (var f=0; f<slaSelectList[i].filter.all.length; f++) {
                      (function(counterF){
                        if (slaSelectList[i].filter.all[f].field.includes('ticket_fields_')) {
                          var ticketId = slaSelectList[i].filter.all[f].field.split('_');
                          $.ajax(getTicketFieldsbyId(ticketId[2])).then(
                            function(ticketFieldData){
                              $.ajax(getTicketFieldsbyIdOption(ticketFieldData.ticket_field.id)).then(
                                function(fieldOption){
                                  var optionString = '';
                                  for (var o=0; o<fieldOption.custom_field_options.length; o++) {
                                    if (fieldOption.custom_field_options[o].id == slaSelectList[counterI].filter.all[counterF].value) {
                                      optionString = fieldOption.custom_field_options[o].name;
                                    }
                                  }
                                  var ticketFieldFound = false;
                                  for (var d=0; d<ticketFieldDestData.ticket_fields.length; d++) {
                                    (function(counterD){
                                      if (ticketFieldData.ticket_field.title == ticketFieldDestData.ticket_fields[d].title) {
                                        ticketFieldFound = true;
                                        $.ajax(getTicketFieldsbyIdOptionDest(ticketFieldDestData.ticket_fields[d].id)).then(
                                          function(fieldOptionDest){
                                            for (var od=0; od<fieldOptionDest.custom_field_options.length; od++) {
                                              filterAllCounter++;
                                              if (fieldOptionDest.custom_field_options[od].name == optionString) {
                                                slaSelectList[counterI].filter.all[counterF].field = 'ticket_fields_' + ticketFieldDestData.ticket_fields[counterD].id;
                                                slaSelectList[counterI].filter.all[counterF].value = fieldOptionDest.custom_field_options[od].id;
                                                counterArray.push(counterI);
                                                var caCounter = 0;
                                                for (c in counterArray) {
                                                  var alCounter = slaSelectList[counterI].filter.all.length;
                                                  var anCounter = slaSelectList[counterI].filter.any.length;
                                                  if (counterArray[c] == counterI) {
                                                    caCounter++;
                                                    if (caCounter == alCounter + anCounter) {
                                                      doCreateSla(slaSelectList[counterI]);
                                                    }
                                                  }
                                                }
                                              }

                                              // if (filterAllCounter == slaSelectList[counterI].filter.all.length) {
                                              //   console.log('all filter has finish');
                                              //   console.log(slaSelectList[counterI]);
                                              //   filterFinish++;
                                              //   if (filterFinish == 2) {
                                              //     doCreateSla(slaSelectList[counterI]);
                                              //   }
                                              // }
                                            }
                                          },
                                          function(fieldOptionDestError){
                                            console.log('===== fieldOptionDestError =====');
                                            console.log(fieldOptionDestError);
                                          });
                                      }
                                    })(d);
                                  }
                                  if (!ticketFieldFound) {
                                    console.log('===== ticket field doest exist =====');
                                    updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some ticket fields not exist: ' + ticketFieldsData.ticket_field.title);
                                    // errorMigrate.push({
                                    //   name: 'SLA: ' + slaSelectList[counterI].title,
                                    //   error: 'ticket field doesnt exist: ' + ticketFieldData.ticket_field.title
                                    // });
                                  }
                                },
                                function(fieldOptionError){
                                  console.log('fieldOptionError');
                                  console.log(fieldOptionError);
                                });
                            },
                            function(ticketFieldsError){
                              console.log('===== ticketFieldsError =====');
                              console.log(ticketFieldsError);
                            });
                        } else if (slaSelectList[i].filter.all[f].field.includes('brand_id')) {
                          $.ajax(getBrands(slaSelectList[i].filter.all[f].value)).then(
                            function(brands){
                              var brandsIsFound = false;
                              for (var br=0; br<brandsDest.brands.length; br++) {
                                if (brands.brand.name == brandsDest.brands[br].name) {
                                  brandsIsFound = true;
                                  console.log('brands found');
                                  filterAllCounter++;
                                  slaSelectList[counterI].filter.all[counterF].value = brandsDest.brands[br].id;
                                  // if (filterAllCounter == slaSelectList[counterI].filter.all.length) {
                                  //   console.log('all filter has finish');
                                  //   console.log(slaSelectList[counterI]);
                                  //   filterFinish++;
                                  //   if (filterFinish == 2) {
                                  //     doCreateSla(slaSelectList[counterI]);
                                  //   }
                                  // }
                                  counterArray.push(counterI);
                                  var caCounter = 0;
                                  for (c in counterArray) {
                                    var alCounter = slaSelectList[counterI].filter.all.length;
                                    var anCounter = slaSelectList[counterI].filter.any.length;
                                    if (counterArray[c] == counterI) {
                                      caCounter++;
                                      if (caCounter == alCounter + anCounter) {
                                        doCreateSla(slaSelectList[counterI]);
                                      }
                                    }
                                  }
                                }
                              }
                              if (!brandsIsFound) {
                              console.log('===== brands not found =====');
                                updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some brands not exist: ' + brands.brand.name);
                                // errorMigrate.push({
                                //   name: 'SLA: ' + slaSelectList[counterI].title,
                                //   error: 'brands doesnt exist: ' + brands.brand.name
                                // });
                              }
                            },
                            function(brandsError){
                              console.log('===== brandsError =====');
                              console.log(brandsError);
                            });
                        } else if (slaSelectList[i].filter.all[f].field.includes('ticket_form_id')) {
                          $.ajax(getTicketFormsById(slaSelectList[i].filter.all[f].value)).then(
                            function(ticketForm){
                              var ticketFormFound = false;
                              for (var tf=0; tf<ticketFormsDest.ticket_forms.length; tf++){
                                if (ticketForm.ticket_form.name == ticketFormsDest.ticket_forms[tf].name) {
                                  ticketFormFound = true;
                                  console.log('===== ticket forms found =====');
                                  filterAllCounter++;
                                  slaSelectList[counterI].filter.all[counterF].value = ticketFormsDest.ticket_forms[tf].id;
                                  // if (filterAllCounter == slaSelectList[counterI].filter.all.length) {
                                  //   console.log('all filter has finish');
                                  //   console.log(slaSelectList[counterI]);
                                  //   filterFinish++;
                                  //   if (filterFinish == 2) {
                                  //     doCreateSla(slaSelectList[counterI]);
                                  //   }
                                  // }
                                  counterArray.push(counterI);
                                  var caCounter = 0;
                                  for (c in counterArray) {
                                    var alCounter = slaSelectList[counterI].filter.all.length;
                                    var anCounter = slaSelectList[counterI].filter.any.length;
                                    if (counterArray[c] == counterI) {
                                      caCounter++;
                                      if (caCounter == alCounter + anCounter) {
                                        doCreateSla(slaSelectList[counterI]);
                                      }
                                    }
                                  }
                                }
                              }
                              if (!ticketFormFound) {
                                console.log('===== ticket form not found =====');
                                updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some ticket form not exist: ' + ticketForm.ticket_form.name);
                                // errorMigrate.push({
                                //   name: 'SLA: ' + slaSelectList[counterI].title,
                                //   error: 'ticket form doesnt exist: ' + ticketForm.ticket_form.name
                                // });
                              }
                            },
                            function(ticketFormError){
                              console.log('===== ticketFormError =====');
                              console.log(ticketFormError);
                            });
                        } else if (slaSelectList[i].filter.all[f].field.includes('group_id')) {
                          $.ajax(getGroups(slaSelectList[i].filter.all[f].value)).then(
                            function(groups){
                              $.ajax(srcGroups_dest(groups.group.name)).then(
                                function(srcGroup){
                                  if (srcGroup.results.length > 0) {
                                    console.log('===== group found =====');
                                    filterAllCounter++;
                                    slaSelectList[counterI].filter.all[counterF].value = srcGroup.results[0].id;
                                    // if (filterAllCounter == slaSelectList[counterI].filter.all.length) {
                                    //   console.log('all filter has finish');
                                    //   console.log(slaSelectList[counterI]);
                                    //   filterFinish++;
                                    //   if (filterFinish == 2) {
                                    //     doCreateSla(slaSelectList[counterI]);
                                    //   }
                                    // }
                                    counterArray.push(counterI);
                                    var caCounter = 0;
                                    for (c in counterArray) {
                                      var alCounter = slaSelectList[counterI].filter.all.length;
                                      var anCounter = slaSelectList[counterI].filter.any.length;
                                      if (counterArray[c] == counterI) {
                                        caCounter++;
                                        if (caCounter == alCounter + anCounter) {
                                          doCreateSla(slaSelectList[counterI]);
                                        }
                                      }
                                    }
                                  } else {
                                    console.log('===== group not found =====');
                                    updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some groups not exist: ' + groups.group.name);
                                    // errorMigrate.push({
                                    //   name: 'SLA: ' + slaSelectList[counterI].title,
                                    //   error: 'group doesnt exist: ' + groups.group.name
                                    // });
                                  }
                                },
                                function(srcGroupError){
                                  console.log('===== srcGroupError =====');
                                  console.log(srcGroupError);
                                });
                            },
                            function(groupsError){
                              console.log('===== groupsError =====');
                              console.log(groupsError);
                            });
                        } else if (slaSelectList[i].filter.all[f].field.includes('organization_id')) {
                          $.ajax(getOrganizationsById(slaSelectList[i].filter.all[f].value)).then(
                            function(org){
                              $.ajax(srcOrganizationDest(org.organization.name)).then(
                                function(orgDest){
                                  if (orgDest.results.length > 0) {
                                    console.log('===== organization found =====');
                                    slaSelectList[counterI].filter.all[counterF].value = orgDest.results[0].id;
                                    filterAllCounter++;
                                    // if (filterAllCounter == slaSelectList[counterI].filter.all.length) {
                                    //   console.log('all filter has finish');
                                    //   console.log(slaSelectList[counterI]);
                                    //   filterFinish++;
                                    //   if (filterFinish == 2) {
                                    //     doCreateSla(slaSelectList[counterI]);
                                    //   }
                                    // }
                                    counterArray.push(counterI);
                                    var caCounter = 0;
                                    for (c in counterArray) {
                                      var alCounter = slaSelectList[counterI].filter.all.length;
                                      var anCounter = slaSelectList[counterI].filter.any.length;
                                      if (counterArray[c] == counterI) {
                                        caCounter++;
                                        if (caCounter == alCounter + anCounter) {
                                          doCreateSla(slaSelectList[counterI]);
                                        }
                                      }
                                    }
                                  } else {
                                    console.log('===== organization not found =====');
                                    updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some organizations not exist: ' + org.organization.name);
                                    // errorMigrate.push({
                                    //   name: 'SLA: ' + slaSelectList[counterI].title,
                                    //   error: 'organization doesnt exist: ' + org.organization.name
                                    // });
                                  }
                                },
                                function(orgDestError){
                                  console.log('===== orgDestError =====');
                                  console.log(orgDestError);
                                });
                            },
                            function(orgError){
                              console.log('===== orgError =====');
                              console.log(orgError);
                            });
                        } else {
                          counterArray.push(counterI);
                          var caCounter = 0;
                          for (c in counterArray) {
                            var alCounter = slaSelectList[counterI].filter.all.length;
                            var anCounter = slaSelectList[counterI].filter.any.length;
                            if (counterArray[c] == counterI) {
                              caCounter++;
                              if (caCounter == alCounter + anCounter) {
                                doCreateSla(slaSelectList[counterI]);
                              }
                            }
                          }
                        }
                      })(f);
                    }
                    // if (slaSelectList[i].filter.all)
                  } else {
                    console.log('filter all null');
                    filterFinish++;
                  }

                  if (slaSelectList[i].filter.any.length > 0) {
                    var filterAnyCounter = 0;
                    for (var f=0; f<slaSelectList[i].filter.any.length; f++) {
                      (function(counterF){
                        if (slaSelectList[i].filter.any[f].field.includes('ticket_fields_')) {
                          var ticketId = slaSelectList[i].filter.any[f].field.split('_');
                          $.ajax(getTicketFieldsbyId(ticketId[2])).then(
                            function(ticketFieldData){
                              $.ajax(getTicketFieldsbyIdOption(ticketFieldData.ticket_field.id)).then(
                                function(fieldOption){
                                  var optionString = '';
                                  for (var o=0; o<fieldOption.custom_field_options.length; o++) {
                                    if (fieldOption.custom_field_options[o].id == slaSelectList[counterI].filter.any[counterF].value) {
                                      optionString = fieldOption.custom_field_options[o].name;
                                    }
                                  }
                                  var ticketFieldFound = false;
                                  for (var d=0; d<ticketFieldDestData.ticket_fields.length; d++) {
                                    (function(counterD){
                                      if (ticketFieldData.ticket_field.title == ticketFieldDestData.ticket_fields[d].title) {
                                        ticketFieldFound = true;
                                        $.ajax(getTicketFieldsbyIdOptionDest(ticketFieldDestData.ticket_fields[d].id)).then(
                                          function(fieldOptionDest){
                                            for (var od=0; od<fieldOptionDest.custom_field_options.length; od++) {
                                              filterAnyCounter++;
                                              if (fieldOptionDest.custom_field_options[od].name == optionString) {
                                                slaSelectList[counterI].filter.any[counterF].field = 'ticket_fields_' + ticketFieldDestData.ticket_fields[counterD].id;
                                                slaSelectList[counterI].filter.any[counterF].value = fieldOptionDest.custom_field_options[od].id;
                                                counterArray.push(counterI);
                                                var caCounter = 0;
                                                console.log(counterArray);
                                                for (c in counterArray) {
                                                  var alCounter = slaSelectList[counterI].filter.all.length;
                                                  var anCounter = slaSelectList[counterI].filter.any.length;
                                                  if (counterArray[c] == counterI) {
                                                    caCounter++;
                                                    if (caCounter == alCounter + anCounter) {
                                                      doCreateSla(slaSelectList[counterI]);
                                                    }
                                                  }
                                                }
                                              }
                                              // if (filterAnyCounter == slaSelectList[counterI].filter.any.length) {
                                              //   console.log('any filter has finish');
                                              //   console.log(slaSelectList[counterI]);
                                              //   filterFinish++;
                                              //   if (filterFinish == 2) {
                                              //     doCreateSla(slaSelectList[counterI]);
                                              //   }
                                              // }
                                            }
                                          },
                                          function(fieldOptionDestError){
                                            console.log('===== fieldOptionDestError =====');
                                            console.log(fieldOptionDestError);
                                          });
                                      }
                                    })(d);
                                  }
                                  if (!ticketFieldFound) {
                                    console.log('===== ticket field doest exist =====');
                                    updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some ticket fields not exist: ' + ticketFieldData.ticket_field.title);
                                    // errorMigrate.push({
                                    //   name: 'SLA: ' + slaSelectList[counterI].title,
                                    //   error: 'ticket field doesnt exist: ' + ticketFieldData.ticket_field.title
                                    // });
                                  }
                                },
                                function(fieldOptionError){
                                  console.log('fieldOptionError');
                                  console.log(fieldOptionError);
                                });
                            },
                            function(ticketFieldsError){
                              console.log('===== ticketFieldsError =====');
                              console.log(ticketFieldsError);
                            });
                        } else if (slaSelectList[i].filter.any[f].field.includes('brand_id')) {
                          $.ajax(getBrands(slaSelectList[i].filter.any[f].value)).then(
                            function(brands){
                              var brandsIsFound = false;
                              for (var br=0; br<brandsDest.brands.length; br++) {
                                if (brands.brand.name == brandsDest.brands[br].name) {
                                  brandsIsFound = true;
                                  console.log('brands found');
                                  filterAnyCounter++;
                                  slaSelectList[counterI].filter.any[counterF].value = brandsDest.brands[br].id;
                                  // if (filterAnyCounter == slaSelectList[counterI].filter.any.length) {
                                  //   console.log('any filter has finish');
                                  //   console.log(slaSelectList[counterI]);
                                  //   filterFinish++;
                                  //   if (filterFinish == 2) {
                                  //     doCreateSla(slaSelectList[counterI]);
                                  //   }
                                  // }
                                  counterArray.push(counterI);
                                  var caCounter = 0;
                                  for (c in counterArray) {
                                    var alCounter = slaSelectList[counterI].filter.all.length;
                                    var anCounter = slaSelectList[counterI].filter.any.length;
                                    if (counterArray[c] == counterI) {
                                      caCounter++;
                                      if (caCounter == alCounter + anCounter) {
                                        doCreateSla(slaSelectList[counterI]);
                                      }
                                    }
                                  }
                                }
                              }
                              if (!brandsIsFound) {
                                console.log('===== brands not found =====');
                                updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some brands not exist: ' + brands.brand.name);
                                // errorMigrate.push({
                                //   name: 'SLA: ' + slaSelectList[counterI].title,
                                //   error: 'brands doesnt exist: ' + brands.brand.name
                                // });
                              }
                            },
                            function(brandsError){
                              console.log('===== brandsError =====');
                              console.log(brandsError);
                            });
                        } else if (slaSelectList[i].filter.any[f].field.includes('ticket_form_id')) {
                          $.ajax(getTicketFormsById(slaSelectList[i].filter.any[f].value)).then(
                            function(ticketForm){
                              var ticketFormFound = false;
                              for (var tf=0; tf<ticketFormsDest.ticket_forms.length; tf++){
                                if (ticketForm.ticket_form.name == ticketFormsDest.ticket_forms[tf].name) {
                                  ticketFormFound = true;
                                  console.log('===== ticket forms found =====');
                                  filterAnyCounter++;
                                  slaSelectList[counterI].filter.any[counterF].value = ticketFormsDest.ticket_forms[tf].id;
                                  // if (filterAnyCounter == slaSelectList[counterI].filter.any.length) {
                                  //   console.log('any filter has finish');
                                  //   console.log(slaSelectList[counterI]);
                                  //   filterFinish++;
                                  //   if (filterFinish == 2) {
                                  //     doCreateSla(slaSelectList[counterI]);
                                  //   }
                                  // }
                                  counterArray.push(counterI);
                                  var caCounter = 0;
                                  for (c in counterArray) {
                                    var alCounter = slaSelectList[counterI].filter.all.length;
                                    var anCounter = slaSelectList[counterI].filter.any.length;
                                    if (counterArray[c] == counterI) {
                                      caCounter++;
                                      if (caCounter == alCounter + anCounter) {
                                        doCreateSla(slaSelectList[counterI]);
                                      }
                                    }
                                  }
                                }
                              }
                              if (!ticketFormFound) {
                                console.log('===== ticket form not found =====');
                                updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some ticket form not exist: ' + ticketForm.ticket_form.name);
                                // errorMigrate.push({
                                //   name: 'SLA: ' + slaSelectList[counterI].title,
                                //   error: 'ticket form doesnt exist: ' + ticketForm.ticket_form.name
                                // });
                              }
                            },
                            function(ticketFormError){
                              console.log('===== ticketFormError =====');
                              console.log(ticketFormError);
                            });
                        } else if (slaSelectList[i].filter.any[f].field.includes('group_id')) {
                          $.ajax(getGroups(slaSelectList[i].filter.any[f].value)).then(
                            function(groups){
                              $.ajax(srcGroups_dest(groups.group.name)).then(
                                function(srcGroup){
                                  if (srcGroup.results.length > 0) {
                                    console.log('===== group found =====');
                                    filterAnyCounter++;
                                    slaSelectList[counterI].filter.any[counterF].value = srcGroup.results[0].id;
                                    // if (filterAnyCounter == slaSelectList[counterI].filter.any.length) {
                                    //   console.log('any filter has finish');
                                    //   console.log(slaSelectList[counterI]);
                                    //   filterFinish++;
                                    //   if (filterFinish == 2) {
                                    //     doCreateSla(slaSelectList[counterI]);
                                    //   }
                                    // }
                                    counterArray.push(counterI);
                                    var caCounter = 0;
                                    for (c in counterArray) {
                                      var alCounter = slaSelectList[counterI].filter.all.length;
                                      var anCounter = slaSelectList[counterI].filter.any.length;
                                      if (counterArray[c] == counterI) {
                                        caCounter++;
                                        if (caCounter == alCounter + anCounter) {
                                          doCreateSla(slaSelectList[counterI]);
                                        }
                                      }
                                    }
                                  } else {
                                    console.log('===== group not found =====');
                                    updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some group not exist: ' + groups.group.name);
                                    // errorMigrate.push({
                                    //   name: 'SLA: ' + slaSelectList[counterI].title,
                                    //   error: 'group doesnt exist: ' + groups.group.name
                                    // });
                                  }
                                },
                                function(srcGroupError){
                                  console.log('===== srcGroupError =====');
                                  console.log(srcGroupError);
                                });
                            },
                            function(groupsError){
                              console.log('===== groupsError =====');
                              console.log(groupsError);
                            });
                        } else if (slaSelectList[i].filter.any[f].field.includes('organization_id')) {
                          $.ajax(getOrganizationsById(slaSelectList[i].filter.any[f].value)).then(
                            function(org){
                              $.ajax(srcOrganizationDest(org.organization.name)).then(
                                function(orgDest){
                                  if (orgDest.results.length > 0) {
                                    console.log('===== organization found =====');
                                    slaSelectList[counterI].filter.any[counterF].value = orgDest.results[0].id;
                                    filterAnyCounter++;
                                    // if (filterAnyCounter == slaSelectList[counterI].filter.any.length) {
                                    //   console.log('any filter has finish');
                                    //   console.log(slaSelectList[counterI]);
                                    //   filterFinish++;
                                    //   if (filterFinish == 2) {
                                    //     doCreateSla(slaSelectList[counterI]);
                                    //   }
                                    // }
                                    counterArray.push(counterI);
                                    var caCounter = 0;
                                    for (c in counterArray) {
                                      var alCounter = slaSelectList[counterI].filter.all.length;
                                      var anCounter = slaSelectList[counterI].filter.any.length;
                                      if (counterArray[c] == counterI) {
                                        caCounter++;
                                        if (caCounter == alCounter + anCounter) {
                                          doCreateSla(slaSelectList[counterI]);
                                        }
                                      }
                                    }
                                  } else {
                                    console.log('===== organization not found =====');
                                    updateProgress('SLA', '<b>' + slaSelectList[counterI].title + '</b> Error. Some organizations not exist: ' + org.organization.name);
                                    // errorMigrate.push({
                                    //   name: 'SLA: ' + slaSelectList[counterI].title,
                                    //   error: 'organizations doesnt exist: ' + org.organization.name
                                    // });
                                  }
                                },
                                function(orgDestError){
                                  console.log('===== orgDestError =====');
                                  console.log(orgDestError);
                                });
                            },
                            function(orgError){
                              console.log('===== orgError =====');
                              console.log(orgError);
                            });
                        } else {
                          counterArray.push(counterI);
                          var caCounter = 0;
                          for (c in counterArray) {
                            var alCounter = slaSelectList[counterI].filter.all.length;
                            var anCounter = slaSelectList[counterI].filter.any.length;
                            if (counterArray[c] == counterI) {
                              caCounter++;
                              if (caCounter == alCounter + anCounter) {
                                doCreateSla(slaSelectList[counterI]);
                              }
                            }
                          }
                        }
                      })(f);
                    }
                  } else {
                    console.log('filter any null');
                    filterFinish++;
                  }
                })(i);
              }
            },
            function(ticketFormsDestError){
              console.log('===== ticketFormsDestError =====');
              console.log(ticketFormsDestError);
            });
        },
        function(brandsDestError){
          console.log('===== brandsDestError =====');
          console.log(brandsDestError);
        });
    },
    function(ticketFieldsDestError){
      console.log('===== ticketFieldsDestError =====');
      console.log(ticketFieldsDestError);
    });
}
</script>
@endsection
