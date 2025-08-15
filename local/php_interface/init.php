<?
function getRealIpAddr2()
{
 $v="";
 if (!empty($_SERVER['HTTP_CLIENT']))
  {
    $ip=$_SERVER['HTTP_CLIENT'];$v="; ";
  }elseif (!empty($_SERVER['HTTP_CLIENT_IP']))
  {
    $ip=$_SERVER['HTTP_CLIENT_IP'];
  }
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
  {
    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
    $ip=$_SERVER['REMOTE_ADDR'];
  }

  return preg_replace("/[^,.0-9]/", '', $ip).$v;
}