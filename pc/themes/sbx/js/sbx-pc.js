/**
 * Created by asus on 2015/10/26.
 */
$(function(){
    $(".topbar-cart").hover(
        function(){
        $(".cart-mini").css({"color":"#ffce28","background":"#fff"});
        $(".cart-menu").slideDown(200);

    },
        function(){
            $(".cart-mini").css({"color":"#b0b0b0","background":"#424242"});
            $(".cart-menu").slideUp(200);
    }
    );

    $(".search-select").click(function(){
        $(".search-select-item").toggle();
    });
    $(".search-select-item a").click(function(){
        $(".search-select span").text($(this).html());
        $(".search-select-item").hide();
    });

    $(".dd-inner .item").hover(
        function(){
            $(this).addClass("hover");
            $(this).find(".sub-item").show();
        },
        function(){
            $(".dd-inner .item").removeClass("hover");
            $(".dd-inner .item").find(".sub-item").hide();
        }
    );

    $(".categorys").hover(function(){
        $(".inner").show();
    },function(){
        $(".inner").hide();
    });

    //搜索结果上下箭头js有误
    $(".f-sort a").click(function(){
        $(".f-sort a").removeClass("curr");
        $(this).addClass("curr");
        if($(this).children().hasClass("icon")){
            if(($(this).children().html()=="") || ($(this).children().html()=="&#xe608;")){
                $(this).children().html("&#xe607;");
            }else{
                $(this).children().html("&#xe608;");
            };
        }else{
            $(".f-sort a .icon").html("");
        }

    });

});


