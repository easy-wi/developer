<section class="content-header">
    <h1><?php echo $gsprache->substitutes;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->substitutes;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_substitutes_list;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->substitutes;?> <a href="userpanel.php?w=su&amp;d=ad"<span class="btn btn-success"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->user;?></th>
                                <th><?php echo $sprache->active;?></th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($table as $table_row) { ?>
                            <tr>
                                <td><?php echo $table_row['loginName'];?></td>
                                <td><?php echo $table_row['active'];?></td>
                                <td>
                                    <a href="userpanel.php?w=su&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> <?php echo $gsprache->edit;?></span></a>
                                    <a href="userpanel.php?w=su&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-danger btn-sm"><i class="fa fa-trash-o"> <?php echo $gsprache->del;?></i></span></a>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?php echo $sprache->user;?></th>
                                <th><?php echo $sprache->active;?></th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>