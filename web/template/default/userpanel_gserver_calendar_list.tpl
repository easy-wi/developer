<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $sprache->restarttime;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.":".$port;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_calendar;?></div>
</div>
<hr>

<div class="row-fluid">
	<div class="span12">
		<form id="newRestart" action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>" method="post" class="form-inline">
            <input type="hidden" id="date" name="date" value=""/>
            <input type="hidden" id="edit" name="edit" value="edit" />
            <select id="day" name="day">
                <option value="mon"><?php echo $sprache->monday;?></option>
                <option value="tue"><?php echo $sprache->tuesday;?></option>
                <option value="wed"><?php echo $sprache->wednesday;?></option>
                <option value="thu"><?php echo $sprache->thursday;?></option>
                <option value="fri"><?php echo $sprache->friday;?></option>
                <option value="sat"><?php echo $sprache->saturday;?></option>
                <option value="sun"><?php echo $sprache->sunday;?></option>
            </select>
            <select id="hour" name="hour">
            	<?php for($i=0;$i<=23;$i++) { ?>
                <option value="<?php echo $i;?>"><?php echo str_pad($i,2,"0",STR_PAD_LEFT);?>:00</option>
                <?php } ?>
            </select>
            <button class="btn btn-primary"><i class="icon-edit icon-white"></i> <?php echo $gsprache->add;?></button>
        </form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function (){
	$('#newRestart').submit(function() {
		var date=$('#day').val()+'_'+$('#hour').val();
		$('#date').val(date);
	});		
});
</script>  
<br>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th><?php echo $gsprache->time;?></th>
					<th><?php echo $gsprache->template;?></th>
					<th><?php echo $sprache->map;?></th>
					<th><?php echo $sprache->restart;?></th>
					<th><?php echo $gsprache->backup;?></th>
					<th><?php echo $sprache->protect;?></th>
					<th> </th>
					<th> </th>
				</td>
			</thead>
            <tbody>
				<?php
				$days=array('mon'=>$sprache->monday,
					'tue'=>$sprache->tuesday,
					'wed'=>$sprache->wednesday,
					'thu'=>$sprache->thursday,
					'fri'=>$sprache->friday,
					'sat'=>$sprache->saturday,
					'sun'=>$sprache->sunday);
				foreach($days as $day => $dayname) {
					for($i=0;$i<=23;$i++) {
						if(!empty($restarts[$i][$day])){ 
							$restart=explode("<br />", $restarts[$i][$day]['out']);
							?>
							<td>
								<?php echo $dayname." - ".str_pad($i,2,"0",STR_PAD_LEFT).":00" ?>
							</td>
							<td>
								<?php echo $restart[0]; ?>
							</td>
							<td>
								<?php echo $restart[1]; ?>
							</td>
							<td>
								<?php echo $restart[2]; ?>
							</td>
							<td>
								<?php echo $restart[3]; ?>
							</td>
							<td>
								<?php if(isset($restarts[$i]['mon']['img'])){ ?><img src="images/<?php echo $restarts[$i]['mon']['img'];?>.png" alt="<?php echo $restarts[$i]['mon']['alt'];?>" width="16" height="16"/><?php }?>
							</td>
							<td class="span1">
				                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>&amp;r=gs" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
				                    <input type="hidden" name="date" value="mon_<?php echo $i;?>"/>
				                    <input type="hidden" name="delete" value="delete" />
				                    <button class="btn btn-danger btn-mini"><i class="icon-trash icon-white"></i> <?php echo $gsprache->del;?></button>
				                </form>
			                </td>
							<td class="span1">
				                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>" method="post">
				                    <input type="hidden" name="date" value="mon_<?php echo $i;?>"/>
				                    <input type="hidden" name="edit" value="edit" />
				                    <button class="btn btn-primary btn-mini"><i class="icon-edit icon-white"></i> <?php echo $gsprache->mod;?></button>
			                	</form>
		                	</td>
		                <?php
		                }
					}
				} ?>
			</tbody>
		</table>
	</div>
</div>







<hr>
<div class="row-fluid">
<div class="span12">
<table class="table table-striped table-bordered table-hover fixed">
<thead>
<tr>
    <th><?php echo $sprache->hour;?></th>
    <th><?php echo $sprache->monday;?></th>
    <th><?php echo $sprache->tuesday;?></th>
    <th><?php echo $sprache->wednesday;?></th>
    <th><?php echo $sprache->thursday;?></th>
    <th><?php echo $sprache->friday;?></th>
    <th><?php echo $sprache->saturday;?></th>
    <th><?php echo $sprache->sunday;?></th>
</tr>
</thead>
<tbody>
<?php $i=0; while ($i<24) { ?>
<tr>
    <td><?php if ($i>9) echo $i; else echo "$i";?>:00</td>
    <td>
        <?php if(!empty($restarts[$i]['mon'])){ ?>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $restarts[$i]['mon']['out']; if(isset($restarts[$i]['mon']['img'])){ ?><br /><img src="images/<?php echo $restarts[$i]['mon']['img'];?>.png" alt="<?php echo $restarts[$i]['mon']['alt'];?>" width="16" height="16"/><?php }?>
            </div>
        </div>
        <?php } ?>
        <div class="row-fluid">
            <div class="span6">
                <?php if(!empty($restarts[$i]['mon'])){ ?>
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>&amp;r=gs" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                    <input type="hidden" name="date" value="mon_<?php echo $i;?>"/>
                    <input type="hidden" name="delete" value="delete" />
                    <button class="btn btn-danger btn-mini btn-block"><i class="icon-trash icon-white"></i></button>
                </form>
                <?php } ?>
            </div>
            <div class="span6">
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>" method="post">
                    <input type="hidden" name="date" value="mon_<?php echo $i;?>"/>
                    <input type="hidden" name="edit" value="edit" />
                    <button class="btn btn-primary btn-mini btn-block"><i class="icon-edit icon-white"></i></button>
                </form>
            </div>
        </div>
    </td>
    <td>
        <?php if(!empty($restarts[$i]['tue'])){ ?>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $restarts[$i]['tue']['out']; if(isset($restarts[$i]['tue']['img'])){ ?><br /><img src="images/<?php echo $restarts[$i]['tue']['img'];?>.png" alt="<?php echo $restarts[$i]['tue']['alt'];?>" width="16" height="16"/><?php }?>
            </div>
        </div>
        <?php } ?>
        <div class="row-fluid">
            <div class="span6">
                <?php if(!empty($restarts[$i]['tue'])){ ?>
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>&amp;r=gs" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                    <input type="hidden" name="date" value="tue_<?php echo $i;?>"/>
                    <input type="hidden" name="delete" value="delete" />
                    <button class="btn btn-danger btn-mini btn-block"><i class="icon-trash icon-white"></i></button>
                </form>
                <?php } ?>
            </div>
            <div class="span6">
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>" method="post">
                    <input type="hidden" name="date" value="tue_<?php echo $i;?>"/>
                    <input type="hidden" name="edit" value="edit" />
                    <button class="btn btn-primary btn-mini btn-block"><i class="icon-edit icon-white"></i></button>
                </form>
            </div>
        </div>
    </td>
    <td>
        <?php if(!empty($restarts[$i]['wed'])){ ?>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $restarts[$i]['wed']['out']; if(isset($restarts[$i]['wed']['img'])){ ?><br /><img src="images/<?php echo $restarts[$i]['wed']['img'];?>.png" alt="<?php echo $restarts[$i]['wed']['alt'];?>" width="16" height="16"/><?php }?>
            </div>
        </div>
        <?php } ?>
        <div class="row-fluid">
            <div class="span6">
                <?php if(!empty($restarts[$i]['wed'])){ ?>
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>&amp;r=gs" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                    <input type="hidden" name="date" value="wed_<?php echo $i;?>"/>
                    <input type="hidden" name="delete" value="delete" />
                    <button class="btn btn-danger btn-mini btn-block"><i class="icon-trash icon-white"></i></button>
                </form>
                <?php } ?>
            </div>
            <div class="span6">
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>" method="post">
                    <input type="hidden" name="date" value="wed_<?php echo $i;?>"/>
                    <input type="hidden" name="edit" value="edit" />
                    <button class="btn btn-primary btn-mini btn-block"><i class="icon-edit icon-white"></i></button>
                </form>
            </div>
        </div>
    </td>
    <td>
        <?php if(!empty($restarts[$i]['thu'])){ ?>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $restarts[$i]['thu']['out']; if(isset($restarts[$i]['thu']['img'])){ ?><br /><img src="images/<?php echo $restarts[$i]['thu']['img'];?>.png" alt="<?php echo $restarts[$i]['thu']['alt'];?>" width="16" height="16"/><?php }?>
            </div>
        </div>
        <?php } ?>
        <div class="row-fluid">
            <div class="span6">
                <?php if(!empty($restarts[$i]['thu'])){ ?>
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>&amp;r=gs" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                    <input type="hidden" name="date" value="thu_<?php echo $i;?>"/>
                    <input type="hidden" name="delete" value="delete" />
                    <button class="btn btn-danger btn-mini btn-block"><i class="icon-trash icon-white"></i></button>
                </form>
                <?php } ?>
            </div>
            <div class="span6">
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>" method="post">
                    <input type="hidden" name="date" value="thu_<?php echo $i;?>"/>
                    <input type="hidden" name="edit" value="edit" />
                    <button class="btn btn-primary btn-mini btn-block"><i class="icon-edit icon-white"></i></button>
                </form>
            </div>
        </div>
    </td>
    <td>
        <?php if(!empty($restarts[$i]['fri'])){ ?>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $restarts[$i]['fri']['out']; if(isset($restarts[$i]['fri']['img'])){ ?><br /><img src="images/<?php echo $restarts[$i]['fri']['img'];?>.png" alt="<?php echo $restarts[$i]['fri']['alt'];?>" width="16" height="16"/><?php }?>
            </div>
        </div>
        <?php } ?>
        <div class="row-fluid">
            <div class="span6">
                <?php if(!empty($restarts[$i]['fri'])){ ?>
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>&amp;r=gs" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                    <input type="hidden" name="date" value="fri_<?php echo $i;?>"/>
                    <input type="hidden" name="delete" value="delete" />
                    <button class="btn btn-danger btn-mini btn-block"><i class="icon-trash icon-white"></i></button>
                </form>
                <?php } ?>
            </div>
            <div class="span6">
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>" method="post">
                    <input type="hidden" name="date" value="fri_<?php echo $i;?>"/>
                    <input type="hidden" name="edit" value="edit" />
                    <button class="btn btn-primary btn-mini btn-block"><i class="icon-edit icon-white"></i></button>
                </form>
            </div>
        </div>
    </td>
    <td>
        <?php if(!empty($restarts[$i]['sat'])){ ?>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $restarts[$i]['sat']['out']; if(isset($restarts[$i]['sat']['img'])){ ?><br /><img src="images/<?php echo $restarts[$i]['sat']['img'];?>.png" alt="<?php echo $restarts[$i]['sat']['alt'];?>" width="16" height="16"/><?php }?>
            </div>
        </div>
        <?php } ?>
        <div class="row-fluid">
            <div class="span6">
                <?php if(!empty($restarts[$i]['sat'])){ ?>
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>&amp;r=gs" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                    <input type="hidden" name="date" value="sat_<?php echo $i;?>"/>
                    <input type="hidden" name="delete" value="delete" />
                    <button class="btn btn-danger btn-mini btn-block"><i class="icon-trash icon-white"></i></button>
                </form>
                <?php } ?>
            </div>
            <div class="span6">
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>" method="post">
                    <input type="hidden" name="date" value="sat_<?php echo $i;?>"/>
                    <input type="hidden" name="edit" value="edit" />
                    <button class="btn btn-primary btn-mini btn-block"><i class="icon-edit icon-white"></i></button>
                </form>
            </div>
        </div>
    </td>
    <td>
        <?php if(!empty($restarts[$i]['sun'])){ ?>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $restarts[$i]['sun']['out']; if(isset($restarts[$i]['sun']['img'])){ ?><br /><img src="images/<?php echo $restarts[$i]['sun']['img'];?>.png" alt="<?php echo $restarts[$i]['sun']['alt'];?>" width="16" height="16"/><?php }?>
            </div>
        </div>
        <?php } ?>
        <div class="row-fluid">
            <div class="span6">
                <?php if(!empty($restarts[$i]['sun'])){ ?>
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>&amp;r=gs" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                    <input type="hidden" name="date" value="sun_<?php echo $i;?>"/>
                    <input type="hidden" name="delete" value="delete" />
                    <button class="btn btn-danger btn-mini btn-block"><i class="icon-trash icon-white"></i></button>
                </form>
                <?php } ?>
            </div>
            <div class="span6">
                <form action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>" method="post">
                    <input type="hidden" name="date" value="sun_<?php echo $i;?>"/>
                    <input type="hidden" name="edit" value="edit" />
                    <button class="btn btn-primary btn-mini btn-block"><i class="icon-edit icon-white"></i></button>
                </form>
            </div>
        </div>
    </td>
</tr>
<?php $i++; } ?>
</tbody>
</table>
</div>
</div>