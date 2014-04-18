<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=my">MySQL <?php echo $gsprache->databases;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=my&amp;d=ad&amp;r=my" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group">
                <label class="control-label" for="inputUser"><?php echo $sprache->user;?></label>
                <div class="controls">
                    <select id="inputUser" name="userid">
                        <?php foreach ($table as $id =>$user){ ?>
                        <option value="<?php echo $id;?>"><?php echo $user;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputServer">MySQL Server</label>
                <div class="controls">
                    <select id="inputServer" name="serverid" onchange="getdetails('serverallocation.php?d=my&id=', this.value)">
                        <?php foreach ($table2 as $table_row2){ ?>
                        <option value="<?php echo $table_row2['id'];?>"><?php echo $table_row2['ip'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->usage;?></label>
                <div id="information" class="controls">
                    <?php echo $installed."/".$max_databases.' '.$gsprache->databases;?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDescription"><?php echo $sprache->description;?></label>
                <div class="controls">
                    <input id=inputDescription type="text" name="description" value="">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassword"><?php echo $sprache->password;?></label>
                <div class="controls">
                    <input id=inputPassword type="text" name="password" value="<?php echo $password;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHostTable"><?php echo $sprache->manage_host_table;?></label>
                <div class="controls">
                    <select id="inputHostTable" name="manage_host_table">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIPs"><?php echo $sprache->ips;?></label>
                <div class="controls">
                    <textarea id="inputIPs" name="ips" rows="5" ></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxQueriesPerHour"><?php echo $sprache->max_queries_per_hour;?></label>
                <div class="controls">
                    <input id="inputMaxQueriesPerHour" type="number" name="max_queries_per_hour" value="<?php echo $max_queries_per_hour;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxUpdatesPerHour"><?php echo $sprache->max_updates_per_hour;?></label>
                <div class="controls">
                    <input id="inputMaxUpdatesPerHour" type="number" name="max_updates_per_hour" value="<?php echo $max_updates_per_hour;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxConnectionsPerHour"><?php echo $sprache->max_connections_per_hour;?></label>
                <div class="controls">
                    <input id="inputMaxConnectionsPerHour" type="number" name="max_connections_per_hour" value="<?php echo $max_connections_per_hour;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxUserConnectionsPerHour"><?php echo $sprache->max_userconnections_per_hour;?></label>
                <div class="controls">
                    <input id="inputMaxUserConnectionsPerHour" type="number" name="max_userconnections_per_hour" value="<?php echo $max_userconnections_per_hour;?>" required>
                </div>
            </div>
            <?php foreach(customColumns('D') as $row){ ?>
            <div class="control-group">
                <label class="control-label" for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                <div class="controls">
                    <?php echo $row['input'];?>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>