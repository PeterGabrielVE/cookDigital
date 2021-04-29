<?php

namespace App\Http\Controllers;

use App\Labels;
use App\Note;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage note'))
        {
            $notes = Note::select(
                [
                    '*',
                ]
            )->where('created_by', '=', Auth::user()->id)->get();

            return view('notes.index', compact('notes'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function create()
    {
        if(\Auth::user()->can('create note'))
        {
            $colors = Labels::$colors;

            return view('notes.create', compact('colors'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create note'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'title' => 'required',
                                   'text' => 'required',
                                   'color' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('notes.index')->with('error', $messages->first());
            }

            $objUser            = Auth::user();
            $post               = $request->all();
            $post['created_by'] = $objUser->id;
            Note::create($post);

            return redirect()->route('notes.index')->with('success', __('Note successfully created.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }


    }

    public function edit($note_id)
    {
        if(\Auth::user()->can('edit note'))
        {
            $colors = Labels::$colors;
            $note   = Note::where('created_by', '=', Auth::user()->id)->where('id', '=', $note_id)->first();

            return view('notes.edit', compact('note', 'colors'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $note_id)
    {
        if(\Auth::user()->can('edit note'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'title' => 'required',
                                   'text' => 'required',
                                   'color' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('notes.index')->with('error', $messages->first());
            }

            $objUser = Auth::user();
            $notes   = Note::where('created_by', '=', Auth::user()->id)->where('id', '=', $note_id)->first();
            $notes->update($request->all());

            return redirect()->route('notes.index')->with('success', __('Note successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }


    }

    public function destroy($note_id)
    {


        if(\Auth::user()->can('delete note'))
        {
            $objUser = Auth::user();
            $note    = Note::find($note_id);
            if($note->created_by == $objUser->id)
            {
                $note->delete();

                return redirect()->route('notes.index')->with('success', __('Note successfully deleted.'));
            }
            else
            {
                return redirect()->route('notes.index')->with('error', __('You can\'t delete note.'));
            }

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }
}
