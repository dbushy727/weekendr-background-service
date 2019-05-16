/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 277);
/******/ })
/************************************************************************/
/******/ ({

/***/ 277:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(278);


/***/ }),

/***/ 278:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(98);
__webpack_require__(99);
__webpack_require__(279);
__webpack_require__(280);
__webpack_require__(281);

/***/ }),

/***/ 279:
/***/ (function(module, exports) {

(function ($) {

	/**
  * Generate an indented list of links from a nav. Meant for use with panel().
  * @return {jQuery} jQuery object.
  */
	$.fn.navList = function () {

		var $this = $(this);
		$a = $this.find('a'), b = [];

		$a.each(function () {

			var $this = $(this),
			    indent = Math.max(0, $this.parents('li').length - 1),
			    href = $this.attr('href'),
			    target = $this.attr('target');

			b.push('<a ' + 'class="link depth-' + indent + '"' + (typeof target !== 'undefined' && target != '' ? ' target="' + target + '"' : '') + (typeof href !== 'undefined' && href != '' ? ' href="' + href + '"' : '') + '>' + '<span class="indent-' + indent + '"></span>' + $this.text() + '</a>');
		});

		return b.join('');
	};

	/**
  * Panel-ify an element.
  * @param {object} userConfig User config.
  * @return {jQuery} jQuery object.
  */
	$.fn.panel = function (userConfig) {

		// No elements?
		if (this.length == 0) return $this;

		// Multiple elements?
		if (this.length > 1) {

			for (var i = 0; i < this.length; i++) {
				$(this[i]).panel(userConfig);
			}return $this;
		}

		// Vars.
		var $this = $(this),
		    $body = $('body'),
		    $window = $(window),
		    id = $this.attr('id'),
		    config;

		// Config.
		config = $.extend({

			// Delay.
			delay: 0,

			// Hide panel on link click.
			hideOnClick: false,

			// Hide panel on escape keypress.
			hideOnEscape: false,

			// Hide panel on swipe.
			hideOnSwipe: false,

			// Reset scroll position on hide.
			resetScroll: false,

			// Reset forms on hide.
			resetForms: false,

			// Side of viewport the panel will appear.
			side: null,

			// Target element for "class".
			target: $this,

			// Class to toggle.
			visibleClass: 'visible'

		}, userConfig);

		// Expand "target" if it's not a jQuery object already.
		if (typeof config.target != 'jQuery') config.target = $(config.target);

		// Panel.

		// Methods.
		$this._hide = function (event) {

			// Already hidden? Bail.
			if (!config.target.hasClass(config.visibleClass)) return;

			// If an event was provided, cancel it.
			if (event) {

				event.preventDefault();
				event.stopPropagation();
			}

			// Hide.
			config.target.removeClass(config.visibleClass);

			// Post-hide stuff.
			window.setTimeout(function () {

				// Reset scroll position.
				if (config.resetScroll) $this.scrollTop(0);

				// Reset forms.
				if (config.resetForms) $this.find('form').each(function () {
					this.reset();
				});
			}, config.delay);
		};

		// Vendor fixes.
		$this.css('-ms-overflow-style', '-ms-autohiding-scrollbar').css('-webkit-overflow-scrolling', 'touch');

		// Hide on click.
		if (config.hideOnClick) {

			$this.find('a').css('-webkit-tap-highlight-color', 'rgba(0,0,0,0)');

			$this.on('click', 'a', function (event) {

				var $a = $(this),
				    href = $a.attr('href'),
				    target = $a.attr('target');

				if (!href || href == '#' || href == '' || href == '#' + id) return;

				// Cancel original event.
				event.preventDefault();
				event.stopPropagation();

				// Hide panel.
				$this._hide();

				// Redirect to href.
				window.setTimeout(function () {

					if (target == '_blank') window.open(href);else window.location.href = href;
				}, config.delay + 10);
			});
		}

		// Event: Touch stuff.
		$this.on('touchstart', function (event) {

			$this.touchPosX = event.originalEvent.touches[0].pageX;
			$this.touchPosY = event.originalEvent.touches[0].pageY;
		});

		$this.on('touchmove', function (event) {

			if ($this.touchPosX === null || $this.touchPosY === null) return;

			var diffX = $this.touchPosX - event.originalEvent.touches[0].pageX,
			    diffY = $this.touchPosY - event.originalEvent.touches[0].pageY,
			    th = $this.outerHeight(),
			    ts = $this.get(0).scrollHeight - $this.scrollTop();

			// Hide on swipe?
			if (config.hideOnSwipe) {

				var result = false,
				    boundary = 20,
				    delta = 50;

				switch (config.side) {

					case 'left':
						result = diffY < boundary && diffY > -1 * boundary && diffX > delta;
						break;

					case 'right':
						result = diffY < boundary && diffY > -1 * boundary && diffX < -1 * delta;
						break;

					case 'top':
						result = diffX < boundary && diffX > -1 * boundary && diffY > delta;
						break;

					case 'bottom':
						result = diffX < boundary && diffX > -1 * boundary && diffY < -1 * delta;
						break;

					default:
						break;

				}

				if (result) {

					$this.touchPosX = null;
					$this.touchPosY = null;
					$this._hide();

					return false;
				}
			}

			// Prevent vertical scrolling past the top or bottom.
			if ($this.scrollTop() < 0 && diffY < 0 || ts > th - 2 && ts < th + 2 && diffY > 0) {

				event.preventDefault();
				event.stopPropagation();
			}
		});

		// Event: Prevent certain events inside the panel from bubbling.
		$this.on('click touchend touchstart touchmove', function (event) {
			event.stopPropagation();
		});

		// Event: Hide panel if a child anchor tag pointing to its ID is clicked.
		$this.on('click', 'a[href="#' + id + '"]', function (event) {

			event.preventDefault();
			event.stopPropagation();

			config.target.removeClass(config.visibleClass);
		});

		// Body.

		// Event: Hide panel on body click/tap.
		$body.on('click touchend', function (event) {
			$this._hide(event);
		});

		// Event: Toggle.
		$body.on('click', 'a[href="#' + id + '"]', function (event) {

			event.preventDefault();
			event.stopPropagation();

			config.target.toggleClass(config.visibleClass);
		});

		// Window.

		// Event: Hide on ESC.
		if (config.hideOnEscape) $window.on('keydown', function (event) {

			if (event.keyCode == 27) $this._hide(event);
		});

		return $this;
	};

	/**
  * Apply "placeholder" attribute polyfill to one or more forms.
  * @return {jQuery} jQuery object.
  */
	$.fn.placeholder = function () {

		// Browser natively supports placeholders? Bail.
		if (typeof document.createElement('input').placeholder != 'undefined') return $(this);

		// No elements?
		if (this.length == 0) return $this;

		// Multiple elements?
		if (this.length > 1) {

			for (var i = 0; i < this.length; i++) {
				$(this[i]).placeholder();
			}return $this;
		}

		// Vars.
		var $this = $(this);

		// Text, TextArea.
		$this.find('input[type=text],textarea').each(function () {

			var i = $(this);

			if (i.val() == '' || i.val() == i.attr('placeholder')) i.addClass('polyfill-placeholder').val(i.attr('placeholder'));
		}).on('blur', function () {

			var i = $(this);

			if (i.attr('name').match(/-polyfill-field$/)) return;

			if (i.val() == '') i.addClass('polyfill-placeholder').val(i.attr('placeholder'));
		}).on('focus', function () {

			var i = $(this);

			if (i.attr('name').match(/-polyfill-field$/)) return;

			if (i.val() == i.attr('placeholder')) i.removeClass('polyfill-placeholder').val('');
		});

		// Password.
		$this.find('input[type=password]').each(function () {

			var i = $(this);
			var x = $($('<div>').append(i.clone()).remove().html().replace(/type="password"/i, 'type="text"').replace(/type=password/i, 'type=text'));

			if (i.attr('id') != '') x.attr('id', i.attr('id') + '-polyfill-field');

			if (i.attr('name') != '') x.attr('name', i.attr('name') + '-polyfill-field');

			x.addClass('polyfill-placeholder').val(x.attr('placeholder')).insertAfter(i);

			if (i.val() == '') i.hide();else x.hide();

			i.on('blur', function (event) {

				event.preventDefault();

				var x = i.parent().find('input[name=' + i.attr('name') + '-polyfill-field]');

				if (i.val() == '') {

					i.hide();
					x.show();
				}
			});

			x.on('focus', function (event) {

				event.preventDefault();

				var i = x.parent().find('input[name=' + x.attr('name').replace('-polyfill-field', '') + ']');

				x.hide();

				i.show().focus();
			}).on('keypress', function (event) {

				event.preventDefault();
				x.val('');
			});
		});

		// Events.
		$this.on('submit', function () {

			$this.find('input[type=text],input[type=password],textarea').each(function (event) {

				var i = $(this);

				if (i.attr('name').match(/-polyfill-field$/)) i.attr('name', '');

				if (i.val() == i.attr('placeholder')) {

					i.removeClass('polyfill-placeholder');
					i.val('');
				}
			});
		}).on('reset', function (event) {

			event.preventDefault();

			$this.find('select').val($('option:first').val());

			$this.find('input,textarea').each(function () {

				var i = $(this),
				    x;

				i.removeClass('polyfill-placeholder');

				switch (this.type) {

					case 'submit':
					case 'reset':
						break;

					case 'password':
						i.val(i.attr('defaultValue'));

						x = i.parent().find('input[name=' + i.attr('name') + '-polyfill-field]');

						if (i.val() == '') {
							i.hide();
							x.show();
						} else {
							i.show();
							x.hide();
						}

						break;

					case 'checkbox':
					case 'radio':
						i.attr('checked', i.attr('defaultValue'));
						break;

					case 'text':
					case 'textarea':
						i.val(i.attr('defaultValue'));

						if (i.val() == '') {
							i.addClass('polyfill-placeholder');
							i.val(i.attr('placeholder'));
						}

						break;

					default:
						i.val(i.attr('defaultValue'));
						break;

				}
			});
		});

		return $this;
	};

	/**
  * Moves elements to/from the first positions of their respective parents.
  * @param {jQuery} $elements Elements (or selector) to move.
  * @param {bool} condition If true, moves elements to the top. Otherwise, moves elements back to their original locations.
  */
	$.prioritize = function ($elements, condition) {

		var key = '__prioritize';

		// Expand $elements if it's not already a jQuery object.
		if (typeof $elements != 'jQuery') $elements = $($elements);

		// Step through elements.
		$elements.each(function () {

			var $e = $(this),
			    $p,
			    $parent = $e.parent();

			// No parent? Bail.
			if ($parent.length == 0) return;

			// Not moved? Move it.
			if (!$e.data(key)) {

				// Condition is false? Bail.
				if (!condition) return;

				// Get placeholder (which will serve as our point of reference for when this element needs to move back).
				$p = $e.prev();

				// Couldn't find anything? Means this element's already at the top, so bail.
				if ($p.length == 0) return;

				// Move element to top of parent.
				$e.prependTo($parent);

				// Mark element as moved.
				$e.data(key, $p);
			}

			// Moved already?
			else {

					// Condition is true? Bail.
					if (condition) return;

					$p = $e.data(key);

					// Move element back to its original location (using our placeholder).
					$e.insertAfter($p);

					// Unmark element as moved.
					$e.removeData(key);
				}
		});
	};
})(jQuery);

/***/ }),

/***/ 280:
/***/ (function(module, exports) {

/*
	Eventually by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
*/

(function () {

	"use strict";

	var $body = document.querySelector('body');

	// Methods/polyfills.

	// classList | (c) @remy | github.com/remy/polyfills | rem.mit-license.org
	!function () {
		function t(t) {
			this.el = t;for (var n = t.className.replace(/^\s+|\s+$/g, "").split(/\s+/), i = 0; i < n.length; i++) {
				e.call(this, n[i]);
			}
		}function n(t, n, i) {
			Object.defineProperty ? Object.defineProperty(t, n, { get: i }) : t.__defineGetter__(n, i);
		}if (!("undefined" == typeof window.Element || "classList" in document.documentElement)) {
			var i = Array.prototype,
			    e = i.push,
			    s = i.splice,
			    o = i.join;t.prototype = { add: function add(t) {
					this.contains(t) || (e.call(this, t), this.el.className = this.toString());
				}, contains: function contains(t) {
					return -1 != this.el.className.indexOf(t);
				}, item: function item(t) {
					return this[t] || null;
				}, remove: function remove(t) {
					if (this.contains(t)) {
						for (var n = 0; n < this.length && this[n] != t; n++) {}s.call(this, n, 1), this.el.className = this.toString();
					}
				}, toString: function toString() {
					return o.call(this, " ");
				}, toggle: function toggle(t) {
					return this.contains(t) ? this.remove(t) : this.add(t), this.contains(t);
				} }, window.DOMTokenList = t, n(Element.prototype, "classList", function () {
				return new t(this);
			});
		}
	}();

	// canUse
	window.canUse = function (p) {
		if (!window._canUse) window._canUse = document.createElement("div");var e = window._canUse.style,
		    up = p.charAt(0).toUpperCase() + p.slice(1);return p in e || "Moz" + up in e || "Webkit" + up in e || "O" + up in e || "ms" + up in e;
	};

	// window.addEventListener
	(function () {
		if ("addEventListener" in window) return;window.addEventListener = function (type, f) {
			window.attachEvent("on" + type, f);
		};
	})();

	// Play initial animations on page load.
	window.addEventListener('load', function () {
		window.setTimeout(function () {
			$body.classList.remove('is-preload');
		}, 100);
	});

	// Slideshow Background.
	(function () {

		var mobilecheck = function mobilecheck() {
			var check = false;
			(function (a) {
				if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true;
			})(navigator.userAgent || navigator.vendor || window.opera);
			return check;
		};

		// Settings.
		if (mobilecheck()) {
			var settings = {

				// Images (in the format of 'url': 'alignment').
				images: {
					'/images/girl-canyon-1.jpg': 'center',
					'/images/man-airport-1.jpg': 'center',
					'/images/airplane-seats-1.jpg': 'center',
					'/images/couple-forest-2.jpg': 'bottom',
					'/images/man-lake-mountains-1.jpg': 'center',
					'/images/airplane-sunset-1.jpg': 'center'
				},

				// Delay.
				delay: 6000

			};
		} else {
			var settings = {

				// Images (in the format of 'url': 'alignment').
				images: {
					'/images/girl-canyon-2-2.jpg': 'center',
					'/images/man-airport-1.jpg': 'center',
					'/images/airplane-seats-1.jpg': 'center',
					'/images/couple-forest-2.jpg': 'bottom',
					'/images/man-lake-mountains-1.jpg': 'center',
					'/images/airplane-sunset-1.jpg': 'center'
				},

				// Delay.
				delay: 6000

			};
		}

		// Vars.
		var pos = 0,
		    lastPos = 0,
		    $wrapper,
		    $bgs = [],
		    $bg,
		    k,
		    v;

		// Create BG wrapper, BGs.
		$wrapper = document.createElement('div');
		$wrapper.id = 'bg';
		$body.appendChild($wrapper);

		for (k in settings.images) {

			// Create BG.
			$bg = document.createElement('div');
			$bg.style.backgroundImage = 'url("' + k + '")';
			$bg.style.backgroundPosition = settings.images[k];
			$wrapper.appendChild($bg);

			// Add it to array.
			$bgs.push($bg);
		}

		// Main loop.
		$bgs[pos].classList.add('visible');
		$bgs[pos].classList.add('top');

		// Bail if we only have a single BG or the client doesn't support transitions.
		if ($bgs.length == 1 || !canUse('transition')) return;

		window.setInterval(function () {

			lastPos = pos;
			pos++;

			// Wrap to beginning if necessary.
			if (pos >= $bgs.length) pos = 0;

			// Swap top images.
			$bgs[lastPos].classList.remove('top');
			$bgs[pos].classList.add('visible');
			$bgs[pos].classList.add('top');

			// Hide last image after a short delay.
			window.setTimeout(function () {
				$bgs[lastPos].classList.remove('visible');
			}, settings.delay / 2);
		}, settings.delay);
	})();

	// Signup Form.
	(function () {

		// Vars.
		var $form = document.querySelectorAll('#mc-embedded-subscribe-form')[0],
		    $submit = document.querySelectorAll('#mc-embedded-subscribe-form input[type="submit"]')[0],
		    $message;

		// Bail if addEventListener isn't supported.
		if (!('addEventListener' in $form)) return;

		// Message.
		$message = document.createElement('span');
		$message.classList.add('message');
		$form.appendChild($message);

		$message._show = function (type, text) {

			$message.innerHTML = text;
			$message.classList.add(type);
			$message.classList.add('visible');

			window.setTimeout(function () {
				$message._hide();
			}, 3000);
		};

		$message._hide = function () {
			$message.classList.remove('visible');
		};

		// Events.
		// Note: If you're *not* using AJAX, get rid of this event listener.
		$form.addEventListener('submit', function (event) {

			event.stopPropagation();
			event.preventDefault();

			// Hide message.
			$message._hide();

			// Disable submit.
			$submit.disabled = true;

			// Process form.
			// Note: Doesn't actually do anything yet (other than report back with a "thank you"),
			// but there's enough here to piece together a working AJAX submission call that does.
			window.setTimeout(function () {

				var email = $('#mce-EMAIL').val();
				var airport = $('#mce-AIRPORT').val();

				$.ajax({
					method: 'POST',
					url: '/subscribe.php',
					data: { email: email, airport: airport },
					success: function success(data) {
						window.location.href = '/thank-you.html';
						$form.reset();
						$message._show('success', 'Thank You');
						$submit.disabled = false;
					}, error: function error(err) {
						console.log(err);
						$message._show('failure', 'Something went wrong. Please check your email address, airport code and try again.');
						$submit.disabled = false;
					}
				});
			}, 750);
		});

		$('#mce-AIRPORT').select2({
			ajax: {
				delay: 250,
				url: function url(params) {
					return "/places/" + (params.term || 'Anywhere');
				},
				dataType: 'json',
				type: "GET",
				processResults: function processResults(data) {
					return {
						results: _.map(data.Places, function (place) {
							return { id: place.PlaceId, text: place.PlaceName };
						})
					};
				}
			}
		});
	})();
})();

/***/ }),

/***/ 281:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__browser_min_js__ = __webpack_require__(99);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__browser_min_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__browser_min_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__breakpoints_min_js__ = __webpack_require__(98);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__breakpoints_min_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__breakpoints_min_js__);



(function ($) {

    var $window = $(window),
        $body = $('body');

    // Breakpoints.
    __WEBPACK_IMPORTED_MODULE_1__breakpoints_min_js___default()({
        xlarge: ['1281px', '1680px'],
        large: ['981px', '1280px'],
        medium: ['737px', '980px'],
        small: ['481px', '736px'],
        xsmall: ['361px', '480px'],
        xxsmall: [null, '360px']
    });

    // Play initial animations on page load.
    $window.on('load', function () {
        window.setTimeout(function () {
            $body.removeClass('is-preload');
        }, 100);
    });

    // Touch?
    if (__WEBPACK_IMPORTED_MODULE_0__browser_min_js___default.a.mobile) $body.addClass('is-touch');

    // Menu.
    var $menu = $('#menu');

    $menu.wrapInner('<div class="inner"></div>');

    $menu._locked = false;

    $menu._lock = function () {

        if ($menu._locked) return false;

        $menu._locked = true;

        window.setTimeout(function () {
            $menu._locked = false;
        }, 350);

        return true;
    };

    $menu._show = function () {

        if ($menu._lock()) $body.addClass('is-menu-visible');
    };

    $menu._hide = function () {

        if ($menu._lock()) $body.removeClass('is-menu-visible');
    };

    $menu._toggle = function () {

        if ($menu._lock()) $body.toggleClass('is-menu-visible');
    };

    $menu.appendTo($body).on('click', function (event) {
        event.stopPropagation();
    }).on('click', 'a', function (event) {

        var href = $(this).attr('href');

        event.preventDefault();
        event.stopPropagation();

        // Hide.
        $menu._hide();

        // Redirect.
        if (href == '#menu') return;

        window.setTimeout(function () {
            window.location.href = href;
        }, 350);
    }).append('<a class="close" href="#menu">Close</a>');

    $body.on('click', 'a[href="#menu"]', function (event) {

        event.stopPropagation();
        event.preventDefault();

        // Toggle.
        $menu._toggle();
    }).on('click', function (event) {

        // Hide.
        $menu._hide();
    }).on('keydown', function (event) {

        // Hide on escape.
        if (event.keyCode == 27) $menu._hide();
    });
})(jQuery);

/***/ }),

/***/ 98:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* breakpoints.js v1.0 | @ajlkn | MIT licensed */
var breakpoints = function () {
  "use strict";
  function e(e) {
    t.init(e);
  }var t = { list: null, media: {}, events: [], init: function init(e) {
      t.list = e, window.addEventListener("resize", t.poll), window.addEventListener("orientationchange", t.poll), window.addEventListener("load", t.poll), window.addEventListener("fullscreenchange", t.poll);
    }, active: function active(e) {
      var n, a, s, i, r, d, c;if (!(e in t.media)) {
        if (">=" == e.substr(0, 2) ? (a = "gte", n = e.substr(2)) : "<=" == e.substr(0, 2) ? (a = "lte", n = e.substr(2)) : ">" == e.substr(0, 1) ? (a = "gt", n = e.substr(1)) : "<" == e.substr(0, 1) ? (a = "lt", n = e.substr(1)) : "!" == e.substr(0, 1) ? (a = "not", n = e.substr(1)) : (a = "eq", n = e), n && n in t.list) if (i = t.list[n], Array.isArray(i)) {
          if (r = parseInt(i[0]), d = parseInt(i[1]), isNaN(r)) {
            if (isNaN(d)) return;c = i[1].substr(String(d).length);
          } else c = i[0].substr(String(r).length);if (isNaN(r)) switch (a) {case "gte":
              s = "screen";break;case "lte":
              s = "screen and (max-width: " + d + c + ")";break;case "gt":
              s = "screen and (min-width: " + (d + 1) + c + ")";break;case "lt":
              s = "screen and (max-width: -1px)";break;case "not":
              s = "screen and (min-width: " + (d + 1) + c + ")";break;default:
              s = "screen and (max-width: " + d + c + ")";} else if (isNaN(d)) switch (a) {case "gte":
              s = "screen and (min-width: " + r + c + ")";break;case "lte":
              s = "screen";break;case "gt":
              s = "screen and (max-width: -1px)";break;case "lt":
              s = "screen and (max-width: " + (r - 1) + c + ")";break;case "not":
              s = "screen and (max-width: " + (r - 1) + c + ")";break;default:
              s = "screen and (min-width: " + r + c + ")";} else switch (a) {case "gte":
              s = "screen and (min-width: " + r + c + ")";break;case "lte":
              s = "screen and (max-width: " + d + c + ")";break;case "gt":
              s = "screen and (min-width: " + (d + 1) + c + ")";break;case "lt":
              s = "screen and (max-width: " + (r - 1) + c + ")";break;case "not":
              s = "screen and (max-width: " + (r - 1) + c + "), screen and (min-width: " + (d + 1) + c + ")";break;default:
              s = "screen and (min-width: " + r + c + ") and (max-width: " + d + c + ")";}
        } else s = "(" == i.charAt(0) ? "screen and " + i : i;t.media[e] = !!s && s;
      }return t.media[e] !== !1 && window.matchMedia(t.media[e]).matches;
    }, on: function on(e, n) {
      t.events.push({ query: e, handler: n, state: !1 }), t.active(e) && n();
    }, poll: function poll() {
      var e, n;for (e = 0; e < t.events.length; e++) {
        n = t.events[e], t.active(n.query) ? n.state || (n.state = !0, n.handler()) : n.state && (n.state = !1);
      }
    } };return e._ = t, e.on = function (e, n) {
    t.on(e, n);
  }, e.active = function (e) {
    return t.active(e);
  }, e;
}();!function (e, t) {
   true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (t),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) ? module.exports = t() : e.breakpoints = t();
}(this, function () {
  return breakpoints;
});

/***/ }),

/***/ 99:
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* browser.js v1.0 | @ajlkn | MIT licensed */
var browser = function () {
  "use strict";
  var e = { name: null, version: null, os: null, osVersion: null, touch: null, mobile: null, _canUse: null, canUse: function canUse(n) {
      e._canUse || (e._canUse = document.createElement("div"));var o = e._canUse.style,
          r = n.charAt(0).toUpperCase() + n.slice(1);return n in o || "Moz" + r in o || "Webkit" + r in o || "O" + r in o || "ms" + r in o;
    }, init: function init() {
      var n,
          o,
          r,
          i,
          t = navigator.userAgent;for (n = "other", o = 0, r = [["firefox", /Firefox\/([0-9\.]+)/], ["bb", /BlackBerry.+Version\/([0-9\.]+)/], ["bb", /BB[0-9]+.+Version\/([0-9\.]+)/], ["opera", /OPR\/([0-9\.]+)/], ["opera", /Opera\/([0-9\.]+)/], ["edge", /Edge\/([0-9\.]+)/], ["safari", /Version\/([0-9\.]+).+Safari/], ["chrome", /Chrome\/([0-9\.]+)/], ["ie", /MSIE ([0-9]+)/], ["ie", /Trident\/.+rv:([0-9]+)/]], i = 0; i < r.length; i++) {
        if (t.match(r[i][1])) {
          n = r[i][0], o = parseFloat(RegExp.$1);break;
        }
      }for (e.name = n, e.version = o, n = "other", o = 0, r = [["ios", /([0-9_]+) like Mac OS X/, function (e) {
        return e.replace("_", ".").replace("_", "");
      }], ["ios", /CPU like Mac OS X/, function (e) {
        return 0;
      }], ["wp", /Windows Phone ([0-9\.]+)/, null], ["android", /Android ([0-9\.]+)/, null], ["mac", /Macintosh.+Mac OS X ([0-9_]+)/, function (e) {
        return e.replace("_", ".").replace("_", "");
      }], ["windows", /Windows NT ([0-9\.]+)/, null], ["bb", /BlackBerry.+Version\/([0-9\.]+)/, null], ["bb", /BB[0-9]+.+Version\/([0-9\.]+)/, null], ["linux", /Linux/, null], ["bsd", /BSD/, null], ["unix", /X11/, null]], i = 0; i < r.length; i++) {
        if (t.match(r[i][1])) {
          n = r[i][0], o = parseFloat(r[i][2] ? r[i][2](RegExp.$1) : RegExp.$1);break;
        }
      }e.os = n, e.osVersion = o, e.touch = "wp" == e.os ? navigator.msMaxTouchPoints > 0 : !!("ontouchstart" in window), e.mobile = "wp" == e.os || "android" == e.os || "ios" == e.os || "bb" == e.os;
    } };return e.init(), e;
}();!function (e, n) {
   true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (n),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) ? module.exports = n() : e.browser = n();
}(this, function () {
  return browser;
});

/***/ })

/******/ });