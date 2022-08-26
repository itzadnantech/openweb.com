<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>

  <body>
  <?php
$mapApiKey = 'AIzaSyClkODVF0A-Da9RnRx_j5M_uu8zuVb7Hts'; $mapWidth = '100%'; $mapHeight = '600px';
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
      
  </body>
</html>
