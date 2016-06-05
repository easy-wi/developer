<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->root;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->traffic.' '.$gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=tf&amp;d=se&amp;r=tf" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <h5><?php echo $sprache->databaseacces;?></h5>
            <div class="control-group">
                <label class="control-label" for="inputType"><?php echo $sprache->type;?></label>
                <div class="controls"><input id="inputType" type="text" name="type" value="<?php echo $type?>" maxlength="15" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputStatIP"><?php echo $sprache->statip;?></label>
                <div class="controls"><input id="inputStatIP" type="text" name="statip" value="<?php echo $statip?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDbName"><?php echo $sprache->dbname;?></label>
                <div class="controls"><input id="inputDbName" type="text" name="dbname" value="<?php echo $dbname?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDbUser"><?php echo $sprache->dbuser;?></label>
                <div class="controls"><input id="inputDbUser" type="text" name="dbuser" value="<?php echo $dbuser?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDbPassword"><?php echo $sprache->dbpassword;?></label>
                <div class="controls"><input id="inputDbPassword" type="text" name="dbpassword" value="<?php echo $dbpassword?>" required></div>
            </div>
            <h5><?php echo $sprache->databasestructure;?></h5>
            <div class="control-group">
                <label class="control-label" for="inputcolumn_sourceip"><?php echo $sprache->column_sourceip;?></label>
                <div class="controls"><input id="inputcolumn_sourceip" type="text" name="column_sourceip" value="<?php echo $column_sourceip?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputtable_name"><?php echo $sprache->table_name;?></label>
                <div class="controls"><input id="inputtable_name" type="text" name="table_name" value="<?php echo $table_name?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputcolumn_destip"><?php echo $sprache->column_destip;?></label>
                <div class="controls"><input id="inputcolumn_destip" type="text" name="column_destip" value="<?php echo $column_destip?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputcolumn_byte"><?php echo $sprache->column_byte;?></label>
                <div class="controls"><input id="inputcolumn_byte" type="text" name="column_byte" value="<?php echo $column_byte?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputcolumn_date"><?php echo $sprache->column_date;?></label>
                <div class="controls"><input id="inputcolumn_date" type="text" name="column_date" value="<?php echo $column_date?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputmultiplier"><?php echo $sprache->multiplier;?></label>
                <div class="controls"><input id="inputmultiplier" type="text" name="multiplier" value="<?php echo $multiplier?>" required></div>
            </div>
            <h5><?php echo $sprache->graphsettings;?></h5>
            <div class="control-group error">
                <label class="control-label" for="inputtext_colourRed"><?php echo $sprache->text_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputtext_colourRed" type="number" name="text_colour_1" value="<?php echo $text_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputtext_colourBlue"><?php echo $sprache->text_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputtext_colourBlue" type="number" name="text_colour_2" value="<?php echo $text_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputtext_colourGreen"><?php echo $sprache->text_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputtext_colourGreen" type="number" name="text_colour_3" value="<?php echo $text_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputbarin_colourRed"><?php echo $sprache->barin_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputbarin_colourRed" type="number" name="barin_colour_1" value="<?php echo $barin_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputbarin_colourBlue"><?php echo $sprache->barin_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputbarin_colourBlue" type="number" name="barin_colour_2" value="<?php echo $barin_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputbarin_colourGreen"><?php echo $sprache->barin_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputbarin_colourGreen" type="number" name="barin_colour_3" value="<?php echo $barin_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputbarout_colourRed"><?php echo $sprache->barout_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputbarout_colourRed" type="number" name="barout_colour_1" value="<?php echo $barout_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputbarout_colourBlue"><?php echo $sprache->barout_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputbarout_colourBlue" type="number" name="barout_colour_2" value="<?php echo $barout_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputbarout_colourGreen"><?php echo $sprache->barout_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputbarout_colourGreen" type="number" name="barout_colour_3" value="<?php echo $barout_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputbartotal_colourRed"><?php echo $sprache->bartotal_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputbartotal_colourRed" type="number" name="bartotal_colour_1" value="<?php echo $bartotal_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputbartotal_colourBlue"><?php echo $sprache->bartotal_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputbartotal_colourBlue" type="number" name="bartotal_colour_2" value="<?php echo $bartotal_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputbartotal_colourGreen"><?php echo $sprache->bartotal_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputbartotal_colourGreen" type="number" name="bartotal_colour_3" value="<?php echo $bartotal_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputbg_colourRed"><?php echo $sprache->bg_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputbg_colourRed" type="number" name="bg_colour_1" value="<?php echo $bg_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputbg_colourBlue"><?php echo $sprache->bg_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputbg_colourBlue" type="number" name="bg_colour_2" value="<?php echo $bg_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputbg_colourGreen"><?php echo $sprache->bg_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputbg_colourGreen" type="number" name="bg_colour_3" value="<?php echo $bg_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputborder_colourRed"><?php echo $sprache->border_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputborder_colourRed" type="number" name="border_colour_1" value="<?php echo $border_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputborder_colourBlue"><?php echo $sprache->border_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputborder_colourBlue" type="number" name="border_colour_2" value="<?php echo $border_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputborder_colourGreen"><?php echo $sprache->border_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputborder_colourGreen" type="number" name="border_colour_3" value="<?php echo $border_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputline_colourRed"><?php echo $sprache->line_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputline_colourRed" type="number" name="line_colour_1" value="<?php echo $line_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputline_colourBlue"><?php echo $sprache->line_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputline_colourBlue" type="number" name="line_colour_2" value="<?php echo $line_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputline_colourGreen"><?php echo $sprache->line_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputline_colourGreen" type="number" name="line_colour_3" value="<?php echo $line_colour_3?>" min="0" max="255" step="1" required></div>
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