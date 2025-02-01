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

echo '    <div id="maingrid">'."\n";
grid($state);
echo '    </div>';
sidebar($state, $highlights);
containend();
htmlfoot();

