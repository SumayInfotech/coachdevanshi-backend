(function ($) {
    "use strict";

    const el1 = document.querySelector('.header-dropdown-list');
    if (el1) {
        new PerfectScrollbar(el1, {
            useBothWheelAxes: true,
            suppressScrollX: true,
            suppressScrollY: false,
        });
    }

    const el2 = document.querySelector('.notifications-menu');
    if (el2) {
        new PerfectScrollbar(el2, {
            useBothWheelAxes: true,
            suppressScrollX: true,
            suppressScrollY: false,
        });
    }

    const el3 = document.querySelector('.message-menu-scroll');
    if (el3) {
        new PerfectScrollbar(el3, {
            useBothWheelAxes: true,
            suppressScrollX: true,
            suppressScrollY: false,
        });
    }

})(jQuery);