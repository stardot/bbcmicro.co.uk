<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');
require_once('includes/admin_menu.php');

show_admin_menu();

$id=null;
$msg='';
# GET params means want to edit a name ...
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id=intval($_GET['id']);
} else {
  # POST params mean an update
  if (isset($_POST) && $_POST) {
    $r['id']=$_POST['id'];
    $gameid=$_POST['gameid'];
    $rel_order=$_POST['rel_order'];
    $keyname=$_POST['keyname'];
    $keydescription=$_POST['keydescription'];
    $jsbeebgamekey=$_POST['jsbeebgamekey'];
    $jsbeebbrowserkey=$_POST['jsbeebbrowserkey'];
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
      $id=intval($_POST['id']);
    } else {
      $id=null;
    }
    if ( strlen($keyname) < 1 ) {
        $msg = "Key name can't be blank";
    } else {
      if ( $id == null ) {
        $s="insert into game_keys (gameid, rel_order, keyname, keydescription, jsbeebgamekey, jsbeebbrowserkey) values (?, ?, ?, ?, ?, ?)";
        $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(1, $gameid, PDO::PARAM_INT);
        $sth->bindParam(2, $rel_order, PDO::PARAM_INT);
        $sth->bindParam(3, $keyname, PDO::PARAM_STR);
        $sth->bindParam(4, $keydescription, PDO::PARAM_STR);
        $sth->bindParam(5, $jsbeebgamekey, PDO::PARAM_STR);
        $sth->bindParam(6, $jsbeebbrowserkey, PDO::PARAM_STR);
	      if ( $sth->execute() ) {
          $id=$dbh->lastInsertId();
          $msg="New key control added: ".$id.".";
        } else {
          $msg="Error adding key control";
        }
      } else {
        $s="update game_keys set gameid=?, rel_order=?, keyname=?, keydescription=?, jsbeebgamekey=?, jsbeebbrowserkey=? where id = ?";
        $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(1, $gameid, PDO::PARAM_INT);
        $sth->bindParam(2, $rel_order, PDO::PARAM_INT);
        $sth->bindParam(3, $keyname, PDO::PARAM_STR);
        $sth->bindParam(4, $keydescription, PDO::PARAM_STR);
        $sth->bindParam(5, $jsbeebgamekey, PDO::PARAM_STR);
        $sth->bindParam(6, $jsbeebbrowserkey, PDO::PARAM_STR);
        $sth->bindParam(7, $id, PDO::PARAM_INT);
        $sth->execute();
	      if ( $sth->execute() ) {
          $msg="Key control updated.";
        } else {
          $msg="Error updating key control";
        }
      }
    }
  }
}

if ($id > 0) {
  $s="select * from game_keys where id = ?";

  $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth->bindParam(1, $id, PDO::PARAM_INT);

  if ($sth->execute()) {
    $r=$sth->fetch(PDO::FETCH_ASSOC);
    $sth->closeCursor();
    if ($r === False ) $rec=-1;
  } else {
    echo "$s gave ".$dbh->errorCode()."<br>\n";
    exit(3);
  }
} else {
  $r['id']='';
  $r['gameid']='';
  $r['rel_order']='';
  $r['keyname']='';
  $r['keydescription']='';
  $r['jsbeebgamekey']='';
  $r['jsbeebbrowserkey']='';
  $msg="New key control.";
}

make_form($r,$msg);

function make_form($r,$msg) {
  echo "<br><b>".$r['keyname']."</b>";
  echo "<hr>";
  echo "<p>$msg</p>\n";
  echo "<form name='frmGame' method='POST' action='admin_keycontrols_details.php'>\n";
  echo "<input type='hidden' name='id' value='".$r['id']."'>\n";

  echo "<label>Game ID: <input type='text' name='gameid' size='80' autofocus='autofocus' value='".htmlspecialchars($r['gameid'],ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Order: <input type='text' name='rel_order' size='80' autofocus='autofocus' value='".htmlspecialchars($r['rel_order'],ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Key name: <input type='text' name='keyname' size='80' autofocus='autofocus' value='".htmlspecialchars($r['keyname'],ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Key description: <input type='text' name='keydescription' size='80' autofocus='autofocus' value='".htmlspecialchars($r['keydescription'],ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>JSBeeb game key: <input type='text' name='jsbeebgamekey' size='80' autofocus='autofocus' value='".htmlspecialchars($r['jsbeebgamekey'],ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>JSBeeb browser key: <input type='text' name='jsbeebbrowserkey' size='80' autofocus='autofocus' value='".htmlspecialchars($r['jsbeebbrowserkey'],ENT_QUOTES)."'/></label><br/><br/>";
 
  echo '<br/><input type="submit" value="Submit"></form>';
  echo '<hr/><a href="admin_keycontrols.php">Back to the list</a>';
}
?>
</body>
</html>
