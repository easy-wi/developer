/**
 * File: easy-wi_cms.js.
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


function post_data (language) {

    var invisibleTempForm = document.createElement('form');
    invisibleTempForm.method = 'post';
    invisibleTempForm.action = 'index.php?site=news&preview=true';
    invisibleTempForm.setAttribute('target', 'text-' + language);

    var userInput = document.createElement('input') ;
    userInput.setAttribute('name', 'title[' + language + ']');
    userInput.setAttribute('value', $('#title-' + language).val());
    invisibleTempForm.appendChild(userInput);

    userInput = document.createElement('input');
    userInput.setAttribute('name', 'text[' + language + ']');
    userInput.setAttribute('value', $('#text-' + language).text());
    invisibleTempForm.appendChild(userInput);

    document.body.appendChild(invisibleTempForm);

    invisibleTempForm.submit();

    document.body.removeChild(invisibleTempForm);
}

function submitToForm() {

    var language, checkbox;

    $( "input[name='language[]']" ).each(function() {

        checkbox = $(this);
        language = checkbox.val();

        $('#textValue-' + language).html($('#text-' + language).code());
    });
}

function trim(Trim) {
    return Trim.replace(/^\s+|\s+$/g,'');
}

function AddKey(clickedObject, target) {

    console.log(target);
    var targetObject = $('#' + target);
    var keys = targetObject.val().split(',');
    var exists = false;
    var value = clickedObject.value;

    for (var i=0; i<keys.length; i++) {
        if (trim(keys[i]).toLowerCase() == value.toLowerCase()) {
            exists = true;
        }
    }

    if (exists == false) {
        if (targetObject.val() == "") {
            targetObject.val(value);
        } else {
            targetObject.val(targetObject.val() + ", " + value);
        }
    }

    clickedObject.remove();
}