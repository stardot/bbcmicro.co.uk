<?php
require 'playlink.php';

function get_reltypes() {
  global $db;

  $sql = "select distinct id, name, selected from reltype order by rel_order";
  $sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  if ($sth->execute()) {
    $res = $sth->fetchAll();
  } else {
    echo "Error:";
    echo "\n";
    $sth->debugDumpParams ();
    $res=array();
  }
  return $res;
}


function reltypes($state) {
?>
     <h4>Browse release types:</h4>
      <div id="reltypes">
<?php
   $reltyps=get_reltypes();
   foreach ( $reltyps as $reltyp ) {
      $checked='';
      $active=' btn-default';
      if (!array_key_exists('rtype',$state) || count($state['rtype'])==0){
         if ($reltyp['selected'] == 'Y') {
            $checked='  <input type="hidden" name="rt_' . $reltyp['id'] .'">';
            $active=' btn-primary';
         }
      } else {
         if (array_key_exists('rtype',$state) && array_search($reltyp['id'],$state['rtype'])===False) {
            ;
         }else{
            $checked='  <input type="hidden" name="rt_' . $reltyp['id'] .'">';
            $active=' btn-primary';
         }
      }
?>

<div class="btn-group" style="margin-bottom:5px;">
  <button type="submit" name="rs_<?php echo $reltyp['id']; ?>" title="Toggle <?php echo $reltyp['name'] ?>" class="btn<?php echo $active; ?>"><?php echo $reltyp['name'] ?></button>
  <button type="submit" name="ro_<?php echo $reltyp['id']; ?>" title="Select only <?php echo $reltyp['name'] ?>" class="btn<?php echo $active; ?>">&#9675;</button></div>
<?php echo $checked; 
   }
   echo "      </div>";
}

function atoz_line($current,$chars,$margin) {
  global $state;

  echo "<div>";
  echo "<ul style=\"margin-$margin:0;\" class=\"pagination\">";
  foreach ($chars as $char) {
    $active=($current==$char)?' class="active"':'';
    $chet=$char; if (empty($chet)) { $chet = "&#8962;"; }
    echo "<li$active><a href='?" . url_state($state,'atoz', $char)."'>$chet</a></li>";
  }
  echo "</ul>";
  echo "</div>";
}

function gameitem( $id, $ta, $name, $image, $img, $publisher, $year, $keys, $platform, $dl, $gp) {
   global $sid;

   $jsbeeb=JB_LOC;
   $root=WS_ROOT;

   $split=explode('(',$name);
   $title=trim($split[0]);
   if (strlen($ta)>0){
     $title=$ta.' '.$title;
   }
   
   $ssd = get_discloc($img["filename"] ?? '',$img['subdir'] ?? '');
?>
     <div class="col-sm-6 col-md-4 col-lg-3 thumb1">
      <div class="thumbnail text-center">
       <a href="game.php?id=<?php echo $id; ?>"><img src="<?php echo $image; ?>" alt="<?php echo $image; ?>" class="pic"></a>
       <div class="row-title"><span class="row-title"><a href="game.php?id=<?php echo $id; ?>"><?php echo $title ?></a></span></div>
       <div class="row-pub"><?php echo $publisher ?></div>
       <div class="row-dt"><a href="?search=<?php echo urlencode($year) ?>&on_Y=on"><?php echo $year; ?></a></div>
<?php
  $playlink=get_playlink($img,$jsbeeb,$root,$keys,$platform);
  if ($dl != null && $dl > 0) {
    $download_title = "Downloaded " . $dl . " time";
    if ($dl > 1) {
      $download_title .= "s";
    }
  } else {
    $download_title = "Not downloaded yet";
  }
  if ($gp != null && $gp > 0) {
    $played_title = "Played " . $gp . " time";
    if ($gp > 1) {
      $played_title .= "s";
    }
  } else {
    $played_title = "Not played yet";
  }
  if ($ssd != null && file_exists($ssd)) { ?>
       <p><a href="<?php echo $ssd ?>" type="button" onmousedown="log(<?php echo $id; ?>);" class="btn btn-default" title="<?php echo $download_title ?>">Download</a><?php
  }
  if ((($img['probs'] ?? '') != 'N' and ($img['probs'] ?? '') != 'P') and $playlink != null) { ?>
          <a id="plybtn" href="<?php echo $playlink ?>" type="button" onmousedown="logPlay(<?php echo $id; ?>);" class="btn btn-default" title="<?php echo $played_title ?>">Play</a></p>
<?php
  }
?>
      </div>
     </div>
<?php
}

function json_state($state, $ko, $vo) {

  foreach ($state as $key => $value) {
    if ( $key == 'only' ) {
       foreach ($state['only'] as $k => $v ) {
         $s2['on_'.$v]='on';
       }
    } elseif ( $key == 'rtype' ) {
       foreach ($state['rtype'] as $k => $v ) {
         $s2['rt_'.$v]='on';
       }
//    } elseif ( $key == 'sort' ) {
//      $s2['sort']=$value;
    } else {
      $s2[$key]=$value;
    }
  }
  $s2[$ko]=$vo;
  return json_encode($s2,JSON_HEX_QUOT);
}

function url_state($state, $k, $v) {
  unset($state['page']);
  $state[$k]=$v;
  $url='';

  foreach ($state as $key => $value) {
    if ( $key == 'only' ) {
       foreach ($state['only'] as $k => $v ) {
         $url=$url.'&on_'.$v.'=on';
       }
    } elseif ( $key == 'rtype' ) {
       foreach ($state['rtype'] as $k => $v ) {
         $url=$url.'&rt_'.$v.'=on';
       }
//    }  elseif ( $key == 'sort' ) {
//      $url=$url.'&'.$key.$value.'=';
    } else {
      $url=$url.'&'.$key.'='.urlencode ( $value );
    }
  }
  return substr($url,1);  //Skip first &
}

function pager($limit, $rows, $page, $state) {
  global $publisher, $year;
  $pages = ceil($rows/$limit);
  $pl='';

  $el = "border-color: white; margin-left: 0; margin-right: 1px; cursor: default; text-align: center;";

  $links = 4;
  $skip = 10;

  // Left three buttons

  $left_buttons = '<ul class="pagination">';
  if ( $page != 1 ) {
    $left_buttons.= '     <li><a title="First page" onclick=\'$.get("getgrid.php", '. json_state($state,'page', 1).', function(data){ $("#maingrid").html(data); window.scrollTo(0,0); }); return false;\' href="?'. url_state($state,'page', 1). '">|&lt;</a></li>' . "\n";
  }else{
    $left_buttons.= '     <li class="disabled"><span>|&lt;</span></li> '. "\n";
  }
  if ( $page != 1 ) {
    $left_buttons.= '     <li><a title="Back ' . $skip . ' pages" onclick=\'$.get("getgrid.php", '. json_state($state,'page', max(1, $page - $skip)).', function(data){ $("#maingrid").html(data); window.scrollTo(0,0); }); return false;\' href="?'. url_state($state,'page', max(1, $page - $skip)). '">&lt;&lt;</a></li>' . "\n";
  }else{
    $left_buttons.= '     <li class="disabled"><span>&lt;&lt;</span></li> '. "\n";
  }
  if ( $page != 1 ) {
    $left_buttons.= '     <li><a title="Previous page" onclick=\'$.get("getgrid.php", '. json_state($state,'page', ($page - 1)).', function(data){ $("#maingrid").html(data); window.scrollTo(0,0); }); return false;\' href="?'. url_state($state,'page', ($page - 1)). '">&lt;</a></li>' . "\n";
  }else{
    $left_buttons.= '     <li class="disabled"><span>&lt;</span></li> '. "\n";
  }
  $left_buttons.= '    </ul>';

  // Right three buttons

  $right_buttons = '<ul class="pagination">';

  if ( $page < $pages ) {
      $right_buttons.= '     <li><a title="Next page" onclick=\'$.get("getgrid.php", '. json_state($state,'page', ($page + 1)).', function(data){ $("#maingrid").html(data); }); window.scrollTo(0,0); return false;\' href="?'. url_state($state,'page', ($page + 1)). '">&gt;</a></li>' . "\n";
  }else{
     $right_buttons.= '     <li class="disabled"><span>&gt;</span></li> '. "\n";
  }

  if ( $page < $pages ) {
      $right_buttons.= '     <li><a title="Forward ' . $skip . ' pages" onclick=\'$.get("getgrid.php", '. json_state($state,'page', min($pages, $page + $skip)).', function(data){ $("#maingrid").html(data); }); window.scrollTo(0,0); return false;\' href="?'. url_state($state,'page', min($pages, $page + $skip)). '">&gt;&gt;</a></li>' . "\n";
  }else{
     $right_buttons.= '     <li class="disabled"><span>&gt;&gt;</span></li> '. "\n";
  }

  if ( $page < $pages ) {
      $right_buttons.= '     <li><a title="Last page" onclick=\'$.get("getgrid.php", '. json_state($state,'page', $pages).', function(data){ $("#maingrid").html(data); }); window.scrollTo(0,0); return false;\' href="?'. url_state($state,'page', $pages). '">&gt;|</a></li>' . "\n";
  }else{
     $right_buttons.= '     <li class="disabled"><span>&gt;|</span></li> '. "\n";
  }
  $right_buttons.= "    </ul>\n";

  // Main pagination block

  $pl.= '    <div class="page-buttons top">' . $left_buttons . '</div>';

  $pl.= '    <div class="page-numbers main">' . $left_buttons . '<ul class="pagination">';

  $left_end = $page - $links;
  $right_end = $page + $links;
  if ($left_end < 1) {
    $right_end -= $left_end - 1;
    $right_end = max(1, $right_end);
    $left_end = 1;
  }
  if ($right_end > $pages - 1) {
    $left_end -= $right_end - $pages;
    $left_end = max(1, $left_end);
    $right_end = $pages;
  }

  // Left ellipsis

  if ($left_end > 1) {
    $pl.= '     <li class="disabled ellipses"><span style="' . $el . ' width: 43px;">...</span></li> '. "\n";
  } else {
    $pl.= '     <li class="disabled ellipses"><span style="' . $el . ' width: 43px;">&nbsp;</span></li> '. "\n";
  }

  // Pagination buttons

  for ( $i=$left_end; $i <= $right_end; $i++ ) {
    if ($i != $page) {
      $clli = '';
    } else {
      $clli = ' class="active"';
    }
    $cla = '';
    if ($i == $left_end) {
      $cla = ' class="page-first"';
    }
    if ($i == $right_end) {
      if ($cla == '') {
        $cla = ' class="page-last"';
      } else {
        $cla = ' class="page-first page-last"';
      }
    }
    $pl.= '     <li' . $clli . '><a' . $cla . ' style="width: 48px; text-align: center" onclick=\'$.get("getgrid.php", '.json_state($state,'page', $i).', function(data){ $("#maingrid").html(data); }); window.scrollTo(0,0); return false;\' href="?'.url_state($state,'page', $i).'">' . $i . '</a></li>' . "\n";
  }

  // Right ellipsis

  if ($right_end < $pages) {
    $pl.= '     <li class="disabled ellipses"><span style="' . $el . ' width: 44px;">...</span></li> '. "\n";
  } else {
    $pl.= '     <li class="disabled ellipses"><span style="' . $el . ' width: 43px;">&nbsp;</span></li> '. "\n";
  }

  $pl.= '    </ul>' . $right_buttons . '</div>';

  // Right three buttons

  $pl.= '    <div class="page-buttons bottom">' . $right_buttons . "</div>\n";

  return $pl;
}

function prepare_search($string) {
  $string=preg_replace('/^(A|An|The) /i','',$string);
  $string=preg_replace('/,? (A|An|The)$/i','',$string);
  $string="%".str_replace(' ','%',$string)."%";
  return $string;
}

function grid($state) {
  global $db;

  $limit=GD_IPP;

  $wc=array();
  $sls=array();
  $binds=array();

  $all=( !array_key_exists('only', $state) || count($state['only']) == 0);

  if (array_key_exists ('search', $state)) {
    if ( $all || !(array_search('T',$state['only'])===False )) {
      $sls[] = "g.title like :search\n";
    }
    if ( $all || !(array_search('P',$state['only'])===False )) {
      $sls[] = "g.id in (select gameid\n" .
               "   from games_publishers gp, publishers p\n" .
               "   where p.id = gp.pubid and p.name like :search)\n";
    }
    if ( $all || !(array_search('A',$state['only'])===False )) {
      $sls[] = "g.id in (select games_id\n   from games_authors ga, authors a\n" .
               "   where a.id = ga.authors_id and (a.name like :search or a.alias like :search))\n";
    }
    if ( $all || !(array_search('Y',$state['only'])===False )) {
      $sls[] = "g.year like :search\n";
    }
    if ( $all || !(array_search('Z',$state['only'])===False )) {
      $sls[] = "g.series like :search\n";
    }
    if ( $all || !(array_search('C',$state['only'])===False )) {
      $sls[] = "g.id in (select games_id from games_compilations gc, compilations c\n" .
               "  where c.id = gc.compilations_id and c.name like :search\n)";
    }
    if ( $all || !(array_search('G',$state['only'])===False )) {
      $sls[] = "g.genre in (select id from genres where name like :search)\n";
    }
    if ( $all || !(array_search('S',$state['only'])===False )) {
      $sls[] = "g.id in (select gameid from game_genre m, genres g where g.id = m.genreid and g.name like :search)\n";
    }
  }

  if (count($sls)>0) {
    $wc[] = '(' . implode ('  OR ',$sls) . ')';
  }

//  if (array_key_exists ('pubid', $state)) {
//    $wc[] = "id in (select gameid from games_publishers gp where gp.pubid = :pubid)\n";
//  }

//  if (array_key_exists ('year', $state)) {
//    $wc[] = "year = :year\n";
//  }

  $doing_atoz_numbers=false;
  if (array_key_exists('atoz',$state)) {
    if ($state['atoz']=='#') {
      $doing_atoz_numbers=true;
      $atoz="^[^a-zA-Z]";
      $atoz2=".*";
      $wc[]="title REGEXP :atoz\n";
    } else {
      $atoz=substr($state['atoz'],0,1)."%";
      $atoz2="%";
      $wc[]="title like :atoz\n";
    }
  }

  if (array_key_exists('rtype',$state)) {
    $wc[]="FIND_IN_SET(reltype,:array)\n";
  } else {
    $wc[]="reltype in (select id from reltype where selected = 'Y')\n";
  }

  if (array_key_exists('page',$state)) {
    $page=$state['page'];
  } else {
    $page=1;
  }

//  $wc[]='parent is null';
  if (isset($state['sort'])) {
    $srt=$state['sort'];
  } else {
    $srt='';
  }

  switch ($srt) {
    case "u":
      $ob = "order by g.imgupdated desc";
      break;
    case "a":
      $ob = "order by g.title";
      break;
    case "p":
      $ob = "order by tt desc, dl desc, gp desc";
      break;
    case "b":
    default:
      $ob = "order by g.year desc, g.id desc";
  }
  $ym=date("Ym",time()-90*24*60*60);
  $offset = $limit * ($page -1);
  $sql ='select SQL_CALC_FOUND_ROWS g.*, sum(d.downloads) as dl, sum(d.gamepages) as gp, sum(coalesce(d.downloads, 0) + coalesce(d.gamepages, 0)) as tt from games g'."\n";
  $sql.=' left join game_downloads d on g.id = d.id and d.year > ' . $ym . ' where ' . implode(" AND ",$wc) . ' group by g.id '. $ob . ' LIMIT :limit OFFSET :offset';
  $sql2 = 'select distinct upper(substring(title,1,1)) AS c1 from games g WHERE ' . implode(' AND ',$wc) . " order by c1"; 

  $sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth2 = $db->prepare($sql2,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

  if (array_key_exists('search',$state)) {
    $search=prepare_search($state['search']);
    $sth->bindParam(':search', $search, PDO::PARAM_STR);
    $sth2->bindParam(':search', $search, PDO::PARAM_STR);
  }
  if (array_key_exists('atoz',$state)) {
    $sth->bindParam(':atoz',$atoz, PDO::PARAM_STR);
    $sth2->bindParam(':atoz',$atoz2, PDO::PARAM_STR);
  }
  if (array_key_exists('rtype',$state)) {
    $t=implode(',',$state['rtype']);
    $sth->bindParam(':array',$t);
    $sth2->bindParam(':array',$t);
  }

  $sth->bindParam(':limit',$limit, PDO::PARAM_INT);
  $sth->bindParam(':offset',$offset, PDO::PARAM_INT);
  if ($sth->execute()) {
    $res = $sth->fetchAll();
  } else {
    echo "<pre>Error:";
    echo "\n";
    $sth->debugDumpParams ();
    $res=array();
    print_r($sth->ErrorInfo());
    echo "</pre>";
  }

  $sfr = $db->prepare("SELECT FOUND_ROWS();");
  if ($sfr->execute()) {
    $sfr_result = $sfr->fetch(PDO::FETCH_ASSOC);
    $rows = $sfr_result['FOUND_ROWS()'];
  } else {
    echo "Error:";
    $sfr->debugDumpParams ();
  }

  if ($sth2->execute()) {
    $res2 = $sth2->fetchAll();
  } else {
    echo "<pre>Error2:";
    echo "\n";
    $sth2->debugDumpParams ();
    $res2=array();
    print_r($sth2->ErrorInfo());
    echo "</pre>";
  }

  $scrsql = 'select filename, subdir from screenshots where gameid = :gameid order by main, id limit 1';
  $dscsql = 'select filename, subdir, customurl, probs from images where gameid = :gameid order by main, id limit 1';
  $pubsql = 'select id,name from publishers where id in (select pubid from games_publishers where gameid = :gameid)';
  $keysql = "select jsbeebbrowserkey,jsbeebgamekey from game_keys where gameid = :gameid order by rel_order";
  $scrpdo = $db->prepare($scrsql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $dscpdo = $db->prepare($dscsql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $pubpdo = $db->prepare($pubsql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $keypdo = $db->prepare($keysql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

  $chars=array();

  // Loop through and get relevant starting letters
  foreach ($res2 as $game ) {
    $a=strtoupper(substr($game['c1'],0,1));
      
    if (!preg_match('/[a-zA-Z]/', $a)) {
       $a='#';
    }

    if (array_search($a,$chars) === False) {
       $chars[]=$a;
    }
  }
  if (array_key_exists('atoz',$state)) {
    $atoz=$state['atoz'];
  } else {
    $atoz="";
  }

  reltypes($state);

  atoz_line($atoz,$chars,'bottom');

  $pl=pager($limit,$rows,$page,$state);
  echo $pl;
  echo '    <div class="row" style="display:flex; flex-wrap: wrap;">'."\n";
  if ( $rows > 0 ) {
    foreach ( $res as $game ) {
      $scrpdo->bindParam(':gameid',$game["id"], PDO::PARAM_INT);
      $dscpdo->bindParam(':gameid',$game["id"], PDO::PARAM_INT);
      $pubpdo->bindParam(':gameid',$game["id"], PDO::PARAM_INT);
      $keypdo->bindParam(':gameid',$game["id"], PDO::PARAM_INT);
      if ($scrpdo->execute()) {
        $img=$scrpdo->fetch(PDO::FETCH_ASSOC);
        $shot = get_scrshot($img['filename'] ?? '',$img['subdir'] ?? '');
      } else {
        echo "Error:";
        $sim->debugDumpParams ();
      }
      if ($dscpdo->execute()) {
        $dnl=$dscpdo->fetch(PDO::FETCH_ASSOC);
      } else {
        echo "Error:";
        $sim->debugDumpParams ();
      }
      $pubs='';
      if ($pubpdo->execute()) {
        while($pub=$pubpdo->fetch(PDO::FETCH_ASSOC)) {
          $t=preg_split('/[,(]/',$pub['name']);
          $u=htmlspecialchars(trim($t[0] ?? ''));
          $pubs=$pubs.'<a href="?search='.urlencode($pub['name']).'&on_P=on">'.$u.'</a>, ';
        }
      } else {
        echo "Error:";
        $sim->debugDumpParams ();
      }
      if ($keypdo->execute()) {
        $keys=$keypdo->fetchAll();
      } else {
        echo "Error:";
        $sim->debugDumpParams ();
        $keys=array();
      }
      $pubs=trim($pubs,', ');

      gameitem($game["id"],htmlspecialchars($game["title_article"] ?? ''),htmlspecialchars($game["title"] ?? ''), $shot, $dnl ,$pubs,$game["year"],$keys,$game["jsbeebplatform"], $game["dl"], $game["gp"]);
    }
  } else {
    echo '    <div class="row" style="display:flex; flex-wrap: wrap;">'."\n<h2>No games found!</h2>";
    echo "    </div>\n";
  }
  echo "    </div>\n";
  echo $pl;
  atoz_line($atoz,$chars,'top');

  if ( defined('GD_DEBUG') && GD_DEBUG == True ) {
    echo "<pre>";
    echo "SQL:\n";
    echo $sql;
    echo "\n\nSQL2:\n";
    echo $sql2;
    echo "\n\nscrsql:\n";
    echo $scrsql;
    echo "\n\ndscsql:\n";
    echo $dscsql;
    echo "\n\npubsql:\n";
    echo $pubsql;
    echo "\n\nState:\n";
    print_r($state);
    echo "</pre>";
  }
  echo "   </div>\n";

}
?>
