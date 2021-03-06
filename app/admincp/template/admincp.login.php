<?php /**
 * @package iCMS
 * @copyright 2007-2010, iDreamSoft
 * @license http://www.idreamsoft.com iDreamSoft
 * @author coolmoo <idreamsoft@qq.com>
 * @$Id: login.php 2379 2014-03-19 02:37:47Z coolmoo $
 */
defined('iPHP') OR exit('What are you doing?');
$seccode_url = './public/api.php?app=public&do=seccode&pre=iACP&i=';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>iCMS Administrator's Control Panel</title>
<meta name="renderer" content="webkit">
<meta name="force-rendering" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta content="iDreamSoft Inc." name="Copyright" />
<link rel="stylesheet" href="./app/ui/common/bootstrap/2.3.2/css/bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="./app/ui/common/bootstrap/2.3.2/css/bootstrap-responsive.min.css" type="text/css" />
<link rel="stylesheet" href="./app/ui/common/font-awesome/4.2.0/css/font-awesome.min.css" type="text/css" />
<link rel="stylesheet" href="./app/ui/common/artDialog/6.0.3/ui-dialog.css" type="text/css" />
<link rel="stylesheet" href="./app/ui/common/iCMS-6.0.0.css" type="text/css" />
<!--[if lt IE 9]>
  <script src="./app/ui/common/ie/html5shiv.min.js"></script>
  <script src="./app/ui/common/ie/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="./app/ui/common/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="./app/ui/common/artDialog/6.0.3/dialog-plus-min.js"></script>
<script type="text/javascript" src="./app/ui/common/iCMS-6.0.0.js"></script>
<style>
/* dialog */
.iCMS_dialog .ui-dialog-header { background-color: #333; background-image: -moz-linear-gradient(top, #3c3c3c, #0a0a0a); background-image: -ms-linear-gradient(top, #3c3c3c, #0a0a0a); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#3c3c3c), to(#0a0a0a)); background-image: -webkit-linear-gradient(top, #3c3c3c, #0a0a0a); background-image: -o-linear-gradient(top, #3c3c3c, #0a0a0a); background-image: linear-gradient(top, #3c3c3c, #0a0a0a); background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#3c3c3c', endColorstr='#0a0a0a', GradientType=0); border: 1px solid #111; }
.iCMS_dialog .ui-dialog-header .ui-dialog-close { color: #999; opacity: .4; font-weight: normal; filter: alpha(opacity=40); }
.iCMS_dialog .ui-dialog-header .ui-dialog-close:hover, .iCMS_dialog .ui-dialog-header .ui-dialog-close:focus { opacity: 0.7; filter: alpha(opacity=70); }
/* login */
body { background-color:#f8f8f8;}
.container{display: table;text-align: center;}
.iCMS_login {vertical-align: middle;display: table-cell;height: 600px;}
.btn { -webkit-box-shadow: none !important; -moz-box-shadow: none !important; box-shadow: none !important; background-image: none !important; }
.login {}
.login label {display: inline;padding: 3px 10px 3px 5px; height:30px; line-height: 30px; }
.login input {display: inline;width: 200px; height:28px; line-height: 30px; }
.login label i { background-repeat: no-repeat; background-attachment: scroll; background-position: center; background-color: transparent; width: 16px; display: inline-block; border-right: 1px solid #dddddd; margin-right: 10px; padding: 10px; vertical-align: middle; }
.login label span { text-align: center !important; color: #666666; text-shadow: 0 1px 0 #ffffff; width: 42px;display: inline-block;}
.ipt_uname,.ipt_pass,.ipt_seccode { margin-bottom:20px !important; }
.ipt_uname i { background-image: url('./app/admincp/ui/img/icons/16/user.png'); }
.ipt_pass i { background-image: url('./app/admincp/ui/img/icons/16/lock.png'); }
.ipt_seccode input{width: 60px; }
.ipt_seccode i { background-image: url('./app/admincp/ui/img/icons/16/survey.png'); }
.iCMS_seccode_img{margin-top: 4px;display: inline;width: 80px;height: 30px;}
.iCMS_seccode_text{display: inline;}
.iCMS-logo{text-align: center;margin-bottom: 10px;}
.btn-primary{width: 200px;}
@media (max-width:480px) {
  .container{padding: 20px;}
  .iCMS_login{width:100%; height:auto;position:static;text-align: center;}
  .iCMS-logo{text-align: center;margin-left: 20px;margin-bottom: 10px;}
  .login {display: block;clear: both;margin: 10px auto;}
  .login input {display: inline;width: 120px; height:24px; line-height: 30px; }
  .login label{display: inline;}
  .ipt_seccode input{width: 36px; }
  .iCMS_seccode_text{display: none;}
}

</style>
<script type="text/javascript">
$(function(){
  $(".iCMS_seccode_img,.iCMS_seccode_text").click(function(event) {
      event.preventDefault();
      $(".iCMS_seccode_img").attr('src','<?php echo $seccode_url;?>'+ Math.random());
  });
	$("form").submit(function(){
      var param={
        username:$("#username").val(),
        password:$("#password").val(),
        iACP_seccode :$("#seccode").val(),
        ajax:1
      };

		if(param.username==""){
      $(".btn").blur();
			iCMS.alert("请填写账号!!");
			$("#username").focus();
			return false;
		}
		if(param.password==""){
      $(".btn").blur();
			iCMS.alert("请填写密码!!");
			$("#password").focus();
			return false;
		}
    if(param.seccode==""){
      $(".btn").blur();
      iCMS.alert("请填写验证码!!");
      $("#seccode").focus();
      return false;
    }
		$.post("<?php echo __SELF__; ?>",param,function(json){
				if(json.code=="1"){
					window.location.href ='<?php echo __SELF__; ?>';
				}else{
          $(".iCMS_seccode_img").attr('src','<?php echo $seccode_url;?>'+ Math.random());
          if(json.msg){
            iCMS.alert(json.msg);
          }else{
            iCMS.alert("账号或密码错误!!");
          }
				}
		},'json');
		return false;
	});
})
</script>
</head>
<body>
<div class="container">
  <div class="iCMS_login">
    <a class="iCMS-logo" href="http://www.idreamsoft.com" target="_blank">
      <img src="./app/admincp/ui/iCMS.login-6.0.png" />
    </a>
    <div class="clear mt10"></div>
    <div class="login">
      <form action="<?php echo __SELF__; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="iCMS-Login" target="iPHP_FRAME">
        <div class="ipt_uname">
          <label for="username"><i></i><span>账 号</span></label>
          <input type="text" name="username" id="username" />
        </div>
        <div class="clear"></div>
        <div class="ipt_pass">
          <label for="password"><i></i><span>密 码</span></label>
          <input type="password" name="password" id="password" />
        </div>
        <div class="clear"></div>
        <div class="ipt_seccode">
          <label for="seccode"><i></i><span>验证码</span></label>
          <input type="text" maxlength="4" name="seccode" id="seccode" class="iCMS_seccode">
          <img src="<?php echo $seccode_url;?>" alt="验证码" class="iCMS_seccode_img r3"/>
          <a href="javascript:;" class="iCMS_seccode_text">换一张</a>
        </div>
        <div class="clear"></div>
        <button class="btn btn-large btn-primary" type="submit"><i class="icon-ok icon-white"></i> 登 陆</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
