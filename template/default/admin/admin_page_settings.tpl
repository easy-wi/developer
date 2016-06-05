<section class="content-header">
    <h1><?php echo 'CMS '.$gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=fn"><i class="fa fa-rss"></i> <?php echo $gsprache->feeds;?></a></li>
        <li class="active"><i class="fa fa-wrench"></i> CMS <?php echo $gsprache->settings;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=ps&amp;r=ps" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputURL">URL</label>
                            <div class="controls">
                                <input class="form-control" class="form-control" id="inputURL" type="text" name="pageurl" value="<?php echo $pageurl;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputSEO"><?php echo $sprache->seo;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputSEO" name="seo">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if ($seo=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefault"><?php echo $sprache->defaultpage;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputDefault" name="defaultpage">
                                    <option value="home">Home</option>
                                    <option value="news" <?php if ($defaultpage=='news') echo 'selected="selected"'; ?>><?php echo $gsprache->news;?></option>
                                    <option value="lendserver" <?php if ($defaultpage=='lendserver') echo 'selected="selected"'; ?>><?php echo $gsprache->lendserver;?></option>
                                    <option value="imprint" <?php if ($defaultpage=='imprint') echo 'selected="selected"'; ?>><?php echo $gsprache->imprint;?></option>
                                    <?php foreach ($subpage as $key => $value) { echo '<option value="'.$key.'"';if($defaultpage==$key){echo' selected="selected"';}echo '">'.$value.'</option>';}?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputProtectioncheck"><?php echo $sprache->protectioncheck;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputProtectioncheck" name="protectioncheck">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if ($protectioncheck=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h4><?php echo $gsprache->news;?></h4>

                        <div class="form-group">
                            <label for="inputMaxNews"><?php echo $sprache->maxnews;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxNews" type="text" name="maxnews" value="<?php echo $maxnews;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputMaxNewsSidebar"><?php echo $sprache->maxnews.' '.$sprache->sitemap;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxNewsSidebar" type="text" name="maxnews_sidebar" value="<?php echo $maxnews_sidebar;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputNewsSidebarTextlength"><?php echo $sprache->newssidebar_textlength;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputNewsSidebarTextlength" type="text" name="newssidebar_textlength" value="<?php echo $newssidebar_textlength;?>">
                            </div>
                        </div>

                        <hr>
                        <h4><?php echo $gsprache->comments;?></h4>

                        <div class="form-group">
                            <label for="inputMailRequired"><?php echo $sprache->mailRequired;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputMailRequired" name="mailRequired">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($mailRequired=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputCommentMinLength"><?php echo $sprache->commentMinLength;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputCommentMinLength" type="text" name="commentMinLength" value="<?php echo $commentMinLength;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputCommentsModerated"><?php echo $sprache->commentsModerated;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputCommentsModerated" name="commentsModerated">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($commentsModerated=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputSpamFilter"><?php echo $sprache->spamFilter;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputSpamFilter" name="spamFilter">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($spamFilter=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputSpamLanguageFilter"><?php echo $sprache->spamLanguageFilter;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputSpamLanguageFilter" name="languageFilter">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($languageFilter=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputSpamBlockLinks"><?php echo $sprache->spamBlockLinks;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputSpamBlockLinks" name="blockLinks">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($blockLinks=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDNSBL"><a href="https://dnsbl.tornevall.org" target="_blank">DNSBL</a></label>
                            <div class="controls">
                                <select class="form-control" id="inputDNSBL" name="dnsbl">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($dnsbl=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputHoneyPotKey"><a href="https://www.projecthoneypot.org/httpbl_api.php" target="_blank">projecthoneypot.org</a> API Key</label>
                            <div class="controls">
                                <input class="form-control" id="inputHoneyPotKey" type="text" name="honeyPotKey" value="<?php echo $honeyPotKey;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputBlockWords"><?php echo $sprache->spamBlockWords;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputBlockWords" name="blockWords" rows="4"><?php echo $blockWords;?></textarea>
                            </div>
                        </div>

                        <hr>
                        <h4><?php echo $sprache->about;?></h4>

                        <div class="form-group">
                            <?php foreach ($lang_avail as $lg){ ?>
                            <label class="checkbox-inline">
                                <input name="language[]" value="<?php echo $lg;?>" onclick="textdrop('<?php echo $lg;?>');" type="checkbox" <?php if($about_text[$lg]) echo 'checked="checked"';?>> <img src="images/flags/<?php echo $lg;?>.png" alt="Flag: 16_<?php echo $lg;?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($lang_avail as $lg) { ?>
                        <div id="<?php echo $lg;?>" class="form-group <?php if($about_text[$lg]==false) echo 'display_none';?>">
                            <label for="inputAbout-<?php echo $lg;?>"><img src="images/flags/<?php echo $lg;?>.png" alt="Flag: 16_<?php echo $lg;?>'.png"/></label>
                            <textarea class="form-control" id="inputAbout-<?php echo $lg;?>" name="about[<?php echo $lg;?>]"><?php echo $about_text[$lg];?></textarea>
                        </div>
                        <?php } ?>

                        <hr>
                        <h4><?php echo $sprache->register;?></h4>
                        <div class="form-group">
                            <label for="inputRegister"><?php echo $sprache->register;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputRegister" name="registration">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="M" <?php if ($registration=='M') echo 'selected="selected"'; ?>><?php echo $sprache->registerByMailActivation;?></option>
                                    <option value="A" <?php if ($registration=='A') echo 'selected="selected"'; ?>><?php echo $sprache->registerByMailAdminActivation;?></option>
                                    <option value="D" <?php if ($registration=='D') echo 'selected="selected"'; ?>><?php echo $sprache->registerDirectActive;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputRegisterBlockMails"><?php echo $sprache->registerBlockMails;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputRegisterBlockMails" name="registrationBadEmail" rows="5"><?php echo $registrationBadEmail;?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputRegisterBlockIPs"><?php echo $sprache->registerBlockIPs;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputRegisterBlockIPs" name="registrationBadIP" rows="5"><?php echo $registrationBadIP;?></textarea>
                            </div>
                        </div>

                        <hr>
                        <h4><?php echo $sprache->tou;?></h4>

                        <div class="form-group">
                            <?php foreach ($lang_avail as $lg){ ?>
                            <label class="checkbox-inline">
                                <input name="touLanguages[]" value="<?php echo $lg;?>" onclick="textdrop('tou_<?php echo $lg;?>');" type="checkbox" <?php if($tous[$lg]) echo 'checked="checked"';?>> <img src="images/flags/<?php echo $lg;?>.png" alt="Flag: 16_<?php echo $lg;?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($lang_avail as $lg) { ?>
                        <div id="tou_<?php echo $lg;?>" class="form-group <?php if($tous[$lg]==false) echo 'display_none';?>">
                            <label for="inputTou-<?php echo $lg;?>"><img src="images/flags/<?php echo $lg;?>.png" alt="<?php echo $lg;?>"/></label>
                            <div class="controls">
                                <input class="form-control" type="text" id="inputTou-<?php echo $lg;?>" name="tou[<?php echo $lg;?>]" value="<?php echo $tous[$lg];?>" placeholder="http://domain.tld/tou.pdf">
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>