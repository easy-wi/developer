<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vs"><?php echo $gsprache->virtual;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->user;?></dt>
            <dd><?php echo $cname;?></dd>
            <dt><?php echo $sprache->ssh_pass;?></dt>
            <dd><?php echo $pass;?></dd>
            <dt><?php echo $sprache->mount;?></dt>
            <dd><?php echo $mountpoint;?></dd>
            <dt><?php echo $gsprache->template;?></dt>
            <dd><?php echo $description." ".$bitversion;?> Bit</dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=vs&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vs" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <?php if(($licenceDetails['lVs']>'0' and $active=="N") or ($licenceDetails['lVs']>='0' and $active=="Y")) { ?><option value="Y"><?php echo $gsprache->yes;?></option><?php } ?>
                        <option value="N" <?php if ($active=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIp"><?php echo $sprache->ip;?></label>
                <div class="controls"><input id="inputIp" type="text" name="ip" value="<?php echo $ip;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIps"><?php echo $sprache->ips;?></label>
                <div class="controls"><textarea id="inputIps" name="ips" rows="5"><?php echo $ips;?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUsedIps"><?php echo $sprache->unused_ips;?></label>
                <div class="controls"><textarea id="inputUsedIps" name="ips" rows="5"><?php foreach ($checkedips as $ip) echo $ip."\r\n";?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMac"><?php echo $sprache->mac;?></label>
                <div class="controls"><input id="inputMac" type="text" name="mac" value="<?php echo $mac;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCores"><?php echo $sprache->cores;?></label>
                <div class="controls">
                    <select id="inputCores" name="cores">
                        <?php foreach ($cpucores as $cpucore){ ?><option <?php if($cpucore==$cores) echo 'selected="selected"';?>><?php echo $cpucore;?></option><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMinMhz"><?php echo $sprache->minmhz;?></label>
                <div class="controls"><input id="inputMinMhz" type="text" name="minmhz" value="<?php echo $minmhz;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxMhz"><?php echo $sprache->maxmhz;?></label>
                <div class="controls"><input id="inputMaxMhz" type="text" name="maxmhz" value="<?php echo $maxmhz;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRam"><?php echo $sprache->ram;?></label>
                <div class="controls"><input id="inputRam" type="text" name="ram" value="<?php echo $ram;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMinRam"><?php echo $sprache->minram;?></label>
                <div class="controls"><input id="inputMinRam" type="text" name="minram" value="<?php echo $minram;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxRam"><?php echo $sprache->maxram;?></label>
                <div class="controls"><input id="inputMaxRam" type="text" name="maxram" value="<?php echo $maxram;?>" ></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHddSize"><?php echo $sprache->hddsize;?></label>
                <div class="controls"><input id="inputHddSize" type="text" name="hddsize" value="<?php echo $hddsize;?>" ></div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>