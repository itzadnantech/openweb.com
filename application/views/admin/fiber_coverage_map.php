<h3>Universal Fibre Coverage Map</h3>

<strong>Please enter the address where you would like to check coverage.  Once the pin has loaded, push search below the map.</strong><br/>
<?php
$mapApiKey = 'AIzaSyClkODVF0A-Da9RnRx_j5M_uu8zuVb7Hts'; $mapWidth = '1000px'; $mapHeight = '600px';
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
</script><br/>

<input type="submit" value="Search" class="btn btn-primary btn-sm" id="search-map-btn"/>

</form>

<div><br/>
<div class="provider-list">
    
</div>    
 <p style="color:green;" id="loading-msg"></p>   
</div>
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
                url: "/admin/api",
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
