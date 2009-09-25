<?php
/**
 * @package Show
 * @access private
 */

$tpl['form']['saved']['name']     = urldecode($_COOKIE['commentname']);
$tpl['form']['saved']['mail']     = $_COOKIE['commentmail'];
$tpl['form']['saved']['homepage'] = $_COOKIE['commenthomepage'];
$tpl['form']['smilies']           = insertSmilies('short', 0);
$tpl['form']['mail']              = $users[$member['author']]['mail'];
$tpl['form']['homepage']          = $users[$member['author']]['homepage'];
$tpl['form']['avatar']            = $config['path_userpic_upload'].'/'.$member['author'].'.'.$users[$member['author']]['avatar'];
$tpl['form']['icq']               = $users[$member['author']]['icq'];
$tpl['form']['location']          = $users[$member['author']]['location'];
$tpl['form']['about']             = run_filters('news-comment-content', $users[$member['author']]['about']);
$tpl['form']['lj-username']       = '<a href="http://'.$users[$member['author']]['lj_username'].'.livejournal.com/profile"><img src="'.$config['http_script_dir'].'/skins/images/user.gif" alt="[info]" align="absmiddle" border="0"></a><a href="http://'.$users[$member['author']]['lj_username'].'.livejournal.com">'.$users[$member['author']]['lj_username'].'</a>';
$tpl['form']['author']            = $users[$member['author']]['name'];
$tpl['form']['username']          = $users[$member['author']]['username'];
$tpl['form']['user-id']           = $users[$member['author']]['id'];
$tpl['form']                      = run_filters('form-show-generic', $tpl['form']);

ob_start();
include templates_directory.'/'.$tpl['template'].'/form.tpl';
$output = ob_get_clean();
?>

<script type="text/javascript" src="<?=$config['http_script_dir']; ?>/skins/prototype.js"></script>

<script>
function insertext(open, close, spot){
    msgfield = document.forms['comment'].elements['comments'];

    // IE support
    if (document.selection && document.selection.createRange){
        msgfield.focus();
        sel = document.selection.createRange();
        sel.text = open + sel.text + close;
        msgfield.focus();
    }

    // Moz support
    else if (msgfield.selectionStart || msgfield.selectionStart == '0'){
        var startPos = msgfield.selectionStart;
        var endPos = msgfield.selectionEnd;

        msgfield.value = msgfield.value.substring(0, startPos) + open + msgfield.value.substring(startPos, endPos) + close + msgfield.value.substring(endPos, msgfield.value.length);
        msgfield.selectionStart = msgfield.selectionEnd = endPos + open.length + close.length;
        msgfield.focus();
    }

    // Fallback support for other browsers
    else {
        msgfield.value += open + close;
        msgfield.focus();
    }

    return;
}

function complete(request){
    if (request.status == 200){
        document.forms['comment'].elements['comments'].value = '';
        document.forms['comment'].elements['parent'].value = 0;
        quickreply(0);
        Element.hide('error_message');
        $('commentslist').innerHTML = request.responseText;
    } else {
        failure(request);
    }
}

function failure(request){
    Element.show('error_message');
    $('error_message').innerHTML = request.responseText;
}

function call_ajax(that){
	new Ajax.Updater(
	    {success: 'commentslist'},
	    '<?=$config['http_script_dir']; ?>/inc/show.add-comment.php',
	    {
	        insertion: Insertion.Top,
	        onComplete: function(request){complete(request)},
	        onFailure: function(request){failure(request)},
	        parameters: Form.serialize(that),
	        evalScripts: true
	    }
	);
}

</script>

<div id="comment0"></div>
<form action="<?=$PHP_SELF; ?>" method="post" name="form" id="comment" onsubmit="call_ajax(this);return false;">
<div id="error_message" class="error_message" style="display: none;"></div>
<?=$output; ?>
<input type="hidden" id="parent" name="parent" value="0">
<input type="hidden" name="id" value="<?=$post['id']; ?>">
<input type="hidden" name="template" value="<?=$tpl['template']; ?>">
<?=$user_post_query; ?>
</form>