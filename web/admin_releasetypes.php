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
		echo "<tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Selected</b></td><td><b>Order</b></td><td><b>Games</b></td></tr>\n";
		while ($r=$sth->fetch()) {
			echo "<tr><td>".$r['id']."</td><td><a href=admin_releasetypes_details.php?id=".$r['id'].">".$r['name']."</a></td>";
			echo "<td>".$r['selected']."</td>";
			echo "<td>".$r['rel_order']."</td>";
			$gamecount=0;
			$s2="select * from games where reltype = ?";
			$sth2 = $dbh->prepare($s2,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$sth2->bindParam(1, $r['id'], PDO::PARAM_STR);
			if ($sth2->execute()) {
			  $gamecount=$sth2->rowCount();
			  $sth2->closeCursor();
			} else {
			  echo "$s2 gave ".$dbh->errorCode()."<br>\n";
			  exit(3);
			}
			if ($gamecount > 0) {
				echo "<td>$gamecount</td>";
			} else {
				echo "<td>Unused (<a href=admin_releasetypes_delete.php?id=".$r['id'].">Delete</a>)</td>";
			}
		}
		echo "</tr>\n</table>\n";
	}
	$sth->closeCursor();
} else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
}
?>
