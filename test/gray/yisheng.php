<?php
/**
 * ��ȡ��������
 */

echo '<pre>';

$url = "http://yeecare.tmall.com/category-884642718.htm?spm=a1z10.5.w4010-6232518504.8.qP6g84&search=y&parentCatId=884642717&parentCatName=%F7%C8%C1%A6%C4%D0%C8%CB&catName=%B8%C4%C9%C6%CA%D3%C1%A6#bd";
$str = HttpGet($url);
 
$str=preg_replace("/\s+/", " ", $str); //���˶���س� 
$str=preg_replace("/<[ ]+/si","<",$str); //����<__("<"�ź�����ո�) 
$str=preg_replace("/<\!--.*?-->/si","",$str); //ע�� 

// print_r($str);die;

preg_match_all('/<li class="cat fst-cat\s?">\s?<h4.+>(.+)<\/h4>\s?<ul.+>(.+)<\/ul>\s?<\/li>/isU',$str,$lists);
unset($lists[1][0]);
unset($lists[2][0]);
$temp[] = $lists[1];
$temp[] = $lists[2];
// var_dump($temp);die; 
$category_1 = array();
foreach ($temp[0] as $k => $v) {
	// var_dump($v);die;
	preg_match_all('/<a.+href=\"(.*?)\".*?>(.*?)<\/a>/i', $v, $category_1[]);
	// var_dump($category_1);die;
}
// var_dump($category_1);
foreach ($temp[1] as $k => $v) {
	// var_dump($v);
	preg_match_all('/<a.*?href=\"(.*?)\".*?>(.*?)<\/a>/i', $v, $temp_1);
	for($i = 0; $i < count($temp_1[1]); $i++){
		$category_2[($k-1)][] = array(
			'name' => $temp_1[2][$i],
			'link' => $temp_1[1][$i]
			);
	}
}
// var_dump($category_2);
// var_dump($category_1);die;
foreach ($category_1 as $k => $v) {
	$category[$k] = array(
		'fname' => $v[2][0],
		'flink' => $v[1][0],
		'children' => $category_2[$k],
	);
}
// var_dump($category);die;

foreach ($category as $k => $v) {
	for ($i=0; $i < count($v['children']); $i++) { 
		$url = $v['children'][$i]['link'];
		$str = HttpGet($url);
// print_r($url);
		$str=preg_replace("/\s+/", " ", $str); //���˶���س� 
		$str=preg_replace("/<[ ]+/si","<",$str); //����<__("<"�ź�����ո�) 
		$str=preg_replace("/<\!--.*?-->/si","",$str); //ע�� 		

		preg_match_all('/<span class=\"c-price\">(\d+.\d+)/', $str, $price);//��ȡ�۸�
		preg_match_all('/<a class="item-name".*?>(.*?)<\/a>/', $str, $name);//����
		preg_match_all('/<span class="sale-num">(\d+)<\/span>/', $str, $sale_num);//����
		preg_match_all('/<a\sclass="item-name".*?href=\"http:\/\/detail.tmall.com\/item.htm\?id=(\d+)&/', $str, $skuId);//��ƷID
		preg_match_all('/<a\sclass="item-name".*?href=\"(.*?)\".*?>.*?<\/a>/', $str, $url);//url

		$data = array();
		for ($j=0; $j < count($name[1]); $j++) { 
			$data[] = array(
				'name' => $name[1][$j],
				'price' => $price[1][$j],
				'skuId' => $skuId[1][$j],
				'url' => $url[1][$j],
				'category_1' => $category[$k]['fname'],
				'category_2' => $category[$k]['children'][$i]['name'],
				);	
		}
		// var_dump($data);die;

		insert_db($data);


//		die;
	}
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
        $sql = 'INSERT INTO yisheng (name, price, skuId, url, category_1, category_2) VALUES ("'.characet($v['name']).'", "'.$v['price'].'", "'.$v['skuId'].'", "'.$v['url'].'", "'.characet($v['category_1']).'", "'.characet($v['category_2']).'")';
        $res = $con->query($sql);

		//echo iconv("UTF-8","gb2312",$sql)."\n";
        
		if(!$res){
            echo "����ʧ�ܣ���Ʒ��ַ��".$v[1]."\n"; 
            echo iconv("UTF-8","gb2312",$sql)."\n";
        }else{
			echo ".";
            $insert_num++;
        }
    }
	echo "\n";
	echo "�ɹ�д��".$insert_num."������\n";
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
    $timeout = 10;  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //���ؽ��
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); //���ó�ʱʱ��
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //�����ض���
    curl_setopt($ch, CURLOPT_HEADER, 0);//�Ƿ����ͷ��Ϣ 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //����SSL
    $file_contents = curl_exec($ch);  
    echo curl_error($ch);
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
      $data = mb_convert_encoding($data ,'utf-8' , $fileType);
    }
  }
  return $data;
}

/**
 * ������Ѻõı������
 * @param mixed $var ����
 * @param boolean $echo �Ƿ���� Ĭ��ΪTrue ���Ϊfalse �򷵻�����ַ���
 * @param string $label ��ǩ Ĭ��Ϊ��
 * @param boolean $strict �Ƿ��Ͻ� Ĭ��Ϊtrue
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}