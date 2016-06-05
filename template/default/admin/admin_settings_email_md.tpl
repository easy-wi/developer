<section class="content-header">
    <h1>E-Mail <?php echo $gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li class="active"><i class="fa fa-envelope"></i> E-Mail <?php echo $gsprache->settings;?></li>
    </ol>
</section>
<form role="form" action="admin.php?w=sm&d=add&r=sm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
<input type="hidden" name="token" value="<?php echo token();?>">
<input type="hidden" name="id" value="<?php echo $email_id;?>">
<input type="hidden" name="email_setting_name" value="<?php echo $email_setting_name;?>">
<input type="hidden" name="email_setting_language" value="<?php echo $email_language;?>">
<input type="hidden" name="email_setting_category" value="<?php echo $email_catid;?>">
<section class="content">
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo $esprache->templatesettinghead; ?></h3>
            </div>
            <div class="box-body">
                <label class="control-label" for="inlineEmailTemplate"><?php echo $gsprache->template;?> Setting</label>
                <table class="formmail">
                    <tbody>
                    <tr>
                        <td class="formmail_left"><?php echo $esprache->templatename; ?></td>
                        <td class="formmail_right">
                            <input class="form-control formmailfield" type="text" name="email_templatename" value="<?php echo $email_setting_name;?>" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td class="formmail_left"><?php echo $esprache->topic; ?>:</td>
                        <td class="formmail_right">
                            <input class="form-control formmailfield" id="inputSubject" type="text" name="email_subject" value="<?php echo $email_subject;?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="formmail_left"><?php echo $esprache->copycc; ?>:</td>
                        <td class="formmail_right">
                            <input class="form-control formmailfield" id="" type="text" name="ccmailing" placeholder="example@example.com" value="<?php echo $email_ccmailing;?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="formmail_left"><?php echo $esprache->copybcc; ?>:</td>
                        <td class="formmail_right">
                            <input class="form-control formmailfield" id="" type="text" name="bccmailing" placeholder="example@example.com" value="<?php echo $email_bccmailing;?>">
                        </td>
                    </tr>
                    <tr style="display:none;">
                        <td class="formmail_left" class="display:none;"><?php echo $esprache->attachments; ?>:</td>
                        <td class="formmail_right" class="display:none;">
                            <div id="attachment">
                                <input name="attachments[]" type="file" class="form-control">
                            </div>
                            <a href="#" id="addattachments" class="btn btn-success btn-xs" style="margin: 5px 2px;"><i class="fa fa-plus-circle"></i> Add</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="email_body_<?php echo $email_language;?> form-group">
                    <label class="control-label" for="email_body"><?php echo $gsprache->template;?> <img src="images/flags/<?php echo $email_language;?>.png" alt="Flag: <?php echo $email_language;?>.png"/></label>
                    <div class="controls">
                        <textarea class="form-control" id="email_body_<?php echo $email_language;?>" name="email_body" rows="30"><?php echo $email_body;?></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                <div class="pull-right">
                    <a href="admin.php?w=sm" class="btn btn-danger"><?php echo $esprache->back;?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo $esprache->variable; ?></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div><!-- /.box-tools -->
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover" style="font-size:0.8em;">
                    <tbody>
                    <tr>
                        <th><?php echo $esprache->variable; ?></th>
                        <th><?php echo $esprache->variabledesc; ?></th>
                    </tr>
                    <tr>
                        <td>%topic%</td>
                        <td><?php echo $esprache->topic; ?></td>
                    </tr>
                    <tr>
                        <td>%id%</td>
                        <td><?php echo $esprache->id; ?></td>
                    </tr>
                    <tr>
                        <td>%creationTime%</td>
                        <td><?php echo $esprache->creationTime; ?></td>
                    </tr>
                    <tr>
                        <td>%active%</td>
                        <td><?php echo $esprache->active; ?></td>
                    </tr>
                    <tr>
                        <td>%salutation%</td>
                        <td><?php echo $esprache->salutation; ?></td>
                    </tr>
                    <tr>
                        <td>%cname%</td>
                        <td><?php echo $esprache->cname; ?></td>
                    </tr>
                    <tr>
                        <td>%fullname%</td>
                        <td><?php echo $esprache->fullname; ?></td>
                    </tr>
                    <tr>
                        <td>%name%</td>
                        <td><?php echo $esprache->name; ?></td>
                    </tr>
                    <tr>
                        <td>%vname%</td>
                        <td><?php echo $esprache->vname; ?></td>
                    </tr>
                    <tr>
                        <td>%birthday%</td>
                        <td><?php echo $esprache->birthday; ?></td>
                    </tr>
                    <tr>
                        <td>%mail%</td>
                        <td><?php echo $esprache->mail; ?></td>
                    </tr>
                    <tr>
                        <td>%phone%</td>
                        <td><?php echo $esprache->phone; ?></td>
                    </tr>
                    <tr>
                        <td>%fax%</td>
                        <td><?php echo $esprache->fax; ?></td>
                    </tr>
                    <tr>
                        <td>%handy%</td>
                        <td><?php echo $esprache->handy; ?></td>
                    </tr>
                    <tr>
                        <td>%country%</td>
                        <td><?php echo $esprache->country; ?></td>
                    </tr>
                    <tr>
                        <td>%city%</td>
                        <td><?php echo $esprache->city; ?></td>
                    </tr>
                    <tr>
                        <td>%cityn%</td>
                        <td><?php echo $esprache->cityn; ?></td>
                    </tr>
                    <tr>
                        <td>%street%</td>
                        <td><?php echo $esprache->street; ?></td>
                    </tr>
                    <tr>
                        <td>%streetn%</td>
                        <td><?php echo $esprache->streetn; ?></td>
                    </tr>
                    <tr>
                        <td>%language%</td>
                        <td><?php echo $esprache->language; ?></td>
                    </tr>
                    <tr>
                        <td>%lastlogin%</td>
                        <td><?php echo $esprache->lastlogin; ?></td>
                    </tr>
                    <tr>
                        <td>%urlhost%</td>
                        <td><?php echo $esprache->urlhost; ?></td>
                    </tr>
                    <tr>
                        <td>%password%</td>
                        <td><?php echo $esprache->password; ?></td>
                    </tr>
                    <tr>
                        <td>%date%</td>
                        <td><?php echo $esprache->date; ?></td>
                    </tr>
                    <tr>
                        <td>%ip%</td>
                        <td><?php echo $esprache->ip; ?></td>
                    </tr>
                    <tr>
                        <td>%port%</td>
                        <td><?php echo $esprache->port; ?></td>
                    </tr>
                    <tr>
                        <td>%port2%</td>
                        <td><?php echo $esprache->port2; ?></td>
                    </tr>
                    <tr>
                        <td>%port3%</td>
                        <td><?php echo $esprache->port3; ?></td>
                    </tr>
                    <tr>
                        <td>%port4%</td>
                        <td><?php echo $esprache->port4; ?></td>
                    </tr>
                    <tr>
                        <td>%ports%</td>
                        <td><?php echo $esprache->ports; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</section>
</form>

<!-- Summernote Editor -->
<script type="text/javascript">
    $(function () {
        $('#email_body_<?php echo $email_language; ?>').summernote({
        height: 500
        });
    });
</script>
