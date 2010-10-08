$(window).load(function() {
  var favlink = $("#f-view-link");
  
  $(".icon-f").each(function(){
    var elem = $(this);
    var href = elem.attr("href");
    function clickFavourite(e){
      elem.addClass("f-working");
      $.getJSON(href,function (data){
        elem.removeClass("f-working");
        if (data.favourite){
          elem.addClass("f-selected");
          elem.attr("title",data.title);
        }
        else{
          elem.removeClass("f-selected");
          elem.attr("title",data.title);
        }
        if (data.hasfavourites){
          favlink.css('display','block');
        }else{
          favlink.css('display','none');
        }
        
      });
      return false;
    }
    elem.bind("click",clickFavourite);
  });
});