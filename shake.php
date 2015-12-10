<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>摇一摇</title>
<style>
<!--
body
{
    background: #333;
}
.shake-icon
{
    display: block;
    margin: 120px auto;
    width: 50%;
}
.shake-icon img
{
    width: 100%;
}
.shake-icon p
{
    color: #fff;
    text-align: center;
    font-size: 1rem;
    line-height: 1.6rem;
}
-->
</style>
</head>
<body onload="init()">
    <div class="shake-icon">
        <img src="css/images/shake.png"/>
        <p id="notice">用力摇一摇你的手机</p>
    </div>
</body>
<script type="text/javascript">
var SHAKE_THRESHOLD = 1000;
var last_update = 0;
var x = y = z = last_x = last_y = last_z = 0;
var slogan = [
    "再大力！",
    "再大力，再大力！",
    "快点摇啊，别停！",
    "快点摇，快点摇！",
    "看灰机~"
];

function get_slogan() {
    var size = slogan.length;

    var i = Math.random()*size;
    i = parseInt(i);

    if(slogan[i] == undefined) {
        i = size - 1;
    }

    return slogan[i];
}

function init() {
    if (window.DeviceMotionEvent) {
        window.addEventListener('devicemotion', deviceMotionHandler, false);
    } else {
        alert('您的手机不支持摇一摇功能');
    }
}

function deviceMotionHandler(eventData) {
    var acceleration = eventData.accelerationIncludingGravity;
    var curTime = new Date().getTime();
    if ((curTime - last_update) > 100) {
        var diffTime = curTime - last_update;
        last_update = curTime;
        x = acceleration.x;
        y = acceleration.y;
        z = acceleration.z;
        var speed = Math.abs(x + y + z - last_x - last_y - last_z) / diffTime * 10000;
        if (speed > SHAKE_THRESHOLD) {
            //shaking
            document.getElementById("notice").innerHTML = get_slogan();
        }
        last_x = x;
        last_y = y;
        last_z = z;
    }
}
</script>
</html>
