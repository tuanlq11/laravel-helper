<?php

namespace tuanlq11\laravelhelper;

/**
 * Class RequestHelper
 * @package App\Helper
 */
class RequestHelper
{
    protected static $agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:39.0) Gecko/20100101 Firefox/39.0';

    /**
     * @param $file
     * @param $postName
     * @param string $contentType
     * @return \CURLFile|string
     */
    protected static function getFileBody($file, $postName, $contentType = 'image/jpeg')
    {
        if (!file_exists($file)) {
            return '';
        }

        return curl_file_create($file, $contentType, $postName);
    }

    protected static function cookie2Text()
    {
        $cookie = '';
        $cookies = $_COOKIE;

        foreach ($cookies as $name => $val) {
            $cookie .= sprintf('%s=%s;', $name, $val);
        }

        return rtrim($cookie, ';');
    }

    /**
     * @param $url
     * @param $param
     * @param $file
     * @param null $postName
     * @param string $fileField
     * @param string $user
     * @param string $password
     * @param int $timeout
     * @return mixed
     */
    public static function transferFile($url, $param, $file, $postName = null, $fileField = 'file', $user = '', $password = '', $timeout = 120)
    {
        $ch = curl_init();

        $data = array_merge([$fileField => self::getFileBody($file, $postName, 'image/jpeg')], $param);

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_USERAGENT => self::$agent,
            CURLOPT_HTTPHEADER => [
                'Content-Type: multipart/form-data',
                sprintf('Authorization: Basic %s', base64_encode("{$user}:{$password}"))
            ]
        ];

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param $url
     * @param $param
     * @param null $user
     * @param string $password
     * @param int $timeout
     * @return mixed
     */
    public static function makePost($url, $param, $user = null, $password = '', $resturnHeader = false, $timeout = 30)
    {
        $query_string = json_encode($param);
        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => self::$agent,
            CURLOPT_POST => count($param),
            CURLOPT_POSTFIELDS => $query_string,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array_merge(array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($query_string)
            ), !is_null($user) ? [sprintf('Authorization: Basic %s', base64_encode("{$user}:{$password}"))] : []),
            CURLOPT_COOKIE => self::cookie2Text(),
            CURLOPT_HEADER => $resturnHeader
        ];

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param $url
     * @param $param
     * @param null $user
     * @param string $password
     * @param int $timeout
     * @return mixed
     */
    public static function makeGet($url, $param, $user = null, $password = '', $resturnHeader = false, $timeout = 30, $contentType = 'application/json')
    {
        $query_string = is_string($param)?$param:http_build_query($param);
        $ch = curl_init();

        $options = [
            CURLOPT_URL => "{$url}?{$query_string}",
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => self::$agent,
            CURLOPT_HTTPHEADER => array_merge([
                !$contentType?'':'Content-Type: application/json'
            ], !is_null($user) ? [sprintf('Authorization: Basic %s', base64_encode("{$user}:{$password}"))] : []),
            CURLOPT_COOKIE => self::cookie2Text(),
            CURLOPT_HEADER => $resturnHeader
        ];

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}