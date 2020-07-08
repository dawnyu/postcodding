<?php
/*
mobantu.com
qq 82708210
*/
if ( !defined('ABSPATH') ) {exit;}

function erphpdown_metaboxs() {
	$meta_boxes = array(
		array(
			"name"             => "start_down",
			"title"            => "收费模式 *",
			"desc"             => "免登录查看请使用短代码隐藏部分内容",
			"type"             => "erphpcheckbox",
			"capability"       => "manage_options"
		),
		array(
			"name"             => "member_down",
			"title"            => "VIP优惠 *",
			"desc"             => "专享指只有VIP用户可下载或查看，普通用户无权单独购买",
			"type"             => "vipradio",
			'options' => array(
				'1' => '无',
	            '4' => '专享',
	            '8' => '年费专享',
	            '9' => '终身专享',
	            '3' => '免费',
	            '6' => '年费免费',
	            '7' => '终身免费',
	            '2' => '5折',
	            '5' => '8折'
	        ),
	        'default' => '1',
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_price",
			"title"            => "收费价格 *",
			"desc"             => "除VIP专享外，其他必须大于0，否则视为免费资源，免费资源需要先登录才能下载，免登录模式时单位为元",
			"type"             => "number",
			'default'          => '0',
			'required'         => '1',
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_url",
			"title"            => "下载地址 *",
			"desc"             => "",
			"type"             => "erphptextarea",
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_url_free",
			"title"            => "免费下载地址",
			"desc"             => "与上面的收费下载地址可同时存在，用户不用登录就能免费下载的地址，不记录下载次数，格式与上面一致",
			"type"             => "textarea",
			"capability"       => "manage_options"
		),
		array(
			"name"             => "hidden_content",
			"title"            => "隐藏内容",
			"desc"             => "收费下载模式的隐藏内容。填纯文本内容，一般填提取码或者解压密码。",
			"type"             => "text",
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_days",
			"title"            => "过期天数",
			"desc"             => "留空或0则表示一次购买永久下载，设置一个大于0的数字比如30，则表示购买30天后得重新购买",
			"type"             => "number",
			'default'          => '0',
			"required"         => "0",
			"capability"       => "manage_options"
		)
	);
	if(plugin_check_activation()){
		$meta_boxes[] = array(
			"name"             => "down_activation",
			"title"            => "激活码发放",
			"desc"             => "（说明：需要安装erphpdown激活码插件，激活码会在用户购买后自动发送到用户邮箱）",
			"type"             => "checkbox",
			"capability"       => "manage_options"
		);
		$meta_boxes[] = array(
			"name"             => "down_repeat",
			"title"            => "重复购买",
			"desc"             => "",
			"type"             => "checkbox",
			"capability"       => "manage_options"
		);
	}
	$meta_boxes[] = array(
		"name"             => "down_box_hide",
		"title"            => "隐藏购买框",
		"desc"             => "隐藏默认添加到文章内容底部的购买框，你可以通过短代码<code>[box]</code>在文章任意地方显示购买框，仅适用于下载、免登录模式",
		"type"             => "checkbox",
		"capability"       => "manage_options"
	);
	return $meta_boxes;
}

function erphpdown_show_metabox() {
	global $post;
	$meta_boxes = erphpdown_metaboxs(); 
	echo '<style>
	.erphpdown-metabox-item{padding-left:100px;position:relative;margin:1em 0}
	.erphpdown-metabox-item label.title{position:absolute;left:0;top:0;display:inline-block;font-weight:bold;width:100px;vertical-align:top}
	</style><div style="border:1px dashed #ccc;padding:5px 8px;">收费隐藏短代码 <code>[erphpdown]部分隐藏内容[/erphpdown]</code>；文章内多个不同价格的隐藏内容短代码 <code>[erphpdown index=1 price=5]部分隐藏内容1[/erphpdown]</code> <code>[erphpdown index=2 price=6]部分隐藏内容2[/erphpdown]</code>，请确保index与price的唯一性；VIP专属隐藏短代码 <code>[vip type=6]VIP内容[/vip]</code>（type选填，可为6、7、8、9、10，分别对应五种VIP）；购买按钮短代码 <code>[buy id=1]</code>（id指文章的id）；自定义位置的购买下载框短代码 <code>[box]</code>。</div>';
	foreach ( $meta_boxes as $meta ) :
		$value = get_post_meta( $post->ID, $meta['name'], true );
		if ( $meta['type'] == 'text' )
			erphpdown_show_text( $meta, $value );
		elseif ( $meta['type'] == 'number' )
			erphpdown_show_number( $meta, $value );
		elseif ( $meta['type'] == 'textarea' )
			erphpdown_show_textarea( $meta, $value );
		elseif ( $meta['type'] == 'erphptextarea' )
			erphpdown_show_erphptextarea( $meta, $value );
		elseif ( $meta['type'] == 'checkbox' )
			erphpdown_show_checkbox( $meta, $value );
		elseif ( $meta['type'] == 'erphpcheckbox' )
			erphpdown_show_erphpcheckbox( $meta, $value );
		elseif ($meta['type'] == 'vipradio')
			erphpdown_show_vipradio( $meta, $value );
	endforeach; 
}

function erphpdown_show_vipradio( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<?php
			$i=1;
            foreach ($options as $key => $option) {
            	if(!$value) $value=$default;
            	if($key != 1 && $key != 3){$class="login";}else{$class="";}
                echo '<span><input type="radio" name="'.$name.'" id="'.$name.$i.'" value="'. esc_attr( $key ) . '" '. checked( $value, $key, false) .' class="'.$class.'"/><label for="'.$name.$i.'">' . esc_html( $option ) . '</label>&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                $i ++;
            }
        ?>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_show_text( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_html( $value, 1 ); ?>" style="width: 100%;" />
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_show_number( $args = array(), $value = false ) {
	extract( $args ); if(!$value) $value=$default; ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<input type="number" min="0" step="0.01" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_html( $value, 1 ); ?>" style="width: 100px;" <?php if($required) echo 'required';?>/>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_show_textarea( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="4" tabindex="30" style="width: 100%;"><?php echo esc_html( $value, 1 ); ?></textarea>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>	
	</div>
	<?php
}

function erphpdown_show_erphptextarea( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="4" tabindex="30" style="width: 100%;"><?php echo esc_html( $value, 1 ); ?></textarea><a href="javascript:;" class="erphp-add-file button">上传媒体库文件</a> <a href="javascript:;" class="erphp-add-file2 button">上传本地文件</a> <span id="file-progress"></span>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description">
			收费查看模式不用填写，地址一行一个，可外链以及内链。地址格式可为以下任意一种：<a href="javascript:;" class="erphpshowtypes">点击显示格式</a><br>
			<div class="erphpurltypes" style="display: none;border:1px dashed #eaeaea;padding:5px 8px"><ol><li>/wp-content/uploads/moban-tu.zip</li><li>https://pan.baidu.com/test</li><li>某某地址,https://pan.baidu.com/test,提取码：2587</li><li>某某地址,https://pan.baidu.com/test</li><li>链接: https://pan.baidu.com/s/test 提取码: xxxx 复制这段内容后打开百度网盘手机App，操作更方便哦</li></ol>模板兔提示：1是内链，可加密下载地址；3与4格式用英文半角逗号隔开（名称,下载地址,提取码或解压密码），不能有空格；5是<b>网页版百度网盘</b>默认分享格式（名称 下载地址 提取码名称 提取码），英文空格分割，最后面那句广告可以去掉</div>
		</p>	
		<script src="<?php echo ERPHPDOWN_URL;?>/static/jquery.form.js"></script>
		<script>
	        jQuery(document).ready(function() {
	            var $ = jQuery;
	            if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
	                $(document).on('click', '.erphp-add-file', function(e) {
	                    e.preventDefault();
	                    var button = $(this);
	                    var id = button.prev();
	                    wp.media.editor.send.attachment = function(props, attachment) {
	                        //console.log(attachment)
	                        if($.trim(id.val()) != ''){
								id.val(id.val()+'\n'+attachment.url);
							}else{
								id.val(attachment.url);	
							}
	                    };
	                    wp.media.editor.open(button);
	                    return false;
	                });
	            }

	            $(".erphpshowtypes").click(function(){
	            	if($(this).hasClass('active')){
	            		$(".erphpurltypes").hide();
	            	}else{
	            		$(".erphpurltypes").show();
	            	}
	            	$(this).toggleClass("active");
	            });

	            $(".erphp-add-file2").click(function(){
                    $("body").append('<form style="display:none" id="erphpFileForm" action="<?php echo ERPHPDOWN_URL;?>/admin/action/file.php" enctype="multipart/form-data" method="post"><input type="file" id="erphpFile" name="erphpFile"></form>');
                    $("#erphpFile").trigger('click');
                    $("#erphpFile").change(function(){
                        $("#erphpFileForm").ajaxSubmit({
                            //dataType:  'json',
                            beforeSend: function() {
                                
                            },
                            uploadProgress: function(event, position, total, percentComplete) {
                                $('#file-progress').text(percentComplete+'%');
                            },
                            success: function(data) {
                                $('#erphpFileForm').remove();
                                var olddata = $('#<?php echo $name;?>').val();
                                if($.trim(olddata)){
                                	$('#<?php echo $name;?>').val(olddata+'\n'+data);   
                                }else{
                                    $('#<?php echo $name;?>').val(data);   
                                }
                            },
                            error:function(xhr){
                                $('#erphpFileForm').remove();
                                alert('上传失败！'); 
                            }
                        });

                    });
                });
	            
	        });
	    </script>	
	</div>
	<?php
}

function erphpdown_show_checkbox( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="1"
		<?php if ( htmlentities( $value, 1 ) == '1' ) echo ' checked="checked"'; ?>
		style="width: auto;" />
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<p class="description"><?php echo $desc; ?></p>
	</div>
<?php }

function erphpdown_show_erphpcheckbox( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<?php 
		global $post;
		$value1 = get_post_meta( $post->ID, 'start_down', true );
		$value2 = get_post_meta( $post->ID, 'start_see', true );
		$value3 = get_post_meta( $post->ID, 'start_see2', true );
		$value5 = get_post_meta( $post->ID, 'start_down2', true );
		?>
		<input type="radio" name="start_down" checked value="4" />不启用&nbsp;
		<input type="radio" name="start_down" <?php if($value1 == 'yes') echo 'checked'?> value="1" />下载 &nbsp;
		<input type="radio" name="start_down" <?php if($value5 == 'yes') echo 'checked'?> value="5" class="nologin"/>免登录 &nbsp;
		<input type="radio" name="start_down" <?php if($value2 == 'yes') echo 'checked'?> value="2" />查看 &nbsp;
		<input type="radio" name="start_down" <?php if($value3 == 'yes') echo 'checked'?> value="3" />部分查看&nbsp;

		<input type="hidden" name="erphpdown" value="1">
		<input type="hidden" name="start_down_input_name" id="start_down_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<input type="hidden" name="start_down2_input_name" id="start_down2_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<input type="hidden" name="start_see_input_name" id="start_see_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<input type="hidden" name="start_see2_input_name" id="start_see2_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<p class="description"><?php echo $desc; ?></p>
		<script>
			jQuery(function(){
				if(jQuery("input[name='start_down'].nologin").is(":checked")){
					jQuery("input[name='member_down'].login").parent().hide();
				}
			});
			jQuery("input[name='start_down']").click(function(){
				if(jQuery(this).hasClass("nologin")){
					jQuery("input[name='member_down'].login").parent().hide();
				}else{
					jQuery("input[name='member_down'].login").parent().show();
				}
			});
		</script>
	</div>
<?php }


add_action( 'admin_menu', 'erphpdown_create_metabox' );
add_action( 'save_post', 'erphpdown_save_metabox' );

function erphpdown_create_metabox() {
	$erphp_post_types = get_option('erphp_post_types');
	$args = array(
		'public'   => true,
	);
	$post_types = get_post_types($args);
	foreach ( $post_types  as $post_type ) {
		if($erphp_post_types){
			if(in_array($post_type,$erphp_post_types)) add_meta_box( 'erphpdown-postmeta-box','Erphpdown属性', 'erphpdown_show_metabox', $post_type, 'normal', 'high' );
		}
	}
	
}

function erphpdown_save_metabox( $post_id ) {

	if(!$_POST['erphpdown'])
		return;

	$meta_boxes = array_merge( erphpdown_metaboxs() );
	foreach ( $meta_boxes as $meta_box ) :
		if($meta_box['type'] == 'erphpcheckbox'){

			if ( !wp_verify_nonce( $_POST['start_down_input_name'], plugin_basename( __FILE__ ) ) || !wp_verify_nonce( $_POST['start_see_input_name'], plugin_basename( __FILE__ ) ) || !wp_verify_nonce( $_POST['start_see2_input_name'], plugin_basename( __FILE__ ) ) || !wp_verify_nonce( $_POST['start_down2_input_name'], plugin_basename( __FILE__ ) ) )
				return $post_id;
			if ( 'page' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) )
				return $post_id;
			elseif ( 'post' == $_POST['post_type'] && !current_user_can( 'edit_post', $post_id ) )
				return $post_id;

			if(isset($_POST['start_down'])){
				$data = stripslashes( $_POST['start_down'] );
				$data1 = '';$data2='';$data3='';$data5='';
				if($data == '1') $data1 = 'yes';
				if($data == '2') $data2 = 'yes';
				if($data == '3') $data3 = 'yes';
				if($data == '5') $data5 = 'yes';
				update_post_meta( $post_id, 'start_down', $data1 );
				update_post_meta( $post_id, 'start_see', $data2 );
				update_post_meta( $post_id, 'start_see2', $data3 );
				update_post_meta( $post_id, 'start_down2', $data5 );
			}
		}else{
			if (!wp_verify_nonce( $_POST[$meta_box['name'] . '_input_name'], plugin_basename( __FILE__ ) ))
				return $post_id;
			if ( 'page' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) )
				return $post_id;
			elseif ( 'post' == $_POST['post_type'] && !current_user_can( 'edit_post', $post_id ) )
				return $post_id;

			
			$data = stripslashes( $_POST[$meta_box['name']] );
			if ( get_post_meta( $post_id, $meta_box['name'] ) == '' )
				add_post_meta( $post_id, $meta_box['name'], $data, true );
			elseif ( $data != get_post_meta( $post_id, $meta_box['name'], true ) )
				update_post_meta( $post_id, $meta_box['name'], $data );
			elseif ( $data == '' )
				delete_post_meta( $post_id, $meta_box['name'], get_post_meta( $post_id, $meta_box['name'], true ) );
			
		}


	endforeach;
}