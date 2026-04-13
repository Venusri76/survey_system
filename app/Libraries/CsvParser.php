<?php

namespace App\Libraries;

class CsvParser
{
    public function parse(string $filePath): array
    {
        $rows     = [];
        $handle   = fopen($filePath, 'r');
        $isHeader = true;

        while (($line = fgetcsv($handle)) !== false) {
            $line = array_map('trim', $line);

            // Skip blank rows
            if (empty(array_filter($line))) continue;

            // Skip header row
            if ($isHeader) { $isHeader = false; continue; }

            // Need: question + correct + at least 1 wrong
            if (count($line) < 3) continue;

            // Collect wrong options from column 3 onwards
            $wrong = [];
            for ($i = 2; $i < count($line); $i++) {
                if ($line[$i] !== '') $wrong[] = $line[$i];
            }

            if (empty($wrong)) continue;

            $rows[] = [
                'question' => $line[0],
                'correct'  => $line[1],
                'wrong'    => $wrong,
            ];
        }

        fclose($handle);
        return $rows;
    }
}