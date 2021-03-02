<?php

class Shopee {
    public function get_item_v1($item_url){
        $explode = explode('.', $item_url);
        $url = 'https://shopee.co.id/api/v2/item/get?itemid=' .$explode[4]. '&shopid=' .$explode[3];
        $result = json_decode($this->curl($url, null, null, null, 'GET'), true);
        if(isset($result['error'])){
            echo "[ERROR] ".date('Y-m-d H:i:s')." | ".$result['error_msg']."\n";
            return false;
        }
        return $result;
    }
    public function get_item_v2($item_url){
        $shop_id = explode('/', $item_url);
        $item_id = explode('?', $shop_id[5])[0];
        //print_r($shop_id);
        //echo "\n";
        //print_r($item_id);
        $url = 'https://shopee.co.id/api/v2/item/get?itemid=' .$item_id. '&shopid=' .$shop_id[4];
        $result = json_decode($this->curl($url, null, null, null, 'GET'), true);
        if(isset($result['error'])){
            echo "[ERROR] ".date('Y-m-d H:i:s')." | ".$result['error_msg']."\n";
            return false;
        }
        return $result;
    }
    public function add_cart($item_url, $data = []){
        $url = 'https://shopee.co.id/api/v2/cart/add_to_cart';
        $postdata = '{"quantity":1,"checkout":true,"update_checkout_only":false,"donot_add_quantity":false,"source":"{\"refer_urls\":[]}","client_source":1,"shopid":' .$data['shopid']. ',"itemid":' .$data['itemid']. ',"modelid":' .$data['modelid']. '}';
        add_cart:
        $result = json_decode($this->curl($url, $postdata, $this->header($item_url)), true);
        if(isset($result['error'])){
            if ($result['error'] == 0) {
                return $result;
            //} else if($result['error'] == 6){
            //    echo "[ERROR] ".date('Y-m-d H:i:s')." | Gagal memasukkan ke dalam keranjang (add_cart_disable)\n";
            //    return false;
            } else {
                echo "[ERROR] ".date('Y-m-d H:i:s')." | Gagal memasukkan ke dalam keranjang\n";
                return false;
            }
        } else {
            echo "[ERROR] ".date('Y-m-d H:i:s')." | Gagal memasukkan ke dalam keranjang\n";
            goto add_cart;
        }
        //return $this->header($url);
    }

    public function cart(){
        $url = 'https://shopee.co.id/api/v4/cart/get';
        $postdata = '{"pre_selected_item_list":[]}';
        cart:
        $result = json_decode($this->curl($url, $postdata, $this->header()), true);
        if(isset($result['error'])){
            if ($result['error'] == 0) {
                return $result['data'];
            } else {
                return false;
            }
        } else {
            echo "[ERROR] ".date('Y-m-d H:i:s')." | Gagal mendapatkan info keranjang\n";
            goto cart;
        }
    }

    public function cart_checkout($data){
        $url = 'https://shopee.co.id/api/v4/cart/checkout';
        $postdata = '{"selected_shop_order_ids":[{"shopid":' .$data['shopid']. ',"item_briefs":[{"itemid":' .$data['itemid']. ',"modelid":' .$data['modelid']. ',"item_group_id":null,"applied_promotion_id":80644,"offerid":null,"price":' .$data['price']. ',"quantity":' .$data['quantity']. ',"is_add_on_sub_item":null,"add_on_deal_id":null,"status":1,"cart_item_change_time":1606943873}],"shop_vouchers":[]}],"platform_vouchers":[]}';
        cart_checkout:
        $result = json_decode($this->curl($url, $postdata, $this->header()), true);
        print_r($result, true);
        if(isset($result['error'])){
            if ($result['error'] == 0) {
                return $result;
            } else {
                return false;
            }
        } else {
            echo "[ERROR] ".date('Y-m-d H:i:s')." | ".$result['error_msg']."\n";
            goto cart_checkout;
        }
        //return $this->header($url);
    }
    public function get_checkout($data){
        $url = 'https://shopee.co.id/api/v2/checkout/get';
        $postdata = '{"shoporders":[{"shop":{"shopid":' .$data['shopid']. '},"items":[{"itemid":' .$data['itemid']. ',"modelid":' .$data['modelid']. ',"add_on_deal_id":null,"is_add_on_sub_item":null,"item_group_id":null,"quantity":' .$data['quantity']. '}],"logistics":{"recommended_channelids":null},"buyer_address_data":{},"selected_preferred_delivery_time_slot_id":null}],"selected_payment_channel_data":{},"promotion_data":{"use_coins":false,"free_shipping_voucher_info":{"free_shipping_voucher_id":0,"disabled_reason":"","description":""},"platform_vouchers":[],"shop_vouchers":[],"check_shop_voucher_entrances":true,"auto_apply_shop_voucher":false},"device_info":{"device_id":"","device_fingerprint":"","tongdun_blackbox":"","buyer_payment_info":{}},"tax_info":{"tax_id":""}}';
        $result = $this->curl($url, $postdata, $this->header());
        if (isset(json_decode($result, true)['error']) == 'error_empty_cart') {
            echo "[ERROR] ".date('Y-m-d H:i:s')." | ".json_decode($result, true)['error_msg']."\n";
            return false;
        } else {
            $result = str_replace('{"cart_type":0', '"cart_type":0', $result);
            return '{"status":200,"headers":{},' .$result;
        }
        
        //print_r(json_decode($result, true));
        //$result = str_replace('{"cart_type":0', '"cart_type":0', $result);
        //return '{"status":200,"headers":{},' .$result;
    }

    public function place_order($postdata){
        $url = 'https://shopee.co.id/api/v2/checkout/place_order';
        $result = json_decode($this->curl($url, $postdata, $this->header()), true);
        if (isset($result['redirect_url'])) {
            echo "[SUCCESS] ".date('Y-m-d H:i:s')." | Berhasil !!! \n Link pembayaran :  ".$result['redirect_url']."\n";
            return true;
        } else {
            echo "[ERROR] ".date('Y-m-d H:i:s')." | ".$result['error_msg']."\n";
            return false;
        }
        //return $postdata;
    }

    protected function header($url = false){
        $headers = [];
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
        $headers[] = 'Accept: application/json';
        $headers[] = 'Cookie: ' . file_get_contents('cookie.txt');
        $headers[] = 'Origin: https://shopee.co.id';
        $headers[] = (isset($url)) ? 'Referer: ' . $url : null;
        $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.190 Mobile Safari/537.36';
        $headers[] = 'Accept: application/json';
        $headers[] = 'X-API-SOURCE: rweb';
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'X-CSRFTOKEN: ' . file_get_contents('csrf_token.txt');
        $headers[] = 'X-Shopee-Language: id';

        return $headers;
    }   
    protected function curl($url, $post, $headers, $follow=false, $method=null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($follow == true) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if ($method !== null) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($headers !== null) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($post !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        $header = substr($result, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        $body = substr($result, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        return $body;
    }
}