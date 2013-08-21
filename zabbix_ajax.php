<?php 
require_once("ZabbixApi.class.php");
require_once("zabbix_config.php");

//clear cookie
if(isset($_POST['clearstatus'])){
	
	SetCookie("itemkey", '', time()-3600);
    SetCookie("stime", '', time()-3600);
    SetCookie("endtime", '', time()-3600);
    SetCookie("order_key", '', time()-3600);
    SetCookie("order_type", '', time()-3600);
    
    return true;
	
}else{
	global $zabbix_api_config;
	
	$url_http=dirname(dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]));
	
    $zabbixApi = new ZabbixApi($url_http.'/'.trim($zabbix_api_config['api_url']),trim($zabbix_api_config['user']),trim($zabbix_api_config['passowrd']));
	$groupid=isset($_GET["groupid"])? $_GET["groupid"]:0;
	
	if($groupid > 0 ){
		//根据分组id查询分组下的机器
		$hosts=$zabbixApi->hostGet(array("output"=>"extend","monitored_hosts"=>true,"groupids"=>array($groupid)));
		foreach($hosts as &$each_host){
			$each_host->target='rightFrame';
			$each_host->groupids=$groupid;
			$each_host->url='graph.php?hostid='.$each_host->hostid;
		}
		echo  json_encode($hosts);
	}else{
		//查询所有的分组列表
		$groups=$zabbixApi->hostgroupGet(array("output"=>"extend","monitored_hosts"=>true));
	    foreach($groups as &$each){
	    	$each->id=$each->groupid;
			$each->isParent=true;
			$each->target='rightFrame';
			$each->url='graph.php?group_class='.$each->groupid;
			
		    //查询下面有多少机器
		    $hosts=$zabbixApi->hostGet(array("output"=>"extend","monitored_hosts"=>true,"groupids"=>array($each->groupid)));	
		    $each->name=$each->name.'('.count($hosts).')';
			
			
		} 
		echo  json_encode($groups);	
	}

}

?>