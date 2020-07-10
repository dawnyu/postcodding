jQuery(function($){
    $(".ews-button").click(function(){
        var that = $(this),
            code = that.prev().val();
        if(code){
            if(!that.hasClass("disabled")){
                that.text("验证中...");
                that.addClass("disabled");
                $.post(ews_ajax_url, {
                    "action": "ews_login",
                    "code": code
                }, function(data) {
                    if(data.status == "1"){
                        location.reload();
                    }else{
                        that.removeClass("disabled");
                        that.text("验证登录");
                        if(typeof(logtips) != "undefined"){
                            logtips("登录失败！请检查是否验证码已过期～");
                        }else if(typeof(layer) != "undefined"){
                            layer.msg("登录失败！请检查是否验证码已过期～");
                        }else{
                            alert("登录失败！请检查是否验证码已过期～");
                        }
                    }
                });
            }
        }else{
            if(typeof(logtips) != "undefined"){
                logtips("请输入验证码～");
            }else if(typeof(layer) != "undefined"){
                layer.msg("请输入验证码～");
            }else{
                alert("请输入验证码～");
            }
        }
        return false;
    });

    function parseUrl(_url) {
      const pattern = /(\w+)=(\w+|[\u4E00-\u9FA5]+)/ig;
      const parames = {};
      _url.replace(pattern, (a, b, c) => {
        parames[b] = decodeURIComponent(c);
      });
      return parames;
    }

    $(".ews-bind-button").click(function(){
      console.log('.ews-bind-button')
        var that = $(this),
            code = that.prev().val();
        if(code){
            if(!that.hasClass("disabled")){
                that.text("验证中...");
                that.addClass("disabled");
                $.post(ews_ajax_url, {
                    "action": "ews_bind",
                    "code": code
                }, function(data) {
                    if(data.status == "1"){
                        var obj = parseUrl(window.location.href);
                        debugger
                        if (obj.redirect_to) {
                          history.go(-2);
                        } else {
                          location.reload();
                        }
                    }else if(data.status == "2"){
                        that.removeClass("disabled");
                        that.text("验证绑定");
                        if(typeof(layer) != "undefined"){
                            layer.msg("绑定失败！此微信已绑定过其他账号了～");
                        }else{
                            alert("绑定失败！此微信已绑定过其他账号了～");
                        }
                    }else{
                        that.removeClass("disabled");
                        that.text("验证绑定");
                        if(typeof(layer) != "undefined"){
                            layer.msg("绑定失败！请检查是否验证码已过期～");
                        }else{
                            alert("绑定失败！请检查是否验证码已过期～");
                        }
                    }
                });
            }
        }else{
            if(typeof(layer) != "undefined"){
                layer.msg("请输入验证码～");
            }else{
                alert("请输入验证码～");
            }
        }
        return false;
    });
});
