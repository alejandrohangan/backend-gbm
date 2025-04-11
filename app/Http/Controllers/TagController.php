<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }

    public function show($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['error' => 'Tag no encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tag,
            'message' => 'Tag obtenido correctamente'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->rules());
        $tag = Tag::create($request->only('name'));
        return response()->json($tag, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $request->validate($this->rules());
        $tag->update($request->only('name'));
        return response()->json($tag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Tag::destroy($id);
        return response()->json(['mensaje' => 'Tag eliminado']);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:50'] // Reducido min:10 a min:3 para ser más flexible
        ];
    }
}
