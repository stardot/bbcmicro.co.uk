<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');
require_once('includes/admin_menu.php');

show_admin_menu();

$id=null;
$msg='';
# GET params means want to edit an entry, so fetch it
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id=$_GET['id'];
  $s="select * from game_keys where id = ?";

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
  # POST params mean an update
  if (isset($_POST) && $_POST) {
    $r['id']=$_POST['id'];
    $r['gameid']=$_POST['gameid'];
    $r['rel_order']=$_POST['rel_order'];
    $r['keyname']=$_POST['keyname'];
    $r['keydescription']=$_POST['keydescription'];
    $r['jsbeebgamekey']=$_POST['jsbeebgamekey'];
    $r['jsbeebbrowserkey']=$_POST['jsbeebbrowserkey'];
    if ( strlen($r['gameid']) < 1 ) {
        $msg = "Game ID can't be blank";
    } elseif ( strlen($r['rel_order']) < 1 ) {
        $msg = "Key order can't be blank";
    } elseif ( strlen($r['keyname']) < 1 ) {
        $msg = "Key name can't be blank";
    } elseif ( strlen($r['keydescription']) < 1 ) {
        $msg = "Key function can't be blank";
    } else {
      if ( $r['id'] == null || $r['id'] == '' ) {
        $s="insert into game_keys (gameid, rel_order, keyname, keydescription, jsbeebgamekey, jsbeebbrowserkey) values (?, ?, ?, ?, ?, ?)";
        $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(1, $r['gameid'], PDO::PARAM_INT);
        $sth->bindParam(2, $r['rel_order'], PDO::PARAM_INT);
        $sth->bindParam(3, $r['keyname'], PDO::PARAM_STR);
        $sth->bindParam(4, $r['keydescription'], PDO::PARAM_STR);
        $sth->bindParam(5, $r['jsbeebgamekey'], PDO::PARAM_STR);
        $sth->bindParam(6, $r['jsbeebbrowserkey'], PDO::PARAM_STR);
	      if ( $sth->execute() ) {
          $id=$dbh->lastInsertId();
          $msg="New key control added: ".$id.".";
        } else {
          $msg="Error adding key control";
        }
      } else {
        $s="update game_keys set gameid=?, rel_order=?, keyname=?, keydescription=?, jsbeebgamekey=?, jsbeebbrowserkey=? where id = ?";
        $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(1, $r['gameid'], PDO::PARAM_INT);
        $sth->bindParam(2, $r['rel_order'], PDO::PARAM_INT);
        $sth->bindParam(3, $r['keyname'], PDO::PARAM_STR);
        $sth->bindParam(4, $r['keydescription'], PDO::PARAM_STR);
        $sth->bindParam(5, $r['jsbeebgamekey'], PDO::PARAM_STR);
        $sth->bindParam(6, $r['jsbeebbrowserkey'], PDO::PARAM_STR);
        $sth->bindParam(7, $r['id'], PDO::PARAM_INT);
        $sth->execute();
	      if ( $sth->execute() ) {
          $msg="Key control updated.";
        } else {
          $msg="Error updating key control";
        }
      }
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
}

make_form($r,$msg);

function make_form($r,$msg) {
  echo "<br><b>".$r['keyname']."</b>";
  echo "<hr>";
  echo "<p>$msg</p>\n";
  echo "<form name='frmGame' method='POST' action='admin_keycontrols_details.php'>\n";
  echo "<input type='hidden' name='id' value='".$r['id']."'>\n";

  echo "<label>Game ID: <input type='text' name='gameid' size='80' autofocus='autofocus' value='".htmlspecialchars($r['gameid'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Key order: <input type='text' name='rel_order' size='80' autofocus='autofocus' value='".htmlspecialchars($r['rel_order'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Key name: <input type='text' name='keyname' size='80' autofocus='autofocus' value='".htmlspecialchars($r['keyname'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Key function: <input type='text' name='keydescription' size='80' autofocus='autofocus' value='".htmlspecialchars($r['keydescription'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>JSBeeb game key: <input type='text' name='jsbeebgamekey' size='80' autofocus='autofocus' value='".htmlspecialchars($r['jsbeebgamekey'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>JSBeeb browser key: <input type='text' name='jsbeebbrowserkey' size='80' autofocus='autofocus' value='".htmlspecialchars($r['jsbeebbrowserkey'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
 
  echo '<br/><input type="submit" value="Submit"></form>';
  echo '<hr/><a href="admin_keycontrols.php">Back to the list</a>';
}
?>
</body>
</html>
