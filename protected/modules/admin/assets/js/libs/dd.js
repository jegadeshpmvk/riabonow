//Display images after completely loaded
var dd = {
    init: function (sel) {
        if (typeof sel == "undefined")
            sel = ".dd";

        $(sel).each(function () {
            var label = $(this).find('a.selected-item');
            if (!label.length) {
                var sel = $(this).find('select');
                var prefix = sel.data('prefix') ? '<span class="dd-prefix">' + sel.data('prefix') + '</span>' : "";
                sel.before('<a class="selected-item">' + prefix + '<span class="b"></span><i class="fa fa-angle-down"></i></a>');
                dd.change(sel);
            }
        });
    },
    change: function (el) {
        $(el).blur();
        var $el = $(el),
                label = $el.find('option:selected').text();
        $(el).prev('.selected-item').find('.b').text(label);
        if ($el.val() == "") {
            $(el).prev('.selected-item').addClass("prompt");
            if ($el.hasClass('remove_prefix')) {
                $(el).prev('.selected-item').find('.dd-prefix').css("display", "inline-block");
            }
        } else {
            $(el).prev('.selected-item').removeClass("prompt");
            if ($el.hasClass('remove_prefix')) {
                $(el).prev('.selected-item').find('.dd-prefix').css("display", "none");
            }
        }
    }
};