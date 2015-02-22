<section class="content-header">
    <h1>MySQL Server</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=md"><i class="fa fa-database"></i> MySQL</a></li>
        <li><a href="admin.php?w=my"><i class="fa fa-server"></i> MySQL Server</a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>


<section class="content">

    <?php if (count($errors)>0){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><?php echo $gsprache->errors;?></h4>
                <?php echo implode(', ',$errors);?>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">

                <form role="form" action="admin.php?w=my&amp;d=ad&amp;r=my" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if ($active=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputExternalID"><?php echo $gsprache->externalID;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID;?>" maxlength="255">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputIP">IP</label>
                            <div class="controls">
                                <input class="form-control" id="inputIP" type="text" name="ip" maxlength="15" value="<?php echo $ip;?>" required>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputConnectIpOnly"><?php echo $gsprache->connect_ip_only;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputConnectIpOnly" name="connectIpOnly">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($connectIpOnly=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputExternalAddress"><?php echo $gsprache->externalIP;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputExternalAddress" name="externalAddress" type="text" value="<?php echo $externalAddress;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPort">Port</label>
                            <div class="controls">
                                <input class="form-control" id="inputPort" type="text" name="port" maxlength="5" value="<?php echo $port;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputUser"><?php echo $sprache->user;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputUser" type="text" name="user" value="<?php echo $user;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword"><?php echo $sprache->password;?></label>
                            <div class="controls">
                                <input class="form-control" id=inputPassword type="text" name="password" value="<?php echo $password;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputMaxDatabases"><?php echo $sprache->max_databases;?></label>
                            <div class="controls">
                                <input class="form-control" id=inputMaxDatabases type="number" name="max_databases" value="<?php echo $max_databases;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputInterface"><?php echo $sprache->interface;?></label>
                            <div class="controls">
                                <input class="form-control" id=inputInterface type="text" name="interface" value="<?php echo $interface;?>">
                            </div>
                        </div>
                    </div>

                    <div class="box-body">

                        <h3 class="box-title"><?php echo $sprache->standards;?></h3>

                        <div class="form-group">
                            <label for="inputMaxQueriesPerHour"><?php echo $sprache->max_queries_per_hour;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxQueriesPerHour" type="number" name="max_queries_per_hour" value="<?php echo $max_queries_per_hour;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputMaxUpdatesPerHour"><?php echo $sprache->max_updates_per_hour;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxUpdatesPerHour" type="number" name="max_updates_per_hour" value="<?php echo $max_updates_per_hour;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputMaxConnectionsPerHour"><?php echo $sprache->max_connections_per_hour;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxConnectionsPerHour" type="number" name="max_connections_per_hour" value="<?php echo $max_connections_per_hour;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputMaxUserConnectionsPerHour"><?php echo $sprache->max_userconnections_per_hour;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxUserConnectionsPerHour" type="number" name="max_userconnections_per_hour" value="<?php echo $max_userconnections_per_hour;?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>