<?php
date_default_timezone_set("Asia/Jakarta"); //WIB
require 'class.final.php';
$shopee = new Shopee();
start:
    $flashsale_date = strtotime(date('2021-03-06 07:09:00')); // SETTING WAKTU FLASHSALE DISINI * (Tahun-Bulan-Tanggal Jam:Menit:Detik) 
	$cart = $shopee->cart();
	if ($cart) {
		if (isset($cart['shop_orders'][0]['items'][0]['shopid'])) {
				$itemArray['shopid'] = $cart['shop_orders'][0]['items'][0]['shopid'];
				$itemArray['itemid'] = $cart['shop_orders'][0]['items'][0]['itemid'];
				$itemArray['modelid'] = $cart['shop_orders'][0]['items'][0]['modelid'];
				$itemArray['name'] = $cart['shop_orders'][0]['items'][0]['name'];
				$itemArray['quantity'] = $cart['shop_orders'][0]['items'][0]['quantity'];
				$itemArray['price'] =  $cart['shop_orders'][0]['items'][0]['price'];
				
				echo "[ITEM] ".date('Y-m-d H:i:s')." | ".$itemArray['name']." \n";
			cart_checkout:
				get_checkout:
				$cart_checkout = $shopee->cart_checkout($itemArray);
				if ($cart_checkout) {
					echo "[SUCCESS] ".date('Y-m-d H:i:s')." | Berhasil checkout barang yang ada di keranjang \n";
if (strtotime(date('Y-m-d H:i:s')) < $flashsale_date) {
				echo "[ERROR] ".date('Y-m-d H:i:s')." | Flashsale belum dimulai \n";
				goto cart_checkout;
}
					$get_checkout = $shopee->get_checkout($itemArray);
					if ($get_checkout) {
						place_order:
						$place_order = $shopee->place_order($get_checkout);
						if ($place_order) {
							echo $place_order. "\n";
							goto start;
						} else {
							echo $place_order . "\n";
							goto place_order;
						}
					} else {
						goto get_checkout;
					}	
				} else {
					echo "[ERROR] ".date('Y-m-d H:i:s')." | Gagal checkout !! \n";
					goto cart_checkout;
				}
				
		} else {
			echo "[ERROR] ".date('Y-m-d H:i:s')." | Keranjang kosong 1 \n";
			sleep(3);
			goto start;
		}
		
	} else {
		echo "[ERROR] ".date('Y-m-d H:i:s')." | Keranjang kosong 2 \n";	
	}
	
//goto start;	
