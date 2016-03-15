var MalinkyAjaxPaging=function(t){var a=function(){function a(t,a,n){var i;return function(){var e=this,o=arguments,r=function(){i=null,n||t.apply(e,o)},p=n&&!i;clearTimeout(i),i=setTimeout(r,a),p&&t.apply(e,o)}}var n=p,i=g,e=l,o=c,r=m,w=d,v=u,x=y,j=f,b=k,T=_,L=S,E=h,I=!1,D=function(){t.ajax({type:"GET",url:L,dataType:"html",success:function(a){var n=t.parseHTML(a),i=(t(n).find(v),1),e=0;for(var o in malinkySettings)t(n).find(malinkySettings[o].posts_wrapper).length&&t(n).find(malinkySettings[o].post_wrapper).length&&t(n).find(malinkySettings[o].pagination_wrapper).length&&(1==t(n).find(malinkySettings[o].posts_wrapper).length?e++:t(n).find(malinkySettings[o].posts_wrapper).each(function(t){e++}));for(var o in malinkySettings)t(n).find(malinkySettings[o].posts_wrapper).length&&t(n).find(malinkySettings[o].post_wrapper).length&&t(n).find(malinkySettings[o].pagination_wrapper).length&&(1==e?(t(n).find(malinkySettings[o].posts_wrapper).attr("data-paginator-count",i),t(n).find(malinkySettings[o].pagination_wrapper).attr("data-paginator-count",i)):1==t(n).find(malinkySettings[o].posts_wrapper).length?(t(n).find(malinkySettings[o].posts_wrapper).attr("data-paginator-count",i),t(n).find(malinkySettings[o].posts_wrapper+" "+malinkySettings[o].pagination_wrapper).attr("data-paginator-count",i),t(n).find(malinkySettings[o].posts_wrapper+" "+malinkySettings[o].next_page_selector).attr("data-paginator-count",i),i++):t(n).find(malinkySettings[o].posts_wrapper).each(function(a){t(this).attr("data-paginator-count",i),t(this).find(malinkySettings[o].pagination_wrapper).attr("data-paginator-count",i),t(this).find(malinkySettings[o].next_page_selector).attr("data-paginator-count",i),i++}));var p=t(n).find(v+'[data-paginator-count="'+E+'"] '+x),g=t(v+'[data-paginator-count="'+E+'"] '+x).last(),s=t(v+'[data-paginator-count="'+E+'"] '+x);if(g.after(p),"infinite-scroll"!=w&&"load-more"!=w||(b++,1==e?(b>j&&(t('#malinky-ajax-pagination-button[data-paginator-count="'+E+'"]').parent().remove(),window.removeEventListener("scroll",P)),L=L.replace(/\/page\/[0-9]*/,"/page/"+b)):(t(n).find(T+'[data-paginator-count="'+E+'"]').attr("href")||(t('#malinky-ajax-pagination-button[data-paginator-count="'+E+'"]').parent().remove(),window.removeEventListener("scroll",P)),L=t(n).find(T+'[data-paginator-count="'+E+'"]').attr("href"))),"pagination"==w){s.remove(),history.pushState(null,null,L);var l=t(n).find(r+'[data-paginator-count="'+E+'"]').first();t(r+'[data-paginator-count="'+E+'"]').replaceWith(l)}A()},error:function(t,a){G()},complete:function(){"pagination"==w&&t("body,html").animate({scrollTop:t(v+'[data-paginator-count="'+E+'"]').offset().top-150},400),"infinite-scroll"==w&&(I=!1)}})},H=function(){t(r+'[data-paginator-count="'+E+'"]').last().before('<div class="malinky-ajax-pagination-loading" data-paginator-count="'+E+'">'+n+"</div>")},M=function(){t('.malinky-ajax-pagination-loading[data-paginator-count="'+E+'"]').show(),"load-more"!=w&&"infinite-scroll"!=w||t('#malinky-ajax-pagination-button[data-paginator-count="'+E+'"]').text(e)},A=function(){t('.malinky-ajax-pagination-loading[data-paginator-count="'+E+'"]').hide(),"load-more"!=w&&"infinite-scroll"!=w||t('#malinky-ajax-pagination-button[data-paginator-count="'+E+'"]').text(o),clearTimeout(s)},G=function(){t('.malinky-ajax-pagination-loading[data-paginator-count="'+E+'"]').hide(),clearTimeout(s)},P=a(function(){if(!I){var a=(t(document).height()-t(window).scrollTop()-t(window).height(),t(v+'[data-paginator-count="'+E+'"]').offset().top),n=t(v+'[data-paginator-count="'+E+'"]').outerHeight();t(window).height()+t(window).scrollTop()+i>a+n&&(I=!0,M(),D())}},250);"infinite-scroll"==w?t(T+'[data-paginator-count="'+E+'"]').attr("href")&&(H(),t(r+'[data-paginator-count="'+E+'"]').remove(),window.addEventListener("scroll",P)):"load-more"==w?t(T+'[data-paginator-count="'+E+'"]').attr("href")&&(t(r+'[data-paginator-count="'+E+'"]').last().after('<div class="malinky-load-more"><a href="'+L+'" id="malinky-ajax-pagination-button" class="malinky-load-more__button" data-paginator-count="'+E+'">'+c+"</a></div>"),H(),t(r+'[data-paginator-count="'+E+'"]:not(:has(>a#malinky-ajax-pagination-button[data-paginator-count="'+E+'"]))').remove(),t('#malinky-ajax-pagination-button[data-paginator-count="'+E+'"]').click(function(t){t.preventDefault(),s=setTimeout(M,750),D()})):"pagination"==w&&(H(),t(document).on("click",r+'[data-paginator-count="'+E+'"]',function(t){t.preventDefault(),s=setTimeout(M,750),L=t.target.href,D()}),window.addEventListener("popstate",function(t){L=document.URL,D()}))},n=0,e=1,o=0;for(var r in malinkySettings)t(malinkySettings[r].posts_wrapper).length&&t(malinkySettings[r].post_wrapper).length&&t(malinkySettings[r].pagination_wrapper).length&&t(malinkySettings[r].next_page_selector).length&&(1==t(malinkySettings[r].posts_wrapper).length?o++:t(malinkySettings[r].posts_wrapper).each(function(t){o++}));for(var r in malinkySettings)if(t(malinkySettings[r].posts_wrapper).length&&t(malinkySettings[r].post_wrapper).length&&t(malinkySettings[r].pagination_wrapper).length&&t(malinkySettings[r].next_page_selector).length)for(1==o?(t(malinkySettings[r].posts_wrapper).attr("data-paginator-count",e),t(malinkySettings[r].pagination_wrapper).attr("data-paginator-count",e),t(malinkySettings[r].next_page_selector).attr("data-paginator-count",e)):1==t(malinkySettings[r].posts_wrapper).length?(t(malinkySettings[r].posts_wrapper).attr("data-paginator-count",e),t(malinkySettings[r].posts_wrapper+" "+malinkySettings[r].pagination_wrapper).attr("data-paginator-count",e),t(malinkySettings[r].posts_wrapper+" "+malinkySettings[r].next_page_selector).attr("data-paginator-count",e),e++):t(malinkySettings[r].posts_wrapper).each(function(a){t(this).attr("data-paginator-count",e),t(this).find(malinkySettings[r].pagination_wrapper).attr("data-paginator-count",e),t(this).find(malinkySettings[r].next_page_selector).attr("data-paginator-count",e),e++}),i=1;i<=t(malinkySettings[r].posts_wrapper).length;i++){var p=malinkySettings[r].ajax_loader,g=(malinkySettings[r].malinky_load_more,malinkySettings[r].malinky_load_more_button,parseInt(malinkySettings[r].infinite_scroll_buffer)),s="",l=malinkySettings[r].loading_more_posts_text,c=malinkySettings[r].load_more_button_text,m=malinkySettings[r].pagination_wrapper,d=(t(document).height()-t(m).offset().top,malinkySettings[r].paging_type),u=malinkySettings[r].posts_wrapper,y=malinkySettings[r].post_wrapper,f=parseInt(malinkySettings.max_num_pages),k=parseInt(malinkySettings.next_page_number),_=malinkySettings[r].next_page_selector,S=t(malinkySettings[r].next_page_selector).attr("href")||malinkySettings.next_page_url,h=++n;a()}}(jQuery);