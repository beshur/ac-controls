<?php
include_once dirname(__FILE__) . '/inc/index.php';

$state = get_state();

function is_checked($unit, $param, $option) {
  if($unit[$param] == $option) {
    echo ' checked="true"';
  }
}

function unit_template($unit) {
  ?>
  <div class="unit">
    <form action="./api.php" method="POST">
      <h2>Unit: <?= $unit['name'] ?></h2>
      <input type="hidden" name="name" value="<?= $unit['name'] ?>" />
      <input type="hidden" name="from_site" value="1" />

      <h3>Mode</h3>
      <label>
        <input type="radio" name="mode" value="1" <?= is_checked($unit, 'mode', '1'); ?> /> Cold
      </label>
      <label>
        <input type="radio" name="mode" value="2" <?= is_checked($unit, 'mode', '2'); ?> /> Hot
      </label>
      <label>
        <input type="radio" name="mode" value="3" <?= is_checked($unit, 'mode', '3'); ?> /> Dry
      </label>
      <label>
        <input type="radio" name="mode" value="4" <?= is_checked($unit, 'mode', '4'); ?> /> Vent
      </label>

      <h3>Temperature in °C</h3>
      <input type="number" name="temp" min="10" max="30" value="<?= $unit['temp'] ?>" />

      <h3>Air Speed</h3>
      <label>
        <input type="radio" name="speed" value="1" <?= is_checked($unit, 'speed', '1'); ?> /> 1
      </label>
      <label>
        <input type="radio" name="speed" value="2" <?= is_checked($unit, 'speed', '2'); ?> /> 2
      </label>
      <label>
        <input type="radio" name="speed" value="3" <?= is_checked($unit, 'speed', '3'); ?> /> 3
      </label>

      <h3>Power</h3>
      <label>
        <input type="radio" name="status" value="1" <?= is_checked($unit, 'status', '1'); ?> /> On
      </label>
      <label>
        <input type="radio" name="status" value="0" <?= is_checked($unit, 'status', '0'); ?> /> Off
      </label>

      <h3>Password</h3>
      <input type="password" name="secret" placeholder="Password" required />

      <div class="submit">
        <button type="submit">Submit</button>
      </div>
    </form>
  </div>

  <?php
}

?>
<html>
  <header>
    <title>Home Automation AC Controls</title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </header>
  <body>
    <div class="app">
      <h1>Home Automation AC Controls 💨</h1>
      <?php if (isset($_GET['updated'])) {
        if ($_GET['updated'] == '1') { ?>
          <div class="notice success">
            Settings updated!
          </div>
        <?php } else { ?>
          <div class="notice error">
            Updating settings failed!
          </div>
      <?php }} ?>

      <div class="units">
        <?php foreach ($state['units'] as $unit) {
            unit_template($unit);
          }
        ?>
      </div>
    </div>
  </body>
</html>
