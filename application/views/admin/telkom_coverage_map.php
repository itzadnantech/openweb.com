<h3>Telkom LTE Coverage Map</h3>
<strong>Please enter the clients address to check if they have coverage on the Telkom LTE network. Dark blue and light blue means they have coverage. Grey means they do not.</strong>
<?php
$mapApiKey = 'AIzaSyClkODVF0A-Da9RnRx_j5M_uu8zuVb7Hts'; $mapWidth = '1000px'; $mapHeight = '600px';
?>

<script type="text/javascript" id="telkomscript">
  (function() {
   var ax = document.createElement('script');
   ax.id = 'mainscript';
   ax.type = 'text/javascript';
   ax.async = true;
   ax.src = 'https://rcp.axxess.co.za/public/js/telkomLteJs.php?key=<?php echo $mapApiKey;?>&width=<?php echo $mapWidth; ?>&height=<?php echo $mapHeight; ?>';
   var s = document.getElementsByTagName('script')[0];
   s.parentNode.insertBefore(ax, s);
  })();

 </script>