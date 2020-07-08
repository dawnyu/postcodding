<?php
require_once('../../../../../wp-load.php');
ksort($_POST); //排序post参数
reset($_POST); //内部指针指向数组中的第一个元素
$codepay_key=get_option('erphpdown_codepay_appsecret'); //这是您的密钥
$sign = '';//初始化
foreach ($_POST AS $key => $val) { //遍历POST参数
    if ($val == '' || $key == 'sign') continue; //跳过这些不签名
    if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
    $sign .= "$key=$val"; //拼接为url参数形式
}
if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
    exit('fail');  //返回失败 继续补单
} else { //合法的数据
    //业务处理
    $pay_id = $_POST['pay_id']; //需要充值的ID 或订单号 或用户名
    $money = (float)$_POST['money']; //实际付款金额
    $total_fee = (float)$_POST['price']; //订单的原价
    $param = $_POST['param']; //自定义参数
    $pay_no = $_POST['pay_no']; //流水号

    global $wpdb;
    $money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$wpdb->escape($pay_id)."'");
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
            $wpdb->query("UPDATE $wpdb->icemoney SET ice_money = '".$total_fee*get_option('ice_proportion_alipay')."',ice_success=1, ice_success_time = '".date("Y-m-d H:i:s")."' WHERE ice_num = '".$wpdb->escape($pay_id)."'");

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

    exit('success'); //返回成功 不要删除哦
}