<?php
defined('_JEXEC') or die();


JFormHelper::loadFieldClass('filelist');
class JFormFieldMethods extends JFormFieldFileList
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'Methods';

	/**
	 *  load about available methods to admin config
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
		$gwKey = Ifthenpayhelper::getGatewayKey();
		if (isset($gwKey) && $gwKey != '') {
			$html .= Ifthenpayhelper::getPaymentMethodsHtml($gwKey);
		}



		return $html;
	}


	protected function getLabel(): string
	{
		$html = '';
		$gwKey = Ifthenpayhelper::getGatewayKey();
		if (isset($gwKey) && $gwKey != '') {
			$html = '<label class="hasPopover" title="" data-content="' . JText::_('VMPAYMENT_IFTHENPAY_METHODS_DESC') . '" data-original-title="' . JText::_('VMPAYMENT_IFTHENPAY_METHODS_LBL') . '">
			' . JText::_('VMPAYMENT_IFTHENPAY_METHODS_LBL') . '</label>';
		}

		return $html;
	}
}
