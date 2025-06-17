<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    public function getProfessions()
    {
        $professions = Profession::All();
        return response()->json(
            ['professions' => $professions]
        );
    }
}
