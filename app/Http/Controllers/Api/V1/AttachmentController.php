<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\DecisionVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    /**
     * Upload an attachment.
     * If decision_version_id is provided, link it immediately.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'decision_version_id' => 'nullable|exists:decision_versions,id',
        ], [
            'file.required' => 'Aucun fichier n’a été transmis.',
            'file.file' => 'Le fichier transmis est invalide ou a échoué pendant l’upload.',
            'file.max' => 'Le fichier dépasse la taille maximale autorisée de 10 Mo.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Impossible de téléverser ce fichier.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! $request->hasFile('file') || ! $request->file('file')->isValid()) {
            return response()->json([
                'message' => 'Le fichier a échoué pendant le téléversement.',
                'errors' => [
                    'file' => ['Le fichier a échoué pendant le téléversement. Vérifiez la taille autorisée et la configuration PHP.'],
                ],
            ], 422);
        }

        if ($request->filled('decision_version_id')) {
            $version = DecisionVersion::with('decision')->findOrFail($request->decision_version_id);
            $this->authorize('update', $version->decision);
        }

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $mime = $file->getClientMimeType();
        $size = $file->getSize();

        // Store in public disk for easy access
        $path = $file->store('attachments', 'public');

        $attachment = Attachment::create([
            'decision_version_id' => $request->decision_version_id, // Can be null if uploading during creation
            'uploader_id' => $request->user()->id,
            'filename' => $filename,
            's3_path' => $path,
            'mime_type' => $mime,
            'size_bytes' => $size,
        ]);

        return response()->json([
            'message' => 'Fichier uploadé.',
            'attachment' => $attachment,
            'url' => Storage::url($path),
        ], 201);
    }

    /**
     * Link existing attachments to a version.
     */
    public function link(Request $request, string $versionId): JsonResponse
    {
        $request->validate([
            'attachment_ids' => 'required|array',
            'attachment_ids.*' => 'exists:attachments,id',
        ]);

        $version = DecisionVersion::with('decision')->findOrFail($versionId);
        $this->authorize('update', $version->decision);

        Attachment::whereIn('id', $request->attachment_ids)
            ->where('uploader_id', $request->user()->id)
            ->update(['decision_version_id' => $versionId]);

        return response()->json(['message' => 'Fichiers liés à la version.']);
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        $this->authorize('delete', $attachment);

        Storage::disk('public')->delete($attachment->s3_path);
        $attachment->delete();

        return response()->json(['message' => 'Fichier supprimé.']);
    }
}
