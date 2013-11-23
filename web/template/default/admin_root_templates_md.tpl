<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ot"><?php echo $gsprache->template;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $description;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=ot&amp;d=md&amp;id=<?php echo $id;?>&amp;r=ot" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                <div class="controls"><input id="inputDesc" class="span11" type="text" name="description" value="<?php echo $description;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" class="span11" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBitVersion"><?php echo $sprache->bitversion;?></label>
                <div class="controls">
                    <select id="inputBitVersion" class="span11" name="bitversion">
                        <option>64</option>
                        <option <?php if($bitversion==32) echo 'selected="selected"';?>>32</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDistro"><?php echo $sprache->distro;?></label>
                <div class="controls">
                    <select id="inputDistro" class="span11" name="distro">
                        <option value="centos">Centos</option>
                        <option value="debian5" <?php if($distro=='debian5') echo 'selected="selected"';?>>Debian</option>
                        <option value="freebsd" <?php if($distro=='freebsd') echo 'selected="selected"';?>>FreeBSD</option>
                        <option value="redhat" <?php if($distro=='redhat') echo 'selected="selected"';?>>Red Hat Linux</option>
                        <option value="suse" <?php if($distro=='suse') echo 'selected="selected"';?>>SUSE Linux</option>
                        <option value="ubuntu" <?php if($distro=='ubuntu') echo 'selected="selected"';?>>Ubuntu</option>
                        <option value="other" <?php if($distro=='other') echo 'selected="selected"';?>>Other</option>
                        <option value="other24xlinux" <?php if($distro=='other24xlinux') echo 'selected="selected"';?>>Other 2.4 Kernel Linux</option>
                        <option value="other26xlinux" <?php if($distro=='other26xlinux') echo 'selected="selected"';?>>Other 2.6 Kernel Linux</option>
                        <option value="windows7srv" <?php if($distro=='windows7srv') echo 'selected="selected"';?>>Windows 7 Server</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPXE">pxelinux.cfg</label>
                <div class="controls">
                    <textarea id="inputPXE" name="pxelinux" class="span11" rows="10"><?php echo $pxelinux;?></textarea>
                </div>
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