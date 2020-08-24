<?php
/* Air Conditioning settings */
define(API_VERSION, 1);

define(DELIMITER, ';');

define(UNIT_MODES, ['cold' => 1, 'hot' => 2, 'dry' => 3, 'vent' => 4]);

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
$config_value = file_get_contents('./config');
if (strlen($config_value) > 0) {
  $state = json_decode($config_value, TRUE);
}
if ($state['api'] != API_VERSION) {
  header("HTTP/1.1 500 Server Error");
  echo "500 Server Error";
  return;
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

// getting config
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
  $unit = substr($_GET['unit'], 0, 16);
  if (strlen($unit) > 0) {
    echo prepare_unit_data($state, $unit);
  } else {
    echo json_encode($state);
  }
} else if ($method == 'POST') {
  echo "Not implemented";
}

?>
