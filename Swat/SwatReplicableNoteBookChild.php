<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatReplicableContainer.php';
require_once 'Swat/SwatNoteBookChild.php';
require_once 'Swat/SwatNoteBookPage.php';

/**
 * A replicable container that replicates {@link SwatNoteBookChild} objects
 *
 * @package   Swat
 * @copyright 2007-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatReplicableNoteBookChild extends SwatReplicableContainer
	implements SwatNoteBookChild
{
	// {{{ public function getPages()

	/**
	 * Gets the notebook pages of this replicable notebook child
	 *
	 * Implements the {@link SwatNoteBookChild::getPages()} interface.
	 *
	 * @return array an array containing all the replicated pages of this
	 *                child.
	 */
	public function getPages()
	{
		$pages = array();

		foreach ($this->children as $child) {
			if ($child instanceof SwatNoteBookChild) {
				$pages = array_merge($pages, $child->getPages());
			}
		}

		return $pages;
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a {@link SwatNoteBookChild} to this replicable notebook child
	 *
	 * This method fulfills the {@link SwatUIParent} interface.
	 *
	 * @param SwatNoteBookChild $child the notebook child to add.
	 *
	 * @throws SwatInvalidClassException if the given object is not an instance
	 *                                    of SwatNoteBookChild.
	 *
	 * @see SwatUIParent
	 */
	public function addChild(SwatObject $child)
	{
		if (!($child instanceof SwatNoteBookChild))
			throw new SwatInvalidClassException(
				'Only SwatNoteBookChild objects may be nested within a '.
				'SwatReplicableNoteBookChild object.', 0, $child);

		parent::addChild($child);
	}

	// }}}
}

?>
