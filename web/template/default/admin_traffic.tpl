<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->traffic;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <dl>
            <dt><?php echo $gsprache->traffic." ".$display;?></dt>
            <dd><?php echo $startdate." - ".$stopdate;?></dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=tf" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="inputFormat"><?php echo $sprache->dmy;?></label>
                <div class="controls">
                    <select id="inputFormat" name="dmy">
                        <option value="da" <?php if ($dmy=='da') echo 'selected="selected"'?>><?php echo $sprache->days;?></option>
                        <option value="mo" <?php if ($dmy=='mo') echo 'selected="selected"'?>><?php echo $sprache->months;?></option>
                        <option value="ye" <?php if ($dmy=='ye') echo 'selected="selected"'?>><?php echo $sprache->years;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRange"><?php echo $sprache->range;?></label>
                <div class="controls form-inline">
                    <input class="span2" name="daystart" type="number" min="1" max="31" step="1" value="<?php echo $day; ?>">
                    <input class="span2" name="monthstart" type="number" min="1" max="12" step="1" value="<?php echo $month; ?>">
                    <input class="span2" name="yearstart" type="number" min="2011" max="2200" step="1" value="<?php echo $year; ?>">
                    -
                    <input class="span2" name="daystop" type="number" min="1" max="31" step="1" value="<?php echo $daystop; ?>">
                    <input class="span2" name="monthstop" type="number" min="1" max="12" step="1" value="<?php echo $monthstop; ?>">
                    <input class="span2" name="yearstop" type="number" min="2011" max="2200" step="1" value="<?php echo $yearstop; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputStats"><?php echo $gsprache->stats;?></label>
                <div class="controls">
                    <select id="inputStats" name="kind" onchange="getdetails('serverallocation.php?d=tr&amp;w=',this.value)">
                        <option value="al"><?php echo $sprache->all;?></option>
                        <option value="su" <?php if ($kind=='su') echo 'selected="selected"'?>><?php echo $sprache->subnets;?></option>
                        <?php if ($reseller_id==0) {?><option value="rs" <?php if ($kind=='rs') echo 'selected="selected"'?>><?php echo $gsprache->reseller;?></option><?php } ?>
                        <?php if ($reseller_id==0 or $reseller_id==$admin_id) {?><option value="us" <?php if ($kind=='us') echo 'selected="selected"'?>><?php echo $sprache->user;?></option><?php } ?>
                        <option value="se" <?php if ($kind=='se') echo 'selected="selected"'?>><?php echo $sprache->server;?></option>
                        <option value="ip" <?php if ($kind=='ip') echo 'selected="selected"'?>><?php echo $sprache->ips;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSelect"></label>
                <div id="information" class="controls">
                    <?php if(isset($ui->post['kind']) and $ui->post['kind']!="al"){ ?>
                    <select id="inputSelect" name="what">
                        <?php foreach ($data as $value) echo $value;?>
                    </select>
                    <?php } ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls"><button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button></div>
            </div>
        </form>
    </div>
</div>
<div class="row-fluid">
    <div class="span11"><img src="<?php echo $trafficdata;?>" alt="Stats" /></div>
</div>