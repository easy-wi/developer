<?php
/**
 * File: class_page_settings.php.
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

class PageSettings {
	public $seo='',$language,$about,$canurl,$pageurl,$title,$keywords=array(),$lastnews,$tags,$pages,$hiddenPages,$last_news=array(),$MSIE=false,$lendactive,$lendactiveGS,$lendactiveVS,$lendGS=false,$lendVS=false,$protectioncheck,$pages_array=array(),$languageLinks = array();
	function __construct($user_language,$pageurl,$seo) {
		$this->language = $user_language;
		$this->pageurl = $pageurl;
		$this->seo = $seo;
		$this->canurl = $this->pageurl.'/';
	}
	private function NameToLink ($value) {
        $szrm=array('ä' => 'ae','ö' => 'oe','ü' => 'ue','Ä' => 'Ae','Ö' => 'Oe','Ü' => 'Ue','ß' => 'ss','á' => 'a','à' => 'a','Á' => 'A','À' => 'A','é' => 'e','è' => 'e','É' => 'E','È' => 'E','ó' => 'o','ò' => 'o','Ó' => 'O','Ò' => 'O','ú' => 'u','ù' => 'u','Ú' => 'U','Ù' => 'U');
        return strtolower(preg_replace('/[^a-zA-Z0-9]{1}/','-',strtr($value,$szrm)));
	}
	function SetData ($var,$value) {
		$this->$var = $value;
	}
	function AddData ($var,$value) {
		if (is_array($this->$var)) {
			if (!in_array($value,$this->$var,true)) array_push($this->$var,$value);
		} else {
			$this->$var .= ' ' . $value;
		}
	}
	function SetCanUrl ($value) {
		$this->canurl = $this->pageurl. '/' . $value;
	}
	private function AddPageToArray ($type,$pageid,$value) {
		$i = 1;
		while (isset($this->pages_array[$type]) and in_array($value,$this->pages_array[$type])) {
			$i++;
			$value = $value . '-' . $i;
		}
		$this->pages_array[$type][$pageid] = $value;
		return $value;
	}
	function SetMenu ($linkname,$request,$subid,$id=false,$listPage=true) {
		$linkname=(string)$linkname;
		$subdata = array();
		if ($this->seo== 'Y') {
			if (is_array($request)) {
				$link = '';
				foreach ($request as $r) $link .= '/' . $this->NameToLink($r);
			} else if ($id == false) {
				$link = '/' . $this->AddPageToArray('pages',$subid,$this->NameToLink($request));
			} else {
				$link = '/' . $this->AddPageToArray('pages',$id,$this->NameToLink($request));
			}
			$subdata['link'] = $this->pageurl. '/' . $this->language.$link.'/';
		} else {
			if (is_array($request)) {
				$getparams = '';
				$i = 0;
				foreach ($request as $k=>$v) {
                    if ($v != '' and $v != null) $getparams .=($i==0) ? '?'.$k.'='.$v : '&amp;'.$k.'='.$v;
					$i++;
				}
			} else if (is_numeric($request)) {
				$getparams='?site=page&amp;id='.$request;
			} else {
                $getparams='?site='.$request;
            }
			$subdata['link'] = $this->pageurl.'/index.php'.$getparams;
		}
		$subdata['href'] = '<a href="'.$subdata['link'].'" title="'.$linkname.'">'.$linkname.'</a>';
		$subdata['linkname'] = $linkname;
		if ($id == false and $listPage == true) {
			$this->pages[$subid] = $subdata;
		} else if ($listPage == true) {
			$this->pages[$subid][$id] = $subdata;
		} else if ($id == false and $listPage == false) {
            $this->hiddenPages[$subid] = $subdata;
        } else if ($listPage == false) {
            $this->hiddenPages[$subid][$id] = $subdata;
        }
	}
    function SetNewsPost ($id,$title,$text,$cutOff) {
        $this->last_news[$id]['title'] = $title;
        $this->last_news[$id]['text']=(strlen($text)<=$cutOff) ? $text : substr($text,0,$cutOff).' ...';
        $this->last_news[$id]['link']=($this->seo== 'Y') ? $this->pages['news']['link'].$this->NameToLink($title).'/' : $this->pages['news']['link'].'&amp;id='.$id;
        $this->last_news[$id]['href'] = '<a href="'.$this->last_news[$id]['link'].'" title="'.$title.'">'.$title.'</a>';
    }
	function SetLinks ($var,$linkname,$request,$id,$date=null) {
		if ($this->seo== 'Y') {
			if (is_array($request)) {
				$link = '';
				for ($i = 0;$i<(count($request)-1);$i++) $link .= '/' . $this->NameToLink($request[$i]);
				$link .= '/' . $this->AddPageToArray($var,$id,$this->NameToLink($request[$i]));
			} else {
				$link = '/' . $this->AddPageToArray($var,$id,$this->NameToLink($request));
			}
			$subdata[$id]['link'] = $this->pageurl. '/' . $this->language.$link.'/';
		} else {
			if (is_array($request)) {
				$getparams = '';
				$i = 0;
				foreach ($request as $k=>$v) {
                    if ($v != '' and $v != null) $getparams .=($i==0) ? '?'.$k.'='.$v : '&amp;'.$k.'='.$v;
					$i++;
				}
			} else {
				$getparams='?site='.$request;
			}
			$subdata[$id]['link'] = $this->pageurl.'/index.php'.$getparams;
		}
		$subdata[$id]['href'] = '<a href="'.$subdata[$id]['link'].'" title="'.$linkname.'">'.$linkname.'</a>';
		$subdata[$id]['linkname'] = $linkname;
		$this->$var = $subdata;
	}
    function setCanonicalUrl($s=null,$ID=null) {
        if ($s==null) {
            $this->canurl = $this->pageurl;
        } else {
            global $sql,$gsprache,$page_sprache;
            if ($this->seo== 'Y' and $ID != null and ($s == 'news' or $s == 'page')) {
                $query = $sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `id`=? LIMIT 1");
                $query->execute(array($ID));
                $title = $query->fetchColumn();
                $addToUrl=($s == 'news') ? '/' . $this->language. '/' . $this->NameToLink($gsprache->news). '/' . $this->NameToLink($title).'/' : '/' . $this->language. '/' . $this->NameToLink($title).'/';
            } else if ($this->seo== 'Y' and in_array($s, array('imprint','lendserver','news'))) {
                $addToUrl='/' . $this->language. '/' . $this->NameToLink($gsprache->$s).'/';
            } else if ($this->seo== 'Y') {
                $addToUrl='/' . $this->language. '/' . $this->NameToLink($page_sprache->$s).'/';
            } else if ($ID != null) {
                $addToUrl='?s='.$s.'&amp;id='.$ID;
            } else {
                $addToUrl='?s='.$s;
            }
            $this->canurl = $this->pageurl.$addToUrl;
        }
    }
    public function showLend ($admin,$user,$type) {
        global $sql;
        if ($type == 'g') {
            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `gsswitch` WHERE `resellerid`=0 AND `active`='Y' AND `lendserver`='Y' LIMIT 1");
            $query->execute();
            $count = $query->fetchColumn();
            if ($count>0 and ($this->lendactiveGS== 'B' or ($admin === true and $this->lendactiveGS!='N') or ($user === true and $this->lendactiveGS== 'R') or ($user === false and $this->lendactiveGS== 'A'))) {
                $this->lendGS = true;
                return true;
            } else if ($count>0) {
                $this->lendGS = null;
                return null;
            }
        } else {
            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_server` WHERE `resellerid`=0 AND `active`='Y' AND `lendserver`='Y' LIMIT 1");
            $query->execute();
            $count = $query->fetchColumn();
            if ($count>0 and ($this->lendactiveVS== 'B' or ($admin === true and $this->lendactiveVS!='N') or ($user === true and $this->lendactiveVS== 'R') or ($user === false and $this->lendactiveVS== 'A'))) {
                $this->lendVS = true;
                return true;
            } else if ($count>0) {
                $this->lendVS = null;
                return null;
            }
        }
        return false;
    }

    // https://github.com/easy-wi/developer/issues/62
    public function langLinks ($links=array()) {
        global $languages;
        foreach ($languages as $l) {
            if ($this->seo== 'Y') {
                $this->languageLinks[$l]=(isset($links[$l])) ? $this->pageurl. '/' . $l. '/' . $links[$l].'/' : $this->pageurl. '/' . $l.'/';
            } else {
                $this->languageLinks[$l]=(isset($links[$l])) ? $this->pageurl.'/index.php'.$links[$l].'&amp;l='.$l : $this->pageurl.'/index.php?l='.$l;
            }
        }
    }
    public function getLangLinks () {
        if (count($this->languageLinks)==0) {
            $this->langLinks();
        }
        return $this->languageLinks;
    }

	function __destruct() {
		unset($this->seo,$this->language,$this->about,$this->canurl,$this->pageurl,$this->title,$this->keywords,$this->lastnews,$this->tags,$this->pages,$this->hiddenPages,$this->last_news,$this->MSIE);
	}
}

function checkForSpam ($checkURL=null) {
    global $ui,$blockLinks,$languageFilter,$page_data,$user_language,$textID,$blockWords,$honeyPotKey,$tornevall,$sql;
    $spamReason = array();
    $ips = array();

    // Check if IP exists at DB as a spammer
    if ($checkURL==null) {
        $hostByIp = '';
        if ($ui->ip4('REMOTE_ADDR', 'server')) {
            $hostByIp=gethostbyaddr($ui->ip4('REMOTE_ADDR', 'server'));
            $ips[] = $hostByIp;
        }
        $query = $sql->prepare("SELECT COUNT(`commentID`) AS `amount` FROM `page_comments` WHERE `markedSpam`='Y' AND (`ip`=? OR `dns`=?) AND `resellerid`=0 LIMIT 1");
        $query->execute(array($ui->ip('REMOTE_ADDR', 'server'),$hostByIp));
        if ($query->fetchColumn()>0) $spamReason[] = 'IP or Host already known for spam';
    } else {
        $check=str_replace(array('https://','http://','ftps://','ftp://'),'',$checkURL);
        $ips=gethostbynamel($check);
        foreach($ips as $ip) {
            $query = $sql->prepare("SELECT COUNT(`commentID`) AS `amount` FROM `page_comments` WHERE `markedSpam`='Y' AND `ip`=? AND `resellerid`=0 LIMIT 1");
            $query->execute(array($ip));
            if ($query->fetchColumn()>0 and !in_array('IP or Host already known for spam',$spamReason)) $spamReason[] = 'IP or Host already known for spam';
        }
    }

    // reverse DNS does not add up
    if ($checkURL==null and count($spamReason)==0 and $ui->ip4('REMOTE_ADDR', 'server') and !in_array($ui->ip4('REMOTE_ADDR', 'server'),gethostbynamel($ips))) $spamReason[] = 'Fake IP';

    // hidden fields have been filled
    if ($checkURL==null and count($spamReason)==0 and strlen($ui->escaped('mail', 'post'))>0) $spamReason[] = 'XSS: Hidden field';

    // CSFR token does not add up
    if ($checkURL==null and count($spamReason)==0 and (!isset($_SESSION['news'][$textID]) or $_SESSION['news'][$textID] != $ui->escaped('token', 'post'))) $spamReason[] = 'XSS: Token';

    // Links not allowed in comments
    if ($checkURL==null and count($spamReason)==0 and $blockLinks == 'Y') {
        foreach (array('http://','https://','ftp://','ftps://') as $key) if (strpos($ui->escaped('comment', 'post'),$key) !== false and (!in_array('URL Spam',$spamReason))) $spamReason[] = 'URL Spam';
    }

    // Post contains blacklisted words
    if ($checkURL==null and count($spamReason)==0) {
        foreach (explode(',',$blockWords) as $word) {
            if (strlen(trim($word))>0 and strpos($ui->escaped('comment', 'post'),trim($word)) !== false and !in_array('Word Blacklist',$spamReason)) $spamReason[] = 'Word Blacklist';
        }
    }

    // use google translation REST API for language detection. If the current page contains a different language we likely have a spammer
    if ($checkURL==null and count($spamReason)==0 and $languageFilter== 'Y') {
        $raw=webhostRequest('translate.google.com',$page_data->pageurl,'/translate_a/t?client=x&text='.urlencode(htmlentities(substr($ui->escaped('comment', 'post'),0,200))));
        $json=json_decode($raw);
        if ($json and isset($json->src) and $json->src != $user_language) $spamReason[] = 'Language';
    }

    // check if the remote address (IP) is known for spamming at the tornevall.org list
    if (count($spamReason)==0 and ($checkURL != null or $ui->ip4('REMOTE_ADDR', 'server')) and (($honeyPotKey != null and $honeyPotKey != '') or $tornevall== 'Y')) {
        if ($checkURL != null) $ips=array($ui->ip4('REMOTE_ADDR', 'server'));
        foreach ($ips as $ip) {
            $ipRevers=implode('.', array_reverse(explode('.',$ip)));
            if (count($spamReason)==0 and $tornevall== 'Y' and (bool)checkdnsrr($ipRevers.'.opm.tornevall.org.','A')) $spamReason[] = 'IP is listed at dnsbl.tornevall.org';
            if (count($spamReason)==0 and $honeyPotKey != null and $honeyPotKey != '') {
                $ex=explode('.',gethostbyname($honeyPotKey . '.' . $ipRevers.'.dnsbl.httpbl.org'));
                if ($ex[0]==127){
                    $types=array(1=>'Suspicious',2=>'Harvester',3=>'Suspicious & Harvester',4=>'Comment Spammer',5=>'Suspicious & Comment Spammer',6=>'Harvester & Comment Spammer',7=>'Suspicious & Harvester & Comment Spammer');
                    if ($ex[3] != 0) $spamReason[] = 'IP seems to be a '.$types[$ex[3]].'. It was last seen '.$ex[1].' day(s) ago and has a threat score of '.$ex[2];
                }
            }
        }
    }
    return $spamReason;
}