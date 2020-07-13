<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_data->pages['protectioncheck']['linkname'];?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span5">
        <form class="form-signin" action="<?php echo $protection_link;?>" method="post">
            <h2 class="form-signin-heading"><?php echo $sprache->protect;?></h2>
            <div class="control-group <?php if(isset($protected) and $protected=='Y'){ echo 'success'; }else{ echo 'error';}?>">
                <div class="controls">
                    <label class="control-label" for="inputIP"></label>
                    <div class="input-prepend input-append">
                        <span class="add-on"><i class="fa fa-shield"></i></span>
                        <input class="input-block-level" id="inputIP" type="text" name="serveraddress" value="<?php echo $ipvalue ?>" maxlength="22"  >
                        <button class="btn btn-primary" type="submit"><i class="icon-search icon-white"></i></button>
                    </div>
                </div>
            </div>
            <?php if(isset($protected) and $protected=="Y"){ ?>
            <table class="table table-striped">
                <tr>
                    <th><?php echo $sprache->since.' '.$since;?></th>
                </tr>
                <?php foreach($logs as $log) { ?>
                <tr>
                    <td><?php echo $log;?></td>
                </tr>
                <?php } ?>
            </table>
            <?php } ?>
        </form>
    </div>
</div>