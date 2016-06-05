<?php

/**
 * File: addonslist.php.
 * Author: Ulrich Block
 * Date: 14.12.13
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

$gameAddons = array(
    array(
        ':paddon' => 'N',
        ':addon' => 'metamod',
        ':type' => 'tool',
        ':folder' => 'metamod',
        ':menudescription' => 'Metamod Source',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'metamod-dev',
        ':type' => 'tool',
        ':folder' => 'metamod',
        ':menudescription' => 'Metamod Source Dev Snapshot',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'sourcemod',
        ':type' => 'tool',
        ':folder' => 'sourcemod',
        ':menudescription' => 'SourceMod',
        ':configs' => "cfg/sourcemod/sourcemod.cfg both\r\ncfg/sourcemod/sm_warmode_on.cfg full\r\ncfg/sourcemod/sm_warmode_off.cfg full\r\ncfg/sourcemod/funcommands.cfg both\r\ncfg/sourcemod/mapchooser.cfg both\r\ncfg/sourcemod/randomcycle.cfg both\r\ncfg/sourcemod/rtv.cfg both",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'metamod',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'sourcemod-dev',
        ':type' => 'tool',
        ':folder' => 'sourcemod',
        ':menudescription' => 'SourceMod Dev Snapshot',
        ':configs' => "cfg/sourcemod/sourcemod.cfg both\r\ncfg/sourcemod/sm_warmode_on.cfg full\r\ncfg/sourcemod/sm_warmode_off.cfg full\r\ncfg/sourcemod/funcommands.cfg both\r\ncfg/sourcemod/mapchooser.cfg both\r\ncfg/sourcemod/randomcycle.cfg both\r\ncfg/sourcemod/rtv.cfg both",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'metamod-latest-1.11',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'css-bhop-maps',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'Bhop Maps',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'css'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'css-gungame',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'Gungame Maps',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'css'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'css-eslmaps',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'ESL Maps',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'css'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'dods-aim',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'Aim Maps',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'dods'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'dods-custom',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'Custom Maps',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'dods'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'dods-eslmaps',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'ESL Maps',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'dods'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'dods-fun',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'Fun Maps',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'dods'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'dods-gungame',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'Gun Game Maps',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'dods'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'dods-gungame2',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'Gun Game Maps 2',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'dods'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'dods-orange',
        ':type' => 'map',
        ':folder' => '',
        ':menudescription' => 'Orange Maps',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'dods'
        )
    )
);