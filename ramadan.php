<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>Salat Times</title>
    <style type="text/css">
      @import url(style.css);
      
      @media print {
        .no_print {display:none;}
      }
    </style>
  </head>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-5173658-1");
pageTracker._trackPageview();
</script>


<body>

<font size="2" face="Arial"><script language="javascript" src="../scriptd/header2.js"></script></font>
<center>
<script type="text/javascript"><!--
google_ad_client = "pub-7803654475705139";
google_ad_width = 728;
google_ad_height = 15;
google_ad_format = "728x15_0ads_al_s";
google_ad_channel = "";
//-->
</script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script><br>
<tr>
<td>
<script type="text/javascript"><!--
google_ad_client = "pub-7803654475705139";
google_alternate_color = "FFFFFF";
google_ad_width = 728;
google_ad_height = 90;
google_ad_format = "728x90_as";
google_ad_type = "image";
google_ad_channel = "";
//-->
</script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</center>


    <div id="container">
      <h1>Ramadan <?php print gmdate('Y'); ?></h1>
<?php
require_once 'common.php';
?>
<?php if (isset($_POST['location'])): ?>
<?php
  require_once 'salat_clock.php';
  
  function view_times($location, $dst) {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    $coords = get_coords($location);
    
    if (!$coords) {
      return array("error"=>"We could not locate you.");
    }
    
    list($loc, $lat, $lon) = $coords;
    
    $gmt_offset = get_gmt_offset_for_location($lat, $lon, $dst);

    $data = file('ramadan.dat');
    foreach ($data as $line) {
      $date = explode("-", trim($line));
      $start = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
      if (time() - $start <= 60*60*24*30) {
        break;
      }
    }
  
    if (!$date) {
      die('Error (check ramadan.dat).');
    }

    $times = array();
    for ($i=0; $i<30; $i++) {
      $day = $date[2] + $i;
      $times[$day] = new SalatClock(array($date[0], $date[1], $day),
        $loc, $gmt_offset);
    }
    
    return array('times'=>$times, 'location'=>$loc);
  }
  
  $data = view_times($_POST['location'], ($_POST['dst'] == 'on'));
  if (isset($data['error'])):
?>
  <p><b><?php print $data['error']; ?></b></p>
<?php
  else:
?>
  <h2><?php print $data['location']['address']; ?></h2>
  <table id="times">
    <tr>
      <th></th>
      <th>Fajr</th>
      <th>Sunrise</th>
      <th>Dhuhr</th>
      <th>Asr</th>
      <th>Maghrib (Iftar)</th>
      <th>Isha'a</th>
    </tr>
<?php foreach ($data['times'] as $day=>$clock): ?>
    <tr<?php if ($day % 2 == 0): ?> class="zebra"<?php endif; ?>>
      <td><?php print $clock->date->format('j M'); ?></td>
      <td><?php print $clock->times[0]->format('g:ia'); ?></td>
      <td><?php print $clock->times[1]->format('g:ia'); ?></td>
      <td><?php print $clock->times[2]->format('g:ia'); ?></td>
      <td><?php print $clock->times[3]->format('g:ia'); ?></td>
      <td><?php print $clock->times[4]->format('g:ia'); ?></td>
      <td><?php print $clock->times[5]->format('g:ia'); ?></td>
    </tr>
<?php endforeach; ?>
  </table>
  <p class="no_print"><small>
    Ramadan dates are approximate and may vary in your locale.
  </small></p>
  <p class="no_print"><a href="./index.php">return</a></p>
<?php endif; ?>
<?php else: ?>
      <form id="form" action="" method="post">
        <p>
          <label for="location">Location:</label>
          <input type="text" name="location" id="location"
            value="<?php print guess_location(); ?>" />
        </p>
        <p><small>(City or zip code.)</small></p>
        <p>
          <label for="dst">Daylight savings time:</label>
          <input type="checkbox" class="checkbox" name="dst" value="on" id="dst" />
        </p>
        <p><input type="submit" class="submit" value="Calculate" /></p>
      </form>
    </div>
<?php endif; ?>
  <script language="javascript" src="http://www.islam.com/scriptd/footer1.js"></script>
  </body>
</html>