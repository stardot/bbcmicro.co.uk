<?php
  try {
    $db  = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8",DB_USER,DB_PASS);
  } catch (PDOException $e) {
    throw new PDOException("Error  : " .$e->getMessage());
echo "error";
  }
?>

