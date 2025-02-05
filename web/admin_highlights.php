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
		echo "<tr><td><b>ID</b></td><td><b>Heading</b></td><td><b>Show on site?</b></td><td><b>Random?</b></td><td><b>Details</b></td><td><b>Delete</b></td></tr>\n";
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
			echo "<td><p>Background colour: ";
			if (empty($r['colour'])){
                echo "<i>White</i>";
            } else {
                echo $r['colour'];
            }
			echo "</p>";
			echo "<p>Sort order: ".$r['sort_order']."</p>";
			echo "<p>Position: ".($r['position'] == 1 ? 'Bottom' : 'Top')."</p>";
			echo "<p>Game ID: ";
			if (empty($r['games_id'])){
                echo "<i>None</i>";
            } else {
                echo $r['games_id'];
            }
			echo "</p>";
			echo "<p>Title override: ";
			if (empty($r['title'])){
                echo "<i>None</i>";
            } else {
                echo $r['title'];
            }
			echo "</p>";
			echo "<p>Subtitle: ";
			if (empty($r['subtitle'])){
                echo "<i>None</i>";
            } else {
                echo $r['subtitle'];
            }
			echo "</p>";
			echo "<p>Link override: ";
			if (empty($r['url'])){
                echo "<i>None</i>";
            } else {
                $url = $r['url'];
                if (strlen($url) > 15) $url = substr($r['url'], 0, 15) . "...";
                echo $url;
            }
			echo "</p>";
			echo "<p>Screenshot override: ";
			if (empty($r['screenshot_url'])){
                echo "<i>None</i>";
            } else {
                $screenshot = $r['screenshot_url'];
                if (strlen($screenshot_url) > 15) $screenshot = substr($r['screenshot_url'], 0, 15) . "...";
                echo $screenshot;
            }
			echo "</p>";
			echo "<p>Download button? ".($r['download_button'] == 1 ? 'Y' : 'N')."</p>";
			echo "<p>Play button? ".($r['play_button'] == 1 ? 'Y' : 'N')."</p>";
			echo "<p>Show publisher? ".($r['show_publisher'] == 1 ? 'Y' : 'N')."</p>";
			echo "<p>Show date? ".($r['show_year'] == 1 ? 'Y' : 'N')."</p></td>";
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
