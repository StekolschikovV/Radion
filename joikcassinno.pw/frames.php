<?php if($_GET["type"] == "registration") {?>
	<iframe src="/plugin/" width="500" height="500" scrolling="auto" frameborder="0" style="display:block;background:url('http://loadinggif.com/images/image-selection/3.gif') no-repeat black; background-position:center;" id="pluginRegister"></iframe>
<? } ?>
<?php if($_GET["type"] == "download") {?>
<iframe src="/download/media/<?=$_GET["load_id"]?>" width="790" height="300" scrolling="no" frameborder="0" style="display:block;background:black;"></iframe>
<? } ?>
<?php if($_GET["type"] == "reviews") {?>
<iframe src="/reviews/" width="600" height="450" scrolling="auto" frameborder="0" style="display:block;background:black;"></iframe>
<? } ?>
<?php if($_GET["type"] == "support") {?>
<iframe src="/reviews/support/" width="600" height="450" scrolling="auto" frameborder="0" style="display:block;background:black;"></iframe>
<? } ?>
<?php if($_GET["type"] == "trailer") {?>
<!-- Всплывающее окно просмотра трейлера к текущему фильму -->
<div style="background:black; color:white; padding:10px">
	<style>
	.alert {
	padding: 15px;
	border: 1px solid transparent;
	border-radius: 4px;
	}

	.alert-success {
	background-color: #18bc9c;
	border-color: #18bc9c;
	color: #ffffff;
	}

	.alert-warning {
	background-color: #f39c12;
	border-color: #f39c12;
	color: #ffffff;
	}
	</style>
	<div class="alert alert-success" style="color:black;"><b><?= strip_tags($_GET["title"]); ?></b></div>
	<div id="bt-in1">
		<div id="bt-in2">
			<?php 
			$urlLinksMoview = $_GET["urlpreview"];
			$url_trailer = explode("/", $urlLinksMoview);
			if($url_trailer[2] == "kp.cdn.yandex.net" || $url_trailer[3] == "kp.cdn.yandex.net"){ 
				$urlLinksMoview = str_replace("http://kp.cdn.yandex.net/trailers", "http://kp.cdn.yandex.net", $urlLinksMoview);?>
				<video width="100%" height="350" controls="controls" poster="<?= strip_tags($_GET["poster"]); ?>">
				   <source src="<?=$urlLinksMoview;?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
				</video>
			<? } else { ?>
				<iframe width="100%" height="350" src="http://www.youtube.com/embed/<?=$urlLinksMoview;?>" frameborder="0" allowfullscreen></iframe>
			<? } ?>
		</div>
	</div>
	<br />
	<div class="alert alert-warning" style="color:black;">
		<b>Смотреть целый фильм в HD качестве можно только после <a href="javascript:void(0)" style="color: #0b88b1!important;" class="registration initializeaudio" onclick="$('#view-link').click()">регистрации</a></b>
	</div>
</div>
<!-- Всплывающее окно просмотра трейлера к текущему фильму -->
<? } ?>
<?php if($_GET["type"] == "trailer-register") {?>
<!-- Всплывающее окно просмотра трейлера к текущему фильму -->
<div style="background:black; color:white; padding:10px">
	<style>
	.alert {
	padding: 15px;
	border: 1px solid transparent;
	border-radius: 4px;
	}

	.alert-success {
	background-color: #18bc9c;
	border-color: #18bc9c;
	color: #ffffff;
	}

	.alert-warning {
	background-color: #f39c12;
	border-color: #f39c12;
	color: #ffffff;
	}
	</style>
	<div class="alert alert-success" style="color:black;"><b><?= strip_tags($_GET["title"]); ?></b></div>
	<div id="bt-in1">
		<div id="bt-in2">
			<?php 
			$urlLinksMoview = $_GET["urlpreview"];
			$url_trailer = explode("/", $urlLinksMoview);
			if($url_trailer[2] == "kp.cdn.yandex.net" || $url_trailer[3] == "kp.cdn.yandex.net"){ 
				$urlLinksMoview = str_replace("http://kp.cdn.yandex.net/trailers", "http://kp.cdn.yandex.net", $urlLinksMoview);?>
				<video width="100%" height="350" controls="controls" poster="<?= strip_tags($_GET["poster"]); ?>">
				   <source src="<?=$urlLinksMoview;?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
				</video>
			<? } else { ?>
				<iframe width="100%" height="350" src="http://www.youtube.com/embed/<?=$urlLinksMoview;?>" frameborder="0" allowfullscreen></iframe>
			<? } ?>
		</div>
	</div>
	<br />
	<div class="alert alert-warning" style="color:black;">
		<b>Смотреть целый фильм в HD качестве можно только после <a href="javascript:void(0)" style="color: #0b88b1!important;" class="registration initializeaudio" onclick="$('#view-link').click()">регистрации</a></b>
	</div>
</div>
<!-- Всплывающее окно просмотра трейлера к текущему фильму -->
<? } ?>