<?php
$base_url = Config::get("general","hostname") . Config::get("general","subdir");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>The DataTank: SPECTQL end-point</title>
    <link rel="stylesheet" href="http://lib.thedatatank.com/bootstrap/css/bootstrap.min.css">  
    <link rel="stylesheet" href="http://datatank.demo.ibbt.be/installer/static/main.css"> 
    <script src="http://datatank.demo.ibbt.be/The-Semantifier/lib/jquery-1.7.1.min.js"></script>
    <link href="/lib/gcp/prettify.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="/lib/gcp/prettify.js"></script>
  </head>
  <body>
    <div id="masterhead" class="container">
      <div class="row">
	<div class="columns"><img src="http://datatank.demo.ibbt.be/installer/static/logo.png"/>
	</div>
      </div>
    </div>
    <div id="main">
      <div class="container">
    <label>Build your query:</label>
        <textarea name="query" style="width: 78%; height: 100px;" id="query">/TDTStats/Year/all/all/2012{month-,requests}</textarea>
        <select id="resources" style="width: 20%; height: 110px;" size="5">
        </select>
    <br/><label>History:</label>
        <select id="history" style="width: 99.5%; height: 110px;" size="5">
        </select>
	<input type="button" id="run" value="Run the Query!"/>
        <br/>
        <div id="uri"></div>
	<hr/>
    <label>Response:</label>
	<pre id="result" class="prettyprint">
	</pre>
      </div>
    </div>
    <footer>
      <div class="footer" align="center">
	&copy; 2011 <a href="http://npo.irail.be" target="_blank">iRail npo</a> - Powered by <a href="http://thedatatank.com" target="_blank">The DataTank</a>
      </div>
    </footer>

    <script>
      $('#run').click(function () {
          $('#history').prepend("<option value=\"" + $('#query').val() + "\">" + $('#query').val() +  "</option>");
          location.hash = "#!" + $('#query').val();
          var history_array = JSON.parse(localStorage.getItem('history'));
          if(!history_array)
              history_array = new Array();
          history_array[history_array.length] = $('#query').val();
          localStorage.setItem('history',JSON.stringify(history_array));
        $.ajax({
           headers: { 
               Accept : "text/csv"
           },
           url: "<?php echo $base_url ?>spectql" + $('#query').val(),
           dataType: "text",
           success: function(data) {
                 $('#result').text("");
                 $('#result').text(data.toString());
                 prettyPrint();
           },
           error: function(jqXHR, textStatus, errorThrown){
                 $('#result').html("<font color=\"red\">" + errorThrown + "</font>");
           }
        });
      });

//load history
var history_array = JSON.parse(localStorage.getItem('history'));
if(!history_array)
    history_array = new Array();
history_array.reverse();
$.each(history_array,function(key,value){
    $('#history').append("<option value=\"" + value + "\">" + value +  "</option>");
});

//load hash into query when hash is given
if(location.hash){
    $("#query").val(location.hash.substr(2,location.hash.length-2));
}

      //Load options
      $.ajax({
        url: "<?php echo $base_url; ?>TDTInfo/Resources.json",
        success: function(data) {
           data = data["Resources"];
           $.each(data, function(package, resources){
             $.each(resources, function(resourcename, resource){
                var resourcename = package + "/" + resourcename;
                $('#resources').append("<option value=\"" + resourcename + "\">" + resourcename +  "</option>");
             });
           });
        }
      });

    $("#resources").dblclick(function(){
      $("#query").val($("#query").val() + $("#resources").val());
    });


    $("#history").dblclick(function(){
      $("#query").val($("#history").val());
        location.hash = "#!" + $("#history").val();
    });

    $("#query").keyup(function(){
      $("#uri").html("In programming code, use this URL to access your query:<br/><strong><?php echo $base_url; ?>spectql" + encodeURI($("#query").val())+ "</strong>" );
    });
    </script>
  </body>
</html>
