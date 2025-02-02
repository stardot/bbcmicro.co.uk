<?php
function get_publisher($id) {
  global $db;

  $sql = 'select name from publishers where id = ?';
  $sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth->bindParam(1, $id, PDO::PARAM_INT);
  $sth->execute(); 
  $str=$sth->fetchColumn();
  return $str;
}

function get_genre($id) {
  global $db;

  $sql = 'select name from genres where id = ?';
  $sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth->bindParam(1, $id, PDO::PARAM_INT);
  $sth->execute(); 
  $str=$sth->fetchColumn();
  return $str;
}

function getstate() {
  $state=array();

  if ( isset($_GET["search"])) {
    $search=$_GET["search"];
    if (strlen($search) > 0 ) {
      $state['search']=$search;
    }
  }

function get_highlights() {
  global $db;
  $sql = 'select url, random, visible, colour, title, subtitle, heading, games_id, screenshot_url, position from highlights where visible = 1 order by sort_order';
  $sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth->execute(); 
  $hs = $sth->fetchAll();
  return $hs;
}

  // Tick boxes
  foreach($_GET as $k => $v ) {
    if (preg_match('/^rt_[A-Z]$/',$k)) {  // Release types checkbox (Hidden)
      $state['rtype'][] = substr($k,-1);
    }
    if (preg_match('/^ro_[A-Z]$/',$k)) {  // Release type radio (Display only)
      $ro = substr($k,-1);
    }
    if (preg_match('/^rs_[A-Z]$/',$k)) {  // Release types toggle
      $rt = substr($k,-1);
    }
    if (preg_match('/^on_[A-Z]$/',$k)) {
      $state['only'][] = substr($k,-1);
    }
    if ($k == 'f_pubid') {                // Publisher dropdown
      if ($v > 0) {
        $state['f_pubid'] = $v;
      }
    }
    if ($k == 'f_genreid') {              // Genre dropdown
      if ($v > 0) {
        $state['f_genreid'] = $v;
      }
    }
    if ($k == 'f_year1') {                // From year dropdown
      if ($v > 0) {
        $state['f_year1'] = $v;
      }
    }
    if ($k == 'f_year2') {                // To year dropdown
      if ($v > 0) {
        $state['f_year2'] = $v;
      }
    }
    if ($k == 'f_exact') {                // Exact match checkbox
      if ($v > 0) {
        $state['f_exact'] = $v;
      }
    }
    if ($k == 'f_words') {                // Match whole words checkbox
      if ($v > 0) {
        $state['f_words'] = $v;
      }
    }
  }

  if (isset($ro)) {                       // Radio - Only set this one
     $state['rtype']=array();
     $state['rtype'][]=$ro;
  } else if (isset($rt)) {                // Toggle
     if (in_array($rt,$state['rtype'])) {
       $i=array_search($rt,$state['rtype']);
       unset($state['rtype'][$i]);
       if (count($state['rtype']) == 0 ) {
         unset($state['rtype']);
       }
     } else {
       $state['rtype'][]=$rt;
     }
  }

  if ( isset($_GET["atoz"])) {
    $atoz=$_GET["atoz"];
    if (strlen($atoz) > 0 ) {
      $state["atoz"]=$atoz;
    }
  }

  if ( isset($_GET["page"])) {
    $page=intval($_GET["page"]);
    if ($page > 0 ) {
      $state["page"]=$page;
    }
  }

  // Search Order
  if ( isset($_GET["sort"])) {
    if ($_GET["sort"]=='p' || $_GET["sort"]=='a' || $_GET["sort"]=='b' || $_GET["sort"]=='u') {
      $state["sort"]=$_GET["sort"];
    }
  }

  return $state;
}
?>
