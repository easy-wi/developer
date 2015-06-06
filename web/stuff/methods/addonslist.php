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
        ':addon' => 'metamod-latest-1.10',
        ':type' => 'tool',
        ':folder' => 'metamod',
        ':menudescription' => 'Metamod Source Latest 1.10',
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
        ':addon' => 'metamod-latest-1.11',
        ':type' => 'tool',
        ':folder' => 'metamod',
        ':menudescription' => 'Metamod Source Latest 1.11',
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
        ':addon' => 'sourcemod-latest_1.7',
        ':type' => 'tool',
        ':folder' => 'sourcemod',
        ':menudescription' => 'SourceMod Latest 1.7',
        ':configs' => "cfg/sourcemod/sourcemod.cfg both\r\ncfg/sourcemod/sm_warmode_on.cfg full\r\ncfg/sourcemod/sm_warmode_off.cfg full\r\ncfg/sourcemod/funcommands.cfg both\r\ncfg/sourcemod/mapchooser.cfg both\r\ncfg/sourcemod/randomcycle.cfg both\r\ncfg/sourcemod/rtv.cfg both",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'metamod-latest-1.10',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'sourcemod-latest_1.8',
        ':type' => 'tool',
        ':folder' => 'sourcemod',
        ':menudescription' => 'SourceMod Latest 1.8',
        ':configs' => "cfg/sourcemod/sourcemod.cfg both\r\ncfg/sourcemod/sm_warmode_on.cfg full\r\ncfg/sourcemod/sm_warmode_off.cfg full\r\ncfg/sourcemod/funcommands.cfg both\r\ncfg/sourcemod/mapchooser.cfg both\r\ncfg/sourcemod/randomcycle.cfg both\r\ncfg/sourcemod/rtv.cfg both",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'metamod-latest-1.11',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'css-cp',
        ':type' => 'tool',
        ':folder' => '',
        ':menudescription' => 'SM_CheckpointSaver',
        ':configs' => "cfg/sourcemod/sm_cpsaver.cfg full",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'sourcemod',
        ':supported' => array(
            'css'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'css-stamm',
        ':type' => 'tool',
        ':folder' => 'stamm',
        ':menudescription' => 'Stamm Plugin',
        ':configs' => "cfg/stamm/LevelSettings.txt full\r\ncfg/stamm/ModelDownloads.txt full\r\ncfg/stamm/ModelSettings.txt full\r\ncfg/stamm/stamm_config.cfg full",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'sourcemod',
        ':supported' => array(
            'css'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'parachute',
        ':type' => 'tool',
        ':folder' => '',
        ':menudescription' => 'Parachute',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'sourcemod',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'saysounds',
        ':type' => 'tool',
        ':folder' => '',
        ':menudescription' => 'SaySounds',
        ':configs' => "cfg/sourcemod/sm_saysounds.cfg\r\naddons/sourcemod/configs/saysounds.cfg",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'sourcemod',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'smac',
        ':type' => 'tool',
        ':folder' => '',
        ':menudescription' => 'SourceMod Anti-Cheat',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'sourcemod',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'smac-block',
        ':type' => 'tool',
        ':folder' => '',
        ':menudescription' => 'SmacBans.com Block Plugin',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => 'smac',
        ':supported' => array(
            'ageofchivalry','css','csgo','dods','gmod','hl2mp','insurgency','left4dead','left4dead2','pvkii','tf','zps'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'css-anno',
        ':type' => 'tool',
        ':folder' => 'css-anno',
        ':menudescription' => 'SmacBans.com Announcer',
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
        ':addon' => 'csgo-anno',
        ':type' => 'tool',
        ':folder' => 'csgo-anno',
        ':menudescription' => 'SMACBans Announcer',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'csgo'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'dods-anno',
        ':type' => 'tool',
        ':folder' => 'dods-anno',
        ':menudescription' => 'SMACBans Announcer',
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
        ':addon' => 'l4d-anno',
        ':type' => 'tool',
        ':folder' => 'dods-anno',
        ':menudescription' => 'SMACBans Announcer',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'left4dead'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'l4d2-anno',
        ':type' => 'tool',
        ':folder' => 'l4d2-anno',
        ':menudescription' => 'SMACBans Announcer',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'left4dead2'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'tf-anno',
        ':type' => 'tool',
        ':folder' => 'tf-anno',
        ':menudescription' => 'SMACBans Announcer',
        ':configs' => "",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'tf'
        )
    ),
    array(
        ':paddon' => 'Y',
        ':addon' => 'zblock',
        ':type' => 'tool',
        ':folder' => 'zblock',
        ':menudescription' => 'zBlock',
        ':configs' => "cfg/zblock.cfg both",
        ':cmd' => null,
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'css'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'mpaseco',
        ':type' => 'tool',
        ':folder' => '',
        ':menudescription' => 'Servercontroller MPAseco',
        ':configs' => "servercontroller/configs/config.xml\r\nservercontroller/configs/localdatabase.xml\r\nservercontroller/configs/plugins.xml",
        ':cmd' => '/mpaseco',
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'shootmania'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'xaseco2',
        ':type' => 'tool',
        ':folder' => '',
        ':menudescription' => 'Servercontroller XAseco2',
        ':configs' => "servercontroller/config.xml\r\nservercontroller/localdatabase.xml\r\nservercontroller/plugins.xml",
        ':cmd' => '/xaseco',
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'trackmania'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'Instagib',
        ':type' => 'tool',
        ':folder' => '',
        ':menudescription' => 'Instagib',
        ':configs' => "",
        ':cmd' => '+set g_instagib "1"',
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'warsow'
        )
    ),
    array(
        ':paddon' => 'N',
        ':addon' => 'instajump',
        ':type' => 'tool',
        ':folder' => '',
        ':menudescription' => 'InstaJump',
        ':configs' => "",
        ':cmd' => '+seta g_instajump "1"',
        ':rmcmd' => null,
        ':depends' => '',
        ':supported' => array(
            'warsow'
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