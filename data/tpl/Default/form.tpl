<noscript><div class="error_message">����� ��������� ������������ JavaScript, ����� ����������� �� �� ��������. ����� ����, ��.</div><br /></noscript>
<a name="comments"></a>
<div>�������� ����������� � &laquo;<?=$tpl['post']['title']; ?>&raquo;</div>
<div class="comment_form">
<? if (!$tpl['if-logged']){ ?>
���<br /><input type="text" name="name" value="<?=$tpl['form']['saved']['name']; ?>"><br />
E-mail<br /><input type="text" name="mail" value="<?=$tpl['form']['saved']['mail']; ?>"><br />
�����������<br /><input type="text" name="homepage" value="<?=($tpl['form']['saved']['homepage'] ? $tpl['form']['saved']['homepage'] : 'http://'); ?>"><br />
<? } ?>
<?=$tpl['form']['smilies']; ?>
<br />
<textarea cols="40" rows="6" name="comments" style="overflow-x: hidden;overflow-y: visible;width: 350px;height: 100px;"></textarea>
<br />
<input type="submit" name="submit" value="   ��������   " accesskey="s" style="cursor: hand;">
<br />
<label for="rememberme"><input type="checkbox" id="rememberme" name="rememberme" value="on" checked> ��������� ���?</label>
<label for="sendcomments"><input type="checkbox" id="sendcomments" name="sendcomments" value="on"> �������� ����������� �� ��� e-mail?</label>
<br />
<noindex><?=tpl('bbcodes', 1); ?></noindex>
</div>