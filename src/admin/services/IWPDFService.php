<?php

defined('_JEXEC') or die('=;)');
if (!class_exists('TCPDF'))
{
    $path = JPATH_COMPONENT_ADMINISTRATOR . DS . 'services' . DS . 'tcpdf';
    require_once($path . DS . 'config' . DS . 'lang' . DS . 'spa.php');
    require_once($path . DS . 'tcpdf.php');
}

class IWPDFService
{

    private static $_instance = false;
    private $destFile = false;
    private $title = false;

    private function __construct()
    {
        
    }

    /**
     * 
     * @return IWPDFService
     */
    public static function getInstance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new IWPDFService();
        }
        return self::$_instance;
    }

    /**
     * 
     */
    public function newDocument($destFile, $title, $type = 'COM_QUIPU_PDF_INVOICE')
    {
        $this->destFile = $destFile;
        $this->title = $title;
        $this->config = IWConfig::getInstance();
        $subtitulo = JText::sprintf('Generated by ' . $this->config->company_name . ' using QUIPU: the ERP for Joomla! - https://github.com/NachoBrito/QuipuForJoomla/quipu');

        $path = realpath(dirname(dirname(__FILE__)));
        $logo_file = IWConfig::getInstance()->logo;
        $logo = $path . DS . $logo_file;
        if (!file_exists($logo))
        {
            $logo = '';
        }
        $this->pdf = new QuipuPDF();
        $this->pdf->documentType = JText::_($type);
        $this->pdf->SetCreator('Quipu for Joomla!');
        //$this->pdf->SetAuthor($this->empresa->nombre);
        $this->pdf->SetTitle($title);
        //$this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('expertosjoomla, erp');

        $this->pdf->SetHeaderData($logo, PDF_HEADER_LOGO_WIDTH, $title, $subtitulo);

        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', 9));
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);        
        $this->pdf->SetFont('helvetica', '', 9);
    }

    /**
     * 
     * @param type $html
     */
    public function addHTMLPage($html)
    {
        $this->pdf->AddPage();
        $this->pdf->writeHTML($html);
    }

    /**
     * 
     */
    public function closeDocument()
    {
        $this->pdf->Output($this->destFile, 'F');

        return $this->destFile;
    }

}

/**
 * Utilizamos una subclase de TCPDF para personalizar la cabecera.
 */
class QuipuPDF extends TCPDF
{

    static $_iwHeaderHTML = NULL;

    //Page header
    public function Header()
    {
        if (!self::$_iwHeaderHTML)
        {
            $data = $this->getHeaderData();
            $html = array();
            $html[] = '<table style="border:none;height:20mm;" cellpadding="3">';
            $html[] = '<tr>';

            $html[] = '<td style="vertical-align:middle" align="left" valign="top" >';
            if ($data['logo'])
            {
                try
                {
                    $stream = base64_encode(file_get_contents($data['logo']));
                } catch (Exception $x)
                {
                    $stream = false;
                }
            } 

            if ($stream)
            {
                $html[] = '<img src="@' . $stream . '" style="width:50mm;margin:auto;"/><br>';
            } else
            {
                $html[] = '<div style="font-size:14pt;font-weight:bold;"><br />' . $this->author . '</div>';
            }


            $html[] = '</td>';
            $html[] = '<td width="50%" align="right">';
            $html[] = '<div style="font-size:18pt;font-weight:bold;color:darkgray">' . $this->documentType . '</div>';
            $html[] = '</td>';
            $html[] = '</tr>';
            $html[] = '</table>';

            self::$_iwHeaderHTML = implode('', $html);
        }
        $this->writeHTML(self::$_iwHeaderHTML);
    }

    /**
     * This method is used to render the page footer.
     * It is automatically called by AddPage() and could be overwritten in your own inherited class.
     * @public
     */
    public function Footer()
    {
        $cur_y = $this->y;
        $this->SetTextColor(0, 0, 0);
        //set style for cell border
        $line_width = 0.85 / $this->k;
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        //print document barcode
        $barcode = $this->getBarcode();
        if (!empty($barcode))
        {
            $this->Ln($line_width);
            $barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
            $style = array(
                'position' => $this->rtl ? 'R' : 'L',
                'align' => $this->rtl ? 'R' : 'L',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'padding' => 0,
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false,
                'text' => false
            );
            $this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
        }
        if (empty($this->pagegroups))
        {
            $pagenumtxt = $this->l['w_page'] . ' ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages();
        } else
        {
            $pagenumtxt = $this->l['w_page'] . ' ' . $this->getPageNumGroupAlias() . ' / ' . $this->getPageGroupAlias();
        }
        $this->SetY($cur_y);

        //pie de página con datos de empresa
        //Print page number
        $this->SetFontSize(8);
        if ($this->getRTL())
        {
            $this->SetX($this->original_rMargin);
            $this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
        } else
        {
            $this->SetX($this->original_lMargin);

            $this->Cell(0, 0, IWConfig::getInstance()->info, 'T', 0, 'L');
            $this->Cell(0, 0, "pág. $pagenumtxt", 'T', 0, 'R');
        }
        $this->SetFontSize(9);
    }

}
