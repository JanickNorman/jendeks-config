<html>
<head>
   <title>Config Migrator - @yield('zendesk_config_name')</title>
   <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
</head>
<body>
   <!-- Second navbar for categories -->
   <nav class="navbar navbar-default">
      <div class="container">
         <!-- Brand and toggle get grouped for better mobile display -->
         <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Jendeks Config Migrator</a>
         </div>

         <!-- Collect the nav links, forms, and other content for toggling -->
         <div class="collapse navbar-collapse" id="navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
               <li class="{{ url()->current() == route('excelTicketFields') ? 'active': ''}}"><a href="{{route('excelTicketFields')}}">Ticket Fields</a></li>
               <li class="{{ url()->current() == route('excelTicketForms') ? 'active': ''}}"><a href="{{route('excelTicketForms')}}">Ticket Forms</a></li>
               <li class="{{ url()->current() == route('excelTriggers') ? 'active': ''}}"><a href="{{route('excelTriggers')}}">Triggers</a></li>
               <li class="{{ url()->current() == route('excelAutomations') ? 'active': ''}}"><a href="{{route('excelAutomations')}}">Automations</a></li>
               <li class="{{ url()->current() == route('excelSLAs') ? 'active': ''}}"><a href="{{route('excelSLAs')}}">SLAs</a></li>
               <li class="{{ url()->current() == route('excelViews') ? 'active': ''}}"><a href="{{route('excelViews')}}">Views</a></li>
               <li class="{{ url()->current() == route('excelMacros') ? 'active': ''}}"><a href="{{route('excelMacros')}}">Macros</a></li>
               <li class="{{ url()->current() == route('excelGroups') ? 'active': ''}}"><a href="{{route('excelGroups')}}">Groups</a></li>
            </ul>
         </div><!-- /.navbar-collapse -->
      </div><!-- /.container -->
   </nav><!-- /.navbar -->

   <nav class="subheading">
      <div class="container">
         <h1>@yield('zendesk_config_name') Migrator</h1>
      </div>
   </nav>

   <div class="container">
      <div class="row">
         @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
         @endif
         @if(session()->has('success'))
             <div class="alert alert-success">
                 {!! session()->get('success') !!}
             </div>
         @endif
         <div class="col-md-6">
            <div>
               <h3>Zendesk Source</h3>
               @if (Session::has('zendesk_source_auth'))
                  Currently Logged In to <b>{{ Session::get('zendesk_source_auth.subdomain') }}.zendesk.com</b>
               @else
                  <em>Not Logged In</em>
               @endif
            </div>
            <div class="row">
               <form action="/login" method="post" autocomplete="on">
                  <div class="form-group col-md-12">
                     <label for="source_subdomain">Subdomain</label>
                     <input type="name" class="form-control" id="source_domain" name="source_subdomain" placeholder="Enter subdomain">
                  </div>
                  <div class="form-group col-md-6">
                     <label for="source_username">Username</label>
                     <input type="name" class="form-control" id="source_username" name="source_username" placeholder="Enter username">
                  </div>
                  <div class="form-group col-md-6">
                     <label for="source_password">Password</label>
                     <input type="password" class="form-control" id="source_password" name="source_password" placeholder="Password">
                  </div>
                  <div class="col-sm-2 pull-right">
                     <div class="form-group">
                        <button type="submit" class="btn btn-primary">Log In</button>
                     </div>
                  </div>
               </form>
               <div class="col-sm-9">
                  <form action="{!! url()->current() !!}/download" method="post">
                     <button name="mysubmitbutton" id="mysubmitbutton" type="submit" class="btn btn-secondary">
                        Download excel template
                     </button>
                  </form>
               </div>
            </div>
         </div>

         {{-- Zendesk Tujuan --}}
         <div class="col-md-6">
            <div>
               <h3>Zendesk Destination</h3>
               @if (Session::has('zendesk_destination_auth'))
                  Currently Logged In to <b>{{ Session::get('zendesk_destination_auth.subdomain') }}.zendesk.com</b>
               @else
                  <em>Not Logged In</em>
               @endif
            </div>
            <form class="row">
               <div class="form-group col-md-12">
                  <label for="exampleInputEmail1">Subdomain</label>
                  <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter subdomain">
               </div>
               <div class="form-group col-md-6">
                  <label for="exampleInputEmail1">Username</label>
                  <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
               </div>
               <div class="form-group col-md-6">
                  <label for="exampleInputPassword1">Password</label>
                  <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
               </div>
               <div class="pull-right">
                  <button type="submit" class="btn btn-primary">Log In</button>
               </div>
            </form>
         </div>
      </div>

      <div class="row">
         <div class="col-sm-12 text-center">
            <h2>Upload from template</h2>
            <form id="upload-excel" enctype="multipart/form-data" method="post" action="{!! url()->current() !!}/upload">
               <label class="custom-file">
                 <input type="file" id="resource-excel-file" name="resource-excel-file" class="custom-file-input">
                 <span class="custom-file-control"></span>
               </label>
               <label for="" >
                  <input type="submit" value="submit" id="submit" />
               </label>
            </form>
         </div>
      </div>

      <div class="row">
         <h4>@yield('zendesk_config_name') result</h4>
         @yield('content')
      </div>
   </div>

   {{-- Library scripts --}}
   <script type="text/javascript" src="{{URL::asset('js/jquery.js')}}"></script>
   <script type="text/javascript" src="{{URL::asset('js/bootstrap.min.js')}}"></script>
   <script type="text/javascript" src="{{URL::asset('js/dropzone.js')}}"></script>

   {{-- Client scripts --}}
   <script type="text/javascript">
      var zendesk_destination_subdomain = "treesdemo11496822632";
      var zendesk_destination_username = "eldien.hasmanto@treessolutions.com";
      var zendesk_destination_password = "";
      var zendesk_destination_token = "ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz"
      var zendesk_source_subdomain = "treesdemo1";
      var zendesk_source_username = "eldien.hasmanto@treessolutions.com";
      var zendesk_source_password = "";
      var zendesk_source_token = "ZWxkaWVuLmhhc21hbnRvQHRyZWVzc29sdXRpb25zLmNvbTpXM2xjb21lMTIz";
   </script>
   <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
   @yield('javascript')
</body>
</html>
