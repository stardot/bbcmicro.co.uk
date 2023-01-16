// ==UserScript==
// @name         bbcmicro edit details
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  bbcmicro edit details
// @author       You
// @match        http://bbcmicro.co.uk/admin_game_details.php*
// @grant        none
// ==/UserScript==

(function() {
    'use strict';

    var gameid = document.getElementsByName('id')[0].value;
    var editscr =  "<a href='http://bbcmicro.co.uk/admin_file.php?t=s&id="+gameid+"'>Edit screenshot</a>";
    var editdisc = "<a href='http://bbcmicro.co.uk/admin_file.php?t=d&id="+gameid+"'>Edit disc-image</a>";
    var game     = "<a href='http://bbcmicro.co.uk/game.php?id="+gameid+"'>Public view</a>";

    var repstr = "&nbsp;&nbsp;&nbsp;&nbsp;"+editscr+"&nbsp;&nbsp;&nbsp;&nbsp;"+editdisc+"&nbsp;&nbsp;&nbsp;&nbsp;"+game;

    document.getElementsByTagName("a")[2].insertAdjacentHTML('afterend',repstr);

})();