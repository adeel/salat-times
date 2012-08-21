<?php
# http://tanzil.info/praytime/doc/calculation/#Dhuhr

// require_once 'common.php';
require_once 'ninjadate.php';

class SalatClock {
  
  function __construct($date, $location, $gmt_offset) {
    $this->date = new NinjaDate($date, $gmt_offset);
    $this->time = $this->date->timestamp;

    $this->latitude = $location['latitude'];
    $this->longitude = $location['longitude'];
    $this->address = $location['address'];
    $this->country = $location['country'];
    $this->gmt_offset = $gmt_offset;
    
    $times = array();

    $twilight = 108;
    
    if ($this->country == "US" || $this->country == "USA") {
      $twilight = 105;
    }
    
    // Fajr and Isha times
    $times[0] = $this->_sunrise_time($twilight);
    $times[5] = $this->_sunset_time($twilight);

    // Sunrise and sunset (Maghrib) times
    list($times[1], $times[4]) = $this->_sunrise_sunset();
    
    // Dhuhr time
    $times[2] = $this->_solar_noon();
    
    // Asr time
    $rad_lat = deg2rad($this->latitude);
    $day = $this->_day_of_year();
    $beta = (2*pi()*$day)/365;
    $d = 0.006918
       - 0.399912 * cos(1*$beta)
       + 0.070570 * sin(1*$beta)
       - 0.006758 * cos(2*$beta)
       + 0.000907 * sin(2*$beta)
       - 0.002697 * cos(3*$beta)
       + 0.001480 * sin(3*$beta);

    $w = 1/15 * acos((sin(pi()/2
       - atan(2 + tan(abs($rad_lat-$d))))
      - sin($d)*sin($rad_lat)) / (cos($d)*cos($rad_lat))) * 180/pi();
    
    $times[3] = $times[2] + $w;
    $times[2] = $this->_float_to_time($times[2]);
    $times[3] = $this->_float_to_time($times[3]);
    
    ksort($times);
    $this->times = $times;
  }
    
  ##
  # Date functions
  ##
  
  function _solar_noon() {
    $r = 15 * $this->gmt_offset;
    $day = $this->_day_of_year();
    $beta = (2*pi()*$day)/365;
    $t = 229.18 * (0.000075 + (0.001868 * cos($beta))
    				                - (0.032077 * sin($beta))
    				                - (0.014615 * cos(2*$beta))
    				                - (0.040849 * sin(2*$beta)));
    return 12 + (($r - $this->longitude)/15) - ($t/60);
  }

  function _sunrise_sunset() {
    $z = 90+5/6;
    return array($this->_sunrise_time($z), $this->_sunset_time($z));
  }
  
  function _sunrise_time($zenith) {
    $date = date_sunrise($this->time, SUNFUNCS_RET_DOUBLE,
      $this->latitude, $this->longitude, $zenith, $this->gmt_offset);
    $date = $this->_float_to_time($date);
    return $date;
  }
  
  function _sunset_time($zenith) {
    $date = $this->_float_to_time(date_sunset($this->time,
      SUNFUNCS_RET_DOUBLE, $this->latitude, $this->longitude, $zenith,
      $this->gmt_offset));
    return $date;
  }
  
  function _day_of_year() {
    return $this->date->format('z') + 1;
  }
  
  function _float_to_time($float) {
    $hour = floor($float);
    $minute = floor(60 * ($float - $hour));
    
    list($year, $month, $day) = $this->date->get();
    $datetime = array($year, $month, $day, $hour, $minute);
    $datetime = new NinjaDateTime($datetime, $this->gmt_offset);
    return $datetime;
  }
  
  ##
  # Math functions
  ##
  
  function _mod($n, $d) {
    return ($n - $d * floor($n/$d));
  }
  
}
?>