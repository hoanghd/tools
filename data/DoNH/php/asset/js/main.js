if('undefined' == typeof(VS1K5716)){
    function VS1K5716(){}
}

$this = VS1K5716;

VS1K5716.signUp = function(){
	var form = $("#signUpForm");
	if($this.valid(form)){
		form.get(0).submit();	
	}
};

VS1K5716.login = function(){
	var form = $("#loginForm");
	if($this.valid(form)){
		form.get(0).submit();	
	}
};

VS1K5716.lostPassword = function(){
	var form = $("#lostForm");
	if($this.valid(form)){
		form.get(0).submit();	
	}
};

VS1K5716.changePassword = function(){
	var form = $("#changepwForm");
	if($this.valid(form)){
		form.get(0).submit();	
	}
};

VS1K5716.valid = function(id){
	var valid = true;
	
	$("[valid]", id).each(function(){
		var elm   = $(this);
		var val   = $.trim(elm.val());
		var data  = elm.attr('valid').split('|');
		
		$.each(data, function(index, value){
			if(valid){
				if(value == 'req' && !val){
					valid = false;
					elm.notify("Bạn không được để trống trường này.");
					return;
				}
				else if(value == 'email' && !$this.validation.isEmail(val)){
					valid = false;
					elm.notify("Địa chỉ email không hơp lệ.");
					return;
				}
				else if(value == 'username' && !$this.validation.username(val)){
					valid = false;
					elm.notify("Vui lòng nhập chữ cái, số và dấu chấm.");
					return;
				}
				else if(value.startsWith('equal') && !$this.validation.equal(val, value, id)){
					valid = false;
					elm.notify("Mật khẩu này không khớp.");
					return;
				}
				else if(value.startsWith('length') && !$this.validation.length(val, value, elm)){
					valid = false;
					return;
				}
				else if(value == 'captcha' && (grecaptcha.getResponse().length == 0)){
					valid = false;
					elm.notify("Vui lòng chọn Captcha");
					return;	
				}
			}
		});
	});
	
	return valid;
};

VS1K5716.replace = function(str, data){
    return str.replace(/{([^}]+)}/g, function(match, key, offset, old){
        return ((data && data[key]) ? data[key] : '');
    });
};

VS1K5716.validation = {
	isEmail: function(email){
		var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	},
	username: function(val){
		var re = /^[a-zA-Z0-9\.]+$/;
		return re.test(val);
	}	,
	equal: function(val, value, id){
		var compareVal = $(value.substring(5), id).val();
		return (compareVal == val);
	},
	length: function(val, value, elm){
		var cond = JSON.parse(value.substring(6).replace(/\'/gi, '"'));
		var len = val.length;
		if(cond.min && cond.max){
			if(len<cond.min || len>cond.max){
				elm.notify($this.replace("Vui lòng sử dụng từ {min} đến {max} ký tự.", cond));
				return false;
			}
		}
		else if(cond.min){
			if(len<cond.min){
				elm.notify($this.replace("Vui lòng sử dụng ít nhất {min} ký tự.", cond));
				return false;
			}
		}
		else if(cond.max){
			if(len>cond.max){
				elm.notify($this.replace("Vui lòng sử dụng nhiều nhất {max} ký tự.", cond));
				return false;
			}
		}
		
		return true;
	}
};

if(!String.prototype.startsWith){
    String.prototype.startsWith = function(searchString, position){
      position = position || 0;
      return this.substr(position, searchString.length) === searchString;
  };
}

$(document).ready(function(){
   $(".vs1k5716 .nav a").click(function(){
		$('.vs1k5716 .nav a').removeClass('active');  	
		$(this).addClass('active'); 
		
		$('.vs1k5716 .tab-content > .tab-pane').addClass('hide');
		$($(this).attr('ref')).removeClass('hide');
   });
});