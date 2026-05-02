<?php

namespace Database\Seeders;

use App\Services\ConfigService;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ConfigService $configService): void
    {
        $defaults = [
            'page_legal_title'      => 'Mentions Légales',
            'page_legal_slug'       => 'mentions-legales',
            'page_legal_content'    => '<h1>Mentions Légales</h1><p>Cette plateforme est éditée par votre organisation.</p><p>Hébergement : Votre hébergeur ici.</p>',
            
            'page_privacy_title'    => 'Politique de Confidentialité',
            'page_privacy_slug'     => 'politique-confidentialite',
            'page_privacy_content'  => '<h1>Politique de Confidentialité</h1><p>Nous protégeons vos données personnelles conformément au RGPD.</p>',
            
            'page_terms_title'      => 'Conditions Générales (CGU)',
            'page_terms_slug'       => 'cgu',
            'page_terms_content'    => '<h1>Conditions Générales d\'Utilisation</h1><p>En utilisant cette plateforme, vous acceptez les règles de fonctionnement de notre organisation.</p>',

            // EMAILS
            'mail_reminder_subject' => '⚠️ Rappel : Votre avis est attendu sur {title}',
            'mail_reminder_body' => '<p>Bonjour {name},</p><p>Nous vous rappelons que la décision "<strong>{title}</strong>" est actuellement en phase "<strong>{phase}</strong>".</p><p>La date limite est fixée au <strong>{deadline}</strong>.</p><p><a href="{url}">Donner mon avis</a></p>',
            
            'mail_invitation_subject' => '👋 Invitation : Rejoignez le cercle {circle}',
            'mail_invitation_body' => '<p>Bonjour,</p><p><strong>{inviter}</strong> vous invite à rejoindre le cercle "<strong>{circle}</strong>".</p><p>Description : {description}</p><p><a href="{url}">Accepter l\'invitation</a></p>',
            
            'mail_notification_subject' => '📢 DAZO : La décision {title} passe en phase {phase}',
            'mail_notification_body' => '<p>Bonjour {name},</p><p>La décision "<strong>{title}</strong>" vient d\'entrer en phase "<strong>{phase}</strong>".</p><p><a href="{url}">Voir la décision</a></p>',
            
            'mail_contact_subject' => '📬 Nouveau message : {subject}',
            'mail_contact_body' => '<p><strong>De :</strong> {name} ({email})</p><p><strong>Sujet :</strong> {subject}</p><p><strong>Message :</strong></p><p>{message}</p>',

            // OAUTH
            'auth_google_enabled' => 'false',
            'auth_google_client_id' => '',
            'auth_google_client_secret' => '',
            'auth_github_enabled' => 'false',
            'auth_github_client_id' => '',
            'auth_github_client_secret' => '',
            'auth_facebook_enabled' => 'false',
            'auth_facebook_client_id' => '',
            'auth_facebook_client_secret' => '',
            'auth_twitter_enabled' => 'false',
            'auth_twitter_client_id' => '',
            'auth_twitter_client_secret' => '',
            'auth_linkedin-openid_enabled' => 'false',
            'auth_linkedin-openid_client_id' => '',
            'auth_linkedin-openid_client_secret' => '',
            'auth_gitlab_enabled' => 'false',
            'auth_gitlab_client_id' => '',
            'auth_gitlab_client_secret' => '',
            'auth_microsoft_enabled' => 'false',
            'auth_microsoft_client_id' => '',
            'auth_microsoft_client_secret' => '',
            'auth_apple_enabled' => 'false',
            'auth_apple_client_id' => '',
            'auth_apple_client_secret' => '',
            'auth_franceconnect_enabled' => 'false',
            'auth_franceconnect_client_id' => '',
            'auth_franceconnect_client_secret' => '',

            // TEMPLATE GÉNÉRAL EMAILS
            'mail_template_logo'            => '/images/logo-email.png',
            'mail_template_logo_perso'      => '',
            'mail_template_site_link'       => 'https://dazo.app',
            'mail_template_site_link_register' => 'https://dazo.app/register',
            'mail_template_wrapper'         => '<div style="font-family: \'Inter\', sans-serif; background-color: #f8fafc; padding: 40px 20px; color: #1e293b;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div style="padding: 32px; text-align: center; border-bottom: 1px solid #f1f5f9;">
      <a href="{site_link}">
        <img src="{logo}" alt="Logo" style="max-height: 48px; width: auto;">
      </a>
    </div>
    <div style="padding: 40px; line-height: 1.6; font-size: 16px;">
      {message}
    </div>
    <div style="padding: 32px; background: #f8fafc; text-align: center; font-size: 14px; color: #64748b; border-top: 1px solid #f1f5f9;">
      <p style="margin: 0 0 16px 0;">Vous recevez cet email car vous participez à la gouvernance sur notre plateforme.</p>
      <div style="display: flex; justify-content: center; gap: 16px;">
        <a href="{site_link}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Notre Site</a>
        <span style="color: #cbd5e1;">&bull;</span>
        <a href="{site_link_register}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">S\'inscrire</a>
      </div>
    </div>
  </div>
</div>',
        ];


        foreach ($defaults as $key => $value) {
            // On ne remplace pas si déjà présent
            if (!$configService->get($key)) {
                $configService->set($key, $value);
            }
        }
    }
}
