<section class="content-header">
    <h1><?php echo $gsprache->voiceserver;?> Token</h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $targetFile;?>"><i class="fa fa-home"></i> Home</a></li>
		<li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a></li>
		<li class="active">Token</li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

	<!-- Content Help -->
	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-11">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_voiceserver_key;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">
                <div class="box-body">

                    <div>
                        <a href="userpanel.php?w=vo&amp;d=pk&amp;id=<?php echo $id;?>&amp;po=1"><span class="btn btn-success"><i class="fa fa-plus-circle"></i> <?php echo $sprache->token;?></span></a>
                    </div>

                    <hr>

                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->groupname;?></th>
                                <th><?php echo $sprache->token;?></th>
                                <th><?php echo $gsprache->del;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pklist as $token) { ?>
                            <tr>
                                <td><?php echo $token['groupname'];?></td>
                                <td><?php echo $token['token'];?></td>
                                <td>
                                    <form method="post" action="userpanel.php?w=vo&amp;d=pk&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                                        <input type="hidden" name="token" value="<?php echo $token['token'];?>" >
                                        <input type="hidden" name="action" value="dl" >
                                        <button class="btn btn-danger btn-sm" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>