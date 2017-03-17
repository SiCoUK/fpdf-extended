<?php
/**
 * SiCo Pdf Table Plugin
 * Based on Table with MultiCells Add On by Olivier
 * http://www.fpdf.de/downloads/addons/3/
 *
 * @version 16th March 2017
 * @author Simon Corless simon@sico.co.uk
 * @copyright Copyright Simon Corless 2017
 */
namespace SiCoUK\FpdfExtended;

class FpdfTable
{
    protected $_pdf;
    
    protected $_rowHeight = 7;
    protected $widths = array();
    protected $aligns = array();
    protected $_headers = array();
    protected $_border = true;
    protected $_fill = false;
    protected $_striping = true;
    protected $_stripeColours = array(
        'r' => 220,
        'g' => null,
        'b' => null,
    );

    /**
     *
     * @param SiCo_Pdf $pdf
     */
    public function __construct($pdf)
    {
        $this->_pdf = $pdf;
    }
    
    /**
     * Set the array of column widths
     * 
     * @param array $w 
     */
    public function setWidths($w)
    {
        $this->widths = $w;
    }

    /**
     * Set the array of column alignments
     * 
     * @param array $a 
     */
    public function setAligns($a)
    {
        $this->aligns = $a;
    }

    /**
     * Set the array of column headers
     * 
     * @param array $h 
     */
    public function setHeaders($h)
    {
        $this->_headers = $h;
    }
    
    /**
     * Set the row height
     * 
     * @param int $h 
     */
    public function setRowHeight($h)
    {
        $this->_rowHeight = (int) $h;
    }
    
    /**
     * Set whether the table should be bordered
     * 
     * @param boolean $border
     */
    public function setBorder($border = true)
    {
        $this->_border = (bool) $border;
    }
    
    /**
     * Set whether the table should be striped
     * 
     * @param boolean $striping
     */
    public function setStriping($striping = true)
    {
        $this->_striping = (bool) $striping;
    }
    
    /**
     * Set the RGB colour of the striping
     * 
     * @param int $r Red value or if nothing else is set acts as a grey level value
     * @param int|null $g Green value
     * @param int|null $b Blue Value
     */
    public function setStripeColour($r, $g = null, $b = null)
    {
        $this->_stripeColours = array(
            'r' => $r,
            'g' => $g,
            'b' => $b,
        );
    }
    
    /**
     * Output the table headers
     */
    public function headers()
    {
        // Check if there are any headers
        if (empty($this->_headers)) {
            return;
        }
        
        // Set a bottom border or surround if border set to true
        $border = 'B';
        if ($this->_border) {
            $border = '1';
        }
        
        // Loop through the headers
        foreach($this->_headers AS $col => $title) {
            $this->_pdf->Cell($this->widths[$col], 7, $title, 'B');
        }
        $this->_pdf->Ln();
    }
    
    /**
     * Calculate the height of the row
     * 
     * @param type $data 
     */
    public function row($data)
    {
        $nb = 0;
        for($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->nbLines($this->widths[$i], $data[$i]));
        }
        $h = $this->_rowHeight * $nb;
        
        // Set up the fill colour
        if (!$this->_striping) {
            $this->_fill = false;
        } else {
            $this->_pdf->SetFillColor($this->_stripeColours['r'], $this->_stripeColours['g'], $this->_stripeColours['b']);
        }
        
        //Issue a page break first if needed
        $this->checkPageBreak($h);
        
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->_pdf->GetX();
            $y = $this->_pdf->GetY();
            //Draw the border
            if ($this->_border) {
                $this->_pdf->Rect($x, $y, $w, $h);
            }
            //Print the text
            $this->_pdf->MultiCell($w, $this->_rowHeight, $data[$i], 0, $a, $this->_fill);
            //Put the position to the right of the cell
            $this->_pdf->SetXY($x+$w, $y);
        }
        
        $this->_fill = !$this->_fill;
        
        //Go to the next line
        $this->_pdf->Ln($h);
    }

    /**
     * If the height h would cause an overflow, add a new page immediately
     * 
     * @param type $h 
     */
    protected function checkPageBreak($h)
    {
        if($this->_pdf->GetY() + $h > $this->_pdf->getPageBreakTrigger()) {
            $this->_pdf->AddPage($this->_pdf->getCurOrientation());
        }
    }
    
    /**
     * Computes the number of lines a MultiCell of width w will take
     * 
     * @param type $w
     * @param type $txt
     * @return int 
     */
    function nbLines($w, $txt)
    {
        if($w == 0) {
            $w = $this->w-$this->rMargin-$this->x;
        }
        
        // Count the number of new line characters if any
        // +1 so that 1 line will always be returned as opposed to 0
        $nl = substr_count($txt, "\n") + 1;
        
        // Compute new lines based on string length as there are no new lines
        // http://stackoverflow.com/questions/4731838/count-new-lines-in-text-file
        if ($nl == 1) {
            return  ceil($this->_pdf->GetStringWidth($txt) / ($w - 1));
        }
        
        return $nl;
    }
}