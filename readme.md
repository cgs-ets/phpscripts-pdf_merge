
Script used to merge 2 NAPLAN PDF files into a single PDF file. 

**Installation**

 - Install PHP
 - Install [composer](https://getcomposer.org/download/)
 - In your terminal
	 - `git clone git@github.com:cgs-ets/phpscripts-pdf_merge.git pdf_merge`
	 - `cd pdf merge`
	 - `composer install`
	 - Run the command:
		 - **Usage:** `php merge_pdfs.php --inputFolder="/path/to/input" --outputFolder="/path/to/output"`
		 - **Example:** `php merge_pdfs.php --inputFolder="../NAPLAN/Year 5 individual reports" --outputFolder="../NAPLAN/Year 5 individual reports merged"`
