$(function () {
    /**********************
     Flexible Contents
     **********************/
    flexibleContent.sortable();

    $('body').on('click', '.flexible-add', function () {
        $(this).addClass("focused-flex-add");
        var widgetType = $(this).data("type");
        if (widgetType === "content_widgets") {
            $(".flexible-library-title").html("Content Widget Library");
        }

        $('html').addClass('has-flexible-library').addClass("has-" + widgetType + "-library");
        flexibleContent.adjustLayoutGrid();
    });

    $('body').on('click', 'a.flexible-list-item', function (e) {
        e.preventDefault();
        var type = $(this).data("type");

        var template = $('.flexible-templates [data-for="' + type + '"]').html();

        var typeArr = ["banner", "testimonial"];

        if (typeArr.indexOf(type) >= 0) {
            template = flexibleContent.replaceImageUniqueId(template);
        }

        $(".focused-flex-add").each(function () {
            var thiss = $(this);
            if (thiss.hasClass("flexible-next-add")) {
                var target = thiss.closest('.flexible-li');
                $(template).css('display', 'none').insertAfter(target).slideDown(function () {
                    flexibleContent.order(this);
                    flexibleContent.scrollTo(this);
                });
            } else if (thiss.hasClass("flexible-add-new")) {
                var list = thiss.closest('.flexible-content-wrap').find(".flexible-content");
                $(template).css('display', 'none').appendTo(list).slideDown(function () {
                    flexibleContent.order(this);
                    flexibleContent.scrollTo(this);
                });
            }
        });

        $(".close-flexible-layout").trigger("click");
    });

    $('body').on('click', 'a.flexible-del', function (e) {
        e.preventDefault();
        var block = $(this).closest('.flexible-li');
        var rep = $(this).closest('.flexible-content');
        block.slideUp(function () {
            $(this).remove();
            flexibleContent.order(rep);
        });
    });

    $('body').on('click', 'a.flexible-up', function (e) {
        e.preventDefault();
        var block = $(this).closest('.flexible-li'),
                prev = block.prev('.flexible-li');

        if (prev.length) {
            block.insertBefore(prev);
            flexibleContent.order(this);
        }
    });

    $('body').on('click', 'a.flexible-down', function (e) {
        e.preventDefault();
        var block = $(this).closest('.flexible-li'),
                next = block.next('.flexible-li');

        if (next.length) {
            block.insertAfter(next);
            flexibleContent.order(this);
        }
    });

    $('body').on("click", ".close-flexible-layout", function () {
        $(".focused-flex-add").removeClass("focused-flex-add");
        $('html').removeClass('has-flexible-library');
        setTimeout(function () {
            $('html').removeClass('has-content_widgets-library');
            $('html').removeClass('has-sidebar-library');
            $('html').removeClass('has-full_top_widgets-library');
            $('html').removeClass('has-full_bottom_widgets-library');
        }, 300);
    });

    $(document).on("click", function () {
        $(".flexible-add-wrap").removeClass("open");
    });

    flexibleContent.initEditor();
    repeater.initFullEditor();
    flexibleContent.pageLoadMap();

    flexibleContent.destroySelect2($('.model-form .select2-multi-list'));
    flexibleContent.makeMultiSelect($('.model-form .select2-multi-list'));

    flexibleContent.destroySelect2($('.model-form .select2-single-list'));
    flexibleContent.makeSingleSelect($('.model-form .select2-single-list'));

    flexibleContent.select2Links($('.model-form .select2-dropdown-links'));
});

var flexibleContent = {
    redactorInstance: {},
    order: function (el) {
        flexibleContent.destroyEditor();
        flexibleContent.destroySelect2($('.model-form .select2-dropdown-links'));
        repeater.destroyFullEditor();
        flexibleContent.destroySelect2($('.model-form .select2-multi-list'));

        flexibleContent.destroySelect2($('.model-form .select2-single-list'));

        var isChild = false;
        if (!$(el).hasClass('flexible-content')) {
            el = $(el).closest('.flexible-content');
        }

        if (el.closest('.flexible-item').length)
            isChild = el.closest('.flexible-item').attr('data-key');


        $(el).find('> .flexible-li').each(function (i) {
            var item = $(this).find('> .flexible-item'),
                    oldid = item.attr('data-key'),
                    html = item.html(),
                    regex,
                    id = i;

            var nth = 0;
            html = html.replace(/-\d+-/g, function (match, i, original) {
                nth++;
                var div = isChild === false ? 1 : 2;
                return '-' + ((nth % div == 0) ? id : isChild) + '-';
            });

            nth = 0;
            html = html.replace(/\[(\d)+\]/g, function (match, i, original) {
                nth++;
                var div = isChild === false ? 1 : 2;
                return '[' + ((nth % div == 0) ? id : isChild) + ']';
            });

            //Replace HTML
            $(this).find('> .flexible-item').attr('data-key', i).html(html);

            flexibleContent.subRepeatOrder($(this));

            repeater.order(item.find(".repeater"));

            flexibleContent.makeImageUploadable(this);

            flexibleContent.mapInit(this);
        });

        //Make textarea autoresize
        $(el).find('textarea').autosize();

        //Make it sortable
        this.sortable();

        //add editors
        flexibleContent.initEditor();
        repeater.initFullEditor();
        flexibleContent.select2Links($('.model-form .select2-dropdown-links'));

        flexibleContent.makeMultiSelect($('.model-form .select2-multi-list'));

        flexibleContent.makeSingleSelect($('.model-form .select2-single-list'));
    },
    sortable: function () {
        $('.flexible-content').each(function () {
            $(this).sortable({
                handle: $(this).find('.drag'),
                helper: 'clone',
                update: function (event, ui) {
                    flexibleContent.order(ui.item);
                }
            });
        });
    },
    subRepeatOrder: function (el) {
        var rep = el.find('.flexible-content');
        if (rep.length) {
            rep.each(function () {
                flexibleContent.order($(this));
            });
        }
    },
    adjustLayoutGrid: function () {
        var gridContainer = $(".flexible-list");
        if (gridContainer.length) {
            var winWidth = parseInt($(window).width()) - 14;
            var itemWidth = parseInt($(".flexible-list-item").outerWidth());
            var totalSize = Math.floor(winWidth / itemWidth);
            var gridWidth = totalSize * itemWidth;
            gridContainer.css("width", gridWidth + "px");
        }
    },
    scrollTo: function (el) {
        $('html, body').animate({
            scrollTop: $(el).offset().top - $(".header").outerHeight()
        }, 1000);
    },
    createUniqueKey: function () {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < 10; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    },
    replaceImageUniqueId: function (template) {
        var block = $(template).find(".file-upload .upload");

        $(block).each(function () {
            var uniqueKey = flexibleContent.createUniqueKey();
            var instance = $(this).data("instance").split("-");
            instance = instance[instance.length - 1];

            var replace = instance;
            var re = new RegExp(replace, "g");
            template = template.replace(re, function (match, i, original) {
                return uniqueKey;
            });
        });
        return template;
    },
    makeImageUploadable: function (template) {
        var makable = $(template).find(".file-upload .upload");
        if (makable.length) {
            $(makable).each(function () {
                var id = $(this).data("instance");
                flexibleContent.imageUploadScript(id);
            });
        }
    },
    imageUploadScript: function (id) {
        if (page.upload_object[id]) {
            delete page.upload_object[id];
        }
        page.upload_object[id] = $("#" + id + "-file-control").upload({
            uploadlink: "/admin/upload/file",
            multiple: 0,
            formats: {"image": ["jpg", "png", "jpeg", "gif", "bmp", "pdf", "svg"]},
            sizelimit: {"image": 10}
        });
    },
    destroyEditor: function () {
        $('.model-form textarea.flexi-widget-editor').each(function (e) {
            $(this).attr('data-html', $R(this, 'source.getCode'));
            $(this).attr('data-name', $(this).attr("name"));
            $R(this, 'destroy');
        });
    },
    initEditor: function () {
        if ($('.model-form textarea.flexi-widget-editor').length) {
            $('.model-form textarea.flexi-widget-editor').each(function (e) {
                $(this).attr("name", $(this).attr("data-name"));
                $(this).removeAttr("data-name");

                if ($(this).attr('data-html')) {
                    $(this).val($(this).attr('data-html'));
                }

                $R(this, {
                    plugins: ['source'],
                    focus: false,
                    formatting: ['p', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote'],
                    buttons: ['format', 'bold', 'italic', 'underline', 'link', 'lists', 'html'],
                    linkEmail: true,
                    toolbarFixed: true,
                    toolbarFixedTopOffset: 56
                });
            });
        }
    },
    mapInit: function (el) {
        var block = $(el).find('.flexi-map-input');
        if (block.length) {
            $(block).each(function () {
                initFlexiMap(this);
            });
        }
    },
    pageLoadMap: function () {
        var block = $('.model-form .flexi-map-input');
        if (block.length) {
            $(block).each(function () {
                initFlexiMap(this);
            });
        }
    },
    destroySelect2: function (selector) {
        var select2 = selector;
        if (select2.length) {
            select2.each(function () {
                var thiss = $(this);
                if (thiss.hasClass("select2-hidden-accessible")) {
                    thiss.attr("data-value", thiss.val());
                    thiss.select2("destroy");
                }

            });
        }
    },
    select2Links: function (selector) {
        var select2 = selector;
        if (select2.length) {
            select2.each(function () {
                var thiss = $(this);

                if (thiss.attr("data-value") !== "" && thiss.attr("data-value") !== undefined) {
                    var dataVal = thiss.attr("data-value");
                    thiss.removeAttr("data-value");

                    var results = [];
                    for (var i = 0; i < linkTypesArr.length; i++) {
                        for (key in linkTypesArr[i]) {
                            if (linkTypesArr[i][key] === dataVal) {
                                results.push(linkTypesArr[i]);
                            }
                        }
                    }

                    if (results.length == 0) {
                        var myObj = {
                            "id": dataVal,
                            "text": dataVal
                        };
                        linkTypesArr.push(myObj);
                    }

                }

                thiss.select2({
                    minimumInputLength: 0,
                    containerCssClass: "custom-select2",
                    dropdownCssClass: "custom-select2-dropdown",
                    placeholder: "Please Select...",
                    tags: true,
                    data: linkTypesArr,
                    createTag: function (params) {
                        return {
                            id: params.term,
                            text: params.term,
                            newOption: true
                        }
                    },
                    templateResult: function (data) {
                        if (!data.id)
                            return data.text;

                        var splitData = data.text.split("<!_>");
                        var linkTitle = (splitData[0] != undefined && splitData[0] != "") ? splitData[0] : ""
                        var link = (splitData[1] != undefined && splitData[1] != "") ? splitData[1] : ""
                        var $state = $('<div class="link-template"><div class="label">' + linkTitle + '</div><div class="link">' + link + '</div></div>');
                        return $state;
                    },
                    templateSelection: function (selection) {
                        var splitData = selection.text.split("<!_>");
                        return splitData[0];
                    },
                });
                if (dataVal !== "" && dataVal !== undefined) {
                    thiss.val(dataVal).trigger("change");
                }
            });
        }
    },
    makeMultiSelect: function (selector) {
        var select2 = selector;
        if (select2.length) {
            select2.each(function () {
                var thiss = $(this);
                var dataVal = "";
                if (thiss.attr("data-value") !== "" && thiss.attr("data-value") !== undefined) {
                    dataVal = thiss.attr("data-value");
                    thiss.removeAttr("data-value");
                }
                thiss.select2({
                    containerCssClass: "custom-select2",
                    dropdownCssClass: "custom-select2-dropdown",
                    multiple: true,
                    tags: false
                });
                if (dataVal !== "" && dataVal !== undefined) {
                    thiss.val(dataVal.split(",")).trigger("change");
                }
            });
        }
    },
    makeSingleSelect: function (selector) {
        var select2 = selector;
        if (select2.length) {
            select2.each(function () {
                var thiss = $(this);
                var dataVal = "";
                if (thiss.attr("data-value") !== "" && thiss.attr("data-value") !== undefined) {
                    dataVal = thiss.attr("data-value");
                    thiss.removeAttr("data-value");
                }
                var tags = false;
                if (thiss.attr("data-tags") === "true") {
                    tags = true;
                }
                thiss.select2({
                    containerCssClass: "custom-select2",
                    dropdownCssClass: "custom-select2-dropdown",
                    multiple: false,
                    tags: tags
                });
                if (dataVal !== "" && dataVal !== undefined) {
                    thiss.val(dataVal).trigger("change");
                }
            });
        }
    }
};