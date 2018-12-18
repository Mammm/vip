<?php

if (!function_exists('app')) {
    function app($key)
    {
        if (!$key)
            return false;

        return Container::instance()->make($key);
    }
}

if (!function_exists('httpRequest')) {
    function httpRequest($url, $param, $method = 'GET', $header = [])
    {
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $header
        );
        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $url = $url.'?'.http_build_query($param);
                $opts[CURLOPT_URL] = $url;
                break;
            case 'POST':
                //判断是否传输文件
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $param;
                break;
            default:
                throw new Exception('no support method');
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error)
            throw new Exception('请求发生错误：' . $error);

        return $data;
    }
}

if (!function_exists('jsonResponse')) {
    /**
     * 响应
     * @param $errorCode
     * @param $data
     * @return string
     */
    function jsonResponse($errorCode, $data = [])
    {
        $response = [
            'code' => $errorCode,
            'msg' => Message::getErrorMessage($errorCode),
            'data' => $data
        ];

        exit(json_encode($response));
    }
}
if (!function_exists('paramHas')) {

    function paramHas($params, $has)
    {
        $params = is_object($params) ? (array)$params : $params;
        $has = is_array($has) ? $has : [$has];

        foreach ($has as $v) {
            if (!isset($params[$v]) || isEmptyStr($params[$v]))
                return false;
        }

        return true;
    }
}
if (!function_exists('isEmptyStr')) {

    function isEmptyStr($value)
    {
        $boolOrArray = is_bool($value) || is_array($value);

        return !$boolOrArray && trim((string)$value) === '';
    }
}

if (!function_exists('dd')) {
    function dd($value)
    {
        echo "<pre/>";
        var_dump($value);
        exit;
    }
}

if (!function_exists('sqlGet')) {
    function sqlGet($sql)
    {
        return app('db')->query($sql);
    }
}

if (!function_exists('sqlFirst')) {
    function sqlFirst($sql)
    {
        $result = sqlGet($sql);

        return isset($result[0]) ? $result[0] : null;
    }
}

if (!function_exists('sqlList')) {
    function sqlList($sql, $field, $keyField = false)
    {
        $result = app('db')->query($sql);

        if (0 == count($result))
            return [];

        if (!isset($result[0][$field]) || ($keyField && !isset($result[0][$keyField])))
            throw new \Exception('sql list field error');

        $list = [];
        foreach ($result as $key => $item) {
            $listKey = $keyField ? $item[$keyField] : $key;
            $list[$listKey] = $item[$field];
        }

        return $list;
    }
}

if (!function_exists('sqlValue')) {
    function sqlValue($sql, $field)
    {
        $first = sqlFirst($sql);

        return isset($first[$field]) ? $first[$field] : null;
    }
}


if (!function_exists('publicPath')) {
    function publicPath($path)
    {
        return ROOT.DIRECTORY_SEPARATOR.'Public'.DIRECTORY_SEPARATOR.$path;
    }
}

if (!function_exists('giveMeASavePath')) {
    function giveMeASavePath($ext)
    {
        $day = date('Ymd');

        $path = publicPath("Uploads".DIRECTORY_SEPARATOR."{$day}");

        if (!is_dir($path) && !mkdir($path))
            throw new \Exception('add image path');

        return $path . DIRECTORY_SEPARATOR . date('His') . mt_rand(100, 999) . '.' . $ext;
    }
}

