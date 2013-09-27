<?php
/**
 * File: index.php.
 * Author: Ulrich Block
 * Date: 03.10.12
 * Time: 17:09
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

$main=1;
$page_include=1;
define('EASYWIDIR', dirname(__FILE__));
include(EASYWIDIR . '/stuff/vorlage.php');
include(EASYWIDIR . '/stuff/class_validator.php');
include(EASYWIDIR . '/stuff/functions.php');
include(EASYWIDIR . '/stuff/settings.php');
if (isset ($page_active) and $page_active=='Y') {
    include(EASYWIDIR . '/stuff/init_page.php');
    if (isset($throw404)) {
        $template_file='page_404.tpl';
    } else if (isset($what_to_be_included_array[$s]) and is_file(EASYWIDIR.'/stuff/'.$what_to_be_included_array[$s])) {
        include(EASYWIDIR . '/stuff/'.$what_to_be_included_array[$s]);
    } else if (isset($what_to_be_included_array[$s]) and is_file(EASYWIDIR.'/'.$what_to_be_included_array[$s])) {
        include(EASYWIDIR . '/'.$what_to_be_included_array[$s]);
    } else if (isset($s) and !isset($what_to_be_included_array[$s])) {
        $template_file='page_404.tpl';
    } else {
        $template_file='page_home.tpl';
    }
    unset($dbConnect);
    include(IncludeTemplate($template_to_use,'page_header.tpl'));
    include(IncludeTemplate($template_to_use,(isset($template_file) and preg_match('/^(.*)\.[\w]{1,}$/',$template_file)) ? $template_file : 'page_general.tpl'));
    include(IncludeTemplate($template_to_use,'page_footer.tpl'));
} else {
    redirect('login.php');
}
$sql=null;