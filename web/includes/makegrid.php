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

function gameitem( $id, $ta, $name, $image, $img, $publisher, $year, $keys, $platform, $dl, $gp, $dl_all, $gp_all) {
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
    $download_title .= " in the last 90 days";
    if ($dl_all != null && $dl_all > 0) {
      $download_title .= " (" . $dl_all . " time";
      if ($dl_all > 1) {
        $download_title .= "s";
      }
      $download_title .= " since 2017)";
    }
  } else {
    $download_title = "Not downloaded yet";
  }
  if ($gp != null && $gp > 0) {
    $played_title = "Played " . $gp . " time";
    if ($gp > 1) {
      $played_title .= "s";
    }
    $played_title .= " in the last 90 days";
    if ($gp_all != null && $gp_all > 0) {
      $played_title .= " (" . $gp_all . " time";
      if ($gp_all > 1) {
        $played_title .= "s";
      }
      $played_title .= " since 2017)";
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

  if ($pages == 0) {
    return "";
  }

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

function prepare_search($string, $state) {
  $string = trim($string);
  $exact = (array_key_exists('f_exact', $state) && $state['f_exact'] > 0);
  $words = (array_key_exists('f_words', $state) && $state['f_words'] > 0);
  if ($exact && $words) {
    if ($string = "'n'" || $string = "'n" || $string = "n'") $string="n";
    return '(\b|^)'.preg_quote($string).'(\b|$)';
  } elseif ($exact) {
    return "%".$string."%";
  } elseif ($words) {
    return '(\b|^)'.regex_string($string).'(\b|$)';
  }
  $string=preg_replace('/^(A|An|The) /i','',$string);
  $string=preg_replace('/,? (A|An|The)$/i','',$string);
  $string="%".str_replace(' ','%',$string)."%";
  return $string;
}

function regex_string($string) {
  $string=preg_quote($string);
  $string=str_replace("'","'?",$string);
  $string=str_replace(' ','\b.*\b',$string);
  $string=str_replace('\b–\b','.*',$string);
  return $string;
}

function prepare_like($state) {
  $exact = (array_key_exists('f_exact', $state) && $state['f_exact'] > 0);
  $words = (array_key_exists('f_words', $state) && $state['f_words'] > 0);
  if ($exact && $words) {
    return ' REGEXP ';
  } elseif ($exact) {
    return ' like ';
  } elseif ($words) {
    return ' REGEXP ';
  }
  return ' like ';
}

function grid($state) {
  global $db;

  $limit=GD_IPP;

  $wc=array();
  $sls=array();
  $binds=array();

  $all=( !array_key_exists('only', $state) || count($state['only']) == 0);

  $like = prepare_like($state);

  if (array_key_exists ('search', $state)) {
    if ( $all || !(array_search('T',$state['only'])===False )) {
      $sls[] = "g.title $like :search\n";
    }
    if ( $all || !(array_search('P',$state['only'])===False )) {
      $sls[] = "g.id in (select gameid\n" .
               "   from games_publishers gp, publishers p\n" .
               "   where p.id = gp.pubid and p.name $like :search)\n";
    }
    if ( $all || !(array_search('A',$state['only'])===False )) {
      $sls[] = "g.id in (select games_id\n   from games_authors ga, authors a\n" .
               "   where a.id = ga.authors_id and (a.name $like :search or a.alias $like :search))\n";
    }
    if ( $all || !(array_search('Y',$state['only'])===False )) {
      $sls[] = "g.year $like :search\n";
    }
    if ( $all || !(array_search('Z',$state['only'])===False )) {
      $sls[] = "g.series $like :search\n";
    }
    if ( $all || !(array_search('C',$state['only'])===False )) {
      $sls[] = "g.id in (select games_id from games_compilations gc, compilations c\n" .
               "  where c.id = gc.compilations_id and c.name $like :search\n)";
    }
    if ( $all || !(array_search('G',$state['only'])===False )) {
      $sls[] = "g.genre in (select id from genres where name $like :search)\n";
    }
    if ( $all || !(array_search('S',$state['only'])===False )) {
      $sls[] = "g.id in (select gameid from game_genre m, genres g where g.id = m.genreid and g.name $like :search)\n";
    }
  }

  if (count($sls)>0) {
    $wc[] = '(' . implode ('  OR ',$sls) . ')';
  }

  if (array_key_exists ('f_pubid', $state)) {
    if ($state['f_pubid'] > 0) {
      $wc[] = "g.id in (select gameid from games_publishers gp where gp.pubid = :pubid)\n";
    }
  }

  if (array_key_exists ('f_genreid', $state)) {
    if ($state['f_genreid'] > 0) {
      $wc[] = "g.id in (select gameid from game_genre gg where gg.genreid = :genreid)\n";
    }
  }

  if (array_key_exists ('f_year1', $state)) {
    if ($state['f_year1'] > 0) {
      $wc[] = "g.year >= :year1\n";
      if ($state['f_year1'] <= 1989) {
        $wc[] = "(g.year >= :year1 or g.year = '198X' or g.year = '19XX')\n";
      } elseif ($state['f_year1'] <= 1999) {
        $wc[] = "(g.year >= :year1 or g.year = '19XX')\n";
      } else {
        $wc[] = "g.year >= :year1\n";
      }
    }
  }

  if (array_key_exists ('f_year2', $state)) {
    if ($state['f_year2'] > 0) {
      if ($state['f_year2'] <= 1979) {
        $wc[] = "g.year <= :year2\n";
      } elseif ($state['f_year2'] <= 1989) {
        $wc[] = "(g.year <= :year2 or g.year = '198X')\n";
      } else {
        $wc[] = "(g.year <= :year2 or g.year = '198X' or g.year = '19XX')\n";
      }
    }
  }

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
  $sql ='select SQL_CALC_FOUND_ROWS g.*, sum(d.downloads) as dl, sum(d.gamepages) as gp, (select sum(d1.downloads) from game_downloads d1 where d1.id = g.id) as dl_all, (select sum(d2.gamepages) from game_downloads d2 where d2.id = g.id) as gp_all, sum(coalesce(d.downloads, 0) + coalesce(d.gamepages, 0)) as tt from games g'."\n";
  $sql.=' left join game_downloads d on g.id = d.id and d.year > ' . $ym . " where IFNULL(g.hide,'N') <> 'Y' and " . implode(" AND ",$wc) . ' group by g.id '. $ob . ' LIMIT :limit OFFSET :offset';
  $sql2 = "select distinct upper(substring(title,1,1)) AS c1 from games g WHERE IFNULL(g.hide,'N') <> 'Y' and " . implode(' AND ',$wc) . " order by c1"; 

  $sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth2 = $db->prepare($sql2,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

  if (array_key_exists('search',$state)) {
    $search=prepare_search($state['search'], $state);
    $sth->bindParam(':search', $search, PDO::PARAM_STR);
    $sth2->bindParam(':search', $search, PDO::PARAM_STR);
  }
  if (array_key_exists ('f_pubid',$state)) {
    $pubid=$state['f_pubid'];
    if($pubid > 0) {
      $sth->bindParam(':pubid', $pubid, PDO::PARAM_STR);
      $sth2->bindParam(':pubid', $pubid, PDO::PARAM_STR);
    }
  }
  if (array_key_exists ('f_genreid',$state)) {
    $genreid=$state['f_genreid'];
    if($genreid > 0) {
      $sth->bindParam(':genreid', $genreid, PDO::PARAM_STR);
      $sth2->bindParam(':genreid', $genreid, PDO::PARAM_STR);
    }
  }
  if (array_key_exists ('f_year1',$state)) {
    $year1=$state['f_year1'];
    if($year1 > 0) {
      $sth->bindParam(':year1', $year1, PDO::PARAM_STR);
      $sth2->bindParam(':year1', $year1, PDO::PARAM_STR);
    }
  }
  if (array_key_exists ('f_year2',$state)) {
    $year2=$state['f_year2'];
    if($year2 > 0) {
      $sth->bindParam(':year2', $year2, PDO::PARAM_STR);
      $sth2->bindParam(':year2', $year2, PDO::PARAM_STR);
    }
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

  // No longer show release types along the top of the homepage (moved to search bar)
  //reltypes($state);

  if (!array_key_exists('search',$state)) {
    echo "<h2>All games</h2>\n";
  } elseif ( $rows > 0 ) {
    echo "<h2>Search results</h2>\n";
  }

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

      gameitem($game["id"],htmlspecialchars($game["title_article"] ?? ''),htmlspecialchars($game["title"] ?? ''), $shot, $dnl ,$pubs,$game["year"],$keys,$game["jsbeebplatform"], $game["dl"], $game["gp"], $game["dl_all"], $game["gp_all"]);
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

function filters($state) {
  global $db;

  $wc=array();
  $sls=array();

  $all=( !array_key_exists('only', $state) || count($state['only']) == 0);

  $like = prepare_like($state);

  if (array_key_exists ('search', $state)) {
    if ( $all || !(array_search('T',$state['only'])===False )) {
      $sls[] = "g.title $like :search\n";
    }
    if ( $all || !(array_search('P',$state['only'])===False )) {
      $sls[] = "g.id in (select gameid\n" .
               "   from games_publishers gp, publishers p\n" .
               "   where p.id = gp.pubid and p.name $like :search)\n";
    }
    if ( $all || !(array_search('A',$state['only'])===False )) {
      $sls[] = "g.id in (select games_id\n   from games_authors ga, authors a\n" .
               "   where a.id = ga.authors_id and (a.name $like :search or a.alias $like :search))\n";
    }
    if ( $all || !(array_search('Y',$state['only'])===False )) {
      $sls[] = "g.year $like :search\n";
    }
    if ( $all || !(array_search('Z',$state['only'])===False )) {
      $sls[] = "g.series $like :search\n";
    }
    if ( $all || !(array_search('C',$state['only'])===False )) {
      $sls[] = "g.id in (select games_id from games_compilations gc, compilations c\n" .
               "  where c.id = gc.compilations_id and c.name $like :search\n)";
    }
    if ( $all || !(array_search('G',$state['only'])===False )) {
      $sls[] = "g.genre in (select id from genres where name $like :search)\n";
    }
    if ( $all || !(array_search('S',$state['only'])===False )) {
      $sls[] = "g.id in (select gameid from game_genre m, genres g where g.id = m.genreid and g.name $like :search)\n";
    }
  }

  if (count($sls)>0) {
    $wc[] = '(' . implode ('  OR ',$sls) . ')';
  }

  if (array_key_exists('rtype',$state)) {
    $wc[]="FIND_IN_SET(reltype,:array)\n";
  } else {
    $wc[]="reltype in (select id from reltype where selected = 'Y')\n";
  }

  $sql3 ="select DISTINCT p.id, p.name from games g left join games_publishers gp on gp.gameid = g.id left join publishers p on gp.pubid = p.id where IFNULL(g.hide,'N') <> 'Y' and p.id is not null and " . implode(" AND ",$wc) . ' order by p.name';

  $sql4 ="select DISTINCT gr.id, gr.name from games g left join game_genre gg on gg.gameid = g.id left join genres gr on gg.genreid = gr.id where IFNULL(g.hide,'N') <> 'Y' and gr.id is not null and " . implode(" AND ",$wc) . ' order by gr.name';

  $sql5 ="select DISTINCT g.year from games g where IFNULL(g.hide,'N') <> 'Y' and g.year not like '%X%' and " . implode(" AND ",$wc) . ' order by g.year';

  $sth3 = $db->prepare($sql3,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth4 = $db->prepare($sql4,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth5 = $db->prepare($sql5,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

  if (array_key_exists('search',$state)) {
    $search=prepare_search($state['search'], $state);
    $sth3->bindParam(':search', $search, PDO::PARAM_STR);
    $sth4->bindParam(':search', $search, PDO::PARAM_STR);
    $sth5->bindParam(':search', $search, PDO::PARAM_STR);
  }
  if (array_key_exists('rtype',$state)) {
    $t=implode(',',$state['rtype']);
    $sth3->bindParam(':array',$t);
    $sth4->bindParam(':array',$t);
    $sth5->bindParam(':array',$t);
  }

  if ($sth3->execute()) {
    $res3 = $sth3->fetchAll();
  } else {
    echo "<pre>Error3:";
    echo "\n";
    $sth3->debugDumpParams ();
    $res3=array();
    print_r($sth3->ErrorInfo());
    echo "</pre>";
  }

  if ($sth4->execute()) {
    $res4 = $sth4->fetchAll();
  } else {
    echo "<pre>Error4:";
    echo "\n";
    $sth4->debugDumpParams ();
    $res4=array();
    print_r($sth4->ErrorInfo());
    echo "</pre>";
  }

  if ($sth5->execute()) {
    $res5 = $sth5->fetchAll();
  } else {
    echo "<pre>Error5:";
    echo "\n";
    $sth5->debugDumpParams ();
    $res5=array();
    print_r($sth5->ErrorInfo());
    echo "</pre>";
  }

  // Loop through and get relevant publishers
  echo "<h4 style='margin-top:3ch'>Filter by:</h4>";

  echo "Publisher:<br /><select style='max-width:100%;margin-bottom: 2ch' name='f_pubid' id='f_pubid'>\n<option value='0'>All publishers</option>";
  foreach ($res3 as $pub ) {
    $pid=$_GET['f_pubid'];
    $id=$pub['id'];
    $name=$pub['name'];
    $sel=($id==$pid)?' selected':'';
    echo "<option value='$id'$sel>$name</option>\n";
  }
  echo "</select><br />";

  echo "Genre:<br /><select style='max-width:100%;margin-bottom: 2ch' name='f_genreid' id='f_genreid'>\n<option value='0'>All genres</option>";
  foreach ($res4 as $gen ) {
    $gid=$_GET['f_genreid'];
    $id=$gen['id'];
    $name=$gen['name'];
    $sel=($id==$gid)?' selected':'';
    echo "<option value='$id'$sel>$name</option>\n";
  }
  echo "</select><br />";

  echo "From year:<br /><select style='max-width:100%;margin-bottom: 2ch' name='f_year1' id='f_year1'>\n<option value='0'>Any start year</option>";
  foreach ($res5 as $year ) {
    $y1=$_GET['f_year1'];
    $y=$year["year"];
    $sel=($y==$y1)?' selected':'';
    echo "<option value='$y'$sel>$y</option>\n";
  }
  echo "</select><br />";

  echo "To year:<br /><select style='max-width:100%;margin-bottom: 3ch' name='f_year2' id='f_year2'>\n<option value='0'>Any end year</option>";
  foreach ($res5 as $year ) {
    $y2=$_GET['f_year2'];
    $y=$year["year"];
    $sel=($y==$y2)?' selected':'';
    echo "<option value='$y'$sel>$y</option>\n";
  }
  echo "</select><br />";

  echo "<div><a href='#' onclick='resetFilters(); return false;'>Reset filters</a></div>";

  if ( defined('GD_DEBUG') && GD_DEBUG == True ) {
    echo "<pre>";
    echo "\n\nSQL3:\n";
    echo $sql3;
    echo "\n\nSQL4:\n";
    echo $sql4;
    echo "</pre>";
  }
}
?>
