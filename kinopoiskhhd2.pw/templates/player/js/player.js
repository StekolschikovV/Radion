// ТЗ

function onPlay() {
    console.log('onPlay()');
}
$('.progressBar').click(function () {
    console.log('onSeek()');
});


function onTime() {
    console.log('onTime()');
}

// + webm
$('video source').each(function(num,val){
    var src = '' + $(this).attr('src');
    src = src.substring(0, src.length - 3);
    var newSrc = src + 'webm';
    $("#player").append('<source src="' + newSrc + '" />');
});

// настройки
var MAX_TIME = 60;
var VOLUME_LINE_TOGGLE = 'HIDE';
var FULL_VOLUME_LINE = 0;
var FULL_VOLUME_LINE_ONE_PROCENT = 0;
var FAKE_FULL_TIME = 0;
var FAKE_FULL_TIME_WIDTH_ONE_PROCENT = 0;
var FAKE_FULL_TIME_WIDTH = 0;
var PLAY = false;
var FULL_SCREEN_STATUS = 'MIN';

// подключение видео
var vid = document.getElementById("player");
vid.controls = false;

// получение информации
function getStartInf() {
    FAKE_FULL_TIME = 4320;
    FULL_TIME_WIDTH = $(".defaultBar").width();
    FULL_TIME_WIDTH_ONE_PROCENT = FULL_TIME_WIDTH/100;
    FAKE_FULL_TIME_WIDTH_ONE_PROCENT = FAKE_FULL_TIME/100;
    FULL_VOLUME_LINE =  $(".line").height();
    FULL_VOLUME_LINE_ONE_PROCENT = FULL_VOLUME_LINE/100;
    $('.videoBlock').height($('video').width()/1.78)
    // console.log($('video').height())
}

$( document ).ready(function() { getStartInf() });
$(window).resize(getStartInf);

// установка иконки полного экрана
function setFullScreenIcon() {
    if (FULL_SCREEN_STATUS == 'MIN'){
        FULL_SCREEN_STATUS = 'FULL';
        $(".fullScreenBtn").hide();
        $(".fullScreenBtn i").removeClass('fa-expand');
        $(".fullScreenBtn i").addClass('fa-compress');
        $(".fullScreenBtn").show();
    } else{
        FULL_SCREEN_STATUS = 'MIN';
        $(".fullScreenBtn").hide();
        $(".fullScreenBtn i").removeClass('fa-compress');
        $(".fullScreenBtn i").addClass('fa-expand');
        $(".fullScreenBtn").show();
    }
}

// ВРЕМЯ // // // // // // //
// установка ползунка времени
function setPlayBarWidth(width) {
    $('.playBar').width(width/10 + '%');
}

// подсчет сек
function sec2time(sec){
    var h = sec/3600 ^ 0 ;
    var m = (sec-h*3600)/60 ^ 0 ;
    var s = sec-h*3600-m*60 ;
    return (h<10?"0"+h:h)+":"+(m<10?"0"+m:m)+":"+(s<10?"0"+s:s)
}
// установка оставшегося времени
function setLasrTime() {
    var currentTime = parseInt(vid.currentTime);
    var now = FAKE_FULL_TIME - currentTime;
    now=sec2time(now);
    $('.fullTime').text(now)
}

// FullScreen // // // // // // //
// // установка иконки полного экрана
function setFullScreenIcon() {
    if (FULL_SCREEN_STATUS == 'MIN'){
        FULL_SCREEN_STATUS = 'FULL';
        $(".fullScreenBtn").hide();
        $(".fullScreenBtn i").removeClass('fa-expand');
        $(".fullScreenBtn i").addClass('fa-compress');
        $(".fullScreenBtn").show();
    } else{
        FULL_SCREEN_STATUS = 'MIN';
        $(".fullScreenBtn").hide();
        $(".fullScreenBtn i").removeClass('fa-compress');
        $(".fullScreenBtn i").addClass('fa-expand');
        $(".fullScreenBtn").show();
    }
}
// кнопка FullScreen
function fn() {
    $('.videoBlock').toggleClass('videoBlockFull');
    setFullScreenIcon()
}
$(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', fn);
$('.fullScreenBtn').click(function () {
    screenfull.toggle($('.videoBlock')[0]);
});

// ГРОМКОСТЬ // // // // // // //
// показать/скрыть громкость
function toggleVolumeLine() {
    if (VOLUME_LINE_TOGGLE == ''){
        VOLUME_LINE_TOGGLE = 'HIDE';
    } else if (VOLUME_LINE_TOGGLE == 'HIDE'){
        VOLUME_LINE_TOGGLE = 'SHOW';
        $('.line').addClass('lineShow');
    } else if (VOLUME_LINE_TOGGLE == 'SHOW'){
        VOLUME_LINE_TOGGLE = 'HIDE';
        $('.line').removeClass('lineShow');
    }
}
// громкость
$('.volume i').click(function () {
    toggleVolumeLine();
});
// установка громкости
function setVolume(v) {
    // vid.volume = 1 - v.toFixed(1)
    // console.log(v.toFixed(1));
    vid.volume = 1 - v.toFixed(1);
}
// установка ползунка громкости
function setVolumeHeight(height) {
    $('.setVolumeLine').height(100 - height + '%');
}
setVolumeHeight(0);
// установка ползунка громкости пользователем
$(".volume .line").click(function(e){
    var offset = $(this).offset();
    var x = e.pageY - offset.top;
    var result =  x / FULL_VOLUME_LINE;
    setVolumeHeight( result * 100);
    setVolume(result)
});

// ПЛЕЙ // // // // // // //
// установка иконки плей
function setPalyIcon(s) {
    if (s == 'play'){
        $(".playAndPauseBtn").hide();
        $(".playAndPauseBtn i").removeClass('fa-play');
        $(".playAndPauseBtn i").addClass('fa-pause');
        $(".playAndPauseBtn").show();
    } else if(s == 'pause') {
        $(".playAndPauseBtn").hide();
        $(".playAndPauseBtn i").removeClass('fa-pause');
        $(".playAndPauseBtn i").addClass('fa-play');
        $(".playAndPauseBtn").show();
    }
}

$('.playAndPauseBtn, .videoBlock #player, .pausSreen').on("click", function () {
    onPlay();
    if(PLAY == true){
        vid.pause();
        PLAY = false;
        setPalyIcon('pause');
        $('.pausSreen').addClass('pausSreenShow');
    } else if(PLAY == false){
        vid.play();
        PLAY = true;
        setPalyIcon('play');
        $('.pausSreen').removeClass('pausSreenShow');
    }
});

function watcher() {
    if(vid.currentTime >= MAX_TIME){
        vid.pause();
        vid.currentTime = 0;
        PLAY = false;
        setPalyIcon('pause');
        $('.pausSreen').addClass('pausSreenShow');
        onTime();
    }
    setPlayBarWidth(vid.currentTime/10);
    setLasrTime();
    if(1){
        // console.log('WATCHET');
        clearInterval(watcher);
        setTimeout(watcher, 1000);
    }
}

// клик на постере
$('.poster').click(function () {
    $('.poster').addClass('display-none');
    vid.load();
    setPalyIcon('play');
    PLAY = true;
    onPlay();
    function w(){
        $('.preloader').addClass('display-none');
        vid.play();
        watcher();
    }
    clearInterval(w);
    setTimeout(w, 1000);
});
