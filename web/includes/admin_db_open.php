<?php
  try {

    $dbh  = new PDO("mysql:host=".ADMIN_DB_HOST.";dbname=".ADMIN_DB_NAME.";charset=utf8",ADMIN_DB_USER,ADMIN_DB_PASS);
  } catch (PDOException $e) {
    throw new PDOException("Error  : " .$e->getMessage());
echo "error";
  }
?>

