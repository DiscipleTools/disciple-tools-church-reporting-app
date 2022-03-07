<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


/**
 * Class DT_Reporting_App_Portal
 */
class DT_Reporting_App_Portal extends DT_Magic_Url_Base {

    public $page_title = 'Reporting Portal';
    public $page_description = 'This is a portal for reporting church multiplication and community practitioner profile.';
    public $root = "reporting_app";
    public $type = 'portal';
    public $type_name = 'Reporting Portal';
    public $show_bulk_send = true;
    public $show_app_tile = true;
    public $root_url;
    private $meta_key;
    public $post_id;
    public $post;
    public $post_type = 'contacts';
    public $type_actions = [
        '' => "Home",
        'profile' => "Profile",
        'list' => "List View",
        'map' => "Map View",
        'goals_map' => "Goals Map View",
        'setup' => "Setup View",
    ];
    public $us_div = 2500; // this is 2 for every 5000
    public $global_div = 25000; // this equals 2 for every 50000

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        $this->meta_key = $this->root . '_' . $this->type . '_magic_key';
        parent::__construct();

        $this->page_title = __( 'Reporting Portal' );

        add_action( 'rest_api_init', [ $this, 'add_endpoints' ] );
        add_filter( 'dt_settings_apps_list', [ $this, 'dt_settings_apps_list' ], 10, 1 );

        /**
         * tests if other URL
         */
        $url = dt_get_url_path();
        if ( strpos( $url, $this->root . '/' . $this->type ) === false ) {
            return;
        }

        if ( empty( $this->parts['public_key'] ) ) {
            wp_redirect( site_url() . '/reporting_app/access' );
            return;
        }
        /**
         * tests magic link parts are registered and have valid elements
         */
        if ( !$this->check_parts_match() ){
            return;
        }

        // load if valid url
        $this->root_url = site_url() . '/' . $this->parts['root'] . '/' . $this->parts['type'] . '/' . $this->parts['public_key'] . '/';

        if ( 'list' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'list_body' ] );
        }
        else if ( 'map' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'map_body' ] );
        }
        else if ( 'goals_map' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'map_goals_body' ] );
        }
        else if ( 'profile' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'profile_body' ] );
        }
        else if ( 'setup' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'setup_body' ] );
        }
        else if ( '' === $this->parts['action'] ) {
            add_action( 'dt_blank_body', [ $this, 'home_body' ] );
        } else {
            return;
        }

        // load if valid url
        add_filter( 'dt_magic_url_base_allowed_css', [ $this, 'dt_magic_url_base_allowed_css' ], 10, 1 );
        add_filter( 'dt_magic_url_base_allowed_js', [ $this, 'dt_magic_url_base_allowed_js' ], 10, 1 );
        add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ], 99 );
    }


    public function dt_settings_apps_list( $apps_list ) {
        $apps_list[ $this->meta_key ] = [
            'key'              => $this->meta_key,
            'url_base'         => $this->root . '/' . $this->type,
            'label'            => $this->page_title,
            'description'      => $this->page_description,
            'settings_display' => true,
        ];

        return $apps_list;
    }

    public function dt_magic_url_base_allowed_js( $allowed_js ) {

        $allowed_js[] = 'jquery-touch-punch';
        $allowed_js[] = 'mapbox-gl';
        $allowed_js[] = 'lodash';
        $allowed_js[] = 'introjs-js';
        $allowed_js[] = 'jquery-cookie';
        $allowed_js[] = 'portal';

        if ( 'map' === $this->parts['action'] ) {
            $allowed_js[] = 'mapbox-cookie';
        }
        else if ( 'goals_map' === $this->parts['action'] ) {
            $allowed_js[] = 'heatmap-js';
            $allowed_js[] = 'mapbox-cookie';
        }
        else if ( 'list' === $this->parts['action'] ) {
            $allowed_js[] = 'portal-app-domenu-js';
        }

        return $allowed_js;
    }

    public function dt_magic_url_base_allowed_css( $allowed_css ) {

        $allowed_css[] = 'mapbox-gl-css';
        $allowed_css[] = 'introjs-css';
        $allowed_css[] = 'portal';

        if ( 'goals_map' === $this->parts['action'] ) {
            $allowed_css[] = 'heatmap-css';
        }
        else if ( 'list' === $this->parts['action'] ) {
            $allowed_css[] = 'portal-app-domenu-css';
        }

        return $allowed_css;
    }

    public function _wp_enqueue_scripts() {
        wp_enqueue_script( 'lodash' );
        wp_register_script( 'jquery-touch-punch', '/wp-includes/js/jquery/jquery.ui.touch-punch.js' ); // @phpcs:ignore

        /* intro js */
        wp_enqueue_script( 'introjs-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'intro.min.js', [ 'jquery' ],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'intro.min.js' ), true );

        wp_enqueue_style( 'introjs-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'introjs.min.css', [],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'introjs.min.css' ) );

        /* jquery cookie */
        wp_enqueue_script( 'jquery-cookie', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js.cookie.min.js', [ 'jquery' ],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'js.cookie.min.js' ), true );

        /* group-gen */
        wp_enqueue_script( 'portal', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'portal.js', [ 'jquery' ],
        filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'portal.js' ), true );

        if ( 'map' === $this->parts['action'] ) {

            wp_enqueue_script( 'mapbox-cookie', trailingslashit( get_stylesheet_directory_uri() ) . 'dt-mapping/geocode-api/mapbox-cookie.js', [ 'jquery', 'jquery-cookie' ], '3.0.0' );
        }
        else if ( 'goals_map' === $this->parts['action'] ) {

            /* heatmap */
            wp_enqueue_script( 'heatmap-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.js', [],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'heatmap.js' ), true );

            wp_enqueue_style( 'heatmap-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'heatmap.css', [],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'heatmap.css' ) );

            wp_enqueue_script( 'mapbox-cookie', trailingslashit( get_stylesheet_directory_uri() ) . 'dt-mapping/geocode-api/mapbox-cookie.js', [ 'jquery', 'jquery-cookie' ], '3.0.0' );
        }
        else if ( 'list' === $this->parts['action'] ) {

            /* domenu */
            wp_enqueue_script( 'portal-app-domenu-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'jquery.domenu-0.100.77.min.js', [ 'jquery' ],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'jquery.domenu-0.100.77.min.js' ), true );

            wp_enqueue_style( 'portal-app-domenu-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'jquery.domenu-0.100.77.css', [],
            filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) .'jquery.domenu-0.100.77.css' ) );
        }

    }

    public function header_style(){
        DT_Mapbox_API::geocoder_scripts();
        ?>
        <style>
            body {
                background-color: white !important;
                padding: 0 .2rem;
            }
            #wrapper {
                padding:0 .5rem;
                margin: 0 auto;
            }
            #offCanvasLeft ul {
                list-style-type: none;
            }
            #location-status {
                height:1.5rem;
                width:1.5rem;
                border-radius: 50%;
                position: absolute;
                bottom: 20px;
                right: 20px;
                z-index: 100;
            }
            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }
            .loading-field-spinner.active {
                border-radius: 50%;
                width: 24px;
                height: 24px;
                border: 0.25rem solid #919191;
                border-top-color: black;
                animation: spin 1s infinite linear;
                display: inline-block;
            }
            .wrapper-field-spinner {
                padding: 5px 5px 0;
            }
            #initial-loading-spinner {
                padding-top: 10px;
            }
            .mapboxgl-ctrl-top-right.mapboxgl-ctrl{
                width:100% !important;
                margin:10px !important;
            }
            #map-edit, #map-wrapper-edit  {
                height: 300px !important;
            }
            .float{
                position:fixed;
                width: 40px;
                height: 40px;
                top: 10px;
                border: 1px solid white;
                background-color: #4CAF50;
                color:#FFF;
                border-radius:50px;
                text-align:center;
                box-shadow: 2px 2px 3px #999;
                z-index:10;
                cursor: pointer;
            }
            .floating.fi-plus:before {
                margin-top: 12px;
            }
            .fi-plus.add-new-green {
                border: 1px solid white;
                border-radius: 50px;
                padding:5px 9px;
                background-color: #4CAF50;
            }
            .fi-list {
                font-size:2em;
                color:black;
            }

        </style>
        <?php
    }

    public function header_javascript(){
        if ( '' === $this->parts['action'] ) {
            ?>
            <script>
                jQuery(document).ready(function(){
                    window.onload = () => {
                        let viewed = Cookies.get('portal_intro_home')
                        if ( typeof viewed === 'undefined' ) {
                            introJs().setOptions({
                                steps: [
                                    {
                                        title: '<?php echo esc_html__( 'Welcome' ) ?>',
                                        intro: '<?php echo esc_html__( 'Thank you for reporting your movement activity for our community!' ) ?><br><br><?php echo esc_html__( 'This link is a permanent link for you. You can add it to your homescreen on your phone or save it as a bookmark.' ) ?>'
                                    },
                                    {
                                        element: document.querySelector('.float'),
                                        intro: '<?php echo esc_html__( 'Create new churches at any time.' ) ?>'
                                    },
                                    {
                                        element: document.querySelector('.intro-profile'),
                                        intro: '<?php echo esc_html__( 'Setup your community profile so we know where your are pushing for the kingdom and how to connect other practitioners with you.' ) ?>'
                                    },
                                    {
                                        element: document.querySelector('.intro-church-list'),
                                        intro: '<?php echo esc_html__( 'Edit your simple churches and order them according to their generations.' ) ?>'
                                    },
                                    {
                                        element: document.querySelector('.intro-map'),
                                        intro: '<?php echo esc_html__( 'View your simple churches on your personal map and view them with the goals map for the entire community.' ) ?>'
                                    },
                                    {
                                        element: document.querySelector('#menu-icon'),
                                        intro: '<?php echo esc_html__( 'Use the slide menu to navigate through the portal.' ) ?>'
                                    }
                                ]
                            }).start();

                            Cookies.set('portal_intro_home', true )
                        }
                    }
                })
            </script>
            <?php
        } else if ( 'list' === $this->parts['action'] ) {
            ?>
            <script>
                jQuery(document).ready(function(){
                    window.onload = () => {
                        let viewed = Cookies.get('portal_intro_list')
                        if ( typeof viewed === 'undefined' ) {
                            introJs().setOptions({
                                steps: [
                                    {
                                        title: '<?php echo esc_html__( 'Edit Church List' ) ?>',
                                        intro: '<?php echo esc_html__( 'Once a church is added you can arrange it according to generation.' ) ?> <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ) ?>/images/nesting-generations.gif" />'
                                    }
                                ]
                            }).start();

                            Cookies.set('portal_intro_list', true )
                        }
                    }
                })
            </script>
            <?php
        }

    }

    /**
     * Writes javascript to the footer
     *
     * @see DT_Magic_Url_Base()->footer_javascript() for default state
     */
    public function footer_javascript(){
        if ( empty( $this->post ) && ! empty( $this->parts["post_id"] ) ) {
            $this->post_id = $this->parts["post_id"];
            $this->post = DT_Posts::get_post( $this->post_type, $this->parts["post_id"], true, false );
            if ( is_wp_error( $this->post ) ){
                return;
            }
        }
        $post = $this->post;
        $translation = [
            'add' => __( 'Add Magic', 'disciple-tools-reporting-app' ),
        ];

        if ( 'map' === $this->parts['action'] ) {
            ?>
            <script>
                let jsObject = [<?php echo json_encode([
                    'map_key' => DT_Mapbox_API::get_key(),
                    'mirror_url' => dt_get_location_grid_mirror( true ),
                    'theme_uri' => trailingslashit( get_stylesheet_directory_uri() ),
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'intro_images' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/',
                    'parts' => $this->parts,
                    'post' => $post,
                    'post_fields' => [],
                    'post_type' => 'groups',
                    'translation' => $translation,
                    'grid_data' => ['data' => [], 'highest_value' => 1 ], // placeholder. filled by api call
                    'custom_marks' => $this->get_custom_map_markers( $this->parts['post_id'] )
                ]) ?>][0]

            </script>
            <?php

        }
        else if ( 'goals_map' === $this->parts['action'] ) {
            ?>
            <script>
                let jsObject = [<?php echo json_encode([
                    'map_key' => DT_Mapbox_API::get_key(),
                    'mirror_url' => dt_get_location_grid_mirror( true ),
                    'theme_uri' => trailingslashit( get_stylesheet_directory_uri() ),
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'intro_images' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/',
                    'parts' => $this->parts,
                    'post' => $post,
                    'post_fields' => [],
                    'post_type' => 'groups',
                    'translation' => $translation,
                    'grid_data' => ['data' => [], 'highest_value' => 1 ], // placeholder. filled by api call
                    'custom_marks' => $this->get_custom_map_markers( $this->parts['post_id'] )
                ]) ?>][0]

                /* custom content */
                function load_self_content( data ) {
                    let pop_div = data.population_division_int * 2
                    jQuery('#custom-paragraph').html(`
                          <span class="self_name ucwords temp-spinner bold">${data.name}</span> is one of <span class="self_peers  bold">${data.peers}</span>
                          administrative divisions in <span class="parent_name ucwords bold">${data.parent_name}</span> and it has a population of
                          <span class="self_population  bold">${data.population}</span>.
                          In order to reach the community goal of 2 churches for every <span class="population_division  bold">${pop_div.toLocaleString("en-US")}</span> people,
                          <span class="self_name ucwords  bold">${data.name}</span> needs
                          <span class="self_needed bold">${data.needed}</span> new churches.
                    `)
                }
                /* custom level content */
                function load_level_content( data, level ) {
                    let gl = jQuery('#'+level+'-list-item')
                    gl.empty()
                    if ( false !== data ) {
                        gl.append(`
                        <div class="cell">
                          <strong>${data.name}</strong><br>
                          Population: <span>${data.population}</span><br>
                          Churches Needed: <span>${data.needed}</span><br>
                          Churches Reported: <span class="reported_number">${data.reported}</span><br>
                          Goal Reached: <span>${data.percent}</span>%
                          <meter class="meter" value="${data.percent}" min="0" low="33" high="66" optimum="100" max="100"></meter>
                        </div>
                    `)
                    }
                }
            </script>
            <?php

            $this->customized_welcome_script();
        }
        else if ( 'list' === $this->parts['action'] ) {
            ?>
            <script>
                let jsObject = [<?php echo json_encode([
                    'map_key' => DT_Mapbox_API::get_key(),
                    'mirror_url' => dt_get_location_grid_mirror( true ),
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'intro_images' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/',
                    'parts' => $this->parts,
                    'post' => $post,
                    'post_fields' => [],
                    'translation' => $translation,
                    'grid_data' => ['data' => [], 'highest_value' => 1 ], // placeholder. filled by api call
                    'custom_marks' => [],
                    'title_list' => $this->get_title_list()
                ]) ?>][0]

            </script>
            <?php
        }
        else if ( 'profile' === $this->parts['action'] ) {
            $post_fields = DT_Posts::get_post_field_settings( $this->post_type );
            ?>
            <script>
                let jsObject = [<?php echo json_encode([
                    'map_key' => DT_Mapbox_API::get_key(),
                    'mirror_url' => dt_get_location_grid_mirror( true ),
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'intro_images' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/',
                    'parts' => $this->parts,
                    'post' => $post,
                    'post_fields' => $post_fields,
                    'translation' => $translation,
                    'grid_data' => ['data' => [], 'highest_value' => 1 ], // placeholder. filled by api call
                    'custom_marks' => []
                ]) ?>][0]
            </script>
            <?php
        }
        else if ( '' === $this->parts['action'] ) {
            ?>
            <script>
                let jsObject = [<?php echo json_encode([
                    'map_key' => DT_Mapbox_API::get_key(),
                    'mirror_url' => dt_get_location_grid_mirror( true ),
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'intro_images' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/',
                    'parts' => $this->parts,
                    'post' => $post,
                    'post_fields' => [],
                    'translation' => $translation,
                    'grid_data' => ['data' => [], 'highest_value' => 1 ], // placeholder. filled by api call
                    'custom_marks' => []
                ]) ?>][0]

                jQuery('.loading-spinner').removeClass('active')
            </script>
            <?php
        }
    }

    public function home_body(){
        if ( empty( $this->post ) ) {
            $this->post_id = $this->parts["post_id"];
            $this->post = DT_Posts::get_post( $this->post_type, $this->parts["post_id"], true, false );
            if ( is_wp_error( $this->post ) ){
                return;
            }
        }
        $post = $this->post;
        ?>
        <!-- title -->
        <div class="grid-x">
            <div class="cell padding-1" >
                <button type="button" style="margin:1em .5em 1em; color: black;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list"></i></button>
                <span style="font-size:1.5rem;font-weight: bold;"><?php echo esc_html__( 'Home' ) ?></span>
            </div>
        </div>

        <!-- nav -->
        <?php $this->nav(); ?>

        <?php if ( isset( $post['title'] ) ) : ?>
        <div class="grid-x center">
            <div class="cell">
                <h1 style="margin-bottom:0;"><?php echo esc_html__( 'Welcome' ) ?> <?php echo esc_html( $post['title'] ) ?></h1>
                <a style="font-size: .8rem;" href="<?php echo esc_url( site_url() . '/' . $this->root . '/access/' ) ?>">Not <?php echo esc_html( $post['title'] ) ?>?</a>
            </div>
        </div>
        <hr>
        <?php endif; ?>

        <div id="wrapper">
            <div class="grid-x">
                <div class="cell top-message"></div>
                <div class="cell">
                    <a class="button large expanded intro-profile" data-intro='Hello step one!' href="<?php echo esc_url( $this->root_url . 'profile' ) ?>"><i class="fi-torso"></i> <?php echo esc_html__( 'COMMUNITY PROFILE' ) ?></a>
                </div>
                <div class="cell">
                    <a class="button large expanded intro-church-list" data-intro='Hello step two!' href="<?php echo esc_url( $this->root_url . 'list' ) ?>"><i class="fi-list-thumbnails"></i> <?php echo esc_html__( 'EDIT CHURCH LIST' ) ?></a>
                </div>
                <div class="cell">
                    <a class="button large expanded intro-map" data-intro='Hello step three!' href="<?php echo esc_url( $this->root_url . 'map' ) ?>"><i class="fi-map"></i> <?php echo esc_html__( 'MAP' ) ?></a>
                </div>
            </div>
        </div>

        <?php
        $this->create_modal();
    }

    public function profile_body(){
        ?>
        <!-- title -->
        <div class="grid-x">
            <div class="cell padding-1" >
                <button type="button" style="margin:1em .5em 1em;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list"></i></button>
                <a style="margin:1em 1em 1em 0; color:black;" href="<?php echo esc_url( $this->root_url ) ?>"><i class="fi-home" style="font-size:2em;"></i></a>
                <span style="font-size:1.5rem;font-weight: bold;"><?php echo esc_html__( 'Community Profile' ) ?></span>
                <span class="loading-spinner active"></span>
            </div>
        </div>

        <!-- nav -->
        <?php $this->nav(); ?>

        <div id="wrapper"></div>
        <?php
    }

    public function setup_body(){
        ?>
        <!-- title -->
        <div class="grid-x">
            <div class="cell padding-1" >
                <button type="button" style="margin:1em .5em 1em;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list"></i></button>
                <a style="margin:1em 1em 1em 0; color:black;" href="<?php echo esc_url( $this->root_url ) ?>"><i class="fi-home" style="font-size:2em;"></i></a>
                <span style="font-size:1.5rem;font-weight: bold;"><?php echo esc_html__( 'Reporting Portal Setup' ) ?></span>
                <span class="loading-spinner active"></span>
            </div>
        </div>

        <!-- nav -->
        <?php $this->nav(); ?>

        <div id="wrapper"></div>
        <?php
    }

    public function list_body(){
        ?>
        <!--title -->
        <div class="grid-x">
            <div class="cell padding-1" >
                <button type="button" style="margin:1em .5em 1em;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list"></i></button>
                <a style="margin:1em 1em 1em 0; color:black;" href="<?php echo esc_url( $this->root_url ) ?>"><i class="fi-home" style="font-size:2em;"></i></a>
                <span style="font-size:1.5rem;font-weight: bold;"><?php echo esc_html__( 'Edit Church List' ) ?></span>
                <span class="loading-spinner active"></span>
            </div>
        </div>

        <!-- nav -->
        <?php $this->nav(); ?>

        <hr style="padding: 0; margin: 0;">

        <!-- body-->
        <div id="wrapper"></div>

        <!-- modal -->
        <?php $this->create_modal();
    }

    public function map_body(){
        ?>
        <!-- title -->
        <div class="grid-x">
            <div class="cell padding-1" >
                <button type="button" style="margin:1em .5em 1em;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list"></i></button>
                <a style="margin:1em 1em 1em 0; color:black;" href="<?php echo esc_url( $this->root_url ) ?>"><i class="fi-home" style="font-size:2em;"></i></a>
                <span style="font-size:1.5rem;font-weight: bold;"><?php echo esc_html__( 'Map' ) ?></span> <a class="button small hollow" style="margin:0 0 5px 10px;padding:.5em;" href="<?php echo esc_url( $this->root_url ) ?>/goals_map"><?php echo esc_html__( 'Show Goals' ) ?></a>

            </div>
        </div>

        <!-- nav -->
        <?php $this->nav(); ?>

        <hr style="padding: 0; margin: 0;">

        <div id="custom-map-style"></div>
        <div class="grid-x">
            <div class="medium-8 large-9 cell">
                <div id="map-wrapper">
                    <div id='map'></div>
                </div>
            </div>
            <div class="medium-4 large-3 cell">
                <div class="grid-x grid-padding-x">
                    <div class="cell center">
                        <h2 style="padding-top:.7rem; font-weight: bold;"><?php echo esc_html__( 'Church List' ) ?></h2>
                    </div>
                    <div class="cell"><div class="loading-spinner active"></div></div>
                </div>
                <div id="church-list-wrapper"></div>
            </div>
        </div>


        <?php $this->create_modal() ?>
        <?php
    }

    public function map_goals_body(){
        ?>
        <!-- title -->
        <div class="grid-x">
            <div class="cell padding-1" >
                <button type="button" style="margin:1em .5em 1em;" id="menu-icon" data-open="offCanvasLeft"><i class="fi-list" style="font-size:2em;"></i></button>
                <a style="margin:1em 1em 1em 0; color:black;" href="<?php echo esc_url( $this->root_url ) ?>"><i class="fi-home" style="font-size:2em;"></i></a>
                <span style="font-size:1.5rem;font-weight: bold;"><?php echo esc_html__( 'Map' ) ?></span> <a class="button small" style="margin:0 0 5px 10px;padding:.5em;" href="<?php echo esc_url( $this->root_url ) ?>/map"><?php echo esc_html__( 'Hide Goals' ) ?></a>

            </div>
        </div>

        <!-- nav -->
        <?php $this->nav(); ?>

        <style id="custom-style-portal">
            #wrapper {
                height: 2000px !important;
            }
            #map-wrapper {
                height: 2000px !important;
            }
            #map {
                height: 2000px !important;
            }
        </style>

        <div id="initialize-screen">
            <div id="initialize-spinner-wrapper" class="center">
                <progress class="success initialize-progress" max="46" value="0"></progress><br>
                <?php echo esc_html__( 'Loading the planet' ) ?> ...<br>
                <span id="initialize-people" style="display:none;"><?php echo esc_html__( "Locating world population" ) ?>...</span><br>
                <span id="initialize-activity" style="display:none;"><?php echo esc_html__( "Calculating movement activity" ) ?>...</span><br>
                <span id="initialize-coffee" style="display:none;"><?php echo esc_html__( "Shamelessly brewing coffee" ) ?>...</span><br>
                <span id="initialize-dothis" style="display:none;"><?php echo esc_html__( "Let's do this" ) ?>...</span><br>
            </div>
        </div>

        <div class="large reveal" id="welcome-modal" data-v-offset="10px" data-reveal>
            <div id="welcome-content" data-close></div>
            <div class="center"><button class="button" id="welcome-close-button" data-close><?php echo esc_html__( "Get Started!" ) ?></button></div>
        </div>

        <div class="grid-x">
            <div class="cell medium-9" >
                <div id="map-wrapper">
                    <span class="loading-spinner active"></span>
                    <div id='map'></div>
                </div>
            </div>
            <div class="cell medium-3 hide-for-small-only" id="map-sidebar-wrapper">
                <!-- details panel -->
                <div id="details-panel">
                    <div class="grid-x grid-padding-x" >
                        <div class="cell">
                            <h1 id="title"></h1>
                            <h3><?php echo esc_html__( "Get Started!" ) ?>Population: <span id="population">0</span></h3>
                            <hr>
                        </div>
                        <div class="cell">
                            <h2 id="panel-type-title">Churches</h2>
                        </div>
                        <div class="cell" id="needed-row">
                            <h3><?php echo esc_html__( "Get Started!" ) ?>Needed: <span id="needed">0</span></h3>
                        </div>
                        <div class="cell">
                            <h3><?php echo esc_html__( "Get Started!" ) ?>Reported: <span id="reported">0</span></h3>
                        </div>
                        <div class="cell">
                            <hr>
                        </div>
                        <div class="cell" id="goal-row">
                            <h2><?php echo esc_html__( "Goal:" ) ?> <span id="saturation-goal">0</span>%</h2>
                            <meter id="meter" class="meter" value="30" min="0" low="33" high="66" optimum="100" max="100"></meter>
                        </div>
                    </div>
                </div>

                <!-- start screen training-->
                <div id="training-start-screen" class="training-content"></div>
                <div id="training-help-screen" class="training-content" style="display:none;"><hr></div>
                <div class="center"><i class="fi-info" id="help-toggle-icon" onclick="jQuery('#training-help-screen').toggle()"></i></div>
            </div>
        </div>


        <!-- modal -->
        <div class="off-canvas position-right is-closed" id="offCanvasNestedPush" data-transition-time=".3s" data-off-canvas>
            <input type="hidden" id="report-modal-title" />
            <input type="hidden" id="report-grid-id" />
            <div class="grid-x" id="canvas_panel">
                <div class="cell">
                    <div class="grid-x">
                        <div class="cell">
                            <h1 id="modal_tile"></h1>
                            <h3>Population: <span id="modal_population">0</span></h3>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="cell" id="slider-content">
                    <div class="grid-x grid-padding-x">
                        <div class="cell">
                            <div class="grid-x">
                                <div class="cell">
                                    <h3>PROGRESS</h3>
                                </div>
                                <div class="cell" id="progress-content">
                                    <div class="grid-x">
                                        <div class="cell">
                                            <p id="custom-paragraph" class="temp-spinner"></p>
                                        </div>
                                        <div class="cell"><hr></div>
                                        <div class="cell temp-spinner" id="a3-list-item"></div>
                                        <div class="cell temp-spinner" id="a2-list-item"></div>
                                        <div class="cell temp-spinner" id="a1-list-item"></div>
                                        <div class="cell temp-spinner" id="a0-list-item"></div>
                                        <div class="cell temp-spinner" id="world-list-item"></div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <?php $this->create_modal() ?>
        <?php
    }

    public function nav() {
        ?>
        <!-- off canvas menus -->
        <div class="off-canvas-wrapper">
            <!-- Left Canvas -->
            <div class="off-canvas position-left" id="offCanvasLeft" data-off-canvas data-transition="push">
                <button class="close-button" aria-label="Close alert" type="button" data-close>
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="grid-x grid-padding-x" style="padding:1em">
                    <div class="cell"><br><br></div>
                    <div class="cell"><a href="<?php echo esc_url( $this->root_url ) ?>"><h3><i class="fi-home"></i> Home</h3></a></div>
                    <div class="cell"><a href="<?php echo esc_url( $this->root_url . 'profile' ) ?>"><h3><i class="fi-torso"></i> Community Profile</h3></a></div>
                    <div class="cell"><a href="<?php echo esc_url( $this->root_url . 'list' ) ?>"><h3><i class="fi-list-thumbnails"></i> Edit Church List</h3></a></div>
                    <div class="cell"><a href="<?php echo esc_url( $this->root_url . 'map' ) ?>"><h3><i class="fi-map"></i> Map</h3></a></div>
                    <br><br>
                </div>
                <div class="center" style="position: absolute; bottom: 10px; width:100%;">
                    <a href="<?php echo esc_url( site_url() . '/contacts' ) ?>">Login</a>
                </div>
            </div>
        </div>
        <?php
    }

    public function create_modal() {
        ?>
        <div class="reveal large" id="edit-modal" data-v-offset="0" data-close-on-click="false" data-reveal>
            <div id="modal-title"></div>
            <div id="modal-content"></div>
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">x</span>
            </button>
        </div>

        <div class="float">
            <i class="fi fi-plus floating small"></i>
        </div>
        <script>
            jQuery(document).ready(function(){
                jQuery('.float').css('left', window.innerWidth - 50 )
            })
        </script>
        <?php
    }

    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints() {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace,
            '/'.$this->type,
            [
                [
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => [ $this, 'endpoint' ],
                    'permission_callback' => function( WP_REST_Request $request ){
                        $magic = new DT_Magic_URL( $this->root );
                        return $magic->verify_rest_endpoint_permissions_on_post( $request ); // verify magic permission
                    },
                ],
            ]
        );
    }

    public function endpoint( WP_REST_Request $request ) {
        $params = $request->get_params();

        if ( ! isset( $params['parts'], $params['action'] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400 ] );
        }

        $params = dt_recursive_sanitize_array( $params );
        $action = sanitize_text_field( wp_unslash( $params['action'] ) );

        switch ( $action ) {
            case 'intro':
                return $this->_endpoint_intro( $params );


            // profile support
            case 'get_profile':
            case 'update_profile_title':
            case 'update_profile_phone':
            case 'update_profile_email':
            case 'update_profile_location':
            case 'delete_profile_location':
            case 'update_multiselect':
                return $this->_endpoint_profile( $params );



            // list and church modal
            case 'create_church':
                return $this->_endpoint_create_church( $params );
            case 'onItemRemoved':
            case 'onItemDrop':
            case 'get_group':
            case 'update_group_title':
            case 'update_group_member_count':
            case 'update_group_start_date':
            case 'update_group_status':
            case 'update_church':
                return $this->_endpoint_update_list( $params );

            case 'load_tree':
                return $this->_endpoint_load_tree( $params );


            // location
            case 'update_location':
            case 'delete_location':
                return $this->_endpoint_location( $params );


            // mapping
            case 'get_geojson':
                return $this->_endpoint_geojson( $params );
            case 'self':
                return DT_Reporting_App_Heatmap::get_self( $params['grid_id'], $this->global_div, $this->us_div );
            case 'a3':
            case 'a2':
            case 'a1':
            case 'a0':
            case 'world':
                $list = DT_Reporting_App_Heatmap::query_church_grid_totals( $action );
                return DT_Reporting_App_Heatmap::endpoint_get_level( $params['grid_id'], $action, $list, $this->global_div, $this->us_div );
            case 'activity_data':
                $grid_id = sanitize_text_field( wp_unslash( $params['grid_id'] ) );
                $offset = sanitize_text_field( wp_unslash( $params['offset'] ) );
                return DT_Reporting_App_Heatmap::query_activity_data( $grid_id, $offset );
            case 'grid_data':
                $grid_totals = DT_Reporting_App_Heatmap::query_church_grid_totals();
                return DT_Reporting_App_Heatmap::_initial_polygon_value_list( $grid_totals, $this->global_div, $this->us_div );
            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );
        }
    }

    public function _endpoint_intro( $params ) {

        return true;
    }

    public function _endpoint_location( $params ) {
        if ( !isset( $params["data"]["post_type"], $params["data"]["post_id"], $params["parts"]["post_id"], $params["data"]["fields"] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400, 'data' => $params ] );
        }

        // permission check for groups
        $contact_id = $params["parts"]["post_id"];
        if ( 'groups' === $params["data"]["post_type"] ) {
            // verify contact is a reporter on the contact
            $contact = DT_Posts::get_post( 'contacts', $contact_id, true, false );
            if ( is_wp_error( $contact ) || empty( $contact ) || ! isset( $contact['church_reporter'] ) ) {
                return new WP_Error( __METHOD__, "No group found", [ 'status' => 400, 'data' => $params ] );
            }
            $has_permission = false;
            foreach ( $contact['church_reporter'] as $value ) {
                if ( $params["data"]["post_id"] === $value['ID'] ) {
                    $has_permission = true;
                }
            }
            if ( ! $has_permission ) {
                return new WP_Error( __METHOD__, "Contact not listed as a reporter on this group.", [ 'status' => 400, 'data' => $params ] );
            }
        }

        // build vars for action
        $post_id = $params["data"]["post_id"];
        $post_type = $params["data"]["post_type"];
        $action = sanitize_text_field( wp_unslash( $params['action'] ) );

        switch ( $action ) {
            case 'update_location':

                $fields = $params['data']['fields'];

                delete_post_meta( $post_id, 'location_grid' );
                delete_post_meta( $post_id, 'location_grid_meta' );
                Location_Grid_Meta::delete_location_grid_meta( $post_id, 'all', 0 );

                $result = DT_Posts::update_post( $post_type, $post_id, $fields, false, false );

                if ( 'contacts' === $post_type ) {
                    DT_Reporting_App_Heatmap::clear_practitioner_grid_totals();
                } else {
                    DT_Reporting_App_Heatmap::clear_church_grid_totals();
                }

                return $result;

            case 'delete_location':

                delete_post_meta( $post_id, 'location_grid' );
                delete_post_meta( $post_id, 'location_grid_meta' );

                Location_Grid_Meta::delete_location_grid_meta( $post_id, 'all', 0 );

                if ( 'contacts' === $post_type ) {
                    DT_Reporting_App_Heatmap::clear_practitioner_grid_totals();
                } else {
                    DT_Reporting_App_Heatmap::clear_church_grid_totals();
                }

                return DT_Posts::get_post( $post_type, $post_id, false, false );

            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );

        }
    }

    public function _endpoint_create_church( $params ) {
        if ( !isset( $params["data"]["name"], $params["data"]["members"], $params["data"]["start_date"], $params["data"]["location_grid_meta"] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", ['status' => 400, 'data' => $params] );
        }

        $post_id = $params["parts"]["post_id"]; //has been verified in verify_rest_endpoint_permissions_on_post()

        if ( empty( $params['data']['start_date'] ) ){
            $params['data']['start_date'] = gmdate( 'Y-m-d' );
        }

        $assigned_to = 0;
        $corresponds_to_user = get_post_meta( $post_id, 'corresponds_to_user', true );
        if ( $corresponds_to_user && 'user' === get_post_meta( $post_id, 'type', true ) ) {
            $assigned_to = $corresponds_to_user;
        }

        $fields = [
            "title" => $params["data"]['name'],
            "assigned_to" => $assigned_to,
            "group_status" => "active",
            "group_type" => "church",
            "church_reporter" => [
                "values" => [
                    [ "value" => $post_id ]
                ]
            ],
            'member_count' => $params['data']['members'],
            "start_date" => $params['data']['start_date'],
            "church_start_date" => $params['data']['start_date'],
            'location_grid_meta' => $params['data']['location_grid_meta']
        ];

        if ( 'none' !== $params['data']['parent'] ) {
            $fields["parent_groups"]  = [
                "values" => [
                  [ "value" => $params['data']['parent'] ],
                ],
            ];
        }

        $new_post = DT_Posts::create_post( 'groups', $fields, true, false );
        if ( ! is_wp_error( $new_post ) ) {
            // clear cash on church grid totals
            DT_Reporting_App_Heatmap::clear_church_grid_totals();
            $grid_id = 0;
            if ( $new_post['location_grid_meta'] ) {
                $grid_id = $new_post['location_grid_meta'][0]['grid_id'];
            }

            return [
                'id' => $new_post['ID'],
                'title' => $new_post['name'],
                'grid_id' => $grid_id,
                'contact_post' => DT_Posts::get_post( 'contacts', $post_id, true, false ),
                'new_church_post' => $new_post,
                'custom_marks' => self::get_custom_map_markers( $post_id ),
            ];
        }
        else {
            dt_write_log( $new_post );
            return $new_post;
        }

    }

    public function _endpoint_profile( $params ) {

        $post_id = $params["parts"]["post_id"];
        $post = DT_Posts::get_post( $this->post_type, $post_id, true, false );

        $action = sanitize_text_field( wp_unslash( $params['action'] ) );

        switch ( $action ) {
            case 'get_profile':
                return $post;
            case 'update_profile_title':
                $fields = [
                    "nickname" => $params['data']['new_value']
                ];
                return DT_Posts::update_post( 'contacts', $post_id, $fields, false, false );
            case 'update_profile_phone':
                $fields = [
                    "contact_phone" => [
                        "values" => [
                            [ "value" => $params['data']['new_value'] ],
                        ],
                        "force_values" => true
                    ]
                ];
                return DT_Posts::update_post( 'contacts', $post_id, $fields, false, false );
            case 'update_profile_email':
                $fields = [
                    "contact_email" => [
                        "values" => [
                            [ "value" => $params['data']['new_value'] ],
                        ],
                        "force_values" => true
                    ]
                ];
                return DT_Posts::update_post( 'contacts', $post_id, $fields, false, false );
            case 'update_multiselect':
                $fields = [
                    $params['data']['key'] => [
                        "values" => [
                            [ "value" => $params['data']['option'], "delete" => $params['data']['state'] ],
                        ],
                    ]
                ];
                return DT_Posts::update_post( 'contacts', $post_id, $fields, false, false );

            default:
                return new WP_Error( __METHOD__, "Missing valid action", [ 'status' => 400 ] );
        }
    }

    public function _endpoint_load_tree( $params) {
        $tree = [];
        $title_list = [];
        $pre_tree = [];
        $post_id = $params["parts"]["post_id"];
        $list = DT_Posts::list_posts('groups', [
            'fields_to_return' => [],
            'church_reporter' => [ $post_id ]
        ], false );

        if ( ! empty( $list['posts'] ) ) {
            foreach ( $list['posts'] as $p ) {
                if ( isset( $p['child_groups'] ) && ! empty( $p['child_groups'] ) ) {
                    foreach ( $p['child_groups'] as $children ) {
                        $pre_tree[$children['ID']] = $p['ID'];
                    }
                }
                if ( empty( $p['parent_groups'] ) ) {
                    $pre_tree[$p['ID']] = null;
                }
                $title = $p['name'];
                if ( ! isset( $p['location_grid_meta'] ) ) {
                    $title = $title . ' (Needs Location)';
                }
                $title_list[$p['ID']] = $title;
            }
            $tree = $this->parse_tree( $pre_tree, $title_list );
        }

        if ( is_null( $tree ) ) {
            $tree = [];
        }

        return [
            'parent_list' => $pre_tree,
            'title_list' => $title_list,
            'tree' => $tree
        ];
    }

    public function _endpoint_update_list( $params ) {

        $contact_id = $params["parts"]["post_id"]; //has been verified in verify_rest_endpoint_permissions_on_post()
        $group_id = $params['data']['post_id'];


        switch ( $params['action'] ) {
            case 'get_group':
                $group = DT_Posts::get_post( 'groups', $group_id, true, false );
                if ( empty( $group ) || is_wp_error( $group ) ) {
                    return new WP_Error( __METHOD__, 'no group found with that id', ['status' => 400, 'data' => $params] );
                }

                // custom permission check. Contact must be coaching group to retrieve group
                if ( ! isset( $group['church_reporter'] ) || empty( $group['church_reporter'] ) ) {
                    return new WP_Error( __METHOD__, 'no reporting found for group' );
                }
                $found = false;
                foreach ( $group['church_reporter'] as $coach ) {
                    if ( (int) $coach['ID'] === (int) $contact_id ) {
                        $found = true;
                    }
                }

                if ( $found ) {
                    $group_fields = DT_Posts::get_post_field_settings( 'groups', true, false );
                    return [
                        'post' => $group,
                        'post_fields' => $group_fields,
                    ];
                } else {
                    return new WP_Error( __METHOD__, 'no reporting connection found', ['status' => 400, 'data' => $params] );
                }

            case 'update_church':
                $fields = $params['data']['fields'];
                return DT_Posts::update_post( 'groups', $group_id, $fields, false, false );

            case 'onItemRemoved':
                dt_write_log( 'onItemRemoved' );
                $deleted_post = Disciple_Tools_Posts::delete_post( 'groups', $params['data']['id'], false );

                DT_Reporting_App_Heatmap::clear_church_grid_totals();

                if ( ! is_wp_error( $deleted_post ) ) {
                    return $params['data']['id'];
                }
                else {
                    return false;
                }
            case 'onItemDrop':
                dt_write_log( 'onItemDrop' );
                if ( ! isset( $params['data']['new_parent'], $params['data']['self'], $params['data']['previous_parent'] ) ) {
                    dt_write_log( 'Defaults not found' );
                    return false;
                }

                global $wpdb;
                if ( 'domenu-0' !== $params['data']['previous_parent'] ) {
                    $wpdb->query( $wpdb->prepare(
                        "DELETE
                                FROM $wpdb->p2p
                                WHERE p2p_from = %s
                                  AND p2p_to = %s
                                  AND p2p_type = 'groups_to_groups'", $params['data']['self'], $params['data']['previous_parent'] ) );
                }
                // add parent child
                $wpdb->query( $wpdb->prepare(
                    "INSERT INTO $wpdb->p2p (p2p_from, p2p_to, p2p_type)
                            VALUES (%s, %s, 'groups_to_groups');
                    ", $params['data']['self'], $params['data']['new_parent'] ) );
                return true;

            case 'update_group_title':
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post( 'groups', $group_id, [ 'title' => trim( $new_value ) ], false, false );

            case 'update_group_member_count':
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post( 'groups', $group_id, [ 'member_count' => trim( $new_value ) ], false, false );

            case 'update_group_start_date':
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post( 'groups', $group_id, [ 'church_start_date' => trim( $new_value ) ], false, false );

            case 'update_group_status':
                $new_value = $params['data']['new_value'];

                return DT_Posts::update_post( 'groups', $group_id, [ 'group_status' => trim( $new_value ) ], false, false );


        }
        return false;
    }

    public function _endpoint_geojson( $params ) {
        if ( !isset( $params["parts"]["post_id"] ) ) {
            return new WP_Error( __METHOD__, "Missing parameters", [ 'status' => 400, 'data' => $params ] );
        }

        $post_id = $params["parts"]["post_id"];

        $list = DT_Posts::list_posts('groups', [
            'fields_to_return' => [],
            'church_reporter' => [ $post_id ]
        ], false );

        $features = [];
        $missing_location = [];
        if ( isset( $list['posts'] ) && ! empty( $list['posts'] ) ) {
            foreach ( $list['posts'] as $item ) {
                if ( ! isset( $item['location_grid_meta'] ) ) {
                    $missing_location[] = [
                        'ID' => $item['ID'],
                        'title' => $item['post_title'],
                    ];
                    continue;
                }

                $lng = $item['location_grid_meta'][0]['lng'];
                $lat = $item['location_grid_meta'][0]['lat'];
                $parent_title = '';
                if ( isset( $item['parent_groups'], $item['parent_groups'][0], $item['parent_groups'][0]['post_title'] ) ) {
                    $parent_title = $item['parent_groups'][0]['post_title'];
                }
                $church_start_date = '';
                if ( isset( $item['church_start_date'] ) ) {
                    $church_start_date = $item['church_start_date']['formatted'];
                }

                $features[] = array(
                    'type' => 'Feature',
                    'properties' => [
                        'ID' => $item['ID'],
                        'title' => $item['post_title'],
                        'member_count' => $item['member_count'] ?? '',
                        'church_start_date' => $church_start_date,
                        'location_title' => $item['location_grid_meta'][0]['label'],
                        'parent_title' => $parent_title,
                    ],
                    'geometry' => array(
                        'type' => 'Point',
                        'coordinates' => array(
                            (float) $lng,
                            (float) $lat,
                            1
                        ),
                    ),
                );
            }
        }

        $new_data = array(
            'type' => 'FeatureCollection',
            'total' => $list['total'],
            'missing_location' => $missing_location,
            'features' => $features,
        );

        return $new_data;
    }

    public function get_custom_map_markers( $post_id ) {
        global $wpdb;
        $list = $wpdb->get_results($wpdb->prepare( "
            SELECT lgm.lng, lgm.lat, p.post_title
            FROM $wpdb->p2p as p2p
            LEFT JOIN $wpdb->dt_location_grid_meta as lgm ON lgm.post_id = p2p.p2p_to
            LEFT JOIN $wpdb->posts as p ON p.ID = p2p.p2p_to
            WHERE p2p.p2p_from = %s AND p2p.p2p_type = 'reporter_to_groups'
        ", $post_id), ARRAY_A );

        if ( ! empty( $list ) ) {
            foreach ( $list as $index => $item ) {
                $list[$index]['lng'] = (float) $item['lng'];
                $list[$index]['lat'] = (float) $item['lat'];
            }
        }
        return $list;
    }

    public function get_title_list() {
        $title_list = [];
        $post_id = $this->parts['post_id'];
        $list = DT_Posts::list_posts('groups', [
            'fields_to_return' => [],
            'church_reporter' => [ $post_id ]
        ], false );

        if ( ! empty( $list['posts'] ) ) {
            foreach ( $list['posts'] as $p ) {
                $title_list[$p['ID']] = $p['name'];
            }
        }
        return $title_list;
    }

    /**
     * @see https://stackoverflow.com/questions/2915748/convert-a-series-of-parent-child-relationships-into-a-hierarchical-tree
     *
     * @param $tree
     * @param $title_list
     * @param null $root
     * @return array|null
     */
    public function parse_tree( $tree, $title_list, $root = null) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach ($tree as $child => $parent) {
            # A direct child is found
            if ($parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset( $tree[$child] );
                # Append the child into result array and parse its children
                $return[] = array(
                    'id' => $child,
                    'title' => $child,
                    'name' => $title_list[$child] ?? 'No Name',
                    'children' => $this->parse_tree( $tree, $title_list, $child ),
                    '__domenu_params' => []
                );
            }
        }
        return empty( $return ) ? null : $return;
    }

    /* map section */
    public function get_grid_totals(){
        return DT_Reporting_App_Heatmap::query_church_grid_totals();
    }

    public function get_grid_totals_by_level( $administrative_level ) {
        return DT_Reporting_App_Heatmap::query_church_grid_totals( $administrative_level );
    }

    public function _browser_tab_title( $title ){
        return __( "Zúme Churches Map", 'disciple-tools-reporting-app' );
    }

    /**
     * Can be customized with class extension
     */
    public function customized_welcome_script(){
        ?>
        <script>
            jQuery(document).ready(function($){
                let asset_url = '<?php echo esc_url( trailingslashit( plugin_dir_url( __FILE__ ) ) . 'images/' ) ?>'
                $('.training-content').append(`
                <div class="grid-x grid-padding-x" >
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'search.svg'}" alt="search icon" />
                        <h2>Search</h2>
                        <p>Search for any city or place with the search input.</p>
                    </div>
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'zoom.svg'}" alt="zoom icon"  />
                        <h2>Zoom</h2>
                        <p>Scroll zoom with your mouse or pinch zoom with track pads and phones to focus on sections of the map.</p>
                    </div>
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'drag.svg'}" alt="drag icon"  />
                        <h2>Drag</h2>
                        <p>Click and drag the map any direction to look at a different part of the map.</p>
                    </div>
                    <div class="cell center">
                        <img class="training-screen-image" src="${asset_url + 'click.svg'}" alt="click icon" />
                        <h2>Click</h2>
                        <p>Click a single section and reveal a details panel with more information about the location.</p>
                    </div>
                </div>
                `)
            })
        </script>
        <?php
    }
}
DT_Reporting_App_Portal::instance();
