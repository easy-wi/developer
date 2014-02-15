<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->banAdd;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $server;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=vo&amp;d=bl&amp;e=ad&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="inputBanType"><?php echo $sprache->banType;?></label>
                <div class="controls">
                    <select id="inputBanType" name="banType" onchange="SwitchShowHideRows(this.value)">
                        <option value="U">clientUID</option>
                        <option value="I"><?php echo $sprache->ip;?></option>
                        <option value="N"><?php echo $sprache->user;?></option>
                    </select>
                </div>
            </div>

            <div class="U switch control-group">
                <label class="control-label" for="inputUser">clientUID</label>
                <div class="controls">
                    <select id="inputUser" name="clientUID">
                        <?php foreach($userList as $r) echo '<option value="'.$r['clid'].'">'.$r['client_nickname'].' ('.$r['clid'].')</option>';?>
                    </select>
                </div>
            </div>
            <div class="I switch control-group display_none">
                <label class="control-label" for="inputIP"><?php echo $sprache->ip;?></label>
                <div class="controls">
                    <input id="inputIP" type="text" name="ip">
                </div>
            </div>
            <div class="N switch control-group display_none">
                <label class="control-label" for="inputIP"><?php echo $sprache->user;?></label>
                <div class="controls">
                    <input id="inputIP" type="text" name="name">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTime"><?php echo $sprache->banTime;?></label>
                <div class="controls">
                    <div class="input-append">
                        <input id="inputTime" type="number" name="time" value="3600">
                        <span class="add-on"><?php echo $sprache->seconds;?></span>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBanReason"><?php echo $sprache->banReason;?></label>
                <div class="controls">
                    <input id="inputBanReason" type="text" name="banReason" value="Web ban">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-white icon-plus-sign"></i> <?php echo $sprache->ban;?></button>
                    <input type="hidden" name="action" value="ad">
                </div>
            </div>
        </form>
    </div>
</div>