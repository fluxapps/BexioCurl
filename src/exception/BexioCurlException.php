<?php
/**
 * Created by PhpStorm.
 * User: jdolf
 * Date: 29.03.19
 * Time: 10:17
 */

class BexioCurlException extends ilException {

	public function __construct($a_message, $a_code = 0) {
		parent::__construct($a_message, $a_code);
	}
}