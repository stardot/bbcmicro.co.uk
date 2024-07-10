<?php
function get_scrshot($file,$subdir) {
  $di='gameimg/screenshots/default.jpg';
  if ($subdir === NULL or $subdir === '') {
    $imgfile = 'gameimg/screenshots/' . $file;
  } else {
    $imgfile = 'gameimg/screenshots/' . $subdir . '/' . $file;
  }
  if ($file === NULL || $file === '') {
    $imgfile=$di;
  }
  if (!file_exists($imgfile)) {
    $imgfile=$di;
  }
  return $imgfile;
}

function get_playlink($image,$jsbeeb,$wsroot,$keys,$platform) {
  $jsbdisc=$jsbeeb . '?autoboot&disc=';
  $jsbtape=$jsbeeb . '?autochain&tape=';
  $url = Null;
  if (($image['customurl'] ?? '') === '' ) {
    $ssd=get_discloc($image['filename'] ?? '',$image['subdir'] ?? '');
    if (isset($ssd) && file_exists($ssd)) {
      $file_parts = pathinfo($ssd);
      if (strtolower($file_parts['extension']) == 'uef') {
        $url = $jsbtape . $wsroot . '/' . $ssd;
      } else {
        $url = $jsbdisc . $wsroot . '/' . $ssd;
      }
    }
  } else {
    $url = str_replace('%jsbeeb%',$jsbdisc,$image['customurl']);
    $url = str_replace('%wsroot%',$wsroot,$url);
  }
  //Add key controls
  $keyurl='';
  foreach ($keys as $key) {
    if ($key["jsbeebbrowserkey"] && $key["jsbeebgamekey"]) {
      $keyurl .= "&KEY." . $key["jsbeebbrowserkey"] . "=" . $key["jsbeebgamekey"];
    }
  }
  if (!is_null($platform) && $platform !== '' && $platform !== '0') {
    $copro=[ 'Master' => '', 'MasterTurbo' => '&coProcessor=true', 'B-Tube' => '&coProcessor=true', 'b-dfs1.2' => '', 'B1770' => ''];
    $model=[ 'Master' => '&model=Master', 'MasterTurbo' => '&model=Master', 'B-Tube' => '', 'b-dfs1.2' => '&model=B-DFS1.2', 'B1770' => '&model=B1770'];

    if (array_key_exists($platform, $copro)) {
      $keyurl .= $copro[$platform] . $model[$platform];
    }
  }
  //Stop disc-operating sounds
  if ($url === NULL ) {} else { $url.=$keyurl."&noseek"; }
  return $url;
}

function get_discloc($file,$subdir) {
  $di = Null;
  if ($subdir === NULL or $subdir === '') {
    $imgfile = 'gameimg/discs/' . $file;
  } else {
    $imgfile = 'gameimg/discs/' . $subdir . '/' . $file;
  }
  if ($file === NULL || $file === '') {
    $imgfile=$di;
  }
  if (isset($imgfile) && !file_exists($imgfile)) {
    $imgfile=$di;
  }
  return $imgfile;
}
?>
