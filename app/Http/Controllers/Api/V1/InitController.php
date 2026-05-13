<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ConfigService;
use Illuminate\Http\JsonResponse;

class InitController extends Controller
{
    public function __construct(private ConfigService $configService) {}

    public function index(): JsonResponse
    {
        $data = [
            'app_name' => $this->configService->get('app_name'),
            'app_logo' => $this->configService->get('app_logo'),
            'enable_public_front' => filter_var($this->configService->get('enable_public_front'), FILTER_VALIDATE_BOOLEAN),
            'enable_registration' => filter_var($this->configService->get('public_registration'), FILTER_VALIDATE_BOOLEAN),
            'recaptcha_site_key' => $this->configService->get('recaptcha_site_key'),
            'mail_template_wrapper_default' => $this->configService->defaults()['mail_template_wrapper'] ?? '',
        ];

        // Add content pages config
        foreach (['legal', 'privacy', 'terms'] as $key) {
            $data["page_{$key}_enabled"] = $this->configService->get("page_{$key}_enabled");
            $data["page_{$key}_title"] = $this->configService->get("page_{$key}_title");
            $data["page_{$key}_slug"] = $this->configService->get("page_{$key}_slug");
            $data["page_{$key}_content"] = $this->configService->get("page_{$key}_content");
        }

        // Add theme public & meeting config
        $data['theme_public_palette_mode'] = $this->configService->get('theme_public_palette_mode');
        $data['theme_meeting_palette_mode'] = $this->configService->get('theme_meeting_palette_mode');
        foreach (['primary', 'secondary', 'accent', 'bg_main', 'bg_alt', 'text_main', 'text_muted', 'success', 'warning', 'error', 'info', 'border', 'link', 'header'] as $key) {
            $data["theme_public_{$key}"] = $this->configService->get("theme_public_{$key}");
            $data["theme_meeting_{$key}"] = $this->configService->get("theme_meeting_{$key}");
        }

        return response()->json($data);
    }
}
