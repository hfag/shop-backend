<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php //do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_login ) ); ?></p>
<?php /* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */ ?>
<p>
	Vielen Dank für die Erstellung eines Kontos im Shop der Hauser Feuerschutz AG. Ihr Benutzername lautet <?php echo esc_html( $user_login ) ?>.
	Unter folgendem Link können Sie auf Ihr Konto zugreifen, um Bestellungen anzuzeigen, Ihr Passwort zu ändern usw.: <a href="https://shop.feuerschutz.ch/konto/">https://shop.feuerschutz.ch/konto/</a>
</p>


<p>Wir freuen uns darauf, bald von Ihnen zu hören.</p>

<?php
//do_action( 'woocommerce_email_footer', $email );
