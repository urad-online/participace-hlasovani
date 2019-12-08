<?php

/**
* 20.02
* Add a check box with the terms of the login and registration form
*
*/

// souhlas s podmínkami při registraci uživatele nebo při jeho přihlášení
// nesouhlasím-li, nejsou dostupná tlačítka připojení pomocí sociálních služeb (plugin Nextend facebook connect)

// As part of WP authentication process, call our function
function pb_enqueue_user_reg_scripts(){
    // <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    wp_enqueue_script( 'jquery' );
    wp_print_scripts();
}
 // add_action( 'wp_enqueue_scripts', 'pb_enqueue_user_reg_scripts',1);

add_filter('wp_authenticate_user', 'wp_authenticate_user_acc', 99999, 2);

function wp_authenticate_user_acc($user, $password) {
   // See if the checkbox #login_accept was checked
   if ( isset( $_REQUEST['login_accept'] ) && $_REQUEST['login_accept'] == 'on' ) {
       // Checkbox on, allow login
       return $user;
   } else {
       // Did NOT check the box, do not allow login
       $error = new WP_Error();
       $error->add('did_not_accept', 'Musíte akceptovat naše podmínky užití a podmínky ochrany osobních údajů' );
       return $error;
   }
}

// As part of WP login form construction, call our function
add_filter ( 'login_form', 'login_form_acc');

function login_form_acc(){
   // Add an element to the login form, which must be checked
    pb_enqueue_user_reg_scripts();

   echo '<label><input type="checkbox" name="login_accept" id="login_accept" checked /> Souhlasím s <a href="podminky-pouziti-a-ochrana-osobnich-udaju/" target="_blank" rel="noopener">podmínkami použití</a></label>';

     ?>
    <script>

    jQuery('#login_accept').on('change', function () {

        if (jQuery('#login_accept').is(':checked') ) {

            jQuery('#nsl-custom-login-form-main').css( { display: 'block'});

        } else {

            jQuery('#nsl-custom-login-form-main').css({ display: 'none'});
        }
    });



   </script>
   <?php

}

// As part of WP authentication process, call our function
add_filter('registration_errors', 'user_register_acc', 99999, 2);

function user_register_acc($user, $password) {
   // See if the checkbox #login_accept was checked
   if ( isset( $_REQUEST['login_accept'] ) && $_REQUEST['login_accept'] == 'on' ) {
       // Checkbox on, allow login
       return $user;
   } else {
       // Did NOT check the box, do not allow login
       $error = new WP_Error();
       $error->add('did_not_accept', 'Musíte akceptovat naše podmínky užití a podmínky ochrany osobních údajů' );
       return $error;
   }
}

// As part of WP login form construction, call our function
add_filter ( 'register_form', 'user_register_form_acc');

function user_register_form_acc(){
   // Add an element to the login form, which must be checked
    pb_enqueue_user_reg_scripts();
    echo '<label><input type="checkbox" name="login_accept" id="login_accept" checked /> Souhlasím s <a href="podminky-pouziti-a-ochrana-osobnich-udaju/" target="_blank" rel="noopener">podmínkami použití</a></label>';

        ?>
    <script>

    jQuery('#login_accept').on('change', function () {

        if (jQuery('#login_accept').is(':checked') ) {

            jQuery('#nsl-custom-login-form-main').css( { display: 'block'});

        } else {

            jQuery('#nsl-custom-login-form-main').css({ display: 'none'});
        }
    });



   </script>
   <?php



}
