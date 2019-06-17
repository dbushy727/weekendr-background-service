import browser from './browser.min.js';
import breakpoints from './breakpoints.min.js';

(function($) {

    var $window = $(window),
        $body = $('body');

    // Breakpoints.
        breakpoints({
            xlarge:   [ '1281px',  '1680px' ],
            large:    [ '981px',   '1280px' ],
            medium:   [ '737px',   '980px'  ],
            small:    [ '481px',   '736px'  ],
            xsmall:   [ '361px',   '480px'  ],
            xxsmall:  [ null,      '360px'  ]
        });

    // Play initial animations on page load.
        $window.on('load', function() {
            window.setTimeout(function() {
                $body.removeClass('is-preload');
            }, 100);
        });

    // Touch?
        if (browser.mobile)
            $body.addClass('is-touch');

    // Menu.
        var $menu = $('#menu');

        $menu.wrapInner('<div class="inner"></div>');

        $menu._locked = false;

        $menu._lock = function() {

            if ($menu._locked)
                return false;

            $menu._locked = true;

            window.setTimeout(function() {
                $menu._locked = false;
            }, 350);

            return true;

        };

        $menu._show = function() {

            if ($menu._lock())
                $body.addClass('is-menu-visible');

        };

        $menu._hide = function() {

            if ($menu._lock())
                $body.removeClass('is-menu-visible');

        };

        $menu._toggle = function() {

            if ($menu._lock())
                $body.toggleClass('is-menu-visible');

        };

        $menu
            .appendTo($body)
            .on('click', function(event) {
                event.stopPropagation();
            })
            .on('click', 'a', function(event) {

                var href = $(this).attr('href');

                event.preventDefault();
                event.stopPropagation();

                // Hide.
                    $menu._hide();

                // Redirect.
                    if (href == '#menu')
                        return;

                    window.setTimeout(function() {
                        window.location.href = href;
                    }, 350);

            })
            .append('<a class="close" href="#menu">Close</a>');

        $body
            .on('click', 'a[href="#menu"]', function(event) {

                event.stopPropagation();
                event.preventDefault();

                // Toggle.
                    $menu._toggle();

            })
            .on('click', function(event) {

                // Hide.
                    $menu._hide();

            })
            .on('keydown', function(event) {

                // Hide on escape.
                    if (event.keyCode == 27)
                        $menu._hide();

            });

})(jQuery);