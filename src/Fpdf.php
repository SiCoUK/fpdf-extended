<?php
/**
 * SiCo FPdf Extension
 * Simply allows access to some private variables
 *
 * @version 16th March 2017
 * @author Simon Corless simon@sico.co.uk
 * @copyright Copyright Simon Corless 2017
 */
namespace SiCoUK\FpdfExtended;

class Fpdf extends \FPDF
{
    /**
     * Threshold used to trigger page breaks
     * 
     * @return type
     */
    public function getPageBreakTrigger()
    {
        return $this->PageBreakTrigger;
    }
    
    /**
     * Current orientation
     * 
     * @return type
     */
    public function getCurOrientation()
    {
        return $this->CurOrientation;
    }
}