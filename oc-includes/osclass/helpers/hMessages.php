<?php

    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */


    // Adds an ephemeral message to the session.
    function osc_add_flash_message($msg, $section = 'pubMessages') {
        Session::newInstance()->_setMessage($section, $msg, 'error') ;
    }

    function osc_add_flash_ok_message($msg, $section = 'pubMessages') {
        Session::newInstance()->_setMessage($section, $msg, 'ok') ;
    }

    function osc_add_flash_error_message($msg, $section = 'pubMessages') {
        Session::newInstance()->_setMessage($section, $msg, 'error') ;
    }

    function osc_add_flash_info_message($msg, $section = 'pubMessages') {
        Session::newInstance()->_setMessage($section, $msg, 'info') ;
    }

    //Shows all the pending flash messages in session and cleans up the array.
    function osc_show_flash_message($section = 'pubMessages', $class = "FlashMessage", $id = "FlashMessage") {
        $message = Session::newInstance()->_getMessage($section) ;

        echo '<div id="flash_js"></div>';
        
        if (isset($message['msg']) && $message['msg'] != '') {
            echo '<div id="' . $id . '" class="' . $class . ' ' . $message['type'] . '">' ;
                echo $message['msg'];
            echo '</div>' ;

            Session::newInstance()->_dropMessage($section) ;
        } else if($message!='') {
            echo '<div id="' . $id . '" class="' . $class . '">' ;
                echo $message;
            echo '</div>' ;

            Session::newInstance()->_dropMessage($section) ;
        }
    }

?>