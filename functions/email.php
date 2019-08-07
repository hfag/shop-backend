<?php
	
	class Hfag_Email {
		
		public function __construct(){
			add_filter("password_change_email", array($this, 'password_change_email'), 10, 3);
			add_filter("email_change_email", array($this, 'email_change_email'), 10, 3);
		}
		
		public function password_change_email($pass_change_email, $user, $userdata){
	
			return array(
				'to'      => $pass_change_email["to"],
				/* translators: User email change notification email subject. %s: Site name */
				'subject' => $pass_change_email["subject"],
				/* translators: Do not translate USERNAME, ADMIN_EMAIL, EMAIL, SITENAME, SITEURL: those are placeholders. */
				'message' => __(
'Hi ###USERNAME###,

This notice confirms that your password was changed.

If you did not change your password, please contact us at info@feuerschutz.ch.

This email has been sent to ###EMAIL###.

Regards,
All at Hauser Feuerschutz AG
https://shop.feuerschutz.ch',
				'b4st'
				),
				'headers' => $pass_change_email["headers"],
			);
		}
		
		public function email_change_email($email_change_email, $user, $userdata){
			return array(
				'to'      => $email_change_email["to"],
				/* translators: User email change notification email subject. %s: Site name */
				'subject' => $email_change_email["subject"],
				/* translators: Do not translate USERNAME, ADMIN_EMAIL, EMAIL, NEW_EMAIL, SITENAME, SITEURL: those are placeholders. */
				'message' => __(
'Hi ###USERNAME###,

This notice confirms that your email address was changed to ###NEW_EMAIL###.

If you did not change your email, please contact us at info@feuerschutz.ch.

This email has been sent to ###EMAIL###.

Regards,
All at Hauser Feuerschutz AG
https://shop.feuerschutz.ch',
				'b4st'
				),
				'headers' => $email_change_email["headers"],
			);
		}
	}
	
	new Hfag_Email();
	
?>