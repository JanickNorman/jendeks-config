<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<div class="container">    
<div class="col-lg-12 col-md-12">

                            <!--Section: Doc content-->
<section class="documentation">

    <!--Section: Intro-->
    <section id="introduction">

        <!--Title-->
        <h1 class="main-title">Tutorial</h1>

        <!--Description-->
        <p>Bootstrap stepper is a component that displays content as a process with defined by user milestones. Following steps are separated and connected by buttons.</p>

        <p>This is a great solution for a variety of registration forms, where you don't want to scare the user with loads of fields and questions.</p>

        <p>Stepper can be aligned vertically as well as horizontally.</p>

        <p>Examples of Bootstrap steps use:</p>

        <ul>
            <li>Registration form</li>    
            <li>Payment gateway</li>
            <li>Tutorial with steps</li>   
        </ul>


    </section>


    <div id="signupbox" style=" margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Chat Token Generator</div>
                
            </div>  
            <div class="panel-body" >
                <form method="post" action="{{ url('/chats/oauth') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">  
                            

                    <form  class="form-horizontal" method="post" >
                        <div id="div_id_location" class="form-group required">
                            <label for="id_location" class="control-label col-md-4  requiredField"> Client Id<span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md textinput textInput form-control" id="client_id" name="client_id" style="margin-bottom: 10px" type="text" />
                            </div> 
                        </div>
                        <div id="div_id_location" class="form-group required">
                            <label for="id_location" class="control-label col-md-4  requiredField"> Client Secret<span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md textinput textInput form-control" id="client_secret" name="client_secret" style="margin-bottom: 10px" type="text" />
                            </div> 
                        </div>
                        <div id="div_id_location" class="form-group required">
                            <label for="id_location" class="control-label col-md-4  requiredField"> Subdomain<span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md textinput textInput form-control" id="subdomain" name="subdomain" style="margin-bottom: 10px" type="text" />
                            </div> 
                        </div>
                        
                        <div class="form-group"> 
                            <div class="aab controls col-md-4"></div>
                            <div class="controls col-md-8 ">
                                <input type="submit" name="Generate" value="Generate" class="btn btn-primary btn btn-info" id="submit-id-signup" />
                            </div>
                        </div> 
                            
                    </form>

                </form>
            </div>
        </div>
    </div> 
</div>
    





</div>            