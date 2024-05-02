<?php
/*
Template Name: Custom Order Meta Page
*/



require("../wp-load.php");
// get_header();

if (isset($_POST['order_number']) && $_POST['order_number'] != '') {
    $order_number = $_POST['order_number'];
    $order = wc_get_order($order_number);

    if ($order) {
        $order_meta = $order->get_meta_data();
        $status = $order->get_status();
        $id=$order->get_id();
        $url = $order->get_checkout_order_received_url();

        // echo "<h2>訂單狀態: { $order->get_status() }</h2>";
        echo "<h2>訂單ID: {$id}</h2>";
        echo "<h2>訂單狀態: {$status}</h2>";

        if (!empty($order_meta)) {
            echo '<h2>訂單編號：' . $order_number . ' 的 Meta Data：</h2>';
            echo '<ul>';
            foreach ($order_meta as $meta) {
                echo '<li>' . $meta->key . '：' . print_r($meta->value,true) . '</li>';
            }
            echo '<li>'.$url.'</li>';
            echo '</ul>';
        } else {
            echo '<p>找不到訂單編號：' . $order_number . ' Meta Data。</p>';
        }
    } else {
        echo '<p>找不到訂單編號：' . $order_number . '。</p>';
    }
}

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <form action="" method="post">
            <label for="order_number">輸入訂單編號:</label>
            <input type="text" id="order_number" name="order_number">
            <input type="submit" value="查詢">
        </form>

    </main><!-- #main -->
</div><!-- #primary -->
