<?php
if ( !defined('ABSPATH') ) {exit;}
function erphpdown_content_show($content){
	$down_box_hide = get_post_meta(get_the_ID(), 'down_box_hide', true);
	if(!$down_box_hide){
		$content2 = $content;
		$start_down=get_post_meta(get_the_ID(), 'start_down', true);
		$start_down2=get_post_meta(get_the_ID(), 'start_down2', true);
		$start_see=get_post_meta(get_the_ID(), 'start_see', true);
		$start_see2=get_post_meta(get_the_ID(), 'start_see2', true);

		if(is_singular()){
			$days=get_post_meta(get_the_ID(), 'down_days', true);
			$price=get_post_meta(get_the_ID(), 'down_price', true);
			$price_type=get_post_meta(get_the_ID(), 'down_price_type', true);
			$url=get_post_meta(get_the_ID(), 'down_url', true);
			$urls=get_post_meta(get_the_ID(), 'down_urls', true);
			$url_free=get_post_meta(get_the_ID(), 'down_url_free', true);
			$memberDown=get_post_meta(get_the_ID(), 'member_down',TRUE);
			$hidden=get_post_meta(get_the_ID(), 'hidden_content', true);
			$userType=getUsreMemberType();
			$down_info = null;$downMsgFree = '';
			
			$erphp_url_front_vip = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-update-vip.php';
			if(get_option('erphp_url_front_vip')){
				$erphp_url_front_vip = get_option('erphp_url_front_vip');
			}
			$erphp_url_front_login = wp_login_url();
			if(get_option('erphp_url_front_login')){
				$erphp_url_front_login = get_option('erphp_url_front_login');
			}

			if($url_free){
				$downMsgFree .= '<div class="erphpdown-title">免费资源</div><div class="erphpdown-free">';
				$downList=explode("\r\n",$url_free);
				foreach ($downList as $k=>$v){
					$filepath = $downList[$k];
					if($filepath){
						$filepath = str_replace('：', ': ', $filepath);
						if(strpos($filepath,',')){
							$filearr = explode(',',$filepath);
							$arrlength = count($filearr);
							if($arrlength == 1){
								$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 2){
								$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 3){
								$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr[2]."）<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr[2])."' href='javascript:;'>复制</a></div>";
							}
						}elseif(strpos($filepath,'  ')){
							$filearr = explode('  ',$filepath);
							$arrlength = count($filearr);
							if($arrlength == 1){
								$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength >= 2){
								$filearr2 = explode(':',$filearr[0]);
								$filearr3 = explode(':',$filearr[1]);
								$downMsgFree.="<div class='erphpdown-item'>".$filearr2[0]."<a href='".$filearr2[1].':'.$filearr2[2]."' target='_blank' class='erphpdown-down'>点击下载</a>（提取码: ".$filearr3[1]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr3[1]."' href='javascript:;'>复制</a></div>";
							}
						}elseif(strpos($filepath,' ')){
							$filearr = explode(' ',$filepath);
							$arrlength = count($filearr);
							if($arrlength == 1){
								$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 2){
								$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength >= 3){
								$downMsgFree.="<div class='erphpdown-item'>".str_replace(':', '', $filearr[0])."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr[2].' '.$filearr[3]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>复制</a></div>";
							}
						}else{
							$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
						}
					}
				}

				$downMsgFree .= '</div>';
				if(get_option('ice_tips_free')) $downMsgFree.='<div class="erphpdown-tips erphpdown-tips-free">'.get_option('ice_tips_free').'</div>';
				if($start_down2 || $start_down){
					$downMsgFree .= '<div class="erphpdown-title">付费资源</div>';
				}
			}
			
			if($start_down2){
				if($url){
					$content.='<fieldset class="erphpdown" id="erphpdown"><legend>资源下载</legend>'.$downMsgFree;
					
					$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
					$wppay = new EPD(get_the_ID(), $user_id);
					if($wppay->isWppayPaid() || !$price){
						$downList=explode("\r\n",trim($url));
						foreach ($downList as $k=>$v){
							$filepath = trim($downList[$k]);
							if($filepath){
								$filepath = str_replace('：', ': ', $filepath);
								if(strpos($filepath,',')){
									$filearr = explode(',',$filepath);
									$arrlength = count($filearr);
									if($arrlength == 1){
										$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength == 2){
										$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength == 3){
										$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr[2]."）<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr[2])."' href='javascript:;'>复制</a></div>";
									}
								}elseif(strpos($filepath,'  ')){
									$filearr = explode('  ',$filepath);
									$arrlength = count($filearr);
									if($arrlength == 1){
										$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength >= 2){
										$filearr2 = explode(':',$filearr[0]);
										$filearr3 = explode(':',$filearr[1]);
										$downMsg.="<div class='erphpdown-item'>".$filearr2[0]."<a href='".$filearr2[1].':'.$filearr2[2]."' target='_blank' class='erphpdown-down'>点击下载</a>（提取码: ".$filearr3[1]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr3[1]."' href='javascript:;'>复制</a></div>";
									}
								}elseif(strpos($filepath,' ')){
									$filearr = explode(' ',$filepath);
									$arrlength = count($filearr);
									if($arrlength == 1){
										$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength == 2){
										$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength >= 3){
										$downMsg.="<div class='erphpdown-item'>".str_replace(':', '', $filearr[0])."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr[2].' '.$filearr[3]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>复制</a></div>";
									}
								}else{
									$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
								}
							}
						}
						$content .= $downMsg;	
					}else{
						if($memberDown == 3 && $userType){
							$downList=explode("\r\n",trim($url));
							foreach ($downList as $k=>$v){
								$filepath = $downList[$k];
								if($filepath){
									$filepath = str_replace('：', ': ', $filepath);
									if(strpos($filepath,',')){
										$filearr = explode(',',$filepath);
										$arrlength = count($filearr);
										if($arrlength == 1){
											$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
										}elseif($arrlength == 2){
											$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
										}elseif($arrlength == 3){
											$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr[2]."）<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr[2])."' href='javascript:;'>复制</a></div>";
										}
									}elseif(strpos($filepath,'  ')){
										$filearr = explode('  ',$filepath);
										$arrlength = count($filearr);
										if($arrlength == 1){
											$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
										}elseif($arrlength >= 2){
											$filearr2 = explode(':',$filearr[0]);
											$filearr3 = explode(':',$filearr[1]);
											$downMsg.="<div class='erphpdown-item'>".$filearr2[0]."<a href='".$filearr2[1].':'.$filearr2[2]."' target='_blank' class='erphpdown-down'>点击下载</a>（提取码: ".$filearr3[1]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr3[1]."' href='javascript:;'>复制</a></div>";
										}
									}elseif(strpos($filepath,' ')){
										$filearr = explode(' ',$filepath);
										$arrlength = count($filearr);
										if($arrlength == 1){
											$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
										}elseif($arrlength == 2){
											$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
										}elseif($arrlength >= 3){
											$downMsg.="<div class='erphpdown-item'>".str_replace(':', '', $filearr[0])."<a href='".$filearr[1]."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr[2].' '.$filearr[3]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>复制</a></div>";
										}
									}else{
										$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}
								}
							}
							$content .= $downMsg;
						}else{
							if($url){
								$tname = '资源下载';
							}else{
								$tname = '内容查看';
							}
							if($memberDown == 3){
								$content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即支付</a>&nbsp;&nbsp;<b>或</b>&nbsp;&nbsp;升级VIP后免费<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
							}else{
								$content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即支付</a>';	
							}
						}
					}
					
					if(get_option('ice_tips')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
					$content.='</fieldset>';
				}

			}elseif($start_down){
				$content.='<fieldset class="erphpdown" id="erphpdown"><legend>资源下载</legend>'.$downMsgFree;
				if($price_type){
					if($urls){
						$cnt = count($urls['index']);
            			if($cnt){
            				for($i=0; $i<$cnt;$i++){
            					$index = $urls['index'][$i];
            					$index_name = $urls['name'][$i];
            					$price = $urls['price'][$i];
            					$index_url = $urls['url'][$i];
            					$content .= '<fieldset class="erphpdown-child"><legend>'.$index_name.'</legend>';
            					if(is_user_logged_in()){
									if($price){
										if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
											$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
									}else{
										if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
											$content.='此资源为免费资源';
									}

									if($price || $memberDown == 4 || $memberDown == 8 || $memberDown == 9){
										global $wpdb;
										$user_info=wp_get_current_user();
										$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
										if($days > 0){
											$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
											$nowDate = date('Y-m-d H:i:s');
											if(strtotime($nowDate) > strtotime($lastDownDate)){
												$down_info = null;
											}
										}

										if($memberDown > 1){
											$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
											if($userType){
												$vipText = '';
											}
											if($memberDown==3 && $down_info==null){
												$content.='（VIP免费'.$vipText.'）';
											}elseif ($memberDown==2 && $down_info==null){
												$content.='（VIP 5折'.$vipText.'）';
											}elseif ($memberDown==5 && $down_info==null){
												$content.='（VIP 8折'.$vipText.'）';
											}elseif ($memberDown==6 && $down_info==null){
												if($userType < 9){
													$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级年费VIP</a>';
												}
												$content.='（年费VIP免费'.$vipText.'）';
											}elseif ($memberDown==7 && $down_info==null){
												if($userType < 10){
													$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级终身VIP</a>';
												}
												$content.='（终身VIP免费'.$vipText.'）';
											}elseif ($memberDown==4){
												if($userType){
													$content.='此资源为VIP专享资源';
												}
											}elseif ($memberDown==8){
												if($userType >= 9){
													$content.='此资源为年费VIP专享资源';
												}
											}elseif ($memberDown==9){
												if($userType >= 10){
													$content.='此资源为终身VIP专享资源';
												}
											}
										}

										if($memberDown==4 && $userType==FALSE){
											$content.='抱歉，此资源仅限VIP下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级VIP</a>';
										}elseif($memberDown==8 && $userType < 9){
											$content.='抱歉，此资源仅限年费VIP下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级VIP</a>';
										}elseif($memberDown==9 && $userType < 10){
											$content.='抱歉，此资源仅限终身VIP下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级VIP</a>';
										}else{
											
											if($userType && $memberDown > 1){
												if($memberDown==3 || $memberDown==4){
													if(get_option('erphp_popdown')){
														$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
													}else{
														$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
													}
												}elseif ($memberDown==2 && $down_info==null){
													$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
												}elseif ($memberDown==5 && $down_info==null){
													$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
												}elseif ($memberDown==6 && $down_info==null){
													if($userType == 9){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}elseif($userType == 10){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}else{
														$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
													}
												}elseif ($memberDown==7 && $down_info==null){
													if($userType == 10){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}else{
														$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
													}
												}elseif ($memberDown==8 && $down_info==null){
													if($userType >= 9){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}
												}elseif ($memberDown==9 && $down_info==null){
													if($userType >= 10){
														if(get_option('erphp_popdown')){
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
														}else{
															$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
														}	
													}
												}elseif($down_info){
													if(get_option('erphp_popdown')){
														$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'" class="erphpdown-down erphpdown-down-layui">立即下载</a>';
													}else{
														$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'" class="erphpdown-down" target="_blank">立即下载</a>';
													}
												}
											}else{
												if($down_info && $down_info->ice_price > 0){
													if(get_option('erphp_popdown')){
														$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'" class="erphpdown-down erphpdown-down-layui">已购买，立即下载</a>';
													}else{
														$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.'" class="erphpdown-down" target="_blank">已购买，立即下载</a>';
													}
												}else{
													$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">立即购买</a>';
												}
											}
										}
										
									}else{
										if(get_option('erphp_popdown')){
											$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
										}else{
											$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index."' class='erphpdown-down' target='_blank'>立即下载</a>";
										}
									}
									
								}else{
									if($memberDown == 4 || $memberDown == 8 || $memberDown == 9){
										$content.='抱歉，此资源仅限VIP下载，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
									}else{
										if($price){
											$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
										}else{
											$content.='此资源为免费资源，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
										}
									}
								}
            					$content .= '</fieldset>';
            				}
            			}
					}
				}else{
					
					if(is_user_logged_in()){
						if($price){
							if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
								$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
						}else{
							if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
								$content.='此资源为免费资源';
						}

						if($price || $memberDown == 4 || $memberDown == 8 || $memberDown == 9){
							global $wpdb;
							$user_info=wp_get_current_user();
							$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
							if($days > 0){
								$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
								$nowDate = date('Y-m-d H:i:s');
								if(strtotime($nowDate) > strtotime($lastDownDate)){
									$down_info = null;
								}
							}

							if($memberDown > 1){
								$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
								if($userType){
									$vipText = '';
								}
								if($memberDown==3 && $down_info==null){
									$content.='（VIP免费'.$vipText.'）';
								}elseif ($memberDown==2 && $down_info==null){
									$content.='（VIP 5折'.$vipText.'）';
								}elseif ($memberDown==5 && $down_info==null){
									$content.='（VIP 8折'.$vipText.'）';
								}elseif ($memberDown==6 && $down_info==null){
									if($userType < 9){
										$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级年费VIP</a>';
									}
									$content.='（年费VIP免费'.$vipText.'）';
								}elseif ($memberDown==7 && $down_info==null){
									if($userType < 10){
										$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级终身VIP</a>';
									}
									$content.='（终身VIP免费'.$vipText.'）';
								}elseif ($memberDown==4){
									if($userType){
										$content.='此资源为VIP专享资源';
									}
								}elseif ($memberDown==8){
									if($userType >= 9){
										$content.='此资源为年费VIP专享资源';
									}
								}elseif ($memberDown==9){
									if($userType >= 10){
										$content.='此资源为终身VIP专享资源';
									}
								}
							}

							if($memberDown==4 && $userType==FALSE){
								$content.='抱歉，此资源仅限VIP下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级VIP</a>';
							}elseif($memberDown==8 && $userType < 9){
								$content.='抱歉，此资源仅限年费VIP下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级VIP</a>';
							}elseif($memberDown==9 && $userType < 10){
								$content.='抱歉，此资源仅限终身VIP下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级VIP</a>';
							}else{
								
								if($userType && $memberDown > 1){
									if($memberDown==3 || $memberDown==4){
										if(get_option('erphp_popdown')){
											$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
										}else{
											$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
										}
									}elseif ($memberDown==2 && $down_info==null){
										$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
									}elseif ($memberDown==5 && $down_info==null){
										$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
									}elseif ($memberDown==6 && $down_info==null){
										if($userType == 9){
											if(get_option('erphp_popdown')){
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}elseif($userType == 10){
											if(get_option('erphp_popdown')){
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}else{
											$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
										}
									}elseif ($memberDown==7 && $down_info==null){
										if($userType == 10){
											if(get_option('erphp_popdown')){
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}else{
											$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
										}
									}elseif ($memberDown==8 && $down_info==null){
										if($userType >= 9){
											if(get_option('erphp_popdown')){
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}
									}elseif ($memberDown==9 && $down_info==null){
										if($userType >= 10){
											if(get_option('erphp_popdown')){
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
											}else{
												$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
											}	
										}
									}elseif($down_info){
										if(get_option('erphp_popdown')){
											$content.='<a href='.constant("erphpdown").'download.php?url='.$down_info->ice_url.' class="erphpdown-down erphpdown-down-layui">立即下载</a>';
										}else{
											$content.='<a href='.constant("erphpdown").'download.php?url='.$down_info->ice_url.' class="erphpdown-down" target="_blank">立即下载</a>';
										}
									}
								}else{
									if($down_info && $down_info->ice_price > 0){
										if(get_option('erphp_popdown')){
											$content.='<a href='.constant("erphpdown").'download.php?url='.$down_info->ice_url.' class="erphpdown-down erphpdown-down-layui">已购买，立即下载</a>';
										}else{
											$content.='<a href='.constant("erphpdown").'download.php?url='.$down_info->ice_url.' class="erphpdown-down" target="_blank">已购买，立即下载</a>';
										}
									}else{
										$content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
									}
								}
							}
							
						}else{
							if(get_option('erphp_popdown')){
								$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down erphpdown-down-layui'>立即下载</a>";
							}else{
								$content.="<a href=".constant("erphpdown").'download.php?postid='.get_the_ID()." class='erphpdown-down' target='_blank'>立即下载</a>";
							}
						}
						
					}else{
						if($memberDown == 4 || $memberDown == 8 || $memberDown == 9){
							$content.='抱歉，此资源仅限VIP下载，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
						}else{
							if($price){
								$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
							}else{
								$content.='此资源为免费资源，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
							}
						}
					}
					
				}
				if(get_option('ice_tips')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
				$content.='</fieldset>';
			}elseif($start_see){
				
				if(is_user_logged_in()){
					global $wpdb;
					$user_info=wp_get_current_user();
					$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
					if($days > 0){
						$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
						$nowDate = date('Y-m-d H:i:s');
						if(strtotime($nowDate) > strtotime($lastDownDate)){
							$down_info = null;
						}
					}
					if( ($userType && ($memberDown==3 || $memberDown==4)) || ($down_info && $down_info->ice_price > 0) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9) && $userType == 10) || (!$price && $memberDown!=4 && $memberDown!=8 && $memberDown!=9)){
						return $content;
					}else{
					
						$content2='<fieldset class="erphpdown erphpdown-see" id="erphpdown" style="display:block"><legend>内容查看</legend>';
						if($price){
							if($memberDown != 4 && $memberDown != 8 && $memberDown != 9)
								$content2.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
						}
						
						
						if($memberDown > 1)
						{
							$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
							if($userType){
								$vipText = '';
							}
							if($memberDown==3 && $down_info==null){
								$content2.='（VIP免费'.$vipText.'）';
							}elseif ($memberDown==2 && $down_info==null){
								$content2.='（VIP 5折'.$vipText.'）';
							}elseif ($memberDown==5 && $down_info==null){
								$content2.='（VIP 8折'.$vipText.'）';
							}elseif ($memberDown==6 && $down_info==null){
								if($userType < 9){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级年费VIP</a>';
								}
								$content2.='（年费VIP免费'.$vipText.'）';
							}elseif ($memberDown==7 && $down_info==null){
								if($userType < 10){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级终身VIP</a>';
								}
								$content2.='（终身VIP免费'.$vipText.'）';
							}elseif ($memberDown==4){
								if($userType){
									
								}
							}
						}
						
						if($memberDown==4 && $userType==FALSE)
						{
							$content2.='抱歉，此内容仅限VIP查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
						}elseif($memberDown==8 && $userType<9)
						{
							$content2.='抱歉，此内容仅限年费VIP查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
						}elseif($memberDown==9 && $userType<10)
						{
							$content2.='抱歉，此内容仅限终身VIP查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级VIP</a>';
						}
						else 
						{
							if($userType && $memberDown > 1)
							{
								if ($memberDown==2 && $down_info==null)
								{
									$content2.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
								}
								elseif ($memberDown==5 && $down_info==null)
								{
									$content2.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
								}
								elseif ($memberDown==6 && $down_info==null)
								{
									if($userType < 9){
										$content2.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
									}
								}
								elseif ($memberDown==7 && $down_info==null)
								{
									if($userType < 10){
										$content2.='<a class="erphpdown-iframe erphpdown-buy"  href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank" >立即购买</a>';
									}
								}
							}
							else 
							{
								if($down_info  && $down_info->ice_price > 0){
									
								}else {
									$content2.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'>立即购买</a>';
								}
							}
						}
					}

				}else{
					$content2='<fieldset class="erphpdown erphpdown-see" id="erphpdown" style="display:block"><legend>内容查看</legend>';
					
					if($memberDown == 4 || $memberDown == 8 || $memberDown == 9){
						$content2.='抱歉，此内容仅限VIP查看，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
					}else{
						if($price){
							$content2.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
						}else{
							$content2.='此内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
						}
					}
					
				}
				if(get_option('ice_tips')) $content2.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
				$content2.='</fieldset>';
				return $content2;
				
			}else{
				if($downMsgFree) $content.='<fieldset class="erphpdown" id="erphpdown"><legend>资源下载</legend>'.$downMsgFree.'</fieldset>';
			}
			
		}else{
			if($start_see){
				return '';
			}
		}
	}
	
	return $content;
}

add_action('the_content','erphpdown_content_show');


