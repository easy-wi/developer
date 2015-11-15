<?php
/**
 * File: tickets_reseller.php.
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
if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['usertickets'])) {
    header('Location: userpanel.php');
    die;
}
$sprache = getlanguagefile('tickets',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
	if (isset($_SESSION['oldid'])) {
		$logsubuser = $_SESSION['oldid'];
	} else {
		$logsubuser = 0;
	}
	$logreseller = 0;
}
if ($reseller_id != 0 and $admin_id==$reseller_id) {
	$resellerid = 0;
} else if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$resellerid = $reseller_id;
} else if ($reseller_id == 0) {
	$resellerid = 0;
}
if ($ui->st('d', 'get') == 'ad') {
    if (!$ui->smallletters('action',2, 'post')) {
        $table = array();
        $table2 = array();
        $default_language = $rSA['language'];
        $query = $sql->prepare("SELECT * FROM `ticket_topics` WHERE `maintopic`=`id` AND `resellerid`=? ORDER BY id");
        $query->execute(array($resellerid));
        $i = 1;
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $topic = '';
            $query3 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ti' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
            $query3->execute(array($user_language, $row['id'],$resellerid));
            $topic = $query3->fetchColumn();
            if (empty($topic)) {
                $query3->execute(array($default_language, $row['id'],$resellerid));
                $topic = $query3->fetchColumn();
            }
            if (empty($topic)) {
                $topic = $row['topic'];
            }
            $table[] = array('id' => $row['id'], 'topic' => $topic);
            if ($i==1) {
                $query2 = $sql->prepare("SELECT * FROM `ticket_topics` WHERE `maintopic`=? AND `maintopic`!=`id` AND `resellerid`=? ORDER BY `id`");
                $query2->execute(array($row['id'],$resellerid));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $topic = '';
                    $query3->execute(array($user_language, $row2['id'],$resellerid));
                    $topic = $query3->fetchColumn();
                    if (empty($topic)) {
                        $query3->execute(array($default_language, $row2['id'],$resellerid));
                        $topic = $query3->fetchColumn();
                    }
                    if (empty($topic)) {
                        $topic = $row2['topic'];
                    }
                    $table2[] = array('id' => $row2['id'], 'topic' => $topic);
                }
            }
            $i++;
        }
        $template_file = "admin_reseller_tickets_add.tpl";
    } else if ($ui->smallletters('action',2, 'post') == 'ad' and $ui->id('topic',30, 'post')){
        $topic = $ui->id('topic',30, 'post');
        $userPriority = $ui->id('userPriority',30, 'post');
        $ticketText=htmlentities($ui->post['ticket']);
        $query = $sql->prepare("SELECT `priority` FROM `ticket_topics` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($topic,$resellerid));
        $priority = $query->fetchColumn();
        $query = $sql->prepare("INSERT INTO `tickets` (`topic`,`userid`,`priority`,`userPriority`,`writedate`,`resellerid`) VALUES (?,?,?,?,?,?)");
        $query->execute(array($topic,$admin_id,$priority,$userPriority,$logdate,$resellerid));
        $lastID = $sql->lastInsertId();
        $query = $sql->prepare("INSERT INTO `tickets_text` (`ticketID`,`writeDate`,`userID`,`message`,`resellerID`) VALUES (?,?,?,?,?)");
        $query->execute(array($lastID,$logdate,$admin_id,$ticketText,$resellerid));
        if ($resellerid==0) {
            $query = $sql->prepare("SELECT `id`,`mail_ticket` FROM `userdata` WHERE `resellerid`='0' AND `accounttype`='a'");
            $query->execute();
        } else {
            $query = $sql->prepare("SELECT `id`,`mail_ticket` FROM `userdata` WHERE `id`=? AND `id`=`resellerid`");
            $query->execute(array($resellerid));
        }
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            if ($row['mail_ticket'] == 'Y') sendmail('emailnewticket', $row['id'],$ticketText, array($lastID,$admin_id));
        }
        $template_file = $spracheResponse->table_add;
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d', 'get') == 'md' and $ui->id('id',19, 'get')) {
    $id = $ui->id('id',19, 'get');
    if ($ui->st('action', 'get') == 'cl') {
        $template_file = 'admin_reseller_tickets_close.tpl';
    } else if ($ui->st('action', 'get') == 'op') {
        $query = $sql->prepare("UPDATE `tickets` SET `state`='R' WHERE `id`=? AND `resellerid`=? AND `state`='D' LIMIT 1");
        $query->execute(array($id,$resellerid));
        $template_file = ($query->rowCount() > 0) ? $spracheResponse->table_add : 'admin_404.tpl';
    } else if (!$ui->smallletters('action',2, 'post') or $ui->smallletters('action',2, 'get') == 'md') {
        $table = array();
        $default_language = $rSA['language'];
        $query = $sql->prepare("SELECT * FROM `tickets` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query2 = $sql->prepare("SELECT * FROM `tickets_text` WHERE `ticketID`=? AND `resellerID`=? ORDER BY `writeDate`");
        $query3 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ti' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
        $query4 = $sql->prepare("SELECT `topic` FROM `ticket_topics` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$resellerid));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $query2->execute(array($id,$resellerid));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                $table[] = array('writedate' => ($user_language == 'de') ? date('d.m.Y', strtotime($row2['writeDate'])) : date('Y-m-d', strtotime($row2['writeDate'])), 'writeTime' => date('H:i:s', strtotime($row2['writeDate'])), 'ticket' => nl2br(htmlspecialchars(stripslashes($row2['message']))),'writer' => getusername($row2['userID']));
            }
            if ($row['userPriority']==1) $priority = $sprache->priority_low;
            else if ($row['userPriority']==2) $priority = $sprache->priority_medium;
            else if ($row['userPriority']==3) $priority = $sprache->priority_high;
            else if ($row['userPriority']==4) $priority = $sprache->priority_very_high;
            else $priority = $sprache->priority_critical;
            $userPriority = $row['userPriority'];
            if ($row['state'] == 'A') $status = $sprache->status_author;
            else if ($row['state'] == 'C') $status = $sprache->status_confirmed;
            else if ($row['state'] == 'D') $status = $sprache->status_done;
            else if ($row['state'] == 'N') $status = $sprache->status_new;
            else if ($row['state'] == 'P') $status = $sprache->status_process;
            else $status = $sprache->status_reopen;
            if ($row['state'] == 'C') $open = 'N';
            else if ($row['state'] == 'D') $open='D';
            else $open = 'Y';
            if (is_numeric($row['topic'])) {
                $topic = '';
                $query3->execute(array($user_language, $row['id'],$resellerid));
                $topic = $query3->fetchColumn();
                if (empty($topic)) {
                    $query3->execute(array($default_language, $row['id'],$resellerid));
                    $topic = $query3->fetchColumn();
                }
                if (empty($topic)) {
                    $query4->execute(array($row['topic'],$resellerid));
                    $topic=stripslashes($query4->fetchColumn());
                }
                if (empty($topic)) {
                    $topic=stripslashes($row['topic']);
                }
            } else {
                $topic = $row['topic'];
            }
        }
        $lastdate = '';
        $template_file = ($ui->smallletters('action',2, 'get') == 'md') ? 'admin_reseller_tickets_md.tpl' : 'admin_reseller_tickets_view.tpl';
    } else if ($ui->smallletters('action',2, 'post') == 'wr') {
        $query = $sql->prepare("SELECT `supporter`,`state` FROM `tickets` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$resellerid));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $userid = $row['supporter'];
            $state = $row['state'];
        }
        if (isset($state) and $state!='C' and $state!='P') {
            if ($ui->id('userPriority',1, 'post')) {
                $query=($state == 'A') ? $sql->prepare("UPDATE `tickets` SET `state`='P',`userPriority`=? WHERE `id`=? AND `resellerid`=? LIMIT 1") : $sql->prepare("UPDATE `tickets` SET `userPriority`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($ui->id('userPriority',1, 'post'),$id,$resellerid));
            }
            $query = $sql->prepare("INSERT INTO `tickets_text` (`ticketID`,`message`,`writeDate`,`userID`,`resellerid`) VALUES (?,?,?,?,?)");
            $query->execute(array($id,$ui->post['ticket'],$logdate,$admin_id,$resellerid));
            if (isid($userid,10)) {
                $query = $sql->prepare("SELECT `mail_ticket` FROM `userdata` WHERE `id`=? LIMIT 1");
                $query->execute(array($userid));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['mail_ticket'] == 'Y') sendmail('emailnewticket',$userid,$ui->post['ticket'], array($id,$admin_id));
                }
            }
            $template_file = $spracheResponse->table_add;
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else if ($ui->smallletters('action',2, 'post') == 'cl' and $ui->id('rating',1, 'post')) {
        $query = $sql->prepare("UPDATE `tickets` SET `state`='C', `rating`=?,`comment`=? WHERE `id`=? AND `userid`=? AND `resellerid`=? AND `state`!='C' LIMIT 1");
        $query->execute(array($ui->id('rating',1, 'post'),$ui->description('comment', 'post'),$id,$admin_id,$resellerid));
        $template_file = $spracheResponse->table_add;
    }
} else {
    $table = array();
    $ticketLinks['all'] = 'admin.php?w=tr&amp;d=md&amp;a='.$ui->id('a',3, 'get');
    $ticketLinks['amount'] = 'admin.php?w=tr&amp;d=md';
    $ticketLinks['A'] = 'admin.php?w=tr&amp;d=md&amp;a='.$ui->id('a',3, 'get');
    $ticketLinks['C'] = 'admin.php?w=tr&amp;d=md&amp;a='.$ui->id('a',3, 'get');
    $ticketLinks['D'] = 'admin.php?w=tr&amp;d=md&amp;a='.$ui->id('a',3, 'get');
    $ticketLinks['N'] = 'admin.php?w=tr&amp;d=md&amp;a='.$ui->id('a',3, 'get');
    $ticketLinks['P'] = 'admin.php?w=tr&amp;d=md&amp;a='.$ui->id('a',3, 'get');
    $ticketLinks['R'] = 'admin.php?w=tr&amp;d=md&amp;a='.$ui->id('a',3, 'get');
    $where='WHERE t.`userid`=? AND t.`resellerid`=?';
    if (isset($ui->get['ts'])) {
        foreach ($ui->get['ts'] as $get) {
            if (preg_match('/[ACDNPR]/',$get)) $selected[] = $get;
        }
    } else {
        $selected=array('A','D','N','P','R');
    }
    $temp=' AND (';
    $i = 0;
    foreach ($selected as $get) {
        if ($i==0) $temp.="`state`='${get}'";
        else $temp.=" OR `state`='${get}'";
        $selected[] = $get;
        $i++;
    }
    $temp .= ')';
    if ($i != 0) $where .= $temp;
    foreach ($ticketLinks as $k => $v) {
        foreach (array('A','C','D','N','P','R') as $s) {
            if ((in_array($s,$selected) and $k != $s) or (!in_array($s,$selected) and $k==$s)) $ticketLinks[$k] .= '&amp;ts[]='.$s;
        }
    }
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'di') {
        $orderby = 't.`id` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = 't.`id` ASC';
    } else if ($ui->st('o', 'get') == 'dd') {
        $orderby = 't.`writedate` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = 't.`writedate` ASC';
    } else if ($ui->st('o', 'get') == 'du') {
        $orderby = 'u.`cname` DESC';
    } else if ($ui->st('o', 'get') == 'au') {
        $orderby = 'u.`cname` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = 't.`state` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = 't.`state` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = 'l.`text` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = 'l.`text` ASC';
    } else if ($ui->st('o', 'get') == 'dp') {
        $orderby = 't.`userPriority` DESC';
    } else if ($ui->st('o', 'get') == 'ap') {
        $orderby = 't.`userPriority` ASC';
    } else {
        $orderby = 't.`userPriority` DESC, t.`writedate` ASC';
    }
    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `tickets` t $where");
    $query->execute(array($admin_id,$resellerid));
    $colcount = $query->fetchColumn();
    if ($start>$colcount) {
        while ($start>0 and $start>$colcount) $start = $start - $amount;
        if ($start<0) $start = 0;
    }
    $next = $start+$amount;
    if ($colcount>$next) {
        $vor = $start+$amount;
    } else {
        $vor = $start;
    }
    $back = $start - $amount;
    if ($back>=0){
        $zur = $start - $amount;
    } else {
        $zur = $start;
    }
    $pageamount = ceil($colcount / $amount);
    $i = 1;
    $pages = array();
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="'.$ticketLinks['all'].'&amp;p=' . $selectpage . '" class="bold">' . $i . '</a>';
        } else {
            $pages[] = '<a href="'.$ticketLinks['all'].'&amp;p=' . $selectpage . '">' . $i . '</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $query = $sql->prepare("SELECT t.*,l.`text`,d.`text` AS `defaultsubject`,u.`cname` FROM `tickets` t LEFT JOIN `ticket_topics` o ON t.`topic`=o.`id` LEFT JOIN `translations` l ON o.`id`=l.`transID` AND l.`type`='ti' AND l.`lang`=? LEFT JOIN `translations` d ON t.`id`=d.`transID` AND d.`type`='ti' AND d.`lang`=? LEFT JOIN `userdata` u ON t.`supporter`=u.`id` $where ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($user_language,$default_language,$admin_id,$resellerid));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        if ($row['userPriority']==1) $priority = $sprache->priority_low;
        else if ($row['userPriority']==2) $priority = $sprache->priority_medium;
        else if ($row['userPriority']==3) $priority = $sprache->priority_high;
        else if ($row['userPriority']==4) $priority = $sprache->priority_very_high;
        else $priority = $sprache->priority_critical;
        if (is_numeric($row['topic'])) {
            $topic = '';
            if ($row['text'] != null and $row['text'] != '') {
                $topic = $row['text'];
            } else if ($row['defaultsubject'] != null or $row['defaultsubject'] != '') {
                $topic = $row['defaultsubject'];
            } else {
                $topic = $row['topic'];
            }
        } else {
            $topic = $row['topic'];
        }
        if ($user_language == 'de') {
            $writedate=date('d.m.Y H:i:s',strtotime($row['writedate']));
        } else {
            $writedate = $row['writedate'];
        }
        $statusClass='warning';
        if ($row['state'] == 'A') {
            $status = $sprache->status_author;
            $statusClass='info';
        } else if ($row['state'] == 'C') {
            $status = $sprache->status_confirmed;
            $statusClass='success';
        } else if ($row['state'] == 'D') {
            $status = $sprache->status_done;
            $statusClass='success';
        } else if ($row['state'] == 'N') {
            $status = $sprache->status_new;
        } else if ($row['state'] == 'P') {
            $status = $sprache->status_process;
        } else {
            $status = $sprache->status_reopen;
        }
        $table[] = array('id' => $row['id'], 'priority' => $priority,'writedate' => $writedate,'supporter' => $row['supporter'], 'subject' => $topic,'status' => $status,'rawState' => $row['state'], 'statusClass' => $statusClass);
    }

    configureDateTables('-1', '5, "desc"');

    $template_file = "admin_reseller_tickets_list.tpl";
}