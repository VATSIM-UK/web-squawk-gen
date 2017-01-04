<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <style type="text/css">
      #panelBox {
        max-width:75%;
        text-align: center;
        float: none;
        margin: 0 auto;
      }
      #inputs{
        text-align: center;
        max-width: 200px;
      }
      #icaoCode{
        text-transform: uppercase;
      }
      #form{
        display:inline-block;
        text-align:center;
      }
      .centralBox{
        padding: 30px;
      }
      .centralBox .title {
        margin-top: 0 !important;
      }
      .panel {
        margin-bottom: 20px;
        background-color: #fff;
        border: 1px solid transparent;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .05)
      }

      .panel-body {
          background-color: rgba(0, 0, 0, 0.1);
          padding: 15px
      }

      .panel-heading {
          padding: 10px 15px;
          border-bottom: 1px solid transparent;
          border-top-left-radius: 3px;
          border-top-right-radius: 3px
      }

      .panel-heading>.dropdown .dropdown-toggle,
      .panel-title {
          color: inherit
      }

      .panel-title {
          margin-top: 0;
          margin-bottom: 0;
          font-size: 16px
      }

      .panel-title>.small,
      .panel-title>.small>a,
      .panel-title>a,
      .panel-title>small,
      .panel-title>small>a {
          color: inherit
      }

      .panel-footer {
          padding: 10px 15px;
          background-color: #f5f5f5;
          border-top: 1px solid #ddd;
          border-bottom-right-radius: 3px;
          border-bottom-left-radius: 3px
      }

      .panel-ukblue {
          border: 0;
          border-top-left-radius: 0;
          border-top-right-radius: 0
      }

      .panel-ukblue>.panel-heading {
          background-color: #17375e;
          border-color: #17375e;
          color: #fff;
          border-top-left-radius: 0;
          border-top-right-radius: 0
      }
    </style>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript">

      $(document).ready(function(){
        $('#icaoCode').keypress(function(event){
        	var keycode = (event.keyCode ? event.keyCode : event.which);
          if(keycode == '13'){
            if($("#icaoCode").val().length == 4){
              generateSquawk();
            }else{
              $("#squawk").text("");
            }
        	}
        });
      });

      function generateSquawk(){
        $.get( "api/index.php", {destICAO: $("#icaoCode").val()}, function( data ) {
          var isnum = /^\d+$/.test(data);
          if(isnum){
            $("#squawk").text(data);
          }else{
            $("#squawk").text("");
          }
        });
      }
      function buttonClicked(){
        $("#icaoCode").val($('#icaoCode').val().toUpperCase());
        generateSquawk();
      }
    </script>
    <title>VATSIM-UK Squawk Code Allocator</title>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="text-center centralBox">
            <img src="https://vatsim.uk/system/view/images/logo.png" width="200px" />
            <h3 class="title">UK Squawk Code Allocator</h3>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div id="panelBox" class="panel panel-ukblue">
            <div class="panel-heading"><i class="glyphicon glyphicon-cog"></i>   Generate Squawk Code</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-8 col-xs-8">
                  <div class="row">
                    <div class="col-md-12">
                      <label for="icaoCode">Destination ICAO Code</label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <form id="form">
                        <div class="input-group" id="inputs">
                          <span class="input-group-addon" id="sizing-addon1"><span class="glyphicon glyphicon-plane"></span></span>
                          <input type="text" name="icaoCode" id="icaoCode" class="form-control" placeholder="e.g EHAM" aria-describedby="inputAddon" onkeyup="this.value=this.value.replace(/[^A-Za-z]/g,'');this.value = this.value.toUpperCase();">
                        </div>
                      </form>
                      </br>
                    </div>
                  </div>
                  <button class="btn btn-primary" onclick="buttonClicked()" type="button">Generate Squawk</button>
                </div>
                <div class="col-md-4 col-xs-4">
                    Squawk Code:
                    <h1 id="squawk"></h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </body>
</html>
