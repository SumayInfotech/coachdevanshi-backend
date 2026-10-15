(function ($) {
    "use strict";

    const el = document.querySelector('.sidebar-right');
    if (el) {
        new PerfectScrollbar(el, {
            useBothWheelAxes: true,
            suppressScrollX: true,
        });
    }

})(jQuery);