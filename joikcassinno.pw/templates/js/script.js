// load top img
/*if($("*").is(".video-page-img")){
  ScreenWidth = screen.width;
  if(ScreenWidth > 982){
    $('<img src="img/top.jpg" class="top-img">').appendTo($(".video-page-img"));
  }
}
if($("*").is(".i-video-page-img")){
  ScreenWidth = screen.width;
  if(ScreenWidth > 751){
    console.log('1');
      $('<div class="i-top-img"><div class="bg"></div></div>').appendTo($(".i-video-page-img"));
  }
}*/
//\ load top img

// stars-info-block

var starNow = 0;
var position = 0;

function star(e) {
    i = 0
    while(true){
        if(e === i){
            if(parseInt(e) === e){
                $(".star-" + e).html('<i class="fa fa-star" aria-hidden="true"></i>');
            }
            else {
                $(".star-" + (e + 0.5)).html('<i class="fa fa-star-half-o" aria-hidden="true"></i>');
            };
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

    var newVal = $("#current_rate").val();
      for (i=0; i<newVal; i++){
         $(".star-" + i).html('<i class="fa fa-star" aria-hidden="true"></i>');
      }
    
    starNow = e;
    position = e;
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
        console.log(i);
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
    $(".big-frames-block").html('<img src="img/' + e + '.jpg">');
}
$(".big-frames-block").click(function () {
    $(".big-frames-block").fadeOut();
})
// \big frames

// genres menu
$( ".video-genres-cont" ).fadeOut(0);
$(".genres, .video-genres-cont").hover(function () {
    $(".genres").addClass("hover");
});
$( ".genres" ).on( "mouseleave", function() {
    $(".genres").removeClass("hover");
});
$(".genres").click(function () {
    $(".genres").addClass("hover");
    $( ".video-genres-cont" ).fadeIn();
});
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
$(function() {
  $(".mobile-menu-cont").swipe( {
    swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
       $( ".mobile-menu-cont" ).removeClass("mobile-menu-fadein");
    }
  });

});
// \

// Hover search
// 450 - 40 = 410
  var status = 0;
$( ".menu input" ).focus(function() {
  var maxH = $(window).height() - 40;

  console.log("in");

  var i = 0;
  if(status == 0){
    status = 1;
    while (maxH > i) {
      i = i + 400;
      $('').appendTo($(".hover-search"));
    }
  }
  $(".hover-search").fadeIn();
});
$( ".menu input" ).focusout(function() {
  $(".hover-search").fadeOut();
})

//\ Hover search

/*window.onload = function() {
    $( "#episodesList" ).scrollLeft( 9999 );
    var lLoc = (location).toString().length;
    var lLO = (location.origin).length;
    if(lLoc == (lLO + 1)){
        $( "footer .i-footer-post-link" ).remove();
    }

    if($("#episodesList").children().length < 1){
        $( "#episodesList" ).remove();
    }
};*/