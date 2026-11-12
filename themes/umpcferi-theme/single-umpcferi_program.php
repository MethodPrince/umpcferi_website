<?php get_header(); ?>

<section>
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            echo '<article class="program-detail">';
            echo '<h1>' . esc_html( get_the_title() ) . '</h1>';
            echo '<div>' . apply_filters( 'the_content', get_the_content() ) . '</div>';
            echo '</article>';
        }
    }
    ?>
</section>

<?php get_footer(); ?>
