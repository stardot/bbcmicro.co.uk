// ==UserScript==
// @name         bbcmicro edit disc/screenshot
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  bbcmicro edit disc/screenshot
// @author       You
// @match        http://bbcmicro.co.uk/admin_file.php*
// @grant        none
// ==/UserScript==

(function() {
    'use strict';

    var locid = (new URL(document.getElementsByTagName('form')[0].action)).searchParams.get('t');
    var gameid = (new URL(document.getElementsByTagName('form')[0].action)).searchParams.get('id');
    var editdet = "<a href='http://bbcmicro.co.uk/admin_game_details.php?id="+gameid+"'>Edit details</a>";

    var editoth;
    if (locid === "d") {
        editoth = "<a href='http://bbcmicro.co.uk/admin_file.php?t=s&id="+gameid+"'>Edit screenshot</a>";
    }
    if (locid === "s") {
        editoth = "<a href='http://bbcmicro.co.uk/admin_file.php?t=d&id="+gameid+"'>Edit disc-image</a>";
    }

    var game     = "<a href='http://bbcmicro.co.uk/game.php?id="+gameid+"'>Public view</a>";

    var repstr = "&nbsp;&nbsp;&nbsp;&nbsp;"+editdet+"&nbsp;&nbsp;&nbsp;&nbsp;"+editoth+"&nbsp;&nbsp;&nbsp;&nbsp;"+game;

    document.getElementsByTagName("a")[2].insertAdjacentHTML('afterend',repstr);

})();