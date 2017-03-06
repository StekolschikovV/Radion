<?php
session_start();

# Файл работы с отзывами на сайте
# Секция обычных комментариев
# Доступна только если пользователь не рефферальный
# Секция саппорта ( тех. поддержки )
# Доступна только для рефферальных пользователей

# Проверка на вхождение ссылки поодержка или просто отзывы
# Если это обычные отзывы
	$url = explode("/" ,$_SERVER['QUERY_STRING']);
	# Определение путей
	define(TEMPLATE_ROOT, "/templates/");
	
	# Подключение директив ( Основного набора функций, параметры подключения, главный контроллер )
	require_once("core/Application.php");
	require_once("core/Connection.php");
	require_once("controllers/main.controller.php");
	
	# Получение параметров реального IP адресса
	$ip = Main::GetRealIp();
	# Инициализация класса подключения
	$DB = new DB;
	# Проверка на наличие возможности
	# Добавления комментариев
	$getSettings = Main::Reviews();

	# Подключение класса определения мобильных устройств
	require_once('core/MobileDetect.php'); 
	$detect = new Mobile_Detect;

	if($url[1] == NULL){
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta charset="utf8" />
		<title>КиноПоиск HD - Комментарии</title>
		<link rel="stylesheet" type="text/css" href="<?=TEMPLATE_ROOT;?>css/popup.css" media="screen" />
		<!--[if IE]>
		<script type="text/javascript" src="scripts/html5.js"></script>
		<link rel="stylesheet" type="text/css" href="styles/ie.css" media="screen" />
		<![endif]-->
		<script type="text/javascript" src="<?=TEMPLATE_ROOT;?>js/jquery.js"></script>
		<script type="text/javascript">
		$(function(){
			$('.close').click(function(){
				window.parent.$.fancybox.close();
			})
		})
		function slidePage(){
			$("#hiden").slideDown(800);
			$("#errors").slideUp(800);
		}
		function InsertReview(){
			var author = $("#author").val();
			var comment = $("#text-count").val();
			var ip = "<?=$ip;?>";
			if(author == '' && comment == ''){
				$("#errors").html("<div class='alert alert-error'><b>Не заполнены обязательные поля!</b></div>");
			} else {
				$.ajax({
					type: "POST",
					url: "/ajax-actions.php",
					data: {"author": author, "comment": comment,"ip": ip},
					cache: false,						
					success: function(response){
						if(response == 0){
							$("#hiden").slideUp(800);
							$("#errors").css("display", "none");
							$("#errors").html("<div class='alert alert-success'><b>Ваш отзыв был успешно добавлен!</b></div>");
							$("#errors").slideDown(400);
							setTimeout(slidePage, 3000);
							$("#reviews-list").prepend('<div class="review"><div style="margin-bottom:12px;color:red;"><b>'+ author +'</b></div><div style="color: black">'+ comment +'</div></div><br />');
							$("#author").val('');
							$("#text-count").val('');
						}
					}
				});
			}
			
		}
		function textCounter( field, countfield, maxlimit ) {
		  if ( field.value.length > maxlimit ){
			field.value = field.value.substring( 0, maxlimit );
			$("#errors").html("<div class='alert alert-error'><b>Достигнут лимит вводимы символов в поле</b></div>");
			return false;
		  }else{
			$("#text-counter").text(maxlimit - field.value.length);
		  }
		}
		</script>
	</head>
	<?php 
	/*
	Проверка мобильного устройства,
	если определено мобильное устройство, запускам <body> с нужным стилем
	если это ПК оставляем все как есть
	*/
	if ( $detect->isMobile() ) {?>
		<body style="background:url('/templates/images/bg013a50.jpg'); color: white" class="body_review">
	<? } else { ?>
		<body style="background:transparent; color: white" class="body_review">
	<? } ?>
    <div class="reg-loading">Некое сообщение </div>
    <div class="content" style="font-size: 14px;">
		<div id="errors"></div>
		<?php if($getSettings["active"] == 1){?>
		  <form action="" method="post" id="hiden">
			<center><h1>Отзывы и предложения</h1></center>
			<table cellspacing="10" cellpadding="0">
			  <tr>
				<td><input type="text" name="author" id="author" value="" size="20" style="padding:5px; width:500px" placeholder="Автор..."/></td>
			  </tr>
			  <tr>
				<td>
					<textarea style="width:510px" name="comment" cols="50" rows="5" id="text-count" onkeyup="textCounter(this,'text-counter',200);" placeholder="Комментарий..."></textarea><br />
					Осталось символов: <span id='text-counter' style='font-weight:bold'>200</span>
				</td>
			  </tr>
			  <tr>
				<td>
					<input type="button" value="Отправить" class="submit btn-next" onclick="InsertReview();" style="background:#00b0eb; color:white;cursor:pointer"/>
				</td>
			  </tr>
			</table>
		  </form>
		<? } else {?>
			<div class='alert alert-danger'><b>Добавление отзывов временно отключена администратором!</b></div>
		<? } ?>
		<?php if($getSettings["comments"] != 1){?>
		  <div id="reviews-list"  style="padding:10px;">
			<?php
			$DB->query("select count(1) from `serial_website_reviews` where `active` = '1' and `ip` in ('0.0.0.0','".$ip."') order by `id` desc");
			$count = $DB->fetch_array();
			if($count[0] == 0){
				echo "<div class='alert alert-warning'><b>Отзывов пока нет. Вы можете быть первым!</b></div>";
			} else {
				$DB->query("select * from `serial_website_reviews` where `active` = '1' and `ip` in ('0.0.0.0','".$ip."') order by `id` desc");
				while($review = $DB->fetch_array()){
					echo '<div class="review" style="background: #CFCFCF; "><div style="margin-bottom:12px;color:red;"><b>' . $review['author'] . '</b></div><div style="color: black">' . $review['comment'] . '</div></div><br />';
				}
			}
			?>
			</div>
		<? } else { ?>
			<div class='alert alert-danger'><b>Список всех комментариев временно недоступен!</b></div>
		<? } ?>
    </div>
  </body>
</html>
<? } 
if($url[1] == "support") { ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="/templates/css/popup.css" media="screen" />
        <style>
            #chat {
                width: 555px;
                margin: 0px auto;
            }
            #send-sms {
                width: 555px;
                margin: 0px auto;
                display: none;
            }
            #dialog {
                height: 155px;
                margin: 20px 0px;
                overflow: auto;
                padding-right: 10px;
            }
            #question {
                padding: 5px;
                border: 2px solid #765942;
                border-radius: 3px;
                background-color: #323841;
                color: #FFF;
                resize:none;
            }
            .question, .wait{
                color: #B46B31;
                border: 2px solid #765942;
                border-radius: 15px;
                margin-bottom: 5px;
                padding: 13px
            }
            .text-info {
                font-size: 16px;
                color:#B46B31;
            }
            .text-right {
                text-align: right;
            }
            .text-center {
                text-align: center;
            }
            .stress {
                color: #FBCB09;
            }
            .send-button {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 10px;
                color: #B46B31;
                margin-right: 5px;
                padding: 10px;
                background: -moz-linear-gradient(
                        top,
                        #6b6b6b 0%,
                        #000000);
                background: -webkit-gradient(
                        linear, left top, left bottom, 
                        from(#6b6b6b),
                        to(#000000));
                -moz-border-radius: 30px;
                -webkit-border-radius: 30px;
                border-radius: 30px;
                border: 1px solid #000000;
                -moz-box-shadow:
                        0px 1px 3px rgba(000,000,000,0.5),
                        inset 0px 0px 1px rgba(255,255,255,0.7);
                -webkit-box-shadow:
                        0px 1px 3px rgba(000,000,000,0.5),
                        inset 0px 0px 1px rgba(255,255,255,0.7);
                box-shadow:
                        0px 1px 3px rgba(000,000,000,0.5),
                        inset 0px 0px 1px rgba(255,255,255,0.7);
                text-shadow:
                        0px -1px 0px rgba(000,000,000,0.4),
                        0px 1px 0px rgba(255,255,255,0.3);
            }
            .send-button:hover {
                cursor: pointer;
            }

        </style>
        <!--[if IE]>
        <script type="text/javascript" src="scripts/html5.js"></script>
        <link rel="stylesheet" type="text/css" href="styles/ie.css" media="screen" />
        <![endif]-->
        <script type="text/javascript" src="/templates/js/jquery.js"></script>
        <script type="text/javascript">
            function sendQuestion() {
                var question = $('#question').val();
                if (question !== '') {
                    $('#dialog').append('<p class="question">' + question + '</p>');
                    var wait = ' Ожидайте подключение оператора...';
                    var img  = '<img src="/templates/images/loading.gif" alt="ожидайте" />';
                    $("p.text-info").remove();
                    $('#dialog').append('<p class="text-info text-right">' + img + wait + '</p>');
                    setTimeout(sendSms, 120000);
                } else {
                    alert('Пустое сообщение!');
                }
            }
            function sendSms() {
                $('#chat').hide();
                $('#send-sms').show();
            }
        </script>
    </head>
    <?php if ( $detect->isMobile() ) {?>
		<body style="background:url('/templates/images/bg013a50.jpg'); color: white" class="body_review">
	<? } else { ?>
		<body style="background:transparent;" >
	<? } ?>
        <div id="chat">
            <div>
                <h3 style="text-align: center; color: white">Служба поддержки 24 / 7</h3>
            </div>
            <div id="dialog"></div>
            <span class="text-info">Введите свой вопрос и дождитесь ответа оператора:</span>
            <div>
                <textarea id="question" cols="67" rows="5"></textarea>
            </div>
            <div style="text-align: right;">
                <button class="send-button" onclick="sendQuestion()">Отправить</button>
            </div>
            <div class="text-info text-center" style="color: #FFF;">
                ООО "Джойказино"<br />
                Адрес: 129085, г.Москва, Звёздный бульвар, д.21.оф.407<br />
                Телефон: (495)797-56-76 E-mail: support24@<?=$_SERVER["SERVER_NAME"]?>
            </div>
        </div>
        <div id="send-sms">
            <h3 style="text-align: center; margin-bottom: 50px;">Служба поддержки 24 / 7</h3>
            <p style="text-align: center;">Извините, на данный момент все операторы заняты.</p>
        </div>
    </body>
</html>
<? } ?>
