<?php
/*
Template Name: Programs Page
*/
get_header(); ?>

<section class="programs-page">
    <h1><?php the_title(); ?></h1>
    <?php echo do_shortcode( '[umpcferi_programs]' ); ?>
</section>

<?php get_footer(); ?>
