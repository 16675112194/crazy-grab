<?php
/**
 * ��ȡ��è��������Ʒ����ŵ�
 */
$page = 1;//��ǰҳ��;
$totalPage = 0;//��ҳ��;
do {
    $s = ($page - 1) * 95;
    $url = "http://list.taobao.com/itemlist/market/food2011.htm?_input_charset=utf-8&json=on&atype=b&cat=50008825&s=".$s."&style=grid&as=0&viewIndex=1&spm=a2106.2206569.0.0.M8WiNf&same_info=1&isnew=2&pSize=95&_ksTS=1405235037780_20";
    //��ȡjson����
    $json = HttpGet($url);
    //�����������
    $utf8_json = characet($json);
    //json����Ϊ����
    $arr = json_decode($utf8_json, true);
    //��ȡҳ��
    $page = $arr['page']['currentPage'];
    //��ȡ��ҳ��
    $totalPage = $arr['page']['totalPage'];
    //��ȡ��Ʒ�б�
    $lists = $arr['itemList'];
    //�����ܹ���Ʒ��
    $count = count($lists);
    //���ն������Ϣ
    echo "��ȡ....��".$page."ҳ,��".$count."����Ϣ\n";
    //��ʼ�������ݿ�
    $insert_num = insert_db($lists);
    //���ն������Ϣ
    echo "\n";
    echo "�ɹ�".$insert_num."�� ʧ��".($count-$insert_num)."��\n\n";    
    $page++;
} while ( $page <= $totalPage);




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
        $sql = 'INSERT INTO tao (title, tip, price, currentPrice, unitPrice, unit, loc, href) VALUES ("'.$v["title"].'", "'.$v['tip'].'", "'.$v['price'].'", "'.$v['currentPrice'].'", "'.$v['unitPrice'].'", "'.$v['unit'].'", "'.$v['loc'].'", "'.$v['href'].'")';
        $res = $con->query($sql);
        if(!$res){
            echo "����ʧ�ܣ���Ʒ��ַ��".$v['href']."\n"; 
            echo iconv("UTF-8","gb2312",$sql)."\n";
        }else{
			echo ".";
            $insert_num++;
        }
    }
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
    $timeout = 5;  
    curl_setopt ($ch, CURLOPT_URL, $url);  
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
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