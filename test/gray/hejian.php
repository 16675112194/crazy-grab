<?php
/**
 * ��ȡ�̽�����Ʒ���ŵ�
 */

echo "<pre>";
$url = "http://www.hejian.com/gallery-137-grid.html";
$baseurl = "http://www.hejian.com/";
$str = HttpGet($url);

$str=preg_replace("/\s+/", " ", $str); //���˶���س� 
$str=preg_replace("/<[ ]+/si","<",$str); //����<__("<"�ź�����ո�) 
$str=preg_replace("/<\!--.*?-->/si","",$str); //ע�� 

// print_r($str);

preg_match_all('/<div class = "nav-list">.*?<\/div>/', $str, $temp_lists);

// print_r($temp_lists);

$category = array();//��Ʒ�ܹ�����

foreach ($temp_lists[0] as $k => $v) {
	preg_match_all('/a-fist">(.*?)</', $v, $temp_cate_1);
	// print_r($temp_cate_1);

	preg_match_all('/<a href="(.*?)".*?>(.*?)<\/a>/', $v, $temp_cate_2);
	// print_r($temp_cate_2);
	
	for ($i=0; $i < count($temp_cate_2[1]); $i++) { 
		$category[] = array(
			'category_1' => $temp_cate_1[1][0],
			'category_2' => $temp_cate_2[2][$i],
			'link' => $baseurl.$temp_cate_2[1][$i],
			);
	}
}

//print_r($category);
$goods = array();
foreach ($category as $k => $v) {

echo iconv("UTF-8","gb2312",$v['category_1'])." >> ".iconv("UTF-8","gb2312",$v['category_2'])."\n";
echo "URL:".$v['link']."\n";

	$url = $v['link'];

	// print_r($url);die;
	$str = HttpGet($url);

	$str=preg_replace("/\s+/", " ", $str); //���˶���س� 
	$str=preg_replace("/<[ ]+/si","<",$str); //����<__("<"�ź�����ո�) 
	$str=preg_replace("/<\!--.*?-->/si","",$str); //ע�� 
// print_r($str);die;
	preg_match_all('/<div class = "list-cp">(.*?)<\/div> <\/div>/', $str, $temp_good_group);

	if(empty($temp_good_group[1][0])){
		echo "δƥ�䵽��Ʒ�����ݣ������˴�\n";
		continue;
	}

	preg_match_all("/<dd class=.?demo-bt.?> <a.*?href=.*?(\d+).*?>(.*?)<\/a>.*?(\d+.\d+).*?(\d+.\d+)/", $temp_good_group[1][0], $temp_goods);

	if(empty($temp_goods[1])){
		echo "Ϊƥ�䵽������Ʒ���ݣ������˴�\n";
		continue;
	}

	// print_r($temp_goods);

	for ($i=0; $i < count($temp_goods[1]); $i++) { 
		$goods[] = array(
			'skuId' => $temp_goods[1][$i],
			'name' => $temp_goods[2][$i],
			'url' => $baseurl.'product-'.$temp_goods[1][$i].'.html',
			'price' => $temp_goods[3][$i],
			'martPrice' => $temp_goods[4][$i],
			'category_1' => $v['category_1'],
			'category_2' => $v['category_2'],
			);		
	}

	//�������ݿ�
	insert_db($goods);

}

/**
 * �������ݿ�
 * @param  [type] $data [description]
 * @return [type] $inser_num [���سɹ����������]
 */
function insert_db($data) {
	echo "��ʼд�����ݿ�\n";
    $insert_num = 0;//�ɹ����������
    $con = new mysqli("localhost","root","", "collection");
    if (!$con){
        die('�������ݿ�ʧ��:' . $con->error() . "\n\n");
    }
    $con->query('SET NAMES UTF8');
    foreach ($data as $v) { 
        $sql = 'INSERT INTO hejian (skuId, name, url, price, martPrice, category_1, category_2) VALUES ("'.$v['skuId'].'", "'.characet($v['name']).'", "'.$v['url'].'", "'.$v['price'].'", "'.$v['martPrice'].'", "'.characet($v['category_1']).'", "'.characet($v['category_2']).'")';
        $res = $con->query($sql);

		if(!$res){
            echo "����ʧ�ܣ���Ʒ��ַ��".$v[1]."\n"; 
            echo iconv("UTF-8","gb2312",$sql)."\n";
        }else{
			echo ".";
            $insert_num++;
        }
    }
	echo "\n";
	echo "�ɹ�����".$insert_num."������\n\n";
    $con->close();
    return $insert_num;
}

/**
 * �������󲢻�ȡ���ص���Ϣ
 * @param $url
 */
function HttpGet($url)
{	
    $ch = curl_init();  
    $timeout = 120;  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //���ؽ��
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); //���ó�ʱʱ��
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //�����ض���
    curl_setopt($ch, CURLOPT_HEADER, 0);//�Ƿ����ͷ��Ϣ 
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');//���ҳ���gzipѹ��
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //����SSL
    $file_contents = curl_exec($ch);  
    curl_close($ch);  
    return $file_contents; 
}

/**
 * �Զ�������תΪUTF-8
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function characet($data){
  if( !empty($data) ){
    $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
    if( $fileType != 'UTF-8'){
      $data = mb_convert_encoding($data ,'utf-8' ,$fileType);
    }
  }
  return $data;
}