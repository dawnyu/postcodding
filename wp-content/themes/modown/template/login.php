<?php 
/*
	template name: 登录页面
	description: update login to wx by ylm
*/
	if(is_user_logged_in()){
		if(isset($_GET["redirect_to"])){
			header("Location:".$_GET["redirect_to"]);
		}else{
			header("Location:".get_permalink(MBThemes_page('template/user.php')));
		}
	} else {
    header("Location:/wxlogin?redirect_to=vip");
  }
?>