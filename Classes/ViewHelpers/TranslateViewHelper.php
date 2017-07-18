<?php
namespace ROQUIN\RoqNewsevent\ViewHelpers;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper as FluidTranslateViewHelper;

/**
 * Copyright (c) 2012, ROQUIN B.V. (C), http://www.roquin.nl
 *
 * @author:         J. de Groot <jochem@roquin.nl>
 * @file:           EventController.php
 * @description:    Translate view helper, extending the fluid translate viewhelper
 */
class TranslateViewHelper extends FluidTranslateViewHelper
{
    /**
     * Render translation
     *
     * @param string $key Translation Key
     * @param string $id Translation Key compatible to TYPO3 Flow
     * @param string $default If the given locallang key could not be found, this value is used. If this argument is
     *     not set, child nodes will be used to render the default
     * @param bool $htmlEscape TRUE if the result should be htmlescaped. This won't have an effect for the default
     *     value
     * @param array $arguments Arguments to be replaced in the resulting string
     * @param string $extensionName UpperCamelCased extension key (for example BlogExample)
     * @return string The translated key or tag body if key doesn't exist
     */
    public function render(
        $key = null,
        $id = null,
        $default = null,
        $htmlEscape = null,
        array $arguments = null,
        $extensionName = null
    ) {
        $value = parent::render($key, $id, $default, $htmlEscape, $arguments, $extensionName);

        if (!isset($value)) {
            $value = LocalizationUtility::translate($this->arguments['key'], 'roq_newsevent', $this->arguments);
        }

        return $value;
    }
}
