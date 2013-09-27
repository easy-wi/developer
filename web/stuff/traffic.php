<?php
/**
 * File: traffic.php.
 * Author: Ulrich Block
 * Contact: <ulrich.block@easy-wi.com>
 *
 * This file is part of Easy-WI.
 *
 * Easy-WI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Easy-WI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy-WI.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Diese Datei ist Teil von Easy-WI.
 *
 * Easy-WI ist Freie Software: Sie koennen es unter den Bedingungen
 * der GNU General Public License, wie von der Free Software Foundation,
 * Version 3 der Lizenz oder (nach Ihrer Wahl) jeder spaeteren
 * veroeffentlichten Version, weiterverbreiten und/oder modifizieren.
 *
 * Easy-WI wird in der Hoffnung, dass es nuetzlich sein wird, aber
 * OHNE JEDE GEWAEHELEISTUNG, bereitgestellt; sogar ohne die implizite
 * Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
 * Siehe die GNU General Public License fuer weitere Details.
 *
 * Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 * Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 */
if(!isset($admin_id) or !$main == "1" or (isset($admin_id) and !$pa['traffic'])) {
	header('Location: admin.php');
	die('No acces');
}
$sprache=getlanguagefile('traffic',$user_language,$reseller_id);
if ($d=='se' and $reseller_id==0) {

	include(EASYWIDIR . '/stuff/keyphrasefile.php');
	
    if ($ui->w('action',4,'post') and !token(true)) {
        $template_file=$spracheResponse->token;
    } else if (isset($ui->post['type']) and $ui->w('action',4,'post')=='md') {
		$error=0;
		if (!isset($ui->post['type']) or (!small_letters_check($ui->post['type'],'30'))) $error=1;
		if (!isset($ui->post['statip']) or (!isip($ui->post['statip'],'all') and !isurl($ui->post['statip']))) $error=1;
		if (!isset($ui->post['dbname']) or (!gamestring($ui->post['dbname']))) $error=1;
		if (!isset($ui->post['dbuser']) or (!gamestring($ui->post['dbuser']))) $error=1;
		if (!isset($ui->post['dbpassword']) or (!gamestring($ui->post['dbpassword']))) $error=1;
		if (!isset($ui->post['table_name']) or (!gamestring($ui->post['table_name']))) $error=1;
		if (!isset($ui->post['column_sourceip']) or (!gamestring($ui->post['column_sourceip']))) $error=1;
		if (!isset($ui->post['column_destip']) or (!gamestring($ui->post['column_destip']))) $error=1;
		if (!isset($ui->post['column_byte']) or (!gamestring($ui->post['column_byte']))) $error=1;
		if (!isset($ui->post['column_date']) or (!gamestring($ui->post['column_date']))) $error=1;
		if (!isset($ui->post['multiplier']) or (!isinteger($ui->post['multiplier']))) $error=1;
		if (!isset($ui->post['text_colour_1']) or (!validate_int($ui->post['text_colour_1'],'0','255') and $ui->post['text_colour_1']!=0)) $error=1;
		if (!isset($ui->post['text_colour_2']) or (!validate_int($ui->post['text_colour_2'],'0','255') and $ui->post['text_colour_2']!=0)) $error=1;
		if (!isset($ui->post['text_colour_3']) or (!validate_int($ui->post['text_colour_3'],'0','255') and $ui->post['text_colour_3']!=0)) $error=1;
		if (!isset($ui->post['barin_colour_1']) or (!validate_int($ui->post['barin_colour_1'],'0','255') and $ui->post['barin_colour_1']!=0)) $error=1;
		if (!isset($ui->post['barin_colour_2']) or (!validate_int($ui->post['barin_colour_2'],'0','255') and $ui->post['barin_colour_2']!=0)) $error=1;
		if (!isset($ui->post['barin_colour_3']) or (!validate_int($ui->post['barin_colour_3'],'0','255') and $ui->post['barin_colour_3']!=0)) $error=1;
		if (!isset($ui->post['barout_colour_1']) or (!validate_int($ui->post['barout_colour_1'],'0','255') and $ui->post['barout_colour_1']!=0)) $error=1;
		if (!isset($ui->post['barout_colour_2']) or (!validate_int($ui->post['barout_colour_2'],'0','255') and $ui->post['barout_colour_2']!=0)) $error=1;
		if (!isset($ui->post['barout_colour_3']) or (!validate_int($ui->post['barout_colour_3'],'0','255') and $ui->post['barout_colour_3']!=0)) $error=1;
		if (!isset($ui->post['bartotal_colour_1']) or (!validate_int($ui->post['bartotal_colour_1'],'0','255') and $ui->post['bartotal_colour_1']!=0)) $error=1;
		if (!isset($ui->post['bartotal_colour_2']) or (!validate_int($ui->post['bartotal_colour_2'],'0','255') and $ui->post['bartotal_colour_2']!=0)) $error=1;
		if (!isset($ui->post['bartotal_colour_3']) or (!validate_int($ui->post['bartotal_colour_3'],'0','255') and $ui->post['bartotal_colour_3']!=0)) $error=1;
		if (!isset($ui->post['bg_colour_1']) or (!validate_int($ui->post['bg_colour_1'],'0','255') and $ui->post['bg_colour_1']!=0)) $error=1;
		if (!isset($ui->post['bg_colour_2']) or (!validate_int($ui->post['bg_colour_2'],'0','255') and $ui->post['bg_colour_2']!=0)) $error=1;
		if (!isset($ui->post['bg_colour_3']) or (!validate_int($ui->post['bg_colour_3'],'0','255') and $ui->post['bg_colour_3']!=0)) $error=1;
		if (!isset($ui->post['border_colour_1']) or (!validate_int($ui->post['border_colour_1'],'0','255') and $ui->post['border_colour_1']!=0)) $error=1;
		if (!isset($ui->post['border_colour_2']) or (!validate_int($ui->post['border_colour_2'],'0','255') and $ui->post['border_colour_2']!=0)) $error=1;
		if (!isset($ui->post['border_colour_3']) or (!validate_int($ui->post['border_colour_3'],'0','255') and $ui->post['border_colour_3']!=0)) $error=1;
		if (!isset($ui->post['line_colour_1']) or (!validate_int($ui->post['line_colour_1'],'0','255') and $ui->post['line_colour_1']!=0)) $error=1;
		if (!isset($ui->post['line_colour_2']) or (!validate_int($ui->post['line_colour_2'],'0','255') and $ui->post['line_colour_2']!=0)) $error=1;
		if (!isset($ui->post['line_colour_3']) or (!validate_int($ui->post['line_colour_3'],'0','255') and $ui->post['line_colour_3']!=0)) $error=1;
		if ($error==0) {
			$query=$sql->prepare("UPDATE `traffic_settings` SET `type`=:type,`statip`=:statip,`dbname`=AES_ENCRYPT(:dbname,:aeskey),`dbuser`=AES_ENCRYPT(:dbuser,:aeskey),`dbpassword`=AES_ENCRYPT(:dbpassword,:aeskey),`multiplier`=:multiplier,`table_name`=:table_name,`column_sourceip`=:column_sourceip,`column_destip`=:column_destip,`column_byte`=:column_byte,`column_date`=:column_date,`text_colour_1`=:text_colour_1,`text_colour_2`=:text_colour_2,`text_colour_3`=:text_colour_3,`barin_colour_1`=:barin_colour_1,`barin_colour_2`=:barin_colour_2,`barin_colour_3`=:barin_colour_3,`barout_colour_1`=:barout_colour_1,`barout_colour_2`=:barout_colour_2,`barout_colour_3`=:barout_colour_3,`bartotal_colour_1`=:bartotal_colour_1,`bartotal_colour_2`=:bartotal_colour_2,`bartotal_colour_3`=:bartotal_colour_3,`bg_colour_1`=:bg_colour_1,`bg_colour_2`=:bg_colour_2,`bg_colour_3`=:bg_colour_3,`border_colour_1`=:border_colour_1,`border_colour_2`=:border_colour_2,`border_colour_3`=:border_colour_3,`line_colour_1`=:line_colour_1,`line_colour_2`=:line_colour_2,`line_colour_3`=:line_colour_3 LIMIT 1");
            $query->execute(array(':aeskey'=>$aeskey,':type'=>$ui->post['type'],':statip'=>$ui->post['statip'],':dbname'=>$ui->post['dbname'],':dbuser'=>$ui->post['dbuser'],':dbpassword'=>$ui->post['dbpassword'],':table_name'=>$ui->post['table_name'],':multiplier'=>$ui->post['multiplier'],':column_sourceip'=>$ui->post['column_sourceip'],':column_destip'=>$ui->post['column_destip'],':column_byte'=>$ui->post['column_byte'],':column_date'=>$ui->post['column_date'],':text_colour_1'=>$ui->post['text_colour_1'],':text_colour_2'=>$ui->post['text_colour_2'],':text_colour_3'=>$ui->post['text_colour_3'],':barin_colour_1'=>$ui->post['barin_colour_1'],':barin_colour_2'=>$ui->post['barin_colour_2'],':barin_colour_3'=>$ui->post['barin_colour_3'],':barout_colour_1'=>$ui->post['barout_colour_1'],':barout_colour_2'=>$ui->post['barout_colour_2'],':barout_colour_3'=>$ui->post['barout_colour_3'],':bartotal_colour_1'=>$ui->post['bartotal_colour_1'],':bartotal_colour_2'=>$ui->post['bartotal_colour_2'],':bartotal_colour_3'=>$ui->post['bartotal_colour_3'],':bg_colour_1'=>$ui->post['bg_colour_1'],':bg_colour_2'=>$ui->post['bg_colour_2'],':bg_colour_3'=>$ui->post['bg_colour_3'],':border_colour_1'=>$ui->post['border_colour_1'],':border_colour_2'=>$ui->post['border_colour_2'],':border_colour_3'=>$ui->post['border_colour_3'],':line_colour_1'=>$ui->post['line_colour_1'],':line_colour_2'=>$ui->post['line_colour_2'],':line_colour_3'=>$ui->post['line_colour_3']));
			$template_file=$spracheResponse->table_add;
		} else {
			$template_file='Error';
		}
	} else {
        $query=$sql->prepare("SELECT `type`,`statip`,AES_DECRYPT(`dbname`,:aeskey) AS `decpteddbname`,AES_DECRYPT(`dbuser`,:aeskey) AS `decpteddbuser`,AES_DECRYPT(`dbpassword`,:aeskey) AS `decpteddbpassword`,`table_name`,`column_sourceip`,`column_destip`,`column_byte`,`column_date`,`multiplier`,`text_colour_1`,`text_colour_2`,`text_colour_3`,`barin_colour_1`,`barin_colour_2`,`barin_colour_3`,`barout_colour_1`,`barout_colour_2`,`barout_colour_3`,`bartotal_colour_1`,`bartotal_colour_2`,`bartotal_colour_3`,`bg_colour_1`,`bg_colour_2`,`bg_colour_3`,`border_colour_1`,`border_colour_2`,`border_colour_3`,`line_colour_1`,`line_colour_2`,`line_colour_3` FROM `traffic_settings` LIMIT 1");
        $query->execute(array(':aeskey'=>$aeskey));
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$type=$row['type'];
			$statip=$row['statip'];
			$dbname=$row['decpteddbname'];
			$dbuser=$row['decpteddbuser'];
			$dbpassword=$row['decpteddbpassword'];
			$table_name=$row['table_name'];
			$column_sourceip=$row['column_sourceip'];
			$column_destip=$row['column_destip'];
			$column_byte=$row['column_byte'];
			$column_date=$row['column_date'];
			$multiplier=$row['multiplier'];
			$text_colour_1=$row['text_colour_1'];
			$text_colour_2=$row['text_colour_2'];
			$text_colour_3=$row['text_colour_3'];
			$barin_colour_1=$row['barin_colour_1'];
			$barin_colour_2=$row['barin_colour_2'];
			$barin_colour_3=$row['barin_colour_3'];
			$barout_colour_1=$row['barout_colour_1'];
			$barout_colour_2=$row['barout_colour_2'];
			$barout_colour_3=$row['barout_colour_3'];
			$bartotal_colour_1=$row['bartotal_colour_1'];
			$bartotal_colour_2=$row['bartotal_colour_2'];
			$bartotal_colour_3=$row['bartotal_colour_3'];
			$bg_colour_1=$row['bg_colour_1'];
			$bg_colour_2=$row['bg_colour_2'];
			$bg_colour_3=$row['bg_colour_3'];
			$border_colour_1=$row['border_colour_1'];
			$border_colour_2=$row['border_colour_2'];
			$border_colour_3=$row['border_colour_3'];
			$line_colour_1=$row['line_colour_1'];
			$line_colour_2=$row['line_colour_2'];
			$line_colour_3=$row['line_colour_3'];
		}
		$template_file='admin_traffic_settings.tpl';
	}
} else {
	$display=$sprache->total;
	$data=array();
	if (!isset($ui->post['unit'])) {
		$unit="mb";
	} else if ($ui->post['unit']=="mb") {
		$unit="mb";
	} else if ($ui->post['unit']=="gb") {
		$unit="gb";
	} else if ($ui->post['unit']=="tb") {
		$unit="tb";
	}
	if (!isset($ui->post['kind'])) {
		$kind="al";
		$whichdata="";
	} else if ($ui->post['kind']=="al") {
		$kind="al";
		$whichdata="";
	} else if ($ui->post['kind']=="su") {
		$kind="su";
		if (isips($ui->post['what'])) {
			$whichdata="&amp;ips=".$ui->post['what'];
			$display=$sprache->subnet." ".$ui->post['what'];
		}
		if ($reseller_id==0) {
			$pselect=$sql->prepare("SELECT `ips` FROM `resellerdata`");
			$pselect->execute();
		} else if ($reseller_id==$admin_id) {
			$pselect=$sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellersid`=:reseller_id");
			$pselect->execute(array(':reseller_id' => $reseller_id));
		} else {
			$pselect=$sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellerid`=:admin_id AND c.`resellersid`=:reseller_id");
			$pselect->execute(array(':admin_id' => $admin_id,':reseller_id' => $reseller_id));
		}		
		$ips=array();
		$userips=array();
		foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
			unset($userips);
			$userips=ipstoarray($row['ips']);
			foreach ($userips as $ip) {
				$ip_ex=explode(".",$ip);
				$ips[]=$ip_ex[0].".".$ip_ex[1].".".$ip_ex[2].".";
			}
		}
		$subnets=array_unique($ips);
		natsort($subnets);
		foreach ($subnets as $subnet) {
			if ($ui->post['what']==$subnet) {
				$data[]='<option selected="selected">'.$subnet.'</option>';
			} else {
				$data[]='<option>'.$subnet.'</option>';
			}
		}
	} else if ($ui->post['kind']=="rs" or $ui->post['kind']=="us") {
		if (isid($ui->post['what'],'30') and $ui->post['kind']=="rs") {
			$kind="rs";
			$whichdata="&amp;short=".$ui->post['what'];
			$extra=$gsprache->reseller;
			if ($reseller_id==0) {
				$pselect=$sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `accounttype`='r' AND `id`=`resellerid`");
				$pselect->execute();
			}
			foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
				if ($ui->post['what']==$row['id']) {
					$data[]='<option value='.$row['id'].' selected="selected">'.$row['cname'].'</option>';
				} else {
					$data[]='<option value='.$row['id'].'>'.$row['cname'].'</option>';
				}
			}
		} else if (isid($ui->post['what'],'30') and $ui->post['kind']=="us") {
			$kind="us";
			$whichdata="&amp;distro=".$ui->post['what'];
			$extra=$sprache->user;
			if ($reseller_id==0) {
				$pselect=$sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `accounttype`='r'");
				$pselect->execute();
			} else if ($reseller_id==$admin_id) {
				$pselect=$sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `accounttype`='r' AND `resellerid`=:reseller_id");
				$pselect->execute(array(':reseller_id' => $reseller_id));
			}
			foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
				if ($ui->post['what']==$row['id']) {
					$data[]='<option value='.$row['id'].' selected="selected">'.$row['cname'].'</option>';
				} else {
					$data[]='<option value='.$row['id'].'>'.$row['cname'].'</option>';
				}
			}
		}
		if ($reseller_id==0) {
			$pselect=$sql->prepare("SELECT `cname` FROM `userdata` WHERE `accounttype`='r' AND `id`=:id LIMIT 1");
			$pselect->execute(array(':id'=>$ui->post['what']));
		} else if ($reseller_id==$admin_id) {
			$pselect=$sql->prepare("SELECT `cname` FROM `userdata` WHERE `accounttype`='r' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
			$pselect->execute(array(':id'=>$ui->post['what'],':reseller_id'=>$reseller_id));
		}
		foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$display=$extra." ".$row['cname'];
		}
	} else if ($ui->post['kind']=="se") {
		$kind="se";
		if (isid($ui->post['what'],'30')) {
			$whichdata="&amp;shorten=".$ui->post['what'];
		}
		if ($reseller_id==0) {
			$pselect=$sql->prepare("SELECT u.`cname` FROM `virtualcontainer` c LEFT JOIN `userdata` u ON c.`userid`=u.`id` WHERE c.`id`=:id ORDER BY u.`id`,c.`id` LIMIT 1");
			$pselect->execute(array(':id' =>$ui->post['what']));
		} else if ($reseller_id==$admin_id){
			$pselect=$sql->prepare("SELECT u.`cname` FROM `virtualcontainer` c LEFT JOIN `userdata` u ON c.`userid`=u.`id` WHERE c.`id`=:id  AND c.`resellerid`=:reseller_id ORDER BY u.`id`,c.`id` LIMIT 1");
			$pselect->execute(array(':id' =>$ui->post['what'],':reseller_id' => $reseller_id));
		} else {
			$pselect=$sql->prepare("SELECT u.`cname` FROM `virtualcontainer` c LEFT JOIN `userdata` u ON c.`userid`=u.`id` WHERE c.`id`=:id  AND c.`userid`=:admin_id AND c.`resellerid`=:reseller_id ORDER BY u.`id`,c.`id` LIMIT 1");
			$pselect->execute(array(':id' =>$ui->post['what'],':admin_id' => $admin_id,':reseller_id' => $reseller_id));
		}
		foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$display=$sprache->server." ".$row['cname'].'-'.$ui->post['what'];
		}
		if ($reseller_id==0) {
			$pselect=$sql->prepare("SELECT c.`id`,u.`cname` FROM `virtualcontainer` c LEFT JOIN `userdata` u ON c.`userid`=u.`id` ORDER BY u.`id`,c.`id`");
			$pselect->execute();
		} else if ($reseller_id==$admin_id){
			$pselect=$sql->prepare("SELECT c.`id`,u.`cname` FROM `virtualcontainer` c LEFT JOIN `userdata` u ON c.`userid`=u.`id` WHERE c.`resellerid`=:reseller_id ORDER BY u.`id`,c.`id`");
			$pselect->execute(array(':reseller_id' => $reseller_id));
		} else {
			$pselect=$sql->prepare("SELECT c.`id`,u.`cname` FROM `virtualcontainer` c LEFT JOIN `userdata` u ON c.`userid`=u.`id` WHERE c.`userid`=:admin_id AND c.`resellerid`=:reseller_id ORDER BY u.`id`,c.`id`");
			$pselect->execute(array(':admin_id' => $admin_id,':reseller_id' => $reseller_id));
		}
		foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
			if ($ui->post['what']==$row['id']) {
				$data[]='<option value='.$row['id'].' selected="selected">'.$row['cname'].'-'.$row['id'].'</option>';
			} else {
				$data[]='<option value='.$row['id'].'>'.$row['cname'].'-'.$row['id'].'</option>';
			}
		}
	} else if ($ui->post['kind']=="ip") {
		$kind="ip";
		if (isip($ui->post['what'],'all')) {
			$whichdata="&amp;ip=".$ui->post['what'];
			$display=$sprache->ip." ".$ui->post['what'];
		}
		if ($reseller_id==0) {
			$pselect=$sql->prepare("SELECT `ips` FROM `resellerdata`");
			$pselect->execute();
		} else if ($reseller_id==$admin_id) {
			$pselect=$sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellersid`=:reseller_id");
			$pselect->execute(array(':reseller_id' => $reseller_id));
		} else {
			$pselect=$sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellerid`=:admin_id AND c.`resellersid`=:reseller_id");
			$pselect->execute(array(':admin_id' => $admin_id,':reseller_id' => $reseller_id));
		}		
		$ips=array();
		$user_ips=array();
		foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
			unset($user_ips);
			$user_ips=ipstoarray($row['ips']);
			foreach ($user_ips as $userip) {
				$userips[]=$userip;
			}
		}
		$ips=array_unique($userips);
		natsort($ips);
		foreach ($ips as $ip) {
			if ($ui->post['what']==$ip) {
				$data[]='<option selected="selected">'.$ip.'</option>';
			} else {
				$data[]='<option>'.$ip.'</option>';
			}
		}
	}

	if (!isset($ui->post['dmy'])) {
		$dmy='da';
		$year=date('Y',strtotime("-6 days"));
		$month=date('m',strtotime("-6 days"));
		$day=date('d',strtotime("-6 days"));
		$yearstop=date('Y');
		$monthstop=date('m');
		$daystop=date('d');
		$amount=7;
	} else if ($ui->post['dmy']=='da') {
		$dmy='da';
		if (validate_int($ui->post['daystart'],1,31)) {
			$day=$ui->post['daystart'];
		} else {
			$day=date('d',strtotime("-6 days"));
		}
		if (validate_int($ui->post['daystop'],1,31)) {
			$daystop=$ui->post['daystop'];
		} else {
			$day=date('d');
		}
		if (validate_int($ui->post['monthstart'],1,12)) {
			$month=$ui->post['monthstart'];
		} else {
			$month=date('m',strtotime("-6 days"));
		}
		if (validate_int($ui->post['monthstop'],1,12)) {
			$monthstop=$ui->post['monthstop'];
		} else {
			$monthstop=date('m');
		}
		if (validate_int($ui->post['yearstart'],2000,date('Y'))) {
			$year=$ui->post['yearstart'];
		} else {
			$year=date('Y',strtotime("-6 days"));
		}
		if (validate_int($ui->post['yearstop'],2000,date('Y'))) {
			$yearstop=$ui->post['yearstop'];
		} else {
			$yearstop=date('Y');
		}
		$now=date('Y-m-d');
		$date1=strtotime("$year-$month-$day");
		$date2=strtotime("$yearstop-$monthstop-$daystop");
		$amount=intval(($date2-$date1)/86400)+1;
		if ($amount<0 and "$yearstop-$monthstop-$daystop">$now){
			$yearstop=date('Y');
			$monthstop=date('m');
			$daystop=date('d');	
			$day=date('d',strtotime("-6 days"));
			$month=date('m',strtotime("-6 days"));
			$year=date('Y',strtotime("-6 days"));
			$amount=7;
		}
	} else if ($ui->post['dmy']=='mo') {
		$dmy='mo';
		$day=1;
		if (validate_int($ui->post['monthstart'],1,12)) {
			$month=$ui->post['monthstart'];
		} else {
			$month=date('m',strtotime("-6 days"));
		}
		if (validate_int($ui->post['yearstart'],2000,date('Y'))) {
			$year=$ui->post['yearstart'];
		} else {
			$year=date('Y',strtotime("-6 days"));
		}
		if (validate_int($ui->post['yearstop'],2000,date('Y'))) {
			$yearstop=$ui->post['yearstop'];
		} else {
			$yearstop=date('Y');
		}
		if (validate_int($ui->post['monthstop'],1,12)) {
			$monthstop=$ui->post['monthstop'];
		} else {
			$monthstop=date('m');
		}
		$daystop=date('t', strtotime("$yearstop-$monthstop"));
		$now=date('Y-m');
		$date1=strtotime("$year-$month-$day");
		$add=$date1;
		$date2=strtotime("$yearstop-$monthstop-$daystop");
		$i=0;
		while ($add<=$date2) {
			$newadd=strtotime("+1 months",$add);
			$add=$newadd;
			$i++;
		}
		$amount=$i;
		if ($amount<0 or "$yearstop-$monthstop">$now){
			$yearstop=date('Y');
			$monthstop=date('m');
			$daystop=date('t', strtotime("$yearstop-$monthstop"));
			$day='1';
			$month=date('m',strtotime("-6 months"));
			$year=date('Y',strtotime("-6 months"));
			$amount=7;
		}
	} else if ($ui->post['dmy']=='ye') {
		$dmy='ye';
		$day=1;
		if (validate_int($ui->post['yearstart'],2000,date('Y'))) {
			$year=$ui->post['yearstart'];
		} else {
			$year=date('Y',strtotime("-6 days"));
		}
		if (validate_int($ui->post['yearstop'],2000,date('Y'))) {
			$yearstop=$ui->post['yearstop'];
		} else {
			$yearstop=date('Y');
		}
		$month=1;
		$monthstop=12;
		$daystop=31;
		$now=date('Y');
		$date1=strtotime("$year-$month-$day");
		$date2=strtotime("$yearstop-$monthstop-$daystop");
		$add=$date1;
		$i=0;
		while ($add<=$date2) {
			$newadd=strtotime("+1 year",$add);
			$add=$newadd;
			$i++;
		}
		$amount=$i;
		if ($amount<0 or "$yearstop">$now){
			$yearstop=date('Y');
			$monthstop=12;
			$daystop=31;
			$day=1;
			$month=1;
			$year=date('Y',strtotime("-1 year"));
			$amount=2;
		}
	}
	if ($user_language="de") {
		$startdate="$day.$month.$year";
		$stopdate="$daystop.$monthstop.$yearstop";
	} else {
		$startdate="$year-$month-$day";
		$stopdate="$yearstop-$monthstop-$daystop";
	}
	$trafficdata="images.php?img=tr&amp;d=$dmy&amp;w=$unit&amp;p=$year&amp;id=$day&amp;po=$month&amp;m=$amount$whichdata";
	$template_file="admin_traffic.tpl";
}