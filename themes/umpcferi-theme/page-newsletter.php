<?php
/*
Template Name: Newsletter Page
*/
get_header(); ?>

<section class="newsletter-page">
    <h1><?php the_title(); ?></h1>
    <?php echo do_shortcode( '[umpcferi_latest_newsletter]' ); ?>
</section>

<?php get_footer(); ?>
