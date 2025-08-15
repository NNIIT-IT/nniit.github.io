<pre>
<? echo '<br>GET='; print_r($_GET);?>
<? echo '<br>POST='; print_r($_POST);?>
<?
 $location="Location: http://asmu.ru/".htmlspecialchars($_POST['B_URL']);
 Header($location); 
 exit; 
?>
</pre>