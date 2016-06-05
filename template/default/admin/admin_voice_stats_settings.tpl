<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->voice.' '.$sprache->graphsettings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=vu&amp;d=se&amp;r=vu" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group error">
                <label class="control-label" for="inputTextColourRed"><?php echo $sprache->text_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputTextColourRed" type="number" name="text_colour_1" value="<?php echo $text_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputTextColourGreen"><?php echo $sprache->text_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputTextColourGreen" type="number" name="text_colour_2" value="<?php echo $text_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputTextColourBlue"><?php echo $sprache->text_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputTextColourBlue" type="number" name="text_colour_3" value="<?php echo $text_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputBarInColourRed"><?php echo $sprache->bar_slots_usage_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputBarInColourRed" type="number" name="barin_colour_1" value="<?php echo $barin_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputBarInColourGreen"><?php echo $sprache->bar_slots_usage_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputBarInColourGreen" type="number" name="barin_colour_2" value="<?php echo $barin_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputBarInColourBlue"><?php echo $sprache->bar_slots_usage_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputBarInColourBlue" type="number" name="barin_colour_3" value="<?php echo $barin_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputBarOutColourRed"><?php echo $sprache->bar_slots_total_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputBarOutColourRed" type="number" name="barout_colour_1" value="<?php echo $barout_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputBarOutColourGreen"><?php echo $sprache->bar_slots_total_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputBarOutColourGreen" type="number" name="barout_colour_2" value="<?php echo $barout_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputBarOutColourBlue"><?php echo $sprache->bar_slots_total_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputBarOutColourBlue" type="number" name="barout_colour_3" value="<?php echo $barout_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputBarTrafficColourRed"><?php echo $sprache->bartotal_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputBarTrafficColourRed" type="number" name="bartraffic_colour_1" value="<?php echo $bartraffic_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputBarTrafficColourGreen"><?php echo $sprache->bartotal_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputBarTrafficColourGreen" type="number" name="bartraffic_colour_2" value="<?php echo $bartraffic_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputBarTrafficColourBlue"><?php echo $sprache->bartotal_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputBarTrafficColourBlue" type="number" name="bartraffic_colour_3" value="<?php echo $bartraffic_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputBGColourRed"><?php echo $sprache->bg_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputBGColourRed" type="number" name="bg_colour_1" value="<?php echo $bg_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputBGColourGreen"><?php echo $sprache->bg_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputBGColourGreen" type="number" name="bg_colour_2" value="<?php echo $bg_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputBGColourBlue"><?php echo $sprache->bg_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputBGColourBlue" type="number" name="bg_colour_3" value="<?php echo $bg_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputBorderColourRed"><?php echo $sprache->border_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputBorderColourRed" type="number" name="border_colour_1" value="<?php echo $border_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputBorderColourGreen"><?php echo $sprache->border_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputBorderColourGreen" type="number" name="border_colour_2" value="<?php echo $border_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputBorderColourBlue"><?php echo $sprache->border_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputBorderColourBlue" type="number" name="border_colour_3" value="<?php echo $border_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group error">
                <label class="control-label" for="inputLineColourRed"><?php echo $sprache->line_colour.' '.$sprache->red;?></label>
                <div class="controls"><input id="inputLineColourRed" type="number" name="line_colour_1" value="<?php echo $line_colour_1?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group info">
                <label class="control-label" for="inputLineColourGreen"><?php echo $sprache->line_colour.' '.$sprache->green;?></label>
                <div class="controls"><input id="inputLineColourGreen" type="number" name="line_colour_2" value="<?php echo $line_colour_2?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group success">
                <label class="control-label" for="inputLineColourBlue"><?php echo $sprache->line_colour.' '.$sprache->blue;?></label>
                <div class="controls"><input id="inputLineColourBlue" type="number" name="line_colour_3" value="<?php echo $line_colour_3?>" min="0" max="255" step="1" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls"><button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button></div>
            </div>
        </form>
    </div>
</div>