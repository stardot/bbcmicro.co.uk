<?php
require 'includes/config.php';
require 'includes/db_connect.php';

header('Content-type: application/csv');
header('Content-Disposition: inline; filename=bbcmicro.co.uk.csv');

echo "\"ID\",\"Publishers(s)\",\"Title\",\"Year\",\"PrimaryGenre\",\"SecondaryGenre(s)...\"\n";
$s="	SELECT 		games.id AS the_game_id,
			title,
			year,
			genres.name AS the_genre_name 

	FROM 		games 
	LEFT JOIN 	genres ON games.genre=genres.id";
#	LIMIT 		100";
#	WHERE 		games.id=4018";
foreach ($db->query($s) as $row) {
	#print_r($row);
	$game_id=$row['the_game_id'];
	echo $game_id.',';

	# Can have multiple publishers
	$ps="SELECT name FROM games_publishers LEFT JOIN publishers ON publishers.id=games_publishers.pubid WHERE games_publishers.gameid=$game_id";
	$publishers='';
	foreach ($db->query($ps) as $pub_row) {
		$publishers.=$pub_row['name'].';';
	}
	if ($publishers) $publishers=substr($publishers,0,strlen($publishers)-1);
	echo '"'.$publishers.'",';

	echo '"'.$row['title'].'",';
	echo '"'.$row['year'].'",';
	echo '"'.$row['the_genre_name'].'"';

	# And run any secondary genres "off the end"
	$genres='';
	$gs="SELECT name FROM game_genre LEFT JOIN genres ON genres.id=game_genre.genreid WHERE game_genre.gameid=$game_id ORDER BY ord";
	foreach ($db->query($gs) as $genre_row) {
		$genres.='"'.$genre_row['name'].'",';
	}
	if ($genres) echo ','.substr($genres,0,strlen($genres)-1);
	
	echo "\n";
}
?>
