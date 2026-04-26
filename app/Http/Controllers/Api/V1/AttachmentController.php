<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\DecisionVersion;
use App\Services\ConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AttachmentController extends Controller
{
    /**
     * Types MIME dangereux toujours bloqués, quelles que soient les configs admin.
     * Empêche l'exécution de scripts côté serveur ou navigateur.
     */
    private const ALWAYS_BLOCKED_MIME_TYPES = [
        'application/x-php',
        'application/x-httpd-php',
        'application/x-httpd-php-source',
        'application/x-sh',
        'application/x-csh',
        'text/x-php',
        'text/x-shellscript',
        'application/x-msdownload',
        'application/x-msdos-program',
        'application/x-executable',
        'application/x-elf',
        'application/octet-stream', // Exclut les binaires génériques exécutables
    ];

    /**
     * Extensions dangereuses toujours bloquées.
     */
    private const ALWAYS_BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
        'sh', 'bash', 'zsh', 'fish', 'csh',
        'exe', 'dll', 'msi', 'bat', 'cmd', 'com',
        'vbs', 'vbe', 'wsf', 'wsh', 'js', 'jse',
        'py', 'pl', 'rb', 'lua', 'cgi',
        'htaccess', 'htpasswd',
    ];

    public function __construct(private ConfigService $configService)
    {
    }

    /**
     * Upload an attachment.
     * If decision_version_id is provided, link it immediately.
     */
    public function store(Request $request): JsonResponse
    {
        // Taille max depuis la config admin (défaut 10 Mo)
        $maxSizeMb = (int) $this->configService->get('max_file_size_mb', 10);
        $maxSizeKb = $maxSizeMb * 1024;

        $validator = Validator::make($request->all(), [
            'file'                => "required|file|max:{$maxSizeKb}",
            'decision_version_id' => 'nullable|exists:decision_versions,id',
        ], [
            'file.required' => "Aucun fichier n'a été transmis.",
            'file.file'     => "Le fichier transmis est invalide ou a échoué pendant l'upload.",
            'file.max'      => "Le fichier dépasse la taille maximale autorisée de {$maxSizeMb} Mo.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Impossible de téléverser ce fichier.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (! $request->hasFile('file') || ! $request->file('file')->isValid()) {
            return response()->json([
                'message' => 'Le fichier a échoué pendant le téléversement.',
                'errors'  => ['file' => ['Le fichier a échoué pendant le téléversement. Vérifiez la taille autorisée et la configuration PHP.']],
            ], 422);
        }

        $file = $request->file('file');

        // Validation de sécurité (extension + MIME réel)
        $securityCheck = $this->validateFileSecurity($file);
        if ($securityCheck !== null) {
            return response()->json([
                'message' => $securityCheck,
                'errors'  => ['file' => [$securityCheck]],
            ], 422);
        }

        // Validation des types autorisés (config admin)
        $allowedMimeCheck = $this->validateAllowedMimeType($file);
        if ($allowedMimeCheck !== null) {
            return response()->json([
                'message' => $allowedMimeCheck,
                'errors'  => ['file' => [$allowedMimeCheck]],
            ], 422);
        }

        if ($request->filled('decision_version_id')) {
            $version = DecisionVersion::with('decision')->findOrFail($request->decision_version_id);
            $this->authorize('update', $version->decision);
        }

        $filename = $file->getClientOriginalName();
        $mime     = $file->getMimeType(); // getMimeType() utilise finfo (MIME réel, pas déclaré)
        $size     = $file->getSize();

        // Stocker dans un répertoire non-public pour forcer l'accès via contrôleur
        $path = $file->store('attachments', 'local');

        $attachment = Attachment::create([
            'decision_version_id' => $request->decision_version_id,
            'uploader_id'         => $request->user()->id,
            'filename'            => $filename,
            's3_path'             => $path,
            'mime_type'           => $mime,
            'size_bytes'          => $size,
        ]);

        return response()->json([
            'message'    => 'Fichier uploadé.',
            'attachment' => $attachment,
            'url'        => route('attachments.download', $attachment->id),
        ], 201);
    }

    /**
     * Vérifie les extensions et MIME types bloqués inconditionnellement.
     */
    private function validateFileSecurity(UploadedFile $file): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, self::ALWAYS_BLOCKED_EXTENSIONS, true)) {
            return "Ce type de fichier (.$extension) n'est pas autorisé pour des raisons de sécurité.";
        }

        // Vérification du MIME type réel via finfo
        $realMime = $file->getMimeType();
        if (in_array($realMime, self::ALWAYS_BLOCKED_MIME_TYPES, true)) {
            return "Ce type de fichier (MIME: $realMime) n'est pas autorisé pour des raisons de sécurité.";
        }

        return null;
    }

    /**
     * Vérifie si le type de fichier est dans la liste des types autorisés (config admin).
     * Si la config est vide, tous les types non-bloqués sont acceptés.
     */
    private function validateAllowedMimeType(UploadedFile $file): ?string
    {
        $allowedTypes = $this->configService->get('allowed_file_types', '');

        if (empty($allowedTypes)) {
            return null; // Pas de restriction configurée
        }

        $allowedList = array_map('trim', explode(',', $allowedTypes));
        $realMime    = $file->getMimeType();
        $extension   = strtolower($file->getClientOriginalExtension());

        // Accepter si le MIME ou l'extension correspond
        foreach ($allowedList as $allowed) {
            if ($realMime === $allowed || $extension === ltrim($allowed, '.')) {
                return null;
            }
        }

        return "Ce type de fichier ($realMime) n'est pas dans la liste des types autorisés.";
    }

    /**
     * Link existing attachments to a version.
     */
    public function link(Request $request, string $versionId): JsonResponse
    {
        $request->validate([
            'attachment_ids'   => 'required|array',
            'attachment_ids.*' => 'exists:attachments,id',
        ]);

        $version = DecisionVersion::with('decision')->findOrFail($versionId);
        $this->authorize('update', $version->decision);

        Attachment::whereIn('id', $request->attachment_ids)
            ->where('uploader_id', $request->user()->id)
            ->update(['decision_version_id' => $versionId]);

        return response()->json(['message' => 'Fichiers liés à la version.']);
    }

    /**
     * Téléchargement sécurisé d'une pièce jointe (vérifie les droits d'accès).
     */
    public function download(Request $request, string $attachmentId): mixed
    {
        $attachment = Attachment::with('version.decision')->findOrFail($attachmentId);

        // Vérifier que l'utilisateur a accès à la décision parente
        if ($attachment->version?->decision) {
            $this->authorize('view', $attachment->version->decision);
        }

        if (! Storage::disk('local')->exists($attachment->s3_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::disk('local')->download(
            $attachment->s3_path,
            $attachment->filename,
            ['Content-Type' => $attachment->mime_type]
        );
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        $this->authorize('delete', $attachment);

        Storage::disk('local')->delete($attachment->s3_path);
        $attachment->delete();

        return response()->json(['message' => 'Fichier supprimé.']);
    }
}
