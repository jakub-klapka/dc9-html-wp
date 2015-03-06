<?php

namespace DC9\Models;

require_once( 'MailChimpAPI.php' );

class Subscriber extends MailChimpAPI {

	public function addNewSubscriber( $email ) {

		$list_id = ( icl_get_current_language() === 'en' ) ? DC9_MC_EN_LIST : DC9_MC_CZ_LIST;

		$response = $this->apiRequest( 'lists/subscribe', array(
			'id' => $list_id,
			'email[email]' => $email
		) );

		if( isset( $response->email ) && $response->email === $email ) return true;

		if( isset( $response->error ) ) return $response->error;

	}

}