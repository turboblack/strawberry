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
/ �������: <?=tpl('rating'); ?>
<? if (!tpl('rating', 'check')){ ?>
 / ������� �������:
<form name="cnpostrating" method="post" style="margin: 0; padding: 0;">
<select size="1" name="rating[<?=$tpl['post']['id']; ?>]">
 <option value="0">0</option>
 <option value="1">1</option>
 <option value="2">2</option>
 <option value="3">3</option>
 <option value="4">4</option>
 <option value="5">5</option>
</select>
<input type="submit" value="ok"><!-- �������! "���-���" - ���-�� ���������� ��� -->
</form>
<? } ?>
</div>
</div>
</div>