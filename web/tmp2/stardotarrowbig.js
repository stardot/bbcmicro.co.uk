// ==UserScript==
// @name         Stardot arrow emBIGgenner
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  Stardot arrow emBIGgenner
// @author       You
// @match        https://stardot.org.uk/forums/*
// @grant        none
// ==/UserScript==

(function() {
    'use strict';

    var c = document.getElementsByTagName("i");

    for (var i=0; i<c.length; i++)
    {
      if (c[i].className == "icon fa-external-link-square fa-fw icon-lightgray icon-md")
      {
        c[i].className = "icon fa-external-link-square fa-fw icon-lightgray icon-lg";
      }
    }
})();