<?php
require('includes/admin_session.php');
require_once('includes/config.php');
require_once('includes/admin_db_open.php');
require_once('includes/admin_menu.php');

show_admin_menu();

$id=null;
$msg='';
$showdelete=1;
# GET params means want to edit an entry, so fetch it
if (isset($_GET['id']) && is_string($_GET['id'])) {
  $id=$_GET['id'];
  $s="select * from highlights where id = ?";

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
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
      $r['id']=($_POST['id'] == '0' || $_POST['id'] == '') ? null : $_POST['id'];
    } else {
      $r['id']=null;
    }
    $r['games_id']=($_POST['games_id'] == '0' || $_POST['games_id'] == '') ? null : $_POST['games_id'];
    $r['heading']=$_POST['heading'];
    $r['title']=$_POST['title'];
    $r['subtitle']=$_POST['subtitle'];
    $r['random']=($_POST['random'] == '') ? 0 : $_POST['random'];
    $r['visible']=($_POST['visible'] == '') ? 0 : $_POST['visible'];
    $r['colour']=$_POST['colour'];
    $r['url']=$_POST['url'];
    $r['screenshot_url']=$_POST['screenshot_url'];
    $r['sort_order']=($_POST['sort_order'] == '') ? 0 : $_POST['sort_order'];
    $r['position']=($_POST['position'] == '') ? 0 : $_POST['position'];
    $r['download_button']=($_POST['download_button'] == '') ? 0 : $_POST['download_button'];
    $r['play_button']=($_POST['play_button'] == '') ? 0 : $_POST['play_button'];
    $r['show_publisher']=($_POST['show_publisher'] == '') ? 0 : $_POST['show_publisher'];
    $r['show_year']=($_POST['show_year'] == '') ? 0 : $_POST['show_year'];
    $r['action']=$_POST['action'];

    if ( $r['id'] == null || $r['id'] == '' ) {
      $msg = "ID can't be blank";
    } else {
      $s="delete from highlights where id = ?";
      $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      $sth->bindParam(1, $r['id'], PDO::PARAM_INT);
      $sth->execute();
      if ( $sth->execute() ) {
        $msg="Highlight deleted: ".$r['heading'].".";
        $showdelete=-1;
      } else {
        $msg="Error deleting highlight: ".$r['heading'].".";
      }
    }
  } else {
    $r['id']='';
    $r['games_id']='';
    $r['url']='';
    $r['random']='';
    $r['visible']='';
    $r['colour']='';
    $r['title']='';
    $r['subtitle']='';
    $r['heading']='';
    $r['screenshot_url']='';
    $r['download_button']='';
    $r['play_button']='';
    $r['show_publisher']='';
    $r['show_year']='';
    $r['sort_order']='';
    $r['position']='';
    $msg="No ID set.";
  }
}

make_form($r,$showdelete,$msg);

function make_form($r,$showdelete,$msg) {
  echo "<br><b>".$r['heading']."</b>";
  echo "<hr>";
  echo "<p>$msg</p>\n";
  echo "<form name='frmGame' method='POST' action='admin_highlights_delete.php'>\n";

  if ($r['id']=='' || $r['action']=='new') {
    echo '<input type="hidden" name="action" value="new">';
  } else {
    echo "<label>ID: <input type='text' name='id' class='big-text' readonly='readonly' style='border: 0' value='".htmlspecialchars($r['id'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
    echo '<input type="hidden" name="action" value="edit">';
  }

  echo "<label>Heading: <input type='text' name='heading' class='big-text' autofocus='autofocus' readonly='readonly' value='".htmlspecialchars($r['heading'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Show on site? <input type='text' name='visible' class='big-text' autofocus='autofocus' readonly='readonly' value='".($r['visible'] == 1 ? 'Y' : 'N')."'/></label><br/><br/>";

  echo "<label>Show a random game? <input type='text' name='random' class='big-text' autofocus='autofocus' readonly='readonly' value='";
  if ($r['random'] == 1) {
      echo "Random game";
  } elseif ($r['random'] == 2) {
      echo "Lucky dip button";
  } else {
      echo "No";
  }
  echo "'/></label><br/><br/>";

  echo "<label>Sort order within search column: <input type='text' name='sort_order' size='10' autofocus='autofocus' readonly='readonly' value='".htmlspecialchars($r['sort_order'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Position within search column <input type='text' name='position' class='big-text' autofocus='autofocus' readonly='readonly' value='".($r['position'] == 1 ? 'Bottom' :'Top')."'/></label><br/><br/>";
  echo "<label>Background colour (CSS, e.g. #123456 or yellow): <input type='text' name='colour' size='10' autofocus='autofocus' readonly='readonly' value='".htmlspecialchars($r['colour'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";

  if ($r['random'] == 1) {
      echo "<p>The following have no effect when showing a random game.</p>";
  }

  $disabled = ($r['random'] == 1 ? "readonly='readonly' style='border: 0'" : '');

  echo "<label>Game ID: <input $disabled type='text' name='games_id' size='10' autofocus='autofocus' value='".htmlspecialchars($r['games_id'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Game title override: <input $disabled type='text' name='title' class='big-text' autofocus='autofocus' value='".htmlspecialchars($r['title'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Subtitle: <input $disabled type='text' name='subtitle' class='big-text' autofocus='autofocus' value='".htmlspecialchars($r['subtitle'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Link URL override: <input $disabled type='text' name='url' class='big-text' autofocus='autofocus' value='".htmlspecialchars($r['url'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Screenshot URL override: <input $disabled type='text' name='screenshot_url' class='big-text' autofocus='autofocus' value='".htmlspecialchars($r['screenshot_url'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";

  echo "<label>Show download button? <input type='text' name='download_button' class='big-text' autofocus='autofocus' readonly='readonly' value='".($r['download_button'] == 1 ? 'Yes' :'No')."'/></label><br/><br/>";
  echo "<label>Show play button? <input type='text' name='play_button' class='big-text' autofocus='autofocus' readonly='readonly' value='".($r['play_button'] == 1 ? 'Yes' :'No')."'/></label><br/><br/>";
  echo "<label>Show publisher? <input type='text' name='show_publisher' class='big-text' autofocus='autofocus' readonly='readonly' value='".($r['show_publisher'] == 1 ? 'Yes' :'No')."'/></label><br/><br/>";
  echo "<label>Show year? <input type='text' name='show_year' class='big-text' autofocus='autofocus' readonly='readonly' value='".($r['show_year'] == 1 ? 'Yes' :'No')."'/></label><br/><br/>";

  if ($showdelete > 0) {
    echo '<br/><input type="submit" value="Delete"></form>';
  }

  echo '<hr/><a href="admin_highlights.php">Back to the list</a>';
}
?>
</body>
</html>
