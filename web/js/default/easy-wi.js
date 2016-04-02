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

    var x, TheClass;
    var TheRest = document.getElementsByTagName('div');
    var amount = TheRest.length;
    var ElementLenght = Element.length;
    var foundAmount = 0;

    if(typeof(change) === 'undefined') {
        change = 'switch';
    }

    for(x = 0; x < amount; x++) {

        TheClass = TheRest[x].getAttribute('class');

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
        for(x = 0; x < amount; x++) {
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

function toggleID (id, value) {
    if (value == 'Y') {
        $(id).show();
    } else {
        $(id).hide();
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
$(function(){
	   $('#submitTest').on('click',function (event){ 
		      $('#smtptestresult').find('div').remove();
		      $('.smtpresult').find('i').remove();
		      event.preventDefault(); // Totally stop stuff happening
		      event.stopPropagation(); // Stop stuff happening
		      
		      // START A LOADING SPINNER HERE
		      $('.boxtest').append('<div class="overlay imagelay"><i class="fa fa-refresh fa-spin"></i></div>');
		      
		            // Create a formdata object and add the files
		      var data = new FormData();
		      
		      //Host
		      data.append('email_settings_host', $('#inputHost').val());
		      
		      //Port
		      data.append('email_settings_port', $('#inputPort').val());
		      
		      //SSL
		      data.append('email_settings_ssl', $('#inputSSL').val());
		      
		      //Username
		      data.append('email_settings_user', $('#inputUser').val());
		      
		      //Passwort
		      data.append('email_settings_password', $('#inputPassword').val());
		      
		      //E-Mail
		      data.append('inputEmail', $('#inputEmail').val());
		      
		      console.log(data);
		      
		      $.ajax({
		                url: 'ajaxfunctions.php?testsmtp',
		                type: 'POST',
		                data: data,
		                cache: false,
		                dataType: 'json',
		                processData: false, // Don't process the files
		                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
		                success: function(data, textStatus, jqXHR)
		                {
		                 if(typeof data.error === 'undefined')
		                 {
					         console.log('SUCCESS: ' + data.success);
					         $('#smtptestresult').append('<div><span style="font-size:2em;color:#00a65a;" class="fa fa-check"></span>'+data.success+'</div>');
					         $('.smtp').removeClass('has-error');
					         $('.smtp').addClass('has-success');
					         $('.smtpresult').append('<i class="fa fa-check"></i>');
		                 }
		                 else
		                 {
		                  // Handle errors here
		                  console.log('ERRORS: ' + data.error);
		                  $('#smtptestresult').append('<div><span style="font-size:2em;color:#d73925;" class="fa fa-times"></span>'+data.error+'</div>');
		                  $('.smtp').addClass('has-error');
		                 }
		                },
		                error: function(jqXHR, textStatus, errorThrown)
		                {
		                 // Handle errors here
		                 console.log('ERRORS: ' + textStatus);
		                 $('#smtptestresult').append('<div><span style="font-size:2em;color:#d73925;" class="fa fa-times"></span>'+data.error+'</div>');
		                 $('.smtp').addClass('has-error');
		                 // STOP LOADING SPINNER
		                },
		                complete: function()
		                {
		                 // STOP LOADING SPINNER
		        		$( "div" ).remove( ".imagelay" );
		                }
		            });
		      });
});