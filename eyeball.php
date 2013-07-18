<?php
/**
 * Plugin Name: Eyeball Licks
 * Plugin URI: http://www.mrstk.net
 * Description: A response ot the Japanese trend of eyeball licking to show affection, lick a posts eyeball!
 * Author: Clorith
 * Version: 1.0
 * Author URI: http://www.mrstk.net
 * License: GPL2
 *
 * Copyright 2013 Marius Jensen (email : marius@jits.no)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Function for inserting an eyeball licking form after the_content thanks to the_content hook
 *
 * @param $content
 * @return string
 */
function eye_post_suffix( $content ) {
    /**
     * We need post details, so we globalize the $post variable
     */
    global $post;

    /**
     * Get the current amount of licks
     */
    $licks = get_post_meta( $post->ID, 'eyeball_licks', true );

    /**
     * Output our form for licking, we also check if this post has been licked before, if it has add the lick count
     */
    $content .= '
        <form action="" method="post">
            <input type="hidden" name="eyeball_post" value="' . $post->ID . '" />
            <button type="submit" class="eyeball_lick_button" style="background: transparent; border: none;">
                <img src="' . plugins_url( 'eyeball.png', __FILE__ ) . '" style="height: 25px;" alt="Lick an eyeball" />
                <span>
                    Show your appreciation, lick an eyeball!' . ( ! empty( $licks ) ? ' (You will be sharing germs with ' . $licks . ' others)' : '' ) . '
                </span>
            </button>
        </form>
    ';

    return $content;
}

/**
 * The eye_post_form_handler is triggered by the "init" hook to run on every page
 *
 * It will only perform an action if our form has been submitted for eyeballs
 */
function eye_post_form_handler() {
    if ( isset( $_POST['eyeball_post'] ) )
    {
        /**
         * $licks holds the post's meta data for amount of licks to date
         * $lickers are the users who has already licked this eyeball
         */
        $licks = get_post_meta( $_POST['eyeball_post'], 'eyeball_licks', true );
        $lickers = get_post_meta( $_POST['eyeball_post'], 'eyeball_lickers', true );

        /**
         * If the $licks meta hasn't been set before, it will be empty, we set it to 0 if it's new
         */
        if ( empty( $licks ) )
            $licks = 0;

        $licks++;

        /**
         * If $lickers isn't set already (new eyeball), make it into an array
         * If it has data, unserialize that data (DB stored array)
         */
        if ( empty( $lickers ) )
            $lickers = array();
        else
            $lickers = unserialize( $lickers );

        /**
         * If the current IP has already licked the eyeball, we don't want to do anything, they only get one lick each!
         */
        if ( in_array( $_SERVER['REMOTE_ADDR'], $lickers ) )
            return;

        /**
         * The user hasnt' licked this one before, great, add him to the licked list
         */
        $lickers[] = $_SERVER['REMOTE_ADDR'];

        /**
         * Update the post metas, $lickers is serialized as it is an array and we want to retain the data type
         */
        update_post_meta( $_POST['eyeball_post'], 'eyeball_lickers', serialize( $lickers ) );
        update_post_meta( $_POST['eyeball_post'], 'eyeball_licks', $licks );
    }
}

/**
 * Our hooks, attaching to WordPress events
 */
add_action( 'the_content', 'eye_post_suffix' );
add_action( 'init', 'eye_post_form_handler' );