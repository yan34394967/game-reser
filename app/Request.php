<?php

namespace app;
/**
 * 请求参数封装,过滤
 */
class Request extends \support\Request
{
    public function get($name = null, $default = null, $verif = null)
    {
        $value = $this->filter(parent::get($name, $default), $verif);
        if ($value == '' && $default) $value = $default;
        return $value;
    }

    public function post($name = null, $default = null, $verif = null)
    {
        $value = $this->filter(parent::post($name, $default), $verif);
        if ($value == '' && $default) $value = $default;
        return $value;
    }

    public function param($name = null, $default = null, $verif = null)
    {
        if (request()->method() == 'POST') {
            $value = $this->filter(parent::post($name, $default), $verif);
        } elseif (request()->method() == 'GET') {
            $value = $this->filter(parent::get($name, $default), $verif);
        }
        if ($value == '' && $default) $value = $default;
        return $value;
    }

    public function filter($value, $verif=null)
    {
        if (!$value) {
            return $value;
        }
        if (is_array($value)) {
            array_walk_recursive($value, function(&$item, $key) use($verif) {
                if (is_string($item) && empty($verif)) {
                    $item = trim(htmlentities($item,ENT_QUOTES,"UTF-8"));
                } else {
                    if ($verif) {
                        $verifExp = explode(',', $verif);
                    } else {
                        $verifExp = ['trim'];
                    }
                    foreach ($verifExp as $exp) {
                        $item = $exp($item);
                    }
                }
            });
        } else {
            if (empty($verif)) {
                $value = trim(htmlentities($value, ENT_QUOTES, "UTF-8"));
            } else {
                $verifExp = explode(',', $verif);
                foreach ($verifExp as $exp) {
                    $value = $exp($value);
                }
            }
        }
        return $value;
    }
}
