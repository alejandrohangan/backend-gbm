<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attachment = Attachment::with('ticket', 'uploaded_by');
        return response()->json($attachment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->rules());
        $attachment = Attachment::create($request->all());
        return response()->json($attachment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $attachment = Attachment::find($id);

        if (!$attachment) {
            return response()->json(['mensaje' => 'attachment no encontrado'], 404);
        }

        return response()->json($attachment);
    }

    public function download($id)
    {
        $attachment = Attachment::find($id);

        if (!$attachment) {
            return response()->json(['mensaje' => 'Attachment no encontrado'], 404);
        }

        // Path completo: storage/app/public/ + filepath
        $fullPath = storage_path('app/public/' . $attachment->file_path);

        if (!file_exists($fullPath)) {
            return response()->json(['mensaje' => 'Archivo no encontrado en el servidor'], 404);
        }

        // Obtener el nombre original del archivo
        $fileName = basename($attachment->file_path);

        return response()->download($fullPath, $fileName);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attachment $attachment)
    {
        $request->validate($this->rules());
        $attachment->update($request->all());
        return response()->json($attachment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Attachment::destroy($id);
        return response()->json(['mensaje' => 'attachment eliminado!!']);
    }

    public function rules(): array
    {
        return [
            'ticket_id' => ['required', 'exists:tickets,id'],
            'file_path' => ['required', 'string', 'max:255'],
            'uploaded_by' => ['required', 'exists:users,id']
        ];
    }
}
