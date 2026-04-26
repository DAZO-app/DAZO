<?php

namespace Tests\Feature;

use App\Enums\CircleMemberRole;
use App\Enums\DecisionParticipantRole;
use App\Enums\DecisionStatus;
use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Models\Circle;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Tests de sécurité pour l'upload de pièces jointes.
 */
class AttachmentSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $author;
    private Decision $decision;
    private DecisionVersion $version;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->author = User::factory()->create();
        $circle = Circle::factory()->create();
        $circle->members()->create(['user_id' => $this->author->id, 'role' => CircleMemberRole::MEMBER->value]);

        $this->decision = Decision::factory()->create([
            'circle_id' => $circle->id,
            'status'    => DecisionStatus::DRAFT->value,
        ]);
        $this->version = DecisionVersion::factory()->create([
            'decision_id' => $this->decision->id,
            'author_id'   => $this->author->id,
            'is_current'  => true,
        ]);
        $this->decision->participants()->create([
            'user_id' => $this->author->id,
            'role'    => DecisionParticipantRole::AUTHOR->value,
        ]);

        Sanctum::actingAs($this->author);
    }

    public function test_php_file_upload_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent('shell.php', '<?php phpinfo(); ?>');

        $response = $this->postJson('/api/v1/attachments', ['file' => $file]);

        $response->assertStatus(422);
        $this->assertStringContainsString('sécurité', $response->json('message'));
    }

    public function test_shell_script_upload_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent('exploit.sh', '#!/bin/bash\nrm -rf /');

        $response = $this->postJson('/api/v1/attachments', ['file' => $file]);

        $response->assertStatus(422);
    }

    public function test_exe_file_upload_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent('virus.exe', 'MZ binary content');

        $response = $this->postJson('/api/v1/attachments', ['file' => $file]);

        $response->assertStatus(422);
    }

    public function test_pdf_file_upload_is_accepted(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->postJson('/api/v1/attachments', ['file' => $file]);

        $response->assertStatus(201);
    }

    public function test_image_upload_is_accepted(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $response = $this->postJson('/api/v1/attachments', ['file' => $file]);

        $response->assertStatus(201);
    }

    public function test_file_too_large_is_rejected(): void
    {
        // Default max is 10 MB — fake a 15 MB file
        $file = UploadedFile::fake()->create('big.pdf', 15 * 1024, 'application/pdf');

        $response = $this->postJson('/api/v1/attachments', ['file' => $file]);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_upload_is_rejected(): void
    {
        // Réinitialiser l'authentification pour simuler un utilisateur non connecté
        $this->app['auth']->guard('sanctum')->forgetUser();
        $this->refreshApplication(); // Réinitialise le guard

        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $response = $this->postJson('/api/v1/attachments', ['file' => $file]);

        $response->assertUnauthorized();
    }
}
