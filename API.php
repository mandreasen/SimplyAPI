<?php

namespace Simply;

/**
 * API requests for Simply.com
 */
class API
{
	private const baseuri = "https://api.simply.com/1/";
	private $account;
	private $apikey;

	/**
	 * @param      string  $account  Simply.com accountname or account number
	 * @param      string  $apikey   Simply.com API key
	 */
	public function __construct(string $account, string $apikey)
	{
		$this->account = trim($account);
		$this->apikey = trim($apikey);
	}

	/**
	 * Send a request to the API
	 *
	 * @param      string  $uri     URI without api link, accountname and apikey
	 * @param      array   $parms   Request parameters
	 * @param      string  $method  Request method
	 * 
	 * @return     json  Response from the CURL request
	 */
	public function request(string $uri, array $parms = array(), string $method = 'GET')
	{
		// URI to request
		$uri = ltrim($uri, '/');
		$uri = self::baseuri.$this->account.'/'.$this->apikey.'/'.$uri;
		
		// HTTP Headers
		$headers = array(
			'Content-Type: application/json'
		);

		// CURL Request
		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parms));

		$response = curl_exec($ch);

		curl_close($ch);

		return json_decode($response);
	}

	/**
	 * Send a GET request to the API
	 *
	 * @param      string  $uri    URI without api link, accountname and apikey
	 * @param      array   $parms  Request parameters
	 *
	 * @return     json  Response from the CURL request
	 */
	public function get(string $uri, array $parms = array())
	{
		return $this->request($uri, $parms, 'GET');
	}


	/**
	 * Send a POST request to the API
	 *
	 * @param      string  $uri    URI without api link, accountname and apikey
	 * @param      array   $parms  Request parameters
	 *
	 * @return     json  Response from the CURL request
	 */
	public function post(string $uri, array $parms = array())
	{
		return $this->request($uri, $parms, 'POST');
	}

	/**
	 * Send a PUT request to the API
	 *
	 * @param      string  $uri    URI without api link, accountname and apikey
	 * @param      array   $parms  Request parameters
	 *
	 * @return     json  Response from the CURL request
	 */
	public function put(string $uri, array $parms = array())
	{
		return $this->request($uri, $parms, 'PUT');
	}

	/**
	 * Send a DELETE request to the API
	 *
	 * @param      string  $uri    URI without api link, accountname and apikey
	 * @param      array   $parms  Request parameters
	 *
	 * @return     json  Response from the CURL request
	 */
	public function delete(string $uri, array $parms = array())
	{
		return $this->request($uri, $parms, 'DELETE');
	}
}

?>