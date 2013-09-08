<?php
/**
 * File: images.php.
 * Author: Ulrich Block
 * Date: 03.10.12
 * Time: 17:09
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


define('EASYWIDIR',dirname(__FILE__));
include(EASYWIDIR."/stuff/functions.php");
include(EASYWIDIR.'/stuff/class_validator.php');
include(EASYWIDIR.'/stuff/vorlage.php');
include(EASYWIDIR."/stuff/settings.php");
if (isset($admin_id) and $ui->st('img','get')) {
    $pa=User_Permissions($admin_id);
    if ($ui->st('img','get')=='tr' and ($pa['traffic'] or $pa['root'])) {
        $values=array();
        $query=$sql->prepare("SELECT `multiplier`,`text_colour_1`,`text_colour_2`,`text_colour_3`,`barin_colour_1`,`barin_colour_2`,`barin_colour_3`,`barout_colour_1`,`barout_colour_2`,`barout_colour_3`,`bartotal_colour_1`,`bartotal_colour_2`,`bartotal_colour_3`,`bg_colour_1`,`bg_colour_2`,`bg_colour_3`,`border_colour_1`,`border_colour_2`,`border_colour_3`,`line_colour_1`,`line_colour_2`,`line_colour_3` FROM `traffic_settings` LIMIT 1");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
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
        if (isset($server_id) and $list_gtype!="" and $start>0) {
            $i=0;
            $stop=$list_gtype;
            if ($d=="md" or $d=="da") {
                $starttime = strtotime("$start-$server_port-$server_id");
            } else if ($d=="mo") {
                $starttime = strtotime("$start-$server_port");
            } else if ($d=="ye") {
                $starttime = strtotime("$start");
            }
            while ($i<$stop) {
                if ($d=="md" or $d=="da") {
                    $day1=date('Y-m-d',strtotime("+$i day",$starttime));
                } else if ($d=="mo") {
                    $day1=date('Y-m',strtotime("+$i month",$starttime));
                } else if ($d=="ye") {
                    $day1=date('Y',strtotime("+$i year",$starttime));
                }
                if ($day1<=date('Y-m-d')) {
                    $like=$day1."%";
                    if ($w=="mb") {
                        $divisor=(1024 * 1024);
                        $rounder=0;
                    } else if ($w=="tb") {
                        $divisor=(1024 * 1024 * 1024 * 1024);
                        $rounder=6;
                    } else {
                        $divisor=(1024 * 1024 * 1024);
                        $rounder=2;
                    }
                    if ($d=="md" or $d=="ho") {
                        $day2=date('d.m.Y H',strtotime($day1));
                    } else if ($d=="da") {
                        $day2=date('d.m.Y',strtotime($day1));
                    } else if ($d=="mo") {
                        $day2=date('m.Y',strtotime($day1));
                    } else if ($d=="ye") {
                        $day2=date('Y',strtotime($day1));
                    }
                    if ($reseller_id==0 and isset($get_shorten)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `serverid`=:get_shorten");
                        $query->execute(array(':like' => $like,':get_shorten'=>$get_shorten));
                    } else if ($reseller_id!=0 and isset($get_shorten)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `serverid`=:get_shorten AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':get_shorten'=>$get_shorten,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0 and isset($get_distro)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `userid`=:resellerid");
                        $query->execute(array(':like' => $like,':resellerid' => $get_distro));
                    } else if ($reseller_id!=0 and $reseller_id==$admin_id and isset($get_distro)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `userid`=:get_distro AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':get_distro' => $get_distro,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0 and isset($get_short)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `resellerid`=:get_short");
                        $query->execute(array(':like' => $like,':get_short'=>$get_short));;
                    } else if ($reseller_id!=0 and $reseller_id!=$admin_id and isset($server_ips)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `ip` LIKE :server_ips AND `userid`=:admin_id AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':server_ips' => $server_ips."%",':reseller_id'=>$reseller_id,':admin_id'=>$admin_id));
                    } else if ($reseller_id!=0 and $reseller_id==$admin_id and isset($server_ips)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `ip` LIKE :server_ips AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':server_ips' => $server_ips."%",':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0 and isset($server_ips)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `ip` LIKE :server_ips");
                        $query->execute(array(':like' => $like,':server_ips'=>$server_ips."%"));
                    } else if ($reseller_id!=0 and $reseller_id!=$admin_id and isset($server_ip)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `ip`=:server_ip AND `userid`=:admin_id AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':server_ip' => $server_ip,':admin_id'=>$admin_id,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id!=0 and $reseller_id==$admin_id and isset($server_ip)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `ip`=:server_ip AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':server_ip' => $server_ip,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0 and isset($server_ip)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `ip`=:server_ip");
                        $query->execute(array(':like' => $like,':server_ip'=>$server_ip));
                    } else if ($reseller_id!=0 and $reseller_id!=$admin_id) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `userid`=:admin_id AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':admin_id'=>$admin_id,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id!=0 and $reseller_id==$admin_id) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data` WHERE `day` LIKE :like");
                        $query->execute(array(':like' => $like));
                    }
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        if (isset($row['ingoing']) or isset($row['outgoing']) or isset($row['total'])) {
                            if (!isset($row['ingoing'])) $ingoing="0";
                            if (!isset($row['outgoing'])) $outgoing="0";
                            if (!isset($row['total'])) $total="0";
                            $ingoing=($multiplier * $row['ingoing']) / $divisor;
                            $outgoing=($multiplier * $row['outgoing']) / $divisor;
                            $total=($multiplier * $row['total']) / $divisor;
                            $values[$day2]=array($ingoing,$outgoing,$total);
                        }
                    }
                    if ($reseller_id==0 and isset($get_shorten)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `serverid`=:get_shorten");
                        $query->execute(array(':like' => $like,':get_shorten'=>$get_shorten));
                    } else if ($reseller_id!=0 and isset($get_shorten)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `serverid`=:get_shorten AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':get_shorten'=>$get_shorten,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0 and isset($get_distro)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `userid`=:resellerid");
                        $query->execute(array(':like' => $like,':resellerid' => $get_distro));
                    } else if ($reseller_id!=0 and $reseller_id==$admin_id and isset($get_distro)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `userid`=:get_distro AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':get_distro' => $get_distro,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0 and isset($get_short)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `resellerid`=:get_short");
                        $query->execute(array(':like' => $like,':get_short'=>$get_short));;
                    } else if ($reseller_id!=0 and $reseller_id!=$admin_id and isset($server_ips)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `ip` LIKE :server_ips AND `userid`=:admin_id AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':server_ips' => $server_ips."%",':reseller_id'=>$reseller_id,':admin_id'=>$admin_id));
                    } else if ($reseller_id!=0 and $reseller_id==$admin_id and isset($server_ips)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `ip` LIKE :server_ips AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':server_ips' => $server_ips."%",':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0 and isset($server_ips)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `ip` LIKE :server_ips");
                        $query->execute(array(':like' => $like,':server_ips'=>$server_ips."%"));
                    } else if ($reseller_id!=0 and $reseller_id!=$admin_id and isset($server_ip)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `ip`=:server_ip AND `userid`=:admin_id AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':server_ip' => $server_ip,':admin_id'=>$admin_id,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id!=0 and $reseller_id==$admin_id and isset($server_ip)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `ip`=:server_ip AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':server_ip' => $server_ip,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0 and isset($server_ip)) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `ip`=:server_ip");
                        $query->execute(array(':like' => $like,':server_ip'=>$server_ip));
                    } else if ($reseller_id!=0 and $reseller_id!=$admin_id) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `userid`=:admin_id AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':admin_id'=>$admin_id,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id!=0 and $reseller_id==$admin_id) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like AND `resellerid`=:reseller_id");
                        $query->execute(array(':like' => $like,':reseller_id'=>$reseller_id));
                    } else if ($reseller_id==0) {
                        $query=$sql->prepare("SELECT SUM(`in`) AS `ingoing`,SUM(`out`) AS `outgoing`,SUM(`in`)+SUM(`out`) AS `total` FROM `traffic_data_day` WHERE `day` LIKE :like");
                        $query->execute(array(':like' => $like));
                    }
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        $ingoing=($multiplier * $row2['ingoing']) / $divisor;
                        $outgoing=($multiplier * $row2['outgoing']) / $divisor;
                        $total=($multiplier * $row2['total']) / $divisor;
                        if (isset($values[$day2])) {
                            $ingoing=$ingoing+$values[$day2][0];
                            $outgoing=$outgoing+$values[$day2][1];
                            $total=$total+$values[$day2][2];
                        }
                        $values[$day2]=array($ingoing,$outgoing,$total);
                    }
                }
                $i++;
            }
        }
        foreach($values as $value) {
            $max_values[]=max($value);
        }
        $total_bars=count($max_values);
        if ($total_bars>0) {
            $img_width=725;
            $margintop=30;
            $marginbottom=30;
            $marginleft=100;
            $marginright=40;
            $bar_heigth=10;
            $spacing1=2;
            $spacing2=20;
            $textspacingleft=($marginleft / 10) + 2;
            $img_height=$margintop + $marginbottom + ($total_bars * 3 * $bar_heigth) + ((( $total_bars * 3 ) - $total_bars) * $spacing1 ) + ( ( $total_bars - 1 ) * $spacing2 );
            $graph_width=$img_width - ( $marginleft + $marginright );
            $img=imagecreate($img_width,$img_height);
            $text_color=imagecolorallocate($img,$text_colour_1,$text_colour_2,$text_colour_3);
            $bar_in=imagecolorallocate($img,$barin_colour_1,$barin_colour_2,$barin_colour_3);
            $bar_out=imagecolorallocate($img,$barout_colour_1,$barout_colour_2,$barout_colour_3);
            $bar_total=imagecolorallocate($img,$bartotal_colour_1,$bartotal_colour_2,$bartotal_colour_3);
            $background_color=imagecolorallocate($img,$bg_colour_1,$bg_colour_2,$bg_colour_3);
            $border_color=imagecolorallocate($img,$border_colour_1,$border_colour_2,$border_colour_3);
            $line_color=imagecolorallocate($img,$line_colour_1,$line_colour_2,$line_colour_3);
            $max_value=max($max_values);
            if ($max_value==0) {
                $max_value=0.000001;
            }
            $ratio = $graph_width / $max_value;
            imagefilledrectangle($img,0,0,$img_width,$img_height,$background_color);
            $lines=10;
            $vertical_gap=$graph_width/$lines;
            $i=1;
            while($i<=$lines){
                if ($max_value>=10) {
                    $key=round(($max_value / $lines ) * ($lines - $i));
                } else if ($max_value>=1 and $max_value<10) {
                    $key=round(($max_value / $lines ) * ($lines - $i),1);
                } else if ($max_value<1 and $max_value>=0.01) {
                    $key=round(($max_value / $lines ) * ($lines - $i),2);
                } else if ($max_value<0.01) {
                    $key=round(($max_value / $lines ) * ($lines - $i),4);
                }
                $x=$img_width - $marginright - $vertical_gap * $i ;
                imageline($img,$x,$margintop,$x,$img_height-$marginbottom,$line_color);
                imagestring($img,$x,$x,$spacing1,$key,$border_color);
                imagestring($img,$x,$x,$img_height-$marginbottom+$spacing1,$key,$border_color);
                $v=intval($vertical_gap * $i /$ratio);
                $i++;
            }
            $i="0";
            $more="0";
            while ($i<$total_bars) {
                foreach ($values as $key=>$array) {
                    $i2="0";
                    foreach ($array as $amount) {
                        $x1=$marginleft;
                        $x2=$marginleft + $amount * $ratio ;
                        $y1=$margintop + $i * $bar_heigth ;
                        $y1new=$y1+$more;
                        $y1=$y1new;
                        $y2= $y1 + $bar_heigth;
                        if ($i2>"0" and ($i/2)==($i/$i2)) {
                            $morenew=$more+$spacing2;
                            $more=$morenew;
                        } else {
                            $morenew=$more+$spacing1;
                            $more=$morenew;
                        }
                        if ($amount>=10) {
                            $display=round($amount);
                        } else if ($amount>=1 and $amount<10) {
                            $display=round($amount,1);
                        } else if ($amount<1 and $amount>=0.01) {
                            $display=round($amount,2);
                        } else if ($amount<0.01) {
                            $display=round($amount,4);
                        }
                        imagestring($img,0,$x2+5,$y1,$display,$text_color);
                        if ($i2=="0") {
                            imagestring($img,$y1+$spacing1+$bar_heigth,2,$y1+$spacing1+$bar_heigth,$key,$text_color);
                        }
                        if ($i2=="0") {
                            imagefilledrectangle($img,$x1,$y1,$x2,$y2,$bar_in);
                        } else if ($i2=="1") {
                            imagefilledrectangle($img,$x1,$y1,$x2,$y2,$bar_out);
                        } else if ($i2=="2") {
                            imagefilledrectangle($img,$x1,$y1,$x2,$y2,$bar_total);
                        }
                        $i++;
                        $i2++;
                    }
                }
            }
            header("Content-type:image/png");
            imagepng($img);
        }
    } else if ($ui->st('img','get')=='vo' and ($pa['voicemasterserver'] or $pa['voiceserver'] or $pa['root'])) {
        $values=array();
        $pselect=$sql->prepare("SELECT * FROM `voice_stats_settings` WHERE `resellerid`=? LIMIT 1");
        $pselect->execute(array($reseller_id));
        foreach ($pselect->fetchall() as $row) {
            $text_colour_1=$row['text_colour_1'];
            $text_colour_2=$row['text_colour_2'];
            $text_colour_3=$row['text_colour_3'];
            $barin_colour_1=$row['barin_colour_1'];
            $barin_colour_2=$row['barin_colour_2'];
            $barin_colour_3=$row['barin_colour_3'];
            $barout_colour_1=$row['barout_colour_1'];
            $barout_colour_2=$row['barout_colour_2'];
            $barout_colour_3=$row['barout_colour_3'];
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
        if (isset($server_id) and $list_gtype!="" and $start>0) {
            $i=0;
            $stop=$list_gtype;
            if ($d=="md" or $d=="to") {
                $stop=23;
                $starttime = strtotime("$start-$server_port-$server_id");
                $now=date('Y-m-d H');
            } else if ($d=="da") {
                $starttime = strtotime("$start-$server_port-$server_id");
                $now=date('Y-m-d');
            } else if ($d=="mo") {
                $starttime = strtotime("$start-$server_port");
                $now=date('Y-m-d');
            } else if ($d=="ye") {
                $starttime = strtotime("$start");
                $now=date('Y-m-d');
            }
            while ($i<$stop) {
                if ($d=="md" or $d=="to") {
                    $day1=date('Y-m-d H',strtotime("+$i hour",$starttime));
                } else if ($d=="da") {
                    $day1=date('Y-m-d',strtotime("+$i day",$starttime));
                } else if ($d=="mo") {
                    $day1=date('Y-m',strtotime("+$i month",$starttime));
                } else if ($d=="ye") {
                    $day1=date('Y',strtotime("+$i year",$starttime));
                }
                if ($day1<=$now) {
                    $like=$day1."%";
                    if ($d=="md" or $d=="to") {
                        $day2=date('H',strtotime($day1.':00:00')).':00:00';
                    } else if ($d=="da") {
                        $day2=date('d.m.Y',strtotime($day1));
                    } else if ($d=="mo") {
                        $day2=date('m.Y',strtotime($day1));
                    } else if ($d=="ye") {
                        $day2=date('Y',strtotime($day1));
                    }
                    if (isset($get_shorten)) {
                        $pselect=$sql->prepare("SELECT SUM(`used`)/COUNT(`sid`) AS `averageused`,SUM(`installed`)/COUNT(`sid`) AS `averageinstalled` FROM `voice_server_stats` WHERE `date` LIKE ? AND `sid`=? AND `resellerid`=?");
                        $pselect->execute(array($like,$get_shorten,$reseller_id));
                    } else if (isset($get_distro)) {
                        $pselect=$sql->prepare("SELECT SUM(`used`)/COUNT(`sid`)*COUNT(DISTINCT(`sid`)) AS `averageused`,SUM(`installed`)/COUNT(`sid`)*COUNT(DISTINCT(`sid`)) AS `averageinstalled` FROM `voice_server_stats` WHERE `date` LIKE ? AND `uid`=? AND `resellerid`=?");
                        $pselect->execute(array($like,$get_distro,$reseller_id));
                    } else if (isset($get_short)) {
                        $pselect=$sql->prepare("SELECT SUM(`used`)/COUNT(`sid`)*COUNT(DISTINCT(`sid`)) AS `averageused`,SUM(`installed`)/COUNT(`sid`)*COUNT(DISTINCT(`sid`)) AS `averageinstalled` FROM `voice_server_stats` WHERE `date` LIKE ? AND `mid`=? AND `resellerid`=?");
                        $pselect->execute(array($like,$get_short,$reseller_id));
                    } else {
                        $pselect=$sql->prepare("SELECT SUM(`used`)/COUNT(`sid`)*COUNT(DISTINCT(`sid`)) AS `averageused`,SUM(`installed`)/COUNT(`sid`)*COUNT(DISTINCT(`sid`)) AS `averageinstalled` FROM `voice_server_stats` WHERE `date` LIKE ? AND `resellerid`=?");
                        $pselect->execute(array($like,$reseller_id));
                    }
                    foreach ($pselect->fetchall() as $row) {
                        if (!isset($row['averageused'])) $averageused="0";
                        else $averageused=round($row['averageused']);
                        if (!isset($row['averageinstalled'])) $averageinstalled="0";
                        else $averageinstalled=round($row['averageinstalled']);
                        $values[$day2]=array($averageused,$averageinstalled);
                    }
                    /*if (isset($get_shorten)) {
                        $pselect=$sql->prepare("SELECT SUM(`used`)/COUNT(`id`) AS `averageused`,SUM(`installed`)/COUNT(`id`) AS `averageinstalled` FROM `voice_server_stats_hours` WHERE `date` LIKE ? AND `sid`=? AND `resellerid`=?");
                        $pselect->execute(array($like,$get_shorten,$reseller_id));
                    } else if (isset($get_distro)) {
                        $pselect=$sql->prepare("SELECT SUM(`used`)/COUNT(`id`)*COUNT(DISTINCT(`sid`)) AS `averageused`,SUM(`installed`)/COUNT(`id`)*COUNT(DISTINCT(`sid`)) AS `averageinstalled` FROM `voice_server_stats_hours` WHERE `date` LIKE ? AND `uid`=? AND `resellerid`=?");
                        $pselect->execute(array($like,$get_distro,$reseller_id));
                    } else if (isset($get_short)) {
                        $pselect=$sql->prepare("SELECT SUM(`used`)/COUNT(`id`)*COUNT(DISTINCT(`sid`)) AS `averageused`,SUM(`installed`)/COUNT(`id`)*COUNT(DISTINCT(`sid`)) AS `averageinstalled` FROM `voice_server_stats_hours` WHERE `date` LIKE ? AND `mid`=? AND `resellerid`=?");
                        $pselect->execute(array($like,$get_short,$reseller_id));
                    } else {
                        $pselect=$sql->prepare("SELECT SUM(`used`)/COUNT(`id`)*COUNT(DISTINCT(`sid`)) AS `averageused`,SUM(`installed`)/COUNT(`id`)*COUNT(DISTINCT(`sid`)) AS `averageinstalled` FROM `voice_server_stats_hours` WHERE `date` LIKE ? AND `resellerid`=?");
                        $pselect->execute(array($like,$reseller_id));
                    }
                    foreach ($pselect->fetchall() as $row) {
                        if (!isset($row['averageused'])) $averageused="0";
                        else $averageused=round($row['averageused']);
                        if (!isset($row['averageinstalled'])) $averageinstalled="0";
                        else $averageinstalled=round($row['averageinstalled']);
                        $values[$day2]=array($averageused,$averageinstalled);
                    }*/
                }
                $i++;
            }
        }
        $max_values=array();
        foreach($values as $value) {
            $max_values[]=max($value);
        }
        $total_bars=count($max_values);
        if ($total_bars>0) {
            $img_width=725;
            $margintop=30;
            $marginbottom=30;
            $marginleft=100;
            $marginright=40;
            $bar_heigth=10;
            $spacing1=2;
            $spacing2=20;
            $textspacingleft=($marginleft / 10) + 2;
            $img_height=$margintop + $marginbottom + ($total_bars * 2 * $bar_heigth) + ((( $total_bars * 2 ) - $total_bars) * $spacing1 ) + ( ( $total_bars - 1 ) * $spacing2 );
            $graph_width=$img_width - ( $marginleft + $marginright );
            $img=imagecreate($img_width,$img_height);
            $text_color=imagecolorallocate($img,$text_colour_1,$text_colour_2,$text_colour_3);
            $bar_in=imagecolorallocate($img,$barin_colour_1,$barin_colour_2,$barin_colour_3);
            $bar_out=imagecolorallocate($img,$barout_colour_1,$barout_colour_2,$barout_colour_3);
            $background_color=imagecolorallocate($img,$bg_colour_1,$bg_colour_2,$bg_colour_3);
            $border_color=imagecolorallocate($img,$border_colour_1,$border_colour_2,$border_colour_3);
            $line_color=imagecolorallocate($img,$line_colour_1,$line_colour_2,$line_colour_3);
            $max_value=max($max_values);
            if ($max_value==0) {
                $max_value=0.000001;
            }
            $ratio = $graph_width / $max_value;
            imagefilledrectangle($img,0,0,$img_width,$img_height,$background_color);
            $lines=10;
            $vertical_gap=$graph_width/$lines;
            $i=1;
            while ($i<=$lines) {
                $key=round(($max_value / $lines ) * ($lines - $i));
                $x=$img_width - $marginright - $vertical_gap * $i ;
                imageline($img,$x,$margintop,$x,$img_height-$marginbottom,$line_color);
                imagestring($img,$x,$x,$spacing1,$key,$border_color);
                imagestring($img,$x,$x,$img_height-$marginbottom+$spacing1,$key,$border_color);
                $v=intval($vertical_gap * $i /$ratio);
                $i++;
            }
            $i="0";
            $more="0";
            while ($i<$total_bars) {
                foreach ($values as $key=>$array) {
                    $i2="0";
                    foreach ($array as $amount) {
                        $x1=$marginleft;
                        $x2=$marginleft + $amount * $ratio ;
                        $y1=$margintop + $i * $bar_heigth ;
                        $y1new=$y1+$more;
                        $y1=$y1new;
                        $y2= $y1 + $bar_heigth;
                        if ($i2==1) {
                            $morenew=$more+$spacing2;
                            $more=$morenew;
                        } else {
                            $morenew=$more+$spacing1;
                            $more=$morenew;
                        }
                        $display=round($amount);
                        imagestring($img,0,$x2+5,$y1,$display,$text_color);
                        if ($i2=="0") {
                            imagestring($img,$y1+$spacing1+$bar_heigth,2,$y1+$spacing1,$key,$text_color);
                        }
                        if ($i2=="0") {
                            imagefilledrectangle($img,$x1,$y1,$x2,$y2,$bar_in);
                        } else if ($i2=="1") {
                            imagefilledrectangle($img,$x1,$y1,$x2,$y2,$bar_out);
                        }
                        $i++;
                        $i2++;
                    }
                }
            }
            header("Content-type:image/png");
            imagepng($img);
        }
    }
} else if (!$ui->st('img','get')) {
    $randompass=passwordgenerate(4);
    $_SESSION['captcha']=md5($randompass);
    $captcha=$randompass;
    $bildhoehe=20;
    $bildbreite=40;
    $bild=imagecreate($bildbreite, $bildhoehe);
    imagecolorallocate($bild, 255, 255, 255);
    $text=imagecolorallocate($bild, 0, 0, 0);
    $font=20;
    $a=2;
    $x=floor($bildbreite/strlen($captcha))-0;
    for($b=0; $b < strlen($captcha); $b++) {
        $c=mt_rand(2,$bildhoehe-20);
        imagestring ($bild,$font,$a,$c, $captcha{$b}, $text);
        $a=$a+$x;
    }
    # get errors and throw away to ensure captcha display
    $errors=ob_get_clean();

    header("Content-type: image/png");
    imagepng($bild);
}
$sql=null;