<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active">CMS <?php echo $gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=ps&amp;r=ps" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <h3>CMS</h3>
            <div class="control-group">
                <label class="control-label" for="inputURL">URL</label>
                <div class="controls">
                    <input id="inputURL" type="text" name="pageurl" value="<?php echo $pageurl;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSEO"><?php echo $sprache->seo;?></label>
                <div class="controls">
                    <select id="inputSEO" name="seo">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($seo=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefault"><?php echo $sprache->defaultpage;?></label>
                <div class="controls">
                    <select id="inputDefault" name="defaultpage">
                        <option value="home">Home</option>
                        <option value="news" <?php if ($defaultpage=='news') echo 'selected="selected"'; ?>><?php echo $gsprache->news;?></option>
                        <option value="lendserver" <?php if ($defaultpage=='lendserver') echo 'selected="selected"'; ?>><?php echo $gsprache->lendserver;?></option>
                        <option value="imprint" <?php if ($defaultpage=='imprint') echo 'selected="selected"'; ?>><?php echo $gsprache->imprint;?></option>
                        <?php foreach ($subpage as $key => $value) { echo '<option value="'.$key.'"';if($defaultpage==$key){echo' selected="selected"';}echo '">'.$value.'</option>';}?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputProtectioncheck"><?php echo $sprache->protectioncheck;?></label>
                <div class="controls">
                    <select id="inputProtectioncheck" name="protectioncheck">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($protectioncheck=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <hr>
            <h3><?php echo $gsprache->news;?></h3>
            <div class="control-group">
                <label class="control-label" for="inputMaxNews"><?php echo $sprache->maxnews;?></label>
                <div class="controls">
                    <input id="inputMaxNews" type="text" name="maxnews" value="<?php echo $maxnews;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxNewsSidebar"><?php echo $sprache->maxnews.' '.$sprache->sitemap;?></label>
                <div class="controls">
                    <input id="inputMaxNewsSidebar" type="text" name="maxnews_sidebar" value="<?php echo $maxnews_sidebar;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputNewsSidebarTextlength"><?php echo $sprache->newssidebar_textlength;?></label>
                <div class="controls">
                    <input id="inputNewsSidebarTextlength" type="text" name="newssidebar_textlength" value="<?php echo $newssidebar_textlength;?>">
                </div>
            </div>
            <hr>
            <h3><?php echo $gsprache->comments;?></h3>
            <div class="control-group">
                <label class="control-label" for="inputMailRequired"><?php echo $sprache->mailRequired;?></label>
                <div class="controls">
                    <select id="inputMailRequired" name="mailRequired">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($mailRequired=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCommentMinLength"><?php echo $sprache->commentMinLength;?></label>
                <div class="controls">
                    <input id="inputCommentMinLength" type="text" name="commentMinLength" value="<?php echo $commentMinLength;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCommentsModerated"><?php echo $sprache->commentsModerated;?></label>
                <div class="controls">
                    <select id="inputCommentsModerated" name="commentsModerated">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($commentsModerated=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSpamFilter"><?php echo $sprache->spamFilter;?></label>
                <div class="controls">
                    <select id="inputSpamFilter" name="spamFilter">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($spamFilter=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSpamLanguageFilter"><?php echo $sprache->spamLanguageFilter;?></label>
                <div class="controls">
                    <select id="inputSpamLanguageFilter" name="languageFilter">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($languageFilter=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSpamBlockLinks"><?php echo $sprache->spamBlockLinks;?></label>
                <div class="controls">
                    <select id="inputSpamBlockLinks" name="blockLinks">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($blockLinks=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDNSBL"><a href="https://dnsbl.tornevall.org" target="_blank">DNSBL</a></label>
                <div class="controls">
                    <select id="inputDNSBL" name="dnsbl">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($dnsbl=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHoneyPotKey"><a href="https://www.projecthoneypot.org/httpbl_api.php" target="_blank">projecthoneypot.org</a> API Key</label>
                <div class="controls">
                    <input id="inputHoneyPotKey" type="text" name="honeyPotKey" value="<?php echo $honeyPotKey;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBlockWords"><?php echo $sprache->spamBlockWords;?></label>
                <div class="controls">
                    <textarea id="inputinputBlockWords" name="blockWords" rows="4"><?php echo $blockWords;?></textarea>
                </div>
            </div>
            <hr>
            <h3><?php echo $sprache->about;?></h3>
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->about;?></label>
                <div class="controls">
                    <?php foreach ($lang_avail as $lg) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inlineCheckbox<?php echo $array['lang'];?>" name="language[]" value="<?php echo $lg;?>" onclick="textdrop('<?php echo $lg;?>');" <?php if($about_text[$lg]!=false) echo 'checked';?>> <img src="images/flags/<?php echo $lg;?>.png" alt="Flag: <?php echo $lg;?>.png" class="inline">
                    </label>
                    <?php } ?>
                </div>
            </div>
            <?php foreach ($lang_avail as $lg) { ?>
            <div id="<?php echo $lg;?>" class="control-group <?php if($about_text[$lg]==false) echo 'display_none';?>">
                <label class="control-label" for="inputAbout<?php echo $lg;?>"><img src="images/flags/<?php echo $lg;?>.png" alt="<?php echo $lg;?>"/></label>
                <div class="controls">
                    <textarea id="inputAbout<?php echo $lg;?>" name="about[<?php echo $lg;?>]" rows="5"><?php echo $about_text[$lg];?></textarea>
                </div>
            </div>
            <?php } ?>
            <hr>
            <h3><?php echo $sprache->register;?></h3>
            <div class="control-group">
                <label class="control-label" for="inputRegister"><?php echo $sprache->register;?></label>
                <div class="controls">
                    <select id="inputRegister" name="registration">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="M" <?php if ($registration=='M') echo 'selected="selected"'; ?>><?php echo $sprache->registerByMailActivation;?></option>
                        <option value="A" <?php if ($registration=='A') echo 'selected="selected"'; ?>><?php echo $sprache->registerByMailAdminActivation;?></option>
                        <option value="D" <?php if ($registration=='D') echo 'selected="selected"'; ?>><?php echo $sprache->registerDirectActive;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRegisterBlockMails"><?php echo $sprache->registerBlockMails;?></label>
                <div class="controls">
                    <textarea id="inputRegisterBlockMails" name="registrationBadEmail" rows="5"><?php echo $registrationBadEmail;?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRegisterBlockIPs"><?php echo $sprache->registerBlockIPs;?></label>
                <div class="controls">
                    <textarea id="inputRegisterBlockIPs" name="registrationBadIP" rows="5"><?php echo $registrationBadIP;?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->tou;?></label>
                <div class="controls">
                    <?php foreach ($lang_avail as $lg) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inlineCheckboxTou<?php echo $array['lang'];?>" name="touLanguages[]" value="<?php echo $lg;?>" onclick="textdrop('tou_<?php echo $lg;?>');" <?php if($tous[$lg]!=false) echo 'checked';?>> <img src="images/flags/<?php echo $lg;?>.png" alt="Flag: <?php echo $lg;?>.png" class="inline">
                    </label>
                    <?php } ?>
                </div>
            </div>
            <?php foreach ($lang_avail as $lg) { ?>
            <div id="tou_<?php echo $lg;?>" class="control-group <?php if($tous[$lg]==false) echo 'display_none';?>">
                <label class="control-label" for="inputTou<?php echo $lg;?>"><img src="images/flags/<?php echo $lg;?>.png" alt="<?php echo $lg;?>"/></label>
                <div class="controls">
                    <input type="text" id="inputTou<?php echo $lg;?>" name="tou[<?php echo $lg;?>]" value="<?php echo $tous[$lg];?>" placeholder="http://domain.tld/tou.pdf">
                </div>
            </div>
            <?php } ?>
            <!--<div class="control-group">
                <label class="control-label" for="inputRegistrationQuestion"><?php echo $sprache->registerQuestions;?></label>
                <div class="controls">
                    <select id="inputRegistrationQuestion" name="registrationQuestion">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($dnsbl=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>-->
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>