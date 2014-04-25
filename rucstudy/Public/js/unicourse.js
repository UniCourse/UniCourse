//////////////////////////////////////////////////
/////////////////ready///////////////////////////////
//////////////////////////////////////////////////////////////

$(document).ready(function() {

	/*****************cornor部分*******************/
	//“回顶部”消失出现
	$("#btn-totop").hide();
	$(window).scroll(function() {
		if ($(window).scrollTop() > 100) {
			$("#btn-totop").fadeIn(500);
		} else {
			$("#btn-totop").fadeOut(500);
		}
	});
	//反馈加一个title项
	$("#feedback-title").val($("title").text());
	//反馈点击后的处理
	$("#btn-submitfeedback").click(function(event) {
		$s=$(this);
		$.ajax({
			url:"",
			data:$s.closest('form').serialize(),
			success:function(data){
				if(data=="1"){
					alert("感谢您对我们的建议~我们会马上处理的~");
					CA(1);
				}
			},
			error:function(){}
		});		
		return false;
	});

	/************header部分************/

	//登出时清除cookie
	$("#btn-logout").click(function() {
		$.cookie('u',"");
		$.cookie('p',"");
	});
	//切换主题
	$(".list-theme-item").click(function(event) {
		var theme=$(this).attr("data-theme");
		var dm=document.domain;
		$.cookie('theme',theme,{expires:30,path:'/',domain:dm});
		location.reload();
	});
	//页面加载时加载主题
	setTheme();
	/******************全局**************************/
	window.alert = bootbox.alert;


	//当前选项添加“active”类
	//前提：id为info的data-activeitem属性存在且唯一
	if($("#info")){
		var s=$("#info").attr('data-activeitem');
		if(s){
			setActive($(s));
		}
		var t=$("#info").attr('data-activeheader');
		if(t){
			$(t).addClass('active');
		}
	}
	//重写一下，把dropdownmenu的click改为hover触发
	$('.dropdown-toggle').dropdownHover();


});





/***************************************************************************/
/*																			*/
/*						以下为自建函数									   */
/*																			*/
/*																			*/
/***************************************************************************/

//====表单验证公共解决办法====
//=======公用验证函数=======
//thisForm  : 传入 this
//myText    ：待验证的文本，如$("#password").val()
//myPattern ：正则模式
//alertStr  ：验证失败时的提示文本。 不需要提示时传入 ""

function checkSubmit(thisForm, myText, myPattern, alertStr) {
	with(thisForm) {
		var ptn1 = new RegExp(myPattern);
		if (!ptn1.test(myText)) {
			if (alertStr != "") alert(alertStr);
			return false;
		}
		return true;
	}
}

function checkFormat(text, pattern) {
	var ptn = new RegExp(pattern);
	if (!ptn.test(text)) {
		return false;
	}
	return true;
}

/*====需要的正则表达式====
学号：	"^[0-9]{10}$"
工号：	"^[0-9]{8}$"
6位密码：	".{6,}"
不能为空：	"[^ \t\n]+"
*/

/*=====使用示例======
登陆界面中：
if(checkSubmit(this, v, "[0-9]{8}", "") || checkSubmit(this, v, "[0-9]{10}", "")) {
	//检测学工号是否为8位或十位
}
else {
	//非法输入
}
*/

/////////////////////////////////////////////////
///////////////////////全局/////////////////////
////////////////////////////////////////////////


//以下自建函数，清除alert

function clearAlert(time) {
	if (!time) {
		time = 0;
	}
	setTimeout(function() {
		$(".bootbox").modal("hide");
	}, time * 2000);
}

function CA(time) {
	if (!time) {
		time = 0;
	}
	setTimeout(function() {
		$(".bootbox").modal("hide");
	}, time * 2000);
}

function CAR(time) {
	if (!time) {
		time = 0;
	}
	setTimeout(function() {
		location.reload();
	}, time * 2000);
}


//list-group-item 设置为当前（Active）项
function setActive($s){
	$s.append("<span class='glyphicon glyphicon-chevron-right pull-right'></span>");
}


///////////////////////////////////////////////////////////
////////////////////header部分///////////////////////////////
///////////////////////////////////////////////////////////



/////////////////////加载主题//////////////////////////
function setTheme(){
	var theme=$.cookie("theme");
	if(theme){
		$("#theme").attr("href",theme);
	}		
}
/////////////////////////////////////////////////////////////////
/////////////////////////corner部分/////////////////////////////////
//////////////////////////////////////////////////////////////////////


////////////////////////////////////回顶部//////////////////////////

function goTop(acceleration, time) {
	acceleration = acceleration || 0.3;
	time = time || 3;
	var x1 = 0;
	var y1 = 0;
	var x2 = 0;
	var y2 = 0;
	var x3 = 0;
	var y3 = 0;
	if (document.documentElement) {
		x1 = document.documentElement.scrollLeft || 0;
		y1 = document.documentElement.scrollTop || 0;
	}
	if (document.body) {
		x2 = document.body.scrollLeft || 0;
		y2 = document.body.scrollTop || 0;
	}
	var x3 = window.scrollX || 0;
	var y3 = window.scrollY || 0;

	// 滚动条到页面顶部的水平距离
	var x = Math.max(x1, Math.max(x2, x3));
	// 滚动条到页面顶部的垂直距离
	var y = Math.max(y1, Math.max(y2, y3));
	// 滚动距离 = 目前距离 / 速度, 因为距离原来越小, 速度是大于 1 的数, 所以滚动距离会越来越小
	var speed = 1 + acceleration;
	window.scrollTo(Math.floor(x / speed), Math.floor(y / speed));
	// 如果距离不为零, 继续调用迭代本函数
	if (x > 0 || y > 0) {
		var invokeFunction = "goTop(" + acceleration + ", " + time + ")";
		window.setTimeout(invokeFunction, time);
	}
}
/********************************************************/
/**angular http修正****************************************/

var app=angular.module('Unicourse', [], function($httpProvider)
{
  // Use x-www-form-urlencoded Content-Type
  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
 
  // Override $http service's default transformRequest
  $httpProvider.defaults.transformRequest = [function(data)
  {
    /**
     * The workhorse; converts an object to x-www-form-urlencoded serialization.
     * @param {Object} obj
     * @return {String}
     */
    var param = function(obj)
    {
      var query = '';
      var name, value, fullSubName, subName, subValue, innerObj, i;
      
      for(name in obj)
      {
        value = obj[name];
        
        if(value instanceof Array)
        {
          for(i=0; i<value.length; ++i)
          {
            subValue = value[i];
            fullSubName = name + '[' + i + ']';
            innerObj = {};
            innerObj[fullSubName] = subValue;
            query += param(innerObj) + '&';
          }
        }
        else if(value instanceof Object)
        {
          for(subName in value)
          {
            subValue = value[subName];
            fullSubName = name + '[' + subName + ']';
            innerObj = {};
            innerObj[fullSubName] = subValue;
            query += param(innerObj) + '&';
          }
        }
        else if(value !== undefined && value !== null)
        {
          query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
        }
      }
      
      return query.length ? query.substr(0, query.length - 1) : query;
    };
    
    return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
  }];
});

//下面这个使dropdown的单击触发改为hover触发，需要在ready里$('.dropdown-toggle').dropdownHover(options);
/************************************************
options

    delay: (optional) The delay in miliseconds. This is the time to wait before closing a dropdown when the mouse is no longer over the dropdown or the button/nav item that activated it. Defaults to 500.
    instantlyCloseOthers: (optional) A boolean value that when true, will instantly close all other dropdowns matched by the selector used when you activate a new navigation. This is nice for when you have dropdowns close together that may overlap. Default is true.

***********************************************/
/*
 * Project: Twitter Bootstrap Hover Dropdown
 * Author: Cameron Spear
 * Contributors: Mattia Larentis
 *
 * Dependencies?: Twitter Bootstrap's Dropdown plugin
 *
 * A simple plugin to enable twitter bootstrap dropdowns to active on hover and provide a nice user experience.
 *
 * No license, do what you want. I'd love credit or a shoutout, though.
 *
 * http://cameronspear.com/blog/twitter-bootstrap-dropdown-on-hover-plugin/
 */
;(function($, window, undefined) {
    // outside the scope of the jQuery plugin to
    // keep track of all dropdowns
    var $allDropdowns = $();

    // if instantlyCloseOthers is true, then it will instantly
    // shut other nav items when a new one is hovered over
    $.fn.dropdownHover = function(options) {

        // the element we really care about
        // is the dropdown-toggle's parent
        $allDropdowns = $allDropdowns.add(this.parent());

        return this.each(function() {
            var $this = $(this).parent(),
                defaults = {
                    delay: 500,
                    instantlyCloseOthers: true
                },
                data = {
                    delay: $(this).data('delay'),
                    instantlyCloseOthers: $(this).data('close-others')
                },
                options = $.extend(true, {}, defaults, options, data),
                timeout;

            $this.hover(function() {
                if(options.instantlyCloseOthers === true)
                    $allDropdowns.removeClass('open');

                window.clearTimeout(timeout);
                $(this).addClass('open');
            }, function() {
                timeout = window.setTimeout(function() {
                    $this.removeClass('open');
                }, options.delay);
            });
        });
    };

    $('[data-hover="dropdown"]').dropdownHover();
})(jQuery, this);
