<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Leo Feyer 2005-2011
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Backend
 * @license    LGPL
 * @filesource
 */


/**
 * Class Password
 *
 * Provide methods to handle password fields.
 * @copyright  Leo Feyer 2005-2011
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Controller
 */
class Password extends Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget_pw';


	/**
	 * Always decode entities
	 * @param array
	 */
	public function __construct($arrAttributes=false)
	{
		parent::__construct($arrAttributes);
		$this->decodeEntities = true;
	}


	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'maxlength':
				$this->arrAttributes[$strKey] = ($varValue > 0) ? $varValue : '';
				break;

			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	/**
	 * Validate input and set value
	 * @param mixed
	 * @return string
	 */
	protected function validator($varInput)
	{
		$this->blnSubmitInput = false;

		if (!strlen($varInput) && strlen($this->varValue))
		{
			return '';
		}

		if (utf8_strlen($varInput) < $GLOBALS['TL_CONFIG']['minPasswordLength'])
		{
			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['passwordLength'], $GLOBALS['TL_CONFIG']['minPasswordLength']));
		}

		if ($varInput != $this->getPost($this->strName . '_confirm'))
		{
			$this->addError($GLOBALS['TL_LANG']['ERR']['passwordMatch']);
		}

		if ($varInput == $GLOBALS['TL_USERNAME'])
		{
			$this->addError($GLOBALS['TL_LANG']['ERR']['passwordName']);
		}

		$varInput = parent::validator($varInput);

		if (!$this->hasErrors())
		{
			$this->blnSubmitInput = true;
			$_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['MSC']['pw_changed'];
			$strSalt = substr(md5(uniqid(mt_rand(), true)), 0, 23);

			return sha1($strSalt . $varInput) . ':' . $strSalt;
		}

		return '';
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		return sprintf('<input type="password" name="%s" id="ctrl_%s" class="tl_text tl_password%s" value=""%s onfocus="Backend.getScrollOffset();" />%s%s',
						$this->strName,
						$this->strId,
						(strlen($this->strClass) ? ' ' . $this->strClass : ''),
						$this->getAttributes(),
						$this->wizard,
						((strlen($this->description) && $GLOBALS['TL_CONFIG']['showHelp'] && ($GLOBALS['TL_CONFIG']['oldBeTheme'] || !$this->hasErrors())) ? "\n  " . '<p class="tl_help' . (!$GLOBALS['TL_CONFIG']['oldBeTheme'] ? ' tl_tip' : '') . '">'.$this->description.'</p>' : ''));
	}


	/**
	 * Generate the label of the confirmation field and return it as string
	 * @param array
	 * @return string
	 */
	public function generateConfirmationLabel()
	{
		return sprintf('<label for="ctrl_%s_confirm" class="confirm%s">%s</label>',
						$this->strId,
						(strlen($this->strClass) ? ' ' . $this->strClass : ''),
						$GLOBALS['TL_LANG']['MSC']['confirm'][0]);
	}


	/**
	 * Generate the widget and return it as string
	 * @param array
	 * @return string
	 */
	public function generateConfirmation()
	{
		return sprintf('<input type="password" name="%s_confirm" id="ctrl_%s_confirm" class="tl_text tl_password confirm%s" value=""%s onfocus="Backend.getScrollOffset();" />%s',
						$this->strName,
						$this->strId,
						(strlen($this->strClass) ? ' ' . $this->strClass : ''),
						$this->getAttributes(),
						((strlen($GLOBALS['TL_LANG']['MSC']['confirm'][1]) && $GLOBALS['TL_CONFIG']['showHelp']) ? "\n  " . '<p class="tl_help' . (!$GLOBALS['TL_CONFIG']['oldBeTheme'] ? ' tl_tip' : '') . '">'.$GLOBALS['TL_LANG']['MSC']['confirm'][1].'</p>' : ''));
	}
}

?>