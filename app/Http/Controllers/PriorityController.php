<?php

namespace App\Http\Controllers;

use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function index()
    {
        return response()->json(Priority::all());
    }

    public function show($id)
    {
        $priority = Priority::find($id);

        if (!$priority) {
            return response()->json(['error' => 'Prioridad no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $priority,
            'message' => 'Prioridad obtenida correctamente'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());
        $priority = Priority::create($request->only('name', 'description'));
        return response()->json($priority, 201);
    }

    public function update(Request $request, $id)
    {
        $priority = Priority::findOrFail($id);
        $request->validate($this->rules());
        $priority->update($request->only('name', 'description'));
        return response()->json($priority);
    }

    public function destroy($id)
    {
        Priority::destroy($id);
        return response()->json(['mensaje' => 'Prioridad eliminada']);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'description' => ['required', 'string', 'min:3', 'max:250'],
        ];
    }
}
