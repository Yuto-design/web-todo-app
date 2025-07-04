<?php

namespace App\Http\Controllers;

use App\Models\TodoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(Request $request){
        if ($request->has('done')) {
            $todos = TodoItem::where(['user_id' => Auth::id(), 'is_done' => true])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $todos = TodoItem::where(['user_id' => Auth::id(), 'is_done' => false])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return view('todo.index', compact('todos'));
    }

    public function create()
    {
        return view('todo.create');
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|max:255',
        ]);

        TodoItem::create(
            [
                'user_id' => Auth::id(),
                'title' => $request->title,
                'is_done' => false,
            ]
        );
        return redirect()->route('todo.index');
    }

    public function show($id){
        $todo = TodoItem::find($id);

        return view('todo.show', compact('todo'));
    }

    public function edit($id){
        $todo = TodoItem::find($id);

        return view('todo.edit', compact('todo'));
    }

    public function update($id, Request $request){
        $request->validate([
            'title' => 'required|max:255',
        ]);

        $todo = TodoItem::find($id);
        $todo->title = $request->title;
        $todo->save();

        return redirect()->route('todo.index');
    }

    public function destroy($id){
        TodoItem::find($id)->delete();

        return redirect()->route('todo.index');
    }

    public function done($id){
        TodoItem::find($id)->update(['is_done' => true]);

        return redirect()->route('todo.index');
    }

    public function undone($id){
        TodoItem::find($id)->update(['is_done' => false]);

        return redirect()->route('todo.index', ['done' => true]);
    }
}
