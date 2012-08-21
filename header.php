<?php
error_reporting(E_ALL);
if (isset($_REQUEST['gmt_offset'])) {
  require_once 'salat_clock.php';
  require_once 'common.php';
  
  $coords = get_coords();
  
  if (!$coords) {
    exit('<!--could not be located-->');
  }

  list($loc, $lat, $lon) = $coords;

  $gmt_offset = intval($_REQUEST['gmt_offset']);

  $today = new NinjaDate('today', $gmt_offset);
  $tomorrow = new NinjaDate('tomorrow', $gmt_offset);

  $today_clock = new SalatClock($today, $loc, $gmt_offset);
  $tmr_clock = new SalatClock($tomorrow, $loc, $gmt_offset);
?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-5173658-1");
pageTracker._trackPageview();
</script>

<html><body>
<div id="time_loc">Salat times for <?php print $loc['address']; ?></div>

  <div class="time_date">Today &ndash; <?php print $today->format('j M Y'); ?></div>
  <div>
    <span class="time_block"><span class="time_title">Fajr:</span>
      <span class="time"><?php print $today_clock->times[0]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Sunrise:</span>
      <span class="time"><?php print $today_clock->times[1]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Duhr:</span>
      <span class="time"><?php print $today_clock->times[2]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Asr:</span>
      <span class="time"><?php print $today_clock->times[3]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Maghrib (Iftar):</span>
      <span class="time"><?php print $today_clock->times[4]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Isha:</span>
      <span class="time"><?php print $today_clock->times[5]->format('g:ia'); ?></span>
    </span>
  </div>
  
  <div class="time_date">Tomorrow &ndash; <?php print $tomorrow->format('j M Y'); ?></div>
  <div>
    <span class="time_block"><span class="time_title">Fajr:</span>
      <span class="time"><?php print $tmr_clock->times[0]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Sunrise:</span>
      <span class="time"><?php print $tmr_clock->times[1]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Duhr:</span>
      <span class="time"><?php print $tmr_clock->times[2]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Asr:</span>
      <span class="time"><?php print $tmr_clock->times[3]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Maghrib (Iftar):</span>
      <span class="time"><?php print $tmr_clock->times[4]->format('g:ia'); ?></span>
    </span>
    <span class="time_block"><span class="time_title">Isha:</span>
      <span class="time"><?php print $tmr_clock->times[5]->format('g:ia'); ?></span>
    </span>
  </div>
</body></html>
<?php
} else {
?>
<style type="text/css" media="screen">
html, body {
  margin: 0;
  padding: 0;
  background-color: #FFFFFA;
}
  #ramadan_header {
    display: block;
    width: 98%;
    padding: 5px 1%;
    color: black;
    font-family: Helvetica, Arial, sans-serif;
    font-size: 0.75em;
    text-align: center;
  }
    #ramadan_header #time_loc {
      line-height: 1.2em;
      color: black;
      font-weight: bold;
      font-size: 1.2em;
    }
    #ramadan_header .time_date {
      margin: 3px 0px;
      color: black;
      font-weight: bold;
    }
    #ramadan_header .time_block {padding: 0px 3px;}
    #ramadan_header .time_block .time_title {color: black;}
    #ramadan_header .time_block .time {font-weight: bold; color: rgb(33, 67, 33)}
</style>
<div id="ramadan_header"></div>
<script
  src="http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js"
  type="text/javascript"></script>
<script type="text/javascript">
  gmt_offset = -1 * new Date().getTimezoneOffset() / 60;
  new Ajax.Updater('ramadan_header', './header.php', {
    'method': 'get', 'parameters': {'gmt_offset': gmt_offset, 'r': new Date().getTime()}});
</script>
<?php
}
?>