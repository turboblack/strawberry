<?
/**
 * @package Show
 * @access private
 */

do {
    if ($allow_active_news or $allow_full_story){
    	include root_directory.'/inc/show.news.php';
    }

    $allow_comments     = run_filters('allow-comments', $allow_comments);
    $allow_comment_form = run_filters('allow-comment-form', $allow_comment_form);

    if ($allow_comments and !$allow_comment_form){
        include root_directory.'/inc/show.comments.php';
    }

    if ($allow_comments and $allow_comment_form){
	    echo '<script type="text/javascript" src="'.$config['http_script_dir'].'/skins/cute.js"></script>';
	    echo '<span id="commentslist">';
        include root_directory.'/inc/show.comments.php';
        echo '</span>';
        include root_directory.'/inc/show.comment-form.php';
    }
} while (0);
?>