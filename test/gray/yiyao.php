<?php
/**
 * ��ȡ��è��������Ʒ����ŵ�
 */

echo '<pre>';

$url = "http://yao.tmall.com/?spm=a2167.7202441.a2226n0.1.8YUCoN";
$str = HttpGet($url);
 
$str=preg_replace("/\s+/", " ", $str); //���˶���س� 
$str=preg_replace("/<[ ]+/si","<",$str); //����<__("<"�ź�����ո�) 
$str=preg_replace("/<\!--.*?-->/si","",$str); //ע�� 

preg_match_all('/<div class="leftCategory">(.+)<\/div>/isU',$str,$lists);
$category = array();
foreach ($lists[1] as $v) {
	preg_match_all('/<a>(.+)<i.+>.+<\/i><\/a>/isU', $v, $top);
	preg_match_all('/<a href=\"(.*?)\".*?>(.*?)<\/a>/i', $v, $link);
	for ($i=0; $i < count($link[1]); $i++) { 
		$temp = explode('#', $link[1][$i]);
		$category[] = array(
			'name_1' => $top[1][0],
			'name_2' => $link[2][$i],
			'link' => $temp[0],
			);
	}
}
preg_match_all('/<dl class="clearfix">(.+)<\/dl>/isU', $str, $lists);
foreach ($lists[1] as $v) {
	preg_match_all('/<dt><a>(.+)<\/a><\/dt>/isU', $v, $top);
	preg_match_all('/<a href=\"(.*?)\".*?>(.*?)<\/a>/i', $v, $link);
	for ($i=0; $i < count($link[1]); $i++) { 
		$temp = explode('#', $link[1][$i]);
		$category[] = array(
			'name_1' => $top[1][0],
			'name_2' => $link[2][$i],
			'link' => $temp[0],
			);
	}
}

//ץȡ���������Ʒ��Ϣ
for($i = 0; $i < count($category); $i++){
	$baseurl = $category[$i]['link'];
	$the_page = 0;
	$tootal_page = 0;
	do{
		$wares = array();//������Ʒ��Ϣ
echo "The_Page:".$the_page."\n";
echo "Tootal_Page:".$tootal_page."\n";
echo "I:".$i."\n";
		$url = $baseurl.'&s='.($the_page*60);
echo "URL:".$url."\n";
		$str = HttpGet($url);

		$str=preg_replace("/\s+/", " ", $str); //���˶���س� 
		$str=preg_replace("/<[ ]+/si","<",$str); //����<__("<"�ź�����ո�) 
		$str=preg_replace("/<\!--.*?-->/si","",$str); //ע�� 

		//��ȡ��ҳ��Ϣ
		preg_match_all('/<p class="ui-page-s">(.+?)<\/p>/i', $str, $pages);

		if(!empty($pages[1][0])){
			preg_match_all('/<b class="ui-page-s-len\">(\d{1,2})\/(\d{1,2})<\/b>/i', $pages[1][0], $page);
			if(!empty($page[1][0])){
				$the_page = $page[1][0];
				$tootal_page = $page[2][0];	
			}else{
				$the_page++;
				continue;
			}	
		}else{
			$the_page++;
			continue;
		}


		//��ȡ�۸���Ϣ
		preg_match_all('/<p\s+class="productPrice\s*?"\s*?>\s<em.+>.+([0-9]{1,2}\.[0-9]{1,2}).+<\/em>(.+)<\/p>/isU', $str, $prices);
		//�жϴ�����Ƿ��пɳ��������
		if(empty($prices)){
			echo $category[$i]['name_1']." >> ".$category['name_2']." û�пɳ�ȡ������\nURL:".$category['link']."\n";
			continue;
		}

		foreach ($prices[2] as $k => $v) {
			preg_match('/(\d{2}\.\d{2})/', $v, $temp);
			if (!empty($temp)) {
				$prices[2][$k] = $temp[1];
			}
		}

		preg_match_all('/<p\s+class="productTitle\s*?"\s*?>(.+)<\/p>/isU', $str, $titles);
		foreach ($titles[1] as $k => $v) {
			preg_match('/<a href=\"(.*?)\".*?>(.*?)<\/a>/i', $v, $temp);
			preg_match('/\?id=(\d+)\&/i', $v, $arr_id);
			$temp[] = $arr_id[1];
			$wares[] = $temp;
		}

		for($j = 0; $j < count($wares); $j++){
			$wares[$j]['price'] = $prices[1][$j];
			$wares[$j]['one_price'] = $prices[2][$j];
		}

		echo $category[$i]['name_1']." >> ".$category[$i]['name_2']." ��".$the_page."ҳ ��ȡ��".count($wares)."������\n";

		//�������ݿ�
		insert_db($wares, $category[$i]);

	}while($the_page <= $tootal_page);


}

/**
 * �������ݿ�
 * @param  [type] $data [description]
 * @return [type] $inser_num [���سɹ����������]
 */
function insert_db($data, $category) {
	echo "��ʼд�����ݿ�\n";
    $insert_num = 0;//�ɹ����������
    $con = new mysqli("localhost","root","", "collection");
    if (!$con){
        die('�������ݿ�ʧ��:' . $con->error() . "\n\n");
    }
    $con->query('SET NAMES UTF8');
    foreach ($data as $v) { 
        $sql = 'INSERT INTO yiyao_2 (skuId, title, url, price, one_price, category_1, category_2) VALUES ("'.$v['3'].'", "'.characet($v['2']).'", "'.$v['1'].'", "'.$v['price'].'", "'.$v['one_price'].'", "'.characet($category['name_1']).'", "'.characet($category['name_2']).'")';
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