/**
 * File: easy-wi.js.
 * Author: Ulrich Block
 * Contact: <ulrich.block@easy-wi.com>
 *
 * This file is part of Easy-WI.
 *
 * Easy-WI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Easy-WI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy-WI.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Diese Datei ist Teil von Easy-WI.
 *
 * Easy-WI ist Freie Software: Sie koennen es unter den Bedingungen
 * der GNU General Public License, wie von der Free Software Foundation,
 * Version 3 der Lizenz oder (nach Ihrer Wahl) jeder spaeteren
 * veroeffentlichten Version, weiterverbreiten und/oder modifizieren.
 *
 * Easy-WI wird in der Hoffnung, dass es nuetzlich sein wird, aber
 * OHNE JEDE GEWAEHELEISTUNG, bereitgestellt; sogar ohne die implizite
 * Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
 * Siehe die GNU General Public License fuer weitere Details.
 *
 * Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 * Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 */

function SwitchShowHideRows (Element, change, showNotIfEmpty) {

    var TheRest = document.getElementsByTagName('div');
    var amount = TheRest.length;
    var ElementLenght = Element.length;
    var foundAmount = 0;

    if(typeof(change) === 'undefined') {
        change = 'switch';
    }

    for(var x=0; x<amount; x++) {

        var TheClass = TheRest[x].getAttribute('class');

        if (TheClass != null) {
            if (Element == 'init_ready' || Element == '') {
                if (TheClass.indexOf("display_none") != -1) {
                    TheRest[x].style.display = 'none';
                } else {
                    TheRest[x].style.display = '';
                }
            } else {
                if (TheClass.indexOf(change)!= -1) {

                    foundElement = TheClass.substring(0, ElementLenght);

                    if (foundElement == Element) {
                        foundAmount++;
                    }

                    if (foundElement == Element && TheRest[x].style.display == 'none') {
                        TheRest[x].style.display = '';
                    } else if (foundElement != Element && TheRest[x].style.display != 'none') {
                        TheRest[x].style.display = 'none';
                    }
                }
            }
        }
    }

    if (Element != 'init_ready' && foundAmount == 0 && showNotIfEmpty != 1) {
        for(x=0; x<amount; x++) {
            TheClass = TheRest[x].getAttribute('class');
            if (TheClass != null) {
                if (TheClass.indexOf(change)!= -1) {
                    TheRest[x].style.display = '';
                }
            }
        }
    }
}

function checkall(checked,check) {
    var checkBoxes = document.getElementsByTagName('input');
    for(var i=0; i<checkBoxes.length; i++) {
        var theType = checkBoxes[i].getAttribute('type');
        var theName = checkBoxes[i].getAttribute('name');
        if (checked==true && theType.toLowerCase()=='checkbox' && theName==check) {
            checkBoxes[i].checked = true ;
        } else if (checked==false && theType.toLowerCase()=='checkbox' && theName==check) {
            checkBoxes[i].checked = false ;
        }
    }
}

function textdrop(id) {
    if (document.getElementById(id).style.display == "") {
        document.getElementById(id).style.display = "none";
    } else {
        document.getElementById(id).style.display = "";
    }
}

window.onDomReady = initReady;

function initReady(fn) {
    if(document.addEventListener) {
        document.addEventListener("DOMContentLoaded", fn, false);
    } else {
        document.onreadystatechange = function() {
            readyState(fn);
        }
    }
}

function readyState(func) {
    if(document.readyState == "interactive" || document.readyState == "complete") {
        func();
    }
}

window.onDomReady(onReady); function onReady() {
    SwitchShowHideRows('init_ready');
}