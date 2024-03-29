<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');

require_once('includes/admin_menu.php');

show_admin_menu();

$s="	SELECT 		id,name,email,message,date
		FROM 		contacts
		ORDER BY	id desc
        LIMIT		200";

$sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
if ($sth->execute()) {
	if ($sth->rowCount()) {
		echo '<p>'.$sth->rowCount()." contacts.</p><hr>";
		echo "<table>\n";
		echo "<tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Email</b></td><td><b>Message</b></td><td><b>Date</b></td></tr>\n";
		while ($r=$sth->fetch()) {
			echo "<tr><td>".$r['id']."</td>";
			echo "<td>".$r['name']."</td>";
			echo "<td>".$r['email']."</td>";
			echo "<td>".$r['message']."</td>";
			echo "<td>".$r['date']."</td></tr>\n";
		}
		echo "</table>\n";
	}
	$sth->closeCursor();
} else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
}
?>
