function getTime(){
	var o=new Date,
	t=o.getHours(),
	i=o.getMinutes(),
	n=o.getSeconds();
	return t+":"+i+":"+n
}function chatUpDown(){0==satus?($(".pb-chat").addClass("chat-show"),$(".fa-angle-double-up").addClass("transform-rotate"),satus=1):($(".pb-chat").removeClass("chat-show"),$(".fa-angle-double-up").removeClass("transform-rotate"),satus=0)}
function chatUpDownTwo(){}
function closeChat(){$(".pb-chat").remove()}
$("#input_question").focusin(function(){
	console.log(" Курсор на инпуте! ");
	$("#input_question").keypress(function(o){
	if(13==o.keyCode){
		var t=$(".input input").val();

		if(t.length > 3){

			var div = $(".chat-dialog");
			var nameoperator = $("#nameoperator").html();

			$.ajax({
				url:    	'/chat.php',
				type:		'POST',
				cache: 		false,
				data:  		{'t':t},
				dataType: 	'html',
				success: function(data) {
					if(nameoperator == null){
						$('.chat-dialog').append(data);
					} else {
						setTimeout(function(){
							
							$('.chat-dialog').append('<div class=\'chat-dialog-el printing\'><div class=\'chat-dialog-mess\' style=\'color:red\'><i class="fa fa-spinner fa-spin"></i> Ожидайте ответа оператора...</div></div>');
							setTimeout(function(){
								$(".printing").html(data);
								$(".chat-dialog-el").removeClass("printing");
								div.scrollTop(div.prop('scrollHeight'));
							},5000);
							div.scrollTop(div.prop('scrollHeight'));
						},3000);
					}
				}
			});

			$("#input_question").val("");

			$('.chat-dialog').append('<div class=\'chat-dialog-el answer\'><div class=\'chat-dialog-name\'>Вы</div><div class=\'chat-dialog-mess\'>'+t+'</div><div class=\'chat-dialog-el-time\'>'+getTime()+'</div></div>');
			div.scrollTop(div.prop('scrollHeight'));


		} else {
			//alert("Слишком короткое сообщение...");
		}
	}})});

var satus=0;
$(document).mouseup(function(o){var t=$(".pb-chat");t.is(o.target)||0!==t.has(o.target).length||0!=satus&&($(".pb-chat").removeClass("chat-show"),$(".fa-angle-double-up").removeClass("transform-rotate"))});
