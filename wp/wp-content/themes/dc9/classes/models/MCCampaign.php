<?php

namespace DC9\Models;

require_once( 'MailChimpAPI.php' );

class MCCampaign extends MailChimpAPI {

	private $atts;

	public function __construct( $atts ) {

		$this->atts = $atts;

	}

	public function send() {

		$campaign_id = $this->create_campaign();
		if( $campaign_id === false ) return;

		$this->sendCampaign( $campaign_id );

	}

	private function create_campaign() {

		$request = $this->apiRequest( 'campaigns/create', array(
			'type' => 'regular',
			'options[template_id]' => ( icl_get_current_language() === 'en' ) ? DC9_MC_EN_NEWS_TEMPLATE : DC9_MC_CZ_NEWS_TEMPLATE,
			'options[list_id]' => ( icl_get_current_language() === 'en' ) ? DC9_MC_EN_LIST : DC9_MC_CZ_LIST,
			'options[subject]' => $this->atts[ 'mail_subject' ],
			'options[from_email]' => 'petr.bily@dc-9.eu',
			'options[from_name]' => 'Petr Bílý',
			'content[sections][content]' => $this->generateContent()
		) );

		return ( isset( $request->id ) ) ? $request->id : false;

	}

	private function generateContent() {

		$read_more_text = ( icl_get_current_language() === 'en' ) ? 'Read more on www.dc-9.eu' : 'Čtěte dále na www.dc-9.eu';

		return sprintf( '<h2><a href="%s" target="_blank">%s</a></h2><p>%s</p><p><a href="%s" target="_blank">' . $read_more_text . '</a></p>',
			$this->atts[ 'url' ], $this->atts[ 'title' ], $this->atts[ 'content' ], $this->atts[ 'url' ] );

	}

	private function sendCampaign( $campaign_id ) {

		$this->apiRequest( 'campaigns/send', array(
			'cid' => $campaign_id
		) );

	}

}