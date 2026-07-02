<?php
/**
 * Plugin Name: UmpCferi Plugin
 * Description: Adds user-to-admin messaging, newsletter uploads, and admin management screens for UmpCferi.
 * Version: 1.0.0
 * Author: GitHub Copilot
 * Text Domain: umpcferi-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Umpcferi_Plugin {
    const VERSION = '1.0.0';
    const OPTION_NAME = 'umpcferi_plugin_settings';
    const API_NAMESPACE = 'umpcferi/v1';

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_shortcodes' ) );
        add_action( 'init', array( $this, 'create_sample_content' ) );
        add_action( 'init', array( $this, 'add_umpcferi_rewrite_rules' ) );
        add_action( 'init', array( $this, 'maybe_flush_rewrite_rules' ), 20 );
        add_filter( 'query_vars', array( $this, 'add_umpcferi_query_vars' ) );
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        add_action( 'template_redirect', array( $this, 'serve_react_static_assets' ) );
        add_action( 'template_redirect', array( $this, 'handle_umpcferi_routes' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_post_umpcferi_send_message', array( $this, 'handle_send_message' ) );
        add_action( 'admin_post_umpcferi_upload_newsletter', array( $this, 'handle_upload_newsletter' ) );
        add_action( 'wp_ajax_umpcferi_fetch_messages', array( $this, 'ajax_fetch_messages' ) );
        add_action( 'wp_ajax_umpcferi_create_program', array( $this, 'ajax_create_program' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    public function register_post_types() {
        register_post_type( 'umpcferi_program', array(
            'labels' => array(
                'name' => __( 'Programs', 'umpcferi-plugin' ),
                'singular_name' => __( 'Program', 'umpcferi-plugin' ),
            ),
            'public' => true,
            'has_archive' => false,
            'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'rewrite' => array( 'slug' => 'programs' ),
            'show_in_rest' => true,
        ) );

        register_post_type( 'umpcferi_newsletter', array(
            'labels' => array(
                'name' => __( 'Newsletters', 'umpcferi-plugin' ),
                'singular_name' => __( 'Newsletter', 'umpcferi-plugin' ),
            ),
            'public' => false,
            'show_ui' => true,
            'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'capability_type' => 'post',
            'menu_icon' => 'dashicons-media-document',
            'show_in_rest' => true,
        ) );

        $custom_post_types = array(
            'umpcferi_page' => array(
                'name' => __( 'UMPCFERI Pages', 'umpcferi-plugin' ),
                'singular_name' => __( 'UMPCFERI Page', 'umpcferi-plugin' ),
                'slug' => 'umpcferi-pages',
                'supports' => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
            ),
            'umpcferi_partner' => array(
                'name' => __( 'Partners', 'umpcferi-plugin' ),
                'singular_name' => __( 'Partner', 'umpcferi-plugin' ),
                'slug' => 'umpcferi-partners',
                'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            ),
            'umpcferi_slide' => array(
                'name' => __( 'Slides', 'umpcferi-plugin' ),
                'singular_name' => __( 'Slide', 'umpcferi-plugin' ),
                'slug' => 'umpcferi-slides',
                'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
            ),
            'umpcferi_business' => array(
                'name' => __( 'Businesses', 'umpcferi-plugin' ),
                'singular_name' => __( 'Business', 'umpcferi-plugin' ),
                'slug' => 'umpcferi-businesses',
                'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            ),
            'umpcferi_event' => array(
                'name' => __( 'Events', 'umpcferi-plugin' ),
                'singular_name' => __( 'Event', 'umpcferi-plugin' ),
                'slug' => 'umpcferi-events',
                'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            ),
            'umpcferi_mentor' => array(
                'name' => __( 'Mentors', 'umpcferi-plugin' ),
                'singular_name' => __( 'Mentor', 'umpcferi-plugin' ),
                'slug' => 'umpcferi-mentors',
                'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            ),
            'umpcferi_gallery_image' => array(
                'name' => __( 'Gallery Images', 'umpcferi-plugin' ),
                'singular_name' => __( 'Gallery Image', 'umpcferi-plugin' ),
                'slug' => 'umpcferi-gallery',
                'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
            ),
            'umpcferi_newsletter_page' => array(
                'name' => __( 'Newsletter Pages', 'umpcferi-plugin' ),
                'singular_name' => __( 'Newsletter Page', 'umpcferi-plugin' ),
                'slug' => 'umpcferi-newsletter-pages',
                'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
            ),
        );

        foreach ( $custom_post_types as $type => $args ) {
            register_post_type( $type, array(
                'labels' => array(
                    'name' => $args['name'],
                    'singular_name' => $args['singular_name'],
                ),
                'public' => true,
                'has_archive' => false,
                'supports' => $args['supports'],
                'rewrite' => array( 'slug' => $args['slug'] ),
                'show_in_rest' => true,
            ) );
        }
    }

    public function create_sample_content() {
        $this->create_sample_page( 'Home', 'Welcome to the UMP CFERI website. Explore our programs, events, and partner network.', 'home', 0, true );
        $this->create_sample_page( 'About', 'UMPCFERI is dedicated to supporting student startups through mentorship, programs, and community impact.', 'about', 1, true );
        $this->create_sample_page( 'Programs', 'Discover our entrepreneurship programs designed to help young founders grow and succeed.', 'programs', 2, true );
        $this->create_sample_page( 'Contact', 'Get in touch with the UMP CFERI team for inquiries, mentorship, and partnership opportunities.', 'contact', 3, true );
        $this->create_sample_page( 'UMP CFERI App', '[umpcferi_app]', 'umpcferi-app', 4, true );
        $this->create_sample_page( 'Login', 'Use the form below to sign in as a user or administrator.', 'login', 5, true, 'page-login.php' );

        if ( ! $this->has_published_posts( 'umpcferi_program' ) ) {
            wp_insert_post( array(
                'post_type' => 'umpcferi_program',
                'post_title' => 'Startup Accelerator',
                'post_content' => 'A 12-week program providing mentorship, funding guidance, and investor preparation for student startups.',
                'post_excerpt' => 'A 12-week program for student startups.',
                'post_status' => 'publish',
                'menu_order' => 1,
            ) );
            wp_insert_post( array(
                'post_type' => 'umpcferi_program',
                'post_title' => 'Business Incubation',
                'post_content' => 'Hands-on incubation support and training for emerging entrepreneurs with market-driven business models.',
                'post_excerpt' => 'Incubation support and training for entrepreneurs.',
                'post_status' => 'publish',
                'menu_order' => 2,
            ) );
        }

        if ( ! $this->has_published_posts( 'umpcferi_partner' ) ) {
            wp_insert_post( array(
                'post_type' => 'umpcferi_partner',
                'post_title' => 'CFERI Innovation Hub',
                'post_content' => 'A strategic partner supporting entrepreneurship development through shared resources and networking.',
                'post_status' => 'publish',
                'menu_order' => 1,
            ) );
        }

        if ( ! $this->has_published_posts( 'umpcferi_slide' ) ) {
            $slide_id = wp_insert_post( array(
                'post_type' => 'umpcferi_slide',
                'post_title' => 'Welcome to UMP CFERI',
                'post_content' => 'Empowering young innovators across the region.',
                'post_excerpt' => 'Empowering young innovators across the region.',
                'post_status' => 'publish',
                'menu_order' => 1,
            ) );
            if ( $slide_id ) {
                update_post_meta( $slide_id, 'umpcferi_slide_section', 'home' );
                update_post_meta( $slide_id, 'umpcferi_slide_order', 1 );
                update_post_meta( $slide_id, 'umpcferi_slide_active', '1' );
            }
        }

        if ( ! $this->has_published_posts( 'umpcferi_business' ) ) {
            wp_insert_post( array(
                'post_type' => 'umpcferi_business',
                'post_title' => 'BrightStart Technologies',
                'post_content' => 'A student-founded startup creating digital tools for local businesses.',
                'post_excerpt' => 'A student startup building local digital tools.',
                'post_status' => 'publish',
                'menu_order' => 1,
            ) );
        }

        if ( ! $this->has_published_posts( 'umpcferi_event' ) ) {
            $event_id = wp_insert_post( array(
                'post_type' => 'umpcferi_event',
                'post_title' => 'Entrepreneurship Launch Day',
                'post_content' => 'An annual event featuring startup pitches, mentor sessions, and networking opportunities.',
                'post_excerpt' => 'Startup pitches, mentors, and networking.',
                'post_status' => 'publish',
                'menu_order' => 1,
            ) );
            if ( $event_id ) {
                update_post_meta( $event_id, 'umpcferi_event_date', date( 'Y-m-d', strtotime( '+14 days' ) ) );
                update_post_meta( $event_id, 'umpcferi_event_location', 'UMP CFERI Campus' );
            }
        }

        if ( ! $this->has_published_posts( 'umpcferi_mentor' ) ) {
            wp_insert_post( array(
                'post_type' => 'umpcferi_mentor',
                'post_title' => 'Jane Doe',
                'post_content' => 'An experienced entrepreneur and mentor with a strong track record in startups and social enterprise.',
                'post_excerpt' => 'Experienced entrepreneur and mentor.',
                'post_status' => 'publish',
                'menu_order' => 1,
            ) );
        }

        if ( ! $this->has_published_posts( 'umpcferi_gallery_image' ) ) {
            wp_insert_post( array(
                'post_type' => 'umpcferi_gallery_image',
                'post_title' => 'Community Workshop',
                'post_content' => 'Highlights from the latest UMP CFERI community workshop.',
                'post_excerpt' => 'Community workshop highlights.',
                'post_status' => 'publish',
                'menu_order' => 1,
            ) );
        }

        if ( ! $this->has_published_posts( 'umpcferi_newsletter_page' ) ) {
            wp_insert_post( array(
                'post_type' => 'umpcferi_newsletter_page',
                'post_title' => 'Newsletter Issue 1',
                'post_content' => 'Our first newsletter overview with program highlights and upcoming activities.',
                'post_excerpt' => 'Our first newsletter overview.',
                'post_status' => 'publish',
                'menu_order' => 1,
            ) );
        }
    }

    private function create_sample_page( $title, $content, $slug, $order = 0, $show_in_nav = false, $template = '' ) {
        if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
            return;
        }

        $page_args = array(
            'post_type' => 'page',
            'post_title' => $title,
            'post_content' => $content,
            'post_name' => $slug,
            'post_status' => 'publish',
            'menu_order' => $order,
        );

        if ( $template ) {
            $page_args['page_template'] = $template;
        }

        $page_id = wp_insert_post( $page_args );

        if ( $page_id && $template ) {
            update_post_meta( $page_id, '_wp_page_template', $template );
        }

        if ( $page_id && $show_in_nav ) {
            update_post_meta( $page_id, 'show_in_nav', '1' );
        }
    }

    public function add_umpcferi_rewrite_rules() {
        add_rewrite_rule( '^umpcferi-app/?$', 'index.php?umpcferi_app=1', 'top' );
        add_rewrite_rule( '^login/?$', 'index.php?umpcferi_login=1', 'top' );
    }

    public function add_umpcferi_query_vars( $vars ) {
        $vars[] = 'umpcferi_app';
        $vars[] = 'umpcferi_login';
        return $vars;
    }

    public function handle_umpcferi_routes() {
        $app = intval( get_query_var( 'umpcferi_app' ) );
        $login = intval( get_query_var( 'umpcferi_login' ) );
        $is_app_page = is_page( 'umpcferi-app' );
        $is_login_page = is_page( 'login' );

        if ( $app || $is_app_page ) {
            status_header( 200 );
            get_header();
            echo '<main class="page-content"><section class="about-page"><h1>UMP CFERI App</h1><div class="umpcferi-app-root"><div id="umpcferi-root"></div></div></section></main>';
            get_footer();
            exit;
        }

        if ( $login || $is_login_page ) {
            status_header( 200 );
            get_header();
            echo '<main class="page-content"><section class="about-page"><h1>Login</h1><div class="login-form"><form method="post" action="' . esc_url( wp_login_url() ) . '"><p><label for="user_login">Username or Email</label><br><input type="text" name="log" id="user_login" required></p><p><label for="user_pass">Password</label><br><input type="password" name="pwd" id="user_pass" required></p><p><label><input type="checkbox" name="rememberme" value="forever"> Remember Me</label></p><p><input type="submit" value="Login"></p></form><p><a href="' . esc_url( wp_registration_url() ) . '">Register</a> | <a href="' . esc_url( wp_lostpassword_url() ) . '">Lost your password?</a></p></div></section></main>';
            get_footer();
            exit;
        }
    }

    public function maybe_flush_rewrite_rules() {
        if ( get_option( 'umpcferi_plugin_rewrites_flushed' ) ) {
            return;
        }

        flush_rewrite_rules();
        update_option( 'umpcferi_plugin_rewrites_flushed', 1 );
    }

    private function has_published_posts( $post_type ) {
        $posts = get_posts( array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
        ) );
        return ! empty( $posts );
    }

    public function admin_menu() {
        add_menu_page(
            __( 'UmpCferi Dashboard', 'umpcferi-plugin' ),
            __( 'UmpCferi', 'umpcferi-plugin' ),
            'manage_options',
            'umpcferi-dashboard',
            array( $this, 'admin_page' ),
            'dashicons-admin-comments',
            20
        );
    }

    public function admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $newsletters = get_posts( array( 'post_type' => 'umpcferi_newsletter', 'posts_per_page' => 10 ) );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'UmpCferi Admin Dashboard', 'umpcferi-plugin' ); ?></h1>
            <h2><?php esc_html_e( 'Upload Newsletter', 'umpcferi-plugin' ); ?></h2>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                <?php wp_nonce_field( 'umpcferi_upload_newsletter' ); ?>
                <input type="hidden" name="action" value="umpcferi_upload_newsletter">
                <table class="form-table">
                    <tr>
                        <th><label for="newsletter_title"><?php esc_html_e( 'Newsletter Title', 'umpcferi-plugin' ); ?></label></th>
                        <td><input type="text" id="newsletter_title" name="newsletter_title" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="newsletter_file"><?php esc_html_e( 'PDF / Word File', 'umpcferi-plugin' ); ?></label></th>
                        <td><input type="file" id="newsletter_file" name="newsletter_file" accept=".pdf,.doc,.docx" required></td>
                    </tr>
                </table>
                <?php submit_button( __( 'Upload Newsletter', 'umpcferi-plugin' ) ); ?>
            </form>

            <h2><?php esc_html_e( 'Programs', 'umpcferi-plugin' ); ?></h2>
            <p><?php esc_html_e( 'Create program posts using the Programs post type in the WordPress admin.', 'umpcferi-plugin' ); ?></p>

            <h2><?php esc_html_e( 'User Messages', 'umpcferi-plugin' ); ?></h2>
            <div id="umpcferi-messages"></div>
        </div>
        <?php
    }

    public function handle_upload_newsletter() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'umpcferi-plugin' ) );
        }
        check_admin_referer( 'umpcferi_upload_newsletter' );

        $title = sanitize_text_field( $_POST['newsletter_title'] ?? '' );
        if ( empty( $title ) ) {
            wp_redirect( wp_get_referer() );
            exit;
        }

        if ( empty( $_FILES['newsletter_file']['name'] ) ) {
            wp_redirect( wp_get_referer() );
            exit;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $file = $_FILES['newsletter_file'];
        $upload = wp_handle_upload( $file, array( 'test_form' => false ) );
        if ( isset( $upload['error'] ) ) {
            wp_die( esc_html( $upload['error'] ) );
        }

        $attachment = array(
            'post_mime_type' => $upload['type'],
            'post_title' => sanitize_file_name( $file['name'] ),
            'post_content' => '',
            'post_status' => 'inherit',
        );
        $attachment_id = wp_insert_attachment( $attachment, $upload['file'] );
        $attach_data = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
        wp_update_attachment_metadata( $attachment_id, $attach_data );

        $newsletter_id = wp_insert_post( array(
            'post_type' => 'umpcferi_newsletter',
            'post_title' => $title,
            'post_status' => 'publish',
            'meta_input' => array(
                'umpcferi_newsletter_attachment' => $attachment_id,
            ),
        ) );

        wp_redirect( admin_url( 'admin.php?page=umpcferi-dashboard&uploaded=1' ) );
        exit;
    }

    public function handle_send_message() {
        if ( ! is_user_logged_in() ) {
            wp_die( __( 'Please login to send a message.', 'umpcferi-plugin' ) );
        }

        check_admin_referer( 'umpcferi_send_message' );
        $current_user = wp_get_current_user();
        $message = sanitize_textarea_field( $_POST['umpcferi_message'] ?? '' );
        if ( empty( $message ) ) {
            wp_redirect( wp_get_referer() );
            exit;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'umpcferi_messages';
        $wpdb->insert( $table, array(
            'user_id' => $current_user->ID,
            'message' => $message,
            'status' => 'new',
            'created_at' => current_time( 'mysql' ),
        ) );

        wp_redirect( wp_get_referer() );
        exit;
    }

    public function ajax_fetch_messages() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'umpcferi-plugin' ) );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'umpcferi_messages';
        $messages = $wpdb->get_results( "SELECT m.*, u.user_login FROM $table m LEFT JOIN {$wpdb->users} u ON u.ID = m.user_id ORDER BY m.created_at DESC LIMIT 50" );
        wp_send_json_success( $messages );
    }

    public function ajax_create_program() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'umpcferi-plugin' ) );
        }

        $title = sanitize_text_field( $_POST['title'] ?? '' );
        $content = wp_kses_post( $_POST['content'] ?? '' );
        if ( empty( $title ) ) {
            wp_send_json_error( __( 'Title is required', 'umpcferi-plugin' ) );
        }

        $post_id = wp_insert_post( array(
            'post_type' => 'umpcferi_program',
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
        ) );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( $post_id->get_error_message() );
        }

        wp_send_json_success( array( 'id' => $post_id ) );
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'umpcferi-style', plugin_dir_url( __FILE__ ) . 'assets/css/umpcferi.css' );
        wp_enqueue_script( 'umpcferi-script', plugin_dir_url( __FILE__ ) . 'assets/js/umpcferi.js', array( 'jquery' ), self::VERSION, true );
        wp_localize_script( 'umpcferi-script', 'umpcferi_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'umpcferi_ajax_nonce' ),
        ) );

        $show_react_app = false;
        if ( is_singular() && has_shortcode( get_post()->post_content, 'umpcferi_app' ) ) {
            $show_react_app = true;
        }

        if ( intval( get_query_var( 'umpcferi_app' ) ) === 1 || is_page( 'umpcferi-app' ) ) {
            $show_react_app = true;
        }

        if ( $show_react_app ) {
            $this->enqueue_react_app_assets();
            wp_enqueue_style( 'umpcferi-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), null );
        }
    }

    private function enqueue_react_app_assets() {
        $manifest = $this->get_react_build_manifest();
        if ( ! $manifest || empty( $manifest['entrypoints'] ) ) {
            return;
        }

        $build_url = plugin_dir_url( __FILE__ ) . 'ump-cferi-web/frontend/build/';
        $first_js_handle = null;

        foreach ( $manifest['entrypoints'] as $entry ) {
            $url = $build_url . ltrim( $entry, '/' );
            if ( substr( $entry, -4 ) === '.css' ) {
                wp_enqueue_style( 'umpcferi-react-' . md5( $entry ), $url, array(), self::VERSION );
                continue;
            }
            if ( substr( $entry, -3 ) === '.js' ) {
                $handle = 'umpcferi-react-' . md5( $entry );
                wp_enqueue_script( $handle, $url, array(), self::VERSION, true );
                if ( ! $first_js_handle ) {
                    $first_js_handle = $handle;
                }
            }
        }

        if ( $first_js_handle ) {
            wp_add_inline_script( $first_js_handle, sprintf(
                'window.umpcferiApiUrl = %s; window.umpcferiBackendUrl = %s;',
                wp_json_encode( esc_url_raw( rest_url( self::API_NAMESPACE ) ) ),
                wp_json_encode( esc_url_raw( plugin_dir_url( __FILE__ ) ) )
            ), 'before' );
        }
    }

    private function get_react_build_manifest() {
        $manifest_path = plugin_dir_path( __FILE__ ) . 'ump-cferi-web/frontend/build/asset-manifest.json';
        if ( ! file_exists( $manifest_path ) ) {
            return false;
        }

        $manifest = json_decode( file_get_contents( $manifest_path ), true );
        return is_array( $manifest ) ? $manifest : false;
    }

    public function admin_enqueue_scripts( $hook ) {
        if ( strpos( $hook, 'umpcferi-dashboard' ) === false ) {
            return;
        }
        wp_enqueue_style( 'umpcferi-admin-style', plugin_dir_url( __FILE__ ) . 'assets/css/umpcferi-admin.css' );
        wp_enqueue_script( 'umpcferi-admin-script', plugin_dir_url( __FILE__ ) . 'assets/js/umpcferi-admin.js', array( 'jquery' ), self::VERSION, true );
        wp_localize_script( 'umpcferi-admin-script', 'umpcferiAdmin', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'umpcferi_admin_nonce' ),
        ) );
    }

    public function register_shortcodes() {
        add_shortcode( 'umpcferi_user_chat', array( $this, 'shortcode_user_chat' ) );
        add_shortcode( 'umpcferi_latest_newsletter', array( $this, 'shortcode_latest_newsletter' ) );
        add_shortcode( 'umpcferi_programs', array( $this, 'shortcode_programs' ) );
        add_shortcode( 'umpcferi_app', array( $this, 'shortcode_react_app' ) );
    }

    public function shortcode_react_app() {
        return '<div class="umpcferi-app-root"><div id="umpcferi-root"></div></div>';
    }

    public function register_rest_routes() {
        register_rest_route( self::API_NAMESPACE, '/admin/login', array(
            'methods' => 'POST',
            'callback' => array( $this, 'rest_admin_login' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/admin/me', array(
            array(
                'methods' => 'GET',
                'callback' => array( $this, 'rest_admin_me' ),
                'permission_callback' => array( $this, 'rest_protect' ),
            ),
            array(
                'methods' => 'PUT',
                'callback' => array( $this, 'rest_update_admin_me' ),
                'permission_callback' => array( $this, 'rest_protect' ),
            ),
        ) );

        register_rest_route( self::API_NAMESPACE, '/settings', array(
            array(
                'methods' => 'GET',
                'callback' => array( $this, 'rest_get_settings' ),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods' => 'PUT',
                'callback' => array( $this, 'rest_update_settings' ),
                'permission_callback' => array( $this, 'rest_protect' ),
            ),
        ) );

        register_rest_route( self::API_NAMESPACE, '/pages', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_pages' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/pages/slug/(?P<slug>[a-zA-Z0-9-_]+)', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_page_by_slug' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/programs', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_programs' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/slides', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_slides' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/partners', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_partners' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/newsletter', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_newsletter_pages' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/businesses', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_businesses' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/events', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_events' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/mentors', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_mentors' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/gallery', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_gallery_images' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::API_NAMESPACE, '/messages', array(
            array(
                'methods' => 'GET',
                'callback' => array( $this, 'rest_get_messages' ),
                'permission_callback' => array( $this, 'rest_protect' ),
            ),
            array(
                'methods' => 'POST',
                'callback' => array( $this, 'rest_create_message' ),
                'permission_callback' => '__return_true',
            ),
        ) );

        register_rest_route( self::API_NAMESPACE, '/messages/(?P<id>\d+)/status', array(
            'methods' => 'PATCH',
            'callback' => array( $this, 'rest_update_message_status' ),
            'permission_callback' => array( $this, 'rest_protect' ),
        ) );
    }

    public function serve_react_static_assets() {
        $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
        if ( ! $request_uri ) {
            return;
        }

        if ( preg_match( '#^/(slideshow|images|impactImages|newsletter|static|sponsorsImages)(/.+)$#', $request_uri, $matches ) ) {
            $path = plugin_dir_path( __FILE__ ) . 'ump-cferi-web/frontend/build' . $matches[0];
            if ( file_exists( $path ) ) {
                $mime = wp_get_mime_types();
                $ext = pathinfo( $path, PATHINFO_EXTENSION );
                $content_type = isset( $mime[ $ext ] ) ? $mime[ $ext ] : wp_get_mime_type( $path );
                if ( $content_type ) {
                    header( 'Content-Type: ' . $content_type );
                }
                readfile( $path );
                exit;
            }
        }
    }

    private function get_post_image_url( $post_id ) {
        $image = get_the_post_thumbnail_url( $post_id, 'full' );
        if ( $image ) {
            return $image;
        }
        return '';
    }

    private function rest_protect( $request ) {
        $user = $this->authenticate_rest_request();
        return $user && user_can( $user, 'manage_options' );
    }

    private function authenticate_rest_request() {
        $headers = getallheaders();
        $auth_header = isset( $headers['Authorization'] ) ? $headers['Authorization'] : ( isset( $headers['authorization'] ) ? $headers['authorization'] : '' );
        if ( empty( $auth_header ) || stripos( $auth_header, 'Bearer ' ) !== 0 ) {
            return false;
        }

        $token = trim( substr( $auth_header, 7 ) );
        if ( empty( $token ) ) {
            return false;
        }

        $users = get_users( array(
            'meta_key' => '_umpcferi_api_token',
            'meta_value' => $token,
            'number' => 1,
        ) );

        if ( empty( $users ) ) {
            return false;
        }

        $user = $users[0];
        $expires = get_user_meta( $user->ID, '_umpcferi_api_token_expires', true );
        if ( $expires && strtotime( $expires ) < current_time( 'timestamp' ) ) {
            return false;
        }

        wp_set_current_user( $user->ID );
        return $user;
    }

    public function rest_admin_login( $request ) {
        $params = $request->get_json_params();
        $email = sanitize_email( $params['email'] ?? '' );
        $password = $params['password'] ?? '';

        if ( empty( $email ) || empty( $password ) ) {
            return new WP_Error( 'invalid_credentials', __( 'Email and password are required.', 'umpcferi-plugin' ), array( 'status' => 400 ) );
        }

        $user = null;
        if ( is_email( $email ) ) {
            $user = get_user_by( 'email', $email );
        }

        if ( ! $user ) {
            $user = get_user_by( 'login', $email );
        }

        if ( ! $user ) {
            return new WP_Error( 'invalid_credentials', __( 'Invalid email or password.', 'umpcferi-plugin' ), array( 'status' => 401 ) );
        }

        $creds = array(
            'user_login' => $user->user_login,
            'user_password' => $password,
            'remember' => true,
        );
        $signed_in = wp_signon( $creds, false );

        if ( is_wp_error( $signed_in ) ) {
            return new WP_Error( 'invalid_credentials', __( 'Invalid email or password.', 'umpcferi-plugin' ), array( 'status' => 401 ) );
        }

        if ( ! user_can( $signed_in, 'manage_options' ) ) {
            return new WP_Error( 'not_allowed', __( 'Only administrators can access this area.', 'umpcferi-plugin' ), array( 'status' => 403 ) );
        }

        $token = bin2hex( wp_generate_password( 32, false ) );
        update_user_meta( $signed_in->ID, '_umpcferi_api_token', $token );
        update_user_meta( $signed_in->ID, '_umpcferi_api_token_expires', gmdate( 'Y-m-d H:i:s', time() + DAY_IN_SECONDS * 30 ) );

        return array(
            '_id' => $signed_in->ID,
            'name' => $signed_in->display_name,
            'email' => $signed_in->user_email,
            'token' => $token,
        );
    }

    public function rest_admin_me() {
        $user = $this->authenticate_rest_request();
        if ( ! $user ) {
            return new WP_Error( 'unauthorized', __( 'Unauthorized', 'umpcferi-plugin' ), array( 'status' => 401 ) );
        }
        return array(
            '_id' => $user->ID,
            'name' => $user->display_name,
            'email' => $user->user_email,
        );
    }

    public function rest_update_admin_me( $request ) {
        $user = $this->authenticate_rest_request();
        if ( ! $user ) {
            return new WP_Error( 'unauthorized', __( 'Unauthorized', 'umpcferi-plugin' ), array( 'status' => 401 ) );
        }

        $params = $request->get_json_params();
        $user_id = $user->ID;
        $updated_user = array( 'ID' => $user_id );

        if ( isset( $params['name'] ) ) {
            $updated_user['display_name'] = sanitize_text_field( $params['name'] );
        }
        if ( isset( $params['email'] ) ) {
            $updated_user['user_email'] = sanitize_email( $params['email'] );
        }
        if ( isset( $params['password'] ) && ! empty( $params['password'] ) ) {
            $updated_user['user_pass'] = $params['password'];
        }

        $result = wp_update_user( $updated_user );
        if ( is_wp_error( $result ) ) {
            return new WP_Error( 'update_failed', $result->get_error_message(), array( 'status' => 500 ) );
        }

        return array(
            '_id' => $user_id,
            'name' => get_the_author_meta( 'display_name', $user_id ),
            'email' => get_the_author_meta( 'user_email', $user_id ),
        );
    }

    public function rest_get_settings() {
        $settings = get_option( self::OPTION_NAME, array() );
        return wp_parse_args( $settings, array(
            'slideInterval' => 5000,
            'slideTransition' => 'fade',
            'autoPlay' => true,
            'showNavigation' => true,
            'showDots' => true,
            'themeMode' => 'light',
            'siteName' => 'UMP CFERI Platform',
            'siteDescription' => '',
            'contactEmail' => '',
            'contactPhone' => '',
            'aboutDescription' => 'UMPCFERI is a dynamic entrepreneurship hub dedicated to fostering innovation, supporting student startups, and driving economic growth through structured incubation programs and mentorship.',
            'mission' => 'To foster a dynamic entrepreneurship ecosystem through development programmes, strategic partnerships, and applied research aimed at building sustainable enterprises and contributing to local, national and regional economic growth.',
            'vision' => 'To be a leading entrepreneurship centre in nurturing innovative and creative enterprises for sustainable development within the African context.',
            'achievementsHeading' => "CFERI'S KEY ACHIEVEMENTS",
            'values' => array( 'Innovation', 'Excellence', 'Collaboration', 'Integrity', 'Diversity', 'Adaptability', 'Relevance' ),
            'goals' => array(),
            'achievements' => array(),
            'socialLinks' => array(),
        ) );
    }

    public function rest_update_settings( $request ) {
        $settings = get_option( self::OPTION_NAME, array() );
        $params = $request->get_json_params();
        $settings = array_merge( $settings, $params );
        update_option( self::OPTION_NAME, $settings );
        return $settings;
    }

    public function rest_get_pages( $request ) {
        $admin = $request->get_param( 'admin' ) === 'true';
        $args = array(
            'post_type' => 'page',
            'post_status' => $admin ? array( 'publish', 'draft', 'private' ) : 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
        );
        $pages = get_posts( $args );
        $result = array();
        foreach ( $pages as $page ) {
            $result[] = array(
                '_id' => $page->ID,
                'title' => $page->post_title,
                'slug' => $page->post_name,
                'excerpt' => $page->post_excerpt,
                'content' => apply_filters( 'the_content', $page->post_content ),
                'showInNav' => get_post_meta( $page->ID, 'show_in_nav', true ) === '1',
                'order' => $page->menu_order,
                'active' => $page->post_status === 'publish',
            );
        }
        return $result;
    }

    public function rest_get_page_by_slug( $request ) {
        $slug = sanitize_text_field( $request['slug'] );
        $page = get_page_by_path( $slug, OBJECT, 'page' );
        if ( ! $page || $page->post_status !== 'publish' ) {
            return new WP_Error( 'not_found', __( 'Page not found', 'umpcferi-plugin' ), array( 'status' => 404 ) );
        }
        return array(
            '_id' => $page->ID,
            'title' => $page->post_title,
            'slug' => $page->post_name,
            'excerpt' => $page->post_excerpt,
            'content' => apply_filters( 'the_content', $page->post_content ),
            'showInNav' => get_post_meta( $page->ID, 'show_in_nav', true ) === '1',
            'order' => $page->menu_order,
            'active' => true,
        );
    }

    public function rest_get_programs() {
        $args = array(
            'post_type' => 'umpcferi_program',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
        );
        $programs = get_posts( $args );
        $result = array();
        foreach ( $programs as $program ) {
            $result[] = array(
                '_id' => $program->ID,
                'title' => $program->post_title,
                'slug' => $program->post_name,
                'description' => wp_trim_words( $program->post_content, 24, '...' ),
                'fullDetails' => apply_filters( 'the_content', $program->post_content ),
                'image' => $this->get_post_image_url( $program->ID ),
                'active' => true,
            );
        }
        return $result;
    }

    public function rest_get_slides( $request ) {
        $section = sanitize_text_field( $request->get_param( 'section' ) );
        $args = array(
            'post_type' => 'umpcferi_slide',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
        );
        if ( $section ) {
            $args['meta_query'] = array(
                array(
                    'key' => 'umpcferi_slide_section',
                    'value' => $section,
                    'compare' => '=',
                ),
            );
        }
        $slides = get_posts( $args );
        $result = array();
        foreach ( $slides as $slide ) {
            $result[] = array(
                '_id' => $slide->ID,
                'title' => $slide->post_title,
                'caption' => $slide->post_excerpt ?: $slide->post_content,
                'image' => $this->get_post_image_url( $slide->ID ),
                'section' => get_post_meta( $slide->ID, 'umpcferi_slide_section', true ) ?: 'home',
                'order' => (int) get_post_meta( $slide->ID, 'umpcferi_slide_order', true ),
                'active' => get_post_meta( $slide->ID, 'umpcferi_slide_active', true ) !== '0',
            );
        }
        return $result;
    }

    public function rest_get_partners() {
        $args = array(
            'post_type' => 'umpcferi_partner',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
        );
        $partners = get_posts( $args );
        $result = array();
        foreach ( $partners as $partner ) {
            $result[] = array(
                '_id' => $partner->ID,
                'name' => $partner->post_title,
                'description' => apply_filters( 'the_content', $partner->post_content ),
                'logo' => $this->get_post_image_url( $partner->ID ),
                'link' => esc_url_raw( get_post_meta( $partner->ID, 'umpcferi_partner_link', true ) ),
                'order' => (int) get_post_meta( $partner->ID, 'umpcferi_partner_order', true ),
                'active' => get_post_meta( $partner->ID, 'umpcferi_partner_active', true ) !== '0',
            );
        }
        return $result;
    }

    public function rest_get_newsletter_pages() {
        $args = array(
            'post_type' => 'umpcferi_newsletter_page',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
        );
        $pages = get_posts( $args );
        $result = array();
        foreach ( $pages as $page ) {
            $result[] = array(
                '_id' => $page->ID,
                'title' => $page->post_title,
                'alt' => get_post_meta( $page->ID, 'umpcferi_newsletter_page_alt', true ) ?: $page->post_excerpt,
                'image' => $this->get_post_image_url( $page->ID ),
                'order' => (int) get_post_meta( $page->ID, 'umpcferi_newsletter_page_order', true ),
                'active' => get_post_meta( $page->ID, 'umpcferi_newsletter_page_active', true ) !== '0',
            );
        }
        return $result;
    }

    public function rest_get_businesses() {
        $args = array(
            'post_type' => 'umpcferi_business',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
        );
        $businesses = get_posts( $args );
        $result = array();
        foreach ( $businesses as $business ) {
            $result[] = array(
                '_id' => $business->ID,
                'name' => $business->post_title,
                'description' => apply_filters( 'the_content', $business->post_content ),
                'owner' => get_post_meta( $business->ID, 'umpcferi_business_owner', true ),
                'industry' => get_post_meta( $business->ID, 'umpcferi_business_industry', true ),
                'yearStarted' => get_post_meta( $business->ID, 'umpcferi_business_year_started', true ),
                'jobsCreated' => (int) get_post_meta( $business->ID, 'umpcferi_business_jobs_created', true ),
                'logo' => $this->get_post_image_url( $business->ID ),
            );
        }
        return $result;
    }

    public function rest_get_events() {
        $args = array(
            'post_type' => 'umpcferi_event',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'meta_value',
            'meta_key' => 'umpcferi_event_date',
            'order' => 'ASC',
        );
        $events = get_posts( $args );
        $result = array();
        foreach ( $events as $event ) {
            $result[] = array(
                '_id' => $event->ID,
                'title' => $event->post_title,
                'description' => apply_filters( 'the_content', $event->post_content ),
                'date' => get_post_meta( $event->ID, 'umpcferi_event_date', true ),
                'location' => get_post_meta( $event->ID, 'umpcferi_event_location', true ),
            );
        }
        return $result;
    }

    public function rest_get_mentors() {
        $args = array(
            'post_type' => 'umpcferi_mentor',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        );
        $mentors = get_posts( $args );
        $result = array();
        foreach ( $mentors as $mentor ) {
            $result[] = array(
                '_id' => $mentor->ID,
                'name' => $mentor->post_title,
                'bio' => apply_filters( 'the_content', $mentor->post_content ),
                'email' => get_post_meta( $mentor->ID, 'umpcferi_mentor_email', true ),
                'phone' => get_post_meta( $mentor->ID, 'umpcferi_mentor_phone', true ),
                'expertise' => get_post_meta( $mentor->ID, 'umpcferi_mentor_expertise', true ),
                'company' => get_post_meta( $mentor->ID, 'umpcferi_mentor_company', true ),
                'yearsExperience' => get_post_meta( $mentor->ID, 'umpcferi_mentor_years_experience', true ),
                'linkedin' => get_post_meta( $mentor->ID, 'umpcferi_mentor_linkedin', true ),
                'availability' => get_post_meta( $mentor->ID, 'umpcferi_mentor_availability', true ),
            );
        }
        return $result;
    }

    public function rest_get_gallery_images() {
        $args = array(
            'post_type' => 'umpcferi_gallery_image',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
        );
        $images = get_posts( $args );
        $result = array();
        foreach ( $images as $image ) {
            $result[] = array(
                '_id' => $image->ID,
                'title' => $image->post_title,
                'description' => apply_filters( 'the_content', $image->post_content ),
                'category' => get_post_meta( $image->ID, 'umpcferi_gallery_image_category', true ),
                'image' => $this->get_post_image_url( $image->ID ),
                'order' => (int) get_post_meta( $image->ID, 'umpcferi_gallery_image_order', true ),
            );
        }
        return $result;
    }

    public function rest_get_messages() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return new WP_Error( 'unauthorized', __( 'Unauthorized', 'umpcferi-plugin' ), array( 'status' => 403 ) );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'umpcferi_messages';
        $messages = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC LIMIT 50" );
        return $messages;
    }

    public function rest_create_message( $request ) {
        $params = $request->get_json_params();
        $senderName = sanitize_text_field( $params['senderName'] ?? '' );
        $senderEmail = sanitize_email( $params['senderEmail'] ?? '' );
        $message = sanitize_textarea_field( $params['message'] ?? '' );
        $status = sanitize_text_field( $params['status'] ?? 'unanswered' );

        if ( empty( $senderName ) || empty( $senderEmail ) || empty( $message ) ) {
            return new WP_Error( 'invalid_data', __( 'Name, email and message are required.', 'umpcferi-plugin' ), array( 'status' => 400 ) );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'umpcferi_messages';
        $wpdb->insert( $table, array(
            'user_id' => 0,
            'message' => $message,
            'status' => $status,
            'created_at' => current_time( 'mysql' ),
        ) );

        return array( 'success' => true );
    }

    public function rest_update_message_status( $request ) {
        $id = intval( $request['id'] );
        $params = $request->get_json_params();
        $status = sanitize_text_field( $params['status'] ?? '' );
        if ( empty( $status ) ) {
            return new WP_Error( 'invalid_data', __( 'Status is required.', 'umpcferi-plugin' ), array( 'status' => 400 ) );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'umpcferi_messages';
        $updated = $wpdb->update( $table, array( 'status' => $status ), array( 'id' => $id ) );
        if ( $updated === false ) {
            return new WP_Error( 'update_failed', __( 'Could not update message status.', 'umpcferi-plugin' ), array( 'status' => 500 ) );
        }
        return array( 'success' => true );
    }

    public function shortcode_user_chat() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . esc_html__( 'Please log in to send a message to the admin.', 'umpcferi-plugin' ) . '</p>';
        }

        $current_user = wp_get_current_user();
        global $wpdb;
        $table = $wpdb->prefix . 'umpcferi_messages';
        $messages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC", $current_user->ID ) );

        $output  = '<div class="umpcferi-chat">';
        $output .= '<h2>' . esc_html__( 'Send a Message to Admin', 'umpcferi-plugin' ) . '</h2>';
        $output .= '<form id="umpcferi-message-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
        $output .= wp_nonce_field( 'umpcferi_send_message', '_wpnonce', true, false );
        $output .= '<input type="hidden" name="action" value="umpcferi_send_message">';
        $output .= '<textarea id="umpcferi_message" name="umpcferi_message" rows="5" style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;" placeholder="' . esc_attr__( 'Write your message here...', 'umpcferi-plugin' ) . '"></textarea>';
        $output .= '<p><button type="submit" class="button button-primary">' . esc_html__( 'Send Message', 'umpcferi-plugin' ) . '</button></p>';
        $output .= '</form>';

        if ( $messages ) {
            $output .= '<div class="umpcferi-chat-history"><h3>' . esc_html__( 'Your Messages', 'umpcferi-plugin' ) . '</h3>';
            foreach ( $messages as $message ) {
                $output .= '<div class="umpcferi-chat-item">';
                $output .= '<p><strong>' . esc_html( $message->status ) . '</strong> - ' . esc_html( $message->created_at ) . '</p>';
                $output .= '<p>' . esc_html( $message->message ) . '</p>';
                $output .= '</div>';
            }
            $output .= '</div>';
        }

        $output .= '</div>';
        return $output;
    }

    public function shortcode_latest_newsletter() {
        $latest = get_posts( array( 'post_type' => 'umpcferi_newsletter', 'posts_per_page' => 1 ) );
        if ( empty( $latest ) ) {
            return '<p>' . esc_html__( 'No newsletter available yet.', 'umpcferi-plugin' ) . '</p>';
        }

        $attachment_id = get_post_meta( $latest[0]->ID, 'umpcferi_newsletter_attachment', true );
        if ( ! $attachment_id ) {
            return '<p>' . esc_html__( 'Newsletter upload is missing an attachment.', 'umpcferi-plugin' ) . '</p>';
        }

        $url = wp_get_attachment_url( $attachment_id );
        $filetype = get_post_mime_type( $attachment_id );
        $output  = '<div class="umpcferi-newsletter-viewer">';
        $output .= '<h2>' . esc_html( $latest[0]->post_title ) . '</h2>';

        if ( strpos( $filetype, 'pdf' ) !== false ) {
            $output .= '<iframe src="' . esc_url( $url ) . '" style="width:100%;min-height:700px;border:1px solid #ddd;border-radius:10px;"></iframe>';
        } elseif ( in_array( $filetype, array( 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ), true ) ) {
            $viewer_url = 'https://view.officeapps.live.com/op/embed?src=' . rawurlencode( $url );
            $output .= '<iframe src="' . esc_url( $viewer_url ) . '" style="width:100%;min-height:700px;border:1px solid #ddd;border-radius:10px;"></iframe>';
        } else {
            $output .= '<p>' . esc_html__( 'This newsletter is available to download below.', 'umpcferi-plugin' ) . '</p>';
        }

        $output .= '<p><a class="umpcferi-newsletter-download" href="' . esc_url( $url ) . '" download>' . esc_html__( 'Download Newsletter', 'umpcferi-plugin' ) . '</a></p>';
        $output .= '</div>';
        return $output;
    }

    public function shortcode_programs() {
        $programs = get_posts( array( 'post_type' => 'umpcferi_program', 'posts_per_page' => 10 ) );
        if ( empty( $programs ) ) {
            return '<p>' . esc_html__( 'No programs available at the moment.', 'umpcferi-plugin' ) . '</p>';
        }

        $output  = '<div class="umpcferi-program-list">';
        foreach ( $programs as $program ) {
            $output .= '<article class="umpcferi-program-card">';
            $output .= '<h3><a href="' . esc_url( get_permalink( $program ) ) . '">' . esc_html( $program->post_title ) . '</a></h3>';
            $output .= '<div>' . wp_trim_words( $program->post_content, 24, '...' ) . '</div>';
            $output .= '</article>';
        }
        $output .= '</div>';
        return $output;
    }
}

function umpcferi_plugin_activate() {
    global $wpdb;
    $plugin = new Umpcferi_Plugin();
    $plugin->register_post_types();
    $plugin->create_sample_content();

    $table = $wpdb->prefix . 'umpcferi_messages';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        message text NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'new',
        created_at datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'umpcferi_plugin_activate' );
new Umpcferi_Plugin();
