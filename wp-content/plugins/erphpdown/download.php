<?php
header("Content-type:text/html;character=utf-8");
require_once('../../../wp-load.php');
date_default_timezone_set('Asia/Shanghai');
function assignPageTitle(){
	return "文件下载";
}
add_filter('wp_title', 'assignPageTitle');
if(!is_user_logged_in()){
	wp_die('请先登录网站！');
}

$postid=isset($_GET['postid']) && is_numeric($_GET['postid']) ?intval($_GET['postid']) :false;
$url=isset($_GET['url']) ? $_GET['url'] :false;
$key=isset($_GET['key']) ? $_GET['key'] :false;
$iframe=isset($_GET['iframe']) ? $_GET['iframe'] : 0;
$index=isset($_GET['index']) ? $_GET['index'] : '';

$postid = esc_sql($postid);
$url = esc_sql($url);
$key = esc_sql($key);
$index = esc_sql($index);
$index_name = '';

if($postid==false && $url==false ){
	wp_die("下载信息错误！");
}

if ($postid){
	$ypost = get_post($postid);
	if(!$ypost){
		wp_die("下载信息错误！");
	}
	$isDown=FALSE;

	if($index){
		$urls = get_post_meta($postid, 'down_urls', true);
		if($urls){
			$cnt = count($urls['index']);
			if($cnt){
				for($i=0; $i<$cnt;$i++){
					if($urls['index'][$i] == $index){
    					$data = $urls['url'][$i];
    					$index_name = $urls['name'][$i];
    					$price = $urls['price'][$i];
    					break;
    				}
				}
			}
		}
	}else{
		$data=get_post_meta($postid, 'down_url', true);
		$price=get_post_meta($postid, 'down_price', true);
	}

	$memberDown=get_post_meta($postid, 'member_down',TRUE);
	$userType=getUsreMemberType();
	$user_info=wp_get_current_user();
	$days=get_post_meta($postid, 'down_days', true);
	if($index){
		$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
	}else{
		$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
	}
	
	if($days > 0){
		$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($hasdown_info->ice_time)));
		$nowDate = date('Y-m-d H:i:s');
		if(strtotime($nowDate) > strtotime($lastDownDate)){
			$hasdown_info = null;
		}
	}

	if(!$hasdown_info){
		if(!$price && $memberDown != 4 && $memberDown != 8 && $memberDown != 9){
			$erphp_reg_times  = get_option('erphp_reg_times');
			if(!$userType && $erphp_reg_times > 0){
				if( checkDownLog($user_info->ID,$postid,$erphp_reg_times,erphpGetIP()) ){

				}else{
					wp_die("普通用户每天只能下载".$erphp_reg_times."个免费资源！<a href='".get_option('erphp_url_front_vip')."'>升级VIP下载更多资源</a>","友情提示");
				}
			}

		}else{
			if($memberDown == 3 || $memberDown == 4 || $memberDown == 6 || $memberDown == 7 || $memberDown == 8 || $memberDown == 9){
				
				if($userType){
					
					$erphp_life_times    = get_option('erphp_life_times');
					$erphp_year_times    = get_option('erphp_year_times');
					$erphp_quarter_times = get_option('erphp_quarter_times');
					$erphp_month_times  = get_option('erphp_month_times');
					$erphp_day_times  = get_option('erphp_day_times');

					if($userType == 6 && $erphp_day_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_day_times,erphpGetIP()) ){

						}else{
							wp_die("体验VIP用户每天只能免费下载".$erphp_day_times."个VIP资源！","友情提示");
						}
					}elseif($userType == 7 && $erphp_month_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_month_times,erphpGetIP()) ){

						}else{
							wp_die("包月VIP用户每天只能免费下载".$erphp_month_times."个VIP资源！","友情提示");
						}
					}elseif($userType == 8 && $erphp_quarter_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_quarter_times,erphpGetIP()) ){

						}else{
							wp_die("包季VIP用户每天只能免费下载".$erphp_quarter_times."个VIP资源！","友情提示");
						}
					}elseif($userType == 9 && $erphp_year_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_year_times,erphpGetIP()) ){

						}else{
							wp_die("包年VIP用户每天只能免费下载".$erphp_year_times."个VIP资源！","友情提示");
						}
					}elseif($userType == 10 && $erphp_life_times > 0){
						if( checkDownLog($user_info->ID,$postid,$erphp_life_times,erphpGetIP()) ){

						}else{
							wp_die("终身VIP用户每天只能免费下载".$erphp_life_times."个VIP资源！","友情提示");
						}
					}
					
				}
			}
		}
	}


	if(strlen($data) > 2)
	{
		$memberDown=get_post_meta($postid,'member_down',TRUE);
		$user_info=wp_get_current_user();
		$userType=getUsreMemberType();
		if($hasdown_info){
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && $userType && ($memberDown ==3 || $memberDown ==4))
		{
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && ($userType == 9 || $userType == 10) && ($memberDown ==6 || $memberDown ==8) )
		{
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && $userType == 10 && ($memberDown ==7 || $memberDown ==9) )
		{
			$isDown=true;
			$pp = $postid;
		}
		else 
		{
			if( empty($price) || $price==0 )
			{
				if( ($memberDown ==4 && !$userType) || ($memberDown ==8 && $userType < 9) || ($memberDown ==9 && $userType < 10) ){
					
				}else{
					$isDown=true;
					$pp = $postid;
				}
			}
		}
	}

	if(!$isDown)
	{
		wp_die('下载信息错误！');
	}
}elseif($url){
	$user_info=wp_get_current_user();

	$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_url='".esc_sql($url)."' and ice_user_id=".$user_info->ID." order by ice_time desc");
	if($down_info->ice_price > 0){
		$downPostId=$down_info->ice_post;
		$days=get_post_meta($downPostId, 'down_days', true);
		if($days > 0){
			$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
			$nowDate = date('Y-m-d H:i:s');
			if(strtotime($nowDate) > strtotime($lastDownDate)){
				wp_die('下载权限已过期，请重新购买');
			}
		}
		$pp = $downPostId;
		$postid = $downPostId;
		if($down_info->ice_index){
			$index = $down_info->ice_index;
			$urls = get_post_meta($postid, 'down_urls', true);
			if($urls){
				$cnt = count($urls['index']);
				if($cnt){
					for($i=0; $i<$cnt;$i++){
						if($urls['index'][$i] == $index){
	    					$data = $urls['url'][$i];
	    					$index_name = $urls['name'][$i];
	    					break;
	    				}
					}
				}
			}
		}else{
			$data=get_post_meta($downPostId, 'down_url', true);
		}
	}
	
	if(!$down_info || !$data)
	{
		wp_die('下载信息错误！');
	}
}

$downList=explode("\r\n",trim($data));
$downMsg = '<div class="title"><span>'.($index_name?$index_name:'下载地址').'</span></div>';

if($key){
	if(is_numeric($key)){
		$key=intval($key);
	}else {
		wp_die('下载信息错误！');
	}

	$user_info=wp_get_current_user();
	$file=$downList[$key-1];
	//$file = iconv('UTF-8', 'GBK//TRANSLIT', $file);
	$times=time();
	$md5key=md5($user_info->ID.'erphpdown'.$key.$times.get_option('erphpdown_downkey'));
	$entemp = erphpdown_lock_url($times,get_option('erphpdown_downkey'));

	$file = trim($file);

	header("Location:downloadfile.php?id=".$pp."&filename=".$key."&index=".$index."&md5key=".$md5key."&times=".$times."&session_name=".$entemp);
	exit;
	
}else{
	foreach ($downList as $k=>$v){
		$filepath = trim($downList[$k]);
		if($filepath){
			$filepath = str_replace('：', ': ', $filepath);
			if(strpos($filepath,',')){
				$filearr = explode(',',$filepath);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$downMsg.="<p>文件".($k+1)."地址<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength == 2){
					$downMsg.="<p>".$filearr[0]."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength == 3){
					$downMsg.="<p>".$filearr[0]."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".str_replace('提取码: ', '', $filearr[2])."'>点击下载</a>（".$filearr[2]."）<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr[2])."' href='javascript:;'>复制</a></p>";
				}
			}elseif(strpos($filepath,'  ')){//适用MAC客户端版百度网盘分享
				$filearr = explode('  ',$filepath);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$downMsg.="<p>文件".($k+1)."地址<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength >= 2){
					$filearr2 = explode(':',$filearr[0]);
					$filearr3 = explode(':',$filearr[1]);
					$downMsg.="<p>".$filearr2[0]."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".$filearr3[1]."'>点击下载</a>（提取码: ".$filearr3[1]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr3[1]."' href='javascript:;'>复制</a></p>";
				}
			}elseif(strpos($filepath,' ')){//适用网页版百度网盘分享
				$filearr = explode(' ',$filepath);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$downMsg.="<p>文件".($k+1)."地址<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength == 2){
					$downMsg.="<p>".$filearr[0]."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
				}elseif($arrlength >= 3){
					$downMsg.="<p>".str_replace(':', '', $filearr[0])."<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".$filearr[3]."' >点击下载</a>（".$filearr[2].' '.$filearr[3]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>复制</a></p>";
				}
			}else{
				$downMsg.="<p>文件".($k+1)."地址<a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>点击下载</a></p>";
			}
		}
	}
	$hiddens = get_post_meta($pp,'hidden_content',true);
	if($hiddens){
		$downMsg .='<div class="title"><span>隐藏信息</span></div><div class="hidden-content" style="border:2px dashed #ff5f33;padding:15px;">'.$hiddens.'<a class="erphpdown-copy" data-clipboard-text="'.$hiddens.'" href="javascript:;" style="margin-left:10px;">复制</a></div>';
	}
	
	if(function_exists('MBThemes_erphpdown_download') && !$iframe){
		MBThemes_erphpdown_download($downMsg,$pp);
	}else{
		epd_download_page($downMsg,$pp);
	}
}