<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  $db = Database::instance();
  $tables = $db->query("SHOW TABLE STATUS");
  $database_size = 0;
  foreach($tables as $table) {
    $database_size += ($table->Data_length + $table->Index_length);
  }
  $database_size = $database_size / 1024 / 1024;
?>
<ul>
  <li>
    <?= t("Database size: %dbsize MB", array("dbsize" => number_format($database_size, 2))) ?>
  </li>
  <li>
    <?= t("Number of tables: %dbtables", array("dbtables" => count($tables))) ?>
  </li>
</ul>
