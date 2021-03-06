<?php
/*******************************************************************************
*  Title: Help Desk Software HESK
*  Version: 2.6.4 from 22nd June 2015
*  Author: Klemen Stirn
*  Website: http://www.hesk.com
********************************************************************************
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2005-2015 Klemen Stirn. All Rights Reserved.
*  HESK is a registered trademark of Klemen Stirn.

*  The HESK may be used and modified free of charge by anyone
*  AS LONG AS COPYRIGHT NOTICES AND ALL THE COMMENTS REMAIN INTACT.
*  By using this code you agree to indemnify Klemen Stirn from any
*  liability that might arise from it's use.

*  Selling the code for this program, in part or full, without prior
*  written consent is expressly forbidden.

*  Using this code, in part or full, to create derivate work,
*  new scripts or products is expressly forbidden. Obtain permission
*  before redistributing this software over the Internet or in
*  any other medium. In all cases copyright and header must remain intact.
*  This Copyright is in full effect in any country that has International
*  Trade Agreements with the United States of America or
*  with the European Union.

*  Removing any of the copyright notices without purchasing a license
*  is expressly forbidden. To remove HESK copyright notice you must purchase
*  a license for this script. For more information on how to obtain
*  a license please visit the page below:
*  https://www.hesk.com/buy.php
*******************************************************************************/

/* Check if this is a valid include */
if (!defined('IN_SCRIPT')) {die('Invalid attempt');}


function hesk_profile_tab($session_array='new',$is_profile_page=true,$action='profile_page')
{
	global $hesk_settings, $hesklang, $can_reply_tickets, $can_view_tickets, $can_view_unassigned;
	?>
    <div role="tabpanel">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#profile-info" aria-controls="profile-info" role="tab" data-toggle="tab"><?php echo $hesklang['pinfo']; ?></a></li>
            <?php
            if (!$is_profile_page)
            {
            ?>
                <li role="presentation"><a href="#permissions" aria-controls="permissions" role="tab" data-toggle="tab"><?php echo $hesklang['permissions']; ?></a></li>
            <?php
            }
            ?>
            <li role="presentation"><a href="#signature" aria-controls="signature" role="tab" data-toggle="tab"><?php echo $hesklang['sig']; ?></a></li>
            <li role="presentation"><a href="#preferences" aria-controls="preferences" role="tab" data-toggle="tab"><?php echo $hesklang['pref']; ?></a></li>
            <li role="presentation"><a href="#notifications" aria-controls="notifications" role="tab" data-toggle="tab"><?php echo $hesklang['notn']; ?></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content summaryList tabPadding">
            <div role="tabpanel" class="tab-pane fade in active" id="profile-info">
                <div class="form-group">
                    <label for="name" class="col-md-3 control-label"><?php echo $hesklang['real_name']; ?>: <font class="important">*</font></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="name" size="40" maxlength="50" value="<?php echo $_SESSION[$session_array]['name']; ?>" placeholder="<?php echo htmlspecialchars($hesklang['real_name']); ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="col-md-3 control-label"><?php echo $hesklang['email']; ?>: <font class="important">*</font></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="email" size="40" maxlength="255" placeholder="<?php echo htmlspecialchars($hesklang['email']); ?>" value="<?php echo $_SESSION[$session_array]['email']; ?>" />
                    </div>
                </div>
                <?php
                if ( ! $is_profile_page || $_SESSION['isadmin']) {
                    ?>
                    <div class="form-group">
                        <label for="user" class="col-md-3 control-label"><?php echo $hesklang['username']; ?>: <font
                                class="important">*</font></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="user" size="40" maxlength="20"
                                   value="<?php echo $_SESSION[$session_array]['user']; ?>"
                                   placeholder="<?php echo htmlspecialchars($hesklang['username']); ?>"/>
                        </div>
                    </div>
                <?php
                }
                $passwordRequiredSpan = $action == 'create_user' ? '' : 'display:none';
                ?>
                <div class="form-group">
                    <label for="pass" class="col-md-3 control-label"><?php echo $is_profile_page ? $hesklang['new_pass'] : $hesklang['pass']; ?>: <span class="important" style="<?php echo $passwordRequiredSpan; ?>">*</span></label>
                    <div class="col-md-9">
                        <input type="password" class="form-control" name="newpass" autocomplete="off" size="40" placeholder="<?php echo htmlspecialchars($hesklang['pass']); ?>" value="<?php echo isset($_SESSION[$session_array]['cleanpass']) ? $_SESSION[$session_array]['cleanpass'] : ''; ?>" onkeyup="javascript:hesk_checkPassword(this.value)" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirmPass" class="col-md-3 control-label"><?php echo $hesklang['confirm_pass']; ?>: <span class="important" style="<?php echo $passwordRequiredSpan; ?>">*</span></label>
                    <div class="col-md-9">
                        <input type="password" name="newpass2" class="form-control" autocomplete="off" placeholder="<?php echo htmlspecialchars($hesklang['confirm_pass']); ?>" size="40" value="<?php echo isset($_SESSION[$session_array]['cleanpass']) ? $_SESSION[$session_array]['cleanpass'] : ''; ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="pwStrength" class="col-md-3 control-label"><?php echo $hesklang['pwdst']; ?>:</label>
                    <div class="col-md-9">
                        <div class="progress">
                            <div id="progressBar" class="progress-bar progress-bar-danger" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (!$is_profile_page) {
                    ?>
                    <div class="blankSpace"></div>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <?php
                            if ($hesk_settings['autoassign']) {
                                ?>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="autoassign"
                                                  value="Y" <?php if (!isset($_SESSION[$session_array]['autoassign']) || $_SESSION[$session_array]['autoassign'] == 1) {
                                            echo 'checked="checked"';
                                        } ?> /> <?php echo $hesklang['user_aa']; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php
            if (!$is_profile_page) {
                ?>
                <div role="tabpanel" class="tab-pane fade" id="permissions">
                    <div class="form-group">
                        <label for="administrator" class="col-md-3 control-label"><?php echo $hesklang['permission_template_colon']; ?></label>
                        <div class="col-md-9">
                            <?php
                            // Get list of permission templates. If current user is not admin, exclude permission tpl 1
                            $excludeSql = $_SESSION['isadmin'] ? '' : " WHERE `heskprivileges` <> 'ALL'";
                            $res = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_templates`".$excludeSql);
                            $templates = array();
                            echo '<select name="template" id="permission-tpl" class="form-control" onchange="updateCheckboxes()">';
                            while ($row = hesk_dbFetchAssoc($res)) {
                                array_push($templates, $row);
                                $selected = $_SESSION[$session_array]['permission_template'] == $row['id'] ? 'selected' : '';
                                echo '<option value="'.$row['id'].'" '.$selected.'>'.htmlspecialchars($row['name']).'</option>';
                            }
                            $selected = $_SESSION[$session_array]['permission_template'] == '-1' ? 'selected' : '';
                            echo '<option value="-1" '.$selected.'>'.htmlspecialchars($hesklang['custom']).'</option>';
                            echo '</select>';
                            outputCheckboxJavascript();
                            ?>
                        </div>
                    </div>
                    <div id="options">
                        <div class="form-group">
                            <label for="categories[]" class="col-md-3 control-label"><?php echo $hesklang['allowed_cat']; ?>: <font class="important">*</font></label>
                            <div class="col-md-9">
                                <?php
                                foreach ($hesk_settings['categories'] as $catid => $catname)
                                {
                                    echo '<div class="checkbox"><label><input id="cat-'.$catid.'" class="cat-checkbox"
                                    type="checkbox" name="categories[]" onchange="setTemplateToCustom()" value="' . $catid . '" ';
                                    if ( in_array($catid,$_SESSION[$session_array]['categories']) )
                                    {
                                        echo ' checked="checked" ';
                                    }
                                    echo ' />' . $catname . '</label></div> ';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="features[]" class="col-md-3 control-label"><?php echo $hesklang['allow_feat']; ?>: <font class="important">*</font></label>
                            <div class="col-md-9">
                                <?php
                                foreach ($hesk_settings['features'] as $k)
                                {
                                    echo '<div class="checkbox"><label><input id="feat-'.$k.'" class="feat-checkbox"
                                    type="checkbox" name="features[]" onchange="setTemplateToCustom()" value="' . $k . '" ';
                                    if (in_array($k,$_SESSION[$session_array]['features']))
                                    {
                                        echo ' checked="checked" ';
                                    }
                                    echo ' />' . $hesklang[$k] . '</label></div> ';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
            <div role="tabpanel" class="tab-pane fade" id="signature">
                <div class="form-group">
                    <label for="signature" class="col-md-3 control-label"><?php echo $hesklang['signature_max']; ?>:</label>

                    <div class="col-md-9">
                        <textarea class="form-control" name="signature" rows="6" placeholder="<?php echo htmlspecialchars($hesklang['sig']); ?>" cols="40"><?php echo $_SESSION[$session_array]['signature']; ?></textarea>
                        <?php echo $hesklang['sign_extra']; ?>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="preferences">
                <?php
                if ( ! $is_profile_page || $can_reply_tickets )
                {
                    ?>
                    <div class="form-group">
                        <label for="afterreply" class="col-sm-3 control-label"><?php echo $hesklang['aftrep']; ?>:</label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label><input type="radio" name="afterreply" value="0" <?php if (!$_SESSION[$session_array]['afterreply']) {echo 'checked="checked"';} ?>/> <?php echo $hesklang['showtic']; ?></label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="afterreply" value="1" <?php if ($_SESSION[$session_array]['afterreply'] == 1) {echo 'checked="checked"';} ?>/> <?php echo $hesklang['gomain']; ?></label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="afterreply" value="2" <?php if ($_SESSION[$session_array]['afterreply'] == 2) {echo 'checked="checked"';} ?>/> <?php echo $hesklang['shownext']; ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $hesklang['defaults']; ?>:</label>
                        <div class="col-sm-9">
                            <?php
                            if ($hesk_settings['time_worked'])
                            {
                                ?>
                            <div class="checkbox">
                                <label><input type="checkbox" name="autostart" value="1" <?php if (!empty($_SESSION[$session_array]['autostart'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['autoss']; ?></label>
                            </div>
                            <?php
                            }
                            ?>
                            <div class="checkbox">
                                <label><input type="checkbox" name="notify_customer_new" value="1" <?php if (!empty($_SESSION[$session_array]['notify_customer_new'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['pncn']; ?></label><br />
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="notify_customer_reply" value="1" <?php if (!empty($_SESSION[$session_array]['notify_customer_reply'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['pncr']; ?></label><br />
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="show_suggested" value="1" <?php if (!empty($_SESSION[$session_array]['show_suggested'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['pssy']; ?></label><br />
                            </div>
                        </div>
                    </div>
                <?php }?>
                <div class="form-group">
                    <label for="autoRefresh" class="col-sm-3 control-label"><?php echo $hesklang['ticket_auto_refresh']; ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="autorefresh" name="autorefresh" value="<?php echo $_SESSION[$session_array]['autorefresh']; ?>">
                        <span class="help-block"><?php echo $hesklang['autorefresh_restrictions']; ?></span>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="notifications">
                <?php $disabledText =
                    (!$_SESSION[$session_array]['isadmin'] && strpos($_SESSION[$session_array]['heskprivileges'],'can_change_notification_settings') === false)
                        ? 'disabled' : '';
                if (!$is_profile_page) {
                    $disabledText = '';
                }
                if ($disabledText == 'disabled') { ?>
                    <div class="alert alert-info"><?php echo $hesklang['notifications_disabled_info']; ?></div>
                <?php }
                ?>
                <div class="form-group">
                    <?php
                    if (! $is_profile_page || $can_view_tickets)
                    {
                        if (! $is_profile_page || $can_view_unassigned)
                        {
                            ?>
                            <div class="col-md-9 col-md-offset-3"><div class="checkbox"><label><input type="checkbox" name="notify_new_unassigned" value="1" <?php if (!empty($_SESSION[$session_array]['notify_new_unassigned'])) {echo 'checked="checked"';} echo ' '.$disabledText ?> /> <?php echo $hesklang['nwts']; ?> <?php echo $hesklang['unas']; ?></label></div></div>

                            <?php
                            if ($disabledText == 'disabled')
                            { ?>
                                <input type="hidden" name="notify_new_unassigned" value="<?php echo !empty($_SESSION[$session_array]['notify_new_unassigned']) ? '1' :  '0'; ?>">
                            <?php }
                        }
                        else
                        {
                            ?>
                            <input type="hidden" name="notify_new_unassigned" value="0" />
                        <?php
                        }
                        ?>
                        <div class="col-md-9 col-md-offset-3"><div class="checkbox"><label><input type="checkbox" name="notify_new_my" value="1" <?php if (!empty($_SESSION[$session_array]['notify_new_my'])) {echo 'checked="checked"';} echo ' '.$disabledText ?> /> <?php echo $hesklang['nwts']; ?> <?php echo $hesklang['s_my']; ?></label></div></div>
                        <?php
                        if ($disabledText == 'disabled')
                        { ?>
                            <input type="hidden" name="notify_new_my" value="<?php echo !empty($_SESSION[$session_array]['notify_new_my']) ? '1' : '0'; ?>">
                        <?php }

                        if ( ! $is_profile_page || $can_view_unassigned)
                        {
                            ?>
                            <div class="col-md-9 col-md-offset-3"><div class="checkbox"><label><input type="checkbox" name="notify_reply_unassigned" value="1" <?php if (!empty($_SESSION[$session_array]['notify_reply_unassigned'])) {echo 'checked="checked"';} echo ' '.$disabledText ?> /> <?php echo $hesklang['ncrt']; ?> <?php echo $hesklang['unas']; ?></label></div></div>
                            <?php
                            if ($disabledText == 'disabled')
                            { ?>
                                <input type="hidden" name="notify_reply_unassigned" value="<?php echo !empty($_SESSION[$session_array]['notify_reply_unassigned']) ? '1' : '0'; ?>">
                            <?php }
                        }
                        else
                        {
                            ?>
                            <input type="hidden" name="notify_reply_unassigned" value="0" />
                        <?php
                        }
                        ?>
                        <div class="col-md-9 col-md-offset-3"><div class="checkbox"><label><input type="checkbox" name="notify_reply_my" value="1" <?php if (!empty($_SESSION[$session_array]['notify_reply_my'])) {echo 'checked="checked"';} echo ' '.$disabledText ?> /> <?php echo $hesklang['ncrt']; ?> <?php echo $hesklang['s_my']; ?></label></div></div>
                        <div class="col-md-9 col-md-offset-3"><div class="checkbox"><label><input type="checkbox" name="notify_assigned" value="1" <?php if (!empty($_SESSION[$session_array]['notify_assigned'])) {echo 'checked="checked"';} echo ' '.$disabledText ?> /> <?php echo $hesklang['ntam']; ?></label></div></div>
                        <div class="col-md-9 col-md-offset-3"><div class="checkbox"><label><input type="checkbox" name="notify_note" value="1" <?php if (!empty($_SESSION[$session_array]['notify_note'])) {echo 'checked="checked"';} echo ' '.$disabledText ?> /> <?php echo $hesklang['ntnote']; ?></label></div></div>
                        <div class="col-md-9 col-md-offset-3"><div class="checkbox"><label><input type="checkbox" name="notify_pm" value="1" <?php if (!empty($_SESSION[$session_array]['notify_pm'])) {echo 'checked="checked"';} echo ' '.$disabledText ?> /> <?php echo $hesklang['npms']; ?></label></div></div>
                        <?php
                        if ($disabledText == 'disabled')
                        { ?>
                            <input type="hidden" name="notify_reply_my" value="<?php echo !empty($_SESSION[$session_array]['notify_reply_my']) ? '1' : '0'; ?>">
                            <input type="hidden" name="notify_assigned" value="<?php echo !empty($_SESSION[$session_array]['notify_assigned']) ? '1' : '0'; ?>">
                            <input type="hidden" name="notify_note" value="<?php echo !empty($_SESSION[$session_array]['notify_note']) ? '1' : '0'; ?>">
                            <input type="hidden" name="notify_pm" value="<?php echo !empty($_SESSION[$session_array]['notify_pm']) ? '1' : '0'; ?>">
                        <?php }

                        if ($_SESSION['isadmin']) { ?>
                            <div class="col-md-9 col-md-offset-3"><div class="checkbox"><label><input type="checkbox" name="notify_note_unassigned" value="1" <?php if (!empty($_SESSION[$session_array]['notify_note_unassigned'])) {echo 'checked="checked"';}?>> <?php echo $hesklang['notify_note_unassigned']; ?></label></div> </div>
                        <?php
                        }
                    }

                    ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                    <?php
                    if ($action == 'profile_page')
                    { ?>
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>">
                        <input type="submit" class="btn btn-default" value="<?php echo $hesklang['update_profile']; ?>">
                    <?php
                    } elseif ($action == 'create_user')
                    { ?>
                        <input type="hidden" name="a" value="new" />
                        <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
                        <input type="submit" value="<?php echo $hesklang['create_user']; ?>" class="btn btn-default">
                        <a href="manage_users.php?a=reset_form" class="btn btn-danger"><?php echo $hesklang['refi']; ?></a></p>
                    <?php
                    } elseif ($action == 'edit_user')
                    { ?>
                        <input type="hidden" name="a" value="save" />
                        <input type="hidden" name="userid" value="<?php echo intval( hesk_GET('id') ); ?>" />
                        <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
                        <input type="hidden" name="active" value="<?php echo $_SESSION[$session_array]['active']; ?>">
                        <input class="btn btn-default" type="submit" value="<?php echo $hesklang['save_changes']; ?>" />
                        <a class="btn btn-danger" href="manage_users.php"><?php echo $hesklang['dich']; ?></a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

	<script language="Javascript" type="text/javascript"><!--
	hesk_checkPassword(document.form1.newpass.value);
	//-->
	</script>

	<?php
} // END hesk_profile_tab()

function outputCheckboxJavascript() {
    global $hesk_settings, $hesklang;

    // Get categories and features for each template
    $res = hesk_dbQuery("SELECT `categories`, `heskprivileges`, `id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_templates`");
    $templates = array();
    $finalCatMarkup = "var categories = [];\n";
    $finalFeatMarkup = "var features = [];\n";
    while ($row = hesk_dbFetchAssoc($res)) {
        $templates[$row['id']]['features'] = explode(',', $row['heskprivileges']);
        $templates[$row['id']]['categories'] = explode(',', $row['categories']);
        $jsFeatureArray = array();
        $jsCategoryArray = array();
        foreach ($templates[$row['id']]['features'] as $array) {
            $goodText = "'".$array."'";
            array_push($jsFeatureArray, $goodText);
        }
        foreach ($templates[$row['id']]['categories'] as $array) {
            $goodText = "'".$array."'";
            array_push($jsCategoryArray, $goodText);
        }
        $builtFeatureArray = implode(',', $jsFeatureArray);
        $builtCategoryArray = implode(',', $jsCategoryArray);
        $finalCatMarkup .= "categories[".$row['id']."] = [".$builtCategoryArray."];\n";
        $finalFeatMarkup .= "features[".$row['id']."] = [".$builtFeatureArray."];\n";
    }

    echo "<script>
    ".$finalCatMarkup."
    ".$finalFeatMarkup."
    function updateCheckboxes() {
        // Get the value from the dropdown
        var dropdownValue = $('#permission-tpl').val();
        updateCategoriesAndFeatures(dropdownValue);
    }
    function updateCategoriesAndFeatures(dropdownValue) {
        // Get the category array
        var newCats = categories[dropdownValue];
        var newFeats = features[dropdownValue];
        // Uncheck everything
        $('.cat-checkbox').prop('checked', false);
        $('.feat-checkbox').prop('checked', false);
        newCats.forEach(function(entry) {
            if (entry == 'ALL') {
                $('.cat-checkbox').prop('checked', true);
            } else {
                $('#cat-'+entry).prop('checked', true);
            }
        });
        newFeats.forEach(function(entry) {
            if (entry == 'ALL') {
                $('.feat-checkbox').prop('checked', true);
            } else {
                $('#feat-'+entry).prop('checked', true);
            }
        });
    }
    function setTemplateToCustom() {
        $('#permission-tpl').val('-1');
    }
    </script>";
}