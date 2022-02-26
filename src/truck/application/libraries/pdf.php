<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class pdf {


	function pdf()

	{

		$CI = & get_instance();

		log_message('Debug', 'mPDF class is loaded.');

	}

	function Header() {
        // Logo
			if ($this->getPageWidth() < 230){ //P
				$imgdata = Template::theme_url('images/bar.jpg');
				$this->Image($imgdata, 10, 12, 188, 4, 'JPG');
				
				$imgdata = Template::theme_url('images/lh_logo.png');
				$this->Image($imgdata, 12, 9.5, 12, 12, 'PNG');
				
			} else {
			
				
				$imgdata = Template::theme_url('images/bar.jpg');
				$this->Image($imgdata, 10, 12, 275, 5, 'JPG');
				
				$imgdata = Template::theme_url('images/lh_logo.png');
				$this->Image($imgdata, 12, 9.5, 9, 9, 'PNG');
				
			}
    }

    // Page footer
    function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font


			if ($this->getPageWidth() < 230){ //P

				$imgdata = Template::theme_url('images/bar.jpg');
				$this->Image($imgdata, 10, 280, 188, 3, 'JPG');	
			} else {
			

				
				$imgdata = Template::theme_url('images/bar.jpg');
				$this->Image($imgdata, 10, 195, 275, 3, 'JPG');
				
			}
			
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }


	function load($param=NULL)

	{

		include_once APPPATH.'/third_party/mpdf/mpdf.php';


		if ($params == NULL)

		{

			$param = '"en-GB-x","A4","","",10,10,10,10,6,3';

		}


		return new mPDF($param);

	}

}