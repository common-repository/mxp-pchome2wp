<?php
if (!defined('WPINC')) {
	die;
}
echo "<p><a href=" . esc_url(admin_url('admin.php?page=mxp-pchome2wp-options')) . ">回到上一頁</a></p>";
if (isset($_GET['cate_num'])) {
	$data = Mxp_PCHOME2WP::get_cate_list();
	echo "<ul>";
	foreach ($data as $row) {
		$cate_name = esc_html($row['cate_name']);
		$count = esc_html($row['count']);
		$post_cate = sanitize_text_field(intval($row['post_cate']));
		$url = esc_url(admin_url('admin.php?page=mxp-pchome2wp-options&cate_num=' . $post_cate));
		if ($post_cate == $_GET['cate_num']) {
			echo "<li>分類名：<font color=blue>{$cate_name}</font>（{$count} 筆資料）</li>";
		}
	}
	echo "</ul>";
	echo "<p>請先選擇匯入文章分類：";
	wp_dropdown_categories(array('name' => 'mxp_pchome2wp_post_category', 'hide_empty' => 0, 'selected' => get_option("mxp_pchome2wp_post_category", "1")));
	echo "（若無欲新增之分類，請先至文章分類新增。）<input type='submit' id='submit' class='batch_submit button button-primary' value='批次匯入'></p>";
	$cate_num = sanitize_text_field(intval($_GET['cate_num']));
	$list = Mxp_PCHOME2WP::get_post_list($cate_num);
	usort($list, function ($a, $b) {
		if ($a['created_time'] == $b['created_time']) {
			return 0;
		}
		return ($a['created_time'] < $b['created_time']) ? -1 : 1;
	});
	echo "<table border='1'><thead><tr><th>操作</th><th>不匯入</th><th>發文時間</th><th>分類文章清單</th></tr></thead><tbody>";
	foreach ($list as $item) {
		$post_html = "<a href='{$item['post_url']}'>{$item['post_name']}</a>";
		$datetime = date('Y-m-d H:i:s', $item['created_time']);
		$import_flag = $item['is_import'];
		$action_btn = "<button id='b_{$item['sid']}' class='mxp-import' data-id='{$item['sid']}'>匯入</button>";
		if ($import_flag == 0) {
			$import_html = "<div id='d_{$item['sid']}'><input type='checkbox' id='c_{$item['sid']}' data-id='{$item['sid']}'/></div>";
		} else {
			$import_html = "已匯入";
			$action_btn = "<button id='b_{$item['sid']}' class='mxp-import' data-id='{$item['sid']}' disabled>匯入</button>";
		}

		echo "<tr><td>{$action_btn}</td><td style='text-align: center;'>{$import_html}</td><td>{$datetime}</td><td>{$post_html}</td></tr>";
	}
	echo "</tbody></table>";
	echo "<br/><input type='submit' id='submit' class='batch_submit button button-primary' value='批次匯入'>";
} else {
	echo "錯誤的請求。";
}