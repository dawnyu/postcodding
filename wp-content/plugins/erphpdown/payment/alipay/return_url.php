<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */
require_once('../../../../../wp-load.php');
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号

	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号

	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];


    if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
		
    }else if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
    	global $wpdb;
		$total_fee=$_GET['total_fee'];
		$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$wpdb->escape($_GET['out_trade_no'])."'");
		if($money_info){
			if(!$money_info->ice_success){
				if(!$money_info->ice_post_id && !$money_info->ice_user_type){
					$epd_game_price  = get_option('epd_game_price');
					if($epd_game_price){
						$cnt = count($epd_game_price['buy']);
						for($i=0; $i<$cnt;$i++){
							if($total_fee == $epd_game_price['buy'][$i]){
								$total_fee = $epd_game_price['get'][$i];
								break;
							}
						}
					}
				}
				addUserMoney($money_info->ice_user_id, $total_fee*get_option('ice_proportion_alipay'));
				$wpdb->query("UPDATE $wpdb->icemoney SET ice_money = '".$total_fee*get_option('ice_proportion_alipay')."', ice_alipay = '".$wpdb->escape($_GET['buyer_email'])."',ice_success=1, ice_success_time = '".date("Y-m-d H:i:s")."' WHERE ice_num = '".$wpdb->escape($_GET['out_trade_no'])."'");

				if($money_info->ice_post_id){
	                $okMoney=erphpGetUserOkMoneyById($money_info->ice_user_id);
	                $postid = $money_info->ice_post_id;
	                $price=$total_fee*get_option('ice_proportion_alipay');
	                if($okMoney >= $price){
	                    if(erphpSetUserMoneyXiaoFeiByUid($price,$money_info->ice_user_id))
	                    {
	                        $subject   = get_post($postid)->post_title;
	                        $postUserId=get_post($postid)->post_author;
	                        $data=get_post_meta($postid, 'down_url', true);
	                        $result=erphpAddDownloadByUid($subject, $postid, $money_info->ice_user_id,$price,1, '', $postUserId);
	                        if($result)
	                        {
	                        	$down_activation = get_post_meta($postid, 'down_activation', true);
	                        	if($down_activation && function_exists('doErphpAct')){
									$activation_num = doErphpAct($money_info->ice_user_id,$postid);
									$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_url='".$result."'");
									$cuser = get_user_by('id',$money_info->ice_user_id);
									if($cuser && $cuser->user_email){
										wp_mail($cuser->user_email, '【'.$subject.'】注册码', '您购买的资源【'.$subject.'】注册码：'.$activation_num);
									}
								}
								
	                            $ice_ali_money_author = get_option('ice_ali_money_author');
	                            if($ice_ali_money_author){
	                                addUserMoney($postUserId,$price*$ice_ali_money_author/100);
	                            }elseif($ice_ali_money_author == '0'){

	                            }else{
	                                addUserMoney($postUserId,$price);
	                            }

	                            $EPD = new EPD();
								$EPD->doAff($price, $money_info->ice_user_id);
	                        } 
	                    }
	                }
	            }elseif($money_info->ice_user_type){
					addUserMoney($money_info->ice_user_id, '-'.$total_fee*get_option('ice_proportion_alipay'));
					userSetMemberSetData($money_info->ice_user_type,$money_info->ice_user_id);
					addVipLogByAdmin($total_fee*get_option('ice_proportion_alipay'), $money_info->ice_user_type, $money_info->ice_user_id);

					$EPD = new EPD();
					$EPD->doAff($total_fee*get_option('ice_proportion_alipay'), $money_info->ice_user_id);
						
				}
			}
		}
		$re = get_option('erphp_url_front_success');
		if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){
		    $re = $_COOKIE['erphpdown_return'];
		}
		if($re)
			wp_redirect($re);
		else{
			echo 'success';
			exit;
		}
		
    }else {
      	echo 'charge error';
		exit;
    }
		
	

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "verify error";
}
?>
       
