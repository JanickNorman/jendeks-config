  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
  <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
  <!------ Include the above in your HEAD tag ---------->


  <div class="container">
    <div class="row">
    <form class="form-horizontal" method="post" action="/chats/download">
      {!! csrf_field() !!}
      <fieldset>

      <!-- Form Name -->
      <legend>Chats</legend>

      <!-- Text input-->
      <div class="form-group">
        <label class="col-md-4 control-label" for="textinput">Token</label>  
        <div class="col-md-4">
          <input id="textinput" name="token" type="text" placeholder="{{ Session::get('chats_access_token') }}" class="form-control input-md">
          <a href="/chats/oauth">generate</a>
        </div>
      </div>

      <!-- Select Basic -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="selectbasic">Resource</label>
        <div class="col-md-4">
          <select id="resource" name="resource" class="form-control">
            <option value="chats">Chats</option>
            <option value="agent_timeline">Agent Timeline</option>
            <option value="agents">Agent</option>
          </select>
        </div>
      </div>

      <!-- Button (Double) -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="button1id"></label>
        <div class="col-md-8">
          <input type="submit" id="button1id" name="button1id" class="btn btn-primary">
          <!-- <button id="button2id" name="button2id" class="btn btn-danger">Scary Button</button> -->
        </div>
      </div>

    </fieldset>
  </form>

    </div>
  </div>