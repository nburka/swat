<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatButton.php');
require_once('Swat/SwatFlydown.php');
require_once('Swat/SwatActionItem.php');

/**
 * Actions widget.
 */
class SwatActions extends SwatControl {
	
	private $actionfly;
	private $btn_apply;

	private $action_items;
	public $selected = null;

	public function init() {
		$this->action_items = array();
	}
		
	public function display() {
		$this->createWidgets();
		$this->displayJavascript();
		
		echo '<div class="swat-actions">';
		echo _S('Action: ');
		$this->actionfly->display();
		echo ' ';
		$this->btn_apply->display();
		
		foreach ($this->action_items as $item) {
			if ($item->widget != null) {
				echo '<div id="'.$this->name.'_'.$item->name.'" class="swat-hidden">';
				$item->widget->display();
				echo '</div>';
			}
		}

		echo '<br />', _S('Actions apply to checked items.');
		echo '</div>';
		
	}
	
	public function process() {
		$this->createWidgets();

		$this->actionfly->process();
		$selected_name = $this->actionfly->value;

		if (isset($this->action_items[$selected_name])) {
			$this->selected = $this->action_items[$selected_name];

			if ($this->selected->widget != null)
				$this->selected->widget->process();

		} else {
			$this->selected = null;
		}
	}

	public function addActionItem(SwatActionItem $item) {
		$this->action_items[$item->name] = $item;
	}

	private function createWidgets() {	
		$this->actionfly = new SwatFlydown($this->name.'_actionfly');
		$this->actionfly->onchange = "swatActionsDisplay('{$this->name}', this.value);";
		$this->actionfly->options = array('');

		foreach ($this->action_items as $item)
			$this->actionfly->options[$item->name] = $item->title;

		$this->btn_apply = new SwatButton($this->name.'_btn_apply');
		$this->btn_apply->setTitleFromStock('apply');
	}

	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-actions.js');
		echo '</script>';
	}
}
?>
