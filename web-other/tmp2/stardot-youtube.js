// ==UserScript==
// @name         Stardot YouTube video embedder
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  Replace the phpBB flash tag with an iframe for YouTube
// @author       You
// @match        https://stardot.org.uk/forums/viewtopic.php*
// @icon         https://www.google.com/s2/favicons?domain=stardot.org.uk
// @grant        none
// ==/UserScript==

(function() {
    'use strict';

    var arr = document.getElementsByName("movie");
    for (var i=0; i<arr.length; i++)
    {
        //console.log(arr[i].value);

        const reg = /(http(?:s)?:\/\/((?:www\.)|(?:m\.))?youtube\.com\/v\/[^&?]+)/ig;
        const str = arr[i].value;
        var array = [...str.matchAll(reg)];
        //console.log(array[0][1]);
        var url = array[0][1];

        if (typeof url !== "undefined")
        {
            const regexp = /[&?]t=((\d+)h)?((\d+)m)?((\d+)(?:s)?)?/ig;
            array = [...str.matchAll(regexp)];
            //console.log(array[0]);

            var h = 0, m=0, s=0;
            if (typeof array[0] !== "undefined" )
            {
                if (typeof (array[0][2]) !== "undefined") { h=60*60*parseInt(array[0][2]); }
                //console.log(h);
                if (typeof (array[0][4]) !== "undefined") { m=60*parseInt(array[0][4]); }
                //console.log(m);
                if (typeof (array[0][6]) !== "undefined") { s=parseInt(array[0][6]); }
                //console.log(s);
            }

            url = url + "?start="+(h+m+s).toString();
            //console.log(url);

            var ht=315, wd=420;
            if (typeof arr[i].parentNode.height !== "undefined") { ht = arr[i].parentNode.height; }
            if (typeof arr[i].parentNode.width !== "undefined") { wd = arr[i].parentNode.width; }
            //console.log(arr[i].parentNode); console.log(ht); console.log(wd);

            var iframe = "<iframe width='"+wd.toString()+"' height='"+ht.toString()+"' allow='fullscreen;' src='"+url.replace("/v/","/embed/")+"'></iframe>";
            //console.log(iframe);
            arr[i].parentNode.insertAdjacentHTML("afterend",iframe);
            arr[i].parentNode.hidden=true;
        }
    }
})();