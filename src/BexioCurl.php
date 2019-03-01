<?php

namespace srag\BexioCurl;

use ILIAS\BackgroundTasks\Exceptions\InvalidArgumentException;
use srag\DIC\DICTrait;

/**
 * Class BexioCurl
 *
 * This class uses the public key method of accessing Bexio and not the OAuth method.
 *
 * @package srag\BexioCurl
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class BexioCurl {

	use DICTrait;
	const API_URL = "https://office.bexio.com/api2.php/";
	const METHOD_POST = "POST";
	const METHOD_GET = "GET";
	/**
	 * @var string The identifier for your company. You can find your company_id in the bexio API settings.
	 */
	private $companyId;
	/**
	 * @var int The ID of a user in your account. Please contact the bexio-support if you do not know this ID.
	 */
	private $userId;
	/**
	 * @var string The public key. You are able to generate public keys in the bexio API settings.
	 */
	private $publicKey;
	/**
	 * @var string Optional parameter to mitigate manipulated data (e.g. man-in-the-middle attack)
	 */
	private $signature;


	public function __construct($companyId, $userId, $publicKey, $signature = "") {
		$this->companyId = $companyId;
		$this->userId = $userId;
		$this->publicKey = $publicKey;
		if (!empty($signature)) {
			$this->signature = $signature;
		}
	}


	/**
	 * Create a POST request using JSON data
	 *
	 * @param $suffix string String at the end of request (e.g. "/account/1")
	 * @param $data   mixed JSON encoded array with keys. (e.g. json_encode(array(array("field" => "account_no", "value" => "3600"))) )
	 *
	 * @return array
	 */
	public function postRequest($suffix, $data) {
		$ch = $this->prepareRequest($suffix);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_POST);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$result = json_decode(curl_exec($ch));

		if (property_exists($result, "error_code")) {
			throw new InvalidArgumentException("Invalid Bexio API access data or server maintenance. Check the access data values in the plugin configuration.");
		}

		return $result;
	}


	/**
	 * Create a GET request using JSON data
	 *
	 * @param $suffix string String at the end of request (e.g. "/account")
	 *
	 * @return array
	 */
	public function getRequest($suffix) {
		$ch = $this->prepareRequest($suffix);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_GET);

		$result = json_decode(curl_exec($ch));

		if (property_exists($result, "error_code")) {
			throw new InvalidArgumentException("Invalid Bexio API access data or server maintenance. Check the access data values in the plugin configuration.");
		}

		return $result;
	}


	/**
	 * @param $method int Request method (use const)
	 * @param $suffix String at the end of request (e.g. "/account/1")
	 *
	 * @return mixed JSON response
	 */
	private function prepareRequest($suffix) {
		$preparedURL = self::API_URL . $this->companyId . "/" . $this->userId . "/" . $this->publicKey . $suffix;
		$httpHeader = array(
			'Accept: application/json',
			'Content-Type: application/json',
		);
		if (!empty($this->signature)) {
			array_push($httpHeader, 'Signature: ' . $this->signature);
		}

		$ch = curl_init($preparedURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);

		return $ch;
	}


	/**
	 * @return mixed
	 */
	public function getCompanyId() {
		return $this->companyId;
	}


	/**
	 * @param mixed $companyId
	 */
	public function setCompanyId($companyId) {
		$this->companyId = $companyId;
	}


	/**
	 * @return mixed
	 */
	public function getUserId() {
		return $this->userId;
	}


	/**
	 * @param mixed $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}


	/**
	 * @return mixed
	 */
	public function getPublicKey() {
		return $this->publicKey;
	}


	/**
	 * @param mixed $publicKey
	 */
	public function setPublicKey($publicKey) {
		$this->publicKey = $publicKey;
	}


	/**
	 * @return mixed
	 */
	public function getSignature() {
		return $this->signature;
	}


	/**
	 * @param mixed $signature
	 */
	public function setSignature($signature) {
		$this->signature = $signature;
	}


	/**
	 * @return mixed
	 */
	public function getMethod() {
		return $this->method;
	}


	/**
	 * @param mixed $method
	 */
	public function setMethod($method) {
		$this->method = $method;
	}
}
