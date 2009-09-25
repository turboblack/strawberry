<?php
/*
Plugin Name:    Auto link words
Plugin URI:        http://cutenews.ru
Description:    Create a list with words which are automatically converted into links.
Version:        0.2
Application:    Strawberry
Author:            FI-DD
Author URI:        http://english.cutenews.ru/forum/profile.php?mode=viewprofile&u=2
*/


add_filter('news-entry-content', 'link_filter');

add_filter('options', 'link_AddToOptions');
add_action('plugins', 'link_CheckAdminOptions');

function link_filter($output){

    $barword = new PluginSettings('linkwords');

        foreach($barword->settings as $bad){
            list($link, $replacement) = $bad;
            $find = "#(| |\{nl\})$link( |\{nl\})#i";
            $replace = ' <a href="'.$replacement.'">'.$replacement.'</a> ';
            $output = preg_replace($find, $replace, $output);
        }

return $output;

}

function link_AddToOptions($options){
global $PHP_SELF;

    $options[] = array(
        'name'        => 'Linkwords',
        'url'        => 'plugin=linkwords',
        'access'    => '1',
    );

return $options;
}

function link_CheckAdminOptions(){
    if ($_GET['plugin'] == 'linkwords'){link_AdminOptions();}
}

function link_AdminOptions(){
global $PHP_SELF;

    echoheader('options', 'Linkwords');

    $barword = new PluginSettings('linkwords');

    $buffer = '<table border=0 cellpading=0 cellspacing=0 width="645">
              <table border=0 cellpading=0 cellspacing=0 width="645" >
              <form method=post action="'.$PHP_SELF.'?plugin=linkwords">
              <td width=321 height="33"><b>Add a word</b>
              <table border=0 cellpading=0 cellspacing=0 width=379    class="panel" cellpadding="7" >
              <tr>
              <td valign="top" width=350 height="25">Word:<br /><input type="text" name="add_badword">
              <td width=350 height="25">Replacement:<br /><input type="text" name="add_replacement">
              <br />e.g. http://www.domain.com
              </tr>
              <tr>
              <td><input type="submit" value="Add to list">
              </tr>
              </form>
              </table>

    <tr>
    <td width=654 height="11">
        <img height=20 border=0 src="skins/images/blank.gif" width=1>
    </tr><tr>
    <td width=654 height=14>
    <b>Word list</b>
    </tr>
    <tr>
    <td width=654 height=1>
  <table width=641 height=100% cellspacing=2 cellpadding=2>
    <tr>
      <td width=200 class="panel"><b>Word</b></td>
      <td width=340 class="panel"><b>Replacement</b></td>
      <td width=40 class="panel">&nbsp;<b>Action</b></td>
    </tr>';

    if ($words = $barword->settings){
        foreach($words as $key => $bad){
            list($link, $replacement) = $bad;

                $i++;
                if ($i%2 == 0){$bg = ' class="enabled"';}
                else {$bg = ' class="disabled"';}

            if ($bad){$buffer .= '<tr'.$bg.'><td>'.$link.'</td><td>'.$replacement.'</td><td><a href="'.$PHP_SELF.'?plugin=linkwords&amp;action=remove&amp;id='.$key.'">[Remove]</a></td>';}
        }
    }

    $buffer .= '</table></table>';

    if ($_POST['add_badword']){
        $barword -> settings[] = array($_POST['add_badword'], $_POST['add_replacement']);
        $barword -> save();

        $buffer = 'The word was added!<br><br><a href="'.$PHP_SELF.'?plugin=linkwords">Back to the list</a>';
    }

    if ($_GET['action'] == 'remove'){
        unset($barword -> settings[$_GET['id']]);
        $barword -> save();

        $buffer = 'The word was removed from the list!<br><br><a href="'.$PHP_SELF.'?plugin=linkwords">Back to the list</a>';
    }

    echo $buffer;

    echofooter();
}
?>