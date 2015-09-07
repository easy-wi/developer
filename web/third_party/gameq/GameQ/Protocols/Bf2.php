<?php
/**
 * This file is part of GameQ.
 *
 * GameQ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GameQ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace GameQ\Protocols;

use GameQ\Protocol;
use GameQ\Buffer;

/**
 * Battlefield 2 Protocol Class
 *
 * @package GameQ\Protocols
 * @author  Austin Bischoff <austin@codebeard.com>
 */
class Bf2 extends Gamespy3
{

    /**
     * Array of packets we want to query.
     *
     * @type array
     */
    protected $packets = [
        self::PACKET_ALL => "\xFE\xFD\x00\x10\x20\x30\x40\xFF\xFF\xFF\x01",
    ];

    /**
     * String name of this protocol class
     *
     * @type string
     */
    protected $name = 'bf2';

    /**
     * Longer string name of this protocol class
     *
     * @type string
     */
    protected $name_long = "Battlefield 2";
}