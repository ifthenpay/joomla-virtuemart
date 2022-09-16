<?php
defined('_JEXEC') or die();


JFormHelper::loadFieldClass('filelist');
class JFormFieldCallbackApk extends JFormFieldFileList
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'CallbackApk';

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

			$apk = Ifthenpayhelper::getAntiPhishingKey();

			$html = '
			<div id="apk">
				<p>
					' . $apk . '
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

			$html = JText::_('VMPAYMENT_IFTHENPAY_CALLBACK_APK_LBL');
		}
		return $html;
	}
}
