<?php
require_once('Swat/SwatObject.php');

/**
 * Data class for a user error message
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatErrorMessage extends SwatObject {

	public $message;

	function __construct($msg) {
		$this->message = $msg;
	}

}
