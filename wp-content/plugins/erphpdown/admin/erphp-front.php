<?php
if ( !defined('ABSPATH') ) {exit;}
if(isset($_POST['Submit'])) {
	update_option('erphp_url_front_vip', esc_sql(trim($_POST['erphp_url_front_vip'])));
	update_option('erphp_url_front_recharge', esc_sql(trim($_POST['erphp_url_front_recharge'])));
	update_option('erphp_url_front_login', esc_sql(trim($_POST['erphp_url_front_login'])));
	update_option('erphp_url_front_noadmin', esc_sql(trim($_POST['erphp_url_front_noadmin'])));
	update_option('erphp_url_front_userpage', esc_sql(trim($_POST['erphp_url_front_userpage'])));
	update_option('erphp_url_front_success', esc_sql(trim($_POST['erphp_url_front_success'])));
	update_option('erphp_post_types', $_POST['erphp_post_types']);
	update_option('erphp_down_default', $_POST['erphp_down_default']);
	update_option('member_down_default', $_POST['member_down_default']);
	update_option('down_price_type_default', $_POST['down_price_type_default']);
	update_option('down_price_default', $_POST['down_price_default']);
	update_option('down_days_default', $_POST['down_days_default']);
	echo'<div class="updated settings-error"><p>更新成功！</p></div>';
}

$erphp_url_front_vip = get_option('erphp_url_front_vip');
$erphp_url_front_recharge = get_option('erphp_url_front_recharge');
$erphp_url_front_login = get_option('erphp_url_front_login');
$erphp_url_front_noadmin = get_option('erphp_url_front_noadmin');
$erphp_url_front_userpage = get_option('erphp_url_front_userpage');
$erphp_url_front_success = get_option('erphp_url_front_success');
$erphp_post_types = get_option('erphp_post_types');
$erphp_down_default = get_option('erphp_down_default');
$member_down_default = get_option('member_down_default');
$down_price_type_default = get_option('down_price_type_default');
$down_price_default = get_option('down_price_default');
$down_days_default = get_option('down_days_default');
?>
<style>.form-table th{font-weight: 400}</style>
<div class="wrap">
	<h1>显示设置</h1>
	<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
		<h3>文章类型设置</h3>
		<p>选择你所需要支持erphpdown的文章类型。</p>
		<table class="form-table">
			<tr>
				<th valign="top">文章类型</th>
				<td>
					<?php 
					$args = array('public' => true,);
					$post_types = get_post_types($args);
					foreach ( $post_types  as $post_type ) {
						if($post_type != 'attachment'){
							$postType = get_post_type_object($post_type);
							?>
							<label>
								<input type="checkbox" name="erphp_post_types[]" value="<?php echo $post_type;?>" <?php if($erphp_post_types) {if(in_array($post_type,$erphp_post_types)) echo 'checked';}?>> <?php echo $postType->labels->singular_name;?>&nbsp;&nbsp;&nbsp;&nbsp;
							</label>
							<?php
						}
					}
					?>
				</td>
			</tr>
		</table>
		<br><br>
		<h3>发布默认设置</h3>
		<p>仅对后台新发布有效（只是默认选中，需提交发布才会应用上），其他地方发布、编辑、采集无效。</p>
		<table class="form-table">
			<tr>
				<th valign="top">收费模式</th>
				<td>
					<select name="erphp_down_default">
 						<option value ="4" <?php if($erphp_down_default == '4') echo 'selected="selected"';?>>不启用</option>
 						<option value ="1" <?php if($erphp_down_default == '1') echo 'selected="selected"';?>>下载</option>
 						<option value ="5" <?php if($erphp_down_default == '5') echo 'selected="selected"';?>>免登录</option>
 						<option value ="2" <?php if($erphp_down_default == '2') echo 'selected="selected"';?>>查看</option>
 						<option value ="3" <?php if($erphp_down_default == '3') echo 'selected="selected"';?>>部分查看</option>
 					</select>
				</td>
			</tr>
			<tr>
				<th valign="top">VIP优惠</th>
				<td>
					<select name="member_down_default">
 						<option value ="1" <?php if($member_down_default == '1') echo 'selected="selected"';?>>无</option>
 						<option value ="4" <?php if($member_down_default == '4') echo 'selected="selected"';?>>专享</option>
 						<option value ="8" <?php if($member_down_default == '8') echo 'selected="selected"';?>>年费专享</option>
 						<option value ="9" <?php if($member_down_default == '9') echo 'selected="selected"';?>>终身专享</option>
 						<option value ="3" <?php if($member_down_default == '3') echo 'selected="selected"';?>>免费</option>
 						<option value ="6" <?php if($member_down_default == '6') echo 'selected="selected"';?>>年费免费</option>
 						<option value ="7" <?php if($member_down_default == '7') echo 'selected="selected"';?>>终身免费</option>
 						<option value ="2" <?php if($member_down_default == '2') echo 'selected="selected"';?>>5折</option>
 						<option value ="5" <?php if($member_down_default == '5') echo 'selected="selected"';?>>8折</option>
 					</select>
				</td>
			</tr>
			<tr>
				<th valign="top">价格类型</th>
				<td>
					<select name="down_price_type_default">
 						<option value ="0" <?php if($down_price_type_default == '0') echo 'selected="selected"';?>>单价格</option>
 						<option value ="1" <?php if($down_price_type_default == '1') echo 'selected="selected"';?>>多价格</option>
 					</select>
				</td>
			</tr>
			<tr>
				<th valign="top">收费价格</th>
				<td>
					<input type="number" step="0.01" id="down_price_default" name="down_price_default" value="<?php echo $down_price_default;?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th valign="top">过期天数</th>
				<td>
					<input type="number" step="0.01" id="down_days_default" name="down_days_default" value="<?php echo $down_days_default;?>" class="regular-text" />
				</td>
			</tr>
		</table>
		<br><br>
		<h3>前端设置</h3>
		<p>假如你主题集成了前端用户中心，并且包含了erphpdown插件功能，可以把相应链接填在此处！没有设置可不填！</p>
		<table class="form-table">
			<tr>
				<th valign="top">禁止进后台</th>
				<td>
					<input type="checkbox" id="erphp_url_front_noadmin" name="erphp_url_front_noadmin" value="yes" <?php if($erphp_url_front_noadmin == 'yes') echo 'checked'; ?> />（普通用户无法进后台，若开启此项，下面的升级VIP与充值地址均得设置为非后台的地址）
				</td>
			</tr>
			<tr>
				<th valign="top">前端用户中心地址</th>
				<td>
					<input type="text" id="erphp_url_front_userpage" name="erphp_url_front_userpage" value="<?php echo $erphp_url_front_userpage;?>" class="regular-text" placeholder="http://"/>（例如：http://www.65kr.com/user）
				</td>
			</tr>
			<tr>
				<th valign="top">前端升级VIP地址</th>
				<td>
					<input type="text" id="erphp_url_front_vip" name="erphp_url_front_vip" value="<?php echo $erphp_url_front_vip ; ?>" class="regular-text" placeholder="http://" />（例如：http://www.65kr.com/user?pd=vip）
				</td>
			</tr>
			<tr>
				<th valign="top">前端充值地址</th>
				<td>
					<input type="text" id="erphp_url_front_recharge" name="erphp_url_front_recharge" value="<?php echo $erphp_url_front_recharge ; ?>" class="regular-text" placeholder="http://"/>（例如：http://www.65kr.com/user?pd=money）
				</td>
			</tr>
			<tr>
				<th valign="top">支付成功跳转地址 *</th>
				<td>
					<input type="text" id="erphp_url_front_success" name="erphp_url_front_success" value="<?php echo $erphp_url_front_success;?>" class="regular-text" placeholder="http://" />（一般是充值记录页面，例如：http://www.65kr.com/user?pd=history）
				</td>
			</tr>
			<tr>
				<th valign="top">前端登录地址</th>
				<td>
					<input type="text" id="erphp_url_front_login" name="erphp_url_front_login" value="<?php echo $erphp_url_front_login ; ?>" class="regular-text" placeholder="http://"/>（不填则显示默认wp-login.php登录地址；链接的class为erphp-login-must）
				</td>
			</tr>
			
			
		</table><table> <tr>
			<td><p class="submit">
				<input type="submit" name="Submit" value="保存设置" class="button-primary"/>
			</p>
		</td>

	</tr> </table>
	

</form>
</div>