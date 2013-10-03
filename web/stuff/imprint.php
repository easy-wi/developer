<?php

/**
 * File: imprint.php.
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

if (!isset($reseller_id)) $reseller_id = 0;
$sprache=(isset($user_language)) ? getlanguagefile('images',$user_language,$reseller_id) : getlanguagefile('images',$page_language,$reseller_id);
if (isset($admin_id) and $admin_id==$reseller_id) {
	$resellerid = 0;
} else if (isset($reseller_id)) {
	$resellerid=$reseller_id;
} else {
	$resellerid = 0;
}
$query = $sql->prepare("SELECT `imprint` FROM `imprints` WHERE language=? AND resellerid=? LIMIT 1");
$query->execute(array($user_language,$resellerid));
$imprint=$query->fetchColumn();
if ($imprint != '') {
    $query = $sql->prepare("SELECT `language` FROM `settings` WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($resellerid));
    $defaultlanguage=$query->fetchColumn();
    $query = $sql->prepare("SELECT `imprint` FROM `imprints` WHERE language=? AND resellerid=? LIMIT 1");
    $query->execute(array($defaultlanguage,$resellerid));
    $imprint=$query->fetchColumn();
}
if (isset($page_data)) {
    $page_data->setCanonicalUrl($s);

    // https://github.com/easy-wi/developer/issues/62
    $langLinks = array();
    foreach ($languages as $l) {
        $tempLanguage = getlanguagefile('general',$l,0);
        $langLinks[$l]=($page_data->seo== 'Y') ? szrp($tempLanguage->$s)  : '?s='.$s;
    }
    $page_data->langLinks($langLinks);
}
$template_file = "imprint.tpl";