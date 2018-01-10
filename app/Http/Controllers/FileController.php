<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;

use Excel;

class FileController extends Controller
{
    /**
    *   PRODUCT IMPORT VIEW
    *
    */
    public function importExportExcelORCSV()
    {
        return view('file_import_export');
    }

    /**
    *   PRODUCT IMPORT ACTION
    *
    */
    public function importFileIntoDB(Request $request)
    {
        if ($request->hasFile('sample_file')) {
            $path = $request->file('sample_file')->getRealPath();
            $records = Excel::load($path)->get();

            if ($records->count() > 0) {

                foreach ($records as $record) {
                    if (!empty($record['name'] && !empty($record['details']))) {
                        Product::create([
                            'name' => $record['name'],
                            'details' => $record['details']
                        ]);
                    }
                }

                dd('Insert Record successfully.');

            } else {
                dd("The File not have records.");
            }
        }
        dd('Request data does not have any files to import.');
    }

    /**
    *   PRODUCT EXPORT ACTION
    *
    */
    public function downloadExcelFile($type)
    {
        $products = Product::all()->toArray();

        return Excel::create('expertphp_demo', function($excel) use ($products) {
            $excel->sheet('sheet name', function($sheet) use ($products) {
                $sheet->fromArray($products);
            });
        })->download($type);
    }
}