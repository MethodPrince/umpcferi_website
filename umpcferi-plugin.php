<?php
/*
 * UMP CFERI Plugin (repaired)
 * Minimal header added to restore class context.
 */

if ( ! class_exists( 'Umpcferi_Plugin' ) ) {
class Umpcferi_Plugin {
    const VERSION = '1.0.0';

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_shortcodes' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'template_redirect', array( $this, 'handle_umpcferi_routes' ) );
        add_action( 'init', array( $this, 'add_umpcferi_rewrite_rules' ) );
    }

    public function register_post_types() {
        $labels = array(
            'name' => __( 'Programs', 'umpcferi-plugin' ),
            'singular_name' => __( 'Program', 'umpcferi-plugin' ),
        );
        register_post_type( 'umpcferi_program', array(
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'has_archive' => false,
            'supports' => array( 'title', 'editor', 'excerpt' ),
            'rewrite' => array( 'slug' => 'programs' ),
        ) );

        // Additional basic post types used by the plugin
        register_post_type( 'umpcferi_event', array(
            'labels' => array( 'name' => __( 'Events', 'umpcferi-plugin' ), 'singular_name' => __( 'Event', 'umpcferi-plugin' ) ),
            'public' => true,
            'show_ui' => true,
            'supports' => array( 'title', 'editor' ),
        ) );
        register_post_type( 'umpcferi_business', array(
            'labels' => array( 'name' => __( 'Student Businesses', 'umpcferi-plugin' ), 'singular_name' => __( 'Business', 'umpcferi-plugin' ) ),
            'public' => true,
            'show_ui' => true,
            'supports' => array( 'title', 'editor' ),
        ) );
        register_post_type( 'umpcferi_slide', array(
            'labels' => array( 'name' => __( 'Slides', 'umpcferi-plugin' ), 'singular_name' => __( 'Slide', 'umpcferi-plugin' ) ),
            'public' => false,
            'show_ui' => true,
            'supports' => array( 'title', 'editor' ),
        ) );
        register_post_type( 'umpcferi_partner', array(
            'labels' => array( 'name' => __( 'Partners', 'umpcferi-plugin' ), 'singular_name' => __( 'Partner', 'umpcferi-plugin' ) ),
            'public' => false,
            'show_ui' => true,
            'supports' => array( 'title', 'editor' ),
        ) );
        register_post_type( 'umpcferi_mentor', array(
            'labels' => array( 'name' => __( 'Mentors', 'umpcferi-plugin' ), 'singular_name' => __( 'Mentor', 'umpcferi-plugin' ) ),
            'public' => false,
            'show_ui' => true,
            'supports' => array( 'title', 'editor' ),
        ) );
        register_post_type( 'umpcferi_gallery_image', array(
            'labels' => array( 'name' => __( 'Gallery Images', 'umpcferi-plugin' ), 'singular_name' => __( 'Gallery Image', 'umpcferi-plugin' ) ),
            'public' => false,
            'show_ui' => true,
            'supports' => array( 'title' ),
        ) );
        register_post_type( 'umpcferi_newsletter', array(
            'labels' => array( 'name' => __( 'Newsletters', 'umpcferi-plugin' ), 'singular_name' => __( 'Newsletter', 'umpcferi-plugin' ) ),
            'public' => false,
            'show_ui' => true,
            'supports' => array( 'title' ),
        ) );
    }

    public function create_sample_content() {
        // Create main pages
        $this->create_sample_page( 'Home', '[umpcferi_home]', 'home', 0, true );
        $this->create_sample_page( 'About', '[umpcferi_about]', 'about', 1, true );
        $this->create_sample_page( 'Programs', '[umpcferi_programs]', 'programs', 2, true );
        $this->create_sample_page( 'Newsletter', '[umpcferi_newsletter_page]', 'newsletter', 3, true );
        $this->create_sample_page( 'Contact Us', '[umpcferi_contact]', 'contact-us', 4, true );
        $this->create_sample_page( 'UMP CFERI App', '[umpcferi_app]', 'umpcferi-app', 5, true );
        $this->create_sample_page( 'Events', '[umpcferi_events]', 'events', 6, true );

        // Create Events (from React app)
        if ( ! $this->has_published_posts( 'umpcferi_event' ) ) {
            $events = array(
                array(
                    'title' => 'Entrepreneurship Launch Day',
                    'description' => 'An annual event featuring startup pitches, mentor sessions, and networking opportunities.',
                    'date' => date( 'Y-m-d', strtotime( '+14 days' ) ),
                    'location' => 'UMP CFERI Campus',
                ),
                array(
                    'title' => 'Startup Pitch Competition',
                    'description' => 'Showcase your business idea and compete for funding and mentorship opportunities.',
                    'date' => date( 'Y-m-d', strtotime( '+21 days' ) ),
                    'location' => 'University of Mpumalanga',
                ),
                array(
                    'title' => 'Networking Breakfast',
                    'description' => 'Meet with entrepreneurs, investors, and mentors over breakfast.',
                    'date' => date( 'Y-m-d', strtotime( '+7 days' ) ),
                    'location' => 'Student Hub',
                ),
                array(
                    'title' => 'Business Model Workshop',
                    'description' => 'Learn how to develop a sustainable business model for your startup.',
                    'date' => date( 'Y-m-d', strtotime( '+30 days' ) ),
                    'location' => 'CFERI Innovation Hub',
                ),
            );

            foreach ( $events as $event ) {
                $event_id = wp_insert_post( array(
                    'post_type' => 'umpcferi_event',
                    'post_title' => $event['title'],
                    'post_content' => $event['description'],
                    'post_excerpt' => wp_trim_words( $event['description'], 20, '...' ),
                    'post_status' => 'publish',
                ) );
                if ( $event_id ) {
                    update_post_meta( $event_id, 'umpcferi_event_date', $event['date'] );
                    update_post_meta( $event_id, 'umpcferi_event_location', $event['location'] );
                }
            }
        }

        // Create Student Businesses (from React app - Impact page)
        if ( ! $this->has_published_posts( 'umpcferi_business' ) ) {
            $businesses = array(
                array(
                    'name' => 'BrightStart Technologies',
                    'owner' => 'Thabo Mkhize',
                    'industry' => 'Technology',
                    'description' => 'A student-founded startup creating digital tools for local businesses.',
                    'yearStarted' => 2022,
                    'jobsCreated' => 8,
                ),
                array(
                    'name' => 'GreenLeaf Solutions',
                    'owner' => 'Nomsa Dlamini',
                    'industry' => 'Agriculture',
                    'description' => 'Sustainable agriculture technology platform connecting farmers with markets.',
                    'yearStarted' => 2023,
                    'jobsCreated' => 12,
                ),
                array(
                    'name' => 'SkillHub',
                    'owner' => 'David Chen',
                    'industry' => 'Education',
                    'description' => 'Online platform for skill development and professional training.',
                    'yearStarted' => 2021,
                    'jobsCreated' => 15,
                ),
                array(
                    'name' => 'EcoPackaging',
                    'owner' => 'Zanele Nkosi',
                    'industry' => 'Manufacturing',
                    'description' => 'Eco-friendly packaging solutions for sustainable businesses.',
                    'yearStarted' => 2022,
                    'jobsCreated' => 10,
                ),
                array(
                    'name' => 'HealthCare Connect',
                    'owner' => 'Sipho Malinga',
                    'industry' => 'Healthcare',
                    'description' => 'Mobile health platform bringing quality healthcare to rural areas.',
                    'yearStarted' => 2023,
                    'jobsCreated' => 20,
                ),
            );

            foreach ( $businesses as $business ) {
                $business_id = wp_insert_post( array(
                    'post_type' => 'umpcferi_business',
                    'post_title' => $business['name'],
                    'post_content' => $business['description'],
                    'post_excerpt' => $business['description'],
                    'post_status' => 'publish',
                ) );
                if ( $business_id ) {
                    update_post_meta( $business_id, 'umpcferi_business_owner', $business['owner'] );
                    update_post_meta( $business_id, 'umpcferi_business_industry', $business['industry'] );
                    update_post_meta( $business_id, 'umpcferi_business_year_started', $business['yearStarted'] );
                    update_post_meta( $business_id, 'umpcferi_business_jobs_created', $business['jobsCreated'] );
                }
            }
        }

        // Create Programs
        $this->ensure_program_posts();

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

        if ( ! $this->has_published_posts( 'umpcferi_partner' ) ) {
            wp_insert_post( array(
                'post_type' => 'umpcferi_partner',
                'post_title' => 'CFERI Innovation Hub',
                'post_content' => 'A strategic partner supporting entrepreneurship development through shared resources and networking.',
                'post_status' => 'publish',
                'menu_order' => 1,
            ) );
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

    private function ensure_program_posts() {
        $programs = array(
            array(
                'title' => 'Renting Facilities',
                'description' => 'Access to affordable workspace and facilities for your startup.',
                'excerpt' => 'Affordable workspace, meeting rooms and startup facilities.',
                'category' => 'Incubation Programs',
                'full_details' => 'State-of-the-art facilities including co-working spaces, meeting rooms, and specialized equipment available at subsidized rates for student entrepreneurs. Located at the UMPCFERI hub on Mbombela Campus.',
                'full_description' => 'UMPCFERI gives founders access to affordable, flexible spaces for developing and growing their ventures. The facilities are designed to support early-stage startups with professional environments, meeting rooms, access to equipment and networking opportunities.',
                'benefits' => array( 'Affordable rates', 'Flexible terms', 'Access to equipment', 'Networking opportunities' ),
                'eligibility' => array( 'Registered or aspiring student entrepreneurs', 'Startups in early development stages', 'Open to UMP and partner stakeholders' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 1,
            ),
            array(
                'title' => 'Mentorship',
                'description' => 'Industry-specific mentorship and networking opportunities.',
                'excerpt' => 'Connect with experienced professionals and industry mentors.',
                'category' => 'Incubation Programs',
                'full_details' => 'Connect with industry experts, attend sector-specific workshops, and gain insights into market trends through structured mentorship support.',
                'full_description' => 'The mentorship programme links founders with experienced professionals who can guide product, market and business development decisions. It includes one-on-one sessions, workshops and peer-learning opportunities.',
                'benefits' => array( 'Industry mentors', 'Sector workshops', 'Market insights', 'Professional network' ),
                'eligibility' => array( 'Studentpreneurs and incubated ventures', 'Founders with a clear business or product idea', 'Open to both current and aspiring founders' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 2,
            ),
            array(
                'title' => 'Business Compliance Planning & Registration',
                'description' => 'Support to help founders formalise and structure their ventures correctly.',
                'excerpt' => 'Guidance for registration, compliance and business setup.',
                'category' => 'Incubation Programs',
                'full_details' => 'Get practical support on business registration, compliance planning and formalising your startup in a way that is clear and manageable.',
                'full_description' => 'This programme helps entrepreneurs understand the legal, regulatory and administrative steps required to formalise their venture. It focuses on helping founders build a legally sound and sustainable business foundation.',
                'benefits' => array( 'Registration support', 'Compliance guidance', 'Structure and planning', 'Business readiness' ),
                'eligibility' => array( 'Existing or prospective founders', 'Individuals at registration or compliance planning stage', 'Entrepreneurs seeking formalisation support' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 3,
            ),
            array(
                'title' => 'Business Plan Development',
                'description' => 'Expert guidance in creating comprehensive business plans.',
                'excerpt' => 'Develop investor-ready business plans with expert support.',
                'category' => 'Incubation Programs',
                'full_details' => 'One-on-one coaching to develop a robust business plan that attracts investors. Covers market analysis, financial projections and operational strategies.',
                'full_description' => 'Entrepreneurs receive practical support in structuring their business model, documenting growth assumptions and preparing for investor or partner conversations.',
                'benefits' => array( 'Expert coaches', 'Investor-ready plans', 'Financial modeling', 'Market research support' ),
                'eligibility' => array( 'Founders preparing for funding or partnerships', 'Student entrepreneurs refining their venture model', 'Innovators with a testable idea' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 4,
            ),
            array(
                'title' => 'Coaching on Record Keeping & Accounting',
                'description' => 'Training in financial management and bookkeeping.',
                'excerpt' => 'Build strong financial habits and bookkeeping skills.',
                'category' => 'Incubation Programs',
                'full_details' => 'Workshops and training sessions on financial literacy, bookkeeping, tax compliance and using accounting software for small businesses.',
                'full_description' => 'A practical financial literacy track designed to build confidence in recordkeeping, budgeting, taxes and basic business accounting.',
                'benefits' => array( 'Financial literacy', 'Bookkeeping skills', 'Tax compliance', 'Software training' ),
                'eligibility' => array( 'Studentpreneurs and SMEs', 'Any founder who wants stronger financial discipline', 'Businesses needing better reporting systems' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 5,
            ),
            array(
                'title' => 'Coaching on Market Strategy Development',
                'description' => 'Strategic guidance on market positioning and growth.',
                'excerpt' => 'Develop stronger market entry and growth strategies.',
                'category' => 'Incubation Programs',
                'full_details' => 'Develop effective market entry and growth strategies. Learn about customer acquisition, branding, pricing strategies and competitive analysis.',
                'full_description' => 'This support stream helps startups refine how they position themselves, who they target and how they compete in the market.',
                'benefits' => array( 'Market positioning', 'Growth strategies', 'Customer acquisition', 'Competitive analysis' ),
                'eligibility' => array( 'Startups preparing to launch or scale', 'Founders validating product-market fit', 'Entrepreneurs wanting stronger customer strategy' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 6,
            ),
            array(
                'title' => 'Staff Entrepreneurship Capacity Building',
                'description' => 'Programs to equip staff with entrepreneurial skills.',
                'excerpt' => 'Build entrepreneurial capacity across staff and support teams.',
                'category' => 'Entrepreneurship Development Programs',
                'full_details' => 'Workshops and training sessions designed to help university staff develop entrepreneurial mindsets and skills, enabling them to better support student entrepreneurs.',
                'full_description' => 'This programme strengthens the capacity of academic and support staff to embed entrepreneurial thinking into teaching, mentoring and institutional support systems.',
                'benefits' => array( 'Skill development', 'Mentor training', 'Curriculum integration', 'Research opportunities' ),
                'eligibility' => array( 'University staff and support professionals', 'Academic and administrative teams', 'Partners involved in entrepreneurship development' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 7,
            ),
            array(
                'title' => 'UNIIC',
                'description' => 'A platform for university innovation, entrepreneurship and collaboration.',
                'excerpt' => 'Support innovation and entrepreneurship across university communities.',
                'category' => 'Entrepreneurship Development Programs',
                'full_details' => 'The UNIIC initiative connects innovation, entrepreneurship and collaborative development across academic and industry networks.',
                'full_description' => 'UNIIC promotes innovation and entrepreneurship through partnerships, knowledge-sharing and collaborative activities that connect universities, students and industry stakeholders.',
                'benefits' => array( 'Innovation support', 'Collaboration', 'Entrepreneurial exposure', 'Institutional partnerships' ),
                'eligibility' => array( 'Students, staff and innovation networks', 'Universities and partner institutions', 'Entrepreneurship champions and ecosystem actors' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 8,
            ),
            array(
                'title' => 'EDHE Programme',
                'description' => 'Entrepreneurship Development in Higher Education - National Platform.',
                'excerpt' => 'A national entrepreneurship development platform for students and academics.',
                'category' => 'Entrepreneurship Development Programs',
                'full_details' => 'The EDHE Programme develops the entrepreneurial capacity of students, academics and leaders through entrepreneurship education, training and support across institutions.',
                'full_description' => 'The EDHE Programme is a national platform focused on developing entrepreneurship capacity in students, academics and institutional leaders while supporting the broader entrepreneurial university agenda.',
                'goals' => array( 'Student entrepreneurship and graduate employability', 'Entrepreneurship development in academia', 'Developing entrepreneurial universities' ),
                'benefits' => array( 'Student entrepreneurship', 'Staff development', 'Curriculum development', 'National partnerships' ),
                'eligibility' => array( 'Students', 'Academics and support professionals', 'Higher education institutions' ),
                'contact' => 'info@edhe.co.za',
                'external_link' => 'https://edhe.co.za/',
                'social_links' => array( 'facebook' => 'https://www.facebook.com/EDHEentrepreneurship', 'instagram' => 'https://www.instagram.com/edheentrepreneurship/', 'linkedin' => 'https://www.linkedin.com/company/entrepreneurship-development-in-higher-education-edhe/?viewAsMember=true' ),
                'order' => 9,
            ),
            array(
                'title' => 'Student Indaba',
                'description' => 'Annual gathering for students to showcase innovative ideas.',
                'excerpt' => 'Showcase your ideas, connect with mentors and investors.',
                'category' => 'Entrepreneurship Development Programs',
                'full_details' => 'A platform for students to present innovative projects and business ideas to a panel of judges, industry experts and potential investors.',
                'full_description' => 'Student Indaba brings together student entrepreneurs from across South Africa to pitch ideas, gain feedback and build networks with peers, mentors and investors.',
                'benefits' => array( 'Showcase ideas', 'Expert feedback', 'Investment opportunities', 'Peer learning' ),
                'eligibility' => array( 'Currently enrolled university students', 'Open to all disciplines and faculties', 'Must have a business idea or existing startup' ),
                'contact' => 'indaba@edhe.co.za',
                'order' => 10,
            ),
            array(
                'title' => 'Research in Entrepreneurship Development',
                'description' => 'An emerging hub for high-impact research in entrepreneurship, innovation and inclusive economic development.',
                'excerpt' => 'Research that informs policy, innovation and entrepreneurship development.',
                'category' => 'Research Activities',
                'full_details' => 'CFERI is an emerging hub for high-impact research in entrepreneurship, innovation and inclusive economic development with practical, evidence-based insights.',
                'full_description' => 'The research stream focuses on entrepreneurship, innovation and inclusive development, helping shape solutions that respond to real-world economic and social challenges.',
                'benefits' => array( 'Research grants', 'Publications', 'Conference presentations', 'Policy influence' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 11,
            ),
            array(
                'title' => 'Research Consultancy',
                'description' => 'Tailored research consultancy services for government, private sector and development partners.',
                'excerpt' => 'Data-driven consultancy for strategy, policy and impact work.',
                'category' => 'Research Activities',
                'full_details' => 'CFERI provides research consultancy services tailored to the needs of government, private sector organisations and development partners.',
                'full_description' => 'The consultancy service offers research support for policy, evaluation and strategy work across innovation, entrepreneurship and SME development.',
                'benefits' => array( 'Expert advice', 'Custom solutions', 'Industry insights', 'Strategic planning' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 12,
            ),
            array(
                'title' => 'Policy Advocacy',
                'description' => 'Contributing to policy development through research-driven advocacy.',
                'excerpt' => 'Shape policy through evidence-based entrepreneurship advocacy.',
                'category' => 'Research Activities',
                'full_details' => 'CFERI actively contributes to policy development through research-driven advocacy that strengthens entrepreneurial ecosystems and inclusive growth.',
                'full_description' => 'CFERI uses evidence and stakeholder engagement to contribute to policy development that supports entrepreneurship ecosystems and inclusive growth.',
                'benefits' => array( 'Policy development', 'Stakeholder engagement', 'Inclusive growth', 'Ecosystem strengthening' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 13,
            ),
            array(
                'title' => 'Research Publications',
                'description' => 'Growing portfolio of peer-reviewed publications and conference outputs.',
                'excerpt' => 'Share knowledge through publications and academic contributions.',
                'category' => 'Research Activities',
                'full_details' => 'CFERI has a growing portfolio of peer-reviewed publications and conference outputs that contribute to academic scholarship and policy discussions.',
                'full_description' => 'CFERI continues to build a meaningful portfolio of academic and practical publications that strengthen the entrepreneurship knowledge base.',
                'benefits' => array( 'Academic recognition', 'Knowledge sharing', 'Industry impact', 'Author opportunities' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 14,
            ),
            array(
                'title' => 'Partner with CFERI',
                'description' => 'Collaboration opportunities with government institutions, industry partners and development agencies.',
                'excerpt' => 'Create partnerships that drive innovation and impact.',
                'category' => 'Research Activities',
                'full_details' => 'CFERI welcomes collaboration with government institutions, industry partners, development agencies and academic institutions.',
                'full_description' => 'Partner with CFERI to create knowledge, shape policy and drive sustainable economic impact through collaborative innovation and entrepreneurship partnerships.',
                'benefits' => array( 'Generating knowledge', 'Shaping policy', 'Sustainable impact', 'Strategic collaboration' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 15,
            ),
            array(
                'title' => 'SWEEP',
                'description' => 'Student Women Economic Empowerment Programme.',
                'excerpt' => 'Empowering student women through entrepreneurship and leadership.',
                'category' => 'Student Chapters',
                'full_details' => 'SWEEP supports student women in economic participation through entrepreneurship, professional skills and leadership development.',
                'full_description' => 'SWEEP is a student-led initiative that focuses on entrepreneurship, leadership and economic empowerment for female students.',
                'goals' => array( 'Increase entrepreneurship awareness', 'Enhance business and resilience skills', 'Expand access to finance and support' ),
                'benefits' => array( 'Business management training', 'Mentorship', 'Funding opportunities', 'Networking' ),
                'eligibility' => array( 'Female university students', 'Current UMP or partner institution students', 'Students committed to personal and professional development' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'external_link' => 'https://edhe.co.za/sweep-2025/',
                'social_links' => array(
                    'facebook' => 'https://www.facebook.com/ump.sweep.chapter?mibextid=JroKGi',
                    'instagram' => 'https://www.instagram.com/ump_sweep_chapter?igsh=MTJnNG9mM20xZGF6ZQ==',
                    'linkedin' => 'https://www.linkedin.com/showcase/student-women-economic-empowerment-programme-sweep/?viewAsMember=true',
                    'tiktok' => 'https://www.tiktok.com/@umpsweep.chapter'
                ),
                'order' => 16,
            ),
            array(
                'title' => 'COP',
                'description' => 'Student Community of Practice.',
                'excerpt' => 'A collaborative community for students to learn from each other.',
                'category' => 'Student Chapters',
                'full_details' => 'The Community of Practice brings together students with shared interests in entrepreneurship to share knowledge and support each other.',
                'full_description' => 'COP is a collaborative learning community where students exchange knowledge, grow through shared experiences and strengthen their entrepreneurial journey together.',
                'benefits' => array( 'Peer learning', 'Collaborative projects', 'Workshops', 'Networking' ),
                'eligibility' => array( 'All UMP students interested in entrepreneurship', 'Open to all faculties and academic levels', 'Students committed to active participation' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'order' => 17,
            ),
            array(
                'title' => 'SABYA',
                'description' => 'South African BRICS Youth Association.',
                'excerpt' => 'Connect research, innovation and youth leadership.',
                'category' => 'Student Chapters',
                'full_details' => 'SABYA supports young scholars, researchers and innovators to connect academic excellence with entrepreneurship and community development.',
                'full_description' => 'SABYA is a youth platform that amplifies the voice of young people in key decision-making spaces while linking academic work with innovation and entrepreneurship.',
                'benefits' => array( 'Research mentorship', 'Peer support', 'Conferences', 'Innovation projects' ),
                'eligibility' => array( 'Honours, Master\'s and PhD students', 'Final-year undergraduates with research interests', 'Students in entrepreneurship, development and innovation' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'social_links' => array(
                    'instagram' => 'https://www.instagram.com/umpcferi_sabya_chapter'
                ),
                'order' => 18,
            ),
            array(
                'title' => 'HULT Prize',
                'description' => 'Global Student Entrepreneurship Competition.',
                'excerpt' => 'Compete and grow impact-driven entrepreneurial ideas.',
                'category' => 'Student Chapters',
                'full_details' => 'The Hult Prize provides a platform for students to develop innovative social enterprises that address pressing global challenges.',
                'full_description' => 'Hult Prize is a global student entrepreneurship competition focused on equipping student teams to build impact-driven ventures that respond to real social challenges.',
                'benefits' => array( 'Global exposure', 'Pitch opportunity', 'Mentorship', 'Impact-driven support' ),
                'eligibility' => array( 'Student teams with social enterprise ideas', 'Innovators and changemakers', 'Students looking for mentorship and competition exposure' ),
                'contact' => 'cferi_bdo@ump.ac.za',
                'social_links' => array(
                    'facebook' => 'https://www.facebook.com/HultPrize',
                    'instagram' => 'https://www.instagram.com/hultprize',
                    'twitter' => 'https://twitter.com/HultPrize',
                    'linkedin' => 'https://www.linkedin.com/company/hult-prize'
                ),
                'order' => 19,
            ),
        );

        foreach ( $programs as $program ) {
            $slug = sanitize_title( $program['title'] );
            $existing = get_posts( array(
                'post_type' => 'umpcferi_program',
                'name' => $slug,
                'post_status' => 'any',
                'posts_per_page' => 1,
                'fields' => 'ids',
            ) );

            $post_data = array(
                'post_type' => 'umpcferi_program',
                'post_title' => $program['title'],
                'post_name' => $slug,
                'post_content' => $program['full_details'],
                'post_excerpt' => $program['excerpt'],
                'post_status' => 'publish',
                'menu_order' => $program['order'],
            );

            if ( ! empty( $existing ) ) {
                $post_data['ID'] = $existing[0];
                $post_id = wp_update_post( $post_data );
            } else {
                $post_id = wp_insert_post( $post_data );
            }

            if ( $post_id ) {
                update_post_meta( $post_id, 'umpcferi_program_category', $program['category'] );
                update_post_meta( $post_id, 'umpcferi_program_full_details', $program['full_details'] );
                update_post_meta( $post_id, 'umpcferi_program_full_description', $program['full_description'] );
                update_post_meta( $post_id, 'umpcferi_program_benefits', isset( $program['benefits'] ) ? $program['benefits'] : array() );
                update_post_meta( $post_id, 'umpcferi_program_goals', isset( $program['goals'] ) ? $program['goals'] : array() );
                update_post_meta( $post_id, 'umpcferi_program_eligibility', isset( $program['eligibility'] ) ? $program['eligibility'] : array() );
                update_post_meta( $post_id, 'umpcferi_program_partners', isset( $program['partners'] ) ? $program['partners'] : array() );
                update_post_meta( $post_id, 'umpcferi_program_contact', isset( $program['contact'] ) ? $program['contact'] : '' );
                update_post_meta( $post_id, 'umpcferi_program_external_link', isset( $program['external_link'] ) ? $program['external_link'] : '' );
                update_post_meta( $post_id, 'umpcferi_program_social_links', isset( $program['social_links'] ) ? $program['social_links'] : array() );
                update_post_meta( $post_id, 'umpcferi_program_description', $program['description'] );
            }
        }
    }

    private function create_sample_page( $title, $content, $slug, $order = 0, $show_in_nav = false, $template = '' ) {
        $existing_page = get_page_by_path( $slug, OBJECT, 'page' );
        if ( $existing_page ) {
            if ( $template ) {
                update_post_meta( $existing_page->ID, '_wp_page_template', $template );
            }
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
        // Keep backwards compatibility for old /login link by redirecting to /events
        add_rewrite_rule( '^login/?$', 'index.php?umpcferi_redirect_login=1', 'top' );
    }

    public function add_umpcferi_query_vars( $vars ) {
        $vars[] = 'umpcferi_app';
        $vars[] = 'umpcferi_redirect_login';
        return $vars;
    }

    public function handle_umpcferi_routes() {
        $app = intval( get_query_var( 'umpcferi_app' ) );
        $is_app_page = is_page( 'umpcferi-app' );

        if ( $app || $is_app_page ) {
            status_header( 200 );
            get_header();
            echo '<main class="page-content"><section class="about-page"><h1>UMP CFERI App</h1><div class="umpcferi-app-root"><div id="umpcferi-root"></div></div></section></main>';
            get_footer();
            exit;
        }

        // Redirect legacy /login link to the new Events page
        $redirect_login = intval( get_query_var( 'umpcferi_redirect_login' ) );
        if ( $redirect_login === 1 ) {
            wp_redirect( home_url( '/events' ), 301 );
            exit;
        }
    }

    public function maybe_flush_rewrite_rules() {
        if ( get_option( 'umpcferi_plugin_rewrites_flushed' ) === self::VERSION ) {
            return;
        }

        flush_rewrite_rules();
        update_option( 'umpcferi_plugin_rewrites_flushed', self::VERSION );
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
            <h2><?php esc_html_e( 'Slideshow Settings', 'umpcferi-plugin' ); ?></h2>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'umpcferi_save_settings' ); ?>
                <input type="hidden" name="action" value="umpcferi_save_settings">
                <table class="form-table">
                    <tr>
                        <th><label for="umpcferi_priority_slide"><?php esc_html_e( 'Priority slide (select from theme images)', 'umpcferi-plugin' ); ?></label></th>
                        <td>
                            <select id="umpcferi_priority_slide" name="umpcferi_priority_slide" class="regular-text">
                                <option value=""><?php esc_html_e( '-- Use default --', 'umpcferi-plugin' ); ?></option>
                                <?php
                                $theme_dir = trailingslashit( get_stylesheet_directory() ) . 'images/vice chancellorImages';
                                $theme_uri = trailingslashit( get_stylesheet_directory_uri() ) . 'images/vice chancellorImages';
                                $current = get_option( 'umpcferi_priority_slide', '' );
                                if ( is_dir( $theme_dir ) ) {
                                    $files = glob( $theme_dir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE );
                                    if ( $files ) {
                                        foreach ( $files as $file ) {
                                            $name = basename( $file );
                                            $val = $name;
                                            printf( '<option value="%s" %s>%s</option>', esc_attr( $val ), selected( $current, $val, false ), esc_html( $name ) );
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button( __( 'Save Slideshow Settings', 'umpcferi-plugin' ) ); ?>
            </form>

            <h2><?php esc_html_e( 'Manage Events', 'umpcferi-plugin' ); ?></h2>
            <p><?php esc_html_e( 'Create or remove events that appear on the Events page.', 'umpcferi-plugin' ); ?></p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'umpcferi_create_event' ); ?>
                <input type="hidden" name="action" value="umpcferi_create_event">
                <table class="form-table">
                    <tr>
                        <th><label for="event_title"><?php esc_html_e( 'Title', 'umpcferi-plugin' ); ?></label></th>
                        <td><input type="text" id="event_title" name="event_title" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="event_date"><?php esc_html_e( 'Date (YYYY-MM-DD)', 'umpcferi-plugin' ); ?></label></th>
                        <td><input type="date" id="event_date" name="event_date" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="event_location"><?php esc_html_e( 'Location', 'umpcferi-plugin' ); ?></label></th>
                        <td><input type="text" id="event_location" name="event_location" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="event_content"><?php esc_html_e( 'Description', 'umpcferi-plugin' ); ?></label></th>
                        <td><textarea id="event_content" name="event_content" rows="4" class="large-text"></textarea></td>
                    </tr>
                </table>
                <?php submit_button( __( 'Create Event', 'umpcferi-plugin' ) ); ?>
            </form>

            <h3><?php esc_html_e( 'Existing Events', 'umpcferi-plugin' ); ?></h3>
            <div class="umpcferi-events-list">
                <?php
                $events = get_posts( array( 'post_type' => 'umpcferi_event', 'posts_per_page' => 100, 'orderby' => 'meta_value', 'meta_key' => 'umpcferi_event_date', 'order' => 'ASC' ) );
                if ( empty( $events ) ) {
                    echo '<p>' . esc_html__( 'No events yet.', 'umpcferi-plugin' ) . '</p>';
                } else {
                    echo '<table class="widefat fixed"><thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Actions</th></tr></thead><tbody>';
                    foreach ( $events as $e ) {
                        $d = get_post_meta( $e->ID, 'umpcferi_event_date', true );
                        $loc = get_post_meta( $e->ID, 'umpcferi_event_location', true );
                        $edit_link = get_edit_post_link( $e->ID );
                        echo '<tr>';
                        echo '<td>' . esc_html( $e->post_title ) . '</td>';
                        echo '<td>' . esc_html( $d ) . '</td>';
                        echo '<td>' . esc_html( $loc ) . '</td>';
                        echo '<td><a href="' . esc_url( $edit_link ) . '" class="button">Edit</a> ';
                        echo '<form style="display:inline-block;margin:0 6px;" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
                        wp_nonce_field( 'umpcferi_delete_event' );
                        echo '<input type="hidden" name="action" value="umpcferi_delete_event">';
                        echo '<input type="hidden" name="event_id" value="' . esc_attr( $e->ID ) . '">';
                        echo '<button class="button button-link-delete" type="submit" onclick="return confirm(\'Are you sure you want to delete this event?\')">Delete</button>';
                        echo '</form></td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
                ?>
            </div>
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
        check_admin_referer( 'umpcferi_send_message' );
        
        // Allow both logged-in users and visitors
        $senderName = sanitize_text_field( $_POST['umpcferi_sender_name'] ?? '' );
        $senderEmail = sanitize_email( $_POST['umpcferi_sender_email'] ?? '' );
        $message = sanitize_textarea_field( $_POST['umpcferi_message'] ?? '' );
        
        if ( empty( $senderName ) || empty( $senderEmail ) || empty( $message ) ) {
            wp_redirect( wp_get_referer() );
            exit;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'umpcferi_messages';
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        
        $wpdb->insert( $table, array(
            'user_id' => $user_id,
            'message' => $message,
            'status' => 'unanswered',
            'created_at' => current_time( 'mysql' ),
        ) );

        // Store sender info in a custom table or as post meta
        $message_id = $wpdb->insert_id;
        if ( $message_id ) {
            update_post_meta( $message_id, 'umpcferi_sender_name', $senderName );
            update_post_meta( $message_id, 'umpcferi_sender_email', $senderEmail );
        }

        wp_redirect( wp_get_referer() );
        exit;
    }

    public function handle_create_event() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'umpcferi-plugin' ) );
        }
        check_admin_referer( 'umpcferi_create_event' );

        $title = sanitize_text_field( $_POST['event_title'] ?? '' );
        $date = sanitize_text_field( $_POST['event_date'] ?? '' );
        $location = sanitize_text_field( $_POST['event_location'] ?? '' );
        $content = wp_kses_post( $_POST['event_content'] ?? '' );

        if ( empty( $title ) || empty( $date ) ) {
            wp_redirect( wp_get_referer() );
            exit;
        }

        $post_id = wp_insert_post( array(
            'post_type' => 'umpcferi_event',
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
        ) );

        if ( $post_id ) {
            update_post_meta( $post_id, 'umpcferi_event_date', $date );
            update_post_meta( $post_id, 'umpcferi_event_location', $location );
        }

        wp_redirect( wp_get_referer() );
        exit;
    }

    public function handle_delete_event() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'umpcferi-plugin' ) );
        }
        check_admin_referer( 'umpcferi_delete_event' );

        $id = intval( $_POST['event_id'] ?? 0 );
        if ( $id ) {
            wp_delete_post( $id, true );
        }

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
        wp_enqueue_style( 'umpcferi-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), null );
        wp_enqueue_script( 'umpcferi-script', plugin_dir_url( __FILE__ ) . 'assets/js/umpcferi.js', array( 'jquery' ), self::VERSION, true );
        wp_localize_script( 'umpcferi-script', 'umpcferi_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'umpcferi_ajax_nonce' ),
        ) );

        // Chatbot widget assets
        wp_enqueue_style( 'umpcferi-chatbot-style', plugin_dir_url( __FILE__ ) . 'assets/css/chatbot.css', array(), self::VERSION );
        wp_enqueue_script( 'umpcferi-chatbot', plugin_dir_url( __FILE__ ) . 'assets/js/chatbot.js', array(), self::VERSION, true );

        $preset_qas = array(
            array( 'q' => 'What is the UMP Center For Entrepreneurship Rapid and Incubator?', 'a' => 'The Centre for Entrepreneurship is an incubation hub dedicated to empowering aspiring and existing student entrepreneurs with practical skills, mentorship, resources, and support to start, grow, and sustain successful businesses that reduce unemployment.' ),
            array( 'q' => 'Is the center a society?', 'a' => 'No! The Centre for Entrepreneurship and Rapid Incubator is a technology business incubator housed at Old Building 12. UMPCFERI aims to empower student entrepreneurs, SMEs and alumni by developing entrepreneurial skills and providing business development support that drives business growth, commercialization, and job creation. UMPCFERI houses Student Chapters/initiatives (SWEEP/SABYA/COP).' ),
            array( 'q' => 'Does the center provide Funding?', 'a' => 'No! The Centre provides guidance on funding readiness, proposal writing, and information about available funding opportunities. However, funding is not guaranteed.' ),
            array( 'q' => 'What services does the Centre offer?', 'a' => 'We offer: • Entrepreneurship training, networking and workshops • Business idea development and validation • Business plan writing support • Financial literacy and bookkeeping training • Marketing and branding guidance • Mentorship, incubation support and coaching • Entrepreneurship Wellness Initiatives. Why should I participate in CFERI Activities? • UMPCFERI stimulates and supports entrepreneurial initiatives, providing networking opportunities through local, regional, and international exposure.' ),
        );

        wp_localize_script( 'umpcferi-chatbot', 'umpcferiChatbotData', array(
            'presetQas' => $preset_qas,
            'contactEmail' => 'cferi_bdo@ump.ac.za',
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

    public function render_chatbot_widget() {
        ?>
        <div id="umpcferi-chatbot" class="umpcferi-chatbot">
            <button id="umpcferi-chatbot-toggle" class="umpcferi-chatbot-toggle">Chat</button>
            <div id="umpcferi-chatbot-panel" class="umpcferi-chatbot-panel" aria-hidden="true">
                <div class="umpcferi-chatbot-header">Ask about UMP CFERI</div>
                <div class="umpcferi-chatbot-body">
                    <div id="umpcferi-chatbot-history" class="umpcferi-chatbot-history"></div>
                    <div class="umpcferi-chatbot-presets" id="umpcferi-chatbot-presets"></div>
                </div>
                <div class="umpcferi-chatbot-footer">
                    <a href="mailto:cferi_bdo@ump.ac.za" class="umpcferi-chatbot-contact">Contact Admin</a>
                </div>
            </div>
        </div>
        <?php
    }

    public function register_shortcodes() {
        add_shortcode( 'umpcferi_user_chat', array( $this, 'shortcode_user_chat' ) );
        add_shortcode( 'umpcferi_latest_newsletter', array( $this, 'shortcode_latest_newsletter' ) );
        add_shortcode( 'umpcferi_programs', array( $this, 'shortcode_programs' ) );
        add_shortcode( 'umpcferi_app', array( $this, 'shortcode_react_app' ) );
        add_shortcode( 'umpcferi_home', array( $this, 'shortcode_home' ) );
        add_shortcode( 'umpcferi_about', array( $this, 'shortcode_about' ) );
        add_shortcode( 'umpcferi_contact', array( $this, 'shortcode_contact' ) );
        add_shortcode( 'umpcferi_newsletter_page', array( $this, 'shortcode_newsletter_page' ) );
        add_shortcode( 'umpcferi_impact', array( $this, 'shortcode_impact' ) );
        add_shortcode( 'umpcferi_events', array( $this, 'shortcode_events' ) );
    }

    public function shortcode_home() {
        $output = '<div class="home-container">';
        $output .= '<h1 class="home-title">Welcome to UMP CFERI</h1>';
        $output .= '<p class="home-subtitle">Centre for Entrepreneurship and Rapid Incubator</p>';
        // Resolve About page URL robustly: try slug, then common titles, then /about path, then home
        $about_url = '';
        $about_page = get_page_by_path( 'about' );
        if ( $about_page ) {
            $about_url = get_permalink( $about_page );
        } else {
            // try common page titles
            $try_titles = array( 'About', 'About UMP CFERI', 'About UMP' );
            foreach ( $try_titles as $t ) {
                $p = get_page_by_title( $t );
                if ( $p ) {
                    $about_url = get_permalink( $p );
                    break;
                }
            }
        }
        if ( empty( $about_url ) ) {
            // try a friendly /about path
            $about_url = home_url( '/about' );
        }
        if ( empty( $about_url ) ) {
            $about_url = home_url( '/' );
        }
        $output .= '<div class="home-buttons">';
        $output .= '<a class="btn btn-primary" href="' . esc_url( $about_url ) . '">Learn More</a>';
        $output .= '</div>';
        $output .= '<div class="home-cards">';
        $output .= '<div class="home-card"><h3>Empowering Student Entrepreneurs</h3><p>Supporting students to turn ideas into successful businesses</p></div>';
        $output .= '<div class="home-card"><h3>Incubation Programs</h3><p>Structured support for early-stage startups</p></div>';
        $output .= '<div class="home-card"><h3>Mentorship</h3><p>Connect with experienced business mentors</p></div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    public function shortcode_about() {
        $output = '<div class="about-container">';
        $output .= '<h1 class="about-title">About UMP CFERI</h1>';
        $output .= '<div class="about-section">';
        $output .= '<h2>Centre for Entrepreneurship and Rapid Incubator</h2>';
        $output .= '<p class="about-description">UMP CFERI is a dynamic entrepreneurship hub dedicated to fostering innovation, supporting student startups, and driving economic growth through structured incubation programs and mentorship.</p>';
        $output .= '</div>';
        $output .= '<div class="about-grid">';
        $output .= '<div class="about-card">';
        $output .= '<h3 class="about-card-title">Our Mission</h3>';
        $output .= '<p>To empower students with entrepreneurial skills, resources, and mentorship needed to transform innovative ideas into sustainable businesses.</p>';
        $output .= '</div>';
        $output .= '<div class="about-card">';
        $output .= '<h3 class="about-card-title">Our Vision</h3>';
        $output .= '<p>To be the leading entrepreneurship centre in South Africa, producing successful student entrepreneurs who contribute to economic development.</p>';
        $output .= '</div>';
        $output .= '<div class="about-card">';
        $output .= '<h3 class="about-card-title">Our Values</h3>';
        $output .= '<ul class="about-list">';
        $output .= '<li class="about-list-item">✓ Innovation</li>';
        $output .= '<li class="about-list-item">✓ Excellence</li>';
        $output .= '<li class="about-list-item">✓ Collaboration</li>';
        $output .= '<li class="about-list-item">✓ Impact</li>';
        $output .= '</ul>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="about-section">';
        $output .= '<h2 class="about-title">Our Journey</h2>';
        $output .= '<div class="about-timeline">';
        $output .= '<div class="about-timeline-item"><h3 class="about-timeline-year">2020</h3><p>CFERI established with initial incubation program</p></div>';
        $output .= '<div class="about-timeline-item"><h3 class="about-timeline-year">2021</h3><p>First cohort of 10 student businesses incubated</p></div>';
        $output .= '<div class="about-timeline-item"><h3 class="about-timeline-year">2022</h3><p>Expanded to mentorship network and partnerships</p></div>';
        $output .= '<div class="about-timeline-item"><h3 class="about-timeline-year">2023</h3><p>50+ businesses supported, 100+ jobs created</p></div>';
        $output .= '<div class="about-timeline-item"><h3 class="about-timeline-year">2024</h3><p>Digital platform launch and expanded programs</p></div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="about-section">';
        $output .= '<h2 class="about-title">Key Facts</h2>';
        $output .= '<div class="about-grid">';
        $output .= '<div class="about-card"><div class="about-stat-number">50+</div><p>Businesses Supported</p></div>';
        $output .= '<div class="about-card"><div class="about-stat-number">100+</div><p>Jobs Created</p></div>';
        $output .= '<div class="about-card"><div class="about-stat-number">5+</div><p>Industry Sectors</p></div>';
        $output .= '<div class="about-card"><div class="about-stat-number">20+</div><p>Mentors</p></div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    public function shortcode_contact() {
        $output = '<div class="contact-container">';
        $output .= '<h1 class="contact-title">Contact Us</h1>';
        $output .= '<div class="contact-content">';
        $output .= '<div class="contact-info">';
        $output .= '<h3>Get in Touch</h3>';
        $output .= '<p><strong>Email:</strong> info@umpcferi.ac.za</p>';
        $output .= '<p><strong>Phone:</strong> +27 13 002 0000</p>';
        $output .= '<p><strong>Address:</strong> University of Mpumalanga, South Africa</p>';
        $output .= '</div>';
        $output .= '<div class="contact-form">';
        $output .= '<h3>Send us a Message</h3>';
        $output .= $this->render_contact_form();
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    public function render_contact_form() {
        $output = '<div class="contact-form-wrapper">';
        $output .= '<form id="umpcferi-contact-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
        $output .= wp_nonce_field( 'umpcferi_send_message', '_wpnonce', true, false );
        $output .= '<input type="hidden" name="action" value="umpcferi_send_message">';
        $output .= '<div class="contact-form-group">';
        $output .= '<input type="text" name="umpcferi_sender_name" class="contact-input" placeholder="Your Name" required>';
        $output .= '</div>';
        $output .= '<div class="contact-form-group">';
        $output .= '<input type="email" name="umpcferi_sender_email" class="contact-input" placeholder="Your Email" required>';
        $output .= '</div>';
        $output .= '<div class="contact-form-group">';
        $output .= '<textarea name="umpcferi_message" class="contact-textarea" placeholder="Your Message" rows="5" required></textarea>';
        $output .= '</div>';
        $output .= '<button type="submit" class="contact-button">' . esc_html__( 'Send Message', 'umpcferi-plugin' ) . '</button>';
        $output .= '</form>';
        $output .= '</div>';
        return $output;
    }

    public function shortcode_newsletter_page() {
        $output = '<div class="newsletter-container">';
        $output .= '<h1>Newsletter</h1>';
        $output .= '<p>Subscribe to our newsletter for the latest updates on programs, events, and opportunities.</p>';
        $output .= '[umpcferi_latest_newsletter]';
        $output .= '</div>';
        return do_shortcode( $output );
    }

    public function shortcode_impact() {
        $businesses = get_posts( array(
            'post_type' => 'umpcferi_business',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ) );

        $totalJobs = 0;
        $industries = array();
        foreach ( $businesses as $business ) {
            $jobs = get_post_meta( $business->ID, 'umpcferi_business_jobs_created', true );
            $totalJobs += intval( $jobs );
            $industry = get_post_meta( $business->ID, 'umpcferi_business_industry', true );
            if ( $industry && ! in_array( $industry, $industries ) ) {
                $industries[] = $industry;
            }
        }

        $output = '<div class="impact-container">';
        $output .= '<h1 class="impact-title">Our Impact</h1>';
        $output .= '<div class="impact-stats">';
        $output .= '<div class="impact-stat-card"><h2>' . count( $businesses ) . '</h2><p>Businesses Supported</p></div>';
        $output .= '<div class="impact-stat-card"><h2>' . $totalJobs . '</h2><p>Jobs Created</p></div>';
        $output .= '<div class="impact-stat-card"><h2>' . count( $industries ) . '</h2><p>Industries</p></div>';
        $output .= '</div>';
        $output .= '<h2 class="impact-subtitle">Student Businesses</h2>';
        $output .= '<div class="impact-grid">';

        foreach ( $businesses as $business ) {
            $owner = get_post_meta( $business->ID, 'umpcferi_business_owner', true );
            $industry = get_post_meta( $business->ID, 'umpcferi_business_industry', true );
            $jobs = get_post_meta( $business->ID, 'umpcferi_business_jobs_created', true );

            $output .= '<div class="impact-card">';
            $output .= '<h3>' . esc_html( $business->post_title ) . '</h3>';
            $output .= '<p><strong>Owner:</strong> ' . esc_html( $owner ) . '</p>';
            $output .= '<p><strong>Industry:</strong> ' . esc_html( $industry ) . '</p>';
            $output .= '<p><strong>Jobs Created:</strong> ' . intval( $jobs ) . '</p>';
            $output .= '</div>';
        }

        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    public function shortcode_events() {
        $events = get_posts( array(
            'post_type' => 'umpcferi_event',
            'posts_per_page' => -1,
            'orderby' => 'meta_value',
            'meta_key' => 'umpcferi_event_date',
            'order' => 'ASC',
        ) );
        $today = date( 'Y-m-d' );
        $upcoming = array();
        $past = array();

        foreach ( $events as $event ) {
            $date = get_post_meta( $event->ID, 'umpcferi_event_date', true );
            if ( $date && strtotime( $date ) >= strtotime( $today ) ) {
                $upcoming[] = $event;
            } else {
                $past[] = $event;
            }
        }

        $output = '<div class="events-container">';
        $output .= '<h1 class="events-title">Events & Programs</h1>';

        $output .= '<section class="events-upcoming"><h2>Upcoming Events</h2>';
        if ( empty( $upcoming ) ) {
            $output .= '<p>No upcoming events at the moment.</p>';
        } else {
            $output .= '<div class="events-grid">';
            foreach ( $upcoming as $event ) {
                $date = get_post_meta( $event->ID, 'umpcferi_event_date', true );
                $location = get_post_meta( $event->ID, 'umpcferi_event_location', true );
                $output .= '<div class="event-card">';
                $output .= '<h3>' . esc_html( $event->post_title ) . '</h3>';
                $output .= '<p class="event-description">' . esc_html( $event->post_content ) . '</p>';
                if ( $date ) {
                    $output .= '<p class="event-detail"><strong>Date:</strong> ' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '</p>';
                }
                if ( $location ) {
                    $output .= '<p class="event-detail"><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
                }
                $output .= '</div>';
            }
            $output .= '</div>';
        }
        $output .= '</section>';

        $output .= '<section class="events-past"><h2>Past Events</h2>';
        if ( empty( $past ) ) {
            $output .= '<p>No past events found.</p>';
        } else {
            $output .= '<div class="events-grid">';
            // Show most recent past events first
            usort( $past, function ( $a, $b ) {
                $da = get_post_meta( $a->ID, 'umpcferi_event_date', true );
                $db = get_post_meta( $b->ID, 'umpcferi_event_date', true );
                return strtotime( $db ) - strtotime( $da );
            } );
            foreach ( $past as $event ) {
                $date = get_post_meta( $event->ID, 'umpcferi_event_date', true );
                $location = get_post_meta( $event->ID, 'umpcferi_event_location', true );
                $output .= '<div class="event-card">';
                $output .= '<h3>' . esc_html( $event->post_title ) . '</h3>';
                $output .= '<p class="event-description">' . esc_html( $event->post_content ) . '</p>';
                if ( $date ) {
                    $output .= '<p class="event-detail"><strong>Date:</strong> ' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '</p>';
                }
                if ( $location ) {
                    $output .= '<p class="event-detail"><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
                }
                $output .= '</div>';
            }
            $output .= '</div>';
        }
        $output .= '</section>';

        $output .= '</div>';
        return $output;
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

        // Include images from the active theme's images/vice chancellorImages folder
        $theme_dir = trailingslashit( get_stylesheet_directory() ) . 'images/vice chancellorImages';
        $theme_uri = trailingslashit( get_stylesheet_directory_uri() ) . 'images/vice chancellorImages';
        $themeSlides = array();
        if ( is_dir( $theme_dir ) ) {
            $files = glob( $theme_dir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE );
            if ( $files ) {

                // Attempt to prioritise a file using the admin setting or fallback names
                $priority_setting = get_option( 'umpcferi_priority_slide', '' );
                $priority_index = -1;
                if ( ! empty( $priority_setting ) ) {
                    $needle = strtolower( $priority_setting );
                    foreach ( $files as $i => $file ) {
                        $base = strtolower( basename( $file ) );
                        if ( strpos( $base, $needle ) !== false ) {
                            $priority_index = $i;
                            break;
                        }
                    }
                }

                if ( $priority_index === -1 ) {
                    $fallback_names = array( 'first.jpg', 'first.jpeg', 'vice_chancellor.jpg', 'vice chancellor.jpg' );
                    foreach ( $files as $i => $file ) {
                        $base = strtolower( basename( $file ) );
                        if ( in_array( $base, $fallback_names, true ) ) {
                            $priority_index = $i;
                            break;
                        }
                    }
                }

                // If a priority file exists, move it to the front
                if ( $priority_index >= 0 ) {
                    $first = $files[ $priority_index ];
                    unset( $files[ $priority_index ] );
                    array_unshift( $files, $first );
                }

                foreach ( $files as $file ) {
                    if ( ! is_file( $file ) ) {
                        continue;
                    }
                    $name = basename( $file );
                    $url = $theme_uri . '/' . rawurlencode( $name );
                    $themeSlides[] = array(
                        '_id' => 'theme-' . md5( $file ),
                        'title' => '',
                        'caption' => '',
                        'image' => esc_url_raw( $url ),
                        'section' => 'home',
                        'order' => 0,
                        'active' => true,
                    );
                }
            }
        }
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
        // If theme slides were found, put them at the beginning so they show first
        if ( ! empty( $themeSlides ) ) {
            $result = array_merge( $themeSlides, $result );
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
        $programs = get_posts( array(
            'post_type' => 'umpcferi_program',
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ) );

        if ( empty( $programs ) ) {
            return '<p>' . esc_html__( 'No programs available at the moment.', 'umpcferi-plugin' ) . '</p>';
        }

        $grouped_programs = array();
        foreach ( $programs as $program ) {
            $category = get_post_meta( $program->ID, 'umpcferi_program_category', true );
            if ( empty( $category ) ) {
                $category = __( 'Programs', 'umpcferi-plugin' );
            }
            $grouped_programs[ $category ][] = $program;
        }

        // Static program data provided by the site owner. These are used in addition
        // to any programs found in the database so Learn More works for each item.
        $static_program_sections = array(
            'Incubation Programs' => array(
                'id' => 'incubation-programs',
                'color' => '#5b60ae',
                'description' => 'Comprehensive support for startups at every stage',
                'programs' => array(
                    array(
                        'id' => 'renting-facilities',
                        'name' => 'Renting Facilities',
                        'description' => 'Access to affordable workspace and facilities for your startup.',
                        'fullDetails' => 'State-of-the-art facilities including co-working spaces, meeting rooms, and specialized equipment available at subsidized rates for student entrepreneurs. Located at the UMPCFERI hub on Mbombela Campus.',
                        'benefits' => array('Affordable rates','Flexible terms','Access to equipment','Networking opportunities'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'industry-development',
                        'name' => 'Mentorship',
                        'description' => 'Industry-specific mentorship and networking opportunities.',
                        'fullDetails' => 'Connect with industry experts, attend sector-specific workshops, and gain insights into market trends. Program includes mentorship from established professionals in your field.',
                        'benefits' => array('Industry mentors','Sector workshops','Market insights','Professional network'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'business-compliance-planning',
                        'name' => 'Business Compliance Planning & Registration',
                        'description' => 'Industry-specific mentorship and networking opportunities.',
                        'fullDetails' => 'Connect with industry experts, attend sector-specific workshops, and gain insights into market trends. Program includes mentorship from established professionals in your field.',
                        'benefits' => array('Industry mentors','Sector workshops','Market insights','Professional network'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'business-plan',
                        'name' => 'Business Plan Development',
                        'description' => 'Expert guidance in creating comprehensive business plans.',
                        'fullDetails' => 'One-on-one coaching to develop a robust business plan that attracts investors. Covers market analysis, financial projections, and operational strategies.',
                        'benefits' => array('Expert coaches','Investor-ready plans','Financial modeling','Market research support'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'record-keeping',
                        'name' => 'Coaching on Record Keeping & Accounting',
                        'description' => 'Training in financial management and bookkeeping.',
                        'fullDetails' => 'Workshops and training sessions on financial literacy, bookkeeping, tax compliance, and using accounting software for small businesses.',
                        'benefits' => array('Financial literacy','Bookkeeping skills','Tax compliance','Software training'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'market-strategy',
                        'name' => 'Coaching on Market Strategy Development',
                        'description' => 'Strategic guidance on market positioning and growth.',
                        'fullDetails' => 'Develop effective market entry and growth strategies. Learn about customer acquisition, branding, pricing strategies, and competitive analysis.',
                        'benefits' => array('Market positioning','Growth strategies','Customer acquisition','Competitive analysis'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                ),
            ),
            'Entrepreneurship Development Programs' => array(
                'id' => 'student-funding-competitions',
                'color' => '#cd3131',
                'description' => 'Opportunities to win funding and recognition for your ideas',
                'programs' => array(
                    array(
                        'id' => 'staff-capacity',
                        'name' => 'Staff Entrepreneurship Capacity Building',
                        'description' => 'Programs to equip staff with entrepreneurial skills.',
                        'fullDetails' => 'Workshops and training sessions designed to help university staff develop entrepreneurial mindsets and skills, enabling them to better support student entrepreneurs.',
                        'benefits' => array('Skill development','Mentor training','Curriculum integration','Research opportunities'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'UNIIC',
                        'name' => 'UNIIC',
                        'description' => 'Programs to equip staff with entrepreneurial skills.',
                        'fullDetails' => 'Workshops and training sessions designed to help university staff develop entrepreneurial mindsets and skills, enabling them to better support student entrepreneurs.',
                        'benefits' => array('Skill development','Mentor training','Curriculum integration','Research opportunities'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'edhe-programme',
                        'name' => 'EDHE Programme',
                        'description' => 'Entrepreneurship Development in Higher Education - National Platform',
                        'fullDetails' => 'The EDHE Programme is people-focused. It is intended to develop the entrepreneurial capacity of students, academics and leaders. The intention is for students to be more successful in terms of becoming economically active during and after their tertiary education. Entrepreneurial activity during their studies could enable students to generate an additional income and fast-track the process of becoming economically active. Upon graduation, students would be able to consider entrepreneurship as a career, either as a first choice, or as an alternative, especially if they have difficulty in finding employment.',
                        'fullDescription' => <<<'EDHE'
Background on the EDHE Programme

Within the context of graduate and youth unemployment, the resources available at universities, the national drive for SMEs and the need for third-stream income at universities, the imperative to drive student and graduate entrepreneurship has been growing steadily. In response, a platform for Entrepreneurship Development in Higher Education (EDHE) was established at the end of 2016 from within the University Education Branch of the Department Higher Education and Training. The EDHE Programme has since been included in the University Capacity Development Programme (UCDP) of the Department Higher Education and Training.

The goals of the EDHE Programme are:

• Student entrepreneurship, i.e. mobilising the national student and graduate resource to create successful enterprises that will ultimately lead to both wealth and job creation.
• Entrepreneurship development in academia, i.e. support academics in instilling an entrepreneurial mindset within all students and graduates through the offering of relevant knowledge, transferral of practical skills and the application of business principles, not only to a specific discipline, but across disciplines.
• Developing entrepreneurial universities, i.e. creating a conducive environment that will enable universities to adapt strategically and embark on projects whereby third-stream income can be generated through innovative business ideas.

The EDHE Programme is people-focused. It is intended to develop the entrepreneurial capacity of students, academics and leaders. The intention is for students to be more successful in terms of becoming economically active during and after their tertiary education. Entrepreneurial activity during their studies could enable students to generate an additional income and fast-track the process of becoming economically active. Upon graduation, students would be able to consider entrepreneurship as a career, either as a first choice, or as an alternative, especially if they have difficulty in finding employment.

The people-focused nature of the EDHE Programme extends to developing the capacity of academics and support professionals to deliver informed and relevant services in terms of entrepreneurship education. This would include equipping academics across disciplines to encourage an entrepreneurial mindset and culture, as well as subject-specific entrepreneurship education, delivered with confidence.

The EDHE Programme is positioned to make entrepreneurship education and training accessible for all students across disciplines. This is a much-needed intervention that would better equip students to successfully participate in the economy upon graduation, regardless of whether they are employed or not.

The EDHE Programme, through driving entrepreneurship development in academia, is intended to optimise existing entrepreneurship research and encourage contextually relevant new research. This would include drawing together existing research on the broader topic of entrepreneurship development, but also specifically on the topic of entrepreneurial universities. Entrepreneurship research would be made available to university stakeholders, students and other stakeholders through a centralised online repository of information.

The UCDP emphasises an increasing focus on curriculum development initiatives that examine new and alternative contents and pedagogies which are relevant to the South African context. In terms of driving the entrepreneurship development agenda through the curriculum, the EDHE Programme is aimed at embedding entrepreneurship in the curriculum, through flexible curriculum pathways, across disciplines. In the spirit of the UCDP, the intention is for entrepreneurship in the curriculum to be relevant to the local context in order to support the ability of graduates to participate meaningfully in society and in the world of work.

In essence, student development, staff development and curriculum development are interlinked in terms of the EDHE Programme. High quality entrepreneurship programmes would benefit both students and staff. Better equipped and skilled staff would contribute to student entrepreneurship and more relevant academic programmes and research. Similarly, as students expand their entrepreneurship skillsets, staff would be motivated to invest in their own development and academic programmes.

The EDHE Programme spans across institutional and regional boundaries. It is aimed at addressing entrepreneurship development needs at individual institutions, while drawing together regional resources and efforts to the benefit of stakeholders on a national level.

This is a large and complex endeavour that requires multiple focus areas and much time. The good news is that most universities are already taking ownership of the problem and implementing initiatives. Add to this the fact that universities have access to their own resources, and it is clear that the main role the EDHE Programme is to provide strategic focus, create enabling environments, collaboration, partnering and unlocking what is existing.
EDHE
                        ,
                        'goals' => array(
                            'Student entrepreneurship: mobilising the national student and graduate resource to create successful enterprises that will ultimately lead to both wealth and job creation',
                            'Entrepreneurship development in academia: support academics in instilling an entrepreneurial mindset within all students and graduates through the offering of relevant knowledge, transferral of practical skills and the application of business principles, not only to a specific discipline, but across disciplines',
                            'Developing entrepreneurial universities: creating a conducive environment that will enable universities to adapt strategically and embark on projects whereby third-stream income can be generated through innovative business ideas',
                        ),
                        'benefits' => array(
                            'Develop entrepreneurial capacity of students, academics and leaders',
                            'Enable students to generate additional income during their studies',
                            'Fast-track the process of becoming economically active',
                            'Consider entrepreneurship as a career option upon graduation',
                            'Access to entrepreneurship education and training across disciplines',
                            'Connect with a national network of entrepreneurship stakeholders',
                        ),
                        'subPrograms' => array(
                            array(
                                'id' => 'student-indaba',
                                'name' => 'Student Indaba',
                                'description' => 'Annual gathering for students to showcase innovative ideas',
                                'fullDetails' => 'A platform for students to present their innovative projects and business ideas to a panel of judges, industry experts, and potential investors.',
                                'fullDescription' => <<<'INDABA'
The Student Indaba is a premier annual event organized by Entrepreneurship Development in Higher Education (EDHE) that brings together student entrepreneurs from universities across South Africa.

Key Features:
• Student entrepreneurs showcase their innovative business ideas
• Pitch competitions with cash prizes
• Networking opportunities with industry experts and potential investors
• Workshops and masterclasses on entrepreneurship skills
• Exhibition space for student businesses to display their products and services

The Indaba serves as a platform for students to gain exposure, receive feedback from experts, and connect with like-minded entrepreneurs from other institutions.
INDABA
                                ,
                                'benefits' => array(
                                    'Showcase your business idea to a national audience',
                                    'Network with student entrepreneurs from other universities',
                                    'Learn from successful entrepreneurs and industry experts',
                                    'Opportunity to win cash prizes and recognition',
                                    'Access to mentorship and funding opportunities',
                                ),
                                'eligibility' => array(
                                    'Currently enrolled university students',
                                    'Open to all disciplines and faculties',
                                    'Must have a business idea or existing startup',
                                ),
                                'contact' => 'indaba@edhe.co.za',
                                'deadline' => 'September 2025',
                            ),
                        ),
                        'contactInfo' => array(
                            'website' => 'https://edhe.co.za/',
                            'contact' => 'https://edhe.co.za/contact/',
                            'social' => array(
                                'facebook' => 'https://www.facebook.com/EDHEentrepreneurship',
                                'twitter' => 'https://x.com/EDHEOffical',
                                'instagram' => 'https://www.instagram.com/edheentrepreneurship/',
                                'linkedin' => 'https://www.linkedin.com/company/entrepreneurship-development-in-higher-education-edhe/?viewAsMember=true',
                                'sweepLinkedin' => 'https://www.linkedin.com/showcase/student-women-economic-empowerment-programme-sweep/?viewAsMember=true',
                                'youtube' => 'https://www.youtube.com/@entrepreneurshipdevelopmen6394',
                            ),
                        ),
                        'social' => array(
                            'facebook' => 'https://www.facebook.com/EDHEentrepreneurship',
                            'twitter' => 'https://x.com/EDHEOffical',
                            'instagram' => 'https://www.instagram.com/edheentrepreneurship/',
                            'linkedin' => 'https://www.linkedin.com/company/entrepreneurship-development-in-higher-education-edhe/?viewAsMember=true',
                            'youtube' => 'https://www.youtube.com/@entrepreneurshipdevelopmen6394',
                        ),
                        'contact' => 'info@edhe.co.za',
                        'externalLink' => 'https://edhe.co.za/',
                        'hasDropdown' => true,
                        'dropdownTitle' => 'CFERI Student Chapters',
                        'dropdownColor' => '#ff9d00',
                    ),
                ),
            ),
            'Research Activities' => array(
                'id' => 'research-activities',
                'color' => '#ff9d00',
                'description' => 'Cutting-edge research in entrepreneurship and innovation',
                'programs' => array(
                    array(
                        'id' => 'entrepreneurship-research',
                        'name' => 'Research in Entrepreneurship Development',
                        'description' => 'An emerging hub for high-impact research in entrepreneurship, innovation, and inclusive economic development.',
                        'fullDetails' => 'The University of Mpumalanga Centre for Entrepreneurship Rapid Incubator (CFERI) is an emerging hub for high-impact research in entrepreneurship, innovation, and inclusive economic development. Within a short period, the Centre has demonstrated rapid growth in research output, with increasing publications, a strong pipeline of ongoing studies, and international recognition through conference participation and awards.\n\nCFERI’s research focuses on delivering practical, evidence-based insights to address key development challenges, including unemployment, small-business sustainability, and economic transformation. By integrating entrepreneurship with economic and innovation analysis, the Centre produces research that is both academically rigorous and directly relevant to policy and practice.',
                        'benefits' => array('Research grants','Publications','Conference presentations','Policy influence'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'consultancy',
                        'name' => 'Research Consultancy',
                        'description' => 'Tailored research consultancy services for government, private sector organisations, and development partners.',
                        'fullDetails' => 'CFERI provides research consultancy services tailored to the needs of government, private sector organisations, and development partners. The Centre specialises in policy analysis, programme evaluation, and impact assessment across entrepreneurial ecosystems, SME development, and innovation-led growth.\n\nWith a strong interdisciplinary foundation, CFERI delivers data-driven, context-specific solutions that support strategic decision-making and measurable development outcomes.',
                        'benefits' => array('Expert advice','Custom solutions','Industry insights','Strategic planning'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'advocacy',
                        'name' => 'Policy Advocacy',
                        'description' => 'Contributing to policy development through research-driven advocacy.',
                        'fullDetails' => 'CFERI actively contributes to policy development through research-driven advocacy. The Centre works with stakeholders to shape policies that strengthen entrepreneurial ecosystems, support emerging enterprises, and promote inclusive economic growth.\n\nIts policy engagement is grounded in empirical research and aligned with national and regional development priorities.',
                        'benefits' => array('Policy development','Stakeholder engagement','Inclusive growth','Ecosystem strengthening'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'publications',
                        'name' => 'Research Publications',
                        'description' => 'Growing portfolio of peer-reviewed publications and conference outputs.',
                        'fullDetails' => 'CFERI has a growing portfolio of peer-reviewed publications and conference outputs that contribute to academic scholarship and inform policy discussions. The Centre’s work continues to gain visibility both locally and internationally, reinforcing its position as a credible research partner in entrepreneurship and development.',
                        'benefits' => array('Academic recognition','Knowledge sharing','Industry impact','Author opportunities'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                    array(
                        'id' => 'partner-cferi',
                        'name' => 'Partner with CFERI',
                        'description' => 'Collaboration opportunities with government institutions, industry partners, and development agencies.',
                        'fullDetails' => 'CFERI welcomes collaboration with government institutions, industry partners, development agencies, and academic institutions. Through research, consultancy, and policy engagement, the Centre offers a platform for generating knowledge, shaping policy, and driving sustainable economic impact.',
                        'benefits' => array('Generating knowledge','Shaping policy','Sustainable impact','Strategic collaboration'),
                        'contact' => 'cferi_bdo@ump.ac.za',
                    ),
                ),
            ),
        );

        

        $category_settings = array(
            'Incubation Programs' => array(
                'id' => 'incubation-programs',
                'color' => '#5b60ae',
                'icon' => '<i class="fas fa-rocket"></i>',
                'description' => __( 'Comprehensive support for startups at every stage', 'umpcferi-plugin' ),
            ),
            'Entrepreneurship Development Programs' => array(
                'id' => 'student-funding-competitions',
                'color' => '#cd3131',
                'icon' => '<i class="fas fa-lightbulb"></i>',
                'description' => __( 'Opportunities to develop and grow entrepreneurial skills', 'umpcferi-plugin' ),
            ),
            'Research Activities' => array(
                'id' => 'research-activities',
                'color' => '#ff9d00',
                'icon' => '<i class="fas fa-flask"></i>',
                'description' => __( 'Cutting-edge research in entrepreneurship and innovation', 'umpcferi-plugin' ),
            ),
            'Student Chapters' => array(
                'id' => 'student-chapters',
                'color' => '#1d9a73',
                'icon' => '<i class="fas fa-users"></i>',
                'description' => __( 'Student-led chapters and communities supporting student entrepreneurs', 'umpcferi-plugin' ),
            ),
        );

        // Merge static sections into grouped_programs so they are rendered alongside CPT items.
        foreach ( $static_program_sections as $title => $section ) {
            $grouped_programs[ $title ] = isset( $grouped_programs[ $title ] ) ? array_merge( $grouped_programs[ $title ], $section['programs'] ) : $section['programs'];
            // ensure category settings include these entries so color/descriptions are available
            if ( ! isset( $category_settings[ $title ] ) ) {
                $category_settings[ $title ] = array(
                    'id' => $section['id'],
                    'color' => $section['color'] ?? '#4256a6',
                    'icon' => '',
                    'description' => $section['description'] ?? '',
                );
            }
        }

        $output  = '<div class="programs-page">';
        $output .= '<section class="programs-hero">';
        $output .= '<div class="programs-hero-overlay"></div>';
        $output .= '<div class="programs-hero-content">';
        $output .= '<h1 class="programs-hero-title">' . esc_html__( 'Our Programs', 'umpcferi-plugin' ) . '</h1>';
        $output .= '<p class="programs-hero-subtitle">' . esc_html__( 'Discover comprehensive programs designed to nurture entrepreneurs at every stage of their journey.', 'umpcferi-plugin' ) . '</p>';
        $output .= '</div></section>';

        $output .= '<div class="programs-quick-nav">';
        $output .= '<div class="container"><div class="quick-nav-links">';
        foreach ( $grouped_programs as $category => $items ) {
            $section_id = sanitize_title( $category );
            $meta = isset( $category_settings[ $category ] ) ? $category_settings[ $category ] : array(
                'id' => $section_id,
                'color' => '#4256a6',
                'icon' => '<i class="fas fa-layer-group"></i>',
                'description' => __( 'Browse our programs', 'umpcferi-plugin' ),
            );
            $output .= '<button type="button" class="quick-nav-btn" data-target="' . esc_attr( $meta['id'] ) . '" style="--hover-color:' . esc_attr( $meta['color'] ) . ';">';
            $output .= '<span>' . esc_html( $category ) . '</span>';
            $output .= '</button>';
        }
        $output .= '</div></div></div>';

        $output .= '<div class="programs-container">';
        foreach ( $grouped_programs as $category => $items ) {
            $section_id = sanitize_title( $category );
            $meta = isset( $category_settings[ $category ] ) ? $category_settings[ $category ] : array(
                'id' => $section_id,
                'color' => '#4256a6',
                'icon' => '<i class="fas fa-layer-group"></i>',
                'description' => __( 'Browse our programs', 'umpcferi-plugin' ),
            );

            $output .= '<section class="program-category-section" id="' . esc_attr( $meta['id'] ) . '">';
            $output .= '<div class="category-header">';
            $output .= '<div class="category-title-wrapper">';
            $output .= '<h2 class="category-title">' . esc_html( $category ) . '</h2>';
            $output .= '<p class="category-description">' . esc_html( $meta['description'] ) . '</p>';
            $output .= '</div></div>';
            $output .= '<div class="category-programs-grid">';

            foreach ( $items as $index => $program ) {
                // Support both WP post objects (from DB) and static arrays (from the provided data).
                if ( is_array( $program ) && isset( $program['name'] ) ) {
                    $program_data = array(
                        'title' => $program['name'],
                        'description' => isset( $program['description'] ) ? $program['description'] : '',
                        'fullDetails' => isset( $program['fullDetails'] ) ? $program['fullDetails'] : '',
                        'fullDescription' => isset( $program['fullDescription'] ) ? $program['fullDescription'] : '',
                        'benefits' => isset( $program['benefits'] ) ? $program['benefits'] : array(),
                        'goals' => isset( $program['goals'] ) ? $program['goals'] : array(),
                        'eligibility' => isset( $program['eligibility'] ) ? $program['eligibility'] : array(),
                        'partners' => isset( $program['partners'] ) ? $program['partners'] : array(),
                        'contact' => isset( $program['contact'] ) ? $program['contact'] : '',
                        'externalLink' => isset( $program['externalLink'] ) ? $program['externalLink'] : '',
                        'social' => isset( $program['social'] ) ? $program['social'] : ( isset( $program['social_links'] ) ? $program['social_links'] : array() ),
                        'social_links' => isset( $program['social_links'] ) ? $program['social_links'] : array(),
                        'contactInfo' => isset( $program['contactInfo'] ) ? $program['contactInfo'] : array(),
                        'subPrograms' => isset( $program['subPrograms'] ) ? $program['subPrograms'] : array(),
                        'hasDropdown' => isset( $program['hasDropdown'] ) ? $program['hasDropdown'] : false,
                        'dropdownTitle' => isset( $program['dropdownTitle'] ) ? $program['dropdownTitle'] : '',
                        'dropdownColor' => isset( $program['dropdownColor'] ) ? $program['dropdownColor'] : '',
                    );
                    $display_title = $program_data['title'];
                    $summary = $program_data['description'];
                } else {
                    $summary = get_post_meta( $program->ID, 'umpcferi_program_description', true );
                    if ( empty( $summary ) ) {
                        $summary = wp_trim_words( $program->post_content, 24, '...' );
                    }
                    $full_details = get_post_meta( $program->ID, 'umpcferi_program_full_details', true );
                    $full_description = get_post_meta( $program->ID, 'umpcferi_program_full_description', true );
                    $benefits = get_post_meta( $program->ID, 'umpcferi_program_benefits', true );
                    $goals = get_post_meta( $program->ID, 'umpcferi_program_goals', true );
                    $eligibility = get_post_meta( $program->ID, 'umpcferi_program_eligibility', true );
                    $partners = get_post_meta( $program->ID, 'umpcferi_program_partners', true );
                    $contact = get_post_meta( $program->ID, 'umpcferi_program_contact', true );
                    $external_link = get_post_meta( $program->ID, 'umpcferi_program_external_link', true );

                    $benefit_items = is_array( $benefits ) ? $benefits : ( ! empty( $benefits ) ? array_filter( explode( PHP_EOL, $benefits ) ) : array() );
                    $goal_items = is_array( $goals ) ? $goals : ( ! empty( $goals ) ? array_filter( explode( PHP_EOL, $goals ) ) : array() );
                    $eligibility_items = is_array( $eligibility ) ? $eligibility : ( ! empty( $eligibility ) ? array_filter( explode( PHP_EOL, $eligibility ) ) : array() );
                    $partner_items = is_array( $partners ) ? $partners : ( ! empty( $partners ) ? array_filter( explode( PHP_EOL, $partners ) ) : array() );

                    $program_data = array(
                        'title' => $program->post_title,
                        'description' => $summary,
                        'fullDetails' => $full_details,
                        'fullDescription' => $full_description,
                        'benefits' => $benefit_items,
                        'goals' => $goal_items,
                        'eligibility' => $eligibility_items,
                        'partners' => $partner_items,
                        'contact' => $contact,
                        'externalLink' => $external_link,
                        'social' => get_post_meta( $program->ID, 'umpcferi_program_social_links', true ) ?: array(),
                    );
                    $display_title = $program->post_title;
                }

                $output .= '<div class="category-program-card" data-program="' . esc_attr( wp_json_encode( $program_data ) ) . '" style="animation-delay:' . esc_attr( $index * 0.08 ) . 's;">';
                $output .= '<div class="program-card-header" style="background-color:' . esc_attr( $meta['color'] ) . ';"></div>';
                $output .= '<div class="program-card-body">';
                $output .= '<h3>' . esc_html( $display_title ) . '</h3>';
                $output .= '<p class="program-description">' . esc_html( $summary ) . '</p>';
                $output .= '<div class="program-card-footer">';
                $output .= '<button type="button" class="learn-more-btn">' . esc_html__( 'Learn More', 'umpcferi-plugin' ) . '</button>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
            }

            $output .= '</div></section>';
        }

        $output .= '<div class="program-modal" aria-hidden="true">';
        $output .= '<div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="program-modal-title">';
        $output .= '<button type="button" class="modal-close" data-close-modal>&times;</button>';
        $output .= '<div class="modal-header">';
        $output .= '<div class="modal-icon">📋</div>';
        $output .= '<div class="modal-title">';
        $output .= '<h2 id="program-modal-title"></h2>';
        $output .= '<p class="modal-subtitle"></p>';
        $output .= '</div></div>';
        $output .= '<div class="modal-body">';
        $output .= '<div class="modal-section">';
        $output .= '<h3>' . esc_html__( 'Program Details', 'umpcferi-plugin' ) . '</h3>';
        $output .= '<p class="modal-details"></p>';
        $output .= '</div>';
        $output .= '<div class="modal-section modal-extra"></div>';
        $output .= '<div class="modal-section modal-contact"></div>';
        $output .= '</div></div>';
        $output .= '<div class="program-modal-overlay" data-close-modal></div>';
        $output .= '</div>';

        $output .= '</div>';
        return $output;
    }
}
}

function umpcferi_plugin_activate() {
    global $wpdb;
    $plugin = new Umpcferi_Plugin();
    $plugin->create_sample_content();

    // Ensure the main site pages exist with the expected slugs before flushing rules.
    $plugin->create_sample_page( 'Home', '[umpcferi_home]', 'home', 0, true );
    $plugin->create_sample_page( 'About', '[umpcferi_about]', 'about', 1, true );
    $plugin->create_sample_page( 'Programs', '[umpcferi_programs]', 'programs', 2, true );
    $plugin->create_sample_page( 'Newsletter', '[umpcferi_newsletter_page]', 'newsletter', 3, true );
    $plugin->create_sample_page( 'Contact Us', '[umpcferi_contact]', 'contact-us', 4, true );
    $plugin->create_sample_page( 'UMP CFERI App', '[umpcferi_app]', 'umpcferi-app', 5, true );
    $plugin->create_sample_page( 'Events', '[umpcferi_events]', 'events', 6, true );

    // Clean up unwanted default WordPress pages and duplicates
    $pages_to_remove = array( 'sample-page', 'site' );
    foreach ( $pages_to_remove as $slug ) {
        $page = get_page_by_path( $slug, OBJECT, 'page' );
        if ( $page ) {
            wp_delete_post( $page->ID, true );
        }
    }

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
