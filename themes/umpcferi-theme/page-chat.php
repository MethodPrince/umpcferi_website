<?php
/*
Template Name: Chat Page
*/
get_header(); ?>

<section class="about-page">
    <h1><?php the_title(); ?></h1>
    <?php echo do_shortcode( '[umpcferi_user_chat]' ); ?>
</section>

<?php get_footer(); ?>
