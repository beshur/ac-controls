<?php
/* Air Conditioning settings */
define(API_VERSION, 1);
define(CONFIG_FILE, './config');
define(DELIMITER, ';');
define(UNIT_MODES, ['cold' => 1, 'hot' => 2, 'dry' => 3, 'vent' => 4]);

include_once('./secret.php');

/* just for reference */
$unit_template = array(
  'name' => 'LVN', // unit unique name
  'mode' => UNIT_MODES['cold'], // cold,
  'temp' => 23, // temp in degrees C
  'speed' => 1, // air speed 1-3
  'status' => 0, // off/on
  'update' => 1 // toggles between 1/0
);

$unit = '';
$state = null;

function get_config() {
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

function prepare_unit_data($state_, $unit_) {
  $found = filter_units($state_, $unit_);
  if (is_null($found)) {
    return header("HTTP/1.1 404 Not Found");
  }
  header('XApiVersion: ' . API_VERSION);
  header('XUnitKeys: name; mode; temp; speed; status; update');
  echo implode(DELIMITER, $found);
}


function filter_units($state_, $unit_) {
  $found = null;
  foreach ($state_['units'] as $item) {
    if ($item['name'] == $unit_) {
      $found = $item;
    }
  }
  return $found;
}

function valid_ac_values($item) {
  global $unit;
  if ($item == $unit) {
    return $item;
  }
  return intval($item);
}

function update_unit($state_, $unit_, $updated) {
  $found = filter_units($state_, $unit_);
  if (is_null($found)) {
    return header("HTTP/1.1 404 Not Found");
  }
  $valid_keys = array_intersect_key($updated, $found);
  $valid_values = array_map('valid_ac_values', $valid_keys);

  for($i = 0; $i < count($state_['units']); $i++) {
    if ($state_['units'][$i]['name'] == $unit_) {
      // found, replacing
      $state_['units'][$i] = $valid_values;
    }
  }
  save_state($state_);
  return true;
}

function save_state($state_) {
  $state_json = json_encode($state_);
  file_put_contents(CONFIG_FILE, $state_json);
}

// getting config
$state = get_config();
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
  $unit = substr($_GET['name'], 0, 16);
  if (strlen($unit) > 0) {
    echo prepare_unit_data($state, $unit);
  } else {
    echo urlencode(json_encode($state));
  }
} else if ($method == 'POST') {
  if (strcmp($_POST['secret'], SECRET)) {
    return header('HTTP/1.1 403 Forbidden');
  }

  $unit = substr($_POST['name'], 0, 16);
  if (strlen($unit) > 0) {
    $update = update_unit($state, $unit, $_POST);
    if ($update == true) {
      return header("HTTP/1.1 200 OK");
    } else {
      return header("HTTP/1.1 500 Server Error");
    }
  } else {
    return header('HTTP/1.1 400 Bad Request');
  }
}

?>
