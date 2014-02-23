<?php

/**
 * File: jobs.php.
 * Author: Ulrich Block
 * Date: 26.05.12
 * Time: 10:56
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

if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $timelimit = ini_get('max_execution_time') - 10;
} else {
    $timelimit = 600;
}
$deamon = false;
if (isset($argv)) {
    $args = array();
    foreach ($argv as $a) {
        if ($a == 'deamon') {
            $deamon = true;
        } else if (is_numeric($a)) {
            $sleep = $a;
        } else {
            $e=explode(':',$a);
            if (isset($e[1])) $args[$e[0]] = $e[1];
        }
    }

    if ($deamon == false) {
        print 'Running job management as cronjob' . "\r\n";
        set_time_limit($timelimit);
    } else {
        print 'Running job management as Deamon' . "\r\n";
    }

    if (!isset($sleep)) {
        $sleep = 60;
    }
}

define('EASYWIDIR', dirname(__FILE__));

include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/class_rootserver.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/methods/class_ts3.php');
include(EASYWIDIR . '/stuff/methods/functions_ts3.php');
include(EASYWIDIR . '/stuff/methods/mysql_functions.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

if (!isset($ip) or $ui->escaped('SERVER_ADDR', 'server') == $ip or in_array($ip, ipstoarray($rSA['cronjob_ips']))) {
    $gsprache = getlanguagefile('general', 'uk', 0);

    class runGraph {

        private $jobsDone = 0, $startTime = 0, $newLine = "\r\n", $jobCount = 0, $spinnerCount = 0, $spinners = array('-', '/', '-', '\\', '|', '/', '-', '\\', '|', '/'), $spinner = '-', $oneJobPercent = 1;

        function __construct($jobCount,$newLine) {

            $this->startTime=strtotime('now');
            $this->jobCount = $jobCount;

            $this->oneJobPercent = ($jobCount > 0) ? 100 / $jobCount : 100;

            $this->newLine = $newLine;
            $this->startTime = strtotime('now');

        }

        public function updateCount($jobCount) {
            $this->jobCount = $jobCount;
            $this->oneJobPercent = ($jobCount > 0) ? 100 / $jobCount : 100;
        }

        public function printGraph ($newCommand) {

            $this->jobsDone = $this->jobsDone + 1;
            $percentDone = number_format($this->jobsDone * $this->oneJobPercent, 2);
            $elapsedSeconds = strtotime('now') - $this->startTime;

            print $this->spinner . ' ' . $percentDone . '% done; ' . $elapsedSeconds . ' Seconds elapsed; Last job: ' . $newCommand.$this->newLine;
            flush();

            $this->runSpinner();

        }

        private function runSpinner () {
            if ($this->newLine == "\r") {

                if ($this->spinnerCount < 9) {
                    $this->spinnerCount++;
                } else {
                    $this->spinnerCount = 0;
                }

                $this->spinner = $this->spinners[$this->spinnerCount] . ' ';

            } else {
                $this->spinner = '';
            }
        }

        function __destruct() {
            $this->jobsDone = null;
            $this->startTime = null;
            $this->newLine = null;
            $this->jobCount = null;
            $this->spinnerCount = null;
            $this->spinners = null;
            $this->spinner = null;
            $this->oneJobPercent = null;
            unset($this->jobsDone,$this->startTime,$this->newLine,$this->jobCount,$this->spinnerCount,$this->spinners,$this->spinner,$this->oneJobPercent);
        }
    }

    $runJobs = true;

    if (isset($ip)) {
        $newLine = "\r\n";
    } else {
        $newLine = "\r";
    }

    $counJobs = $sql->prepare("SELECT COUNT(`jobID`) AS `jobCount` FROM `jobs` WHERE `status` IS NULL OR `status`='1'");
    while ($runJobs == true) {

        $counJobs->execute();
        $jobCount = $counJobs->rowCount();
        print 'Total jobs open: ' . $jobCount . '. Cleaning up outdated and duplicated entries' . "\r\n";
        updateStates('dl','us');
        updateStates('dl');
        updateStates('ad');
        updateStates('md');

        $counJobs->execute();
        $jobCount = $counJobs->rowCount();
        print "\r\n" . 'Total jobs open after cleanup: ' . $jobCount . "\r\n";
        print 'Executing user cleanup jobs' . "\r\n";
        $startTime = strtotime('now');
        $theOutput = new runGraph($jobCount,$newLine);

        # us > vo > gs > my > vs
        include(EASYWIDIR . '/stuff/jobs/jobs_user.php');
        $counJobs->execute();
        $jobCount = $counJobs->rowCount();
        $theOutput->updateCount($jobCount);
        print "\r\n" . 'Total jobs open after user cleanup jobs are done: ' . $jobCount . "\r\n";

        print 'Executing voice jobs' . "\r\n";
        include(EASYWIDIR . '/stuff/jobs/jobs_voice.php');
        $counJobs->execute();
        $jobCount = $counJobs->rowCount();
        $theOutput->updateCount($jobCount);
        print "\r\n" . 'Total jobs open after voice jobs are done: ' . $jobCount . "\r\n";

        print 'Executing TS DNS jobs' . "\r\n";
        include(EASYWIDIR . '/stuff/jobs/jobs_tsdns.php');
        $counJobs->execute();
        $jobCount = $counJobs->rowCount();
        $theOutput->updateCount($jobCount);
        print "\r\n" . 'Total jobs open after TS DNS jobs are done: ' . $jobCount . "\r\n";

        print 'Executing mysql jobs' . "\r\n";
        include(EASYWIDIR . '/stuff/jobs/jobs_mysql.php');
        $counJobs->execute();
        $jobCount = $counJobs->rowCount();
        $theOutput->updateCount($jobCount);
        print "\r\n" . 'Total jobs open after mysql jobs are done: ' . $jobCount . "\r\n";

        print 'Executing gameserver jobs' . "\r\n";
        include(EASYWIDIR . '/stuff/jobs/jobs_gserver.php');
        $counJobs->execute();
        $jobCount = $counJobs->rowCount();
        $theOutput->updateCount($jobCount);
        print "\r\n" . 'Total jobs open after gameserver jobs are done: ' . $jobCount . "\r\n";

        print 'Executing root server jobs' . "\r\n";
        include(EASYWIDIR . '/stuff/jobs/jobs_roots.php');
        $counJobs->execute();
        $jobCount = $counJobs->rowCount();
        $theOutput->updateCount($jobCount);
        print "\r\n" . 'Total jobs open after root server jobs are done: ' . $jobCount . "\r\n";

        print 'Executing user remove jobs' . "\r\n";
        include(EASYWIDIR . '/stuff/jobs/jobs_user_rm.php');
        print "\n";

        if ($deamon == true) {
            $sql = null;
            $theOutput = null;
            unset($sql,$theOutput);
            if ($dbConnect['type'] == 'mysql') {
                $sql=new PDO($dbConnect['connect'],$dbConnect['user'],$dbConnect['pwd'], array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8") );
            } else {
                $sql=new PDO($dbConnect['connect'],$dbConnect['user'],$dbConnect['pwd']);
            }
            if ($dbConnect['debug']==1) {
                $sql->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            }
            flush();
            print "\r\n" . 'Deamon run finished. Current memory usage is: '.memory_get_usage().' Bytes. Waiting ' . $sleep.' seconds before next job run'."\r\n\r\n";
            sleep($sleep);
        } else {
            $runJobs = false;
        }
        $query = $sql->prepare("UPDATE `settings` SET `lastCronJobs`=UNIX_TIMESTAMP()");
        $query->execute();
    }
}

