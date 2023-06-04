$(function () {
    $('body').on('click', 'a[data-scroll]', function (e) {
        e.preventDefault();
        var el = $(this),
            sel = el.data('scroll');
        $('html, body').animate({
            'scrollTop': $(sel).offset().top - $(".header").outerHeight() - 30
        }, 1000);
    });
    /**********************
     SEARCH BAR
     **********************/
    $('body').on('click', 'a', function (e) {
        e.stopPropagation();
    });

    $('body').on('click', '.panel_left_button', function (e) {
        e.stopPropagation();
        $('body').toggleClass('panel_left_bar')
    });

    $('body').on('click', '.options a.fa-search, .search-bar a.fa-arrow-left', function () {
        $('html').toggleClass('has-search');
    });
    $('body').on('click', '.search-bar a.fa-search', function () {
        $('.search-form form').submit();
    });
    $('body').on('click', '.search-bar a.fa-refresh', function () {
        $('.search-form input[type="text"], .search-form select').val('');
    });

    /**********************
     SORTING
     **********************/
    $('body').on('click', '.options a.fa-reorder', function (e) {
        e.preventDefault();
        sort.init('.table tbody');
        $('html').addClass('has-sort');
    });

    /**********************
     SUB MENU TOGGLE
     **********************/
    $('body').on('click', '.sub_main_menu > a', function (e) {
        e.preventDefault();
        $(this).toggleClass('d_active');
        $(this).next().slideToggle();
    });
    /**********************
     REPEATER
     **********************/
    repeater.sortable();
    $('body').on('click', 'a.repeater-next-add', function (e) {
        e.preventDefault();
        var list = $(this).closest('.repeater');
        var target = $(this).closest('li');
        var rel = list.data('rel');
        var template = $('.templates [data-for="' + rel + '"]').html();
        var typeArr = ["slider", "grid", "small_icon", "feature-grid"];

        if (typeArr.indexOf(rel) >= 0) {
            template = flexibleContent.replaceImageUniqueId(template);
        }

        $(template).css('display', 'none').insertAfter(target).slideDown(function () {
            repeater.order(this);
        });
    });
    $('body').on('click', 'a.repeater-del', function (e) {
        e.preventDefault();
        var block = $(this).closest('li');
        rep = $(this).closest('.repeater');
        block.slideUp(function () {
            $(this).remove();
            repeater.order(rep);
        });
    });
    $('body').on('click', 'a.repeat-add', function (e) {
        e.preventDefault();
        var list = $(this).prev('.repeater');
        var rel = list.data('rel');
        var template = $('.templates [data-for="' + rel + '"]').html();
        var typeArr = ["slider", "gallery"];
        if (typeArr.indexOf(rel) >= 0) {
            template = flexibleContent.replaceImageUniqueId(template);
        }

        $(template).css('display', 'none').appendTo(list).slideDown(function () {
            repeater.order(this);
        });
    });

    $('body').on('click', 'a.repeater-up', function (e) {
        e.preventDefault();
        var block = $(this).closest('li'),
            prev = block.prev('li');
        if (prev.length) {
            block.insertBefore(prev);
            repeater.order(this);
        }
    });

    $('body').on('click', 'a.repeater-down', function (e) {
        e.preventDefault();
        var block = $(this).closest('li'),
            next = block.next('li');
        if (next.length) {
            block.insertAfter(next);
            repeater.order(this);
        }
    });

    $('body').on('blur', '.repeater input, .flexible-content input', function () {
        $(this).attr('value', $(this).val());
    });
    $('body').on('blur', '.repeater textarea, .flexible-content textarea', function () {
        $(this).html($(this).val());
    });
    $('body').on('change', '.repeater input[type="checkbox"], .flexible-content input[type="checkbox"]', function () {
        $(this).attr('checked', $(this).is(":checked"));
    });
    $('body').on('blur', '.repeater select, .flexible-content select', function () {
        $(this).find('option').removeAttr('selected');
        $(this).find(":selected").attr('selected', $(this).val());
    });

    /******************************************
     DISPAYING FIELD BASED ON DROP DOWN RESULT
     *******************************************/
    $('body').on('change', 'select.block-change', function (e) {
        var blockgroup = $(this).data('group'),
            groupel = $('.' + blockgroup);
        sel = '.' + blockgroup + '.' + this.value,
            el = $(sel);
        groupel.fadeOut(0);
        if (this.value !== '')
            el.fadeIn(0);
    });

    $('body').on('click', '.widget__title', function () {
        $(this).next().slideToggle();
        $(this).toggleClass('collapse');
    });

    $('body').on('click', '.next_step', function (e) {
        e.preventDefault();
        var el = $(this), parent = el.closest('.step'), step = el.attr('data-step'), width = parent.outerWidth();
        parent.find('.form-control').blur();
        setTimeout(function () {
            if (parent.find('.has-error').length === 0) {
                $('.widgets__content__move').animate({
                    transform: 'translateX(-' + (width * step) + 'px)'
                }, function () {
                    var nextstep = $('.step[data-step=' + step + ']');
                    $(nextstep).find('input[type="text"], select').first().focus();
                    if (step == 4) {
                        $(nextstep).find('#box-a_dash').first().focus();
                    }
                });
            }
        }, 200)
    });


    $('body').on('click', '.option_current_date_clear', function (e) {
        $('#option_current_date').val('');
        page.getOptionChain();
    });


    page.load();
    page.table();

    if ($('.nifty_data').length) {
        page.getNiftyExpiryDate();
        setInterval(function () {
            page.getRealDatas();
        }, 60 * 1000);
    }

    if ($('.option_chain').length) {
        setInterval(function () {
            console.log('setInterval');
            page.getOptionChain();
        }, 60 * 1000);
    }

    $('body').on('change', '#option_expiry_date,#option_options_minute, #option_options_contracts, #from_strike_price, #to_strike_price, #option_current_date', function () {
        page.getOptionChain();
    });

    if ($('#chart').length) {
        page.getCandleStikeChart();
    }

    $('body').on('change', '#expiry_date, #options_contracts', function () {
        page.getRealDatas();
    });
});

//Repeater
var repeater = {
    order: function (el) {
        var isChild = false;
        if (!$(el).hasClass('repeater')) {
            el = $(el).closest('.repeater');
        }

        if (el.closest('.repeater-item').length)
            isChild = el.closest('.repeater-item').attr('data-key');
        if (el.closest('.flexible-item').length)
            isChild = el.closest('.flexible-item').attr('data-key');
        $(el).find('> li').each(function (i) {
            repeater.destroyEditor(this);
            flexibleContent.destroySelect2($(this).find(".select2-dropdown-links"));
            flexibleContent.destroySelect2($(this).find('.select2-multi-list'));
            flexibleContent.destroySelect2($(this).find('.select2-single-list'));
            var item = $(this).find('> .repeater-item'),
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
            $(this).find('> .repeater-item').attr('data-key', i).html(html);
            repeater.subRepeatOrder($(this));
            flexibleContent.makeImageUploadable(this);
            //repeater.datePickerInit(this);
            //add editors
            repeater.initEditor(this);
            flexibleContent.select2Links($(this).find(".select2-dropdown-links"));

            flexibleContent.makeMultiSelect($(this).find('.select2-multi-list'));
            flexibleContent.makeSingleSelect($(this).find('.select2-single-list'));
        });
        //Make textarea autoresize
        $(el).find('textarea').autosize();
        //Make it sortable
        this.sortable();
    },
    sortable: function () {
        $('.repeater').each(function () {
            $(this).sortable({
                handle: $(this).find('.drag'),
                helper: 'clone',
                update: function (event, ui) {
                    repeater.order(ui.item);
                }
            });
        });
    },
    subRepeatOrder: function (el) {
        var rep = el.find('.repeater');
        if (rep.length) {
            rep.each(function () {
                repeater.order($(this));
            });
        }
    },
    datePickerInit: function (el) {
        var inpDates = $(el).find('.hasDatepicker');
        //        if (inpDates.length) {
        //            $(inpDates).each(function () {
        //                $(this).removeClass('hasDatepicker').datepicker({
        //                    onSelect: function (selectedDate) {
        //                        // custom callback logic here
        //                        $(this).attr('value', selectedDate);
        //                    }
        //                });
        //            });
        //        }
    },
    destroyEditor: function (el) {
        var txtArea = $(el).find('.repeater-widget-editor');
        if ($(txtArea).length) {
            $(txtArea).attr('data-html', $R(txtArea[0], 'source.getCode'));
            $(txtArea).attr('data-name', $(txtArea).attr("name"));
            $R(txtArea[0], 'destroy');
        }
    },
    initEditor: function (el) {
        var txtArea = $(el).find('.repeater-widget-editor');
        if ($(txtArea).length) {
            //$('.model-form textarea.repeater-widget-editor').each(function (e) {
            $(txtArea).attr("name", $(txtArea).attr("data-name"));
            $(txtArea).removeAttr("data-name");

            if ($(txtArea).attr('data-html')) {
                $(txtArea).val($(txtArea).attr('data-html'));
            }


            $R(txtArea[0], {
                plugins: ['source', 'table', 'alignment'],
                focus: false,
                formatting: ['p', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote'],
                buttons: ['format', 'bold', 'italic', 'underline', 'link', 'lists', 'html'],
                linkEmail: true, toolbarFixed: true, toolbarFixedTopOffset: 56
            });
            //});
        }
    },
    destroyFullEditor: function () {
        $('.model-form textarea.repeater-widget-editor').each(function (e) {
            $(this).attr('data-html', $R(this, 'source.getCode'));
            $(this).attr('data-name', $(this).attr("name"));
            $R(this, 'destroy');
        });
    },
    initFullEditor: function () {
        if ($('.model-form textarea.repeater-widget-editor').length) {
            $('.model-form textarea.repeater-widget-editor').each(function (e) {
                $(this).attr("name", $(this).attr("data-name"));
                $(this).removeAttr("data-name");

                if ($(this).attr('data-html')) {
                    $(this).val($(this).attr('data-html'));
                }

                $R(this, {
                    plugins: ['source', 'table', 'alignment'],
                    focus: false,
                    formatting: ['p', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote'],
                    buttons: ['format', 'bold', 'italic', 'underline', 'link', 'lists', 'html'],
                    linkEmail: true, toolbarFixed: true, toolbarFixedTopOffset: 56
                });
            });
        }
    },
};
//Page functions
var page = {
    upload_object: {}, dropdowns: {}, timer: 0, ceChart: false, peChart: false,
    saveTimer: 0,
    check: function () {
        if (!Modernizr.history)
            window.location = '/upgrade/browser';
    },
    load: function () {
        //Display alerts
        if ($('.header .alert').length) {
            $('.header .alert').slideDown().delay(5000).slideUp(function () {
                $(this).remove();
            });
        }
        //Scroll to Nav
        if ($('ul.nav .active').length) {
            if ($('ul.nav .active').offset().top > ($(window).height() - 150))
                $('.panel.left').scrollTop($('ul.nav .active').offset().top - 200);
        }
        //Initialize sorting
        sort.init();
        //Setup blocks
        $('select.block-change').change();
        $('input.block-change').change();
        //Autogrow textarea
        $('textarea').not('.html').autosize();
        //Enable HTML editor
        if ($('textarea.html').length) {
            $('textarea.html').each(function (e) {
                if ($(this).hasClass('special')) {
                    $(this).redactor({
                        plugins: ['source'],
                        focus: false,
                        formatting: ['p', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote'],
                        buttons: ['format', 'bold', 'italic', 'link', 'lists', 'html'],
                        linkEmail: true, toolbarFixed: true, toolbarFixedTopOffset: 56
                    });
                } else {
                    $(this).redactor({
                        plugins: ['source', 'table', 'alignment'],
                        focus: false,
                        buttons: ['bold', 'underline', 'link', 'html'],
                        linkEmail: true, toolbarFixed: true, toolbarFixedTopOffset: 56
                    });
                }
            });
        }
        //Render custom dropdowns
        $('.richdropdown').each(function () {
            page.dropdowns[this.id + '_ref'] = $(this).dropdown();
        });
        flexibleContent.adjustLayoutGrid();
        page.resize();
        $('.related-sidebar-type').each(function () {
            relatedWidget.init(this);
        });

    },
    resize: function () {
        //GridView
        if ($('.grid-view').length) {
            $('.full-row-edit').css('width', ($('.content').width() - 20) + 'px');
            $('.full-row-click').each(function () {
                var hei = $(this).closest('td').outerHeight();
                $(this).css('height', hei);
            });
        }

        if ($(".image-cropper").length) {
            cropper.objectFit();
        }

        flexibleContent.adjustLayoutGrid();
    },
    table: function () {
        $('.table.table-striped.table-bordered th').each(function () {
            var text = $(this).find('a').html(),
                a = $(this).find('a'),
                span = '<span>' + text + '</span>';
            a.html('');
            a.append(span);
        })

    },
    urlvars: function (href) {
        var vars = [], hash;
        var hashes = href.slice(href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    },
    getNiftyExpiryDate: function () {
        $.ajax({
            url: '/admin/refresh/get-expiry-date',
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var option = '<option value="">Please select</option>';
                if (data?.expiryDetailsDto?.expiryDates) {
                    $.each(data?.expiryDetailsDto?.expiryDates, function (index, value) {
                        option += '<option value="' + value + '">' + value + '</option>';
                    });
                    $('#expiry_date').html(option);
                }
            }, error: function () {
                alert('Error in form');
            },
            complete: function () {
            }
        });
    },
    getRealDatas: function () {
        var url = $('.nifty_data').attr('data-url');
        var options = $('#options_contracts').val();
        var expiry_date = $('#expiry_date').val();
        if (expiry_date !== '' && options !== '') {
            $.ajax({
                url: '/admin/refresh/get-nifty',
                type: 'POST',
                dataType: 'json',
                data: { expiry_date: expiry_date, options: options },
                success: function (data) {

                }, error: function () {
                    alert('Error in form');
                },
                complete: function () {
                }
            });
        }
    },
    getOptionChain: function () {
        var options = $('#option_options_contracts').val(),
            current_date = $('#option_current_date').val(),
            from_strike_price = $('#from_strike_price').val(),
            to_strike_price = $('#to_strike_price').val(),
            option_options_minute = $('#option_options_minute').val(),
            expiry_date = $('#option_expiry_date').val();
        if (options !== '' && from_strike_price !== '' && to_strike_price !== '' && expiry_date !== '') {
            $.ajax({
                url: '/admin/option-chain/get-data',
                type: 'POST',
                dataType: 'json',
                data: { expiry_date: expiry_date, options: options, to_strike_price: to_strike_price, from_strike_price: from_strike_price, current_date: current_date, min: option_options_minute },
                success: function (data) {
                    if ($('#chart').length) {
                        var rData = [];
                        var rDataPe = [];
                        $.each(data.chart[from_strike_price], function (key, value) {
                            var close_ce_oi = typeof (data.chart[from_strike_price][key + 1]) != "undefined" ? data.chart[from_strike_price][key + 1].ce_oi : value.ce_oi;
                            rData.push({
                                x: value.date_format,
                                y: [value.ce_oi, close_ce_oi, value.ce_oi, close_ce_oi]
                            });

                            var close_pe_oi = typeof (data.chart[from_strike_price][key + 1]) != "undefined" ? data.chart[from_strike_price][key + 1].pe_oi : value.pe_oi;
                            rDataPe.push({
                                x: value.date_format,
                                y: [value.pe_oi, close_pe_oi, value.pe_oi, close_pe_oi]
                            })
                        });
                        page.ceChart.updateSeries([{
                            data: rData
                        }]);
                        page.peChart.updateSeries([{
                            data: rDataPe
                        }])
                    } else {
                        $('.custom_option_headers').html(data.header);
                        $('.custom_option_body').html(data.body);
                    }
                }, error: function () {
                    alert('Error in form');
                },
                complete: function () {
                }
            });
        }
    },
    getCandleStikeChart: function () {
        var options = {
            chart: {
                type: 'candlestick',
                height: 350
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                custom: function ({ seriesIndex, dataPointIndex, w }) {
                    const o = w.globals.seriesCandleO[seriesIndex][dataPointIndex];
                    const h = w.globals.seriesCandleH[seriesIndex][dataPointIndex];
                    const diff = (h - o);
                    let text = "Open: " + o + "<br>";
                    text += "Close: " + h + "<br>";
                    text += "Difference: " + diff + "<br>";
                    return text;
                }
            },
            series: [],
            title: {
                text: 'CE Changes',
            },
            noData: {
                text: 'Loading...'
            }
        };

        page.ceChart = new ApexCharts(document.querySelector("#chart"), options);
        page.ceChart.render();
        options.title.text = 'PE Changes';
        page.peChart = new ApexCharts(document.querySelector("#chart_pe"), options);
        page.peChart.render();
    }
};
//Sorting plugin
var sort = {
    cache: '', init: function (selector) {
        this.cache = $(selector).html();
        $(selector).each(function () {
            $(this).sortable({
                placeholder: "drop-placeholder",
                revert: true, start: function (e, ui) {
                    ui.placeholder.width(ui.helper.width());
                }
            });
        });
    },
    reset: function (selector) {
        $(selector).html(this.cache);
        this.cache = '';
    },
    destroy: function (selector) {
        $(selector).sortable("destroy");
    },
    save: function (el, selector) {
        serial = $(selector).sortable("serialize", {
            key: "items[]",
            attribute: "data-sort"
        });
        $.ajax({
            url: el.href,
            type: "post", data: serial, success: function () {
                sort.destroy(selector);
                $('html').removeClass('has-sort');
                alertify.success("The order was saved successfully.");
            },
            error: function () {
                sort.destroy(selector);
                alertify.error("We are unable to set the sort order at this time.  Please try again in a few minutes.");
            }
        });
    }
};

function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}