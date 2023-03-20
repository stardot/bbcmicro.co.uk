<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');
require_once('includes/admin_menu.php');

show_admin_menu();

$id=null;
$msg='';
$gamecount=0;
# GET params means want to display an entry, so fetch it
if (isset($_GET['id']) && is_string($_GET['id'])) {
  $id=$_GET['id'];
  $s="select * from reltype where id = ?";

  $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth->bindParam(1, $id, PDO::PARAM_STR);

  if ($sth->execute()) {
    $r=$sth->fetch(PDO::FETCH_ASSOC);
    $sth->closeCursor();
    if ($r === False ) $rec=-1;
    $r['action']='delete';

    $s2="select * from games where reltype = ?";
    $sth2 = $dbh->prepare($s2,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth2->bindParam(1, $id, PDO::PARAM_STR);
    if ($sth2->execute()) {
      $gamecount=$sth2->rowCount();
      $sth2->closeCursor();
    } else {
      echo "$s2 gave ".$dbh->errorCode()."<br>\n";
      exit(3);
    }
  } else {
    echo "$s gave ".$dbh->errorCode()."<br>\n";
    exit(3);
  }
} else {
  # POST params mean delete it
  if (isset($_POST) && $_POST) {
    $r['id']=$_POST['id'];
    $r['name']=$_POST['name'];
    $r['selected']=$_POST['selected'];
    $r['rel_order']=$_POST['rel_order'];
    if ( strlen($r['id']) < 1 ) {
        $msg = "ID can't be blank";
    } else {
      if ( $_POST['action'] == 'delete' ) {
        $s="delete from reltype where id = ?";
        $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(1, $r['id'], PDO::PARAM_STR);
        if ( $sth->execute() ) {
          $msg="Release type deleted: ".$r['name'].".";
          $gamecount=-1;
        } else {
          $msg="Error deleting release type: ".$r['name'].".";
        }
      }
    }
  } else {
    $r['name']='';
    $r['id']='';
    $r['selected']='';
    $r['rel_order']='';
    $r['action']='new';
    $msg="No ID set.";
  }
}

make_form($r,$gamecount,$msg);

function make_form($r,$gamecount,$msg) {
  echo "<br><b>".$r['name']."</b>";
  echo "<hr>";
  echo "<p>$msg</p>\n";
  echo "<form name='frmGame' method='POST' action='admin_releasetypes_delete.php'>\n";

  echo "<label>ID: <input type='text' name='id' size='80' readonly='readonly' style='border: 0' value='".htmlspecialchars($r['id'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";

  echo "<label>Name: <input type='text' name='name' size='80' autofocus='autofocus' readonly='readonly' style='border: 0' value='".htmlspecialchars($r['name'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Selected (on homepage): <input type='text' name='selected' size='80' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['selected'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Sort order (on homepage): <input type='text' name='rel_order' size='80' autofocus='autofocus' style='border: 0' readonly='readonly' value='".htmlspecialchars($r['rel_order'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";

  if ($gamecount > 0) {
    $plural = ($gamecount > 1) ? "s" : "";
    echo "Release type is used by " . $gamecount . " game" . $plural . ".<br/><br/>";
    echo "You need to remove the release type from all games before deleting.<br/><br/>";
  } elseif ($gamecount == -1) {
  } else {
    echo '<input type="hidden" name="action" value="delete">';
    echo "Release type is not used, and is safe to delete.<br/><br/>";
    echo '<br/><input type="submit" value="Delete"></form>';
  }

  echo '<hr/><a href="admin_releasetypes.php">Back to the list</a>';
}
?>
</body>
</html>
