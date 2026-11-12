<?php get_header(); ?>

<div class="home-page">
    <section class="hero-section">
        <div class="hero-slideshow" id="hero-slideshow">
            <div class="hero-slide active" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/Images/vice chancellorImages/DSC_5377.jpg' ); ?>');"></div>
            <div class="hero-slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/Images/vice chancellorImages/DSC_5385.jpg' ); ?>');"></div>
            <div class="hero-slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/Images/audiencesimages/IMG_0738.jpg' ); ?>');"></div>
            <div class="hero-slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/Images/speakersimages/IMG_0756.jpg' ); ?>');"></div>
            <div class="hero-slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/Images/interactionImages/IMG_1403.jpg' ); ?>');"></div>
            <div class="hero-slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/Images/groupimages/IMG_1511.jpg' ); ?>');"></div>
            <div class="hero-slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/Images/vice chancellorImages/DSC_5388.jpg' ); ?>');"></div>
            <div class="hero-slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/Images/vice chancellorImages/DSC_5391.jpg' ); ?>');"></div>
        </div>
        <div class="hero-media">
            <div class="hero-overlay"></div>
        </div>

        <div class="container hero-content-wrap">
            <div class="hero-content">
                <p class="eyebrow">Centre for Entrepreneurship and Rapid Incubator</p>
                <h1>UMPCFERI</h1>
                <p class="hero-tagline">Creating Opportunities </p>
                <div class="hero-actions">
                    <?php
                    // Resolve About page URL robustly: try slug, then title, then /about path
                    $about_url = '';
                    $about_page = get_page_by_path( 'about' );
                    if ( $about_page ) {
                        $about_url = get_permalink( $about_page );
                    } else {
                        $p = get_page_by_title( 'About' );
                        if ( $p ) {
                            $about_url = get_permalink( $p );
                        }
                    }
                    if ( empty( $about_url ) ) {
                        $about_url = home_url( '/about' );
                    }
                    ?>
                    <a class="button" href="<?php echo esc_url( $about_url ); ?>">Learn More</a>
                    <a class="button button-secondary" href="<?php echo esc_url( home_url( '/contact-us' ) ); ?>">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content-section programs-section">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Our Programs</p>
                <h2>Support designed for every stage of entrepreneurial growth.</h2>
                <p>From incubation and mentorship to innovation and research, UMPCFERI offers practical support that turns ideas into action.</p>
            </div>

            <?php
            $programs_page = get_page_by_path( 'programs' );
            $programs_url = $programs_page ? get_permalink( $programs_page->ID ) : home_url( '/?page_id=13' );
            ?>

            <div class="program-cards">
                <a class="program-card" href="<?php echo esc_url( $programs_url . '#incubation-programs' ); ?>">
                    <h3>Incubation Programs</h3>
                    <p>Facilities, industry development, coaching and business planning support for early-stage founders.</p>
                    <span>View details</span>
                </a>
                <a class="program-card accent-red" href="<?php echo esc_url( $programs_url . '#entrepreneurship-development' ); ?>">
                    <h3>Entrepreneurship Development</h3>
                    <p>Student Indaba, EDHE competition support, chapter development and practical growth opportunities.</p>
                    <span>View details</span>
                </a>
                <a class="program-card accent-purple" href="<?php echo esc_url( $programs_url . '#research-activities' ); ?>">
                    <h3>Research Activities</h3>
                    <p>Research, consultancy, publications and knowledge-building that strengthen the ecosystem.</p>
                    <span>View details</span>
                </a>
            </div>
        </div>
    </section>

    <section class="content-section features-section">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Why Choose UMPCFERI</p>
                <h2>Real support, practical guidance and a thriving network.</h2>
            </div>

            <div class="feature-grid">
                <article class="feature-card">
                    <h3>Expert Mentorship</h3>
                    <p>Learn from experienced entrepreneurs and industry leaders who guide your next step.</p>
                </article>
                <article class="feature-card">
                    <h3>Funding Access</h3>
                    <p>Connect with opportunities, grants and funding pathways through a strong partner network.</p>
                </article>
                <article class="feature-card">
                    <h3>Global Network</h3>
                    <p>Grow beyond campus with access to markets, collaborations and professional exposure.</p>
                </article>
                <article class="feature-card">
                    <h3>State-of-the-Art Facilities</h3>
                    <p>Work from a modern incubation environment with meeting spaces, resources and support.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="content-section gallery-section">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Event Gallery</p>
                <h2>EDHE Absa Entrepreneurship innoVarsity 2026 National Lunch.</h2>
                <p>A celebration of innovation, collaboration and entrepreneurial excellence.</p>
            </div>

            <div class="gallery-grid">
                <article class="gallery-card">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/Images/speakersimages/IMG_0756.jpg' ); ?>" alt="Speakers">
                    <div class="gallery-card-content">
                        <h3>Speakers</h3>
                        <p>Inspiring voices shaping the future.</p>
                    </div>
                </article>
                <article class="gallery-card">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/Images/groupimages/IMG_1511.jpg' ); ?>" alt="Group activities">
                    <div class="gallery-card-content">
                        <h3>Group Activities</h3>
                        <p>Collaborative learning and innovation.</p>
                    </div>
                </article>
                <article class="gallery-card">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/Images/interactionImages/IMG_1403.jpg' ); ?>" alt="Networking">
                    <div class="gallery-card-content">
                        <h3>Networking</h3>
                        <p>Building meaningful connections.</p>
                    </div>
                </article>
                <article class="gallery-card">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/Images/choirimages/IMG_0783.jpg' ); ?>" alt="Choir performance">
                    <div class="gallery-card-content">
                        <h3>CHOIR</h3>
                        <p>Celebrating culture and excellence.</p>
                    </div>
                </article>
                <article class="gallery-card">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/Images/audiencesimages/IMG_0961.jpg' ); ?>" alt="Community audience">
                    <div class="gallery-card-content">
                        <h3>Community</h3>
                        <p>Engaged participants and supporters.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="content-section split-section">
        <div class="container split-grid">
            <div class="events-panel">
                <div class="section-heading compact">
                    <p class="eyebrow">Upcoming Events</p>
                    <h2>What is happening next.</h2>
                </div>
                <?php
                $events = get_posts( array(
                    'post_type' => 'umpcferi_event',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'orderby' => 'meta_value',
                    'meta_key' => 'umpcferi_event_date',
                    'order' => 'ASC',
                ) );
                $upcoming_events = array();
                $previous_events = array();
                $now = current_time( 'timestamp', true );

                foreach ( $events as $event ) {
                    $date = get_post_meta( $event->ID, 'umpcferi_event_date', true );
                    if ( ! $date ) {
                        continue;
                    }

                    $end_time = get_post_meta( $event->ID, 'umpcferi_event_end_time', true );
                    if ( ! preg_match( '/^([01]\d|2[0-3]):[0-5]\d$/', $end_time ) ) {
                        $end_time = '23:59';
                    }

                    try {
                        $event_end = new DateTimeImmutable( $date . ' ' . $end_time . ':00', wp_timezone() );
                        if ( $event_end->getTimestamp() >= $now ) {
                            $upcoming_events[] = $event;
                        } else {
                            $previous_events[] = $event;
                        }
                    } catch ( Exception $e ) {
                        continue;
                    }
                }

                $upcoming_events = array_slice( $upcoming_events, 0, 3 );
                $previous_events = array_reverse( $previous_events );

                if ( $upcoming_events ) {
                    echo '<div class="event-list">';
                    foreach ( $upcoming_events as $event ) {
                        $date = get_post_meta( $event->ID, 'umpcferi_event_date', true );
                        $location = get_post_meta( $event->ID, 'umpcferi_event_location', true );
                        echo '<article class="event-item">';
                        echo '<h3>' . esc_html( $event->post_title ) . '</h3>';
                        if ( $date ) {
                            echo '<p class="event-meta">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '</p>';
                        }
                        if ( $location ) {
                            echo '<p>' . esc_html( $location ) . '</p>';
                        }
                        echo '<p>' . esc_html( wp_trim_words( $event->post_content, 18, '...' ) ) . '</p>';
                        echo '</article>';
                    }
                    echo '</div>';
                } else {
                    echo '<p>' . esc_html__( 'No upcoming events are scheduled yet.', 'umpcferi-theme' ) . '</p>';
                }

                if ( $previous_events ) {
                    $previous_events_id = wp_unique_id( 'umpcferi-home-previous-events-' );
                    $show_previous_label = __( 'View previous events', 'umpcferi-theme' );
                    $hide_previous_label = __( 'Hide previous events', 'umpcferi-theme' );
                    echo '<button type="button" class="events-previous-toggle" aria-controls="' . esc_attr( $previous_events_id ) . '" aria-expanded="false" data-show-label="' . esc_attr( $show_previous_label ) . '" data-hide-label="' . esc_attr( $hide_previous_label ) . '">' . esc_html( $show_previous_label ) . '</button>';
                    echo '<div id="' . esc_attr( $previous_events_id ) . '" class="events-previous" hidden>';
                    echo '<h3>' . esc_html__( 'Previous Events', 'umpcferi-theme' ) . '</h3>';
                    echo '<div class="event-list">';
                    foreach ( $previous_events as $event ) {
                        $date = get_post_meta( $event->ID, 'umpcferi_event_date', true );
                        $location = get_post_meta( $event->ID, 'umpcferi_event_location', true );
                        echo '<article class="event-item">';
                        echo '<h3>' . esc_html( $event->post_title ) . '</h3>';
                        if ( $date ) {
                            echo '<p class="event-meta">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '</p>';
                        }
                        if ( $location ) {
                            echo '<p>' . esc_html( $location ) . '</p>';
                        }
                        echo '<p>' . esc_html( wp_trim_words( $event->post_content, 18, '...' ) ) . '</p>';
                        echo '</article>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>

            <div class="partners-panel">
                <div class="section-heading compact">
                    <p class="eyebrow">Partner Network</p>
                    <h2>Trusted organisations supporting the ecosystem.</h2>
                </div>
                <?php
                $partners = get_posts( array(
                    'post_type' => 'umpcferi_partner',
                    'posts_per_page' => 4,
                    'post_status' => 'publish',
                ) );
                if ( $partners ) {
                    echo '<div class="partner-list">';
                    foreach ( $partners as $partner ) {
                        echo '<article class="partner-item">';
                        echo '<h3>' . esc_html( $partner->post_title ) . '</h3>';
                        echo '<p>' . esc_html( wp_trim_words( $partner->post_content, 18, '...' ) ) . '</p>';
                        echo '</article>';
                    }
                    echo '</div>';
                } else {
                    echo '<p>We are building an exciting partner network.</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <section class="content-section cta-section">
        <div class="container">
            <h2>Ready to begin your journey?</h2>
            <p>Join the UMPCFERI community and turn your idea into impact.</p>
            <a class="button" href="<?php echo esc_url( home_url( '/contact-us' ) ); ?>">Get Started Today</a>
        </div>
    </section>
</div>

<?php get_footer(); ?>