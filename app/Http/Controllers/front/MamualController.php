<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Paragraph;
use Illuminate\Support\Facades\DB;

class MamualController extends Controller
{
    /**
     *
     */
    public function getAnkiManual()
    {
        //
        $paragraphs = DB::table('paragraphs')
            ->where('doc_type', '=', 'anki')
            ->get(["id", "index_code", "title", "title_translation", "content_translation"]);

        return $paragraphs;
    }

    /**
     *
     */
    public function getAndroidManual()
    {
        //
        $paragraphs = DB::table('paragraphs')
            ->where('doc_type', '=', 'android')
            ->get(["id", "index_code", "title", "title_translation", "content_translation"]);

        return $paragraphs;
    }

    /**
     *
     */
    public function getIosManual()
    {
        //
        $paragraphs = DB::table('paragraphs')
            ->where('doc_type', '=', 'Ios')
            ->get(["id", "index_code", "title", "title_translation", "content_translation"]);

        return $paragraphs;
    }
}
