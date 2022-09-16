<?php
defined('_JEXEC') or die();


JFormHelper::loadFieldClass('filelist');
class JFormFieldAbout extends JFormFieldFileList
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'About';

	/**
	 *  load about text to admin config
	 *
	 * @return string
	 */
	protected function getInput(): string
	{

				// require helper class
				if (!class_exists('Ifthenpayhelper')) {
					$path = VMPATH_ROOT . '/plugins/vmpayment/ifthenpay/helpers/ifthenpayhelper.php';
					if (file_exists($path)) require_once($path);
				}

		// load language code and scripts
		$langCode = JFactory::getLanguage()->getTag();
		vmJsApi::addJScript('admin_lang', 'var lang = "' . $langCode . '";');
		vmJsApi::addJScript('callback_url', 'var callbackUrl = "' . Ifthenpayhelper::getCallbackUrl() . '";');
		vmJsApi::addJScript('callback_update_url', 'var callbackActivateUrl = "' . IFT_URL_GATEWAY_CALLBACK_ACTIVATE . '";');
		vmJsApi::addJScript('cms', 'var ift_cms = "' . IFT_CMS . '";');

		
		vmJsApi::addJScript('admin', '/plugins/vmpayment/ifthenpay/assets/js/admin.js');
		vmJsApi::css('admin', '/plugins/vmpayment/ifthenpay/assets/css/');

		$lang = JFactory::getLanguage();
		$extension = 'plg_vmpayment_ifthenpay';
		$base_dir = JPATH_SITE;
		$language_tag = $lang->getTag();
		$lang->load($extension, $base_dir, $language_tag, TRUE);


		$logoPath = JURI::root() . '/plugins/vmpayment/ifthenpay/assets/images/logo.png';
		$bannerPath = JURI::root() . '/plugins/vmpayment/ifthenpay/assets/images/banner.png';


		$html = '
		<div class="clear"></div>
		<div class="cont_about">
			<div>
				<img class="logo_ifthenpay float_left col-3" src="' . $logoPath . '" alt="logo">
			</div>
			<div class="cont_version"><p class="logo float_left version">' . JText::_('VMPAYMENT_IFTHENPAY_VERSION') . '<span id="version" >' . IFT_VERSION . '</span></p></div>
			<div class="clear"></div>
			<h4>' . JText::_('VMPAYMENT_IFTHENPAY_SLOGAN') . '</h4>
			<ul>
				<li>' . JText::_('VMPAYMENT_IFTHENPAY_ABOUT_DESC_1') . '</li>
				<li>' . JText::_('VMPAYMENT_IFTHENPAY_ABOUT_DESC_2') . '</li>
				<li>' . JText::_('VMPAYMENT_IFTHENPAY_ABOUT_DESC_3') . '</li>
				<li>' . JText::_('VMPAYMENT_IFTHENPAY_ABOUT_DESC_4') . '</li>
				<li>' . JText::_('VMPAYMENT_IFTHENPAY_ABOUT_DESC_5') . '</li>
				<li>' . JText::_('VMPAYMENT_IFTHENPAY_ABOUT_DESC_6') . '</li>
				<li>' . JText::_('VMPAYMENT_IFTHENPAY_ABOUT_DESC_7') . '</li>
			</ul>
			<img class="banner_ifthenpay" src="' . $bannerPath . '" alt="logo"/>
			<div class="clear"></div>
			<p class="float_left mr-5">' . JText::_('VMPAYMENT_IFTHENPAY_DONT_HAVE_ACCOUNT') . '</p>
			<a href="https://www.ifthenpay.com/downloads/ifmb/contratomb.pdf" class="float_left btn btn-default">' . JText::_('VMPAYMENT_IFTHENPAY_GET_ACCOUNT') . '</a>
			<div class="clear"></div>
		</div>
		';



		return $html;
	}
}
