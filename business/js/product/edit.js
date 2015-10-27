/**
 * Created by root on 15-8-27.
 */
var attr_list;
var index = 0;
var editing = 0;
var current_type = 0;

function init_inventory() {
    do_get_attributes();
    console.log(attr_list);
    if( attr_list ) {
        for( var i = 0; i < attr_list.length; i++ ) {

        }
    }

}

//检查待发库存，如果都为0，允许修改，
function do_product_type_change() {
    if ($('#product_type').val() == 0) {
        if ($('#product_attr').is(':visible')) {
            $('#product_attr').slideUp(function() {
                $('#show-attr').empty();
                $('#product_attr').empty();
            });
        }
        return;
    } else {
        if( current_type == $('#product_type').val() ) {
            //空属性库存初始化
            if( attributes.length == 1 && !attributes[0].attributes && attributes[0].inventory_await == 0 ) {
                $('#product_attr').slideUp(function() {
                    $('#show-attr').empty();
                    $('#show-attr').slideUp();
                    $('#product_attr').empty();
                    var temp = '<label class="l-title">产品属性：</label>';
                    temp += ' <input style="cursor: pointer;" class="text-input" type="button" id="add-attr" value="增加属性" />';
                    temp += '<p class="clear" id="show-attr"></p>';
                    $('#product_attr').append(temp);
                    $('#product_attr').slideDown();
                    $('input[name=single_inventory]').removeAttr('disabled');
                });
                return;
            }
            //非空属性库存初始化
            init_inventory();
        } else {
            $('#product_attr').slideUp(function() {
                $('#show-attr').empty();
                $('#show-attr').slideUp();
                $('#product_attr').empty();
                var temp = '<label class="l-title">产品属性：</label>';
                temp += ' <input style="cursor: pointer;" class="text-input" type="button" id="add-attr" value="增加属性" />';
                temp += '<p class="clear" id="show-attr"></p>';
                $('#product_attr').append(temp);
                $('#product_attr').slideDown();
                $('input[name=single_inventory]').removeAttr('disabled');
            });

        }

    }
}

//更换类型时获取分类的属性
function do_get_attributes() {
    var type_id = $('#product_type').val();
    if( 0 >= type_id ) {
        return;
    }
    var url = 'ajax.php';
    var params = {'opera':'get_attr', 'id':type_id};
    $.post(url, params, do_get_attributes_response, 'json');
}

//删除库存，删除前必须确认是否可以删除：1、空属性，有待发；2、空属性，无待发；3、非空属性，有待发；4、非空属性，无待发
function do_remove_attr() {

}

//添加属性，空库存且有待发，不能添加
function do_add_attr() {

}

//编辑属性，无限制
function do_edit_attr() {

}

//无更换类型，随便加；有更换类型，检查待发，删除原来所有
function do_confirm() {

}

//编辑属性，无限制
function do_edit_confirm() {

}

function do_confirm_response() {

}

function do_edit_confirm_response() {

}

function do_remove_attr_response() {

}

function do_get_attributes_response(result) {
    if( result.error == 0 ) {
        console.log(attr_list);
        attr_list = result.data;
    } else {
        alert(result.message);
        return;
    }
}
