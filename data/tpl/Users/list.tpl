<? if (!$tpl['user']['header']){ ?>
<? $tpl['user']['header'] = true; ?>
<tr>
<th width="100">
Имя
</th>
<th width="180">
Дата регистрации
</th>
<th width="180">
Последний визит
</th>
<th width="100">
Группа
</th>
</tr>
<? } ?>

<tr>
<td><a href="<?=$tpl['user']['link']['home/user']; ?>"><?=$tpl['user']['name']; ?></a></td>
<td><?=$tpl['user']['date']; ?></td>
<td><?=$tpl['user']['last_visit']; ?></td>
<td><?=$tpl['user']['usergroup']; ?></td>
</tr>