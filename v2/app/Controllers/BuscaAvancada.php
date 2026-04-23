<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Find\Library\LibraryPlace;

class BuscaAvancada extends BaseController
{
    public function index()
    {
        $libraryCode = $this->request->getVar('library');
        $places = [];
        if ($libraryCode) {
            $LibraryPlace = new LibraryPlace();
            $places = $LibraryPlace->listByLibrary($libraryCode);
        }
        return view('widgets/bibliofind/bibliofind_search_advanced', [
            'libraryCode' => $libraryCode,
            'places' => $places
        ]);
    }
}
