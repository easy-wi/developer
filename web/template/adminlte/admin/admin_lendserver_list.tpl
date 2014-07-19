<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->lendserver.' '.$gsprache->overview;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->lendserver;?></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">

<div class="col-md-3">	
    <div class="box box-info">	
        <div class="box-body">
            <dl >
                <dt><?php echo $sprache->nextfree;?></dt>
                <dd><?php echo $nextfree." ".$sprache->minutes;?></dd>
                <dt><?php echo $sprache->nextfreevo;?></dt>
                <dd><?php echo $vonextfree." ".$sprache->minutes;?></dd>
                <dt><?php echo $sprache->nextcheck;?></dt>
                <dd><?php echo $nextcheck." ".$sprache->minutes;?></dd>
                <dt><?php echo $sprache->usedserver;?></dt>
                <dd><?php echo implode('; ',$used);?></dd>
            </dl>
        </div>
    </div>
</div>

<div class="col-md-9">	
    <div class="box box-info">	
        <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th data-class="expand">Server</th>
                <th data-hide="phone,tablet">Slots</th>
                <th data-hide="phone,tablet">Rcon</th>
                <th data-hide="phone,tablet">Password</th>
                <th data-hide="phone">Ausleihzeit</th>
                <th>Timeleft</th>
                <th data-hide="phone">AusleihIP</th>
                <th><?php echo $gsprache->del;?></a></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <?php if ($table_row['servertype']=='g') { ?>
                <td><a href="hlsw://<?php echo $table_row['password']."@".$table_row['server']."?Rcon=".$table_row['rcon'];?>"><?php echo $table_row['server']." (".$table_row['shorten'].")";?></a></td>
                <?php } else if ($table_row['servertype']=='v') { ?>
                <td><a href="ts3server://<?php echo $table_row['server'].'?password='.$table_row['password'];?>"><?php echo $table_row['server']." ( TS 3 )";?></a></td>
                <?php } ?>
                <td><?php echo $table_row['slots'];?></td>
                <td><?php echo $table_row['rcon'];?></td>
                <td><?php echo $table_row['password'];?></td>
                <td><?php echo $table_row['lendtime'];?></td>
                <td><?php echo $table_row['timeleft'];?></td>
                <td><?php echo $table_row['lenderip'];?></td>
                <td>
                    <form method="post" action="admin.php?w=le&amp;r=le" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                        <input type="hidden" name="id" value="<?php echo $table_row['id'];?>">
                        <button class="btn btn-small btn-danger"><i class="fa fa-trash-o"></i></button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
</div>