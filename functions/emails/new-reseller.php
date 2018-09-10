<?php
	
class WC_Reseller_Added extends WC_Email {
	
	public function __construct() {
		
		$this->id				= 'wc_reseller_added';
		$this->title			= __('New Reseller Added', 'b4st');
		$this->description		= __('"New Reseller Added" mails are being sent to the new resellers added through the excel import.', 'b4st');
		
		$this->heading			= __('Welcome to our new shop', 'b4st');
		$this->subject			= __('New Online Shop', 'b4st');
		
		$this->template_html    = 'emails/reseller-added.php';
		$this->template_plain   = 'emails/plain/reseller-added.php';
		
		add_action( 'feuerschutz_new_reseller_imported', array( $this, 'trigger' ), 10, 2);
		
		parent::__construct();
	}
	
	
	public function trigger($user_id, $reset_key) {
		
		if (!$user_id || !$reset_key){
			return;
		}
		
		$this->object		= get_userdata($user_id);
		
		$this->user_login	= $this->object->user_login;
		$this->reset_key	= $reset_key;
		$this->user_email	= stripslashes($this->object->user_email);
		$this->recipient 	= $this->user_email;
		
		/*$this->find[] = '{order_date}';
		$this->replace[] = date_i18n( woocommerce_date_format(), strtotime( $this->object->order_date ) );*/
		
		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}
		
		$this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
	}
	
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'email_heading'	=> $this->get_heading(),
			'user_login'    => $this->user_login,
			'user'			=> $this->object,
			'reset_key'		=> $this->reset_key,
			'blogname'		=> $this->get_blogname(),
			'sent_to_admin'	=> false,
			'plain_text'	=> false,
			'email'			=> $this
		));
	}
	
	public function get_content_plain() {
		return wc_get_template_html( $this->template_html, array(
			'email_heading'	=> $this->get_heading(),
			'user_login'    => $this->user_login,
			'user'			=> $this->object,
			'reset_key'		=> $this->reset_key,
			'blogname'		=> $this->get_blogname(),
			'sent_to_admin'	=> false,
			'plain_text'	=> true,
			'email'			=> $this
		));
	}

}
?>