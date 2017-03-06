$(function(){
	$('a.popup').each(function(){
		var params = {
			closeBtn : $(this).hasClass('modal') ? false : true,
			modal : $(this).hasClass('modal') ? true : false,
			padding : 5
		}
		$(this).fancybox(params);
	})
	$('form.popup').submit(function(){
		var params = {
			href : $(this).attr('rel'),
			closeBtn : $(this).hasClass('modal') ? false : true,
			modal : $(this).hasClass('modal') ? true : false,
			padding : 5
		}
		$.fancybox(params);
		return false;
	});

	$('.movie-title').equalHeights();

	$('.tip').simpletip({
		//fixed : false,
		offset : [15, 15]
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
	})
})