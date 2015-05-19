<?php
	class barcode_class
	{
		public function __construct($code_text)
		{	

			// Loading Font
			$font = new BCGFontFile('../barcode/font/Arial.ttf', 0);

			// The arguments are R, G, B for color.
			$color_black = new BCGColor(0, 0, 0);
			$color_white = new BCGColor(255, 255, 255);

			$drawException = null;
			try {
			        $code = new BCGcode39();
			        $code->setScale(2); // Resolution
			        $code->setThickness(30); // Thickness
			        $code->setForegroundColor($color_black); // Color of bars
			        $code->setBackgroundColor($color_white); // Color of spaces
			        $code->setFont($font); // Font (or 0)
			        $code->parse($code_text); // Text
			} catch(Exception $exception) {
			        $drawException = $exception;
			}
	
/* Here is the list of the arguments
1 - Filename (empty : display on screen)
2 - Background color */
			$drawing = new BCGDrawing('../img/barcodes/'.$code_text.'.png', $color_white);
			if($drawException) {
			        $drawing->drawException($drawException);
			} else {
			        $drawing->setBarcode($code);
		        	$drawing->draw();
			}
// Draw (or save) the image into PNG format.
			$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
		}
	
	}
?>
