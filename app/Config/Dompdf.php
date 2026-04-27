<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Dompdf extends BaseConfig
{
    /**
     * Default options untuk Dompdf
     *
     * @var array
     */
    public $options = [
        // Directory settings
        'font_dir'                => FCPATH . 'writable/fonts/',
        'font_cache'              => FCPATH . 'writable/fonts/',
        'temp_dir'                => FCPATH . 'writable/temp/',
        'chroot'                  => FCPATH,
        
        // Parser settings - OPTIMIZED
        'isHtml5ParserEnabled'    => true,
        'isFontSubsettingEnabled' => true,
        'isRemoteEnabled'         => true,
        'isJavascriptEnabled'     => false,
        'isPhpEnabled'            => false,
        
        // Performance optimizations
        'enable_css_float'        => false,  // Disable float untuk performa lebih baik
        'enable_html5_parser'     => true,
        'enable_font_subsetting'  => true,
        'enable_remote'           => true,
        
        // Debug settings - DISABLED untuk production
        'debugKeepTemp'           => false,
        'debugCss'                => false,
        'debugLayout'             => false,
        'debugLayoutLines'        => false,
        'debugLayoutBlocks'       => false,
        'debugLayoutInline'       => false,
        'debugLayoutPaddingBox'   => false,
        
        // Layout settings
        'defaultMediaType'        => 'print',  // Changed to 'print' untuk PDF
        'defaultPaperSize'        => 'A4',
        'defaultPaperOrientation' => 'landscape',
        'defaultFont'             => 'DejaVu Sans',
        
        // Performance settings
        'dpi'                     => 96,       // Lower DPI untuk performa lebih baik
        'fontHeightRatio'         => 1.1,
        'logOutputFile'           => FCPATH . 'writable/logs/dompdf.log',
        
        // Memory and timeout optimizations
        'adminUsername'           => null,
        'adminPassword'           => null,
        'isPhp5Compatible'        => false,
    ];
 
}