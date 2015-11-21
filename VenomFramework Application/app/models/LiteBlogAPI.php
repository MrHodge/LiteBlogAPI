<?php

/**
 * LiteBlogAPI.php Nov 21, 2015
 * Copyright (c) 2015 Venom Services
 *
 * LICENSE:
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author VenomServices <contact@venomservices.com>
 * @copyright 2015 VenomServices
 * @license https://opensource.org/licenses/MIT
 * @link https://venomservices.com
 */

namespace models;

use vfw\classes\Log;

class LiteBlogAPI
{

    /**
     * @var string
     */
    private $url = "";

    /**
     * @var string
     */
    private $api_key = "";

    /**
     * @var string
     */
    private $version = "1.0";

    /**
     * @var int
     */
    private $cacheTime = 1200;

    /**
     * @param string $url The url to the API.
     * @param string $api_key The api key from your account settings page.
     * @param integer $cacheTime The time that data will be cached for.
     */
    public function __construct($url, $api_key, $cacheTime = 1200)
    {
        $this->url = rtrim($url, "/");
        $this->api_key = $api_key;
        $this->cacheTime = $cacheTime;
    }

    /**
     * This function returns an object of all posts
     *
     * @param integer $limit The max amount of posts to return, newest first. Default to 100.
     * @return array
     */
    public function getPosts($limit = 100)
    {
        return json_decode($this->getData($this->url . "/" . $this->version . "/" . $this->api_key . "/get/posts/" . $limit));
    }


    /**
     * This function checks if curl exists, if it does it will use it instead of file_get_contents for it's advantages.
     * Data will be returned as a JSON String.
     *
     * @param $url
     * @return mixed|string
     */
    private function getData($url)
    {
        if(!is_writable(APP_PATH)){
            Log::severe("[LiteBlogAPI] Please make the app directory writable.");
            return "{}";
        }
        if(!file_exists(APP_PATH . "/cache/LiteBlogAPI/")){
            mkdir(APP_PATH . "/cache/LiteBlogAPI/");
        }
        $file = APP_PATH . "/cache/LiteBlogAPI/" . md5($url) . ".json";
        if(file_exists($file) && time() - $this->cacheTime < filemtime($file)){
            return file_get_contents($file);
        }
        if(function_exists('curl_version')) {
            $data = $this->curl($url);
            file_put_contents($file, $data);
            return $data;
        } else {
            $data = file_get_contents($url);
            file_put_contents($file, $data);
            return $data;
        }
    }

    /**
     *  This function gets the data from the API and returns the JSON String.
     *
     * @param $url
     * @return mixed|string
     */
    private function curl($url)
    {
        $data = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "PHP LiteBlogAPI v" . $this->version);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}