<section class="content-header">
    <h1><?php echo $gsprache->lendserver.' '.$gsprache->overview;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=le"><i class="fa fa-flask"></i> <?php echo $gsprache->lendserver;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h4><?php echo $sprache->nextcheck;?></h4>
                            <p><?php echo $nextcheck." ".$sprache->minutes;?></p>
                        </div>
                        <div class="col-md-4">
                            <h4><?php echo $sprache->nextfree;?></h4>
                            <?php echo $nextfree." ".$sprache->minutes;?>
                        </div>
                        <div class="col-md-4">
                            <h4><?php echo $sprache->nextfreevo;?></h4>
                            <?php echo $vonextfree." ".$sprache->minutes;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title"><?php echo $gsprache->gameserver;?></h3>
                </div>

                <div class="box-body table-responsive">
                    <table id="dataTableGameServer" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $gssprache->servername;?></th>
                            <th><?php echo $gssprache->slots;?></th>
                            <th><?php echo $gssprache->map;?></th>
                            <th><?php echo $gssprache->games;?></th>
                            <th><?php echo $sprache->free;?></th>
                            <th>RCON</th>
                            <th><?php echo $gsprache->password;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($lendGameServers as $v){ ?>
                        <tr>
                            <td><img src="images/games/icons/<?php echo $v['runningGame'];?>.png"  width="16"> <a href="steam://connect/<?php echo $v['ip'].':'.$v['port'];?>/<?php echo $v['password'];?>"><?php echo $v['ip'].':'.$v['port'].' '.$v['queryName'];?></a></td>
                            <td><?php echo $v['usedslots'].'/'.$v['slots'];?></td>
                            <td><?php echo $v['queryMap'];?></td>
                            <td><?php echo implode(', ',$v['games']);?></td>
                            <td><?php if($v['timeleft']==0) echo $sprache->ready; else echo $v['timeleft'].' '.$sprache->minutes;?></td>
                            <td><?php echo ($v['id']!==null) ? $v['rcon'] : '';?></td>
                            <td><?php echo ($v['id']!==null) ? $v['password'] : '';?></td>
                            <td>
                                <?php if($v['id']!==null){ ?>
                                <form method="post" action="admin.php?w=le&amp;r=le" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                                    <input type="hidden" name="id" value="<?php echo $v['id'];?>">
                                    <button class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> <?php echo $gsprache->stop;?></button>
                                </form>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type='text/javascript'>
        $(function() {
            $('#dataTableGameServer').dataTable({
                'bPaginate': true,
                'bLengthChange': true,
                'bFilter': true,
                'bSort': true,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': [-1, -2, -3]
                }],
                'bInfo': true,
                'bAutoWidth': false,
                'iDisplayLength' : 10,
                'aaSorting': [[0, 'asc']],
                'oLanguage': {
                    'oPaginate': {
                        'sFirst': '<?php echo $gsprache->dataTablesFirst;?>',
                        'sLast': '<?php echo $gsprache->dataTablesLast;?>',
                        'sNext': '<?php echo $gsprache->dataTablesNext;?>',
                        'sPrevious': '<?php echo $gsprache->dataTablesPrevious;?>'
                    },
                    'sEmptyTable': '<?php echo $gsprache->dataTablesEmptyTable;?>',
                    'sInfo': '<?php echo $gsprache->dataTablesInfo;?>',
                    'sInfoEmpty': '<?php echo $gsprache->dataTablesEmpty;?>',
                    'sInfoFiltered': '<?php echo $gsprache->dataTablesFiltered;?>',
                    'sLengthMenu': '<?php echo $gsprache->dataTablesMenu;?>',
                    'sSearch': '<?php echo $gsprache->dataTablesSearch;?>',
                    'sZeroRecords': '<?php echo $gsprache->dataTablesNoRecords;?>'
                }
            });
        });
    </script>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title"><?php echo $gsprache->voiceserver;?></h3>
                </div>

                <div class="box-body table-responsive">
                    <table id="dataTableVoiceServer" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $gssprache->servername;?></th>
                            <th><?php echo $gssprache->slots;?></th>
                            <th><?php echo $sprache->free;?></th>
                            <th><?php echo $gsprache->password;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($lendVoiceServers as $v){ ?>
                        <tr>
                            <td><a href="ts3server://<?php echo (strlen($v['connect'])>0) ? $v['connect'] : $v['ip'].':'.$v['port'];?>?password=<?php echo $v['password'];?>"><?php echo (strlen($v['connect'])>0) ? $v['connect'] : $v['ip'].':'.$v['port'];?></a></td>
                            <td><?php echo $v['usedslots'].'/'.$v['slots'];?></td>
                            <td><?php if($v['timeleft']==0) echo $sprache->ready; else echo $v['timeleft'].' '.$sprache->minutes;?></td>
                            <td><?php echo ($v['id']!==null) ? $v['password'] : '';?></td>
                            <td>
                                <?php if($v['id']!==null){ ?>
                                <form method="post" action="admin.php?w=le&amp;r=le" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                                    <input type="hidden" name="id" value="<?php echo $v['id'];?>">
                                    <button class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> <?php echo $gsprache->stop;?></button>
                                </form>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type='text/javascript'>
        $(function() {
            $('#dataTableVoiceServer').dataTable({
                'bPaginate': true,
                'bLengthChange': true,
                'bFilter': true,
                'bSort': true,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': [-1, -2]
                }],
                'bInfo': true,
                'bAutoWidth': false,
                'iDisplayLength' : 10,
                'aaSorting': [[0, 'asc']],
                'oLanguage': {
                    'oPaginate': {
                        'sFirst': '<?php echo $gsprache->dataTablesFirst;?>',
                        'sLast': '<?php echo $gsprache->dataTablesLast;?>',
                        'sNext': '<?php echo $gsprache->dataTablesNext;?>',
                        'sPrevious': '<?php echo $gsprache->dataTablesPrevious;?>'
                    },
                    'sEmptyTable': '<?php echo $gsprache->dataTablesEmptyTable;?>',
                    'sInfo': '<?php echo $gsprache->dataTablesInfo;?>',
                    'sInfoEmpty': '<?php echo $gsprache->dataTablesEmpty;?>',
                    'sInfoFiltered': '<?php echo $gsprache->dataTablesFiltered;?>',
                    'sLengthMenu': '<?php echo $gsprache->dataTablesMenu;?>',
                    'sSearch': '<?php echo $gsprache->dataTablesSearch;?>',
                    'sZeroRecords': '<?php echo $gsprache->dataTablesNoRecords;?>'
                }
            });
        });
    </script>
</section>