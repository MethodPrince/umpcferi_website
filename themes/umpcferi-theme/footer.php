</main>
<footer class="site-footer">
    <div class="site-footer-grid">
        <div class="site-footer-column">
            <h3>UMPCFERI</h3>
            <p class="site-footer-tagline">Creating Opportunities</p>
            <p>Cnr R40 &amp; D725 Road<br>
            University of Mpumalanga<br>
            Old building 12, Mbombela<br>
            South Africa</p>
        </div>

        <div class="site-footer-column">
            <h4>Connect</h4>
            <ul class="site-footer-links">
                <li><a href="https://www.linkedin.com/in/ump-centre-for-entrepreneurship-0256bb282" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
                <li><a href="https://www.instagram.com/umpcferi" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                <li><a href="https://www.facebook.com/share/1DNv3KxjVb/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer">Facebook</a></li>
            </ul>
        </div>

        <div class="site-footer-column">
            <h4>Contact</h4>
            <ul class="site-footer-links">
                <li><a href="mailto:cferi_bdo@ump.ac.za">cferi_bdo@ump.ac.za</a></li>
                <li>Mon-Fri: 08:00-16:00</li>
            </ul>
        </div>
    </div>

    <div class="site-sponsors-section">
        <p class="site-sponsors-heading">Our Partners</p>
        <div class="site-sponsors-marquee">
            <div class="site-sponsors-track">
                <?php
                $sponsor_images = array(
                    '/Images/cferiSponsors/0e090f32-2706-4175-b054-469b2fceaa67.jpeg',
                    '/Images/cferiSponsors/2024cffd-85a1-4da4-a265-af4b22ee1005.jpeg',
                    '/Images/cferiSponsors/8f0645f6-ab81-470c-9599-048f164def1f.jpeg',
                    '/Images/cferiSponsors/afad9013-9a2a-41b4-85af-19a166992843.jpeg',
                    '/Images/cferiSponsors/f08c823c-6faa-4519-8b9b-b63336d3e7c1.jpeg',
                    '/Images/cferiSponsors/Image.jpeg'
                );
                $sponsor_images_loop = array_merge( $sponsor_images, $sponsor_images );
                foreach ( $sponsor_images_loop as $image ) {
                    echo '<div class="site-sponsor-card"><img src="' . esc_url( get_template_directory_uri() . $image ) . '" alt="Sponsor logo"></div>';
                }
                ?>
            </div>
        </div>
    </div>
    <p class="site-footer-copy">&copy; 2024 UMP CFERI. All rights reserved.</p>
</footer>
<?php wp_footer(); ?>
</body>
</html>
