<?php
defined('_JEXEC') or die();


JFormHelper::loadFieldClass('filelist');
class JFormFieldUpdate extends JFormFieldFileList
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'Update';

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
		if (Ifthenpayhelper::hasUpdate()) {

			$html = '
			<div id="callback">
				<h2>
					' . JText::_('VMPAYMENT_IFTHENPAY_UPDATE_DESC_TITLE') . '
				</h2>
				<p>
					' . JText::_('VMPAYMENT_IFTHENPAY_UPDATE_DESC') . '
				</p>
				<img src="'. IFT_URL_IMAGES_FOLDER .'/system_update.png">
			</div>
				';

		}

		return $html;
	}



	protected function getLabel(): string
	{

		$html = '';

		if (Ifthenpayhelper::hasUpdate()) {

			$html = JText::_('VMPAYMENT_IFTHENPAY_UPDATE_LBL');
		}
		return $html;
	}
}
