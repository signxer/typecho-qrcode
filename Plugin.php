<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 为每篇文章显示一个二维码，可以直接用手机扫描，方便手机查看
 *
 * @package QRCode
 * @author aneasystone
 * @author signxer
 * @version 1.1-MOD
 * @link http://www.aneasystone.com
 * @link https://github.com/signxer
 */
class QRCode_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
    	Typecho_Plugin::factory('Widget_Archive')->footer = array('QRCode_Plugin', 'footer');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('QRCode_Plugin', 'render');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
    	/** 二维码尺寸 */
    	$name = new Typecho_Widget_Helper_Form_Element_Text(
    			'size', NULL, '200', _t('二维码尺寸'), _t('不宜设置的太小，对于比较长的网址生成的二维码可能不正确'));
    	$form->addInput($name);
      	/** 二维码前景色 */
    	$dark = new Typecho_Widget_Helper_Form_Element_Text(
    			'dark', NULL, '#000000', _t('二维码前景色'), _t(''));
    	$form->addInput($dark);
        /** 二维码背景色 */
    	$light = new Typecho_Widget_Helper_Form_Element_Text(
    			'light', NULL, '#ffffff', _t('二维码背景色'), _t(''));
    	$form->addInput($light);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     */
    public static function render($text, $widget)
    {
      $content = $text;
      $content .= '<style>';
      $content .= '.qrcode{ width:{SIZE}px; margin:auto;margin-top:15px;position:relative; text-align:center;}';
      $content .= '.qrcode .qrcode_nr{width:{BACK}px; height:{BACK}px;border:5px solid;border-color: {COLORD};border-radius:20px;background:{COLORL}; text-align:center; position:absolute; display:none;margin-top:{TOP}px;}';
      $content .= '.qrcode .qrcode_nr .arrow{ width:0; height:0; border-top:20px solid {COLORD};border-bottom:20px solid transparent;border-left:20px solid transparent;border-right:20px solid transparent; position:absolute;left:{ARROW}px;bottom:-40px;}';
      $content .= '.qrcode.on .qrcode_nr{ display:block;}';
      $content .= '</style>';
      $content .= '<div class="qrcode" id="qrtext" onmouseover="this.className = \'qrcode on\';" onmouseout="this.className = \'qrcode\';">';
      $content .= '    <div class="qrcode_nr">';
      $content .= '        <div class="qrcode" id="qrcode"></div>';
      $content .= '    	<div class="arrow"></div>';
      $content .= '    </div>';
      $content .= '	<a href="javascript:;">扫描二维码，在手机上阅读</a>';
      $content .= '</div>';
      $qrsize = Typecho_Widget::widget('Widget_Options')->plugin('QRCode')->size;
      $colord = Typecho_Widget::widget('Widget_Options')->plugin('QRCode')->dark;
      $colorl = Typecho_Widget::widget('Widget_Options')->plugin('QRCode')->light;
      $qrsize = $qrsize <= 0 ? 200 : $qrsize;
      $content = str_replace("{SIZE}", $qrsize, $content);
      $content = str_replace("{BACK}", $qrsize+40, $content);
      $content = str_replace("{TOP}", -1*$qrsize-55, $content);
      $content = str_replace("{ARROW}", round(0.5*$qrsize)-5, $content);
      $content = str_replace("{COLORD}", $colord, $content);
      $content = str_replace("{COLORL}", $colorl, $content);
      return $content;
    }

	public static function footer()
	{
    $currentPath = Helper::options()->pluginUrl . '/QRCode/';
    	echo '<script type="text/javascript" src="' . $currentPath . 'assets/qrcode.min.js"></script>' . "\n";
        $js =
<<<EOL
<script type="text/javascript">
function showqr(){
	if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
          document.getElementById("qrtext").style.display = 'none';
     }
     else{
        var qrcode = document.getElementById("qrcode");
        if (qrcode == null) {
            return;
        }

        var url = window.location.href;
        var hashIndex = url.indexOf('#');
        var qrUrl = hashIndex < 0 ? url : url.substring(0, hashIndex);

        new QRCode(document.getElementById("qrcode"), {
            text: qrUrl,
            width: {SIZE},
            height: {SIZE},
            colorDark : "{COLORD}",
            colorLight : "{COLORL}",
            correctLevel : QRCode.CorrectLevel.H
        });
	}
}
showqr();
</script>
EOL;
        $size = Typecho_Widget::widget('Widget_Options')->plugin('QRCode')->size;
        $size = $size <= 0 ? 200 : $size;
        $colord = Typecho_Widget::widget('Widget_Options')->plugin('QRCode')->dark;
        $colorl = Typecho_Widget::widget('Widget_Options')->plugin('QRCode')->light;
        $js = str_replace("{SIZE}", $size, $js);
      	$js = str_replace("{COLORD}", $colord, $js);
      	$js = str_replace("{COLORL}", $colorl, $js);
      	echo $js;
	}
}
