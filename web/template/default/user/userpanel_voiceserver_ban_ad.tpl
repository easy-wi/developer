<section class="content-header">
    <h1><?php echo $sprache->banAdd;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><i class="fa fa-ban"></i> <?php echo $sprache->banAdd;?></li>
        <li class="active"><?php echo $server;?></li>
    </ol>
</section>

<section class="content">

    <?php if (isset($error)){ ?>
    <div class="box box-danger">
        <div class="box-header">
            <i class="fa fa-warning"></i>
            <h3 class="box-title"><?php echo $gsprache->errors;?></h3>
        </div>
        <div class="box-body">
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <b><?php echo $gsprache->errors;?>:</b> <?php echo$error;?>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <form role="form" action="userpanel.php?w=vo&amp;d=bl&amp;e=ad&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputBanType"><?php echo $sprache->banType;?></label>
                            <select class="form-control" id="inputBanType" name="banType" onchange="SwitchShowHideRows(this.value)">
                                <option value="U">clientUID</option>
                                <option value="I"><?php echo $sprache->ip;?></option>
                                <option value="N"><?php echo $sprache->user;?></option>
                            </select>
                        </div>

                        <div class="U switch form-group">
                            <label class="control-label" for="inputUser">clientUID</label>
                            <select class="form-control" id="inputUser" name="clientUID">
                                <?php foreach($userList as $r) echo '<option value="'.$r['clid'].'">'.$r['client_nickname'].' ('.$r['clid'].')</option>';?>
                            </select>
                        </div>

                        <div class="I switch form-group display_none">
                            <label for="inputIP"><?php echo $sprache->ip;?></label>
                            <input class="form-control" id="inputIP" type="text" name="ip">
                        </div>

                        <div class="N switch form-group display_none">
                            <label for="inputIP"><?php echo $sprache->user;?></label>
                            <input class="form-control" id="inputIP" type="text" name="name">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputTime"><?php echo $sprache->banTime;?> (in <?php echo $sprache->seconds;?>)</label>
                            <div class="input-append">
                                <input class="form-control" id="inputTime" type="number" name="time" value="3600">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputBanReason"><?php echo $sprache->banReason;?></label>
                            <input class="form-control" id="inputBanReason" type="text" name="banReason" value="Web ban">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle"></i> <?php echo $sprache->ban;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>