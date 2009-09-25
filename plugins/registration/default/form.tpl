<form method="post">
<table width="400" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td colspan="2" style="border-left:1px dotted black; border-bottom:1px dotted black"><span style="font-family:Georgia; font-size:16px; font-weight:bold">&nbsp;{lang.RegNewUser}</span></td>
  </tr>
  <tr>
    <td align="right" width="125"><span style="font-family:Georgia; font-size:12px">{lang.Login}:&nbsp;</span></td>
    <td width="275"><input type="text" name="register[login]" style="font-family:Georgia; font-size:11px; border:1px dotted black; width:100%" /></td>
  </tr>
  <tr>
    <td align="right"><span style="font-family:Georgia; font-size:12px">{lang.Passw}:&nbsp;</span></td>
    <td><input type="password" name="register[passw1]" style="font-family:Georgia; font-size:11px; border:1px dotted black; width:100%" /></td>
  </tr>
  <tr>
    <td align="right"><span style="font-family:Georgia; font-size:12px">{lang.Passw}<sub>({lang.Re})</sub>:&nbsp;</span></td>
    <td><input type="password" name="register[passw2]" style="font-family:Georgia; font-size:11px; border:1px dotted black; width:100%" /></td>
  </tr>
  <tr>
    <td align="right"><span style="font-family:Georgia; font-size:12px">{lang.Nick}:&nbsp;</span></td>
    <td><input type="text" name="register[nick]" style="font-family:Georgia; font-size:11px; border:1px dotted black; width:100%" /></td>
  </tr>
  <tr>
    <td align="right"><span style="font-family:Georgia; font-size:12px">{lang.EMail}:&nbsp;</span></td>
    <td><input type="text" name="register[email]" style="font-family:Georgia; font-size:11px; border:1px dotted black; width:100%" /></td>
  </tr>
  <tr>
    <td><input type="submit" style="font-family:Georgia; font-size:11px; border:1px dotted black; width:100%; background-color:whitesmoke" value="OK"/></td>
    <td><label style="font-family:Georgia; font-size:12px"><input type="checkbox" name="register[autologin]" value="true" /> {lang.AutoLogin}</label></td>
  </tr>
</table>
<input type="hidden" name="step" value="2" />
</form>