<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vh"><?php echo $gsprache->hostsystem;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=vh&amp;d=ad&amp;r=vh" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group">
                <label class="control-label" for="inputReseller"><?php echo $gsprache->reseller;?></label>
                <div class="controls">
                    <select id="inputReseller" class="span11" name="reseller">
                        <option value="0"><?php echo $sprache->all;?></option>
                        <?php foreach ($table as $table_row){ ?><?php echo $table_row;?><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEsxi">ESXi</label>
                <div class="controls">
                    <select id="inputEsxi" class="span11" name="esxi">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" class="span11" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshIP"><?php echo $sprache->ssh_ip;?></label>
                <div class="controls"><input id="inputSshIP" type="text" class="span11" name="ip" value="" maxlength="15"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPort"><?php echo $sprache->ssh_port;?></label>
                <div class="controls"><input id="inputSshPort" type="number" class="span11" name="port" value="" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshUser"><?php echo $sprache->ssh_user;?></label>
                <div class="controls"><input id="inputSshUser" type="text" class="span11" name="user" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPass"><?php echo $sprache->ssh_pass;?></label>
                <div class="controls"><input id="inputSshPass" type="password" class="span11" name="pass" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                <div class="controls">
                    <select id="inputKeyUse" class="span11" name="publickey">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="B"><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyName"><?php echo $sprache->keyname;?></label>
                <div class="controls"><input id="inputKeyName" type="text" class="span11" name="keyname" maxlength="20" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                <div class="controls"><input id="inputDesc" type="text" class="span11" name="description" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCpu"><?php echo $sprache->cpu;?></label>
                <div class="controls"><input id="inputCpu" type="text" class="span11" name="cpu" value="" maxlength="30"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCores"><?php echo $sprache->cores;?></label>
                <div class="controls"><input id="inputCores" type="text" class="span11" name="cores" value="" maxlength="3"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMhz"><?php echo $sprache->mhz;?></label>
                <div class="controls"><input id="inputMhz" type="text" class="span11" name="mhz" value="" maxlength="4"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRam"><?php echo $sprache->ram;?></label>
                <div class="controls"><input id="inputRam" type="text" class="span11" name="ram" value="" maxlength="4"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxserver"><?php echo $sprache->maxserver;?></label>
                <div class="controls"><input id="inputMaxserver" type="text" class="span11" name="maxserver" value="" maxlength="4"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputThin">.vmdk thin</label>
                <div class="controls">
                    <select id="inputThin" class="span11" name="thin">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputThinQuota">.vmdk thin Quota</label>
                <div class="controls"><input id="inputThinQuota" type="text" class="span11" name="thinquota" value="" maxlength="2"><span class="add-on">%</span></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHdd"><?php echo $sprache->hdd;?></label>
                <div class="controls">
                    <textarea id="inputHdd" class="span11" name="hdd" rows="5"></textarea>
                </div>
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