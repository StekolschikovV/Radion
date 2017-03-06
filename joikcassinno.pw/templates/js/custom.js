(function($) {
    $(document).ready(function() {

    /* IF YOU WANT TO APPLY SOME BASIC JQUERY TO REMOVE THE VIDEO BACKGROUND ON A SPECIFIC VIEWPORT MANUALLY

     var is_mobile = false;

    if( $('.player').css('display')=='none') {
        is_mobile = true;       
    }
    if (is_mobile == true) {
        //Conditional script here
        $('.big-background, .small-background-section').addClass('big-background-default-image');
    }else{
        $(".player").mb_YTPlayer(); 
    }

    });

*/
    /*  IF YOU WANT TO USE DEVICE.JS TO DETECT THE VIEWPORT AND MANIPULATE THE OUTPUT  */

        //Device.js will check if it is Tablet or Mobile - http://matthewhudson.me/projects/device.js/
        /*if (!device.tablet() && !device.mobile()) {
            $(".player").mb_YTPlayer();
        } else {
            //jQuery will add the default background to the preferred class 
            $('.big-background, .small-background-section').addClass(
                'big-background-default-image');
        }*/

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
        });

    });


})(jQuery);