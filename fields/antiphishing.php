<?php
defined('_JEXEC') or die();


JFormHelper::loadFieldClass('filelist');
class JFormFieldAntiphishing extends JFormFieldFileList
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'Antiphishing';

	/**
	 * Get the anti-phishing key from database, or generate as an hash of "ifthenpayvirtuemart" + the current time
	 *
	 * @return string $inputHtml DOM input element with antiphishing key as its value 
	 */
	protected function getInput(): string
	{


		$antiPhishingKey = (empty($this->value) ? substr(hash('sha512', "ifthenpayvirtuemart" . date("D M d, Y G:i")), -50) : $this->value);

		$inputHtml = '<input class="ifthen_input" type="text" name="anti_phishing_key" id="anti_phishing_key" required="true" value=' . $antiPhishingKey . '>';

		return $inputHtml;
	}
}
