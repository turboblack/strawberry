<tr>
<th colspan="2" width="400">������� ������������ <?=$tpl['user']['name']; ?></th>
</tr>

<tr>
<td><b>ICQ</b></td>
<td><?=$tpl['user']['icq']; ?></td>
</tr>

<tr>
<td><b>����������</b></td>
<td><?=$tpl['user']['homepage']; ?></td>
</tr>

<tr>
<td><b>��</b></td>
<td><?=$tpl['user']['lj-username']; ?></td>
</tr>

<tr>
<td><b>������</b></td>
<td><?=$tpl['user']['location']; ?></td>
</tr>

<tr>
<td><b>�������</b></td>
<td>+ <?=$tpl['user']['money']['plus']; ?> / - <?=$tpl['user']['money']['minus']; ?> <small>(<a href="#" onclick="giveMoney('<?=$tpl['user']['username']; ?>')">�������� (+)</a> / <a href="#" onclick="takeMoney('<?=$tpl['user']['username']; ?>')">������ (-)</a> / <a href="#" onclick="showMoney('<?=$tpl['user']['username']; ?>')">���������� ��������</a>)</small></td>
</tr>

<tr>
<td><b>����������</b></td>
<td><?=$tpl['user']['publications']; ?></td>
</tr>

<tr>
<td><b>� ����</b></td>
<td><?=$tpl['user']['about']; ?></td>
</tr>

<tr>
<th colspan="2">��������� ���������� (<a href="<?=$tpl['user']['link']['rss.php/user']; ?>">RSS</a>)</th>
</tr>

<tr>
<td colspan="2">
<?=$tpl['user']['headlines']; ?>
</td>
</tr>