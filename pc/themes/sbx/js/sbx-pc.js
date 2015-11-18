/**
 * Created by arao on 2015/10/26.
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

    /* 搜索结果价格排序 */
    $(".f-sort a").click(function(){
        $(".f-sort a").removeClass("curr");
        $(this).addClass("curr");
        if($(this).children().hasClass("icon") && $(this).find(".up").is(":hidden")){
           $(this).find(".up").show();$(this).find(".down").hide();
        }else{
            $(this).find(".up").hide();$(this).find(".down").show();
        }
    });

    /* 产品详情侧栏店铺分类 */
    $(".aside-box-c dt i").click(function(){
        if($(this).hasClass("hide")){
            $(this).hide();
            $(this).siblings().show();
            $(this).parent().siblings().show();
        }else{
            $(this).hide();
            $(this).siblings().show();
            $(this).parent().siblings().hide();
        }
    });

    /* 产品展示与评论tabs切换 */
    $("#comment_tab").click(function(){
        $(this).addClass("on");
        $("#detail_tab").removeClass("on");
        $(".pro-connent").hide();
        $(".pro-comment").show();
    });
    $("#detail_tab").click(function(){
        $(this).addClass("on");
        $("#comment_tab").removeClass("on");
        $(".pro-connent").show();
        $(".pro-comment").hide();
    });

    /* 个人中心侧栏 开始 */
    $(".home-nav dt i.m").click(function(){
        $(this).hide();
        $(this).siblings().show();
        $(this).parent().siblings().hide();
    });
    $(".home-nav dt i.p").click(function(){
        $(this).hide();
        $(this).siblings().show();
        $(this).parent().siblings().show();
    });
    $(".home-nav dl a").click(function(){
        $(".home-nav dl a").removeClass("hover");
        $(this).addClass("hover");
    });

    /* 头像hover显示编辑头像 */
    $(".user-photo-inner a").hover(function(){
        $(".user-photo-inner .edit-box").toggle();
    });

    /* 我的订单 删除 */
    $(".order-tb tbody").hover(function(){
        $(this).find(".tr-th .order-del").toggle();
    });

    /* 收货地址 */
    $(".address .address-item").hover(function(){
        $(this).find(".addr-con .addr-btn").toggle();
    });

    /*确认订单*/

    //收货地址鼠标hover 效果
    $(".confirm-addr .address-item").hover(function(){
        $(this).addClass("addr-hover");
    },function(){
        $(this).removeClass("addr-hover");
    });

    //收货地址 选择确认效果
    $(".confirm-addr .address-item").click(function(){
        $(".confirm-addr .address-item").removeClass("addr-active");
        $(this).addClass("addr-active");
    });

    //收货地址 收货地址多于三个显示更多
    if($(".confirm-addr .address-item").siblings().length > 3){
        $(".confirm-addr .address-item").eq(2).nextAll().hide();
        $(".addr-confirm-btn .switch-on").show();
        $(".addr-confirm-btn .switch-on").click(function(){
            $(".confirm-addr .address-item").eq(2).nextAll().show();
            $(".addr-confirm-btn .switch-on").hide();
            $(".addr-confirm-btn .switch-off").show();
        });
        $(".addr-confirm-btn .switch-off").click(function(){
            $(".confirm-addr .address-item").eq(2).nextAll().hide();
            $(".addr-confirm-btn .switch-on").show();
            $(".addr-confirm-btn .switch-off").hide();
        });
    };

    //选择支付方式
    $(".select-pay-type li").click(function(){
        $(".select-pay-type li").removeClass("hover");
        $(this).addClass("hover");
    });

    //登陆页
    $('.login input').bind({
        focus:function(){
            $(this).parent().addClass("focus");
        },
        blur:function(){
            $(this).parent().removeClass("focus");
        }
    });

    /* 头部购物车删除商品 */
    $(".topbar .cart-item ").hover(function(){
        $(this).find(".btn-del").toggle();
    });

});


