$(document).ready(function(){
	// Запуск регистрации
	$(".registration").click(function() {
		$.ajax({
			type: "POST",
			url: "/frames.php?type=registration",
			success:function(msg){
				/*if($("#bghide").val() != "noimage"){
					setTimeout(function(){$(".fancybox-overlay").css("background","url("+$("#bghide").val()+")");$(".fancybox-overlay").css("z-index","99999");},100);
				}*/
				$.fancybox(msg,{
					'padding':0,
					'width':525
				});
			}
		});
	});
	
	$("#popup-trailer").click(function() {
		$.ajax({
			type: "POST",
			url: "/frames.php?type=trailer-register&urlpreview="+$(this).attr("alt")+"&title="+$("#title_movie").html()+'&poster='+$("#ajaxposter").val(),
			success:function(msg){
				/*if($("#bghide").val() != "noimage"){
					setTimeout(function(){$(".fancybox-overlay").css("background","url("+$("#bghide").val()+")");$(".fancybox-overlay").css("z-index","99999");},100);
				}*/
				$.fancybox(msg,{
					'padding':0,
					'width':525
				});
			}
		});
	});

	//Окно трейлера
	$(".popup-trailer").click(function() {
		$.ajax({
			type: "POST",
			url: "/frames.php?type=trailer&urlpreview="+$(this).attr("alt")+"&title="+$("#title_movie").html()+'&poster='+$("#ajaxposter").val(),
			success:function(msg){
				/*if($("#bghide").val() != "noimage"){
					setTimeout(function(){$(".fancybox-overlay").css("background","url("+$("#bghide").val()+")");$(".fancybox-overlay").css("z-index","99999");},100);
				}*/
				$.fancybox(msg,{
					'padding':0,
					'width':525
				});
			}
		});
	});

	// Fancybox Images
	$(".fancybox").click(function() {
		$.fancybox('<img src="'+$(this).attr("src")+'" />');
	});

	//Форма скачивания
	$(".download").click(function() {
		$.ajax({
			type: "POST",
			url: "/frames.php?type=download&load_id="+$("#download-link").attr("alt"),
			success:function(msg){
				/*if($("#bghide").val() != "noimage"){
					setTimeout(function(){$(".fancybox-overlay").css("background","url("+$("#bghide").val()+")");$(".fancybox-overlay").css("z-index","99999");},100);
				}*/
				$.fancybox(msg,{
					'padding':0,
					'width':525
				});
			}
		});
	});

	// Отзывы
	$(".site-reviews").click(function() {
		$.ajax({
			type: "POST",
			url: "/frames.php?type=reviews",
			success:function(msg){
				/*if($("#bghide").val() != "noimage"){
					setTimeout(function(){$(".fancybox-overlay").css("background","url("+$("#bghide").val()+")");$(".fancybox-overlay").css("z-index","99999");},100);
				}*/
				$.fancybox(msg,{
					'padding':0,
					'width':525
				});
			}
		});
	});

	// Поддержка
	$(".support").click(function() {
		$.ajax({
			type: "POST",
			url: "/frames.php?type=support",
			success:function(msg){
				/*if($("#bghide").val() != "noimage"){
					setTimeout(function(){$(".fancybox-overlay").css("background","url("+$("#bghide").val()+")");$(".fancybox-overlay").css("z-index","99999");},100);
				}*/
				$.fancybox(msg,{
					'padding':0,
					'width':525
				});
			}
		});
	});

	// Инициализация звука на первом шаге
	$(".initializeaudio").on("click", function(){
		setTimeout(function(){
			$("#pluginRegister").get(0).contentWindow.InitAudioTags();
		}, 1500);
	});

	$('#perpage').val("12");
	// Подгрузка контента
	$( "#load_more" ).click(function() {
		$("#load_more").html('<i class="fa fa-spinner fa-spin"></i> Загружаем...');
		var inProgress = false;
		var startFrom = $('#perpage').val();
		$.ajax({
			url:    	'/loadMore.php',
			type:		'POST',
			cache: 		false,
			data:  		{'startFrom':startFrom},
			dataType: 	'html',
			success: function(data) {
				data = jQuery.parseJSON(data);
				if(data == 'error'){
					$(".videos-list").append('<i class="fa fa-info-circle"></i> Достигнут предел сериалов');
					$( ".loadMore" ).css("display","none");
				} else {
					if (data.length > 0) {                
						$.each(data, function(index, data){ 
							//$(".videos-list").append('<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 posterBlock" style="cursor:pointer" onclick="location.href=\'/serial/view/' + data.last_id + '-' + data.link + '.html\'"><div class="poster"><img src="http://images1.pw/i2/' + data.last_id + '/' + data.poster_url + '"  class="loadPoster"></div><div class="posterInfoBlock"><a href="/serial/view/' + data.last_id + '-' + data.link + '.html"" class="' + data.name + '">' + data.name + '</a></div></div>');
							//$(".videos-list").append('<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 posterBlock" style="cursor:pointer" onclick="location.href=\'/serial/view/' + data.last_id + '-' + data.link + '.html\'"><div class="poster"><img src="http://images1.pw/i2/' + data.last_id + '/' + data.last_poster + '"  class="loadPoster"></div><div class="posterInfoBlock"><a href="/serial/view/' + data.last_id + '-' + data.link + '.html"" class="' + data.name + '">' + data.name + '</a></div></div>');
							$(".watch-movie").append('<a href="/serial/view/'+data.last_id+'-' + data.link + '.html" class="available-to-view-film-block"><div class="available-to-view-film-img"><img src="http://imagees2.pw/i2/' + data.last_id + '/' + data.last_poster + '" alt="' + data.name + '"></div><div class="available-to-view-film-title">' + data.name + '</div></a>');
						});
						inProgress = false;
						var perPage = +$('#perpage').val()+8;
						$('#perpage').val(perPage);	
						$("#load_more").html('Загрузить ещё');
					}
				}
			}
		});
	});
	

	// Функция подгрузки контента в категории по клику ( пагинация )	
	$("#loadmorecategory").click(function() {
		$("#loadmorecategory").html('<i class="fa fa-spinner fa-spin"></i> Загружаем...');
		var inProgress = false;
		var categorypage = $("#currentUrl").val();
		var startFrom = $('#perpage').val();
		$.ajax({
			url:    	'/loadMoreCategory.php',
			type:		'POST',
			cache: 		false,
			data:  		{'startFrom':startFrom,'categorypage':categorypage},
			dataType: 	'html',
			success: function(data) {
    			data = jQuery.parseJSON(data);
    			if(data == 'error'){
    				$(".videos-list").append('<i class="fa fa-info-circle"></i> Достигнут предел сериалов');
    				$( ".loadMore" ).css("display","none");
    			} else {
    				if (data.length > 0) {                
    					$.each(data, function(index, data){ 
    						//$(".videos-list").append('<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 posterBlock" style="cursor:pointer" onclick="location.href=\'/serial/view/' + data.last_id + '-' + data.link + '.html\'"><div class="poster"><img src="http://images1.pw/i2/' + data.last_id + '/' + data.poster_url + '"  class="loadPoster"></div><div class="posterInfoBlock"><a href="/serial/view/' + data.last_id + '-' + data.link + '.html"" class="' + data.name + '">' + data.name + '</a></div></div>');
    						//$(".videos-list").append('<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 posterBlock" style="cursor:pointer" onclick="location.href=\'/serial/view/' + data.findId + '-' + data.link + '.html\'"><div class="poster"><img src="http://images1.pw/i2/' + data.findId + '/' + data.last_poster + '"  class="loadPoster"></div><div class="posterInfoBlock"><a href="/serial/view/' + data.findId + '-' + data.link + '.html"" class="' + data.name + '">' + data.name + '</a></div></div>');
    						$(".watch-movie").append('<a href="/serial/view/'+data.findId+'-' + data.link + '.html" class="available-to-view-film-block"><div class="available-to-view-film-img"><img src="http://imagees2.pw/i2/' + data.findId + '/' + data.last_poster + '" alt="' + data.name + '"></div><div class="available-to-view-film-title">' + data.name + '</div></a>');
    					});
    					inProgress = false;
    					var perPage = +$('#perpage').val()+8;
    					$('#perpage').val(perPage);
    					$("#loadmorecategory").html('Загрузить ещё');
    				}	
    			}
    		}
		});
	});

	// Живой поиск по контенту
	/*$("#live_search").keyup(function(){
		var search = $("#live_search").val();

		if(search.length > 3){
			$(".searchRes").removeClass("displayNone");
			$.ajax({
				type: "POST",
				url: "/ajax.search.php",
				data: {"search": search},
				cache: false,                                 
				success: function(response){
					$("#resSearch").html(response);
				}
			});
		} else {
			$(".searchRes").addClass("displayNone");
		}
		return false;
	});*/
	$( ".menu input" ).on('keyup', function(){
        var search = $( ".menu input" ).val();
        if(search.length > 3){
              $.ajax({
                type: "POST",
                url: "/ajax.search.php",
                data: {"search": search},
                cache: false,                                 
                success: function(response){
                   $(".hover-search").html(response);
                }
            });
        }
      }); 

	$('#btns-btn-1, #btns-btn-2, #btns-btn-3, #btns-btn-4').click(function(){
		var t = new Date(),
		    id = 'datip' + t.getTime();
		$('#btns-cont').append('<div id="' + id + '" class="tooltipec">Аккаунт еще<br />не активирован</div>');
		$('#' + id).css({marginLeft : $(this).css('marginLeft')});
		setTimeout((function(id){
			return function(){
            	$('#' + id).animate({opacity : 0}, 500, '', function(){ $(this).remove(); })
            }
		})(id), 2000);
	});

	$('#btns-btn-11, #btns-btn-22, #btns-btn-33, #btns-btn-44').click(function(){
		var t = new Date(),
		    id = 'datip' + t.getTime();
		$('#btns-cont-2').append('<div id="' + id + '" class="tooltipec">Аккаунт еще<br />не активирован</div>');
		$('#' + id).css({marginLeft : $(this).css('marginLeft')});
		setTimeout((function(id){
			return function(){
            	$('#' + id).animate({opacity : 0}, 500, '', function(){ $(this).remove(); })
            }
		})(id), 2000);
	})

});

function logout(){
	var logout = 1;
	$.ajax({
		url:    	'/functions.php',
	    type:		'POST',
	    cache: 		false,
	    data:  		{'logout':logout},
	    dataType: 	'html',
	    success: function(data) {
			location.reload();
	    }
	});
}

/***** SCRIPT OLD FUNCTION ***********/
// load top img
if($("*").is(".video-page-img")){
  ScreenWidth = screen.width;
  if(ScreenWidth > 982){
    $('<img src="img/top.jpg" class="top-img">').appendTo($(".video-page-img"));
  }
}
if($("*").is(".i-video-page-img")){
  ScreenWidth = screen.width;
  if(ScreenWidth > 751){
      $('<div class="i-top-img"><div class="bg"></div></div>').appendTo($(".i-video-page-img"));
  }
}
//\ load top img

// stars-info-block

var starNow = 0;

function star(e) {
    i = 0
    while(true){
        if(e === i){
            if(parseInt(e) === e){
                $(".star-" + e).html('<i class="fa fa-star" aria-hidden="true"></i>');
            }
            else {
                $(".star-" + (e + 0.5)).html('<i class="fa fa-star-half-o" aria-hidden="true"></i>');
            }
        }
        if((e + 0.5) < i) {
            if(parseInt(i) === i){
                $(".star-" + i).html('<i class="fa fa-star-o" aria-hidden="true"></i>');
            }
        }
        if((i) < e) {
            if(parseInt(i) === i){
                $(".star-" + i).html('<i class="fa fa-star" aria-hidden="true"></i>');
            }
        }
        $(".stars-sect").html(e);
        i = i + 0.5;
        if (i > 10){
            break;
        }
    }
    starNow = e;
}

$('.stars-block').hover(
  function() {
    for (i=0; i<11; i++){
      if($( this ).data( "star" )  == i){
        $(".star-" + i).html('<i class="fa fa-star" aria-hidden="true"></i>');
      }
      if($( this ).data( "star" ) > i){
        $(".star-" + i).html('<i class="fa fa-star" aria-hidden="true"></i>');
      }
      if($( this ).data( "star" ) < i){
        $(".star-" + i).html('<i class="fa fa-star-o" aria-hidden="true"></i>');
      }
    }
  }, function() {
      star(starNow)
  }
);

// \stars-info-block

// big frames
function bigFrame(e) {
    $(".big-frames-block").fadeIn();
    /*$(".big-frames-block").html('<img src="img/' + e + '.jpg">');*/
}
$(".big-frames-block").click(function () {
    $(".big-frames-block").fadeOut();
})
// \big frames

// genres menu
$( ".video-genres-cont" ).fadeOut(0);
$(".genres").hover(function () {
    $(".genres").addClass("hover");
    $( ".video-genres-cont" ).fadeIn();
})
$( ".video-genres-cont" ).on( "mouseleave", function() {
    $( ".video-genres-cont" ).fadeOut();
    $(".genres").removeClass("hover");
});
// \genres menu

// opend and hide menu
$(".mob-button").click(function(){
    $(".mobile-menu-cont").addClass("mobile-menu-fadein");
})
$( ".mobile-menu-cont" ).on( "mouseleave", function() {
    $( ".mobile-menu-cont" ).removeClass("mobile-menu-fadein");
});
$(".title-in-mob i").click(function(){
    $(".mobile-menu-cont").removeClass("mobile-menu-fadein");
})

/*$(function() {
  $(".mobile-menu-cont").swipe({
    swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
       $( ".mobile-menu-cont" ).removeClass("mobile-menu-fadein");
    }
  });

});*/
// \

// Hover search
// 450 - 40 = 410
  var status = 0;
$( ".menu input" ).focus(function() {
  var maxH = $(window).height() - 40;
  var i = 0;
  if(status == 0){
    status = 1;
    /*while (maxH > i) {
      i = i + 400;
      $('<div class="hover-search-el"><div class="hover-search-img"><img src="img/film-img.jpg"></div><div class="hover-search-title">Название</div><div class="hover-search-text"> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur veniam minus laudantium voluptatem nihil iste minima pariatur reprehenderit. Odit inventore neque cum delectus placeat ab quibusdam ullam sapiente, beatae officiis.</div></div><hr>').appendTo($(".hover-search"));
    }*/
  }
  $(".hover-search").fadeIn();
});
$( ".menu input" ).focusout(function() {
  $(".hover-search").fadeOut();
})
//\ Hover search

/************** END SECTION ****************/

function Operator(){
	$('.chat-dialog').append('<div class=\'chat-dialog-el\'><div class=\'chat-dialog-mess\' style=\'color:red\'><i class=\'fa fa-warning\'></i> Функция доступна авторизированным пользователям</div></div>');
	var div = $(".chat-dialog");
	div.scrollTop(div.prop('scrollHeight'));
}

/************ FUNCTION ON PAGE VIEW *********/

function get_cookie(cookie_name) {
					var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
					if ( results )
					return ( unescape ( results[2] ) );
					else
					return null;
				}

function LoadingSeries(go_movie_id,go_season_id,ids_go){
	/*$.ajax({
						url:    	'/functions.php',
						type:		'POST',
						cache: 		false,
						data: {"go_movie_id": go_movie_id,"go_season_id": go_season_id,"ids_go":ids_go},
						dataType: 	'html',
						success: function(data) {
							$("#episodesList").html(data);
						}
					});*/
					$.ajax({
						url:    	'/functions.php',
						type:		'POST',
						cache: 		false,
						data: {"go_movie_id": go_movie_id,"go_season_id": go_season_id,"ids_go":ids_go},
						dataType: 	'html',
						success: function(data) {
							$("#episodesList").html(data);
							setTimeout(function(){
								$("#episodesList").animate({scrollLeft:$('#episodesList').scrollLeft() + $("#episodesList").prop('scrollWidth')}, 800);
							},100);
						}
					});
				}

function closeHint() {
					$('#hint').remove();
				}

var timerout = '10000';
function endAndStartTimer() {
	window.clearTimeout(timeout);
	timeout = setTimeout(hidden_comments,getRandomArbitary(3000,5000));
}

function PostUserComment(message){
		var d = new Date();
		var hours = d.getHours();
		var minutes = d.getMinutes();
		var seconds = d.getSeconds();
		/*$('#comments-cont').prepend('<div class="comment-el"><img src="/templates/images/NoAvatar.png"><div class="name">Незарегистрированный пользователь</div><div class="text">'+message+'</div><div class="info"> <i aria-hidden="true" class="fa fa-clock-o"></i><span class="text">Комментарий оставлен: </span><span class="time">'+hours+':'+minutes+':'+seconds+'</span></div></div>');*/
		//$('#comments-cont').prepend('<div class="userComment"><img src="/templates/images/NoAvatar.png"><div class="userText"><div class="userName">Незарегистрированный пользователь:</div><div class="userDate">'+hours+':'+minutes+':'+seconds+'</div><div class="userText">'+message+'</div></div></div>');
		//$('#comments-cont .comment-el:last').remove();
		//$('#comment-text').val('');
		$('#comments-cont').prepend('<div class="message-line" style="border-bottom:1px solid #212121"><div class="line-one"><div class="time">('+hours+':'+minutes+':'+seconds+')</div><div class="name">Незарегистрированный пользователь:</div></div><div class="line-two"><div class="messag"> '+message+'</div></div></div>');
		$('#comments-cont .comment:last').remove();
		$('#comment-text').val('');
	}



// Функция произвольной генерации чисел
	function getRandomArbitary(min, max){
	  return Math.floor(Math.random() * (max - min + 1)) + min;
	}

// Функция скрытых комментариев
function hidden_comments(){
	 stop();
		var element = $('.subc:first');
        element.removeClass("subc").addClass("subc_show").show(); 
		setTimeout(function(){
            $('#plus-one-comment').animate({opacity : 0}, 1000, '', function(){ $(this).hide(); })
         }, 1000);
		endAndStartTimer();
}
    // Определение количество отправленных пользователем комментариев
function qty_comments() {
    var qty_comments = $.cookie("comment_qty");
    if ( qty_comments == null) {
            $.cookie('comment_qty', '1', { expires: 14, path: '/' });
     }
        if ( qty_comments == 1) {
            $.cookie('comment_qty', '2', { expires: 14, path: '/' });
        }
        if ( qty_comments == 2) {
            $.cookie('comment_qty', '3', { expires: 14, path: '/' });
        }
        if ( qty_comments >= 3) {
            $.cookie('comment_qty', '3', { expires: 14, path: '/' });
            $('#qty_comments').html('Возникли подозрения на спам. Зарегистрируйтесь, что бы оставить комментарий<br /><br />');
            $('#send_comment').remove();
            $('#qty_comments').show();
        }
}

	// Функия вывода сообщения при попытке перемотки вместо default alert
function Alert(AlertTitle,AlertContent,afterFunction){
    $('<div class="overlay" id="alertOverlay"></div>').appendTo('body');
    $('<div id="alert"><a href="#" id="clouseAlert" onclick="clouseAlert('+afterFunction+'); return false" title="Закрыть" class="clousePopup"></a><div class="h1" id="alertH1">'+AlertTitle+'</div><div id="alertText">'+AlertContent+'</div><div class="otbivka"></div><div class="button" onclick="clouseAlert('+afterFunction+'); return false">Продолжить</div></div>').appendTo('body');
    $("#alertOverlay").fadeIn("slow");
    $("#alert").fadeIn("slow");
    $('#alert').css('margin-top', (-1)*($('#alert').height())+'px');
}
    // Функция закрытия окна уведомления
function clouseAlert(afterFunctionClouse){
    $("#alertOverlay").remove();
    $("#alert").remove();
    afterFunctionClouse;
}

function onPlay() {
	setTimeout(function(){InitTitles();},3000);
}
$('.progressBar').click(function () {
	console.log('onSeek()');
});


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

$('.volume i').click(function () {
    toggleVolumeLine();
});

function setVolume(v) {
    vid.volume = 1 - v.toFixed(1);
}
// setVolume(1);
// установка ползунка громкости
function setVolumeHeight(height) {
    $('.setVolumeLine').height(100 - height + '%');
}
setVolumeHeight(1);
// setVolumeHeight(0);
// установка ползунка громкости пользователем
$(".volume .line").click(function(e){
    var offset = $(this).offset();
    var x = e.pageY - offset.top;
	console.log(x , 55);
    var result =  x / 55;
    setVolumeHeight( result * 100);
    setVolume(result)
});

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



