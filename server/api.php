<?php
/* Air Conditioning settings */
include_once dirname(__FILE__) . '/inc/index.php';

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
  // only name is string
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
  $valid_values['update'] = ++$found['update'];
  if ($valid_values['update'] == 100) {
    $valid_values['update'] = 0;
  }

  for($i = 0; $i < count($state_['units']); $i++) {
    if ($state_['units'][$i]['name'] == $unit_) {
      // found, replacing
      $state_['units'][$i] = $valid_values;
    }
  }
  save_state($state_);
  return true;
}

// getting config
$state = get_state();
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
  $unit = substr($_GET['name'], 0, 16);
  if (strlen($unit) > 0) {
    echo prepare_unit_data($state, $unit);
  } else {
    echo urlencode(json_encode($state));
  }
} else if ($method == 'POST') {
  $from_site = isset($_POST['from_site']);
  $user_secret = filter_var( $_POST['secret'], FILTER_SANITIZE_STRING);
  $user_secret_hash = password_hash($user_secret, PASSWORD_BCRYPT, ['salt' => SALT]);
  if (!hash_equals($user_secret_hash, SECRET)) {
    header('HTTP/1.1 403 Forbidden');
    if ($from_site) {
      return header('Location: ./index.php?updated=0');
    } else {
      return;
    }
  }

  $unit = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
  if (strlen($unit) > 0) {
    $update = update_unit($state, $unit, $_POST);
    if ($update == true) {
      if ($from_site) {
        return header('Location: ./index.php?updated=1');
      } else {
        header("HTTP/1.1 200 OK");
        echo "Updated";
        return;
      }
    } else {
      if ($from_site) {
        header('Location: ./index.php?updated=0');
      } else {
        header("HTTP/1.1 500 Server Error");
      }
      return;
    }
  } else {
    if ($from_site) {
      header('Location: ./index.php?updated=0');
    } else {
      header('HTTP/1.1 400 Bad Request');
    }
    return;
  }
}

?>
