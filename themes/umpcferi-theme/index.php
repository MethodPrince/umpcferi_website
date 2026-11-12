<?php get_header(); ?>

<div class="hero">
    <h2>Welcome to UmpCferi</h2>
    <p>Your portal for newsletters, programs, events, and direct support from the UMP CFERI team.</p>
    <div class="hero-actions">
        <a class="button" href="<?php echo esc_url( home_url( '/umpcferi-app' ) ); ?>">Open the App</a>
        <a class="button button-secondary" href="<?php echo esc_url( home_url( '/login' ) ); ?>">Login</a>
    </div>
</div>

<section>
    <h2>About UmpCferi</h2>
    <p>UmpCferi is built to connect students, entrepreneurs, and community partners with the right resources, news, and program opportunities.</p>
</section>

<section>
    <h2>Upcoming Events</h2>
    <?php
    $events = get_posts( array( 'post_type' => 'umpcferi_event', 'posts_per_page' => 3, 'post_status' => 'publish' ) );
    if ( $events ) {
        echo '<ul class="home-list">';
        foreach ( $events as $event ) {
            $date = get_post_meta( $event->ID, 'umpcferi_event_date', true );
            $location = get_post_meta( $event->ID, 'umpcferi_event_location', true );
            echo '<li><strong>' . esc_html( $event->post_title ) . '</strong>';
            if ( $date ) {
                echo ' — ' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) );
            }
            if ( $location ) {
                echo ' | ' . esc_html( $location );
            }
            echo '<div>' . wp_trim_words( $event->post_content, 20, '...' ) . '</div></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>' . __( 'No events are scheduled at the moment. Please check back soon.', 'umpcferi-theme' ) . '</p>';
    }
    ?>
</section>

<section>
    <h2>Partner Network</h2>
    <?php
    $partners = get_posts( array( 'post_type' => 'umpcferi_partner', 'posts_per_page' => 6, 'post_status' => 'publish' ) );
    if ( $partners ) {
        echo '<div class="partner-grid">';
        foreach ( $partners as $partner ) {
            echo '<article class="partner-card">';
            echo '<h3>' . esc_html( $partner->post_title ) . '</h3>';
            echo '<div>' . wp_trim_words( $partner->post_content, 20, '...' ) . '</div>';
            echo '</article>';
        }
        echo '</div>';
    } else {
        echo '<p>' . __( 'We are building our partner network. Check back soon for updates.', 'umpcferi-theme' ) . '</p>';
    }
    ?>
</section>

<section class="newsletter-preview">
    <h2>Latest Newsletter</h2>
    <?php
    $latest = get_posts( array( 'post_type' => 'umpcferi_newsletter', 'posts_per_page' => 1 ) );
    if ( ! empty( $latest ) ) {
        $attachment_id = get_post_meta( $latest[0]->ID, 'umpcferi_newsletter_attachment', true );
        if ( $attachment_id ) {
            $url = wp_get_attachment_url( $attachment_id );
            $filetype = get_post_mime_type( $attachment_id );
            echo '<p><strong>' . esc_html( $latest[0]->post_title ) . '</strong></p>';
            if ( strpos( $filetype, 'pdf' ) !== false ) {
                echo '<iframe src="' . esc_url( $url ) . '" style="width:100%;min-height:420px;border:1px solid #ddd;"></iframe>';
            } else {
                echo '<p>' . __( 'This newsletter can be downloaded below.', 'umpcferi-theme' ) . '</p>';
            }
            echo '<p><a class="umpcferi-newsletter-download" href="' . esc_url( $url ) . '" download>' . __( 'Download Newsletter', 'umpcferi-theme' ) . '</a></p>';
        }
    } else {
        echo '<p>' . __( 'No newsletter available yet.', 'umpcferi-theme' ) . '</p>';
    }
    ?>
</section>

<?php get_footer(); ?>
