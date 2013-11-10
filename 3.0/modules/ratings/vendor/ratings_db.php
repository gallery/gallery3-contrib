<?

if(class_exists("Kohana") && !file_exists(VARPATH."modules/ratings/db.settings.php")){
  $ratings_db_config = Kohana::config('database.default');
    $dbHost = $ratings_db_config['connection']['host'];
    $dbUser = $ratings_db_config['connection']['user'];
    $dbPassword = $ratings_db_config['connection']['pass'];
    $dbDatabase = $ratings_db_config['connection']['database'];
    $dbPrefix = $ratings_db_config['table_prefix'];

  $fh = fopen(VARPATH."modules/ratings/db.settings.php","w");
    fwrite($fh, "<?\n");
    fwrite($fh, "\$this->dbHost = \"".$dbHost."\";\n");
    fwrite($fh, "\$this->dbUser = \"".$dbUser."\";\n");
    fwrite($fh, "\$this->dbPassword = \"".$dbPassword."\";\n");
    fwrite($fh, "\$this->dbDatabase = \"".$dbDatabase."\";\n");
    fwrite($fh, "\$prefix = \"".$dbPrefix."\";\n");
    fwrite($fh, "?>\n");
  fclose($fh);

}

?>
