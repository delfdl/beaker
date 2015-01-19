<?php
  $watchmen    = 'sudo /etc/init.d/galactus restart';
  $galactus = 'sudo /etc/init.d/watchmen restart';

  echo ('Restarting galactus: ');
  $msg      = exec($galactus);
  echo ('done '.$msg.'<br />Restarting watchmen '.$msg.' | ');
  $msg      = exec($watchmen);
  echo ('done '.$msg.'<br />');

 ?>
