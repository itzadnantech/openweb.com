<div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
        <div class="page-title">
         <h3>MTN Fixed LTE Coverage Map</h3>
<p><strong> Dark Orange and Light Orange shade indicates coverage.</strong></p>
<?php
$mapApiKey = 'AIzaSyClkODVF0A-Da9RnRx_j5M_uu8zuVb7Hts'; $mapWidth = '100%'; $mapHeight = '600px';
?>
 
 <script type="text/javascript" id="mtn-fixed-lte-script">  
   (function() { var ax = document.createElement('script');
   ax.id = 'main-script';         
   ax.type = 'text/javascript';         
   ax.async = true;        
   ax.src = 'https://rcp.axxess.co.za/public/js/mtnFixedLteCoverageJs.php?key=<?php echo $mapApiKey;?>&width=<?php echo $mapWidth; ?>&height=<?php echo $mapHeight; ?>';    
   var s = document.getElementsByTagName('script')[0];       
   s.parentNode.insertBefore(ax, s);     
   })();    
   </script>
        </div>
    </div>
</div>
