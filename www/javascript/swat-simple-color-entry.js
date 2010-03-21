// {{{ function SwatSimpleColorEntry

/**
 * Simple color entry widget
 *
 * @param string id
 * @param Array colors
 * @param string none_option_title
 *
 * @copyright 2005-2010 silverorange Inc.
 */

function SwatSimpleColorEntry(id, colors, none_option_title)
{
	SwatSimpleColorEntry.superclass.constructor.call(this, id);

	this.colors = colors;
	this.none_option_title = none_option_title;
	this.input_tag = document.getElementById(this.id + '_value');

	this.hex_input_tag = document.createElement('input');
	this.hex_input_tag.type = 'text';
	this.hex_input_tag.id = this.id + '_hex_color';
	this.hex_input_tag.size = 6;

	YAHOO.util.Event.on(this.hex_input_tag, 'change',
		this.handleInputChange, this, true);

	YAHOO.util.Event.on(this.hex_input_tag, 'keyup',
		this.handleInputChange, this, true);

	// this tries to make a square palette
	this.columns = Math.ceil(Math.sqrt(this.colors.length));

	this.current_color = null;
	this.colorChangeEvent = new YAHOO.util.CustomEvent('colorChange');

	this.setColor(this.input_tag.value);
}

// }}}
// {{{ YAHOO.lang.extend(SwatSimpleColorEntry, SwatAbstractOverlay)

YAHOO.lang.extend(SwatSimpleColorEntry, SwatAbstractOverlay, {
// {{{ getContent: function()

getContent: function()
{
	var table = document.createElement('table');
	table.className = 'swat-simple-color-entry-table';
	table.cellSpacing = '1';

	var tbody = document.createElement('tbody');

	if (this.colors.length % this.columns == 0)
		var num_cells = this.colors.length
	else
		var num_cells = this.colors.length +
			(this.columns - (this.colors.length % this.columns));

	var trow;
	var tcell;
	var anchor;
	var text;

	if (this.none_option_title !== null) {
		trow = document.createElement('tr');
		tcell = document.createElement('td');
		tcell.id = this.id + '_palette_null';
		tcell.colSpan = this.columns;
		YAHOO.util.Dom.addClass(tcell,
			'swat-simple-color-entry-palette-blank');

		text = document.createTextNode(this.none_option_title);

		anchor = document.createElement('a');
		anchor.href = '#';
		anchor.appendChild(text);
		tcell.appendChild(anchor);
		trow.appendChild(tcell);
		tbody.appendChild(trow);

		YAHOO.util.Event.addListener(anchor, 'click', this.selectNull,
			this, true);
	}

	for (var i = 0; i < num_cells; i++) {
		if (i % this.columns == 0)
			trow = document.createElement('tr');

		tcell = document.createElement('td');
		text = document.createTextNode(' '); // non-breaking UTF-8 space

		if (i < this.colors.length) {
			tcell.id = this.id + '_palette_' + i;
			tcell.style.background = '#' + this.colors[i];

			anchor = document.createElement('a');
			anchor.href = '#';
			anchor.appendChild(text);

			YAHOO.util.Event.addListener(anchor, 'click', this.selectColor,
				this, true);

			tcell.appendChild(anchor);
		} else {
			YAHOO.util.Dom.addClass(tcell,
				'swat-simple-color-entry-palette-blank');

			tcell.appendChild(text);
		}

		trow.appendChild(tcell);

		if ((i + 1) % this.columns == 0)
			tbody.appendChild(trow);
	}

	table.appendChild(tbody);

	var hex_div = document.createElement('div');
	hex_div.className = 'swat-simple-color-entry-palette-hex-color';

	var label_tag = document.createElement('label');
	label_tag.htmlFor = this.id + '_hex_color';
	var title = document.createTextNode('#');
	label_tag.appendChild(title);

	hex_div.appendChild(label_tag);
	hex_div.appendChild(this.hex_input_tag);


	var div_tag = document.createElement('div');
	div_tag.appendChild(table);
	div_tag.appendChild(hex_div);

	return div_tag;
}

// }}}
});

// }}}
// {{{ SwatSimpleColorEntry.prototype.handleInputChange

SwatSimpleColorEntry.prototype.handleInputChange = function()
{
	var color = this.hex_input_tag.value;

	if (color[0] == '#') {
		color = color.slice(1, color.length - 1);
	}

	if (color.length == 3) {
		var hex3 = /^[0-9a-f]{3}$/i;
		if (!hex3.test(color)) {
			color = null;
		}
	} else if (color.length == 6) {
		var hex6 = /^[0-9a-f]{6}$/i;
		if (!hex6.test(color)) {
			color = null;
		}
	} else {
		color = null;
	}

	this.setColor(color);
}

// }}}
// {{{ SwatSimpleColorEntry.prototype.setColor

/**
 * Sets the value of the color entry input tag to the selected color and
 * highlights the selected color
 *
 * @param number color the hex value of the color
 */
SwatSimpleColorEntry.prototype.setColor = function(color)
{
	var changed = (this.current_color != color);

	if (changed) {
		this.input_tag.value = color;

		if (color === null) {
			YAHOO.util.Dom.setStyle(this.toggle_button_content,
				'background', null);
		} else {
			if (this.hex_input_tag.value != color) {
				this.hex_input_tag.value = color;
			}
			YAHOO.util.Dom.setStyle(this.toggle_button_content,
				'background', '#' + color);
		}

		this.current_color = color;

		this.value_field.value = color;

		if (color === null) {
			this.colorChangeEvent.fire(null);
		} else {
			this.colorChangeEvent.fire('#' + color);
		}

		this.highlightPalleteEntry(color);
	}
}

// }}}
// {{{ SwatSimpleColorEntry.prototype.selectNull

/**
 * Event handler that sets the color to null
 *
 * @param Event the event that triggered this select.
 */
SwatSimpleColorEntry.prototype.selectNull = function(event)
{
	this.hex_input_tag.value = '';
	YAHOO.util.Event.preventDefault(event);
	this.setColor(null);
}

// }}}
// {{{ SwatSimpleColorEntry.prototype.selectColor

/**
 * Event handler that sets the color to the selected color
 *
 * @param Event the event that triggered this select.
 */
SwatSimpleColorEntry.prototype.selectColor = function(event)
{
	YAHOO.util.Event.preventDefault(event);
	var cell = YAHOO.util.Event.getTarget(event);
	var color_index = cell.parentNode.id.split('_palette_')[1];

	this.setColor(this.colors[color_index]);
}

// }}}
// {{{ SwatSimpleColorEntry.prototype.highlightPalleteEntry

/**
 * Highlights a pallete entry
 *
 * @param number color the hex value of the color
 */
SwatSimpleColorEntry.prototype.highlightPalleteEntry = function(color)
{
	if (this.none_option_title !== null) {
		var null_entry = document.getElementById(this.id + '_palette_null');

		if (color === null) {
			YAHOO.util.Dom.addClass(null_entry,
				'swat-simple-color-entry-palette-selected');
		} else {
			YAHOO.util.Dom.removeClass(null_entry,
				'swat-simple-color-entry-palette-selected');
		}
	}

	for (var i = 0; i < this.colors.length; i++) {
		var palette_entry =
			document.getElementById(this.id + '_palette_' + i);

		if (this.current_color !== null &&
			this.colors[i].toLowerCase() ==
			this.current_color.toLowerCase()) {

			YAHOO.util.Dom.addClass(palette_entry,
				'swat-simple-color-entry-palette-selected');
		} else {
			YAHOO.util.Dom.removeClass(palette_entry,
				'swat-simple-color-entry-palette-selected');
		}
	}
}

// }}}
