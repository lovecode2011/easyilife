/**
 * Created by root on 15-8-19.
 */
$(function() {

    group_init();

    function group_init() {
        $('#group').empty();
        $('#group').css('display', 'none');
        var district_id = $('#district').children(':selected').val();
        var groups = data_groups[district_id];
        if (groups) {
            $('#group').css('display', 'block');
            var temp = '<option value="0">全部</option>';
            for (var i = 0; i < groups.length; i++) {
                temp += '<option value="' + groups[i].id + '"';
                if( selected_group == groups[i].id ) {
                    temp += ' selected="selected"';
                }
                temp += '>';
                temp += groups[i].group_name + '</option>';
            }
            $('#group').empty();
            $('#group').append(temp);
            $('#group').css('display', 'inline');
        }
    }




    //城市、地区改变地图相应定位
    function locate_city(city) {
        if (city != "") {
            map.centerAndZoom(city, 15);      // 用城市名设置地图中心点
        }
    }

    function locate_by_address(address_kw) {
        var local = new BMap.LocalSearch(map, {
            renderOptions: {map: map}
        });
        local.search(address_kw);
    }


    //省市区三级联动
    var province_id = $('#province').val();
    var city_id = $('#city').val();
    var district_id = $('#district').val();
    $('#city').empty();
    $('#district').empty();

    var cities = data_cities[province_id];
    var districts = data_districts[city_id];

    var temp = '';
    for(var i = 0; i < cities.length; i++) {
        if( city_id == cities[i].id ) {
            temp += '<option value="' + cities[i].id + '" selected="selected">' + cities[i].city_name + '</option>';
        } else {
            temp += '<option value="' + cities[i].id + '">' + cities[i].city_name + '</option>';
        }
    }
    $('#city').append(temp);

    temp = '';
    for(var i = 0; i < districts.length; i++) {
        if( district_id == districts[i].id ) {
            temp += '<option value="' + districts[i].id + '" selected="selected">' + districts[i].district_name + '</option>';
        } else {
            temp += '<option value="' + districts[i].id + '">' + districts[i].district_name + '</option>';
        }
    }
    $('#district').append(temp);

    $('#province').change(function() {
        var province_id = $(this).val();
        var cities = data_cities[province_id];
        $('#city').empty();
        var temp = '';
        for(var i = 0; i < cities.length; i++) {
            temp += '<option value="' + cities[i].id + '">' + cities[i].city_name + '</option>';
        }
        $('#city').append(temp);
        var city_id = $(this).val();
        var districts = data_districts[city_id];

        var city = $('#city').children(':selected').text();

        $('#district').empty();
        var temp = '';
        for(var i = 0; i < districts.length; i++) {
            temp += '<option value="' + districts[i].id + '">' + districts[i].district_name + '</option>';
        }
        $('#district').append(temp);

        var district = $('#district').children(':selected').text();
        $('#group').empty();
        $('#group').css('display', 'none');
    });

    $('#city').change(function() {
        var city_id = $(this).val();
        var city = $(this).children(':selected').text();
        var districts = data_districts[city_id];
        $('#district').empty();
        var temp = '';
        for(var i = 0; i < districts.length; i++) {
            temp += '<option value="' + districts[i].id + '">' + districts[i].district_name + '</option>';
        }
        $('#district').append(temp);
        $('#group').empty();
        $('#group').css('display', 'none');

        var district_id = $(this).children(':selected').val();
        var groups = data_groups[district_id];
        if( groups ) {
            $('#group').css('display', 'block');
            var temp = '<option value="0">全部</option>';
            for(var i = 0; i < groups.length; i++ ) {
                temp += '<option value="'+ groups[i].id +'">' + groups[i].group_name + '</option>';
            }
            $('#group').empty();
            $('#group').append(temp);
            $('#group').css('display', 'inline');
        }

    });

    $('#district').change(function() {
        var district_id = $(this).val();
        var district = $(this).children(':selected').text();
        var groups = data_groups[district_id];
        if( groups ) {
            $('#group').css('display', 'block');
            var temp = '<option value="0">全部</option>';
            for(var i = 0; i < groups.length; i++ ) {
                temp += '<option value="'+ groups[i].id +'">' + groups[i].group_name + '</option>';
            }
            $('#group').empty();
            $('#group').append(temp);
            $('#group').css('display', 'inline');
        } else {
            $('#group').empty();
            $('#group').css('display', 'none');
        }

    });

    $('#address').blur(function() {
        var province = $('#province').children(':selected').text();
        var city = $('#city').children(':selected').text();
        var district = $('#district').children(':selected').text();
        var address_kw = province + city + district + $(this).val();
    })
});