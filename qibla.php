<?php
class QiblaLocator {
  function __construct($lat, $lon) {
    $this->latitude = $lat;
    $this->longitude = $lon;
  }
  
  function locate() {
    $makkah = array(21.4409, 39.8068);
    $here = array($this->latitude, $this->longitude);

    $d = pi()/180;

    # http://mathforum.org/library/drmath/view/55417.html
    $y = sin($makkah[1]*$d-$here[1]*$d)*cos($makkah[0]*$d);
    $x = cos($here[0]*$d)*sin($makkah[0]*$d)
       - sin($here[0]*$d)*cos($makkah[0]*$d)*cos($makkah[1]*$d-$here[1]*$d);

    $theta = atan2($y, $x);

    return rad2deg($theta);
  }  
}
?>