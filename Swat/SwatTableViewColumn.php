<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A visible column in a SwatTableView.
 */
class SwatTableViewColumn extends SwatObject {

	public $name = null;
	public $title = '';

	private $renderers;
	private $properties;

	function __construct($name = null) {
		$this->name = $name;
		$this->renderers = array();
		$this->properties = array();
	}

	public function linkField($model_field, $renderer_property) {
		$this->properties[$model_field] = $renderer_property;
	}

	public function addRenderer(SwatCellRenderer $renderer) {
		$this->renderers[] = $renderer;
	}

	public function display($row) {
		if (count($this->renderers) == 0)
			throw new SwatException(__CLASS__.': no renderer has been provided.');

		foreach ($this->properties as $field => $property)
			foreach ($this->renderers as $renderer)
				$renderer->$property = $row->$field;

		reset($this->renderers);
		$first_renderer = current($this->renderers);
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttribs());
		$td_tag->open();

		foreach ($this->renderers as $renderer) {
			$renderer->render();
			echo ' ';
		}

		$td_tag->close();
	}
	
}
