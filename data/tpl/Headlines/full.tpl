<div class="post">
<? if ($tpl['post']['prev-next']['prev']['title']){ ?>
<small><a href="<?=$tpl['post']['prev-next']['prev']['link']; ?>">&laquo; <?=$tpl['post']['prev-next']['prev']['title']; ?></a></small>
<? } ?>
<? if ($tpl['post']['prev-next']['prev']['title'] and $tpl['post']['prev-next']['next']['title']){ ?>
|
<? } ?>
<? if ($tpl['post']['prev-next']['next']['title']){ ?>
<small><a href="<?=$tpl['post']['prev-next']['next']['link']; ?>"><?=$tpl['post']['prev-next']['next']['title']; ?> &raquo;</a></small>
<? } ?>
<br /><br />

<div id="news<?=$tpl['post']['id']; ?>" class="<?=$tpl['post']['alternating']; ?>">
<div class="date"><?=$tpl['post']['date']; ?> <small>(<?=$tpl['post']['ago']; ?>)</small></div>
<div class="title">
<a href="<?=$tpl['post']['link']['post']; ?>"><?=$tpl['post']['title']; ?></a>
<? if ($tpl['post']['pages']){ ?>
<small>(<?=$tpl['post']['pages']; ?>)</small>
<? } ?>
</div>
<hr align="left">
<div class="story">
<?=($tpl['post']['full-story'] ? $tpl['post']['full-story'] : $tpl['post']['short-story']); ?>

<? if ($tpl['post']['attachment']){ ?>
<p>
<b>������������ �����:</b>
<?=$tpl['post']['attachment']; ?>
</p>
<? } ?>
</div>
<hr align="right">
<div class="attr">
<? if ($tpl['post']['if-right-have']){ ?>
��������:
<a href="<?=$config['http_script_dir']; ?>/index.php?mod=editnews&id=<?=$tpl['post']['id']; ?>" title="������������� �������">edit</a>
/ <a href="<?=$config['http_script_dir']; ?>/index.php?mod=editnews&action=delete&selected_news[]=<?=$tpl['post']['id']; ?>" title="������� �������">del</a>
<br />
<? } ?>
<? if ($tpl['post']['category']['name']){ ?>
���������: <?=$tpl['post']['category']['name']; ?> /
<? } ?>
<? if ($tpl['post']['keywords']['name']){ ?>
���������: <?=$tpl['post']['keywords']['name']; ?> /
<? } ?>
<a href="<?=$tpl['post']['link']['trackback.php/post']; ?>">trackback</a>
/ <a href="<?=$tpl['post']['link']['print.php/post']; ?>">������</a>
/ <a href="<?=$tpl['post']['link']['rss.php/post']; ?>">rss ������������</a>
</div>
</div>
</div>