<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');
require_once('includes/admin_menu.php');

show_admin_menu();

$id=null;
$msg='';
# GET params means want to edit an entry, so fetch it
if (isset($_GET['id']) && is_string($_GET['id'])) {
  $id=$_GET['id'];
  $s="select * from reltype where id = ?";

  $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth->bindParam(1, $id, PDO::PARAM_STR);

  if ($sth->execute()) {
    $r=$sth->fetch(PDO::FETCH_ASSOC);
    $sth->closeCursor();
    if ($r === False ) $rec=-1;
    $r['action']='edit';
  } else {
    echo "$s gave ".$dbh->errorCode()."<br>\n";
    exit(3);
  }
} else {
  # POST params mean an update or add
  if (isset($_POST) && $_POST) {
    $r['id']=$_POST['id'];
    $r['name']=$_POST['name'];
    $r['selected']=$_POST['selected'];
    $r['rel_order']=$_POST['rel_order'];
    $r['action']=$_POST['action'];
    if ( strlen($r['id']) < 1 ) {
        $msg = "ID can't be blank";
    } elseif ( strlen($r['name']) < 1 ) {
        $msg = "Name can't be blank";
    } elseif ( strlen($r['selected']) < 1 ) {
        $msg = "Selected can't be blank";
    } elseif ( strlen($r['rel_order']) < 1 ) {
        $msg = "Sort order can't be blank";
    } else {
      if ( $_POST['action'] == 'new' ) {
        $s="select count(*) from reltype where id = ?";
        $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(1, $r['id'], PDO::PARAM_STR);
	      if ( $sth->execute() && $sth->fetchColumn() >= 1 ) {
          $msg = "This ID is already used, please choose a unique ID";
        } else {
          $s="insert into reltype (id, name, selected, rel_order) values (?, ?, ?, ?)";
          $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
          $sth->bindParam(1, $r['id'], PDO::PARAM_STR);
          $sth->bindParam(2, $r['name'], PDO::PARAM_STR);
          $sth->bindParam(3, $r['selected'], PDO::PARAM_STR);
          $sth->bindParam(4, $r['rel_order'], PDO::PARAM_STR);
          if ( $sth->execute() ) {
            $id=$dbh->lastInsertId();
            $msg="New release type added: ".$r['name'].".";
          } else {
            $msg="Error adding release type";
          }
        }
      } else {
        $s="update reltype set name=?, selected=?, rel_order=? where id = ?";
        $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(1, $r['name'], PDO::PARAM_STR);
        $sth->bindParam(2, $r['selected'], PDO::PARAM_STR);
        $sth->bindParam(3, $r['rel_order'], PDO::PARAM_STR);
        $sth->bindParam(4, $r['id'], PDO::PARAM_STR);
        $sth->execute();
	      if ( $sth->execute() ) {
          $msg="Release type updated.";
        } else {
          $msg="Error updating release type";
        }
      }
    }
  } else {
    $r['name']='';
    $r['id']='';
    $r['selected']='';
    $r['rel_order']='';
    $r['action']='new';
    $msg="New release type.";
  }
}

make_form($r,$msg);

function make_form($r,$msg) {
  echo "<br><b>".$r['name']."</b>";
  echo "<hr>";
  echo "<p>$msg</p>\n";
  echo "<form name='frmGame' method='POST' action='admin_releasetypes_details.php'>\n";

  if ($r['id']=='' || $r['action']=='new') {
    echo "<label>ID: <input type='text' name='id' size='80' autofocus='autofocus' value='".$r['id']."'/></label><br/><br/>";
    echo '<input type="hidden" name="action" value="new">';
  } else {
    echo "<label>ID: <input type='text' name='id' size='80' readonly='readonly' style='border: 0' value='".htmlspecialchars($r['id'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
    echo '<input type="hidden" name="action" value="edit">';
  }

  echo "<label>Name: <input type='text' name='name' size='80' autofocus='autofocus' value='".htmlspecialchars($r['name'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Selected (on homepage): <input type='text' name='selected' size='80' autofocus='autofocus' value='".htmlspecialchars($r['selected'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Sort order (on homepage): <input type='text' name='rel_order' size='80' autofocus='autofocus' value='".htmlspecialchars($r['rel_order'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";

  echo '<br/><input type="submit" value="Submit"></form>';
  echo '<hr/><a href="admin_releasetypes.php">Back to the list</a>';
}
?>
</body>
</html>
