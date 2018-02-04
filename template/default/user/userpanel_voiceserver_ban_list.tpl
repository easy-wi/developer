<section class="content-header">
    <h1><?php echo $sprache->banList;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"> Home</a></i></li>
        <li><a href="userpanel.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><i class="fa fa-ban"></i> <?php echo $sprache->banList;?></li>
        <li class="active"><?php echo $server;?></li>
    </ol>
</section>


<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">

                    <div>
                        <a href="userpanel.php?w=vo&amp;d=bl&amp;e=ad&amp;id=<?php echo $id;?>"><span class="btn btn-success"><i class="fa fa-plus-circle"></i> <?php echo $sprache->banAdd;?></span></a>
                    </div>

                    <hr>

                    <div class="box-body table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->UserID;?></th>
                                <th><?php echo $sprache->reason;?></th>
                                <th><?php echo $sprache->created;?></th>
                                <th><?php echo $sprache->creator;?></th>
                                <th><?php echo $sprache->ends;?></th>
                                <th><?php echo $gsprache->del;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($banList as $k => $row) { ?>
                            <tr>
                                <td><?php echo $row['usr_id'];?></td>
                                <td><?php echo $row['reason'];?></td>
                                <td><?php echo $row['created'];?></td>
                                <td><?php echo $row['creator'];?></td>
                                <td><?php echo $row['ends'];?></td>
                                <td>
                                    <form method="post" action="userpanel.php?w=vo&amp;d=bl&amp;id=<?php echo $id;?>&amp;r=vo" name="form" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                                        <button class="btn btn-danger btn-sm" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i></button>
                                        <input type="hidden" name="action" value="dl">
                                        <input type="hidden" name="bannID" value="<?php echo $k;?>">
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?php echo $sprache->UserID;?></th>
                                <th><?php echo $sprache->reason;?></th>
                                <th><?php echo $sprache->created;?></th>
                                <th><?php echo $sprache->creator;?></th>
                                <th><?php echo $sprache->ends;?></th>
                                <th><?php echo $gsprache->del;?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>