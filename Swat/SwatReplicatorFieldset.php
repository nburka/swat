<?php

require_once 'Swat/SwatFieldset.php';

/**
 * A magic fieldset container that replicates itself and its children.
 * It can dynamically create widgets based on an array of replicators.
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatReplicatorFieldset extends SwatFieldset
{
	/**
	 * An array of unique id => title pairs, one for each replication.
	 * The id is used to suffix the original widget id to create a unique
	 * id for the replicated widget.
	 *
	 * @var array
	 */
	public $replicators = null;
	
	private $widgets = array();

	/**
	 * Initilizes the fieldset
	 *
	 * Goes through the internal widgets, clones them, and adds them to the
	 * widget tree.
	 */
	public function init()
	{
		parent::init();
		
		$local_children = array();

		if ($this->replicators === null)
			return;
		
		//first we add each child to the local array, and remove from the widget tree
		foreach ($this->children as $child_widget)
			$local_children[] = $this->remove($child_widget);
		
		$container = new SwatContainer;
		
		//then we clone, change the id and add back to the widget tree
		foreach ($this->replicators as $id => $title) {
			$field_set = new SwatFieldset();
			$field_set->title = $title;
			$container->add($field_set);
			
			$this->widgets[$id] = array();
			
			foreach ($local_children as $child) {
				$new_child = clone $child;
				$new_child->id.= $id;
				$field_set->add($new_child);
				$this->widgets[$id][$new_child->id] = $new_child;
			}
			$container->init();
		}

		$this->parent->replace($this, $container);
	}
	 
	/**
	 * Retrive a reference to a replicated widget
	 *
	 * @param string $widget_id the unique id of the original widget
	 * @param string $replicator_id the replicator id of the replicated widget
	 *
	 * @returns SwatWidget a reference to the replicated widget, or null if the
	 *                      widget is not found.
	 */
	public function getWidget($widget_id, $replicator_id)
	{
		if (isset($this->widgets[$replicator_id][$widget_id.$replicatorid])) {
			return $this->widgets[$replicator_id][$widget_id.$replicatorid];
		} else {
			return null;
		}
	}
}

?>
