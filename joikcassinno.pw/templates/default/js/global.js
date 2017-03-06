$(".fa-bars").click(function(){
	$(".mobMenu").removeClass("displayNone")
});
$( "button.mobSearchButton" ).click(function () {
    location.href = '/search/?query=' + $( "input.mobSearchInput" ).val();
});
function closeWindow(){
	$(".mobMenu").addClass("displayNone");
}