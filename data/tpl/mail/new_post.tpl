<?
// inc/mod/addnews.mdu
?>

Subject: ����� ���������� �� ����� <?=$config['home_title']; ?>

<?=langdate($config['timestamp_comment'], $added_time); ?> ��������� ����� ������� �� ������������ <?=$member['username']; ?>.

<?=$title; ?>
<?=replace_news('admin', $short_story); ?>

--
<?=$config['http_home_url']; ?>?id=<?=$id; ?>