<?php
if (!defined('WPINC')) {
	die;
}

//更新方法都寫這，方法必須要回傳 true 才算更新完成。
class Mxp_Update_PCHOME2WP {
	public static $version_list = array('1.0.0', '1.0.1', '1.0.2', '1.0.3', '1.0.4', '1.0.5', '1.0.6', '1.0.7');

	public static function apply_update($ver) {
		$index = array_search($ver, self::$version_list);
		if ($index === false) {
			echo "<script>console.log('update version: {$ver}, in index: {$index}');</script>";
			return false;
		}
		for ($i = $index + 1; $i < count(self::$version_list); ++$i) {
			$new_v = str_replace(".", "_", self::$version_list[$i]);
			if (defined('WP_DEBUG') && WP_DEBUG === true) {
				echo "<script>console.log('mxp_update_to_v{$new_v}');</script>";
			}
			if (call_user_func(array(__CLASS__, "mxp_update_to_v{$new_v}")) === false) {
				echo "<script>console.log('current version: {$ver}, new version: {$new_v}');</script>";
				return false;
			}
		}
		return true;
	}
	/**
	 *	更新區塊
	 */
	public function mxp_update_to_v1_0_0() {

		return true;
	}
	public function mxp_update_to_v1_0_1() {

		return true;
	}
	public function mxp_update_to_v1_0_2() {

		return true;
	}
	public function mxp_update_to_v1_0_3() {
		// 上線後更新版本測試
		// 修正外掛名稱以及描述
		// 測試語言標籤
		return true;
	}
	public function mxp_update_to_v1_0_4() {
		// 修正品牌名稱
		return true;
	}
	public function mxp_update_to_v1_0_5() {
		// 移除 `<div>` 與 `</div>` 標籤，避免結構不正確
		return true;
	}
	public function mxp_update_to_v1_0_6() {
		// 修正付費使用者如果標籤空白就不匯入的問題
		return true;
	}
	public function mxp_update_to_v1_0_7() {
		// 更新支援版本
		return true;
	}
}