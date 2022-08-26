<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
  </head>
  <body>
  <?php
$mapApiKey = 'AIzaSyClkODVF0A-Da9RnRx_j5M_uu8zuVb7Hts'; $mapWidth = '100%'; $mapHeight = '600px';
?>
<form method="post" id="fiber_map_details">

<script type="text/javascript" id="fibrescript">
 (function() {
 var ax = document.createElement('script');
 ax.id = 'mainscript';
 ax.type = 'text/javascript';
 ax.async = true;
 ax.src = 'https://rcp.axxess.co.za/public/js/fibremapJs.php?key=<?php echo $mapApiKey;
 ?>&width=<?php echo $mapWidth; ?>&height=<?php echo $mapHeight; ?>';
 var s = document.getElementsByTagName('script')[0];
 s.parentNode.insertBefore(ax, s);

 })();
</script>

<br/>
<input type="submit" value="Search" class="btn btn-primary btn-sm" id="search-map-btn"/>
</form>
<br/>
<div class="provider-list">
    
</div>    
 <p style="color:green;" id="loading-msg"></p>

<script>
$(function(){
    $('#fiber_map_details').on('submit',function(){
        $('#loading-msg').text('Loading your request...')
          $('.provider-list').empty();
     event.preventDefault();
            var formData = {
                'latlan': $('input[name=latlong-input]').val(),
                 'address': $('input[name=address-input]').val() 
            };
           
            $.ajax({
                url: "/un_auth_pages/api",
                type: "post",
                data: formData,
                success: function(d) {
                    $('.provider-list').empty();
                      $('#loading-msg').text('')
                      $('#searchbtn').show();
                   $('.provider-list').append(d);
                } 
        });
       
    });
    
});
</script>
      
  </body>
</html>
