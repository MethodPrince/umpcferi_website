<?php
function umpcferi_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'umpcferi-theme' ),
    ) );
}
add_action( 'after_setup_theme', 'umpcferi_theme_setup' );

function umpcferi_primary_menu_fallback() {
    $menu_items = array(
        array(
            'label' => 'Home',
            'url'   => home_url( '/' ),
            'slug'  => '',
        ),
        array(
            'label' => 'About',
            'url'   => get_permalink( get_page_by_path( 'about' ) ),
            'slug'  => 'about',
        ),
        array(
            'label' => 'Programs',
            'url'   => get_permalink( get_page_by_path( 'programs' ) ),
            'slug'  => 'programs',
        ),
        array(
            'label' => 'Newsletter',
            'url'   => get_permalink( get_page_by_path( 'newsletter' ) ),
            'slug'  => 'newsletter',
        ),
        array(
            'label' => 'Contact Us',
            'url'   => get_permalink( get_page_by_path( 'contact-us' ) ) ? get_permalink( get_page_by_path( 'contact-us' ) ) : get_permalink( get_page_by_path( 'contact' ) ),
            'slug'  => 'contact-us',
        ),
    );

    echo '<div class="main-navigation"><ul>';

    foreach ( $menu_items as $item ) {
        if ( empty( $item['url'] ) ) {
            continue;
        }

        $classes = '';
        if ( ! empty( $item['slug'] ) && is_page( $item['slug'] ) ) {
            $classes = ' class="current_page_item"';
        } elseif ( $item['slug'] === '' && is_front_page() ) {
            $classes = ' class="current_page_item"';
        }

        echo '<li' . $classes . '><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a></li>';
    }

    echo '</ul></div>';
}

function umpcferi_theme_scripts() {
    $theme_style_path = get_theme_file_path( 'style.css' );
    $theme_style_version = file_exists( $theme_style_path ) ? filemtime( $theme_style_path ) : wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'umpcferi-theme-style', get_stylesheet_uri(), array(), $theme_style_version );
    if ( is_front_page() ) {
        wp_enqueue_script( 'umpcferi-home-slideshow', get_template_directory_uri() . '/js/home-slideshow.js', array(), '1.0.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'umpcferi_theme_scripts' );

function umpcferi_breadcrumbs() {
    echo '<nav class="breadcrumbs"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></nav>';
}
