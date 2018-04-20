<?php

/**
 * oss上传类
 */
class Oss
{    

    public function __construct()
    {
        $this->config = include "config.php";
    }

    /**
     * [curlPost description]
     * @Author   W_wang
     * @email    1352255400@qq.com
     * @DateTime 2018-03-04T15:37:17+0800
     * @param    [type]                   $url      [description]
     * @param    [type]                   $data     [description]
     * @param    integer                  $is_build [description]
     * @return   [type]                             [description]
     */
    public function curlPost($url, $data ,$is_build = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($is_build == 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }else{
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }        
        $output = curl_exec($ch);
        curl_close($ch);        
        return $output;
    }


    /**
     * [uploadFile 上传文件]
     * @Author   W_wang
     * @email    1352255400@qq.com
     * @DateTime 2018-03-04T15:39:47+0800
     * @return   [type]                   [description]
     */
    public function uploadFile($data_file = [])
    {        
        $data = [];
        $data['c'] = 'uploadFile';//上传标识
        $data['from'] = $this->config['oss_prefix'];//来源
        $data['is_uediter'] = '1';//来源
        $data['is_cover'] = isset($data_file['is_cover']) ? "$data_file[is_cover]" : '0';
        //上传文件名称
        $data['upfile_name'] = isset($data_file['upfile_name']) ? $data_file['upfile_name'] : 'upfile';
        if (!$_FILES[$data['upfile_name']]) {
            return array('code'=>'1000','data'=>[],'msg'=>'uploadfile is missing！');
        }
        //上传地址 多层级用/分割（'path/path1/path2'）
        $data['upfile_path'] = isset($data_file['upfile_path']) ? $data_file['upfile_path'] : '';
        //缩略图设置 多个用|分割（'640_310|320_160'）
        $data['thumb_config'] = isset($data_file['thumb_config']) ? $data_file['thumb_config'] : '';
        ksort($data);//排序
        //生成签名
        $data['sig'] = md5($this->config['oss_token'].serialize($data));
        //初始化上传文件
        $data['upfile'] = new \CURLFile($_FILES[$data['upfile_name']]['tmp_name'],$_FILES[$data['upfile_name']]['type'],$_FILES[$data['upfile_name']]['name']);
        //上传
        $re = $this->curlPost($this->config['url_oss'],$data);
        @unlink($_FILES[$data['upfile_name']]['tmp_name']);//删除临时文件
        return json_decode($re,true);
    }
    

    /**
     * [deleteFile 删除文件]
     * @Author   W_wang
     * @email    1352255400@qq.com
     * @DateTime 2018-03-04T15:40:03+0800
     * @return   [type]                   [description]
     */
    public function deleteFile($data_file = [])
    {
        $data = [];
        $data['c'] = 'del';
        $data['from'] = $this->config['oss_prefix'];//来源
        $data['del_file_arr'] = isset($data_file['del_file_arr']) ? $data_file['del_file_arr'] : [];
        if (empty($data['del_file_arr'])) {
            return array('code'=>'1000','data'=>[],'msg'=>'del_file_arr is missing！');
        }
        ksort($data);
        $data['sig'] = md5($this->config['oss_token'].serialize($data));
        //删除
        $re = $this->curlPost($this->config['url_oss'],$data,1);
        return json_decode($re,true);
    }
    
}
