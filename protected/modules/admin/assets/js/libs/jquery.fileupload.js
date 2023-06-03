/* Custom written file upload plugin */
var fileuploadcount = 0;
(function ($) {
    $.fn.upload = function (options) {
        //Default Options
        var defaults = {
            uploadlink: '/admin/upload/file',
            formats: {'image': ['jpg', 'jpeg', 'png', 'gif']},
            filetype: [],
            extension: [],
            format: [],
            sizelimit: {'image': 5},
            multiple: true,
            method: 'html5',
            input: [],
            beforeUpload: function (elem, fid, file) {
                if (!file.multiple)
                    $('.focused-upload .' + elem.name + '-uploaded-files .media').remove();

                $('.focused-upload .' + elem.name + '-uploaded-files').append('<span id="file' + fid + '" class="media fa ' + file.fa + '"><i></i><img src="" /><a class="file-name">' + file.name + '</a><div class="progress"><div class="status"></div></div><a class="delete-file"></a></span>');
            },
            onProgress: function (elem, fid, completed) {
                $('#file' + fid + ' i').text(completed);
                $('#file' + fid + ' .progress .status').css('width', completed + '%');
            },
            onSuccess: function (elem, fid, data, settings) {
                if (data.status == 'success') {
                    //Add hidden element to store id
                    var hiddenelem = $('#file' + fid).find('input:hidden'),
                            textelem = "",
                            dataname = $(elem).data('hidden'),
                            hname = (typeof dataname == 'undefined') ? "img[]" : dataname;
                    if (!hiddenelem.length) {
                        hiddenelem = $('<input type="hidden" class="media_id_ref_for_' + elem.name + '" value="" name="' + hname + '" />');
                        $('#file' + fid).prepend(hiddenelem);
                    }
                    hiddenelem.val(data.id);
                    //Display thumb is exists
                    if (data.thumb != '')
                        $('#file' + fid).find('img').attr('src', data.thumb);
                    //Setup file link
                    $('#file' + fid + ' a.file-name').attr({
                        'href': data.link,
                        'target': '_blank'
                    });
                    //Add CSS Class
                    $('#file' + fid).addClass('complete').append('<a class="fa fa-pencil edit-image" href="' + data.alt + '"></a>');
                    //Displaying success message
                    if (data.reason != '')
                        alertify.success(data.reason);
                } else {
                    $('#file' + fid).addClass('fa-exclamation-triangle').delay(15000).fadeOut(function () {
                        $(this).remove();
                    });
                    alertify.error(data.reason);
                }
            },
            onError: function (elem, fid, msg) {
                $('#file' + fid).addClass('fa-exclamation-triangle').delay(15000).fadeOut(function () {
                    $(this).remove();
                });
                alertify.error(msg);
            },
            onComplete: function (elem, fid) {
                $('#file' + fid + ' i').remove();
                $('#file' + fid + ' .progress').remove();
                setTimeout(function () {
                    $('.file-upload-widget').removeClass('focused-upload');
                }, 100)
            }
        };
        var settings = $.extend({}, defaults, options);
        //Initialize
        this.each(function () {
            //Generate the extension array
            $.each(settings.formats, function (index, value) {
                settings.filetype.push(index);
                $.each(value, function (i, v) {
                    settings.extension.push(v);
                    settings.format[v] = index;
                });
            });

            //Check if tag is input file
            if (this.tagName != 'INPUT' || this.type != 'file') {
                alert('The file upload plugin must be used only on input file element');
                return false;
            }

            if (!new XMLHttpRequest().upload)
                settings.method = 'iframe';

            //Setup multiple file upload
            if (settings.multiple && settings.method == 'html5')
                $(this).attr('multiple', 'multiple');
            else
                $(this).removeAttr('multiple');

            //Bind event
            $(this).bind('change', function () {
                fileupload(settings, this);
            });
        });

        //Expose private methods for imagemanager.js
        this.fakeBeforeUpload = function (elem, fid, fileattr) {
            triggerBeforeUpload(elem, fid, fileattr);
        }

        this.fakeSuccess = function (elem, fid, data) {
            triggerSuccess(elem, fid, data);
        }

        //Upload smugmug images via json
        this.uploadSmugmug = function (elem, fid, data) {
            keyupload(elem, fid, data);
        }

        //Method to upload files on drag drop
        this.triggerUpload = function (elem, files) {
            fileupload(settings, elem, files)
        }

        return this;

        //Core function
        function fileupload(settings, object, drag) {
            if (settings.method == 'html5') {
                var files = (typeof drag == "undefined") ? object.files : drag;
                $.each(files, function (i) {
                    var uniqueid = ++fileuploadcount;
                    clientcheck = checkFile(settings, object, this);
                    $(object).closest('.file-upload-widget').addClass('focused-upload');
                    triggerBeforeUpload(object, uniqueid, clientcheck[2]);
                    if (clientcheck[0]) {
                        //Send file
                        var fd = new FormData;
                        fd.append('file', this);
                        fd.append('fileid', uniqueid);
                        fd.append('objectid', object.name);
                        $.ajax({
                            url: settings.uploadlink,
                            type: 'POST',
                            dataType: 'json',
                            xhr: function () {
                                myXhr = $.ajaxSettings.xhr();
                                myXhr.addEventListener('progress', function (e) { }, false);
                                if (myXhr.upload) {
                                    myXhr.upload.onprogress = function (e) {
                                        var completed = 0;
                                        if (e.lengthComputable) {
                                            var done = e.position || e.loaded,
                                                    total = e.totalSize || e.total;
                                            completed = Math.round(Math.floor(done / total * 1000) / 10);
                                        }
                                        triggerProgress(object, uniqueid, completed);
                                    }
                                }
                                return myXhr;
                            },
                            success: function (data) {
                                triggerSuccess(object, uniqueid, data);
                            },
                            error: function (jqXHR, textStatus, responseText) {
                                triggerError(object, uniqueid, responseText);
                            },
                            data: fd,
                            cache: false,
                            contentType: false,
                            processData: false
                        });
                    } else
                        triggerError(object, uniqueid, clientcheck[1]);
                });
            } else {
                var uniqueid = ++fileuploadcount;
                clientcheck = checkFile(settings, object);

                triggerBeforeUpload(object, uniqueid, clientcheck[2]);

                if (clientcheck[0])
                    iFrameUpload(settings, object, uniqueid);
                else
                    triggerError(object, uniqueid, clientcheck[1]);
            }
        }

        function keyupload(object, uniqueid, jsonkey) {
            $('#file' + uniqueid).addClass('waiting');
            $.ajaxq("copyImage", {
                url: _app_prefix + "/admin/smugmug/copy",
                type: 'POST',
                dataType: 'json',
                data: {"fileid": uniqueid, "objectid": object.name, "id": jsonkey.id, "key": jsonkey.key},
                beforeSend: function (xhr) {
                    $('#file' + uniqueid).removeClass('waiting');
                    $('#file' + uniqueid + ' .progress .status').animate({
                        'width': '90%'
                    }, 25000, "linear");
                },
                success: function (data) {
                    $('#file' + uniqueid + ' .progress .status').stop(true).animate({
                        'width': '100%'
                    }, 300, function () {
                        triggerSuccess(object, uniqueid, data);
                    });
                },
                error: function (jqXHR, textStatus, responseText) {
                    triggerError(object, uniqueid, responseText);
                },
                cache: false
            });
        }

        //Check file before upload
        function checkFile(settings, object, file) {
            if (settings.method == 'html5') {
                var file_ext = getExtension(file.name),
                        allowed_size = getAllowedSize(file_ext, settings),
                        fileattr = {name: file.name, size: file.size, multiple: settings.multiple, fa: ''};

                if (typeof settings.format[file_ext] != "undefined")
                    fileattr.fa = 'fa-file-' + settings.format[file_ext] + '-o';

                if ($.inArray(file_ext, settings.extension) == -1)
                    return [false, file.name + ' is invalid. Please upload a valid ' + combineTypes(settings.filetype) + ' file', fileattr];
                else if (file.size > allowed_size * 1024 * 1024)
                    return [false, 'Please upload a file of size less than ' + allowed_size + 'MB', fileattr];
                else
                    return [true, 'Passed all checks', fileattr];
            } else {
                var uploadname = $(object).val(),
                        arr = uploadname.split('\\'),
                        filename = (arr.length) ? arr[arr.length - 1] : '',
                        file_ext = getExtension(filename),
                        fileattr = {name: filename, size: '', multiple: settings.multiple, fa: ""};

                if (typeof settings.format[file_ext] != "undefined")
                    fileattr.fa = 'fa-file-' + settings.format[file_ext] + '-o';

                if ($.inArray(file_ext, settings.extension) == -1)
                    return [false, filename + ' is invalid. Please upload a valid ' + combineTypes(settings.filetype) + ' file', fileattr];
                else
                    return [true, 'Passed all checks', fileattr];
            }
        }

        //Utility functions
        function combineTypes(filetype) {
            var result = '';
            $.each(filetype, function (i, v) {
                result += ' / ' + v;
            });
            return result.substr(3);
        }

        function getAllowedSize(extension, settings) {
            var size = '';
            $.each(settings.formats, function (index, value) {
                if ($.inArray(extension, value) >= 0) {
                    try {
                        size = settings.sizelimit[index];
                    } catch (e) {
                        size = 5;
                    }
                }
            });
        }

        function getExtension(filename) {
            var extension = filename.substr((filename.lastIndexOf('.') + 1));
            return extension.toLowerCase();
        }

        function triggerBeforeUpload(elem, fid, fileattr) {
            if ($.isFunction(settings.beforeUpload))
                settings.beforeUpload(elem, fid, fileattr);
        }

        function triggerProgress(elem, fid, completed) {
            if ($.isFunction(settings.onProgress))
                settings.onProgress(elem, fid, completed);
        }

        function triggerSuccess(elem, fid, data) {
            triggerComplete(elem, fid);
            if ($.isFunction(settings.onSuccess))
                settings.onSuccess(elem, fid, data, settings);
        }

        function triggerError(elem, fid, msg) {
            triggerComplete(elem, fid);
            if ($.isFunction(settings.onError))
                settings.onError(elem, fid, msg);
        }

        function triggerComplete(elem, fid) {
            if ($.isFunction(settings.onComplete))
                settings.onComplete(elem, fid);

            elem.value = "";
        }

        //iFrame upload for old browsers
        function iFrameUpload(settings, object, uniqueid) {
            var id = new Date().getTime(),
                    iframe = $('<iframe name="iframe' + id + '" id="iframe' + id + '" style="display: none" />');
            form = $('<form action="" method="POST" name="form' + id + '" id="form' + id + '" data-action="default"></form>');
            form.attr("action", settings.uploadlink);
            form.attr("method", "post");
            form.attr("enctype", "multipart/form-data");
            form.attr("encoding", "multipart/form-data");
            form.attr("target", "iframe" + id);
            $('<input type="hidden" name="fileid" value="' + uniqueid + '" />').appendTo(form);
            $('<input type="hidden" name="objectid" value="' + object.name + '" />').appendTo(form);
            $('<input type="hidden" name="ajax" value="yes" />').appendTo(form);
            //Fake object in front-end
            var newobj = $(object).clone();
            $(object).before(newobj);
            $(object).attr('name', 'file').appendTo(form);
            newobj.upload(settings);
            $("body").append(iframe);
            $("body").append(form);
            form.submit();

            $("#iframe" + id).load(function () {
                var iframeContents = $("#iframe" + id)[0].contentWindow.document.body.innerText,
                        data = $.parseJSON(iframeContents);
                form.remove();
                iframe.remove();
                triggerSuccess(object, uniqueid, data);
            });
        }
    };
}(jQuery));