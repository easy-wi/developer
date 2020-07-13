<div class="row-fluid">
    <div>
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=sn"><?php echo $gsprache->subnets;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_vlan;?></div>
</div>
<hr>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span6">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->subnet;?></dt>
            <dd><?php echo $subnet;?></dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=sn&amp;d=md&amp;id=<?php echo $id;?>&amp;r=sn" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group<?php if(isset($errors['active'])) echo ' error';?>">
                <label class="control-label" for="inputActive"><?php echo $gsprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputResellerID">Reseller</label>
                <div class="controls">
                    <select id="inputResellerID" name="resellerID">
                        <?php foreach($resellerList as $k=>$v){ ?>
                        <option value="<?php echo $k;?>" <?php if ($k==$ownerID) echo 'selected="selected"'; ?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['dhcpServer'])) echo ' error';?>">
                <label class="control-label" for="inputDHCP">DHCP</label>
                <div class="controls">
                    <select id="inputDHCP" name="dhcpServer">
                        <?php foreach($dhcpServers as $k=>$v){ ?>
                        <option value="<?php echo $k;?>" <?php if ($k==$dhcpServer) echo 'selected="selected"'; ?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['subnetStart'])) echo ' error';?>">
                <label class="control-label" for="inputSubnetStart"><?php echo $sprache->subnetStart;?></label>
                <div class="controls"><input id="inputSubnetStart" type="number" name="subnet" value="<?php echo $subnetStart;?>" maxlength="3" min="1" max="254" step="1"  required></div>
            </div>
            <div class="control-group<?php if(isset($errors['subnetStop'])) echo ' error';?>">
                <label class="control-label" for="inputSubnetStop"><?php echo $sprache->subnetStop;?></label>
                <div class="controls"><input id="inputSubnetStop" type="number" name="subnetStop" value="<?php echo $subnetStop;?>" maxlength="3" min="1" max="254" step="1" required></div>
            </div>
            <div class="control-group<?php if(isset($errors['netmask'])) echo ' error';?>">
                <label class="control-label" for="inputNetmask"><?php echo $sprache->netmask;?></label>
                <div class="controls"><input id="inputNetmask" type="text" name="netmask" value="<?php echo $netmask;?>" maxlength="15" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSubnetOptions"><?php echo $sprache->subnetOptions;?></label>
                <div class="controls">
                    <textarea id="inputSubnetOptions" rows="5" name="subnetOptions"><?php echo $subnetOptions;?></textarea>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['vlan'])) echo ' error';?>">
                <label class="control-label" for="inputVlan"><?php echo $sprache->vlan;?></label>
                <div class="controls">
                    <select id="inputVlan" name="vlan" onchange="textdrop('vlanName');">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($vlan=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['vlanName'])) echo ' error';?><?php if ($vlan=='N') echo ' display_none';?>" id="vlanName">
                <label class="control-label" for="inputVlanName"><?php echo $sprache->vlanName;?></label>
                <div class="controls"><input id="inputVlanName" type="text" name="vlanName" value="<?php echo $vlanName;?>"></div>
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