<?php
/*
	template name: 个人中心
	description: template for mobantu.com modown theme 
*/
if(!is_user_logged_in()){
  // header("Location:".get_permalink(MBThemes_page("template/login.php")));
  	header("Location:".get_permalink('wxlogin'));
}
get_header();
if(wp_is_erphpdown_active()){
	get_template_part("module/user-erphpdown");
}else{
	get_template_part("module/user");
}
?>

<?php get_footer();?>