$(".story a").click(function() {
    $(".story .description").toggleClass("descriptionFull");
    $(".story .dots").toggleClass("displayNone");
});

function scrollStoLeft() {
    setTimeout(function(){
        $( "#episodesList" ).scrollLeft( 90000 );
    }, 4000);
}
$( window ).resize(function() { scrollStoLeft(); });
$( document ).ready(function() { scrollStoLeft(); });
