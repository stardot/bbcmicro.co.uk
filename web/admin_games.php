<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');

require_once('includes/admin_menu.php');

show_admin_menu();


$s="	SELECT 		id,title,year,
			(select filename from images where main=100 and gameid = games.id) as disc,
			(select filename from screenshots where main=100 and gameid = games.id) as screenshot,";
	if (defined('ST_FILES') && ST_FILES ) {
		$s=$s."
			(select GROUP_CONCAT(CONCAT(files.id,'|',files.filename) SEPARATOR '@') from files where files.gameid = games.id) as files,";
	}
		$s=$s."
			(SELECT GROUP_CONCAT(CONCAT(publishers.id,'|',publishers.name) SEPARATOR '@') FROM games_publishers LEFT JOIN publishers ON pubid=publishers.id WHERE gameid=games.id) AS publishers,
			(SELECT GROUP_CONCAT(CONCAT(authors.id,'|',authors.name) SEPARATOR '@') FROM games_authors LEFT JOIN authors ON authors_id=authors.id WHERE games_id=games.id) AS authors,
			(SELECT GROUP_CONCAT(CONCAT(genres.id,'|',genres.name) SEPARATOR '@') FROM game_genre LEFT JOIN genres ON genreid=genres.id WHERE gameid=games.id) AS genres
	FROM 		games 
	ORDER BY 	title";

$sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
if ($sth->execute()) {
	echo $sth->rowCount()." games. <a href='admin_game_details.php?id=0'>New game</a><hr>";
	if ($sth->rowCount()) {
		echo "<table>\n";
		echo "<tr><td><b>Title</b></td><td><b>Details</b></td></tr>\n";
		while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
			if ($r['id']) {
				$t=$r['id'];
			} else {
				$t='<i>None</i>';
			}
            $title = str_replace(",", ", ", $r['title']);
			echo "<tr><td style='width: 50%; max-width: 50%; overflow: hidden;'><a href='admin_game_details.php?id=".$r['id']."'>".$title."</td>\n";

            echo "<td style='width: 50%; max-width: 50%'>";

            echo "<p><b>Year:</b> ".$r['year']."</p>";

			$pubs=explode('@',$r['publishers'] ?? '');

            if (count($pubs) > 1) {
    			echo "<p><b>Publishers:</b> ";
            } else {
    			echo "<p><b>Publisher:</b> ";
            }

			$names='';
			foreach ($pubs as $pub) {
				if ($pub) {
					list($id,$name)=explode('|',$pub);
					if ($name) $names.="$name, ";
				}
			}
			if ($names) {
				echo substr($names,0,strlen($names)-2);
			} else {
				echo "<i>None</i>";
			}
			echo "</p>";

			echo "<p><b>Screenshot:</b> <a href='admin_file.php?t=s&id=".$r['id']."'>";
			if (empty($r['screenshot'])){
                echo "<i>None</i>";
            } else {
                $screenshot = $r['screenshot'];
                if (strlen($screenshot) > 15) $screenshot = substr($r['screenshot'], 0, 15) . "...";
                echo $screenshot;
            }
			echo "</a></p>";

			echo "<p><b>Disc:</b> <a href='admin_file.php?t=d&id=".$r['id']."'>";
			if (empty($r['disc'])) {
                echo "<i>None</i>";
            } else {
                $disc = $r['disc'];
                if (strlen($disc) > 15) $disc = substr($r['disc'], 0, 15) . "...";
                echo $disc;
            }
			echo "</a></p>";

			if (defined('ST_FILES') && ST_FILES ) {
				echo "<p>Files: <a href='admin_file.php?t=f&id=".$r['id']."'>";
				$files=explode('@',$r['files'] ?? '');
				$names='';
				$filenames='';
				foreach ($files as $file) {
					if ($file) {
						list($id,$filename)=explode('|',$file);
						if ($filename) $filenames.="$filename, ";
					}
				}
				if ($filenames) {
					echo substr($filenames,0,strlen($filenames)-2);
				} else {
					echo "<i>None</i>";
				}
			echo "</a></p>";
			}
			echo "</td>\n</tr>\n";
		}
		echo "</table>\n";
	}
} else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
}
$sth->closeCursor();
?>
