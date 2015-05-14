<div class="form-group">
    <label for="inputMaxVhost"><?php echo $sprache->maxVhost;?></label>
    <div class="controls">
        <input class="form-control" id="inputMaxVhost" type="text" name="maxVhost" value="<?php echo $totalVhosts.'/'.$maxVhost;?>" readonly="readonly">
    </div>
</div>

<div class="form-group">
    <label for="inputMaxHDD"><?php echo $sprache->maxHDD;?></label>
    <div class="controls">
        <input class="form-control" id="inputMaxHDD" type="text" name="maxHDD" value="<?php echo $leftHDD.'/'.$maxHDD;?>" readonly="readonly">
    </div>
</div>

<?php if($quotaActive=='Y'){ ?>
<div class="form-group<?php if(isset($errors['hdd'])) echo ' has-error';?>">
    <label for="inputHDD"><?php echo $sprache->hdd;?></label>
    <div class="controls">
        <div class="input-group">
            <input class="form-control" id="inputHDD" type="number" name="hdd" value="<?php echo $maxHDD;?>">
            <span class="input-group-addon">MB</span>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($usageType == 'W') { ?>
<?php foreach($phpConfigurationMaster as $groupName => $array) { ?>
<div class="form-group">
    <label for="input<?php echo str_replace(' ', '', $groupName);?>"><?php echo $groupName;?></label>
    <div class="controls">
        <select class="form-control" id="input<?php echo str_replace(' ', '', $groupName);?>" name="<?php echo str_replace(' ', '', $groupName);?>">
            <?php foreach($array as $key => $value) { ?>
            <?php echo ($phpConfigurationVhost->$groupName == $key) ? '<option value="' . $key . '" selected="selected">' . $value . '</option>' : '<option value="' . $key . '">' . $value . '</option>'; ?>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>
<?php } ?>

<div class="form-group<?php if(isset($errors['ownVhost'])) echo ' has-error';?>">
    <label for="inputOwnVhost"><?php echo $sprache->ownVhost;?></label>
    <div class="controls">
        <select class="form-control" id="inputOwnVhost" name="ownVhost" onchange="SwitchShowHideRows(this.value,'switch',1);">
            <option value="N"><?php echo $gsprache->no;?></option>
            <option value="Y" <?php if ($ownVhost=='Y') echo 'selected="selected";'?>><?php echo $gsprache->yes;?></option>
        </select>
    </div>
</div>

<div class="Y switch form-group <?php if($ownVhost=='N') echo 'display_none';?>">
    <label for="inputvhostTemplate"><?php echo $sprache->vhostTemplate;?></label>
    <div class="controls">
        <textarea class="form-control" id="inputvhostTemplate" name="vhostTemplate" rows="20"><?php echo $vhostTemplate;?></textarea>
    </div>
</div>

<hr>
<h3><?php echo $gsprache->domains;?> <span class="btn btn-success btn-sm" onclick="addRow();"><i class="fa fa-plus-circle"></i></span></h3>
<div id="domains">

    <textarea id="hiddenDefaultTemplate" style="display: none;"><?php echo $vhostTemplate;?></textarea>

    <?php foreach($dns as $key => $domain) { ?>
    <div id="domain-<?php echo $key;?>" class="row" data-id="<?php echo $key;?>">
        <div class="col-xs-4">
            <label for="inputDNS-<?php echo $key;?>"><?php echo $sprache->dns;?></label>
            <input class="form-control" id="inputDNS-<?php echo $key;?>" type="text" name="domain[<?php echo $key;?>]" value="<?php echo $domain['domain'];?>" placeholder="domain.tld" required>
        </div>
        <div class="col-xs-4">
            <label for="inputDNSPath-<?php echo $key;?>"><?php echo $sprache->path;?></label>
            <input class="form-control" id="inputDNSPath-<?php echo $key;?>" type="text" name="path[<?php echo $key;?>]" value="<?php echo $domain['path'];?>" placeholder="some/path/">
        </div>
        <div class="col-xs-4">
            <label for="inputOwnVhost-<?php echo $key;?>"><?php echo $sprache->ownVhost;?></label>
            <select class="form-control" id="inputOwnVhost-<?php echo $key;?>" name="ownVhost[<?php echo $key;?>]" onchange="showHideVhost('<?php echo $key;?>', this.value);">
                <option value="N"><?php echo $gsprache->no;?></option>
                <option value="Y" <?php if ($domain['ownVhost']=='Y') echo 'selected="selected";'?>><?php echo $gsprache->yes;?></option>
            </select>
        </div>
    </div>
    <div id="vhostTemplateRow-<?php echo $key;?>" class="row <?php if($domain['ownVhost']=='N') echo 'display_none';?>">
        <br>
        <div class="col-xs-12">
            <label for="inputvhostTemplate-<?php echo $key;?>"><?php echo $sprache->vhostTemplate;?></label>
            <textarea class="form-control" id="inputvhostTemplate-<?php echo $key;?>" name="vhostTemplate[<?php echo $key;?>]" rows="20"><?php echo $domain['vhostTemplate'];?></textarea>
        </div>
    </div>
    <div id="rm-<?php echo $key;?>" class="row">
        <br>
        <div class="col-xs-2 col-xs-offset-10">
            <span class="btn btn-danger btn-sm" data-id="<?php echo $key;?>" onclick="removeRow(this);"><i class="fa fa-minus-circle"></i> <?php echo $sprache->dns.' '.$gsprache->del;?></span>
        </div>
    </div>
    <?php } ?>
</div>

<script type="text/javascript">
    SwitchShowHideRows('init_ready');
</script>

<script type="text/javascript">

    function showHideVhost(divId, yesNo) {

        var divContainer = document.getElementById('vhostTemplateRow-' + divId);

        if (yesNo == 'Y') {
            divContainer.style.display = "";
        } else {
            divContainer.style.display = "none";
        }
    }

    function getNextFreeIndex() {

        var domainDivs = $("div[id^='domain-']");
        var usedIndexes = [];
        var div;

        for (div in domainDivs) {
            if (domainDivs.hasOwnProperty(div)) {
                if (domainDivs[div].dataset && domainDivs[div].dataset.id) {
                    usedIndexes.push(parseInt(domainDivs[div].dataset.id));
                }
            }
        }

        var i = 0;
        while (usedIndexes.indexOf(i) > -1 && i < 1000) {
            i++;
        }

        return i;
    }

    function removeAllChilds(domainNodeId) {

        var domainNode = document.getElementById(domainNodeId);

        while (domainNode.firstChild) {
            domainNode.removeChild(domainNode.firstChild);
        }

        domainNode.remove();
    }

    function removeRow(clickedButton) {

        var arrayKey = clickedButton.dataset.id;

        removeAllChilds("domain-" + arrayKey);
        removeAllChilds("vhostTemplateRow-" + arrayKey);
        removeAllChilds("rm-" + arrayKey);
    }

    /* Yes its not nice to do it this way, but it works and the concert was about to begin */
    function returnHtml(type, id) {

        var div = document.createElement('div');
        div.id = type + '-' + id;
        div.className = "row";

        var newRow = '';

        if (type == 'domain') {

            newRow = '<br><div class="col-xs-4"><label for="inputDNS-' + id + '"><?php echo $sprache->dns;?></label><input class="form-control" id="inputDNS-' + id + '" type="text" name="domain[' + id + ']" value="" placeholder="domain.tld" required></div>';
            newRow += '<div class="col-xs-4"><label for="inputDNSPath-' + id + '"><?php echo $sprache->path;?></label><input class="form-control" id="inputDNSPath-' + id + '" type="text" name="path[' + id + ']" value="" placeholder="some/path/"></div>';
            newRow += '<div class="col-xs-4"><label for="inputOwnVhost-' + id + '"><?php echo $sprache->ownVhost;?></label><select class="form-control" id="inputOwnVhost-' + id + '" name="ownVhost[' + id + ']" onchange="showHideVhost(' + id + ', this.value);"><option value="N"><?php echo $gsprache->no;?></option><option value="Y"><?php echo $gsprache->yes;?></option></select></div>';

            div.dataset.id = "" + id;

        } else if (type == 'vhostTemplateRow') {

            newRow = '<br><div class="col-xs-12"><label for="inputvhostTemplate-' + id + '"><?php echo $sprache->vhostTemplate;?></label><textarea class="form-control" id="inputvhostTemplate-' + id + '" name="vhostTemplate[' + id + ']" rows="20"></textarea></div>';
            div.style.display = "none";

        } else if (type == 'rm') {

            newRow = '<br><div class="col-xs-2 col-xs-offset-10"><span class="btn btn-danger btn-sm" data-id="' + id + '" onclick="removeRow(this);"><i class="fa fa-minus-circle"></i> <?php echo $sprache->dns." ".$gsprache->del;?></span></div>';

        }

        div.innerHTML = newRow;

        return div;
    }

    function addRow() {

        var nextId = getNextFreeIndex();
        var domainsDiv = document.getElementById('domains');

        domainsDiv.appendChild(returnHtml('domain', nextId));
        domainsDiv.appendChild(returnHtml('vhostTemplateRow', nextId));
        domainsDiv.appendChild(returnHtml('rm', nextId));

        $('#inputvhostTemplate-' + nextId).val($('#hiddenDefaultTemplate').val());
    }
</script>