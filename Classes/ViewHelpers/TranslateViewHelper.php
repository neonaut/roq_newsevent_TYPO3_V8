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
     * @return NULL|string
     */
    public function render()
    {
        $value = parent::render();

        if (null === $value) {
            $value = LocalizationUtility::translate($this->arguments['key'], 'roq_newsevent', $this->arguments);
        }

        return $value;
    }
}
