<section class="content-header">
    <h1><?php echo $gsprache->webspace.' '.$gsprache->domains;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=wv"><i class="fa fa-cubes"></i> <?php echo $gsprache->webspace;?></a></li>
        <li><i class="fa fa-cog"></i> <?php echo $gsprache->domains;?></li>
        <li class="active"><?php echo $dns;?></li>
    </ol>
</section>


<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <form role="form" action="userpanel.php?w=wv&amp;d=dm&amp;id=<?php echo $id;?>&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dm">
                    <textarea id="hiddenDefaultTemplate" style="display: none;"><?php echo $vhostTemplate;?></textarea>

                    <div class="box-header">
                        <h3 class="box-title"><?php echo $gsprache->domains;?></h3>
                    </div>

                    <div class="box-body">

                        <div class="form-group">
                            <label for="defaultdns"><?php echo $sprache->defaultdns;?></label>
                            <div class="controls">
                                <input class="form-control" id="defaultdns" type="text" name="defaultdns" value="<?php echo $defaultDns; ?>" readonly>
                                <span class="help-block alert alert-info">
                                    <?php echo $sprache->help_default_dns;?>
                                </span>
                            </div>
                        </div>

                        <div id="domains">
                            <?php foreach($dnsArray as $key => $domain) { ?>
                            <div id="domain-<?php echo $key;?>" class="row" data-id="<?php echo $key;?>">
                                <div class="col-xs-5">
                                    <label for="inputDNS-<?php echo $key;?>"><?php echo $sprache->dns;?></label>
                                    <input class="form-control" id="inputDNS-<?php echo $key;?>" type="text" name="domain[<?php echo $key;?>]" value="<?php echo $domain['domain'];?>" placeholder="domain.tld" required>
                                </div>
                                <div class="col-xs-5">
                                    <label for="inputDNSPath-<?php echo $key;?>"><?php echo $sprache->path;?></label>
                                    <input class="form-control" id="inputDNSPath-<?php echo $key;?>" type="text" name="path[<?php echo $key;?>]" value="<?php echo $domain['path'];?>" placeholder="some/path/">
                                </div>
                                <div class="col-xs-2">
                                    <label for="inputButton-<?php echo $key;?>"><?php echo $gsprache->del;?></label>
                                    <div class="controls">
                                        <button class="btn btn-danger btn-sm" id="inputButton-<?php echo $key;?>" data-id="<?php echo $key;?>" onclick="removeRow(this);"><i class="fa fa-minus-circle"></i></button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <?php } ?>
                        </div>

                        <div class="row">
                            <div class="col-xs-2 col-xs-offset-10">
                                <span class="btn btn-success btn-sm" onclick="addRow();"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save"></i> <?php echo $gsprache->save;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    SwitchShowHideRows('init_ready');
</script>

<script type="text/javascript">

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
        removeAllChilds("br-" + arrayKey);
    }

    /* Yes its not nice to do it this way, but it works and the concert was about to begin */
    function returnDomainRow(id) {

        var div = document.createElement('div');
        div.id = 'domain-' + id;
        div.className = "row";
        div.dataset.id = "" + id;

        div.innerHTML = '<div class="col-xs-5"><label for="inputDNS-' + id + '"><?php echo $sprache->dns;?></label><input class="form-control" id="inputDNS-' + id + '" type="text" name="domain[' + id + ']" value="" placeholder="domain.tld" required></div>';
        div.innerHTML += '<div class="col-xs-5"><label for="inputDNSPath-' + id + '"><?php echo $sprache->path;?></label><input class="form-control" id="inputDNSPath-' + id + '" type="text" name="path[' + id + ']" value="" placeholder="some/path/"></div>';
        div.innerHTML += '<div class="col-xs-2"><label for="inputButton-' + id + '"><?php echo $gsprache->del;?></label><div class="controls"><button class="btn btn-danger btn-sm" id="inputButton-' + id + '" data-id="' + id + '" onclick="removeRow(this);"><i class="fa fa-minus-circle"></i></button></div></div>';

        return div;
    }

    function returnBr(id) {
        var br = document.createElement('br');
        br.id = 'br-' + id;
        return br;
    }

    function addRow() {

        var nextId = getNextFreeIndex();
        var domainsDiv = document.getElementById('domains');

        domainsDiv.appendChild(returnDomainRow(nextId));
        domainsDiv.appendChild(returnBr(nextId));

        $('#inputvhostTemplate-' + nextId).val($('#hiddenDefaultTemplate').val());
    }
</script>