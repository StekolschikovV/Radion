$( document ).ready(function() {
	//$('.scrollUp').liScrollToTop();
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

	/*$('.movie-title').equalHeights();

	$('.tip').simpletip({
		//fixed : false,
		offset : [15, 15]
	});*/

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

})

function LoadMore(){
	    var inProgress = false;
	    var startFrom = 8;
	            $.ajax({            
	                url: 'ajax.php',
	                method: 'POST',
	                data: {"startFrom" : startFrom},
	                beforeSend: function() {
	                inProgress = true;}        
	                }).done(function(data){
	                data = jQuery.parseJSON(data);
	                if (data.length > 0) {                
	                    $.each(data, function(index, data){ 
	                    $("#articles").append('<div class="movie"><a class="movie-poster" href=""><img src="/templates/images/poster500.jpg" alt="111111"></a><a class="movie-title" href="" style="height: 40px; overflow: auto;">Тест Имя</a><span class="movie-icons"><a class="movie-icon" href="#watch"><img src="/templates/images/icon-view.png" alt="11111" width="20" height="20"></a><a class="movie-icon" href="#download"><img src="/templates/images/icon-download.png" alt="222222" width="20" height="20"></a><a class="movie-icon" href="#comments"><img src="/templates/images/icon-comments.png" alt="333333" width="20" height="20"></a></span></div>');
	                    });
	                    inProgress = false;
	                    startFrom += 8;
	                }}); 
}