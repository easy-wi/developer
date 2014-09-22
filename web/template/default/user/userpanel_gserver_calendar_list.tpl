<section class="content-header">
    <h1><?php echo $sprache->restarttime;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> </li>
        <li><?php echo $sprache->restarttime;?> </li>
        <li class="active"><?php echo $serverip.":".$port;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <!-- Content Help -->
	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_calendar;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <form class="form-inline" role="form" id="newRestart" action="userpanel.php?w=ca&amp;id=<?php echo $id;?>" method="post">

                        <input type="hidden" id="date" name="date" value="mon_0">
                        <input type="hidden" id="edit" name="edit" value="edit">

                        <div class="form-group">
                            <div class="input-group">
                                <label class="input-group-addon" for="day"><i class="fa fa-calendar"></i></label>
                                <select class="form-control" id="day" name="day" onchange="setDate();">
                                    <option value="mon"><?php echo $sprache->monday;?></option>
                                    <option value="tue"><?php echo $sprache->tuesday;?></option>
                                    <option value="wed"><?php echo $sprache->wednesday;?></option>
                                    <option value="thu"><?php echo $sprache->thursday;?></option>
                                    <option value="fri"><?php echo $sprache->friday;?></option>
                                    <option value="sat"><?php echo $sprache->saturday;?></option>
                                    <option value="sun"><?php echo $sprache->sunday;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <label class="input-group-addon" for="hour"><i class="fa fa-clock-o"></i></label>
                                <select class="form-control" id="hour" name="hour" onchange="setDate();">
                                    <?php for($i=0;$i<=23;$i++) { ?>
                                    <option value="<?php echo $i;?>"><?php echo str_pad($i,2,"0",STR_PAD_LEFT);?>:00</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="addEntry"></label>
                            <button id="addEntry" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th data-class="expand"><?php echo $gsprache->datetime;?></th>
                            <th data-hide="phone"><?php echo $gsprache->backup;?></th>
                            <th data-hide="phone"><?php echo $sprache->restarts;?></th>
                            <th data-hide="phone,tablet"><?php echo $gsprache->template;?></th>
                            <th data-hide="phone,tablet"><?php echo $sprache->startmap;?></th>
                            <th data-hide="phone"><?php echo $sprache->protect;?></th>
                            <th> </th>
                            <th> </th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php $days = array('mon'=>$sprache->monday,'tue'=>$sprache->tuesday,'wed'=>$sprache->wednesday,'thu'=>$sprache->thursday,'fri'=>$sprache->friday,'sat'=>$sprache->saturday,'sun'=>$sprache->sunday);
                        foreach($days as $day => $dayname) {
                        for($i=0;$i<=23;$i++) {
                        if(!empty($restarts[$i][$day])){ ?>
                        <tr>
                            <td>
                                <?php echo $dayname." - ".str_pad($i,2,"0",STR_PAD_LEFT).":00" ?>
                            </td>
                            <td>
                                <?php echo $restarts[$i][$day]['backup']; ?>
                            </td>
                            <td>
                                <?php echo $restarts[$i][$day]['restart']; ?>
                            </td>
                            <td>
                                <?php echo $restarts[$i][$day]['template']; ?>
                            </td>
                            <td>
                                <?php echo $restarts[$i][$day]['map']; ?>
                            </td>
                            <td>
                                <?php echo $restarts[$i][$day]['protected']; ?>
                            </td>
                            <td class="span1">
                                <form action="userpanel.php?w=ca&amp;id=<?php echo $id;?>&amp;r=ca" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                                    <input type="hidden" name="date" value="<?php echo $day.'_'.$i;?>"/>
                                    <input type="hidden" name="delete" value="delete" />
                                    <button class="btn btn-danger btn-mini"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                                </form>
                            </td>
                            <td class="span1">
                                <form action="userpanel.php?w=ca&amp;id=<?php echo $id;?>" method="post">
                                    <input type="hidden" name="date" value="<?php echo $day.'_'.$i;?>"/>
                                    <input type="hidden" name="edit" value="edit" />
                                    <button class="btn btn-primary btn-mini"><i class="fa fa-edit"></i> <?php echo $gsprache->mod;?></button>
                                </form>
                            </td>
                        </tr>
                        <?php } } } ?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function setDate() {
        var date = $('#day').val() + '_' + $('#hour').val();
        $('#date').val(date);
    }
</script>