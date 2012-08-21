<?php
# PUT YOUR PEAR PATH HERE
# set_include_path(get_include_path() . PATH_SEPARATOR . 'c:/Program Files/php/');
set_include_path(get_include_path() . PATH_SEPARATOR . "C:\PHP\\" . PATH_SEPARATOR . "C:\PHP\PEAR\\");
require_once "Net/GeoIP.php";

class Locator {
  
  var $geoip_path = 'GeoLiteCity.dat';
  
  function __construct($ip) {
    $this->ip = $ip;
    $this->lookup = Net_GeoIP::getInstance($this->geoip_path);
  }
  
  function locate() {
    $ip = $this->ip;
    try {
      $loc = $this->lookup->lookupLocation($ip);
    } catch (Exception $e) {
      return false;
    }
    return array('latitude'=>$loc->latitude, 'longitude'=>$loc->longitude,
      'address'=>($loc->city . ", " . $loc->region . ", " . $loc->countryName),
      'country'=>$loc->countryCode);
  }
  
}
?>