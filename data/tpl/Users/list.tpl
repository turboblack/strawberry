<? if (!$tpl['user']['header']){ ?>
<? $tpl['user']['header'] = true; ?>
<tr>
<th width="100">
���
</th>
<th width="180">
���� �����������
</th>
<th width="180">
��������� �����
</th>
<th width="100">
������
</th>
</tr>
<? } ?>

<tr>
<td><a href="<?=$tpl['user']['link']['home/user']; ?>"><?=$tpl['user']['name']; ?></a></td>
<td><?=$tpl['user']['date']; ?></td>
<td><?=$tpl['user']['last_visit']; ?></td>
<td><?=$tpl['user']['usergroup']; ?></td>
</tr>