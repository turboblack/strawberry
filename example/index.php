<?
include_once '../head.php';

// pgt start
// Старт подсчёта времени генерации страницы (page generation time aka pgt)
$pgt = new microTimer;
$pgt->start();
// end pgt star

// Чтобы вывести таймер нужно написать: echo $pgt->stop();
// это есть в блоке example/output
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<!--block:example/head-->
</head>

<body>
<table width="700" align="center" cellpadding="0" cellspacing="0" style="border: solid 5px #fff;">
 <tr>
  <td width="330" valign="top" class="left" style="border-right: solid 5px #fff;">
  	<!--block:example/left-->
  </td>
  <td valign="top" width="99%" style="background: #fff;">
   <div class="menu">
    <!--block:example/menu-->
   </div>
   <br /><br />
   <div class="content">
    <!--block:example/content-->
   </div>
  </td>
 </tr>
 <tr>
  <td colspan="2" style="padding: 2px;font-size: 10px;">
   <!--block:example/bottom-->
  </td>
 </tr>
</table>

</body>
</html>

<?
// это нужно для работы плагина Drag'n'Drop Blocks
include plugins_directory.'/ddb/foot.php';
?>