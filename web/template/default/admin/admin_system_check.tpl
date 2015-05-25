<section class="content-header">
    <h1>Easy-WI <?php echo $gsprache->system_check;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><i class="fa fa-heartbeat"></i> <?php echo $gsprache->system_check;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3>Conjobs</h3>
                </div>

                <div class="box-body">

                    <div class="callout callout-info">
                        <p><?php echo $sprache->cron_text;?></p>
                    </div>

                    <h4><?php echo $sprache->cron_internal;?> (/etc/crontab)</h4>
                    <pre>
0 */1 * * * <?php echo $displayPHPUser;?> cd <?php echo EASYWIDIR;?> && timeout 300 php ./reboot.php >/dev/null 2>&1
*/5 * * * * <?php echo $displayPHPUser;?> cd <?php echo EASYWIDIR;?> && timeout 290 php ./statuscheck.php >/dev/null 2>&1
*/1 * * * * <?php echo $displayPHPUser;?> cd <?php echo EASYWIDIR;?> && timeout 290 php ./startupdates.php >/dev/null 2>&1
*/5 * * * * <?php echo $displayPHPUser;?> cd <?php echo EASYWIDIR;?> && timeout 290 php ./jobs.php >/dev/null 2>&1
*/10 * * * * <?php echo $displayPHPUser;?> cd <?php echo EASYWIDIR;?> && timeout 290 php ./cloud.php >/dev/null 2>&1
                    </pre>

                    <h4><?php echo $sprache->cron_internal;?> (crontab -e)</h4>
                    <pre>
0 */1 * * * cd <?php echo EASYWIDIR;?> && timeout 300 php ./reboot.php >/dev/null 2>&1
*/5 * * * * cd <?php echo EASYWIDIR;?> && timeout 290 php ./statuscheck.php >/dev/null 2>&1
*/1 * * * * cd <?php echo EASYWIDIR;?> && timeout 290 php ./startupdates.php >/dev/null 2>&1
*/5 * * * * cd <?php echo EASYWIDIR;?> && timeout 290 php ./jobs.php >/dev/null 2>&1
*/10 * * * *cd <?php echo EASYWIDIR;?> && timeout 290 php ./cloud.php >/dev/null 2>&1
                    </pre>

                    <h4><?php echo $sprache->cron_external;?> (/etc/crontab)</h4>
                    <pre>
0 */1 * * * ExternalSSH2User wget -q --no-check-certificate -O - <?php echo $pageUrl;?>reboot.php >/dev/null 2>&1
/5 * * * * ExternalSSH2User wget -q --no-check-certificate -O - <?php echo $pageUrl;?>statuscheck.php >/dev/null 2>&1
/1 * * * * ExternalSSH2User wget -q --no-check-certificate -O - <?php echo $pageUrl;?>startupdates.php >/dev/null 2>&1
/5 * * * * ExternalSSH2User wget -q --no-check-certificate -O - <?php echo $pageUrl;?>jobs.php >/dev/null 2>&1
/10 * * * * ExternalSSH2User wget -q --no-check-certificate -O - <?php echo $pageUrl;?>cloud.php >/dev/null 2>&1
                    </pre>

                    <h4><?php echo $sprache->cron_external;?> (crontab -e)</h4>
                    <pre>
0 */1 * * * wget -q --no-check-certificate -O - <?php echo $pageUrl;?>reboot.php >/dev/null 2>&1
/5 * * * * wget -q --no-check-certificate -O - <?php echo $pageUrl;?>statuscheck.php >/dev/null 2>&1
/1 * * * * wget -q --no-check-certificate -O - <?php echo $pageUrl;?>startupdates.php >/dev/null 2>&1
/5 * * * * wget -q --no-check-certificate -O - <?php echo $pageUrl;?>jobs.php >/dev/null 2>&1
/10 * * * * wget -q --no-check-certificate -O - <?php echo $pageUrl;?>cloud.php >/dev/null 2>&1
                    </pre>
                </div>
            </div>

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3>PHP Extensions</h3>
                </div>

                <div class="box-body">
                    <?php foreach ($systemCheckError as $v){ ?>
                    <?php if (is_array($v)) { ?>
                    <?php foreach ($v as $v2) { ?>
                    <div class='alert alert-danger'><?php echo $v2;?></div>
                    <?php } ?>
                    <?php } else { ?>
                    <div class='alert alert-danger'><?php echo $v;?></div>
                    <?php }}?>

                    <?php foreach ($systemCheckOk as $v){ ?>
                    <?php if (is_array($v)) { ?>
                    <?php foreach ($v as $v2) { ?>
                    <div class='alert alert-success'><?php echo $v2;?></div>
                    <?php } ?>
                    <?php } else { ?>
                    <div class='alert alert-success'><?php echo $v;?></div>
                    <?php }}?>
                </div>
            </div>
        </div>
    </div>
</section>