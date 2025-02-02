<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');
require_once('includes/admin_menu.php');

show_admin_menu();

$id=null;
$msg='';
$showdelete=1;
# GET params means want to display an entry, so fetch it
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id=$_GET['id'];
  $s="select k.id as id, k.keydescription, k.keyname, k.jsbeebgamekey, k.jsbeebbrowserkey, k.rel_order, g.id as gameid, g.title from game_keys k inner join games g on k.gameid = g.id where k.id = ?";

  $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth->bindParam(1, $id, PDO::PARAM_STR);

  if ($sth->execute()) {
    $r=$sth->fetch(PDO::FETCH_ASSOC);
    $sth->closeCursor();
    if ($r === False ) $rec=-1;
  } else {
    echo "$s gave ".$dbh->errorCode()."<br>\n";
    exit(3);
  }
} else {
  # POST params mean delete the entry
  if (isset($_POST) && $_POST) {
    $r['id']=$_POST['id'];
    $r['gameid']=$_POST['gameid'];
    $r['title']=$_POST['title'];
    $r['rel_order']=$_POST['rel_order'];
    $r['keyname']=$_POST['keyname'];
    $r['keydescription']=$_POST['keydescription'];
    $r['jsbeebgamekey']=$_POST['jsbeebgamekey'];
    $r['jsbeebbrowserkey']=$_POST['jsbeebbrowserkey'];
    if ( strlen($r['gameid']) < 1 ) {
        $msg = "Game ID can't be blank";
    } else {
      if ( $r['id'] == null || $r['id'] == '' ) {
        $msg = "ID can't be blank";
      } else {
        $s="delete from game_keys where id = ?";
        $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(1, $r['id'], PDO::PARAM_INT);
        $sth->execute();
	      if ( $sth->execute() ) {
          $msg="Key control deleted: ".$r['keydescription']." for game ".$r['title'].".";
          $showdelete=-1;
        } else {
          $msg="Error deleting key control: ".$r['keydescription']." for game ".$r['title'].".";
        }
      }
    }
  } else {
    $r['id']='';
    $r['gameid']='';
    $r['title']='';
    $r['rel_order']='';
    $r['keyname']='';
    $r['keydescription']='';
    $r['jsbeebgamekey']='';
    $r['jsbeebbrowserkey']='';
    $msg="No ID set.";
  }
}

make_form($r,$showdelete,$msg);

function make_form($r,$showdelete,$msg) {
  echo "<br><b>".$r['keyname']."</b>";
  echo "<hr>";
  echo "<p>$msg</p>\n";
  echo "<form name='frmGame' method='POST' action='admin_keycontrols_delete.php'>\n";
  echo "<input type='hidden' name='id' value='".$r['id']."'>\n";

  echo "<label>Key ID: <input type='text' name='id' class='big-text' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['id'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Key order: <input type='text' name='rel_order' class='big-text' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['rel_order'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Key name: <input type='text' name='keyname' class='big-text' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['keyname'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Key function: <input type='text' name='keydescription' class='big-text' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['keydescription'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>JSBeeb game key: <input type='text' name='jsbeebgamekey' class='big-text' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['jsbeebgamekey'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>JSBeeb browser key: <input type='text' name='jsbeebbrowserkey' class='big-text' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['jsbeebbrowserkey'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Game ID: <input type='text' name='gameid' class='big-text' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['gameid'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Game title: <input type='text' name='title' class='big-text' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['title'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";

  if ($showdelete > 0) {
    echo '<br/><input type="submit" value="Delete"></form>';
  }

  echo '<hr/><a href="admin_keycontrols.php">Back to the list</a>';
}
?>
</body>
</html>
