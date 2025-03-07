<?php

/**
 * PDF Generator plugin for Craft CMS 4.x.
 *
 * Document helpers
 *
 * @see      https://cooltronic.pl
 * @see      https://potacki.com
 *
 * @copyright Copyright (c) 2022 CoolTRONIC.pl sp. z o.o. by Pawel Potacki
 */

namespace cooltronicpl\documenthelpers\variables;

use Craft;

/**
 * @author    Pawel Potacki
 *
 * @since     0.0.2
 */
class DocumentHelperVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function pdf($template, $destination, $filename, $variables, $attributes)
    {
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();

        if (file_exists($filename) && isset($attributes['date'])) {
            if (filemtime($filename) > $attributes['date']) {
                return $filename;
            }
        }
        $vars['entry'] = $variables->getFieldValues();
        if (isset($attributes['custom'])) {
            $vars['custom'] = $attributes['custom'];
        }
        if (isset($variables['title'])) {
            $vars['title'] = $variables['title'];
        }
        $html = Craft::$app->getView()->renderTemplate($template, $vars);
        if (isset($attributes['header'])) {
            $html_header = Craft::$app->getView()->renderTemplate($attributes['header'], $vars);
        }
        if (isset($attributes['footer'])) {
            $html_footer = Craft::$app->getView()->renderTemplate($attributes['footer'], $vars);
        }

        if (isset($attributes['margin_top'])) {
            $margin_top = $attributes['margin_top'];
        } else {
            $margin_top = 30;
        }
        if (isset($attributes['margin_left'])) {
            $margin_left = $attributes['margin_left'];
        } else {
            $margin_left = 15;
        }
        if (isset($attributes['margin_right'])) {
            $margin_right = $attributes['margin_right'];
        } else {
            $margin_right = 15;
        }
        if (isset($attributes['margin_bottom'])) {
            $margin_bottom = $attributes['margin_bottom'];
        } else {
            $margin_bottom = 30;
        }
        if (isset($attributes['mirrorMargins'])) {
            $mirrorMargins = $attributes['mirrorMargins'];
        } else {
            $mirrorMargins = 0;
        }
        if (isset($attributes['fontDir'])) {
            $fontDir = $attributes['fontDir'];
        } else {
            $fontDir = $defaultConfig['fontDir'];
        }
        if (isset($attributes['fontdata'])) {
            $fontData = $attributes['fontdata'];
        } else {
            $fontData = $defaultFontConfig['fontdata'];
        }

        $pdf = new \Mpdf\Mpdf([
            'margin_top' => $margin_top,
            'margin_left' => $margin_left,
            'margin_right' => $margin_right,
            'margin_bottom' => $margin_bottom,
            'mirrorMargins' => $mirrorMargins,
            'fontDir' => $fontDir,
            'fontdata' => $fontData,
        ]);

        if (isset($attributes['header'])) {
            $pdf_string = $pdf->SetHTMLHeader($html_header);
        }
        if (isset($attributes['footer'])) {
            $pdf_string = $pdf->SetHTMLFooter($html_footer);
        }
        if (isset($attributes['pageNumbers'])) {
            $pdf_string = $pdf->setFooter('{PAGENO}');
        }
        $pdf_string = $pdf->WriteHTML($html);
        if (isset($attributes['title'])) {
            $pdf->SetTitle($attributes['title']);
        } elseif (isset($variables['title'])) {
            $pdf->SetTitle($variables['title']);
        }

        switch ($destination) {
            case 'file':
                $output = \Mpdf\Output\Destination::FILE;
                break;
            case 'inline':
                $output = \Mpdf\Output\Destination::INLINE;
                break;
            case 'download':
                $output = \Mpdf\Output\Destination::DOWNLOAD;
                break;
            case 'string':
                $output = \Mpdf\Output\Destination::STRING_RETURN;
                break;
            default:
                $output = \Mpdf\Output\Destination::FILE;
                break;
        }
        $return = $pdf->Output($filename, $output);
        if ($destination == 'file') {
            return $filename;
        }
        if ($destination == 'download') {
            return $filename;
        }
        if ($destination == 'inline') {
            return $return;
        }
        if ($destination == 'string') {
            return $return;
        }

        return null;
    }
}
