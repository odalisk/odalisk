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
});

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


            
