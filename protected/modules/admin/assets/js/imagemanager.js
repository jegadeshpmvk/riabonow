$(function () {

    /**********************
     DRAG & DROP
     **********************/

    if ($('.drag-drop').length) {
        var dragged;
        $(window).bind({
            dragover: function (e) {
                e.stopPropagation();
                e.preventDefault();
                clearTimeout(dragged);
                $('body').addClass('incoming');
            },
            drop: function (event) {
                event.stopPropagation();
                event.preventDefault();
                var el = $('input.upload')[0],
                        upload_instance = page.upload_object[$(el).data('instance')],
                        files = event.dataTransfer.files;
                upload_instance.triggerUpload(el, files);
                $('body').removeClass('incoming');
            },
            dragleave: function (e) {
                dragged = setTimeout(function () {
                    e.stopPropagation();
                    $('body').removeClass('incoming');
                }, 100);
            }
        });
    }


    /**********************
     IMAGE FUNCTIONS
     **********************/

    //Enable sorting in image gallery
    sort.init('[data-sort="yes"]');

    // Delete an image
    $('body').on('click', '.delete-file', function (event) {
        var el = $(this).closest('.media'),
                list = $(this).closest('.list-of-files'),
                autosave = $(this).closest('.autosave-content');
        el.find('input[type=hidden]').val('');
        el.animate({
            'opacity': 0
        }, function () {
            $(this).remove();
            if (autosave.length)
                page.save(list[0]);
        });
    });


    /**********************
     IMAGE LIBRARY
     **********************/

    // Open, close, mark images in the library
    $('body').on('click', 'a.browse-library', function (e) {
        e.preventDefault();
        $('html').addClass('has-image-library');
        library.show(this);
    });

    $('body').on('click', '.image-library-bar a.fa-arrow-left', function (e) {
        library.close();
    });

    $('body').on('click', '.image-library[data-multiple="true"] .item', function (e) {
        e.stopPropagation();
        $(this).toggleClass('selected');
    });

    $('body').on('click', '.image-library[data-multiple="false"] .item', function (e) {
        e.stopPropagation();
        $('.images-container .selected').removeClass('selected');
        $(this).addClass('selected');
        $('.image-library-bar a.fa-check').click();
    });

    //Select button
    $('body').on('click', '.image-library-bar a.fa-check', function (e) {
        if ($('.item.selected').length) {
            $('.focused-upload .list-of-files[data-for="' + library.folder + '"]').html('');
            $('.item.selected').each(function () {
                library.select(this);
            });
            library.close();
        } else
            alertify.error('No image has been selected');
    });

    /**********************
     SMUGMUG LIBRARY
     **********************/

    // Open, close, mark images in the library
    $('body').on('click', 'a.browse-smugmug', function (e) {
        e.preventDefault();
        smugmug.destroy();
        $(this).closest('.file-upload-widget').addClass('focused-upload');
        smugmug.element = $(this).closest('.file-upload-widget').find('.upload');
        $('html').addClass('has-smugmug-library');
        //Assign library
        smugmug.folder = smugmug.element.attr('name');
        smugmug.show(this.href);
    });

    $('body').on('click', '.smugmug-library-bar a.fa-arrow-left', function (e) {
        smugmug.close();
    });

    $('body').on('click', '.smugmug-album a', function (e) {
        e.stopPropagation();
        e.preventDefault();
        smugmug.album.load(this);
    });

    $('body').on('submit', '.smugmug-library form', function (e) {
        e.stopPropagation();
        e.preventDefault();
        var form = this;
        $('.smugmug-library').addClass('fetching');
        $.ajax({
            url: form.action,
            type: 'post',
            data: $(form).serialize(),
            success: function (data) {
                $('.smugmug-container').html($.cleanHTML(data));
                //Setup the container 
                smugmug.sizer();
                $('.smugmug-library').removeClass('fetching');
            },
            error: function () {
                alertify.error('Please report the issue to the developer');
            }
        });
    });

    $('body').on('click', '.smugmug-library[data-multiple="true"] .smugmug-select', function (e) {
        e.stopPropagation();
        $(this).closest('.smugmug-image').toggleClass('selected');
    });

    $('body').on('click', '.smugmug-library[data-multiple="false"] .smugmug-select', function (e) {
        $('.smugmug-container .selected').removeClass('selected');
        $(this).closest('.smugmug-image').addClass('selected');
        $('.smugmug-library-bar a.fa-check').click();
    });

    //Select button
    $('body').on('click', '.smugmug-library-bar a.fa-check', function (e) {
        if ($('.smugmug-image.selected').length) {
            $('.list-of-files[data-for="' + smugmug.folder + '"]').html('');
            $('.smugmug-image.selected').each(function () {
                smugmug.select(this);
            });
            smugmug.close(1);
        } else
            alertify.error('No image has been selected');
    });

    // Window resize
    $(window).resize(function () {
        smugmug.sizer();
    });
});

var smugmug = {
    folder: '',
    element: null,
    show: function (link) {
        $('.smugmug-library').addClass('fetching');
        $.ajax({
            url: link,
            type: 'post',
            data: {ajax: 'yes'},
            success: function (data) {
                $('.smugmug-container').html($.cleanHTML(data));
                //Setup the container 
                smugmug.sizer();
                $('.smugmug-library').removeClass('fetching');
            },
            error: function () {
                alertify.error('Please report the issue to the developer');
            }
        });
    },
    close: function (fullclose) {
        if (($('.smugmug-container form').length || $('.smugmug-image').length) && (fullclose != 1)) {
            smugmug.show($('a.browse-smugmug').attr('href'));
        } else {
            $('html').removeClass('has-smugmug-library');
            $('.smugmug-library').removeClass('fetching');
            setTimeout(smugmug.destroy, 500);
        }
    },
    destroy: function () {
        $('.focused-upload').removeClass('focused-upload');
        $('.smugmug-container').html('');
        smugmug.folder = '';
        $('.smugmug-library').removeAttr('data-multiple');
    },
    album: {
        load: function (el) {
            $('.smugmug-library').addClass('fetching');
            $.ajax({
                url: el.href,
                type: 'post',
                data: {ajax: 'yes'},
                success: function (data) {
                    $('.smugmug-container').html($.cleanHTML(data));
                    //Setup the container 
                    smugmug.album.setup(el);
                    $('.smugmug-library').removeClass('fetching');
                },
                error: function () {
                    alertify.error('Please report the issue to the developer');
                }
            });
        },
        setup: function (el) {
            //Set size
            smugmug.sizer();
            //File element
            var file_elem = smugmug.element;
            //Specify the type of selection
            $('.smugmug-library').attr('data-multiple', file_elem.attr('multiple') == 'multiple' ? "true" : "false");
        },
    },
    select: function (item) {
        var file_object = $('input[name="' + smugmug.folder + '"]'),
                select_multiple = file_object.attr('multiple') == 'multiple' ? true : false;
        upload_instance = page.upload_object[file_object.data('instance')],
                unique_id = fileuploadcount++,
                file_attributes = {name: $(item).find('.name').text(), size: '', multiple: select_multiple, fa: "fa-file-image-o"};

        upload_instance.fakeBeforeUpload(file_object[0], unique_id, file_attributes);
        upload_instance.uploadSmugmug(file_object[0], unique_id, $(item).data('details'));
    },
    sizer: function () {
        var wid = $('.smugmug-container').width();
        if ($('.smugmug-album').length) {
            var col = Math.round(wid / 220);
            $('.smugmug-container').attr('class', 'smugmug-container col' + col);
        }
        if ($('.smugmug-image').length) {
            var col = Math.ceil(wid / 160);
            $('.smugmug-container').attr('class', 'smugmug-container col' + col);
        }
    }
};

var library = {
    folder: '',
    show: function (el) {
        this.destroy();
        $(el).closest('.file-upload-widget').addClass('focused-upload');
        $.ajax({
            url: el.href,
            type: 'post',
            data: {ajax: 'yes'},
            success: function (data) {
                $('.images-container').append(data);

                //Setup the container 
                library.setup(el);
            },
            error: function () {
                alertify.error('Please report the issue to the developer');
            }
        });
    },
    close: function () {
        $('html').removeClass('has-image-library');
        setTimeout(library.destroy, 500);
    },
    destroy: function () {
        $('.focused-upload').removeClass('focused-upload');
        $('.images-container').html('');
        library.folder = '';
        $('.image-library').removeAttr('data-multiple');
    },
    setup: function (el) {
        var file_elem = $(el).closest('.file-upload-widget').find('.upload');

        //Assign library
        library.folder = file_elem.attr('name');

        var hidden_sel = ".media_id_ref_for_" + file_elem.attr('name');

        //Specify the type of selection
        $('.image-library').attr('data-multiple', file_elem.attr('multiple') == 'multiple' ? "true" : "false");

        //Arrange images using row-grid plugin
        if ($('.images-container .item').length) {
            var options = {minMargin: 15, maxMargin: 15, itemSelector: ".item", firstItemClass: "first-item"};
            $(".images-container").rowGrid(options);
        }

        //Mark the already existing items
        $(hidden_sel).each(function () {
            $("#media" + $(this).val()).addClass('selected');
        });
    },
    select: function (item) {
        var file_object = $('.focused-upload input[name="' + library.folder + '"]'),
                select_multiple = file_object.attr('multiple') == 'multiple' ? true : false;
        upload_instance = page.upload_object[file_object.data('instance')],
                unique_id = fileuploadcount++,
                file_attributes = {name: $(item).find('.name').text(), size: '', multiple: select_multiple, fa: $(item).data('fa')};

        upload_instance.fakeBeforeUpload(file_object[0], unique_id, file_attributes);
        upload_instance.fakeSuccess(file_object[0], unique_id, $(item).data('response'));
    }
};

//Custom jquery function
(function ($) {
    $.cleanHTML = function (data) {
        document.title = $.trim($(data).filter('.ajaxTitle').text());
        var tempDiv = $('<div/>').html(data);
        tempDiv.find('script[src]').remove();
        tempDiv.find('.ajaxTitle').remove();
        return tempDiv.html();
    };
})(jQuery);