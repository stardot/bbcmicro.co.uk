<?php

function get_fn($otitle,$file,$id) {
  $path_parts = pathinfo($file);
  $title=preg_split('/[,(]/',$otitle)[0];
  $title=preg_replace("/[^a-zA-Z0-9]+/","",$title);
  $tf=substr($otitle,0,1);
  if (is_numeric($tf)) {
    $tf = '0';
  } else {
    $tf = strtoupper($tf);
  }
  $fn=$tf . '/' . $title.'-'.$id.'.'.$path_parts['extension'];
  return $fn;
}

function get_disc_fn($dname) {
  if (!$dname || $dname=='') {
    return "0/noname";
  }
  $discname=preg_split('/\-/',$dname)[0];
  $discname=str_replace('Disc','',$discname);
  if (!$discname || $discname == '') {
    $discname="Other";
  }
//  $gamename=preg_split('/\-/',$dname)[1];
//  $tf=substr($gamename,0,1);
//  if (!$tf) {
//    $tf = 'None';
//  } elseif (is_numeric($tf)) {
//    $tf = '0';
//  } else {
//    $tf = strtoupper($tf);
//  }
//  $fn=$tf . '/' . $dname;
  $fn=$discname . '/' . $dname;
  return $fn;
}

function get_data ($arch='N') {
global $db;
$sql="select g.parent, g.id, g.title_article, g.title, g.year, g.genre, g.reltype, g.players_max, g.players_min, g.joystick, i.filename, r.name, g.save, g.hardware, g.electron, g.version, g.series, g.series_no, g.notes, g.compat_a, g.compat_b, g.compat_master from games g left join images i on g.id = i.gameid left join genres r on g.genre = r.id where IFNULL(g.hide,'N') <> 'Y' order by g.id";

$sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
if ($sth->execute()) {
  $res = $sth->fetchAll();
} else {
  echo "Error:";
  echo "\n";
  $sth->debugDumpParams ();
  $res=array();
}

$gsql="select g.name from genres g left join game_genre gg on gg.genreid = g.id where gg.gameid = ? order by gg.ord";
$gsth = $db->prepare($gsql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$gsth->bindParam(1, $id, PDO::PARAM_INT);

$asql="select a.name, a.alias from authors a left join games_authors ga on ga.authors_id = a.id where ga.games_id = ?";
$asth = $db->prepare($asql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$asth->bindParam(1, $id, PDO::PARAM_INT);

$psql="select a.name from publishers a left join games_publishers ga on ga.pubid = a.id where ga.gameid =?";
$psth = $db->prepare($psql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$psth->bindParam(1, $id, PDO::PARAM_INT);

$csql="select c.name from compilations c left join games_compilations gc on gc.compilations_id = c.id where gc.games_id =?";
$csth = $db->prepare($csql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$csth->bindParam(1, $id, PDO::PARAM_INT);

$text='<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="robots" content="noindex">
</head>
<body>
<table><tr>
<th>Id</th><th>Parent</th><th>Disc</th><th>title article</th><th>title</th><th>publisher</th><th>filename</th><th>Commercial</th><th>genre</th><th>genre2</th><th>year</th><th>author</th><th>save</th><th>joystick</th><th>Compilation</th><th>series/no</th><th>playersmin</th><th>playersmax</th><th>hardware</th><th>electron</th><th>version</th><th>A</th><th>B</th><th>M</th><th>Notes</th></tr>';

foreach ($res as $line) {
  // print_r($line);
  $fp=explode('-',$line['filename'] ?? '');
  $id=$line['id'];
  // Secondary Genres

  $gsth->execute(); 
  $sgs=$gsth->fetchAll();
  $gen2=array();
  // print_r($sgs);
  foreach ($sgs as $sg) { 
    $gen2[]=$sg['name'];
  }

  // Author

  $asth->execute(); 
  $ags=$asth->fetchAll();
  $auths=array();
  // print_r($ags);
  foreach ($ags as $auth) { 
    if ( empty($auth['alias']) ) {
      $auths[]=$auth['name'];
    }else{
      $auths[]=$auth['name'] . ' (' . $auth['alias'] . ')';
    }
  }

  // Publisher

  $psth->execute(); 
  $pgs=$psth->fetchAll();
  $pubs=array();
  // print_r($ags);
  foreach ($pgs as $pub) {
    $pubs[]=$pub['name'];
  }

  // Compilation

  $csth->execute(); 
  $cgs=$csth->fetchAll();
  $comps=array();
  // print_r($cgs);
  foreach ($cgs as $comp) {
    $comps[]=$comp['name'];
  }

  $ol=array();
  $ol[]=strtoupper($line['id']);
  $ol[]=strtoupper($line['parent'] ?? '');
  $ol[]=strtoupper($fp[0]); 	//Disc
				// Title
  $ol[]=$line['title_article'];
  $ol[]=$line['title'];

  $ol[]=implode(', ',$pubs);	// Publisher

  if ($arch=='N') {
    $ol[]=$line['filename'];	// Filename
  } else {
    if ($line['filename'] > ' ') {
      $archfn=get_fn($line['title'],$line['filename'],$line['id']);
      $archfn='<a href="'.$archfn.'">'.$archfn.'</a>';
    } else {
      $archfn = ' ';
    }
    $ol[]=$archfn;		// File name
  }

  $ol[]=$line['reltype'];	// Release Type
  $ol[]=$line['name'];		// Genre1

  $ol[]=implode(', ',$gen2);	// Genre2

  $ol[]=$line['year'];		// Year

  $ol[]=implode(', ',$auths);	// Authors

				// Save
  if ( $line['save'] == 'D' or $line ['save'] =='T' ) {
    $ol[]='ST'.$line['save'];
  } else {
    $ol[]=$line['save'];
  }

  $ol[]=$line['joystick'];	// Joystick

  $ol[]=implode(', ',$comps);	// Compilation

				// Series.no
  $ol[]=trim($line['series'].' '.$line['series_no']);

  $ol[]=$line['players_min'];	// Players
  $ol[]=$line['players_max'];

  $ol[]=$line['hardware'];	// Hardware

				// Electron release
  if ($line['electron'] == 'Y') {
    $ol[]=$line['electron'].'es';
  } else {
    $ol[]=$line['electron'];
  }

  $ol[]=$line['version'];	// Version

  $ol[]=$line['compat_a'];
  $ol[]=$line['compat_b'];
  $ol[]=$line['compat_master'];
  $ol[]=htmlspecialchars($line['notes'] ?? '');
//  $ol[]='';

  $ol2='<tr><td>'.implode('</td><td>',$ol).'</td></tr>';
  $text=$text . $ol2 . "\n";

}
$text=$text . "
</table>
</body>
</html>
";
return $text;
}
?>
