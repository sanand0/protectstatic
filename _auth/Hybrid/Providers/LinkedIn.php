<?php
/**
* HybridAuth
* 
* A Social-Sign-On PHP Library for authentication through identity providers like Facebook,
* Twitter, Google, Yahoo, LinkedIn, MySpace, Windows Live, Tumblr, Friendster, OpenID, PayPal,
* Vimeo, Foursquare, AOL, Gowalla, and others.
*
* Copyright (c) 2009-2011 (http://hybridauth.sourceforge.net) 
*/ 

/**
 * Hybrid_Providers_LinkedIn class, wrapper for LinkedIn  
 */
class Hybrid_Providers_LinkedIn extends Hybrid_Provider_Model
{ 
	/**
	* IDp wrappers initializer 
	*/
	function initialize() 
	{
		if ( ! $this->config["keys"]["key"] || ! $this->config["keys"]["secret"] )
		{
			throw new Exception( "Your application key and secret are required in order to connect to {$this->providerId}.", 4 );
		} 

		require_once Hybrid_Auth::$config["path_libraries"] . "OAuth/OAuth.php";
		require_once Hybrid_Auth::$config["path_libraries"] . "LinkedIn/LinkedIn.php"; 
 
		$this->api = new LinkedIn( array( 'appKey' => $this->config["keys"]["key"], 'appSecret' => $this->config["keys"]["secret"], 'callbackUrl' => $this->endpoint ) ); 

		if( $this->token( "access_token" ) )
		{
			$this->api->setTokenAccess( $this->token( "access_token" ) );
		}
	}

   /**
	* begin login step 
	*/
	function loginBegin()
	{
        // send a request for a LinkedIn access token
        $response = $this->api->retrieveTokenRequest();

        if( isset( $response['success'] ) && $response['success'] === TRUE ) 
		{
			$this->token( "oauth_token",        $response['linkedin']['oauth_token'] ); 
			$this->token( "oauth_token_secret", $response['linkedin']['oauth_token_secret'] ); 

			# redirect user to LinkedIn authorisation web page
			Hybrid_Auth::redirect( LINKEDIN::_URL_AUTH . $response['linkedin']['oauth_token'] );
        }
		else 
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Token.", 5 );
        }
	}

   /**
	* finish login step 
	*/
	function loginFinish()
	{
		$oauth_token    = @ $_REQUEST['oauth_token'];
		$oauth_verifier = @ $_REQUEST['oauth_verifier'];

		if ( ! $oauth_verifier )
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Token.", 5 );
		}

		$response = $this->api->retrieveTokenAccess( $oauth_token, $this->token( "oauth_token_secret" ), $oauth_verifier );

        if( isset( $response['success'] ) && $response['success'] === TRUE ) 
		{
			$this->token( "access_token", $response['linkedin'] ); 

			// set user as logged in
			$this->setUserConnected();
        }
		else 
		{
			throw new Exception( "Authentification failed! {$this->providerId} returned an invalid Token.", 5 );
        } 
	}

	/**
	* load the user profile from the IDp api client
	*/
	function getUserProfile()
	{
		try{
			// http://developer.linkedin.com/docs/DOC-1061
			$response = $this->api->profile('~:(id,first-name,last-name,public-profile-url,picture-url,date-of-birth,phone-numbers,summary)');
		}
		catch( LinkedInException $e ){
			throw new Exception( "User profile request failed! {$this->providerId} returned an error: $e", 6 );
		}

		if( isset( $response['success'] ) && $response['success'] === TRUE ) 
		{
			$data = @ new SimpleXMLElement( $response['linkedin'] ); 

			if ( ! is_object( $data ) )
			{
				throw new Exception( "User profile request failed! {$this->providerId} returned an invalide xml data.", 6 );
			}  

			$this->user->profile->identifier    = @ (string) $data->{'id'};
			$this->user->profile->firstName  	= @ (string) $data->{'first-name'};
			$this->user->profile->lastName  	= @ (string) $data->{'last-name'}; 
			$this->user->profile->displayName  	= trim( $this->user->profile->firstName . " " . $this->user->profile->lastName );

			$this->user->profile->photoURL  	= @ (string) $data->{'picture-url'}; 
			$this->user->profile->profileURL    = @ (string) $data->{'public-profile-url'}; 
			$this->user->profile->description   = @ (string) $data->{'summary'};  

			$this->user->profile->phone         = @ (string) $data->{'phone-numbers'}->{'phone-number'}->{'phone-number'};  

			if( $data->{'date-of-birth'} ) { 
				$this->user->profile->birthDay      = @ (string) $data->{'date-of-birth'}->day;  
				$this->user->profile->birthMonth    = @ (string) $data->{'date-of-birth'}->month;  
				$this->user->profile->birthYear     = @ (string) $data->{'date-of-birth'}->year;  
			} 

			return $this->user->profile;
		}
		else 
		{
			throw new Exception( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
        } 
	}

   /**
	* load the user contacts
	*/
	function getUserContacts()
	{
		try{ 
			$response = $this->api->profile('~/connections:(id,first-name,last-name,picture-url,public-profile-url,summary)');
		}
		catch( LinkedInException $e ){
			throw new Exception( "User contacts request failed! {$this->providerId} returned an error: $e" );
		}

		if( ! $response || ! $response['success'] ){
			return ARRAY();
		}

		$connections = new SimpleXMLElement( $response['linkedin'] ); 
		
		$contacts = ARRAY();

		foreach( $connections->person as $connection ) {
			$uc = new Hybrid_User_Contact();

			$uc->identifier   = @ $connection->id;
			$uc->displayName  = @ $connection->{'last-name'} . " " . $connection->{'first-name'};
			$uc->profileURL   = @ $connection->{'public-profile-url'};
			$uc->photoURL     = @ $connection->{'picture-url'};
			$uc->description  = @ $connection->{'summary'};

			$contacts[] = $uc; 
		}

		return $contacts;
 	}
	
   /**
	* update user status
	*/
	function setUserStatus( $status )
	{
		try{ 
			$response  = $this->api->updateNetwork( $status );
		}
		catch( LinkedInException $e ){
			throw new Exception( "Update user status update failed!  {$this->providerId} returned an error: $e" );
		}

		if ( ! $response || ! $response['success'] )
		{
			throw new Exception( "Update user status update failed! {$this->providerId} returned an error." );
		} 
 	}

   /**
	* load the user latest activity  
	*    - timeline : all the stream
	*    - me       : the user activity only  
	*/
	function getUserActivity( $stream )
	{
		try{ 
			if( $stream == "me" ){
				$response  = $this->api->updates( '?type=SHAR&scope=self&count=25' ); 
			}                                                          
			else{
				$response  = $this->api->updates( '?type=SHAR&count=25' ); 
			}
		}
		catch( LinkedInException $e ){
			throw new Exception( "User activity stream request failed! {$this->providerId} returned an error: $e" );
		}

		if( ! $response || ! $response['success'] ){
			return ARRAY();
		}

		$updates = new SimpleXMLElement( $response['linkedin'] );

		$activities = ARRAY(); 

		foreach( $updates->update as $update ) { 
			$person = $update->{'update-content'}->person;
			$share  = $update->{'update-content'}->person->{'current-share'};

			$ua = new Hybrid_User_Activity();

			$ua->id                 = @ $item->id;
			$ua->date               = @ $item->timestamp;
			$ua->text               = @ $share->{'comment'};

			$ua->user->identifier   = @ $person->id;
			$ua->user->displayName  = @ $person->{'first-name'} . ' ' . $person->{'last-name'};
			$ua->user->profileURL   = @ $person->{'site-standard-profile-request'}->url;
			$ua->user->photoURL     = NULL;
			
			$activities[] = $ua;
		}

		return $activities;
 	}
}
