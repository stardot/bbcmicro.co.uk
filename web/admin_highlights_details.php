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
    if ( $_POST['action'] == 'new' ) {
      $s="insert into highlights (games_id, url, random, visible, colour, title, subtitle, heading, screenshot_url, sort_order, position, download_button, play_button, show_publisher, show_year) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      $sth->bindParam(1, $r['games_id'], PDO::PARAM_STR);
      $sth->bindParam(2, $r['url'], PDO::PARAM_STR);
      $sth->bindParam(3, $r['random'], PDO::PARAM_STR);
      $sth->bindParam(4, $r['visible'], PDO::PARAM_STR);
      $sth->bindParam(5, $r['colour'], PDO::PARAM_STR);
      $sth->bindParam(6, $r['title'], PDO::PARAM_STR);
      $sth->bindParam(7, $r['subtitle'], PDO::PARAM_STR);
      $sth->bindParam(8, $r['heading'], PDO::PARAM_STR);
      $sth->bindParam(9, $r['screenshot_url'], PDO::PARAM_STR);
      $sth->bindParam(10, $r['sort_order'], PDO::PARAM_STR);
      $sth->bindParam(11, $r['position'], PDO::PARAM_STR);
      $sth->bindParam(12, $r['download_button'], PDO::PARAM_STR);
      $sth->bindParam(13, $r['play_button'], PDO::PARAM_STR);
      $sth->bindParam(14, $r['show_publisher'], PDO::PARAM_STR);
      $sth->bindParam(15, $r['show_year'], PDO::PARAM_STR);
      if ( $sth->execute() ) {
        $id=$dbh->lastInsertId();
        $msg="New highlight added: ".$id.".";
        $r['id']=$id;
        $r['action']='edit';
      } else {
        $msg="Error adding highlight";
      }
    } else {
      $s="update highlights set games_id=?, url=?, random=?, visible=?, colour=?, title=?, subtitle=?, heading=?, screenshot_url=?, sort_order=?, position=?, download_button=?, play_button=?, show_publisher=?, show_year=? where id = ?";
      $sth = $dbh->prepare($s,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      $sth->bindParam(1, $r['games_id'], PDO::PARAM_STR);
      $sth->bindParam(2, $r['url'], PDO::PARAM_STR);
      $sth->bindParam(3, $r['random'], PDO::PARAM_STR);
      $sth->bindParam(4, $r['visible'], PDO::PARAM_STR);
      $sth->bindParam(5, $r['colour'], PDO::PARAM_STR);
      $sth->bindParam(6, $r['title'], PDO::PARAM_STR);
      $sth->bindParam(7, $r['subtitle'], PDO::PARAM_STR);
      $sth->bindParam(8, $r['heading'], PDO::PARAM_STR);
      $sth->bindParam(9, $r['screenshot_url'], PDO::PARAM_STR);
      $sth->bindParam(10, $r['sort_order'], PDO::PARAM_STR);
      $sth->bindParam(11, $r['position'], PDO::PARAM_STR);
      $sth->bindParam(12, $r['download_button'], PDO::PARAM_STR);
      $sth->bindParam(13, $r['play_button'], PDO::PARAM_STR);
      $sth->bindParam(14, $r['show_publisher'], PDO::PARAM_STR);
      $sth->bindParam(15, $r['show_year'], PDO::PARAM_STR);
      $sth->bindParam(16, $r['id'], PDO::PARAM_STR);
      $sth->execute();
      if ( $sth->execute() ) {
        $msg="Highlight updated.";
      } else {
        $msg="Error updating highlight";
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
    $r['action']='new';
    $msg="New highlight.";
  }
}

make_form($r,$msg);

function make_form($r,$msg) {
  echo "<br><b>".$r['heading']."</b>";
  echo "<hr>";
  echo "<p>$msg</p>\n";
  echo "<form name='frmGame' method='POST' action='admin_highlights_details.php'>\n";

  if ($r['id']=='' || $r['id']==0 || $r['action']=='new') {
    echo '<input type="hidden" name="action" value="new">';
  } else {
    echo "<label>ID: <input type='text' name='id' class='big-text' readonly='readonly' style='border: 0' value='".htmlspecialchars($r['id'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
    echo '<input type="hidden" name="action" value="edit">';
  }

  echo "<label>Heading: <input type='text' name='heading' class='big-text' autofocus='autofocus' value='".htmlspecialchars($r['heading'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Show on site? <select name='visible'><option value='0'" . ($r['visible'] != 1 ? ' selected' : '') . ">No</option><option value='1'" . ($r['visible'] == 1 ? ' selected' : '') . ">Yes</option></select></label><br/><br/>";
  echo "<label>Show a random game? <select name='random'><option value='0'" . (($r['random'] != 1 && $r['random'] != 2) ? ' selected' : '') . ">No</option>";
  echo "<option value='1'" . ($r['random'] == 1 ? ' selected' : '') . ">Random game (all games)</option>";
  echo "<option value='3'" . ($r['random'] == 3 ? ' selected' : '') . ">Random game (no lost games)</option>";
  echo "<option value='4'" . ($r['random'] == 4 ? ' selected' : '') . ">Random game (lost games only)</option>";
  echo "<option value='2'" . ($r['random'] == 2 ? ' selected' : '') . ">Lucky dip button</option></select></label><br/><br/>";
  echo "<label>Sort order within search column: <input type='text' name='sort_order' size='10' autofocus='autofocus' value='".htmlspecialchars($r['sort_order'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Position within search column <select name='position'><option value='0'" . ($r['position'] != 1 ? ' selected' : '') . ">Top</option><option value='1'" . ($r['position'] == 1 ? ' selected' : '') . ">Bottom</option></select></label><br/><br/>";
  echo "<label>Background colour (CSS, e.g. #123456 or yellow): <input type='text' name='colour' size='10' autofocus='autofocus' value='".htmlspecialchars($r['colour'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";

  if ($r['random'] == 1) {
      echo "<p>The following have no effect when showing a random game.</p>";
  }

  $disabled = ($r['random'] == 1 ? "readonly='readonly' style='border: 0'" : '');

  echo "<label>Game ID: <input $disabled type='text' name='games_id' size='10' autofocus='autofocus' value='".htmlspecialchars($r['games_id'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Game title override: <input $disabled type='text' name='title' class='big-text' autofocus='autofocus' value='".htmlspecialchars($r['title'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Subtitle: <input $disabled type='text' name='subtitle' class='big-text' autofocus='autofocus' value='".htmlspecialchars($r['subtitle'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Link URL override: <input $disabled type='text' name='url' class='big-text' autofocus='autofocus' value='".htmlspecialchars($r['url'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";
  echo "<label>Screenshot URL override: <input $disabled type='text' name='screenshot_url' class='big-text' autofocus='autofocus' value='".htmlspecialchars($r['screenshot_url'] ?? '',ENT_QUOTES)."'/></label><br/><br/>";

  echo "<label>Show download button? <select name='download_button'><option value='0'" . ($r['download_button'] != 1 ? ' selected' : '') . ">No</option><option value='1'" . ($r['download_button'] == 1 ? ' selected' : '') . ">Yes</option></select></label><br/><br/>";
  echo "<label>Show play button? <select name='play_button'><option value='0'" . ($r['play_button'] != 1 ? ' selected' : '') . ">No</option><option value='1'" . ($r['play_button'] == 1 ? ' selected' : '') . ">Yes</option></select></label><br/><br/>";
  echo "<label>Show publisher? <select name='show_publisher'><option value='0'" . ($r['download_button'] != 1 ? ' selected' : '') . ">No</option><option value='1'" . ($r['show_publisher'] == 1 ? ' selected' : '') . ">Yes</option></select></label><br/><br/>";
  echo "<label>Show year? <select name='show_year'><option value='0'" . ($r['download_button'] != 1 ? ' selected' : '') . ">No</option><option value='1'" . ($r['show_year'] == 1 ? ' selected' : '') . ">Yes</option></select></label><br/><br/>";

  echo '<br/><input type="submit" value="Submit"></form>';
  echo '<hr/><a href="admin_highlights.php" style="display: block; margin-bottom: 20ch">Back to the list</a>';
}
?>
</body>
</html>
