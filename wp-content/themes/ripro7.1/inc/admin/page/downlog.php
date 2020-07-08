<?php
date_default_timezone_set('Asia/Shanghai');
global $wpdb;
?>

<?php
// 主页面PHP
$params['perpage'] = 20; // 每页数量
$params['paged']=isset($_GET['paged']) ?intval($_GET['paged']) :1;  //当前页
$params['user_id'] = (!empty( $_GET['user_id'] )) ? $_GET['user_id'] : '0' ; //偏移页
$Reflog = new Reflog(0);
$result = $Reflog->get_down_log($params);
$total   = count($result);
?>

<!-- 主页面 -->
<div class="wrap">
	<h1 class="wp-heading-inline">用户下载记录查询</h1>
    <hr class="wp-header-end">
	
	<form id="order-filter" method="get">
		<!-- 初始化页面input -->
		<input type="hidden" name="page" value="<?php echo $_GET['page']?>">
		<!-- 筛选 -->
		<div class="wp-filter">
		    <div class="filter-items">
		    	<div class="view-switch">
		    		<a class="view-list current"></a>
		    	</div>
		    </div>
		    <div class="search-form">
		        <span class="">根据用户的ID搜索例如（admin） ，共<?php echo $total?>个项目 </span>
		        <input class="search" id="media-search-input" name="user_id" placeholder="根据用户搜索,回车确定…" type="search" value=""/>
		    </div>
		    <br class="clear">
		</div>
		<!-- 筛选END -->

		<table class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<th class="column-primary">用户ID</th>	
					<th>用户级别</th>
					<th>所下资源</th>
					<th>下载IP</th>
					<th>下载时间</th>
					<th>今日可下载</th>
					<th>今日已下载</th>
					<th>今日剩余下载</th>
				</tr>
			</thead>
			<tbody id="the-list">

		<?php

			if($result) {
				
				foreach($result as $item){
					$CaoUser = new CaoUser($item->user_id);
					$cao_vip_downum = $CaoUser->cao_vip_downum($item->user_id,$CaoUser->vip_status());
					echo '<tr id="order-info">';
					$user_loginName = ($item->user_id > 0) ? get_user_by('id',$item->user_id)->user_login : '游客' ;
					echo '<td class="has-row-actions column-primary">'.$user_loginName.'<button type="button" class="toggle-row"><span class="screen-reader-text">显示详情</span></button></td>';
					echo '<td data-colname="用户级别">'.$CaoUser->vip_name().'</td>';
					echo '<td data-colname="所下资源"><a target="_blank" href='.get_permalink($item->down_id).'>'.get_the_title($item->down_id).'</a></td>';
                    echo '<td data-colname="下载IP地址">'.$item->ip.'</td>';
                    echo '<td data-colname="下载时间">'.date('Y-m-d H:i:s',$item->create_time).'</td>';
                    echo '<td data-colname="今日可下载"><span class="badge badge-blue">'.$cao_vip_downum['today_count_num'].'次</span></td>';
                    echo '<td data-colname="今日已下载"><span class="badge">'.$cao_vip_downum['today_down_num'].'次</span></td>';
                    echo '<td data-colname="今日剩余下载"><span class="badge badge-primary">'.$cao_vip_downum['over_down_num'].'次</span></td>';

					echo "</tr>";
				}
			}
			else{
				echo '<tr><td colspan="12" align="center"><strong>没有数据</strong></td></tr>';
			}
		?>
		</tbody>
		</table>
	</form>
    <?php echo cao_admin_pagenavi($total,$perpage);?>
    <script>
            jQuery(document).ready(function($){

            });
	</script>
</div>
