<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">   
<title>zatree图形列表</title>
<script type="text/javascript" src="static/jquery-2.0.3.min.js"></script>
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="static/jquery.fancybox.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox();
	});
</script>
<link href="static/page.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="static/jquery.fancybox.min.css" type="text/css" media="screen" />
</head>
<body style="text-align:center;" >

<?php
date_default_timezone_set( 'PRC' );
include_once("page.class.php");
include_once("zabbix_config.php");
include_once("ZabbixApi.class.php");
include_once dirname(dirname(__FILE__)) . '/include/items.inc.php';

$hostid = (isset($_REQUEST["hostid"]) && $_REQUEST["hostid"] > 0) ? $_REQUEST["hostid"] : '';//主机id
$group_class = (isset($_REQUEST["group_class"]) && $_REQUEST["group_class"] != '') ? $_REQUEST["group_class"] : '';//分组
$page=(isset($_REQUEST["page"]) && $_REQUEST["page"]!='')? $_REQUEST["page"]:1;
$url = 'graph.php?' . ($hostid > 0 ? 'hostid=' . $hostid : 'group_class=' . $group_class);
$curtime = time();//当前时间

/*条件不从cookie里面获取*/
if (!isset($_GET["itemkey"]) && !isset($_GET["stime"]) && !isset($_GET["endtime"]) && 
    !isset($_GET["order_key"]) && !isset($_GET["order_type"])) {
	if (isset($_COOKIE['stime'])) {
		$stime=$_COOKIE['stime'];
		$endtime=$_COOKIE['endtime'];
		$itemkey=isset($_COOKIE['itemkey']) ? $_COOKIE['itemkey'] : '' ;
		$orderkey=isset($_COOKIE['order_key']) ? $_COOKIE['order_key'] : '' ;
		$ordertype=isset($_COOKIE['order_type']) ? $_COOKIE['order_type'] : '' ;
	} else {
		$itemkey=isset($_GET["itemkey"]) ? $_GET["itemkey"] : '';
		$stime=(isset($_GET["stime"])&& $_GET["stime"]!='' )? $_GET["stime"] : date("Y-m-d H:i:s",$curtime-3600*24);
		$endtime=(isset($_GET["endtime"])&& $_GET["endtime"]!='' )? $_GET["endtime"] : date("Y-m-d H:i:s",$curtime);
		$orderkey=(isset($_GET["order_key"]) && $_GET["order_key"]!='')? $_GET["order_key"] :'';
	$ordertype=(isset($_GET["order_type"]) && $_GET["order_type"]!='')? $_GET["order_type"] :'';
		//然后把数据存入到cookie里面
		SetCookie("itemkey", $itemkey, $curtime+3600);
		SetCookie("stime", $stime, $curtime+3600);
		SetCookie("endtime", $endtime, $curtime+3600);
		SetCookie("order_key", $orderkey, $curtime+3600);
		SetCookie("order_type", $ordertype,$curtime+3600);
	}
} else {
	$itemkey=isset($_GET["itemkey"]) ? $_GET["itemkey"] : '';
	$stime=(isset($_GET["stime"])&& $_GET["stime"]!='' )? $_GET["stime"] :  date("Y-m-d H:i:s",$curtime-3600*24);
	$endtime=(isset($_GET["endtime"])&& $_GET["endtime"]!='' )? $_GET["endtime"] : date("Y-m-d H:i:s",$curtime);
	$orderkey=(isset($_GET["order_key"]) && $_GET["order_key"]!='')? $_GET["order_key"] :'';
    $ordertype=(isset($_GET["order_type"]) && $_GET["order_type"]!='')? $_GET["order_type"] :'';
    //然后把数据存入到cookie里面
	SetCookie("itemkey", $itemkey, $curtime+3600);
	SetCookie("stime", $stime, $curtime+3600);
	SetCookie("endtime", $endtime, $curtime+3600);
	SetCookie("order_key", $orderkey, $curtime+3600);
	SetCookie("order_type", $ordertype, $curtime+3600);
}


$period=strtotime($endtime)-strtotime($stime);
$fortime=date("YmdHis",strtotime($stime));
$width=286;//图形宽度
global $zabbix_api_config;

$port= $_SERVER['SERVER_PORT'];
if($port !=80){
	$url_http=dirname(dirname('http://'.$_SERVER['SERVER_NAME'].':'.$port.$_SERVER["REQUEST_URI"]));
}else{
	$url_http=dirname(dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]));
}

$zabbixApi = new ZabbixApi($url_http.'/'.trim($zabbix_api_config['api_url']),trim($zabbix_api_config['user']),trim($zabbix_api_config['passowrd']));
?>
<form method="get" style="font-size:8px;text-align:left;padding-left:10px;" id="searchForm" >
<input type="hidden" name="hostid" value="<?php echo $hostid;?>"/>
<input type="hidden" name="group_class" value="<?php echo $group_class;?>"/>
开始时间：<input type="text"   value="<?php echo $stime;?>"  width="100px;" class="Wdate" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"  id="stime"  name="stime"/> &nbsp;
结束时间：<input type="text" id="endtime"  value="<?php echo $endtime;?>" width="100px;" class="Wdate" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"  name="endtime"/>&nbsp;
关键字:<input type="text" style="width:130px;" id="itemkey" name="itemkey" value="<?php echo $itemkey;?>" />&nbsp;
排序：<select id="order_key" name="order_key">
<option value=''>默认</option>
<option value="lastvalue" <?php if($orderkey=='lastvalue'){ echo 'selected="selected"';};?>>最新</option>
<option value="max" <?php if($orderkey=='max'){ echo 'selected="selected"';};?>>最大</option>
<option value="min" <?php if($orderkey=='min'){ echo 'selected="selected"';};?>>最小</option>
<option value="avg" <?php if($orderkey=='avg'){ echo 'selected="selected"';};?>>平均</option>
</select>&nbsp;
<select id="order_type" name="order_type">
<option value=''>默认</option>
<option value="asc" <?php if($ordertype=='asc'){echo 'selected="selected"';};?>>升序</option>
<option value="desc" <?php if($ordertype=='desc'){echo 'selected="selected"';};?>>降序</option>
</select>
&nbsp;<input type="button" value="搜索" onclick="onCheckSubmit();"/>
&nbsp;<input type="button" value="清除" onclick="clearCookie();"/>
<input type ="button" onclick="javascript:window.parent.location.href='<?php echo "http://".$_SERVER ['HTTP_HOST']; ?>'" value="回到首頁" />
</form>

<p></p>
<?php
$order_list_result=array(); //记录结果信息数组
$order_list_result_page=array();
if ((isset($_REQUEST["hostid"]) && $_REQUEST["hostid"] > 0) || ($group_class != '' && $group_class>0)) {
	//根据主机id查询当前主机下的图形
	if ($group_class == '') {
		$graphs=$zabbixApi->graphGet(array("hostids"=>array($_REQUEST["hostid"]),"output"=>"extend","sortfield"=>"name"));
		foreach($graphs as &$each){
			$graphids[]=$each->graphid;
		}
		$items_list=$zabbixApi->graphitemGet(array("graphids"=>$graphids,"output"=>"extend")); 
	} else {
		//查询分组里面的所有机器
		$host_ids=array();
		$hosts=$zabbixApi->hostGet(array("output"=>"extend","monitored_hosts"=>true,"groupids"=>array($group_class)));
		foreach($hosts as $each_host){
			$host_ids[]=$each_host->hostid;
		}
		//查询分组下的所有机器的所有图形
		$graphs=$zabbixApi->graphGet(array("hostids"=>$host_ids,"output"=>"extend","sortfield"=>"name"));	
		foreach($graphs as &$each){
			$graphids[]=$each->graphid;
		}
		$items_list=$zabbixApi->graphitemGet(array("graphids"=>$graphids,"output"=>"extend")); 
	}

	$list=array('list_item'=>$items_list,'parame'=>array('stime'=>strtotime($stime),'period'=>$period,'sizeX'=>$width,'item_name_search'=>$itemkey));
	$format_list=$zabbixApi->getItemListFormat($list,'');
	foreach ($format_list as &$format){
	$format=(array)$format;
	}
	$order_list_result=(array)$format_list;
	//对结果进行排序
	if($orderkey!='' && $ordertype!=''){
	if($orderkey=='avg'){
	$arr = array_map(create_function('$sort', 'return $sort["avg"];'), $order_list_result);  
		}elseif($orderkey=='min'){
			$arr = array_map(create_function('$sort', 'return $sort["min"];'), $order_list_result); 
		}elseif($orderkey=='max'){
			$arr = array_map(create_function('$sort', 'return $sort["max"];'), $order_list_result); 
		}elseif($orderkey=='lastvalue'){
			$arr = array_map(create_function('$sort', 'return $sort["lastvalue"];'),$order_list_result); 
		}else{
			$arr = array_map(create_function('$sort', 'return $sort["hostname"];'),$order_list_result); 
		}
		if($ordertype=="asc"){
			array_multisort($arr, SORT_ASC, $order_list_result); 
		}else{
			array_multisort($arr, SORT_DESC, $order_list_result); 
		}
	}
	//获取当前页的数据
	if(count($order_list_result) > 0 ){
		$page=new page($order_list_result,array('total'=>count($order_list_result),'url'=>$url,'nowindex'=>$page ));
		$page_link=$page->show(1);
		$order_list_result_page=$page->_get_result();
	}
	if(isset($page_link)){  
?>

<div class="page" ><?php echo $page_link;?></div>
<?php 
}
	//循环输出                                                                       
	foreach($order_list_result_page as $result){
?>

<?php 
	$zoom_x = 3;//点击小图看大图，放大三倍
	$zoom_width = $width * $zoom_x;
	$zoom_height = 70 * $zoom_x;
	$big_graph = "../zabbix_chart.php?graphid=".$result['graphid']."&width=".$zoom_width."&height=".$zoom_height."&stime=".$fortime."&period=".$period."&box=box.jpg";
	$small_graph = "../zabbix_chart.php?graphid=".$result['graphid']."&width=".$width."&height=70&stime=".$fortime."&period=".$period."&box=box.jpg";
?>
<a class="fancybox" rel="group" href=<?php echo $big_graph; ?> >
<img  src="<?php echo $small_graph; ?>" width="357" height="211" style="float:left;padding-top:4px;padding-left:4px;"  /> </a>
<?php
}

}

if(isset($page_link)){  
?>
<p></p>
<div class="page" style="clear:both;padding-top:20px;"><?php echo $page_link;?></div>
<?php 
}
?>

<script type="text/javascript">
function clearCookie() {
	var myDate = new Date();
	var endtime=myDate.Format("yyyy-MM-dd hh:mm:ss");
	myDate.setDate(myDate.getDate()-1);
	var stime=myDate.Format("yyyy-MM-dd") + " " + myDate.Format("hh:mm:ss");
	$("#stime").attr("value",stime);
	$("#endtime").attr("value",endtime);
	$("#itemkey").attr("value",'');
	$("#order_key").attr("value",'');
	$("#order_type").attr("value",'');
	var url="zabbix_ajax.php";
	$.ajax({
		type : "POST",
			url : url,
			data : {
				'clearstatus':1
			},
			success : function(result) {
				window.parent.frames["rightFrame"].location.reload(); 
			}
	});
}

function onCheckSubmit(){
	var orderkey= $("select[name='order_key']").val();
	var ordertype=$("select[name='order_type']").val();
	var itemkey=$("#itemkey").val();

	if((orderkey == '' && ordertype !='') || (orderkey != '' && ordertype =='')){
		alert('排序对象、排序方式要同时选择或同时默认');
		return false;
	}

	$("#searchForm").attr("action",'graph.php');
	$("#searchForm").submit();
}

$(document).ready(function(){
	/**
	 ** 日期格式化函数
	 */
	Date.prototype.Format = function(fmt) {
		var o = {
			"M+" : this.getMonth()+1,                 //月份
				"d+" : this.getDate(),                    //日
			 "h+" : this.getHours(),                   //小时
			 "m+" : this.getMinutes(),                 //分
			 "s+" : this.getSeconds(),                 //秒
			 "q+" : Math.floor((this.getMonth()+3)/3), //季度
			 "S"  : this.getMilliseconds()             //毫秒
		};
		if(/(y+)/.test(fmt))
			fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
		for(var k in o)
			if(new RegExp("("+ k +")").test(fmt))
				fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
		return fmt;
	}
	})
</script>

<div align="center" style='font-size:12px;'>
<a href="https://github.com/spide4k/zatree" target="_blank">Zatree</a> version 1.0.1, 技术支持：
<a href="http://weibo.com/spider4k" target="_blank">@南非蜘蛛</a>
<a href="http://weibo.com/chinahanna" target="_blank">@hanna</a>
<a href="http://weibo.com/678236656" target="_blank">@lijian</a></br>
感谢<a href="http://www.zabbix.com" target="_blank">zabbix</a>这么牛逼的监控软件,如果你觉得zatree插件对你有帮助,
<a href="http://me.alipay.com/spider4k">点击这里</a>可以对作者进行小额捐款
</div>
</br>
</body>
