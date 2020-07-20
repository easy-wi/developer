<section class="content-header">
    <h1>Beispiel Modul / Exemple Module</h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo (isset($targetFile)) ? $targetFile : '../admin.php';?>"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Beispiel Modul / Exemple Module</li>
    </ol>
</section>
<section class="content" style="margin-top: 25px;">
    <div class="row">
        <div class="col-md-4">
            <div class="box <?php echo $trafficlight; ?>">
                <div class="box-body">
                    <div class="form-group col-md-12" style="text-align: center;">
                        <label>Server Used</label>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label >RAM Usage:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $memusage; ?>%</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>CPU Usage:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $cpuload; ?>%</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>Hard Disk Usage:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $diskusage; ?>%</label>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>&nbsp;</label>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>Established Connections:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $connections; ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>Total Connections:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $totalconnections; ?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box <?php echo $trafficlight; ?>">
                <div class="box-body">
                    <div class="form-group col-md-12" style="text-align: center;">
                        <label>Memory</label>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>RAM Total:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $memtotal; ?> GB</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>RAM Free:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $memfree; ?> GB</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>RAM Used:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $memused; ?> GB</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>RAM Shared:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $memshared; ?> GB</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>RAM Cached:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $memcached; ?> GB</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>RAM Available:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $memavailable; ?> GB</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box <?php echo $trafficlight; ?>">
                <div class="box-body">
                    <div class="form-group col-md-12" style="text-align: center;">
                        <label>Disk</label>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>Hard Disk Free:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $diskfree; ?> GB</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>Hard Disk Used:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $diskused; ?> GB</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-8 col-md-6">
                            <label>Hard Disk Total:</label>
                        </div>
                        <div class="col-xs-4 col-md-6">
                            <label><?php echo $disktotal; ?> GB</label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- rows have max 12 cols -->
    <div class="row">
        <div class="col-md-4">
            <div class="box <?php echo $trafficlight; ?>">
                <div class="box-body">
                    <div class="form-group col-md-12" style="text-align: center;">
                        <label>Server</label>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-5 col-md-6">
                            <label>Server Name:</label>
                        </div>
                        <div class="col-xs-7 col-md-6">
                            <label><?php echo $ui->server['SERVER_NAME']; ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-6 col-md-6">
                            <label>Server Addr:</label>
                        </div>
                        <div class="col-xs-6 col-md-6">
                            <label><?php echo $ui->server['SERVER_ADDR']; ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-6 col-md-6">
                            <label>PHP Version:</label>
                        </div>
                        <div class="col-xs-6 col-md-6">
                            <label><?php echo phpversion(); ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-6 col-md-6">
                            <label>Load Time:</label>
                        </div>
                        <div class="col-xs-6 col-md-6">
                            <label><?php echo $total_time; ?> sec</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-5 col-md-6">
                            <label>Local Time:</label>
                        </div>
                        <div class="col-xs-7 col-md-6">
                            <label><?php echo date("Y-m-d H:i:s"); ?></label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>