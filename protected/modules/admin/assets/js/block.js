/*************************************
 CREATE BLOCKS ON THE FLY
*************************************/
$(function () {
    //On clicking create
    $('body').on('click', 'a.add-block, .ajax-block .update-tr, .edit-image', function(e){
        e.preventDefault();
        e.stopPropagation();
        //Load the form in iframe
        blogIframe.open(this, $(this).attr('table-id'));
    });

    $('body').on('click', '.ajax-block .fa-trash-o', function(e) {
        e.preventDefault();
        $(this).closest('tr').fadeOut(400, function() {
            $(this).remove();
            blogIframe.fldCount();
            if($('.grid-view tr').length < 2) {
                $('.grid-view tbody').append('<tr><td colspan="4"><div class="empty">No results found.</div></td></tr>');
            }
        });
    });
});

//Function to connect iFrame with Dropdown
var blogIframe = {
    insertParam: function (url, key, value) {
        key = escape(key); 
        value = escape(value);
        if (url.indexOf('?') > -1 == '') {
            return url += '?' + key + '=' + value;
        }
        else {
            var kvp = url.substr(0).split('&');
            var i = kvp.length; var x; while (i--) {
                x = kvp[i].split('=');

                if (x[0] == key) {
                    x[1] = value;
                    kvp[i] = x.join('=');
                    break;
                }
            }

            if (i < 0) { kvp[kvp.length] = [key, value].join('='); }
            
            //this will reload the page, it's likely better to store this until finished
            return kvp.join('&');
        }
    },
    open: function (el, block) {
        var count = 0,
            link = el.href;

        if (typeof block == "undefined")
            block = "field_table";

        if($('#' + block).length) {
            this.maxCount(block);
            if($('#' + block +' .table-form-fields .job-fields-cnt').val()) {
                count = parseInt($('#' + block +' .table-form-fields .job-fields-cnt').val()) + 1;
                $('#' + block +' .table-form-fields .job-fields-cnt').val(count);
            }
            else
                count = 1;
        }

        link = blogIframe.insertParam(link, "count", count);

        $('body').append('<iframe class="dropdown-create-option" src="' + link + '" scrolling="yes" data-for="list-of-blocks"></iframe>').addClass('has-iframe');
    },
    rowAdded: function(html, block) {
        var empty = $('.grid-view td[colspan]');
        var sel = $('.grid-view table');
        if (typeof block != "undefined") {
            empty = $('#' + block + '.grid-view td[colspan]');
            sel = $('#' + block + '.grid-view table');
        }

        if (empty.length) {
            var td = empty,
                tr = td.closest('tr');
            tr.remove();
        }

        sel.append(html);
        this.fldCount(block);
        this.close();
    },
    rowUpdated: function(html, id, block) {
        var sel = $('tr[data-sort="items[]_' + id + '"]');
        if (typeof block != "undefined")
            sel = $('#' + block + ' tr[data-sort="items[]_' + id + '"]');

        sel.replaceWith(html);
        this.fldCount(block);
        this.close();
    },
    close: function() {
        $('body').removeClass('has-iframe');
        $('.dropdown-create-option').remove();
        page.resize();
    },
    fldCount: function(block) {
        var sel = $('table.table-form-fields');
        var selspn = $('.spn-field-cnt');
        
        if (typeof block != "undefined") {
            sel = $('#' + block + ' table.table-form-fields'),
            selspn = $('#' + block + ' .spn-field-cnt');
        }

        if(sel.length) {
            var fldCnt = sel.find('tbody tr').length;
            selspn.text(parseInt(fldCnt));
            this.maxCount(block);
        }
    },
    maxCount: function(block) {
        var sel = $('table.table-form-fields').find('tbody tr[data-key]'),
            selcnt = $('.table-form-fields .job-fields-cnt');
            arr = [];

        if (typeof block != "undefined") {
            sel = $('#' + block +' table.table-form-fields').find('tbody tr[data-key]'),
            selcnt = $('#' + block + ' table.table-form-fields .job-fields-cnt');
        }

        sel.each(function(i){
            if($(this).attr('data-key'))
                arr[i] = $(this).attr('data-key');
            else
                arr[i] = 0;
        });
     
        if(arr.length > 1) {
            selcnt.val(Math.max.apply(Math, arr));
        }
    } 
}