<?php
require 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;

// Function to merge two PDF files while retaining their page orientation and size
function mergePdfFiles($file1, $file2, $outputFile) {
    $pdf = new FPDI();

    $files = [$file1, $file2];
    foreach ($files as $file) {
        $pageCount = $pdf->setSourceFile($file);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($tplIdx);

            // Add a page with the same orientation and size as the template
            if ($size['width'] > $size['height']) {
                $pdf->AddPage('L', [$size['width'], $size['height']]);
            } else {
                $pdf->AddPage('P', [$size['width'], $size['height']]);
            }

            $pdf->useTemplate($tplIdx);
        }
    }

    $pdf->Output($outputFile, 'F');
}

// Function to process the files in the input folder
function processFiles($inputFolder, $outputFolder) {
    $files = glob("$inputFolder/*.pdf");

    $fileGroups = [];
    foreach ($files as $file) {
        $filename = basename($file);
        preg_match('/^(\d+)\s/', $filename, $matches);
        if ($matches) {
            $id = $matches[1];
            $fileGroups[$id][] = $file;
        }
    }

    foreach ($fileGroups as $id => $group) {
        if (count($group) == 2) {
            // Sort the group to ensure ISR file comes before ASR file
            usort($group, function($a, $b) {
                return (strpos($a, 'ISR') !== false) ? -1 : 1;
            });

            $outputFile = "$outputFolder/$id.pdf";
            mergePdfFiles($group[0], $group[1], $outputFile);
            echo "Merged $group[0] and $group[1] into $outputFile\n";
        } else {
            echo "Skipping ID $id: expected 2 files, found " . count($group) . "\n";
        }
    }
}

// Parse command-line arguments
$options = getopt("", ["inputFolder:", "outputFolder:"]);
$inputFolder = $options['inputFolder'] ?? null;
$outputFolder = $options['outputFolder'] ?? null;

if (!$inputFolder || !$outputFolder) {
    die("Usage: php merge_pdfs.php --inputFolder=\"./input\" --outputFolder=\"./output\"\n");
}

// Ensure output folder exists
if (!is_dir($outputFolder)) {
    mkdir($outputFolder, 0777, true);
}

// Process files
processFiles($inputFolder, $outputFolder);
?>
