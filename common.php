<?php
require_once 'location.php';
require_once 'geocoder.php';

function guess_location() {
  if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  
  $locator = new Locator($ip);
  $loc = $locator->locate();

  if (!$loc) {
    return '';
  }

  return $loc['address'];
}

function get_coords($location=null) {
  if (!$location) {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    $locator = new Locator($ip);
    $loc = $locator->locate();

    if (!$loc) {
      return false;
    }

    $location = $loc['address'];
  }

  $geocoder = new Geocoder($location);
  $loc = $geocoder->geocode();
  if ($loc) {
    $lat = $loc['latitude'];
    $lon = $loc['longitude'];
  }
  if (!$loc || (($lat == 0) && ($lon == 0))) {
    return false;
  }

  return array($loc, $lat, $lon);
}

function get_gmt_offset_for_location($lat, $lon, $dst) {
  $lat = urlencode($lat);
  $lon = urlencode($lon);
  try {
    $url = "http://ws.geonames.org/timezoneJSON?lat=$lat&lng=$lon";
    $gmt_offset = json_decode(read_url($url), true);
    if (!$dst)
      $gmt_offset = $gmt_offset['gmtOffset'];
    else
      $gmt_offset = $gmt_offset['dstOffset'];    
  } catch (Exception $e) {
    try {
      $url = "http://www.earthtools.org/timezone/$lat/$lon";
      $r = read_url($url);
      $gmt_offset = str_between($r, "<offset>", "</offset>");
      if ($dst)
        $gmt_offset += 1;
    } catch (Exception $e) {
      die("<b>Error:</b>"
        . "  The request could not be completed at this time."
        . "  Please try again later.");
    }
  }
  return $gmt_offset;
}

function read_url($url) {
  return file_get_contents($url);
  $ch = curl_init($url);
  
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 3);
  
  $response = curl_exec($ch);
  curl_close($ch);
  
  if (!$response) {
    throw new Exception('The request could not be completed.');
  }
  
  return $response;
}

function str_between($str, $a, $b) {
  if (strpos($str, $a) === false || strpos($str, $b) === false)
    return false;
  else {
      $start = strpos($str, $a) + strlen($a);
      $end = strpos($str, $b);
      return substr($str, $start, $end - $start);
  }
}
?>