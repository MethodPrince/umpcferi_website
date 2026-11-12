<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header>
    <div class="site-branding">
        <a class="site-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Home">
            <img class="site-logo" src="<?php echo esc_url( get_template_directory_uri() . '/Images/programs images/ump-logo.jpg' ); ?>" alt="UMP CFERI logo">
        </a>
        <div class="site-branding-text">
            <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
            <p><?php bloginfo( 'description' ); ?></p>
        </div>
    </div>
    <nav>
        <?php umpcferi_primary_menu_fallback(); ?>
    </nav>
</header>
<main>
