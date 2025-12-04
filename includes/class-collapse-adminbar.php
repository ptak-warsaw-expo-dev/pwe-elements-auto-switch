<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class Collapse_Adminbar {

    public static function init(){
        add_action( 'admin_bar_init', [ __CLASS__, 'hooks' ] );
    }

    public static function hooks(){

        // remove html margin bumps
        remove_action( 'wp_head', '_admin_bar_bump_cb' );

        add_action( 'wp_head', [ __CLASS__, 'collapse_styles' ] );
    }

    public static function collapse_styles(){

        if( is_admin() ){
            return;
        }

        ob_start();
        ?>
        <style id="collapse_admin_bar">
            @media screen and ( max-width: 600px ) {
                #wpadminbar{ background:none; float:left; width:auto; height:auto; bottom:0; min-width:0 !important; }
                #wpadminbar > *{ float:left !important; clear:both !important; }
                #wpadminbar .ab-top-menu li{ float:none !important; }
                #wpadminbar .ab-top-secondary{ float: none !important; }
                #wpadminbar .ab-top-menu>.menupop>.ab-sub-wrapper{ top:0; left:100%; white-space:nowrap; }
                #wpadminbar .quicklinks>ul>li>a{ padding-right:17px; }
                #wpadminbar .ab-top-secondary .menupop .ab-sub-wrapper{ left:100%; right:auto; }
                html{ margin-top:0!important; }

                #wpadminbar{ overflow:hidden; width:auto; height:30px; }
                #wpadminbar:hover{ overflow:visible; width:auto; height:auto; background:rgba(102,102,102,.7); }

                #wp-admin-bar-<?= is_multisite() ? 'my-sites' : 'site-name' ?> .ab-item:before{ color:#797c7d; }
                #wp-admin-bar-wp-logo{ display:none; }
                #wp-admin-bar-search{ display:none; }
                body.admin-bar:before{ display:none; }
                body.logged-in.admin-bar { padding-top: 0 !important; }

                #wpwrap .edit-post-header{ top:0; }
                #wpwrap .edit-post-sidebar{ top:56px; }
            }   
        </style>
        <?php
        $styles = ob_get_clean();

        echo preg_replace( '/[\n\t]/', '', $styles ) . "\n";
    }
}

Collapse_Adminbar::init();