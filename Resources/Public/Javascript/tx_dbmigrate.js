/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Thomas Juhnke <tommy@van-tomas.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * class to handle the dbmigrate menu
 */
var DbmigrateMenu = Class.create({

	/**
	 * registers for resize event listener and executes on DOM ready
	 */
	initialize: function () {

		Ext.onReady(function () {
			var
				self = this;

			this.toolbarItemIcon = $$('#tx-dbmigrate-menu .toolbar-item span')[0].src;

			this.checkStatus();

			Event.observe('tx-dbmigrate-menu', 'click', this.toggleMenu);

			// observe all clicks on dbmigrate actions in the menu
			$$('#tx-dbmigrate-menu li a').each(function (element) {
				$(element).onclick = function (event) {
					self.menuAction.call(self, event);
					return false;
				};
			});

		}, this);
	},

	/**
	 * checks the status for all the menu items
	 *
	 */
	checkStatus: function () {
		var
			self = this;

		$$('#tx-dbmigrate-menu li').each(function (element) {
			var
				visibleIf = $(element).readAttribute('data-visible-if');

			if (null !== visibleIf && '' !== visibleIf) {
				self.fetchStatus.call(self, element);
			}
		});
	},

	fetchStatus: function (menuItem) {
		var
			self = this,
			url = 'ajax.php?ajaxID=' + $(menuItem).readAttribute('data-visible-if');

		new Ajax.Request(url, {
			'method': 'get',
			'onComplete': function (result) {
				var
					status = result.responseText.evalJSON();

				self.setStatus.call(self, menuItem, status);
			}.bind(this)
		})
	},

	setStatus: function (menuItem, status) {
		if ('undefined' !== typeof status.status) {
			if (false === status.status) {
				$(menuItem).hide();
			} else {
				$(menuItem).show();
			}
		}
		if ('undefined' !== typeof status.icon) {
			if ('' !== status.icon) {
				$(menuItem).down('.t3-icon').replace(status.icon);
			}
		}
	},

	/**
	 * toggles the visibility of the menu and places it under the toolbar icon
	 *
	 */
	toggleMenu: function (event) {
		var
			toolbarItem = $$('#tx-dbmigrate-menu > a')[0],
			menu = $$('#tx-dbmigrate-menu .toolbar-item-menu')[0],
			isToolbarItemActive = toolbarItem.hasClassName('toolbar-item-active');

		toolbarItem.blur();

		if (false === isToolbarItemActive) {
			toolbarItem.addClassName('toolbar-item-active');
			Effect.Appear(menu, { duration: 0.2 });
			TYPO3BackendToolbarManager.hideOthers(toolbarItem);
		} else {
			toolbarItem.removeClassName('toolbar-item-active');
			Effect.Fade(menu, { duration: 0.1 });
		}

		if (event) {
			Event.stop(event);
		}
	},

	/**
	 * calls the actual menu action URL using an asynchronious HTTP request
	 *
	 * @param	Event	prototype event object
	 */
	menuAction: function (event) {
		var
			toolbarItemIcon = $$('#tx-dbmigrate-menu .toolbar-item span.t3-icon')[0],
			url = '',
			clickedElement = Event.element(event),
			// activate the spinner
			parent = Element.up(toolbarItemIcon),
			spinner = new Element('span').addClassName('spinner'),
			oldIcon = toolbarItemIcon.replace(spinner),
			call = null;

		if (clickedElement.tagName === 'SPAN') {
			link = clickedElement.up('a');
		} else {
			link = clickedElement;
		}

		if (link.href) {
			call = new Ajax.Request(link.href, {
				'method': 'get',
				'onComplete': function (result) {
					spinner.replace(oldIcon);

					this.checkStatus();
				}.bind(this)
			});
		}

		this.toggleMenu(event);
	}

});

var TYPO3BackendDbmigrateMenu = new DbmigrateMenu();