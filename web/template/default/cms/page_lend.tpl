<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li><?php echo $page_data->pages['lendserver']['href'];?> <span class="divider">/</li>
            <li class="active"><?php if($servertype=='g'){ echo $page_data->pages['lendservergs']['linkname'];}else{ echo $page_data->pages['lendservervoice']['linkname'];}?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span5">
    <?php if(isset($header)) echo "<div id=\"redirect\"><img src=\"images/16_notice.png\" alt=\"notice\" /> $text </div>"; ?>
<?php if ($servertype=='g' and $gslallowed==true) { ?>
<form class="form-horizontal" action="<?php echo $page_data->pages['lendservergs']['link'];?>" method="post">
    <h2 class="form-horizontal-heading"><?php echo $gsprache->lendserver.' '.$gsprache->gameserver; ?></h2>
    <p><?php echo $sprache->nextfree.' '.$nextfree." ".$sprache->minutes;?></p>
    <p><?php echo $sprache->nextcheck.' '.$nextcheck.' '.$sprache->minutes;?></p>
    <?php foreach ($status as $key=>$value){ ?>
    <p><?php echo $key.': '.$sprache->available.' '.$value['amount'].'/'.$value['total'];?></p>
    <?php } ?>
    <?php if ($serveravailable==true) { ?>
    <div class="control-group">
        <label class="control-label" for="inputGame"><?php echo $gssprache->game;?></label>
        <div class="controls">
            <select name="game" id="inputGame">
                <?php foreach($gameselect as $key=>$option) echo '<option value="'.$key.'">'.$option.'</option>';?>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputSlots"><?php echo $gssprache->slots;?></label>
        <div class="controls">
            <select name="slots" id="inputSlots">
                <?php foreach($slotselect as $option) echo '<option>'.$option.'</option>';?>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputTime"><?php echo $sprache->maxtime;?></label>
        <div class="controls">
            <select name="time" id="inputTime">
                <?php foreach($timeselect as $option) echo '<option>'.$option.'</option>';?>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputRcon"><?php echo $gssprache->rcon;?></label>
        <div class="controls">
            <input name="rcon" type="text" id="inputRcon" value="<?php echo $rcon;?>" pattern="[0-9a-zA-Z]{3,20}" required>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPassword"><?php echo $gssprache->password;?></label>
        <div class="controls">
            <input name="password" type="text" id="inputPassword" value="<?php echo $password;?>" pattern="[0-9a-zA-Z]{3,20}" required>
        </div>
    </div>
    <?php if ($ftpupload=='Y') { ?>
    <div class="control-group">
        <label class="control-label" for="inputDemo"><?php echo $sprache->ftpuploadpath;?></label>
        <div class="controls">
            <input name="ftpuploadpath" type="text" id="inputDemo" value="ftp://username:password@1.1.1.1/demos" pattern="^(ftp|ftps):\/\/([\w\.\:\/\-\_]{1,}:[\w]{1,}|[\w]{1,})@[\w\.\:\/\-\_]{1,}$" >
        </div>
    </div>
    <?php } ?>
    <?php } ?>
    <?php } else if ($volallowed==true) { ?>
    <form class="form-horizontal" action="<?php echo $page_data->pages['lendservervoice']['link'];?>" method="post">
        <h2 class="form-horizontal-heading"><?php echo $gsprache->lendserver.' '.$gsprache->voiceserver; ?></h2>
        <p><?php echo $sprache->nextfreevo.' '.$nextfree." ".$sprache->minutes;?></p>
        <p><?php echo $sprache->nextcheck.' '.$nextcheck.' '.$sprache->minutes;?></p>
        <?php if ($serveravailable==true) { ?>
        <div class="control-group">
            <label class="control-label" for="inputSlots"><?php echo $vosprache->slots;?></label>
            <div class="controls">
                <select name="slots" id="inputSlots">
                    <?php foreach($voslotselect as $option) echo '<option>'.$option.'</option>';?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputTime"><?php echo $sprache->maxtime;?></label>
            <div class="controls">
                <select name="time" id="inputTime">
                    <?php foreach($votimeselect as $option) echo '<option>'.$option.'</option>';?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputPassword"><?php echo $gssprache->password;?></label>
            <div class="controls">
                <input name="password" type="text" id="inputPassword" value="<?php echo $password;?>" pattern="[0-9a-zA-Z]{3,20}" required>
            </div>
        </div>
        <div class="hide" aria-hidden="true"><input type="hidden" name="voice" value="1" ></div>
        <?php } ?>
        <?php } ?>
        <?php if ($serveravailable==true) { ?>
        <div class="control-group">
            <label class="control-label" for="inputSubmit"></label>
            <div class="controls">
                <button class="btn btn-primary" id="inputSubmit" type="submit"><?php echo $sprache->lend; ?></button>
            </div>
        </div>
        <div class="hide" aria-hidden="true">
            <input type="text" name="email">
        </div>
        <?php }?>
    </form>
    </div>
</div>