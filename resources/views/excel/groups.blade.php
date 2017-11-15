@extends('app')

@section('zendesk_config_name', 'Groups')

@section('content')
{!! Session::get('zendesk_groups_excel')!!}
@endsection

@section('javascript')
<script>
var groupSelectList = [];
/*=====GROUPS=====*/
    if (groupSelectList.length > 0) {
      var groupIds = [];
      var groupCounter = 0;
      for (var i=0; i<groupSelectList.length; i++) {
        (function(counterI){
          $.ajax(srcGroups_dest(groupSelectList[i].name)).then(
            function(srcGroupData){
             // console.log(srcGroupData);
             var isGroupExist = false;
             var idGroup = '';
             if (srcGroupData.results.length > 0) {
                isGroupExist =  true;
                idGroup = srcGroupData.results[0].id;
                groupIds.push({
                  index: counterI,
                  groupid: idGroup,
                  oldGroup: groupSelectList[counterI].id
                });
                groupCounter++;
                if (groupCounter == groupSelectList.length) {
                  doGenerateGroupMembership(groupIds);
                }
             } else {
                console.log('groups is not exist');
                var createGroup = new Array({group:groupSelectList[counterI]});
                $.ajax(createGroup_dest(JSON.stringify(createGroup[0]))).then(
                  function(createGroupData){
                    groupCounter++;
                    idGroup = createGroupData.group.id;
                    groupIds.push({
                      index: counterI,
                      groupid: idGroup,
                      oldGroup: groupSelectList[counterI].id
                    });
                    if (groupCounter == groupSelectList.length) {
                      doGenerateGroupMembership(groupIds);
                    }
                  },
                  function(createGroupError){
                    console.log('=====failed create group=====');
                    updateProgress('Group', 'Error when creating group: ' + groupSelectList[counterI].name);
                    // console.log(createGroupError);
                    // errorMigrate.push({
                    //   name: groupSelectList[counterI],
                    //   error: createGroupError,
                    //   type: 'group'
                    // });
                  });
                isGroupExist = false;
             }
            },
            function(srcGroupError){
             console.log('=====error search groups=====');
             console.log(srcGroupError);
            });
        })(i);
      }
    }
</script>
@endsection
