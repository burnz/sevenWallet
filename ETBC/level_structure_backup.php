<?php
include_once('./_common.php');
?>
<!doctype html>
<html lang="ko">
<head>
	<?include_once('common_head.php')?>
	<link rel="stylesheet" href="css/level_structure/style.css">

	<script>
		$(function() {

			var menu_dropdown = document.getElementsByClassName('lvl');

			for (var i = 0; i < menu_dropdown.length; i++) {
				menu_dropdown[i].onclick = function() {
					this.classList.toggle('lvl-is-open');

					var menu_item = this.nextElementSibling;

					if (menu_item.style.maxHeight) {
						menu_item.style.maxHeight = null;
					} else {
						menu_item.style.maxHeight = menu_item.scrollHeight + "px";
					}
				}
			}
		});
	</script>
</head>
<body>
	<?include_once('mypage_head.php')?>
	<div id="overlay">
		<div id="text">
			<h2>Your browser is too small.</h2>
			<p>Level structure view works best on browsers that are at least 1235px wide.</p>
		</div>
	</div>

<!-- CONTENT-->
	<div id="content">
<?php
	if (!$is_member)
    goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL."/mypage.php"));


if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/level_structure.php');
    return;
}

// �׸��� mypage.php ������ include
if(defined('G5_THEME_SHOP_PATH')) {
    $theme_mypage_file = G5_THEME_SHOP_PATH.'/mypage.php';
    if(is_file($theme_mypage_file)) {
        include_once($theme_mypage_file);
        return;
        unset($theme_mypage_file);
    }
}

$g5['title'] = $member['mb_name'].'�� ����������';
//include_once('./_head.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

if ($gubun=="B"){
	$class_name     = "g5_member_bclass";
	$recommend_name = "mb_brecommend";
}else{
	$class_name     = "g5_member_class";
	$recommend_name = "mb_recommend";
}

// ����
$cp_count = 0;
$sql = " select cp_id
            from {$g5['g5_shop_coupon_table']}
            where mb_id IN ( '{$member['mb_id']}', '��üȸ��' )
              and cp_start <= '".G5_TIME_YMD."'
              and cp_end >= '".G5_TIME_YMD."' ";
$res = sql_query($sql);

for($k=0; $cp=sql_fetch_array($res); $k++) {
    if(!is_used_coupon($member['mb_id'], $cp['cp_id']))
        $cp_count++;
}

if ($_GET[go]=="Y"){
	goto_url("level_structure.php#org_start");
	exit;
}
?>

<p class="blk" style="height:60px;"></p>

<div class="pk_page">
<style type="text/css">
.pk_page {font-size:14px;}
span.btn,
a.btn {display:inline-block;*display:inline;*zoom:1;height:33px;line-height:33px;padding:0 15px;border-radius:3px;background-color:#e5e5e5;color:#fff;}
.infoBx {border:solid 2px rgba(39,48,62,0.4);border-radius:8px;margin-bottom:30px;}
.infoBx h3 {line-height:40px;font-size:15px;padding-left:20px;border-bottom:solid 1px rgba(0,0,0,0.1);background-color:rgba(39,48,62,0.05);}
.infoBx ul {margin:15px;}
.infoBx ul li {display:inline-block;*display:inline;*zoom:1;width:33%;line-height:40px;font-size:14px;color:#777;border-bottom:solid 1px #fff;}
.infoBx ul li.prc {color:rgba(59,105,178,1);}
.infoBx ul li span {display:inline-block;*display:inline;*zoom:1;color:#000;padding-left:20px;width:100px;background-color:rgba(39,48,62,0.05);margin-right:20px;}

/*��� �ΰ�*/
img.pool {width:40px;vertical-align:middle;margin-right:10px;}

</style>
	
<?
// ************************

include_once('../new/inc.member.class.php');

$sql  = "select count(*) as cnt from g5_member";
$mrow = sql_fetch($sql);

$sql = "select * from g5_member_class_chk where mb_id='".$member[mb_id]."' and  cc_date='".date("Y-m-d",time())."' order by cc_no desc";
$row = sql_fetch($sql);

if ($mrow[cnt]>$row[cc_usr] || !$row[cc_no] || $_GET["reset"]){

	make_habu('');

	$sql = "delete from g5_member_class where mb_id='".$member[mb_id]."'";
	sql_query($sql);

	get_recommend_down($member[mb_id],$member[mb_id],'11');

	$sql  = " select * from g5_member_class where mb_id='{$member[mb_id]}' order by c_class asc";	
	$result = sql_query($sql);
	for ($i=0; $row=sql_fetch_array($result); $i++) { 
		$row2 = sql_fetch("select count(c_class) as cnt from g5_member_class where  mb_id='".$member[mb_id]."' and c_class like '".$row[c_class]."%'");
		$sql = "update g5_member set mb_child='".$row2[cnt]."' where mb_id='".$row[c_id]."'";
		sql_query($sql);
	}

	$sql = "insert into g5_member_class_chk set mb_id='".$member[mb_id]."',cc_date='".date("Y-m-d",time())."',cc_usr='".$mrow[cnt]."'";
	sql_query($sql);

}

$sql = "select * from g5_member_bclass_chk where mb_id='".$member[mb_id]."' and  cc_date='".date("Y-m-d",time())."' order by cc_no desc";
$row = sql_fetch($sql);

if ($mrow[cnt]>$row[cc_usr] || !$row[cc_no] || $_GET["reset"]){

	make_habu('B');

	$sql = "delete from g5_member_bclass where mb_id='".$member[mb_id]."'";
	sql_query($sql);

	get_brecommend_down($member[mb_id],$member[mb_id],'11');

	$sql  = " select * from g5_member_bclass where mb_id='{$member[mb_id]}' order by c_class asc";	
	$result = sql_query($sql);
	for ($i=0; $row=sql_fetch_array($result); $i++) { 
		$row2 = sql_fetch("select count(c_class) as cnt from g5_member_bclass where  mb_id='".$member[mb_id]."' and c_class like '".$row[c_class]."%'");
		$sql = "update g5_member set mb_b_child='".$row2[cnt]."' where mb_id='".$row[c_id]."'";
		sql_query($sql);
	}

	$sql = "insert into g5_member_bclass_chk set mb_id='".$member[mb_id]."',cc_date='".date("Y-m-d",time())."',cc_usr='".$mrow[cnt]."'";
	sql_query($sql);


	if ($_GET["reset"]){
		goto_url("level_structure.php?gubun=".$gubun."&sfl=".$sfl."&stx=".$stx."&gubun=".$gubun);
		exit;		
	}
}
if ($mb_org_num){
	if ($mb_org_num>8) $mb_org_num = 8;
	$sql = "update g5_member set mb_org_num='".$mb_org_num."' where mb_id='".$member[mb_id]."'";
	sql_query($sql);	
	$member[mb_org_num] = $mb_org_num;
}


?>


<style type="text/css">
	.btn_menu {padding:5px;border:1px solid #ced9de;background:rgb(246,249,250);cursor:pointer}
</style>
<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet" />
<link rel="stylesheet" href="/js/zTreeStyle.css" type="text/css">
<script type="text/javascript" src="/js/jquery.ztree.core-3.5.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
<script>
    $.datepicker.regional["ko"] = {
        closeText: "close",
        prevText: "������",
        nextText: "������",
        currentText: "����",
        monthNames: ["1��(JAN)","2��(FEB)","3��(MAR)","4��(APR)","5��(MAY)","6��(JUN)", "7��(JUL)","8��(AUG)","9��(SEP)","10��(OCT)","11��(NOV)","12��(DEC)"],
        monthNamesShort: ["1��","2��","3��","4��","5��","6��", "7��","8��","9��","10��","11��","12��"],
        dayNames: ["��","��","ȭ","��","��","��","��"],
        dayNamesShort: ["��","��","ȭ","��","��","��","��"],
        dayNamesMin: ["��","��","ȭ","��","��","��","��"],
        weekHeader: "Wk",
        dateFormat: "yymmdd",
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: ""
    };
	$.datepicker.setDefaults($.datepicker.regional["ko"]);

</script>



<div style="padding:0px 0px 0px 10px;display:none;">
	<a name="org_start"></a>
	<div style="float:left">
	<input type="button" class="btn_menu" value="�˻��޴��ݱ�" onclick="btn_menu2()">
	<input type="button" class="btn_menu" value="��ü ����������" onclick="location.href='level_structure.php?go=Y'">
	<input type="button" class="btn_menu" style="background:#fadfca" value="������ �μ�" onclick="btn_print()">
	</div>
	<div style="float:right;padding-right:10px">
	<input type="button" class="btn_menu" value="������ �籸��" onclick="btn_org()">
	</div>
</div>
<div style="padding-top:10px;clear:both"></div>
<div id="div_left" style="width:15%;float:left;min-height:670px;display:none">


<?
if ($now_id){
	$go_id = $now_id;
}else{
	$go_id = $member[mb_id];
}
if (!$fr_date) $fr_date = Date("Y-m-d", time()-60*60*24*365);
if (!$to_date) $to_date = Date("Y-m-d", time());
?>

	<div style="margin-left:10px;padding:5px 5px 5px 5px;border:1px solid #d9d9d9;height:683px">
		<form name="sForm2" id="sForm2" method="get" action="level_structure.php">
		<input type="hidden" name="now_id" id="now_id" value="<?=$now_id?>">
		<table>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px">
				<div style="float:left">
				<b>ǥ���ο�</b>
				</div>
				<div style="float:right">
				<input type="text" id="mb_org_num"  name="mb_org_num" value="<?php echo $member[mb_org_num]; ?>" class="frm_input" style="width:40px;text-align:center" size="3" maxlength="3"> �ܰ� &nbsp;
				</div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="20" style="padding:10px 10px 10px 10px">
				<input type="radio" id="gubun" name="gubun" onclick="document.sForm2.submit();" value=""<?if ($gubun=="") echo " checked"?>> ��õ�� <br>
				<input type="radio" id="gubun" name="gubun" onclick="document.sForm2.submit();" value="B"<?if ($gubun=="B") echo " checked"?>> ���̳ʸ�����

				</td>
			</tr>

			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px"><b>�ֹ��Ⱓ</b></td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding:10px 10px 10px 10px" align=center>
				<input type="text" id="fr_date"  name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" style="width:100%" size="10" maxlength="10"> ~
				<input type="text" id="to_date"  name="to_date" value="<?php echo $to_date; ?>" class="frm_input" style="width:100%" size="10" maxlength="10">

				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" align="center">
				<input type="submit"  class="btn_submit" style="padding:5px" value="�� ��">
				</td>
			</tr>
		</table>
		</form>
		<div id="div_member"></div>

		<form name="sForm" id="sForm" method="post" style="padding-top:10px" onsubmit="return false;">
		<input type="hidden" name="gubun" value="<?=$gubun?>">
		<table>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding-left:10px"><b>ȸ���˻�</b></td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" style="padding:10px 10px 10px 10px">
				
				<select name="sfl" id="sfl">
				    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>ȸ�����̵�</option>
					<option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>�̸�</option>
					</select>
				<div style="padding-top:5px">
				<label for="stx" class="sound_only">�˻���<strong class="sound_only"> �ʼ�</strong></label>
				<input type="text" name="stx" value="<?php echo $stx ?>" id="stx"  class="required frm_input" style="width:100%;" onkeypress="event.keyCode==13?btn_search():''">
				</div>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f2f5f9" height="30" align="center">
				<input type="button" onclick="btn_search();" class="btn_submit" style="padding:5px" value="�� ��">
				</td>
			</tr>
		</table>
		</form>

		<div id="div_result" style="margin-top:5px;overflow-y: auto;height:418px">

		</div>
	</div>
</div>
<?
if ($member[mb_org_num]){
	$max_org_num = $member[mb_org_num];
}else{
	$max_org_num = 4;
}
$sql       = "select c.c_id,c.c_class from g5_member m join ".$class_name." c on m.mb_id=c.mb_id where c.mb_id='{$member[mb_id]}' and c.c_id='$go_id'";
$srow      = sql_fetch($sql);
$my_depth  = strlen($srow['c_class']);
$max_depth = ($my_depth+($max_org_num*2));

?>





<div id="div_right" style="width:80%;float:right;min-height:500px;margin-top:50px;">
<!-- Ÿ��Ʋ -->
<div>
<span style="font-weight:300;font-size:32px;color:rgb(124,124,124);letter-spacing:1px;">Level Structure</span>
<span style="font-weight:100;font-size:16px;color:rgb(124,124,124);margin-left:10px;">( 999 Total )<!-- ��ȣ ��Ż�� --></span>
</div>
		<div class="zTreeDemoBackground left" style="min-height:573px;margin:0px 10px 0px 10px;">
			<ul id="treeDemo" class="ztree"></ul>
			
		</div>

		<SCRIPT type="text/javascript">
			
			var setting = {
					view: {
						nameIsHTML: true
						},
					data: {
						simpleData: {
							enable: true
					}	
					}		
			};
			var zNodes =[];
		<?



		$sql = "select c.c_id,c.c_class,(select mb_level from g5_member where mb_id=c.c_id) as mb_level,(select pool_level from g5_member where mb_id=c.c_id) as pool_level,(select mb_name from g5_member where mb_id=c.c_id) as c_name,(select count(*) from g5_member where mb_recommend=c.c_id) as c_child,(select mb_b_child from g5_member where mb_id=c.c_id) as b_child,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='L' limit 1) as b_recomm,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='R' limit 1) as b_recomm2,(select count(mb_no) from g5_member where ".$recommend_name."=c.c_id and mb_leave_date = '') as m_child, (select it_pool1 from g5_member where mb_id=c.c_id) as it_pool1, (select it_pool2 from g5_member where mb_id=c.c_id) as it_pool2, (select it_pool3 from g5_member where mb_id=c.c_id) as it_pool3, (select it_pool4 from g5_member where mb_id=c.c_id) as it_pool4, (select it_GPU from g5_member where mb_id=c.c_id) as it_GPU from g5_member m join ".$class_name." c on m.mb_id=c.mb_id where c.mb_id='{$member[mb_id]}' and length(c.c_class)<".$max_depth." order by c.c_class";

		$result = sql_query($sql);

		for ($i=0; $row=sql_fetch_array($result); $i++) {
			if (strlen($row[c_class])==2){
				$parent_id = 0;
			}else{
				$parent_id = substr($row[c_class],0,strlen($row[c_class])-2);
			}

			if ($order_proc==1){
				$sql  = "select today as tpv from ".$ngubun."today where mb_id='".$row[c_id]."'";
				$row2 = sql_fetch($sql);

				$sql  = "select noo as tpv from ".$ngubun."noo where mb_id='".$row[c_id]."'";
				$row3 = sql_fetch($sql);

				$sql  = "select thirty as tpv from ".$ngubun."thirty where mb_id='".$row[c_id]."'";
				$row5 = sql_fetch($sql);
			}else{
		
				$sql  = "select sum(od_receipt_price) as tprice,sum(pv) as tpv from g5_shop_order where mb_id='".$row[c_id]."' and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
				$row2 = sql_fetch($sql);

				$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id in (select c_id from ".$class_name." where mb_id='".$go_id."' and c_class like '".$row[c_class]."%') and od_receipt_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
				$row3 = sql_fetch($sql);
				// and c_id<>'".$row[c_id]."'

				//���� 30��
				$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id in (select c_id from ".$class_name." where mb_id='".$go_id."' and c_class like '".$row[c_class]."%') and od_receipt_time between '".Date("Y-m-d",time()-(60*60*24*30))." 00:00:00' and '".Date("Y-m-d",time())." 23:59:59'";
				$row5 = sql_fetch($sql);
				// and c_id<>'".$row[c_id]."'
			}

			//���̳ʸ� ���� ���� ����
			if ($row[b_recomm]){
				$sql  = "select (mb_my_sales+habu_day_sales) as tpv from g5_member where mb_id ='".$row[b_recomm]."' and sales_day='".date("Y-m-d")."'";
				//$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id ='".$row[b_recomm]."' and od_receipt_time between '".Date("Y-m-d",time())." 00:00:00' and '".Date("Y-m-d",time())." 23:59:59'";
				$row6 = sql_fetch($sql);

				$sql  = "select ".$order_field." as tpv from iwol where mb_id ='".$row[b_recomm]."'";
				$row8 = sql_fetch($sql);

				$row6['tpv'] += $row8['tpv'];
			}else{
				$row6['tpv'] = 0;
			}

			//���̳ʸ� ������ ���� ����
			if ($row[b_recomm2]){
				$sql  = "select (mb_my_sales+habu_day_sales) as tpv from g5_member where mb_id ='".$row[b_recomm2]."' and sales_day='".date("Y-m-d")."'";
				//$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id ='".$row[b_recomm2]."' and od_receipt_time between '".Date("Y-m-d",time())." 00:00:00' and '".Date("Y-m-d",time())." 23:59:59'";
				$row7 = sql_fetch($sql);

				$sql  = "select ".$order_field." as tpv from iwol where mb_id ='".$row[b_recomm2]."'";
				$row9 = sql_fetch($sql);
				$row7['tpv'] += $row9['tpv'];
			}else{
				$row7['tpv'] = 0;
			}

			if (!$row['b_child']) $row['b_child']=1;
			//if (!$row['c_child']) $row['c_child']=1;
			$name_line = "<img src='/img/".$row[mb_level]. ".png' class='pool' /> "; //���� ��� �̹��� 					
			$name_line .= $row[c_name]."[ ".$row[c_id]." ]";       

			$name_line .= "<div class='info'><span class='info_tit'>Enrolled : </span> <span class='blue info_in'>[]</span><span class='info_tit'>Sponsor : </span><span class='blue info_in'>[<?php echo $member[mb_recommend]?>]</span><span class='info_tit'>Placement : </span> <span class='blue info_in'>[]</span><span class='info_tit'>Placed in Binary : </span><span class='blue info_in'>[]</span><span class='info_tit'>Total Sales : </span> <span class='blue info_in'>[]</span><span class='info_tit'>30-Day Sales : </span><span class='blue info_in'>[]</span><span class='info_tit'>Personal Sales : </span><span class='blue info_in'>[]</span></div> ";


			

		?> 

			zNodes.push({ id:"<?=$row[c_class]?>", pId:"<?=$parent_id?>", name:"<?=$name_line?>", open:true, click:false});

		<?
		}
		?>


			$(document).ready(function(){
				$.fn.zTree.init($("#treeDemo"), setting, zNodes);
			});

			
		</SCRIPT>
</div>
<style>
.info{float:right;padding:10px;}
.info .info_tit{font-size:14px;font-weight:600;color:#000;margin-left:10px;}
.info .info_in{font-size:14px;font-weight:200;color:#0079d3;}
</style>



<style type="text/css">
.ztree li a:hover {text-decoration:none; background-color: #e5e5e5;}
</style>




<script type="text/javascript">
/*
$(document).ready(function(){
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	<?if ($stx && $sfl){?>
		btn_search();
	<?}?>
});
function btn_print(){

	var html = $('#treeDemo');

	var strHtml = '<!doctype html><html lang="ko"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta http-equiv="imagetoolbar" content="no" /><title></title><link rel="stylesheet" type="text/css" media="all" href="/js/zTreeStyle.css"></';
	strHtml += 'head><body style="padding:0px;margin:0px;"><div class="zTreeDemoBackground left"><ul id="treeDemo" class="ztree"><!--body--></ul></div></body></html>';
	var strContent = html.html();
	var objWindow = window.open('', 'print', 'width=640, height=800, resizable=yes, scrollbars=yes, left=0, top=0');
	if(objWindow)
	{
		 var strSource = strHtml;
		 strSource  = strSource.replace(/\<\!\-\-body\-\-\>/gi, strContent);

		 objWindow.document.open();
		 objWindow.document.write(strSource);
		 objWindow.document.close();

		 setTimeout(function(){ objWindow.print(); }, 500);
	}
}

function btn_menu2(){
	if($("#div_left").css("display") == "none"){ 
		$("#div_left").show();
		$("#div_right").css("width","100%");
	} else { 
		$("#div_left").hide(); 
		$("#div_right").css("width","100%");
	} 
}

function btn_search(){
	if($("#stx").val() == ""){ 
		//alert("�˻�� �Է����ּ���.");
		$("#stx").focus();
	}else{
		$.post("ajax_get_tree_member.php", $("#sForm").serialize(),function(data){
			$("#div_result").html(data);
		});
	}
}
function go_member(go_id){
	$("#now_id").val(go_id);
	$.get("ajax_get_up_member.php?gubun=<?=$gubun?>&go_id="+go_id, function (data) {

		data = $.trim(data);
		temp = data.split("|");

		data2 = "<table style='width:100%'>";
		data2 += "			<tr>";
		data2 += "				<td bgcolor='#f9f9f9' height='30' style='padding-left:10px'><b>���� ȸ��</b></td>";
		data2 += "			</tr>";
		for(i=(temp.length-1);i>=0;i--){
			data2 += temp[i];
		}
		
		data2 += "</table>";

		$('#div_member').html(data2);

		$.get("ajax_get_tree_load.php?gubun=<?=$gubun?>&fr_date=<?=$fr_date?>&to_date=<?=$to_date?>&go_id="+go_id, function (data) {
			$('#div_right').html(data);
		});
	});
}
function btn_org(){
	if (confirm("�������� �籸�� �Ͻðڽ��ϱ�?")){
		location.href="level_structure.php?reset=1&sfl=<?=$sfl?>&stx=<?=$stx?>";
	}
}
*/
</script>


<!-- ���� Ŭ���� �ٵ� ������ ȿ��-->
<script scr="js/script.js"></script>

</div><!-- // pk_page -->












<!-- #######################################   ���������� ���� #######################################{ -->

<div id="smb_my">

    <!-- ȸ������ ���� ���� { -->
   <!-- <section id="smb_my_ov">
        <h2>ȸ������ ����</h2>

        <div id="smb_my_act" style="display:none;">
            <ul>
                <li><a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=member_leave.php" onclick="return member_leave();" class="btn02">ȸ��Ż��</a></li>
            </ul>
        </div>
    </section>
	-->

	
    <!-- } ȸ������ ���� �� -->

</div>

<script>
$(function() {
    $(".win_coupon").click(function() {
        var new_win = window.open($(this).attr("href"), "win_coupon", "left=100,top=100,width=700, height=600, scrollbars=1");
        new_win.focus();
        return false;
    });
});

function member_leave()
{
    return confirm('���� ȸ������ Ż�� �Ͻðڽ��ϱ�?')
}
</script>


<!-- ####################################### } ���������� ��  ####################################### -->
	</div>

<!--
<?php
	include_once('./mypage_footer.php')
?>-->
</html>
