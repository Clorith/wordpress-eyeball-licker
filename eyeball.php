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

function eye_post_suffix( $content ) {
    global $post;

    $licks = get_post_meta( $post->ID, 'eyeball_licks', true );

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

function eye_post_form_handler() {
    if ( isset( $_POST['eyeball_post'] ) )
    {
        $licks = get_post_meta( $_POST['eyeball_post'], 'eyeball_licks', true );
        if ( empty( $licks ) )
            $licks = 0;

        $licks++;

        update_post_meta( $_POST['eyeball_post'], 'eyeball_licks', $licks );
    }
}

add_action( 'the_content', 'eye_post_suffix' );
add_action( 'init', 'eye_post_form_handler' );