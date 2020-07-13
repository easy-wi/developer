<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_data->pages['downloads']['linkname'];?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span10">
        <?php if (isset($user_id)) { ?>
         <div class="alert alert-success" role="alert">
 <b>Hallo <?php echo $great_user;?> !</b><br>
 Soltest du Fragen oder Probleme haben Schreibe uns einfach ein Ticket <a href="https://wi.kingshost.de/userpanel.php?w=ti"> Hier !</a>
</div>
        <?php foreach($table as $row){ ?>
        <hr><br>
        <h4><?php echo $row['description'];?></h4>
        <div class="row-fluid">
            <div class="span8">
                <?php echo $row['text'];?>
            </div>
            <div class="span4"><br>
                <a href="<?php echo $row['link'];?>"><button class="btn btn-primary"><i class="icon-white icon-download"> Download</i></button></a>
            </div>
        </div>
        <?php }?>
    </div>
</div>

<?php } elseif (isset($admin_id)) { ?>
<div class="alert alert-warning" role="alert">
<b>Hallo <?php echo $great_user;?> !</b><br>
</div>
        <?php foreach($table as $row){ ?>
        <hr><br>
        <h4><?php echo $row['description'];?></h4>
        <div class="row-fluid">
            <div class="span8">
                <?php echo $row['text'];?>
            </div>
            <div class="span4"><br>
                <a href="<?php echo $row['link'];?>"><button class="btn btn-primary"><i class="icon-white icon-download"> Download</i></button></a>
            </div>
        </div>
        <?php }?>
    </div>
</div>
<?php } else { ?>
        <div class="alert alert-warning" role="alert">
 Für den Download von Templates & Updates ist ein Account nötig! <a href="https://wi.kingshost.de/index.php?site=register">Registrieren</a>
</div>
        <?php foreach($table as $row){ ?>
        <hr><br>
        <h4><?php echo $row['description'];?></h4>
        <div class="row-fluid">
            <div class="span8">
                <?php echo $row['text'];?>
            </div>
            <div class="span4"><br>
                <a href="<?php echo $row['link'];?>"><button class="btn btn-primary"><i class="icon-white icon-download"> Download</i></button></a>
            </div>
        </div>
        <?php }?>
    </div>
</div>
<?php } ?>