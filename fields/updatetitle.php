<?php
defined('_JEXEC') or die();


JFormHelper::loadFieldClass('filelist');
class JFormFieldUpdateTitle extends JFormFieldFileList
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'UpdateTitle';





	/**
	 * load a title like label for the callback
	 *
	 * @return string
	 */
	protected function getLabel(): string
	{

		// require helper class
		if (!class_exists('Ifthenpayhelper')) {
			$path = VMPATH_ROOT . '/plugins/vmpayment/ifthenpay/helpers/ifthenpayhelper.php';
			if (file_exists($path)) require_once($path);
		}

		$html = '';

		if (Ifthenpayhelper::hasUpdate()) {
			$html = '<label class="conf_header_3"> ' . JText::_('VMPAYMENT_IFTHENPAY_UPDATE_TITLE') . ' </label>';
		}

		return $html;
	}


	protected function getInput(): string
	{
		return '';
	}
}
