--
-- PostgreSQL database dump
--

\restrict hHAujNlCaxTDZjFojk4BEddfgHWWoEClegrG0Ouub5Zhvdip9RKFjee4ZySwmpZ

-- Dumped from database version 15.17 (Debian 15.17-1.pgdg13+1)
-- Dumped by pg_dump version 15.17 (Debian 15.17-1.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: app_logs; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.app_logs (
    id uuid NOT NULL,
    user_id uuid,
    action character varying(255) NOT NULL,
    entity_type character varying(255),
    entity_id uuid,
    payload json,
    ip_address character varying(45),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.app_logs OWNER TO dazo_user;

--
-- Name: attachments; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.attachments (
    id uuid NOT NULL,
    decision_version_id uuid,
    uploader_id uuid NOT NULL,
    filename character varying(255) NOT NULL,
    s3_path character varying(255) NOT NULL,
    mime_type character varying(255) NOT NULL,
    size_bytes integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.attachments OWNER TO dazo_user;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO dazo_user;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO dazo_user;

--
-- Name: categories; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.categories (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    color_hex character varying(7),
    icon character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.categories OWNER TO dazo_user;

--
-- Name: circle_members; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.circle_members (
    id uuid NOT NULL,
    circle_id uuid NOT NULL,
    user_id uuid NOT NULL,
    role character varying(255) DEFAULT 'member'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.circle_members OWNER TO dazo_user;

--
-- Name: circles; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.circles (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    type character varying(255) DEFAULT 'open'::character varying NOT NULL,
    parent_id uuid,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.circles OWNER TO dazo_user;

--
-- Name: consents; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.consents (
    id uuid NOT NULL,
    decision_version_id uuid NOT NULL,
    user_id uuid NOT NULL,
    signal character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    phase character varying(255)
);


ALTER TABLE public.consents OWNER TO dazo_user;

--
-- Name: decision_animator_logs; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.decision_animator_logs (
    id uuid NOT NULL,
    decision_id uuid NOT NULL,
    animator_id uuid,
    assigned_by uuid,
    assigned_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    removed_at timestamp(0) without time zone
);


ALTER TABLE public.decision_animator_logs OWNER TO dazo_user;

--
-- Name: decision_categories; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.decision_categories (
    decision_id uuid NOT NULL,
    category_id uuid NOT NULL
);


ALTER TABLE public.decision_categories OWNER TO dazo_user;

--
-- Name: decision_labels; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.decision_labels (
    decision_id uuid NOT NULL,
    label_id uuid NOT NULL
);


ALTER TABLE public.decision_labels OWNER TO dazo_user;

--
-- Name: decision_models; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.decision_models (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    template_content text NOT NULL,
    requires_distinct_animator boolean DEFAULT false NOT NULL,
    default_objection_days integer DEFAULT 7 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.decision_models OWNER TO dazo_user;

--
-- Name: decision_participants; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.decision_participants (
    id uuid NOT NULL,
    decision_id uuid NOT NULL,
    user_id uuid NOT NULL,
    role character varying(255) DEFAULT 'participant'::character varying NOT NULL,
    added_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.decision_participants OWNER TO dazo_user;

--
-- Name: decision_relations; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.decision_relations (
    id uuid NOT NULL,
    source_decision_id uuid NOT NULL,
    target_decision_id uuid NOT NULL,
    relation_type character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.decision_relations OWNER TO dazo_user;

--
-- Name: decision_user_settings; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.decision_user_settings (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    decision_id uuid NOT NULL,
    is_favorite boolean DEFAULT false NOT NULL,
    notification_level character varying(255) DEFAULT 'all'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.decision_user_settings OWNER TO dazo_user;

--
-- Name: decision_versions; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.decision_versions (
    id uuid NOT NULL,
    decision_id uuid NOT NULL,
    author_id uuid NOT NULL,
    previous_version_id uuid,
    version_number integer NOT NULL,
    is_current boolean DEFAULT false NOT NULL,
    content text NOT NULL,
    change_reason text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.decision_versions OWNER TO dazo_user;

--
-- Name: decisions; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.decisions (
    id uuid NOT NULL,
    circle_id uuid NOT NULL,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    title character varying(255) NOT NULL,
    visibility character varying(255) DEFAULT 'public'::character varying NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    emergency_mode boolean DEFAULT false NOT NULL,
    objection_round_deadline timestamp(0) without time zone,
    model_id uuid,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    revision_content text,
    revision_attachment_ids json,
    current_deadline timestamp(0) without time zone,
    reminder_sent boolean DEFAULT false NOT NULL,
    status_before_suspension character varying(255),
    share_count integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.decisions OWNER TO dazo_user;

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO dazo_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: dazo_user
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO dazo_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dazo_user
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: feedback_joins; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.feedback_joins (
    id uuid NOT NULL,
    feedback_id uuid NOT NULL,
    user_id uuid NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.feedback_joins OWNER TO dazo_user;

--
-- Name: feedback_messages; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.feedback_messages (
    id uuid NOT NULL,
    feedback_id uuid NOT NULL,
    author_id uuid NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.feedback_messages OWNER TO dazo_user;

--
-- Name: feedbacks; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.feedbacks (
    id uuid NOT NULL,
    decision_version_id uuid NOT NULL,
    author_id uuid NOT NULL,
    type character varying(255) NOT NULL,
    status character varying(255) DEFAULT 'submitted'::character varying NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.feedbacks OWNER TO dazo_user;

--
-- Name: help_texts; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.help_texts (
    id uuid NOT NULL,
    key character varying(255) NOT NULL,
    level character varying(255) DEFAULT 'instance'::character varying NOT NULL,
    model_id uuid,
    content text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.help_texts OWNER TO dazo_user;

--
-- Name: instance_config; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.instance_config (
    id uuid NOT NULL,
    key character varying(255) NOT NULL,
    type character varying(255) DEFAULT 'string'::character varying NOT NULL,
    value text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.instance_config OWNER TO dazo_user;

--
-- Name: invitations; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.invitations (
    id uuid NOT NULL,
    circle_id uuid NOT NULL,
    inviter_id uuid NOT NULL,
    email character varying(255) NOT NULL,
    role character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    expires_at timestamp(0) without time zone NOT NULL,
    used_by uuid,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.invitations OWNER TO dazo_user;

--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO dazo_user;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO dazo_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: dazo_user
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO dazo_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dazo_user
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: labels; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.labels (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    color_hex character varying(7),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.labels OWNER TO dazo_user;

--
-- Name: migrations; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO dazo_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: dazo_user
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO dazo_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dazo_user
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notification_preferences; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.notification_preferences (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    category character varying(255) NOT NULL,
    email_enabled boolean DEFAULT true NOT NULL,
    web_enabled boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.notification_preferences OWNER TO dazo_user;

--
-- Name: notifications; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.notifications (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    category character varying(255) NOT NULL,
    event_type character varying(255) NOT NULL,
    payload json NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.notifications OWNER TO dazo_user;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO dazo_user;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id uuid NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO dazo_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: dazo_user
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.personal_access_tokens_id_seq OWNER TO dazo_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dazo_user
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id uuid,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO dazo_user;

--
-- Name: social_accounts; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.social_accounts (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    provider character varying(255) NOT NULL,
    provider_id character varying(255) NOT NULL,
    provider_token text,
    provider_refresh_token text,
    provider_data json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.social_accounts OWNER TO dazo_user;

--
-- Name: thread_messages; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.thread_messages (
    id uuid NOT NULL,
    decision_id uuid NOT NULL,
    author_id uuid NOT NULL,
    tour character varying(255) NOT NULL,
    content text NOT NULL,
    is_moderator_note boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.thread_messages OWNER TO dazo_user;

--
-- Name: users; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.users (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255),
    avatar_url character varying(255),
    role character varying(255) DEFAULT 'user'::character varying NOT NULL,
    is_global_animator boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    custom_views json,
    dashboard_widgets json
);


ALTER TABLE public.users OWNER TO dazo_user;

--
-- Name: wiki_categories; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.wiki_categories (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    "order" integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.wiki_categories OWNER TO dazo_user;

--
-- Name: wiki_pages; Type: TABLE; Schema: public; Owner: dazo_user
--

CREATE TABLE public.wiki_pages (
    id uuid NOT NULL,
    slug character varying(255) NOT NULL,
    title character varying(255) NOT NULL,
    content text NOT NULL,
    is_published boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    wiki_category_id uuid,
    "order" integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.wiki_pages OWNER TO dazo_user;

--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Data for Name: app_logs; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.app_logs (id, user_id, action, entity_type, entity_id, payload, ip_address, created_at, updated_at) FROM stdin;
019de42b-6f2c-72c4-ba94-d17715da1d28	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-01 15:32:25	2026-05-01 15:32:25
019de435-79ab-71b0-8dfb-72d4af2bedb9	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/config/logo	\N	\N	{"logo":{}}	172.19.0.1	2026-05-01 15:43:23	2026-05-01 15:43:23
019de437-3e98-70d6-a66e-a8cdd2cab65f	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/admin/config	\N	\N	{"config":{"app_name":"GVL - D\\u00e9cisions a Z\\u00e9ro Objection","app_logo":"branding\\/6IHIhGeyaEoOHRrMynPobRYv5ne7w0TOhIXMbTMh.jpg","decision_reaction_days":"3","decision_objection_days":"3","reminder_hours_before":"24","public_registration":"true","mail_sender_name":"DAZO Notifications","mail_contact_address":"contact@dazo.app","maintenance_mode":"false","enable_public_front":"true","require_admin_approval":"false","recaptcha_site_key":null,"recaptcha_secret_key":null,"public_circles":[],"public_categories":[],"public_statuses":[],"public_filters":[],"public_api_key":null,"legal_mentions_url":null,"privacy_policy_url":null,"terms_of_service_url":null,"allowed_file_types":null,"max_file_size_mb":"10","mail_host":null,"mail_port":"587","mail_username":null,"mail_password":null,"mail_encryption":"tls","reminder_email_subject":"\\u26a0\\ufe0f Rappel : La d\\u00e9cision '{title}' arrive \\u00e0 \\u00e9ch\\u00e9ance","reminder_email_body":"Bonjour {name},\\n\\nCeci est un rappel concernant la d\\u00e9cision : **{title}**.\\n\\nLa phase actuelle (**{phase}**) arrive bient\\u00f4t \\u00e0 \\u00e9ch\\u00e9ance. Votre participation est attendue afin de faire progresser le processus.\\n\\n**\\u00c9ch\\u00e9ance :** {deadline}\\n\\nMerci de votre contribution."}}	172.19.0.1	2026-05-01 15:45:19	2026-05-01 15:45:19
019de437-a2e2-73d5-baa6-79f8b076696a	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-01 15:45:44	2026-05-01 15:45:44
019de437-e95a-7103-b1b7-62a374c1db5f	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-01 15:46:02	2026-05-01 15:46:02
019de448-3f03-732b-a8fe-25b8c3957ab3	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-01 16:03:53	2026-05-01 16:03:53
019de448-b47b-7144-b722-0f86b4878741	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-01 16:04:23	2026-05-01 16:04:23
019de451-fd12-7113-8a8b-3ad36712a25b	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-01 16:14:31	2026-05-01 16:14:31
019de452-1c97-72ba-aaa6-e0a0f7cabf1b	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-01 16:14:39	2026-05-01 16:14:39
019de454-a65e-70a4-8669-cdcfcb4a20c3	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-01 16:17:26	2026-05-01 16:17:26
019de455-9db4-724d-be83-0f384c6782d7	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-01 16:18:29	2026-05-01 16:18:29
019de458-8f4a-7342-b26d-446b7b8e17c6	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-01 16:21:42	2026-05-01 16:21:42
019de45a-3cb5-70a0-86b0-c601ae64e679	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-01 16:23:32	2026-05-01 16:23:32
019de45b-0086-7280-a58e-5ff0011cabdf	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/config/api-key	\N	\N	[]	172.19.0.1	2026-05-01 16:24:22	2026-05-01 16:24:22
019de45b-6a3e-719c-89bc-7af1ab389916	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/admin/config	\N	\N	{"config":{"public_api_key":"dz_1a413c2b16c64af989f7c47b7e708fcc762257405ff2ec07feba30ffa32b9626","public_circles":["019de42b-1b11-705c-a4a1-ffac5b03ab22","019de42b-1b17-72f8-9b71-b8ae0cc4730f","019de42b-1b19-7381-b0eb-0237e1a655b9","019de42b-1b1c-7112-b34e-ea76d02e771e"],"public_categories":["019de42b-1af3-7225-98c5-2808199094c1","019de42b-1af0-7320-b7d2-ae478f5bb73b","019de42b-1aee-70be-8798-b90c40d961cc","019de42b-1aec-73e2-81ce-ce77521b3f2a","019de42b-1ae9-71ef-ad60-8dc3b83c3082","019de42b-1ae7-7239-84d8-eebd671fb8aa"],"public_statuses":["clarification","reaction","objection","adopted","abandoned","rejected","suspended"],"public_filters":[]}}	172.19.0.1	2026-05-01 16:24:49	2026-05-01 16:24:49
019de471-4690-7242-b61a-bc16e2543ccd	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-01 16:48:42	2026-05-01 16:48:42
019de471-5339-724a-9bee-ec258226d0e6	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-01 16:48:45	2026-05-01 16:48:45
019de4ac-f8f0-70d5-a193-a6e2376fc672	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/config/test-email	\N	\N	{"to_email":"david.haurillon@gmail.com","subject":"\\u26a0\\ufe0f Rappel : La d\\u00e9cision 'D\\u00c9CISION DE TEST' arrive \\u00e0 \\u00e9ch\\u00e9ance","body":"Bonjour ,,\\n\\nCeci est un rappel concernant la d\\u00e9cision : **D\\u00c9CISION DE TEST**.\\n\\nLa phase actuelle (**Objection**) arrive bient\\u00f4t \\u00e0 \\u00e9ch\\u00e9ance. Votre participation est attendue afin de faire progresser le processus.\\n\\n**\\u00c9ch\\u00e9ance :** 31\\/12\\/2026 \\u00e0 18:00\\n\\nMerci de votre contribution."}	172.19.0.1	2026-05-01 17:53:54	2026-05-01 17:53:54
019de4ad-8ee7-7396-ab95-d7d3473cc35f	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/config/test-email	\N	\N	{"to_email":"david.haurillon@gmail.com","subject":"\\ud83d\\udce2 Nouvelle \\u00e9tape pour : D\\u00c9CISION DE TEST","body":"Bonjour ,,\\n\\nLa d\\u00e9cision **D\\u00c9CISION DE TEST** vient de passer en phase de **Objection**.\\n\\nVous pouvez consulter les d\\u00e9tails et participer ici : [Voir la d\\u00e9cision](http:\\/\\/127.0.0.1:3003\\/decisions\\/test)"}	172.19.0.1	2026-05-01 17:54:32	2026-05-01 17:54:32
019de4d2-095c-70e8-ad11-9f15ba304fea	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1ba3-7152-9b76-d7baadc5cebe/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-01 18:34:23	2026-05-01 18:34:23
019de4d2-41a3-7396-99b4-eded82be0246	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1ba3-7152-9b76-d7baadc5cebe/transition	\N	\N	{"to":"suspended"}	172.19.0.1	2026-05-01 18:34:37	2026-05-01 18:34:37
019de7b7-cf68-7267-831b-5c1b5f5c434f	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1bee-7325-a8ed-067285f10b54/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-02 08:04:36	2026-05-02 08:04:36
019de7ca-d548-7020-b8d2-efd40866577d	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/decisions/019de42b-1c25-712a-bc03-854d08a9ad94	\N	\N	{"title":"Politique de remboursement des frais","content":"# Proposition (v3 \\u2013 brouillon)\\n\\nPlafond de 120\\u20ac\\/mois. Justificatif au-del\\u00e0 de 80\\u20ac.","animator_id":"019de42b-1ab2-7286-824f-90df7187f28e","circle_id":"019de42b-1b19-7381-b0eb-0237e1a655b9","category_ids":["019de42b-1aec-73e2-81ce-ce77521b3f2a"],"model_id":"019de42b-1b07-7240-8b59-6e43e5016a29","excluded_members":[],"revision_attachment_ids":[],"notify":false}	172.19.0.1	2026-05-02 08:25:23	2026-05-02 08:25:23
019de7ca-d57b-7050-a5aa-747a16289291	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1c25-712a-bc03-854d08a9ad94/versions	\N	\N	{"content":"# Proposition (v3 \\u2013 brouillon)\\n\\nPlafond de 120\\u20ac\\/mois. Justificatif au-del\\u00e0 de 80\\u20ac.","attachment_ids":[],"notify":false,"status":"objection"}	172.19.0.1	2026-05-02 08:25:23	2026-05-02 08:25:23
019de7ca-fab7-7288-970b-6afc268097e7	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1c25-712a-bc03-854d08a9ad94/versions/019de7ca-d573-7027-a704-ec0aa178752e/consent	\N	\N	{"type":"no_objection","acting_as_user_id":"019de42b-14cf-7203-ba64-cc6c7ba1cf94","notify":false}	172.19.0.1	2026-05-02 08:25:32	2026-05-02 08:25:32
019de7cb-5543-7093-9c1c-48fce7b0739a	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1c25-712a-bc03-854d08a9ad94/versions	\N	\N	{"content":"<p># Proposition (v3 \\u2013 brouillon) Plafond de 120\\u20ac\\/mois. Justificatif au-del\\u00e0 de 80\\u20ac.<\\/p><p>version 3<\\/p>","attachment_ids":[],"status":"objection","notify":true}	172.19.0.1	2026-05-02 08:25:55	2026-05-02 08:25:55
019de7cb-ce27-7177-940c-5a17c0e68277	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1c25-712a-bc03-854d08a9ad94/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-02 08:26:26	2026-05-02 08:26:26
019de812-9eda-719c-a173-487126fda6de	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-02 09:43:47	2026-05-02 09:43:47
019de819-1b59-7004-b5c2-372f58b0a62a	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	\N	[]	172.19.0.1	2026-05-02 09:50:52	2026-05-02 09:50:52
019de81b-316a-7312-968e-953a8c7783a9	019de42b-15b1-72fa-9387-b143e6dbf3ea	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-02 09:53:09	2026-05-02 09:53:09
019de85f-3a2f-730a-83b2-2f6eacf0fc0d	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-02 11:07:28	2026-05-02 11:07:28
019de85f-c7c8-7296-8bc3-2fe3a9182dad	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/attachments	\N	\N	{"replace_id":"019de42b-1d8c-7058-81c2-fe17c538373f","file":{}}	172.19.0.1	2026-05-02 11:08:04	2026-05-02 11:08:04
019de860-d2a3-72fb-8fa1-5b8beae1c88b	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/admin/config	\N	\N	{"config":{"app_name":"GVL - D\\u00e9cisions a Z\\u00e9ro Objection","app_logo":"branding\\/6IHIhGeyaEoOHRrMynPobRYv5ne7w0TOhIXMbTMh.jpg","decision_reaction_days":"3","decision_objection_days":"3","reminder_hours_before":"24","public_registration":"true","mail_sender_name":"DAZO Notifications","mail_contact_address":"contact@dazo.app","maintenance_mode":"false","enable_public_front":"true","require_admin_approval":"false","recaptcha_site_key":null,"recaptcha_secret_key":null,"public_circles":["019de42b-1b11-705c-a4a1-ffac5b03ab22","019de42b-1b17-72f8-9b71-b8ae0cc4730f","019de42b-1b19-7381-b0eb-0237e1a655b9","019de42b-1b1c-7112-b34e-ea76d02e771e"],"public_categories":["019de42b-1af3-7225-98c5-2808199094c1","019de42b-1af0-7320-b7d2-ae478f5bb73b","019de42b-1aee-70be-8798-b90c40d961cc","019de42b-1aec-73e2-81ce-ce77521b3f2a","019de42b-1ae9-71ef-ad60-8dc3b83c3082","019de42b-1ae7-7239-84d8-eebd671fb8aa"],"public_statuses":["clarification","reaction","objection","adopted","abandoned","rejected","suspended"],"public_filters":[],"public_api_key":"dz_1a413c2b16c64af989f7c47b7e708fcc762257405ff2ec07feba30ffa32b9626","legal_mentions_url":null,"privacy_policy_url":null,"terms_of_service_url":null,"page_legal_title":"Mentions L\\u00e9gales","page_legal_slug":"mentions-legales","page_legal_content":"<h1>Mentions L\\u00e9gales<\\/h1><p>Contenu par d\\u00e9faut \\u00e0 personnaliser...<\\/p>","page_privacy_title":"Politique de Confidentialit\\u00e9","page_privacy_slug":"politique-confidentialite","page_privacy_content":"<h1>Politique de Confidentialit\\u00e9<\\/h1><p>Contenu par d\\u00e9faut \\u00e0 personnaliser...<\\/p>","page_terms_title":"Conditions G\\u00e9n\\u00e9rales d'Utilisation","page_terms_slug":"cgu","page_terms_content":"<h1>Conditions G\\u00e9n\\u00e9rales d'Utilisation<\\/h1><p>Contenu par d\\u00e9faut \\u00e0 personnaliser...<\\/p>","allowed_file_types":null,"max_file_size_mb":"10","mail_host":null,"mail_port":"587","mail_username":null,"mail_password":null,"mail_encryption":"tls","mail_reminder_subject":"\\u26a0\\ufe0f Rappel : La d\\u00e9cision '{title}' arrive \\u00e0 \\u00e9ch\\u00e9ance","mail_reminder_body":"Bonjour {name},\\n\\nCeci est un rappel concernant la d\\u00e9cision : **{title}**.\\n\\nLa phase actuelle (**{phase}**) arrive bient\\u00f4t \\u00e0 \\u00e9ch\\u00e9ance. Votre participation est attendue afin de faire progresser le processus.\\n\\n**\\u00c9ch\\u00e9ance :** {deadline}\\n\\nMerci de votre contribution.","mail_invitation_subject":"\\ud83d\\udce9 Invitation \\u00e0 rejoindre le cercle '{circle}'","mail_invitation_body":"Bonjour,\\n\\nVous avez \\u00e9t\\u00e9 invit\\u00e9 \\u00e0 rejoindre le cercle **{circle}** sur la plateforme DAZO par **{inviter}**.\\n\\nCe cercle traite des sujets suivants : {description}\\n\\n[Accepter l'invitation]({url})","mail_notification_subject":"\\ud83d\\udce2 Nouvelle \\u00e9tape pour : {title}","mail_notification_body":"Bonjour {name},\\n\\nLa d\\u00e9cision **{title}** vient de passer en phase de **{phase}**.\\n\\nVous pouvez consulter les d\\u00e9tails et participer ici : [Voir la d\\u00e9cision]({url})","mail_contact_subject":"\\u2709\\ufe0f Nouveau message de contact : {subject}","mail_contact_body":"Nom : {name}\\nEmail : {email}\\n\\nMessage :\\n{message}","auth_google_enabled":"false","auth_google_client_id":null,"auth_google_client_secret":null,"auth_github_enabled":"false","auth_github_client_id":null,"auth_github_client_secret":null,"auth_microsoft_enabled":"false","auth_microsoft_client_id":null,"auth_microsoft_client_secret":null,"auth_facebook_enabled":"false","auth_facebook_client_id":null,"auth_facebook_client_secret":null,"auth_apple_enabled":"false","auth_apple_client_id":null,"auth_apple_client_secret":null,"auth_franceconnect_enabled":"false","auth_franceconnect_client_id":null,"auth_franceconnect_client_secret":null,"mail_template_logo":"\\/images\\/logo-email.png","mail_template_logo_perso":null,"mail_template_site_link":"https:\\/\\/dazo.app","mail_template_site_link_register":"https:\\/\\/dazo.app\\/register","mail_template_wrapper":"<div style=\\"font-family: 'Inter', sans-serif; background-color: #f8fafc; padding: 40px 20px; color: #1e293b;\\">\\n  <div style=\\"max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);\\">\\n    <div style=\\"padding: 32px; text-align: center; border-bottom: 1px solid #f1f5f9;\\">\\n      <a href=\\"{site_link}\\">\\n        <img src=\\"{logo}\\" alt=\\"Logo\\" style=\\"max-height: 48px; width: auto;\\">\\n      <\\/a>\\n    <\\/div>\\n    <div style=\\"padding: 40px; line-height: 1.6; font-size: 16px;\\">\\n      {message}\\n    <\\/div>\\n    <div style=\\"padding: 32px; background: #f8fafc; text-align: center; font-size: 14px; color: #64748b; border-top: 1px solid #f1f5f9;\\">\\n      <p style=\\"margin: 0 0 16px 0;\\">Vous recevez cet email car vous participez \\u00e0 la gouvernance sur notre plateforme.<\\/p>\\n      <div style=\\"display: flex; justify-content: center; gap: 16px;\\">\\n        <a href=\\"{site_link}\\" style=\\"color: #3b82f6; text-decoration: none; font-weight: 600;\\">Notre Site<\\/a>\\n        <span style=\\"color: #cbd5e1;\\">&bull;<\\/span>\\n        <a href=\\"{site_link_register}\\" style=\\"color: #3b82f6; text-decoration: none; font-weight: 600;\\">S'inscrire<\\/a>\\n      <\\/div>\\n    <\\/div>\\n  <\\/div>\\n<\\/div>","reminder_email_subject":"\\u26a0\\ufe0f Rappel : La d\\u00e9cision '{title}' arrive \\u00e0 \\u00e9ch\\u00e9ance","reminder_email_body":"Bonjour {name},\\n\\nCeci est un rappel concernant la d\\u00e9cision : **{title}**.\\n\\nLa phase actuelle (**{phase}**) arrive bient\\u00f4t \\u00e0 \\u00e9ch\\u00e9ance. Votre participation est attendue afin de faire progresser le processus.\\n\\n**\\u00c9ch\\u00e9ance :** {deadline}\\n\\nMerci de votre contribution.","auth_twitter_enabled":"false","auth_twitter_client_id":null,"auth_twitter_client_secret":null,"auth_linkedin-openid_enabled":"false","auth_linkedin-openid_client_id":null,"auth_linkedin-openid_client_secret":null,"auth_gitlab_enabled":"false","auth_gitlab_client_id":null,"auth_gitlab_client_secret":null,"page_legal_enabled":"true"}}	172.19.0.1	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-e9f5-708b-8cf3-ab272da5c7c0	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-02 11:09:18	2026-05-02 11:09:18
019de861-1243-70e2-9748-3939d726e485	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-02 11:09:29	2026-05-02 11:09:29
019de879-5d00-7200-af1b-7ffe3e189fe6	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-02 11:36:01	2026-05-02 11:36:01
019de879-ac1c-7342-bd83-5aad49414a24	\N	POST api/v1/front/decisions/019de42b-1c60-7174-b9f7-8c1cf171c88f/share	\N	\N	[]	172.19.0.1	2026-05-02 11:36:21	2026-05-02 11:36:21
019de87f-9a82-72bb-af21-cebe7ff0a235	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-02 11:42:49	2026-05-02 11:42:49
019de8a2-bbf2-735d-9bfc-ddd05ac5190a	\N	POST api/v1/front/decisions/019de42b-1c60-7174-b9f7-8c1cf171c88f/share	\N	\N	[]	172.19.0.1	2026-05-02 12:21:12	2026-05-02 12:21:12
019de8a3-0245-7344-8469-a6f78050f02b	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-02 12:21:30	2026-05-02 12:21:30
019de8a4-e30f-73ab-9070-60111643426e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-02 12:23:33	2026-05-02 12:23:33
019de8a5-4a35-73b6-9516-9866f3bca509	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-02 12:23:59	2026-05-02 12:23:59
019ded28-afdb-7315-9a65-4bb326ddd4fd	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-03 09:25:59	2026-05-03 09:25:59
019ded29-0a54-7210-9d55-da06af026788	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-03 09:26:23	2026-05-03 09:26:23
019ded29-4d7f-7379-91b6-b540cad880c7	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-03 09:26:40	2026-05-03 09:26:40
019ded31-5833-714e-91e9-dbebd103a5f6	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-03 09:35:27	2026-05-03 09:35:27
019ded31-836c-73df-9dd4-0c0824133025	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-03 09:35:38	2026-05-03 09:35:38
019ded33-32e7-726e-a1cb-5d6dad70ba53	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-03 09:37:28	2026-05-03 09:37:28
019ded35-57db-7253-85be-629f5be9e348	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-03 09:39:49	2026-05-03 09:39:49
019ded36-01bb-7180-89e2-9a3bb096b96a	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-03 09:40:32	2026-05-03 09:40:32
019ded36-1500-72ad-af6f-859544762811	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-03 09:40:37	2026-05-03 09:40:37
019ded44-dcca-72b7-8e44-6119878150e2	\N	POST api/v1/auth/login	\N	\N	{"email":"hugo@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-03 09:56:46	2026-05-03 09:56:46
019deda8-3f5a-737f-9364-1f5fefc9ae7f	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-03 11:45:19	2026-05-03 11:45:19
019dedab-314f-72cd-8efe-d01c1816076b	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/attachments	\N	\N	{"replace_id":"019de42b-1ce4-71cb-ab6f-2cf371e41e51","file":{}}	172.19.0.1	2026-05-03 11:48:32	2026-05-03 11:48:32
019dedab-f70c-7312-b52b-1e15a736759d	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/logout	\N	\N	[]	172.19.0.1	2026-05-03 11:49:23	2026-05-03 11:49:23
019dedac-062f-7053-8957-ac610e51d789	\N	POST api/v1/auth/login	\N	\N	{"email":"admin@dazo.test","recaptcha_token":null}	172.19.0.1	2026-05-03 11:49:27	2026-05-03 11:49:27
019dedad-e02a-7376-85b3-de389967ad7b	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/attachments	\N	\N	{"replace_id":"019de42b-1ce7-717e-9377-093d6e6a7a64","file":{}}	172.19.0.1	2026-05-03 11:51:28	2026-05-03 11:51:28
019dedc0-bc7b-71fc-afcd-0811225176ec	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-1688-7290-b257-cd7e06dcb2d4	\N	\N	[]	172.19.0.1	2026-05-03 12:12:04	2026-05-03 12:12:04
019dedc0-e317-7138-9a20-9356e03839b5	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	\N	[]	172.19.0.1	2026-05-03 12:12:14	2026-05-03 12:12:14
019dedc4-69b9-7284-964f-93bbb3568315	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-1761-70c7-a2de-4906fe0a7166	\N	\N	[]	172.19.0.1	2026-05-03 12:16:05	2026-05-03 12:16:05
019dedc4-e2d4-73c2-bf20-f002c6dc0305	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	\N	[]	172.19.0.1	2026-05-03 12:16:36	2026-05-03 12:16:36
019dedcf-7119-71a9-bca5-f0ce3d4342e0	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	\N	[]	172.19.0.1	2026-05-03 12:28:08	2026-05-03 12:28:08
019dedcf-ccdf-72b4-9a93-7019340145ae	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/admin/users/019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	\N	{"name":"Bob Martin","email":"user@dazo.test","role":"admin","is_active":true}	172.19.0.1	2026-05-03 12:28:31	2026-05-03 12:28:31
019dede7-60cd-723c-928b-24caf87d551f	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-1761-70c7-a2de-4906fe0a7166	\N	\N	[]	172.19.0.1	2026-05-03 12:54:17	2026-05-03 12:54:17
019dede7-828a-705a-8125-90095ee0cfce	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	\N	[]	172.19.0.1	2026-05-03 12:54:25	2026-05-03 12:54:25
019dee4a-7b25-719e-ba79-dd5dfda891c3	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	\N	[]	172.19.0.1	2026-05-03 14:42:31	2026-05-03 14:42:31
019dee4a-9c9b-73ea-a693-ea8f28809aa3	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	\N	[]	172.19.0.1	2026-05-03 14:42:40	2026-05-03 14:42:40
019dee4a-ae5f-727f-8109-21d91bef942b	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	\N	[]	172.19.0.1	2026-05-03 14:42:44	2026-05-03 14:42:44
019dee4a-c517-72b3-a770-60e1627eb3cb	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-1761-70c7-a2de-4906fe0a7166	\N	\N	[]	172.19.0.1	2026-05-03 14:42:50	2026-05-03 14:42:50
019dee4b-4d4d-7339-aed9-f0149dd4563b	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	\N	[]	172.19.0.1	2026-05-03 14:43:25	2026-05-03 14:43:25
019dee4b-55e2-7354-8a6c-7d9788527217	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-1688-7290-b257-cd7e06dcb2d4	\N	\N	[]	172.19.0.1	2026-05-03 14:43:27	2026-05-03 14:43:27
019dee4c-a483-716c-a6a4-f40686e9dcbc	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	\N	[]	172.19.0.1	2026-05-03 14:44:53	2026-05-03 14:44:53
019dee4f-6057-7023-86ca-1fc17192f8e6	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-1761-70c7-a2de-4906fe0a7166	\N	\N	[]	172.19.0.1	2026-05-03 14:47:52	2026-05-03 14:47:52
019dee53-17f0-73e0-addf-43a3434b2b6c	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-190e-70c3-9d21-2b6d2b36612c	\N	\N	[]	172.19.0.1	2026-05-03 14:51:56	2026-05-03 14:51:56
019dee79-9c5e-70bb-b5c4-674a43e6060c	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/extend	\N	\N	[]	172.19.0.1	2026-05-03 15:34:00	2026-05-03 15:34:00
019dee79-f357-72ed-93e1-e99925180267	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/remind	\N	\N	{"type":"extend"}	172.19.0.1	2026-05-03 15:34:22	2026-05-03 15:34:22
019dee7c-5b9a-7323-9ed4-9cbeff6e5a79	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-03 15:37:00	2026-05-03 15:37:00
019dee7d-66bb-736f-9b8b-ea2ddc285d8c	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/versions	\N	\N	{"content":null,"attachment_ids":[],"status":"clarification","notify":true}	172.19.0.1	2026-05-03 15:38:08	2026-05-03 15:38:08
019dee7d-b74f-7178-9a6d-7211163ac05d	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/versions	\N	\N	{"content":"<p>sdqfqsdfqsdfqsf<\\/p>","attachment_ids":[],"status":"clarification","notify":true}	172.19.0.1	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7f-2040-70c5-8e5f-83223ab726c0	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-03 15:40:01	2026-05-03 15:40:01
019dee7f-51c6-7216-bd57-affa920f46a4	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/versions	\N	\N	{"content":"<p>qsfqsdf qfd q<\\/p>","attachment_ids":[],"status":"clarification","notify":true}	172.19.0.1	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee9c-394b-70a1-b099-00a79440e6b0	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-03 16:11:48	2026-05-03 16:11:48
019dee9c-b042-718f-afda-df94a67b82f7	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/transition	\N	\N	{"to":"suspended"}	172.19.0.1	2026-05-03 16:12:19	2026-05-03 16:12:19
019dee9c-e4cf-72fa-a067-604d860666d0	019de42b-190e-70c3-9d21-2b6d2b36612c	POST api/v1/decisions/019de42b-1bd4-71c6-930f-b0a84cfaf0b4/transition	\N	\N	{"to":"clarification"}	172.19.0.1	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9f-2bbb-73eb-bb5f-3cf5c2628b2b	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	\N	[]	172.19.0.1	2026-05-03 16:15:02	2026-05-03 16:15:02
019deea7-db9b-726a-ba41-26e2c7a0c81f	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1d70-72ca-892d-ef83b7d3658e/transition	\N	\N	{"to":"suspended"}	172.19.0.1	2026-05-03 16:24:31	2026-05-03 16:24:31
019deea8-0aba-73d6-8f0e-51eb58c11331	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1d70-72ca-892d-ef83b7d3658e/transition	\N	\N	{"to":"clarification"}	172.19.0.1	2026-05-03 16:24:43	2026-05-03 16:24:43
019deea8-473b-72d6-b853-7605ee624dc6	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1d70-72ca-892d-ef83b7d3658e/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-03 16:24:58	2026-05-03 16:24:58
019deeae-7db8-7204-a0db-830dfce40044	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1ba3-7152-9b76-d7baadc5cebe/transition	\N	\N	{"to":"clarification"}	172.19.0.1	2026-05-03 16:31:46	2026-05-03 16:31:46
019deeaf-0cab-71c7-bdca-e14c298bc30c	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1ba3-7152-9b76-d7baadc5cebe/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-03 16:32:22	2026-05-03 16:32:22
019deeb0-108c-7053-83f6-7469199df8fb	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-1761-70c7-a2de-4906fe0a7166	\N	\N	[]	172.19.0.1	2026-05-03 16:33:29	2026-05-03 16:33:29
019deeb9-5b8f-728b-aa1f-345688143a25	019de42b-1761-70c7-a2de-4906fe0a7166	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":null,"attachment_ids":[],"status":"clarification","notify":true}	172.19.0.1	2026-05-03 16:43:38	2026-05-03 16:43:38
019deeb9-8e09-712a-9c8f-0a54dc9fa8ab	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	\N	[]	172.19.0.1	2026-05-03 16:43:51	2026-05-03 16:43:51
019deebb-2c9c-70be-bc2e-d02f149bf460	019de42b-15b1-72fa-9387-b143e6dbf3ea	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":null,"attachment_ids":[],"status":"clarification","notify":true}	172.19.0.1	2026-05-03 16:45:37	2026-05-03 16:45:37
019deebb-40ab-7079-aa84-03b522de14a2	019de42b-15b1-72fa-9387-b143e6dbf3ea	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":null,"attachment_ids":[],"status":"objection","notify":true}	172.19.0.1	2026-05-03 16:45:42	2026-05-03 16:45:42
019deebb-757b-72bf-bb94-4bdd533248d5	019de42b-15b1-72fa-9387-b143e6dbf3ea	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":null,"attachment_ids":[],"status":"objection","notify":true}	172.19.0.1	2026-05-03 16:45:55	2026-05-03 16:45:55
019deec5-f60b-7217-b79f-33d787ae473e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	\N	[]	172.19.0.1	2026-05-03 16:57:24	2026-05-03 16:57:24
019deeed-833d-7237-81ce-94a61a296543	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":null,"attachment_ids":[],"status":"clarification","notify":true}	172.19.0.1	2026-05-03 17:40:36	2026-05-03 17:40:36
019deeed-bff5-716c-83dd-697b32561b3d	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd	\N	\N	{"title":"Choix du prestataire audit RGPD","content":"# Consultation\\n\\n## Question\\nQuel prestataire retenir pour l'audit RGPD annuel ?\\n\\n## Options\\n1. **DataProtect** \\u2013 8 000\\u20ac, d\\u00e9lai 3 semaines\\n2. **SecureLaw** \\u2013 12 000\\u20ac, d\\u00e9lai 2 semaines\\n3. **PrivacyFirst** \\u2013 6 500\\u20ac, d\\u00e9lai 5 semaines","animator_id":"019de42b-14cf-7203-ba64-cc6c7ba1cf94","circle_id":"019de42b-1b14-717d-8de3-a2df0be3c3a4","category_ids":["019de42b-1aec-73e2-81ce-ce77521b3f2a","019de42b-1aee-70be-8798-b90c40d961cc"],"model_id":null,"excluded_members":[],"revision_attachment_ids":["019de42b-1ce4-71cb-ab6f-2cf371e41e51","019de42b-1ce7-717e-9377-093d6e6a7a64"],"notify":false}	172.19.0.1	2026-05-03 17:40:51	2026-05-03 17:40:51
019deeed-c337-72ea-a225-384337505328	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":"# Consultation\\n\\n## Question\\nQuel prestataire retenir pour l'audit RGPD annuel ?\\n\\n## Options\\n1. **DataProtect** \\u2013 8 000\\u20ac, d\\u00e9lai 3 semaines\\n2. **SecureLaw** \\u2013 12 000\\u20ac, d\\u00e9lai 2 semaines\\n3. **PrivacyFirst** \\u2013 6 500\\u20ac, d\\u00e9lai 5 semaines","attachment_ids":["019de42b-1ce4-71cb-ab6f-2cf371e41e51","019de42b-1ce7-717e-9377-093d6e6a7a64"],"notify":false,"status":"clarification"}	172.19.0.1	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-dfcf-71e5-affd-581021965c4c	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-03 17:40:59	2026-05-03 17:40:59
019def0f-2ac4-723e-9cc8-9481a415c0a8	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"custom_views":[{"name":"Mes urgences","filters":{"status":["objection","reaction"],"priority":1}},{"name":"Archiv\\u00e9es","filters":{"status":["adopted","abandoned"]}},{"id":"my-animations","label":"Mes animations","icon":"fa-solid fa-user-tie","filters":{"role":"animator"}},{"id":"pending-actions","label":"R\\u00e9actions attendues","icon":"fa-solid fa-clock","filters":{"action":"pending"}},{"id":"urgent-decisions","label":"D\\u00e9cisions urgentes","icon":"fa-solid fa-triangle-exclamation","filters":{"urgency":"urgent"}}]}	172.19.0.1	2026-05-03 18:17:21	2026-05-03 18:17:21
019def6f-2b26-7185-bd0d-cd8788955d08	019de42b-14cf-7203-ba64-cc6c7ba1cf94	DELETE api/v1/admin/config/api-key	\N	\N	[]	172.19.0.1	2026-05-03 20:02:13	2026-05-03 20:02:13
019def6f-3d63-709c-874a-091fbe37a8cb	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/admin/config	\N	\N	{"config":{"public_api_key":null,"public_circles":["019de42b-1b11-705c-a4a1-ffac5b03ab22","019de42b-1b17-72f8-9b71-b8ae0cc4730f","019de42b-1b19-7381-b0eb-0237e1a655b9","019de42b-1b1c-7112-b34e-ea76d02e771e"],"public_categories":["019de42b-1af3-7225-98c5-2808199094c1","019de42b-1af0-7320-b7d2-ae478f5bb73b","019de42b-1aee-70be-8798-b90c40d961cc","019de42b-1aec-73e2-81ce-ce77521b3f2a","019de42b-1ae9-71ef-ad60-8dc3b83c3082","019de42b-1ae7-7239-84d8-eebd671fb8aa"],"public_statuses":["clarification","reaction","objection","adopted","abandoned","rejected","suspended"],"public_filters":[]}}	172.19.0.1	2026-05-03 20:02:18	2026-05-03 20:02:18
019def76-c068-72b8-893b-779e6a1c558e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"stats","label":"Statistiques g\\u00e9n\\u00e9rales","enabled":false,"width":"full"},{"id":"tickets","label":"Mes tickets actifs","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences & \\u00c9ch\\u00e9ances","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"third"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"third"},{"id":"circles_watch","label":"\\u00c0 surveiller","enabled":true,"width":"third"}]}	172.19.0.1	2026-05-03 20:10:30	2026-05-03 20:10:30
019def78-d582-72c8-8be0-b19902ea11e8	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"stats","label":"Statistiques g\\u00e9n\\u00e9rales","enabled":true,"width":"half"},{"id":"tickets","label":"Mes tickets actifs","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences & \\u00c9ch\\u00e9ances","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"third"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"third"},{"id":"circles_watch","label":"\\u00c0 surveiller","enabled":true,"width":"third"}]}	172.19.0.1	2026-05-03 20:12:46	2026-05-03 20:12:46
019def79-2a23-70cd-9456-2fda30974e37	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"stats","label":"Statistiques g\\u00e9n\\u00e9rales","enabled":true,"width":"full"},{"id":"tickets","label":"Mes tickets actifs","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences & \\u00c9ch\\u00e9ances","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"third"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"third"},{"id":"circles_watch","label":"\\u00c0 surveiller","enabled":true,"width":"third"}]}	172.19.0.1	2026-05-03 20:13:08	2026-05-03 20:13:08
019def80-5fde-72e2-8a35-7142a3cf3187	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"stats","label":"Statistiques g\\u00e9n\\u00e9rales","enabled":true,"width":"full"},{"id":"tickets","label":"Mes tickets actifs","enabled":true,"width":"full"},{"id":"urgencies","label":"Urgences & \\u00c9ch\\u00e9ances","enabled":true,"width":"full"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"third"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"third"},{"id":"circles_watch","label":"\\u00c0 surveiller","enabled":true,"width":"third"}]}	172.19.0.1	2026-05-03 20:21:01	2026-05-03 20:21:01
019def9c-5715-70ac-ad9e-3ac6bf99231a	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"tickets","label":"Mes tickets actifs","enabled":true,"width":"full"},{"id":"stats","label":"Statistiques g\\u00e9n\\u00e9rales","enabled":true,"width":"full"},{"id":"urgencies","label":"Urgences & \\u00c9ch\\u00e9ances","enabled":true,"width":"full"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"third"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"third"},{"id":"circles_watch","label":"\\u00c0 surveiller","enabled":true,"width":"third"}]}	172.19.0.1	2026-05-03 20:51:33	2026-05-03 20:51:33
019def9c-d4ad-7014-b7f8-4c04048f3537	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/admin/config	\N	\N	{"config":{"app_name":"GVL - D\\u00e9cisions a Z\\u00e9ro Objection","app_logo":"branding\\/6IHIhGeyaEoOHRrMynPobRYv5ne7w0TOhIXMbTMh.jpg","decision_reaction_days":"3","decision_objection_days":"3","decision_revision_months":"6","reminder_hours_before":"24","public_registration":"true","mail_sender_name":"DAZO Notifications","mail_contact_address":"contact@dazo.app","maintenance_mode":"false","enable_public_front":"true","require_admin_approval":"true","allowed_domains":null,"recaptcha_site_key":"admin@dazo.test","recaptcha_secret_key":"password","public_circles":["019de42b-1b11-705c-a4a1-ffac5b03ab22","019de42b-1b17-72f8-9b71-b8ae0cc4730f","019de42b-1b19-7381-b0eb-0237e1a655b9","019de42b-1b1c-7112-b34e-ea76d02e771e"],"public_categories":["019de42b-1af3-7225-98c5-2808199094c1","019de42b-1af0-7320-b7d2-ae478f5bb73b","019de42b-1aee-70be-8798-b90c40d961cc","019de42b-1aec-73e2-81ce-ce77521b3f2a","019de42b-1ae9-71ef-ad60-8dc3b83c3082","019de42b-1ae7-7239-84d8-eebd671fb8aa"],"public_statuses":["clarification","reaction","objection","adopted","abandoned","rejected","suspended"],"public_filters":[],"public_api_key":null,"page_legal_content":"<h1>Mentions L\\u00e9gales<\\/h1><p>Contenu par d\\u00e9faut \\u00e0 personnaliser...<\\/p>","page_privacy_content":"<h1>Politique de Confidentialit\\u00e9<\\/h1><p>Contenu par d\\u00e9faut \\u00e0 personnaliser...<\\/p>","page_terms_content":"<h1>Conditions G\\u00e9n\\u00e9rales d'Utilisation<\\/h1><p>Contenu par d\\u00e9faut \\u00e0 personnaliser...<\\/p>","mail_new_decision_subject":"Nouvelle proposition de d\\u00e9cision","mail_new_decision_body":"<h1>Nouvelle d\\u00e9cision<\\/h1><p>Bonjour {user_name},<\\/p><p>Une nouvelle proposition \\"{decision_title}\\" a \\u00e9t\\u00e9 publi\\u00e9e.<\\/p><p><a href=\\"{link}\\">Voir la d\\u00e9cision<\\/a><\\/p>","mail_phase_change_subject":"Changement de Phase","mail_phase_change_body":"<h1>Changement de phase<\\/h1><p>Bonjour {user_name},<\\/p><p>La d\\u00e9cision \\"{decision_title}\\" est pass\\u00e9e en nouvelle phase.<\\/p><p><a href=\\"{link}\\">Voir la d\\u00e9cision<\\/a><\\/p>","mail_reminder_subject":"\\u26a0\\ufe0f Rappel : La d\\u00e9cision '{title}' arrive \\u00e0 \\u00e9ch\\u00e9ance","mail_reminder_body":"Bonjour {name},\\n\\nCeci est un rappel concernant la d\\u00e9cision : **{title}**.\\n\\nLa phase actuelle (**{phase}**) arrive bient\\u00f4t \\u00e0 \\u00e9ch\\u00e9ance. Votre participation est attendue afin de faire progresser le processus.\\n\\n**\\u00c9ch\\u00e9ance :** {deadline}\\n\\nMerci de votre contribution.","mail_decision_adopted_subject":"Une d\\u00e9cision a \\u00e9t\\u00e9 adopt\\u00e9e","mail_decision_adopted_body":"<h1>D\\u00e9cision adopt\\u00e9e !<\\/h1><p>Bonjour {user_name},<\\/p><p>La proposition \\"{decision_title}\\" a \\u00e9t\\u00e9 officiellement adopt\\u00e9e.<\\/p><p><a href=\\"{link}\\">Voir le r\\u00e9sultat<\\/a><\\/p>","mail_decision_rejected_subject":"Une d\\u00e9cision n'a pas \\u00e9t\\u00e9 adopt\\u00e9e","mail_decision_rejected_body":"<h1>D\\u00e9cision refus\\u00e9e<\\/h1><p>Bonjour {user_name},<\\/p><p>La proposition \\"{decision_title}\\" n'a pas recueilli le consensus n\\u00e9cessaire.<\\/p><p><a href=\\"{link}\\">Voir les d\\u00e9tails<\\/a><\\/p>","mail_invitation_subject":"\\ud83d\\udce9 Invitation \\u00e0 rejoindre le cercle '{circle}'","mail_invitation_body":"Bonjour,\\n\\nVous avez \\u00e9t\\u00e9 invit\\u00e9 \\u00e0 rejoindre le cercle **{circle}** sur la plateforme DAZO par **{inviter}**.\\n\\nCe cercle traite des sujets suivants : {description}\\n\\n[Accepter l'invitation]({url})","mail_host":null,"mail_port":"587","mail_username":null,"mail_password":null,"mail_encryption":"tls","google_client_id":null,"google_client_secret":null,"github_client_id":null,"github_client_secret":null,"facebook_client_id":null,"facebook_client_secret":null,"twitter_client_id":null,"twitter_client_secret":null,"linkedin_client_id":null,"linkedin_client_secret":null,"microsoft_client_id":null,"microsoft_client_secret":null,"franceconnect_client_id":null,"franceconnect_client_secret":null,"mail_template_logo":"\\/images\\/logo-email.png","mail_template_site_link":"https:\\/\\/dazo.app","mail_template_wrapper":"<div style=\\"font-family: 'Inter', sans-serif; background-color: #f8fafc; padding: 40px 20px; color: #1e293b;\\">\\n  <div style=\\"max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);\\">\\n    <div style=\\"padding: 32px; text-align: center; border-bottom: 1px solid #f1f5f9;\\">\\n      <a href=\\"{site_link}\\">\\n        <img src=\\"{logo}\\" alt=\\"Logo\\" style=\\"max-height: 48px; width: auto;\\">\\n      <\\/a>\\n    <\\/div>\\n    <div style=\\"padding: 40px; line-height: 1.6; font-size: 16px;\\">\\n      {message}\\n    <\\/div>\\n    <div style=\\"padding: 32px; background: #f8fafc; text-align: center; font-size: 14px; color: #64748b; border-top: 1px solid #f1f5f9;\\">\\n      <p style=\\"margin: 0 0 16px 0;\\">Vous recevez cet email car vous participez \\u00e0 la gouvernance sur notre plateforme.<\\/p>\\n      <div style=\\"display: flex; justify-content: center; gap: 16px;\\">\\n        <a href=\\"{site_link}\\" style=\\"color: #3b82f6; text-decoration: none; font-weight: 600;\\">Notre Site<\\/a>\\n        <span style=\\"color: #cbd5e1;\\">&bull;<\\/span>\\n        <a href=\\"{site_link_register}\\" style=\\"color: #3b82f6; text-decoration: none; font-weight: 600;\\">S'inscrire<\\/a>\\n      <\\/div>\\n    <\\/div>\\n  <\\/div>\\n<\\/div>","legal_mentions_url":null,"privacy_policy_url":null,"terms_of_service_url":null,"allowed_file_types":null,"max_file_size_mb":"10","reminder_email_subject":"\\u26a0\\ufe0f Rappel : La d\\u00e9cision '{title}' arrive \\u00e0 \\u00e9ch\\u00e9ance","reminder_email_body":"Bonjour {name},\\n\\nCeci est un rappel concernant la d\\u00e9cision : **{title}**.\\n\\nLa phase actuelle (**{phase}**) arrive bient\\u00f4t \\u00e0 \\u00e9ch\\u00e9ance. Votre participation est attendue afin de faire progresser le processus.\\n\\n**\\u00c9ch\\u00e9ance :** {deadline}\\n\\nMerci de votre contribution.","auth_twitter_enabled":"false","auth_linkedin-openid_enabled":"false","auth_gitlab_enabled":"false","page_legal_title":"Mentions L\\u00e9gales","page_legal_slug":"mentions-legales","page_privacy_title":"Politique de Confidentialit\\u00e9","page_privacy_slug":"politique-confidentialite","page_terms_title":"Conditions G\\u00e9n\\u00e9rales d'Utilisation","page_terms_slug":"cgu","auth_github_client_id":null,"auth_github_client_secret":null,"auth_microsoft_client_id":null,"auth_microsoft_client_secret":null,"auth_facebook_client_id":null,"auth_facebook_client_secret":null,"auth_apple_client_id":null,"auth_apple_client_secret":null,"auth_franceconnect_client_id":null,"auth_franceconnect_client_secret":null,"mail_template_logo_perso":null,"auth_twitter_client_id":null,"auth_twitter_client_secret":null,"auth_linkedin-openid_client_id":null,"auth_linkedin-openid_client_secret":null,"auth_gitlab_client_id":null,"auth_gitlab_client_secret":null,"mail_notification_subject":"\\ud83d\\udce2 Nouvelle \\u00e9tape pour : {title}","mail_notification_body":"Bonjour {name},\\n\\nLa d\\u00e9cision **{title}** vient de passer en phase de **{phase}**.\\n\\nVous pouvez consulter les d\\u00e9tails et participer ici : [Voir la d\\u00e9cision]({url})","mail_contact_subject":"\\u2709\\ufe0f Nouveau message de contact : {subject}","mail_contact_body":"Nom : {name}\\nEmail : {email}\\n\\nMessage :\\n{message}","auth_google_enabled":"false","auth_google_client_id":null,"auth_google_client_secret":null,"auth_github_enabled":"false","auth_microsoft_enabled":"false","auth_facebook_enabled":"false","auth_apple_enabled":"false","auth_franceconnect_enabled":"false","mail_template_site_link_register":"https:\\/\\/dazo.app\\/register","page_legal_enabled":"true"}}	172.19.0.1	2026-05-03 20:52:05	2026-05-03 20:52:05
019defa1-7f2f-7050-9bb0-46705b6acf97	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1c0c-72b3-aa14-8cea6be187b7/extend	\N	\N	[]	172.19.0.1	2026-05-03 20:57:11	2026-05-03 20:57:11
019defa1-c9dc-7338-8748-21bda354e73b	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1c0c-72b3-aa14-8cea6be187b7/transition	\N	\N	{"to":"adopted","notify":false,"is_meeting":true}	172.19.0.1	2026-05-03 20:57:30	2026-05-03 20:57:30
019defac-80eb-717b-8b65-04a58cff118e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"urgencies","label":"Urgences","enabled":true,"width":"full"},{"id":"stats","label":"Statistiques","enabled":true,"width":"full"},{"id":"my_circles","label":"Mes cercles","enabled":true,"width":"half"},{"id":"categories","label":"Cat\\u00e9gories","enabled":true,"width":"half"},{"id":"tickets","label":"Clarifications & Objections","enabled":true,"width":"full"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"third"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"third"},{"id":"circles_watch","label":"Mes cercles (flux)","enabled":true,"width":"third"}]}	172.19.0.1	2026-05-03 21:09:13	2026-05-03 21:09:13
019defad-0456-7351-a7ab-d121a27161b8	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"urgencies","label":"Urgences","enabled":true,"width":"full"},{"id":"stats","label":"Statistiques","enabled":true,"width":"full"},{"id":"my_circles","label":"Mes cercles","enabled":true,"width":"half"},{"id":"categories","label":"Cat\\u00e9gories","enabled":true,"width":"half"},{"id":"tickets","label":"Clarifications & Objections","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"half"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"half"},{"id":"circles_watch","label":"Mes cercles (flux)","enabled":true,"width":"half"}]}	172.19.0.1	2026-05-03 21:09:46	2026-05-03 21:09:46
019defae-04fb-73d3-8cd3-6508238fef4f	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"stats","label":"Statistiques","enabled":true,"width":"full"},{"id":"urgencies","label":"Urgences","enabled":true,"width":"full"},{"id":"tickets","label":"Clarifications & Objections","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"half"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"half"},{"id":"circles_watch","label":"Mes cercles (flux)","enabled":true,"width":"half"},{"id":"categories","label":"Cat\\u00e9gories","enabled":true,"width":"half"},{"id":"my_circles","label":"Mes cercles","enabled":true,"width":"half"}]}	172.19.0.1	2026-05-03 21:10:52	2026-05-03 21:10:52
019defae-856c-73ad-92e9-18e5e49db829	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-1688-7290-b257-cd7e06dcb2d4	\N	\N	[]	172.19.0.1	2026-05-03 21:11:25	2026-05-03 21:11:25
019defae-c489-7297-a87a-9076e8442970	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	\N	[]	172.19.0.1	2026-05-03 21:11:41	2026-05-03 21:11:41
019defbb-f394-7281-8435-8ba6a081baeb	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1d1a-7065-a10a-37d15a5a650d/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-03 21:26:05	2026-05-03 21:26:05
019defbc-37b3-7212-bfc0-08bfb6e92ba4	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/decisions/019de42b-1d1a-7065-a10a-37d15a5a650d	\N	\N	{"title":"Augmentation g\\u00e9n\\u00e9rale de 3%","content":"# Proposition\\n\\n## Contexte\\nInflation de 4,2% sur les 12 derniers mois.\\n\\n## Proposition\\nAugmentation g\\u00e9n\\u00e9rale de 3% au 1er juillet pour tous les salari\\u00e9s.","animator_id":"019de42b-1ab2-7286-824f-90df7187f28e","circle_id":"019de42b-1b19-7381-b0eb-0237e1a655b9","category_ids":["019de42b-1ae9-71ef-ad60-8dc3b83c3082","019de42b-1aec-73e2-81ce-ce77521b3f2a"],"model_id":null,"excluded_members":[],"revision_attachment_ids":[],"notify":false}	172.19.0.1	2026-05-03 21:26:22	2026-05-03 21:26:22
019defbc-37dd-724d-9b80-7f2f4cb86aa7	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1d1a-7065-a10a-37d15a5a650d/versions	\N	\N	{"content":"# Proposition\\n\\n## Contexte\\nInflation de 4,2% sur les 12 derniers mois.\\n\\n## Proposition\\nAugmentation g\\u00e9n\\u00e9rale de 3% au 1er juillet pour tous les salari\\u00e9s.","attachment_ids":[],"notify":false,"status":"objection"}	172.19.0.1	2026-05-03 21:26:22	2026-05-03 21:26:22
019defcf-d9b1-71bb-ae47-1f2c2a8d0742	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":"# Consultation\\n\\n## Question\\nQuel prestataire retenir pour l'audit RGPD annuel ?\\n\\n## Options\\n1. **DataProtect** \\u2013 8 000\\u20ac, d\\u00e9lai 3 semaines\\n2. **SecureLaw** \\u2013 12 000\\u20ac, d\\u00e9lai 2 semaines\\n3. **PrivacyFirst** \\u2013 6 500\\u20ac, d\\u00e9lai 5 semaines","attachment_ids":["019deeed-c018-72ab-91a8-26c180da8edd","019deeed-c019-70d4-91a3-cf2a040ea4c5"],"status":{"isTrusted":true,"_vts":1777844869413},"notify":true}	172.19.0.1	2026-05-03 21:47:49	2026-05-03 21:47:49
019defd0-17c8-70a6-914a-8f5cce0746a8	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":"# Consultation\\n\\n## Question\\nQuel prestataire retenir pour l'audit RGPD annuel ?\\n\\n## Options\\n1. **DataProtect** \\u2013 8 000\\u20ac, d\\u00e9lai 3 semaines\\n2. **SecureLaw** \\u2013 12 000\\u20ac, d\\u00e9lai 2 semaines\\n3. **PrivacyFirst** \\u2013 6 500\\u20ac, d\\u00e9lai 5 semaines","attachment_ids":["019deeed-c018-72ab-91a8-26c180da8edd","019deeed-c019-70d4-91a3-cf2a040ea4c5"],"status":{"isTrusted":true,"_vts":1777844885317},"notify":true}	172.19.0.1	2026-05-03 21:48:05	2026-05-03 21:48:05
019defd0-7ee2-7338-a18a-33a39ac9165a	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":"# Consultation\\n\\n## Question\\nQuel prestataire retenir pour l'audit RGPD annuel ?\\n\\n## Options\\n1. **DataProtect** \\u2013 8 000\\u20ac, d\\u00e9lai 3 semaines\\n2. **SecureLaw** \\u2013 12 000\\u20ac, d\\u00e9lai 2 semaines\\n3. **PrivacyFirst** \\u2013 6 500\\u20ac, d\\u00e9lai 5 semaines","attachment_ids":["019deeed-c018-72ab-91a8-26c180da8edd","019deeed-c019-70d4-91a3-cf2a040ea4c5"],"status":"clarification","notify":true}	172.19.0.1	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd7-ec53-70f4-81d1-6579933c0484	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/auth/avatar	\N	\N	{"avatar":{}}	172.19.0.1	2026-05-03 21:56:38	2026-05-03 21:56:38
019defd7-f844-73bf-bb6c-6fc3052a0a16	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"name":"Alice Durand","email":"admin@dazo.test"}	172.19.0.1	2026-05-03 21:56:41	2026-05-03 21:56:41
019defe0-1ae2-7106-bb05-b21a9859b05d	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1bee-7325-a8ed-067285f10b54/versions	\N	\N	{"content":"<p># Proposition ## Contexte La pause de 12h \\u00e0 13h30 est jug\\u00e9e trop rigide par certains. ## Proposition Laisser chacun organiser sa pause librement (min 30 min obligatoire).<\\/p><p><br><\\/p><p>Version 2 !!!!<\\/p>","attachment_ids":[],"status":"objection","notify":true}	172.19.0.1	2026-05-03 22:05:34	2026-05-03 22:05:34
019defe1-487c-7090-8efd-baf7c18c5cc2	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1bee-7325-a8ed-067285f10b54/versions/019defe0-1ad7-70a9-b097-b727b17ce6e8/consent	\N	\N	{"type":"no_objection","acting_as_user_id":"019de42b-1839-721b-990c-b44db2a24f10","notify":false}	172.19.0.1	2026-05-03 22:06:52	2026-05-03 22:06:52
019defe1-7437-71ce-ab97-702ceb9c06ce	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1bee-7325-a8ed-067285f10b54/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-03 22:07:03	2026-05-03 22:07:03
019defe1-a252-7370-9255-6a541b7811d6	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1bee-7325-a8ed-067285f10b54/versions	\N	\N	{"content":"<p># Proposition ## Contexte La pause de 12h \\u00e0 13h30 est jug\\u00e9e trop rigide par certains. ## Proposition Laisser chacun organiser sa pause librement (min 30 min obligatoire).<\\/p><p><br><\\/p><p>Version 2 !!!!<\\/p><p><br><\\/p><p>non, 3!<\\/p>","attachment_ids":[],"status":"objection","notify":true}	172.19.0.1	2026-05-03 22:07:15	2026-05-03 22:07:15
019defe1-b647-70f6-b6bc-43fc01e5e721	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1bee-7325-a8ed-067285f10b54/versions/019defe1-a240-72a5-9660-08244947628d/consent	\N	\N	{"type":"no_objection","acting_as_user_id":"019de42b-1839-721b-990c-b44db2a24f10","notify":false}	172.19.0.1	2026-05-03 22:07:20	2026-05-03 22:07:20
019defe1-bbcf-7198-8130-0742c01ca048	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1bee-7325-a8ed-067285f10b54/versions/019defe1-a240-72a5-9660-08244947628d/consent	\N	\N	{"type":"no_objection","acting_as_user_id":"019de42b-19e1-7098-9b5c-395edc9a3d85","notify":false}	172.19.0.1	2026-05-03 22:07:21	2026-05-03 22:07:21
019defe2-1083-705a-9bef-b5ff5601d188	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1bee-7325-a8ed-067285f10b54/transition	\N	\N	{"to":"adopted","notify":false,"is_meeting":true}	172.19.0.1	2026-05-03 22:07:43	2026-05-03 22:07:43
019defe5-a98c-714d-8bec-d4110f57608e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/transition	\N	\N	{"to":"revision"}	172.19.0.1	2026-05-03 22:11:39	2026-05-03 22:11:39
019defe5-f5bc-70f3-95c9-4831d3b39217	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/decisions/019de42b-1cce-73d1-a0fe-cf31bff682dd/versions	\N	\N	{"content":"<p># Consultation ## Question Quel prestataire retenir pour l'audit RGPD annuel ? ## Options 1. **DataProtect** \\u2013 8 000\\u20ac, d\\u00e9lai 3 semaines 2. **SecureLaw** \\u2013 12 000\\u20ac, d\\u00e9lai 2 semaines 3. **PrivacyFirst** \\u2013 6 500\\u20ac, d\\u00e9lai 5 semaines<\\/p><p><br><\\/p><p>encore une version !<\\/p>","attachment_ids":["019defd0-7bd3-731a-bdac-d9c237ac8d6f","019defd0-7bd4-721d-bb6b-2e9aa7e07718"],"status":"clarification","notify":true}	172.19.0.1	2026-05-03 22:11:58	2026-05-03 22:11:58
019defe8-0224-73df-82fb-059951f94a98	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/attachments	\N	\N	{"file":{}}	172.19.0.1	2026-05-03 22:14:12	2026-05-03 22:14:12
019df01c-26df-7294-9df8-65c403cdcfa2	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"stats","label":"Statistiques","enabled":true,"width":"full"},{"id":"urgencies","label":"Urgences","enabled":true,"width":"half"},{"id":"tickets","label":"Clarifications & Objections","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"half"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"half"},{"id":"circles_watch","label":"Mes cercles (flux)","enabled":true,"width":"half"},{"id":"categories","label":"Cat\\u00e9gories","enabled":true,"width":"half"},{"id":"my_circles","label":"Mes cercles","enabled":true,"width":"half"}]}	172.19.0.1	2026-05-03 23:11:10	2026-05-03 23:11:10
019df01d-0c17-73b0-a98b-759b3e9cc64c	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"stats","label":"Statistiques","enabled":true,"width":"full"},{"id":"my_circles","label":"Mes cercles","enabled":true,"width":"third"},{"id":"categories","label":"Cat\\u00e9gories","enabled":true,"width":"half"},{"id":"tickets","label":"Clarifications & Objections","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"half"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"half"},{"id":"circles_watch","label":"Mes cercles (flux)","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences","enabled":true,"width":"half"}]}	172.19.0.1	2026-05-03 23:12:08	2026-05-03 23:12:08
019df01d-da62-7397-98d3-7f2c112fcc90	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"stats","label":"Statistiques","enabled":false,"width":"full"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"full"},{"id":"my_circles","label":"Mes cercles","enabled":true,"width":"third"},{"id":"categories","label":"Cat\\u00e9gories","enabled":true,"width":"half"},{"id":"tickets","label":"Clarifications & Objections","enabled":true,"width":"half"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"half"},{"id":"circles_watch","label":"Mes cercles (flux)","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences","enabled":true,"width":"half"}]}	172.19.0.1	2026-05-03 23:13:01	2026-05-03 23:13:01
019df030-aa97-73b7-9a9c-9e2d1302bc7e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	PUT api/v1/auth/me	\N	\N	{"dashboard_widgets":[{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"full"},{"id":"my_circles","label":"Mes cercles","enabled":true,"width":"third"},{"id":"stats","label":"Statistiques","enabled":true,"width":"full"},{"id":"categories","label":"Cat\\u00e9gories","enabled":true,"width":"half"},{"id":"tickets","label":"Clarifications & Objections","enabled":true,"width":"half"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"half"},{"id":"circles_watch","label":"Mes cercles (flux)","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences","enabled":true,"width":"half"}]}	172.19.0.1	2026-05-03 23:33:34	2026-05-03 23:33:34
019df031-711a-71cc-aae8-ea86e1ac5d81	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-1761-70c7-a2de-4906fe0a7166	\N	\N	[]	172.19.0.1	2026-05-03 23:34:25	2026-05-03 23:34:25
019df031-8a0a-733e-8575-1e51e235b7fe	019de42b-14cf-7203-ba64-cc6c7ba1cf94	POST api/v1/admin/impersonate/019de42b-190e-70c3-9d21-2b6d2b36612c	\N	\N	[]	172.19.0.1	2026-05-03 23:34:31	2026-05-03 23:34:31
\.


--
-- Data for Name: attachments; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.attachments (id, decision_version_id, uploader_id, filename, s3_path, mime_type, size_bytes, created_at, updated_at) FROM stdin;
019de42b-1b7f-7009-850a-5729b6a78931	019de42b-1b7d-7275-b27a-b1b0d31463ad	019de42b-15b1-72fa-9387-b143e6dbf3ea	budget-previsionnel.xlsx	seed-attachments/130ab6f9-12c8-4916-8325-215b4cafdc04/budget-previsionnel.xlsx	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet	89000	2026-04-29 15:46:03	2026-05-01 15:32:03
019de42b-1b82-7147-bcdc-97b4f9332b4d	019de42b-1b7d-7275-b27a-b1b0d31463ad	019de42b-15b1-72fa-9387-b143e6dbf3ea	maquette-v2.png	seed-attachments/426718ef-f9e4-4fcf-92b9-ee5931086841/maquette-v2.png	image/png	1200000	2026-04-29 17:12:03	2026-05-01 15:32:03
019de42b-1ba1-735b-b16c-89d1ec800c98	019de42b-1b93-7109-a713-dbcf7b2688a7	019de42b-1688-7290-b257-cd7e06dcb2d4	budget-previsionnel.xlsx	seed-attachments/b34825b6-8efe-4919-8965-16933fd6030c/budget-previsionnel.xlsx	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet	89000	2026-04-26 08:43:03	2026-05-01 15:32:03
019de42b-1bec-7188-bdc8-51a3b059a1d7	019de42b-1be2-71f4-8133-7283b4861586	019de42b-190e-70c3-9d21-2b6d2b36612c	budget-previsionnel.xlsx	seed-attachments/182750d7-afd9-4df1-bc30-1fed87ef0d59/budget-previsionnel.xlsx	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet	89000	2026-04-28 07:13:03	2026-05-01 15:32:03
019de42b-1c23-7383-96db-e678a3c93d56	019de42b-1c18-737d-97db-14ebe8957f05	019de42b-14cf-7203-ba64-cc6c7ba1cf94	budget-previsionnel.xlsx	seed-attachments/11d65a00-6aa0-4b87-823f-bebe8920d269/budget-previsionnel.xlsx	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet	89000	2026-04-26 04:40:03	2026-05-01 15:32:03
019de42b-1c7d-70c5-bee3-de26aa2ac629	019de42b-1c76-73d4-9a8e-35c6ab840593	019de42b-1761-70c7-a2de-4906fe0a7166	maquette-v2.png	seed-attachments/8edcad22-fdb2-4cb0-9f77-9fb712b09b12/maquette-v2.png	image/png	1200000	2026-04-02 17:08:03	2026-05-01 15:32:03
019de42b-1cbc-73ce-8645-063fe580fc4d	019de42b-1cb5-72a3-8c0c-9a3e29de43f8	019de42b-1ab2-7286-824f-90df7187f28e	budget-previsionnel.xlsx	seed-attachments/11f8a5f3-8a82-4322-8fad-751f038f9397/budget-previsionnel.xlsx	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet	89000	2026-04-11 15:44:03	2026-05-01 15:32:03
019de42b-1cbe-715c-82ba-b3f5eb617dc3	019de42b-1cb5-72a3-8c0c-9a3e29de43f8	019de42b-1ab2-7286-824f-90df7187f28e	maquette-v2.png	seed-attachments/32dedecd-fe75-40a3-b42a-0ba711c5a188/maquette-v2.png	image/png	1200000	2026-04-11 17:29:03	2026-05-01 15:32:03
019de42b-1d13-715f-9857-0b1065031369	019de42b-1d0c-707d-8cf2-ce0f0b302bf5	019de42b-1688-7290-b257-cd7e06dcb2d4	budget-previsionnel.xlsx	seed-attachments/e2413aa6-a321-4154-84dc-f9af5ce23e06/budget-previsionnel.xlsx	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet	89000	2026-04-21 01:18:03	2026-05-01 15:32:03
019de42b-1d16-7209-a657-1ebe8f9068c3	019de42b-1d0c-707d-8cf2-ce0f0b302bf5	019de42b-1688-7290-b257-cd7e06dcb2d4	maquette-v2.png	seed-attachments/f07daec9-a23e-448e-b16c-1ddbee183c27/maquette-v2.png	image/png	1200000	2026-04-21 00:13:03	2026-05-01 15:32:03
019de42b-1d18-7037-b4b0-60a3b262f535	019de42b-1d0c-707d-8cf2-ce0f0b302bf5	019de42b-1688-7290-b257-cd7e06dcb2d4	compte-rendu-reunion.pdf	seed-attachments/f62c6e0d-7191-4ef8-899c-2d2bd5b35a9b/compte-rendu-reunion.pdf	application/pdf	156000	2026-04-20 23:55:03	2026-05-01 15:32:03
019de42b-1d6e-7040-a77a-7c3fd282c23b	019de42b-1d6c-728c-91dd-c7e1e533d722	019de42b-1ab2-7286-824f-90df7187f28e	budget-previsionnel.xlsx	seed-attachments/2afa0890-e7e2-43fe-a0d0-b36159aff2b1/budget-previsionnel.xlsx	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet	89000	2026-04-21 13:28:04	2026-05-01 15:32:04
019de42b-1d8c-7058-81c2-fe17c538373f	019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-14cf-7203-ba64-cc6c7ba1cf94	DAZO-04-24-2026_01_54_PM (1).png	attachments/QBHOGUBPKtg5BZjvHx9HcYRAn4xZEmcLNCQ2PDxS.png	image/png	836950	2026-04-23 12:37:04	2026-05-02 11:08:04
019de42b-1ce4-71cb-ab6f-2cf371e41e51	019de42b-1cdd-72f9-a1ec-fe038fc63086	019de42b-15b1-72fa-9387-b143e6dbf3ea	80200_Budget_Annexe_Lotissement_Chateau_Rousset_BP_2026_Maquette_Budgetaire_M57.pdf	attachments/nWDNvjUGdQ4wc0HxpJMGaOYRkeljGUoEDdIKuHx0.pdf	application/pdf	665883	2026-04-30 11:31:03	2026-05-03 11:48:32
019de42b-1ce7-717e-9377-093d6e6a7a64	019de42b-1cdd-72f9-a1ec-fe038fc63086	019de42b-15b1-72fa-9387-b143e6dbf3ea	Logo-Gennes-Val-de-Loire_CMJN.jpg	attachments/XX1hj90B08WtmWZsTGzgoAHfndhF7Yc1dsNWFWYJ.jpg	image/jpeg	81543	2026-04-30 10:39:03	2026-05-03 11:51:28
019deeed-c018-72ab-91a8-26c180da8edd	019deeed-c017-70b8-ae26-d80a70af6866	019de42b-15b1-72fa-9387-b143e6dbf3ea	80200_Budget_Annexe_Lotissement_Chateau_Rousset_BP_2026_Maquette_Budgetaire_M57.pdf	attachments/nWDNvjUGdQ4wc0HxpJMGaOYRkeljGUoEDdIKuHx0.pdf	application/pdf	665883	2026-05-03 17:40:51	2026-05-03 17:40:51
019deeed-c019-70d4-91a3-cf2a040ea4c5	019deeed-c017-70b8-ae26-d80a70af6866	019de42b-15b1-72fa-9387-b143e6dbf3ea	Logo-Gennes-Val-de-Loire_CMJN.jpg	attachments/XX1hj90B08WtmWZsTGzgoAHfndhF7Yc1dsNWFWYJ.jpg	image/jpeg	81543	2026-05-03 17:40:51	2026-05-03 17:40:51
019defd0-7bd3-731a-bdac-d9c237ac8d6f	019defd0-7bd0-72d8-8537-6603c636980e	019de42b-15b1-72fa-9387-b143e6dbf3ea	80200_Budget_Annexe_Lotissement_Chateau_Rousset_BP_2026_Maquette_Budgetaire_M57.pdf	attachments/nWDNvjUGdQ4wc0HxpJMGaOYRkeljGUoEDdIKuHx0.pdf	application/pdf	665883	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7bd4-721d-bb6b-2e9aa7e07718	019defd0-7bd0-72d8-8537-6603c636980e	019de42b-15b1-72fa-9387-b143e6dbf3ea	Logo-Gennes-Val-de-Loire_CMJN.jpg	attachments/XX1hj90B08WtmWZsTGzgoAHfndhF7Yc1dsNWFWYJ.jpg	image/jpeg	81543	2026-05-03 21:48:31	2026-05-03 21:48:31
019defe5-f1e5-702d-9866-e353c5dbcae3	019defe5-f1e1-702f-aeb6-40e556582f0b	019de42b-15b1-72fa-9387-b143e6dbf3ea	80200_Budget_Annexe_Lotissement_Chateau_Rousset_BP_2026_Maquette_Budgetaire_M57.pdf	attachments/nWDNvjUGdQ4wc0HxpJMGaOYRkeljGUoEDdIKuHx0.pdf	application/pdf	665883	2026-05-03 22:11:57	2026-05-03 22:11:57
019defe5-f1e7-73d5-aaef-fabe701e9f58	019defe5-f1e1-702f-aeb6-40e556582f0b	019de42b-15b1-72fa-9387-b143e6dbf3ea	Logo-Gennes-Val-de-Loire_CMJN.jpg	attachments/XX1hj90B08WtmWZsTGzgoAHfndhF7Yc1dsNWFWYJ.jpg	image/jpeg	81543	2026-05-03 22:11:57	2026-05-03 22:11:57
019defe8-0212-7020-ba0e-2b4adc100ae7	\N	019de42b-14cf-7203-ba64-cc6c7ba1cf94	capitaine-crochet.jpg	attachments/BCTNZXKmEuQIzMJM2pBLoB27uXNg9ddDoJ7J8w9m.jpg	image/jpeg	17598	2026-05-03 22:14:12	2026-05-03 22:14:12
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.categories (id, name, description, color_hex, icon, is_active, created_at, updated_at, deleted_at) FROM stdin;
019de42b-1ae7-7239-84d8-eebd671fb8aa	Stratégie	Orientations long terme et vision	#1e40af	trending-up	t	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1ae9-71ef-ad60-8dc3b83c3082	RH	Gestion de l'humain et bien-être	#9d174d	users	t	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1aec-73e2-81ce-ce77521b3f2a	Finance	Budgets, dépenses et investissements	#065f46	credit-card	t	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1aee-70be-8798-b90c40d961cc	Tech	Outils, infrastructure et architecture	#374151	code	t	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1af0-7320-b7d2-ae478f5bb73b	Juridique	Conformité, contrats et réglementation	#92400e	shield	t	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1af3-7225-98c5-2808199094c1	Produit	Roadmap, UX et fonctionnalités	#5b21b6	package	t	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
\.


--
-- Data for Name: circle_members; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.circle_members (id, circle_id, user_id, role, created_at, updated_at) FROM stdin;
019de42b-1b1f-70eb-b28c-d8afe368096f	019de42b-1b11-705c-a4a1-ffac5b03ab22	019de42b-14cf-7203-ba64-cc6c7ba1cf94	animator	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b22-738e-bfc7-50e56f0a503a	019de42b-1b11-705c-a4a1-ffac5b03ab22	019de42b-1ab2-7286-824f-90df7187f28e	animator	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b25-730e-a4f1-dcf14a045526	019de42b-1b11-705c-a4a1-ffac5b03ab22	019de42b-1688-7290-b257-cd7e06dcb2d4	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b27-7109-97c1-599fbdd4e4d4	019de42b-1b14-717d-8de3-a2df0be3c3a4	019de42b-1688-7290-b257-cd7e06dcb2d4	animator	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b29-7262-abe3-32b8c21a992b	019de42b-1b14-717d-8de3-a2df0be3c3a4	019de42b-15b1-72fa-9387-b143e6dbf3ea	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b2c-7333-82b1-a488ad610bdc	019de42b-1b14-717d-8de3-a2df0be3c3a4	019de42b-1761-70c7-a2de-4906fe0a7166	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b2e-7332-a833-4aecb9b6c72d	019de42b-1b14-717d-8de3-a2df0be3c3a4	019de42b-190e-70c3-9d21-2b6d2b36612c	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b31-7178-b6f3-a34073f60215	019de42b-1b14-717d-8de3-a2df0be3c3a4	019de42b-1839-721b-990c-b44db2a24f10	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b33-709a-846a-7fdbef7e3a46	019de42b-1b17-72f8-9b71-b8ae0cc4730f	019de42b-14cf-7203-ba64-cc6c7ba1cf94	animator	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b35-7364-a794-e536a42f99db	019de42b-1b17-72f8-9b71-b8ae0cc4730f	019de42b-1839-721b-990c-b44db2a24f10	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b38-701a-a042-05c9b5f88844	019de42b-1b17-72f8-9b71-b8ae0cc4730f	019de42b-19e1-7098-9b5c-395edc9a3d85	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b3a-725a-8198-13cdd5f137bf	019de42b-1b17-72f8-9b71-b8ae0cc4730f	019de42b-1ab2-7286-824f-90df7187f28e	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b3d-703e-9419-86bde9113dcc	019de42b-1b19-7381-b0eb-0237e1a655b9	019de42b-1ab2-7286-824f-90df7187f28e	animator	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b3f-723c-affa-7497cdf9bfe1	019de42b-1b19-7381-b0eb-0237e1a655b9	019de42b-14cf-7203-ba64-cc6c7ba1cf94	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b41-72b0-9426-2023ee699681	019de42b-1b19-7381-b0eb-0237e1a655b9	019de42b-1761-70c7-a2de-4906fe0a7166	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b44-70c4-8bba-005bbfbb3cf7	019de42b-1b1c-7112-b34e-ea76d02e771e	019de42b-15b1-72fa-9387-b143e6dbf3ea	animator	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b47-73c7-80ef-048b05c406a9	019de42b-1b1c-7112-b34e-ea76d02e771e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b49-7116-83ea-ee21a323d3b7	019de42b-1b1c-7112-b34e-ea76d02e771e	019de42b-1688-7290-b257-cd7e06dcb2d4	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b4b-7057-92ff-3f16a43f5840	019de42b-1b1c-7112-b34e-ea76d02e771e	019de42b-1839-721b-990c-b44db2a24f10	member	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b4e-7070-a6a8-8290ed79de68	019de42b-1b1c-7112-b34e-ea76d02e771e	019de42b-190e-70c3-9d21-2b6d2b36612c	member	2026-05-01 15:32:03	2026-05-01 15:32:03
\.


--
-- Data for Name: circles; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.circles (id, name, description, type, parent_id, created_at, updated_at, deleted_at) FROM stdin;
019de42b-1b11-705c-a4a1-ffac5b03ab22	Coordination	Cercle de pilotage stratégique – définit la vision et arbitre les priorités transverses.	closed	\N	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1b14-717d-8de3-a2df0be3c3a4	Technique	Infrastructure, développement et architecture logicielle.	open	019de42b-1b11-705c-a4a1-ffac5b03ab22	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1b17-72f8-9b71-b8ae0cc4730f	RH & Culture	Bien-être, recrutement, onboarding et culture d'entreprise.	observer_open	019de42b-1b11-705c-a4a1-ffac5b03ab22	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1b19-7381-b0eb-0237e1a655b9	Finance & Budget	Gestion budgétaire, achats et suivi de la trésorerie.	closed	019de42b-1b11-705c-a4a1-ffac5b03ab22	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1b1c-7112-b34e-ea76d02e771e	Produit	Roadmap produit, UX/UI et priorisation des fonctionnalités.	open	019de42b-1b11-705c-a4a1-ffac5b03ab22	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
\.


--
-- Data for Name: consents; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.consents (id, decision_version_id, user_id, signal, created_at, updated_at, phase) FROM stdin;
019de42b-1b9e-7270-a2f9-135547df82f7	019de42b-1b93-7109-a713-dbcf7b2688a7	019de42b-1839-721b-990c-b44db2a24f10	no_questions	2026-04-28 02:32:03	2026-05-01 15:32:03	clarification
019de42b-1bb5-71f3-8814-ed0ec4411803	019de42b-1bb0-70a4-9435-08c99df74023	019de42b-1ab2-7286-824f-90df7187f28e	no_questions	2026-04-29 02:32:03	2026-05-01 15:32:03	clarification
019de42b-1bb7-708a-ba9e-13d229369afb	019de42b-1bb0-70a4-9435-08c99df74023	019de42b-19e1-7098-9b5c-395edc9a3d85	no_questions	2026-04-29 15:32:03	2026-05-01 15:32:03	clarification
019de42b-1bd1-72fa-859d-d58663f6cd04	019de42b-1bc8-7297-9b75-bbff38d9b9e6	019de42b-15b1-72fa-9387-b143e6dbf3ea	no_reaction	2026-04-21 03:32:03	2026-05-01 15:32:03	reaction
019de42b-1be7-71e9-95e3-9f4cb2017b80	019de42b-1be2-71f4-8133-7283b4861586	019de42b-1761-70c7-a2de-4906fe0a7166	no_reaction	2026-04-29 00:32:03	2026-05-01 15:32:03	reaction
019de42b-1bea-7191-b882-34606b23a0fe	019de42b-1be2-71f4-8133-7283b4861586	019de42b-190e-70c3-9d21-2b6d2b36612c	no_reaction	2026-04-30 22:32:03	2026-05-01 15:32:03	reaction
019de42b-1c05-721b-8638-6d76fdcfa23e	019de42b-1bf9-73da-82c5-a1a0bb833f6a	019de42b-15b1-72fa-9387-b143e6dbf3ea	no_objection	2026-04-22 11:32:03	2026-05-01 15:32:03	objection
019de42b-1c07-70e0-bf1b-d0cc90873395	019de42b-1bf9-73da-82c5-a1a0bb833f6a	019de42b-1761-70c7-a2de-4906fe0a7166	no_objection	2026-04-23 01:32:03	2026-05-01 15:32:03	objection
019de42b-1c0a-73fc-9620-37fcd6ab6861	019de42b-1bf9-73da-82c5-a1a0bb833f6a	019de42b-19e1-7098-9b5c-395edc9a3d85	no_objection	2026-04-24 11:32:03	2026-05-01 15:32:03	objection
019de42b-1c1b-7221-867a-07ffc6359bfe	019de42b-1c18-737d-97db-14ebe8957f05	019de42b-15b1-72fa-9387-b143e6dbf3ea	no_objection	2026-04-28 04:32:03	2026-05-01 15:32:03	objection
019de42b-1c1d-71e6-a5c7-7a5a7e93b3ee	019de42b-1c18-737d-97db-14ebe8957f05	019de42b-1688-7290-b257-cd7e06dcb2d4	no_objection	2026-04-28 04:32:03	2026-05-01 15:32:03	objection
019de42b-1c20-7169-ab0b-fc3bf3b0a3b9	019de42b-1c18-737d-97db-14ebe8957f05	019de42b-1ab2-7286-824f-90df7187f28e	no_objection	2026-04-26 06:32:03	2026-05-01 15:32:03	objection
019de42b-1c3d-73d0-9b1b-6d9093422031	019de42b-1c34-70ef-a26e-532d562bcc48	019de42b-1ab2-7286-824f-90df7187f28e	no_objection	2026-04-29 12:32:03	2026-05-01 15:32:03	objection
019de42b-1c54-714b-b77e-d1701d983454	019de42b-1c52-72cd-b43b-2ce45083ea6c	019de42b-1688-7290-b257-cd7e06dcb2d4	no_objection	2026-04-17 06:32:03	2026-05-01 15:32:03	objection
019de42b-1c57-7350-a362-56ef7742705d	019de42b-1c52-72cd-b43b-2ce45083ea6c	019de42b-1761-70c7-a2de-4906fe0a7166	no_objection	2026-04-16 17:32:03	2026-05-01 15:32:03	objection
019de42b-1c59-7325-84af-51f158fbe65f	019de42b-1c52-72cd-b43b-2ce45083ea6c	019de42b-190e-70c3-9d21-2b6d2b36612c	no_objection	2026-04-18 10:32:03	2026-05-01 15:32:03	objection
019de42b-1c5b-71fd-9f23-d0a5f0da77c7	019de42b-1c52-72cd-b43b-2ce45083ea6c	019de42b-1839-721b-990c-b44db2a24f10	no_objection	2026-04-17 08:32:03	2026-05-01 15:32:03	objection
019de42b-1c5e-7042-9f5d-c08900b27933	019de42b-1c52-72cd-b43b-2ce45083ea6c	019de42b-15b1-72fa-9387-b143e6dbf3ea	no_objection	2026-04-17 13:32:03	2026-05-01 15:32:03	objection
019de42b-1c79-73c6-b3e3-ae5e1792e93e	019de42b-1c76-73d4-9a8e-35c6ab840593	019de42b-1ab2-7286-824f-90df7187f28e	no_objection	2026-04-03 08:32:03	2026-05-01 15:32:03	objection
019de42b-1c7b-7138-aee4-ac4c5634f23c	019de42b-1c76-73d4-9a8e-35c6ab840593	019de42b-1761-70c7-a2de-4906fe0a7166	no_objection	2026-04-04 20:32:03	2026-05-01 15:32:03	objection
019de42b-1d11-7054-87ad-9207dc829ea3	019de42b-1d0c-707d-8cf2-ce0f0b302bf5	019de42b-1688-7290-b257-cd7e06dcb2d4	no_reaction	2026-04-22 20:32:03	2026-05-01 15:32:03	reaction
019de42b-1d30-72c8-9f8a-345d87ef3391	019de42b-1d29-73f7-b72e-5a76da41bfbf	019de42b-1761-70c7-a2de-4906fe0a7166	no_objection	2026-05-01 08:32:03	2026-05-01 15:32:04	objection
019de42b-1d32-7381-93e5-f24b52ab4cc3	019de42b-1d29-73f7-b72e-5a76da41bfbf	019de42b-1ab2-7286-824f-90df7187f28e	abstention	2026-05-01 08:32:03	2026-05-01 15:32:04	objection
019de42b-1d48-71fb-8bb6-003e7c18e71d	019de42b-1d45-7127-9054-341339655f9c	019de42b-15b1-72fa-9387-b143e6dbf3ea	no_objection	2026-01-04 16:32:04	2026-05-01 15:32:04	objection
019de42b-1d4a-70a6-97db-c33960bec996	019de42b-1d45-7127-9054-341339655f9c	019de42b-1688-7290-b257-cd7e06dcb2d4	no_objection	2026-01-04 20:32:04	2026-05-01 15:32:04	objection
019de42b-1d4d-725f-9ee8-d05430f367fe	019de42b-1d45-7127-9054-341339655f9c	019de42b-1ab2-7286-824f-90df7187f28e	no_objection	2026-01-05 17:32:04	2026-05-01 15:32:04	objection
019de7ca-fab1-727a-8901-3655bce57fed	019de7ca-d573-7027-a704-ec0aa178752e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	no_objection	2026-05-02 08:25:32	2026-05-02 08:25:32	objection
019de7cb-ce1f-7197-9d66-d466398d2336	019de7ca-d573-7027-a704-ec0aa178752e	019de42b-1761-70c7-a2de-4906fe0a7166	abstention	2026-05-02 08:26:26	2026-05-02 08:26:26	objection
019de812-9ecc-71b4-bb8d-e383bbe68c3e	019de42b-1cdd-72f9-a1ec-fe038fc63086	019de42b-1761-70c7-a2de-4906fe0a7166	abstention	2026-05-02 09:43:47	2026-05-02 09:43:47	clarification
019de812-9ed0-7033-af4f-1f53f06ac5c3	019de42b-1cdd-72f9-a1ec-fe038fc63086	019de42b-1839-721b-990c-b44db2a24f10	abstention	2026-05-02 09:43:47	2026-05-02 09:43:47	clarification
019de812-9ed4-7108-89f6-fc4076f709b4	019de42b-1cdd-72f9-a1ec-fe038fc63086	019de42b-190e-70c3-9d21-2b6d2b36612c	abstention	2026-05-02 09:43:47	2026-05-02 09:43:47	clarification
019dee7c-5b8f-735d-968f-65e8f46f9ced	019de42b-1be2-71f4-8133-7283b4861586	019de42b-15b1-72fa-9387-b143e6dbf3ea	abstention	2026-05-03 15:37:00	2026-05-03 15:37:00	reaction
019dee7c-5b93-7260-9fc8-5363d3dcf0ca	019de42b-1be2-71f4-8133-7283b4861586	019de42b-1839-721b-990c-b44db2a24f10	abstention	2026-05-03 15:37:00	2026-05-03 15:37:00	reaction
019dee7f-201e-72c1-9a75-9c197fe0226c	019dee7d-b480-729d-a37a-36f4ec473474	019de42b-1688-7290-b257-cd7e06dcb2d4	abstention	2026-05-03 15:40:01	2026-05-03 15:40:01	clarification
019dee7f-2021-7341-b8f2-c498ae4656df	019dee7d-b480-729d-a37a-36f4ec473474	019de42b-15b1-72fa-9387-b143e6dbf3ea	abstention	2026-05-03 15:40:01	2026-05-03 15:40:01	clarification
019dee7f-2024-706b-8ef4-5a598347c9c2	019dee7d-b480-729d-a37a-36f4ec473474	019de42b-1761-70c7-a2de-4906fe0a7166	abstention	2026-05-03 15:40:01	2026-05-03 15:40:01	clarification
019dee7f-2026-7347-9923-c51e61a993d4	019dee7d-b480-729d-a37a-36f4ec473474	019de42b-1839-721b-990c-b44db2a24f10	abstention	2026-05-03 15:40:01	2026-05-03 15:40:01	clarification
019dee9c-392e-735b-b98d-9e8f694a1ee8	019dee7f-4f61-71c4-abd3-6b555353be6e	019de42b-1688-7290-b257-cd7e06dcb2d4	abstention	2026-05-03 16:11:48	2026-05-03 16:11:48	clarification
019dee9c-3936-7173-8f58-74dcfa25c934	019dee7f-4f61-71c4-abd3-6b555353be6e	019de42b-15b1-72fa-9387-b143e6dbf3ea	abstention	2026-05-03 16:11:48	2026-05-03 16:11:48	clarification
019dee9c-393b-71c2-b597-f4fff6003afa	019dee7f-4f61-71c4-abd3-6b555353be6e	019de42b-1761-70c7-a2de-4906fe0a7166	abstention	2026-05-03 16:11:48	2026-05-03 16:11:48	clarification
019dee9c-3940-7018-98e8-a41a74da64bf	019dee7f-4f61-71c4-abd3-6b555353be6e	019de42b-1839-721b-990c-b44db2a24f10	abstention	2026-05-03 16:11:48	2026-05-03 16:11:48	clarification
019deea7-db8d-725f-bf73-2e7c7c100e68	019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-15b1-72fa-9387-b143e6dbf3ea	abstention	2026-05-03 16:24:31	2026-05-03 16:24:31	reaction
019deea7-db92-73f4-8215-b60c06f1369d	019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-1839-721b-990c-b44db2a24f10	abstention	2026-05-03 16:24:31	2026-05-03 16:24:31	reaction
019deea7-db95-7381-9abf-3c58b6483f2f	019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-190e-70c3-9d21-2b6d2b36612c	abstention	2026-05-03 16:24:31	2026-05-03 16:24:31	reaction
019deea8-4732-70fb-9d3e-0b49e108975f	019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-15b1-72fa-9387-b143e6dbf3ea	abstention	2026-05-03 16:24:58	2026-05-03 16:24:58	clarification
019deea8-4734-7237-b798-59153ac05e96	019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-1839-721b-990c-b44db2a24f10	abstention	2026-05-03 16:24:58	2026-05-03 16:24:58	clarification
019deea8-4737-721e-8eb2-960e9ebc303c	019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-190e-70c3-9d21-2b6d2b36612c	abstention	2026-05-03 16:24:58	2026-05-03 16:24:58	clarification
019deeed-dfc1-73e6-a427-d9700adcf02b	019deeed-c017-70b8-ae26-d80a70af6866	019de42b-1688-7290-b257-cd7e06dcb2d4	abstention	2026-05-03 17:40:59	2026-05-03 17:40:59	clarification
019deeed-dfc4-732f-b20e-b3b22587d65f	019deeed-c017-70b8-ae26-d80a70af6866	019de42b-1761-70c7-a2de-4906fe0a7166	abstention	2026-05-03 17:40:59	2026-05-03 17:40:59	clarification
019deeed-dfc7-735f-b20e-8a1276d27b43	019deeed-c017-70b8-ae26-d80a70af6866	019de42b-190e-70c3-9d21-2b6d2b36612c	abstention	2026-05-03 17:40:59	2026-05-03 17:40:59	clarification
019deeed-dfca-7121-812b-8530f1154464	019deeed-c017-70b8-ae26-d80a70af6866	019de42b-1839-721b-990c-b44db2a24f10	abstention	2026-05-03 17:40:59	2026-05-03 17:40:59	clarification
019defbb-f382-732d-8a47-44e9b5fff85e	019de42b-1d29-73f7-b72e-5a76da41bfbf	019de42b-14cf-7203-ba64-cc6c7ba1cf94	abstention	2026-05-03 21:26:05	2026-05-03 21:26:05	objection
019defe1-4875-70cc-a5bd-c510771c890e	019defe0-1ad7-70a9-b097-b727b17ce6e8	019de42b-1839-721b-990c-b44db2a24f10	no_objection	2026-05-03 22:06:52	2026-05-03 22:06:52	objection
019defe1-7430-73a4-9406-5696263c9a9c	019defe0-1ad7-70a9-b097-b727b17ce6e8	019de42b-19e1-7098-9b5c-395edc9a3d85	abstention	2026-05-03 22:07:03	2026-05-03 22:07:03	objection
019defe1-b643-73d6-bf6c-cdd2b6219fc1	019defe1-a240-72a5-9660-08244947628d	019de42b-1839-721b-990c-b44db2a24f10	no_objection	2026-05-03 22:07:20	2026-05-03 22:07:20	objection
019defe1-bbc9-7191-a641-f80632d60a76	019defe1-a240-72a5-9660-08244947628d	019de42b-19e1-7098-9b5c-395edc9a3d85	no_objection	2026-05-03 22:07:21	2026-05-03 22:07:21	objection
019defe5-a97b-72b7-ba8f-213db6739f35	019defd0-7bd0-72d8-8537-6603c636980e	019de42b-1688-7290-b257-cd7e06dcb2d4	abstention	2026-05-03 22:11:39	2026-05-03 22:11:39	clarification
019defe5-a97f-708f-965d-31dce1475533	019defd0-7bd0-72d8-8537-6603c636980e	019de42b-1761-70c7-a2de-4906fe0a7166	abstention	2026-05-03 22:11:39	2026-05-03 22:11:39	clarification
019defe5-a981-7089-b3d3-35cb19c7319a	019defd0-7bd0-72d8-8537-6603c636980e	019de42b-190e-70c3-9d21-2b6d2b36612c	abstention	2026-05-03 22:11:39	2026-05-03 22:11:39	clarification
019defe5-a984-707b-9365-f591ae935241	019defd0-7bd0-72d8-8537-6603c636980e	019de42b-1839-721b-990c-b44db2a24f10	abstention	2026-05-03 22:11:39	2026-05-03 22:11:39	clarification
\.


--
-- Data for Name: decision_animator_logs; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.decision_animator_logs (id, decision_id, animator_id, assigned_by, assigned_at, removed_at) FROM stdin;
019de42b-1b6a-706e-834a-ce88691c10b6	019de42b-1b5a-7324-b7d1-b9b424335048	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-04-23 01:32:03	\N
019de42b-1b7a-7009-8529-d2f9a4ecded6	019de42b-1b6f-70d4-aeae-944039975ef4	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-15b1-72fa-9387-b143e6dbf3ea	2026-04-29 15:32:03	\N
019de42b-1b91-71f7-a818-b5713b5e0da2	019de42b-1b84-725d-9e03-1e14ca1630f4	019de42b-1ab2-7286-824f-90df7187f28e	019de42b-1688-7290-b257-cd7e06dcb2d4	2026-04-26 07:32:03	\N
019de42b-1bae-72f0-a6cd-6616dffb84c0	019de42b-1ba3-7152-9b76-d7baadc5cebe	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1761-70c7-a2de-4906fe0a7166	2026-04-27 01:32:03	\N
019de42b-1bc6-70e2-a24f-fe883547d268	019de42b-1bb9-734d-a3aa-efcf55a9663b	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-1839-721b-990c-b44db2a24f10	2026-04-20 21:32:03	\N
019de42b-1be0-736d-9354-d01263fd8150	019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-1ab2-7286-824f-90df7187f28e	019de42b-190e-70c3-9d21-2b6d2b36612c	2026-04-28 05:32:03	\N
019de42b-1bf7-73df-8a38-edba8eaa2d18	019de42b-1bee-7325-a8ed-067285f10b54	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1ab2-7286-824f-90df7187f28e	2026-04-21 11:32:03	\N
019de42b-1c16-7178-b6bd-2d8350b505a0	019de42b-1c0c-72b3-aa14-8cea6be187b7	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-04-26 03:32:03	\N
019de42b-1c32-712e-b232-4242f87694ba	019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-1ab2-7286-824f-90df7187f28e	019de42b-15b1-72fa-9387-b143e6dbf3ea	2026-04-26 23:32:03	\N
019de42b-1c4f-71ea-9652-fec8c3ebd95c	019de42b-1c42-70b4-917e-c62aa3f5649e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1688-7290-b257-cd7e06dcb2d4	2026-04-16 15:32:03	\N
019de42b-1c6d-7230-933c-8c0a668803f7	019de42b-1c60-7174-b9f7-8c1cf171c88f	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-1761-70c7-a2de-4906fe0a7166	2026-04-01 15:32:03	\N
019de42b-1c88-721c-a32b-22ee98d96b37	019de42b-1c7f-7029-b504-cdd9f0624d4c	019de42b-1ab2-7286-824f-90df7187f28e	019de42b-1839-721b-990c-b44db2a24f10	2026-03-02 15:32:03	\N
019de42b-1c99-71f4-9096-cd72046a96d0	019de42b-1c8c-734c-b9a8-0f4ad8661ae0	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-190e-70c3-9d21-2b6d2b36612c	2026-03-17 15:32:03	\N
019de42b-1cb3-7249-a5d7-d060f0c311c3	019de42b-1ca4-7379-a626-a48f413fb9ed	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-1ab2-7286-824f-90df7187f28e	2026-04-11 15:32:03	\N
019de42b-1cca-708b-be1e-44bac5e08d2a	019de42b-1cc1-70cf-ae0e-14be5444ad46	019de42b-1ab2-7286-824f-90df7187f28e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-01-31 15:32:03	\N
019de42b-1cdb-73db-822d-bb99e5e1fe72	019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-15b1-72fa-9387-b143e6dbf3ea	2026-04-30 09:32:03	\N
019de42b-1d08-72b8-874d-f4333c23e04f	019de42b-1cee-71fb-8630-9e168aa2ef61	019de42b-1ab2-7286-824f-90df7187f28e	019de42b-1688-7290-b257-cd7e06dcb2d4	2026-04-20 23:32:03	\N
019de42b-1d27-7056-8275-342f1742b6ad	019de42b-1d1a-7065-a10a-37d15a5a650d	019de42b-1ab2-7286-824f-90df7187f28e	019de42b-1761-70c7-a2de-4906fe0a7166	2026-04-29 15:32:03	\N
019de42b-1d3f-7187-b7c7-8c4ca76c7659	019de42b-1d34-7393-9cae-17d56e46534c	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1839-721b-990c-b44db2a24f10	2026-01-01 15:32:04	\N
019de42b-1d57-7144-9774-ec1efb0404ec	019de42b-1d4f-719e-91d2-12c47feb2033	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-190e-70c3-9d21-2b6d2b36612c	2026-02-15 15:32:04	\N
019de42b-1d6a-7269-8a6b-94fd0a20eec3	019de42b-1d5b-73a6-a0b4-cfe18bd52827	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1ab2-7286-824f-90df7187f28e	2026-04-21 11:32:04	\N
019de42b-1d7f-71b6-8492-8a40d3bf79f2	019de42b-1d70-72ca-892d-ef83b7d3658e	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-04-23 12:32:04	\N
\.


--
-- Data for Name: decision_categories; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.decision_categories (decision_id, category_id) FROM stdin;
019de42b-1b5a-7324-b7d1-b9b424335048	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1b6f-70d4-aeae-944039975ef4	019de42b-1ae7-7239-84d8-eebd671fb8aa
019de42b-1b84-725d-9e03-1e14ca1630f4	019de42b-1aee-70be-8798-b90c40d961cc
019de42b-1ba3-7152-9b76-d7baadc5cebe	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1bb9-734d-a3aa-efcf55a9663b	019de42b-1aee-70be-8798-b90c40d961cc
019de42b-1bb9-734d-a3aa-efcf55a9663b	019de42b-1ae7-7239-84d8-eebd671fb8aa
019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-1aee-70be-8798-b90c40d961cc
019de42b-1bee-7325-a8ed-067285f10b54	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1c0c-72b3-aa14-8cea6be187b7	019de42b-1ae7-7239-84d8-eebd671fb8aa
019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-1aec-73e2-81ce-ce77521b3f2a
019de42b-1c42-70b4-917e-c62aa3f5649e	019de42b-1aee-70be-8798-b90c40d961cc
019de42b-1c60-7174-b9f7-8c1cf171c88f	019de42b-1aec-73e2-81ce-ce77521b3f2a
019de42b-1c60-7174-b9f7-8c1cf171c88f	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1c7f-7029-b504-cdd9f0624d4c	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1c8c-734c-b9a8-0f4ad8661ae0	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1c8c-734c-b9a8-0f4ad8661ae0	019de42b-1ae7-7239-84d8-eebd671fb8aa
019de42b-1ca4-7379-a626-a48f413fb9ed	019de42b-1ae7-7239-84d8-eebd671fb8aa
019de42b-1ca4-7379-a626-a48f413fb9ed	019de42b-1aec-73e2-81ce-ce77521b3f2a
019de42b-1cc1-70cf-ae0e-14be5444ad46	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-1aee-70be-8798-b90c40d961cc
019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-1aec-73e2-81ce-ce77521b3f2a
019de42b-1cee-71fb-8630-9e168aa2ef61	019de42b-1aee-70be-8798-b90c40d961cc
019de42b-1cee-71fb-8630-9e168aa2ef61	019de42b-1ae7-7239-84d8-eebd671fb8aa
019de42b-1d1a-7065-a10a-37d15a5a650d	019de42b-1aec-73e2-81ce-ce77521b3f2a
019de42b-1d1a-7065-a10a-37d15a5a650d	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1d34-7393-9cae-17d56e46534c	019de42b-1ae7-7239-84d8-eebd671fb8aa
019de42b-1d4f-719e-91d2-12c47feb2033	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1d5b-73a6-a0b4-cfe18bd52827	019de42b-1ae7-7239-84d8-eebd671fb8aa
019de42b-1d5b-73a6-a0b4-cfe18bd52827	019de42b-1ae9-71ef-ad60-8dc3b83c3082
019de42b-1d70-72ca-892d-ef83b7d3658e	019de42b-1ae7-7239-84d8-eebd671fb8aa
019de42b-1d70-72ca-892d-ef83b7d3658e	019de42b-1aec-73e2-81ce-ce77521b3f2a
\.


--
-- Data for Name: decision_labels; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.decision_labels (decision_id, label_id) FROM stdin;
019de42b-1b5a-7324-b7d1-b9b424335048	019de42b-1afc-71bb-8cbb-3e0eab5bdd8c
019de42b-1b6f-70d4-aeae-944039975ef4	019de42b-1afe-70a6-8c99-c36c40eccebf
019de42b-1b84-725d-9e03-1e14ca1630f4	019de42b-1b03-71f1-838a-4266002006c5
019de42b-1b84-725d-9e03-1e14ca1630f4	019de42b-1afc-71bb-8cbb-3e0eab5bdd8c
019de42b-1ba3-7152-9b76-d7baadc5cebe	019de42b-1b01-7087-a272-d0b4690d316c
019de42b-1bb9-734d-a3aa-efcf55a9663b	019de42b-1afa-73e4-8f41-96ac781bc4d2
019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-1af7-7340-8feb-1c10a4cf1069
019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-1b03-71f1-838a-4266002006c5
019de42b-1c0c-72b3-aa14-8cea6be187b7	019de42b-1afc-71bb-8cbb-3e0eab5bdd8c
019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-1b01-7087-a272-d0b4690d316c
019de42b-1c42-70b4-917e-c62aa3f5649e	019de42b-1afa-73e4-8f41-96ac781bc4d2
019de42b-1c42-70b4-917e-c62aa3f5649e	019de42b-1b01-7087-a272-d0b4690d316c
019de42b-1c60-7174-b9f7-8c1cf171c88f	019de42b-1afc-71bb-8cbb-3e0eab5bdd8c
019de42b-1c8c-734c-b9a8-0f4ad8661ae0	019de42b-1afe-70a6-8c99-c36c40eccebf
019de42b-1ca4-7379-a626-a48f413fb9ed	019de42b-1afc-71bb-8cbb-3e0eab5bdd8c
019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-1af7-7340-8feb-1c10a4cf1069
019de42b-1cee-71fb-8630-9e168aa2ef61	019de42b-1afe-70a6-8c99-c36c40eccebf
019de42b-1d1a-7065-a10a-37d15a5a650d	019de42b-1af7-7340-8feb-1c10a4cf1069
019de42b-1d34-7393-9cae-17d56e46534c	019de42b-1b01-7087-a272-d0b4690d316c
019de42b-1d5b-73a6-a0b4-cfe18bd52827	019de42b-1afc-71bb-8cbb-3e0eab5bdd8c
019de42b-1d5b-73a6-a0b4-cfe18bd52827	019de42b-1afe-70a6-8c99-c36c40eccebf
019de42b-1d70-72ca-892d-ef83b7d3658e	019de42b-1afa-73e4-8f41-96ac781bc4d2
\.


--
-- Data for Name: decision_models; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.decision_models (id, name, description, template_content, requires_distinct_animator, default_objection_days, is_active, created_at, updated_at, deleted_at) FROM stdin;
019de42b-1b07-7240-8b59-6e43e5016a29	Consentement (Standard)	Décision par absence d'objection majeure.	# Proposition\n\n## Contexte\n...\n\n## Détails\n...	t	5	t	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
019de42b-1b0a-7383-b68a-11627b689095	Avis Sollicité	Le porteur prend la décision après avoir écouté les avis.	# Consultation\n\n## Question\n...\n\n## Public cible\n...	f	3	t	2026-05-01 15:32:03	2026-05-01 15:32:03	\N
\.


--
-- Data for Name: decision_participants; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.decision_participants (id, decision_id, user_id, role, added_at) FROM stdin;
019de42b-1b65-70db-a334-525e1c8f4847	019de42b-1b5a-7324-b7d1-b9b424335048	019de42b-14cf-7203-ba64-cc6c7ba1cf94	author	2026-04-23 01:32:03
019de42b-1b67-72a8-bf5c-71686c939e38	019de42b-1b5a-7324-b7d1-b9b424335048	019de42b-1688-7290-b257-cd7e06dcb2d4	animator	2026-04-23 01:32:03
019de42b-1b76-70af-b9c8-4cd90147410e	019de42b-1b6f-70d4-aeae-944039975ef4	019de42b-15b1-72fa-9387-b143e6dbf3ea	author	2026-04-29 15:32:03
019de42b-1b78-7325-8cdf-4fc89cbb106e	019de42b-1b6f-70d4-aeae-944039975ef4	019de42b-1688-7290-b257-cd7e06dcb2d4	animator	2026-04-29 15:32:03
019de42b-1b8d-7135-a26f-bdabdaa9d23b	019de42b-1b84-725d-9e03-1e14ca1630f4	019de42b-1688-7290-b257-cd7e06dcb2d4	author	2026-04-26 07:32:03
019de42b-1b8f-72f9-8880-5532bf979c38	019de42b-1b84-725d-9e03-1e14ca1630f4	019de42b-1ab2-7286-824f-90df7187f28e	animator	2026-04-26 07:32:03
019de42b-1baa-73cc-9a14-433e72170225	019de42b-1ba3-7152-9b76-d7baadc5cebe	019de42b-1761-70c7-a2de-4906fe0a7166	author	2026-04-27 01:32:03
019de42b-1bac-7167-bf04-918e5824507e	019de42b-1ba3-7152-9b76-d7baadc5cebe	019de42b-14cf-7203-ba64-cc6c7ba1cf94	animator	2026-04-27 01:32:03
019de42b-1bc2-7167-b3c4-084b9c2557ec	019de42b-1bb9-734d-a3aa-efcf55a9663b	019de42b-1839-721b-990c-b44db2a24f10	author	2026-04-20 21:32:03
019de42b-1bc4-7110-aa18-b06d490da7d5	019de42b-1bb9-734d-a3aa-efcf55a9663b	019de42b-1688-7290-b257-cd7e06dcb2d4	animator	2026-04-20 21:32:03
019de42b-1bdc-70be-8e6a-d69aca42ee55	019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-190e-70c3-9d21-2b6d2b36612c	author	2026-04-28 05:32:03
019de42b-1bde-705f-82ec-2b779b48a47d	019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-1ab2-7286-824f-90df7187f28e	animator	2026-04-28 05:32:03
019de42b-1bf3-7049-ac0d-67073ceb1568	019de42b-1bee-7325-a8ed-067285f10b54	019de42b-1ab2-7286-824f-90df7187f28e	author	2026-04-21 11:32:03
019de42b-1bf5-70b0-9be8-5da4aaf0d41c	019de42b-1bee-7325-a8ed-067285f10b54	019de42b-14cf-7203-ba64-cc6c7ba1cf94	animator	2026-04-21 11:32:03
019de42b-1c12-72e3-9c77-4027f2dceef8	019de42b-1c0c-72b3-aa14-8cea6be187b7	019de42b-14cf-7203-ba64-cc6c7ba1cf94	author	2026-04-26 03:32:03
019de42b-1c14-7335-9cc5-407767eff1a5	019de42b-1c0c-72b3-aa14-8cea6be187b7	019de42b-1688-7290-b257-cd7e06dcb2d4	animator	2026-04-26 03:32:03
019de42b-1c2d-71e1-809e-f46baa69ac04	019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-15b1-72fa-9387-b143e6dbf3ea	author	2026-04-26 23:32:03
019de42b-1c4b-73b3-8e7c-59ecc8eb5e9c	019de42b-1c42-70b4-917e-c62aa3f5649e	019de42b-1688-7290-b257-cd7e06dcb2d4	author	2026-04-16 15:32:03
019de42b-1c4d-70b7-a231-3ffdc7c27f41	019de42b-1c42-70b4-917e-c62aa3f5649e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	animator	2026-04-16 15:32:03
019de42b-1c69-70e9-9c3b-9e513f268b96	019de42b-1c60-7174-b9f7-8c1cf171c88f	019de42b-1761-70c7-a2de-4906fe0a7166	author	2026-04-01 15:32:03
019de42b-1c6b-72d3-9baf-846b77638d85	019de42b-1c60-7174-b9f7-8c1cf171c88f	019de42b-1688-7290-b257-cd7e06dcb2d4	animator	2026-04-01 15:32:03
019de42b-1c84-72f5-b79d-965f417ebf0d	019de42b-1c7f-7029-b504-cdd9f0624d4c	019de42b-1839-721b-990c-b44db2a24f10	author	2026-03-02 15:32:03
019de42b-1c86-709e-a998-c26d08c17e2a	019de42b-1c7f-7029-b504-cdd9f0624d4c	019de42b-1ab2-7286-824f-90df7187f28e	animator	2026-03-02 15:32:03
019de42b-1c95-72f0-bea6-32fb12082ee8	019de42b-1c8c-734c-b9a8-0f4ad8661ae0	019de42b-190e-70c3-9d21-2b6d2b36612c	author	2026-03-17 15:32:03
019de42b-1c97-70b1-a2f4-063de1d59934	019de42b-1c8c-734c-b9a8-0f4ad8661ae0	019de42b-14cf-7203-ba64-cc6c7ba1cf94	animator	2026-03-17 15:32:03
019de42b-1caf-7023-82b4-dc63bad1802c	019de42b-1ca4-7379-a626-a48f413fb9ed	019de42b-1ab2-7286-824f-90df7187f28e	author	2026-04-11 15:32:03
019de42b-1cb1-709e-bc34-b91a66d4778d	019de42b-1ca4-7379-a626-a48f413fb9ed	019de42b-1688-7290-b257-cd7e06dcb2d4	animator	2026-04-11 15:32:03
019de42b-1cc5-72ec-99b4-71a28492b15f	019de42b-1cc1-70cf-ae0e-14be5444ad46	019de42b-14cf-7203-ba64-cc6c7ba1cf94	author	2026-01-31 15:32:03
019de42b-1cc7-73bd-924a-63c4025e6462	019de42b-1cc1-70cf-ae0e-14be5444ad46	019de42b-1ab2-7286-824f-90df7187f28e	animator	2026-01-31 15:32:03
019de42b-1cd7-7134-b79a-43d8088f2322	019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-15b1-72fa-9387-b143e6dbf3ea	author	2026-04-30 09:32:03
019de42b-1d00-7103-9889-6e46da14d58b	019de42b-1cee-71fb-8630-9e168aa2ef61	019de42b-1688-7290-b257-cd7e06dcb2d4	author	2026-04-20 23:32:03
019de42b-1d04-721c-afa2-2d6c8cbc1cd5	019de42b-1cee-71fb-8630-9e168aa2ef61	019de42b-1ab2-7286-824f-90df7187f28e	animator	2026-04-20 23:32:03
019de42b-1d23-71da-9169-4fcfee6f0e75	019de42b-1d1a-7065-a10a-37d15a5a650d	019de42b-1761-70c7-a2de-4906fe0a7166	author	2026-04-29 15:32:03
019de42b-1d3b-712d-9a17-dbb3bb7cbb0a	019de42b-1d34-7393-9cae-17d56e46534c	019de42b-1839-721b-990c-b44db2a24f10	author	2026-01-01 15:32:04
019de42b-1d3d-7002-8fdf-0b5699cde68a	019de42b-1d34-7393-9cae-17d56e46534c	019de42b-14cf-7203-ba64-cc6c7ba1cf94	animator	2026-01-01 15:32:04
019de42b-1d53-73f6-aafb-26133797dc7c	019de42b-1d4f-719e-91d2-12c47feb2033	019de42b-190e-70c3-9d21-2b6d2b36612c	author	2026-02-15 15:32:04
019de42b-1d55-7215-ac97-f23327496b3f	019de42b-1d4f-719e-91d2-12c47feb2033	019de42b-1688-7290-b257-cd7e06dcb2d4	animator	2026-02-15 15:32:04
019de42b-1d66-70c3-9fd0-437cadad760a	019de42b-1d5b-73a6-a0b4-cfe18bd52827	019de42b-1ab2-7286-824f-90df7187f28e	author	2026-04-21 11:32:04
019de42b-1d68-731e-b3e1-39eded91e84d	019de42b-1d5b-73a6-a0b4-cfe18bd52827	019de42b-14cf-7203-ba64-cc6c7ba1cf94	animator	2026-04-21 11:32:04
019de42b-1d7b-710f-bc3c-c54bf9bdd995	019de42b-1d70-72ca-892d-ef83b7d3658e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	author	2026-04-23 12:32:04
019de42b-1d7d-72a5-b4b2-bbd381a60017	019de42b-1d70-72ca-892d-ef83b7d3658e	019de42b-1688-7290-b257-cd7e06dcb2d4	animator	2026-04-23 12:32:04
019de7ca-d53e-7091-883e-9c5a7bfda5b5	019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-1ab2-7286-824f-90df7187f28e	animator	2026-05-02 08:25:23
019de7ca-fab3-70ba-b004-1a1e5f72ebbf	019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-14cf-7203-ba64-cc6c7ba1cf94	participant	2026-05-02 08:25:33
019deeed-bfeb-71dd-a0f0-95a4df7fc6d8	019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-14cf-7203-ba64-cc6c7ba1cf94	animator	2026-05-03 17:40:52
019defbc-37ab-7321-8e78-151bf4cf335c	019de42b-1d1a-7065-a10a-37d15a5a650d	019de42b-1ab2-7286-824f-90df7187f28e	animator	2026-05-03 21:26:23
019defe1-4878-7091-bdea-4aa9bb1f30fd	019de42b-1bee-7325-a8ed-067285f10b54	019de42b-1839-721b-990c-b44db2a24f10	participant	2026-05-03 22:06:52
019defe1-bbcc-7068-9f36-8025a5cc449b	019de42b-1bee-7325-a8ed-067285f10b54	019de42b-19e1-7098-9b5c-395edc9a3d85	participant	2026-05-03 22:07:22
\.


--
-- Data for Name: decision_relations; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.decision_relations (id, source_decision_id, target_decision_id, relation_type, created_at, updated_at) FROM stdin;
019de42b-1d8e-71b4-ba1a-d9b05b0d10d8	019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-1c60-7174-b9f7-8c1cf171c88f	derives_from	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1d91-70ff-8f95-dcd95d5a9a9d	019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-1b84-725d-9e03-1e14ca1630f4	blocks	2026-05-01 15:32:04	2026-05-01 15:32:04
\.


--
-- Data for Name: decision_user_settings; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.decision_user_settings (id, user_id, decision_id, is_favorite, notification_level, created_at, updated_at) FROM stdin;
019de42b-1d94-720d-bb13-091fcd18153a	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1c0c-72b3-aa14-8cea6be187b7	t	all	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1d97-737f-87e8-ba4f585fbf97	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1ca4-7379-a626-a48f413fb9ed	t	phase_change	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1d99-73e2-931c-bdb3c3680eb3	019de42b-15b1-72fa-9387-b143e6dbf3ea	019de42b-1c42-70b4-917e-c62aa3f5649e	t	relevant	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1d9c-71d1-9053-1ad8e1047caf	019de42b-15b1-72fa-9387-b143e6dbf3ea	019de42b-1bb9-734d-a3aa-efcf55a9663b	f	none	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1d9e-71b3-90ce-1a970b9ec151	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-1b84-725d-9e03-1e14ca1630f4	t	all	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1da1-71ba-b281-1a71029cd61c	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-1bd4-71c6-930f-b0a84cfaf0b4	t	all	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1da3-7134-84d5-c4b7ae42ef7e	019de42b-1688-7290-b257-cd7e06dcb2d4	019de42b-1cee-71fb-8630-9e168aa2ef61	t	all	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1da6-70b2-999a-a8e360f60928	019de42b-1761-70c7-a2de-4906fe0a7166	019de42b-1bee-7325-a8ed-067285f10b54	f	none	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1da8-71ca-bb50-4ab6e3de0363	019de42b-1761-70c7-a2de-4906fe0a7166	019de42b-1d1a-7065-a10a-37d15a5a650d	t	phase_change	2026-05-01 15:32:04	2026-05-01 15:32:04
\.


--
-- Data for Name: decision_versions; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.decision_versions (id, decision_id, author_id, previous_version_id, version_number, is_current, content, change_reason, created_at, updated_at) FROM stdin;
019de42b-1b6d-72a5-9767-f2bf577e9b01	019de42b-1b5a-7324-b7d1-b9b424335048	019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	1	t	# Proposition\n\n## Contexte\nSuite aux retours de l'enquête satisfaction, 78% des collaborateurs souhaitent plus de flexibilité.\n\n## Proposition\nPermettre jusqu'à 3 jours de télétravail par semaine pour tous les postes éligibles.\n\n## Impact attendu\n- Meilleure qualité de vie\n- Réduction des coûts immobiliers\n- Risque d'isolement à monitorer	\N	2026-04-23 01:32:03	2026-04-23 01:32:03
019de42b-1b7d-7275-b27a-b1b0d31463ad	019de42b-1b6f-70d4-aeae-944039975ef4	019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	1	t	# Consultation\n\n## Question\nQuel logo préférez-vous parmi les 3 propositions du designer ?\n\n## Contexte\nNotre identité visuelle date de 2018 et ne reflète plus nos valeurs actuelles.	\N	2026-04-29 15:32:03	2026-04-29 15:32:03
019de42b-1b93-7109-a713-dbcf7b2688a7	019de42b-1b84-725d-9e03-1e14ca1630f4	019de42b-1688-7290-b257-cd7e06dcb2d4	\N	1	t	# Proposition\n\n## Contexte\nMySQL montre ses limites sur les requêtes JSON et le volume de données.\n\n## Détails\nMigrer l'ensemble de la stack vers PostgreSQL 16 avec réplication.\n\n## Plan\n1. Audit des requêtes existantes\n2. Migration schéma\n3. Tests de charge\n4. Bascule progressive	\N	2026-04-26 07:32:03	2026-04-26 07:32:03
019de42b-1bb0-70a4-9435-08c99df74023	019de42b-1ba3-7152-9b76-d7baadc5cebe	019de42b-1761-70c7-a2de-4906fe0a7166	\N	1	t	# Proposition\n\n## Contexte\nChaque été, les collaborateurs demandent des aménagements horaires.\n\n## Proposition\nDu 1er juin au 31 août : arrivée libre entre 7h et 10h, départ après 6h de travail effectif.	\N	2026-04-27 01:32:03	2026-04-27 01:32:03
019de42b-1bc8-7297-9b75-bbff38d9b9e6	019de42b-1bb9-734d-a3aa-efcf55a9663b	019de42b-1839-721b-990c-b44db2a24f10	\N	1	t	# Proposition\n\n## Contexte\nNous utilisons actuellement 3 outils de communication (email, Teams, WhatsApp).\n\n## Proposition\nCentraliser sur Slack avec les intégrations GitHub, Jira et Google Calendar.\n\n## Budget\n12€/utilisateur/mois soit ~1 440€/an pour l'équipe.	\N	2026-04-20 21:32:03	2026-04-20 21:32:03
019de42b-1c18-737d-97db-14ebe8957f05	019de42b-1c0c-72b3-aa14-8cea6be187b7	019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	1	t	# Proposition\n\n## Contexte\nNos objectifs annuels manquent de réactivité face aux changements du marché.\n\n## Proposition\nAdopter le framework OKR avec des cycles de 3 mois et des revues mensuelles.	\N	2026-04-26 03:32:03	2026-04-26 03:32:03
019de42b-1c34-70ef-a26e-532d562bcc48	019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	1	f	# Proposition\n\n## Détails\nPlafond de 50€/mois pour les frais professionnels sans justificatif.	\N	2026-04-26 23:32:03	2026-04-26 23:32:03
019de42b-1c52-72cd-b43b-2ce45083ea6c	019de42b-1c42-70b4-917e-c62aa3f5649e	019de42b-1688-7290-b257-cd7e06dcb2d4	\N	1	t	# Proposition\n\n## Convention\n- `feature/TICKET-description`\n- `fix/TICKET-description`\n- `hotfix/description`\n\n## Merge\nSquash & merge systématique sur main.	\N	2026-04-16 15:32:03	2026-04-16 15:32:03
019de42b-1c6f-7157-abbd-df281ae7602e	019de42b-1c60-7174-b9f7-8c1cf171c88f	019de42b-1761-70c7-a2de-4906fe0a7166	\N	1	f	# Proposition\n\n## Budget\n1 000€/an/personne pour la formation continue.	\N	2026-04-01 15:32:03	2026-04-01 15:32:03
019de42b-1c76-73d4-9a8e-35c6ab840593	019de42b-1c60-7174-b9f7-8c1cf171c88f	019de42b-1761-70c7-a2de-4906fe0a7166	019de42b-1c6f-7157-abbd-df281ae7602e	2	t	# Proposition (v2)\n\n## Budget\n1 500€/an/personne (au lieu de 1 000€ en v1).\n\n## Conditions\n- Formation en lien avec le poste ou l'évolution souhaitée\n- Validation par le cercle concerné	Augmentation du budget suite aux retours	2026-04-02 15:32:03	2026-04-02 15:32:03
019de42b-1c8a-728a-9093-09f18fcce091	019de42b-1c7f-7029-b504-cdd9f0624d4c	019de42b-1839-721b-990c-b44db2a24f10	\N	1	t	# Décision administrative\n\nFermeture de l'organisation le 24 décembre.\nJournée offerte (non décomptée des congés).	\N	2026-03-02 15:32:03	2026-03-02 15:32:03
019de42b-1c9b-7368-906d-eb0fc4d4b814	019de42b-1c8c-734c-b9a8-0f4ad8661ae0	019de42b-190e-70c3-9d21-2b6d2b36612c	\N	1	t	# Proposition\n\n## Idée\nTester la semaine de 4 jours sur un trimestre.\n\n## Raison de l'abandon\nAprès analyse, la charge client ne le permet pas à court terme.	\N	2026-03-17 15:32:03	2026-03-17 15:32:03
019de42b-1cb5-72a3-8c0c-9a3e29de43f8	019de42b-1ca4-7379-a626-a48f413fb9ed	019de42b-1ab2-7286-824f-90df7187f28e	\N	1	t	# Proposition\n\n## Contexte\nLe bail actuel expire en septembre 2027.\n\n## Options étudiées\n1. Renouvellement avec négociation\n2. Déménagement vers un espace plus petit\n3. Full remote + coworking\n\n## Statut\nSuspendu en attente des résultats financiers Q2.	\N	2026-04-11 15:32:03	2026-04-11 15:32:03
019de42b-1ccc-716c-b2b0-d8dd09e02108	019de42b-1cc1-70cf-ae0e-14be5444ad46	019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	1	t	# Consultation\n\n## Question\nSouhaitez-vous changer pour un café équitable local ?\n\n## Détail\nCoût supplémentaire de 15€/mois.	\N	2026-01-31 15:32:03	2026-01-31 15:32:03
019de42b-1d0c-707d-8cf2-ce0f0b302bf5	019de42b-1cee-71fb-8630-9e168aa2ef61	019de42b-1688-7290-b257-cd7e06dcb2d4	\N	1	t	# Proposition\n\n## Contexte\nLe taux de rebond de la page d'accueil est de 65%.\n\n## Proposition\n- Hero section avec vidéo\n- Témoignages clients\n- CTA unique vers l'essai gratuit\n\n## Maquettes\nVoir les pièces jointes.	\N	2026-04-20 23:32:03	2026-04-20 23:32:03
019de42b-1d41-7158-8250-218c5ee0db78	019de42b-1d34-7393-9cae-17d56e46534c	019de42b-1839-721b-990c-b44db2a24f10	\N	1	f	# Charte v1\n\nTransparence et communication bienveillante.	\N	2026-01-01 15:32:04	2026-01-01 15:32:04
019de42b-1d43-709c-871b-b04ce9d82756	019de42b-1d34-7393-9cae-17d56e46534c	019de42b-1839-721b-990c-b44db2a24f10	019de42b-1d41-7158-8250-218c5ee0db78	2	f	# Charte v2\n\n1. Transparence\n2. Async first\n3. Réponse sous 48h	Ajout de la règle des 19h suite aux retours	2026-01-02 15:32:04	2026-01-02 15:32:04
019de42b-1d45-7127-9054-341339655f9c	019de42b-1d34-7393-9cae-17d56e46534c	019de42b-1839-721b-990c-b44db2a24f10	019de42b-1d43-709c-871b-b04ce9d82756	3	t	# Charte v3\n\n## Principes\n1. Transparence par défaut\n2. Communication asynchrone privilégiée\n3. Réponse sous 24h ouvrées\n4. Pas de message professionnel après 19h	Passage de 48h à 24h pour les réponses	2026-01-03 15:32:04	2026-01-03 15:32:04
019de42b-1d59-71ca-a142-cc9089a3cd75	019de42b-1d4f-719e-91d2-12c47feb2033	019de42b-190e-70c3-9d21-2b6d2b36612c	\N	1	t	# Consultation\n\n## Question\nRenouveler l'abonnement corporate à FitClub (3 600€/an) ?\n\n## Contexte\nSeulement 4 personnes utilisent l'abonnement régulièrement.	\N	2026-02-15 15:32:04	2026-02-15 15:32:04
019de42b-1d6c-728c-91dd-c7e1e533d722	019de42b-1d5b-73a6-a0b4-cfe18bd52827	019de42b-1ab2-7286-824f-90df7187f28e	\N	1	t	# Proposition\n\n## Contexte\nAvec la croissance, nous avons besoin d'un cadre éthique formel.\n\n## Proposition\nCréer un comité de 3 personnes renouvelé annuellement par élection sans candidat.	\N	2026-04-21 11:32:04	2026-04-21 11:32:04
019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-1d70-72ca-892d-ef83b7d3658e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	\N	1	t	# Consultation\n\n## Question\nSponsoriser le DevFest Nantes 2026 (stand Gold à 5 000€) ?\n\n## Arguments pour\n- Visibilité auprès de 2 000 développeurs\n- Recrutement potentiel\n- Networking	\N	2026-04-23 12:32:04	2026-04-23 12:32:04
019de42b-1be2-71f4-8133-7283b4861586	019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-190e-70c3-9d21-2b6d2b36612c	\N	1	f	# Proposition\n\n## Contexte\nUne faille critique (CVSS 9.8) a été identifiée dans notre framework.\n\n## Action requise\nDéployer le patch v3.2.1 en production ce soir.\n\n## Risque si inaction\nExposition des données utilisateurs.	\N	2026-04-28 05:32:03	2026-05-03 15:38:28
019de42b-1cdd-72f9-a1ec-fe038fc63086	019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-15b1-72fa-9387-b143e6dbf3ea	\N	1	f	# Consultation\n\n## Question\nQuel prestataire retenir pour l'audit RGPD annuel ?\n\n## Options\n1. **DataProtect** – 8 000€, délai 3 semaines\n2. **SecureLaw** – 12 000€, délai 2 semaines\n3. **PrivacyFirst** – 6 500€, délai 5 semaines	\N	2026-04-30 09:32:03	2026-05-03 17:40:51
019de42b-1d29-73f7-b72e-5a76da41bfbf	019de42b-1d1a-7065-a10a-37d15a5a650d	019de42b-1761-70c7-a2de-4906fe0a7166	\N	1	f	# Proposition\n\n## Contexte\nInflation de 4,2% sur les 12 derniers mois.\n\n## Proposition\nAugmentation générale de 3% au 1er juillet pour tous les salariés.	\N	2026-04-29 15:32:03	2026-05-03 21:26:22
019de42b-1bf9-73da-82c5-a1a0bb833f6a	019de42b-1bee-7325-a8ed-067285f10b54	019de42b-1ab2-7286-824f-90df7187f28e	\N	1	f	# Proposition\n\n## Contexte\nLa pause de 12h à 13h30 est jugée trop rigide par certains.\n\n## Proposition\nLaisser chacun organiser sa pause librement (min 30 min obligatoire).	\N	2026-04-21 11:32:03	2026-05-03 22:05:34
019de42b-1c3f-7354-a997-f8e82222bc77	019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-15b1-72fa-9387-b143e6dbf3ea	019de42b-1c34-70ef-a26e-532d562bcc48	2	f	# Proposition (v2)\n\n## Contexte\nLa v1 a été objectée car le plafond de 50€ était jugé trop bas.\n\n## Nouvelle proposition\nPlafond de 100€/mois sans justificatif, au-delà sur note de frais classique.	Relèvement du plafond suite à l'objection de David	2026-04-27 23:32:03	2026-05-02 08:25:23
019de7ca-d573-7027-a704-ec0aa178752e	019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1c3f-7354-a997-f8e82222bc77	3	t	# Proposition (v3 – brouillon)\n\nPlafond de 120€/mois. Justificatif au-delà de 80€.	\N	2026-05-02 08:25:23	2026-05-02 08:25:23
019dee7d-b480-729d-a37a-36f4ec473474	019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-190e-70c3-9d21-2b6d2b36612c	019de42b-1be2-71f4-8133-7283b4861586	2	f	<p>sdqfqsdfqsdfqsf</p>	\N	2026-05-03 15:38:28	2026-05-03 15:40:14
019dee7f-4f61-71c4-abd3-6b555353be6e	019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-190e-70c3-9d21-2b6d2b36612c	019dee7d-b480-729d-a37a-36f4ec473474	3	t	<p>qsfqsdf qfd q</p>	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019defbc-37d5-731e-9703-882e5b36959d	019de42b-1d1a-7065-a10a-37d15a5a650d	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1d29-73f7-b72e-5a76da41bfbf	2	t	# Proposition\n\n## Contexte\nInflation de 4,2% sur les 12 derniers mois.\n\n## Proposition\nAugmentation générale de 3% au 1er juillet pour tous les salariés.	\N	2026-05-03 21:26:22	2026-05-03 21:26:22
019deeed-c017-70b8-ae26-d80a70af6866	019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1cdd-72f9-a1ec-fe038fc63086	2	f	# Consultation\n\n## Question\nQuel prestataire retenir pour l'audit RGPD annuel ?\n\n## Options\n1. **DataProtect** – 8 000€, délai 3 semaines\n2. **SecureLaw** – 12 000€, délai 2 semaines\n3. **PrivacyFirst** – 6 500€, délai 5 semaines	\N	2026-05-03 17:40:51	2026-05-03 21:48:31
019defe0-1ad7-70a9-b097-b727b17ce6e8	019de42b-1bee-7325-a8ed-067285f10b54	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019de42b-1bf9-73da-82c5-a1a0bb833f6a	2	f	<p># Proposition ## Contexte La pause de 12h à 13h30 est jugée trop rigide par certains. ## Proposition Laisser chacun organiser sa pause librement (min 30 min obligatoire).</p><p><br></p><p>Version 2 !!!!</p>	\N	2026-05-03 22:05:34	2026-05-03 22:07:15
019defe1-a240-72a5-9660-08244947628d	019de42b-1bee-7325-a8ed-067285f10b54	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019defe0-1ad7-70a9-b097-b727b17ce6e8	3	t	<p># Proposition ## Contexte La pause de 12h à 13h30 est jugée trop rigide par certains. ## Proposition Laisser chacun organiser sa pause librement (min 30 min obligatoire).</p><p><br></p><p>Version 2 !!!!</p><p><br></p><p>non, 3!</p>	\N	2026-05-03 22:07:15	2026-05-03 22:07:15
019defd0-7bd0-72d8-8537-6603c636980e	019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019deeed-c017-70b8-ae26-d80a70af6866	3	f	# Consultation\n\n## Question\nQuel prestataire retenir pour l'audit RGPD annuel ?\n\n## Options\n1. **DataProtect** – 8 000€, délai 3 semaines\n2. **SecureLaw** – 12 000€, délai 2 semaines\n3. **PrivacyFirst** – 6 500€, délai 5 semaines	\N	2026-05-03 21:48:31	2026-05-03 22:11:57
019defe5-f1e1-702f-aeb6-40e556582f0b	019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-14cf-7203-ba64-cc6c7ba1cf94	019defd0-7bd0-72d8-8537-6603c636980e	4	t	<p># Consultation ## Question Quel prestataire retenir pour l'audit RGPD annuel ? ## Options 1. **DataProtect** – 8 000€, délai 3 semaines 2. **SecureLaw** – 12 000€, délai 2 semaines 3. **PrivacyFirst** – 6 500€, délai 5 semaines</p><p><br></p><p>encore une version !</p>	\N	2026-05-03 22:11:57	2026-05-03 22:11:57
\.


--
-- Data for Name: decisions; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.decisions (id, circle_id, status, title, visibility, priority, emergency_mode, objection_round_deadline, model_id, created_at, updated_at, revision_content, revision_attachment_ids, current_deadline, reminder_sent, status_before_suspension, share_count) FROM stdin;
019de42b-1b5a-7324-b7d1-b9b424335048	019de42b-1b17-72f8-9b71-b8ae0cc4730f	draft	Mise en place du télétravail 3j/semaine	private	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-23 01:32:03	2026-05-01 15:32:03	\N	\N	\N	f	\N	0
019de42b-1b6f-70d4-aeae-944039975ef4	019de42b-1b1c-7112-b34e-ea76d02e771e	draft	Nouveau logo de l'organisation	public	0	f	\N	019de42b-1b0a-7383-b68a-11627b689095	2026-04-29 15:32:03	2026-05-01 15:32:03	\N	\N	\N	f	\N	0
019de42b-1b84-725d-9e03-1e14ca1630f4	019de42b-1b14-717d-8de3-a2df0be3c3a4	clarification	Migration vers PostgreSQL	public	1	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-26 07:32:03	2026-05-01 15:32:03	\N	\N	\N	f	\N	0
019de42b-1bb9-734d-a3aa-efcf55a9663b	019de42b-1b14-717d-8de3-a2df0be3c3a4	reaction	Adopter Slack comme outil principal	public	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-20 21:32:03	2026-05-01 15:32:03	\N	\N	2026-05-04 15:32:03	f	\N	0
019de42b-1c42-70b4-917e-c62aa3f5649e	019de42b-1b14-717d-8de3-a2df0be3c3a4	adopted	Convention de nommage des branches Git	public	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-16 15:32:03	2026-05-01 15:32:03	\N	\N	\N	f	\N	0
019de42b-1c7f-7029-b504-cdd9f0624d4c	019de42b-1b11-705c-a4a1-ffac5b03ab22	adopted_override	Fermeture exceptionnelle du 24 décembre	public	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-03-02 15:32:03	2026-05-01 15:32:03	\N	\N	\N	f	\N	0
019de42b-1c8c-734c-b9a8-0f4ad8661ae0	019de42b-1b17-72f8-9b71-b8ae0cc4730f	abandoned	Passage à la semaine de 4 jours	public	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-03-17 15:32:03	2026-05-01 15:32:03	\N	\N	\N	f	\N	0
019de42b-1ca4-7379-a626-a48f413fb9ed	019de42b-1b11-705c-a4a1-ffac5b03ab22	suspended	Déménagement des bureaux	private	1	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-11 15:32:03	2026-05-01 15:32:03	\N	\N	\N	f	reaction	0
019de42b-1cc1-70cf-ae0e-14be5444ad46	019de42b-1b17-72f8-9b71-b8ae0cc4730f	lapsed	Changement de fournisseur café	public	0	f	\N	019de42b-1b0a-7383-b68a-11627b689095	2026-01-31 15:32:03	2026-05-01 15:32:03	\N	\N	\N	f	\N	0
019de42b-1cee-71fb-8630-9e168aa2ef61	019de42b-1b1c-7112-b34e-ea76d02e771e	reaction	Refonte de la page d'accueil	public	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-20 23:32:03	2026-05-01 15:32:03	\N	\N	2026-05-05 15:32:03	f	\N	0
019de42b-1d34-7393-9cae-17d56e46534c	019de42b-1b11-705c-a4a1-ffac5b03ab22	adopted	Charte de communication interne	public	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-01-01 15:32:04	2026-05-01 15:32:04	\N	\N	\N	f	\N	0
019de42b-1d4f-719e-91d2-12c47feb2033	019de42b-1b17-72f8-9b71-b8ae0cc4730f	deserted	Renouvellement abonnement salle de sport	public	0	f	\N	019de42b-1b0a-7383-b68a-11627b689095	2026-02-15 15:32:04	2026-05-01 15:32:04	\N	\N	\N	f	\N	0
019de42b-1d5b-73a6-a0b4-cfe18bd52827	019de42b-1b11-705c-a4a1-ffac5b03ab22	draft	Mise en place d'un comité éthique	public	1	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-21 11:32:04	2026-05-01 15:32:04	\N	\N	\N	f	\N	0
019de42b-1d70-72ca-892d-ef83b7d3658e	019de42b-1b1c-7112-b34e-ea76d02e771e	revision	Sponsoring conférence DevFest 2026	public	0	f	\N	019de42b-1b0a-7383-b68a-11627b689095	2026-04-23 12:32:04	2026-05-03 16:24:58	\N	\N	\N	f	\N	0
019de42b-1c25-712a-bc03-854d08a9ad94	019de42b-1b19-7381-b0eb-0237e1a655b9	revision	Politique de remboursement des frais	public	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-26 23:32:03	2026-05-02 08:26:26	\N	\N	\N	f	\N	0
019de42b-1bee-7325-a8ed-067285f10b54	019de42b-1b17-72f8-9b71-b8ae0cc4730f	adopted	Supprimer la pause déjeuner imposée	public	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-21 11:32:03	2026-05-03 22:07:43	\N	\N	\N	f	\N	0
019de42b-1ba3-7152-9b76-d7baadc5cebe	019de42b-1b17-72f8-9b71-b8ae0cc4730f	revision	Horaires flexibles pour l'été	public	0	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-27 01:32:03	2026-05-03 16:32:22	\N	\N	\N	f	\N	0
019de42b-1c60-7174-b9f7-8c1cf171c88f	019de42b-1b19-7381-b0eb-0237e1a655b9	adopted	Budget formation annuel par collaborateur	public	1	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-01 15:32:03	2026-05-02 12:21:12	\N	\N	\N	f	\N	2
019de42b-1bd4-71c6-930f-b0a84cfaf0b4	019de42b-1b14-717d-8de3-a2df0be3c3a4	clarification	Patch de sécurité critique CVE-2026-1234	public	2	t	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-28 05:32:03	2026-05-03 16:12:32	\N	\N	\N	f	\N	0
019de42b-1cce-73d1-a0fe-cf31bff682dd	019de42b-1b14-717d-8de3-a2df0be3c3a4	clarification	Choix du prestataire audit RGPD	private	1	f	\N	\N	2026-04-30 09:32:03	2026-05-03 22:11:57	\N	\N	\N	f	\N	0
019de42b-1c0c-72b3-aa14-8cea6be187b7	019de42b-1b11-705c-a4a1-ffac5b03ab22	adopted	Passer au format OKR trimestriel	public	1	f	\N	019de42b-1b07-7240-8b59-6e43e5016a29	2026-04-26 03:32:03	2026-05-03 20:57:30	\N	\N	\N	f	\N	0
019de42b-1d1a-7065-a10a-37d15a5a650d	019de42b-1b19-7381-b0eb-0237e1a655b9	objection	Augmentation générale de 3%	private	2	f	\N	\N	2026-04-29 15:32:03	2026-05-03 21:26:22	\N	\N	2026-05-06 21:26:22	f	\N	0
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: feedback_joins; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.feedback_joins (id, feedback_id, user_id, created_at, updated_at) FROM stdin;
019de42b-1bfd-704b-992e-e67aff71e883	019de42b-1bfb-7153-96c4-8f34e863d37a	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1c00-728f-b8a7-5490bb613f46	019de42b-1bfb-7153-96c4-8f34e863d37a	019de42b-15b1-72fa-9387-b143e6dbf3ea	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1c39-7153-93e6-7ac009563fd5	019de42b-1c37-7358-a7ae-2187ad834a85	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1c74-70e4-b66c-d6782b8cc059	019de42b-1c72-70f5-b11d-4747f82b498a	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1c9f-716b-af0c-b9f17c5cfff6	019de42b-1c9d-7353-b9f9-f306d30a3785	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1ca1-72be-853e-7ca5e08059b5	019de42b-1c9d-7353-b9f9-f306d30a3785	019de42b-15b1-72fa-9387-b143e6dbf3ea	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1cba-7253-a59b-e12250181edf	019de42b-1cb7-73a1-bf99-789be15ed443	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1d85-7242-8404-d96ea0de8182	019de42b-1d83-7017-91b7-7f2297d4e360	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1d89-706c-9473-532e6bef0ddb	019de42b-1d87-71ab-9389-7c759aa3c6b4	019de42b-14cf-7203-ba64-cc6c7ba1cf94	2026-05-01 15:32:04	2026-05-01 15:32:04
\.


--
-- Data for Name: feedback_messages; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.feedback_messages (id, feedback_id, author_id, content, created_at, updated_at) FROM stdin;
019de42b-1b9b-71be-a133-92c42a2e80dd	019de42b-1b98-70ce-8ada-a968c84127c1	019de42b-1688-7290-b257-cd7e06dcb2d4	Oui, OVH supporte PG16 nativement.	2026-04-27 14:32:03	2026-05-01 15:32:03
\.


--
-- Data for Name: feedbacks; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.feedbacks (id, decision_version_id, author_id, type, status, content, created_at, updated_at) FROM stdin;
019de42b-1b96-720d-8fd0-558fe4846467	019de42b-1b93-7109-a713-dbcf7b2688a7	019de42b-1761-70c7-a2de-4906fe0a7166	clarification	submitted	Quel est le coût estimé de la migration en jours-homme ?	2026-04-27 20:32:03	2026-05-01 15:32:03
019de42b-1b98-70ce-8ada-a968c84127c1	019de42b-1b93-7109-a713-dbcf7b2688a7	019de42b-190e-70c3-9d21-2b6d2b36612c	clarification	treated	Est-ce compatible avec notre hébergeur actuel ?	2026-04-27 11:32:03	2026-05-01 15:32:03
019de42b-1bb2-7151-89bd-25a4584a3d59	019de42b-1bb0-70a4-9435-08c99df74023	019de42b-1839-721b-990c-b44db2a24f10	clarification	submitted	Cela s'applique-t-il aussi aux alternants ?	2026-04-27 17:32:03	2026-05-01 15:32:03
019de42b-1bcb-73ac-a003-a8e9d5178bb8	019de42b-1bc8-7297-9b75-bbff38d9b9e6	019de42b-1761-70c7-a2de-4906fe0a7166	reaction	submitted	Très favorable ! On gagne en productivité.	2026-04-21 14:32:03	2026-05-01 15:32:03
019de42b-1bcd-738c-8e13-b3b24fedba2b	019de42b-1bc8-7297-9b75-bbff38d9b9e6	019de42b-190e-70c3-9d21-2b6d2b36612c	reaction	submitted	Je préfèrerais Discord, c'est gratuit et on y est déjà.	2026-04-21 00:32:03	2026-05-01 15:32:03
019de42b-1bcf-7026-ac86-c880a2446a13	019de42b-1bc8-7297-9b75-bbff38d9b9e6	019de42b-1839-721b-990c-b44db2a24f10	reaction	submitted	OK pour moi tant qu'on garde l'email pour l'externe.	2026-04-22 04:32:03	2026-05-01 15:32:03
019de42b-1be5-73d6-9be2-125dddfc1f1e	019de42b-1be2-71f4-8133-7283b4861586	019de42b-1688-7290-b257-cd7e06dcb2d4	reaction	submitted	Il faut agir immédiatement, je valide.	2026-04-29 23:32:03	2026-05-01 15:32:03
019de42b-1bfb-7153-96c4-8f34e863d37a	019de42b-1bf9-73da-82c5-a1a0bb833f6a	019de42b-1ab2-7286-824f-90df7187f28e	objection	submitted	Cela va fragmenter les réunions d'équipe. On ne trouvera plus de créneau commun.	2026-04-22 02:32:03	2026-05-01 15:32:03
019de42b-1c02-73c7-88d3-9b82791f0d7b	019de42b-1bf9-73da-82c5-a1a0bb833f6a	019de42b-1839-721b-990c-b44db2a24f10	suggestion	submitted	On pourrait garder un créneau protégé de 12h à 13h où aucune réunion n'est planifiée.	2026-04-22 14:32:03	2026-05-01 15:32:03
019de42b-1c37-7358-a7ae-2187ad834a85	019de42b-1c34-70ef-a26e-532d562bcc48	019de42b-1761-70c7-a2de-4906fe0a7166	objection	treated	50€ est insuffisant pour couvrir les déplacements réguliers.	2026-04-27 11:32:03	2026-05-01 15:32:03
019de42b-1c72-70f5-b11d-4747f82b498a	019de42b-1c6f-7157-abbd-df281ae7602e	019de42b-1839-721b-990c-b44db2a24f10	reaction	acknowledged	1000€ c'est trop peu pour des formations certifiantes.	2026-04-02 01:32:03	2026-05-01 15:32:03
019de42b-1c9d-7353-b9f9-f306d30a3785	019de42b-1c9b-7368-906d-eb0fc4d4b814	019de42b-1ab2-7286-824f-90df7187f28e	objection	rejected	Nos clients attendent une disponibilité 5j/7.	2026-03-18 18:32:03	2026-05-01 15:32:03
019de42b-1cb7-73a1-bf99-789be15ed443	019de42b-1cb5-72a3-8c0c-9a3e29de43f8	019de42b-1761-70c7-a2de-4906fe0a7166	reaction	submitted	L'option 3 serait la plus économique.	2026-04-12 17:32:03	2026-05-01 15:32:03
019de42b-1ce0-707f-92ed-e87625a82b6f	019de42b-1cdd-72f9-a1ec-fe038fc63086	019de42b-1ab2-7286-824f-90df7187f28e	clarification	submitted	DataProtect a-t-il des références dans notre secteur ?	2026-05-02 09:32:03	2026-05-01 15:32:03
019de42b-1ce2-732e-8650-8ad7462ae05c	019de42b-1cdd-72f9-a1ec-fe038fc63086	019de42b-1688-7290-b257-cd7e06dcb2d4	clarification	submitted	Le délai de 5 semaines est-il compatible avec notre échéance légale ?	2026-05-02 00:32:03	2026-05-01 15:32:03
019de42b-1d0e-70a0-a5b8-055807f3f7ec	019de42b-1d0c-707d-8cf2-ce0f0b302bf5	019de42b-190e-70c3-9d21-2b6d2b36612c	reaction	submitted	La vidéo risque de ralentir le chargement. Pensons au lazy loading.	2026-04-22 22:32:03	2026-05-01 15:32:03
019de42b-1d2b-705b-9596-15d419124373	019de42b-1d29-73f7-b72e-5a76da41bfbf	019de42b-1839-721b-990c-b44db2a24f10	objection	submitted	3% ne compense pas l'inflation. Cela revient à une baisse de pouvoir d'achat.	2026-04-30 22:32:03	2026-05-01 15:32:04
019de42b-1d2d-73e2-8c3d-08a92c4de618	019de42b-1d29-73f7-b72e-5a76da41bfbf	019de42b-1688-7290-b257-cd7e06dcb2d4	suggestion	submitted	Proposer 3% + prime exceptionnelle de 500€ pour compenser.	2026-05-01 14:32:03	2026-05-01 15:32:04
019de42b-1d83-7017-91b7-7f2297d4e360	019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-1688-7290-b257-cd7e06dcb2d4	reaction	submitted	Excellente opportunité de recrutement tech.	2026-04-24 15:32:04	2026-05-01 15:32:04
019de42b-1d87-71ab-9389-7c759aa3c6b4	019de42b-1d81-7095-ae07-b5bbe039d137	019de42b-1761-70c7-a2de-4906fe0a7166	reaction	submitted	5 000€ c'est beaucoup. Un stand Silver à 2 500€ suffirait.	2026-04-24 18:32:04	2026-05-01 15:32:04
\.


--
-- Data for Name: help_texts; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.help_texts (id, key, level, model_id, content, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: instance_config; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.instance_config (id, key, type, value, created_at, updated_at) FROM stdin;
019de435-79a0-703f-bc51-efd9f55ca4e5	app_logo	string	branding/6IHIhGeyaEoOHRrMynPobRYv5ne7w0TOhIXMbTMh.jpg	2026-05-01 15:43:23	2026-05-01 15:43:23
019de437-3e49-734a-b528-e8d0c28e6a51	app_name	string	GVL - Décisions a Zéro Objection	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e4f-739a-8c7f-d77a25a59b7a	decision_reaction_days	string	3	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e52-73fb-b442-dec89066ce14	decision_objection_days	string	3	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e54-71f8-9042-292cc6a147df	reminder_hours_before	string	24	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e56-7399-b3ba-ba6be1f86f6c	public_registration	string	true	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e59-70ab-93eb-49f664ab7818	mail_sender_name	string	DAZO Notifications	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e5b-708b-ba91-ad3520711ade	mail_contact_address	string	contact@dazo.app	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e5e-7241-8d51-70b15ab0c479	maintenance_mode	string	false	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e60-7297-a982-0bbf9c092fbe	enable_public_front	string	true	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e73-737a-abbe-ac0a14810312	public_filters	string	[]	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e77-72e2-b684-683ecf496a83	legal_mentions_url	string	\N	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e7a-7346-97c1-b3f40c5f5dac	privacy_policy_url	string	\N	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e7c-7378-9c91-47d7aa076935	terms_of_service_url	string	\N	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e7e-7156-94a9-2bdf1a69db60	allowed_file_types	string	\N	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e81-70fb-ad73-3924413f2610	max_file_size_mb	string	10	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e83-721f-8df5-98c0cb3cb550	mail_host	string	\N	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e86-7307-86e9-4fecfd03085e	mail_port	string	587	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e88-7048-951d-17ac05ee9109	mail_username	string	\N	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e8a-725e-af8e-e908fc0d4856	mail_password	string	\N	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e8d-7141-8fda-d13eba4e7ad8	mail_encryption	string	tls	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e90-71b8-8892-58f01d0bca2f	reminder_email_subject	string	⚠️ Rappel : La décision '{title}' arrive à échéance	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e92-7167-b68d-15ab1d382f11	reminder_email_body	string	Bonjour {name},\n\nCeci est un rappel concernant la décision : **{title}**.\n\nLa phase actuelle (**{phase}**) arrive bientôt à échéance. Votre participation est attendue afin de faire progresser le processus.\n\n**Échéance :** {deadline}\n\nMerci de votre contribution.	2026-05-01 15:45:18	2026-05-01 15:45:18
019de437-3e75-7347-a61e-b8a5e925c556	public_api_key	string	\N	2026-05-01 15:45:18	2026-05-03 20:02:18
019de437-3e6a-70f1-86d8-b0fb8f67aeba	public_circles	string	["019de42b-1b11-705c-a4a1-ffac5b03ab22","019de42b-1b17-72f8-9b71-b8ae0cc4730f","019de42b-1b19-7381-b0eb-0237e1a655b9","019de42b-1b1c-7112-b34e-ea76d02e771e"]	2026-05-01 15:45:18	2026-05-01 16:24:49
019de437-3e6c-70fb-9c0b-e3b1c62aadd4	public_categories	string	["019de42b-1af3-7225-98c5-2808199094c1","019de42b-1af0-7320-b7d2-ae478f5bb73b","019de42b-1aee-70be-8798-b90c40d961cc","019de42b-1aec-73e2-81ce-ce77521b3f2a","019de42b-1ae9-71ef-ad60-8dc3b83c3082","019de42b-1ae7-7239-84d8-eebd671fb8aa"]	2026-05-01 15:45:18	2026-05-01 16:24:49
019de437-3e6f-7331-97d2-fb72e21d91a6	public_statuses	string	["clarification","reaction","objection","adopted","abandoned","rejected","suspended"]	2026-05-01 15:45:18	2026-05-01 16:24:49
019de498-fb4a-7028-8e2a-c3b6376624f5	auth_twitter_enabled	string	false	2026-05-01 17:32:04	2026-05-01 17:32:04
019de498-fb60-71c1-94d1-45d640a7a5ce	auth_linkedin-openid_enabled	string	false	2026-05-01 17:32:04	2026-05-01 17:32:04
019de498-fb73-73b8-91c6-59c60998ae3f	auth_gitlab_enabled	string	false	2026-05-01 17:32:04	2026-05-01 17:32:04
019de4a4-21fe-722a-8d6c-bba524627344	mail_template_logo	string	/images/logo-email.png	2026-05-01 17:44:15	2026-05-01 17:44:15
019de860-d1b0-713d-b01a-b64331e76fbb	page_legal_title	string	Mentions Légales	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1b2-70a8-9527-5667fb52f48a	page_legal_slug	string	mentions-legales	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1b5-7347-a468-4e4b2e8f59d6	page_legal_content	string	<h1>Mentions Légales</h1><p>Contenu par défaut à personnaliser...</p>	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1b8-708e-b85b-5fdb004cf6d6	page_privacy_title	string	Politique de Confidentialité	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1ba-7016-8ec1-b658cbe5d168	page_privacy_slug	string	politique-confidentialite	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1bd-7104-9ef1-40b6f1400a59	page_privacy_content	string	<h1>Politique de Confidentialité</h1><p>Contenu par défaut à personnaliser...</p>	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1bf-7259-a1e3-56b87807e848	page_terms_title	string	Conditions Générales d'Utilisation	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1c2-7373-b929-522b62d40473	page_terms_slug	string	cgu	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1c4-7028-a911-4c78dc715750	page_terms_content	string	<h1>Conditions Générales d'Utilisation</h1><p>Contenu par défaut à personnaliser...</p>	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1cb-71f6-8218-05d7ba0bf9dc	mail_reminder_subject	string	⚠️ Rappel : La décision '{title}' arrive à échéance	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1ce-710b-9623-35d3b6f6dfcc	mail_reminder_body	string	Bonjour {name},\n\nCeci est un rappel concernant la décision : **{title}**.\n\nLa phase actuelle (**{phase}**) arrive bientôt à échéance. Votre participation est attendue afin de faire progresser le processus.\n\n**Échéance :** {deadline}\n\nMerci de votre contribution.	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1d0-705c-85ab-5750396b02ab	mail_invitation_subject	string	📩 Invitation à rejoindre le cercle '{circle}'	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1d3-71f2-af86-b92faf3a488b	mail_invitation_body	string	Bonjour,\n\nVous avez été invité à rejoindre le cercle **{circle}** sur la plateforme DAZO par **{inviter}**.\n\nCe cercle traite des sujets suivants : {description}\n\n[Accepter l'invitation]({url})	2026-05-02 11:09:12	2026-05-02 11:09:12
019de497-d5b3-70b3-bc86-79da9d75cf66	auth_github_client_id	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5b7-7065-ba8e-0381bb31165f	auth_github_client_secret	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5ba-7370-8234-6f67fac01ae7	auth_microsoft_client_id	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5be-7106-bec6-eea4b49e0f8b	auth_microsoft_client_secret	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5c2-73bc-b117-ec44dce8ead7	auth_facebook_client_id	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5c5-7137-8abe-536eac38c0d8	auth_facebook_client_secret	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5c9-707f-8d50-b1e58726b176	auth_apple_client_id	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5cd-7190-8e08-85cd115e3e67	auth_apple_client_secret	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5d1-7171-9d22-f71db6b76f21	auth_franceconnect_client_id	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5d5-71a8-9475-79d347d5cb40	auth_franceconnect_client_secret	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de4a4-220a-729d-98c3-d383852eddc7	mail_template_logo_perso	string	\N	2026-05-01 17:44:15	2026-05-02 11:09:12
019de498-fb55-7118-824d-934b8f9ee4de	auth_twitter_client_id	string	\N	2026-05-01 17:32:04	2026-05-02 11:09:12
019de498-fb5b-71f4-994d-39910e49e7f4	auth_twitter_client_secret	string	\N	2026-05-01 17:32:04	2026-05-02 11:09:12
019de498-fb67-7043-afc9-becfeec04f4f	auth_linkedin-openid_client_id	string	\N	2026-05-01 17:32:04	2026-05-02 11:09:12
019de498-fb6d-72c4-9bcd-46eb82eae784	auth_linkedin-openid_client_secret	string	\N	2026-05-01 17:32:04	2026-05-02 11:09:12
019de498-fb78-72af-b3d6-a65260c22752	auth_gitlab_client_id	string	\N	2026-05-01 17:32:04	2026-05-02 11:09:12
019de498-fb8a-7114-988f-fa1f93be9deb	auth_gitlab_client_secret	string	\N	2026-05-01 17:32:04	2026-05-02 11:09:12
019de437-3e63-722c-830c-216a9300d5a7	require_admin_approval	string	true	2026-05-01 15:45:18	2026-05-03 20:52:05
019de437-3e65-70ce-ada0-49919e253b5e	recaptcha_site_key	string	admin@dazo.test	2026-05-01 15:45:18	2026-05-03 20:52:05
019de437-3e67-7162-9f4e-7bb1fec3c02d	recaptcha_secret_key	string	password	2026-05-01 15:45:18	2026-05-03 20:52:05
019de860-d1d5-732c-9b04-4e5db4b288ee	mail_notification_subject	string	📢 Nouvelle étape pour : {title}	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1d8-7333-adc8-fce772510f6d	mail_notification_body	string	Bonjour {name},\n\nLa décision **{title}** vient de passer en phase de **{phase}**.\n\nVous pouvez consulter les détails et participer ici : [Voir la décision]({url})	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1db-7368-aaaf-37aa050fdcc6	mail_contact_subject	string	✉️ Nouveau message de contact : {subject}	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1dd-73be-add2-7d2aac90e60c	mail_contact_body	string	Nom : {name}\nEmail : {email}\n\nMessage :\n{message}	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1e0-7060-88f8-d3dc21e4f6d0	auth_google_enabled	string	false	2026-05-02 11:09:12	2026-05-02 11:09:12
019de497-d5a4-730e-a77a-79b4c9137e11	auth_google_client_id	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de497-d5af-7115-8e6e-0a636f48c388	auth_google_client_secret	string	\N	2026-05-01 17:30:49	2026-05-02 11:09:12
019de860-d1e7-73f4-96b4-a781fa6794f9	auth_github_enabled	string	false	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d1f5-7160-8cfc-d135b9f155c6	auth_microsoft_enabled	string	false	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d208-732d-a30c-5af3d602d2f3	auth_facebook_enabled	string	false	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d21b-70e1-83c3-2672cac69353	auth_apple_enabled	string	false	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d22d-7157-8963-3ea4b439e594	auth_franceconnect_enabled	string	false	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d249-70f2-8fb3-555e62d4501c	mail_template_site_link	string	https://dazo.app	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d24f-72a1-b5d8-61bc6e811045	mail_template_site_link_register	string	https://dazo.app/register	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d255-7074-be20-ec9529ae466e	mail_template_wrapper	string	<div style="font-family: 'Inter', sans-serif; background-color: #f8fafc; padding: 40px 20px; color: #1e293b;">\n  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">\n    <div style="padding: 32px; text-align: center; border-bottom: 1px solid #f1f5f9;">\n      <a href="{site_link}">\n        <img src="{logo}" alt="Logo" style="max-height: 48px; width: auto;">\n      </a>\n    </div>\n    <div style="padding: 40px; line-height: 1.6; font-size: 16px;">\n      {message}\n    </div>\n    <div style="padding: 32px; background: #f8fafc; text-align: center; font-size: 14px; color: #64748b; border-top: 1px solid #f1f5f9;">\n      <p style="margin: 0 0 16px 0;">Vous recevez cet email car vous participez à la gouvernance sur notre plateforme.</p>\n      <div style="display: flex; justify-content: center; gap: 16px;">\n        <a href="{site_link}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Notre Site</a>\n        <span style="color: #cbd5e1;">&bull;</span>\n        <a href="{site_link_register}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">S'inscrire</a>\n      </div>\n    </div>\n  </div>\n</div>	2026-05-02 11:09:12	2026-05-02 11:09:12
019de860-d292-7336-bbf3-3fe0b46ecede	page_legal_enabled	string	true	2026-05-02 11:09:12	2026-05-02 11:09:12
019def9c-d43c-70af-8e14-781f60f234d2	decision_revision_months	string	6	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d445-73fc-b49e-6fcecbb749ce	allowed_domains	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d453-702a-b2df-532e923ee311	mail_new_decision_subject	string	Nouvelle proposition de décision	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d456-7396-b2a0-275aee6577b6	mail_new_decision_body	string	<h1>Nouvelle décision</h1><p>Bonjour {user_name},</p><p>Une nouvelle proposition "{decision_title}" a été publiée.</p><p><a href="{link}">Voir la décision</a></p>	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d458-7351-aecf-ad723c4dd9a5	mail_phase_change_subject	string	Changement de Phase	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d45b-7214-bc8d-7c96df34ab64	mail_phase_change_body	string	<h1>Changement de phase</h1><p>Bonjour {user_name},</p><p>La décision "{decision_title}" est passée en nouvelle phase.</p><p><a href="{link}">Voir la décision</a></p>	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d45e-73a8-9297-4da54a5c5a17	mail_decision_adopted_subject	string	Une décision a été adoptée	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d460-72ae-b098-8b7295c3a022	mail_decision_adopted_body	string	<h1>Décision adoptée !</h1><p>Bonjour {user_name},</p><p>La proposition "{decision_title}" a été officiellement adoptée.</p><p><a href="{link}">Voir le résultat</a></p>	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d463-73d2-9927-95c92e2c3b88	mail_decision_rejected_subject	string	Une décision n'a pas été adoptée	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d466-7163-9b06-b5461f37a81a	mail_decision_rejected_body	string	<h1>Décision refusée</h1><p>Bonjour {user_name},</p><p>La proposition "{decision_title}" n'a pas recueilli le consensus nécessaire.</p><p><a href="{link}">Voir les détails</a></p>	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d46b-71cd-996f-afd8e2bbd029	google_client_id	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d46d-7160-8dc9-6f0d0e12ae92	google_client_secret	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d470-72fc-ba76-507ac08037bd	github_client_id	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d472-722a-87b3-52526a787564	github_client_secret	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d475-714c-9453-243a46bd9e18	facebook_client_id	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d478-722c-b823-ae73e997a9f2	facebook_client_secret	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d47a-7268-93ab-a4edc12f51b7	twitter_client_id	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d47d-72aa-b359-13ab3106e9ac	twitter_client_secret	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d480-73c5-9dc3-bae34553cec9	linkedin_client_id	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d483-71a3-80a0-465383caa4ee	linkedin_client_secret	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d486-728d-a9c1-db93fc0ee7fe	microsoft_client_id	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d489-71c5-864f-1a922cefe64b	microsoft_client_secret	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d48c-725c-9250-f494f3b98b5a	franceconnect_client_id	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
019def9c-d48f-7372-bff6-14b5d3e3ac39	franceconnect_client_secret	string	\N	2026-05-03 20:52:05	2026-05-03 20:52:05
\.


--
-- Data for Name: invitations; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.invitations (id, circle_id, inviter_id, email, role, token, expires_at, used_by, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: labels; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.labels (id, name, color_hex, created_at, updated_at) FROM stdin;
019de42b-1af7-7340-8feb-1c10a4cf1069	Urgent	#dc2626	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1afa-73e4-8f41-96ac781bc4d2	Quick-win	#16a34a	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1afc-71bb-8cbb-3e0eab5bdd8c	Long terme	#2563eb	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1afe-70a6-8c99-c36c40eccebf	Expérimental	#9333ea	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b01-7087-a272-d0b4690d316c	Récurrent	#ca8a04	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1b03-71f1-838a-4266002006c5	Bloquant	#e11d48	2026-05-01 15:32:03	2026-05-01 15:32:03
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_04_17_094732_create_personal_access_tokens_table	1
5	2026_04_17_095056_create_circles_table	1
6	2026_04_17_095057_create_circle_members_table	1
7	2026_04_17_095059_create_categories_table	1
8	2026_04_17_095100_create_labels_table	1
9	2026_04_17_095101_create_decision_models_table	1
10	2026_04_17_095102_create_help_texts_table	1
11	2026_04_17_095103_create_decisions_table	1
12	2026_04_17_095104_create_decision_versions_table	1
13	2026_04_17_095106_create_decision_participants_table	1
14	2026_04_17_095107_create_decision_relations_table	1
15	2026_04_17_095108_create_decision_animator_logs_table	1
16	2026_04_17_095109_create_consents_table	1
17	2026_04_17_095110_create_feedbacks_table	1
18	2026_04_17_095111_create_feedback_joins_table	1
19	2026_04_17_095113_create_feedback_messages_table	1
20	2026_04_17_095114_create_thread_messages_table	1
21	2026_04_17_095115_create_attachments_table	1
22	2026_04_17_095116_create_decision_labels_table	1
23	2026_04_17_095117_create_notifications_table	1
24	2026_04_17_095118_create_notification_preferences_table	1
25	2026_04_17_095120_create_invitations_table	1
26	2026_04_17_095121_create_instance_config_table	1
27	2026_04_17_095122_create_app_logs_table	1
28	2026_04_18_015208_make_decision_version_id_nullable_in_attachments_table	1
29	2026_04_19_145522_add_revision_fields_to_decisions_table	1
30	2026_04_22_100811_add_deadlines_to_decisions_table	1
31	2026_04_22_103732_add_status_before_suspension_to_decisions_table	1
32	2026_04_22_105920_create_wiki_pages_table	1
33	2026_04_22_163200_create_decision_categories_table	1
34	2026_04_26_161313_create_decision_user_settings_table	1
35	2026_04_26_164817_add_custom_views_to_users_table	1
36	2026_04_26_193827_create_wiki_categories_table	1
37	2026_04_26_193840_update_wiki_pages_for_categories	1
38	2026_04_26_235740_make_password_nullable_on_users_table	1
39	2026_04_26_235745_create_social_accounts_table	1
40	2026_04_27_091416_add_phase_to_consents_table	1
41	2026_05_02_112901_add_share_count_to_decisions_table	2
42	2026_05_03_000000_add_performance_indexes	3
43	2026_05_03_191156_add_dashboard_widgets_to_users_table	4
\.


--
-- Data for Name: notification_preferences; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.notification_preferences (id, user_id, category, email_enabled, web_enabled, created_at, updated_at) FROM stdin;
019de42b-1ab6-72cd-b1b6-0dc23ab7be94	019de42b-1688-7290-b257-cd7e06dcb2d4	new_decision	t	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1aba-7112-ac5f-0466608d3ce1	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	t	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1abe-7195-9517-a9b2e3ec9076	019de42b-1688-7290-b257-cd7e06dcb2d4	feedback	t	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1ac2-733d-87c2-0d0cbc579114	019de42b-1688-7290-b257-cd7e06dcb2d4	mention	f	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1ac6-70fc-b81e-c4e010a81503	019de42b-1688-7290-b257-cd7e06dcb2d4	deadline	t	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1aca-70c3-ac90-c1620150b433	019de42b-1761-70c7-a2de-4906fe0a7166	new_decision	f	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1acc-70e6-81f0-d6afbc17707f	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	f	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1acf-70ea-bb26-6a214bbd79d5	019de42b-1761-70c7-a2de-4906fe0a7166	feedback	f	f	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1ad1-727e-9fb9-b803e1fd6921	019de42b-1761-70c7-a2de-4906fe0a7166	mention	f	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1ad4-7065-af28-30abdc89c48c	019de42b-1761-70c7-a2de-4906fe0a7166	deadline	t	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1ad7-7096-8680-000395ce4d5a	019de42b-190e-70c3-9d21-2b6d2b36612c	new_decision	f	f	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1ada-7024-8589-43b685f640a6	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	t	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1add-70fe-9a53-0766e62f7aa0	019de42b-190e-70c3-9d21-2b6d2b36612c	feedback	f	f	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1ae0-73b8-a3eb-13b38fd7a352	019de42b-190e-70c3-9d21-2b6d2b36612c	mention	t	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019de42b-1ae2-70d6-b287-72ea53b15863	019de42b-190e-70c3-9d21-2b6d2b36612c	deadline	t	t	2026-05-01 15:32:03	2026-05-01 15:32:03
019dee7d-b563-72b1-b0b3-5af0e24c7dbf	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	t	t	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b5eb-71f7-85cb-7500f4c443f0	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	t	t	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b621-7270-82d6-488920be961b	019de42b-1839-721b-990c-b44db2a24f10	phase_change	t	t	2026-05-03 15:38:29	2026-05-03 15:38:29
019deea8-0847-7313-8707-123e1508db8d	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	t	t	2026-05-03 16:24:42	2026-05-03 16:24:42
019deeae-7c84-7152-928c-47c58ed0928f	019de42b-19e1-7098-9b5c-395edc9a3d85	phase_change	t	t	2026-05-03 16:31:45	2026-05-03 16:31:45
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.notifications (id, user_id, category, event_type, payload, read_at, created_at, updated_at) FROM stdin;
019dee7d-b494-70d2-a747-09500d8bcac0	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:28	2026-05-03 15:38:28
019dee7d-b565-721f-bf02-649377099b85	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b5b8-7316-9d21-91ff6c3634ae	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b5ea-7329-9a9a-4a3ca44ed61e	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b5ec-71fc-b4e9-ab498c5023ad	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b622-719d-b7cd-e2723d9f60a3	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b653-7165-ae7b-f6c63490cd86	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b684-727a-8ae4-8849e219168d	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b6b7-73cf-9734-24be344789b6	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b6e8-7304-8b05-a08277732c18	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b6ea-7053-8434-e887f8923d97	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7d-b718-737c-b822-92c49f698764	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:38:29	2026-05-03 15:38:29
019dee7f-4f6e-7244-ac52-16499eb0d522	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-4fc0-722f-9752-2b745c8301c2	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-4ff7-735b-8fce-f774dfb39849	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-502b-73d0-8fc1-daf65821c319	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-502d-7330-8e9a-0b944478be4f	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-505f-706e-b86e-6d38bec9df0d	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-509d-703f-8f12-58d01ebdef6d	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-50cf-7231-9996-a471da9d08e5	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-5106-7252-b4df-0b412db2bd34	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-5143-7054-865a-6e3c0bd9b589	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-514b-73c3-b058-75ff75d12f8c	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee7f-5187-72b0-aa7e-ea8cecf3f1f9	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 15:40:14	2026-05-03 15:40:14
019dee9c-e1eb-7024-ad64-fc965d14e85a	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e27f-71df-9566-d2b590930401	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e2c3-72da-91e8-269ff2a03e50	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e301-7063-9353-72f1c22a4dc8	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e306-7272-aacc-f96010b2a0e5	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e33b-7018-86c3-fb3a8225ad61	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e393-7018-98f0-428b54b5cebc	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e3d3-728d-b495-39fb11ca2ee6	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e40a-7319-90fb-d13b22338f2b	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e453-72c0-b38d-9fd17fd7cdba	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e457-70f0-9c83-5e4688295d52	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019dee9c-e492-731e-aa5c-98618267dea5	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1bd4-71c6-930f-b0a84cfaf0b4","title":"Patch de s\\u00e9curit\\u00e9 critique CVE-2026-1234","status":"clarification"}	\N	2026-05-03 16:12:32	2026-05-03 16:12:32
019deea8-084b-71a1-ab84-cd14a872ce6b	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:42	2026-05-03 16:24:42
019deea8-08b2-71aa-a9e3-a80adbdf32f1	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:42	2026-05-03 16:24:42
019deea8-08e9-7015-8e04-0cdb865dfd76	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:42	2026-05-03 16:24:42
019deea8-08ec-73a0-8b1e-38601982ce1a	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:42	2026-05-03 16:24:42
019deea8-0921-7123-8996-98a72ecef74a	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:43	2026-05-03 16:24:43
019deea8-0958-73b8-88a0-a72b780fcda3	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:43	2026-05-03 16:24:43
019deea8-0995-71c6-86d0-cfc17a5936be	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:43	2026-05-03 16:24:43
019deea8-09d5-7202-a6a6-4d6e43f9df9e	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:43	2026-05-03 16:24:43
019deea8-0a0c-7028-8830-03c19566094a	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:43	2026-05-03 16:24:43
019deea8-0a0f-7320-9137-84d7de62ee06	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:43	2026-05-03 16:24:43
019deea8-0a46-73db-b320-bce0fc063072	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:43	2026-05-03 16:24:43
019deea8-0a7e-70b2-a71e-59ad08a8bc58	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1d70-72ca-892d-ef83b7d3658e","title":"Sponsoring conf\\u00e9rence DevFest 2026","status":"clarification"}	\N	2026-05-03 16:24:43	2026-05-03 16:24:43
019deeae-7b13-72d7-96ea-84c1c1306e99	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:45	2026-05-03 16:31:45
019deeae-7b19-7283-85fa-19b77c532a22	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:45	2026-05-03 16:31:45
019deeae-7b9c-7257-a43f-164289c2ed48	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:45	2026-05-03 16:31:45
019deeae-7c49-7208-875d-5facfedbc211	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:45	2026-05-03 16:31:45
019deeae-7c88-70f6-bf2f-c2d7193a523d	019de42b-19e1-7098-9b5c-395edc9a3d85	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:45	2026-05-03 16:31:45
019deeae-7cc3-723e-b781-508e4174a142	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:45	2026-05-03 16:31:45
019deeae-7cc7-7233-a42b-aac3c46cf235	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:45	2026-05-03 16:31:45
019deeae-7cfd-70a7-a8fe-e7c12fecc806	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:45	2026-05-03 16:31:45
019deeae-7d44-7200-9b3d-9e33fcc9dd0f	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:45	2026-05-03 16:31:45
019deeae-7d7d-7392-a9d5-5d123c1f0ca7	019de42b-19e1-7098-9b5c-395edc9a3d85	phase_change	phase_change	{"decision_id":"019de42b-1ba3-7152-9b76-d7baadc5cebe","title":"Horaires flexibles pour l'\\u00e9t\\u00e9","status":"clarification"}	\N	2026-05-03 16:31:46	2026-05-03 16:31:46
019deeed-c025-73a7-b98c-fef7d99dcf73	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:51	2026-05-03 17:40:51
019deeed-c07a-71dc-9097-bdb7955c1ea8	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:51	2026-05-03 17:40:51
019deeed-c0b2-7262-8095-49ada4e292cb	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c151-73bc-9f44-3685dc20eb97	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c185-72bd-ac7e-1d931e2ccb96	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c187-7196-8b8a-90dd8e1b1764	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c1be-709c-9702-17ab946baa4c	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c1f4-700a-90aa-7016ae5ed09f	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c226-7264-a54d-1d4afdbe09b1	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c25b-7211-95e4-075f2f6b6813	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c291-73e7-9a05-425fbeea3d0a	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c2c6-71a1-94c0-a64aac0527e4	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c2c7-7063-8a80-9f8d4c86bd02	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019deeed-c301-7296-afd4-d50806bd2c39	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 17:40:52	2026-05-03 17:40:52
019defd0-7bf1-71ed-8f2d-c69017612afb	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7c80-73e5-9ca3-6a26e2ca5f47	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7cbb-7366-853f-20181dd40a48	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7cf2-7174-a897-5584d18740ea	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7d25-7165-a10c-7fe087a54ba4	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7d27-71dc-adc6-7657e808be64	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7d5c-70b5-8a3a-7e7837b7acff	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7d95-707d-a80c-856a7d98baf0	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7dcb-7331-ba4f-3ec15109c506	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7e05-7221-9c9f-0ba00d3a9bd0	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7e38-71be-8e7f-de49a9c11082	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7e74-707a-9b12-eff1bb86e8fa	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7e76-71cf-84a2-5c46cd3a1f80	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defd0-7ea9-70a7-b056-33017338fa1d	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 21:48:31	2026-05-03 21:48:31
019defe5-f1ff-71f9-a989-5ff50184bd36	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:57	2026-05-03 22:11:57
019defe5-f28e-73f0-9248-cdc8bdd2351e	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:57	2026-05-03 22:11:57
019defe5-f2c4-725c-a629-e647e79538cc	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:57	2026-05-03 22:11:57
019defe5-f316-7209-8243-efe1226bd0d7	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:57	2026-05-03 22:11:57
019defe5-f34f-7088-a447-7ac4b566f5e9	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:57	2026-05-03 22:11:57
019defe5-f350-7008-8c80-14ebb76078d4	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:57	2026-05-03 22:11:57
019defe5-f3ff-7019-b352-6019d00522b1	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:58	2026-05-03 22:11:58
019defe5-f43e-7035-b356-2a100bb24699	019de42b-15b1-72fa-9387-b143e6dbf3ea	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:58	2026-05-03 22:11:58
019defe5-f476-7025-bdc9-516c7678c135	019de42b-14cf-7203-ba64-cc6c7ba1cf94	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:58	2026-05-03 22:11:58
019defe5-f4ae-7053-8fa6-2b2ca25641bd	019de42b-1ab2-7286-824f-90df7187f28e	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:58	2026-05-03 22:11:58
019defe5-f4e7-70f9-aea5-bf1cb652b809	019de42b-1688-7290-b257-cd7e06dcb2d4	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:58	2026-05-03 22:11:58
019defe5-f51e-70aa-ac78-421927a084db	019de42b-1761-70c7-a2de-4906fe0a7166	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:58	2026-05-03 22:11:58
019defe5-f51f-7309-b25f-06b98707a6f5	019de42b-1839-721b-990c-b44db2a24f10	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:58	2026-05-03 22:11:58
019defe5-f582-7394-afd3-9be3b3588893	019de42b-190e-70c3-9d21-2b6d2b36612c	phase_change	phase_change	{"decision_id":"019de42b-1cce-73d1-a0fe-cf31bff682dd","title":"Choix du prestataire audit RGPD","status":"clarification"}	\N	2026-05-03 22:11:58	2026-05-03 22:11:58
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
26	App\\Models\\User	019de42b-1761-70c7-a2de-4906fe0a7166	impersonation_token	a4978a1a8a335b273ccd274b8507a6878da1ca4033c0214e674fa8db59fe5b94	["*"]	2026-05-03 12:54:21	\N	2026-05-03 12:54:17	2026-05-03 12:54:21
7	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	auth_token	7df4181fc1ac5104595d7905bb3d7213e1b3a41ddb9f8e6ecef3644379124894	["*"]	2026-05-02 09:50:53	\N	2026-05-01 16:48:45	2026-05-02 09:50:53
21	App\\Models\\User	019de42b-1688-7290-b257-cd7e06dcb2d4	impersonation_token	a489d8638273663474ea2f656aceea2df8a81ecf44f1cec2a57dc0f52de376fe	["*"]	2026-05-03 12:12:05	\N	2026-05-03 12:12:04	2026-05-03 12:12:05
29	App\\Models\\User	019de42b-15b1-72fa-9387-b143e6dbf3ea	impersonation_token	6c05c92c4f291d064b12a5db5f4eae57fd8c2a1ef8045234f2516c19f8c6b91a	["*"]	2026-05-03 14:42:40	\N	2026-05-03 14:42:40	2026-05-03 14:42:40
12	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	auth_token	f7e29d1fd718626efff490d01503694a76c4a4f925470ac50b24c5e99228fe56	["*"]	2026-05-02 12:21:40	\N	2026-05-02 12:21:30	2026-05-02 12:21:40
36	App\\Models\\User	019de42b-190e-70c3-9d21-2b6d2b36612c	impersonation_token	4c86b6e7d6f3a67f8b820602ccbd8e25e5e4bd166f3010405694f224a69bc2bc	["*"]	2026-05-03 16:14:51	\N	2026-05-03 14:51:56	2026-05-03 16:14:51
30	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	impersonation_token	5da2db537347e4f6406877c7d4762ec4bc103e2be73cbaf3ae97b5a49515be79	["*"]	2026-05-03 14:42:48	\N	2026-05-03 14:42:44	2026-05-03 14:42:48
35	App\\Models\\User	019de42b-1761-70c7-a2de-4906fe0a7166	impersonation_token	c4ed4e92d451f118871a47ce3bc72a69f7f51bde5804cf80b695c7051cb83dc2	["*"]	2026-05-03 14:51:14	\N	2026-05-03 14:47:52	2026-05-03 14:51:14
24	App\\Models\\User	019de42b-15b1-72fa-9387-b143e6dbf3ea	impersonation_token	0d0d502e3c880c88e649130256bc12bfa32e600c2b8b98e1720a1f339424d08e	["*"]	2026-05-03 12:27:35	\N	2026-05-03 12:16:36	2026-05-03 12:27:35
22	App\\Models\\User	019de42b-15b1-72fa-9387-b143e6dbf3ea	impersonation_token	01ef226d47bc8b36ef8142a41d80c52507e995acd3a9ebea5903dd072cc8a600	["*"]	2026-05-03 12:15:48	\N	2026-05-03 12:12:14	2026-05-03 12:15:48
34	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	impersonation_token	96541700427f0a0e74b1f208853046718d9e2ee8a04679561fbc0c50033ae253	["*"]	2026-05-03 14:47:46	\N	2026-05-03 14:44:53	2026-05-03 14:47:46
23	App\\Models\\User	019de42b-1761-70c7-a2de-4906fe0a7166	impersonation_token	fa6c22efe2ad58de46a4097b6f47faf7e506ae48e83793ea31f7f62e887c8dcf	["*"]	2026-05-03 12:16:06	\N	2026-05-03 12:16:05	2026-05-03 12:16:06
31	App\\Models\\User	019de42b-1761-70c7-a2de-4906fe0a7166	impersonation_token	82b546db15d1a1b40c14a52e3f28513834367f84053ed1c9f8e11df5119ffa7f	["*"]	2026-05-03 14:43:22	\N	2026-05-03 14:42:50	2026-05-03 14:43:22
43	App\\Models\\User	019de42b-1761-70c7-a2de-4906fe0a7166	impersonation_token	bb2fbf8129ab29e14110557ce00b5a7297b80c0f0a2674c3e360c1212fb32c94	["*"]	2026-05-03 23:34:26	\N	2026-05-03 23:34:25	2026-05-03 23:34:26
32	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	impersonation_token	f9d37bb79161fce8c6f9dc547993e6f9496cb8f41530e64ca43577ddad44a7be	["*"]	2026-05-03 14:43:26	\N	2026-05-03 14:43:25	2026-05-03 14:43:26
27	App\\Models\\User	019de42b-15b1-72fa-9387-b143e6dbf3ea	impersonation_token	de069873895d87e0574a0774cafc5d4f9dbde5d08b447d2f8ae0ada214615b7a	["*"]	2026-05-03 14:39:44	\N	2026-05-03 12:54:25	2026-05-03 14:39:44
25	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	impersonation_token	53be015c326f197e82ef5aecb1370c160ac4759c708717c5a4f7b3f160ec0f1b	["*"]	2026-05-03 12:54:14	\N	2026-05-03 12:28:08	2026-05-03 12:54:14
33	App\\Models\\User	019de42b-1688-7290-b257-cd7e06dcb2d4	impersonation_token	a75b0552f5dbb194d3b9b40d9267ffb6784694ad3a374a6371a52995cbde781e	["*"]	2026-05-03 14:43:28	\N	2026-05-03 14:43:27	2026-05-03 14:43:28
41	App\\Models\\User	019de42b-1688-7290-b257-cd7e06dcb2d4	impersonation_token	0d1c1c5f50c02e7a3ef090310cd75319de6251e61c624eb738cb613dd34e169b	["*"]	2026-05-03 21:11:27	\N	2026-05-03 21:11:25	2026-05-03 21:11:27
28	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	impersonation_token	2e7bf8b4ec2da21fcfe70c29e06015d127df8f11b6fc10ff6371f25ff4db52e7	["*"]	2026-05-03 14:42:32	\N	2026-05-03 14:42:31	2026-05-03 14:42:32
18	App\\Models\\User	019de42b-1ab2-7286-824f-90df7187f28e	auth_token	669fb84f524618342902807aee3913579806f1a35406813ba62de5862401332b	["*"]	2026-05-03 23:37:36	\N	2026-05-03 09:56:46	2026-05-03 23:37:36
37	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	impersonation_token	33270f09f8383c05bd22e5a62b71bd0c388facf7a493fa3e1a8a2fe0891a21bc	["*"]	2026-05-03 16:32:53	\N	2026-05-03 16:15:02	2026-05-03 16:32:53
20	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	auth_token	fc45f6dde87ecbdf23b3c96c7291a8123c4be234f36c87b3d7f08c18c1d5521b	["*"]	2026-05-03 23:34:32	\N	2026-05-03 11:49:27	2026-05-03 23:34:32
42	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	impersonation_token	ad6f425b750f1296bf37f2b458d4dcad946d2598f88bc1529069421d76bcef4f	["*"]	2026-05-03 23:33:45	\N	2026-05-03 21:11:41	2026-05-03 23:33:45
39	App\\Models\\User	019de42b-15b1-72fa-9387-b143e6dbf3ea	impersonation_token	f76083f6e1724ebf1dfdd10344896e92d4ef550cbcdf5b3ed35d4e286e42d13f	["*"]	2026-05-03 16:57:22	\N	2026-05-03 16:43:51	2026-05-03 16:57:22
40	App\\Models\\User	019de42b-14cf-7203-ba64-cc6c7ba1cf94	impersonation_token	fe8eab320c0d3c4bfc09f3ca3792d4b421b93b5cb24a7c987e23e70cad9426e2	["*"]	2026-05-03 21:11:17	\N	2026-05-03 16:57:24	2026-05-03 21:11:17
38	App\\Models\\User	019de42b-1761-70c7-a2de-4906fe0a7166	impersonation_token	256c99b759ba218007ed1c70510c03004acd5b1374b0d1bb13738421c8cdd163	["*"]	2026-05-03 16:43:46	\N	2026-05-03 16:33:29	2026-05-03 16:43:46
44	App\\Models\\User	019de42b-190e-70c3-9d21-2b6d2b36612c	impersonation_token	193dfa859a248ca6abf8efee5b8175ebd55bbfb60ba78ca786060c62b1d43b4e	["*"]	2026-05-03 23:39:32	\N	2026-05-03 23:34:31	2026-05-03 23:39:32
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: social_accounts; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.social_accounts (id, user_id, provider, provider_id, provider_token, provider_refresh_token, provider_data, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: thread_messages; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.thread_messages (id, decision_id, author_id, tour, content, is_moderator_note, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.users (id, name, email, email_verified_at, password, avatar_url, role, is_global_animator, is_active, remember_token, created_at, updated_at, deleted_at, custom_views, dashboard_widgets) FROM stdin;
019de42b-1839-721b-990c-b44db2a24f10	Emma Petit	emma@dazo.test	2026-05-01 15:32:02	$2y$12$P8IF.LeYUaRAS4U3K2/XteELCbvZ1lufv3TsKJLlt0est3NzmRJIC	\N	user	f	t	\N	2026-05-01 15:32:02	2026-05-01 15:32:02	\N	\N	\N
019de42b-19e1-7098-9b5c-395edc9a3d85	Gaëlle Rousseau	gaelle@dazo.test	2026-05-01 15:32:03	$2y$12$uCxQQ2uPxSS8Sf/IaQntqeMpZkAmwOmi1Ejo0xVyWgvp1XaHFqsAK	\N	user	f	f	\N	2026-05-01 15:32:03	2026-05-01 15:32:03	\N	\N	\N
019de42b-1688-7290-b257-cd7e06dcb2d4	Claire Lefèvre	claire@dazo.test	2026-05-01 15:32:02	$2y$12$yWUqBiSiX52zmosA00N4qOiu.spjFyHrTyaDhlQ71/XFhY2QSR3xe	\N	user	t	t	\N	2026-05-01 15:32:02	2026-05-03 21:11:25	\N	[{"name":"En attente de moi","filters":{"needs_my_action":true}}]	[{"id":"stats","label":"Statistiques g\\u00e9n\\u00e9rales","enabled":true,"width":"full"},{"id":"tickets","label":"Mes tickets actifs","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences & \\u00c9ch\\u00e9ances","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"third"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"third"},{"id":"circles_watch","label":"\\u00c0 surveiller","enabled":true,"width":"third"}]
019de42b-1ab2-7286-824f-90df7187f28e	Hugo Bernard	hugo@dazo.test	2026-05-01 15:32:03	$2y$12$wypIO6VbQihYqgDgDhphQuNEQVPdTIpBUIWPe6O6DdomivUckT0UG	\N	admin	f	t	\N	2026-05-01 15:32:03	2026-05-03 09:56:46	\N	[{"id":"my-proposals","label":"Mes propositions","icon":"fa-solid fa-bullhorn","filters":{"role":"author"}},{"id":"pending-actions","label":"R\\u00e9actions attendues","icon":"fa-solid fa-clock","filters":{"action":"pending"}}]	\N
019de42b-15b1-72fa-9387-b143e6dbf3ea	Bob Martin	user@dazo.test	2026-05-01 15:32:02	$2y$12$XkqJyKZdHHYFcBV3SayVE.cI0zLiI6aEoL2NBv/gac5FVbprPyhLS	\N	admin	f	t	\N	2026-05-01 15:32:02	2026-05-03 12:28:31	\N	[{"id":"my-proposals","label":"Mes propositions","icon":"fa-solid fa-bullhorn","filters":{"role":"author"}},{"id":"pending-actions","label":"R\\u00e9actions attendues","icon":"fa-solid fa-clock","filters":{"action":"pending"}}]	\N
019de42b-1761-70c7-a2de-4906fe0a7166	David Nguyen	david@dazo.test	2026-05-01 15:32:02	$2y$12$1CCNdLPQkl80m7fj1KLNdexmIYu5Fl1cFO3YB7eW61uwOtIIdy4HK	\N	user	f	t	\N	2026-05-01 15:32:02	2026-05-03 23:34:26	\N	[{"id":"my-proposals","label":"Mes propositions","icon":"fa-solid fa-bullhorn","filters":{"role":"author"}},{"id":"pending-actions","label":"R\\u00e9actions attendues","icon":"fa-solid fa-clock","filters":{"action":"pending"}}]	[{"id":"stats","label":"Statistiques g\\u00e9n\\u00e9rales","enabled":true,"width":"full"},{"id":"tickets","label":"Mes tickets actifs","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences & \\u00c9ch\\u00e9ances","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"third"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"third"},{"id":"circles_watch","label":"\\u00c0 surveiller","enabled":true,"width":"third"}]
019de42b-190e-70c3-9d21-2b6d2b36612c	Franck Moreau	franck@dazo.test	2026-05-01 15:32:02	$2y$12$lslGBExDSRDBxp9Gc1YLDez6mIStur2zomsXzXsIJb2M7w1y6fye2	\N	user	f	t	\N	2026-05-01 15:32:02	2026-05-03 23:34:32	\N	[{"name":"Strat\\u00e9gie en cours","filters":{"category":"Strat\\u00e9gie","status":["clarification","reaction","objection"]}}]	[{"id":"stats","label":"Statistiques g\\u00e9n\\u00e9rales","enabled":true,"width":"full"},{"id":"tickets","label":"Mes tickets actifs","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences & \\u00c9ch\\u00e9ances","enabled":true,"width":"half"},{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"third"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"third"},{"id":"circles_watch","label":"\\u00c0 surveiller","enabled":true,"width":"third"}]
019de42b-14cf-7203-ba64-cc6c7ba1cf94	Alice Durand	admin@dazo.test	2026-05-01 15:32:01	$2y$12$iHMibe9w5hoDuhz608lOveyKl68DYeIqDzOW2cA7hwDrUx0sdQLoy	avatars/dwW2tZCckOPFZ5jirCmfZbVb9V7V8FBWwhoGoV8R.jpg	superadmin	t	t	\N	2026-05-01 15:32:01	2026-05-03 23:33:34	\N	[{"name":"Mes urgences","filters":{"status":["objection","reaction"],"priority":1}},{"name":"Archiv\\u00e9es","filters":{"status":["adopted","abandoned"]}},{"id":"my-animations","label":"Mes animations","icon":"fa-solid fa-user-tie","filters":{"role":"animator"}},{"id":"pending-actions","label":"R\\u00e9actions attendues","icon":"fa-solid fa-clock","filters":{"action":"pending"}},{"id":"urgent-decisions","label":"D\\u00e9cisions urgentes","icon":"fa-solid fa-triangle-exclamation","filters":{"urgency":"urgent"}}]	[{"id":"my_proposals","label":"Mes propositions","enabled":true,"width":"full"},{"id":"my_circles","label":"Mes cercles","enabled":true,"width":"third"},{"id":"stats","label":"Statistiques","enabled":true,"width":"full"},{"id":"categories","label":"Cat\\u00e9gories","enabled":true,"width":"half"},{"id":"tickets","label":"Clarifications & Objections","enabled":true,"width":"half"},{"id":"my_animated","label":"Mes animations","enabled":true,"width":"half"},{"id":"circles_watch","label":"Mes cercles (flux)","enabled":true,"width":"half"},{"id":"urgencies","label":"Urgences","enabled":true,"width":"half"}]
\.


--
-- Data for Name: wiki_categories; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.wiki_categories (id, name, slug, "order", created_at, updated_at) FROM stdin;
019de42b-1dac-710e-a43c-111c77480c23	Introduction	introduction	0	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1db1-72e4-90dd-e9ae8b44a430	Gouvernance	gouvernance	1	2026-05-01 15:32:04	2026-05-01 15:32:04
019de42b-1db9-71e6-a6d4-3d3b5a90b2c7	Utilisation	utilisation	2	2026-05-01 15:32:04	2026-05-01 15:32:04
\.


--
-- Data for Name: wiki_pages; Type: TABLE DATA; Schema: public; Owner: dazo_user
--

COPY public.wiki_pages (id, slug, title, content, is_published, created_at, updated_at, wiki_category_id, "order") FROM stdin;
019de42b-1dae-7334-a2be-927704eb0915	bienvenue-sur-dazo	Bienvenue sur DAZO	<h1>Bienvenue !</h1><p>DAZO est une plateforme de gouvernance partagée conçue pour faciliter la prise de décision collective et transparente.</p><blockquote>L'objectif de DAZO est de redonner du pouvoir d'agir aux membres d'une organisation en structurant le chaos créatif par des processus clairs.</blockquote><h2>Comment commencer ?</h2><ul><li>Explorez les <strong>Cercles</strong> pour comprendre la structure de l'organisation.</li><li>Consultez les <strong>Décisions</strong> en cours pour voir sur quoi travaille la communauté.</li><li>Participez en réagissant aux propositions !</li></ul>	t	2026-05-01 15:32:04	2026-05-01 15:32:04	019de42b-1dac-710e-a43c-111c77480c23	0
019de42b-1db4-73f8-a7bc-477b9008041a	lelection-sans-candidat	L'Élection Sans Candidat	<h1>L'Élection Sans Candidat (ESC)</h1><p>Contrairement au vote majoritaire classique, l'ESC est un processus de recherche du candidat le plus apte pour une mission donnée, sans que personne n'ait besoin de se porter volontaire au départ.</p><h2>Les étapes clés :</h2><ol><li><strong>Définition du rôle</strong> : On commence par définir clairement les responsabilités et les compétences nécessaires.</li><li><strong>Nominations</strong> : Chaque membre nomme la personne qu'il juge la plus apte (il peut se nommer lui-même).</li><li><strong>Arguments</strong> : On expose pourquoi on a choisi cette personne.</li><li><strong>Renforcement</strong> : Possibilité de changer son vote après avoir entendu les arguments.</li><li><strong>Proposition de l'animateur</strong> : L'animateur propose un candidat sur la base des échanges.</li><li><strong>Objections</strong> : On cherche le consentement de tous sur la proposition.</li></ol>	t	2026-05-01 15:32:04	2026-05-01 15:32:04	019de42b-1db1-72e4-90dd-e9ae8b44a430	0
019de42b-1db7-7225-893b-50fadef5cdd6	comprendre-le-consentement	Comprendre le Consentement	<h1>Le Consentement vs Le Compromis</h1><p>Dans DAZO, nous visons le <strong>consentement</strong> : "Personne n'a d'objection raisonnable". Ce n'est pas la moyenne des opinions (compromis) ni l'accord enthousiaste de tous (consensus).</p><h2>Qu'est-ce qu'une objection ?</h2><p>Une objection n'est pas une préférence personnelle ("Je préférerais une autre couleur"). C'est une alerte sur un risque pour l'organisation : <strong>"Cette décision va nuire à notre capacité à remplir notre mission."</strong></p><blockquote>"C'est suffisamment bon pour l'instant, et assez sûr pour essayer."</blockquote>	t	2026-05-01 15:32:04	2026-05-01 15:32:04	019de42b-1db1-72e4-90dd-e9ae8b44a430	0
019de42b-1dbb-705a-aac7-4d3f5274b336	gerer-ses-notifications	Gérer ses notifications	<h1>Paramètres et Notifications</h1><p>Pour rester informé sans être submergé, DAZO vous permet de configurer vos préférences.</p><ul><li><strong>Réactions attendues</strong> : Vous recevez un mail lorsqu'une décision nécessite votre attention immédiate.</li><li><strong>Suivi de cercle</strong> : Abonnez-vous à un cercle pour voir toutes ses actualités.</li></ul><p>Vous pouvez modifier ces réglages dans l'onglet <strong>Paramètres</strong> de votre profil.</p>	t	2026-05-01 15:32:04	2026-05-01 15:32:04	019de42b-1db9-71e6-a6d4-3d3b5a90b2c7	0
\.


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dazo_user
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dazo_user
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dazo_user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 43, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dazo_user
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 44, true);


--
-- Name: app_logs app_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.app_logs
    ADD CONSTRAINT app_logs_pkey PRIMARY KEY (id);


--
-- Name: attachments attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.attachments
    ADD CONSTRAINT attachments_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: categories categories_name_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_name_unique UNIQUE (name);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: circle_members circle_members_circle_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.circle_members
    ADD CONSTRAINT circle_members_circle_id_user_id_unique UNIQUE (circle_id, user_id);


--
-- Name: circle_members circle_members_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.circle_members
    ADD CONSTRAINT circle_members_pkey PRIMARY KEY (id);


--
-- Name: circles circles_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.circles
    ADD CONSTRAINT circles_pkey PRIMARY KEY (id);


--
-- Name: consents consents_decision_version_id_user_id_phase_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.consents
    ADD CONSTRAINT consents_decision_version_id_user_id_phase_unique UNIQUE (decision_version_id, user_id, phase);


--
-- Name: consents consents_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.consents
    ADD CONSTRAINT consents_pkey PRIMARY KEY (id);


--
-- Name: decision_animator_logs decision_animator_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_animator_logs
    ADD CONSTRAINT decision_animator_logs_pkey PRIMARY KEY (id);


--
-- Name: decision_categories decision_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_categories
    ADD CONSTRAINT decision_categories_pkey PRIMARY KEY (decision_id, category_id);


--
-- Name: decision_labels decision_labels_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_labels
    ADD CONSTRAINT decision_labels_pkey PRIMARY KEY (decision_id, label_id);


--
-- Name: decision_models decision_models_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_models
    ADD CONSTRAINT decision_models_pkey PRIMARY KEY (id);


--
-- Name: decision_participants decision_participants_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_participants
    ADD CONSTRAINT decision_participants_pkey PRIMARY KEY (id);


--
-- Name: decision_relations decision_relations_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_relations
    ADD CONSTRAINT decision_relations_pkey PRIMARY KEY (id);


--
-- Name: decision_relations decision_relations_unique_idx; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_relations
    ADD CONSTRAINT decision_relations_unique_idx UNIQUE (source_decision_id, target_decision_id, relation_type);


--
-- Name: decision_user_settings decision_user_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_user_settings
    ADD CONSTRAINT decision_user_settings_pkey PRIMARY KEY (id);


--
-- Name: decision_user_settings decision_user_settings_user_id_decision_id_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_user_settings
    ADD CONSTRAINT decision_user_settings_user_id_decision_id_unique UNIQUE (user_id, decision_id);


--
-- Name: decision_versions decision_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_versions
    ADD CONSTRAINT decision_versions_pkey PRIMARY KEY (id);


--
-- Name: decisions decisions_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decisions
    ADD CONSTRAINT decisions_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: feedback_joins feedback_joins_feedback_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedback_joins
    ADD CONSTRAINT feedback_joins_feedback_id_user_id_unique UNIQUE (feedback_id, user_id);


--
-- Name: feedback_joins feedback_joins_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedback_joins
    ADD CONSTRAINT feedback_joins_pkey PRIMARY KEY (id);


--
-- Name: feedback_messages feedback_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedback_messages
    ADD CONSTRAINT feedback_messages_pkey PRIMARY KEY (id);


--
-- Name: feedbacks feedbacks_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedbacks
    ADD CONSTRAINT feedbacks_pkey PRIMARY KEY (id);


--
-- Name: help_texts help_texts_key_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.help_texts
    ADD CONSTRAINT help_texts_key_unique UNIQUE (key);


--
-- Name: help_texts help_texts_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.help_texts
    ADD CONSTRAINT help_texts_pkey PRIMARY KEY (id);


--
-- Name: instance_config instance_config_key_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.instance_config
    ADD CONSTRAINT instance_config_key_unique UNIQUE (key);


--
-- Name: instance_config instance_config_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.instance_config
    ADD CONSTRAINT instance_config_pkey PRIMARY KEY (id);


--
-- Name: invitations invitations_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.invitations
    ADD CONSTRAINT invitations_pkey PRIMARY KEY (id);


--
-- Name: invitations invitations_token_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.invitations
    ADD CONSTRAINT invitations_token_unique UNIQUE (token);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: labels labels_name_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.labels
    ADD CONSTRAINT labels_name_unique UNIQUE (name);


--
-- Name: labels labels_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.labels
    ADD CONSTRAINT labels_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notification_preferences notification_preferences_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.notification_preferences
    ADD CONSTRAINT notification_preferences_pkey PRIMARY KEY (id);


--
-- Name: notification_preferences notification_preferences_user_id_category_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.notification_preferences
    ADD CONSTRAINT notification_preferences_user_id_category_unique UNIQUE (user_id, category);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: social_accounts social_accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.social_accounts
    ADD CONSTRAINT social_accounts_pkey PRIMARY KEY (id);


--
-- Name: social_accounts social_accounts_provider_provider_id_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.social_accounts
    ADD CONSTRAINT social_accounts_provider_provider_id_unique UNIQUE (provider, provider_id);


--
-- Name: thread_messages thread_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.thread_messages
    ADD CONSTRAINT thread_messages_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: wiki_categories wiki_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.wiki_categories
    ADD CONSTRAINT wiki_categories_pkey PRIMARY KEY (id);


--
-- Name: wiki_categories wiki_categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.wiki_categories
    ADD CONSTRAINT wiki_categories_slug_unique UNIQUE (slug);


--
-- Name: wiki_pages wiki_pages_pkey; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.wiki_pages
    ADD CONSTRAINT wiki_pages_pkey PRIMARY KEY (id);


--
-- Name: wiki_pages wiki_pages_slug_unique; Type: CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.wiki_pages
    ADD CONSTRAINT wiki_pages_slug_unique UNIQUE (slug);


--
-- Name: app_logs_entity_type_entity_id_index; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX app_logs_entity_type_entity_id_index ON public.app_logs USING btree (entity_type, entity_id);


--
-- Name: attachments_uploader_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX attachments_uploader_idx ON public.attachments USING btree (uploader_id);


--
-- Name: attachments_version_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX attachments_version_idx ON public.attachments USING btree (decision_version_id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: circle_members_user_role_circle_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX circle_members_user_role_circle_idx ON public.circle_members USING btree (user_id, role, circle_id);


--
-- Name: consents_user_phase_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX consents_user_phase_idx ON public.consents USING btree (user_id, phase);


--
-- Name: consents_version_signal_user_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX consents_version_signal_user_idx ON public.consents USING btree (decision_version_id, signal, user_id);


--
-- Name: decision_categories_category_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decision_categories_category_idx ON public.decision_categories USING btree (category_id);


--
-- Name: decision_labels_label_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decision_labels_label_idx ON public.decision_labels USING btree (label_id);


--
-- Name: decision_participants_decision_role_user_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decision_participants_decision_role_user_idx ON public.decision_participants USING btree (decision_id, role, user_id);


--
-- Name: decision_participants_user_role_decision_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decision_participants_user_role_decision_idx ON public.decision_participants USING btree (user_id, role, decision_id);


--
-- Name: decision_relations_target_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decision_relations_target_idx ON public.decision_relations USING btree (target_decision_id);


--
-- Name: decision_user_settings_decision_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decision_user_settings_decision_idx ON public.decision_user_settings USING btree (decision_id);


--
-- Name: decision_versions_author_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decision_versions_author_idx ON public.decision_versions USING btree (author_id);


--
-- Name: decision_versions_current_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decision_versions_current_idx ON public.decision_versions USING btree (decision_id, is_current);


--
-- Name: decisions_circle_status_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decisions_circle_status_idx ON public.decisions USING btree (circle_id, status);


--
-- Name: decisions_model_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decisions_model_idx ON public.decisions USING btree (model_id);


--
-- Name: decisions_public_listing_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decisions_public_listing_idx ON public.decisions USING btree (visibility, status, created_at);


--
-- Name: decisions_status_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX decisions_status_idx ON public.decisions USING btree (status);


--
-- Name: feedback_messages_author_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX feedback_messages_author_idx ON public.feedback_messages USING btree (author_id);


--
-- Name: feedback_messages_feedback_created_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX feedback_messages_feedback_created_idx ON public.feedback_messages USING btree (feedback_id, created_at);


--
-- Name: feedbacks_author_type_status_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX feedbacks_author_type_status_idx ON public.feedbacks USING btree (author_id, type, status);


--
-- Name: feedbacks_version_type_status_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX feedbacks_version_type_status_idx ON public.feedbacks USING btree (decision_version_id, type, status);


--
-- Name: invitations_circle_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX invitations_circle_idx ON public.invitations USING btree (circle_id);


--
-- Name: invitations_used_by_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX invitations_used_by_idx ON public.invitations USING btree (used_by);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: notifications_user_read_created_idx; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX notifications_user_read_created_idx ON public.notifications USING btree (user_id, read_at, created_at);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: social_accounts_user_id_index; Type: INDEX; Schema: public; Owner: dazo_user
--

CREATE INDEX social_accounts_user_id_index ON public.social_accounts USING btree (user_id);


--
-- Name: app_logs app_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.app_logs
    ADD CONSTRAINT app_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: attachments attachments_decision_version_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.attachments
    ADD CONSTRAINT attachments_decision_version_id_foreign FOREIGN KEY (decision_version_id) REFERENCES public.decision_versions(id) ON DELETE CASCADE;


--
-- Name: attachments attachments_uploader_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.attachments
    ADD CONSTRAINT attachments_uploader_id_foreign FOREIGN KEY (uploader_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: circle_members circle_members_circle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.circle_members
    ADD CONSTRAINT circle_members_circle_id_foreign FOREIGN KEY (circle_id) REFERENCES public.circles(id) ON DELETE CASCADE;


--
-- Name: circle_members circle_members_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.circle_members
    ADD CONSTRAINT circle_members_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: circles circles_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.circles
    ADD CONSTRAINT circles_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.circles(id) ON DELETE SET NULL;


--
-- Name: consents consents_decision_version_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.consents
    ADD CONSTRAINT consents_decision_version_id_foreign FOREIGN KEY (decision_version_id) REFERENCES public.decision_versions(id) ON DELETE CASCADE;


--
-- Name: consents consents_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.consents
    ADD CONSTRAINT consents_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: decision_animator_logs decision_animator_logs_animator_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_animator_logs
    ADD CONSTRAINT decision_animator_logs_animator_id_foreign FOREIGN KEY (animator_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: decision_animator_logs decision_animator_logs_assigned_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_animator_logs
    ADD CONSTRAINT decision_animator_logs_assigned_by_foreign FOREIGN KEY (assigned_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: decision_animator_logs decision_animator_logs_decision_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_animator_logs
    ADD CONSTRAINT decision_animator_logs_decision_id_foreign FOREIGN KEY (decision_id) REFERENCES public.decisions(id) ON DELETE CASCADE;


--
-- Name: decision_categories decision_categories_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_categories
    ADD CONSTRAINT decision_categories_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: decision_categories decision_categories_decision_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_categories
    ADD CONSTRAINT decision_categories_decision_id_foreign FOREIGN KEY (decision_id) REFERENCES public.decisions(id) ON DELETE CASCADE;


--
-- Name: decision_labels decision_labels_decision_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_labels
    ADD CONSTRAINT decision_labels_decision_id_foreign FOREIGN KEY (decision_id) REFERENCES public.decisions(id) ON DELETE CASCADE;


--
-- Name: decision_labels decision_labels_label_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_labels
    ADD CONSTRAINT decision_labels_label_id_foreign FOREIGN KEY (label_id) REFERENCES public.labels(id) ON DELETE CASCADE;


--
-- Name: decision_participants decision_participants_decision_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_participants
    ADD CONSTRAINT decision_participants_decision_id_foreign FOREIGN KEY (decision_id) REFERENCES public.decisions(id) ON DELETE CASCADE;


--
-- Name: decision_participants decision_participants_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_participants
    ADD CONSTRAINT decision_participants_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: decision_relations decision_relations_source_decision_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_relations
    ADD CONSTRAINT decision_relations_source_decision_id_foreign FOREIGN KEY (source_decision_id) REFERENCES public.decisions(id) ON DELETE CASCADE;


--
-- Name: decision_relations decision_relations_target_decision_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_relations
    ADD CONSTRAINT decision_relations_target_decision_id_foreign FOREIGN KEY (target_decision_id) REFERENCES public.decisions(id) ON DELETE CASCADE;


--
-- Name: decision_user_settings decision_user_settings_decision_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_user_settings
    ADD CONSTRAINT decision_user_settings_decision_id_foreign FOREIGN KEY (decision_id) REFERENCES public.decisions(id) ON DELETE CASCADE;


--
-- Name: decision_user_settings decision_user_settings_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_user_settings
    ADD CONSTRAINT decision_user_settings_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: decision_versions decision_versions_author_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_versions
    ADD CONSTRAINT decision_versions_author_id_foreign FOREIGN KEY (author_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: decision_versions decision_versions_decision_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_versions
    ADD CONSTRAINT decision_versions_decision_id_foreign FOREIGN KEY (decision_id) REFERENCES public.decisions(id) ON DELETE CASCADE;


--
-- Name: decision_versions decision_versions_previous_version_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decision_versions
    ADD CONSTRAINT decision_versions_previous_version_id_foreign FOREIGN KEY (previous_version_id) REFERENCES public.decision_versions(id) ON DELETE SET NULL;


--
-- Name: decisions decisions_circle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decisions
    ADD CONSTRAINT decisions_circle_id_foreign FOREIGN KEY (circle_id) REFERENCES public.circles(id) ON DELETE CASCADE;


--
-- Name: decisions decisions_model_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.decisions
    ADD CONSTRAINT decisions_model_id_foreign FOREIGN KEY (model_id) REFERENCES public.decision_models(id) ON DELETE SET NULL;


--
-- Name: feedback_joins feedback_joins_feedback_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedback_joins
    ADD CONSTRAINT feedback_joins_feedback_id_foreign FOREIGN KEY (feedback_id) REFERENCES public.feedbacks(id) ON DELETE CASCADE;


--
-- Name: feedback_joins feedback_joins_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedback_joins
    ADD CONSTRAINT feedback_joins_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: feedback_messages feedback_messages_author_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedback_messages
    ADD CONSTRAINT feedback_messages_author_id_foreign FOREIGN KEY (author_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: feedback_messages feedback_messages_feedback_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedback_messages
    ADD CONSTRAINT feedback_messages_feedback_id_foreign FOREIGN KEY (feedback_id) REFERENCES public.feedbacks(id) ON DELETE CASCADE;


--
-- Name: feedbacks feedbacks_author_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedbacks
    ADD CONSTRAINT feedbacks_author_id_foreign FOREIGN KEY (author_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: feedbacks feedbacks_decision_version_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.feedbacks
    ADD CONSTRAINT feedbacks_decision_version_id_foreign FOREIGN KEY (decision_version_id) REFERENCES public.decision_versions(id) ON DELETE CASCADE;


--
-- Name: help_texts help_texts_model_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.help_texts
    ADD CONSTRAINT help_texts_model_id_foreign FOREIGN KEY (model_id) REFERENCES public.decision_models(id) ON DELETE CASCADE;


--
-- Name: invitations invitations_circle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.invitations
    ADD CONSTRAINT invitations_circle_id_foreign FOREIGN KEY (circle_id) REFERENCES public.circles(id) ON DELETE CASCADE;


--
-- Name: invitations invitations_inviter_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.invitations
    ADD CONSTRAINT invitations_inviter_id_foreign FOREIGN KEY (inviter_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: invitations invitations_used_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.invitations
    ADD CONSTRAINT invitations_used_by_foreign FOREIGN KEY (used_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: notification_preferences notification_preferences_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.notification_preferences
    ADD CONSTRAINT notification_preferences_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: notifications notifications_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: social_accounts social_accounts_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.social_accounts
    ADD CONSTRAINT social_accounts_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: thread_messages thread_messages_author_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.thread_messages
    ADD CONSTRAINT thread_messages_author_id_foreign FOREIGN KEY (author_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: thread_messages thread_messages_decision_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.thread_messages
    ADD CONSTRAINT thread_messages_decision_id_foreign FOREIGN KEY (decision_id) REFERENCES public.decisions(id) ON DELETE CASCADE;


--
-- Name: wiki_pages wiki_pages_wiki_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: dazo_user
--

ALTER TABLE ONLY public.wiki_pages
    ADD CONSTRAINT wiki_pages_wiki_category_id_foreign FOREIGN KEY (wiki_category_id) REFERENCES public.wiki_categories(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict hHAujNlCaxTDZjFojk4BEddfgHWWoEClegrG0Ouub5Zhvdip9RKFjee4ZySwmpZ

