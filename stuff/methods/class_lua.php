<?php

/**
 * File: class_lua.php.
 * Author: Ulrich Block
 * Date: 17.07.16
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

class Lua {

    public static function luaToArray($string) {
        $Lua = new Lua;
        return $Lua->parseBlocks($string);
    }

    private function parseBlocks($string) {

        $parsedBlocks = array();

        preg_match_all('/([^\s]+)[\s]*=[\s]*[\s]+(\{[\s\S]+?[\}](?:[\r\n\s,]*\})*)/m', $string, $blocks, PREG_SET_ORDER);

        foreach ($blocks as $block) {

            if (isset($block[1]) and isset($block[2])) {

                $parsedBlocks[$block[1]] = $this->parseBlocks($block[2]);

                foreach ($this->parseBlock($block[2], $this->getIndention($block[2])) as $key => $value) {
                    $parsedBlocks[$block[1]][$key] = $value;
                }
            }
        }

        return $parsedBlocks;
    }

    private function parseBlock($string, $indention) {

        $parsedBlock = array();

        preg_match_all('/' . $indention . '([^\s]+)[\s]*=[\s]*(?:([\d.]+)|"([\s\S]+?)"),?/', $string, $keyValues, PREG_SET_ORDER);

        foreach($keyValues as $keyValue) {
            if (isset($keyValue[1]) and (isset($keyValue[2]) or isset($keyValue[3]))) {
                $parsedBlock[$keyValue[1]] = (isset($keyValue[3])) ? $keyValue[3] : $keyValue[2];
            }
        }

        return $parsedBlock;
    }

    private function getIndention($string) {

        preg_match('/[\r\n]+(\s+)/', $string, $indention);

        return '[\r\n]+' . $indention[1];
    }

    public static function arrayToLua($array) {
        $Lua = new Lua;
        return $Lua->arrayOrValue($array, '');
    }

    private function arrayOrValue($array, $indention) {

        $newIndention = '    ' . $indention;
        $luaStringParts = array();

        foreach ($array as $key => $value) {

            if (is_array($value)) {
                $luaStringParts[] = $indention . $key . " =\n" . $indention . "{" . $this->arrayOrValue($value, $newIndention) . "\n$indention}";
            } else {
                $luaStringParts[] = $indention . $key . ' = ' . $value;
            }
        }

        if ($indention === '') {
            return implode(",\n", $luaStringParts);
        }

        return "\n" . implode(",\n", $luaStringParts);
    }
}