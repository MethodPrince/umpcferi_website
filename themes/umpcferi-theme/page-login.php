<?php
/*
Template Name: Login Page
*/
get_header(); ?>

<section class="about-page">
    <h1><?php the_title(); ?></h1>
    <div class="login-form">
        <form method="post" action="<?php echo esc_url( wp_login_url() ); ?>">
            <p>
                <label for="user_login">Username or Email</label><br>
                <input type="text" name="log" id="user_login" required>
            </p>
            <p>
                <label for="user_pass">Password</label><br>
                <input type="password" name="pwd" id="user_pass" required>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="rememberme" value="forever"> Remember Me
                </label>
            </p>
            <p>
                <input type="submit" value="Login">
            </p>
        </form>
        <p><a href="<?php echo esc_url( wp_registration_url() ); ?>">Register</a> | <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Lost your password?</a></p>
    </div>
</section>

<?php get_footer(); ?>