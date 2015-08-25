/**
 * Created by root on 15-8-25.
 */
$(function() {


    //$('#product_type').change(function() {
    //    if( $(this).val() == 0 ) {
    //        if( $('#product_attr').is(':visible') ) {
    //            $('#product_attr').slideUp();
    //        }
    //        return;
    //    } else {
    //        if( $('#product_attr').is(':visible') ) {
    //            $('#product_attr').slideUp(function() {
    //                $('#product_attr').empty();
    //                var temp = '<label class="l-title">产品属性：</label><p class="clear"></p>';
    //                var pid = $('#product_type').val();
    //                for(var i = 0; i < data_product_attr[pid].length; i++ ) {
    //                    temp += '<p style="display: inline-block">';
    //                    temp += '<label class="l-title">' + data_product_attr[pid][i].name + '</label>';
    //                    switch( data_product_attr[pid][i].type ) {
    //                        case 'text':
    //                            temp += '<input type="text" class="text-input w50" name="attr[' + data_product_attr[pid][i].id + ']"/>';
    //                            break;
    //                        case 'select':
    //                            temp += '<select name="attr[' + data_product_attr[pid][i].id + ']" class="w100">';
    //                            var options = data_product_attr[pid][i].options.split(',');
    //                            for( var j = 0; j < options.length; j++ ) {
    //                                temp += '<option value="' + options[j] + '">' + options[j] + '</option>';
    //                            }
    //                            temp += '</select>';
    //                            break;
    //                    }
    //                    temp += '</p>';
    //                }
    //
    //                $('#product_attr').append(temp);
    //                $('#product_attr').slideDown();
    //            });
    //        } else {

    //        }
    //
    //    }
    //});

    $('#product_type').change(function() {
        if ($(this).val() == 0) {
            if ($('#product_attr').is(':visible')) {
                $('#product_attr').slideUp(function() {
                    $(this).empty();
                });
            }
            return;
        } else {
            $('#product_attr').slideUp(function() {
                $(this).empty();
                var temp = '<label class="l-title">产品属性：</label>';
                temp += ' <input style="cursor: pointer;" class="text-input" type="button" id="add-attr" value="增加属性" />';
                $(this).append(temp);
                $(this).slideDown();

                $('#add-attr').click(function() {

                    $('.modal-body').empty();
                    var pid = $('#product_type').val();
                    temp = '';
                    for(var i = 0; i < data_product_attr[pid].length; i++ ) {
                        temp += '<p style="display: inline-block">';
                        temp += '<label class="l-title">' + data_product_attr[pid][i].name + '</label>';
                        var required = '';
                        if( data_product_attr[pid][i].needle == 1 ) {
                           required = 'required';
                        }
                        switch( data_product_attr[pid][i].type ) {
                            case 'text':
                                temp += '<input type="text" class="text-input w50 ' + required + '" name="attr[' + data_product_attr[pid][i].id + ']"/>';
                                break;
                            case 'select':
                                temp += '<select name="attr[' + data_product_attr[pid][i].id + ']" class="w100">';
                                var options = data_product_attr[pid][i].options.split(',');
                                for( var j = 0; j < options.length; j++ ) {
                                    temp += '<option value="' + options[j] + '">' + options[j] + '</option>';
                                }
                                temp += '</select>';
                                break;
                        }
                        temp += '</p>';
                    }

                    $('.modal-body').append(temp);

                    $('#modal-product-attr').modal({
                        clickClose: false,
                        showClose: false,
                        width: 800,
                        height: 225
                    });

                    $('.required').focus(function() {
                        if( $(this).css('color') == '#ff0000' ) {
                            $(this).val('');
                        }
                        $(this).css('border', '1px rgb(213,213,213) solid');
                        $(this).css('color', '#000000');
                    });

                    $('#confirm').click(function() {
                        var flag = true;
                        $('.required').each(function() {
                            if( '' == $(this).val() ) {
                                $(this).css('border', '1px #ff0000 solid');
                                $(this).css('color', '#ff0000');
                                $(this).val('必填项');
                                flag = false;
                            }
                        });
                        if( flag ) {
                            $.modal.close();
                        }

                    });
                });

            });
        }
    });


});
