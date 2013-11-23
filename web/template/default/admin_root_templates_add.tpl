<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ot"><?php echo $gsprache->template;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=ot&amp;d=ad&amp;r=ot" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                <div class="controls"><input id="inputDesc" class="span11" type="text" name="description" value="" ></div>
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
                <label class="control-label" for="inputBitVersion"><?php echo $sprache->bitversion;?></label>
                <div class="controls">
                    <select id="inputBitVersion" class="span11" name="bitversion">
                        <option>64</option>
                        <option>32</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDistro"><?php echo $sprache->distro;?></label>
                <div class="controls">
                    <select id="inputDistro" class="span11" name="distro">
                        <option value="centos">Centos</option>
                        <option value="debian5">Debian</option>
                        <option value="freebsd">FreeBSD</option>
                        <option value="redhat">Red Hat Linux</option>
                        <option value="suse">SUSE Linux</option>
                        <option value="ubuntu">Ubuntu</option>
                        <option value="other">Other</option>
                        <option value="other24xlinux">Other 2.4 Kernel Linux</option>
                        <option value="other26xlinux">Other 2.6 Kernel Linux</option>
                        <option value="windows7srv">Windows 7 Server</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPXE">pxelinux.cfg</label>
                <div class="controls">
                    <textarea id="inputPXE" name="pxelinux" class="span11" rows="10">
PROMPT 0
TIMEOUT 3
DEFAULT Linux minimal 64bit

LABEL default
kernel /rescue/vmlinuz-rescue
append initrd=/rescue/initram.igz setkmap=de dodhcp scandelay=5 boothttp=http//1.1.1.1/sysrcd.dat ar_source=http//1.1.1.1/autoruns/debian/64/ autoruns=0 ar_nowait
                    </textarea>
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