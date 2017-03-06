// loadMoreFilms
function loadMoreFilms(){
  for (i=0;i<8;i++){
    $(".available-to-view-cont-film .cont-full .cont").append('<div class="available-to-view-film-block"><div class="available-to-view-film-img"><img src="img/film-img.jpg" alt="Film IMG"></div><div class="available-to-view-film-title">Название</div></div>');
  }
}
//\ loadMoreFilms
// Show text
$( ".i-footer-post-link-cont ul li" ).click(function() {
  $(".i-show-text .cont-full").fadeIn( "slow" );
})
//\ Show text
