<section class="content-header">
    <h1><?php echo $sprache->header_update;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->master;?></li>
        <li class="active"><?php echo $sprache->header_update;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <form role="form" action="admin.php?w=ma&amp;d=ud" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ud">

            <div class="col-md-6">
                <div class="box box-info">

                    <div class="box-header">
                        <h3 class="box-title"><?php echo $gsprache->appRoot;?></h3>
                    </div>

                    <div class="box-body">
                        <?php foreach($appServer as $id => $server){ ?>
                        <div class="form-group">
                            <input id="inputServerID-<?php echo $id;?>" type="checkbox" name="serverID[]" value="<?php echo $id;?>">
                            <label for="inputServerID-<?php echo $id;?>"><?php echo $server['ip'].' '.$server['description'];?></label>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-info">

                    <div class="box-header">
                        <h3 class="box-title"><?php echo $gsprache->master;?></h3>
                    </div>

                    <div class="box-body">
                        <?php foreach($masterList as $id => $master){ ?>
                        <div class="form-group" id="masterID-<?php echo $id;?>" data-server="<?php echo $master['serverIDs'];?>">
                            <input id="inputMasterID-<?php echo $id;?>" type="checkbox" name="masterID[]" value="<?php echo $id;?>">
                            <label for="inputMasterID-<?php echo $id;?>"><img src="images/games/icons/<?php echo $master['shorten'];?>.png" alt="<?php echo $master['description'];?>" width="16" /> <?php echo $master['description'];?></label>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-warning" id="inputRefresh" type="submit"><i class="fa fa-refresh">&nbsp;<?php echo $gsprache->update;?></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript">
    function toggleMasterList () {

        var selectedServers = [];

        $("input[name='serverID[]']").each(function() {
            if (this.checked) {
                selectedServers[selectedServers.length] = this.value;
            }
        });

        /*
        $.each(selectedServers, function( index, value ){
            alert(value);
        });*/

        var serverString = '';
        var splitted = [];

        $("[id^='masterID-']").each(function() {

            showMaster = false;

            serverString = $(this).data("server") + '';

            splitted = serverString.split(",");

            $.each(splitted, function(key, value) {
                if (selectedServers.indexOf(value + '') > -1) {
                    showMaster = true;
                }
            });

            if (showMaster === true) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    $(function() {
        toggleMasterList();
    });

    $("input[name='serverID[]']").change(function() {
        toggleMasterList();
    });
</script>