<?php
if (!defined('WPINC')) {
	die;
}
if (get_option("mxp_pchome2wp_account", "") == "") {
	echo "請先至設定頁設定PChome新聞台資訊！";
	exit;
}

if (isset($_GET['cate_num'])) {
	include plugin_dir_path(__FILE__) . "posts-list.php";
} else {
	if (isset($_POST['pchome2wp-options-post']) && wp_verify_nonce($_POST['pchome2wp-options-post'], 'mxp-pchome2wp-options-post') && isset($_POST['cate_num']) && isset($_POST['cate_name'])) {
		$result = Mxp_PCHOME2WP::set_cate_list(
			sanitize_text_field($_POST['cate_name']),
			sanitize_text_field(intval($_POST['cate_num']))
		);
		if ($result !== true) {
			echo "<p><font color=red>{$result}</font></p>";
		}
	}
//先輸出分類採集數據
	$data = Mxp_PCHOME2WP::get_cate_list();
	echo "<ul>";
	foreach ($data as $row) {
		$cate_name = esc_html($row['cate_name']);
		$count = esc_html($row['count']);
		$post_cate = sanitize_text_field(intval($row['post_cate']));
		$url = esc_url(admin_url('admin.php?page=mxp-pchome2wp-options&cate_num=' . $post_cate));
		echo "<li>分類名：<a href='{$url}'>{$cate_name}</a>（{$count} 筆資料）</li>";
	}
	echo "</ul>";
	?>
	<form method="post" id="mainform" action="" enctype="multipart/form-data">
		<h2>匯入分類資訊</h2>
		新聞台分類名稱：<input type="text" name="cate_name" size="12"/>（識別當前作業用，可隨意）</br>
		新聞台分類編號：<input type="text" name="cate_num" size="3"/>（新聞台分類網址：http://mypaper.pchome.com.tw/<?php echo get_option("mxp_pchome2wp_account"); ?>/category/XX, XX即是分類編號）</br>
		<?php wp_referer_field(admin_url('admin.php?page=mxp-pchome2wp-option'))?>
		<?php wp_nonce_field('mxp-pchome2wp-options-post', 'pchome2wp-options-post')?>
		<input type="submit" name="submit" class="button button-primary" id="get_cate_btn" onclick="this.value='處理中，請稍候！'"/>
	</form>
	<script>
	jQuery(document).ready(function(){
		jQuery('#mainform').submit(function(){
	    	jQuery(this).find(':input[type=submit]').prop('disabled', true);
		});
	});
	</script>
	<?php
if (isset($_POST['pchome2wp-options-post']) && wp_verify_nonce($_POST['pchome2wp-options-post'], 'mxp-pchome2wp-options-post') && isset($_POST['cate_num']) && isset($_POST['cate_name'])) {
		if ($result === true) {
			echo "完成新聞台分類匯入！";
		}
	}
}