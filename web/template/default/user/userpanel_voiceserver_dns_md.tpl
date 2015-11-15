<section class="content-header">
    <h1>TS3 DNS <?php echo $gsprache->mod;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><a href="userpanel.php?w=vd"><i class="fa fa-link"></i> TS3 DNS</a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $defaultdns; ?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <form role="form" action="userpanel.php?w=vd&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="dnsIP"><?php echo $gsprache->externalIP;?></label>
                            <div class="controls">
                                <input id="dnsIP" type="text" class="form-control" value="<?php echo $dnsIp;?>" readonly>
                                <span class="help-block alert alert-info">
                                    <?php echo $sprache->help_tsdns_external_ip;?>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="defaultDNS"><?php echo $sprache->defaultdns;?></label>
                            <div class="controls">
                                <input id="defaultDNS" type="text" class="form-control" value="<?php echo $defaultdns;?>" readonly>
                                <span class="help-block alert alert-info">
                                    <?php echo $sprache->help_tsdns_default_dns;?>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="dns"><?php echo $sprache->dns;?></label>
                            <input class="form-control" id="dns" type="text" name="dns" value="<?php echo $dns;?>" required>
                        </div>

                        <div class="form-group">
                            <label for="ip"><?php echo $sprache->ip;?></label>
                            <input class="form-control" id="ip" type="text" name="ip" value="<?php echo $ip;?>" required>
                        </div>

                        <div class="form-group">
                            <label for="port"><?php echo $sprache->port;?></label>
                            <input class="form-control" id="port" type="text" name="port" value="<?php echo $port;?>" required>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>