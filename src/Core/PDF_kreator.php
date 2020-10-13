<?php

namespace app\Core;
use FPDF;

/*klasa koja kreira pdf izvestaje */

class PDF_kreator extends FPDF{

    protected $col = 0; // Current column
    protected $y0;      // Ordinate of column start


    function Header()
    {
        // Page header
        global $title;
    
        $this->SetFont('Arial','B',15);
      
    }
    
    function Footer()
    {
        // Page footer
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(128);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
    
    function SetCol($col)
    {
        // Set position at a given column
        $this->col = $col;
        $x = 10+$col*65;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }
    
    function AcceptPageBreak()
    {
        // Method accepting or not automatic page break bilo 2
        if($this->col<0)
        {
            // Go to next column
            $this->SetCol($this->col+1);
            // Set ordinate to top
            $this->SetY($this->y0);
            // Keep on page
            return false;
        }
        else
        {
            // Go back to first column
            $this->SetCol(0);
            // Page break
            return true;
        }
    }
    
    function ChapterTitle($num, $label)
    {
        // Title
        $this->SetFont('Arial','',14);
        $this->SetFillColor(200,220,255);
        $this->Cell(0,8,"Fajl $num : $label",0,1,'C',true);
        $this->Ln(4);
        // Save ordinate
        $this->y0 = $this->GetY();
    }
    
    function ChapterBody($file)
    {
        // Read text file
        $txt = file_get_contents($file);
        // Font
        $this->SetFont('Times','',15);
        // Output text in a 6 cm width column
        $this->MultiCell(190,10,$txt,0,'L',false);
        //$this->Ln();
        // Mention
        $this->SetFont('','I');
        $this->Cell(0,5,'kraj');
        // Go back to first column
        $this->SetCol(0);
    }

    function ChapterBody2($txt)
    {

        // Read text file
       // $txt = file_get_contents($file);
        // Font
        $this->SetFont('Times','',15);
        // Output text in a 6 cm width column
        $this->MultiCell(190,10,$txt,0,'L',false);
        //$this->Ln();
        // Mention
        $this->SetFont('','I');
        $this->Cell(0,5,'kraj');
        // Go back to first column
        $this->SetCol(0);
    }
    
    function PrintChapter($num, $title, $file)
    {
        // Add chapter
        $this->AddPage();
        $this->ChapterTitle($num,$title);
        $this->ChapterBody($file);
    }

    function stampajTekst($num, $title, $tekst)
    {

        // Add chapter
        $this->AddPage();
        $this->ChapterTitle($num,$title);
        $this->ChapterBody2($tekst);
    }
}