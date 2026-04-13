<?php
require '/usr/local/apache/share/bbcmicrocouk/public_html/includes/config.php';
require '/usr/local/apache/share/bbcmicrocouk/public_html/includes/db_connect.php';

$ym = date("Ym",time() - 90*24*60*60);
$sql  = "SELECT id, SUM(downloads) as dl_all, SUM(gamepages) as gp_all, ";
$sql .= "SUM(CASE WHEN year > '" . $ym . "' THEN downloads ELSE 0 END) as dl, ";
$sql .= "SUM(CASE WHEN year > '" . $ym . "' THEN gamepages ELSE 0 END) as gp ";
$sql .= "FROM game_downloads GROUP BY id";

$sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
if ($sth->execute()) {
    $res = $sth->fetchAll();
} else {
    echo "Error fetching download stats:\n";
    $sth->debugDumpParams();
    $res = array();
}

foreach ($res as $game) {
    $sql1 = "REPLACE INTO game_stats (game_id, downloads, downloads_all, gamepages, gamepages_all) VALUES (:gameid, :dl, :dlall, :gp, :gpall)";
    $sth1 = $db->prepare($sql1,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth1->bindParam(':gameid', $game["id"], PDO::PARAM_INT);
    $sth1->bindParam(':dlall',$game["dl_all"], PDO::PARAM_INT);
    $sth1->bindParam(':dl',$game["dl"], PDO::PARAM_INT);
    $sth1->bindParam(':gpall',$game["gp_all"], PDO::PARAM_INT);
    $sth1->bindParam(':gp',$game["gp"], PDO::PARAM_INT);
    $res1 = $sth1->execute();
    if (!$res1) {
        echo "Error updating stats for game " . $game["id"] . ":\n";
        $sth1->debugDumpParams();
    }
}

$date=date('Y-m-d H:i:s');
echo $date . " Stats updated for ".$sth->rowCount()." games\n";
?>
