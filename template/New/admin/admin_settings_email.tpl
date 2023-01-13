<section class="content-header">
    <h1>E-Mail <?php echo $gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li class="active"><i class="fa fa-envelope"></i> E-Mail <?php echo $gsprache->settings;?></li>
        <div class="col-sm">
        </div>
        <div class="col-sm">
        </div>
        <div class="col-sm">
                <div class="box box-primary">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                </div>
        </div>
                 
    </ol>
</section>

<form role="form" action="admin.php?w=sm&amp;r=sm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

    <input type="hidden" name="token" value="<?php echo token();?>">
    <input type="hidden" name="action" value="md">

    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary boxtest">
                    <div class="box-header with-border">
                        <h3 class="box-title">E-Mail Settings</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div><!-- /.box-tools -->
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="inputType">E-Mail</label>
                            <div class="controls">
                                <select class="form-control" id="inputType" name="email_settings_type" onchange="SwitchShowHideRows(this.value,'switch',1);">
                                    <option value="P">PHP Mail</option>
                                    <option value="S" <?php if($email_settings['email_settings_type']=='S') echo 'selected="selected"';?>>SMTP</option>
                                    <option value="D" <?php if($email_settings['email_settings_type']=='D') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputEmail"><?php echo $sprache->email;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputEmail" type="email" name="email" value="<?php echo $email_settings['email'];?>">
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputSSL">SSL/TLS</label>
                            <div class="controls">
                                <select class="form-control" id="inputSSL" name="email_settings_ssl">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="S" <?php if($email_settings['email_settings_ssl']=='S') echo 'selected="selected"';?>>SSL</option>
                                    <option value="T" <?php if($email_settings['email_settings_ssl']=='T') echo 'selected="selected"';?>>TLS</option>
                                </select>
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputHost">Host</label>
                            <div class="controls">
                                <input class="form-control" id="inputHost" type="text" name="email_settings_host" value="<?php echo $email_settings['email_settings_host'];?>">
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputPort">Port</label>
                            <div class="controls">
                                <input class="form-control" id="inputPort" type="text" name="email_settings_port" value="<?php echo $email_settings['email_settings_port'];?>">
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputUser"><?php echo $gsprache->user;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputUser" type="text" name="email_settings_user" value="<?php echo $email_settings['email_settings_user'];?>">
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputPassword"><?php echo $sprache->password;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputPassword" type="text" name="email_settings_password" value="<?php echo $email_settings['email_settings_password'];?>">
                            </div>
                        </div>
                        <div class="S switch <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?> smtp">
                            <div class="pull-right" style="margin-left:5px;"><a class="btn btn-success" id="submitTest"><i class="fa fa-retweet"></i> Testing</a></div>
                            <div class="pull-right" id="smtptestresult"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="email_language">E-Mail Templates Language:
                <?php foreach ($emaillanguage_templates as $array){ ?>
                <a class="emnaillanguagechoose" href="admin.php?w=sm&tl=<?php echo $array['lang'];?>"><img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/> </a>
                <?php } ?>
            </label>
            <hr/>
        </div>
        <div class="form-group">
            <label class="control-label" for="email_template">E-Mail Templates (<img src="images/flags/<?php echo $templateLanguage;?>.png" alt="Flag: 16_<?php echo $templateLanguage;?>'.png"/>)</label>
        </div>
        <!-- Categories -->
        <?php echo $resultHtmlCategories; ?>
        <!-- ./Categories -->
    </section>
</form>


<script type="text/javascript">

    $(function(){

        $("#addattachments").click(function () {
            $("#attachment").append("<input type=\"file\" name=\"attachments[]\" class=\"form-control\" />");
        });

        $('.languagechoose').on('click',function(event){
            var language = $(this).attr('value');
            $('.email_body_'+language).toggle( "slow" );
        });

        $('#submitTest').on('click',function (event){

            $('#smtptestresult').find('div').remove();
            $('.smtpresult').find('i').remove();

            event.preventDefault();
            event.stopPropagation();

            $('.boxtest').append('<div class="overlay imagelay"><i class="fa fa-refresh fa-spin"></i></div>');

            $.post({
                url: 'ajax.php?d=smtptest',
                data: {
                    email_settings_host: $('#inputHost').val(),
                    email_settings_port: $('#inputPort').val(),
                    email_settings_ssl: $('#inputSSL').val(),
                    email_settings_user: $('#inputUser').val(),
                    email_settings_password: $('#inputPassword').val(),
                    inputEmail: $('#inputEmail').val()
                },
                cache: false,
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {

                    if (typeof data.error === 'undefined') {
                        $('#smtptestresult').append('<div><span style="font-size:2em;color:#00a65a;" class="fa fa-check"></span></div>');
                        $('.smtp').removeClass('has-error');
                        $('.smtp').addClass('has-success');
                        $('.smtpresult').append('<i class="fa fa-check"></i>');
                    } else {
                        $('#smtptestresult').append('<div><span style="font-size:2em;color:#d73925;" class="fa fa-times"></span></div>');
                        $('.smtp').addClass('has-error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#smtptestresult').append('<div><span style="font-size:2em;color:#d73925;" class="fa fa-times"></span></div>');
                    $('.smtp').addClass('has-error');
                },
                complete: function() {
                    $( "div" ).remove( ".imagelay" );
                }
            });
        });
    });
</script>