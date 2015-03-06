<?php

namespace DC9\Models;


class MailChimpAPI {

	protected function apiRequest( $endpoint, $data = array() ) {

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