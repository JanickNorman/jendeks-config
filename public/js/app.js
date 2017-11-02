// Utility functions
function checkQue (counterArray, selectList, counterI) {
  counterArray.push(counterI);
  var caCounter = 0;
  var alCounter = selectList[counterI].conditions.all.length;
  var anCounter = selectList[counterI].conditions.any.length;
  allCounter = alCounter + anCounter;
  if (selectList[counterI].restriction !== null) {
    allCounter++;
  }
  for (c in counterArray) {
    if (counterArray[c] == counterI) {
      caCounter++;
      if (caCounter == allCounter) {
        doCreateViews(selectList[counterI]);
      }
    }
  }
}

function doCreateMacros (macrosCreate) {
  console.log('macrosCreate');
  console.log(macrosCreate);
  var newMacros = new Array ({macro: macrosCreate});
  $.ajax(createMacros(JSON.stringify(newMacros[0]))).then(
    function(create){
      console.log('Create success');
      console.log(create);
    },
    function(createError){
      console.log('===== createError =====');
      console.log(createError);
      updateProgress('Macros', '<b>' + macrosCreate.title + '</b> Error, Something wrong with the parameter..');
    });
}

function doCreateBrands (brandsCreate) {
  console.log(brandsCreate);
  var newBrands = new Array ({brand:brandsCreate});
  $.ajax(createBrands(JSON.stringify(newBrands[0]))).then(
    function(create){
      console.log('Create success');
      console.log(create);
    },
    function(createBrandsError){
      console.log('===== createViewError =====');
      console.log(createBrandsError);
      updateProgress('Brands', '<b>' + brandsCreate.name + '</b> Error, Something wrong with the parameter..');
    });
}

function doCreateSla(parameter) {
  console.log('Create SLA');
  console.log(parameter);
  var newSla = new Array({sla_policy: parameter});
  $.ajax(createSla_dest(JSON.stringify(newSla[0]))).then(
    function(create){
      console.log(create);
      console.log('Create SLA Success');
    },
    function(createSlaError){
      console.log('===== createSlaError =====');
      console.log(createSlaError);
    });
}

function doCreateViews(viewsCreate){
  console.log('Create View');
  console.log(viewsCreate);
  var newView = new Array ({view:viewsCreate});
  $.ajax(createViews(JSON.stringify(newView[0]))).then(
    function(create){
      console.log('Create success');
      console.log(create);
    },
    function(createViewError){
      console.log('===== createViewError =====');
      console.log(createViewError);
    });
}

function doGenerateGroupMembership (groupIds) {
  console.log(groupIds);
  for (var i=0; i<groupIds.length; i++) {
    (function(counterI){
      $.ajax(getGroupMembership(groupIds[i].oldGroup)).then(
        function(membershipData){
          var createMembership = [];
          if (membershipData.group_memberships.length > 0) {
            for (var j=0; j<membershipData.group_memberships.length; j++) {
              (function(counterJ){
                $.ajax(getUsers(membershipData.group_memberships[j].user_id)).then(
                  function(userData){
                    console.log(userData);
                    if (userData.user.email !== null) {
                      $.ajax(srcUserByEmail_dest(userData.user.email)).then(
                        function(userDestData){
                          if (userDestData.results.length > 0) {
                           console.log('===== user exist =====');
                           membershipsList.push({
                              user_id: userDestData.results[0].id,
                              group_id: groupIds[counterI].groupid
                           });
                           console.log(membershipsList);
                          } else {
                           console.log('=====user doesnt exist=====');
                           console.log(membershipsList);
                          }
                          if (counterI == groupIds.length-1) {
                           console.log('=====group has finish=====');
                           console.log(membershipsList);
                           doCreateMemberships(membershipsList);
                          }
                        },
                        function(userDestEror){
                          console.log('=====error search users======');
                          console.log(userDestEror);
                        });
                    }
                  },
                  function(userError){
                    console.log('=====error get users=====');
                    console.log(userError);
                  });
              })(j);
            }
          } else {
            console.log('=====group has no member=====');
            console.log(groupSelectList[counterI]);
          }
        },
        function(membershipError){
          console.log('=====ERROR GET MEMBERSHIPS=====');
          console.log(membershipError);
        });
    })(i);
  }
}

function doCreateMemberships (memberships) {
  var newMember = new Array({group_memberships: memberships});
  console.log(newMember);
  console.log('create memberships');
  console.log(JSON.stringify(newMember[0]));
  $.ajax(createGroupMembership_dest(JSON.stringify(newMember[0]))).then(
    function(createMembershipsData){
      console.log(createMembershipsData);
    },
    function(createMembershipsError){
      console.log('createMembershipsError');
      console.log(createMembershipsError);
    });
}

function doCreateAutomations (parameter) {
  var newAutoms = new Array({automation:parameter});
  console.log('CREATING AUTOMATIONS');
  console.log(parameter);
  $.ajax(createAutomations(JSON.stringify(newAutoms[0]))).then(
    function(createAutomationsData){
      console.log(createAutomationsData);
    },
    function(createAutomationsError){
      console.log('===== createAutomationsError =====');
      console.log(createAutomationsError);
    });
}

// function doCreateTicketForm (newTicketIds, ticketCount) {
//   /*DO_CREATE_TICKET_FORM*/
//   ticketFormsSelectList[ticketCount].ticket_field_ids = newTicketIds;
//   var ticketForms = new Array({ticket_form:ticketFormsSelectList[ticketCount]});
//   $.ajax({
//     url: ZD_DOMAIN + '/api/v2/ticket_forms.json',
//     type: 'POST',
//     headers: {
//       "Authorization": ZD_TOKEN
//     },
//     dataType : "json",
//     data: JSON.stringify(ticketForms[0]),
//     contentType: "application/json; charset=utf-8",
//     async: false,
//     cors: true,
//     success: function(data) {
//       console.log(data);
//       if (ticketCount == (ticketFormsSelectList.length-1)) {
//         ticketFormsSelectList = [];
//         $('#ticketFormsCounter').text(ticketFormsSelectList.length);
//         console.log(errorMigrate);
//       }
//     },
//     error: function (errors) {
//       console.log(errors);
//       if (errors.responseJSON === undefined) {
//         errorMigrate.push({
//           name: ticketFormsSelectList[ticketCount].title,
//           errors: errors.statusText
//         });
//       } else {
//         errorMigrate.push({
//           name: ticketFormsSelectList[ticketCount].title,
//           errors: errors.responseJSON.details.base[0].description
//         });
//       }
//       if (ticketCount == (ticketFormsSelectList.length-1)) {
//         ticketFormsSelectList = [];
//         $('#ticketFormsCounter').text(ticketFormsSelectList.length);
//         console.log(errorMigrate);
//       }
//     }
//   });
// }

function showResult(migrateCounter, errorMigrate) {
  $('#myModalResult').modal('show');
}

function updateProgress (type, message) {
  var error = '';
  error += '<tr><td>' + type + '</td><td>' + message + '</td><tr>';
  $('.bodyMessage').append(error);
}

function deleteAllTicketFields () {
  $.ajax(getTicketFields()).then(
    function (data){
      console.log(data);
      for (var i=0; i<data.ticket_fields.length; i++) {
        if (data.ticket_fields[i].removable) {
          if (data.ticket_fields[i].title != 'Type') {
            if (data.ticket_fields[i].title != 'Priority') {
              console.log(data.ticket_fields[i].title);
              $.ajax(deleteTicketFields(data.ticket_fields[i].id)).then(
                function (deleteData){
                  console.log(deleteData);
                },
                function(errorDeleteData){
                  console.log(errorDeleteData);
                });
            }
          }
        }
      }
    },
    function (errorData){
      console.log(errorData);
    });
}

function doLoading (type, message) {

  /*CHECK IF MODAL SHOWN OR HIDDEN*/
  if ($('#modal_loading').hasClass('in')) {
    $('.spanLoadMsg').text('Processing:  ' + message);
    $('.spanLoadType').text(type);
  } else {
    $('#modal_loading').modal('show');
  }

}

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}


// Start Migrate
membershipsList = [];
var migrateCounter = 0;
errorMigrate = [];

/*=============API PART============*/

function customs(input) {
  var getTickets = {
    url: input,
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

/*USERS PART*/
function getUsers(input) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/users/' + input + '.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getAllUsers(input) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/users.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function srcUserByEmail_dest(input) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/search.json?query=type:user%20email:' + input,
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}
/*USERS PART*/

function getTriggers (input) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/triggers.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getBrands (id) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/brands/' + id + '.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getAllBrands (id) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/brands.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getAllBrandsDest (id) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/brands.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true
  }
  console.log(getTickets);
  return getTickets;
}

function createBrands (parameter) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/brands.json',
    type: 'POST',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    data: parameter,
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true
  }
  console.log(getTickets);
  return getTickets;
}

function getOrganizationsById (id) {

  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/organizations/' + id + '.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function srcOrganizationDest (input) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/search.json?query=type:organization%20name:' + input,
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}

function getSharingAgreement (id) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/sharing_agreements/' + id + '.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getAllSharingAgreementDest (input) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/sharing_agreements.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}

/*TICKET FIELDS PART*/
function getTicketFields (input) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/ticket_fields.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getTicketFields_dest (input) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/ticket_fields.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true
  }
  console.log(getTickets);
  return getTickets;
}

function deleteTicketFields (id) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/ticket_fields/' + id + '.json',
    type: 'DELETE',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getTicketFieldsbyId (id) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/ticket_fields/' + id + '.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getTicketFieldsbyIdOption (id) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/ticket_fields/' + id + '/options.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getTicketFieldsbyIdOptionDest (id) {
  var getTickets = {
    url:  "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/ticket_fields/' + id + '/options.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}

function createTicketFields (input, i) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/ticket_fields.json',
    type: 'POST',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    data: input,
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true,
    success: function(data) {
      console.log(i);
      console.log(data);
    }
  }
  console.log(getTickets);
  return getTickets;
}
/*TICKET FIELDS PART*/

/*TICKET FORMS PART*/
function getTicketForms (input) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/ticket_forms.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getTicketFormsById (input) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/ticket_forms/' + input + '.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getTicketForms_dest (input) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/ticket_forms.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true
  }
  console.log(getTickets);
  return getTickets;
}

function createTicketForms (input) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/ticket_forms.json',
    type: 'POST',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    data: input,
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true,
    success: function(data) {
      console.log(i);
      console.log(data);
    }
  }
  console.log(getTickets);
  return getTickets;
}
/*TICKET FORMS PART*/

/*AUTOMATIONS PART*/
function getAutomations () {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/automations.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getAutomations_dest () {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/automations.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true
  }
  console.log(getTickets);
  return getTickets;
}

function createAutomations (input) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/automations.json',
    type: 'POST',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    data: input,
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true
  }
  console.log(getTickets);
  return getTickets;
}
/*AUTOMATIONS PART*/

/*GROUP PART*/
function getAllGroup () {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/groups.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getGroups (id) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/groups/' + id + '.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getGroupMembership (id) {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/groups/' + id + '/memberships.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function srcGroups_dest (input) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/search.json?query=type:group%20name:' + input,
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}

function createGroup_dest (parameter) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/groups.json',
    type: 'POST',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    data: parameter,
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}

function createGroupMembership_dest (parameter) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/group_memberships/create_many.json',
    type: 'POST',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    data: parameter,
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}
/*GROUP PART*/

/*SLA PART*/
function getSla () {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/slas/policies.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function srcSla_dest () {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/slas/policies.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}

function createSla_dest (parameter) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/slas/policies.json',
    type: 'POST',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    data: parameter,
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}
/*SLA PART*/

/*VIEWS PART*/
function getViews () {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/views.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getViewsDest () {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/views.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}

function createViews (parameter) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/views.json',
    type: 'POST',
    data: parameter,
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}
/*VIEWS PART*/

/*MACROS PART*/
function getMacros () {
  var getTickets = {
    url: 'https://' + zendesk_source_subdomain + '.zendesk.com' + '/api/v2/macros.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_source_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
  }
  console.log(getTickets);
  return getTickets;
}

function getMacrosDest () {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/macros.json',
    type: 'GET',
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}

function createMacros (parameter) {
  var getTickets = {
    url: "https://" + zendesk_destination_subdomain + ".zendesk.com" + '/api/v2/macros.json',
    type: 'POST',
    data: parameter,
    headers: {
      "Authorization": "basic " + zendesk_destination_token
    },
    dataType : "json",
    contentType: "application/json; charset=utf-8",
    async: false,
    cors: true,
  }
  console.log(getTickets);
  return getTickets;
}
/*MACROS PART*/
