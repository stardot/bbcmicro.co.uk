<?php

require 'includes/config.php';
require 'includes/db_connect.php';
require 'includes/menu.php';
require 'includes/makegrid.php';
require 'header.php';
require 'includes/parsevars.php';

$state=getstate();
$highlights=get_highlights();

htmlhead();
nav();
containstart($state);

if (!array_key_exists('search',$state)) {
  highlights($highlights, 0, 0);
}

echo '    <div id="maingrid">'."\n";
grid($state);

if (!array_key_exists('search',$state)) {
  highlights($highlights, 1, 0);
}

echo '    </div>';

sidebar($state, $highlights);
containend();
htmlfoot();

