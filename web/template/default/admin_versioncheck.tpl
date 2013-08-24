<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->versioncheck;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11 alert <?php if($state==1) echo 'alert-block'; else echo 'alert alert-success';?>">
        <h4><?php echo $gsprache->versioncheck;?></h4>
        <?php echo $isok;?>
    </div>
</div>
<?php if($reseller_id==0){ ?>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><?php echo $sprache->contractTime;?></th>
                <th><?php echo $sprache->updates;?>:</th>
                <th data-hide="phone"><?php echo $sprache->licenceAmount;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->installed.' '.$gsprache->gameserver;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->installed.' '.$gsprache->voiceserver;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->installed.' '.$gsprache->virtual;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->type;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->contract;?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $contractTime;?></td>
                <td><?php echo $updates;?></td>
                <td><?php echo $licenceDetails['count'].' / '.$licenceDetails['s'];?></td>
                <td><?php echo $licenceDetails['gsCount'];?></td>
                <td><?php echo $licenceDetails['voCount'];?></td>
                <td><?php echo $licenceDetails['vCount'];?></td>
                <td><?php echo $type;?></td>
                <td><?php echo $contract;?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php } else { ?>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><?php echo $sprache->licenceAmount.' '.$gsprache->virtual;?></th>
                <th><?php echo $sprache->licenceAmount.' '.$gsprache->gameserver;?></th>
                <th data-hide="phone"><?php echo $sprache->licenceAmount.' '.$gsprache->voiceserver;?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $licenceDetails['vCount'].' / '.$licenceDetails['mVs'];?></td>
                <td><?php echo $licenceDetails['gsCount'].' / '.$licenceDetails['mG'];?></td>
                <td><?php echo $licenceDetails['voCount'].' / '.$licenceDetails['mVo'];?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>
<hr>
<div class="row-fluid">
    <h3><?php echo $vcsprache->changelog;?></h3>
</div>
<?php foreach($table as $changelog) { ?>
<div class="row-fluid">
    <div class="span11">
        <h4><?php echo $changelog['version'];?></h4>
        <?php echo $changelog['text'];?>
        <hr>
    </div>
</div>
<?php } ?>