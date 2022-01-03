<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Class DT_Reporting_App_Menu
 */
class DT_Reporting_App_Menu {

    public $token = 'dt_reporting_app';

    private static $_instance = null;

    /**
     * DT_Reporting_App_Menu Instance
     *
     * Ensures only one instance of DT_Reporting_App_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return DT_Reporting_App_Menu instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()


    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {

        add_action( "admin_menu", array( $this, "register_menu" ) );

    } // End __construct()


    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu() {
        add_submenu_page( 'dt_extensions', 'Reporting App', 'Reporting App', 'manage_dt', $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple Tools Theme fully loads.
     */
    public function extensions_menu() {}

    /**
     * Builds page contents
     * @since 0.1
     */
    public function content() {

        if ( !current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        if ( isset( $_GET["tab"] ) ) {
            $tab = sanitize_key( wp_unslash( $_GET["tab"] ) );
        } else {
            $tab = 'general';
        }

        $link = 'admin.php?page='.$this->token.'&tab=';

        ?>
        <div class="wrap">
            <h2>Zume Map/Report</h2>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_attr( $link ) . 'general' ?>"
                   class="nav-tab <?php echo esc_html( ( $tab == 'general' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">General</a>
            </h2>

            <?php
            switch ($tab) {
                case "general":
                    $object = new DT_Reporting_App_Tab_General();
                    $object->content();
                    break;

                default:
                    break;
            }
            ?>

        </div><!-- End wrap -->

        <?php
    }
}
DT_Reporting_App_Menu::instance();

/**
 * Class DT_Reporting_App_Tab_General
 */
class DT_Reporting_App_Tab_General {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>
                        <?php DT_Ipstack_API::metabox_for_admin(); ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->

                        <?php $this->right_column() ?>

                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        ?>
        <form method="post">
            <?php wp_nonce_field( 'heatmap_settings_nonce', 'heatmap_settings' )?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <tr>
                <th>Heatmaps</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                   Reporter Manager<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/reporter_manager"><?php echo esc_url( site_url() ) ?>/zume_app/reporter_manager</a>
                </td>
            </tr>
            <tr>
                <td>
                    Last 100 Hours<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/last100_hours"><?php echo esc_url( site_url() ) ?>/zume_app/last100_hours</a>
                </td>
            </tr>
            <tr>
                <td>
                    Church Heatmap<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/heatmap_practitioners"><?php echo esc_url( site_url() ) ?>/zume_app/heatmap_practitioners</a>
                </td>
            </tr>
            <tr>
                <td>
                    Church Heatmap<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/heatmap_churches"><?php echo esc_url( site_url() ) ?>/zume_app/heatmap_churches</a>
                </td>
            </tr>
            <tr>
                <td>
                    Trainings Cluster Map<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/cluster_trainings"><?php echo esc_url( site_url() ) ?>/zume_app/cluster_trainings</a>
                </td>
            </tr>
            <tr>
                <td>
                    Streams Cluster Map<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/cluster_streams"><?php echo esc_url( site_url() ) ?>/zume_app/cluster_streams</a>
                </td>
            </tr>

            <!-- Zume -->
            <tr>
                <td>
                    Network Activities Map<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/heatmap_activity"><?php echo esc_url( site_url() ) ?>/zume_app/heatmap_activity</a>
                </td>
            </tr>

            </tbody>
        </table>
        <br>
        <!-- End Box -->
        </form>
        <?php
    }

    public function right_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Information</th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}

/**
 * Class DT_Reporting_App_Tab_General
 */
class DT_Reporting_App_Tab_ShortCodes {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->

                        <?php $this->right_column() ?>

                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        DT_Ipstack_API::metabox_for_admin();
    }

    public function right_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <tr>
                <th>Information</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}
