<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->support;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">
                <div class="box-body">
                    <div>
                        <h4><?php echo $sprache->status;?></h4>
                        <a href="<?php echo $ticketLinks['N'];?>"><?php echo $sprache->status_new;?></a><?php if(in_array('N',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
                        <a href="<?php echo $ticketLinks['P'];?>"><?php echo $sprache->status_process;?></a><?php if(in_array('P',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
                        <a href="<?php echo $ticketLinks['R'];?>"><?php echo $sprache->status_reopen;?></a><?php if(in_array('R',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
                        <a href="<?php echo $ticketLinks['A'];?>"><?php echo $sprache->status_author;?></a><?php if(in_array('A',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
                        <a href="<?php echo $ticketLinks['D'];?>"><?php echo $sprache->status_done;?></a><?php if(in_array('D',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
                        <a href="<?php echo $ticketLinks['C'];?>"><?php echo $sprache->status_confirmed;?></a><?php if(in_array('C',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->subject;?></th>
                                <th>ID</a></th>
                                <th><?php echo $sprache->priority;?></th>
                                <th><?php echo $sprache->edit2;?></th>
                                <th><?php echo $sprache->status;?></th>
                                <th><?php echo $sprache->date;?></th>
                                <th><?php echo $gsprache->mod;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($table as $table_row) { ?>
                            <tr>
                                <td><?php echo $table_row['subject']; ?></td>
                                <td><?php echo $table_row['id']; ?></td>
                                <td><?php echo $table_row['priority']; ?></td>
                                <td><?php echo $table_row['supporter']; ?></td>
                                <td><?php echo $table_row['status']; ?></td>
                                <td><?php echo $table_row['writedate']; ?></td>
                                <td><a href="userpanel.php?w=ti&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> <?php echo $gsprache->mod;?></span></a></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?php echo $sprache->subject;?></th>
                                <th>ID</a></th>
                                <th><?php echo $sprache->priority;?></th>
                                <th><?php echo $sprache->edit2;?></th>
                                <th><?php echo $sprache->status;?></th>
                                <th><?php echo $sprache->date;?></th>
                                <th><?php echo $gsprache->mod;?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
