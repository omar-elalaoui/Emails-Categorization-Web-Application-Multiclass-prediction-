<?php

namespace App\Http\Controllers;

use App\Exports\EmailsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmailsExportController extends Controller
{
    public function export(){
        return Excel::download(new EmailsExport, 'emails.xlsx');
    }
}
