function getProgress(){ 
    var inProgress = false;
    var startFrom = 8;


            $.ajax({            
                url: '/obrabotchik.php',
                method: 'POST',
                data: {"startFrom" : startFrom},
                beforeSend: function() {
                inProgress = true;}        
                }).done(function(data){
                data = jQuery.parseJSON(data);
                if (data.length > 0) {                
                    $.each(data, function(index, data){ 
                    //$("#articles").append("<p><b>" + data.title + "</b><br />" + data.text + "</p>");
                    $("#articles").append('<div class="movie"><a class="movie-poster" href=""><img src="/templates/images/poster500.jpg" alt="111111"></a><a class="movie-title" href="" style="height: 40px; overflow: auto;">Тест Имя</a><span class="movie-icons"><a class="movie-icon" href="#watch"><img src="/templates/images/icon-view.png" alt="11111" width="20" height="20"></a><a class="movie-icon" href="#download"><img src="/templates/images/icon-download.png" alt="222222" width="20" height="20"></a><a class="movie-icon" href="#comments"><img src="/templates/images/icon-comments.png" alt="333333" width="20" height="20"></a></span></div>');
                    });
                    
                    inProgress = false;
                    startFrom += 8;
                }});   

}