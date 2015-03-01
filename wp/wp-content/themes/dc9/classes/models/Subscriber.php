<?php

namespace DC9\Models;


class Subscriber {

	public function addNewSubscriber( $email ) {

		$list_id = ( icl_get_current_language() === 'en' ) ? DC9_MC_EN_LIST : DC9_MC_CZ_LIST;

		$response = $this->apiRequest( 'lists/subscribe', array(
			'id' => $list_id,
			'email[email]' => $email
		) );

		if( isset( $response->email ) && $response->email === $email ) return true;

		if( isset( $response->error ) ) return $response->error;

	}

	private function apiRequest( $endpoint, $data = array() ) {

		$url = "https://" . DC9_MC_DC . ".api.mailchimp.com/2.0/{$endpoint}.json";

		$data = array_merge( array(
			'apikey' => DC9_MC_API_KEY
		), $data );

		$ch = curl_init();
		curl_setopt_array( $ch, array(
			CURLOPT_HEADER => false,
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_URL => $url,
			CURLOPT_POSTFIELDS => $data
		) );

		$output = curl_exec( $ch );

		return ( $output === false ) ? false : json_decode( $output );
	}

}