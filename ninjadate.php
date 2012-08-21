<?php

class NinjaDate {
  
  function __construct($date=null, $gmt_offset=null) {
    $this->server_offset = $this->_get_server_offset();
    
    $this->gmt_offset = $gmt_offset;
    
    if (!$date) {
      $date = time();
    }
    
    if (get_class($date) == "NinjaDate") {
      $date = $date->get();
    }
    
    if (is_int($date)) {
      $this->timestamp = $date;
      $date = $this->get();
    } else if (is_string($date)) {
      $this->timestamp = strtotime($date);
      $date = $this->get();
    }
    
    $this->timestamp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
    if (!is_null($gmt_offset)) {
      $this->timestamp += ($this->server_offset - $gmt_offset) * 3600;
    }
    
  }
  
  function get($gmt_offset=null) {
    if (is_null($gmt_offset) && !is_null($this->gmt_offset)) {
      $gmt_offset = $this->gmt_offset;
    }
    
    $timestamp = $this->timestamp;
    if (!is_null($gmt_offset)) {
      $timestamp = $this->_adjust_timestamp($gmt_offset);
    }
    
    $year = date('Y', $timestamp);
    $month = date('n', $timestamp);
    $day = date('j', $timestamp);
    
    return array($year, $month, $day);
  }
  
  function format($format, $gmt_offset=null) {
    if (is_null($gmt_offset) && !is_null($this->gmt_offset)) {
      $gmt_offset = $this->gmt_offset;
    }
    
    $timestamp = $this->timestamp;
    if (!is_null($gmt_offset)) {
      $timestamp = $this->_adjust_timestamp($gmt_offset);
    }
    
    return date($format, $timestamp);
  }
  
  function _adjust_timestamp($gmt_offset) {
    $offset = $gmt_offset - $this->server_offset;
    return $this->timestamp + $offset * 3600;
  }
  
  function _get_server_offset() {
    $timezone_server = new DateTimeZone(date_default_timezone_get());
    $time_server = new DateTime('now', $timezone_server);
    $offset = $timezone_server->getOffset($time_server);
    return $offset / 3600;
  }
  
}

class NinjaDateTime extends NinjaDate {
  
  function __construct($date, $gmt_offset=null) {
    $this->server_offset = $this->_get_server_offset();
    
    $this->gmt_offset = $gmt_offset;
    
    if (is_int($date)) {
      $this->timestamp = $date;
    } else if (is_string($date)) {
      $this->timestamp = strtotime($date);
    } else {
      if (!isset($date[3])) $date[3] = 0;
      if (!isset($date[4])) $date[4] = 0;
      if (!isset($date[5])) $date[5] = 0;
      
      $this->timestamp = mktime($date[3], $date[4], $date[5],
        $date[1], $date[2], $date[0]);
      if (!is_null($gmt_offset)) {
        $this->timestamp += ($this->server_offset - $gmt_offset) * 3600;
      }
    }
  }
  
  function get($gmt_offset=null) {
    if (is_null($gmt_offset) && !is_null($this->gmt_offset)) {
      $gmt_offset = $this->gmt_offset;
    }
    
    $timestamp = $this->timestamp;
    if (!is_null($gmt_offset)) {
      $timestamp = $this->_adjust_timestamp($gmt_offset);
    }
    
    $year = date('Y', $timestamp);
    $month = date('n', $timestamp);
    $day = date('j', $timestamp);
    $hour = date('G', $timestamp);
    $minute = date('i', $timestamp);
    $second = date('s', $timestamp);
    
    return array($year, $month, $day, $hour, $minute, $second);
  }
  
}

?>