<?php

namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Controllers\Core\CaseController;
use Behin\SimpleWorkflow\Models\Entities\Repair_reports;
use Behin\SimpleWorkflow\Models\Entities\Part_reports;
use Illuminate\Http\Request;

class BeforeGetDeviceRepairPictures extends Controller
{
    private $case;

    public function __construct()
    {
        // $this->case = CaseController::getById($case->id);
    }

    public static function execute(Request $request)
    {
        if ($request->rows) {
            $rows = $request->rows;
            foreach ($rows as $row){
                if(str_contains($row->file, 'http')){
                    $row->file_image = "<a href='" . $row->file . "' download>دانلود</a>";
                }else{
                    $row->file_image = "<img src='" . url('public/'. $row->file) . "' width='50'>";
                }
            }
        }
        return $rows;
    }

}