<?php
/*
Plugin Name:	User.Registration
Plugin URI:     http://cutenews.ru
Description:    <strong>Русский:</strong> плагин позволяет посетителям вашего сайта самостоятельно зарегитсрироваться в системе. Возможно подключение с разными шаблонами.<br />В ACP можно указать с каким уровнем будут регистрироваться пользователи (Админ, Редактор, Журналист и Комментатор), так же там можно включить (по умолчанию включена) и настроить защиту от флуда регистрациями.
Version:		1.1
Author:         Пашка
Author URI:     mailto:pashka.89@mail.ru
Application: 	Strawberry
*/

// Fix #1
if(!function_exists('mysql_escape_string'))
{
    /* Что-то похожее на mysql_escape_string */
    function mysql_escape_string($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
	}
}

class userRegistration
{
	function userRegistration()
	{
	global $xfields;
	    if(!is_a($xfields, 'XFieldsData'))
	    {
	        $xfields = new XFieldsData();
		}

		$this -> lang = cute_lang('plugins/registration');

        $this -> settings = new PluginSettings('registration');

        if(!is_array($this -> settings -> settings))
        {
            $this -> setDefSettings();
		}
	}

	function setDefSettings()
	{
	    $this -> settings -> settings = array(
	        'preventRegFlood' => true,
	        'RegDelay'        => 180,
	        'banOnWarns'      => 3,
	        'regLevel'        => 4,
		);

		$this -> settings -> save();
	}

	function showForm($tpl = 'default')
	{
	global $sql, $xfields;
	    switch($_POST['step'])
	    {
	        default:
			case 1:
				    $tpl = file_exists(rootpath.'/plugins/registration/'.$tpl.'/form.tpl') ? GetContents(rootpath.'/plugins/registration/'.$tpl.'/form.tpl') : GetContents(rootpath.'/plugins/registration/default/form.tpl');

				    $replaces = array(
				        '{lang.RegNewUser}' => $this -> lang['regNewUser'],
				        '{lang.Login}'      => $this -> lang['regLogin'],
				        '{lang.Passw}'      => $this -> lang['regPassw'],
				        '{lang.Re}'         => $this -> lang['regRe'],
				        '{lang.Nick}'       => $this -> lang['regNick'],
				        '{lang.EMail}'      => $this -> lang['regEmail'],
				        '{lang.AutoLogin}'  => $this -> lang['regAutoLogin'],
					);

					if(!$this -> settings -> settings[$_SERVER['REMOTE_ADDR']])
					{
					    $this -> settings -> settings[$_SERVER['REMOTE_ADDR']] = array('warns' => 0);
					}
			    break;
			case 2:
			        if($this -> settings -> settings['preventRegFlood'] === true && (time - $this -> settings -> settings[$_SERVER['REMOTE_ADDR']]['LastRegTime']) < $this -> settings -> settings['RegDelay'])
			        {
			            $tpl = file_exists(rootpath.'/plugins/registration/'.$tpl.'/regError.tpl') ? GetContents(rootpath.'/plugins/registration/'.$tpl.'/regError.tpl') : GetContents(rootpath.'/plugins/registration/default/regError.tpl');
			            $replaces = array(
			            	'{lang.Error}'     => $this -> lang['regError'],
			            	'{lang.ErrorText}' => $this -> lang['regErrorFlood'],
						);

						$this -> settings -> settings[$_SERVER['REMOTE_ADDR']]['warns']++;

						if($this -> settings -> settings[$_SERVER['REMOTE_ADDR']]['warns'] >= $this -> settings -> settings['banOnWarns'])
						{
							if(!$sql -> select(array('table' => 'ipban', 'where' => array('ip = '.$_SERVER['REMOTE_ADDR'])))){
								$sql -> insert(
									array(
									    'table'  => 'ipban',
									    'values' => array(
											'ip' => $_SERVER['REMOTE_ADDR']
										)
							    	)
								);
							}
						}

						break;
					}

			        if($_POST['register']['passw1'] != $_POST['register']['passw2'] || $_POST['register']['passw1'] != mysql_escape_string($_POST['register']['passw1']))
			        {
			            $tpl = file_exists(rootpath.'/plugins/registration/'.$tpl.'/regError.tpl') ? GetContents(rootpath.'/plugins/registration/'.$tpl.'/regError.tpl') : GetContents(rootpath.'/plugins/registration/default/regError.tpl');
			            $replaces = array(
			            	'{lang.Error}'     => $this -> lang['regError'],
			            	'{lang.ErrorText}' => $this -> lang['regErrorPasswords'],
						);

						break;
					}

					if(!preg_match('/^[\.a-z0-9_\-]+[@][a-z0-9_\-]+([.][a-z0-9_\-]+)+[a-z]{1,4}$/i', $_POST['register']['email']))
					{
			            $tpl = file_exists(rootpath.'/plugins/registration/'.$tpl.'/regError.tpl') ? GetContents(rootpath.'/plugins/registration/'.$tpl.'/regError.tpl') : GetContents(rootpath.'/plugins/registration/default/regError.tpl');
			            $replaces = array(
			            	'{lang.Error}'     => $this -> lang['regError'],
			            	'{lang.ErrorText}' => $this -> lang['regErrorMail'],
						);

						break;
					}

					if($sql -> select(array('table' => 'users', 'where' => array('username = '.mysql_escape_string($_POST['register']['login']), 'or', 'name = '.mysql_escape_string($_POST['register']['nick'])))))
					{
			            $tpl = file_exists(rootpath.'/plugins/registration/'.$tpl.'/regError.tpl') ? GetContents(rootpath.'/plugins/registration/'.$tpl.'/regError.tpl') : GetContents(rootpath.'/plugins/registration/default/regError.tpl');
			            $replaces = array(
			            	'{lang.Error}'     => $this -> lang['regError'],
			            	'{lang.ErrorText}' => $this -> lang['regErrorName'],
						);

						break;
					}

			        $this -> settings -> settings[$_SERVER['REMOTE_ADDR']]['LastRegTime'] = time;

					$sql -> insert(
						array(
							'table'  => 'users',
							'values' => array(
								'date'      => time, # --Без поправки по часовому поясу--
					            'usergroup' => $this -> settings -> settings['regLevel'],
					            'username'  => mysql_escape_string($_POST['register']['login']),
					            'password'  => md5x($_POST['register']['passw1']),
					            'name'      => mysql_escape_string($_POST['register']['nick']),
					            'mail'      => $_POST['register']['email'],
					            'hide_mail' => 1
							)
						)
					);

					$tpl = file_exists(rootpath.'/plugins/registration/'.$tpl.'/regOk.tpl') ? GetContents(rootpath.'/plugins/registration/'.$tpl.'/regOk.tpl') : GetContents(rootpath.'/plugins/registration/default/regOk.tpl');

					$replaces = array(
			            '{lang.Ok}' => $this -> lang['regOk'],
					);

					if($_POST['register']['autologin'] == true)
					{
						$replaces = array(
			            	'{lang.Ok}' => $this -> lang['regOkAndLogined'],
						);

						if(session)
						{
				            $_SESSION['username']      = mysql_escape_string($_POST['register']['login']);
				            $_SESSION['md5_password']  = md5x($_POST['register']['passw1']);
				            $_SESSION['ip']            = $_SERVER['REMOTE_ADDR'];
				            # $_SESSION['login_referer'] = $_SERVER['HTTP_REFERER'];
						}

						$sql->update(
							array(
								'table'  => 'users',
								'where'  => array('username = '.mysql_escape_string($_POST['register']['login'])),
								'values' => array('last_visit' => time)
							)
						);
					}
			    break;
		}


	    foreach($replaces as $from => $to)
	    {
	        $tpl = str_replace($from, $to, $tpl);
		}

	    $this -> settings -> save();

	    return $tpl;
	}
}

add_action('head', 'regInit');
function regInit()
{
global $registration;
	$registration = new userRegistration();
}

function regForm($template){
global $registration;
	return $registration -> showForm($template);
}

add_filter('options', 'regAdminLink');
function regAdminLink($options) {
global $PHP_SELF;
    $lang['regmod'] = cute_lang('plugins/registration');

	$options[] = array(
		'name'		=> $lang['regmod']['regPluginName'],
		'url'		=> 'plugin=regmod',
		'category'	=> 'users',
	);

	return $options;
}

add_action('plugins','regAdmin');
function regAdmin()
{
	global $usergroups;

    if ($_GET['plugin'] == 'regmod')
	{
        $lang['regmod'] = cute_lang('plugins/registration');
        $lang['users']  = cute_lang('editusers');

	    echoheader('users', $lang['regmod']['regPluginName']);

        $settings = new PluginSettings('registration');

        if(!is_array($settings -> settings))
        {
            $settings -> settings = array(
	        	'preventRegFlood' => true,
	        	'RegDelay'        => 180,
	        	'banOnWarns'      => 3,
	        	'regLevel'        => 4,
			);

			$settings -> save();
		}

	    if($_GET['save'])
	    {
			$settings -> settings = array(
	        	'preventRegFlood' => $_POST['regmod']['noflood'] ? true : false,
	        	'RegDelay'        => (int) $_POST['regmod']['noregtime'],
	        	'banOnWarns'      => (int) $_POST['regmod']['warnstoban'],
	        	'regLevel'        => (int) $_POST['regmod']['reglevel'],
			);

			$settings -> save();
		}

		$groups_list= array();

		foreach ($usergroups as $row){
			$groups_list[$row['id']] = $row['name'];
		}

		?>
		<table cellspacing="0" cellpadding="0">
			<tr>
				<td width="25" align="middle">
					<img border="0" src="skins/images/help_small.gif">
				</td>
				<td>
					&nbsp;<a href="javascript:Help('regmod')"><?=$lang['regmod']['regHelpTitle']?></a>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<form method="post" action="?plugin=regmod&t=<?=time?>&save=true">
		<table width="450" border="0" cellspacing="0" cellpadding="2">
			<tr>
				<td align="right" width="265"><?=$lang['regmod']['regPreventRegFlood']?>:&nbsp;</td>
				<td>
					<select name="regmod[noflood]" style="width:100%">
						<option value="0"><?=$lang['regmod']['regNo']?></option>
						<option value="1" <?=($settings -> settings['preventRegFlood']) ? 'selected' : '';?>><?=$lang['regmod']['regYes']?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right"><?=$lang['regmod']['regDelayTime']?>:&nbsp;</td>
				<td>
					<input type="text" name="regmod[noregtime]" style="width:100%" value="<?=$settings -> settings['RegDelay']?>" />
				</td>
			</tr>
			<tr>
				<td align="right"><?=$lang['regmod']['regWarns2Ban']?>:&nbsp;</td>
				<td>
					<input type="text" name="regmod[warnstoban]" style="width:100%" value="<?=$settings -> settings['banOnWarns']?>" />
				</td>
			</tr>
			<tr>
				<td align="right">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right"><?=$lang['regmod']['regLevel']?>:&nbsp;</td>
				<td><?=makeDropDown($groups_list, 'regmod[reglevel]', $settings -> settings['regLevel']); ?>
				</td>
			</tr>
		</table>
		<input type="submit" value="<?=$lang['regmod']['regSave']?>" />
		</form>

		<?php
	    echofooter();
    }
}

add_filter('help-sections', 'regAdminHelp');
function regAdminHelp($help_sections)
{
    $lang['regmod'] = cute_lang('plugins/registration');
    $help_sections['regmod'] = $lang['regmod']['regHelp'];

	return $help_sections;
}
?>