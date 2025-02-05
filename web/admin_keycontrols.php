<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');

require_once('includes/admin_menu.php');

show_admin_menu();

$sql = "select k.id as keyid, k.keydescription, k.keyname, k.jsbeebgamekey, k.jsbeebbrowserkey, k.rel_order, g.id as gameid, g.title from game_keys k inner join games g on k.gameid = g.id order by g.title, k.rel_order";
$sth = $dbh->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
if ($sth->execute()) {
	echo '<p>'.$sth->rowCount()." key control entries. <a href='admin_keycontrols_details.php'>New key control entry</a></p><hr>";
	if ($sth->rowCount()) {
		echo "<table>\n";
		echo "<tr><td><b>Game ID</b></td><td><b>Key order</b></td><td><b>Game title</b></td>";
		echo "<td><b>Details</b></td><td><b>Delete</b></td></tr>\n";
		while ($r=$sth->fetch()) {
			echo "<tr><td>".$r['gameid']."</td><td>".$r['rel_order']."</td><td><a href=admin_keycontrols_details.php?id=".$r['keyid'].">".$r['title']."</a></td>";
			echo "<td><p>Key name: ".$r['keyname']."</p><p>Key function: ".$r['keydescription']."</p>";
			echo "<p>JSBeeb game key: ".$r['jsbeebgamekey']."</p><p>JSBeeb browser key: ".$r['jsbeebbrowserkey']."</p></td>";
			echo "<td><a href=admin_keycontrols_delete.php?id=".$r['keyid'].">Delete</a></td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
} else {
	echo "$sth gave ".$dbh->errorCode()."<br>\n";
}
?>
