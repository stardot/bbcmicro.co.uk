<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');

require_once('includes/admin_menu.php');

show_admin_menu();

$s="	SELECT 		id,name,selected,rel_order
		FROM 		reltype
		ORDER BY 	name";

$sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
if ($sth->execute()) {
	if ($sth->rowCount()) {
		echo '<p>'.$sth->rowCount()." release types. <a href='admin_releasetypes_details.php'>New release type</a></p><hr>";
		echo "<table>\n";
		echo "<tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Selected</b></td><td><b>Order</b></td></tr>\n";
		while ($r=$sth->fetch()) {
			echo "<tr><td>".$r['id']."</td><td><a href=admin_releasetypes_details.php?id=".$r['id'].">".$r['name']."</a></td>";
			echo "<td><a href=admin_releasetypes_details.php?id=".$r['id'].">".$r['selected']."</a></td>";
			echo "<td><a href=admin_releasetypes_details.php?id=".$r['id'].">".$r['rel_order']."</a></td></tr>\n";
		}
		echo "</table>\n";
	}
	$sth->closeCursor();
} else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
}
?>
