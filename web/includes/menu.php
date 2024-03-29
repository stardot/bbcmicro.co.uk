<?php
function make_menu_bar($active_tab,$lprefix='') {
	echo "<div id=\"navbar\" class=\"collapse navbar-collapse\">\n";
	echo "<ul class=\"nav navbar-nav\">\n";
	foreach (array	(	"Games"=>$lprefix."index.php",
				"About"=>$lprefix."about.php",
				"Links"=>$lprefix."links.php",
				"Contact"=>$lprefix."contact.php",
			) as $name=>$target) {
		$class=($active_tab==$name)?" class=\"active\"":'';
		echo "<li$class><a href=\"$target\">$name</a></li>\n";
	}
	if ($active_tab == 'Games') {
		ini_set('session.cookie_httponly', True);
		session_start();
		if (array_key_exists('bbcmicro',$_SESSION) && isset($_GET["id"])) {
			echo "<li><a href=\"/admin_game_details.php?id=" . $_GET["id"] . "\">Edit</a></li>\n";
		}
	}
	echo "</ul>\n";
	echo "<a href=\"" . $lprefix . "about.php\"><img src=\"/css/BBC_Micro.png\" class=\"bbc-micro-button\" alt=\"BBC Micro\"></a>";
	echo "</div><!-- /.nav-collapse -->\n";
}
?>
