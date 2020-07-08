<?php
require_once('../../../../../wp-load.php');
$secretkey = get_option('erphpdown_paypy_key');

$sign = $_POST['sign'];
$total_fee = $_POST['qr_price'];
$extension= $_POST['extension'];
$out_trade_no = $wpdb->escape($_POST['order_id']);

if($sign == md5(md5($_POST['order_id']).$secretkey)){
	global $wpdb, $wppay_table_name;
	if(strstr($out_trade_no,'wppay')){
		$order=$wpdb->get_row("select * from $wppay_table_name where order_num='".$out_trade_no."'");
		if($order){
			if(!$order->order_status){
				$total_fee = $order->post_price;
				$wpdb->query("UPDATE $wppay_table_name SET order_status=1 WHERE order_num = '".$out_trade_no."'");

				$postUserId=get_post($order->post_id)->post_author;
				$ice_ali_money_author = get_option('ice_ali_money_author');
				if($ice_ali_money_author){
					addUserMoney($postUserId,$total_fee*get_option('ice_proportion_alipay')*$ice_ali_money_author/100);
				}elseif($ice_ali_money_author == '0'){

				}else{
					addUserMoney($postUserId,$total_fee*get_option('ice_proportion_alipay'));
				}

				if($order->user_id){
					$data=get_post_meta($order->post_id, 'down_url', true);
					$ppost = get_post($order->post_id);
					erphpAddDownloadByUid($ppost->post_title,$order->post_id,$order->user_id,$total_fee*get_option('ice_proportion_alipay'),1,'',$ppost->post_author);
				}
			}
		}
	}else{
		$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$out_trade_no."'");
		if($money_info){
			if(!$money_info->ice_success){
				$total_fee = $money_info->ice_money;

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
				$wpdb->query("UPDATE $wpdb->icemoney SET ice_money = '".$total_fee*get_option('ice_proportion_alipay')."',ice_success=1, ice_success_time = '".date("Y-m-d H:i:s")."' WHERE ice_num = '".$out_trade_no."'");

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
	}
	echo 'success';
}