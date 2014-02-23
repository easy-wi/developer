<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vs"><?php echo $gsprache->virtual;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<?php if($reseller_id!=0){ ?>
<div class="row-fluid">
    <div class="span12">
        <dl class="dl-horizontal">
            <dt><?php echo $gsprache->virtual;?></dt>
            <dd><?php echo $usedservers."/".$maxvserver;?></dd>
            <dt><?php echo $sprache->ram;?></dt>
            <dd><?php echo $useduserram."/".$maxuserram;?></dd>
            <dt><?php echo $sprache->mhz;?></dt>
            <dd><?php echo $usedusercpu."/".$maxusermhz;?></dd>
        </dl>
    </div>
</div>
<?php } ?>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=vs&amp;d=ad&amp;r=vs" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group">
                <label class="control-label" for="inputHost"><?php echo $gsprache->hostsystem;?></label>
                <div class="controls">
                    <select id="inputHost" class="span12" name="hostid" onchange="getdetails('serverallocation.php?d=vs&amp;id=', this.value)">
                        <?php foreach ($table as $table_row){ ?><option value="<?php echo $table_row['id'];?>" <?php if ($table_row['id']==$bestserver) echo 'selected="selected"';?>><?php echo $table_row['ip'];?></option><?php } ?>
                    </select>
                </div>
            </div>
            <div id="information" class="row-fluid">
                <dl class="dl-horizontal">
                    <dt><?php echo $sprache->cpu;?></dt>
                    <dd><?php echo $besthostcpu;?></dd>
                    <?php if(isset($notexclusive)){ ?>
                    <dt><?php echo $sprache->hddsize;?></dt>
                    <dd><?php echo $firstfreespace." GB";?></dd>
                    <?php } else { ?>
                    <dt><?php echo $gsprache->virtual;?></dt>
                    <dd><?php echo $serverused[$bestserver]['server']."/".$maxserver;?></dd>
                    <dt><?php echo $sprache->ram;?></dt>
                    <dd><?php echo $serverused[$bestserver]['ram']."/".$ram;?></dd>
                    <dt><?php echo $sprache->cpu." ".$sprache->cores;?></dt>
                    <dd><?php foreach ($core as $core_row) { ?><?php echo $sprache->core." ".$core_row." ".$serverused[$bestserver]['cpu'][$core_row]."/".$mhz.$sprache->mhz;?><br /><?php } ?></dd>
                    <dt><?php echo $sprache->hdd;?></dt>
                    <dd><?php foreach ($hdd as $hdd_row) { ?><?php echo $hdd_row." ".$serverused[$bestserver]['hdd'][$hdd_row]."/".$mountsize[$hdd_row]." GB";?><br /><?php } ?></dd>
                    <?php } ?>
                </dl>
                <div class="control-group">
                    <label class="control-label" for="inputMount"><?php echo $sprache->mount;?></label>
                    <div class="controls">
                        <select id="inputMount" class="span12" name="mount">
                            <?php if(($admin_id==$reseller_id and !isset($notexclusive)) or $reseller_id=="0") { foreach ($hdd as $hdd_row) { ?>
                            <option><?php echo $hdd_row;?></option>
                            <?php }} else { ?>
                            <option><?php echo $firstpoint;?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputCores"><?php echo $sprache->cores;?></label>
                    <div class="controls">
                        <select id="inputCores" class="span12" name="cores">
                            <?php foreach ($add_core as $cpucore){ ?>
                            <option><?php echo $cpucore;?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUser"><?php echo $sprache->user;?></label>
                <div class="controls">
                    <select id="inputUser" class="span12" name="userid" onchange="getdetails2('serverallocation.php?d=ui&amp;id=',this.value,'userips')">
                        <?php foreach ($reseller as $k=>$v){ ?><option value="<?php echo $k;?>"><?php echo $v;?></option><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIps"><?php echo $sprache->ips;?></label>
                <div class="controls">
                    <textarea id="inputIps" class="span12" name="ips" rows="5"><?php echo $firstresellerip;?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUnusedIps"><?php echo $sprache->unused_ips;?></label>
                <div class="controls">
                    <textarea id="inputUnusedIps" class="span12" name="unusedIps" rows="5"><?php foreach ($checkedips as $ip) echo $ip."\r\n";?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHdd"><?php echo $sprache->hddsize;?> (GB)</label>
                <div class="controls"><input id="inputHdd" class="span12" type="text" name="hddsize"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMinMhz"><?php echo $sprache->minmhz;?></label>
                <div class="controls"><input id="inputMinMhz" class="span12" type="text" name="minmhz"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxMhz"><?php echo $sprache->maxmhz;?></label>
                <div class="controls"><input id="inputMaxMhz" class="span12" type="text" name="maxmhz"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRam"><?php echo $sprache->ram;?> (GB)</label>
                <div class="controls"><input id="inputRam" class="span12" type="text" name="ram"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMinRam"><?php echo $sprache->minram;?> (GB)</label>
                <div class="controls"><input id="inputMinRam" class="span12" type="text" name="minram"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxRam"><?php echo $sprache->maxram;?> (GB)</label>
                <div class="controls"><input id="inputMaxRam" class="span12" type="text" name="maxram"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>