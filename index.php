<?php

ini_set('display_errors', 1);
$root = __DIR__;
//
session_start();

$warning = substr(sprintf('%o', fileperms('files/')), -4) != '777' ? '(Warning: make sure __DIR__/files/ directory is writable for Apache)' : '';

echo sprintf("<html><head>
  </head><body>
  <h1 style='margin-top: 60px'>Welcome to the Simple HTML->PDF generator<h1>
  <h5 style='color: red;'>%s</h5>
  </body></html>", $warning);

echo "<script src=js/bundle.js></script>";

?>
