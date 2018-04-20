# oss
oss 上传
图片预览
http://static.xinxinst.com/sjnhm/banner/2018_03_28/15222060308561_900_450.jpg?size=180_90


http://static.xinxinst.com/sjnhm/banner/2018_03_28/15222060308561_900_450.jpg?size=600_300


http://static.xinxinst.com/sjnhm/banner/2018_03_28/15222060308561_900_450.jpg


返回实例

    # 1. 不生成缩略图
        
    $oss = new oss();
    $data_img = [];
    $data_img['upfile_name'] = 'upfile';//文件上传标签名称
    $data_img['upfile_path'] = 'chat';//存放文件夹
    $re = $oss->uploadFile($data_img);
    echo json_encode($re);
    返回结果：
    {
        "code": 0,
        "data": {
            "file_name": "15208345334323.jpg",
            "file_name_old": "3.jpg",
            "file_path": "xinxin\/chat\/2018_03_12\/15208345334323.jpg",
            "file_fileext": "jpg",
            "file_size": 11184,
            "file_path_thumb": []
        },
        "msg": ""
    }
    

    # 2. 生成缩略图
    
    $oss = new oss();
    $data_img = [];
    $data_img['upfile_name'] = 'upfile';//文件上传标签名称
    $data_img['upfile_path'] = 'chat';//存放文件夹
    $data_img['thumb_config'] = '1200_600|600_300';//缩略图（可选）
    $re = $oss->uploadFile($data_img);
    echo json_encode($re);
    返回结果：
    {
        "code": 0,
        "data": {
            "file_name": "15208344266889.jpg",
            "file_name_old": "3.jpg",
            "file_path": "xinxin\/chat\/2018_03_12\/15208344266889.jpg",
            "file_fileext": "jpg",
            "file_size": 11184,
            "file_path_thumb": {
                "1200_600": "xinxin\/chat\/2018_03_12\/15208344266889_1200_600.jpg",
                "600_300": "xinxin\/chat\/2018_03_12\/15208344266889_600_300.jpg"
            }
        },
        "msg": ""
    }
    

    # 3. 生成缩略图并删除原图
    
    $oss = new oss();
    $data_img = [];
    $data_img['upfile_name'] = 'upfile';//文件上传标签名称
    $data_img['upfile_path'] = 'chat';//存放文件夹
    $data_img['thumb_config'] = '1200_600|600_300';//缩略图（可选）
    $data_img['is_cover'] = 1;//是否覆盖原图1覆盖0不覆盖（可选）
    $re = $oss->uploadFile($data_img);
    echo json_encode($re);
    返回结果：
    {
        "code": 0,
        "data": {
            "file_name": "15208346106979_1200_600.jpg",
            "file_name_old": "9.jpg",
            "file_path": "xinxin\/chat\/2018_03_12\/15208346106979_1200_600.jpg",
            "file_fileext": "jpg",
            "file_size": 17811,
            "file_path_thumb": {
                "1200_600": "xinxin\/chat\/2018_03_12\/15208346106979_1200_600.jpg",
                "600_300": "xinxin\/chat\/2018_03_12\/15208346106979_600_300.jpg"
            }
        },
        "msg": ""
    }
    

实例代码

    # 1. 代码实例
        
    //上传到
    $oss = new oss();
    $data_img = [];
    $data_img['upfile_name'] = 'upfile';//文件上传标签名称
    $data_img['upfile_path'] = 'banner';//存放文件夹
    $data_img['thumb_config'] = '1200_600|600_300';//缩略图（可选）
    $data_img['is_cover'] = 1;//是否覆盖原图1覆盖0不覆盖（可选）
    $re = $oss->uploadFile($data_img);
    echo json_encode($re);

    /**
     * oss上传类
     */
    class oss
    {    

        public function __construct()
        {
            define('UPFILETOKEN','用户token');//用户token
            define('URL_STATIC','http://static.xinxinst.com/');//文件保存地址
            $this->out_data['config']['url_static'] = URL_STATIC;
            $this->out_data['config']['url_oss'] = 'http://oss.xinxinst.com/upload.php';//文件上传地址
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
            //设置为POST
            curl_setopt($ch, CURLOPT_POST, 1);
            //把POST的变量加上
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
            // print_r(UPFILETOKEN);
            // print_r($data);die;
            //生成签名
            $data['sig'] = md5(UPFILETOKEN.serialize($data));
            //初始化上传文件
            $data['upfile'] = new \CURLFile($_FILES[$data['upfile_name']]['tmp_name'],$_FILES[$data['upfile_name']]['type'],$_FILES[$data['upfile_name']]['name']);
            //上传
            $re = $this->curlPost($this->out_data['config']['url_oss'],$data);
            @unlink($_FILES[$data['upfile_name']]['tmp_name']);//删除临时文件
            return json_decode($re,true);
        }        
    }
