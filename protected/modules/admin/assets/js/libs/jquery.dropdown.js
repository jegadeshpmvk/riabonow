/* Custom written dropdown plugin */
(function ($) {
    $.fn.dropdown = function (options) {
        //Default Options
        var defaults = {
            select: 'h2',
            multiSelect: false,
            onRender: function (element) {},
            onChange: function (option, element) {},
            onClose: function (option, element) {}
        };
        var settings = $.extend({}, defaults, options);


        /****************************
        CORE FUNCTION FOR THIS PLUGIN
        *****************************/
        var core = {
            init: function (element, settings) {
                //Check if tag is select
                if (element.tagName != 'SELECT') {
                    alert('The plugin must be used only with select element');
                    return false;
                }

                //Create wrapper div
                var wrapper_id = element.id + '_wrapper',
                    wrapper_sel = '#' + wrapper_id,
                    wrapper = $('<div/>', { 
                    'id': wrapper_id,
                    'class': 'dropdown-wrapper'
                });

                //Wrap select inside the created wrapper
                $(element).wrap(wrapper);

                //Create HTML dropdown container
                var container = $('<div/>', { 
                    'id': element.id + '_dropdown',
                    'class': 'dropdown'
                });

                //Div to hold the selected value
                var selected = $('<a/>', { 
                    'id': element.id + '_dropdown_selected',
                    'class': 'dropdown-selected'
                });

                //Div to hold the options list
                var options = $('<div/>', { 
                    'id': element.id + '_dropdown_options',
                    'class': 'dropdown-options'
                });

                //Add link to create new options (via Ajax) if specified
                var addOptions = $(element).is("[data-new]");
                if (addOptions) {
                    var create = $('<a/>', { 
                        'id': element.id + '_dropdown_new_option',
                        'class': 'dropdown-create b fa fa-plus',
                        'href': $(element).data('new'),
                        'target': '_blank',
                        'html': 'Add New ' + ($(element).is("[data-label]") ? $(element).data('label') : 'Option')
                    });
                    options.append(create);
                }

                //Loop throgh options
                $('#' + element.id + ' > *').each(function() {
                    //If it is an option
                    if($(this).is('option')) 
                        core.row.create(element, options, this, addOptions);
                     //If it is an option-group
                    else if($(this).is('optgroup')) {
                        //Add option group label
                        var label = this.getAttribute('label');

                        //Group Wrapper
                        var optionGroup = $('<div/>', { 
                            'class': 'dropdown-option-group'
                        });

                        var optLabel = $('<label/>', { 
                            'class': 'dropdown-opt-label',
                            'html': label
                        });
                        optionGroup.append(optLabel);
                        
                        // Loop through all options under this optgroup
                        $(this).find('option').each(function() {
                            core.row.create(element, optionGroup, this, addOptions);
                        });

                        options.append(optionGroup);
                    }
                });
                
                //Append selected item & options to container
                container.append(selected).append(options)
                
                //Append container to wrapper
                $(wrapper_sel).append(container);

                
                /**************
                BIND EVENTS
                **************/
                
                //Open the options
                $(wrapper_sel + ' .dropdown-selected').bind('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var open = $(wrapper_sel + ' .dropdown').toggleClass('open').hasClass('open');
                });

                //On selecting an option
                $(wrapper_sel + ' .dropdown-option').bind('click', function (e) { 
                    e.preventDefault(); 
                    e.stopPropagation();            

                    if(!$(this).hasClass('disabled')) {
                        var clickedOption = $(this);
                        //Select the clicked option
                        core.row.select(clickedOption, element);
                    }
                });

                //Edit an option via Ajax
                $(wrapper_sel + ' .dropdown-option a.fa-edit').bind('click', function (e) {  
                    e.preventDefault();
                    e.stopPropagation();

                    dropdownIframe.open(this);
                });

                //Close on clicking ouside the dropdown
                $(document).mouseup(function (e) {
                    var container = $(wrapper_sel),
                        option;

                    if (!container.is(e.target) && container.has(e.target).length === 0) {
                        core.trigger.close(option, element);
                    }
                });


                //If multi-select is enabled, then disable all the selected options
                if(settings.multiSelect) {
                    var tagWrapper = $(element).closest('.multiselector');
                    
                    tagWrapper.find('.tag input').each(function() {
                        core.row.disable(element.id, $(this).val());
                    });
                }

                //The select has been rendered in the DOM
                core.trigger.render(element);
            },
            destroy: function (element) {
                var id = $(element).attr('id'),
                    sel = '#' + id + '_dropdown';
               
                //Unbind click events
                $(sel + ' .dropdown-selected, ' + sel + ' .dropdown-option').unbind('click');
                
                //Remove Dropdown
                $(sel).remove();
                
                //Remove the wrapper
                if($(element).parent().is( "div.dropdown-wrapper" )) {
                    $(element).unwrap();
                }
            },
            row: { /** Functions to work on the options **/
                create: function (element, dropdown, option, addNewOptions) {
                    var optionRow = $('<label/>', { 
                        'id': element.id + '_dropdown-option_' + option.value,
                        'class': 'dropdown-option' + ($(option).is(':disabled') ? ' disabled'  : ''),
                        'data-value': option.value,
                        'html': option.text
                    });
                    
                    if(option.value !== '' && addNewOptions) {
                        var buttons = $('<a/>', { 
                            'id': element.id + '_dropdown-option_' + option.value + '_update',
                            'class': 'fa fa-edit',
                            'href': $(element).data('new') + '&id=' + option.value
                        });
                        optionRow.append(buttons);
                    }
                    
                    dropdown.append(optionRow);
                },
                select: function (option, element) {
                    var selected = '#' + element.id + '_dropdown_selected',
                        active = '#' + element.id + '_dropdown_options .selected',
                        selectTextTag = option.find(settings.select),
                        selectText = selectTextTag.length? selectTextTag.text() : option.text();
                    
                    $(selected).html(selectText);

                    $(active).removeClass('selected');
                    option.addClass('selected');

                    //Trigger change event
                    core.trigger.change(option, element);
                },
                disable: function (id, value) {
                    $('#'+ id +' option[value="' + value + '"]').attr("disabled", "disabled");
                    $('#'+ id + '_dropdown-option_' + value).addClass('disabled');
                }
            },
            trigger: { /** Functions to trigger events **/
                render: function (element) {
                    //Set the default value
                    core.row.select($('#' + element.id + '_dropdown-option_' + element.value), element);

                    //Trigger onRender callback
                    if ($.isFunction(settings.onRender))
                        settings.onRender(element);
                },
                change: function (option, element) {
                    //Set the selected value in select element
                    $(element).val(option.data('value')).change();

                    //Trigger onChange callback
                    if ($.isFunction(settings.onChange))
                        settings.onChange(option, element);

                    //Close the dropdown
                    core.trigger.close(option, element);
                },
                close: function (option, element) {
                    //Remove open class to close the options
                    $('#' + element.id + '_dropdown').removeClass('open');

                    //Trigger onClose callback
                    if ($.isFunction(settings.onClose))
                        settings.onClose(option, element);
                }
            }
        };

        /********************
        INITIALIZE THE PLUGIN
        *********************/
        this.each(function () {
            core.init(this, settings);
        });

        //Expose private variables/methods
        this.selectOption = function (value) {
            var id = $(this).attr('id');
            $('#'+ id + '_dropdown-option_' + value).click();
        }
        this.disableOption = function (value) {
            var id = $(this).attr('id');
            core.row.disable(id, value);
        }

        this.enableOption = function (value) {
            var id = $(this).attr('id');
            $('#'+ id +' option[value="' + value + '"]').removeAttr("disabled");
            $('#'+ id + '_dropdown-option_' + value).removeClass('disabled');
        }

        this.reset = function () {
            core.destroy(this);
            core.init($(this)[0], settings);
        }

        this.destroy = function () {
            core.destroy(this);
        }

        return this;
    };
}(jQuery));


/*************************************
 CREATE DROWDOWN OPTIONS ON THE FLY
*************************************/
$(function () {
    //On clicking create
    $('body').on('click', 'a.dropdown-create', function(e){
        e.preventDefault();
        e.stopPropagation();
        //Load the form in iframe
        dropdownIframe.open(this);
    });

    //On closing an iframe
    $('body').on('click', '.iframe-close', function(e){
        e.preventDefault();
        e.stopPropagation();

        window.parent.dropdownIframe.close();
    });
});

//Function to connect iFrame with Dropdown
var dropdownIframe = {
    open: function (el) {
        var selectbox = $(el).closest('.dropdown').prev();
        $('body').append('<iframe class="dropdown-create-option" src="' + el.href + '" scrolling="yes" data-for="' + selectbox[0].id + '"></iframe>').addClass('has-iframe');
    },
    optionAdded: function(newselect) {
        //Get the dropdown reference from iFrame data-for
        var dd_id = $(".dropdown-create-option").data('for');
        //Remove all the existing options & optgroups
        $('#' + dd_id + ' > *').remove();
        //Copy all the options retrieved via AJAX
        $('#' + dd_id).append($(newselect).html());
        //Destroy existing dropdown instance
        page.dropdowns[dd_id+'_ref'].reset();
        //Close the iFrame
        this.close();
    },
    close: function() {
        $('body').removeClass('has-iframe');
        $('.dropdown-create-option').remove();
    }
}