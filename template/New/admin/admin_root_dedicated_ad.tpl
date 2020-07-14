<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=rh"><?php echo $gsprache->dedicated;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=rh&amp;d=ad&amp;r=rh" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group<?php if(isset($errors['active'])) echo ' error';?>">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" class="span11" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=="N") echo 'selected="selected";'?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputReseller"><?php echo $gsprache->user.'/'.$gsprache->reseller;?></label>
                <div class="controls">
                    <select id="inputReseller" class="span11" name="userID" onchange="getdetails('ajax.php?d=freeips&userID=', this.value)">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <?php foreach ($table as $key=>$val){ ?><option value="<?php echo $key;?>" <?php if($key==$userID) echo 'selected="selected"'; ?>><?php echo $val;?></option><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputExternalID">externalID</label>
                <div class="controls"><input id="inputExternalID" type="text" class="span11" name="externalID" value="<?php echo $externalID; ?>" maxlength="255"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMac"><?php echo $sprache->mac;?></label>
                <div class="controls"><input id="inputMac" type="text" class="span11" name="mac" maxlength="17" value="<?php echo $mac; ?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['ip'])) echo ' error';?>">
                <label class="control-label" for="inputIp"><?php echo $sprache->ip;?></label>
                <div class="controls" id="information">
                    <select id="inputIp" class="span11" name="ip">
                        <?php foreach($ipsAvailable as $i){ ?>
                        <option<?php if($i==$ip) echo ' selected="selected"';?>><?php echo $i;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIps"><?php echo $sprache->ips;?></label>
                <div class="controls"><textarea id="inputIps" class="span11" name="ips" rows="5"><?php echo $ips; ?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                <div class="controls"><textarea id="inputDesc" class="span11" name="description" rows="5"><?php echo $description; ?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDHCP">DHCP</label>
                <div class="controls">
                    <select id="inputDHCP" class="span11" name="useDHCP">
                        <?php if($dhcp=='Y'){ ?><option value="Y"><?php echo $gsprache->yes;?></option><?php } ?>
                        <option value="N" <?php if($useDHCP=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPXE">PXE</label>
                <div class="controls">
                    <select id="inputPXE" class="span11" name="usePXE">
                        <?php if($pxe=='Y'){ ?><option value="Y"><?php echo $gsprache->yes;?></option><?php } ?>
                        <option value="N" <?php if($usePXE=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['active'])) echo ' error';?>">
                <label class="control-label" for="inputRestart"><?php echo $sprache->restart;?></label>
                <div class="controls">
                    <select id="inputRestart" class="span11" name="restart" onchange="SwitchShowHideRows(this.value)">
                        <option value="A">REST API</option>
                        <option value="N" <?php if($restart=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                        <!--<option value="T" <?php if($active=='T') echo 'selected="selected"'; ?>>Telejet Resetter</option>-->
                    </select>
                </div>
            </div>
            <div class="A <?php if($restart!='A') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputHTTPS">HTTPS</label>
                <div class="controls">
                    <select id="inputHTTPS" class="span11" name="https">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($https=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="A <?php if($restart!='A') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputRESTAPIURL">REST API URL</label>
                <div class="controls"><input id="inputRESTAPIURL" type="text" class="span11" name="apiURL" value="<?php echo $apiURL; ?>"></div>
            </div>
            <div class="A <?php if($restart!='A') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputAPIRequestType">API Request Type</label>
                <div class="controls">
                    <select id="inputAPIRequestType" class="span11" name="apiRequestType">
                        <option value="G">GET</option>
                        <option value="P" <?php if($apiRequestType=='P') echo 'selected="selected"'; ?>>POST</option>
                    </select>
                </div>
            </div>
            <div class="A <?php if($restart!='A') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputAPIRequestRestart">API Request Restart</label>
                <div class="controls"><textarea id="inputAPIRequestRestart" class="span11" name="apiRequestRestart" rows="5"><?php echo $apiRequestRestart; ?></textarea></div>
            </div>
            <div class="A <?php if($restart!='A') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputAPIRequestStop">API Request Stop</label>
                <div class="controls"><textarea id="inputAPIRequestStop" class="span11" name="apiRequestStop" rows="5"><?php echo $apiRequestStop; ?></textarea></div>
            </div>
            <?php foreach(customColumns('S') as $row){ ?>
            <div class="control-group">
                <label class="control-label" for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                <div class="controls"><input id="inputCustom-<?php echo $row['customID'];?>" type="text" name="<?php echo $row['name'];?>" class="span11" value=""></div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>