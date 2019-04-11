<?php

namespace Canvas\Http\Controllers;

use Exception;
use Canvas\Models\Topic;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;

class TopicController extends Controller
{
    /**
     * Get all of the topics.
     *
     * @return View
     */
    public function index()
    {
        $data = [
            'topics' => Topic::orderByDesc('created_at')->withCount('posts')->get(),
        ];

        return view('canvas::topics.index', compact('data'));
    }

    /**
     * Create a new topic.
     *
     * @return View
     */
    public function create()
    {
        $data = [
            'id' => Str::uuid(),
        ];

        return view('canvas::topics.create', compact('data'));
    }

    /**
     * Edit a given topic.
     *
     * @param string $id
     *
     * @return View
     */
    public function edit(string $id)
    {
        $data = [
            'topic' => Topic::findOrFail($id),
        ];

        return view('canvas::topics.edit', compact('data'));
    }

    /**
     * Save a new topic.
     *
     * @return RedirectResponse
     */
    public function store()
    {
        $data = [
            'id' => request('id'),
            'name' => request('name'),
            'slug' => request('slug'),
        ];

        validator($data, [
            'name' => 'required',
            'slug' => 'required|'.Rule::unique('canvas_topics', 'slug')->ignore(request('id'))
                .'|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/i',
        ])->validate();

        $topic = new Topic(['id' => request('id')]);
        $topic->fill($data);
        $topic->save();

        return redirect(route('canvas.topic.edit', $topic->id))->with('notify', 'Saved!');
    }

    /**
     * Save a given topic.
     *
     * @param string $id
     *
     * @return RedirectResponse
     */
    public function update(string $id)
    {
        $topic = Topic::findOrFail($id);

        $data = [
            'id' => request('id'),
            'name' => request('name'),
            'slug' => request('slug'),
        ];

        validator($data, [
            'name' => 'required',
            'slug' => 'required|'.Rule::unique('canvas_topics', 'slug')->ignore(request('id'))
                .'|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/i',
        ])->validate();

        $topic->fill($data);
        $topic->save();

        return redirect(route('canvas.topic.edit', $topic->id))->with('notify', 'Saved!');
    }

    /**
     * Delete a given topic.
     *
     * @param string $id
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(string $id)
    {
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return redirect(route('canvas.topic.index'));
    }
}
