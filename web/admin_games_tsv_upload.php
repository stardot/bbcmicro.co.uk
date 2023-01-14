<?php
#require('includes/admin_session.php');
#require_once('includes/config.php');
#require_once('includes/admin_db_open.php');

define('MAX_TSV_SIZE',512*1024);

if ($_FILES) {
	if (array_key_exists('game_list_tsv',$_FILES)) {
		$f=$_FILES['game_list_tsv'];
		$e=$f['error'];
		if ($e===0) {
			$p=$f['tmp_name'];
			$s=$f['size'];
			if (file_exists($p) && $s<=MAX_TSV_SIZE) {
				echo "Processing ...<hr>\n";

						$lines=file_get_contents($p);
		foreach (explode("\n",$lines) as $line) {
			$line=trim($line);
			if ($line) {
				$fields=explode("\t",$line);
				if ($fields && count($fields)>4) {
					$id=$fields[0];
					# Skip header line
					if ($id=="ID") continue;
					$pub=$fields[1];
					$title=$fields[2];
					# Year is sometimes 19XX
					$year=$fields[3];
					# primary sometimes blank, with first genre afterwards "Missing"
					$primary=$fields[4];
					#if (is_numeric($id) && $id && is_numeric($year) && $year) {
					if (is_numeric($id) && $id) {
						#echo count($fields)." $id $pub $title $year $primary\n";
						$other_genres=array();
						$last=count($fields)-1;
						$cur=5;
						if ($last>=5) {
							while ($cur<=$last) {
								$other_genres[]=$fields[$cur++];
							}
						}
						#echo "\n";
						echo "$id primary $primary";
					       	if ($other_genres) {
							echo " other ";
							echo implode(",",$other_genres);
						}
						echo "<br>\n";
					} else {
					#	echo "id or year mangled?  skipping $line\n";
						echo "id mangled?  skipping $line<br>\n";
					}
				}
			} else {
				echo "??? don't understand $line<hr>\n";
				print_r($fields);
				echo "<hr>\n";
			}
		}

			} else {
				echo "File invalid or too large<br>\n";
			}
		} else {
			echo "File error: $e<br>\n";
		}
	} else {
		echo "Invalid file<br>\n";
	}
} else {
	echo "Browse for a .tsv file, max size ".number_format(MAX_TSV_SIZE)." bytes<hr>\n";

	echo "<form enctype='multipart/form-data' method='post' action='admin_games_tsv_upload.php'>\n";

	echo "<input type='file' name='game_list_tsv'><br>\n";
	echo "<input type='submit' value='Upload'>\n";
	echo "</form>\n";
}


?>
