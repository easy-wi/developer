<section class="content-header">
    <h1>Easy-WI <?php echo $gsprache->databases.' E-Mail '.' '.$gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li><i class="fa fa-database"></i> Easy-WI <?php echo $gsprache->databases;?></li>
        <li class="active">E-Mail <?php echo $gsprache->template;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <form role="form" action="admin.php?w=bu&amp;d=re&amp;r=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                <input type="hidden" name="action" value="re">

                <div class="box box-primary">
                    <div class="box-body">

                        <div class="box-header">
                            <h3 class="box-title">E-Mail <?php echo $gsprache->template;?></h3>
                        </div>

                        <div class="box-body">

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailbackup" name="templates[]" value="emailbackup">
                                    <?php echo $gsprache->backup.' '.$gssprache->create;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailbackuprestore" name="templates[]" value="emailbackuprestore">
                                    <?php echo $gsprache->backup.' '.$gssprache->recover;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emaildown" name="templates[]" value="emaildown">
                                    <?php echo $sprache->emaildown;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emaildownrestart" name="templates[]" value="emaildownrestart">
                                    <?php echo $sprache->emaildownrestart;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailgserverupdate" name="templates[]" value="emailgserverupdate">
                                    <?php echo $gsprache->gameserver.' '.$gsprache->master.' '.$gsprache->update;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailvoicemasterold" name="templates[]" value="emailvoicemasterold">
                                    <?php echo $gsprache->voiceserver.' '.$gsprache->master.' '.$gsprache->update;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailnewticket" name="templates[]" value="emailnewticket">
                                    <?php echo $sprache->emailnewticket;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailpwrecovery" name="templates[]" value="emailpwrecovery">
                                    <?php echo $sprache->emailpasswordrecovery;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailregister" name="templates[]" value="emailregister">
                                    <?php echo $gsprache->user.' '.$gsprache->registration;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailsecuritybreach" name="templates[]" value="emailsecuritybreach">
                                    <?php echo $sprache->emailsecuritybreach;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailuseradd" name="templates[]" value="emailuseradd">
                                    <?php echo $gsprache->user.' '.$gsprache->add;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailvinstall" name="templates[]" value="emailvinstall">
                                    <?php echo $gsprache->virtual.' '.$gsprache->add;?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputTemplate-emailvrescue" name="templates[]" value="emailvrescue">
                                    <?php echo $gsprache->virtual;?> Rescue
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'templates[]')">
                                    <?php echo $gsprache->all;?>
                                </label>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-play-circle"></i> <?php echo $gsprache->exec;?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>