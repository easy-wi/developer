<?php

/**
 * File: page_download.php.
 * Author: Ulrich Block
 * Date: 26.08.13
 * Time: 00:13
 * Contact: <ulrich.block@easy-wi.com>
 * Ticket: https://github.com/easy-wi/developer/issues/11
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

if (!isset($page_include)) {
    header('Location: index.php');
    die;
}

if (isset($page_name) and isid($page_name,10)) {
    $downloadID= (int) $page_name;
} else if (isset($page_count) and isid($page_count,10)) {
    $downloadID= (int) $page_count;
} else if ($ui->id('id', 10, 'get')) {
    $downloadID = $ui->id('id', 10, 'get');
}
if (!isset($user_language) or $user_language == '') {
    $user_language=(isset($page_detect_language)) ? $page_detect_language : $rSA['language'];
}

if ((isset($page_name) and $page_name == 'get') or $ui->smallletters('action', 3, 'get') == 'get') {
    $startDownload = true;
}

if (isset($downloadID)) {

    $query = $sql->prepare("SELECT d.*,t.`text` FROM `page_downloads` d LEFT JOIN `translations` t ON t.`type`='pd' AND t.`transID`=d.`fileID` AND t.`lang`=? WHERE d.`fileID`=? LIMIT 1");
    $query->execute(array($user_language, $downloadID));

    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        if (($row['show'] == 'E' or ($row['show'] == 'A' and isset($admin_id)) or ($row['show'] == 'R' and (isset($user_id) or isset($admin_id)))) and file_exists(EASYWIDIR . "/downloads/${row['fileID']}.${row['fileExtension']}")) {

            if (isset($startDownload)) {
                $fileWithPath = EASYWIDIR . "/downloads/${row['fileID']}.${row['fileExtension']}";
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $contentType = finfo_file($finfo, $fileWithPath);
                finfo_close($finfo);
                header("Content-Type: ${contentType}");
                if (strpos(strtolower($ui->server['SERVER_SOFTWARE']),'nginx') !== false) {
                    header('Content-Length: ' . (string) (filesize($fileWithPath)));
                    header('Cache-Control: public, must-revalidate');
                    header('Pragma: no-cache');
                    header("Content-Disposition: attachment; filename=\"${row['fileName']}.${row['fileExtension']}\"");
                    header('Content-Transfer-Encoding: binary');
                    header("X-Accel-Redirect: /downloads/${row['fileID']}.${row['fileExtension']}");
                } else {
                    header("Content-Disposition: attachment; filename=\"${row['fileName']}.${row['fileExtension']}\"");
                    set_time_limit(0);
                    $fp = @fopen(EASYWIDIR . "/downloads/${row['fileID']}.${row['fileExtension']}","rb");
                    while(!feof($fp)) {
                        print(@fread($fp, 1024));
                        ob_flush();
                        flush();
                    }
                }

                $query2 = $sql->prepare("UPDATE `page_downloads` SET `count`=(`count`+1) WHERE `fileID`=? LIMIT 1");
                $query2->execute(array($downloadID));
                $query2 = $sql->prepare("INSERT INTO `page_downloads_log` (`fileID`,`date`,`ip`,`hostname`) VALUES (?,NOW(),?,?) ON DUPLICATE KEY UPDATE `fileID`=`fileID`+1");
                $query2->execute(array($downloadID, $loguserip, $userHostname));

                die;
            } else {
                $template_file = 'page_downloads_detail.tpl';
            }
        }
    }

    if (!isset($template_file)) {
        $template_file = 'page_404.tpl';
    }

} else {

    $table = array();

    $query = $sql->prepare("SELECT d.*,t.`text` FROM `page_downloads` d LEFT JOIN `translations` t ON t.`type`='pd' AND t.`transID`=d.`fileID` AND t.`lang`=? ORDER BY d.`order`,d.`fileID`");
    $query->execute(array($user_language));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (($row['show'] == 'E' or ($row['show'] == 'A' and isset($admin_id)) or ($row['show'] == 'R' and (isset($user_id) or isset($admin_id)))) and file_exists(EASYWIDIR . "/downloads/${row['fileID']}.${row['fileExtension']}")) {
            $table[] = array('id' => $row['fileID'], 'description' => $row['description'], 'link' => (isset($seo) and $seo== 'Y') ? $page_data->pages['downloads']['link'].'get/'.$row['fileID'].'/' : $page_data->pages['downloads']['link'].'&amp;action=get&amp;id='.$row['fileID'], 'text' => $row['text']);
        }
    }

    // https://github.com/easy-wi/developer/issues/62
    $langLinks = array();
    foreach ($languages as $l) {
        $tempLanguage = getlanguagefile('general', $l, 0);
        $langLinks[$l]=($page_data->seo== 'Y') ? szrp($tempLanguage->$s)  : '?s=' . $s;
    }

    $page_data->langLinks($langLinks);

    $template_file = 'page_downloads_list.tpl';
}