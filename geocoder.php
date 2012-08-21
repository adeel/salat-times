<?php
require_once 'common.php';

class Geocoder {
  # PUT YOUR GOOGLE API KEY HERE
  var $key = 'ABQIAAAAW63swZYduI8T3bDGnBAKixQQMWbFHArUOX-rEI8NwSn5Z4TbWBQ6HFLpMAPz8fxVT3r-SWc-_1ajQA';
  var $cache_path = 'cache/geocode';
  
  function __construct($location) {
    $this->location = $location;
  }
  
  function geocode() {
    $cache = json_decode(file_get_contents($this->cache_path), true);
    if (array_key_exists($this->location, $cache)) {
      return $cache[$this->location];
    }
    
    $url = 'http://maps.google.com/maps/geo?output=json&key=';
    $url .= urlencode($this->key);
    $url .= '&q=' . urlencode($this->location);
    $data = json_decode(read_url($url));
    if ($data->Status->code != 200) {
      return false;
    }
    $data = $data->Placemark[0];
    $data = array('address'=>$data->address,
      'latitude'=>$data->Point->coordinates[1],
      'longitude'=>$data->Point->coordinates[0], 
      'country'=>$data->AddressDetails->Country->CountryNameCode);
    $cache[$this->location] = $data;
    file_put_contents($this->cache_path, json_encode($cache));
    return $data;
  }
}
?>