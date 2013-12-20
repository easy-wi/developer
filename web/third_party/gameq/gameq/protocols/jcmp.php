<?php
/**
 * This file is part of GameQ.
 *
 * GameQ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GameQ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Just Cause 2 Multiplayer Protocol Class
 *
 * @author Ulrich Block <ulblock@gmx.de>
 */
class GameQ_Protocols_Jcmp extends GameQ_Protocols_Source
{
    protected $name = "jcmp";
    protected $name_long = "Just Cause 2 Multiplayer";

    // Source Query is not able to return larger player amounts. Map field is used for player return.
    function process_details()
    {
        $return = parent::process_details();

        @list($return['num_players'], $return['max_players']) = explode('/', str_replace('Players: ', '', $return['map']));

        $return['map'] = $return['game_dir'];

        return $return;
    }
}