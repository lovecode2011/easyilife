/**
 * Created by root on 15-8-19.
 */
$(function() {

    // 百度地图API功能
    var map = new BMap.Map("baidu-map");
    if (map_init) {
        map.centerAndZoom(new BMap.Point($('input[name=lng]').val(), $('input[name=lat]').val()), 20);
        var marker = new BMap.Marker(new BMap.Point($('input[name=lng]').val(), $('input[name=lat]').val()));
        map.addOverlay(marker);
    } else {
        map.centerAndZoom(new BMap.Point(116.404, 39.915), 11);

    }

    map.addEventListener("click", showInfo);
    map.addControl(new BMap.NavigationControl());
    map.addControl(new BMap.ScaleControl());
    map.addControl(new BMap.OverviewMapControl());
    map.addControl(new BMap.MapTypeControl());
    map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用

    function showInfo(e) {
        $('input[name=lng]').val(e.point.lng);
        $('input[name=lat]').val(e.point.lat);
        $('input[name=lng-show]').val(e.point.lng);
        $('input[name=lat-show]').val(e.point.lat);
        var marker = new BMap.Marker(new BMap.Point(e.point.lng, e.point.lat));
        map.clearOverlays();
        map.addOverlay(marker);
        //alert(e.point.lng + ", " + e.point.lat);
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
        console.log(address_kw);
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
        locate_city(city);

        $('#district').empty();
        var temp = '';
        for(var i = 0; i < districts.length; i++) {
            temp += '<option value="' + districts[i].id + '">' + districts[i].district_name + '</option>';
        }
        $('#district').append(temp);

        var district = $('#district').children(':selected').text();
        locate_city(district);
    });

    $('#city').change(function() {
        var city_id = $(this).val();
        var city = $(this).children(':selected').text();
        locate_city(city);
        var districts = data_districts[city_id];
        $('#district').empty();
        var temp = '';
        for(var i = 0; i < districts.length; i++) {
            temp += '<option value="' + districts[i].id + '">' + districts[i].district_name + '</option>';
        }
        $('#district').append(temp);
    });

    $('#district').change(function() {
        var district = $('#district').children(':selected').text();
        locate_city(district);
    });

    $('#address').blur(function() {
        var province = $('#province').children(':selected').text();
        var city = $('#city').children(':selected').text();
        var district = $('#district').children(':selected').text();
        var address_kw = province + city + district + $(this).val();
        locate_by_address(address_kw);
    })
});