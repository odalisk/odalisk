/* 
 *= require libs/jquery
 *= require scrollspy
 *= require transition
 *= require carousel
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
    
    
    if(typeof window.page != 'undefined' && window.page == 'home')
    {
        $('#carouselHome').carousel({
            interval:10000
        });
        
        $('#carouselHome').on('slide',function() {
            var active = $('#carouselHome .item.active');
            if(active.attr('id') == 'browserSlide')
            {
                $('#headerWrapper').attr('class','backblue');
                console.log('kikou');
            }
            else
            {
                $('#headerWrapper').attr('class','backwhite');
                
            }
        });
        
        /*$('#carouselHome').on('slid',function() {
            var active = $('#carouselHome .item.active');
            if(active.attr('id') == 'browserSlide')
            {
                $('#headerWrapper').addClass('afterBackblue');
            }
            else
            {
                $('#headerWrapper').addClass('afterBackwhite');
                
            }
        });*/
    }
    
    window.api = new jsApi();
    
});




jsApi = function() {
    this.request = {};
    this.page = 0;
    this.search = '';
    this.searchType = '';
    this.initActions = new Array();
    this.actions = new Object();
    this.statics = new Array();
    this.pageSize = (typeof window.pageSize === 'number') ? window.pageSize : 20;
    
    this.updateResult = function() {
        this.resetDisplay();
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
        
        this.request['search'] = this.search;
    }
    
    this.getData = function() {
        this.page = 0;
        $.post(
            '/app_dev.php/api/html',
            {
                'request':this.request,
                'page_number':this.page,
                'type':window.searchType,
                'page_size':this.pageSize
            },
            function(data) {
                window.api.data = data;
                window.api.updateTable();
            }
        );
    }
    
    this.updateTable = function() {
        $('.no-result').remove();
        $('#request-result').html(window.api.data);
    }
    
    this.addToTable = function() {
        $('.no-result').remove();
        $('#request-result').append($(window.api.data));
    }
    
    this.nextPage = function(elem) {
        $(elem).parent().parent().remove();
        this.page += 1;
        $.post(
            '/app_dev.php/api/html',
            {
                'request':window.api.request,
                'page_number':this.page,
                'type':window.searchType,
                'page_size':this.pageSize
            },
            function(data) {
                
                if(data == '')
                {
                    window.api.noMoreResult();
                    
                }
                
                window.api.data = data;
                window.api.addToTable();
                
            }
        );
    }
    
    this.noMoreResult = function() {
        $('#moreResults').remove();
    }
    
    this.resetDisplay = function() {
    }
    
    //Recherche
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
    
    $('#search').keyup(function() {
        window.api.search = $(this).val();
        window.api.updateResult();
    });
    
    
    
    //Always on top
    var a = function() {};
    $(window).scroll(a);a();
    $(document).scroll(function() {
        var b = $(window).scrollTop();
        var alwaysOnTop = $("#alwaysOnTop");
        var offset = alwaysOnTop.parent().offset();
        var d = offset.top - 90;
        var c = alwaysOnTop;
        if (b>d) {
        c.css({position:"fixed",top:"90px", right: (offset.left - 5)})
        } else {
        if (b<=d) {
          c.css({position:"relative",top:"", right: ''})
        }
        }
    });
    
    /* Init actions */
    this.addInitAction = function(actionName) {
        for(i in this.initActions)
        {
            if(this.initActions[i] == actionName) return;
        }
        this.initActions.push(actionName);
    }

    this.init = function() {
        console.log(window.pageNamespace);
        if(window.pageNamespace)
        {
            for(var i in window.api.rules[window.pageNamespace]) (this.actions[window.api.rules[window.pageNamespace][i]])();
        }
    }

    this.addAction = function(actionName, action)
    {
        this.actions[actionName] = action;
    }

    this.availableActions = function() {
        var result = "Available actions :\n";
        for(i in this.actions)
        {
            result += "\t- "+i+"\n";
        }
        console.log(result);
    }

    /* Parameters */
    this.setStatic = function(staticName, staticContent) {
        this.statics[staticName] = staticContent;
    }

    this.getStatic = function(staticName) {
        return this.statics[staticName];
    }

    this.availableStatics = function() {
        var result = "Available statics :\n";
        for(i in this.statics)
        {
            result += "\t- "+i+"\n";
        }
        console.log(result);
    }
    
    if(window.needResult)
    {
        this.updateResult();
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


            
