<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=fd"><?php echo $gsprache->fastdownload;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.':'.$port;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="userpanel.php?w=fd&amp;d=es&amp;id=<?php echo $id;?>&amp;r=fd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="inputFLD"><?php echo $sprache->haupt2;?></label>
                <div class="controls">
                    <select id="inputFLD" name="masterfdl" onchange="SwitchShowHideRows('details');">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($masterfdl=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="details <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch row-fluid">
                <div class="control-group">
                    <?php echo $sprache->example;?>
                </div>
                <div class="control-group">
                    <label class="control-label" for="mfdldata"><?php echo $sprache->own2;?></label>
                    <div class="controls">
                        <input id="mfdldata" type="text" name="mfdldata" value="<?php echo $mfdldata;?>">
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i></button>
                    <input type="hidden" name="action" value="md">
                </div>
            </div>
        </form>
    </div>
</div>