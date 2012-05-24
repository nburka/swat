<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatString.php';
require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A control for recording a rating out of a variable number of values
 *
 * @package   Swat
 * @copyright 2007-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRating extends SwatInputControl
{
	// {{{ public properties

	/**
	 * The value of this rating control
	 *
	 * @var integer
	 */
	public $value = null;

	/**
	 * The maximum value of this rating control
	 *
	 * @var integer
	 */
	public $maximum_value = 5;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new rating control
	 *
	 * @param string $id optional. A non-visible unique id for this rating
	 *                    control.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->requires_id = true;
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this rating control
	 */
	public function init()
	{
		parent::init();

		$flydown = $this->getCompositeWidget('flydown');
		$flydown->addOptionsByArray($this->getRatings());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this rating control
	 */
	public function process()
	{
		parent::process();

		$flydown = $this->getCompositeWidget('flydown');
		if ($flydown->value == '') {
			$this->value = null;
		} else {
			$this->value = (integer)$flydown->value;
		}
	}

	// }}}
	//  {{{ public function display()

	/**
	 * Displays this rating control
	 */
	public function display(SwatDisplayContext $context)
	{
		if (!$this->visible) {
			return;
		}

		parent::display($context);

		$flydown = $this->getCompositeWidget('flydown');
		$flydown->value = (string)$this->value;

		$div = new SwatHtmlTag('div');
		$div->id = $this->id;
		$div->class = $this->getCSSClassString();
		$div->open($context);
		$flydown->display($context);
		$div->close($context);

		$context->addYUI('dom', 'event', 'animation');
		$context->addScript('packages/swat/javascript/swat-rating.js');
		$context->addStyleSheet('packages/swat/styles/swat-rating.css');
		$context->addInlineScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getRatings()

	protected function getRatings()
	{
		$ratings = array();

		for ($i = 1; $i <= $this->maximum_value; $i++) {
			$ratings[$i] = sprintf('%s/%s', $i, $this->maximum_value);
		}

		return $ratings;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this rating control
	 *
	 * @return array the array of CSS classes that are applied to this rating
	 *                control.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-rating');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this rating control
	 *
	 * @return string the inline JavaScript required for this rating control.
	 */
	protected function getInlineJavaScript()
	{
		$quoted_string = SwatString::quoteJavaScriptString($this->id);
		return sprintf('var %s_obj = new SwatRating(%s, %s);',
			$this->id, $quoted_string, intval($this->maximum_value));
	}

	// }}}
	// {{{ protected function createCompositeWidgets()

	/**
	 * Creates the composite flydown used by this rating control
	 *
	 * @see SwatWidget::createCompositeWidgets()
	 */
	protected function createCompositeWidgets()
	{
		$flydown = new SwatFlydown();
		$flydown->id = $this->id.'_flydown';
		$flydown->serialize_values = false;
		$this->addCompositeWidget($flydown, 'flydown');
	}

	// }}}
}

?>
