<?php
defined('_JEXEC') or die();


JFormHelper::loadFieldClass('filelist');
class JFormFieldCallbackUrl extends JFormFieldFileList
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'CallbackUrl';

	/**
	 * load callback url to admin config
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

		if (Ifthenpayhelper::isGateKeyAntiPhisKeySet()) {

			$urlCallback = Ifthenpayhelper::getCallbackUrl();

			$html = '
			<div id="callback">
				<p>
					' . $urlCallback . '
				</p>
			</div>
				';
		}

		return $html;
	}



	protected function getLabel(): string
	{

		$html = '';

		if (Ifthenpayhelper::isGateKeyAntiPhisKeySet()) {

			$html = JText::_('VMPAYMENT_IFTHENPAY_CALLBACK_URL_LBL');
		}
		return $html;
	}
}
