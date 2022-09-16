<?php
defined('_JEXEC') or die();


JFormHelper::loadFieldClass('filelist');
class JFormFieldCallbackActivate extends JFormFieldFileList
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'CallbackActivate';

	/**
	 * load callback status to admin config
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

		$html = '';

		if (Ifthenpayhelper::isGateKeyAntiPhisKeySet() && !empty(Ifthenpayhelper::getCallbackUrl())) {

			$pluginId = Ifthenpayhelper::getPluginId();
			$params = Ifthenpayhelper::getPaymentMethodParams($pluginId);


			$activateCallbackUrl = 'https://gateway.ifthenpay.com/plugins/callback/?cms=joomla'.
															'&apk=' . $params['anti_phishing_key'] .
															'&token=' . $params['gateway_key']
			;

			$html = '
			<div>
			<a class="btn" href="'. $activateCallbackUrl .'" target="blank">'.JText::_('VMPAYMENT_IFTHENPAY_CALLBACK_ACTIVATE').'</a>
			</div>
				';
		}

		return $html;
	}



	protected function getLabel(): string
	{

		$html = '';

		if (Ifthenpayhelper::isGateKeyAntiPhisKeySet()) {

			$html = '<label class="hasPopover" title="" data-content="'.JText::_('VMPAYMENT_IFTHENPAY_CALLBACK_ACTIVATE_DESC').'" data-original-title="'.JText::_('VMPAYMENT_IFTHENPAY_CALLBACK_ACTIVATE_LBL').'">
			'.JText::_('VMPAYMENT_IFTHENPAY_CALLBACK_ACTIVATE_LBL').'</label>';
		}
		return $html;
	}
}
