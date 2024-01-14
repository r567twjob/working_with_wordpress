<?php

function irent_register_nav_menu() {
    register_nav_menus(array(
        'desktop_menu' => __('Desktop Menu', 'mytheme'),
        'mobile_menu' => __('Mobile Menu', 'mytheme')
    ));
}
add_action('after_setup_theme', 'irent_register_nav_menu');


//關閉 XML_RPC
add_filter('xmlrpc_enabled', '__return_false');

//最佳化主題樣式相關
function mxp_optimize_theme_setup() {
    //整理head資訊
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    add_filter('the_generator', '__return_false');
    remove_action('wp_head', 'feed_links_extra', 3);
    //移除css, js資源載入時的版本資訊
    function remove_version_query($src) {
        if (strpos($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
    add_filter('style_loader_src', 'remove_version_query', 999);
    add_filter('script_loader_src', 'remove_version_query', 999);
}
add_action('after_setup_theme', 'mxp_optimize_theme_setup');

// 停用表情符號 & 嵌入 & RSS 等功能
function mxp_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}

function disable_embeds_tiny_mce_plugin($plugins) {
    return array_diff($plugins, array('wpembed'));
}

function disable_embeds_rewrites($rules) {
    foreach ($rules as $rule => $rewrite) {
        if (false !== strpos($rewrite, 'embed=true')) {
            unset($rules[$rule]);
        }
    }
    return $rules;
}

function mxp_disable_functions() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_action('rest_api_init', 'wp_oembed_register_route');
    add_filter('embed_oembed_discover', '__return_false');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    add_filter('tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin');
    add_filter('rewrite_rules_array', 'disable_embeds_rewrites');
    remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
    add_filter('tiny_mce_plugins', 'mxp_disable_emojis_tinymce');
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
}
add_action('init', 'mxp_disable_functions');

// 停用自己站內的引用
function mxp_disable_self_ping(&$links) {
    $home = get_option('home');
    foreach ($links as $l => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
}
add_action('pre_ping', 'mxp_disable_self_ping');

function mxp_disable_rss_feed_function() {
    wp_die(__('無提供 RSS 服務，請返回 <a rel="noopener" href="' . esc_url(home_url('/')) . '">首頁</a>!'));
}
add_action('do_feed', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_rdf', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_rss', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_rss2', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_atom', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_rss2_comments', 'mxp_disable_rss_feed_function', 1);
add_action('do_feed_atom_comments', 'mxp_disable_rss_feed_function', 1);

function mxp_admin_menu_modify_for_user() {
}
add_action('admin_init', 'mxp_admin_menu_modify_for_user', 100);

//使用 instant.page 加速網站頁面讀取(處理連結預處理功能)
function mxp_add_instant_page() {
    echo '<script src="//instant.page/5.2.0" type="module" integrity="sha384-jnZyxPjiipYXnSU0ygqeac2q7CVYMbh84q0uHVRRxEtvFPiQYbXWUorga2aqZJ0z"></script>';
}
add_action('wp_footer', 'mxp_add_instant_page');

// //修改「網站遭遇技術性問題」通知信收件人
// function mxp_change_recovery_mode_email($email, $url) {
//     $email['to'] = 'im@mxp.tw'; //收件人
//     // $email['subject'] //主旨
//     // $email['message'] //內文
//     // $email['headers'] //信件標頭
//     return $email;
// }
// add_filter('recovery_mode_email', 'mxp_change_recovery_mode_email', 11, 2);

//輸出 X-Frame-Options HTTP Header
add_action('send_headers', 'send_frame_options_header', 10, 0);
//關閉 HTTP Header 中出現的 Links
add_filter('oembed_discovery_links', '__return_null');
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);
remove_action('template_redirect', 'wp_shortlink_header', 11);
// 關閉 wp-json 首頁顯示的 API 清單
add_filter('rest_index', '__return_empty_array');
// 沒登入的使用者都無法呼叫 wp/users 這隻 API。不建議完全封鎖掉，會導致有些後台功能運作失靈
if (function_exists('is_user_logged_in') && !is_user_logged_in()) {
    add_filter('rest_user_query', '__return_null');
    add_filter('rest_prepare_user', '__return_null');
}

function mxp_security_headers($headers) {
    // 啟用 X-XSS-Protection
    $headers['X-XSS-Protection'] = '1; mode=block';
    // 啟用 Strict-Transport-Security
    // max-age=31536000; 表示這個頭部設定將持續 1 年 (以秒為單位)
    // includeSubDomains; 表示所有子域都將使用 HTTPS
    // preload; 表示這個設定可以被瀏覽器預加載
    $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains; preload';
    // 啟用 X-Content-Type-Options
    // nosniff 選項阻止瀏覽器嘗試根據內容或URL猜測並更改內容類型
    $headers['X-Content-Type-Options'] = 'nosniff';
    // 啟用 X-Content-Security-Policy
    // 這裡設定為最嚴格的策略，只允許從同一來源加載腳本和其他資源
    $headers['X-Content-Security-Policy'] = "default-src 'self'; script-src 'self'; connect-src 'self'";
    // 啟用 X-Permitted-Cross-Domain-Policies
    // none 表示不允許跨域政策文件
    $headers['X-Permitted-Cross-Domain-Policies'] = "none";
    return $headers;
}
add_filter('wp_headers', 'mxp_security_headers');

// //內對外請求管制方法
// function mxp_block_external_request($preempt, $parsed_args, $url) {
//     // $block_urls = array(
//     //     "wpemaillog.com",
//     // );
//     // $whitelist_urls = array(
//     //     "api.wordpress.org",
//     //     "downloads.wordpress.org",
//     // );
//     $request_domain = parse_url($url, PHP_URL_HOST);
//     if (!in_array($request_domain, $whitelist_urls, true)) {
//         return new WP_Error('http_request_block', '不允許的對外請求路徑' . "\n:: {$url}", $url);
//     }
//     return $preempt;
// }
// add_filter("pre_http_request", "mxp_block_external_request", 11, 3);

// 預設不顯示出系統輸出的作者連結與頁面，避免資安問題
function mxp_hide_author_for_safe($link) {
    return '小編';
}
add_filter('the_author_posts_link', 'mxp_hide_author_for_safe', 11, 1);

// 預設作者的連結都不顯示
function mxp_hide_author_link($link, $author_id, $author_nicename) {
    return '#';
}
add_filter('author_link', 'mxp_hide_author_link', 3, 100);

// 關閉 heartbeat 功能
function mxp_stop_heartbeat_function() {
    wp_deregister_script('heartbeat');
}
add_action('init', 'mxp_stop_heartbeat_function', 1);


// if (!function_exists('wpdb_bulk_insert')) {
//     //一次大量新增資料的資料庫操作方法  Ref: https://gist.github.com/pauln/884e1a229d439640fbe35e848852fe0b
//     function wpdb_bulk_insert($table, $rows) {
//         global $wpdb;

//         // Extract column list from first row of data
//         $columns = array_keys($rows[0]);
//         asort($columns);
//         $columnList = '`' . implode('`, `', $columns) . '`';

//         // Start building SQL, initialise data and placeholder arrays
//         $sql          = "INSERT INTO `$table` ($columnList) VALUES\n";
//         $placeholders = array();
//         $data         = array();

//         // Build placeholders for each row, and add values to data array
//         foreach ($rows as $row) {
//             ksort($row);
//             $rowPlaceholders = array();

//             foreach ($row as $key => $value) {
//                 $data[]            = $value;
//                 $rowPlaceholders[] = is_numeric($value) ? '%d' : '%s';
//             }

//             $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
//         }

//         // Stitch all rows together
//         $sql .= implode(",\n", $placeholders);

//         // Run the query.  Returns number of affected rows.
//         return $wpdb->query($wpdb->prepare($sql, $data));
//     }
// }

// if (!function_exists('mxp_get_error_backtrace')) {
//     // WordPress PHP 偵錯用的方法
//     function mxp_get_error_backtrace($last_error_file = __FILE__, $for_irc = false) {

//         $backtrace = debug_backtrace(0);
//         $call_path = array();
//         foreach ($backtrace as $bt_key => $call) {
//             if (!isset($call['args'])) {
//                 $call['args'] = array('');
//             }

//             if (in_array($call['function'], array(__FUNCTION__, 'mxp_get_error_backtrace'))) {
//                 continue;
//             }

//             $path = '';
//             if (!$for_irc) {
//                 $path = isset($call['file']) ? str_replace(ABSPATH, '', $call['file']) : '';
//                 $path .= isset($call['line']) ? ':' . $call['line'] : '';
//             }

//             if (isset($call['class'])) {
//                 $call_type = $call['type'] ? $call['type'] : '???';
//                 $path .= " {$call['class']}{$call_type}{$call['function']}()";
//             } elseif (in_array($call['function'], array('do_action', 'apply_filters'))) {
//                 if (is_object($call['args'][0]) && !method_exists($call['args'][0], '__toString')) {
//                     $path .= " {$call['function']}(Object)";
//                 } elseif (is_array($call['args'][0])) {
//                     $path .= " {$call['function']}(Array)";
//                 } else {
//                     $path .= " {$call['function']}('{$call['args'][0]}')";
//                 }
//             } elseif (in_array($call['function'], array('include', 'include_once', 'require', 'require_once'))) {
//                 $file = 0 == $bt_key ? $last_error_file : $call['args'][0];
//                 $path .= " {$call['function']}('" . str_replace(ABSPATH, '', $file) . "')";
//             } else {
//                 $path .= " {$call['function']}()";
//             }

//             $call_path[] = trim($path);
//         }

//         return implode(', ' . PHP_EOL, $call_path);
//     }
// }

// // 關閉後臺的「網站活動」區塊
// function mxp_remove_dashboard_widgets() {
//     remove_meta_box('dashboard_activity', 'dashboard', 'normal');
// }
// add_action('wp_dashboard_setup', 'mxp_remove_dashboard_widgets');

// // 不要發通知給終端使用者
// function mxp_wp_send_new_user_notification_to_user($bool, $user) {
//     return false;
// }
// add_filter('wp_send_new_user_notification_to_user', 'mxp_wp_send_new_user_notification_to_user', 11, 2);



// // 給發信標題全都加上請勿回覆字樣
// function mxp_wp_mail_add_subject_prefix($atts) {
//     $atts['subject'] = "(請勿回覆/No-Reply) " . $atts['subject'];
//     return $atts;
// }
// add_filter('wp_mail', 'mxp_wp_mail_add_subject_prefix', 11, 1);

// 使用者登入後轉址回指定位置
// function mxp_redirect_to_after_login() {
//     if (!is_user_logged_in()) {
//         $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
//         if (strpos($redirect_to, get_site_url()) === 0) {
//             setcookie('mxp_redirect_to', $redirect_to);
//             setcookie('mxp_redirect_to_count', 0);
//         }
//     } else {
//         if (isset($_COOKIE['mxp_redirect_to']) && $_COOKIE['mxp_redirect_to'] != '' && isset($_COOKIE['mxp_redirect_to_count']) && $_COOKIE['mxp_redirect_to_count'] != 0) {
//             setcookie("mxp_redirect_to", "", time() - 3600);
//             setcookie('mxp_redirect_to_count', 1);
//             wp_redirect($_COOKIE['mxp_redirect_to']);
//             exit;
//         }
//     }
// }
// add_action('template_redirect', 'mxp_redirect_to_after_login', -1);

/**
 ** 選擇性新增程式碼片段
 **/
// // 如果要關閉某些 CPT 的區塊編輯器可以啟用此區
// function mxp_disable_gutenberg($current_status, $post_type) {
//     if ($post_type === 'page') {
//         return false;
//     }
//     return $current_status;
// }
// add_filter('use_block_editor_for_post_type', 'mxp_disable_gutenberg', 11, 2);
//修正管理後台頁尾顯示
function dashboard_footer_design() {
    echo 'Design by <a href="http://www.knockers.com.tw">Knockers</a>';
}
add_filter('admin_footer_text', 'dashboard_footer_design');
//修正管理後台頁尾顯示
function dashboard_footer_developer() {
    echo '<br/><span id="footer-thankyou">Developed by <a href="https://www.mxp.tw">一介資男</a></span>';
}
add_filter('admin_footer_text', 'dashboard_footer_developer');
// //修正管理後台顯示
// function clean_my_admin_head() {
//     $screen = get_current_screen();
//     $str    = '';
//     if (is_admin() && ($screen->id == 'dashboard')) {
//         $str .= '<style>#wp-version-message { display: none; } #footer-upgrade {display: none;}</style>';
//     }
//     echo $str;
// }
// add_action('admin_head', 'clean_my_admin_head');

// 補上客製化檔案格式支援
// function mxp_custom_mime_types($mime_types) {
//     $mime_types['zip']  = 'application/zip';
//     $mime_types['rar']  = 'application/x-rar-compressed';
//     $mime_types['tar']  = 'application/x-tar';
//     $mime_types['gz']   = 'application/x-gzip';
//     $mime_types['gzip'] = 'application/x-gzip';
//     $mime_types['tiff'] = 'image/tiff';
//     $mime_types['tif']  = 'image/tiff';
//     $mime_types['bmp']  = 'image/bmp';
//     $mime_types['svg']  = 'image/svg+xml';
//     $mime_types['psd']  = 'image/vnd.adobe.photoshop';
//     $mime_types['ai']   = 'application/postscript';
//     $mime_types['indd'] = 'application/x-indesign';
//     $mime_types['eps']  = 'application/postscript';
//     $mime_types['rtf']  = 'application/rtf';
//     $mime_types['txt']  = 'text/plain';
//     $mime_types['wav']  = 'audio/x-wav';
//     $mime_types['csv']  = 'text/csv';
//     $mime_types['xml']  = 'application/xml';
//     $mime_types['flv']  = 'video/x-flv';
//     $mime_types['swf']  = 'application/x-shockwave-flash';
//     $mime_types['vcf']  = 'text/x-vcard';
//     $mime_types['html'] = 'text/html';
//     $mime_types['htm']  = 'text/html';
//     $mime_types['css']  = 'text/css';
//     $mime_types['js']   = 'application/javascript';
//     $mime_types['json'] = 'application/json';
//     $mime_types['ico']  = 'image/x-icon';
//     $mime_types['otf']  = 'application/x-font-otf';
//     $mime_types['ttf']  = 'application/x-font-ttf';
//     $mime_types['woff'] = 'application/x-font-woff';
//     $mime_types['ics']  = 'text/calendar';
//     $mime_types['ppt']  = 'application/vnd.ms-powerpoint';
//     $mime_types['pot']  = 'application/vnd.ms-powerpoint';
//     $mime_types['pps']  = 'application/vnd.ms-powerpoint';
//     $mime_types['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
//     $mime_types['doc']  = 'application/msword';
//     $mime_types['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
//     return $mime_types;
// }
// add_filter('upload_mimes', 'mxp_custom_mime_types', 1, 1);

// 修正針對 ico 上傳的解析問題
// function mxp_getimagesize_mimes_to_exts($img_arr) {
//     $img_arr['image/x-icon']             = 'ico';
//     $img_arr['image/vnd.microsoft.icon'] = 'ico';
//     return $img_arr;
// }
// add_filter('getimagesize_mimes_to_exts', 'mxp_getimagesize_mimes_to_exts', 11, 1);

// function logger($file, $data) {
//     file_put_contents(
//         ABSPATH . "wp-content/{$file}.txt",
//         '===' . date('Y-m-d H:i:s', time()) . '===' . PHP_EOL . $data . PHP_EOL,
//         FILE_APPEND
//     );
// }
// //引用 WooCommerce 設定程式碼片段
// include dirname(__FILE__). '/wc-settings.php';
// //引用 Knockers 網站狀態追蹤程式碼片段
// include dirname(__FILE__) . '/ks_server_checker.php';