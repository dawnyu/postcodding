<?php
/*
www.mobantu.com
82708210@qq.com
*/
if ( !defined('ABSPATH') ) {exit;}
$user_info=wp_get_current_user();
$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_user_id=".$user_info->ID);
//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);

$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_user_id=".$user_info->ID." order by ice_time DESC limit $offset,$ice_perpage");


?>
<div class="wrap">
	<h2>VIP升级记录</h2>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th>VIP类型</th>
				<th>价格</th>
				<th>交易时间</th>				
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{
					if($value->ice_user_type == 6) $typeName = '体验';
					else{$typeName=$value->ice_user_type==7 ?'包月' :($value->ice_user_type==8 ?'包季' : ($value->ice_user_type==10 ?'终身' : '包年'));}
					echo "<tr>\n";
					echo "<td>$typeName</td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="3" align="center"><strong>没有交易记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
</div>
