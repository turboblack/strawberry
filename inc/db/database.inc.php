<?php
$database = array(

// users
'users'        => array(
'date'         => array('type' => 'int'),
'usergroup'    => array('type' => 'int', 'default' => 0),
'username'     => array('type' => 'string'),
'password'     => array('type' => 'string'),
'name'         => array('type' => 'string'),
'mail'         => array('type' => 'string'),
'publications' => array('type' => 'int', 'default' => 0),
'hide_mail'    => array('type' => 'bool'),
'avatar'       => array('type' => 'string'),
'last_visit'   => array('type' => 'int'),
'homepage'     => array('type' => 'string'),
'icq'          => array('type' => 'int'),
'location'     => array('type' => 'string'),
'about'        => array('type' => 'text'),
'lj_username'  => array('type' => 'string'),
'lj_password'  => array('type' => 'string'),
'id'           => array(
	              'type'           => 'int',
	              'auto_increment' => 1,
	              'primary'        => 1
				  ),
'deleted'      => array('type' => 'bool', 'default' => 0)
),

// categories
'categories'  => array(
'id'          => array('type' => 'int', 'primary' => 1),
'name'        => array('type' => 'string'),
'icon'        => array('type' => 'string'),
'url'         => array('type' => 'string'),
'parent'      => array('type' => 'int', 'default' => 0),
'level'       => array('type' => 'int', 'default' => 0),
'template'    => array('type' => 'string'),
'description' => array('type' => 'text'),
'usergroups'  => array('type' => 'string')
),

// comments
'comments' => array(
'date'     => array('type' => 'int'),
'author'   => array('type' => 'string'),
'mail'     => array('type' => 'string'),
'homepage' => array('type' => 'string'),
'ip'       => array('type' => 'string'),
'comment'  => array('type' => 'text'),
'reply'    => array('type' => 'text'),
'post_id'  => array('type' => 'int'),
'user_id'  => array('type' => 'int', 'default' => 0),
'parent'   => array('type' => 'int', 'default' => 0),
'level'    => array('type' => 'int', 'default' => 0),
'id'       => array(
           'type'           => 'int',
           'auto_increment' => 1,
           'primary'        => 1
           )
),

// news
'news'     => array(
'date'     => array('type' => 'int'),
'author'   => array('type' => 'string'),
'title'    => array('type' => 'string'),
'short'    => array('type' => 'int', 'default' => 0),
'full'     => array('type' => 'int', 'default' => 0),
'avatar'   => array('type' => 'string'),
'category' => array('type' => 'string'),
'url'      => array('type' => 'string'),
'id'       => array(
           'type'           => 'int',
           'auto_increment' => 1,
           'primary'        => 1
           ),
'views'    => array('type' => 'int', 'default' => 0),
'comments' => array('type' => 'int', 'default' => 0),
'hidden'   => array('type' => 'bool', 'default' => 0),
'sticky'   => array('type' => 'bool', 'default' => 0)
),

// ipban
'ipban' => array(
'ip'    => array('type' => 'string'),
'count' => array('type' => 'int', 'default' => 0)
),

// flood
'flood'   => array(
'date'    => array('type' => 'int'),
'ip'      => array('type' => 'string'),
'post_id' => array('type' => 'int', 'primary' => 1)
),

// story
'story'   => array(
'post_id' => array('type' => 'int', 'primary' => 1),
'short'   => array('type' => 'text'),
'full'    => array('type' => 'text')
),

// usergroups
'usergroups'  => array(
'id'          => array(
                'type'           => 'int',
                'auto_increment' => 1,
                'primary'        => 1
                ),
'name'        => array('type' => 'string'),
'access'      => array('type' => 'text'),
'permissions' => array('type' => 'text')
),

// money
'money'      => array(
'to'         => array('type' => 'string'),
'from'       => array('type' => 'string'),
'motivation' => array('type' => 'text'),
'money'      => array('type' => 'string'),
'date'       => array('type' => 'int')
),

//lang
'lang' => array(
'id'   => array('type' => 'string', 'permanent' => 1),
'name' => array('type' => 'string'),
'text' => array('type' => 'text')
),

);
?>