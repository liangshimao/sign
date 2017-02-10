<?php

$url = 'http://inside.wot.kongzhong.com/inside/wotinside/signact/sign';
$failArr = [];
$failArr2 = [];
$sleep = 0;
$str = file_get_contents('sign.txt');

$Arr = explode('#',$str);
$total = count($Arr)-1;
foreach ($Arr as $key=>$value){
    if($key == 0){
        continue;
    }
    $val = substr($value,0, strpos($value,'----'));

    $param = '?jsonpcallback=jQuery333344&useraccount=1801012411&marks=inside&login='.base64_encode(utf16_to_utf8($val)).'&zoneid=1500200&_='.time()*1000;
    $result = get($url.$param);
    if(isset($result->state) && $result->state == '1'){
        echo '第'.$key.'条签到成功,还剩余'.($total-$key).'个!';
    }elseif(isset($result->state) && $result->state == '0'){
        array_push($failArr,$val);
        echo '第'.$key.'条签到失败,已经添加到失败列表!,还剩余'.($total-$key).'个!';
    }else{
        echo '未知错误!';
    }
    echo "\n";
    sleep($sleep);
}

$failTotal = count($failArr);
echo "签到已经完成!其中有{$failTotal}条数据签到失败!,将进行补签!\n";

foreach ($failArr as $k=>$v){

    $param = '?jsonpcallback=jQuery333344&useraccount=1801012411&marks=inside&login='.base64_encode(utf16_to_utf8($v)).'&zoneid=1500200&_='.time()*1000;
    $result = get($url.$param);
    if(isset($result->state) && $result->state == '1'){
        echo '补签进行中,第'.($k+1).'条签到成功,还剩余'.($failTotal-$k-1).'个!';
    }elseif(isset($result->state) && $result->state == '0'){
        array_push($failArr2,$v);
        echo '补签进行中,第'.($k+1).'条补签失败,已经添加到失败列表!,还剩余'.($failTotal-$k-1).'个!';
    }else{
        echo '未知错误!';
    }
    echo "\n";
    sleep($sleep);
}

$failTotal2 = count($failArr2);
echo "补签已经完成!其中有{$failTotal2}条数据签到失败!,将进行二次补签!\n";

foreach ($failArr2 as $kk=>$vv){

    $param = '?jsonpcallback=jQuery333344&useraccount=1801012411&marks=inside&login='.base64_encode(utf16_to_utf8($vv)).'&zoneid=1500200&_='.time()*1000;
    $result = get($url.$param);
    if(isset($result->state) && $result->state == '1'){
        echo '二次补签进行中,第'.($kk+1).'条签到成功,还剩余'.($failTotal2-$kk-1).'个!';
    }elseif(isset($result->state) && $result->state == '0'){
        echo '二次补签中,第'.($kk+1).'条二次补签失败,还剩余'.($failTotal-$kk-1).'个!';
    }else{
        echo '未知错误!';
    }
    echo "\n";
    sleep($sleep);
}

function get($url)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_HEADER, false);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    $s1 = strpos($data,'{');
    $s2 = strpos($data,'}');
    $length = $s2-$s1+1;
    $result =substr($data,$s1,$length);

    return json_decode($result);
}

function utf16_to_utf8($str) {
    $c0 = ord($str[0]);
    $c1 = ord($str[1]);

    if ($c0 == 0xFE && $c1 == 0xFF) {
        $be = true;
    } else if ($c0 == 0xFF && $c1 == 0xFE) {
        $be = false;
    } else {
        return $str;
    }

    $str = substr($str, 2);
    $len = strlen($str);
    $dec = '';
    for ($i = 0; $i < $len; $i += 2) {
        $c = ($be) ? ord($str[$i]) << 8 | ord($str[$i + 1]) :
            ord($str[$i + 1]) << 8 | ord($str[$i]);
        if ($c >= 0x0001 && $c <= 0x007F) {
            $dec .= chr($c);
        } else if ($c > 0x07FF) {
            $dec .= chr(0xE0 | (($c >> 12) & 0x0F));
            $dec .= chr(0x80 | (($c >>  6) & 0x3F));
            $dec .= chr(0x80 | (($c >>  0) & 0x3F));
        } else {
            $dec .= chr(0xC0 | (($c >>  6) & 0x1F));
            $dec .= chr(0x80 | (($c >>  0) & 0x3F));
        }
    }
    return $dec;
}

