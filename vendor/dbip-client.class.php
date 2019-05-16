<?php

/**
 *
 * DB-IP.com API client class
 *
 * Copyright (C) 2016 db-ip.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */

namespace DBIP;

class Client_Exception extends \Exception {

}

class Client {
    
    private $base_url = "http://api.db-ip.com/v2/";
    private $api_key;
    private $lang;
    
    public function __construct($api_key, $base_url = null) {
        $this->api_key = $api_key;
        if (isset($base_url)) {
            $this->base_url = $base_url;
        }
        if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
            $this->Set_Preferred_Language($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        }
    }
    
    protected function Do_API_Call($path = "") {
        $url = $this->base_url . $this->api_key . $path;
        if (isset($this->lang)) {
            $jdata = @file_get_contents($url, false, stream_context_create(array(
                "http" => array(
                    "header" => "Accept-Language: " . $this->lang,
                ),
            )));
        } else {
            $jdata = @file_get_contents($url);
        }
        if (!$jdata) {
            throw new Client_Exception("unable to fetch URL: {$url}");
        }
        if (!$data = @json_decode($jdata)) {
            throw new Client_Exception("cannot decode server response");
        }
        if (isset($data->error) && $data->error) {
            throw new Client_Exception("server reported an error: {$data->error}");
        }
        return $data;
    }
    
    public function Set_Preferred_Language($lang) {
        $this->lang = $lang;
    }
    
    public function Get_Address_Info($addr) {
        if (is_array($addr)) {
            return $this->Do_API_Call("/" . implode(",", $addr));
        } else {
            return $this->Do_API_Call("/" . $addr);
        }
    }
    
    public function Get_Key_Info() {
        return $this->Do_API_Call();
    }
    
}
