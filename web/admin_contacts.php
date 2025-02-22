<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');

require_once('includes/admin_menu.php');

show_admin_menu();

$message="";
$action="";
$filter="";
$filter_sql="WHERE status is null\n";

if (isset($_GET['filter']) && $_GET['filter'] != "") {
  $filter_sql="WHERE status='" . $_GET['filter'] . "'\n";
  $filter=$_GET['filter'];
}

if (isset($_GET['action'])) {
  $action=$_GET['action'];
  $id=$_GET['id'];
}

if ($action == "i" && preg_match('/^[0-9]+$/',$id)) {
  $s="	UPDATE 		contacts
		SET         status = null
		WHERE       id = $id";
  $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  if ($sth->execute()) {
	$message="Contact $id moved to inbox.";
	$sth->closeCursor();
  } else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
  }
}

if ($action == "b" && preg_match('/^[0-9]+$/',$id)) {
  $s="	UPDATE 		contacts
		SET         status = 'd'
		WHERE       id = $id";
  $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  if ($sth->execute()) {
	$message="Contact $id moved to bin.";
	$sth->closeCursor();
  } else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
  }
}

if ($action == "a" && preg_match('/^[0-9]+$/',$id)) {
  $s="	UPDATE 		contacts
		SET         status = 'a'
		WHERE       id = $id";
  $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  if ($sth->execute()) {
	$message="Contact $id archived.";
	$sth->closeCursor();
  } else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
  }
}

if ($action == "d" && preg_match('/^[0-9]+$/',$id)) {
  $s="	DELETE FROM contacts
		WHERE       id = $id";
  $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  if ($sth->execute()) {
	$message="Contact $id deleted.";
	$sth->closeCursor();
  } else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
  }
}

if ($message != "") {
  $message="<span style=\"margin-left:20px\"><i>$message</i></span>";
}

if ($filter == 'a') {
    $links="<a href='admin_contacts.php'>Inbox</a> | Archive | <a href='admin_contacts.php?filter=d'>Bin</a>";
} else if ($filter == 'd') {
    $links="<a href='admin_contacts.php'>Inbox</a> | <a href='admin_contacts.php?filter=a'>Archive</a> | Bin";
} else {
    $links="Inbox | <a href='admin_contacts.php?filter=a'>Archive</a> | <a href='admin_contacts.php?filter=d'>Bin</a>";
}

$s="	SELECT 		id,name,email,message,date,status
		FROM 		contacts
		$filter_sql
		ORDER BY	id desc
        LIMIT		500";

$sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
if ($sth->execute()) {
	echo '<p>'.$sth->rowCount()." contacts. $links $message</p><hr>";
	if ($sth->rowCount()) {
		echo "<table>\n";
		echo "<tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Email</b></td><td><b>Message</b></td><td><b>Date</b></td><td><b>Action</b></td></tr>\n";
		while ($r=$sth->fetch()) {
			echo "<tr><td>".$r['id']."</td>";
			echo "<td id=\"name-".$r['id']."\">".$r['name']."</td>";
			echo "<td id=\"email-".$r['id']."\">".$r['email']."</td>";
			echo "<td id=\"message-".$r['id']."\">".$r['message']."</td>";
			echo "<td>".$r['date']."</td>";
            if ($filter == 'a') {
                echo "<td><a href=\"admin_contacts.php?action=i&id=".$r['id']."&filter=$filter\">Inbox</a></td></tr>\n";
            } else if ($filter == 'd') {
                echo "<td><a href=\"admin_contacts.php?action=i&id=".$r['id']."&filter=$filter\">Inbox</a><br><br><a href=\"admin_contacts.php?action=d&id=".$r['id']."&filter=$filter\">Delete!</a></td></tr>\n";
            } else {
                echo "<td><a href=\"#\" onclick=\"copyEmail(".$r['id'].")\">Reply</a><br><br><a href=\"admin_contacts.php?action=a&id=".$r['id']."&filter=$filter\">Archive</a><br><br><a href=\"admin_contacts.php?action=b&id=".$r['id']."&filter=$filter\">Bin</a></td></tr>\n";
            }
		}
		echo "</table>\n";
	}
	$sth->closeCursor();
} else {
	echo "$s gave ".$dbh->errorCode()."<br>\n";
}

?>
<script>
function copyEmail(id) {
  event.preventDefault();
  var name = document.getElementById("name-" + id).innerText;
  var email = document.getElementById("email-" + id).innerText;
  var message = document.getElementById("message-" + id).textContent;
  var text = email + "\n\n" + name + "\n\nYou wrote:\n~~~\n" + message + "\n~~~\n";
  navigator.clipboard.writeText(text).then(function() {
    alert('Email reply copied to clipboard');
  }, function(err) {
    alert('Copy to clipboard failed');
  });
  return false;
}
</script>
<?php
?>
