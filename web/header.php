<?php
function htmlhead() {
global $site_name;
?><!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico" type="image/png">
    <link type="image/png" rel="shortcut icon" href="favicon.ico"/>

    <title><?php echo $site_name?></title>

    <!-- Bootstrap core CSS -->
    <link href="bs/css/bootstrap.min.css" rel="stylesheet">
    <link href="bs/css/bootstrap-theme.min.css" rel="stylesheet">
    <link rel="stylesheet" href="bs/offcanvas.css">
    <link rel="stylesheet" href="bs/css/typeahead.css">
    <link rel="stylesheet" href="bs/css/grid.css">

    <!-- Local CSS -->
    <link rel="stylesheet" href="css/custom.css?v=<?php echo CSSV ?>">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body><?php
}

function nav() {
global $site_name_html;
?>
 <nav class="navbar navbar-fixed-top navbar-inverse">
  <div class="container">
   <div class="navbar-header">
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
     <span class="sr-only">Toggle navigation</span>
     <span class="icon-bar"></span>
     <span class="icon-bar"></span>
     <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="index.php"><?php echo $site_name_html?></a>
   </div>
   <?php make_menu_bar("Games")?>
  </div><!-- /.container -->
 </nav><!-- /.navbar -->
<?php
}


function sidebar($state, $highlights) {
?>   <div class="col-xs-3 col-sm-3 col-md-3 col-lg-2 sidebar-offcanvas" id="sidebar" style="margin-top: 20px">
<?php
  if (!array_key_exists('search',$state)) {
    highlights($highlights);
  }
  searchbox($state);
  if (array_key_exists('search',$state)) {
    refines($state);
    filters($state);
  }
  searchbuttons();
  if (!array_key_exists('search',$state)) {
    randomgame();
  }
  echo "    </div>\r";
}

function highlights($highlights) {
  global $db;

  foreach ($highlights as $h) {

    if ($h['random'] == 1) {
      $rndsql = 'select id from games order by rand() limit 1';
      $rndpdo = $db->prepare($rndsql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      if ($rndpdo->execute()) {
        $random_game=$rndpdo->fetch(PDO::FETCH_ASSOC);
        $game_id = $random_game['id'];
      } else {
        echo "Error:";
      }
    } else {
      $game_id = $h['games_id'];
    }

    $gamsql = 'select id, title_article, title, year, jsbeebplatform from games where id = :gameid';
    $scrsql = 'select filename, subdir from screenshots where gameid = :gameid order by main, id limit 1';
    $dscsql = 'select filename, subdir, customurl, probs from images where gameid = :gameid order by main, id limit 1';
    $pubsql = 'select id,name from publishers where id in (select pubid from games_publishers where gameid = :gameid)';
    $keysql = "select jsbeebbrowserkey,jsbeebgamekey from game_keys where gameid = :gameid order by rel_order";

    $gampdo = $db->prepare($gamsql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $scrpdo = $db->prepare($scrsql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $dscpdo = $db->prepare($dscsql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $pubpdo = $db->prepare($pubsql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $keypdo = $db->prepare($keysql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

    $gampdo->bindParam(':gameid',$game_id, PDO::PARAM_INT);
    $scrpdo->bindParam(':gameid',$game_id, PDO::PARAM_INT);
    $dscpdo->bindParam(':gameid',$game_id, PDO::PARAM_INT);
    $pubpdo->bindParam(':gameid',$game_id, PDO::PARAM_INT);
    $keypdo->bindParam(':gameid',$game_id, PDO::PARAM_INT);

    if ($scrpdo->execute()) {
      $img=$scrpdo->fetch(PDO::FETCH_ASSOC);
      $shot = get_scrshot($img['filename'] ?? '',$img['subdir'] ?? '');
    } else {
	    echo "$scrsql gave ".$db->errorCode()."<br>\n";
    }

    if ($dscpdo->execute()) {
      $dnl=$dscpdo->fetch(PDO::FETCH_ASSOC);
    } else {
	    echo "$dscsql gave ".$db->errorCode()."<br>\n";
    }

    $pubs='';
    if ($pubpdo->execute()) {
      while($pub=$pubpdo->fetch(PDO::FETCH_ASSOC)) {
        $t=preg_split('/[,(]/',$pub['name']);
        $u=htmlspecialchars(trim($t[0] ?? ''));
        $pubs=$pubs.$u.', ';
      }
    } else {
	    echo "$pubsql gave ".$db->errorCode()."<br>\n";
    }
    $pubs=trim($pubs,', ');

    if ($keypdo->execute()) {
      $keys=$keypdo->fetchAll();
    } else {
	    echo "$keysql gave ".$db->errorCode()."<br>\n";
      $keys=array();
    }

    if ($gampdo->execute()) {
      $game=$gampdo->fetch(PDO::FETCH_ASSOC);
    } else {
	    echo "$gamsql gave ".$db->errorCode()."<br>\n";
    }

    highlightitem($h, $game["id"],htmlspecialchars($game["title_article"] ?? ''),htmlspecialchars($game["title"] ?? ''), $shot, $dnl ,$pubs,$game["year"],$keys,$game["jsbeebplatform"]);
  }
}

function highlightitem( $h, $id, $ta, $name, $image, $img, $publisher, $year, $keys, $platform) {
  $jsbeeb=JB_LOC;
  $root=WS_ROOT;

  if ($h['title'] && strlen($h['title'] && $h['random']) != 1) {
    $title=$h['title'];
  } else {
    $split=explode('(',$name);
    $title=trim($split[0]);
    if (strlen($ta)>0){
      $title=$ta.' '.$title;
    }
  }

  if ($h['url'] && strlen($h['url']) > 0) {
    $url=$h['url'];
  } else {
    $url='game.php?id='.$id;
  }

  if ($h['screenshot_url'] && strlen($h['screenshot_url']) > 0) {
    $image=$h['screenshot_url'];
  }

  if ($h['colour'] && strlen($h['colour']) > 0) {
    $background=" style='background-color: " . $h['colour'] . "'";
  } else {
    $background='';
  }

  // Taken from gameitem()
  $ssd = get_discloc($img["filename"] ?? '',$img['subdir'] ?? '');
?>
      <div class="thumbnail text-center" style="margin-bottom: 1em" <?php echo $background; ?>>
       <h4 style="margin-top: 0"><?php echo $h['heading']; ?></h4>
       <a href="<?php echo $url; ?>"><img src="<?php echo $image; ?>" alt="<?php echo $image; ?>" class="pic"></a>
       <div class="row-title" style="height: auto; margin-bottom: 0.25em"><span class="row-title"><a href="<?php echo $url; ?>"><?php echo $title ?></a></span></div>
       <div class="row-pub" style="height: auto; font-size: 0.85em; margin-bottom: 1em"><?php echo $publisher ?> (<?php echo $year; ?>)</div>
<?php
  if ($h['subtitle'] && strlen($h['subtitle']) > 0) {
?>
       <div class="row-subtitle" style="margin-bottom: 1em"><span class="row-subtitle"><?php echo $h['subtitle'] ?></span></div>
<?php
  }
  $playlink=get_playlink($img,$jsbeeb,$root,$keys,$platform);
  if ($ssd != null && file_exists($ssd)) { ?>
       <p><a href="<?php echo $ssd ?>" type="button" onmousedown="log(<?php echo $id; ?>);" class="btn btn-default">Download</a></p><?php
  }
  if ((($img['probs'] ?? '') != 'N' and ($img['probs'] ?? '') != 'P') and $playlink != null) { ?>
          <p><a id="plybtn" href="<?php echo $playlink ?>" type="button" onmousedown="logPlay(<?php echo $id; ?>);" class="btn btn-default">Play</a></p>
<?php
  }
?>
      </div>
<?php
}

function searchbox($state) {
  if (array_key_exists('search',$state)) {
    $search = htmlspecialchars($state['search'] ?? '',ENT_QUOTES);
  } else {
    $search= "";
  }
?>
     <fieldset class="form-group" id="search">
      <label for="search"><h3>Search</h3></label>
      <input id="searchbox" name="search" class="typeahead form-control" type="text" placeholder="Search" oninput="clearFilters()" value="<?php echo $search ; ?>" />
     </fieldset>
     <fieldset class="form-group" id="order">
<?php
}

function randomgame() {
?>     <p>&nbsp;</p><h3>Random Game</h3>
       <p><a href="q/random.php" class="btn btn-default btn-lg btn-block">Lucky Dip</a></p>
<?php
}

function refines($state) {
  $checked='';
  if (array_key_exists ('f_words', $state)) {
    if ($state['f_words'] > 0) {
      $checked='checked';
    }
  }
 ?>
      <div class="checkbox" style="margin-top: 0">
       <label><input type="checkbox" name="f_words" <?php echo $checked ?> value="1"/>Match whole words</label>
      </div>
<?php
  $checked='';
  if (array_key_exists ('f_exact', $state)) {
    if ($state['f_exact'] > 0) {
      $checked='checked';
    }
  }
 ?>
      <div class="checkbox" style="margin-top: 0">
       <label><input type="checkbox" name="f_exact" <?php echo $checked ?> value="1"/>Match full search term</label>
      </div>
     <h4 style="margin-top:3ch">Only include matches on:</h4>
<?php
  $types=array('T'=>'Title','Y'=>'Year','P'=>'Publisher','A'=>'Author','G'=>'Primary Genre','S'=>'Secondary Genre','Z'=>'Series','C'=>'Compilation');
  foreach ( $types as $tid => $type ) {
    $checked='';
    if (array_key_exists('only',$state) && count($state['only'])==0){
      $checked='checked';
    } else {
      if (array_key_exists('only',$state) && array_search($tid,$state['only'])===False) {
        ;
      }else{
        $checked='checked';
      }
    }
?>
      <div class="checkbox">
       <label><input type="checkbox" name="on_<?php echo $tid; ?>" <?php echo $checked ?>/><?php echo $type ?></label>
      </div>
<?php
  }
 ?>

     <h4 style="margin-top:3ch">Only include release types:</h4>

<?php

  $reltyps=get_reltypes();
  foreach ( $reltyps as $reltyp ) {
    $checked='';
    $active=' btn-default';
    if (!array_key_exists('rtype',$state) || count($state['rtype'])==0){
        if ($reltyp['selected'] == 'Y') {
          $checked='  <input type="hidden" name="rt_' . $reltyp['id'] .'">';
          $active=" checked='checked'";
        }
    } else {
        if (array_key_exists('rtype',$state) && array_search($reltyp['id'],$state['rtype'])===False) {
          ;
        }else{
          $checked='  <input type="hidden" name="rt_' . $reltyp['id'] .'">';
          $active=" checked='checked'";
        }
    }
?>
      <div class="checkbox">
       <label><input type="checkbox" name="rt_<?php echo $reltyp['id']; ?>" <?php echo $active ?>/><?php echo $reltyp['name'] ?></label>
      </div>
<?php
   }

}


function searchbuttons() {
  global $state;

  $s='b'; // Default sort order - Releases. 
  $sortbtn='';
  if (isset($state['sort'])) {
    $sortbtn='name="sort'.$state['sort'].'"';
    $s=$state['sort'];
  }
  $sel='<span style="float:right">&#10004;</span>';
?>
<br/>
<h4>Sort by:</h4>
<label style="font-weight: 400"><input type="radio" name="sort" value="p" <?php if ($s=="p") echo "checked"; ?>/> Popularity</label><br/>
<label style="font-weight: 400"><input type="radio" name="sort" value="a" <?php if ($s=="a") echo "checked"; ?>/> Title (A-Z)</label><br/>
<label style="font-weight: 400"><input type="radio" name="sort" value="u" <?php if ($s=="u") echo "checked"; ?>/> Last Updated</label><br/>
<label style="font-weight: 400"><input type="radio" name="sort" value="b" <?php if ($s=="b") echo "checked"; ?>/> Release Date</label><br/>
<br/>
<div class="btn-group btn-block">
  <button type="submit" class="btn btn-default btn-lg btn-block">Search</button>
</div> 
<?php
}

function containstart($state) {
?>
 <form id="searchform" action="index.php" method="get">
 <div class="container">
  <div class="row row-offcanvas row-offcanvas-right">
   <div class="col-xs-12 col-sm-9 col-md-9 col-lg-10">
    <p class="pull-right visible-xs">
     <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Search</button>
    </p><?php // Add a button to catch searches using the enter key ?>
    <button style="overflow: visible !important; height: 0 !important; width: 0 !important; margin: 0 !important; border: 0 !important; padding: 0 !important; display: block !important;" type="submit"></button>
<?php
}

function containend() { 
?>
  </div><!-- Container -->
 </div>
 </form>
<?php
}

function htmlfoot() {
?>

 <script src="bs/jquery.min.js"></script>
 <script src="bs/js/bootstrap.min.js"></script>
 <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
 <script src="bs/js/ie10-viewport-bug-workaround.js"></script>
 <script src="bs/offcanvas.js"></script>
 <script src="bs/js/typeahead.js"></script>
 <script>
<?php // Set up the typeahead search ?>
$(document).ready(function() {
  var suggestions = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: 'q?qt=suggestions&qv=%QUERY%',
      wildcard: '%QUERY'
    }
  });

  $('#search .typeahead').typeahead(null, {
    name: 'suggestions',
    displayKey: 'title',
    source: suggestions,
    matcher: function (t) {
        return t;
    }
  });
});
// Log downloads.
function log(a) {
  var i = document.createElement("img");
  i.src = "count.php?t=d&id="+a;
  return true;
}
// Log plays.
function logPlay(a) {
  var i = document.createElement("img");
  i.src = "count.php?t=g&id="+a;
  return true;
}
function resetFilters() {
  resetOption("f_pubid");
  resetOption("f_genreid");
  resetOption("f_year1");
  resetOption("f_year2");
}
function clearFilters() {
  removeOptions("f_pubid");
  removeOptions("f_genreid");
  removeOptions("f_year1");
  removeOptions("f_year2");
}
function removeOptions(id) {
  var list = document.getElementById(id);
  list.value = "0";
  var i, L = list.options.length - 1;
  if (L > 0) {
    for(i = L; i >= 1; i--) {
      list.remove(i);
    }
  }
}
function resetOption(id) {
  var list = document.getElementById(id);
  list.value = "0";
}
  </script>
<?php include_once("includes/googleid.php") ?>
</body>
</html><?php
}

