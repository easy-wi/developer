<?php

/**
 * File: update_616-620.php.
 * Author: Ulrich Block
 * Date: 19.07.20
 * Time: 18:38
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

if (isset($include) and $include == true and isset($devVersion)) {

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('6.2.0.1','<div align=\"right\">02.08.2020</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>General
<ul>
<li>Config Variable <b>\$coloreddashboard</b> Hinzugefügt</li>
<li>Config Variable <b>\$easywitweets</b> Hinzugefügt</li>
<li>Config Variable <b>\$examplemodule</b> Hinzugefügt</li>
<li>Updated Socialmedia links in allen Templates</li>
<li>Entfernung des Dedicated Server module vom <b>New</b> Theme</li>
<li>Fontaweasome 5 Support/li>
<li>Twitter Header button kann nun entfernt werden</li>
</ul></li></ul>
<b>Bugfixes:</b>
<ul>
<li>Speichernutzung auf dem Masterserver Workaround Fix</li>
<li>Spieletemplate: Left4Dead und Modfolder Fix</li>
<li>Discord Icons Fix</li>
<li>E-Mail Sende Bug Fix</li>
<li>Logout im <b>New</b> Theme Fix</li>
<li>Socialmedia Login Fix fürs <b>New</b> Theme</li>
<li>Mobile Navigation Workaround für <b>New</b> Theme</li>
<li>Steam CMD issues Fixed</li>
</ul>
</ul>','<div align=\"right\">02.08.2020</div>
<b>Changes:</b><br/>
<ul>
<li>General
<ul>
<li>Add Config Variable <b>\$coloreddashboard</b></li>
<li>Add Config Variable <b>\$easywitweets</b></li>
<li>Add Config Variable <b>\$examplemodule</b></li>
<li>Updated Socialmedia links in all Templates</li>
<li>Removed Dedicated Server module from <b>New</b> Theme</li>
<li>Added Fontaweasome 5 Support</li>
<li>Twitter Headline button can be disabled aswell now</li>
</ul></li></ul>
<b>Bugfixes:</b>
<ul>
<li>Fix of Fix for Diskspace View on Masterserver/li>
<li>Gametemplate: Fix for Left4Dead and Modfolder</li>
<li>Fix for Discord Icons</li>
<li>Fix for Email send Issue</li>
<li>Log-out loop Fix for <b>New</b> Theme</li>
<li>Socialmedia Auth Fix for <b>New</b> Theme/li>
<li>Mobile Navigation Workaround for <b>New</b> Theme</li>
<li>Steam CMD issues Fixed</li>
</ul>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');

    require_once(EASYWIDIR . '/stuff/config.php');

    @copy(EASYWIDIR . '/stuff/config.php',EASYWIDIR . '/tmp/config.php.bak');
    $configFp = @fopen(EASYWIDIR . '/stuff/config.php', "a");
    if ($configFp) {
        $configdata = "";
        if(!isset($coloreddashboard)){
            $configdata .= "" . '$coloreddashboard' . " = false;" . PHP_EOL;
        }
        if(!isset($easywitweets)){
            $configdata .= "" . '$easywitweets' . " = true;" . PHP_EOL;
        }
        if(!isset($examplemodule)){
            $configdata .= "" . '$examplemodule' . " = false;" . PHP_EOL;
        }
        @fwrite($configFp, $configdata);
        fclose($configFp);
    }

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}
