<?php
/**
 * File: tickets.php.
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
if (!isset($admin_id) or $main!=1 or (isset($admin_id) and !$pa['tickets'])) {
    header('Location: admin.php');
    die;
}
$sprache=getlanguagefile('tickets',$user_language,$reseller_id,$sql);
$loguserid=$admin_id;
$logusername=getusername($admin_id);
$logusertype="admin";
if ($reseller_id==0) {
	$logreseller=0;
	$logsubuser=0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller=0;
}
if ($reseller_id!=0 and $admin_id!=$reseller_id) $reseller_id=$admin_id;
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->st('d','get')=='at') {
    if (!$ui->smallletters('action',2,'post')) {
        $foundlanguages=array();
        foreach (getlanguages($template_to_use) as $row) {
            if ($row==$rSA['language']) {
                $checked='checked';
                $style='';
                $checkbox="<input type=\"checkbox\" name=\"language[]\" value=\"$row\" onclick=\"textdrop('$row');\" checked /> ";
            } else {
                $checked='';
                $style='display_none';
                $checkbox="<input type=\"checkbox\" name=\"language[]\" value=\"$row\" onclick=\"textdrop('$row');\" /> ";
            }
            $foundlanguages[]=array('style'=>$style,'lang'=>$row,'checkbox'=>$checkbox,'checked'=>$checked);
        }
        $query=$sql->prepare("SELECT `id`,`topic` FROM `ticket_topics` WHERE `id`=maintopic AND `resellerid`=?");
        $query2=$sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ti' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $topic='';
            $query2->execute(array($user_language,$row['id'],$reseller_id));
            $topic=$query2->fetchColumn();
            if (empty($topic)) {
                $query2->execute(array($rSA['language'],$row['id'],$reseller_id));
                $topic=$query2->fetchColumn();
            }
            if (empty($topic)) {
                $topic=$row['topic'];
            }
            $options[]="<option value=\"".$row['id']."\">".$topic."</option>";
        }
        $template_file="admin_ticket_topic_add.tpl";
    } else if ($ui->smallletters('action',2,'post')=="ad") {
        if ($ui->description('maintopic','post')){
            $topic_name=description($ui->post['topic_name']);
            $priority=isid($ui->post['priority'],1);
            $maintopic=$ui->description('maintopic','post');
            $query=$sql->prepare("SELECT `id` FROM `ticket_topics` WHERE `topic`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($topic_name,$reseller_id));
            $num=$query->rowCount();
            if ($num==1) {
                $template_file=$sprache->error_exist;
            } else {
                $query=$sql->prepare("INSERT INTO `ticket_topics` (`topic`,`maintopic`,`priority`,`resellerid`) VALUES (?,?,?,?)");
                $query->execute(array($topic_name,$maintopic,$priority,$reseller_id));
                $id=$sql->lastInsertId();
                if ($maintopic=="none") {
                    $query=$sql->prepare("UPDATE `ticket_topics` SET `maintopic`=:id, priority='NULL' WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                    $query->execute(array(':id'=> $id,':reseller_id'=>$reseller_id));
                }
                if (isset($ui->post['language'])) {
                    $query=$sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('ti',?,?,?,?) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
                    foreach($ui->post['language'] as $language) {
                        if (small_letters_check($language, '2')) {
                            $subject=description($ui->post["subject_$language"]);
                            $query->execute(array($id,$language,$subject,$reseller_id));
                        }
                    }
                }
                $template_file=$spracheResponse->table_add;
                $loguseraction="%add% %ticket_subject% $topic_name";
                $insertlog->execute();
            }
        } else {
            $template_file="Error: Topic";
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else if ($ui->st('d','get')=='dt' and $ui->id('id',19,'get')) {
    $id=$ui->id('id',19,'get');
    if (!isset($action)) {
        $topic="";
        $query=$sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ti' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($user_language,$id,$reseller_id));
        $topic=$query->fetchColumn();
        if (empty($topic)) {
            $query->execute(array($rSA['language'],$id,$reseller_id));
            $topic=$query->fetchColumn();
        }
        if (empty($topic)) {
            $query=$sql->prepare("SELECT `topic` FROM `ticket_topics` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id,$reseller_id));
            $topic=stripslashes($query->fetchColumn());
        }
        $template_file="admin_ticket_topic_dl.tpl";
    } else if ($action=='dl'){
        $query=$sql->prepare("SELECT `topic` FROM `ticket_topics` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $topic=stripslashes($query->fetchColumn());
        $query=$sql->prepare("DELETE FROM `tickets` WHERE `topic`=? AND `resellerid`=?");
        $query->execute(array($id,$reseller_id));
        $query=$sql->prepare("DELETE FROM `ticket_topics` WHERE `maintopic`=? AND `resellerid`=?");
        $query->execute(array($id,$reseller_id));
        $query=$sql->prepare("DELETE FROM `ticket_topics` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $query=$sql->prepare("DELETE FROM `translations` WHERE `type`='ti' AND `transID`=? AND `resellerID`=?");
        $query->execute(array($id,$reseller_id));
        $loguseraction="%del% %ticket_subject% $topic";
        $insertlog->execute();
        $template_file=$spracheResponse->table_del;
    } else {
        $template_file='admin_404.tpl';
    }
} else if ($ui->st('d','get')=='mt') {
    if (!$ui->smallletters('action',2,'post') and $ui->id('id',19,'get')) {
        $id=$ui->id('id',19,'get');
        $query=$sql->prepare("SELECT `topic`,`maintopic`,`priority` FROM `ticket_topics` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $topic=$row['topic'];
            $priority=$row['priority'];
            if ($id==$row['maintopic']) {
                $maintopic="";
            } else {
                $maintopic=$row['maintopic'];
            }
        }
        $query=$sql->prepare("SELECT `id`,`topic` FROM `ticket_topics` WHERE `id`=`maintopic` AND `resellerid`=?");
        $query->execute(array($reseller_id));
        $query2=$sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ti' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $topics="";
            $query2->execute(array($user_language,$row['id'],$reseller_id));
            $topic=$query2->fetchColumn();
            if (empty($topics)) {
                $query2->execute(array($rSA['language'],$row['id'],$reseller_id));
                $topic=$query2->fetchColumn();
            }
            if (empty($topics)) {
                $topics=$row['topic'];
            }
            if ($row['id']==$maintopic) {
                $options[]="<option value=\"".$row['id']."\" selected=\"selected\">".$topics."</option>";
            } else {
                $options[]="<option value=\"".$row['id']."\">".$topics."</option>";
            }
        }
        $foundlanguages=array();
        $query=$sql->prepare("SELECT `text`,`lang` FROM `translations` WHERE `type`='ti' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
        foreach (getlanguages($template_to_use) as $langrow2) {
            unset($lang);
            $subject="";
            $query->execute(array($id,$langrow2,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $lang=$row['lang'];
                $subject=$row['text'];
            }
            if (isset($lang)) {
                $style='';
                $checked='checked';
                $checkbox="<input type=\"checkbox\" name=\"language[]\" value=\"$langrow2\" onclick=\"textdrop('$langrow2');\" checked /> ";
            } else {
                $style='display_none';
                $checked='';
                $checkbox="<input type=\"checkbox\" name=\"language[]\" value=\"$langrow2\" onclick=\"textdrop('$langrow2');\" /> ";
            }
            $foundlanguages[]=array('style'=>$style,'lang'=>$langrow2,'checkbox'=>$checkbox,'checked'=>$checked,'subject' => stripslashes($subject));
        }
        $template_file="admin_ticket_topic_md.tpl";
    } else if ($ui->smallletters('action',2,'post')=='md' and $ui->id('id',19,'get')){
        $id=$ui->id('id',19,'get');
        if (description($ui->post['maintopic'])){
            $topic_name=description($ui->post['topic_name']);
            $priority=isid($ui->post['priority'], "1");
            $maintopic=description($ui->post['maintopic']);
            if ($maintopic=="none") {
                $maintopic=$id;
                $priority='';
            }
            $query=$sql->prepare("UPDATE `ticket_topics` SET `topic`=:topic,`maintopic`=:maintopic,`priority`=:priority WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':topic'=> $topic_name, ':maintopic'=> $maintopic, ':priority'=> $priority, ':id'=> $id, ':reseller_id'=>$reseller_id));
            if (isset($ui->post['language'])) {
                $query=$sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('ti',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
                foreach($ui->post['language'] as $language) {
                    if (small_letters_check($language, '2')) {
                        $subject=description($ui->post["subject_$language"]);
                        $query->execute(array($id,$language,$subject,$reseller_id));
                    }
                }
                $query=$sql->prepare("SELECT `lang` FROM `translations` WHERE `type`='ti' AND `transID`=? AND `resellerID`=?");
                $query->execute(array($id,$reseller_id));
                $query2=$sql->prepare("DELETE FROM `translations` WHERE `type`='ti' AND `lang`=? AND `transID`=? AND `resellerID`=?");
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (!in_array($row['lang'],(array)$ui->post['language'])) {
                        $query2->execute(array($row['lang'],$id,$reseller_id));
                    }
                }
            } else {
                $query=$sql->prepare("DELETE FROM `translations` WHERE `type`='ti' AND `transID`=? AND `resellerID`=?");
                $query->execute(array($id,$reseller_id));
            }

            $template_file=$spracheResponse->table_add;
            $loguseraction="%mod% %ticket_subject% $topic_name";
            $insertlog->execute();
        } else {
            $template_file="Error: Topic";
        }
    } else {
        $o=$ui->st('o','get');
        if ($ui->st('o','get')=='dn') {
            $orderby='l.`text` DESC';
        } else if ($ui->st('o','get')=='an') {
            $orderby='l.`text` ASC';
        } else if ($ui->st('o','get')=='dp') {
            $orderby='`priority` DESC';
        } else if ($ui->st('o','get')=='ap') {
            $orderby='`priority` ASC';
        } else if ($ui->st('o','get')=='di') {
            $orderby='`id` DESC';
        } else if ($ui->st('o','get')=='ai') {
            $orderby='`id` ASC';
        } else {
            $o='an';
            $orderby='t.`maintopic`,t.`id` ASC';
        }
        $table=array();
        $query2=$sql->prepare("SELECT t.*,l.`text`,d.`text` AS `defaultsubject` FROM `ticket_topics` t LEFT JOIN `translations` l ON t.`id`=l.`transID` AND l.`type`='ti' AND l.`lang`=? LEFT JOIN `translations` d ON t.`id`=d.`transID` AND d.`type`='ti' AND d.`lang`=? WHERE t.`resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
        $query2->execute(array($user_language,$rSA['language'],$reseller_id));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $priority="";
            $topic="";
            if ($row2['priority']==1) {
                $priority=$sprache->priority_low;
            } else if ($row2['priority']==2) {
                $priority=$sprache->priority_medium;
            } else if ($row2['priority']==3) {
                $priority=$sprache->priority_high;
            } else if ($row2['priority']==4) {
                $priority=$sprache->priority_highest;
            }
            if ($row2['text']!=null and $row2['text']!='') {
                $topic=$row2['text'];
            } else if ($row2['defaultsubject']!=null or $row2['defaultsubject']!='') {
                $topic=$row2['defaultsubject'];
            } else {
                $topic=$row2['topic'];
            }
            if ($row2['id']==$row2['maintopic']) {
                $maintopic='';
                $mTopic='';
            } else {
                $query3=$sql->prepare("SELECT t.*,l.`text`,d.`text` AS `defaultsubject` FROM `ticket_topics` t LEFT JOIN `translations` l ON t.`id`=l.`transID` AND l.`type`='ti' AND l.`lang`=? LEFT JOIN `translations` d ON t.`id`=d.`transID` AND d.`type`='ti' AND d.`lang`=? WHERE t.`id`=?  AND t.`resellerid`=? LIMIT 1");
                $query3->execute(array($user_language,$rSA['language'],$row2['maintopic'],$reseller_id));
                foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                    if ($row3['text']!=null and $row3['text']!="") {
                        $mTopic=$row3['text'];
                    } else if ($row3['defaultsubject']!=null or $row3['defaultsubject']!="") {
                        $mTopic=$row3['defaultsubject'];
                    } else {
                        $mTopic=$row3['topic'];
                    }
                }
                $maintopic=' - ';
            }
            $table[]=array('id'=>$row2['id'],'topic'=>$topic,'maintopic'=>$maintopic,'mTopic'=>$mTopic,'priority'=>$priority);
        }
        $next=$start+$amount;
        $countp=$sql->prepare("SELECT `id` FROM `ticket_topics` WHERE `resellerid`=?");
        $countp->execute(array($reseller_id));
        $colcount=$countp->rowCount();
        if ($colcount>$next) {
            $vor=$start+$amount;
        } else {
            $vor=$start;
        }
        $back=$start-$amount;
        if ($back>=0){
            $zur=$start-$amount;
        } else {
            $zur=$start;
        }
        $pageamount=ceil($colcount/$amount);
        $link='<a href="admin.php?w=ti&amp;d=mt&amp;o='.$o.'&amp;a=';
        if(!isset($amount)) {
            $link .="20";
        } else {
            $link .=$amount;
        }
        if ($start==0) {
            $link .='&p=0" class="bold">1</a>';
        } else {
            $link .='&p=0">1</a>';
        }
        $pages[]=$link;
        $i=2;
        while ($i<=$pageamount) {
            $selectpage=($i-1)*$amount;
            if ($start==$selectpage) {
                $pages[]='<a href="admin.php?w=ti&amp;d=mt&amp;a='.$amount.'&p='.$selectpage.'&amp;o='.$o.'" class="bold">'.$i.'</a>';
            } else {
                $pages[]='<a href="admin.php?w=ti&amp;d=mt&amp;a='.$amount.'&p='.$selectpage.'&amp;o='.$o.'">'.$i.'</a>';
            }
            $i++;
        }
        $pages=implode(', ',$pages);
        $template_file="admin_ticket_topic_list.tpl";
    }
} else if ($ui->st('d','get')=='md' and $ui->id('id',19,'get')) {
    $id=$ui->id('id',19,'get');
    if (!$ui->smallletters('action',2,'post') or $ui->smallletters('action',2,'get')=='md') {
        $supporterList=array();
        if (!$ui->smallletters('action',2,'post')) {
            $query=$sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='a' ORDER BY `id` DESC");
            $query->execute(array($reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $supporterList[$row['id']]=(trim($row['vname'].' '.$row['name'])!='') ? trim($row['vname'].' '.$row['name']) : $row['cname'];
            }
        }
        $table=array();
        $default_language=$rSA['language'];
        $query=$sql->prepare("SELECT * FROM `tickets` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query2=$sql->prepare("SELECT t.*,u.`cname`,u.`name`,u.`vname` FROM `tickets_text` t LEFT JOIN `userdata` u ON t.`userID`=u.`id` WHERE t.`ticketID`=? AND t.`resellerID`=? ORDER BY t.`writeDate`");
        $query3=$sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ti' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
        $query4=$sql->prepare("SELECT `topic` FROM `ticket_topics` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $supporter=$row['supporter'];
            $state=$row['state'];
            $query2->execute(array($id,$reseller_id));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $table[]=array('writedate'=>($user_language=='de') ? date('d.m.Y H:i:s',strtotime($row2['writeDate'])) : $row2['writeDate'],'ticket'=>nl2br(htmlspecialchars(stripslashes($row2['message']))),'writer'=>(trim($row2['vname'].' '.$row2['name'])!='') ? trim($row2['vname'].' '.$row2['name']) : $row2['cname']);
            }
            if ($row['priority']==1) $priority=$sprache->priority_low;
            else if ($row['priority']==2) $priority=$sprache->priority_medium;
            else if ($row['priority']==3) $priority=$sprache->priority_high;
            else if ($row['priority']==4) $priority=$sprache->priority_very_high;
            else $priority=$sprache->priority_critical;
            $realPriority=$row['priority'];
            if ($row['userPriority']==1) $userPriority=$sprache->priority_low;
            else if ($row['userPriority']==2) $userPriority=$sprache->priority_medium;
            else if ($row['userPriority']==3) $userPriority=$sprache->priority_high;
            else if ($row['userPriority']==4) $userPriority=$sprache->priority_very_high;
            else $userPriority=$sprache->priority_critical;
            if ($row['state']=='A') $status=$sprache->status_author;
            else if ($row['state']=='C') $status=$sprache->status_confirmed;
            else if ($row['state']=='D') $status=$sprache->status_done;
            else if ($row['state']=='N') $status=$sprache->status_new;
            else if ($row['state']=='P') $status=$sprache->status_process;
            else $status=$sprache->status_reopen;
            if ($row['state']=='C') $open='N';
            else $open='Y';
            if (is_numeric($row['topic'])) {
                $topic='';
                $query3->execute(array($user_language,$row['id'],$reseller_id));
                $topic=$query3->fetchColumn();
                if (empty($topic)) {
                    $query3->execute(array($default_language,$row['id'],$reseller_id));
                    $topic=$query3->fetchColumn();
                }
                if (empty($topic)) {
                    $query4->execute(array($row['topic'],$reseller_id));
                    $topic=stripslashes($query4->fetchColumn());
                }
                if (empty($topic)) {
                    $topic=stripslashes($row['topic']);
                }
            } else {
                $topic=$row['topic'];
            }
        }
        if (isset($priority)) $template_file=($ui->smallletters('action',2,'get')=='md') ? 'admin_tickets_md.tpl' : 'admin_tickets_view.tpl';
        else $template_file='admin_404.tpl';
    } else if ($ui->smallletters('action',2,'post')=='wr') {
        $query=$sql->prepare("SELECT `userid`,`state` FROM `tickets` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $userid=$row['userid'];
            $state=$row['state'];
        }
        if (isset($state) and $state!='C' and $ui->w('state',1,'post')!='C') {
            if($ui->id('priority',1,'post')) {
                $query=$sql->prepare("UPDATE `tickets` SET `state`=?,`supporter`=?,`priority`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($ui->w('state',1,'post'),$ui->id('supporter',10,'post'),$ui->id('priority',1,'post'),$id,$reseller_id));
            }
            $template_file=($query->rowCount()>0) ? $spracheResponse->table_add : $spracheResponse->error_table;
            if (isset($ui->post['ticket']) and strlen($ui->post['ticket'])>0) {
                $query=$sql->prepare("INSERT INTO `tickets_text` (`ticketID`,`message`,`writeDate`,`userID`,`resellerid`) VALUES (?,?,?,?,?)");
                $query->execute(array($id,$ui->post['ticket'],$logdate,$admin_id,$reseller_id));
            }
            if(isid($userid,10)) {
                $query=$sql->prepare("SELECT `mail_ticket` FROM `userdata` WHERE `id`=? LIMIT 1");
                $query->execute(array($userid));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if ($row['mail_ticket']=='Y') sendmail('emailnewticket',$userid,$ui->post['ticket'],array($id,$admin_id),$sql);
                }
            }
        } else {
            $template_file='userpanel_404.tpl';
        }
    }
} else {
    $table=array();
    $ticketLinks['all']='admin.php?w=ti&amp;d=md&amp;a='.$ui->id('a',3,'get');
    $ticketLinks['amount']='admin.php?w=ti&amp;d=md';
    $ticketLinks['A']='admin.php?w=ti&amp;d=md&amp;a='.$ui->id('a',3,'get');
    $ticketLinks['C']='admin.php?w=ti&amp;d=md&amp;a='.$ui->id('a',3,'get');
    $ticketLinks['D']='admin.php?w=ti&amp;d=md&amp;a='.$ui->id('a',3,'get');
    $ticketLinks['N']='admin.php?w=ti&amp;d=md&amp;a='.$ui->id('a',3,'get');
    $ticketLinks['P']='admin.php?w=ti&amp;d=md&amp;a='.$ui->id('a',3,'get');
    $ticketLinks['R']='admin.php?w=ti&amp;d=md&amp;a='.$ui->id('a',3,'get');
    $where='WHERE t.`resellerid`=?';
    if (isset($ui->get['ts'])) {
        foreach ($ui->get['ts'] as $get) {
            if (preg_match('/[ACDNPR]/',$get)) $selected[]=$get;
        }
    } else {
        $selected=array('N','P','R');
    }
    $temp=' AND (';
    $i=0;
    foreach ($selected as $get) {
        if ($i==0) $temp.="`state`='${get}'";
        else $temp.=" OR `state`='${get}'";
        if (!in_array($get,$selected)) $selected[]=$get;
        $i++;
    }
    $temp.=')';
    if ($i!=0) $where.=$temp;
    foreach ($ticketLinks as $k=>$v) {
        foreach (array('A','C','D','N','P','R') as $s) {
            if ((in_array($s,$selected) and $k!=$s) or (!in_array($s,$selected) and $k==$s)) $ticketLinks[$k].='&amp;ts[]='.$s;
        }
    }
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='di') {
        $orderby='t.`id` DESC';
    } else if ($ui->st('o','get')=='ai') {
        $orderby='t.`id` ASC';
    } else if ($ui->st('o','get')=='dd') {
        $orderby='t.`writedate` DESC';
    } else if ($ui->st('o','get')=='ad') {
        $orderby='t.`writedate` ASC';
    } else if ($ui->st('o','get')=='du') {
        $orderby='u.`cname` DESC';
    } else if ($ui->st('o','get')=='au') {
        $orderby='u.`cname` ASC';
    } else if ($ui->st('o','get')=='ds') {
        $orderby='t.`state` DESC';
    } else if ($ui->st('o','get')=='as') {
        $orderby='t.`state` ASC';
    } else if ($ui->st('o','get')=='dt') {
        $orderby='l.`text` DESC';
    } else if ($ui->st('o','get')=='at') {
        $orderby='l.`text` ASC';
    } else if ($ui->st('o','get')=='dp') {
        $orderby='t.`userPriority` DESC';
    } else if ($ui->st('o','get')=='ap') {
        $orderby='t.`userPriority` ASC';
    } else {
        $orderby='t.`userPriority` DESC, t.`writedate` ASC';
    }
    $query=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `tickets` t $where");
    $query->execute(array($resellerid));
    $colcount=$query->fetchColumn();
    if ($start>$colcount) {
        while ($start>0 and $start>$colcount) $start=$start-$amount;
        if ($start<0) $start=0;
    }
    $next=$start+$amount;
    if ($colcount>$next) {
        $vor=$start+$amount;
    } else {
        $vor=$start;
    }
    $back=$start-$amount;
    if ($back>=0){
        $zur=$start-$amount;
    } else {
        $zur=$start;
    }
    $pageamount=ceil($colcount/$amount);
    $i=1;
    $pages=array();
    while ($i<=$pageamount) {
        $selectpage=($i-1)*$amount;
        if ($start==$selectpage) {
            $pages[]='<a href="'.$ticketLinks['all'].'&amp;p='.$selectpage.'" class="bold">'.$i.'</a>';
        } else {
            $pages[]='<a href="'.$ticketLinks['all'].'&amp;p='.$selectpage.'">'.$i.'</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $query=$sql->prepare("SELECT t.*,l.`text`,d.`text` AS `defaultsubject`,u.`cname`,CONCAT(u.`name`,' ',u.`vname`) AS `username`,s.`cname` AS `supporter`,CONCAT(s.`name`,' ',s.`vname`) AS `supportername` FROM `tickets` t LEFT JOIN `ticket_topics` o ON t.`topic`=o.`id` LEFT JOIN `translations` l ON o.`id`=l.`transID` AND l.`type`='ti' AND l.`lang`=? LEFT JOIN `translations` d ON t.`id`=d.`transID` AND d.`type`='ti' AND d.`lang`=? LEFT JOIN `userdata` s ON t.`supporter`=s.`id` LEFT JOIN `userdata` u ON t.`userid`=u.`id` $where ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($user_language,$default_language,$resellerid));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['priority']==1) $priority=$sprache->priority_low;
        else if ($row['priority']==2) $priority=$sprache->priority_medium;
        else if ($row['priority']==3) $priority=$sprache->priority_high;
        else if ($row['priority']==4) $priority=$sprache->priority_very_high;
        else $priority=$sprache->priority_critical;
        if ($row['userPriority']==1) $userPriority=$sprache->priority_low;
        else if ($row['userPriority']==2) $userPriority=$sprache->priority_medium;
        else if ($row['userPriority']==3) $userPriority=$sprache->priority_high;
        else if ($row['userPriority']==4) $userPriority=$sprache->priority_very_high;
        else $userPriority=$sprache->priority_critical;
        if (is_numeric($row['topic'])) {
            $topic='';
            if ($row['text']!=null and $row['text']!="") {
                $topic=$row['text'];
            } else if ($row['defaultsubject']!=null or $row['defaultsubject']!="") {
                $topic=$row['defaultsubject'];
            } else {
                $topic=$row['topic'];
            }
        } else {
            $topic=$row['topic'];
        }
        if ($user_language=='de') {
            $writedate=date('d.m.Y H:i:s',strtotime($row['writedate']));
        } else {
            $writedate=$row['writedate'];
        }
        $statusClass='warning';
        if ($row['state']=='A') {
            $status=$sprache->status_author;
            $statusClass='info';
        } else if ($row['state']=='C') {
            $status=$sprache->status_confirmed;
            $statusClass='success';
        } else if ($row['state']=='D') {
            $status=$sprache->status_done;
            $statusClass='success';
        } else if ($row['state']=='N') {
            $status=$sprache->status_new;
        } else if ($row['state']=='P') {
            $status=$sprache->status_process;
        } else {
            $status=$sprache->status_reopen;
        }
        $table[]=array('id'=>$row['id'],'priority'=>$priority,'userPriority'=>$userPriority,'writedate'=>$writedate,'supporter'=>(trim($row['supportername'])!='') ? trim($row['supportername']) : $row['supporter'],'user'=>(trim($row['username'])!='') ? trim($row['username']) : $row['cname'],'subject'=>$topic,'status'=>$status,'rawState'=>$row['state'],'statusClass'=>$statusClass);
    }
    $template_file="admin_tickets_list.tpl";
}