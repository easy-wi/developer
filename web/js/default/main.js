/**
 * File: main.js.
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

window.onDomReady = initReady;
function initReady(fn) {
    if(document.addEventListener) {
        document.addEventListener("DOMContentLoaded", fn, false);
    } else {
        document.onreadystatechange = function(){readyState(fn)}
    }
}
function readyState(func) {
    if(document.readyState == "interactive" || document.readyState == "complete") {
        func();
    }
}
function textdrop(id) {
    if (document.getElementById(id).style.display == "") {
        document.getElementById(id).style.display = "none";
    } else {
        document.getElementById(id).style.display = "";
    }
}
function getdetails(file, str) {
    if (str=="") {
        document.getElementById("information").innerHTML="";
        return;
    } else {
        file += str;
    }
    xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById("information").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET",file,true);
    xmlhttp.send();
}
function getdetails2(file, str, id) {
    if (str=="") {
        document.getElementById(id).innerHTML="";
        return;
    } else {
        file += str;
    }
    xmlhttp2=new XMLHttpRequest();
    xmlhttp2.onreadystatechange=function() {
        if (xmlhttp2.readyState==4 && xmlhttp2.status==200) {
            document.getElementById(id).innerHTML=xmlhttp2.responseText;
        }
    }
    xmlhttp2.open("GET",file,true);
    xmlhttp2.send();
}
function onloaddata(file, str, id) {
    file += str;
    var xmlhttp = id;
    xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById(id).innerHTML=xmlhttp.responseText;
        } else if (xmlhttp.readyState==4) {
            document.getElementById(id).innerHTML="Connection Error";
        }
    }
    xmlhttp.open("GET",file,true);
    xmlhttp.send();
}
function details(select) {
    var details = document.getElementsByTagName("table");
    for(var x=0; x<details.length; x++) {
        name = details[x].getAttribute("name");
        if (name == 'details') {
            if (details[x].id == select) {
                details[x].style.display = 'block';
            } else {
                details[x].style.display = 'none';
            }
        }
    }
}
function details2(select,namevalue) {
    var details = document.getElementsByTagName("table");
    for(var x=0; x<details.length; x++) {
        name = details[x].getAttribute("name");
        if (name == namevalue) {
            if (details[x].id == select) {
                details[x].style.display = 'block';
            } else {
                details[x].style.display = 'none';
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
function popup(url) {
    window.open(url, "popup_id", "scrollbars,resizable,width=1024,height=756");
    return false;
}
function SwitchShowHideRows (Element, change) {
    var TheRest = document.getElementsByTagName('div');
    var ElementLenght = Element.length;

    if(typeof(change)==='undefined') {
        change = 'switch';
    }

    for(var x=0; x<TheRest.length; x++) {
        var TheClass = TheRest[x].getAttribute('class');
        if (TheClass != null) {
            if (Element == 'init_ready') {
                if (TheClass.indexOf("display_none") != '-1') {
                    TheRest[x].style.display = 'none';
                } else {
                    TheRest[x].style.display = '';
                }
            } else {
                if (TheClass.indexOf(change)!=-1) {
                    if (TheClass.substring(0,ElementLenght) == Element) {
                        if (TheRest[x].style.display == 'none') {
                            TheRest[x].style.display = '';
                        } else {
                            TheRest[x].style.display = 'none';
                        }
                    } else {
                        TheRest[x].style.display = 'none';
                    }
                }
            }
        }
    }
}
function trim(Trim) {
    return Trim.replace(/^\s+|\s+$/g,'');
}
function TextToHtml(Text) {
    Text = Text.replace(/\</g,"&lt;");
    Text = Text.replace(/\>/g,"&gt");
    return Text;
}
function AddKey (ThisValue,Target) {
    var keys = document.getElementById(Target).value.split(',');
    var exists = false;
    for (var i=0; i<keys.length; i++) {
        if (trim(keys[i]).toLowerCase()==ThisValue.toLowerCase()) {
            exists = true;
        }
    }
    if (exists==false) {
        if (document.getElementById(Target).value=="") {
            document.getElementById(Target).value = ThisValue;
        } else {
            document.getElementById(Target).value += ", "+ThisValue;
        }
    }
}
function AddCategory (UseFrom,Target,Language) {
    var exists = false;
    var catlist = document.getElementById(Target);
    var cats = document.getElementsByName('categories['+Language+']');
    var newcat = document.getElementById(UseFrom).value;
    if (newcat!="") {
        for (var i=0; i<cats.length; i++) {
            if (trim(cats[i].value).toLowerCase()==newcat.toLowerCase()) {
                exists = true;
            }
        }
        if (exists==false) {
            var NewRow = catlist.insertRow(-1);
            var NewCell = NewRow.insertCell(0);
            NewCell.innerHTML += '<input type="checkbox" name="categories['+Language+'][]" value="'+newcat+'" checked="checked"> '+TextToHtml(newcat);
        }
    }
}

function creatediv(id, html, width, height, left, top) {
    var newdiv = document.createElement('div');
    newdiv.setAttribute('id', id);
    if (width) {
        newdiv.style.width = 300;
    }
    if (height) {
        newdiv.style.height = 300;
    }
    if ((left || top) || (left && top)) {
        newdiv.style.position = "absolute";
        if (left) {
            newdiv.style.left = left;
        }
        if (top) {
            newdiv.style.top = top;
        }
    }
    newdiv.style.background = "#00C";
    newdiv.style.border = "4px solid #000";
    if (html) {
        newdiv.innerHTML = html;
    } else {
        newdiv.innerHTML = "nothing";
    }
    document.body.appendChild(newdiv);
}
function AddInput (Form,Target,Name) {
    var theTarget = document.getElementById(Target);
    var IPCount = document.getElementsByName(Name).length;
    var newDiv = document.createElement('div');
    IPCount++;
    newDiv.setAttribute('id',IPCount);
    newDiv.innerHTML += '<div id="'+IPCount+'" class="control-group"><label class="control-label" for="inputIPs-'+IPCount+'">IP</label><div class="controls"><input id="inputIPs-'+ IPCount +'" type="text" name="ip[]" value="" maxlength="15" required> <span class="btn btn-mini btn-primary" onclick="Remove('+ IPCount +')"><i class="icon-white icon-remove-sign"></i></span></div>';
    theTarget.appendChild(newDiv);
}
function Remove (ID) {
    var toBeRemoved = document.getElementById(ID);
    toBeRemoved.parentNode.removeChild(toBeRemoved);
}
function post_data (target,id_array) {
    var invisibleTempForm = document.createElement('form');
    invisibleTempForm.method='post';
    invisibleTempForm.setAttribute('target',id_array[0]);
    invisibleTempForm.action = target;
    for (i = 0; i < id_array.length;i++) {
        var TheID = id_array[i];
        var RawData = document.getElementById(TheID);
        var TheText = RawData.value;
        var TheName = RawData.name;
        var UserInput = document.createElement('input') ;
        UserInput.setAttribute('name', TheName);
        UserInput.setAttribute('value', TheText);
        invisibleTempForm.appendChild(UserInput);
    }
    document.body.appendChild(invisibleTempForm);
    invisibleTempForm.submit();
    document.body.removeChild(invisibleTempForm);
}