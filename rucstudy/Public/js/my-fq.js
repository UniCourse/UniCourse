
function focus($s){
  $.post("{:U('Index/ShowQuestion/focus')}", 
        $s.parent().serialize(), 
        function(data) {
       if (data.status == 1) {
        $s.removeClass('focus').removeClass('btn-success').addClass('unfocus').addClass('btn-warning').text("取消关注");          
          var num=parseInt($s.next("span").text());
          num=num+1;
          $s.next("span").text(num);
          $s.unbind('click');
          $s.click(function(event) {
            unfocus($s);
          });
         
        } else {
          alert("关注失败");
        }        
      }, "json"); 
}
function unfocus($s){
  $.post("{:U('Index/ShowQuestion/unFocus')}", 
        $s.parent().serialize(), 
        function(data) {
        if (data.status == 1) {
          $s.removeClass('unfocus').removeClass('btn-warning').addClass('focus').addClass('btn-success').text("关注");          
          var num=parseInt($s.next("span").text());
          num=num-1;
          $s.next("span").text(num);
          $s.unbind('click');
          $s.click(function(event) {
            focus($s);
          });
        } else {
          alert("取消关注失败");
        }
      }, "json");
}



     $(".cancelfocus").click(function() {
        unfocus($(this));
    });
    $(".focus").click(function() {
      focus($(this));
    });

