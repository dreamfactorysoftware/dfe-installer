/**
 * app.jquery.js
 * The file contains client-side functions that are global to the entire application.
 */

/**
 * Our global options
 */
var _options = {
	alertHideDelay:      5000,
	notifyDiv:           'div#request-message',
	masterTable:         'table#admin-table',
	detailsDiv:          'div#admin-detail',
	detailsTable:        'table#details-table',
	detailsTitle:        'h3#details-table-title',
	detailsCallback:     function(data, status, xhr, element) {
	},
	ajaxMessageFadeTime: 6000
};

/**
 * Shows a spinner
 * @param stop
 * @private
 */
var _wait = function(stop) {
	if (stop) {
		$('span#background-activity').addClass('hide');
		$('body').css({cursor: 'default'});

		$('span.loading-message').fadeOut(_options.ajaxMessageFadeTime, function() {
			$(this).empty();
		});
	} else {
		$('span#background-activity').removeClass('hide');
		$('body').css({cursor: 'wait'});
	}
};

/**
 * Shows a notification
 * @param style
 * @param options
 */
var notify = function(style, options) {
	var $_element = $('form:visible');
	var _message;

	if ('Success!' == options.title) {
		_message =
			'<div id="success-message" class="alert alert-block alert-success"><button type="button" class= "close" data-dismiss="alert">×</button><h4>' +
			options.title +
			'</h4>' +
			options.text +
			'</div>';
	} else {
		_message =
			'<div id="failure-message" class="alert alert-block alert-error"><button type="button" class= "close" data-dismiss="alert">×</button><h4>' +
			options.title +
			'</h4>' +
			options.text +
			'</div>';
	}

	if ($_element.length) {
		$_element.before(_message);
	} else {
		$_element = $('h1.ui-generated-header');
		if (!$_element.length) {
			return;
		}
		$_element.after(_message);
	}
};

/**
 * @private
 * @param element
 * @param errorClass
 */
var _highlightError = function(element, errorClass) {
	$(element).closest('div.control-group').addClass('error');
	$(element).addClass(errorClass);
};

/**
 * @private
 * @param element
 * @param errorClass
 */
var _unhighlightError = function(element, errorClass) {
	$(element).closest('div.control-group').removeClass('error');
	$(element).removeClass(errorClass);
};

/**
 * Adds a button to the right side of the breadcrumb bar
 *
 * @param text The button text
 * @param url The url to hit when clicked
 * @param type The Bootstrap button type (info, success, danger, etc...). Defaults to "info"
 * @private
 * @param modal
 */
var _addBreadcrumbButton = function(text, url, type, modal) {
	var dataToggle;

	if (modal) {
		dataToggle = 'data-toggle="modal"';
	}

	var _button = '<a class="btn btn-primary btn-mini btn-' + (type || 'info') + '" href="' + url + '" ' + dataToggle + ' >' + text + '</a>';
	$('ul.breadcrumb').append('<li class="crumb-button">' + _button + '</li>');
};

/**
 * Initialize any buttons and set fieldset menu classes
 */
$(function() {
	/**
	 * Breadcrumb bar button click handler. Set data-url="click url" to use
	 */
	$('li.crumb-button button').on('click', function(e) {
		e.preventDefault();
		window.location.href = $(this).data('url') || window.location.href;
	});

	//	Popovers
	if ($.fn.popover) {
		$('.auto-link-help').popover({
			placement: 'right',
			trigger:   'hover',
			live:      'true',
			content:   function() {
				return 'Popover help';
			}
		});
	}

	/**
	 * Clear any alerts after configured time
	 */
	if (_options.alertHideDelay) {
		window.setTimeout(function() {
			$('div.alert').not('.alert-fixed').fadeTo(500, 0).slideUp(500, function() {
				$(this).remove();
			});
		}, _options.alertHideDelay);
	}
});
