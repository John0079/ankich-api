<?php

namespace App\Http\Controllers;

use App\Paragraph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParagraphController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $paragraphs = DB::table('paragraphs')->paginate(10);
        return $paragraphs;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $requestData = \request()->all();
        $info = Paragraph::forceCreate([
            'index_code' => $requestData['index_code'],
            'title' => $requestData['title'],
            'title_translation' => $requestData['title_translation'],
            'content' => $requestData['content'],
            'content_translation' => $requestData['content_translation'],
            'doc_type' => $requestData['doc_type'],
        ]);

        return response($info);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //3. 获取所有的翻译段落信息
        $paragraph = DB::table('paragraphs')
            ->where('id', $id)
            ->get();

        return $paragraph;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

//        return $request;

        //如果有“翻译内容”的字段，就是用户在交互翻译，否则，只是在修改段落属性
        $action = $request['action'];   // action属性内容有 addParagraph， translateParagraph
        if($action === "translateParagraph"){
            $rs = DB::table('paragraphs')
                ->where('id', $id)
                ->update([
                    'title_translation' =>  $request['title_translation'],
                    'content_translation' =>  $request['content_translation'],
                ]);
            return $rs;

        }

        if($action === "addParagraph"){
            $rs = DB::table('paragraphs')
                ->where('id', $id)
                ->update([
                    'title_translation' =>  $request['title_translation'],
                    'content' =>  $request['content'],
                    'content_translation' =>  $request['content_translation'],
                ]);
            return $rs;
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    /**
     * search paragraph
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        //
        $requestData = \request()->all();

        $likeIndex_code = '%'.$requestData['index_code'].'%';
        $likeTitle = '%'.$requestData['title'].'%';
        $likeTitle_translation = '%'.$requestData['title_translation'].'%';
        $doc_type = $requestData['doc_type'];

        $paragraphs = DB::table('paragraphs')
            ->where('index_code', 'like', $likeIndex_code)
            ->where('title', 'like', $likeTitle)
            ->where('title_translation', 'like', $likeTitle_translation)
            ->when($doc_type,
                function ($query, $doc_type) {
                    return $query->where('doc_type', '=', $doc_type);
                }
            )
            ->paginate(10);

        return $paragraphs;
    }


    public function assignTansTask()
    {
        $request = \request()->all();

        // 1. 更新user表
        DB::table('users')
            ->where('id', $request["userId"])
            ->update([
                'production' =>  $request['production'],
            ]);


        // 2. 更新paragraph表
        DB::table('paragraphs')
            ->where('id', $request["paragraphId"])
            ->update([
                'translators' =>  $request['translators'],
            ]);


        return "ok";
    }

    public function cancelTanslator()
    {
        $request = \request()->all();

        // 1. 更新paragraph表
        DB::table('paragraphs')
            ->where('id', $request["paragraphId"])
            ->update([
                'translators' =>  $request['translators'],
            ]);

        // 2. 更新user表
        $changeUser = $request["changeUser"];
        foreach ($changeUser as $chu){
            DB::table('users')
                ->where('id', $chu["userId"])
                ->update([
                    'production' =>  $chu['production'],
                ]);
        }


        return "ok";
    }

    public function getMyTasks() {
        //1. 判断当前登陆用户
        $user = Auth::user();
        $userId = $user['id'];

        //2. 给出可以获取所有数据的用户的id数组
        $vipUsers = array(45, 46);

        //3. 获取所有的翻译段落信息
        $paragraphs = DB::table('paragraphs')
            ->get(["id", "index_code", "title", "title_translation", "doc_type"]);

        //3.1. 判断用户的production是否为null
        if(!$user["production"]){
            return "no data";
        }

        //4. 获取用户自己的授权任务
        $production = $user["production"];
        $translation = $production["translation"];
        $myParagraphs = DB::table('paragraphs')
            ->whereIn('id', $translation)
            ->get(["id", "index_code", "title", "title_translation", "doc_type"]);

        //5. 根据不同用户角色返回不同的数据
        if(in_array($userId, $vipUsers)){
            return $paragraphs;
        }else{
            return $myParagraphs;
        }
    }

}
