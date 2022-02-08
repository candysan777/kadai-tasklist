<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::check()) { //ログイン済みの場合は
            $user = \Auth::user();// ログイン済みユーザを取得
            //自分のユーザーIDのタスクのみを取得する
            $tasks = $user->tasks()->orderBy('created_at')->paginate(10);
         
        
        //ログインしていなければwelcome、ログインしていたらtasks.indexを開く
            return view('tasks.index',[
                'tasks' => $tasks,
            ]);
        }
        else {
            return view('welcome');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;
        
            return view('tasks.create',[
                'task' => $task,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
            $request->validate([
                'status' => 'required|max:10',  
                'content' => 'required|max:255',
            ]);
            
            $request->user()->tasks()->create([
                'status' => $request->status,
                'content' => $request->content
            ]);
        
            return redirect('/');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        
        //ログイン済ユーザがその投稿の所有者である場合、投稿の詳細を表示できる
        if(\Auth::id() === $task->user_id){
            return view('tasks.show',[
                'task' => $task,
            ]);
    
        }
        //リダイレクト
       return redirect('/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

       //idの値で投稿を検索して取得
       $task = \App\Task::findOrFail($id); 
       
       //ログイン済ユーザがその投稿の所有者である場合、投稿を編集できる
       if(\Auth::id() === $task->user_id){
            return view('tasks.edit',[
            'task' => $task,
            ]);
       }
       
       //リダイレクト
       return redirect('/');
            
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
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
        $task = Task::FindOrFail($id);
        
        if(\Auth::id() === $task->user_id){
            $request->user()->tasks()->create([
                'status' => $request->status,
                'content' => $request->content
            ]);
        }
        
        return redirect('/');
    }

    /**Fai
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       //idの値で投稿を検索して取得
       $task = \App\Task::findOrFail($id); 
       
       //ログイン済ユーザがその投稿の所有者である場合、投稿を削除できる
       if(\Auth::id() === $task->user_id){
           $task->delete();
       }
       
       //リダイレクト
       return redirect('/');
    }
}
