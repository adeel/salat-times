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
      
      .day_selection #date_choose_day {display: block;}
      .day_selection #date_choose_month {display: none;}
      .day_selection a#do_date_by_day {color: #777;
        text-decoration: none;}
      .day_selection a#do_date_by_day:hover {cursor: text;}
      
      .month_selection #date_choose_day {display: none;}
      .month_selection #date_choose_month {display: block;}
      .month_selection a#do_date_by_month {color: #777;
        text-decoration: none;}
      .month_selection a#do_date_by_month:hover {cursor: text;}
      
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
      <h1>Salat Times</h1>
<?php
require_once 'common.php';
?>
<?php if (isset($_POST['date'])): ?>
<?php
  require_once 'salat_clock.php';
  require_once 'qibla.php';
  
  function view_times_for_day($date, $location, $dst) {
    $coords = get_coords($location);
    
    if (!$coords) {
      return array("error"=>"We could not locate you.");
    }
    
    list($loc, $lat, $lon) = $coords;
    
    $gmt_offset = get_gmt_offset_for_location($lat, $lon, $dst);

    $time = strtotime($date);
    if (!strtotime($date)) {
      return array('error'=>"Invalid date.");
    }

    $date = new NinjaDate($date, $gmt_offset);

    $clock = new SalatClock($date, $loc, $gmt_offset);
    
    $qibla_locator = new QiblaLocator($lat, $lon);
    $qibla = $qibla_locator->locate();
    
    return array('location'=>$loc, 'clock'=>$clock, 'qibla'=>$qibla);
  }
  
  function view_times_for_month($month, $year, $location, $dst) {
    if (!preg_match("/\d{4}/", $year)) {
      return array('error'=>"Invalid year.");
    }
    
    if (!is_numeric($month) or !in_array($month, range(1, 12))) {
      return array('error'=>"Invalid month.");
    }
    
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
    
    $times_for_month = array();
    $d = new NinjaDate(time(), $gmt_offset);
    $days_in_month = $d->format('t');
    for ($day=1; $day<=$days_in_month; $day++) {
      $date = array($year, $month, $day);
      $clock = new SalatClock($date, $loc, $gmt_offset);

      $times_for_month[$day] = $clock;
    }
    
    $qibla_locator = new QiblaLocator($lat, $lon);
    $qibla = $qibla_locator->locate();
    
    return array('times_for_month'=>$times_for_month, 'location'=>$loc,
      'qibla'=>$qibla);
  }
  
  $date_type = $_POST['date_selection_type'];
  if ($date_type == 'day') {
    $data = view_times_for_day($_POST['date'], $_POST['location'],
      ($_POST['dst'] == 'on'));
    if (isset($data['error'])) {
?>
  <p><b><?php print $data['error']; ?></b></p>
<?php
    } else {
?>
  <h2><?php print $data['location']['address']; ?></h2>
  <h3><?php print $data['clock']->date->format('j M Y'); ?></h3>
  <p>
    <b>Fajr:</b>
    <?php print $data['clock']->times[0]->format('g:ia'); ?>
  </p>
  <p>
    <b>Shuruq (sunrise):</b>
    <?php print $data['clock']->times[1]->format('g:ia'); ?>
  </p>
  <p>
    <b>Dhuhr:</b>
    <?php print $data['clock']->times[2]->format('g:ia'); ?>
  </p>
  <p>
    <b>Asr:</b>
    <?php print $data['clock']->times[3]->format('g:ia'); ?>
  </p>
  <p>
    <b>Maghrib:</b>
    <?php print $data['clock']->times[4]->format('g:ia'); ?>
  </p>
  <p>
    <b>Isha:</b>
    <?php print $data['clock']->times[5]->format('g:ia'); ?>
  </p>
  <p>
    <b>Qibla direction:</b>
    <?php print round($data['qibla'], 1); ?>&deg; N
  </p>
  <p class="no_print">
    <a href="./index.php">return</a>
  </p>
<?php
    }
  } else {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $data = view_times_for_month($month, $year, $_POST['location'],
      ($_POST['dst'] == 'on'));
    if (isset($data['error'])) {
?>
  <p><b><?php print $data['error']; ?></b></p>
<?php
    } else {
?>
  <h2><?php print $data['location']['address']; ?></h2>
  <h3><?php print date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?></h3>
  <table id="times">
    <tr>
      <th>Day</th>
      <th>Fajr</th>
      <th>Sunrise</th>
      <th>Dhuhr</th>
      <th>Asr</th>
      <th>Maghrib</th>
      <th>Isha</th>
    </tr>
<?php foreach ($data['times_for_month'] as $day=>$clock): ?>
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
  <p>
    <b>Qibla direction:</b>
    <?php print round($data['qibla'], 1); ?>&deg; N.
  </p>
  <p class="no_print">
    <a href="./index.php">return</a>
  </p>
<?php
    }
  }
?>
<?php else: ?>
      <p><a href="./ramadan.php">Ramadan calendar</a></p>
      <form id="form" action="./index.php" method="post">
        <div id="date_selection_method">
          <p>
            <a href="#" id="do_date_by_day">Daily</a>
            or
            <a href="#" id="do_date_by_month">Monthly</a>
          </p>
        </div>
        <div id="date_choose_day">
          <div id="calendar"></div>
          <p>
            <label for="date">Date:</label>
            <input type="text" name="date"
              value="<?php print date('d M Y'); ?>" id="date" />
          </p>
        </div>
        <div id="date_choose_month">
          <p>
            <select name="month">
<?php
$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$n = date('n');
foreach ($months as $i=>$month):
?>
              <option value="<?php print $i+1; ?>"<?php if (($i+1) == $n): ?>
                selected="selected"<?php endif; ?>><?php print $month; ?>
              </option>
<?php endforeach; ?>
            </select>
<?php
$y = date('Y');
?>
            <select name="year">
              <option value="<?php print $y; ?>" selected="selected">
                <?php print $y; ?></option>
<?php for ($i=($y+1); $i<($y+5); $i++): ?>
              <option value="<?php print $i; ?>"><?php print $i; ?></option>
<?php endfor; ?>
            </select>
          </p>
        </div>
        <p>
          <label for="location">Location:</label>
          <input type="text" name="location" id="location"
            value="<?php print guess_location(); ?>" />
        </p>
        <p><small>(City or zip code.)</small></p>
        <p>
          <label for="dst">Daylight savings time:</label>
          <input type="checkbox" class="checkbox" name="dst" value="on"
            id="dst" />
        </p>
        <p><input type="submit" class="submit" value="Calculate" /></p>
      </form>
    </div>
    <script
      src="http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js"
      type="text/javascript" charset="utf-8"></script>
    <script src="timeframe.js" type="text/javascript"></script>
    <script type="text/javascript" charset="utf-8">
      //<![CDATA[
        new Timeframe('calendar', {months: 1, maxRange: 1, startField: 'date',
          endField: 'date', earliest: new Date(), format: '%d %b %Y'});
        
        $('container').addClassName('js_enabled');
        $('container').addClassName('day_selection')
                      .removeClassName('month_selection');
        
        $('do_date_by_day').observe('click', function() {
          $('container').addClassName('day_selection')
                        .removeClassName('month_selection');
        });
        
        $('do_date_by_month').observe('click', function() {
          $('container').addClassName('month_selection')
                        .removeClassName('day_selection');
        });
        
        $('form').observe('submit', function() {
          if ($('container').hasClassName('day_selection'))
            type = 'day';
          else
            type = 'month';
          $(this).insert(new Element('input', {'type': 'hidden',
            'name': 'date_selection_type', 'value': type}));
        });
      //]]>
    </script>
<?php endif; ?>

  <script language="javascript" src="http://www.islam.com/scriptd/footer1.js"></script>
  </body>
</html>