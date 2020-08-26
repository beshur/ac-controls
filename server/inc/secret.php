<?php
  if (defined('API_VERSION')) {
    define('SALT', 'SUPER_SECRET_SALT');
    define('SECRET', 'YOUR_GENERATED_PASSWORD_HASH');
  } else {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden');
  }
?>
