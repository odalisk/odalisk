/* 
 *= require libs/jquery
 *= require transition
 *= require carousel
 *= require modal
 *= require tooltip
 *= require popover
 *= require tab
 */

 /*
 CSS Browser Selector v0.4.0 (Nov 02, 2010)
 Rafael Lima (http://rafael.adm.br)
 http://rafael.adm.br/css_browser_selector
 License: http://creativecommons.org/licenses/by/2.5/
 Contributors: http://rafael.adm.br/css_browser_selector#contributors
 */
 function css_browser_selector(u){var ua=u.toLowerCase(),is=function(t){return ua.indexOf(t)>-1},g='gecko',w='webkit',s='safari',o='opera',m='mobile',h=document.documentElement,b=[(!(/opera|webtv/i.test(ua))&&/msie\s(\d)/.test(ua))?('ie ie'+RegExp.$1):is('firefox/2')?g+' ff2':is('firefox/3.5')?g+' ff3 ff3_5':is('firefox/3.6')?g+' ff3 ff3_6':is('firefox/3')?g+' ff3':is('gecko/')?g:is('opera')?o+(/version\/(\d+)/.test(ua)?' '+o+RegExp.$1:(/opera(\s|\/)(\d+)/.test(ua)?' '+o+RegExp.$2:'')):is('konqueror')?'konqueror':is('blackberry')?m+' blackberry':is('android')?m+' android':is('chrome')?w+' chrome':is('iron')?w+' iron':is('applewebkit/')?w+' '+s+(/version\/(\d+)/.test(ua)?' '+s+RegExp.$1:''):is('mozilla/')?g:'',is('j2me')?m+' j2me':is('iphone')?m+' iphone':is('ipod')?m+' ipod':is('ipad')?m+' ipad':is('mac')?'mac':is('darwin')?'mac':is('webtv')?'webtv':is('win')?'win'+(is('windows nt 6.0')?' vista':''):is('freebsd')?'freebsd':(is('x11')||is('linux'))?'linux':'','js']; c = b.join(' '); h.className += ' '+c; return c;}; css_browser_selector(navigator.userAgent);

$(".icon-info[rel=popover], span[rel=popover]")
  .popover()
  .click(function(e) {
    e.preventDefault()
  });

$('#portalTab a:first').tab('show');



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
    
    
    
    $(window).resize(function() {
        window.spanSize = ($(window).width() * 0.23404255317 - 23)+'px';
      $('#alwaysOnTop .nav-list').css({'maxHeight':($(window).height() - 300)+'px', width:window.spanSize+'px'});
      
      this.resizeSpan();
      
    });
    $('#alwaysOnTop .nav-list').css({'maxHeight':($(window).height() - 300)+'px'});
    window.spanSize = ($(window).width() * 0.23404255317 - 23)+'px';
    
    if(typeof window.page != 'undefined' && window.page == 'home')
    {
        //$('#navbar').scrollspy();
        $('#carouselHome').carousel({
            interval:10000
        });
        
        $('#carouselHome').on('slide',function() {
            var active = $('#carouselHome .item.active');
            if(active.attr('id') == 'browserSlide')
            {
                $('#headerWrapper').attr('class','backblue');
            }
            else
            {
                $('#headerWrapper').attr('class','backwhite');
                
            }
        });
        
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
    this.portalSpanSize = 0;
    this.portalSpanArray = new Array();
    
    this.updateResult = function() {
        this.resetDisplay();
        this.updateRequest();
        this.getData();
    }
    
    /*
        $params = array(
         *    'in' => array(
         *        'portal' => array(1,2),
         *        'categories' => array(1,2),
         *    ),
         *    // WHERE name LIKE %test% AND id > 4
         *    'where' => array(
         *        array('name', 'LIKE', '%test%'),
         *        array('id', '>', 4),
         *    ),
         * );
         
    */
    this.updateRequest = function() {
        var labels = $('.tag-list .label').toArray();
        
        this.request = new Object();
        this.request['in'] = new Object();
        this.request['where'] = new Array();
        for(var i in labels)
        {
            var label = $(labels[i]);
            var dataType = label.attr('data-type');
            
            if(label.attr('data-active') == 'true')
            {
                switch(dataType)
                {
                    case 'portal':
                        if(this.request['in']['portal'] == undefined)
                        {
                            this.request['in']['portal'] = new Array();
                        }
                        this.request['in']['portal'].push(label.attr('data-value'));
                    break;
                    case 'category':
                        if(this.request['in']['categories'] == undefined)
                        {
                            this.request['in']['categories'] = new Array();
                        }
                        this.request['in']['categories'].push(label.attr('data-value'));
                    break;
                    case 'format':
                        if(this.request['in']['formats'] == undefined)
                        {
                            this.request['in']['formats'] = new Array();
                        }
                        this.request['in']['formats'].push(label.attr('data-value'));
                    break;
                    case 'license':
                        if(this.request['in']['license'] == undefined)
                        {
                            this.request['in']['license'] = new Array();
                        }
                        this.request['in']['license'].push(label.attr('data-value'));
                    break;
                    default:
                        this.request['where'].push([dataType, '=', label.attr('data-value')]);
                    break;
                }
                
                
            }
        }
        if(this.search != '')
        {
            this.request['where'].push(['name', 'LIKE', '%'+this.search+'%']);
        }
        console.log(this.request);
    }
    
    
    
    this.getData = function() {
        this.page = 0;
        $('#request-result').html('<tr class="loading"><td colspan="3" class="dataset-item"><span>Chargement</span></td></tr>');
        $.post(
            window.baseUrl+'api/'+window.searchType+'s/'+this.page+'/'+this.pageSize+'/'+window.displayType,
            {
                'request':this.request
            },
            function(data) {
                window.api.data = data;
                window.api.updateTable();
            }
        );
    }
    
    this.beforeRequest = function() {
        
    }
    
    this.afterRequest = function() {
        
        this.resizeSpan();
        
        $(".icon-info[rel=popover], span[rel=popover]")
          .popover()
          .click(function(e) {
            e.preventDefault()
          });
    }
    
    this.updateTable = function() {
        $('.no-result, .loading').remove();
        $('#request-result').html(window.api.data);
        this.afterRequest();
    }
    
    this.addToTable = function() {
        $('.no-result, .loading').remove();
        $('#request-result').append($(window.api.data));
        this.afterRequest();
    }
    
    this.nextPage = function(elem) {
        $(elem).parent().parent().remove();
        this.page += 1;
        $.post(
            window.baseUrl+'api/'+window.searchType+'s/'+this.page+'/'+this.pageSize+'/'+window.displayType,
            {
                'request':this.request
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
    
    //SpanSize
    
    this.resizeSpan = function() {
        /*window.api.portalSpanSize = 0;
          $('#request-result .span4').each(function() {
              var height = $(this).parent().height() - 20;
              if(window.api.portalSpanSize < height)
              {
                  window.api.portalSpanSize = height;
              }
              window.api.portalSpanArray.push($(this));
          });
          for(i in window.api.portalSpanArray)
          {
              window.api.portalSpanArray[i].children('.portal-span-item').css({height:window.api.portalSpanSize+'px'});
          }*/
    }
    
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
        c.css({position:"fixed",top:"90px", right: (offset.left + 2)})
        $('#alwaysOnTop').css({'width':window.spanSize}).parent().css({'minHeight':($(window).height() - 120)+'px'});
        } else {
        if (b<=d) {
          c.css({position:"relative",top:"", right: ''})
          $('#alwaysOnTop').css({'width':'23.404255317%'})
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


            
