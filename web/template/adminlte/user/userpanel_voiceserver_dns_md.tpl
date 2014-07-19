<!-- Content Header -->
<section class="content-header">
    <h1>TS3 DNS <?php echo $gsprache->mod;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a></li>
            <li><a href="userpanel.php?w=vd">TS3 DNS <?php echo $gsprache->mod;?></a></li>
            <li class="active"><?php echo $defaultdns; ?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">
	
	<div class="box box-info">	
		<div class="box-body">
			<div class="form-group">
				<label><?php echo $sprache->defaultdns;?></label>
				<input type="text" class="form-control" placeholder="<?php echo $defaultdns;?>" disabled/>
			</div>
			
			<form role="form" action="userpanel.php?w=vd&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
				<input type="hidden" name="token" value="<?php echo token();?>">
				<input type="hidden" name="action" value="md">
				<div class="form-group">
					<label for="dns"><?php echo $sprache->dns;?></label>
						<input class="form-control" id="dns" type="text" name="dns" value="<?php echo $dns;?>" required>
				</div>
				<div class="form-group">
					<label for="ip"><?php echo $sprache->ip;?></label>
						<input class="form-control" id="ip" type="text" name="ip" value="<?php echo $ip;?>" required>
				</div>
				<div class="form-group">
					<label for="port"><?php echo $sprache->port;?></label>
						<input class="form-control" id="port" type="text" name="port" value="<?php echo $port;?>" required>
				</div>
		</div>
	</div>
					<label for="inputEdit"></label>
						<button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-edit"></i> <?php echo $gsprache->save;?></button>
			</form>		
</section>