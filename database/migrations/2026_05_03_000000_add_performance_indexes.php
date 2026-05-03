<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('decisions', function (Blueprint $table) {
            $table->index('status', 'decisions_status_idx');
            $table->index(['circle_id', 'status'], 'decisions_circle_status_idx');
            $table->index(['visibility', 'status', 'created_at'], 'decisions_public_listing_idx');
            $table->index('model_id', 'decisions_model_idx');
        });

        Schema::table('decision_versions', function (Blueprint $table) {
            $table->index(['decision_id', 'is_current'], 'decision_versions_current_idx');
            $table->index('author_id', 'decision_versions_author_idx');
        });

        Schema::table('decision_participants', function (Blueprint $table) {
            $table->index(['decision_id', 'role', 'user_id'], 'decision_participants_decision_role_user_idx');
            $table->index(['user_id', 'role', 'decision_id'], 'decision_participants_user_role_decision_idx');
        });

        Schema::table('circle_members', function (Blueprint $table) {
            $table->index(['user_id', 'role', 'circle_id'], 'circle_members_user_role_circle_idx');
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->index(['decision_version_id', 'type', 'status'], 'feedbacks_version_type_status_idx');
            $table->index(['author_id', 'type', 'status'], 'feedbacks_author_type_status_idx');
        });

        Schema::table('feedback_messages', function (Blueprint $table) {
            $table->index(['feedback_id', 'created_at'], 'feedback_messages_feedback_created_idx');
            $table->index('author_id', 'feedback_messages_author_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'read_at', 'created_at'], 'notifications_user_read_created_idx');
        });

        Schema::table('consents', function (Blueprint $table) {
            $table->index(['decision_version_id', 'signal', 'user_id'], 'consents_version_signal_user_idx');
            $table->index(['user_id', 'phase'], 'consents_user_phase_idx');
        });

        Schema::table('decision_categories', function (Blueprint $table) {
            $table->index('category_id', 'decision_categories_category_idx');
        });

        Schema::table('decision_labels', function (Blueprint $table) {
            $table->index('label_id', 'decision_labels_label_idx');
        });

        Schema::table('decision_relations', function (Blueprint $table) {
            $table->index('target_decision_id', 'decision_relations_target_idx');
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->index('decision_version_id', 'attachments_version_idx');
            $table->index('uploader_id', 'attachments_uploader_idx');
        });

        Schema::table('decision_user_settings', function (Blueprint $table) {
            $table->index('decision_id', 'decision_user_settings_decision_idx');
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->index('circle_id', 'invitations_circle_idx');
            $table->index('used_by', 'invitations_used_by_idx');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropIndex('invitations_used_by_idx');
            $table->dropIndex('invitations_circle_idx');
        });

        Schema::table('decision_user_settings', function (Blueprint $table) {
            $table->dropIndex('decision_user_settings_decision_idx');
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->dropIndex('attachments_uploader_idx');
            $table->dropIndex('attachments_version_idx');
        });

        Schema::table('decision_relations', function (Blueprint $table) {
            $table->dropIndex('decision_relations_target_idx');
        });

        Schema::table('decision_labels', function (Blueprint $table) {
            $table->dropIndex('decision_labels_label_idx');
        });

        Schema::table('decision_categories', function (Blueprint $table) {
            $table->dropIndex('decision_categories_category_idx');
        });

        Schema::table('consents', function (Blueprint $table) {
            $table->dropIndex('consents_user_phase_idx');
            $table->dropIndex('consents_version_signal_user_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_created_idx');
        });

        Schema::table('feedback_messages', function (Blueprint $table) {
            $table->dropIndex('feedback_messages_author_idx');
            $table->dropIndex('feedback_messages_feedback_created_idx');
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropIndex('feedbacks_author_type_status_idx');
            $table->dropIndex('feedbacks_version_type_status_idx');
        });

        Schema::table('circle_members', function (Blueprint $table) {
            $table->dropIndex('circle_members_user_role_circle_idx');
        });

        Schema::table('decision_participants', function (Blueprint $table) {
            $table->dropIndex('decision_participants_user_role_decision_idx');
            $table->dropIndex('decision_participants_decision_role_user_idx');
        });

        Schema::table('decision_versions', function (Blueprint $table) {
            $table->dropIndex('decision_versions_author_idx');
            $table->dropIndex('decision_versions_current_idx');
        });

        Schema::table('decisions', function (Blueprint $table) {
            $table->dropIndex('decisions_model_idx');
            $table->dropIndex('decisions_public_listing_idx');
            $table->dropIndex('decisions_circle_status_idx');
            $table->dropIndex('decisions_status_idx');
        });
    }
};
