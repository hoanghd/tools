<html> 
  <body onload="parentIframeResize()"> 
    <script type="text/javascript"> 
      function parentIframeResize()
      {
		parent.parent.$Core.appsResizeIframe('<?php echo (isset($_GET['height']) ? $_GET['height'] : '500'); ?>');
      }     
    </script> 
  </body> 
</html>
