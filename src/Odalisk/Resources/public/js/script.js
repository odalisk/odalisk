/* 
 *= require libs/jquery
 *= require plugins
 *= require scrollspy
 *= require modal
 */

jQuery(function($) {
    $('.home .nav-collapse li a').click(function() {
        var href = $(this).attr('href');
        $('body').scrollTop($(href).offset().top - 95);
        $(this).parent().parent().children('li').each(function() {
            $(this).removeClass('active');
        });
        $(this).parent().addClass('active');
        return false;
    });
    $('#navbar').scrollspy();
    
    
    
    
    
    $('.tag-list .label').each(function() {
        $(this).click(function() {
            if($(this).attr('data-active') == 'true')
            {
                $(this).attr('data-active','false');
                $(this).removeClass('label-success');
                $(this).children('span').html('+');
            }
            else
            {
                $(this).attr('data-active','true');
                $(this).addClass('label-success');
                $(this).children('span').html('&times;');
            }
            window.api.updateResult();
        });
    });
    window.api = new jsApi();
});



jsApi = function() {
    this.request = new Object();
    
    
    this.updateResult = function() {
        this.updateRequest();
        this.getData();
    }
    
    this.updateRequest = function() {
        var labels = $('.tag-list .label').toArray();
        
        this.request = new Object();
        
        for(var i in labels)
        {
            var label = $(labels[i]);
            
            if(label.attr('data-active') == 'true')
            {
                if(this.request[label.attr('data-type')] == undefined)
                {
                    this.request[label.attr('data-type')] = new Array();
                }
            
                this.request[label.attr('data-type')].push(label.attr('data-value'));
            }
        }
    }
    
    this.getData = function() {
        console.log(window.api.request);
        $.post(
            '/app_dev.php/api/html',
            window.api.request,
            function(data) {
                window.api.data = data;
                window.api.updateTable();
            }
        );
    }
    
    this.updateTable = function() {
        console.log($('.request-result').html());
        $('.request-result').html(window.api.data);
    }
}

var konami = document.createElement('input');
			konami.setAttribute('type','text');		konami.setAttribute('style','position:fixed;left:-100px;top:-100px');
			var cpt = 0;
			var tab = new Array(38,38,40,40,37,39,37,39,66,65);

			konami.onkeyup = function(e){
				var unicode=e.keyCode? e.keyCode : e.charCode;

				if(unicode == tab[cpt] && cpt == 9)
				{
					cpt = 0;
					$('#konami').modal();
				}
				else if(unicode == tab[cpt])
				{
					cpt++;
				}
				else
				{
					cpt = 0;
				}
			};
			
			document.body.appendChild(konami);
			konami.focus();

$('body').append($('<div id="konami" class="modal hide fade"><div class="modal-header">  <a class="close" data-dismiss="modal" href="#">&times;</a>  <h3>Exoskull vaincra !</h3></div><div class="modal-body">  <img style="position:relative; display:block; margin:auto;" src="http://kap.la/ptrans/img/exo.jpg"/></div></div>'));


            
