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
