<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spipu\Html2Pdf\Html2Pdf;

class ExportController extends Controller
{
    public function mahasiswaExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['No', 'Nama', 'Program Studi', 'Jenis Kelamin', 'Telepon', 'Email', 'Foto'];
        foreach ($headers as $index => $header) {
            $column = $this->columnName($index + 1);
            $sheet->setCellValue($column.'2', $header);
        }

        $dataMahasiswa = Mahasiswa::all();

        $no = 1;
        $row = 3;
        foreach ($dataMahasiswa as $mahasiswa) {
            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $mahasiswa->nama);
            $sheet->setCellValue('C'.$row, $mahasiswa->prodi);
            $sheet->setCellValue('D'.$row, $mahasiswa->jk);
            $sheet->setCellValue('E'.$row, $mahasiswa->telepon);
            $sheet->setCellValue('F'.$row, $mahasiswa->email);
            $sheet->setCellValue('G'.$row, asset('assets/img/'.$mahasiswa->foto));
            $row++;
        }

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $lastRow = $row - 1;
        $sheet->getStyle('A2:G'.$lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $fileName = 'Laporan Data Mahasiswa.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'mahasiswa').'.xlsx';
        (new Xlsx($spreadsheet))->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function mahasiswaPdf()
    {
        $dataMahasiswa = Mahasiswa::all();

        $content = '
        <page>
            <table border="1" align="center">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Program Studi</th>
                    <th>Jenis Kelamin</th>
                    <th>Telepon</th>
                    <th>Email</th>
                </tr>';

        $no = 1;
        foreach ($dataMahasiswa as $mahasiswa) {
            $content .= '
                <tr>
                    <td>'.$no++.'</td>
                    <td>'.e($mahasiswa->nama).'</td>
                    <td>'.e($mahasiswa->prodi).'</td>
                    <td>'.e($mahasiswa->jk).'</td>
                    <td>'.e($mahasiswa->telepon).'</td>
                    <td>'.e($mahasiswa->email).'</td>
                </tr>';
        }

        $content .= '
            </table>
        </page>';

        $html2pdf = new Html2Pdf();
        $html2pdf->writeHTML($content);

        return response($html2pdf->output('Laporan-mahasiswa.pdf', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Laporan-mahasiswa.pdf"');
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }
}
