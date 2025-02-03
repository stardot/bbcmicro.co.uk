<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');

require_once('includes/admin_menu.php');

show_admin_menu();

$s="	SELECT 		id,games_id,heading,title,subtitle,random,visible,colour,url,screenshot_url,sort_order,position,download_button,play_button,show_publisher,show_year
		FROM 		highlights
		ORDER BY 	sort_order";

$sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
if ($sth->execute()) {
	if ($sth->rowCount()) {
		echo '<p>'.$sth->rowCount()." highlights. <a href='admin_highlights_details.php'>New highlight</a></p><hr>";
		echo "<table>\n";
		echo "<tr><td><b>ID</b></td><td><b>Heading</b></td><td><b>Show on site?</b></td><td><b>Random?</b></td><td><b>Colour</b></td><td><b>Sort order</b></td><td><b>Position</b></td><td><b>Game ID</b></td><td><b>Title</b></td><td><b>Subtitle</b></td><td><b>Link</b></td><td><b>Screenshot</b></td><td><b>Download button?</b></td><td><b>Play button?</b></td><td><b>Show publisher?</b></td><td><b>Show date?</b></td><td><b>Delete</b></td></tr>\n";
		while ($r=$sth->fetch()) {
			echo "<tr><td>".$r['id']."</td><td><a href=admin_highlights_details.php?id=".$r['id'].">".$r['heading']."</a></td>";
			echo "<td>".($r['visible'] == 1 ? 'Y' : 'N')."</td>";
            if ($r['random'] == 1) {
			    echo "<td>Random game</td>";
            } elseif ($r['random'] == 2) {
			    echo "<td>Lucky dip button</td>";
            } else {
			    echo "<td>No</td>";
            }
			echo "<td>".$r['colour']."</td>";
			echo "<td>".$r['sort_order']."</td>";
			echo "<td>".($r['position'] == 1 ? 'Bottom' : 'Top')."</td>";
			echo "<td>".$r['games_id']."</td>";
			echo "<td>".$r['title']."</td>";
			echo "<td>".$r['subtitle']."</td>";
			echo "<td>".$r['url']."</td>";
			echo "<td>".$r['screenshot_url']."</td>";
			echo "<td>".($r['download_button'] == 1 ? 'Y' : 'N')."</td>";
			echo "<td>".($r['play_button'] == 1 ? 'Y' : 'N')."</td>";
			echo "<td>".($r['show_publisher'] == 1 ? 'Y' : 'N')."</td>";
			echo "<td>".($r['show_year'] == 1 ? 'Y' : 'N')."</td>";
			echo "<td><a href=admin_highlights_delete.php?id=".$r['id'].">Delete</a></td>";
		}
		echo "</tr>\n</table>\n";
	} else {
		echo "<p>No highlights. <a href='admin_highlights_details.php'>New highlight</a></p><hr>";
    }
	$sth->closeCursor();
} else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
}
?>
