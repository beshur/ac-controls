<?php
  if (defined('API_VERSION')) {
    define('SECRET', 'REPLACE_WITH_YOUR_SECRET');
  }
  header('HTTP/1.1 403 Forbidden');
?>
