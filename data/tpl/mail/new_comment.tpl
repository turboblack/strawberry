<?
// inc/show.add-comment.php
?>

Subject: ����� ����������� �� <?=$name; ?>

URL: <?=cute_get_link($row); ?>

���������: <?=$row['title']; ?>

���: <?=$name; ?>

IP: <?=$_SERVER['REMOTE_ADDR']; ?>

E-mail: <?=$mail; ?>

Homepage: <?=$homepage; ?>


�����������:
------------
<?=str_replace('<br />', "\n", $comments); ?>

�������������:
<?=$config['http_script_dir']; ?>/?mod=editcomments&action=editcomment&newsid=<?=$id; ?>&comid=<?=$comid; ?>

�������:
<?=$config['http_script_dir']; ?>/?mod=editcomments&action=doeditcomment&newsid=<?=$id; ?>&delcomid[]=<?=$comid; ?>&deletecomment=yes