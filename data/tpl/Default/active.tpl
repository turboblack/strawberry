<? if ($tpl['post']['dateheader']){ ?>
<div class="dateheader"><a href="<?=$tpl['post']['link']['day']; ?>"><?=$tpl['post']['dateheader']; ?></a></div>
<? } ?>

<div class="post">
<div id="news<?=$tpl['post']['id']; ?>" class="<?=$tpl['post']['alternating']; ?>">
<div class="date"><?=$tpl['post']['date']; ?> <small>(<?=$tpl['post']['ago']; ?>)</small></div>
<div class="title">
<a href="<?=$tpl['post']['link']['post']; ?>"><?=$tpl['post']['title']; ?></a>
<? if ($tpl['post']['pages']){ ?>
<small>(<?=$tpl['post']['pages']; ?>)</small>
<? } ?>
</div>
<hr align="left">
<div class="story"><?=$tpl['post']['short-story']; ?></div>
<? if ($tpl['post']['full-story']){ ?>
<div class="full_link"><a href="<?=$tpl['post']['link']['post']; ?>">читать полностью</a></div>
<? } ?>
<hr align="right">
<div class="attr">
<? if ($tpl['post']['if-right-have']){ ?>
действие:
<a href="<?=$config['http_script_dir']; ?>/index.php?mod=editnews&id=<?=$tpl['post']['id']; ?>" title="Редактировать новость">edit</a>
/ <a href="<?=$config['http_script_dir']; ?>/index.php?mod=editnews&action=delete&selected_news[]=<?=$tpl['post']['id']; ?>" title="Удалить новость">del</a>
<br />
<? } ?>
<? if ($tpl['post']['category']['name']){ ?>
категория: <?=$tpl['post']['category']['name']; ?> /
<? } ?>
<? if ($tpl['post']['keywords']['name']){ ?>
ключслова: <?=$tpl['post']['keywords']['name']; ?> /
<? } ?>
просмотров: <?=$tpl['post']['views']; ?>
<? if ($tpl['post']['comments']){ ?>
 / <a href="<?=$tpl['post']['link']['post']; ?>#comments">комментарии (<?=$tpl['post']['comments']; ?>)</a>
<? } ?>
 / рейтинг: <?=tpl('rating'); ?>
<? if (!tpl('rating', 'check')){ ?>
 / оценить новость:
<form name="cnpostrating" method="post" style="margin: 0; padding: 0;">
<select size="1" name="rating[<?=$tpl['post']['id']; ?>]">
 <option value="0">0</option>
 <option value="1">1</option>
 <option value="2">2</option>
 <option value="3">3</option>
 <option value="4">4</option>
 <option value="5">5</option>
</select>
<input type="submit" value="ok"><!-- голосуй! "суй-суй" - где-то отзывалось эхо -->
</form>
<? } ?>
</div>
</div>
</div>