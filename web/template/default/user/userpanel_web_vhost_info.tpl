<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=wv"><?php echo $gsprache->webspace;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->fdlInfo;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $dns;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span12 alert alert-info">
        <b><?php echo $sprache->help_fdl_attention;?></b>
    </div>
</div>
<div class="row-fluid">
    <div class="span12 alert alert-info">
        <?php echo $sprache->help_fdl_hl;?>
        <textarea rows="3" class="span12"><?php echo $hlCfg;?></textarea>
    </div>
</div>
<div class="row-fluid">
    <div class="span12 alert alert-info">
        <?php echo $sprache->help_fdl_cod;?>
        <textarea rows="4" class="span12"><?php echo $codCfg;?></textarea>
    </div>
</div>