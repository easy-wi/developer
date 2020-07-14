<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
		<li>Web FTP</li>
		<li class="active"><?php echo $address;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
        <div class="box-primary">
            <div class="box-body">
                <?php echo $monstaDisplay;?>
            </div>
        </div>
    </div></div>
</section>