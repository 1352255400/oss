<?php
function p($str)
{
    echo '<pre>';
    print_r($str);
}
//上传到
if (isset($_FILES) && !empty($_FILES)) {
    include "Oss.php";
    $oss = new Oss();
    $data_img = [];
    $data_img['is_cover'] = 1;
    $data_img['upfile_name'] = 'upfile';
    $data_img['upfile_path'] = 'demo';
    $data_img['thumb_config'] = '300_150|200_100|100_50';
    $data = $oss->uploadFile($data_img);
    if ($data['code'] == 0) {
        if (isset($data['data']['file_path_thumb']) && !empty($data['data']['file_path_thumb'])) {
            $config = include "config.php";
            $url_static = $config['url_static'];
            foreach ($data['data']['file_path_thumb'] as $k => $v) {
                $img_url = $url_static.$v;
                echo "<p>$img_url</p>";
                echo "<img src='$img_url'>";
            }
        }
    }
}

?>

<form class='form_login' id='form' action="" method="post" name="theForm" enctype="multipart/form-data" >
    <div class="input active">
        <input type="file"  name="upfile" class="text name" />
    </div>
    <input type="submit" value="上传" >
</form>
