<?php
define('CONFIG_FILE', dirname(__FILE__) . '/../state');

function get_state() {
  $state_ = null;
  $config_value = file_get_contents(CONFIG_FILE);
  if (strlen($config_value) > 0) {
    $state_ = json_decode($config_value, TRUE);
  }
  if ($state_['api'] != API_VERSION) {
    header("HTTP/1.1 500 Server Error");
    echo "500 Server Error";
    return;
  }
  return $state_;
}

function save_state($state) {
  $state_json = json_encode($state);
  file_put_contents(CONFIG_FILE, $state_json);
}
