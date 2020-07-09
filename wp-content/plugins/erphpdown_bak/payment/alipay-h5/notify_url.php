<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */
require_once('../../../../../wp-config.php');
require_once("config.php");
require_once 'wappay/service/AlipayTradeService.php';

$sign = $_POST['sign'];
$signType = $_POST['sign_type'];
$total_fee= $_POST['total_amount'];
$out_trade_no = $wpdb->escape($_POST['out_trade_no']);
$trade_no = $wpdb->escape($_POST['trade_no']);
$buyer_logon_id = $wpdb->escape($_POST['buyer_logon_id']);
$status = $_POST['trade_status'];

ksort($_POST); //排序post参数
reset($_POST); //内部指针指向数组中的第一个元素
$signStr = '';//初始化
foreach ($_POST AS $key => $val) { //遍历POST参数
    if ($val == '' || $key == 'sign' || $key == 'sign_type') continue; //跳过这些不签名
    if ($signStr) $signStr .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
    $signStr .= "$key=$val"; //拼接为url参数形式
}
$signStr = str_replace('\"', '"', $signStr);
$res = "-----BEGIN PUBLIC KEY-----\n".wordwrap($config['alipay_public_key'], 64, "\n", true) ."\n-----END PUBLIC KEY-----";
$result = (bool)openssl_verify($signStr, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);

if($result){
	if($status == 'TRADE_FINISHED' || $status == 'TRADE_SUCCESS') {

		global $wpdb;
		$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$out_trade_no."'");
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
				$wpdb->query("UPDATE $wpdb->icemoney SET ice_money = '".$total_fee*get_option('ice_proportion_alipay')."', ice_alipay = '".$buyer_logon_id."',ice_success=1, ice_success_time = '".date("Y-m-d H:i:s")."' WHERE ice_num = '".$out_trade_no."'");

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
	        
		echo "success";
	}
		
}else {
    //验证失败
    echo "fail";

}

