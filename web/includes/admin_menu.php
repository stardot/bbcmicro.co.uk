<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin tools</title>

	<style>
body { font-family: arial,helvetica,sans-serif; }
table { border-collapse: collapse; }
td { border: 1px solid black; padding: 10px; }
tr:nth-child(even) { background: #eee; }
.big-text { width: 80ch; max-width: calc(100% - 20px); }
hr { margin-bottom: 2em }
	</style>
</head>
<body>

<?php
function show_admin_menu($current_page='', $id='', $type='') {
	$space='&nbsp;&nbsp;&nbsp;';
	echo "<a href='admin_index.php'>Admin Home</a>$space";
	echo "<a href='admin_games.php'>Games List</a>$space";
	if ($current_page == 'game_details') {
    	echo "<a href='/admin_file.php?t=s&id=" . $id . "'>Edit screenshot</a>$space";
    	echo "<a href='/admin_file.php?t=d&id=" . $id . "'>Edit disc image</a>$space";
    	echo "<a href='/game.php?id=" . $id . "'>Public view</a>$space";
	}
	if ($current_page == 'file') {
    	echo "<a href='/admin_game_details.php?t=s&id=" . $id . "'>Edit game details</a>$space";
		if ($type == 'd') {
	    	echo "<a href='/admin_file.php?t=s&id=" . $id . "'>Edit screenshot</a>$space";
		}
		if ($type == 's') {
	    	echo "<a href='/admin_file.php?t=d&id=" . $id . "'>Edit disc image</a>$space";
		}
    	echo "<a href='/game.php?id=" . $id . "'>Public view</a>$space";
	}
	echo "<a href='logout.php'>Logout</a>";
	echo "<br>\n";
}
?>
