<?php
/**
 * 自定义配置文件
 * 创建在extra目录下
 */
$path = config_path('extra');
$res = [];
try {
    foreach (scandir($path) as $item) {
        if( pathinfo($item,PATHINFO_EXTENSION)=='php'){
            $file_path = $path.'/'.$item;
            $file_name = pathinfo($item,PATHINFO_FILENAME);
            $res[$file_name] = include  $file_path;
        }
    }

}catch (Exception $e){}
return $res;
