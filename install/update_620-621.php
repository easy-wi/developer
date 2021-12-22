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

if (isset($include) and $include == true) {

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('6.2.1','<div align=\"right\">22.12.2021</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>General
<ul>
<li>Konfigurationsvariable $coloreddashboard Hinzugefügt</li>
<li>Konfigurationsvariable $easywitweets Hinzugefügt</li>
<li>Konfigurationsvariable $examplemodule . Hinzugefügt</li>
<li>Aktualisierte Social-Media-Links in allen Vorlagen</li>
<li>Dediziertes Servermodul aus dem neuen Design entfernt</li>
<li>Unterstützung für Fontawesome 5 hinzugefügt</li>
<li>Der Twitter Headline-Button kann jetzt auch deaktiviert werden</li>
</ul></li></ul>
<b>Bugfixes:</b>
<ul>
<li>Fix of Fix für Diskspace View auf Masterserver</li>
<li>Fix für Gametemplate Left4Dead und Modfolder</li>
<li>Fix für Discord-Symbole</li>
<li>Behebung des Problems beim Senden von E-Mails</li>
<li>Abmeldeschleife Fix für neues Theme</li>
<li>Socialmedia-Auth-Fix für neues Thema</li>
<li>Mobile Navigation Workaround für neues Design</li>
<li>Steam-CMD Probleme behoben</li>
</ul>
<b>Zukunft:</b>
<li>Vorbereitung um auf eine Neuere PHP Version zu updaten</li>
</ul>','<div align=\"right\">22.12.2021</div>
<b>Changes:</b><br/>
<ul>
<li>General
<ul>
<li>Add Config Variable $coloreddashboard</li>
<li>Add Config Variable $easywitweets</li>
<li>Add Config Variable $examplemodule</li>
<li>Updated Socialmedia links in all Templates</li>
<li>Removed Dedicated Server module from New Theme</li>
<li>Added Fontaweasome 5 Support</li>
<li>Twitter Headline button can be disabled aswell now</li>
</ul></li></ul>
<b>Bugfixes:</b>
<ul>
<li>Fix of Fix for Diskspace View on Masterserver</li>
<li>Fix for Gametemplate Left4Dead and Modfolder</li>
<li>Fix for Discord Icons</li>
<li>Fix for Email send Issue</li>
<li>Log-out loop Fix for New Theme</li>
<li>Socialmedia Auth Fix for New Theme</li>
<li>Mobile Navigation Workaround for New Theme</li>
<li>Fixed Issues with steam CMD</li>
</ul>
<b>Future:</b>
<li>preparation to update PHP</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}
