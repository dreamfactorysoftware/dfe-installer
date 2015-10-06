<?php
/**
 * Configuration file for the dfe-common library
 */

return [
    //******************************************************************************
    //* Global Options
    //******************************************************************************
    'display-name'      => 'DreamFactory Enterprise&trade;',
    'display-version'   => 'v1.0.x-alpha',
    'display-copyright' => '© DreamFactory Software, Inc. 2012-' . date( 'Y' ) . '. All Rights Reserved.',
    /**
     * Theme selection -- a bootswatch theme name
     * Included are cerulean, darkly, flatly, paper, and superhero.
     * You may also install other compatible themes and use them as well.
     */
    'themes'            => ['auth' => 'darkly', 'page' => 'flatly'],
];