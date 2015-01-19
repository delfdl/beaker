<?php
     $stream = $_REQUEST["url"];
     // echo $stream;
     switch ($stream) {
        case 'white tigers':
         $url = 'aa-port1';
         break;
        case 'african lions':
         $url = 'aa-port2';
         break;
        case 'african wild dogs':
         $url = 'aa-port3';
         break;
        case 'meerkats':
         $url = 'aa-port5';
         break;
        case 'african mixed pole':
         $url = 'aa-port6';
         break;
        case 'african mixed hut':
         $url = 'aa-port7';
         break;
} 
?>
<html>
<head>
  <script src="/fdl/jwplayer.js"></script>
  <script>jwplayer.key="zYUzlLKZYyvvBzT13IPX1XDX5rCXrr4QzOkjBTtSdJA=";</script>
<link href="/css/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div class="contentTop">
      <span class="pageTitle"><span class="icon-screen"></span><?php echo $stream.'&nbsp;('.$url.')'; ?></span>
  </div>
<div id="fdlElement" style='border:1px solid silver'> Loading the player... </div>
<script type="text/javascript">
	jwplayer("fdlElement").setup({
    file: "rtmp://rtmp-qa1.projectapollo2.com:8935/livecams/<?php echo $url?>",
    image: "/fdl/fdl.jpg",
    height: 270,
    width: 480,
    autostart: true,
    displaytitle: true,
    rtmp: {
    bufferlength: 0.01
    }
	});
</script>
</body>
</html>
