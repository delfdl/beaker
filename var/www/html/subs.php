<!doctype html>
<html lang="en">

<head>

  <meta charset="utf-8" />
  <title>jQuery UI Effects - Animate demo</title>
  <link rel="stylesheet" href="/stylesheets/jquery-ui.css" />
  <script src="/javascripts/jquery-1.9.1.js"></script>
  <script src="/javascripts//jquery-ui.js"></script>

  <style>
    .toggler { width: 500px; height: 200px; position: relative; }
    #button { padding: .5em 1em; text-decoration: none; }
    #effect { width: 240px; height: 135px; padding: 0.4em; position: relative; background: #fff; }
    #effect h3 { margin: 0; padding: 0.4em; text-align: center; }
  </style>

  <script>
  $(function() {
    var state = true;
    $( "#button" ).click(function() {
      if ( state ) {
        $( "#effect" ).animate({
          backgroundColor: "#aa0000",
          color: "#fff",
          width: 500
        }, 1000 );
      } else {
        $( "#effect" ).animate({
          backgroundColor: "#fff",
          color: "#000",
          width: 240
        }, 1000 );
      }
      state = !state;
    });
  });
  </script>

</head>

<body>
<div class="toggler">
  <div id="effect" class="ui-widget-content ui-corner-all">
    <h3 class="ui-widget-header ui-corner-all">Animate</h3>
    <p>
     Random text goes here, no seriously
    </p>
  </div>
</div>


<a href="#" id="button" class="ui-state-default ui-corner-all">Toggle Effect</a>

</body>
</html>
