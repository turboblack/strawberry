<tr>
<th colspan="2" width="400">Профаил пользователя <?=$tpl['user']['name']; ?></th>
</tr>

<tr>
<td><b>ICQ</b></td>
<td><?=$tpl['user']['icq']; ?></td>
</tr>

<tr>
<td><b>Домстаница</b></td>
<td><?=$tpl['user']['homepage']; ?></td>
</tr>

<tr>
<td><b>ЖЖ</b></td>
<td><?=$tpl['user']['lj-username']; ?></td>
</tr>

<tr>
<td><b>Откуда</b></td>
<td><?=$tpl['user']['location']; ?></td>
</tr>

<tr>
<td><b>Денежек</b></td>
<td>+ <?=$tpl['user']['money']['plus']; ?> / - <?=$tpl['user']['money']['minus']; ?> <small>(<a href="#" onclick="giveMoney('<?=$tpl['user']['username']; ?>')">добавить (+)</a> / <a href="#" onclick="takeMoney('<?=$tpl['user']['username']; ?>')">отнять (-)</a> / <a href="#" onclick="showMoney('<?=$tpl['user']['username']; ?>')">посмотреть операции</a>)</small></td>
</tr>

<tr>
<td><b>Публикаций</b></td>
<td><?=$tpl['user']['publications']; ?></td>
</tr>

<tr>
<td><b>О себе</b></td>
<td><?=$tpl['user']['about']; ?></td>
</tr>

<tr>
<th colspan="2">Последнии публикации (<a href="<?=$tpl['user']['link']['rss.php/user']; ?>">RSS</a>)</th>
</tr>

<tr>
<td colspan="2">
<?=$tpl['user']['headlines']; ?>
</td>
</tr>