<?php
/**
 * Plugin Name: PCHome 新聞台搬家匯入工具 - Mxp.TW
 * Plugin URI: https://tw.wordpress.org/plugins/mxp-pchome2wp/
 * Description: 使用此外掛把你的 PChome 新聞台搬家來 WordPress ，做你真正的自媒體吧！
 * Version: 1.0.7
 * Author: Chun
 * Author URI: https://www.mxp.tw/contact/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
//20171108
if (!defined('WPINC')) {
	die;
}
class Mxp_PCHOME2WP {
	static $version = '1.0.7';
	protected static $instance = null;
	public $slug = 'mxp-pchome2wp';
	protected static $blog2wp_api = 'https://api.undo.im/wp-json/mxp_pchome2wp/v1/app';
	/*
		Core Functions
	*/
	private function __construct() {
		//check if install or not
		$ver = get_option("mxp_pchome2wp_db_version");
		if (!isset($ver) || $ver == "") {
			$this->install();
		} else if (version_compare(self::$version, $ver, '>')) {
			$this->update($ver);
		}
		$this->init();
	}

	public static function get_instance() {
		global $wp_version;
		if (!isset(self::$instance) && is_super_admin()) {
			self::$instance = new self;
		}
		self::register_public_action();
		return self::$instance;
	}

	private function init() {
		add_action('admin_enqueue_scripts', array($this, 'load_assets'));
		add_action('admin_menu', array($this, 'create_plugin_menu'));
		add_action('wp_ajax_mxp_options_import_action', array($this, 'mxp_options_import_action'));
	}

	public function load_assets() {
		wp_register_script($this->slug . '-options-page', plugin_dir_url(__FILE__) . 'views/js/options.js', array('jquery'), false, false);
	}

	public static function register_public_action() {
		// 尚未有什麼特別公開事件註冊～
		// add_filter('http_request_args', function ($params, $url) {
		// 	add_filter('https_ssl_verify', '__return_false');
		// 	add_filter('https_local_ssl_verify', '__return_false');
		// 	return $params;
		// }, 10, 2);
	}

	private function install() {
		global $wpdb;
		$collate = '';

		if ($wpdb->has_cap('collation')) {
			$collate = $wpdb->get_charset_collate();
		}
		$table_name = $wpdb->prefix . 'mxp_pchome_post_log';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$tables = "
			CREATE TABLE $table_name (
			  sid bigint(20) NOT NULL AUTO_INCREMENT,
			  pid bigint(20) NULL,
			  created_time bigint(32) NOT NULL,
			  post_name varchar(255) NULL,
			  post_cate varchar(255) NULL,
			  cate_name varchar(255) NULL,
			  is_import int(1) NOT NULL,
			  post_url varchar(199) NULL,
			  PRIMARY KEY  (sid),
			  UNIQUE KEY pid (pid)
			) $collate;";
			if (!function_exists('dbDelta')) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}
			dbDelta($tables);
			add_option("mxp_pchome2wp_db_version", self::$version);
		}
	}

	private function update($ver) {
		include plugin_dir_path(__FILE__) . "update.php";
		$res = Mxp_Update_PCHOME2WP::apply_update($ver);
		if ($res == true) {
			update_option("mxp_pchome2wp_db_version", self::$version);
		} else {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins(plugin_basename(__FILE__));
			//更新失敗的TODO:聯絡我～回報錯誤
			wp_die('更新失敗惹...Q_Q||| 請來信至: im@mxp.tw 告訴我您是從哪個版本升級發生意外的？可以使用 Chrome Dev tools 的 console 分頁查看是否有錯誤提示！', 'Q_Q|||');
		}

	}

	/**
	 *	public methods
	 **/
	public function create_plugin_menu() {
		add_menu_page('PChome 文章匯入工具設定', 'PChome 文章匯入工具設定', 'administrator', $this->slug, array($this, 'main_page_cb'), 'dashicons-admin-generic');
		//add_submenu_page($this->slug, '即時匯入', '即時匯入', 'administrator', $this->slug . '-realtime', array($this, 'realtime_page_cb'));
		add_submenu_page($this->slug, '文章匯入工具', '文章匯入工具', 'administrator', $this->slug . '-options', array($this, 'options_page_cb'));
	}

	public function page_wraper($title, $cb) {
		echo '<div class="wrap" id="mxp"><h1>' . $title . '</h1>';
		call_user_func($cb);
		echo '</div>';
	}

	public function main_page_cb() {
		$this->page_wraper('PChome 文章匯入工具設定', function () {
			include plugin_dir_path(__FILE__) . "views/main.php";
		});
		wp_localize_script($this->slug . '-main-page', 'MXP_PCHOME2WP', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('mxp-ajax-nonce'),
		));
		wp_enqueue_script($this->slug . '-main-page');
	}

	public function realtime_page_cb() {
		$this->page_wraper('即時匯入', function () {
			include plugin_dir_path(__FILE__) . "views/realtime.php";
		});
		wp_localize_script($this->slug . '-realtime-page', 'MXP_PCHOME2WP', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('mxp-ajax-nonce'),
		));
		wp_enqueue_script($this->slug . '-realtime-page');
	}

	public function options_page_cb() {
		$this->page_wraper('文章匯入工具', function () {
			include plugin_dir_path(__FILE__) . "views/options.php";
		});
		wp_localize_script($this->slug . '-options-page', 'MXP_PCHOME2WP', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('mxp-ajax-nonce'),
		));
		wp_enqueue_script($this->slug . '-options-page');
	}

	public static function get_category_last_page($cate_id = "") {
		if ($cate_id == "" || get_option("mxp_pchome2wp_user_id", "") == "") {
			return array('message' => "資料不完整，請檢查是否正確輸入");
		}
		$response = wp_remote_request(self::$blog2wp_api,
			array(
				'method' => 'GET',
				'timeout' => 300,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(),
				'body' => array('method' => 'get_category_last_page', 'auth_domain' => $_SERVER['HTTP_HOST'], 'user_id' => get_option("mxp_pchome2wp_user_id"), 'cate_id' => $cate_id),
				'cookies' => array(),
			)
		);
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			self::logger('request_get_category_last_page_error', print_r("Something went wrong: $error_message", true));
			return array('message' => "請求發生錯誤: $error_message");
		} else {
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body, true);
			if (isset($data['data']) && $data['code'] == "ok") {
				return intval($data['data']['last_page']);
			} else {
				self::logger('get_category_last_page_data_error', print_r($response, true));
				return $data;
			}
		}
	}

	public static function start_from_category($cate_id = "", $start_page_num = 0, $end_page_num = "") {
		if ($cate_id == "" || get_option("mxp_pchome2wp_user_id", "") == "") {
			return array('message' => "資料不完整，請檢查是否正確輸入");
		}
		$response = wp_remote_request(self::$blog2wp_api,
			array(
				'method' => 'GET',
				'timeout' => 300,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(),
				'body' => array('method' => 'start_from_category', 'auth_domain' => $_SERVER['HTTP_HOST'], 'user_id' => get_option("mxp_pchome2wp_user_id"), 'cate_id' => $cate_id, 'page_num' => $start_page_num),
				'cookies' => array(),
			)
		);
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			self::logger('request_start_from_category_error', print_r("Something went wrong: $error_message", true));
			return array('message' => "請求發生錯誤: $error_message");
		} else {
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body, true);
			if (isset($data['data']) && $data['code'] == "ok") {
				return $data['data']['posts_list'];
			} else {
				self::logger('start_from_category_data_error', print_r($response, true));
				return $data;
			}
		}
	}

	public static function parsing_image($post_id, $content) {
		$content = preg_replace_callback('/<img\s+.*?src=[\"\'](http[s]{0,1}\:\/\/?[^\"\' >]*)[\"\']?[^>]*>/i',
			function ($res) {
				$upload_dir = wp_upload_dir();
				$tmp_name = join('_', explode('/', $res[1]));
				$tmp_name = join('_', explode('.', $tmp_name));
				$tmp_name = join('_', explode('?', $tmp_name));
				$tmp_name = join('_', explode('&', $tmp_name));
				$tmp_name = explode(':', $tmp_name)[1];
				$file_path = "{$upload_dir['basedir']}/{$tmp_name}";
				copy($res[1], $file_path);
				$info = getimagesize($file_path);
				$type = explode('/', $info['mime']);
				$type = end($type);
				switch ($type) {
				case 'gif':
					rename($file_path, $file_path . ".gif");
					$tmp_name .= ".gif";
					break;
				case 'jpeg':
					rename($file_path, $file_path . ".jpg");
					$tmp_name .= ".jpg";
					break;
				case 'png':
					rename($file_path, $file_path . ".png");
					$tmp_name .= ".png";
					break;
				default:
					rename($file_path, $file_path . ".jpg");
					$tmp_name .= ".jpg";
					break;
				}
				return '<img class="post_image image pchome_img import_img" src="' . "{$upload_dir['baseurl']}/{$tmp_name}" . '">';
			},
			$content);
		$content = preg_replace_callback("/href=[\"\'](http[s]{0,1}\:\/\/photo\.pchome\.com\.tw?[^\"\'>]+)[\"\']?/i",
			function ($res) {
				$pid = substr(md5(uniqid(rand(1, 6))), 0, 8);
				return "href='#{$pid}' id='{$pid}'";
			}, $content);
		// 把站內連結的流量也補一下～
		$content = preg_replace('/(http|https)\:\/\/mypaper.pchome.com.tw\/' . get_option("mxp_pchome2wp_account") . '\/post\//s', site_url() . "/post-", $content);
		preg_match_all('/<img\s+.*?src=[\"\'](http[s]{0,1}\:\/\/?[^\"\' >]*)[\"\']?[^>]*>/i', $content, $matches);
		$images = $matches[1];
		//self::logger('parsing_image', print_r($matches, true));
		self::mxp_import_image($post_id, $images);
		$remove_divs = array("<div>", "</div>");
		$content = str_replace($remove_divs, "", $content);
		return $content;
	}

	public static function single_post_parsing($url) {
		if (get_option("mxp_pchome2wp_user_id", "") == "") {
			return array('message' => "資料不完整，請檢查是否正確輸入");
		}
		$response = wp_remote_request(self::$blog2wp_api . "/post",
			array(
				'method' => 'GET',
				'timeout' => 300,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(),
				'body' => array('user_id' => get_option("mxp_pchome2wp_user_id"), 'auth_domain' => $_SERVER['HTTP_HOST'], 'link' => base64_encode($url)),
				'cookies' => array(),
			)
		);
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			self::logger('request_single_post_parsing_error', print_r("Something went wrong: $error_message", true));
			return array('message' => "請求發生錯誤: $error_message");
		} else {
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body, true);
			if (isset($data['data']) && $data['code'] == "ok") {
				return $data['data'];
			} else {
				self::logger('single_post_parsing_data_error', print_r($response, true));
				return $data;
			}
		}
	}

	public static function register($account, $name = "") {
		$response = wp_remote_request(self::$blog2wp_api . "/register",
			array(
				'method' => 'GET',
				'timeout' => 10,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(),
				'body' => array('blog_account' => $account, 'auth_domain' => $_SERVER['HTTP_HOST'], 'blog_type' => 0, 'blog_name' => $name),
				'cookies' => array(),
			)
		);
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			self::logger('request_register_error', print_r("Something went wrong: $error_message", true));
			return array('message' => "請求發生錯誤: $error_message");
		} else {
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body, true);
			if (isset($data['data']) && $data['code'] == "ok") {
				return $data['data'];
			} else {
				self::logger('register_data_error', print_r($response, true));
				return $data;
			}
		}
	}

	public static function get_post_list($cate_id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'mxp_pchome_post_log';
		$cate_id = sanitize_text_field(intval($cate_id));
		$res = $wpdb->get_results(
			$wpdb->prepare("SELECT sid,created_time,post_name,is_import,post_url FROM {$table_name} WHERE post_cate=%d", $cate_id)
			, ARRAY_A);
		return $res;
	}

	public static function get_cate_list() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'mxp_pchome_post_log';
		$res = $wpdb->get_results(
			"SELECT post_cate,cate_name FROM {$table_name} GROUP BY post_cate"
			, ARRAY_A);
		for ($i = 0; $i < count($res); $i++) {
			$res[$i]['count'] = $wpdb->get_var(
				$wpdb->prepare("SELECT count(sid) FROM {$table_name} WHERE post_cate=%d", esc_sql($res[$i]['post_cate']))
			);
		}
		return $res;
	}

	public static function set_cate_list($cate_name = "", $cate_id = "") {
		global $wpdb;
		$table_name = $wpdb->prefix . 'mxp_pchome_post_log';
		if ($cate_id == "" || $cate_name == "") {
			return "錯誤參數";
		}
		$account = get_option("mxp_pchome2wp_account", "");
		if ($account == "") {
			return "無授權";
		}
		$cate_name = esc_sql(sanitize_text_field($cate_name));
		$cate_id = sanitize_text_field(intval($cate_id));
		$last_page_num = self::get_category_last_page($cate_id);
		if (isset($last_page_num['message'])) {
			return $last_page_num['message'];
		}
		for ($i = 0; $i <= $last_page_num; ++$i) {
			$lists = self::start_from_category($cate_id, $i);
			if (isset($lists['message'])) {
				return $lists['message'];
			}
			foreach ($lists as $item) {
				$exists = $wpdb->get_var(
					$wpdb->prepare("SELECT COUNT(pid) FROM {$table_name} WHERE pid = %s", esc_sql($item['pid']))
				);
				if (!$exists) {
					$insert_res = $wpdb->insert($table_name, array('post_url' => esc_sql($item['link']), 'cate_name' => esc_sql($cate_name), 'post_name' => esc_sql($item['title']), 'is_import' => 0, 'post_cate' => $cate_id, 'created_time' => esc_sql($item['date']), 'pid' => esc_sql($item['pid'])));
					if ($insert_res === false) {
						return "發生問題，紀錄失敗！";
					}
				}
			}
		}
		return true;
	}

	public static function mxp_import_image($post_id, $imgs) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$filename = array();
		$upload_file = array();
		$upload_dir = wp_upload_dir();
		for ($i = 0; $i < count($imgs); ++$i) {
			$filename[] = str_replace($upload_dir['baseurl'] . "/", "", $imgs[$i]);
			$upload_file[] = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $imgs[$i]);
		}
		//self::logger('mxp_import_image', print_r($filename, true));
		//如果上傳沒失敗，就附加到剛剛那篇文章
		$set_feature_image = true;
		for ($i = 0; $i < count($upload_file); ++$i) {
			if (isset($upload_file[$i]) && $upload_file[$i] != "" && isset($filename[$i]) && $filename[$i] != "") {
				$wp_filetype = wp_check_filetype($filename[$i], null);
				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_parent' => $post_id,
					'post_title' => preg_replace('/\.[^.]+$/', '', $filename[$i]),
					'post_content' => '',
					'post_status' => 'inherit',
				);
				$attachment_id = wp_insert_attachment($attachment, $upload_file[$i], $post_id);
				if (!is_wp_error($attachment_id)) {
					//產生附加檔案中繼資料
					$attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file[$i]);
					wp_update_attachment_metadata($attachment_id, $attachment_data);
					//將圖像的附加檔案設為特色圖片
					$type = explode("/", $wp_filetype['type']);
					if ($set_feature_image == true && $type[0] == 'image') {
						set_post_thumbnail($post_id, $attachment_id);
						$set_feature_image = false;
					}
				}
			}
		}
	}

	public function mxp_options_import_action() {
		set_time_limit(0);
		ini_set('memory_limit', '256M');
		global $wpdb;
		$table_name = $wpdb->prefix . 'mxp_pchome_post_log';
		$sid = $_POST['sid'];
		$post_cate = $_POST['cate_id'];
		$nonce = $_POST['nonce'];
		if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce') || !isset($sid) || !isset($post_cate)) {
			wp_send_json_error(array('data' => array('msg' => '錯誤的請求')));
		}
		$sid = sanitize_text_field(intval($sid));
		$post_cate = sanitize_text_field(intval($post_cate));
		$url = $wpdb->get_var(
			$wpdb->prepare("SELECT post_url FROM {$table_name} WHERE sid=%d", esc_sql($sid))
		);
		$import_result = self::single_post_parsing($url);
		if (isset($import_result['message'])) {
			wp_send_json_error(array('msg' => $import_result['message']));
		}
		if (get_option("mxp_pchome2wp_pay_user") != "" && get_option("mxp_pchome2wp_pay_user") == 1) {
			$tags = array();
			if (get_option("mxp_pchome2wp_post_tags", "") != "") {
				$tags = explode(',', get_option("mxp_pchome2wp_post_tags"));
				$tags = array_filter($tags);
			}
			$post_tags = array_filter($import_result['post_tags']);
			$merge_tags = array_merge($tags, $post_tags);
		}
		$new_post = array(
			'post_title' => $import_result['title'],
			'post_date' => date('Y-m-d H:i:s', $import_result['post_date']),
			'post_name' => "post-" . $import_result['post_slug'],
			'post_content' => '', //先不處理，等圖片爬完後使用更新方式
			'post_status' => get_option("mxp_pchome2wp_post_status", "publish"),
			'post_author' => get_option("mxp_pchome2wp_post_author", "1"),
			'post_category' => array($post_cate),
			'tags_input' => $merge_tags,
			'comment_status' => get_option("mxp_pchome2wp_post_comment_status", "open"),
			'ping_status' => get_option("mxp_pchome2wp_post_ping_status", "open"),
			'post_type' => get_option("mxp_pchome2wp_post_type", "post"),
		);
		$post_id = wp_insert_post($new_post);
		if (!is_wp_error($post_id)) {
			//匯入圖片
			$update_attachment_post = array(
				'ID' => $post_id,
				'post_content' => self::parsing_image($post_id, $import_result['post_body']),
			);
			if (get_option("mxp_pchome2wp_pay_user") != "" && get_option("mxp_pchome2wp_pay_user") == 1) {
				$update_attachment_post['post_excerpt'] = wp_trim_words($update_attachment_post['post_content'], 200, '...');
			}
			$upid = wp_update_post($update_attachment_post);
			//匯入留言
			for ($i = 0; $i < count($import_result['post_comment']); ++$i) {
				$cm = $import_result['post_comment'][$i];
				if ($cm['body'] != 'null' && $cm['user'] != 'null') {
					$comment_data = array(
						'comment_post_ID' => $post_id,
						'comment_author' => $cm['user'],
						// 'comment_author_email' => 'admin@admin.com',
						// 'comment_author_url' => 'http://',
						'comment_content' => $cm['body'],
						'comment_parent' => 0,
						'comment_author_IP' => '127.0.0.1',
						'comment_agent' => 'By Mxp.TW',
						'comment_date' => date('Y-m-d H:i:s', $cm['date']),
						'comment_approved' => 1,
					);
					$cm_id = wp_insert_comment($comment_data);
				}
				if (isset($cm['reply']['body']) && isset($cm_id)) {
					$comment_data = array(
						'comment_post_ID' => $post_id,
						'comment_author' => get_option("mxp_pchome2wp_post_comment_admin_displayname", "版主"),
						'comment_author_email' => get_option("mxp_pchome2wp_post_comment_admin_email", ""),
						'comment_author_url' => get_site_url(),
						'comment_content' => $cm['reply']['body'],
						// 'comment_type' => '',
						'comment_parent' => $cm_id,
						'user_id' => get_option("mxp_pchome2wp_post_author", "1"),
						'comment_author_IP' => '127.0.0.1',
						'comment_agent' => 'By Mxp.TW',
						'comment_date' => date('Y-m-d H:i:s', $cm['reply']['date']),
						'comment_approved' => 1,
					);
					$cm_id = wp_insert_comment($comment_data);
				}
			}
			$update_result = $wpdb->update($table_name, array('is_import' => 1), array('sid' => $sid), array('%d'), array('%d'));
			if (false === $update_result) {
				wp_send_json_error(array('data' => array('msg' => '更新資料庫發生錯誤')));
			} else {
				wp_send_json_success(array('data' => $import_result));
			}
		} else {
			wp_send_json_error(array('data' => array('msg' => '匯入發生錯誤：' . $post_id->get_error_message())));
		}
	}

	public static function get_plugin_logs() {
		$list = scandir(plugin_dir_path(__FILE__) . 'logs/');
		if ($list == false) {
			return array();
		}
		$logs = array();
		for ($i = 0; $i < count($list); ++$i) {
			$end = explode('.', $list[$i]);
			if ('txt' == end($end)) {
				$logs[] = plugin_dir_url(__FILE__) . 'logs/' . $list[$i];
			}
		}
		return $logs;
	}

	public static function logger($file, $data) {
		if (get_option("mxp_enable_debug", "yes") == "yes") {
			file_put_contents(
				plugin_dir_path(__FILE__) . "logs/{$file}.txt",
				'===' . time() . '===' . PHP_EOL . $data . PHP_EOL,
				FILE_APPEND
			);
		}
	}
}

add_action('plugins_loaded', array('Mxp_PCHOME2WP', 'get_instance'));
