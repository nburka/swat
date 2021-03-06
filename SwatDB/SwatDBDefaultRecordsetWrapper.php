<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'SwatDB/SwatDBRecordsetWrapper.php';

/**
 * MDB2 Recordset Wrapper
 *
 * Used to wrap an MDB2 recordset into a traversable collection of objects.
 *
 * @package   SwatDB
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBDefaultRecordsetWrapper extends SwatDBRecordsetWrapper
{
	// {{{ public function __construct()

	public function __construct($rs = null)
	{
		$this->row_wrapper_class = null;
		parent::__construct($rs);
	}

	// }}}
}

?>
