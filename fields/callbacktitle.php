<?php
defined('_JEXEC') or die();


JFormHelper::loadFieldClass('filelist');
class JFormFieldCallbackTitle extends JFormFieldFileList
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'CallbackTitle';


	/**
	 * load a title like label for the callback
	 *
	 * @return string
	 */
	protected function getLabel(): string
	{

		$html = '';

		if (Ifthenpayhelper::isGateKeyAntiPhisKeySet()) {
			$html = '<label class="conf_header_3"> ' . JText::_('VMPAYMENT_IFTHENPAY_CALLBACK_TITLE') . ' </label>';
		}

		return $html;
	}


	protected function getInput(): string
	{
		return '';
	}
}
