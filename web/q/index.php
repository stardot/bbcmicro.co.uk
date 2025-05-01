<?php
// For use in search boxes - return JSON encoded key values.

require '../includes/config.php';
require '../includes/db_connect.php';

$limit=20;

if ( isset($_GET["qt"])) {
  $qtype=$_GET["qt"];
}

if ( isset($_GET["qv"])) {
  $qvalue=$_GET["qv"];
}

switch ($qtype) {
  case "suggestions":
    $sql = "select substring_index(title,'(',1) as title from games where IFNULL(hide,'N') <> 'Y' and title like :query union select substring_index(name,'(',1) as name from publishers where name like :query union select series from games where IFNULL(hide,'N') <> 'Y' and series like :query union select substring_index(name,'(',1) as name from genres where name like :query union select distinct year as name from games where IFNULL(hide,'N') <> 'Y' and year like :query union select name from authors where name like :query union select substring_index(name,'(',1) as name from compilations where name like :query";
    $qvalue = $qvalue.'%';
    break;
  case "publisher":
    $sql = "select id, name from publishers where name like :query";
    $qvalue = '%'.$qvalue.'%';
    break;
  case "genre1":
    $sql = "select id, name from genres where id in (select distinct genre from games where IFNULL(hide,'N') <> 'Y') and name like :query";
    $qvalue = '%'.$qvalue.'%';
    break;
  default:
    exit();
}

$sql=$sql.' limit '.$limit;

$sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->bindParam(':query', $qvalue, PDO::PARAM_STR);

if ($sth->execute()) {
  $str=json_encode($sth->fetchAll(PDO::FETCH_ASSOC));
  echo $str . "\n";
} else {
  echo "Error:";
  echo "\n";
  $sth->debugDumpParams ();
  $res=array();
}



?>
