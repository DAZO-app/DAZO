# Graphy Codebase Analysis

## Project: /home/daha/DEV/DAZO/PROJECT

### File Structure Summary
- Total Files: 8967
- Total Directories: 1367
- File Extensions: .png: 3, .svg: 17, .yaml: 2, .md: 270, .json: 156, .js: 47, .yml: 1, .xml: 1, .lock: 5, .pub: 1, .ico: 1, .txt: 13, .php: 8024, .css: 33, .html: 1, .puml: 2, .neon: 2, .rst: 37, .py: 1, .inc: 4, .ini: 6, .conf: 6, .sh: 2, .sql: 1, .stub: 104, .xsd: 37, .fish: 1, .bash: 1, .zsh: 1, .exe: 1, .base64: 1, .bat: 1, .dist: 18, .tpl: 8, .sqlite: 1, .pail: 1, .log: 1, .vue: 23

### Directory Tree
```
  ├── DAZO_logo.png
  ├── artisan
  ├── public
   │  ├── DAZO-logo-carre-blanc.svg
   │  ├── DAZO-logo-carre-noir.svg
   │  ├── favicon.ico
   │  ├── hot
   │  ├── robots.txt
   │  ├── index.php
   │  ├── images
   │  │  ├── dazo-logo.png
   │  │  └── dazo-logo.svg
   │  ├── DAZO-picto-carre-gris.svg
   │  └── build
   │    ├── assets
   │     │  ├── vue-router-W1eaaT2p.js
   │     │  ├── DecisionDetail-C9CLntMi.js
   │     │  ├── app-x1XGuNl0.css
   │     │  ├── AdminCircles-Byz-ppU-.css
   │     │  ├── CircleDetail-B2ZoOd7o.js
   │     │  ├── DecisionList-BAPhKQF-.css
   │     │  ├── app-BjEzhu5A.js
   │     │  ├── axios-C0-gKWuB.js
   │     │  ├── DecisionCreate-CiFZdsPf.css
   │     │  ├── DecisionCreate-Ck_jFB9q.js
   │     │  ├── PendingList-ChAj9Vq0.js
   │     │  ├── pinia-s1kidI2B.js
   │     │  ├── pending-D73uE1PW.js
   │     │  ├── dazo-theme-DqC6Ky8j.css
   │     │  ├── Login-CKk3E0F3.css
   │     │  ├── DecisionList-DEl9ZJEX.js
   │     │  ├── Dashboard-A8J43cl7.css
   │     │  ├── AppLayout-CnbFkIZr.css
   │     │  ├── auth-DQt0SDZr.js
   │     │  ├── AdminCategories-DigWQsaR.css
   │     │  ├── DecisionListItem-ByKjZE5Q.js
   │     │  ├── _plugin-vue_export-helper-TcpyXLsZ.js
   │     │  ├── AdminDashboard-hhWIgEHg.css
   │     │  ├── AdminUsers-DrU8T3zu.css
   │     │  ├── AdminConfig-DOHLX14i.css
   │     │  ├── DecisionDetail-DuVCf7yD.css
   │     │  ├── CircleDetail-DfqlplBL.css
   │     │  ├── AdminConfig-B8PXPWOK.js
   │     │  ├── runtime-dom.esm-bundler-DKjcKQBw.js
   │     │  ├── PendingList-BeBqTae1.css
   │     │  ├── CircleList-CULfrf7U.css
   │     │  ├── AppLayout-Q3i3-Y3d.js
   │     │  ├── AdminCategories-nX7BvXmf.js
   │     │  ├── AdminDashboard-BvcuKBN3.js
   │     │  ├── decision-ZY75FGE_.js
   │     │  ├── CircleList-Dx8cCwyg.js
   │     │  ├── Login-Mr-le9c7.js
   │     │  ├── runtime-core.esm-bundler-C9gM9-nC.js
   │     │  ├── AdminCircles-C4Hpk2ky.js
   │     │  ├── DecisionListItem-SA5mkJcx.css
   │     │  ├── Dashboard-xXYLDCe-.js
   │     │  ├── AdminUsers-B5j3HKXl.js
   │     │  └── circle-D_tN-EyG.js
   │    └── manifest.json
  ├── app
   │  ├── Traits
   │  │  └── HasUserActionStatus.php
   │  ├── Policies
   │  │  ├── InvitationPolicy.php
   │  │  ├── CirclePolicy.php
   │  │  ├── AttachmentPolicy.php
   │  │  ├── DecisionPolicy.php
   │  │  └── DecisionModelPolicy.php
   │  ├── Events
   │  │  ├── DecisionAbandoned.php
   │  │  ├── DecisionTransitioned.php
   │  │  ├── DecisionCreated.php
   │  │  ├── DecisionDeserted.php
   │  │  ├── FeedbackResolved.php
   │  │  ├── DecisionAnimatorChanged.php
   │  │  ├── ParticipationReminderNeeded.php
   │  │  ├── DecisionAdopted.php
   │  │  ├── DecisionAdoptedWithOverride.php
   │  │  ├── DecisionRevisionStarted.php
   │  │  ├── DecisionLapsed.php
   │  │  └── FeedbackSubmitted.php
   │  ├── Enums
   │  │  ├── FeedbackType.php
   │  │  ├── CircleMemberRole.php
   │  │  ├── FeedbackStatus.php
   │  │  ├── ConfigValueType.php
   │  │  ├── DecisionRelationType.php
   │  │  ├── DecisionParticipantRole.php
   │  │  ├── NotificationEventType.php
   │  │  ├── UserRole.php
   │  │  ├── ThreadTour.php
   │  │  ├── InvitationRole.php
   │  │  ├── DecisionStatus.php
   │  │  ├── HelpTextLevel.php
   │  │  ├── DecisionVisibility.php
   │  │  ├── NotificationCategory.php
   │  │  ├── ConsentSignal.php
   │  │  └── CircleType.php
   │  ├── Services
   │  │  ├── DecisionService.php
   │  │  ├── FeedbackService.php
   │  │  ├── ConfigService.php
   │  │  ├── NotificationService.php
   │  │  └── CircleService.php
   │  ├── Http
   │  │  ├── Middleware
   │  │  │  ├── LogRequest.php
   │  │  │  ├── AdminMiddleware.php
   │  │  │  └── ActiveUser.php
   │  │  ├── Controllers
   │  │  │  ├── Api
   │  │  │  │  └── V1
   │  │  │  │    ├── InvitationController.php
   │  │  │  │    ├── AttachmentController.php
   │  │  │  │    ├── DecisionModelController.php
   │  │  │  │    ├── AuthController.php
   │  │  │  │    ├── FeedbackMessageController.php
   │  │  │  │    ├── CategoryController.php
   │  │  │  │    ├── PendingItemsController.php
   │  │  │  │    ├── PendingCountsController.php
   │  │  │  │    ├── NotificationController.php
   │  │  │  │    ├── FeedbackController.php
   │  │  │  │    ├── ProfileController.php
   │  │  │  │    ├── DashboardController.php
   │  │  │  │    ├── Admin
   │  │  │  │     │  ├── CategoryController.php
   │  │  │  │     │  ├── ImpersonationController.php
   │  │  │  │     │  ├── UserController.php
   │  │  │  │     │  └── ConfigController.php
   │  │  │  │    ├── DecisionController.php
   │  │  │  │    ├── ConsentController.php
   │  │  │  │    ├── UserController.php
   │  │  │  │    ├── CircleMemberController.php
   │  │  │  │    ├── CircleController.php
   │  │  │  │    ├── DecisionTransitionController.php
   │  │  │  │    └── DecisionVersionController.php
   │  │  │  └── Controller.php
   │  │  └── Requests
   │  │    ├── DecisionModel
   │  │     │  ├── UpdateDecisionModelRequest.php
   │  │     │  └── CreateDecisionModelRequest.php
   │  │    ├── Decision
   │  │     │  ├── TransitionDecisionRequest.php
   │  │     │  ├── CreateDecisionRequest.php
   │  │     │  └── CreateDecisionVersionRequest.php
   │  │    ├── Consent
   │  │     │  └── CreateConsentRequest.php
   │  │    ├── Circle
   │  │     │  ├── UpdateCircleRequest.php
   │  │     │  └── CreateCircleRequest.php
   │  │    ├── Invitation
   │  │     │  └── CreateInvitationRequest.php
   │  │    ├── Auth
   │  │     │  ├── RegisterRequest.php
   │  │     │  ├── LoginRequest.php
   │  │     │  └── UpdateProfileRequest.php
   │  │    ├── Thread
   │  │    └── Feedback
   │  │       ├── CreateFeedbackRequest.php
   │  │       ├── CreateFeedbackMessageRequest.php
   │  │       └── UpdateFeedbackStatusRequest.php
   │  ├── Jobs
   │  │  ├── CheckDecisionDeadlines.php
   │  │  └── SendEmailNotification.php
   │  ├── Console
   │  │  └── Commands
   │  │    └── PurgeOldLogs.php
   │  ├── Models
   │  │  ├── DecisionParticipant.php
   │  │  ├── Consent.php
   │  │  ├── NotificationPreference.php
   │  │  ├── Label.php
   │  │  ├── Notification.php
   │  │  ├── FeedbackJoin.php
   │  │  ├── Feedback.php
   │  │  ├── HelpText.php
   │  │  ├── AppLog.php
   │  │  ├── DecisionVersion.php
   │  │  ├── DecisionModel.php
   │  │  ├── User.php
   │  │  ├── FeedbackMessage.php
   │  │  ├── Attachment.php
   │  │  ├── Circle.php
   │  │  ├── CircleMember.php
   │  │  ├── DecisionRelation.php
   │  │  ├── DecisionAnimatorLog.php
   │  │  ├── Decision.php
   │  │  ├── InstanceConfig.php
   │  │  ├── Invitation.php
   │  │  └── Category.php
   │  ├── Providers
   │  │  └── AppServiceProvider.php
   │  ├── Http⁄Controllers
   │  └── Listeners
   │    ├── SendDecisionCreatedNotification.php
   │    ├── SendDecisionTransitionedNotification.php
   │    └── SendFeedbackSubmittedNotification.php
  ├── daha_dazo_github
  ├── docs
   │  ├── enums.md
   │  ├── dazo-ui.html
   │  ├── ROADMAP_V1.md
   │  ├── state-machine.md
   │  ├── domain-model.md
   │  ├── decision-lifecycle.md
   │  ├── api.md
   │  ├── frontend.md
   │  ├── steps
   │  │  ├── 09_infrastructure_backend.md
   │  │  ├── 11_impersonation.md
   │  │  ├── 05_feedback_consentement_thread.md
   │  │  ├── 03_moteur_decision_core.md
   │  │  ├── 06_adoption_et_revision.md
   │  │  ├── 02_gestion_cercles.md
   │  │  ├── 13_draft_lifecycle.md
   │  │  ├── 01_authentification_utilisateurs.md
   │  │  ├── 08_moteur_decision_ui.md
   │  │  ├── 07_frontend_foundations.md
   │  │  ├── 00_fondations_techniques.md
   │  │  ├── 10_frontend_features.md
   │  │  ├── 04_machine_etats.md
   │  │  └── 12_admin_crud.md
   │  ├── mcd
   │  │  ├── dazo_mcd_peripherique.puml
   │  │  └── dazo_mcd_core.puml
   │  └── architecture.md
  ├── DAZO-logo.svg
  ├── compose.yaml
  ├── CONTRIBUTING.md
  ├── vendor
   │  ├── nunomaduro
   │  │  ├── termwind
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  ├── playground.php
   │  │  │  └── src
   │  │  │    ├── Html
   │  │  │     │  ├── PreRenderer.php
   │  │  │     │  ├── CodeRenderer.php
   │  │  │     │  ├── TableRenderer.php
   │  │  │     │  └── InheritStyles.php
   │  │  │    ├── Functions.php
   │  │  │    ├── Helpers
   │  │  │     │  └── QuestionHelper.php
   │  │  │    ├── Terminal.php
   │  │  │    ├── Actions
   │  │  │     │  └── StyleToMethod.php
   │  │  │    ├── HtmlRenderer.php
   │  │  │    ├── Enums
   │  │  │     │  └── Color.php
   │  │  │    ├── Exceptions
   │  │  │     │  ├── StyleNotFound.php
   │  │  │     │  ├── InvalidChild.php
   │  │  │     │  ├── InvalidColor.php
   │  │  │     │  ├── InvalidStyle.php
   │  │  │     │  └── ColorNotFound.php
   │  │  │    ├── Question.php
   │  │  │    ├── Termwind.php
   │  │  │    ├── Components
   │  │  │     │  ├── Dt.php
   │  │  │     │  ├── Anchor.php
   │  │  │     │  ├── Span.php
   │  │  │     │  ├── Raw.php
   │  │  │     │  ├── Div.php
   │  │  │     │  ├── Ol.php
   │  │  │     │  ├── Ul.php
   │  │  │     │  ├── Element.php
   │  │  │     │  ├── Li.php
   │  │  │     │  ├── Paragraph.php
   │  │  │     │  ├── Dd.php
   │  │  │     │  ├── Hr.php
   │  │  │     │  ├── BreakLine.php
   │  │  │     │  └── Dl.php
   │  │  │    ├── ValueObjects
   │  │  │     │  ├── Styles.php
   │  │  │     │  ├── Style.php
   │  │  │     │  └── Node.php
   │  │  │    ├── Laravel
   │  │  │     │  └── TermwindServiceProvider.php
   │  │  │    └── Repositories
   │  │  │       └── Styles.php
   │  │  └── collision
   │  │    ├── LICENSE.md
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── ArgumentFormatter.php
   │  │     │  ├── Handler.php
   │  │     │  ├── Writer.php
   │  │     │  ├── SolutionsRepositories
   │  │     │  │  └── NullSolutionsRepository.php
   │  │     │  ├── Contracts
   │  │     │  │  ├── RenderableOnCollisionEditor.php
   │  │     │  │  ├── RenderlessTrace.php
   │  │     │  │  ├── RenderlessEditor.php
   │  │     │  │  ├── Adapters
   │  │     │  │  │  └── Phpunit
   │  │     │  │  │    └── HasPrintableTestCaseName.php
   │  │     │  │  └── SolutionsRepository.php
   │  │     │  ├── Exceptions
   │  │     │  │  ├── ShouldNotHappen.php
   │  │     │  │  ├── TestException.php
   │  │     │  │  ├── InvalidStyleException.php
   │  │     │  │  └── TestOutcome.php
   │  │     │  ├── Coverage.php
   │  │     │  ├── Adapters
   │  │     │  │  ├── Phpunit
   │  │     │  │  │  ├── ConfigureIO.php
   │  │     │  │  │  ├── Subscribers
   │  │     │  │  │  │  ├── EnsurePrinterIsRegisteredSubscriber.php
   │  │     │  │  │  │  └── Subscriber.php
   │  │     │  │  │  ├── Support
   │  │     │  │  │  │  └── ResultReflection.php
   │  │     │  │  │  ├── Style.php
   │  │     │  │  │  ├── TestResult.php
   │  │     │  │  │  ├── State.php
   │  │     │  │  │  ├── Autoload.php
   │  │     │  │  │  └── Printers
   │  │     │  │  │    ├── DefaultPrinter.php
   │  │     │  │  │    └── ReportablePrinter.php
   │  │     │  │  └── Laravel
   │  │     │  │    ├── CollisionServiceProvider.php
   │  │     │  │    ├── Commands
   │  │     │  │     │  └── TestCommand.php
   │  │     │  │    ├── Inspector.php
   │  │     │  │    ├── ExceptionHandler.php
   │  │     │  │    ├── Exceptions
   │  │     │  │     │  ├── NotSupportedYetException.php
   │  │     │  │     │  └── RequirementsException.php
   │  │     │  │    └── IgnitionSolutionsRepository.php
   │  │     │  ├── ConsoleColor.php
   │  │     │  ├── Provider.php
   │  │     │  └── Highlighter.php
   │  │    ├── phpstan-baseline.neon
   │  │    └── scripts
   │  │       └── fix-pdo-constant.php
   │  ├── hamcrest
   │  │  └── hamcrest-php
   │  │    ├── hamcrest
   │  │     │  ├── Hamcrest.php
   │  │     │  └── Hamcrest
   │  │     │    ├── FeatureMatcher.php
   │  │     │    ├── Collection
   │  │     │     │  ├── IsTraversableWithSize.php
   │  │     │     │  └── IsEmptyTraversable.php
   │  │     │    ├── StringDescription.php
   │  │     │    ├── Core
   │  │     │     │  ├── Is.php
   │  │     │     │  ├── Every.php
   │  │     │     │  ├── AllOf.php
   │  │     │     │  ├── HasToString.php
   │  │     │     │  ├── IsInstanceOf.php
   │  │     │     │  ├── IsTypeOf.php
   │  │     │     │  ├── IsCollectionContaining.php
   │  │     │     │  ├── IsIdentical.php
   │  │     │     │  ├── ShortcutCombination.php
   │  │     │     │  ├── IsEqual.php
   │  │     │     │  ├── IsNot.php
   │  │     │     │  ├── IsAnything.php
   │  │     │     │  ├── DescribedAs.php
   │  │     │     │  ├── IsNull.php
   │  │     │     │  ├── AnyOf.php
   │  │     │     │  ├── Set.php
   │  │     │     │  ├── CombinableMatcher.php
   │  │     │     │  └── IsSame.php
   │  │     │    ├── BaseDescription.php
   │  │     │    ├── Arrays
   │  │     │     │  ├── IsArrayContainingKeyValuePair.php
   │  │     │     │  ├── IsArrayContainingInOrder.php
   │  │     │     │  ├── IsArrayWithSize.php
   │  │     │     │  ├── SeriesMatchingOnce.php
   │  │     │     │  ├── IsArrayContaining.php
   │  │     │     │  ├── IsArrayContainingInAnyOrder.php
   │  │     │     │  ├── IsArray.php
   │  │     │     │  ├── IsArrayContainingKey.php
   │  │     │     │  └── MatchingOnce.php
   │  │     │    ├── Xml
   │  │     │     │  └── HasXPath.php
   │  │     │    ├── MatcherAssert.php
   │  │     │    ├── Util.php
   │  │     │    ├── Text
   │  │     │     │  ├── StringContains.php
   │  │     │     │  ├── StringContainsIgnoringCase.php
   │  │     │     │  ├── IsEmptyString.php
   │  │     │     │  ├── MatchesPattern.php
   │  │     │     │  ├── IsEqualIgnoringWhiteSpace.php
   │  │     │     │  ├── IsEqualIgnoringCase.php
   │  │     │     │  ├── StringEndsWith.php
   │  │     │     │  ├── SubstringMatcher.php
   │  │     │     │  ├── StringStartsWith.php
   │  │     │     │  └── StringContainsInOrder.php
   │  │     │    ├── AssertionError.php
   │  │     │    ├── Description.php
   │  │     │    ├── DiagnosingMatcher.php
   │  │     │    ├── Type
   │  │     │     │  ├── IsScalar.php
   │  │     │     │  ├── IsCallable.php
   │  │     │     │  ├── IsDouble.php
   │  │     │     │  ├── IsString.php
   │  │     │     │  ├── IsObject.php
   │  │     │     │  ├── IsResource.php
   │  │     │     │  ├── IsNumeric.php
   │  │     │     │  ├── IsArray.php
   │  │     │     │  ├── IsInteger.php
   │  │     │     │  └── IsBoolean.php
   │  │     │    ├── Matcher.php
   │  │     │    ├── Matchers.php
   │  │     │    ├── Number
   │  │     │     │  ├── IsCloseTo.php
   │  │     │     │  └── OrderingComparison.php
   │  │     │    ├── TypeSafeMatcher.php
   │  │     │    ├── BaseMatcher.php
   │  │     │    ├── SelfDescribing.php
   │  │     │    ├── NullDescription.php
   │  │     │    ├── TypeSafeDiagnosingMatcher.php
   │  │     │    └── Internal
   │  │     │       └── SelfDescribingValue.php
   │  │    ├── CONTRIBUTING.md
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── generator
   │  │     │  ├── FactoryFile.php
   │  │     │  ├── FactoryClass.php
   │  │     │  ├── FactoryCall.php
   │  │     │  ├── StaticMethodFile.php
   │  │     │  ├── run.php
   │  │     │  ├── FactoryGenerator.php
   │  │     │  ├── FactoryMethod.php
   │  │     │  ├── FactoryParameter.php
   │  │     │  ├── GlobalFunctionFile.php
   │  │     │  └── parts
   │  │     │    ├── matchers_imports.txt
   │  │     │    ├── matchers_footer.txt
   │  │     │    ├── functions_imports.txt
   │  │     │    ├── matchers_header.txt
   │  │     │    ├── functions_header.txt
   │  │     │    ├── functions_footer.txt
   │  │     │    └── file_header.txt
   │  │    ├── LICENSE.txt
   │  │    └── CHANGES.txt
   │  ├── monolog
   │  │  └── monolog
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  └── Monolog
   │  │     │    ├── Level.php
   │  │     │    ├── ResettableInterface.php
   │  │     │    ├── Attribute
   │  │     │     │  ├── WithMonologChannel.php
   │  │     │     │  └── AsMonologProcessor.php
   │  │     │    ├── Formatter
   │  │     │     │  ├── GelfMessageFormatter.php
   │  │     │     │  ├── SyslogFormatter.php
   │  │     │     │  ├── WildfireFormatter.php
   │  │     │     │  ├── LineFormatter.php
   │  │     │     │  ├── LogstashFormatter.php
   │  │     │     │  ├── ChromePHPFormatter.php
   │  │     │     │  ├── GoogleCloudLoggingFormatter.php
   │  │     │     │  ├── ElasticaFormatter.php
   │  │     │     │  ├── FluentdFormatter.php
   │  │     │     │  ├── ScalarFormatter.php
   │  │     │     │  ├── HtmlFormatter.php
   │  │     │     │  ├── NormalizerFormatter.php
   │  │     │     │  ├── LogmaticFormatter.php
   │  │     │     │  ├── ElasticsearchFormatter.php
   │  │     │     │  ├── FormatterInterface.php
   │  │     │     │  ├── MongoDBFormatter.php
   │  │     │     │  ├── FlowdockFormatter.php
   │  │     │     │  ├── LogglyFormatter.php
   │  │     │     │  └── JsonFormatter.php
   │  │     │    ├── Registry.php
   │  │     │    ├── JsonSerializableDateTimeImmutable.php
   │  │     │    ├── Test
   │  │     │     │  ├── TestCase.php
   │  │     │     │  └── MonologTestCase.php
   │  │     │    ├── Handler
   │  │     │     │  ├── MongoDBHandler.php
   │  │     │     │  ├── CouchDBHandler.php
   │  │     │     │  ├── TelegramBotHandler.php
   │  │     │     │  ├── ChromePHPHandler.php
   │  │     │     │  ├── SlackWebhookHandler.php
   │  │     │     │  ├── TestHandler.php
   │  │     │     │  ├── FirePHPHandler.php
   │  │     │     │  ├── SyslogHandler.php
   │  │     │     │  ├── PushoverHandler.php
   │  │     │     │  ├── NativeMailerHandler.php
   │  │     │     │  ├── InsightOpsHandler.php
   │  │     │     │  ├── FilterHandler.php
   │  │     │     │  ├── WebRequestRecognizerTrait.php
   │  │     │     │  ├── DynamoDbHandler.php
   │  │     │     │  ├── DeduplicationHandler.php
   │  │     │     │  ├── OverflowHandler.php
   │  │     │     │  ├── BufferHandler.php
   │  │     │     │  ├── HandlerWrapper.php
   │  │     │     │  ├── ElasticsearchHandler.php
   │  │     │     │  ├── GroupHandler.php
   │  │     │     │  ├── FlowdockHandler.php
   │  │     │     │  ├── RedisHandler.php
   │  │     │     │  ├── ProcessableHandlerInterface.php
   │  │     │     │  ├── NoopHandler.php
   │  │     │     │  ├── ErrorLogHandler.php
   │  │     │     │  ├── ProcessableHandlerTrait.php
   │  │     │     │  ├── SendGridHandler.php
   │  │     │     │  ├── Handler.php
   │  │     │     │  ├── PsrHandler.php
   │  │     │     │  ├── AbstractProcessingHandler.php
   │  │     │     │  ├── PHPConsoleHandler.php
   │  │     │     │  ├── LogglyHandler.php
   │  │     │     │  ├── FormattableHandlerTrait.php
   │  │     │     │  ├── GelfHandler.php
   │  │     │     │  ├── DoctrineCouchDBHandler.php
   │  │     │     │  ├── FormattableHandlerInterface.php
   │  │     │     │  ├── NewRelicHandler.php
   │  │     │     │  ├── RotatingFileHandler.php
   │  │     │     │  ├── FallbackGroupHandler.php
   │  │     │     │  ├── MailHandler.php
   │  │     │     │  ├── NullHandler.php
   │  │     │     │  ├── SymfonyMailerHandler.php
   │  │     │     │  ├── RollbarHandler.php
   │  │     │     │  ├── SyslogUdpHandler.php
   │  │     │     │  ├── Slack
   │  │     │     │  │  └── SlackRecord.php
   │  │     │     │  ├── FingersCrossed
   │  │     │     │  │  ├── ChannelLevelActivationStrategy.php
   │  │     │     │  │  ├── ErrorLevelActivationStrategy.php
   │  │     │     │  │  └── ActivationStrategyInterface.php
   │  │     │     │  ├── MandrillHandler.php
   │  │     │     │  ├── WhatFailureGroupHandler.php
   │  │     │     │  ├── ElasticaHandler.php
   │  │     │     │  ├── StreamHandler.php
   │  │     │     │  ├── Curl
   │  │     │     │  │  └── Util.php
   │  │     │     │  ├── FleepHookHandler.php
   │  │     │     │  ├── IFTTTHandler.php
   │  │     │     │  ├── SqsHandler.php
   │  │     │     │  ├── CubeHandler.php
   │  │     │     │  ├── RedisPubSubHandler.php
   │  │     │     │  ├── AbstractHandler.php
   │  │     │     │  ├── LogEntriesHandler.php
   │  │     │     │  ├── ProcessHandler.php
   │  │     │     │  ├── SocketHandler.php
   │  │     │     │  ├── MissingExtensionException.php
   │  │     │     │  ├── BrowserConsoleHandler.php
   │  │     │     │  ├── SlackHandler.php
   │  │     │     │  ├── AbstractSyslogHandler.php
   │  │     │     │  ├── HandlerInterface.php
   │  │     │     │  ├── SyslogUdp
   │  │     │     │  │  └── UdpSocket.php
   │  │     │     │  ├── AmqpHandler.php
   │  │     │     │  ├── SamplingHandler.php
   │  │     │     │  ├── FingersCrossedHandler.php
   │  │     │     │  ├── LogmaticHandler.php
   │  │     │     │  └── ZendMonitorHandler.php
   │  │     │    ├── LogRecord.php
   │  │     │    ├── Utils.php
   │  │     │    ├── SignalHandler.php
   │  │     │    ├── Logger.php
   │  │     │    ├── DateTimeImmutable.php
   │  │     │    ├── Processor
   │  │     │     │  ├── HostnameProcessor.php
   │  │     │     │  ├── MemoryPeakUsageProcessor.php
   │  │     │     │  ├── WebProcessor.php
   │  │     │     │  ├── GitProcessor.php
   │  │     │     │  ├── TagProcessor.php
   │  │     │     │  ├── ProcessorInterface.php
   │  │     │     │  ├── LoadAverageProcessor.php
   │  │     │     │  ├── ProcessIdProcessor.php
   │  │     │     │  ├── IntrospectionProcessor.php
   │  │     │     │  ├── UidProcessor.php
   │  │     │     │  ├── ClosureContextProcessor.php
   │  │     │     │  ├── MemoryUsageProcessor.php
   │  │     │     │  ├── MercurialProcessor.php
   │  │     │     │  ├── PsrLogMessageProcessor.php
   │  │     │     │  └── MemoryProcessor.php
   │  │     │    └── ErrorHandler.php
   │  │    ├── CHANGELOG.md
   │  │    └── LICENSE
   │  ├── nette
   │  │  ├── schema
   │  │  │  ├── composer.json
   │  │  │  ├── license.md
   │  │  │  ├── src
   │  │  │  │  └── Schema
   │  │  │  │    ├── Message.php
   │  │  │  │    ├── Elements
   │  │  │  │     │  ├── Type.php
   │  │  │  │     │  ├── Base.php
   │  │  │  │     │  ├── AnyOf.php
   │  │  │  │     │  └── Structure.php
   │  │  │  │    ├── Processor.php
   │  │  │  │    ├── ValidationException.php
   │  │  │  │    ├── Helpers.php
   │  │  │  │    ├── DynamicParameter.php
   │  │  │  │    ├── Schema.php
   │  │  │  │    ├── Expect.php
   │  │  │  │    └── Context.php
   │  │  │  └── readme.md
   │  │  └── utils
   │  │    ├── composer.json
   │  │    ├── license.md
   │  │    ├── src
   │  │     │  ├── HtmlStringable.php
   │  │     │  ├── SmartObject.php
   │  │     │  ├── StaticClass.php
   │  │     │  ├── exceptions.php
   │  │     │  ├── Translator.php
   │  │     │  ├── Iterators
   │  │     │  │  ├── CachingIterator.php
   │  │     │  │  └── Mapper.php
   │  │     │  ├── compatibility.php
   │  │     │  └── Utils
   │  │     │    ├── Validators.php
   │  │     │    ├── Type.php
   │  │     │    ├── Reflection.php
   │  │     │    ├── ObjectHelpers.php
   │  │     │    ├── Iterables.php
   │  │     │    ├── Callback.php
   │  │     │    ├── Json.php
   │  │     │    ├── Html.php
   │  │     │    ├── ArrayHash.php
   │  │     │    ├── DateTime.php
   │  │     │    ├── exceptions.php
   │  │     │    ├── ImageColor.php
   │  │     │    ├── Helpers.php
   │  │     │    ├── Arrays.php
   │  │     │    ├── Floats.php
   │  │     │    ├── ReflectionMethod.php
   │  │     │    ├── FileSystem.php
   │  │     │    ├── Finder.php
   │  │     │    ├── Paginator.php
   │  │     │    ├── Random.php
   │  │     │    ├── Strings.php
   │  │     │    ├── ImageType.php
   │  │     │    ├── FileInfo.php
   │  │     │    ├── Image.php
   │  │     │    └── ArrayList.php
   │  │    └── readme.md
   │  ├── autoload.php
   │  ├── fruitcake
   │  │  └── php-cors
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── Exceptions
   │  │     │  │  └── InvalidOptionException.php
   │  │     │  └── CorsService.php
   │  │    └── LICENSE
   │  ├── fakerphp
   │  │  └── faker
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── autoload.php
   │  │     │  └── Faker
   │  │     │    ├── Container
   │  │     │     │  ├── NotInContainerException.php
   │  │     │     │  ├── ContainerBuilder.php
   │  │     │     │  ├── ContainerException.php
   │  │     │     │  ├── Container.php
   │  │     │     │  └── ContainerInterface.php
   │  │     │    ├── Core
   │  │     │     │  ├── File.php
   │  │     │     │  ├── Coordinates.php
   │  │     │     │  ├── DateTime.php
   │  │     │     │  ├── Barcode.php
   │  │     │     │  ├── Uuid.php
   │  │     │     │  ├── Number.php
   │  │     │     │  ├── Version.php
   │  │     │     │  ├── Blood.php
   │  │     │     │  └── Color.php
   │  │     │    ├── Documentor.php
   │  │     │    ├── Provider
   │  │     │     │  ├── UserAgent.php
   │  │     │     │  ├── et_EE
   │  │     │     │  │  └── Person.php
   │  │     │     │  ├── Lorem.php
   │  │     │     │  ├── de_CH
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── fr_CA
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Color.php
   │  │     │     │  ├── ms_MY
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Miscellaneous.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── PhoneNumber.php
   │  │     │     │  ├── bn_BD
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Utils.php
   │  │     │     │  ├── Medical.php
   │  │     │     │  ├── hu_HU
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── File.php
   │  │     │     │  ├── en_NG
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  └── Address.php
   │  │     │     │  ├── uk_UA
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── Person.php
   │  │     │     │  ├── me_ME
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── lt_LT
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── nb_NO
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── sr_RS
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── cs_CZ
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── DateTime.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── ka_GE
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── DateTime.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── Text.php
   │  │     │     │  ├── de_DE
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── ro_RO
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── es_ES
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── HtmlLorem.php
   │  │     │     │  ├── es_VE
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Company.php
   │  │     │     │  ├── en_GB
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── el_GR
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── is_IS
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── ar_EG
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── en_PH
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  └── Address.php
   │  │     │     │  ├── sr_Cyrl_RS
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── zh_CN
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── DateTime.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── ro_MD
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── bg_BG
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── lv_LV
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── DateTime.php
   │  │     │     │  ├── fr_BE
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── it_IT
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── ja_JP
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Company.php
   │  │     │     │  ├── pl_PL
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── LicensePlate.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── en_AU
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  └── Address.php
   │  │     │     │  ├── ne_NP
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── de_AT
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── th_TH
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── pt_PT
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── Base.php
   │  │     │     │  ├── fr_FR
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── ar_SA
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── Miscellaneous.php
   │  │     │     │  ├── pt_BR
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── check_digit.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── en_SG
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  └── Address.php
   │  │     │     │  ├── Barcode.php
   │  │     │     │  ├── fa_IR
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Company.php
   │  │     │     │  ├── Internet.php
   │  │     │     │  ├── sv_SE
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Municipality.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── fi_FI
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── da_DK
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── sr_Latn_RS
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── id_ID
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Color.php
   │  │     │     │  ├── Uuid.php
   │  │     │     │  ├── vi_VN
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Color.php
   │  │     │     │  ├── es_PE
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Company.php
   │  │     │     │  ├── sk_SK
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── Address.php
   │  │     │     │  ├── Biased.php
   │  │     │     │  ├── nl_NL
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── ru_RU
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── ko_KR
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Company.php
   │  │     │     │  ├── en_ZA
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Company.php
   │  │     │     │  ├── Company.php
   │  │     │     │  ├── en_CA
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  └── Address.php
   │  │     │     │  ├── at_AT
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── fr_CH
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── ar_JO
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Company.php
   │  │     │     │  ├── en_UG
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  └── Address.php
   │  │     │     │  ├── en_US
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── hy_AM
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Color.php
   │  │     │     │  ├── sl_SI
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── en_NZ
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  └── Address.php
   │  │     │     │  ├── es_AR
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  └── Company.php
   │  │     │     │  ├── hr_HR
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── mn_MN
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  └── Person.php
   │  │     │     │  ├── Color.php
   │  │     │     │  ├── en_HK
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  └── Address.php
   │  │     │     │  ├── el_CY
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── en_IN
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  └── Address.php
   │  │     │     │  ├── kk_KZ
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── tr_TR
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── DateTime.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── nl_BE
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── it_CH
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── zh_TW
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Text.php
   │  │     │     │  │  ├── DateTime.php
   │  │     │     │  │  ├── Internet.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  ├── Color.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── he_IL
   │  │     │     │  │  ├── PhoneNumber.php
   │  │     │     │  │  ├── Person.php
   │  │     │     │  │  ├── Address.php
   │  │     │     │  │  ├── Company.php
   │  │     │     │  │  └── Payment.php
   │  │     │     │  ├── Image.php
   │  │     │     │  └── Payment.php
   │  │     │    ├── Generator.php
   │  │     │    ├── ChanceGenerator.php
   │  │     │    ├── ORM
   │  │     │     │  ├── Propel
   │  │     │     │  │  ├── ColumnTypeGuesser.php
   │  │     │     │  │  ├── Populator.php
   │  │     │     │  │  └── EntityPopulator.php
   │  │     │     │  ├── CakePHP
   │  │     │     │  │  ├── ColumnTypeGuesser.php
   │  │     │     │  │  ├── Populator.php
   │  │     │     │  │  └── EntityPopulator.php
   │  │     │     │  ├── Mandango
   │  │     │     │  │  ├── ColumnTypeGuesser.php
   │  │     │     │  │  ├── Populator.php
   │  │     │     │  │  └── EntityPopulator.php
   │  │     │     │  ├── Doctrine
   │  │     │     │  │  ├── ColumnTypeGuesser.php
   │  │     │     │  │  ├── backward-compatibility.php
   │  │     │     │  │  ├── Populator.php
   │  │     │     │  │  └── EntityPopulator.php
   │  │     │     │  ├── Propel2
   │  │     │     │  │  ├── ColumnTypeGuesser.php
   │  │     │     │  │  ├── Populator.php
   │  │     │     │  │  └── EntityPopulator.php
   │  │     │     │  └── Spot
   │  │     │     │    ├── ColumnTypeGuesser.php
   │  │     │     │    ├── Populator.php
   │  │     │     │    └── EntityPopulator.php
   │  │     │    ├── ValidGenerator.php
   │  │     │    ├── Factory.php
   │  │     │    ├── DefaultGenerator.php
   │  │     │    ├── UniqueGenerator.php
   │  │     │    ├── Guesser
   │  │     │     │  └── Name.php
   │  │     │    ├── Extension
   │  │     │     │  ├── PhoneNumberExtension.php
   │  │     │     │  ├── Extension.php
   │  │     │     │  ├── BarcodeExtension.php
   │  │     │     │  ├── BloodExtension.php
   │  │     │     │  ├── UuidExtension.php
   │  │     │     │  ├── PersonExtension.php
   │  │     │     │  ├── Helper.php
   │  │     │     │  ├── ColorExtension.php
   │  │     │     │  ├── ExtensionNotFound.php
   │  │     │     │  ├── FileExtension.php
   │  │     │     │  ├── NumberExtension.php
   │  │     │     │  ├── AddressExtension.php
   │  │     │     │  ├── DateTimeExtension.php
   │  │     │     │  ├── GeneratorAwareExtension.php
   │  │     │     │  ├── CountryExtension.php
   │  │     │     │  ├── GeneratorAwareExtensionTrait.php
   │  │     │     │  ├── VersionExtension.php
   │  │     │     │  └── CompanyExtension.php
   │  │     │    └── Calculator
   │  │     │       ├── Inn.php
   │  │     │       ├── Iban.php
   │  │     │       ├── Luhn.php
   │  │     │       ├── TCNo.php
   │  │     │       ├── Isbn.php
   │  │     │       └── Ean.php
   │  │    ├── CHANGELOG.md
   │  │    ├── LICENSE
   │  │    └── rector-migrate.php
   │  ├── nikic
   │  │  └── php-parser
   │  │    ├── composer.json
   │  │    ├── bin
   │  │     │  └── php-parse
   │  │    ├── README.md
   │  │    ├── lib
   │  │     │  └── PhpParser
   │  │     │    ├── NodeTraverserInterface.php
   │  │     │    ├── ParserFactory.php
   │  │     │    ├── PhpVersion.php
   │  │     │    ├── ErrorHandler
   │  │     │     │  ├── Collecting.php
   │  │     │     │  └── Throwing.php
   │  │     │    ├── ConstExprEvaluator.php
   │  │     │    ├── Node.php
   │  │     │    ├── PrettyPrinter
   │  │     │     │  └── Standard.php
   │  │     │    ├── ConstExprEvaluationException.php
   │  │     │    ├── NodeVisitor.php
   │  │     │    ├── PrettyPrinterAbstract.php
   │  │     │    ├── Lexer.php
   │  │     │    ├── Token.php
   │  │     │    ├── Modifiers.php
   │  │     │    ├── Lexer
   │  │     │     │  ├── TokenEmulator
   │  │     │     │  │  ├── ReadonlyTokenEmulator.php
   │  │     │     │  │  ├── ExplicitOctalEmulator.php
   │  │     │     │  │  ├── MatchTokenEmulator.php
   │  │     │     │  │  ├── AttributeEmulator.php
   │  │     │     │  │  ├── PipeOperatorEmulator.php
   │  │     │     │  │  ├── EnumTokenEmulator.php
   │  │     │     │  │  ├── KeywordEmulator.php
   │  │     │     │  │  ├── AsymmetricVisibilityTokenEmulator.php
   │  │     │     │  │  ├── ReverseEmulator.php
   │  │     │     │  │  ├── PropertyTokenEmulator.php
   │  │     │     │  │  ├── VoidCastEmulator.php
   │  │     │     │  │  ├── ReadonlyFunctionTokenEmulator.php
   │  │     │     │  │  ├── TokenEmulator.php
   │  │     │     │  │  └── NullsafeTokenEmulator.php
   │  │     │     │  └── Emulative.php
   │  │     │    ├── Comment
   │  │     │     │  └── Doc.php
   │  │     │    ├── NameContext.php
   │  │     │    ├── JsonDecoder.php
   │  │     │    ├── NodeAbstract.php
   │  │     │    ├── PrettyPrinter.php
   │  │     │    ├── Error.php
   │  │     │    ├── NodeFinder.php
   │  │     │    ├── Builder.php
   │  │     │    ├── ParserAbstract.php
   │  │     │    ├── Builder
   │  │     │     │  ├── Use_.php
   │  │     │     │  ├── EnumCase.php
   │  │     │     │  ├── Interface_.php
   │  │     │     │  ├── FunctionLike.php
   │  │     │     │  ├── Class_.php
   │  │     │     │  ├── ClassConst.php
   │  │     │     │  ├── TraitUseAdaptation.php
   │  │     │     │  ├── Enum_.php
   │  │     │     │  ├── Param.php
   │  │     │     │  ├── Namespace_.php
   │  │     │     │  ├── Declaration.php
   │  │     │     │  ├── Property.php
   │  │     │     │  ├── Method.php
   │  │     │     │  ├── Function_.php
   │  │     │     │  ├── Trait_.php
   │  │     │     │  └── TraitUse.php
   │  │     │    ├── Parser.php
   │  │     │    ├── Node
   │  │     │     │  ├── MatchArm.php
   │  │     │     │  ├── VarLikeIdentifier.php
   │  │     │     │  ├── StaticVar.php
   │  │     │     │  ├── Name.php
   │  │     │     │  ├── ArrayItem.php
   │  │     │     │  ├── NullableType.php
   │  │     │     │  ├── Const_.php
   │  │     │     │  ├── PropertyHook.php
   │  │     │     │  ├── Identifier.php
   │  │     │     │  ├── Stmt.php
   │  │     │     │  ├── Arg.php
   │  │     │     │  ├── FunctionLike.php
   │  │     │     │  ├── IntersectionType.php
   │  │     │     │  ├── Attribute.php
   │  │     │     │  ├── DeclareItem.php
   │  │     │     │  ├── PropertyItem.php
   │  │     │     │  ├── ClosureUse.php
   │  │     │     │  ├── InterpolatedStringPart.php
   │  │     │     │  ├── Param.php
   │  │     │     │  ├── Stmt
   │  │     │     │  │  ├── Use_.php
   │  │     │     │  │  ├── Foreach_.php
   │  │     │     │  │  ├── Continue_.php
   │  │     │     │  │  ├── If_.php
   │  │     │     │  │  ├── StaticVar.php
   │  │     │     │  │  ├── Label.php
   │  │     │     │  │  ├── Catch_.php
   │  │     │     │  │  ├── For_.php
   │  │     │     │  │  ├── EnumCase.php
   │  │     │     │  │  ├── Const_.php
   │  │     │     │  │  ├── Interface_.php
   │  │     │     │  │  ├── Do_.php
   │  │     │     │  │  ├── Case_.php
   │  │     │     │  │  ├── ClassLike.php
   │  │     │     │  │  ├── HaltCompiler.php
   │  │     │     │  │  ├── PropertyProperty.php
   │  │     │     │  │  ├── Block.php
   │  │     │     │  │  ├── Class_.php
   │  │     │     │  │  ├── Goto_.php
   │  │     │     │  │  ├── Unset_.php
   │  │     │     │  │  ├── ClassConst.php
   │  │     │     │  │  ├── TraitUseAdaptation.php
   │  │     │     │  │  ├── Finally_.php
   │  │     │     │  │  ├── Enum_.php
   │  │     │     │  │  ├── TryCatch.php
   │  │     │     │  │  ├── Static_.php
   │  │     │     │  │  ├── Else_.php
   │  │     │     │  │  ├── Echo_.php
   │  │     │     │  │  ├── Switch_.php
   │  │     │     │  │  ├── Namespace_.php
   │  │     │     │  │  ├── ClassMethod.php
   │  │     │     │  │  ├── Property.php
   │  │     │     │  │  ├── Break_.php
   │  │     │     │  │  ├── Global_.php
   │  │     │     │  │  ├── DeclareDeclare.php
   │  │     │     │  │  ├── Expression.php
   │  │     │     │  │  ├── TraitUseAdaptation
   │  │     │     │  │  │  ├── Alias.php
   │  │     │     │  │  │  └── Precedence.php
   │  │     │     │  │  ├── Function_.php
   │  │     │     │  │  ├── ElseIf_.php
   │  │     │     │  │  ├── Trait_.php
   │  │     │     │  │  ├── UseUse.php
   │  │     │     │  │  ├── GroupUse.php
   │  │     │     │  │  ├── Declare_.php
   │  │     │     │  │  ├── Nop.php
   │  │     │     │  │  ├── InlineHTML.php
   │  │     │     │  │  ├── Return_.php
   │  │     │     │  │  ├── TraitUse.php
   │  │     │     │  │  └── While_.php
   │  │     │     │  ├── ComplexType.php
   │  │     │     │  ├── VariadicPlaceholder.php
   │  │     │     │  ├── UseItem.php
   │  │     │     │  ├── AttributeGroup.php
   │  │     │     │  ├── Scalar.php
   │  │     │     │  ├── Expr
   │  │     │     │  │  ├── YieldFrom.php
   │  │     │     │  │  ├── NullsafeMethodCall.php
   │  │     │     │  │  ├── Match_.php
   │  │     │     │  │  ├── Include_.php
   │  │     │     │  │  ├── UnaryMinus.php
   │  │     │     │  │  ├── Cast
   │  │     │     │  │  │  ├── Array_.php
   │  │     │     │  │  │  ├── Double.php
   │  │     │     │  │  │  ├── Unset_.php
   │  │     │     │  │  │  ├── String_.php
   │  │     │     │  │  │  ├── Object_.php
   │  │     │     │  │  │  ├── Void_.php
   │  │     │     │  │  │  ├── Int_.php
   │  │     │     │  │  │  └── Bool_.php
   │  │     │     │  │  ├── PostDec.php
   │  │     │     │  │  ├── Throw_.php
   │  │     │     │  │  ├── ArrayItem.php
   │  │     │     │  │  ├── Empty_.php
   │  │     │     │  │  ├── Instanceof_.php
   │  │     │     │  │  ├── Array_.php
   │  │     │     │  │  ├── AssignOp.php
   │  │     │     │  │  ├── CallLike.php
   │  │     │     │  │  ├── BinaryOp
   │  │     │     │  │  │  ├── SmallerOrEqual.php
   │  │     │     │  │  │  ├── BitwiseXor.php
   │  │     │     │  │  │  ├── ShiftRight.php
   │  │     │     │  │  │  ├── Spaceship.php
   │  │     │     │  │  │  ├── GreaterOrEqual.php
   │  │     │     │  │  │  ├── BitwiseOr.php
   │  │     │     │  │  │  ├── LogicalAnd.php
   │  │     │     │  │  │  ├── Equal.php
   │  │     │     │  │  │  ├── LogicalOr.php
   │  │     │     │  │  │  ├── NotEqual.php
   │  │     │     │  │  │  ├── Pipe.php
   │  │     │     │  │  │  ├── Div.php
   │  │     │     │  │  │  ├── NotIdentical.php
   │  │     │     │  │  │  ├── BooleanOr.php
   │  │     │     │  │  │  ├── Minus.php
   │  │     │     │  │  │  ├── Greater.php
   │  │     │     │  │  │  ├── Mod.php
   │  │     │     │  │  │  ├── LogicalXor.php
   │  │     │     │  │  │  ├── Pow.php
   │  │     │     │  │  │  ├── ShiftLeft.php
   │  │     │     │  │  │  ├── Plus.php
   │  │     │     │  │  │  ├── Identical.php
   │  │     │     │  │  │  ├── Coalesce.php
   │  │     │     │  │  │  ├── Concat.php
   │  │     │     │  │  │  ├── Smaller.php
   │  │     │     │  │  │  ├── Mul.php
   │  │     │     │  │  │  ├── BooleanAnd.php
   │  │     │     │  │  │  └── BitwiseAnd.php
   │  │     │     │  │  ├── NullsafePropertyFetch.php
   │  │     │     │  │  ├── UnaryPlus.php
   │  │     │     │  │  ├── FuncCall.php
   │  │     │     │  │  ├── Exit_.php
   │  │     │     │  │  ├── PostInc.php
   │  │     │     │  │  ├── ClassConstFetch.php
   │  │     │     │  │  ├── BinaryOp.php
   │  │     │     │  │  ├── ArrowFunction.php
   │  │     │     │  │  ├── ErrorSuppress.php
   │  │     │     │  │  ├── ArrayDimFetch.php
   │  │     │     │  │  ├── Clone_.php
   │  │     │     │  │  ├── Print_.php
   │  │     │     │  │  ├── Variable.php
   │  │     │     │  │  ├── ClosureUse.php
   │  │     │     │  │  ├── Closure.php
   │  │     │     │  │  ├── PropertyFetch.php
   │  │     │     │  │  ├── Error.php
   │  │     │     │  │  ├── ConstFetch.php
   │  │     │     │  │  ├── Isset_.php
   │  │     │     │  │  ├── StaticCall.php
   │  │     │     │  │  ├── PreDec.php
   │  │     │     │  │  ├── StaticPropertyFetch.php
   │  │     │     │  │  ├── New_.php
   │  │     │     │  │  ├── AssignOp
   │  │     │     │  │  │  ├── BitwiseXor.php
   │  │     │     │  │  │  ├── ShiftRight.php
   │  │     │     │  │  │  ├── BitwiseOr.php
   │  │     │     │  │  │  ├── Div.php
   │  │     │     │  │  │  ├── Minus.php
   │  │     │     │  │  │  ├── Mod.php
   │  │     │     │  │  │  ├── Pow.php
   │  │     │     │  │  │  ├── ShiftLeft.php
   │  │     │     │  │  │  ├── Plus.php
   │  │     │     │  │  │  ├── Coalesce.php
   │  │     │     │  │  │  ├── Concat.php
   │  │     │     │  │  │  ├── Mul.php
   │  │     │     │  │  │  └── BitwiseAnd.php
   │  │     │     │  │  ├── BitwiseNot.php
   │  │     │     │  │  ├── List_.php
   │  │     │     │  │  ├── Eval_.php
   │  │     │     │  │  ├── PreInc.php
   │  │     │     │  │  ├── Ternary.php
   │  │     │     │  │  ├── AssignRef.php
   │  │     │     │  │  ├── MethodCall.php
   │  │     │     │  │  ├── Cast.php
   │  │     │     │  │  ├── BooleanNot.php
   │  │     │     │  │  ├── ShellExec.php
   │  │     │     │  │  ├── Yield_.php
   │  │     │     │  │  └── Assign.php
   │  │     │     │  ├── UnionType.php
   │  │     │     │  ├── Expr.php
   │  │     │     │  ├── Scalar
   │  │     │     │  │  ├── EncapsedStringPart.php
   │  │     │     │  │  ├── DNumber.php
   │  │     │     │  │  ├── Float_.php
   │  │     │     │  │  ├── LNumber.php
   │  │     │     │  │  ├── MagicConst.php
   │  │     │     │  │  ├── String_.php
   │  │     │     │  │  ├── InterpolatedString.php
   │  │     │     │  │  ├── Encapsed.php
   │  │     │     │  │  ├── MagicConst
   │  │     │     │  │  │  ├── File.php
   │  │     │     │  │  │  ├── Dir.php
   │  │     │     │  │  │  ├── Class_.php
   │  │     │     │  │  │  ├── Namespace_.php
   │  │     │     │  │  │  ├── Property.php
   │  │     │     │  │  │  ├── Method.php
   │  │     │     │  │  │  ├── Function_.php
   │  │     │     │  │  │  ├── Trait_.php
   │  │     │     │  │  │  └── Line.php
   │  │     │     │  │  └── Int_.php
   │  │     │     │  └── Name
   │  │     │     │    ├── FullyQualified.php
   │  │     │     │    └── Relative.php
   │  │     │    ├── compatibility_tokens.php
   │  │     │    ├── NodeTraverser.php
   │  │     │    ├── NodeDumper.php
   │  │     │    ├── Comment.php
   │  │     │    ├── BuilderHelpers.php
   │  │     │    ├── NodeVisitor
   │  │     │     │  ├── NodeConnectingVisitor.php
   │  │     │     │  ├── NameResolver.php
   │  │     │     │  ├── FindingVisitor.php
   │  │     │     │  ├── CloningVisitor.php
   │  │     │     │  ├── FirstFindingVisitor.php
   │  │     │     │  ├── CommentAnnotatingVisitor.php
   │  │     │     │  └── ParentConnectingVisitor.php
   │  │     │    ├── Internal
   │  │     │     │  ├── DiffElem.php
   │  │     │     │  ├── TokenStream.php
   │  │     │     │  ├── TokenPolyfill.php
   │  │     │     │  ├── PrintableNewAnonClassNode.php
   │  │     │     │  └── Differ.php
   │  │     │    ├── Parser
   │  │     │     │  ├── Php7.php
   │  │     │     │  └── Php8.php
   │  │     │    ├── BuilderFactory.php
   │  │     │    ├── ErrorHandler.php
   │  │     │    └── NodeVisitorAbstract.php
   │  │    └── LICENSE
   │  ├── egulias
   │  │  └── email-validator
   │  │    ├── CONTRIBUTING.md
   │  │    ├── composer.json
   │  │    ├── src
   │  │     │  ├── EmailParser.php
   │  │     │  ├── EmailValidator.php
   │  │     │  ├── EmailLexer.php
   │  │     │  ├── Warning
   │  │     │  │  ├── IPV6ColonEnd.php
   │  │     │  │  ├── ObsoleteDTEXT.php
   │  │     │  │  ├── IPV6Deprecated.php
   │  │     │  │  ├── DomainLiteral.php
   │  │     │  │  ├── QuotedString.php
   │  │     │  │  ├── CFWSWithFWS.php
   │  │     │  │  ├── NoDNSMXRecord.php
   │  │     │  │  ├── AddressLiteral.php
   │  │     │  │  ├── IPV6MaxGroups.php
   │  │     │  │  ├── IPV6BadChar.php
   │  │     │  │  ├── TLD.php
   │  │     │  │  ├── Warning.php
   │  │     │  │  ├── IPV6GroupCount.php
   │  │     │  │  ├── LocalTooLong.php
   │  │     │  │  ├── DeprecatedComment.php
   │  │     │  │  ├── Comment.php
   │  │     │  │  ├── CFWSNearAt.php
   │  │     │  │  ├── IPV6ColonStart.php
   │  │     │  │  ├── IPV6DoubleColon.php
   │  │     │  │  ├── EmailTooLong.php
   │  │     │  │  └── QuotedPart.php
   │  │     │  ├── Validation
   │  │     │  │  ├── DNSCheckValidation.php
   │  │     │  │  ├── RFCValidation.php
   │  │     │  │  ├── NoRFCWarningsValidation.php
   │  │     │  │  ├── MessageIDValidation.php
   │  │     │  │  ├── Exception
   │  │     │  │  │  └── EmptyValidationList.php
   │  │     │  │  ├── DNSGetRecordWrapper.php
   │  │     │  │  ├── Extra
   │  │     │  │  │  └── SpoofCheckValidation.php
   │  │     │  │  ├── DNSRecords.php
   │  │     │  │  ├── MultipleValidationWithAnd.php
   │  │     │  │  └── EmailValidation.php
   │  │     │  ├── Parser.php
   │  │     │  ├── MessageIDParser.php
   │  │     │  ├── Result
   │  │     │  │  ├── SpoofEmail.php
   │  │     │  │  ├── Reason
   │  │     │  │  │  ├── ExpectingCTEXT.php
   │  │     │  │  │  ├── AtextAfterCFWS.php
   │  │     │  │  │  ├── UnclosedComment.php
   │  │     │  │  │  ├── NoDNSRecord.php
   │  │     │  │  │  ├── ConsecutiveAt.php
   │  │     │  │  │  ├── SpoofEmail.php
   │  │     │  │  │  ├── UnOpenedComment.php
   │  │     │  │  │  ├── NoLocalPart.php
   │  │     │  │  │  ├── DotAtStart.php
   │  │     │  │  │  ├── RFCWarnings.php
   │  │     │  │  │  ├── ExpectingDomainLiteralClose.php
   │  │     │  │  │  ├── CRNoLF.php
   │  │     │  │  │  ├── DetailedReason.php
   │  │     │  │  │  ├── DotAtEnd.php
   │  │     │  │  │  ├── NoDomainPart.php
   │  │     │  │  │  ├── UnableToGetDNSRecord.php
   │  │     │  │  │  ├── CRLFAtTheEnd.php
   │  │     │  │  │  ├── DomainTooLong.php
   │  │     │  │  │  ├── EmptyReason.php
   │  │     │  │  │  ├── ExpectingATEXT.php
   │  │     │  │  │  ├── DomainHyphened.php
   │  │     │  │  │  ├── ExpectingDTEXT.php
   │  │     │  │  │  ├── LabelTooLong.php
   │  │     │  │  │  ├── UnclosedQuotedString.php
   │  │     │  │  │  ├── DomainAcceptsNoMail.php
   │  │     │  │  │  ├── Reason.php
   │  │     │  │  │  ├── CommaInDomain.php
   │  │     │  │  │  ├── CRLFX2.php
   │  │     │  │  │  ├── CommentsInIDRight.php
   │  │     │  │  │  ├── UnusualElements.php
   │  │     │  │  │  ├── CharNotAllowed.php
   │  │     │  │  │  ├── ExceptionFound.php
   │  │     │  │  │  ├── ConsecutiveDot.php
   │  │     │  │  │  └── LocalOrReservedDomain.php
   │  │     │  │  ├── InvalidEmail.php
   │  │     │  │  ├── Result.php
   │  │     │  │  ├── ValidEmail.php
   │  │     │  │  └── MultipleErrors.php
   │  │     │  └── Parser
   │  │     │    ├── LocalPart.php
   │  │     │    ├── DoubleQuote.php
   │  │     │    ├── DomainLiteral.php
   │  │     │    ├── DomainPart.php
   │  │     │    ├── FoldingWhiteSpace.php
   │  │     │    ├── IDRightPart.php
   │  │     │    ├── IDLeftPart.php
   │  │     │    ├── Comment.php
   │  │     │    ├── CommentStrategy
   │  │     │     │  ├── LocalComment.php
   │  │     │     │  ├── DomainComment.php
   │  │     │     │  └── CommentStrategy.php
   │  │     │    └── PartParser.php
   │  │    └── LICENSE
   │  ├── graham-campbell
   │  │  └── result-type
   │  │    ├── composer.json
   │  │    ├── src
   │  │     │  ├── Success.php
   │  │     │  ├── Result.php
   │  │     │  └── Error.php
   │  │    └── LICENSE
   │  ├── composer
   │  │  ├── installed.php
   │  │  ├── installed.json
   │  │  ├── InstalledVersions.php
   │  │  ├── autoload_static.php
   │  │  ├── LICENSE
   │  │  ├── autoload_files.php
   │  │  ├── autoload_psr4.php
   │  │  ├── autoload_classmap.php
   │  │  ├── ClassLoader.php
   │  │  ├── autoload_real.php
   │  │  ├── platform_check.php
   │  │  └── autoload_namespaces.php
   │  ├── bin
   │  │  ├── php-parse
   │  │  ├── sail
   │  │  ├── var-dump-server
   │  │  ├── yaml-lint
   │  │  ├── carbon
   │  │  ├── psysh
   │  │  ├── pint
   │  │  ├── phpunit
   │  │  └── patch-type-declarations
   │  ├── voku
   │  │  └── portable-ascii
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  └── voku
   │  │     │    └── helper
   │  │     │       ├── ASCII.php
   │  │     │       └── data
   │  │     │          ├── x0ac.php
   │  │     │          ├── x0fd.php
   │  │     │          ├── x023.php
   │  │     │          ├── x01f.php
   │  │     │          ├── x0c5.php
   │  │     │          ├── x0c0.php
   │  │     │          ├── x0b5.php
   │  │     │          ├── x059.php
   │  │     │          ├── x078.php
   │  │     │          ├── x098.php
   │  │     │          ├── x09c.php
   │  │     │          ├── x027.php
   │  │     │          ├── x032.php
   │  │     │          ├── x021.php
   │  │     │          ├── x031.php
   │  │     │          ├── x0d3.php
   │  │     │          ├── x015.php
   │  │     │          ├── x065.php
   │  │     │          ├── x0af.php
   │  │     │          ├── x09f.php
   │  │     │          ├── x0c8.php
   │  │     │          ├── x0b8.php
   │  │     │          ├── x0ae.php
   │  │     │          ├── x0fa.php
   │  │     │          ├── x06d.php
   │  │     │          ├── x084.php
   │  │     │          ├── x012.php
   │  │     │          ├── x007.php
   │  │     │          ├── x0ad.php
   │  │     │          ├── x024.php
   │  │     │          ├── x00a.php
   │  │     │          ├── x06c.php
   │  │     │          ├── x095.php
   │  │     │          ├── x09e.php
   │  │     │          ├── x022.php
   │  │     │          ├── x0cd.php
   │  │     │          ├── x0b1.php
   │  │     │          ├── x051.php
   │  │     │          ├── x0c7.php
   │  │     │          ├── x099.php
   │  │     │          ├── x085.php
   │  │     │          ├── x033.php
   │  │     │          ├── x08f.php
   │  │     │          ├── x0cc.php
   │  │     │          ├── x08c.php
   │  │     │          ├── x025.php
   │  │     │          ├── x08a.php
   │  │     │          ├── x0c2.php
   │  │     │          ├── x1d4.php
   │  │     │          ├── x08e.php
   │  │     │          ├── x0d0.php
   │  │     │          ├── x062.php
   │  │     │          ├── x0c9.php
   │  │     │          ├── x0b4.php
   │  │     │          ├── x016.php
   │  │     │          ├── x006.php
   │  │     │          ├── x081.php
   │  │     │          ├── x07f.php
   │  │     │          ├── x026.php
   │  │     │          ├── x000.php
   │  │     │          ├── x0b6.php
   │  │     │          ├── x01d.php
   │  │     │          ├── x072.php
   │  │     │          ├── x0fc.php
   │  │     │          ├── x0a0.php
   │  │     │          ├── ascii_language_max_key.php
   │  │     │          ├── x04f.php
   │  │     │          ├── x058.php
   │  │     │          ├── x05c.php
   │  │     │          ├── x0b0.php
   │  │     │          ├── x0ca.php
   │  │     │          ├── x0bf.php
   │  │     │          ├── x057.php
   │  │     │          ├── x0be.php
   │  │     │          ├── x002.php
   │  │     │          ├── x0a3.php
   │  │     │          ├── ascii_extras_by_languages.php
   │  │     │          ├── x00e.php
   │  │     │          ├── x053.php
   │  │     │          ├── x0b7.php
   │  │     │          ├── x0cb.php
   │  │     │          ├── x05d.php
   │  │     │          ├── x091.php
   │  │     │          ├── x02c.php
   │  │     │          ├── x020.php
   │  │     │          ├── x0fe.php
   │  │     │          ├── x070.php
   │  │     │          ├── x066.php
   │  │     │          ├── x055.php
   │  │     │          ├── x0b3.php
   │  │     │          ├── x05e.php
   │  │     │          ├── x08b.php
   │  │     │          ├── x068.php
   │  │     │          ├── x0fb.php
   │  │     │          ├── x00d.php
   │  │     │          ├── x0d6.php
   │  │     │          ├── x0d2.php
   │  │     │          ├── x0d7.php
   │  │     │          ├── x052.php
   │  │     │          ├── x09d.php
   │  │     │          ├── x001.php
   │  │     │          ├── x087.php
   │  │     │          ├── x0ff.php
   │  │     │          ├── x07c.php
   │  │     │          ├── x088.php
   │  │     │          ├── x089.php
   │  │     │          ├── ascii_ord.php
   │  │     │          ├── x0ba.php
   │  │     │          ├── x05f.php
   │  │     │          ├── x029.php
   │  │     │          ├── x063.php
   │  │     │          ├── x0ce.php
   │  │     │          ├── x003.php
   │  │     │          ├── x02a.php
   │  │     │          ├── x0bc.php
   │  │     │          ├── x04d.php
   │  │     │          ├── x00c.php
   │  │     │          ├── x0c1.php
   │  │     │          ├── x0bd.php
   │  │     │          ├── x097.php
   │  │     │          ├── x086.php
   │  │     │          ├── x010.php
   │  │     │          ├── x06f.php
   │  │     │          ├── x074.php
   │  │     │          ├── x0b2.php
   │  │     │          ├── x013.php
   │  │     │          ├── x0d1.php
   │  │     │          ├── x06a.php
   │  │     │          ├── x073.php
   │  │     │          ├── x06b.php
   │  │     │          ├── x02e.php
   │  │     │          ├── x096.php
   │  │     │          ├── x064.php
   │  │     │          ├── x0bb.php
   │  │     │          ├── x028.php
   │  │     │          ├── x1d5.php
   │  │     │          ├── x02f.php
   │  │     │          ├── x0cf.php
   │  │     │          ├── x018.php
   │  │     │          ├── x004.php
   │  │     │          ├── x07e.php
   │  │     │          ├── x077.php
   │  │     │          ├── x056.php
   │  │     │          ├── x005.php
   │  │     │          ├── x1f1.php
   │  │     │          ├── x05b.php
   │  │     │          ├── ascii_by_languages.php
   │  │     │          ├── x05a.php
   │  │     │          ├── x090.php
   │  │     │          ├── x083.php
   │  │     │          ├── x07b.php
   │  │     │          ├── x075.php
   │  │     │          ├── x011.php
   │  │     │          ├── x0c4.php
   │  │     │          ├── x082.php
   │  │     │          ├── x09b.php
   │  │     │          ├── x04e.php
   │  │     │          ├── x067.php
   │  │     │          ├── x079.php
   │  │     │          ├── x00b.php
   │  │     │          ├── x094.php
   │  │     │          ├── x06e.php
   │  │     │          ├── x076.php
   │  │     │          ├── x0d5.php
   │  │     │          ├── x0f9.php
   │  │     │          ├── x1d7.php
   │  │     │          ├── x050.php
   │  │     │          ├── x069.php
   │  │     │          ├── x054.php
   │  │     │          ├── x060.php
   │  │     │          ├── x071.php
   │  │     │          ├── x014.php
   │  │     │          ├── x009.php
   │  │     │          ├── x01e.php
   │  │     │          ├── x0d4.php
   │  │     │          ├── x0a4.php
   │  │     │          ├── x0a1.php
   │  │     │          ├── x080.php
   │  │     │          ├── x0b9.php
   │  │     │          ├── x0c3.php
   │  │     │          ├── x0a2.php
   │  │     │          ├── x061.php
   │  │     │          ├── x00f.php
   │  │     │          ├── x1d6.php
   │  │     │          ├── x017.php
   │  │     │          ├── x0c6.php
   │  │     │          ├── x092.php
   │  │     │          ├── x09a.php
   │  │     │          ├── x08d.php
   │  │     │          ├── x07d.php
   │  │     │          ├── x093.php
   │  │     │          ├── x030.php
   │  │     │          └── x07a.php
   │  │    ├── CHANGELOG.md
   │  │    └── LICENSE.txt
   │  ├── staabm
   │  │  └── side-effects-detector
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── lib
   │  │     │  ├── functionMetadata.php
   │  │     │  ├── SideEffectsDetector.php
   │  │     │  └── SideEffect.php
   │  │    └── LICENSE
   │  ├── psr
   │  │  ├── event-dispatcher
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── ListenerProviderInterface.php
   │  │  │  │  ├── StoppableEventInterface.php
   │  │  │  │  └── EventDispatcherInterface.php
   │  │  │  └── LICENSE
   │  │  ├── clock
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  └── ClockInterface.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  └── LICENSE
   │  │  ├── simple-cache
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  └── src
   │  │  │    ├── CacheException.php
   │  │  │    ├── CacheInterface.php
   │  │  │    └── InvalidArgumentException.php
   │  │  ├── http-factory
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── ServerRequestFactoryInterface.php
   │  │  │  │  ├── ResponseFactoryInterface.php
   │  │  │  │  ├── UploadedFileFactoryInterface.php
   │  │  │  │  ├── UriFactoryInterface.php
   │  │  │  │  ├── RequestFactoryInterface.php
   │  │  │  │  └── StreamFactoryInterface.php
   │  │  │  └── LICENSE
   │  │  ├── log
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── LogLevel.php
   │  │  │  │  ├── LoggerAwareInterface.php
   │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  ├── AbstractLogger.php
   │  │  │  │  ├── LoggerTrait.php
   │  │  │  │  ├── NullLogger.php
   │  │  │  │  ├── LoggerAwareTrait.php
   │  │  │  │  └── LoggerInterface.php
   │  │  │  └── LICENSE
   │  │  ├── container
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── ContainerExceptionInterface.php
   │  │  │  │  ├── NotFoundExceptionInterface.php
   │  │  │  │  └── ContainerInterface.php
   │  │  │  └── LICENSE
   │  │  ├── http-message
   │  │  │  ├── docs
   │  │  │  │  ├── PSR7-Interfaces.md
   │  │  │  │  └── PSR7-Usage.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── ResponseInterface.php
   │  │  │  │  ├── MessageInterface.php
   │  │  │  │  ├── ServerRequestInterface.php
   │  │  │  │  ├── UploadedFileInterface.php
   │  │  │  │  ├── UriInterface.php
   │  │  │  │  ├── StreamInterface.php
   │  │  │  │  └── RequestInterface.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  └── LICENSE
   │  │  └── http-client
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── ClientInterface.php
   │  │     │  ├── NetworkExceptionInterface.php
   │  │     │  ├── ClientExceptionInterface.php
   │  │     │  └── RequestExceptionInterface.php
   │  │    ├── CHANGELOG.md
   │  │    └── LICENSE
   │  ├── mockery
   │  │  └── mockery
   │  │    ├── docs
   │  │     │  ├── requirements.txt
   │  │     │  ├── index.rst
   │  │     │  ├── reference
   │  │     │  │  ├── creating_test_doubles.rst
   │  │     │  │  ├── pass_by_reference_behaviours.rst
   │  │     │  │  ├── phpunit_integration.rst
   │  │     │  │  ├── instance_mocking.rst
   │  │     │  │  ├── partial_mocks.rst
   │  │     │  │  ├── expectations.rst
   │  │     │  │  ├── index.rst
   │  │     │  │  ├── argument_validation.rst
   │  │     │  │  ├── final_methods_classes.rst
   │  │     │  │  ├── magic_methods.rst
   │  │     │  │  ├── alternative_should_receive_syntax.rst
   │  │     │  │  ├── public_static_properties.rst
   │  │     │  │  ├── demeter_chains.rst
   │  │     │  │  ├── protected_methods.rst
   │  │     │  │  ├── public_properties.rst
   │  │     │  │  ├── map.rst.inc
   │  │     │  │  └── spies.rst
   │  │     │  ├── README.md
   │  │     │  ├── mockery
   │  │     │  │  ├── reserved_method_names.rst
   │  │     │  │  ├── exceptions.rst
   │  │     │  │  ├── gotchas.rst
   │  │     │  │  ├── index.rst
   │  │     │  │  ├── configuration.rst
   │  │     │  │  └── map.rst.inc
   │  │     │  ├── cookbook
   │  │     │  │  ├── default_expectations.rst
   │  │     │  │  ├── big_parent_class.rst
   │  │     │  │  ├── class_constants.rst
   │  │     │  │  ├── mocking_class_within_class.rst
   │  │     │  │  ├── index.rst
   │  │     │  │  ├── not_calling_the_constructor.rst
   │  │     │  │  ├── mockery_on.rst
   │  │     │  │  ├── detecting_mock_objects.rst
   │  │     │  │  ├── mocking_hard_dependencies.rst
   │  │     │  │  └── map.rst.inc
   │  │     │  ├── conf.py
   │  │     │  ├── Makefile
   │  │     │  ├── _static
   │  │     │  └── getting_started
   │  │     │    ├── quick_reference.rst
   │  │     │    ├── simple_example.rst
   │  │     │    ├── index.rst
   │  │     │    ├── upgrading.rst
   │  │     │    ├── installation.rst
   │  │     │    └── map.rst.inc
   │  │    ├── library
   │  │     │  ├── Mockery
   │  │     │  │  ├── CountValidator
   │  │     │  │  │  ├── Exact.php
   │  │     │  │  │  ├── CountValidatorInterface.php
   │  │     │  │  │  ├── CountValidatorAbstract.php
   │  │     │  │  │  ├── AtMost.php
   │  │     │  │  │  ├── Exception.php
   │  │     │  │  │  └── AtLeast.php
   │  │     │  │  ├── VerificationExpectation.php
   │  │     │  │  ├── Container.php
   │  │     │  │  ├── ExpectsHigherOrderMessage.php
   │  │     │  │  ├── Mock.php
   │  │     │  │  ├── Exception
   │  │     │  │  │  ├── NoMatchingExpectationException.php
   │  │     │  │  │  ├── InvalidOrderException.php
   │  │     │  │  │  ├── InvalidCountException.php
   │  │     │  │  │  ├── RuntimeException.php
   │  │     │  │  │  ├── InvalidArgumentException.php
   │  │     │  │  │  ├── BadMethodCallException.php
   │  │     │  │  │  └── MockeryExceptionInterface.php
   │  │     │  │  ├── ExpectationDirector.php
   │  │     │  │  ├── Configuration.php
   │  │     │  │  ├── Matcher
   │  │     │  │  │  ├── NoArgs.php
   │  │     │  │  │  ├── Pattern.php
   │  │     │  │  │  ├── Type.php
   │  │     │  │  │  ├── NotAnyOf.php
   │  │     │  │  │  ├── MatcherInterface.php
   │  │     │  │  │  ├── Any.php
   │  │     │  │  │  ├── MatcherAbstract.php
   │  │     │  │  │  ├── Contains.php
   │  │     │  │  │  ├── IsEqual.php
   │  │     │  │  │  ├── Closure.php
   │  │     │  │  │  ├── Ducktype.php
   │  │     │  │  │  ├── Subset.php
   │  │     │  │  │  ├── MustBe.php
   │  │     │  │  │  ├── MultiArgumentClosure.php
   │  │     │  │  │  ├── AndAnyOtherArgs.php
   │  │     │  │  │  ├── AnyArgs.php
   │  │     │  │  │  ├── AnyOf.php
   │  │     │  │  │  ├── ArgumentListMatcher.php
   │  │     │  │  │  ├── HasKey.php
   │  │     │  │  │  ├── HasValue.php
   │  │     │  │  │  ├── IsSame.php
   │  │     │  │  │  └── Not.php
   │  │     │  │  ├── Exception.php
   │  │     │  │  ├── HigherOrderMessage.php
   │  │     │  │  ├── LegacyMockInterface.php
   │  │     │  │  ├── ReceivedMethodCalls.php
   │  │     │  │  ├── CompositeExpectation.php
   │  │     │  │  ├── MockInterface.php
   │  │     │  │  ├── VerificationDirector.php
   │  │     │  │  ├── ClosureWrapper.php
   │  │     │  │  ├── Instantiator.php
   │  │     │  │  ├── Adapter
   │  │     │  │  │  └── Phpunit
   │  │     │  │  │    ├── MockeryPHPUnitIntegrationAssertPostConditions.php
   │  │     │  │  │    ├── MockeryTestCase.php
   │  │     │  │  │    ├── TestListener.php
   │  │     │  │  │    ├── TestListenerTrait.php
   │  │     │  │  │    ├── MockeryPHPUnitIntegration.php
   │  │     │  │  │    └── MockeryTestCaseSetUp.php
   │  │     │  │  ├── Reflector.php
   │  │     │  │  ├── Undefined.php
   │  │     │  │  ├── Loader
   │  │     │  │  │  ├── EvalLoader.php
   │  │     │  │  │  ├── Loader.php
   │  │     │  │  │  └── RequireLoader.php
   │  │     │  │  ├── MethodCall.php
   │  │     │  │  ├── QuickDefinitionsConfiguration.php
   │  │     │  │  ├── Expectation.php
   │  │     │  │  ├── ExpectationInterface.php
   │  │     │  │  └── Generator
   │  │     │  │    ├── MockConfiguration.php
   │  │     │  │    ├── DefinedTargetClass.php
   │  │     │  │    ├── TargetClassInterface.php
   │  │     │  │    ├── StringManipulation
   │  │     │  │     │  └── Pass
   │  │     │  │     │    ├── RemoveDestructorPass.php
   │  │     │  │     │    ├── ClassAttributesPass.php
   │  │     │  │     │    ├── CallTypeHintPass.php
   │  │     │  │     │    ├── ClassPass.php
   │  │     │  │     │    ├── ConstantsPass.php
   │  │     │  │     │    ├── MethodDefinitionPass.php
   │  │     │  │     │    ├── RemoveUnserializeForInternalSerializableClassesPass.php
   │  │     │  │     │    ├── MagicMethodTypeHintsPass.php
   │  │     │  │     │    ├── InstanceMockPass.php
   │  │     │  │     │    ├── InterfacePass.php
   │  │     │  │     │    ├── ClassNamePass.php
   │  │     │  │     │    ├── AvoidMethodClashPass.php
   │  │     │  │     │    ├── TraitPass.php
   │  │     │  │     │    ├── Pass.php
   │  │     │  │     │    └── RemoveBuiltinMethodsThatAreFinalPass.php
   │  │     │  │    ├── MockDefinition.php
   │  │     │  │    ├── Generator.php
   │  │     │  │    ├── StringManipulationGenerator.php
   │  │     │  │    ├── Parameter.php
   │  │     │  │    ├── MockConfigurationBuilder.php
   │  │     │  │    ├── Method.php
   │  │     │  │    ├── UndefinedTargetClass.php
   │  │     │  │    ├── MockNameBuilder.php
   │  │     │  │    └── CachingGenerator.php
   │  │     │  ├── Mockery.php
   │  │     │  └── helpers.php
   │  │    ├── CONTRIBUTING.md
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── COPYRIGHT.md
   │  │    ├── CHANGELOG.md
   │  │    ├── LICENSE
   │  │    ├── SECURITY.md
   │  │    └── composer.lock
   │  ├── league
   │  │  ├── uri-interfaces
   │  │  │  ├── UriString.php
   │  │  │  ├── HostType.php
   │  │  │  ├── FeatureDetection.php
   │  │  │  ├── KeyValuePair
   │  │  │  │  └── Converter.php
   │  │  │  ├── composer.json
   │  │  │  ├── StringCoercionMode.php
   │  │  │  ├── IPv4
   │  │  │  │  ├── NativeCalculator.php
   │  │  │  │  ├── Calculator.php
   │  │  │  │  ├── BCMathCalculator.php
   │  │  │  │  ├── GMPCalculator.php
   │  │  │  │  └── Converter.php
   │  │  │  ├── HostRecord.php
   │  │  │  ├── QueryExtractMode.php
   │  │  │  ├── UriComparisonMode.php
   │  │  │  ├── IPv6
   │  │  │  │  └── Converter.php
   │  │  │  ├── Contracts
   │  │  │  │  ├── UriComponentInterface.php
   │  │  │  │  ├── SegmentedPathInterface.php
   │  │  │  │  ├── DataPathInterface.php
   │  │  │  │  ├── Conditionable.php
   │  │  │  │  ├── HostInterface.php
   │  │  │  │  ├── PortInterface.php
   │  │  │  │  ├── FragmentDirective.php
   │  │  │  │  ├── UriException.php
   │  │  │  │  ├── QueryInterface.php
   │  │  │  │  ├── UriInterface.php
   │  │  │  │  ├── UriAccess.php
   │  │  │  │  ├── Transformable.php
   │  │  │  │  ├── UserInfoInterface.php
   │  │  │  │  ├── AuthorityInterface.php
   │  │  │  │  ├── PathInterface.php
   │  │  │  │  ├── IpHostInterface.php
   │  │  │  │  ├── DomainHostInterface.php
   │  │  │  │  └── FragmentInterface.php
   │  │  │  ├── LICENSE
   │  │  │  ├── Idna
   │  │  │  │  ├── Converter.php
   │  │  │  │  ├── Result.php
   │  │  │  │  ├── Error.php
   │  │  │  │  └── Option.php
   │  │  │  ├── Exceptions
   │  │  │  │  ├── SyntaxError.php
   │  │  │  │  ├── ConversionFailed.php
   │  │  │  │  ├── OffsetOutOfBounds.php
   │  │  │  │  └── MissingFeature.php
   │  │  │  ├── HostFormat.php
   │  │  │  ├── UrnComparisonMode.php
   │  │  │  ├── QueryString.php
   │  │  │  ├── QueryComposeMode.php
   │  │  │  └── Encoder.php
   │  │  ├── flysystem
   │  │  │  ├── composer.json
   │  │  │  ├── INFO.md
   │  │  │  ├── src
   │  │  │  │  ├── ChecksumAlgoIsNotSupported.php
   │  │  │  │  ├── UnableToProvideChecksum.php
   │  │  │  │  ├── UnableToCreateDirectory.php
   │  │  │  │  ├── PortableVisibilityGuard.php
   │  │  │  │  ├── UnableToDeleteFile.php
   │  │  │  │  ├── Config.php
   │  │  │  │  ├── ChecksumProvider.php
   │  │  │  │  ├── ProxyArrayAccessToProperties.php
   │  │  │  │  ├── Visibility.php
   │  │  │  │  ├── UnableToCheckExistence.php
   │  │  │  │  ├── DecoratedAdapter.php
   │  │  │  │  ├── UnableToReadFile.php
   │  │  │  │  ├── UnableToWriteFile.php
   │  │  │  │  ├── UnableToMoveFile.php
   │  │  │  │  ├── ResolveIdenticalPathConflict.php
   │  │  │  │  ├── PathNormalizer.php
   │  │  │  │  ├── WhitespacePathNormalizer.php
   │  │  │  │  ├── UnableToDeleteDirectory.php
   │  │  │  │  ├── FilesystemReader.php
   │  │  │  │  ├── UnixVisibility
   │  │  │  │  │  ├── VisibilityConverter.php
   │  │  │  │  │  └── PortableVisibilityConverter.php
   │  │  │  │  ├── FilesystemOperationFailed.php
   │  │  │  │  ├── UnableToGeneratePublicUrl.php
   │  │  │  │  ├── UnableToSetVisibility.php
   │  │  │  │  ├── UnreadableFileEncountered.php
   │  │  │  │  ├── UnableToRetrieveMetadata.php
   │  │  │  │  ├── FilesystemOperator.php
   │  │  │  │  ├── PathPrefixer.php
   │  │  │  │  ├── CorruptedPathDetected.php
   │  │  │  │  ├── InvalidVisibilityProvided.php
   │  │  │  │  ├── UnableToCheckDirectoryExistence.php
   │  │  │  │  ├── Filesystem.php
   │  │  │  │  ├── DirectoryAttributes.php
   │  │  │  │  ├── MountManager.php
   │  │  │  │  ├── PathTraversalDetected.php
   │  │  │  │  ├── CalculateChecksumFromStream.php
   │  │  │  │  ├── InvalidStreamProvided.php
   │  │  │  │  ├── DirectoryListing.php
   │  │  │  │  ├── UnableToListContents.php
   │  │  │  │  ├── UnableToGenerateTemporaryUrl.php
   │  │  │  │  ├── UrlGeneration
   │  │  │  │  │  ├── ChainedPublicUrlGenerator.php
   │  │  │  │  │  ├── TemporaryUrlGenerator.php
   │  │  │  │  │  ├── PrefixPublicUrlGenerator.php
   │  │  │  │  │  ├── PublicUrlGenerator.php
   │  │  │  │  │  └── ShardedPrefixPublicUrlGenerator.php
   │  │  │  │  ├── UnableToMountFilesystem.php
   │  │  │  │  ├── FilesystemWriter.php
   │  │  │  │  ├── FileAttributes.php
   │  │  │  │  ├── UnableToCheckFileExistence.php
   │  │  │  │  ├── UnableToCopyFile.php
   │  │  │  │  ├── SymbolicLinkEncountered.php
   │  │  │  │  ├── FilesystemException.php
   │  │  │  │  ├── FilesystemAdapter.php
   │  │  │  │  ├── StorageAttributes.php
   │  │  │  │  └── UnableToResolveFilesystemMount.php
   │  │  │  ├── LICENSE
   │  │  │  └── readme.md
   │  │  ├── mime-type-detection
   │  │  │  ├── composer.json
   │  │  │  ├── src
   │  │  │  │  ├── ExtensionToMimeTypeMap.php
   │  │  │  │  ├── EmptyExtensionToMimeTypeMap.php
   │  │  │  │  ├── ExtensionLookup.php
   │  │  │  │  ├── FinfoMimeTypeDetector.php
   │  │  │  │  ├── OverridingExtensionToMimeTypeMap.php
   │  │  │  │  ├── ExtensionMimeTypeDetector.php
   │  │  │  │  ├── MimeTypeDetector.php
   │  │  │  │  └── GeneratedExtensionToMimeTypeMap.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  └── LICENSE
   │  │  ├── flysystem-local
   │  │  │  ├── LocalFilesystemAdapter.php
   │  │  │  ├── composer.json
   │  │  │  ├── FallbackMimeTypeDetector.php
   │  │  │  └── LICENSE
   │  │  ├── config
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── SchemaBuilderInterface.php
   │  │  │  │  ├── ConfigurationBuilderInterface.php
   │  │  │  │  ├── ReadOnlyConfiguration.php
   │  │  │  │  ├── MutableConfigurationInterface.php
   │  │  │  │  ├── ConfigurationInterface.php
   │  │  │  │  ├── Exception
   │  │  │  │  │  ├── InvalidConfigurationException.php
   │  │  │  │  │  ├── ValidationException.php
   │  │  │  │  │  ├── ConfigurationExceptionInterface.php
   │  │  │  │  │  └── UnknownOptionException.php
   │  │  │  │  ├── Configuration.php
   │  │  │  │  ├── ConfigurationAwareInterface.php
   │  │  │  │  └── ConfigurationProviderInterface.php
   │  │  │  └── CHANGELOG.md
   │  │  ├── uri
   │  │  │  ├── UriInfo.php
   │  │  │  ├── HttpFactory.php
   │  │  │  ├── UriTemplate.php
   │  │  │  ├── UriScheme.php
   │  │  │  ├── composer.json
   │  │  │  ├── Http.php
   │  │  │  ├── Uri.php
   │  │  │  ├── BaseUri.php
   │  │  │  ├── SchemeType.php
   │  │  │  ├── LICENSE
   │  │  │  ├── Builder.php
   │  │  │  ├── UriTemplate
   │  │  │  │  ├── Operator.php
   │  │  │  │  ├── VariableBag.php
   │  │  │  │  ├── Template.php
   │  │  │  │  ├── Expression.php
   │  │  │  │  ├── TemplateCanNotBeExpanded.php
   │  │  │  │  └── VarSpecifier.php
   │  │  │  ├── UriResolver.php
   │  │  │  └── Urn.php
   │  │  └── commonmark
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── Output
   │  │     │  │  ├── RenderedContent.php
   │  │     │  │  └── RenderedContentInterface.php
   │  │     │  ├── Xml
   │  │     │  │  ├── MarkdownToXmlConverter.php
   │  │     │  │  ├── XmlRenderer.php
   │  │     │  │  ├── XmlNodeRendererInterface.php
   │  │     │  │  └── FallbackNodeXmlRenderer.php
   │  │     │  ├── Input
   │  │     │  │  ├── MarkdownInputInterface.php
   │  │     │  │  └── MarkdownInput.php
   │  │     │  ├── Normalizer
   │  │     │  │  ├── UniqueSlugNormalizerInterface.php
   │  │     │  │  ├── TextNormalizer.php
   │  │     │  │  ├── UniqueSlugNormalizer.php
   │  │     │  │  ├── TextNormalizerInterface.php
   │  │     │  │  └── SlugNormalizer.php
   │  │     │  ├── Exception
   │  │     │  │  ├── LogicException.php
   │  │     │  │  ├── AlreadyInitializedException.php
   │  │     │  │  ├── InvalidArgumentException.php
   │  │     │  │  ├── CommonMarkException.php
   │  │     │  │  ├── UnexpectedEncodingException.php
   │  │     │  │  ├── IOException.php
   │  │     │  │  └── MissingDependencyException.php
   │  │     │  ├── Util
   │  │     │  │  ├── HtmlElement.php
   │  │     │  │  ├── UrlEncoder.php
   │  │     │  │  ├── SpecReader.php
   │  │     │  │  ├── Xml.php
   │  │     │  │  ├── LinkParserHelper.php
   │  │     │  │  ├── Html5EntityDecoder.php
   │  │     │  │  ├── PrioritizedList.php
   │  │     │  │  ├── RegexHelper.php
   │  │     │  │  ├── ArrayCollection.php
   │  │     │  │  └── HtmlFilter.php
   │  │     │  ├── Renderer
   │  │     │  │  ├── HtmlRenderer.php
   │  │     │  │  ├── DocumentRendererInterface.php
   │  │     │  │  ├── Inline
   │  │     │  │  │  ├── TextRenderer.php
   │  │     │  │  │  └── NewlineRenderer.php
   │  │     │  │  ├── Block
   │  │     │  │  │  ├── ParagraphRenderer.php
   │  │     │  │  │  └── DocumentRenderer.php
   │  │     │  │  ├── ChildNodeRendererInterface.php
   │  │     │  │  ├── NoMatchingRendererException.php
   │  │     │  │  ├── MarkdownRendererInterface.php
   │  │     │  │  ├── HtmlDecorator.php
   │  │     │  │  └── NodeRendererInterface.php
   │  │     │  ├── CommonMarkConverter.php
   │  │     │  ├── Delimiter
   │  │     │  │  ├── DelimiterParser.php
   │  │     │  │  ├── Bracket.php
   │  │     │  │  ├── DelimiterInterface.php
   │  │     │  │  ├── Delimiter.php
   │  │     │  │  ├── DelimiterStack.php
   │  │     │  │  └── Processor
   │  │     │  │    ├── StaggeredDelimiterProcessor.php
   │  │     │  │    ├── DelimiterProcessorCollectionInterface.php
   │  │     │  │    ├── CacheableDelimiterProcessorInterface.php
   │  │     │  │    ├── DelimiterProcessorCollection.php
   │  │     │  │    └── DelimiterProcessorInterface.php
   │  │     │  ├── GithubFlavoredMarkdownConverter.php
   │  │     │  ├── Node
   │  │     │  │  ├── StringContainerHelper.php
   │  │     │  │  ├── Node.php
   │  │     │  │  ├── StringContainerInterface.php
   │  │     │  │  ├── Query.php
   │  │     │  │  ├── NodeWalker.php
   │  │     │  │  ├── Inline
   │  │     │  │  │  ├── AbstractStringContainer.php
   │  │     │  │  │  ├── Text.php
   │  │     │  │  │  ├── AdjacentTextMerger.php
   │  │     │  │  │  ├── DelimitedInterface.php
   │  │     │  │  │  ├── AbstractInline.php
   │  │     │  │  │  └── Newline.php
   │  │     │  │  ├── Block
   │  │     │  │  │  ├── TightBlockInterface.php
   │  │     │  │  │  ├── AbstractBlock.php
   │  │     │  │  │  ├── Paragraph.php
   │  │     │  │  │  └── Document.php
   │  │     │  │  ├── Query
   │  │     │  │  │  ├── OrExpr.php
   │  │     │  │  │  ├── AndExpr.php
   │  │     │  │  │  └── ExpressionInterface.php
   │  │     │  │  ├── RawMarkupContainerInterface.php
   │  │     │  │  ├── NodeWalkerEvent.php
   │  │     │  │  └── NodeIterator.php
   │  │     │  ├── ConverterInterface.php
   │  │     │  ├── Environment
   │  │     │  │  ├── EnvironmentAwareInterface.php
   │  │     │  │  ├── EnvironmentBuilderInterface.php
   │  │     │  │  ├── Environment.php
   │  │     │  │  └── EnvironmentInterface.php
   │  │     │  ├── MarkdownConverter.php
   │  │     │  ├── Extension
   │  │     │  │  ├── ExtensionInterface.php
   │  │     │  │  ├── ConfigurableExtensionInterface.php
   │  │     │  │  ├── Attributes
   │  │     │  │  │  ├── AttributesExtension.php
   │  │     │  │  │  ├── Util
   │  │     │  │  │  │  └── AttributesHelper.php
   │  │     │  │  │  ├── Node
   │  │     │  │  │  │  ├── AttributesInline.php
   │  │     │  │  │  │  └── Attributes.php
   │  │     │  │  │  ├── Event
   │  │     │  │  │  │  └── AttributesListener.php
   │  │     │  │  │  └── Parser
   │  │     │  │  │    ├── AttributesBlockStartParser.php
   │  │     │  │  │    ├── AttributesBlockContinueParser.php
   │  │     │  │  │    └── AttributesInlineParser.php
   │  │     │  │  ├── Autolink
   │  │     │  │  │  ├── UrlAutolinkParser.php
   │  │     │  │  │  ├── EmailAutolinkParser.php
   │  │     │  │  │  └── AutolinkExtension.php
   │  │     │  │  ├── FrontMatter
   │  │     │  │  │  ├── Output
   │  │     │  │  │  │  └── RenderedContentWithFrontMatter.php
   │  │     │  │  │  ├── Input
   │  │     │  │  │  │  └── MarkdownInputWithFrontMatter.php
   │  │     │  │  │  ├── FrontMatterExtension.php
   │  │     │  │  │  ├── FrontMatterParserInterface.php
   │  │     │  │  │  ├── Exception
   │  │     │  │  │  │  └── InvalidFrontMatterException.php
   │  │     │  │  │  ├── FrontMatterParser.php
   │  │     │  │  │  ├── Listener
   │  │     │  │  │  │  ├── FrontMatterPostRenderListener.php
   │  │     │  │  │  │  └── FrontMatterPreParser.php
   │  │     │  │  │  ├── Data
   │  │     │  │  │  │  ├── FrontMatterDataParserInterface.php
   │  │     │  │  │  │  ├── SymfonyYamlFrontMatterParser.php
   │  │     │  │  │  │  └── LibYamlFrontMatterParser.php
   │  │     │  │  │  └── FrontMatterProviderInterface.php
   │  │     │  │  ├── Highlight
   │  │     │  │  │  ├── HighlightExtension.php
   │  │     │  │  │  ├── MarkRenderer.php
   │  │     │  │  │  ├── Mark.php
   │  │     │  │  │  └── MarkDelimiterProcessor.php
   │  │     │  │  ├── GithubFlavoredMarkdownExtension.php
   │  │     │  │  ├── HeadingPermalink
   │  │     │  │  │  ├── HeadingPermalinkRenderer.php
   │  │     │  │  │  ├── HeadingPermalink.php
   │  │     │  │  │  ├── HeadingPermalinkProcessor.php
   │  │     │  │  │  └── HeadingPermalinkExtension.php
   │  │     │  │  ├── Strikethrough
   │  │     │  │  │  ├── StrikethroughRenderer.php
   │  │     │  │  │  ├── Strikethrough.php
   │  │     │  │  │  ├── StrikethroughExtension.php
   │  │     │  │  │  └── StrikethroughDelimiterProcessor.php
   │  │     │  │  ├── SmartPunct
   │  │     │  │  │  ├── Quote.php
   │  │     │  │  │  ├── SmartPunctExtension.php
   │  │     │  │  │  ├── EllipsesParser.php
   │  │     │  │  │  ├── QuoteProcessor.php
   │  │     │  │  │  ├── ReplaceUnpairedQuotesListener.php
   │  │     │  │  │  ├── DashParser.php
   │  │     │  │  │  └── QuoteParser.php
   │  │     │  │  ├── Embed
   │  │     │  │  │  ├── EmbedParser.php
   │  │     │  │  │  ├── EmbedRenderer.php
   │  │     │  │  │  ├── DomainFilteringAdapter.php
   │  │     │  │  │  ├── Bridge
   │  │     │  │  │  │  └── OscaroteroEmbedAdapter.php
   │  │     │  │  │  ├── EmbedExtension.php
   │  │     │  │  │  ├── EmbedStartParser.php
   │  │     │  │  │  ├── Embed.php
   │  │     │  │  │  ├── EmbedAdapterInterface.php
   │  │     │  │  │  └── EmbedProcessor.php
   │  │     │  │  ├── CommonMark
   │  │     │  │  │  ├── CommonMarkCoreExtension.php
   │  │     │  │  │  ├── Renderer
   │  │     │  │  │  │  ├── Inline
   │  │     │  │  │  │  │  ├── StrongRenderer.php
   │  │     │  │  │  │  │  ├── HtmlInlineRenderer.php
   │  │     │  │  │  │  │  ├── LinkRenderer.php
   │  │     │  │  │  │  │  ├── EmphasisRenderer.php
   │  │     │  │  │  │  │  ├── CodeRenderer.php
   │  │     │  │  │  │  │  └── ImageRenderer.php
   │  │     │  │  │  │  └── Block
   │  │     │  │  │  │    ├── ListItemRenderer.php
   │  │     │  │  │  │    ├── HtmlBlockRenderer.php
   │  │     │  │  │  │    ├── ThematicBreakRenderer.php
   │  │     │  │  │  │    ├── HeadingRenderer.php
   │  │     │  │  │  │    ├── BlockQuoteRenderer.php
   │  │     │  │  │  │    ├── FencedCodeRenderer.php
   │  │     │  │  │  │    ├── IndentedCodeRenderer.php
   │  │     │  │  │  │    └── ListBlockRenderer.php
   │  │     │  │  │  ├── Delimiter
   │  │     │  │  │  │  └── Processor
   │  │     │  │  │  │    └── EmphasisDelimiterProcessor.php
   │  │     │  │  │  ├── Node
   │  │     │  │  │  │  ├── Inline
   │  │     │  │  │  │  │  ├── Emphasis.php
   │  │     │  │  │  │  │  ├── Strong.php
   │  │     │  │  │  │  │  ├── Code.php
   │  │     │  │  │  │  │  ├── AbstractWebResource.php
   │  │     │  │  │  │  │  ├── Link.php
   │  │     │  │  │  │  │  ├── HtmlInline.php
   │  │     │  │  │  │  │  └── Image.php
   │  │     │  │  │  │  └── Block
   │  │     │  │  │  │    ├── Heading.php
   │  │     │  │  │  │    ├── FencedCode.php
   │  │     │  │  │  │    ├── BlockQuote.php
   │  │     │  │  │  │    ├── ListData.php
   │  │     │  │  │  │    ├── IndentedCode.php
   │  │     │  │  │  │    ├── HtmlBlock.php
   │  │     │  │  │  │    ├── ThematicBreak.php
   │  │     │  │  │  │    ├── ListItem.php
   │  │     │  │  │  │    └── ListBlock.php
   │  │     │  │  │  └── Parser
   │  │     │  │  │    ├── Inline
   │  │     │  │  │     │  ├── AutolinkParser.php
   │  │     │  │  │     │  ├── EntityParser.php
   │  │     │  │  │     │  ├── EscapableParser.php
   │  │     │  │  │     │  ├── HtmlInlineParser.php
   │  │     │  │  │     │  ├── BangParser.php
   │  │     │  │  │     │  ├── BacktickParser.php
   │  │     │  │  │     │  ├── OpenBracketParser.php
   │  │     │  │  │     │  └── CloseBracketParser.php
   │  │     │  │  │    └── Block
   │  │     │  │  │       ├── FencedCodeParser.php
   │  │     │  │  │       ├── HtmlBlockParser.php
   │  │     │  │  │       ├── ListBlockStartParser.php
   │  │     │  │  │       ├── HeadingStartParser.php
   │  │     │  │  │       ├── BlockQuoteParser.php
   │  │     │  │  │       ├── HtmlBlockStartParser.php
   │  │     │  │  │       ├── IndentedCodeStartParser.php
   │  │     │  │  │       ├── BlockQuoteStartParser.php
   │  │     │  │  │       ├── ListBlockParser.php
   │  │     │  │  │       ├── FencedCodeStartParser.php
   │  │     │  │  │       ├── IndentedCodeParser.php
   │  │     │  │  │       ├── ThematicBreakParser.php
   │  │     │  │  │       ├── ThematicBreakStartParser.php
   │  │     │  │  │       ├── ListItemParser.php
   │  │     │  │  │       └── HeadingParser.php
   │  │     │  │  ├── TableOfContents
   │  │     │  │  │  ├── TableOfContentsPlaceholderParser.php
   │  │     │  │  │  ├── Normalizer
   │  │     │  │  │  │  ├── AsIsNormalizerStrategy.php
   │  │     │  │  │  │  ├── FlatNormalizerStrategy.php
   │  │     │  │  │  │  ├── RelativeNormalizerStrategy.php
   │  │     │  │  │  │  └── NormalizerStrategyInterface.php
   │  │     │  │  │  ├── TableOfContentsBuilder.php
   │  │     │  │  │  ├── TableOfContentsRenderer.php
   │  │     │  │  │  ├── TableOfContentsGenerator.php
   │  │     │  │  │  ├── TableOfContentsGeneratorInterface.php
   │  │     │  │  │  ├── TableOfContentsPlaceholderRenderer.php
   │  │     │  │  │  ├── TableOfContentsExtension.php
   │  │     │  │  │  └── Node
   │  │     │  │  │    ├── TableOfContents.php
   │  │     │  │  │    └── TableOfContentsPlaceholder.php
   │  │     │  │  ├── ExternalLink
   │  │     │  │  │  ├── ExternalLinkExtension.php
   │  │     │  │  │  └── ExternalLinkProcessor.php
   │  │     │  │  ├── Footnote
   │  │     │  │  │  ├── Renderer
   │  │     │  │  │  │  ├── FootnoteRenderer.php
   │  │     │  │  │  │  ├── FootnoteBackrefRenderer.php
   │  │     │  │  │  │  ├── FootnoteRefRenderer.php
   │  │     │  │  │  │  └── FootnoteContainerRenderer.php
   │  │     │  │  │  ├── Node
   │  │     │  │  │  │  ├── FootnoteRef.php
   │  │     │  │  │  │  ├── FootnoteContainer.php
   │  │     │  │  │  │  ├── Footnote.php
   │  │     │  │  │  │  └── FootnoteBackref.php
   │  │     │  │  │  ├── FootnoteExtension.php
   │  │     │  │  │  ├── Event
   │  │     │  │  │  │  ├── NumberFootnotesListener.php
   │  │     │  │  │  │  ├── FixOrphanedFootnotesAndRefsListener.php
   │  │     │  │  │  │  ├── GatherFootnotesListener.php
   │  │     │  │  │  │  └── AnonymousFootnotesListener.php
   │  │     │  │  │  └── Parser
   │  │     │  │  │    ├── FootnoteRefParser.php
   │  │     │  │  │    ├── AnonymousFootnoteRefParser.php
   │  │     │  │  │    ├── FootnoteStartParser.php
   │  │     │  │  │    └── FootnoteParser.php
   │  │     │  │  ├── Mention
   │  │     │  │  │  ├── MentionExtension.php
   │  │     │  │  │  ├── Mention.php
   │  │     │  │  │  ├── MentionParser.php
   │  │     │  │  │  └── Generator
   │  │     │  │  │    ├── MentionGeneratorInterface.php
   │  │     │  │  │    ├── StringTemplateLinkGenerator.php
   │  │     │  │  │    └── CallbackGenerator.php
   │  │     │  │  ├── DescriptionList
   │  │     │  │  │  ├── Renderer
   │  │     │  │  │  │  ├── DescriptionRenderer.php
   │  │     │  │  │  │  ├── DescriptionListRenderer.php
   │  │     │  │  │  │  └── DescriptionTermRenderer.php
   │  │     │  │  │  ├── Node
   │  │     │  │  │  │  ├── Description.php
   │  │     │  │  │  │  ├── DescriptionList.php
   │  │     │  │  │  │  └── DescriptionTerm.php
   │  │     │  │  │  ├── Event
   │  │     │  │  │  │  ├── LooseDescriptionHandler.php
   │  │     │  │  │  │  └── ConsecutiveDescriptionListMerger.php
   │  │     │  │  │  ├── DescriptionListExtension.php
   │  │     │  │  │  └── Parser
   │  │     │  │  │    ├── DescriptionStartParser.php
   │  │     │  │  │    ├── DescriptionTermContinueParser.php
   │  │     │  │  │    ├── DescriptionContinueParser.php
   │  │     │  │  │    └── DescriptionListContinueParser.php
   │  │     │  │  ├── InlinesOnly
   │  │     │  │  │  ├── ChildRenderer.php
   │  │     │  │  │  └── InlinesOnlyExtension.php
   │  │     │  │  ├── Table
   │  │     │  │  │  ├── TableSectionRenderer.php
   │  │     │  │  │  ├── TableRowRenderer.php
   │  │     │  │  │  ├── TableExtension.php
   │  │     │  │  │  ├── TableRow.php
   │  │     │  │  │  ├── TableCell.php
   │  │     │  │  │  ├── TableParser.php
   │  │     │  │  │  ├── TableSection.php
   │  │     │  │  │  ├── Table.php
   │  │     │  │  │  ├── TableCellRenderer.php
   │  │     │  │  │  ├── TableStartParser.php
   │  │     │  │  │  └── TableRenderer.php
   │  │     │  │  ├── TaskList
   │  │     │  │  │  ├── TaskListItemMarker.php
   │  │     │  │  │  ├── TaskListExtension.php
   │  │     │  │  │  ├── TaskListItemMarkerParser.php
   │  │     │  │  │  └── TaskListItemMarkerRenderer.php
   │  │     │  │  ├── DefaultAttributes
   │  │     │  │  │  ├── ApplyDefaultAttributesProcessor.php
   │  │     │  │  │  └── DefaultAttributesExtension.php
   │  │     │  │  └── DisallowedRawHtml
   │  │     │  │    ├── DisallowedRawHtmlExtension.php
   │  │     │  │    └── DisallowedRawHtmlRenderer.php
   │  │     │  ├── Event
   │  │     │  │  ├── ListenerData.php
   │  │     │  │  ├── DocumentRenderedEvent.php
   │  │     │  │  ├── DocumentPreRenderEvent.php
   │  │     │  │  ├── AbstractEvent.php
   │  │     │  │  ├── DocumentParsedEvent.php
   │  │     │  │  └── DocumentPreParsedEvent.php
   │  │     │  ├── Parser
   │  │     │  │  ├── MarkdownParser.php
   │  │     │  │  ├── MarkdownParserStateInterface.php
   │  │     │  │  ├── InlineParserEngineInterface.php
   │  │     │  │  ├── MarkdownParserInterface.php
   │  │     │  │  ├── CursorState.php
   │  │     │  │  ├── MarkdownParserState.php
   │  │     │  │  ├── Inline
   │  │     │  │  │  ├── NewlineParser.php
   │  │     │  │  │  ├── InlineParserMatch.php
   │  │     │  │  │  └── InlineParserInterface.php
   │  │     │  │  ├── Block
   │  │     │  │  │  ├── ParagraphParser.php
   │  │     │  │  │  ├── BlockContinue.php
   │  │     │  │  │  ├── BlockContinueParserInterface.php
   │  │     │  │  │  ├── BlockStartParserInterface.php
   │  │     │  │  │  ├── BlockStart.php
   │  │     │  │  │  ├── DocumentBlockParser.php
   │  │     │  │  │  ├── AbstractBlockContinueParser.php
   │  │     │  │  │  ├── SkipLinesStartingWithLettersParser.php
   │  │     │  │  │  └── BlockContinueParserWithInlinesInterface.php
   │  │     │  │  ├── InlineParserEngine.php
   │  │     │  │  ├── ParserLogicException.php
   │  │     │  │  ├── InlineParserContext.php
   │  │     │  │  └── Cursor.php
   │  │     │  ├── MarkdownConverterInterface.php
   │  │     │  └── Reference
   │  │     │    ├── ReferenceMapInterface.php
   │  │     │    ├── Reference.php
   │  │     │    ├── ReferenceableInterface.php
   │  │     │    ├── ReferenceParser.php
   │  │     │    ├── ReferenceInterface.php
   │  │     │    ├── MemoryLimitedReferenceMap.php
   │  │     │    └── ReferenceMap.php
   │  │    ├── CHANGELOG.md
   │  │    └── LICENSE
   │  ├── psy
   │  │  └── psysh
   │  │    ├── composer.json
   │  │    ├── bin
   │  │     │  └── psysh
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── Clipboard
   │  │     │  │  ├── Osc52ClipboardMethod.php
   │  │     │  │  ├── ClipboardMethod.php
   │  │     │  │  ├── NullClipboardMethod.php
   │  │     │  │  └── CommandClipboardMethod.php
   │  │     │  ├── ShellLogger.php
   │  │     │  ├── SystemEnv.php
   │  │     │  ├── Output
   │  │     │  │  ├── OutputPager.php
   │  │     │  │  ├── Theme.php
   │  │     │  │  ├── ProcOutputPager.php
   │  │     │  │  ├── PassthruPager.php
   │  │     │  │  ├── ShellOutput.php
   │  │     │  │  └── ShellOutputAdapter.php
   │  │     │  ├── ParserFactory.php
   │  │     │  ├── ContextAware.php
   │  │     │  ├── CommandAware.php
   │  │     │  ├── ManualUpdater
   │  │     │  │  ├── Installer.php
   │  │     │  │  ├── ManualUpdate.php
   │  │     │  │  ├── Checker.php
   │  │     │  │  ├── GitHubChecker.php
   │  │     │  │  └── IntervalChecker.php
   │  │     │  ├── ConfigPaths.php
   │  │     │  ├── CodeCleanerAware.php
   │  │     │  ├── Input
   │  │     │  │  ├── SilentInput.php
   │  │     │  │  ├── ShellInput.php
   │  │     │  │  ├── FilterOptions.php
   │  │     │  │  └── CodeArgument.php
   │  │     │  ├── CommandArgumentCompletionAware.php
   │  │     │  ├── TabCompletion
   │  │     │  │  ├── AutoloadWarmer
   │  │     │  │  │  ├── AutoloadWarmerInterface.php
   │  │     │  │  │  └── ComposerAutoloadWarmer.php
   │  │     │  │  ├── Matcher
   │  │     │  │  │  ├── MagicMethodsMatcher.php
   │  │     │  │  │  ├── AbstractContextAwareMatcher.php
   │  │     │  │  │  ├── FunctionDefaultParametersMatcher.php
   │  │     │  │  │  ├── ObjectMethodDefaultParametersMatcher.php
   │  │     │  │  │  ├── ClassNamesMatcher.php
   │  │     │  │  │  ├── KeywordsMatcher.php
   │  │     │  │  │  ├── CommandsMatcher.php
   │  │     │  │  │  ├── MongoDatabaseMatcher.php
   │  │     │  │  │  ├── ClassMethodDefaultParametersMatcher.php
   │  │     │  │  │  ├── ClassAttributesMatcher.php
   │  │     │  │  │  ├── MagicPropertiesMatcher.php
   │  │     │  │  │  ├── AbstractMatcher.php
   │  │     │  │  │  ├── AbstractDefaultParametersMatcher.php
   │  │     │  │  │  ├── FunctionsMatcher.php
   │  │     │  │  │  ├── ClassMethodsMatcher.php
   │  │     │  │  │  ├── ObjectMethodsMatcher.php
   │  │     │  │  │  ├── VariablesMatcher.php
   │  │     │  │  │  ├── MongoClientMatcher.php
   │  │     │  │  │  ├── ObjectAttributesMatcher.php
   │  │     │  │  │  └── ConstantsMatcher.php
   │  │     │  │  └── AutoCompleter.php
   │  │     │  ├── VersionUpdater
   │  │     │  │  ├── Installer.php
   │  │     │  │  ├── Checker.php
   │  │     │  │  ├── NoopChecker.php
   │  │     │  │  ├── Downloader
   │  │     │  │  │  ├── CurlDownloader.php
   │  │     │  │  │  ├── FileDownloader.php
   │  │     │  │  │  └── Factory.php
   │  │     │  │  ├── GitHubChecker.php
   │  │     │  │  ├── Downloader.php
   │  │     │  │  ├── IntervalChecker.php
   │  │     │  │  └── SelfUpdate.php
   │  │     │  ├── CodeAnalysis
   │  │     │  │  ├── BufferAnalyzer.php
   │  │     │  │  └── BufferAnalysis.php
   │  │     │  ├── VarDumper
   │  │     │  │  ├── Presenter.php
   │  │     │  │  ├── Dumper.php
   │  │     │  │  ├── Cloner.php
   │  │     │  │  ├── DumperBase.php
   │  │     │  │  └── PresenterAware.php
   │  │     │  ├── OutputAware.php
   │  │     │  ├── ProjectTrust.php
   │  │     │  ├── Shell
   │  │     │  │  └── PendingInputState.php
   │  │     │  ├── Completion
   │  │     │  │  ├── CompletionRequest.php
   │  │     │  │  ├── DeepestNodeVisitor.php
   │  │     │  │  ├── ContextAnalyzer.php
   │  │     │  │  ├── SymbolCatalog.php
   │  │     │  │  ├── FuzzyMatcher.php
   │  │     │  │  ├── CompletionKind.php
   │  │     │  │  ├── CompletionEngine.php
   │  │     │  │  ├── Refiner
   │  │     │  │  │  ├── CommandContextRefiner.php
   │  │     │  │  │  ├── AnalysisRefinerInterface.php
   │  │     │  │  │  ├── CommandSyntaxRefiner.php
   │  │     │  │  │  └── PartialInputRefiner.php
   │  │     │  │  ├── TypeResolver.php
   │  │     │  │  ├── Source
   │  │     │  │  │  ├── SourceInterface.php
   │  │     │  │  │  ├── CommandArgumentSource.php
   │  │     │  │  │  ├── ObjectMethodSource.php
   │  │     │  │  │  ├── VariableSource.php
   │  │     │  │  │  ├── CommandOptionSource.php
   │  │     │  │  │  ├── StaticMethodSource.php
   │  │     │  │  │  ├── PropertySource.php
   │  │     │  │  │  ├── HistorySource.php
   │  │     │  │  │  ├── CatalogSource.php
   │  │     │  │  │  ├── CommandSource.php
   │  │     │  │  │  ├── ClassConstantSource.php
   │  │     │  │  │  ├── MatcherAdapterSource.php
   │  │     │  │  │  ├── MagicMethodSource.php
   │  │     │  │  │  ├── ObjectPropertySource.php
   │  │     │  │  │  ├── MethodSource.php
   │  │     │  │  │  ├── StaticPropertySource.php
   │  │     │  │  │  ├── NamespaceSource.php
   │  │     │  │  │  ├── MagicPropertySource.php
   │  │     │  │  │  └── KeywordSource.php
   │  │     │  │  └── AnalysisResult.php
   │  │     │  ├── ExecutionLoopClosure.php
   │  │     │  ├── Manual
   │  │     │  │  ├── V3Manual.php
   │  │     │  │  ├── V2Manual.php
   │  │     │  │  └── ManualInterface.php
   │  │     │  ├── CommandMapTrait.php
   │  │     │  ├── Shell.php
   │  │     │  ├── Formatter
   │  │     │  │  ├── CodeFormatter.php
   │  │     │  │  ├── SignatureFormatter.php
   │  │     │  │  ├── TraceFormatter.php
   │  │     │  │  ├── DocblockFormatter.php
   │  │     │  │  ├── ManualFormatter.php
   │  │     │  │  ├── ReflectorFormatter.php
   │  │     │  │  ├── LinkFormatter.php
   │  │     │  │  └── ManualWrapper.php
   │  │     │  ├── functions.php
   │  │     │  ├── Exception
   │  │     │  │  ├── FatalErrorException.php
   │  │     │  │  ├── BreakException.php
   │  │     │  │  ├── RuntimeException.php
   │  │     │  │  ├── InterruptException.php
   │  │     │  │  ├── ErrorException.php
   │  │     │  │  ├── UnexpectedTargetException.php
   │  │     │  │  ├── InvalidManualException.php
   │  │     │  │  ├── ParseErrorException.php
   │  │     │  │  ├── DeprecatedException.php
   │  │     │  │  ├── Exception.php
   │  │     │  │  └── ThrowUpException.php
   │  │     │  ├── Util
   │  │     │  │  ├── DependencyChecker.php
   │  │     │  │  ├── Json.php
   │  │     │  │  ├── Str.php
   │  │     │  │  ├── TerminalColor.php
   │  │     │  │  ├── Tty.php
   │  │     │  │  ├── Docblock.php
   │  │     │  │  └── Mirror.php
   │  │     │  ├── SuperglobalsEnv.php
   │  │     │  ├── Command
   │  │     │  │  ├── ReflectingCommand.php
   │  │     │  │  ├── EditCommand.php
   │  │     │  │  ├── ThrowUpCommand.php
   │  │     │  │  ├── SudoCommand.php
   │  │     │  │  ├── HelpCommand.php
   │  │     │  │  ├── ShowCommand.php
   │  │     │  │  ├── ParseCommand.php
   │  │     │  │  ├── ListCommand
   │  │     │  │  │  ├── PropertyEnumerator.php
   │  │     │  │  │  ├── GlobalVariableEnumerator.php
   │  │     │  │  │  ├── ConstantEnumerator.php
   │  │     │  │  │  ├── Enumerator.php
   │  │     │  │  │  ├── FunctionEnumerator.php
   │  │     │  │  │  ├── ClassEnumerator.php
   │  │     │  │  │  ├── MethodEnumerator.php
   │  │     │  │  │  ├── VariableEnumerator.php
   │  │     │  │  │  └── ClassConstantEnumerator.php
   │  │     │  │  ├── HistoryCommand.php
   │  │     │  │  ├── TraceCommand.php
   │  │     │  │  ├── Command.php
   │  │     │  │  ├── Config
   │  │     │  │  │  ├── ConfigGetCommand.php
   │  │     │  │  │  ├── ConfigListCommand.php
   │  │     │  │  │  ├── AbstractConfigCommand.php
   │  │     │  │  │  └── ConfigSetCommand.php
   │  │     │  │  ├── DocCommand.php
   │  │     │  │  ├── ExitCommand.php
   │  │     │  │  ├── ListCommand.php
   │  │     │  │  ├── CopyCommand.php
   │  │     │  │  ├── YoloCommand.php
   │  │     │  │  ├── BufferCommand.php
   │  │     │  │  ├── CodeArgumentParser.php
   │  │     │  │  ├── PsyVersionCommand.php
   │  │     │  │  ├── WhereamiCommand.php
   │  │     │  │  ├── TimeitCommand.php
   │  │     │  │  ├── TimeitCommand
   │  │     │  │  │  └── TimeitVisitor.php
   │  │     │  │  ├── WtfCommand.php
   │  │     │  │  ├── ConfigCommand.php
   │  │     │  │  ├── DumpCommand.php
   │  │     │  │  └── ClearCommand.php
   │  │     │  ├── Configuration.php
   │  │     │  ├── Readline
   │  │     │  │  ├── ReadlineAware.php
   │  │     │  │  ├── ShellReadlineInterface.php
   │  │     │  │  ├── Hoa
   │  │     │  │  │  ├── StreamOut.php
   │  │     │  │  │  ├── Ustring.php
   │  │     │  │  │  ├── Xcallable.php
   │  │     │  │  │  ├── EventListenable.php
   │  │     │  │  │  ├── EventListens.php
   │  │     │  │  │  ├── File.php
   │  │     │  │  │  ├── Stream.php
   │  │     │  │  │  ├── IteratorRecursiveDirectory.php
   │  │     │  │  │  ├── EventException.php
   │  │     │  │  │  ├── StreamTouchable.php
   │  │     │  │  │  ├── IteratorSplFileInfo.php
   │  │     │  │  │  ├── ConsoleCursor.php
   │  │     │  │  │  ├── Readline.php
   │  │     │  │  │  ├── Console.php
   │  │     │  │  │  ├── IteratorFileSystem.php
   │  │     │  │  │  ├── StreamPointable.php
   │  │     │  │  │  ├── ProtocolWrapper.php
   │  │     │  │  │  ├── EventBucket.php
   │  │     │  │  │  ├── ConsoleProcessus.php
   │  │     │  │  │  ├── StreamStatable.php
   │  │     │  │  │  ├── ConsoleInput.php
   │  │     │  │  │  ├── AutocompleterPath.php
   │  │     │  │  │  ├── StreamPathable.php
   │  │     │  │  │  ├── FileLink.php
   │  │     │  │  │  ├── EventListener.php
   │  │     │  │  │  ├── StreamLockable.php
   │  │     │  │  │  ├── FileDoesNotExistException.php
   │  │     │  │  │  ├── Protocol.php
   │  │     │  │  │  ├── FileDirectory.php
   │  │     │  │  │  ├── FileGeneric.php
   │  │     │  │  │  ├── Exception.php
   │  │     │  │  │  ├── ExceptionIdle.php
   │  │     │  │  │  ├── StreamException.php
   │  │     │  │  │  ├── AutocompleterAggregate.php
   │  │     │  │  │  ├── IStream.php
   │  │     │  │  │  ├── EventSource.php
   │  │     │  │  │  ├── ConsoleException.php
   │  │     │  │  │  ├── Terminfo
   │  │     │  │  │  │  ├── 77
   │  │     │  │  │  │  │  └── windows-ansi
   │  │     │  │  │  │  └── 78
   │  │     │  │  │  │    ├── xterm-256color
   │  │     │  │  │  │    └── xterm
   │  │     │  │  │  ├── FileRead.php
   │  │     │  │  │  ├── ConsoleOutput.php
   │  │     │  │  │  ├── FileLinkRead.php
   │  │     │  │  │  ├── Event.php
   │  │     │  │  │  ├── StreamBufferable.php
   │  │     │  │  │  ├── ProtocolException.php
   │  │     │  │  │  ├── StreamContext.php
   │  │     │  │  │  ├── FileLinkReadWrite.php
   │  │     │  │  │  ├── StreamIn.php
   │  │     │  │  │  ├── AutocompleterWord.php
   │  │     │  │  │  ├── FileReadWrite.php
   │  │     │  │  │  ├── FileException.php
   │  │     │  │  │  ├── ConsoleWindow.php
   │  │     │  │  │  ├── ProtocolNodeLibrary.php
   │  │     │  │  │  ├── FileFinder.php
   │  │     │  │  │  ├── Autocompleter.php
   │  │     │  │  │  ├── ConsoleTput.php
   │  │     │  │  │  └── ProtocolNode.php
   │  │     │  │  ├── Transient.php
   │  │     │  │  ├── Userland.php
   │  │     │  │  ├── Readline.php
   │  │     │  │  ├── LegacyReadline.php
   │  │     │  │  ├── Libedit.php
   │  │     │  │  ├── InteractiveReadline.php
   │  │     │  │  ├── GNUReadline.php
   │  │     │  │  ├── Interactive
   │  │     │  │  │  ├── Terminal.php
   │  │     │  │  │  ├── Layout
   │  │     │  │  │  │  ├── DisplayString.php
   │  │     │  │  │  │  └── SoftWrapCalculator.php
   │  │     │  │  │  ├── Helper
   │  │     │  │  │  │  ├── CommandHighlighter.php
   │  │     │  │  │  │  ├── DebugLog.php
   │  │     │  │  │  │  ├── ArgumentExtractorVisitor.php
   │  │     │  │  │  │  ├── HistorySearchRenderer.php
   │  │     │  │  │  │  ├── CompletionRenderer.php
   │  │     │  │  │  │  ├── CurrentWord.php
   │  │     │  │  │  │  ├── BracketPair.php
   │  │     │  │  │  │  └── TokenHelper.php
   │  │     │  │  │  ├── Actions
   │  │     │  │  │  │  ├── KillTokenAction.php
   │  │     │  │  │  │  ├── MoveLeftAction.php
   │  │     │  │  │  │  ├── AcceptSuggestionWordAction.php
   │  │     │  │  │  │  ├── SubmitLineAction.php
   │  │     │  │  │  │  ├── AcceptSuggestionAction.php
   │  │     │  │  │  │  ├── InsertLineBreakAction.php
   │  │     │  │  │  │  ├── ClearBufferAction.php
   │  │     │  │  │  │  ├── MoveToEndAction.php
   │  │     │  │  │  │  ├── InsertIndentOnTabAction.php
   │  │     │  │  │  │  ├── KillWholeLineAction.php
   │  │     │  │  │  │  ├── SelfInsertAction.php
   │  │     │  │  │  │  ├── MoveTokenLeftAction.php
   │  │     │  │  │  │  ├── NextHistoryAction.php
   │  │     │  │  │  │  ├── RejectSyntaxErrorAction.php
   │  │     │  │  │  │  ├── DedentLeadingIndentationAction.php
   │  │     │  │  │  │  ├── DeleteBracketPairAction.php
   │  │     │  │  │  │  ├── MoveWordRightAction.php
   │  │     │  │  │  │  ├── TabAction.php
   │  │     │  │  │  │  ├── KillLineAction.php
   │  │     │  │  │  │  ├── MoveWordLeftAction.php
   │  │     │  │  │  │  ├── ReverseSearchAction.php
   │  │     │  │  │  │  ├── ExitIfEmptyAction.php
   │  │     │  │  │  │  ├── InsertLineBreakOnUnclosedBracketsAction.php
   │  │     │  │  │  │  ├── MoveToStartAction.php
   │  │     │  │  │  │  ├── InsertOpenBracketAction.php
   │  │     │  │  │  │  ├── HistoryExpansionAction.php
   │  │     │  │  │  │  ├── FallbackAction.php
   │  │     │  │  │  │  ├── InsertLineBreakOnIncompleteStatementAction.php
   │  │     │  │  │  │  ├── ClearScreenAction.php
   │  │     │  │  │  │  ├── KillWordAction.php
   │  │     │  │  │  │  ├── DeleteForwardAction.php
   │  │     │  │  │  │  ├── InsertQuoteAction.php
   │  │     │  │  │  │  ├── MoveTokenRightAction.php
   │  │     │  │  │  │  ├── PreviousHistoryAction.php
   │  │     │  │  │  │  ├── ExpandHistoryOnTabAction.php
   │  │     │  │  │  │  ├── ActionInterface.php
   │  │     │  │  │  │  ├── MoveRightAction.php
   │  │     │  │  │  │  ├── InsertCloseBracketAction.php
   │  │     │  │  │  │  └── DeleteBackwardCharAction.php
   │  │     │  │  │  ├── TerminalOutput.php
   │  │     │  │  │  ├── Input
   │  │     │  │  │  │  ├── Key.php
   │  │     │  │  │  │  ├── History.php
   │  │     │  │  │  │  ├── InputQueue.php
   │  │     │  │  │  │  ├── IndentationPolicy.php
   │  │     │  │  │  │  ├── KeyBindings.php
   │  │     │  │  │  │  ├── StatementCompletenessPolicy.php
   │  │     │  │  │  │  ├── StdinReader.php
   │  │     │  │  │  │  ├── Buffer.php
   │  │     │  │  │  │  ├── VisualNavigationPolicy.php
   │  │     │  │  │  │  ├── WordNavigationPolicy.php
   │  │     │  │  │  │  └── TokenNavigationPolicy.php
   │  │     │  │  │  ├── Readline.php
   │  │     │  │  │  ├── Suggestion
   │  │     │  │  │  │  ├── WordExtractor.php
   │  │     │  │  │  │  ├── SuggestionFilter.php
   │  │     │  │  │  │  ├── FrecencyIndex.php
   │  │     │  │  │  │  ├── SuggestionResult.php
   │  │     │  │  │  │  ├── Source
   │  │     │  │  │  │  │  ├── SourceInterface.php
   │  │     │  │  │  │  │  ├── ContextAwareSource.php
   │  │     │  │  │  │  │  ├── HistorySource.php
   │  │     │  │  │  │  │  └── CallSignatureSource.php
   │  │     │  │  │  │  └── SuggestionEngine.php
   │  │     │  │  │  ├── Renderer
   │  │     │  │  │  │  ├── FrameRenderer.php
   │  │     │  │  │  │  └── OverlayViewport.php
   │  │     │  │  │  ├── InteractiveSession.php
   │  │     │  │  │  └── HistorySearch.php
   │  │     │  │  └── InteractiveReadlineInterface.php
   │  │     │  ├── CodeCleaner.php
   │  │     │  ├── EnvInterface.php
   │  │     │  ├── Sudo.php
   │  │     │  ├── CodeCleaner
   │  │     │  │  ├── CalledClassPass.php
   │  │     │  │  ├── PassableByReferencePass.php
   │  │     │  │  ├── AssignThisVariablePass.php
   │  │     │  │  ├── LabelContextPass.php
   │  │     │  │  ├── CallTimePassByReferencePass.php
   │  │     │  │  ├── ReturnTypePass.php
   │  │     │  │  ├── ExitPass.php
   │  │     │  │  ├── MagicConstantsPass.php
   │  │     │  │  ├── LeavePsyshAlonePass.php
   │  │     │  │  ├── CodeCleanerPass.php
   │  │     │  │  ├── NoReturnValue.php
   │  │     │  │  ├── ListPass.php
   │  │     │  │  ├── AbstractClassPass.php
   │  │     │  │  ├── LoopContextPass.php
   │  │     │  │  ├── ValidConstructorPass.php
   │  │     │  │  ├── ImplicitUsePass.php
   │  │     │  │  ├── NamespacePass.php
   │  │     │  │  ├── ValidClassNamePass.php
   │  │     │  │  ├── FunctionContextPass.php
   │  │     │  │  ├── StrictTypesPass.php
   │  │     │  │  ├── IssetPass.php
   │  │     │  │  ├── FinalClassPass.php
   │  │     │  │  ├── RequirePass.php
   │  │     │  │  ├── ValidFunctionNamePass.php
   │  │     │  │  ├── ImplicitReturnPass.php
   │  │     │  │  ├── UseStatementPass.php
   │  │     │  │  ├── NamespaceAwarePass.php
   │  │     │  │  ├── FunctionReturnInWriteContextPass.php
   │  │     │  │  └── EmptyArrayDimFetchPass.php
   │  │     │  ├── ShellAware.php
   │  │     │  ├── Sudo
   │  │     │  │  └── SudoVisitor.php
   │  │     │  ├── Logger
   │  │     │  │  └── CallbackLogger.php
   │  │     │  ├── ExecutionLoop
   │  │     │  │  ├── UopzReloader.php
   │  │     │  │  ├── AbstractListener.php
   │  │     │  │  ├── InputLoggingListener.php
   │  │     │  │  ├── RunkitReloader.php
   │  │     │  │  ├── Listener.php
   │  │     │  │  ├── UopzReloaderVisitor.php
   │  │     │  │  ├── ExecutionLoggingListener.php
   │  │     │  │  ├── SignalHandler.php
   │  │     │  │  └── ProcessForker.php
   │  │     │  ├── Reflection
   │  │     │  │  ├── ReflectionMagicProperty.php
   │  │     │  │  ├── ReflectionMagicMethod.php
   │  │     │  │  ├── ReflectionLanguageConstruct.php
   │  │     │  │  ├── ReflectionNamespace.php
   │  │     │  │  ├── ReflectionConstant.php
   │  │     │  │  └── ReflectionLanguageConstructParameter.php
   │  │     │  ├── ExecutionClosure.php
   │  │     │  └── Context.php
   │  │    └── LICENSE
   │  ├── tijsverkoyen
   │  │  └── css-to-inline-styles
   │  │    ├── LICENSE.md
   │  │    ├── composer.json
   │  │    └── src
   │  │       ├── CssToInlineStyles.php
   │  │       └── Css
   │  │          ├── Rule
   │  │           │  ├── Rule.php
   │  │           │  └── Processor.php
   │  │          ├── Processor.php
   │  │          └── Property
   │  │             ├── Processor.php
   │  │             └── Property.php
   │  ├── laravel
   │  │  ├── sanctum
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── UPGRADE.md
   │  │  │  ├── config
   │  │  │  │  └── sanctum.php
   │  │  │  ├── src
   │  │  │  │  ├── NewAccessToken.php
   │  │  │  │  ├── TransientToken.php
   │  │  │  │  ├── Events
   │  │  │  │  │  └── TokenAuthenticated.php
   │  │  │  │  ├── Sanctum.php
   │  │  │  │  ├── Http
   │  │  │  │  │  ├── Middleware
   │  │  │  │  │  │  ├── CheckAbilities.php
   │  │  │  │  │  │  ├── CheckScopes.php
   │  │  │  │  │  │  ├── AuthenticateSession.php
   │  │  │  │  │  │  ├── CheckForAnyScope.php
   │  │  │  │  │  │  ├── EnsureFrontendRequestsAreStateful.php
   │  │  │  │  │  │  └── CheckForAnyAbility.php
   │  │  │  │  │  └── Controllers
   │  │  │  │  │    └── CsrfCookieController.php
   │  │  │  │  ├── PersonalAccessToken.php
   │  │  │  │  ├── Console
   │  │  │  │  │  └── Commands
   │  │  │  │  │    └── PruneExpired.php
   │  │  │  │  ├── Contracts
   │  │  │  │  │  ├── HasAbilities.php
   │  │  │  │  │  └── HasApiTokens.php
   │  │  │  │  ├── Exceptions
   │  │  │  │  │  ├── MissingScopeException.php
   │  │  │  │  │  └── MissingAbilityException.php
   │  │  │  │  ├── HasApiTokens.php
   │  │  │  │  ├── SanctumServiceProvider.php
   │  │  │  │  └── Guard.php
   │  │  │  ├── database
   │  │  │  │  └── migrations
   │  │  │  │    └── 2019_12_14_000001_create_personal_access_tokens_table.php
   │  │  │  └── testbench.yaml
   │  │  ├── sail
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  ├── bin
   │  │  │  │  └── sail
   │  │  │  ├── README.md
   │  │  │  ├── runtimes
   │  │  │  │  ├── 8.0
   │  │  │  │  │  ├── php.ini
   │  │  │  │  │  ├── supervisord.conf
   │  │  │  │  │  ├── Dockerfile
   │  │  │  │  │  └── start-container
   │  │  │  │  ├── 8.2
   │  │  │  │  │  ├── php.ini
   │  │  │  │  │  ├── supervisord.conf
   │  │  │  │  │  ├── Dockerfile
   │  │  │  │  │  └── start-container
   │  │  │  │  ├── 8.5
   │  │  │  │  │  ├── php.ini
   │  │  │  │  │  ├── supervisord.conf
   │  │  │  │  │  ├── Dockerfile
   │  │  │  │  │  └── start-container
   │  │  │  │  ├── 8.3
   │  │  │  │  │  ├── php.ini
   │  │  │  │  │  ├── supervisord.conf
   │  │  │  │  │  ├── Dockerfile
   │  │  │  │  │  └── start-container
   │  │  │  │  ├── 8.4
   │  │  │  │  │  ├── php.ini
   │  │  │  │  │  ├── supervisord.conf
   │  │  │  │  │  ├── Dockerfile
   │  │  │  │  │  └── start-container
   │  │  │  │  └── 8.1
   │  │  │  │    ├── php.ini
   │  │  │  │    ├── supervisord.conf
   │  │  │  │    ├── Dockerfile
   │  │  │  │    └── start-container
   │  │  │  ├── src
   │  │  │  │  ├── SailServiceProvider.php
   │  │  │  │  └── Console
   │  │  │  │    ├── AddCommand.php
   │  │  │  │    ├── Concerns
   │  │  │  │     │  └── InteractsWithDockerComposeServices.php
   │  │  │  │    ├── PublishCommand.php
   │  │  │  │    └── InstallCommand.php
   │  │  │  ├── database
   │  │  │  │  ├── mariadb
   │  │  │  │  │  └── create-testing-database.sh
   │  │  │  │  ├── pgsql
   │  │  │  │  │  └── create-testing-database.sql
   │  │  │  │  └── mysql
   │  │  │  │    └── create-testing-database.sh
   │  │  │  └── stubs
   │  │  │    ├── mongodb.stub
   │  │  │    ├── mariadb.stub
   │  │  │    ├── pgsql.stub
   │  │  │    ├── minio.stub
   │  │  │    ├── meilisearch.stub
   │  │  │    ├── soketi.stub
   │  │  │    ├── devcontainer.stub
   │  │  │    ├── mysql.stub
   │  │  │    ├── mailpit.stub
   │  │  │    ├── rustfs.stub
   │  │  │    ├── memcached.stub
   │  │  │    ├── rabbitmq.stub
   │  │  │    ├── valkey.stub
   │  │  │    ├── redis.stub
   │  │  │    ├── typesense.stub
   │  │  │    ├── compose.stub
   │  │  │    └── selenium.stub
   │  │  ├── prompts
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  └── src
   │  │  │    ├── Key.php
   │  │  │    ├── FormStep.php
   │  │  │    ├── DataTablePrompt.php
   │  │  │    ├── Output
   │  │  │     │  ├── BufferedConsoleOutput.php
   │  │  │     │  └── ConsoleOutput.php
   │  │  │    ├── Terminal.php
   │  │  │    ├── Stream.php
   │  │  │    ├── SelectPrompt.php
   │  │  │    ├── MultiSearchPrompt.php
   │  │  │    ├── ConfirmPrompt.php
   │  │  │    ├── Support
   │  │  │     │  ├── Result.php
   │  │  │     │  ├── Utils.php
   │  │  │     │  └── Logger.php
   │  │  │    ├── NotifyPrompt.php
   │  │  │    ├── Grid.php
   │  │  │    ├── MultiSelectPrompt.php
   │  │  │    ├── Clear.php
   │  │  │    ├── AutoCompletePrompt.php
   │  │  │    ├── Title.php
   │  │  │    ├── Prompt.php
   │  │  │    ├── Table.php
   │  │  │    ├── Progress.php
   │  │  │    ├── Concerns
   │  │  │     │  ├── Fallback.php
   │  │  │     │  ├── FakesInputOutput.php
   │  │  │     │  ├── HasSpinner.php
   │  │  │     │  ├── Colors.php
   │  │  │     │  ├── Truncation.php
   │  │  │     │  ├── TypedValue.php
   │  │  │     │  ├── Termwind.php
   │  │  │     │  ├── Themes.php
   │  │  │     │  ├── Scrolling.php
   │  │  │     │  ├── Erase.php
   │  │  │     │  ├── Events.php
   │  │  │     │  ├── HasInfo.php
   │  │  │     │  ├── Interactivity.php
   │  │  │     │  └── Cursor.php
   │  │  │    ├── Exceptions
   │  │  │     │  ├── NonInteractiveValidationException.php
   │  │  │     │  └── FormRevertedException.php
   │  │  │    ├── Note.php
   │  │  │    ├── PausePrompt.php
   │  │  │    ├── SuggestPrompt.php
   │  │  │    ├── SearchPrompt.php
   │  │  │    ├── NumberPrompt.php
   │  │  │    ├── PasswordPrompt.php
   │  │  │    ├── TextPrompt.php
   │  │  │    ├── Themes
   │  │  │     │  ├── Default
   │  │  │     │  │  ├── PausePromptRenderer.php
   │  │  │     │  │  ├── TitleRenderer.php
   │  │  │     │  │  ├── DataTableRenderer.php
   │  │  │     │  │  ├── SearchPromptRenderer.php
   │  │  │     │  │  ├── Renderer.php
   │  │  │     │  │  ├── SuggestPromptRenderer.php
   │  │  │     │  │  ├── ClearRenderer.php
   │  │  │     │  │  ├── ConfirmPromptRenderer.php
   │  │  │     │  │  ├── TextareaPromptRenderer.php
   │  │  │     │  │  ├── MultiSelectPromptRenderer.php
   │  │  │     │  │  ├── SelectPromptRenderer.php
   │  │  │     │  │  ├── StreamRenderer.php
   │  │  │     │  │  ├── NumberPromptRenderer.php
   │  │  │     │  │  ├── NoteRenderer.php
   │  │  │     │  │  ├── ProgressRenderer.php
   │  │  │     │  │  ├── Concerns
   │  │  │     │  │  │  ├── DrawsBoxes.php
   │  │  │     │  │  │  ├── DrawsScrollbars.php
   │  │  │     │  │  │  └── InteractsWithStrings.php
   │  │  │     │  │  ├── SpinnerRenderer.php
   │  │  │     │  │  ├── GridRenderer.php
   │  │  │     │  │  ├── TextPromptRenderer.php
   │  │  │     │  │  ├── TaskRenderer.php
   │  │  │     │  │  ├── PasswordPromptRenderer.php
   │  │  │     │  │  ├── AutoCompletePromptRenderer.php
   │  │  │     │  │  ├── MultiSearchPromptRenderer.php
   │  │  │     │  │  └── TableRenderer.php
   │  │  │     │  └── Contracts
   │  │  │     │    └── Scrolling.php
   │  │  │    ├── Task.php
   │  │  │    ├── FormBuilder.php
   │  │  │    ├── helpers.php
   │  │  │    ├── Spinner.php
   │  │  │    └── TextareaPrompt.php
   │  │  ├── tinker
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── config
   │  │  │  │  └── tinker.php
   │  │  │  └── src
   │  │  │    ├── ClassAliasAutoloader.php
   │  │  │    ├── TinkerCaster.php
   │  │  │    ├── Console
   │  │  │     │  └── TinkerCommand.php
   │  │  │    └── TinkerServiceProvider.php
   │  │  ├── pail
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  └── src
   │  │  │    ├── Guards
   │  │  │     │  └── EnsurePcntlIsAvailable.php
   │  │  │    ├── File.php
   │  │  │    ├── Options.php
   │  │  │    ├── LoggerFactory.php
   │  │  │    ├── PailServiceProvider.php
   │  │  │    ├── Handler.php
   │  │  │    ├── Console
   │  │  │     │  └── Commands
   │  │  │     │    └── PailCommand.php
   │  │  │    ├── Contracts
   │  │  │     │  └── Printer.php
   │  │  │    ├── Files.php
   │  │  │    ├── ProcessFactory.php
   │  │  │    ├── ValueObjects
   │  │  │     │  ├── Origin
   │  │  │     │  │  ├── Console.php
   │  │  │     │  │  ├── Http.php
   │  │  │     │  │  └── Queue.php
   │  │  │     │  └── MessageLogged.php
   │  │  │    └── Printers
   │  │  │       └── CliPrinter.php
   │  │  ├── framework
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── config
   │  │  │  │  ├── cache.php
   │  │  │  │  ├── view.php
   │  │  │  │  ├── logging.php
   │  │  │  │  ├── hashing.php
   │  │  │  │  ├── concurrency.php
   │  │  │  │  ├── auth.php
   │  │  │  │  ├── app.php
   │  │  │  │  ├── queue.php
   │  │  │  │  ├── database.php
   │  │  │  │  ├── broadcasting.php
   │  │  │  │  ├── cors.php
   │  │  │  │  ├── filesystems.php
   │  │  │  │  ├── mail.php
   │  │  │  │  ├── session.php
   │  │  │  │  └── services.php
   │  │  │  ├── src
   │  │  │  │  └── Illuminate
   │  │  │  │    ├── Collections
   │  │  │  │     │  ├── MultipleItemsFoundException.php
   │  │  │  │     │  ├── Traits
   │  │  │  │     │  │  ├── TransformsToResourceCollection.php
   │  │  │  │     │  │  └── EnumeratesValues.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── Arr.php
   │  │  │  │     │  ├── ItemNotFoundException.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── functions.php
   │  │  │  │     │  ├── Enumerable.php
   │  │  │  │     │  ├── LazyCollection.php
   │  │  │  │     │  ├── HigherOrderCollectionProxy.php
   │  │  │  │     │  ├── Collection.php
   │  │  │  │     │  └── helpers.php
   │  │  │  │    ├── Session
   │  │  │  │     │  ├── DatabaseSessionHandler.php
   │  │  │  │     │  ├── ArraySessionHandler.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── ExistenceAwareInterface.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Middleware
   │  │  │  │     │  │  ├── StartSession.php
   │  │  │  │     │  │  └── AuthenticateSession.php
   │  │  │  │     │  ├── Store.php
   │  │  │  │     │  ├── EncryptedStore.php
   │  │  │  │     │  ├── TokenMismatchException.php
   │  │  │  │     │  ├── FileSessionHandler.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  ├── SessionTableCommand.php
   │  │  │  │     │  │  └── stubs
   │  │  │  │     │  │    └── database.stub
   │  │  │  │     │  ├── SymfonySessionDecorator.php
   │  │  │  │     │  ├── SessionManager.php
   │  │  │  │     │  ├── SessionServiceProvider.php
   │  │  │  │     │  ├── CookieSessionHandler.php
   │  │  │  │     │  ├── CacheBasedSessionHandler.php
   │  │  │  │     │  └── NullSessionHandler.php
   │  │  │  │    ├── Container
   │  │  │  │     │  ├── Attributes
   │  │  │  │     │  │  ├── Singleton.php
   │  │  │  │     │  │  ├── Config.php
   │  │  │  │     │  │  ├── Tag.php
   │  │  │  │     │  │  ├── Cache.php
   │  │  │  │     │  │  ├── Auth.php
   │  │  │  │     │  │  ├── Bind.php
   │  │  │  │     │  │  ├── RouteParameter.php
   │  │  │  │     │  │  ├── Scoped.php
   │  │  │  │     │  │  ├── CurrentUser.php
   │  │  │  │     │  │  ├── Storage.php
   │  │  │  │     │  │  ├── Database.php
   │  │  │  │     │  │  ├── Authenticated.php
   │  │  │  │     │  │  ├── Log.php
   │  │  │  │     │  │  ├── DB.php
   │  │  │  │     │  │  ├── Give.php
   │  │  │  │     │  │  └── Context.php
   │  │  │  │     │  ├── ContextualBindingBuilder.php
   │  │  │  │     │  ├── Util.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Container.php
   │  │  │  │     │  ├── BoundMethod.php
   │  │  │  │     │  ├── RewindableGenerator.php
   │  │  │  │     │  └── EntryNotFoundException.php
   │  │  │  │    ├── Concurrency
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── ConcurrencyManager.php
   │  │  │  │     │  ├── ConcurrencyServiceProvider.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  └── InvokeSerializedClosureCommand.php
   │  │  │  │     │  ├── ForkDriver.php
   │  │  │  │     │  ├── ProcessDriver.php
   │  │  │  │     │  └── SyncDriver.php
   │  │  │  │    ├── Pipeline
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Hub.php
   │  │  │  │     │  ├── Pipeline.php
   │  │  │  │     │  └── PipelineServiceProvider.php
   │  │  │  │    ├── Support
   │  │  │  │     │  ├── Facades
   │  │  │  │     │  │  ├── Date.php
   │  │  │  │     │  │  ├── File.php
   │  │  │  │     │  │  ├── Gate.php
   │  │  │  │     │  │  ├── Notification.php
   │  │  │  │     │  │  ├── Request.php
   │  │  │  │     │  │  ├── Response.php
   │  │  │  │     │  │  ├── Validator.php
   │  │  │  │     │  │  ├── Config.php
   │  │  │  │     │  │  ├── Redirect.php
   │  │  │  │     │  │  ├── Session.php
   │  │  │  │     │  │  ├── Cache.php
   │  │  │  │     │  │  ├── View.php
   │  │  │  │     │  │  ├── Http.php
   │  │  │  │     │  │  ├── Schedule.php
   │  │  │  │     │  │  ├── Auth.php
   │  │  │  │     │  │  ├── App.php
   │  │  │  │     │  │  ├── Pipeline.php
   │  │  │  │     │  │  ├── MaintenanceMode.php
   │  │  │  │     │  │  ├── Facade.php
   │  │  │  │     │  │  ├── Vite.php
   │  │  │  │     │  │  ├── Artisan.php
   │  │  │  │     │  │  ├── Exceptions.php
   │  │  │  │     │  │  ├── Schema.php
   │  │  │  │     │  │  ├── RateLimiter.php
   │  │  │  │     │  │  ├── Blade.php
   │  │  │  │     │  │  ├── Mail.php
   │  │  │  │     │  │  ├── Storage.php
   │  │  │  │     │  │  ├── Concurrency.php
   │  │  │  │     │  │  ├── Bus.php
   │  │  │  │     │  │  ├── Log.php
   │  │  │  │     │  │  ├── Event.php
   │  │  │  │     │  │  ├── Password.php
   │  │  │  │     │  │  ├── Hash.php
   │  │  │  │     │  │  ├── Crypt.php
   │  │  │  │     │  │  ├── ParallelTesting.php
   │  │  │  │     │  │  ├── DB.php
   │  │  │  │     │  │  ├── Queue.php
   │  │  │  │     │  │  ├── Broadcast.php
   │  │  │  │     │  │  ├── URL.php
   │  │  │  │     │  │  ├── Cookie.php
   │  │  │  │     │  │  ├── Lang.php
   │  │  │  │     │  │  ├── Redis.php
   │  │  │  │     │  │  ├── Route.php
   │  │  │  │     │  │  ├── Process.php
   │  │  │  │     │  │  └── Context.php
   │  │  │  │     │  ├── Sleep.php
   │  │  │  │     │  ├── InteractsWithTime.php
   │  │  │  │     │  ├── Traits
   │  │  │  │     │  │  ├── ReadsClassAttributes.php
   │  │  │  │     │  │  ├── InteractsWithData.php
   │  │  │  │     │  │  ├── CapsuleManagerTrait.php
   │  │  │  │     │  │  ├── ForwardsCalls.php
   │  │  │  │     │  │  ├── Dumpable.php
   │  │  │  │     │  │  ├── Tappable.php
   │  │  │  │     │  │  └── Localizable.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── DefaultProviders.php
   │  │  │  │     │  ├── MultipleInstanceManager.php
   │  │  │  │     │  ├── Composer.php
   │  │  │  │     │  ├── Pluralizer.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Carbon.php
   │  │  │  │     │  ├── BinaryCodec.php
   │  │  │  │     │  ├── Defer
   │  │  │  │     │  │  ├── DeferredCallbackCollection.php
   │  │  │  │     │  │  └── DeferredCallback.php
   │  │  │  │     │  ├── UriQueryString.php
   │  │  │  │     │  ├── AggregateServiceProvider.php
   │  │  │  │     │  ├── ProcessUtils.php
   │  │  │  │     │  ├── Str.php
   │  │  │  │     │  ├── Benchmark.php
   │  │  │  │     │  ├── DateFactory.php
   │  │  │  │     │  ├── EncodedHtmlString.php
   │  │  │  │     │  ├── Queue
   │  │  │  │     │  │  └── Concerns
   │  │  │  │     │  │    └── ResolvesQueueRoutes.php
   │  │  │  │     │  ├── Optional.php
   │  │  │  │     │  ├── Uri.php
   │  │  │  │     │  ├── functions.php
   │  │  │  │     │  ├── MessageBag.php
   │  │  │  │     │  ├── Fluent.php
   │  │  │  │     │  ├── Stringable.php
   │  │  │  │     │  ├── NamespacedItemResolver.php
   │  │  │  │     │  ├── Exceptions
   │  │  │  │     │  │  └── MathException.php
   │  │  │  │     │  ├── Number.php
   │  │  │  │     │  ├── Manager.php
   │  │  │  │     │  ├── ViewErrorBag.php
   │  │  │  │     │  ├── ServiceProvider.php
   │  │  │  │     │  ├── Onceable.php
   │  │  │  │     │  ├── Js.php
   │  │  │  │     │  ├── Env.php
   │  │  │  │     │  ├── Lottery.php
   │  │  │  │     │  ├── Once.php
   │  │  │  │     │  ├── HtmlString.php
   │  │  │  │     │  ├── HigherOrderTapProxy.php
   │  │  │  │     │  ├── ValidatedInput.php
   │  │  │  │     │  ├── Timebox.php
   │  │  │  │     │  ├── ConfigurationUrlParser.php
   │  │  │  │     │  ├── RebindsCallbacksToSelf.php
   │  │  │  │     │  ├── helpers.php
   │  │  │  │     │  └── Testing
   │  │  │  │     │    └── Fakes
   │  │  │  │     │       ├── QueueFake.php
   │  │  │  │     │       ├── BatchFake.php
   │  │  │  │     │       ├── MailFake.php
   │  │  │  │     │       ├── EventFake.php
   │  │  │  │     │       ├── PendingChainFake.php
   │  │  │  │     │       ├── PendingBatchFake.php
   │  │  │  │     │       ├── ChainedBatchTruthTest.php
   │  │  │  │     │       ├── PendingMailFake.php
   │  │  │  │     │       ├── BusFake.php
   │  │  │  │     │       ├── ExceptionHandlerFake.php
   │  │  │  │     │       ├── Fake.php
   │  │  │  │     │       ├── NotificationFake.php
   │  │  │  │     │       └── BatchRepositoryFake.php
   │  │  │  │    ├── Redis
   │  │  │  │     │  ├── RedisServiceProvider.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── Connectors
   │  │  │  │     │  │  ├── PhpRedisConnector.php
   │  │  │  │     │  │  └── PredisConnector.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Limiters
   │  │  │  │     │  │  ├── ConcurrencyLimiter.php
   │  │  │  │     │  │  ├── DurationLimiterBuilder.php
   │  │  │  │     │  │  ├── ConcurrencyLimiterBuilder.php
   │  │  │  │     │  │  └── DurationLimiter.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── CommandExecuted.php
   │  │  │  │     │  │  └── CommandFailed.php
   │  │  │  │     │  ├── RedisManager.php
   │  │  │  │     │  └── Connections
   │  │  │  │     │    ├── PhpRedisClusterConnection.php
   │  │  │  │     │    ├── PredisClusterConnection.php
   │  │  │  │     │    ├── PhpRedisConnection.php
   │  │  │  │     │    ├── PacksPhpRedisValues.php
   │  │  │  │     │    ├── Connection.php
   │  │  │  │     │    └── PredisConnection.php
   │  │  │  │    ├── Database
   │  │  │  │     │  ├── MultipleRecordsFoundException.php
   │  │  │  │     │  ├── LazyLoadingViolationException.php
   │  │  │  │     │  ├── Schema
   │  │  │  │     │  │  ├── ForeignKeyDefinition.php
   │  │  │  │     │  │  ├── MariaDbBuilder.php
   │  │  │  │     │  │  ├── SQLiteBuilder.php
   │  │  │  │     │  │  ├── BlueprintState.php
   │  │  │  │     │  │  ├── PostgresBuilder.php
   │  │  │  │     │  │  ├── MySqlBuilder.php
   │  │  │  │     │  │  ├── IndexDefinition.php
   │  │  │  │     │  │  ├── Grammars
   │  │  │  │     │  │  │  ├── MariaDbGrammar.php
   │  │  │  │     │  │  │  ├── SqlServerGrammar.php
   │  │  │  │     │  │  │  ├── Grammar.php
   │  │  │  │     │  │  │  ├── SQLiteGrammar.php
   │  │  │  │     │  │  │  ├── PostgresGrammar.php
   │  │  │  │     │  │  │  └── MySqlGrammar.php
   │  │  │  │     │  │  ├── ColumnDefinition.php
   │  │  │  │     │  │  ├── MySqlSchemaState.php
   │  │  │  │     │  │  ├── PostgresSchemaState.php
   │  │  │  │     │  │  ├── Builder.php
   │  │  │  │     │  │  ├── SqliteSchemaState.php
   │  │  │  │     │  │  ├── ForeignIdColumnDefinition.php
   │  │  │  │     │  │  ├── SchemaState.php
   │  │  │  │     │  │  ├── Blueprint.php
   │  │  │  │     │  │  ├── MariaDbSchemaState.php
   │  │  │  │     │  │  └── SqlServerBuilder.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── Connectors
   │  │  │  │     │  │  ├── MariaDbConnector.php
   │  │  │  │     │  │  ├── ConnectorInterface.php
   │  │  │  │     │  │  ├── PostgresConnector.php
   │  │  │  │     │  │  ├── MySqlConnector.php
   │  │  │  │     │  │  ├── Connector.php
   │  │  │  │     │  │  ├── ConnectionFactory.php
   │  │  │  │     │  │  ├── SqlServerConnector.php
   │  │  │  │     │  │  └── SQLiteConnector.php
   │  │  │  │     │  ├── MigrationServiceProvider.php
   │  │  │  │     │  ├── ConnectionResolver.php
   │  │  │  │     │  ├── Grammar.php
   │  │  │  │     │  ├── SqlServerConnection.php
   │  │  │  │     │  ├── SQLiteDatabaseDoesNotExistException.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── MySqlConnection.php
   │  │  │  │     │  ├── README.md
   │  │  │  │     │  ├── RecordsNotFoundException.php
   │  │  │  │     │  ├── LostConnectionException.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── TransactionBeginning.php
   │  │  │  │     │  │  ├── QueryExecuted.php
   │  │  │  │     │  │  ├── MigrationsEvent.php
   │  │  │  │     │  │  ├── TransactionRolledBack.php
   │  │  │  │     │  │  ├── MigrationsStarted.php
   │  │  │  │     │  │  ├── MigrationEvent.php
   │  │  │  │     │  │  ├── SchemaDumped.php
   │  │  │  │     │  │  ├── ModelPruningFinished.php
   │  │  │  │     │  │  ├── MigrationEnded.php
   │  │  │  │     │  │  ├── DatabaseRefreshed.php
   │  │  │  │     │  │  ├── ModelsPruned.php
   │  │  │  │     │  │  ├── ConnectionEstablished.php
   │  │  │  │     │  │  ├── SchemaLoaded.php
   │  │  │  │     │  │  ├── TransactionCommitted.php
   │  │  │  │     │  │  ├── TransactionCommitting.php
   │  │  │  │     │  │  ├── DatabaseBusy.php
   │  │  │  │     │  │  ├── StatementPrepared.php
   │  │  │  │     │  │  ├── MigrationSkipped.php
   │  │  │  │     │  │  ├── ModelPruningStarting.php
   │  │  │  │     │  │  ├── NoPendingMigrations.php
   │  │  │  │     │  │  ├── MigrationsPruned.php
   │  │  │  │     │  │  ├── ConnectionEvent.php
   │  │  │  │     │  │  ├── MigrationStarted.php
   │  │  │  │     │  │  └── MigrationsEnded.php
   │  │  │  │     │  ├── MariaDbConnection.php
   │  │  │  │     │  ├── QueryException.php
   │  │  │  │     │  ├── ConnectionInterface.php
   │  │  │  │     │  ├── Migrations
   │  │  │  │     │  │  ├── MigrationResult.php
   │  │  │  │     │  │  ├── MigrationRepositoryInterface.php
   │  │  │  │     │  │  ├── MigrationCreator.php
   │  │  │  │     │  │  ├── Migration.php
   │  │  │  │     │  │  ├── Migrator.php
   │  │  │  │     │  │  ├── DatabaseMigrationRepository.php
   │  │  │  │     │  │  └── stubs
   │  │  │  │     │  │    ├── migration.update.stub
   │  │  │  │     │  │    ├── migration.create.stub
   │  │  │  │     │  │    └── migration.stub
   │  │  │  │     │  ├── RecordNotFoundException.php
   │  │  │  │     │  ├── ConcurrencyErrorDetector.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  ├── DatabaseInspectionCommand.php
   │  │  │  │     │  │  ├── ShowModelCommand.php
   │  │  │  │     │  │  ├── WipeCommand.php
   │  │  │  │     │  │  ├── MonitorCommand.php
   │  │  │  │     │  │  ├── ShowCommand.php
   │  │  │  │     │  │  ├── TableCommand.php
   │  │  │  │     │  │  ├── Migrations
   │  │  │  │     │  │  │  ├── RefreshCommand.php
   │  │  │  │     │  │  │  ├── MigrateMakeCommand.php
   │  │  │  │     │  │  │  ├── StatusCommand.php
   │  │  │  │     │  │  │  ├── MigrateCommand.php
   │  │  │  │     │  │  │  ├── RollbackCommand.php
   │  │  │  │     │  │  │  ├── FreshCommand.php
   │  │  │  │     │  │  │  ├── TableGuesser.php
   │  │  │  │     │  │  │  ├── BaseCommand.php
   │  │  │  │     │  │  │  ├── InstallCommand.php
   │  │  │  │     │  │  │  └── ResetCommand.php
   │  │  │  │     │  │  ├── DbCommand.php
   │  │  │  │     │  │  ├── Seeds
   │  │  │  │     │  │  │  ├── SeederMakeCommand.php
   │  │  │  │     │  │  │  ├── WithoutModelEvents.php
   │  │  │  │     │  │  │  ├── SeedCommand.php
   │  │  │  │     │  │  │  └── stubs
   │  │  │  │     │  │  │    └── seeder.stub
   │  │  │  │     │  │  ├── PruneCommand.php
   │  │  │  │     │  │  ├── Factories
   │  │  │  │     │  │  │  ├── FactoryMakeCommand.php
   │  │  │  │     │  │  │  └── stubs
   │  │  │  │     │  │  │    └── factory.stub
   │  │  │  │     │  │  └── DumpCommand.php
   │  │  │  │     │  ├── Concerns
   │  │  │  │     │  │  ├── ManagesTransactions.php
   │  │  │  │     │  │  ├── BuildsQueries.php
   │  │  │  │     │  │  ├── CompilesJsonPaths.php
   │  │  │  │     │  │  ├── ParsesSearchPath.php
   │  │  │  │     │  │  ├── ExplainsQueries.php
   │  │  │  │     │  │  └── BuildsWhereDateClauses.php
   │  │  │  │     │  ├── PostgresConnection.php
   │  │  │  │     │  ├── Connection.php
   │  │  │  │     │  ├── DetectsConcurrencyErrors.php
   │  │  │  │     │  ├── DetectsLostConnections.php
   │  │  │  │     │  ├── DatabaseTransactionRecord.php
   │  │  │  │     │  ├── Capsule
   │  │  │  │     │  │  └── Manager.php
   │  │  │  │     │  ├── MultipleColumnsSelectedException.php
   │  │  │  │     │  ├── DatabaseServiceProvider.php
   │  │  │  │     │  ├── Eloquent
   │  │  │  │     │  │  ├── Relations
   │  │  │  │     │  │  │  ├── Pivot.php
   │  │  │  │     │  │  │  ├── MorphToMany.php
   │  │  │  │     │  │  │  ├── MorphPivot.php
   │  │  │  │     │  │  │  ├── Relation.php
   │  │  │  │     │  │  │  ├── MorphMany.php
   │  │  │  │     │  │  │  ├── HasOneOrMany.php
   │  │  │  │     │  │  │  ├── HasOne.php
   │  │  │  │     │  │  │  ├── MorphTo.php
   │  │  │  │     │  │  │  ├── BelongsTo.php
   │  │  │  │     │  │  │  ├── BelongsToMany.php
   │  │  │  │     │  │  │  ├── Concerns
   │  │  │  │     │  │  │  │  ├── SupportsDefaultModels.php
   │  │  │  │     │  │  │  │  ├── SupportsInverseRelations.php
   │  │  │  │     │  │  │  │  ├── ComparesRelatedModels.php
   │  │  │  │     │  │  │  │  ├── InteractsWithPivotTable.php
   │  │  │  │     │  │  │  │  ├── InteractsWithDictionary.php
   │  │  │  │     │  │  │  │  ├── CanBeOneOfMany.php
   │  │  │  │     │  │  │  │  └── AsPivot.php
   │  │  │  │     │  │  │  ├── HasOneThrough.php
   │  │  │  │     │  │  │  ├── HasManyThrough.php
   │  │  │  │     │  │  │  ├── MorphOneOrMany.php
   │  │  │  │     │  │  │  ├── MorphOne.php
   │  │  │  │     │  │  │  ├── HasMany.php
   │  │  │  │     │  │  │  └── HasOneOrManyThrough.php
   │  │  │  │     │  │  ├── Attributes
   │  │  │  │     │  │  │  ├── Hidden.php
   │  │  │  │     │  │  │  ├── UseFactory.php
   │  │  │  │     │  │  │  ├── ScopedBy.php
   │  │  │  │     │  │  │  ├── Guarded.php
   │  │  │  │     │  │  │  ├── Boot.php
   │  │  │  │     │  │  │  ├── WithoutTimestamps.php
   │  │  │  │     │  │  │  ├── WithoutIncrementing.php
   │  │  │  │     │  │  │  ├── Unguarded.php
   │  │  │  │     │  │  │  ├── UsePolicy.php
   │  │  │  │     │  │  │  ├── Table.php
   │  │  │  │     │  │  │  ├── Fillable.php
   │  │  │  │     │  │  │  ├── CollectedBy.php
   │  │  │  │     │  │  │  ├── Connection.php
   │  │  │  │     │  │  │  ├── DateFormat.php
   │  │  │  │     │  │  │  ├── Initialize.php
   │  │  │  │     │  │  │  ├── Visible.php
   │  │  │  │     │  │  │  ├── UseEloquentBuilder.php
   │  │  │  │     │  │  │  ├── UseResourceCollection.php
   │  │  │  │     │  │  │  ├── Touches.php
   │  │  │  │     │  │  │  ├── UseResource.php
   │  │  │  │     │  │  │  ├── Scope.php
   │  │  │  │     │  │  │  ├── ObservedBy.php
   │  │  │  │     │  │  │  └── Appends.php
   │  │  │  │     │  │  ├── ModelInfo.php
   │  │  │  │     │  │  ├── BroadcastsEvents.php
   │  │  │  │     │  │  ├── BroadcastableModelEventOccurred.php
   │  │  │  │     │  │  ├── BroadcastsEventsAfterCommit.php
   │  │  │  │     │  │  ├── ModelInspector.php
   │  │  │  │     │  │  ├── MassAssignmentException.php
   │  │  │  │     │  │  ├── MissingAttributeException.php
   │  │  │  │     │  │  ├── MassPrunable.php
   │  │  │  │     │  │  ├── SoftDeletingScope.php
   │  │  │  │     │  │  ├── ModelNotFoundException.php
   │  │  │  │     │  │  ├── Concerns
   │  │  │  │     │  │  │  ├── HasUuids.php
   │  │  │  │     │  │  │  ├── HasEvents.php
   │  │  │  │     │  │  │  ├── HidesAttributes.php
   │  │  │  │     │  │  │  ├── HasAttributes.php
   │  │  │  │     │  │  │  ├── GuardsAttributes.php
   │  │  │  │     │  │  │  ├── HasUlids.php
   │  │  │  │     │  │  │  ├── HasUniqueStringIds.php
   │  │  │  │     │  │  │  ├── HasVersion4Uuids.php
   │  │  │  │     │  │  │  ├── HasGlobalScopes.php
   │  │  │  │     │  │  │  ├── PreventsCircularRecursion.php
   │  │  │  │     │  │  │  ├── HasUniqueIds.php
   │  │  │  │     │  │  │  ├── TransformsToResource.php
   │  │  │  │     │  │  │  ├── HasRelationships.php
   │  │  │  │     │  │  │  ├── HasTimestamps.php
   │  │  │  │     │  │  │  └── QueriesRelationships.php
   │  │  │  │     │  │  ├── Collection.php
   │  │  │  │     │  │  ├── JsonEncodingException.php
   │  │  │  │     │  │  ├── HasCollection.php
   │  │  │  │     │  │  ├── Builder.php
   │  │  │  │     │  │  ├── SoftDeletes.php
   │  │  │  │     │  │  ├── Prunable.php
   │  │  │  │     │  │  ├── PendingHasThroughRelationship.php
   │  │  │  │     │  │  ├── Scope.php
   │  │  │  │     │  │  ├── HasBuilder.php
   │  │  │  │     │  │  ├── Model.php
   │  │  │  │     │  │  ├── HigherOrderBuilderProxy.php
   │  │  │  │     │  │  ├── Factories
   │  │  │  │     │  │  │  ├── HasFactory.php
   │  │  │  │     │  │  │  ├── Attributes
   │  │  │  │     │  │  │  │  └── UseModel.php
   │  │  │  │     │  │  │  ├── Sequence.php
   │  │  │  │     │  │  │  ├── BelongsToRelationship.php
   │  │  │  │     │  │  │  ├── Factory.php
   │  │  │  │     │  │  │  ├── CrossJoinSequence.php
   │  │  │  │     │  │  │  ├── BelongsToManyRelationship.php
   │  │  │  │     │  │  │  └── Relationship.php
   │  │  │  │     │  │  ├── QueueEntityResolver.php
   │  │  │  │     │  │  ├── InvalidCastException.php
   │  │  │  │     │  │  ├── Casts
   │  │  │  │     │  │  │  ├── AsCollection.php
   │  │  │  │     │  │  │  ├── AsEncryptedArrayObject.php
   │  │  │  │     │  │  │  ├── Json.php
   │  │  │  │     │  │  │  ├── AsHtmlString.php
   │  │  │  │     │  │  │  ├── AsFluent.php
   │  │  │  │     │  │  │  ├── AsUri.php
   │  │  │  │     │  │  │  ├── Attribute.php
   │  │  │  │     │  │  │  ├── AsBinary.php
   │  │  │  │     │  │  │  ├── ArrayObject.php
   │  │  │  │     │  │  │  ├── AsStringable.php
   │  │  │  │     │  │  │  ├── AsArrayObject.php
   │  │  │  │     │  │  │  ├── AsEncryptedCollection.php
   │  │  │  │     │  │  │  ├── AsEnumArrayObject.php
   │  │  │  │     │  │  │  └── AsEnumCollection.php
   │  │  │  │     │  │  └── RelationNotFoundException.php
   │  │  │  │     │  ├── ClassMorphViolationException.php
   │  │  │  │     │  ├── ConnectionResolverInterface.php
   │  │  │  │     │  ├── Seeder.php
   │  │  │  │     │  ├── DeadlockException.php
   │  │  │  │     │  ├── Query
   │  │  │  │     │  │  ├── Processors
   │  │  │  │     │  │  │  ├── Processor.php
   │  │  │  │     │  │  │  ├── SQLiteProcessor.php
   │  │  │  │     │  │  │  ├── SqlServerProcessor.php
   │  │  │  │     │  │  │  ├── PostgresProcessor.php
   │  │  │  │     │  │  │  ├── MariaDbProcessor.php
   │  │  │  │     │  │  │  └── MySqlProcessor.php
   │  │  │  │     │  │  ├── Grammars
   │  │  │  │     │  │  │  ├── MariaDbGrammar.php
   │  │  │  │     │  │  │  ├── SqlServerGrammar.php
   │  │  │  │     │  │  │  ├── Grammar.php
   │  │  │  │     │  │  │  ├── SQLiteGrammar.php
   │  │  │  │     │  │  │  ├── PostgresGrammar.php
   │  │  │  │     │  │  │  └── MySqlGrammar.php
   │  │  │  │     │  │  ├── Builder.php
   │  │  │  │     │  │  ├── Expression.php
   │  │  │  │     │  │  ├── JoinClause.php
   │  │  │  │     │  │  ├── IndexHint.php
   │  │  │  │     │  │  └── JoinLateralClause.php
   │  │  │  │     │  ├── SQLiteConnection.php
   │  │  │  │     │  ├── DatabaseManager.php
   │  │  │  │     │  ├── ConfigurationUrlParser.php
   │  │  │  │     │  ├── DatabaseTransactionsManager.php
   │  │  │  │     │  ├── LostConnectionDetector.php
   │  │  │  │     │  └── UniqueConstraintViolationException.php
   │  │  │  │    ├── Log
   │  │  │  │     │  ├── LogServiceProvider.php
   │  │  │  │     │  ├── LogManager.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Context
   │  │  │  │     │  │  ├── Repository.php
   │  │  │  │     │  │  ├── Events
   │  │  │  │     │  │  │  ├── ContextDehydrating.php
   │  │  │  │     │  │  │  └── ContextHydrated.php
   │  │  │  │     │  │  ├── ContextLogProcessor.php
   │  │  │  │     │  │  └── ContextServiceProvider.php
   │  │  │  │     │  ├── ParsesLogConfiguration.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  └── MessageLogged.php
   │  │  │  │     │  ├── functions.php
   │  │  │  │     │  └── Logger.php
   │  │  │  │    ├── Hashing
   │  │  │  │     │  ├── HashServiceProvider.php
   │  │  │  │     │  ├── AbstractHasher.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── BcryptHasher.php
   │  │  │  │     │  ├── ArgonHasher.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Argon2IdHasher.php
   │  │  │  │     │  └── HashManager.php
   │  │  │  │    ├── Bus
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── DynamoBatchRepository.php
   │  │  │  │     │  ├── PrunableBatchRepository.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── ChainedBatch.php
   │  │  │  │     │  ├── Batchable.php
   │  │  │  │     │  ├── DatabaseBatchRepository.php
   │  │  │  │     │  ├── Batch.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── BatchFinished.php
   │  │  │  │     │  │  ├── BatchCanceled.php
   │  │  │  │     │  │  ├── BatchStarted.php
   │  │  │  │     │  │  └── BatchDispatched.php
   │  │  │  │     │  ├── UpdatedBatchJobCounts.php
   │  │  │  │     │  ├── Queueable.php
   │  │  │  │     │  ├── UniqueLock.php
   │  │  │  │     │  ├── Dispatcher.php
   │  │  │  │     │  ├── BusServiceProvider.php
   │  │  │  │     │  ├── BatchFactory.php
   │  │  │  │     │  ├── BatchRepository.php
   │  │  │  │     │  └── PendingBatch.php
   │  │  │  │    ├── Events
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── NullDispatcher.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── InvokeQueuedClosure.php
   │  │  │  │     │  ├── functions.php
   │  │  │  │     │  ├── Dispatcher.php
   │  │  │  │     │  ├── CallQueuedListener.php
   │  │  │  │     │  ├── EventServiceProvider.php
   │  │  │  │     │  └── QueuedClosure.php
   │  │  │  │    ├── View
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── Engines
   │  │  │  │     │  │  ├── CompilerEngine.php
   │  │  │  │     │  │  ├── EngineResolver.php
   │  │  │  │     │  │  ├── PhpEngine.php
   │  │  │  │     │  │  ├── Engine.php
   │  │  │  │     │  │  └── FileEngine.php
   │  │  │  │     │  ├── ViewFinderInterface.php
   │  │  │  │     │  ├── ViewServiceProvider.php
   │  │  │  │     │  ├── View.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Middleware
   │  │  │  │     │  │  └── ShareErrorsFromSession.php
   │  │  │  │     │  ├── Component.php
   │  │  │  │     │  ├── ComponentSlot.php
   │  │  │  │     │  ├── ViewName.php
   │  │  │  │     │  ├── AppendableAttributeValue.php
   │  │  │  │     │  ├── ComponentAttributeBag.php
   │  │  │  │     │  ├── InvokableComponentVariable.php
   │  │  │  │     │  ├── Concerns
   │  │  │  │     │  │  ├── ManagesEvents.php
   │  │  │  │     │  │  ├── ManagesTranslations.php
   │  │  │  │     │  │  ├── ManagesLoops.php
   │  │  │  │     │  │  ├── ManagesComponents.php
   │  │  │  │     │  │  ├── ManagesStacks.php
   │  │  │  │     │  │  ├── ManagesLayouts.php
   │  │  │  │     │  │  └── ManagesFragments.php
   │  │  │  │     │  ├── Factory.php
   │  │  │  │     │  ├── Compilers
   │  │  │  │     │  │  ├── ComponentTagCompiler.php
   │  │  │  │     │  │  ├── Compiler.php
   │  │  │  │     │  │  ├── Concerns
   │  │  │  │     │  │  │  ├── CompilesErrors.php
   │  │  │  │     │  │  │  ├── CompilesClasses.php
   │  │  │  │     │  │  │  ├── CompilesHelpers.php
   │  │  │  │     │  │  │  ├── CompilesLayouts.php
   │  │  │  │     │  │  │  ├── CompilesComponents.php
   │  │  │  │     │  │  │  ├── CompilesJs.php
   │  │  │  │     │  │  │  ├── CompilesContexts.php
   │  │  │  │     │  │  │  ├── CompilesStacks.php
   │  │  │  │     │  │  │  ├── CompilesFragments.php
   │  │  │  │     │  │  │  ├── CompilesRawPhp.php
   │  │  │  │     │  │  │  ├── CompilesComments.php
   │  │  │  │     │  │  │  ├── CompilesLoops.php
   │  │  │  │     │  │  │  ├── CompilesIncludes.php
   │  │  │  │     │  │  │  ├── CompilesUseStatements.php
   │  │  │  │     │  │  │  ├── CompilesAuthorizations.php
   │  │  │  │     │  │  │  ├── CompilesJson.php
   │  │  │  │     │  │  │  ├── CompilesSessions.php
   │  │  │  │     │  │  │  ├── CompilesTranslations.php
   │  │  │  │     │  │  │  ├── CompilesStyles.php
   │  │  │  │     │  │  │  ├── CompilesConditionals.php
   │  │  │  │     │  │  │  ├── CompilesInjections.php
   │  │  │  │     │  │  │  └── CompilesEchos.php
   │  │  │  │     │  │  ├── CompilerInterface.php
   │  │  │  │     │  │  └── BladeCompiler.php
   │  │  │  │     │  ├── DynamicComponent.php
   │  │  │  │     │  ├── FileViewFinder.php
   │  │  │  │     │  ├── AnonymousComponent.php
   │  │  │  │     │  └── ViewException.php
   │  │  │  │    ├── Queue
   │  │  │  │     │  ├── WorkerStopReason.php
   │  │  │  │     │  ├── InvalidPayloadException.php
   │  │  │  │     │  ├── Attributes
   │  │  │  │     │  │  ├── Backoff.php
   │  │  │  │     │  │  ├── UniqueFor.php
   │  │  │  │     │  │  ├── MaxExceptions.php
   │  │  │  │     │  │  ├── Tries.php
   │  │  │  │     │  │  ├── Delay.php
   │  │  │  │     │  │  ├── Connection.php
   │  │  │  │     │  │  ├── Timeout.php
   │  │  │  │     │  │  ├── WithoutRelations.php
   │  │  │  │     │  │  ├── Queue.php
   │  │  │  │     │  │  ├── FailOnTimeout.php
   │  │  │  │     │  │  ├── ReadsQueueAttributes.php
   │  │  │  │     │  │  └── DeleteWhenMissingModels.php
   │  │  │  │     │  ├── BackgroundQueue.php
   │  │  │  │     │  ├── MaxAttemptsExceededException.php
   │  │  │  │     │  ├── RedisQueue.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── SyncQueue.php
   │  │  │  │     │  ├── Connectors
   │  │  │  │     │  │  ├── DatabaseConnector.php
   │  │  │  │     │  │  ├── RedisConnector.php
   │  │  │  │     │  │  ├── ConnectorInterface.php
   │  │  │  │     │  │  ├── NullConnector.php
   │  │  │  │     │  │  ├── SyncConnector.php
   │  │  │  │     │  │  ├── BackgroundConnector.php
   │  │  │  │     │  │  ├── FailoverConnector.php
   │  │  │  │     │  │  ├── SqsConnector.php
   │  │  │  │     │  │  ├── DeferredConnector.php
   │  │  │  │     │  │  └── BeanstalkdConnector.php
   │  │  │  │     │  ├── Listener.php
   │  │  │  │     │  ├── WorkerOptions.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Middleware
   │  │  │  │     │  │  ├── Skip.php
   │  │  │  │     │  │  ├── RateLimitedWithRedis.php
   │  │  │  │     │  │  ├── WithoutOverlapping.php
   │  │  │  │     │  │  ├── ThrottlesExceptionsWithRedis.php
   │  │  │  │     │  │  ├── FailOnException.php
   │  │  │  │     │  │  ├── SkipIfBatchCancelled.php
   │  │  │  │     │  │  ├── RateLimited.php
   │  │  │  │     │  │  └── ThrottlesExceptions.php
   │  │  │  │     │  ├── Failed
   │  │  │  │     │  │  ├── NullFailedJobProvider.php
   │  │  │  │     │  │  ├── DatabaseFailedJobProvider.php
   │  │  │  │     │  │  ├── PrunableFailedJobProvider.php
   │  │  │  │     │  │  ├── FailedJobProviderInterface.php
   │  │  │  │     │  │  ├── DynamoDbFailedJobProvider.php
   │  │  │  │     │  │  ├── FileFailedJobProvider.php
   │  │  │  │     │  │  ├── DatabaseUuidFailedJobProvider.php
   │  │  │  │     │  │  └── CountableFailedJobProvider.php
   │  │  │  │     │  ├── README.md
   │  │  │  │     │  ├── LuaScripts.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── QueuePaused.php
   │  │  │  │     │  │  ├── JobAttempted.php
   │  │  │  │     │  │  ├── QueueFailedOver.php
   │  │  │  │     │  │  ├── QueueResumed.php
   │  │  │  │     │  │  ├── JobTimedOut.php
   │  │  │  │     │  │  ├── WorkerStopping.php
   │  │  │  │     │  │  ├── JobProcessed.php
   │  │  │  │     │  │  ├── JobRetryRequested.php
   │  │  │  │     │  │  ├── Looping.php
   │  │  │  │     │  │  ├── JobReleasedAfterException.php
   │  │  │  │     │  │  ├── JobQueueing.php
   │  │  │  │     │  │  ├── JobProcessing.php
   │  │  │  │     │  │  ├── JobQueued.php
   │  │  │  │     │  │  ├── JobExceptionOccurred.php
   │  │  │  │     │  │  ├── JobPopped.php
   │  │  │  │     │  │  ├── JobFailed.php
   │  │  │  │     │  │  ├── WorkerStarting.php
   │  │  │  │     │  │  ├── JobPopping.php
   │  │  │  │     │  │  └── QueueBusy.php
   │  │  │  │     │  ├── FailoverQueue.php
   │  │  │  │     │  ├── SerializesAndRestoresModelIdentifiers.php
   │  │  │  │     │  ├── ManuallyFailedException.php
   │  │  │  │     │  ├── BeanstalkdQueue.php
   │  │  │  │     │  ├── DeferredQueue.php
   │  │  │  │     │  ├── CallQueuedClosure.php
   │  │  │  │     │  ├── NullQueue.php
   │  │  │  │     │  ├── SerializesModels.php
   │  │  │  │     │  ├── Jobs
   │  │  │  │     │  │  ├── DatabaseJobRecord.php
   │  │  │  │     │  │  ├── RedisJob.php
   │  │  │  │     │  │  ├── SqsJob.php
   │  │  │  │     │  │  ├── SyncJob.php
   │  │  │  │     │  │  ├── JobName.php
   │  │  │  │     │  │  ├── InspectedJob.php
   │  │  │  │     │  │  ├── Job.php
   │  │  │  │     │  │  ├── BeanstalkdJob.php
   │  │  │  │     │  │  ├── DatabaseJob.php
   │  │  │  │     │  │  └── FakeJob.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  ├── RestartCommand.php
   │  │  │  │     │  │  ├── BatchesTableCommand.php
   │  │  │  │     │  │  ├── FailedTableCommand.php
   │  │  │  │     │  │  ├── WorkCommand.php
   │  │  │  │     │  │  ├── ListenCommand.php
   │  │  │  │     │  │  ├── RetryCommand.php
   │  │  │  │     │  │  ├── RetryBatchCommand.php
   │  │  │  │     │  │  ├── ForgetFailedCommand.php
   │  │  │  │     │  │  ├── MonitorCommand.php
   │  │  │  │     │  │  ├── ListFailedCommand.php
   │  │  │  │     │  │  ├── PauseCommand.php
   │  │  │  │     │  │  ├── ResumeCommand.php
   │  │  │  │     │  │  ├── PruneFailedJobsCommand.php
   │  │  │  │     │  │  ├── TableCommand.php
   │  │  │  │     │  │  ├── FlushFailedCommand.php
   │  │  │  │     │  │  ├── Concerns
   │  │  │  │     │  │  │  └── ParsesQueue.php
   │  │  │  │     │  │  ├── stubs
   │  │  │  │     │  │  │  ├── batches.stub
   │  │  │  │     │  │  │  ├── failed_jobs.stub
   │  │  │  │     │  │  │  └── jobs.stub
   │  │  │  │     │  │  ├── PruneBatchesCommand.php
   │  │  │  │     │  │  └── ClearCommand.php
   │  │  │  │     │  ├── TimeoutExceededException.php
   │  │  │  │     │  ├── InteractsWithQueue.php
   │  │  │  │     │  ├── Capsule
   │  │  │  │     │  │  └── Manager.php
   │  │  │  │     │  ├── ListenerOptions.php
   │  │  │  │     │  ├── QueueServiceProvider.php
   │  │  │  │     │  ├── DatabaseQueue.php
   │  │  │  │     │  ├── CallQueuedHandler.php
   │  │  │  │     │  ├── SqsQueue.php
   │  │  │  │     │  ├── Worker.php
   │  │  │  │     │  ├── QueueRoutes.php
   │  │  │  │     │  ├── QueueManager.php
   │  │  │  │     │  └── Queue.php
   │  │  │  │    ├── Mail
   │  │  │  │     │  ├── TextMessage.php
   │  │  │  │     │  ├── SentMessage.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── Message.php
   │  │  │  │     │  ├── Markdown.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── SendQueuedMailable.php
   │  │  │  │     │  ├── PendingMail.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── MessageSent.php
   │  │  │  │     │  │  └── MessageSending.php
   │  │  │  │     │  ├── Mailable.php
   │  │  │  │     │  ├── MailManager.php
   │  │  │  │     │  ├── Transport
   │  │  │  │     │  │  ├── SesTransport.php
   │  │  │  │     │  │  ├── SesV2Transport.php
   │  │  │  │     │  │  ├── ArrayTransport.php
   │  │  │  │     │  │  ├── ResendTransport.php
   │  │  │  │     │  │  └── LogTransport.php
   │  │  │  │     │  ├── Mailables
   │  │  │  │     │  │  ├── Content.php
   │  │  │  │     │  │  ├── Envelope.php
   │  │  │  │     │  │  ├── Address.php
   │  │  │  │     │  │  ├── Headers.php
   │  │  │  │     │  │  └── Attachment.php
   │  │  │  │     │  ├── Mailer.php
   │  │  │  │     │  ├── MailServiceProvider.php
   │  │  │  │     │  ├── resources
   │  │  │  │     │  │  └── views
   │  │  │  │     │  │    ├── html
   │  │  │  │     │  │     │  ├── layout.blade.php
   │  │  │  │     │  │     │  ├── button.blade.php
   │  │  │  │     │  │     │  ├── footer.blade.php
   │  │  │  │     │  │     │  ├── header.blade.php
   │  │  │  │     │  │     │  ├── subcopy.blade.php
   │  │  │  │     │  │     │  ├── panel.blade.php
   │  │  │  │     │  │     │  ├── message.blade.php
   │  │  │  │     │  │     │  ├── table.blade.php
   │  │  │  │     │  │     │  └── themes
   │  │  │  │     │  │     │    └── default.css
   │  │  │  │     │  │    └── text
   │  │  │  │     │  │       ├── layout.blade.php
   │  │  │  │     │  │       ├── button.blade.php
   │  │  │  │     │  │       ├── footer.blade.php
   │  │  │  │     │  │       ├── header.blade.php
   │  │  │  │     │  │       ├── subcopy.blade.php
   │  │  │  │     │  │       ├── panel.blade.php
   │  │  │  │     │  │       ├── message.blade.php
   │  │  │  │     │  │       └── table.blade.php
   │  │  │  │     │  └── Attachment.php
   │  │  │  │    ├── Encryption
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── MissingAppKeyException.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── EncryptionServiceProvider.php
   │  │  │  │     │  └── Encrypter.php
   │  │  │  │    ├── Http
   │  │  │  │     │  ├── ResponseTrait.php
   │  │  │  │     │  ├── File.php
   │  │  │  │     │  ├── Request.php
   │  │  │  │     │  ├── Response.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── Resources
   │  │  │  │     │  │  ├── Attributes
   │  │  │  │     │  │  │  ├── Collects.php
   │  │  │  │     │  │  │  └── PreserveKeys.php
   │  │  │  │     │  │  ├── DelegatesToResource.php
   │  │  │  │     │  │  ├── CollectsResources.php
   │  │  │  │     │  │  ├── MissingValue.php
   │  │  │  │     │  │  ├── JsonApi
   │  │  │  │     │  │  │  ├── JsonApiResource.php
   │  │  │  │     │  │  │  ├── JsonApiRequest.php
   │  │  │  │     │  │  │  ├── RelationResolver.php
   │  │  │  │     │  │  │  ├── AnonymousResourceCollection.php
   │  │  │  │     │  │  │  ├── Concerns
   │  │  │  │     │  │  │  │  ├── ResolvesJsonApiElements.php
   │  │  │  │     │  │  │  │  └── ResolvesJsonApiRequest.php
   │  │  │  │     │  │  │  └── Exceptions
   │  │  │  │     │  │  │    └── ResourceIdentificationException.php
   │  │  │  │     │  │  ├── MergeValue.php
   │  │  │  │     │  │  ├── ConditionallyLoadsAttributes.php
   │  │  │  │     │  │  ├── Json
   │  │  │  │     │  │  │  ├── ResourceResponse.php
   │  │  │  │     │  │  │  ├── ResourceCollection.php
   │  │  │  │     │  │  │  ├── JsonResource.php
   │  │  │  │     │  │  │  ├── AnonymousResourceCollection.php
   │  │  │  │     │  │  │  └── PaginatedResourceResponse.php
   │  │  │  │     │  │  └── PotentiallyMissing.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Middleware
   │  │  │  │     │  │  ├── SetCacheHeaders.php
   │  │  │  │     │  │  ├── FrameGuard.php
   │  │  │  │     │  │  ├── ValidatePostSize.php
   │  │  │  │     │  │  ├── HandleCors.php
   │  │  │  │     │  │  ├── ValidatePathEncoding.php
   │  │  │  │     │  │  ├── TrustProxies.php
   │  │  │  │     │  │  ├── AddLinkHeadersForPreloadedAssets.php
   │  │  │  │     │  │  ├── TrustHosts.php
   │  │  │  │     │  │  └── CheckResponseForModifications.php
   │  │  │  │     │  ├── UploadedFile.php
   │  │  │  │     │  ├── JsonResponse.php
   │  │  │  │     │  ├── Concerns
   │  │  │  │     │  │  ├── InteractsWithInput.php
   │  │  │  │     │  │  ├── InteractsWithContentTypes.php
   │  │  │  │     │  │  ├── CanBePrecognitive.php
   │  │  │  │     │  │  └── InteractsWithFlashData.php
   │  │  │  │     │  ├── Exceptions
   │  │  │  │     │  │  ├── PostTooLargeException.php
   │  │  │  │     │  │  ├── HttpResponseException.php
   │  │  │  │     │  │  ├── MalformedUrlException.php
   │  │  │  │     │  │  ├── OriginMismatchException.php
   │  │  │  │     │  │  └── ThrottleRequestsException.php
   │  │  │  │     │  ├── RedirectResponse.php
   │  │  │  │     │  ├── StreamedEvent.php
   │  │  │  │     │  ├── FileHelpers.php
   │  │  │  │     │  ├── Client
   │  │  │  │     │  │  ├── HttpClientException.php
   │  │  │  │     │  │  ├── Request.php
   │  │  │  │     │  │  ├── Response.php
   │  │  │  │     │  │  ├── ConnectionException.php
   │  │  │  │     │  │  ├── Pool.php
   │  │  │  │     │  │  ├── Batch.php
   │  │  │  │     │  │  ├── Events
   │  │  │  │     │  │  │  ├── ResponseReceived.php
   │  │  │  │     │  │  │  ├── RequestSending.php
   │  │  │  │     │  │  │  └── ConnectionFailed.php
   │  │  │  │     │  │  ├── Concerns
   │  │  │  │     │  │  │  └── DeterminesStatusCode.php
   │  │  │  │     │  │  ├── Factory.php
   │  │  │  │     │  │  ├── PendingRequest.php
   │  │  │  │     │  │  ├── Promises
   │  │  │  │     │  │  │  ├── FluentPromise.php
   │  │  │  │     │  │  │  └── LazyPromise.php
   │  │  │  │     │  │  ├── ResponseSequence.php
   │  │  │  │     │  │  ├── StrayRequestException.php
   │  │  │  │     │  │  ├── RequestException.php
   │  │  │  │     │  │  └── BatchInProgressException.php
   │  │  │  │     │  └── Testing
   │  │  │  │     │    ├── File.php
   │  │  │  │     │    ├── FileFactory.php
   │  │  │  │     │    └── MimeType.php
   │  │  │  │    ├── Config
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── Repository.php
   │  │  │  │     │  └── composer.json
   │  │  │  │    ├── Filesystem
   │  │  │  │     │  ├── LockableFile.php
   │  │  │  │     │  ├── AwsS3V3Adapter.php
   │  │  │  │     │  ├── FilesystemManager.php
   │  │  │  │     │  ├── LocalFilesystemAdapter.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── ReceiveFile.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── ServeFile.php
   │  │  │  │     │  ├── functions.php
   │  │  │  │     │  ├── Filesystem.php
   │  │  │  │     │  ├── FilesystemServiceProvider.php
   │  │  │  │     │  └── FilesystemAdapter.php
   │  │  │  │    ├── Console
   │  │  │  │     │  ├── ContainerCommandLoader.php
   │  │  │  │     │  ├── Application.php
   │  │  │  │     │  ├── Attributes
   │  │  │  │     │  │  ├── Hidden.php
   │  │  │  │     │  │  ├── Aliases.php
   │  │  │  │     │  │  ├── Description.php
   │  │  │  │     │  │  ├── Usage.php
   │  │  │  │     │  │  ├── Help.php
   │  │  │  │     │  │  └── Signature.php
   │  │  │  │     │  ├── PromptValidationException.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── CommandMutex.php
   │  │  │  │     │  ├── Signals.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── ConfirmableTrait.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── ScheduledTaskSkipped.php
   │  │  │  │     │  │  ├── ScheduledTaskFinished.php
   │  │  │  │     │  │  ├── ScheduledBackgroundTaskFinished.php
   │  │  │  │     │  │  ├── SchedulePaused.php
   │  │  │  │     │  │  ├── CommandStarting.php
   │  │  │  │     │  │  ├── ScheduledTaskFailed.php
   │  │  │  │     │  │  ├── ScheduleResumed.php
   │  │  │  │     │  │  ├── ScheduledTaskStarting.php
   │  │  │  │     │  │  ├── ArtisanStarting.php
   │  │  │  │     │  │  └── CommandFinished.php
   │  │  │  │     │  ├── View
   │  │  │  │     │  │  ├── TaskResult.php
   │  │  │  │     │  │  └── Components
   │  │  │  │     │  │    ├── AskWithCompletion.php
   │  │  │  │     │  │    ├── Success.php
   │  │  │  │     │  │    ├── Choice.php
   │  │  │  │     │  │    ├── Component.php
   │  │  │  │     │  │    ├── Alert.php
   │  │  │  │     │  │    ├── Warn.php
   │  │  │  │     │  │    ├── Ask.php
   │  │  │  │     │  │    ├── BulletList.php
   │  │  │  │     │  │    ├── Factory.php
   │  │  │  │     │  │    ├── Secret.php
   │  │  │  │     │  │    ├── Confirm.php
   │  │  │  │     │  │    ├── Error.php
   │  │  │  │     │  │    ├── TwoColumnDetail.php
   │  │  │  │     │  │    ├── Mutators
   │  │  │  │     │  │     │  ├── EnsureDynamicContentIsHighlighted.php
   │  │  │  │     │  │     │  ├── EnsurePunctuation.php
   │  │  │  │     │  │     │  ├── EnsureRelativePaths.php
   │  │  │  │     │  │     │  └── EnsureNoPunctuation.php
   │  │  │  │     │  │    ├── Info.php
   │  │  │  │     │  │    ├── Task.php
   │  │  │  │     │  │    └── Line.php
   │  │  │  │     │  ├── ManuallyFailedException.php
   │  │  │  │     │  ├── Command.php
   │  │  │  │     │  ├── Concerns
   │  │  │  │     │  │  ├── FindsAvailableModels.php
   │  │  │  │     │  │  ├── PromptsForMissingInput.php
   │  │  │  │     │  │  ├── InteractsWithIO.php
   │  │  │  │     │  │  ├── CreatesMatchingTest.php
   │  │  │  │     │  │  ├── ConfiguresPrompts.php
   │  │  │  │     │  │  ├── CallsCommands.php
   │  │  │  │     │  │  ├── InteractsWithSignals.php
   │  │  │  │     │  │  └── HasParameters.php
   │  │  │  │     │  ├── Contracts
   │  │  │  │     │  │  └── NewLineAware.php
   │  │  │  │     │  ├── BufferedConsoleOutput.php
   │  │  │  │     │  ├── MigrationGeneratorCommand.php
   │  │  │  │     │  ├── Scheduling
   │  │  │  │     │  │  ├── EventMutex.php
   │  │  │  │     │  │  ├── CacheEventMutex.php
   │  │  │  │     │  │  ├── CronExpressionTimezoneConverter.php
   │  │  │  │     │  │  ├── ScheduleRunCommand.php
   │  │  │  │     │  │  ├── Schedule.php
   │  │  │  │     │  │  ├── CommandBuilder.php
   │  │  │  │     │  │  ├── ScheduleTestCommand.php
   │  │  │  │     │  │  ├── ScheduleInterruptCommand.php
   │  │  │  │     │  │  ├── ScheduleListCommand.php
   │  │  │  │     │  │  ├── ManagesAttributes.php
   │  │  │  │     │  │  ├── ScheduleResumeCommand.php
   │  │  │  │     │  │  ├── CacheSchedulingMutex.php
   │  │  │  │     │  │  ├── ScheduleFinishCommand.php
   │  │  │  │     │  │  ├── ScheduleWorkCommand.php
   │  │  │  │     │  │  ├── CallbackEvent.php
   │  │  │  │     │  │  ├── ManagesFrequencies.php
   │  │  │  │     │  │  ├── CacheAware.php
   │  │  │  │     │  │  ├── Event.php
   │  │  │  │     │  │  ├── SchedulePauseCommand.php
   │  │  │  │     │  │  ├── PendingEventAttributes.php
   │  │  │  │     │  │  ├── SchedulingMutex.php
   │  │  │  │     │  │  └── ScheduleClearCacheCommand.php
   │  │  │  │     │  ├── Parser.php
   │  │  │  │     │  ├── GeneratorCommand.php
   │  │  │  │     │  ├── resources
   │  │  │  │     │  │  └── views
   │  │  │  │     │  │    └── components
   │  │  │  │     │  │       ├── alert.php
   │  │  │  │     │  │       ├── two-column-detail.php
   │  │  │  │     │  │       ├── line.php
   │  │  │  │     │  │       └── bullet-list.php
   │  │  │  │     │  ├── CacheCommandMutex.php
   │  │  │  │     │  ├── Prohibitable.php
   │  │  │  │     │  ├── QuestionHelper.php
   │  │  │  │     │  └── OutputStyle.php
   │  │  │  │    ├── Contracts
   │  │  │  │     │  ├── Session
   │  │  │  │     │  │  ├── Session.php
   │  │  │  │     │  │  └── Middleware
   │  │  │  │     │  │    └── AuthenticatesSessions.php
   │  │  │  │     │  ├── Container
   │  │  │  │     │  │  ├── ContextualBindingBuilder.php
   │  │  │  │     │  │  ├── BindingResolutionException.php
   │  │  │  │     │  │  ├── Container.php
   │  │  │  │     │  │  ├── SelfBuilding.php
   │  │  │  │     │  │  ├── ContextualAttribute.php
   │  │  │  │     │  │  └── CircularDependencyException.php
   │  │  │  │     │  ├── Concurrency
   │  │  │  │     │  │  └── Driver.php
   │  │  │  │     │  ├── Pipeline
   │  │  │  │     │  │  ├── Hub.php
   │  │  │  │     │  │  └── Pipeline.php
   │  │  │  │     │  ├── Support
   │  │  │  │     │  │  ├── Renderable.php
   │  │  │  │     │  │  ├── HasOnceHash.php
   │  │  │  │     │  │  ├── DeferrableProvider.php
   │  │  │  │     │  │  ├── Jsonable.php
   │  │  │  │     │  │  ├── Responsable.php
   │  │  │  │     │  │  ├── CanBeEscapedWhenCastToString.php
   │  │  │  │     │  │  ├── MessageBag.php
   │  │  │  │     │  │  ├── ValidatedData.php
   │  │  │  │     │  │  ├── DeferringDisplayableValue.php
   │  │  │  │     │  │  ├── MessageProvider.php
   │  │  │  │     │  │  ├── Htmlable.php
   │  │  │  │     │  │  └── Arrayable.php
   │  │  │  │     │  ├── Debug
   │  │  │  │     │  │  ├── ExceptionHandler.php
   │  │  │  │     │  │  └── ShouldntReport.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── Redis
   │  │  │  │     │  │  ├── LimiterTimeoutException.php
   │  │  │  │     │  │  ├── Connector.php
   │  │  │  │     │  │  ├── Factory.php
   │  │  │  │     │  │  └── Connection.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Database
   │  │  │  │     │  │  ├── ModelIdentifier.php
   │  │  │  │     │  │  ├── Events
   │  │  │  │     │  │  │  └── MigrationEvent.php
   │  │  │  │     │  │  ├── ConcurrencyErrorDetector.php
   │  │  │  │     │  │  ├── Eloquent
   │  │  │  │     │  │  │  ├── SerializesCastableAttributes.php
   │  │  │  │     │  │  │  ├── CastsInboundAttributes.php
   │  │  │  │     │  │  │  ├── SupportsPartialRelations.php
   │  │  │  │     │  │  │  ├── DeviatesCastableAttributes.php
   │  │  │  │     │  │  │  ├── Builder.php
   │  │  │  │     │  │  │  ├── ComparesCastableAttributes.php
   │  │  │  │     │  │  │  ├── Castable.php
   │  │  │  │     │  │  │  └── CastsAttributes.php
   │  │  │  │     │  │  ├── Query
   │  │  │  │     │  │  │  ├── Builder.php
   │  │  │  │     │  │  │  ├── Expression.php
   │  │  │  │     │  │  │  └── ConditionExpression.php
   │  │  │  │     │  │  └── LostConnectionDetector.php
   │  │  │  │     │  ├── Log
   │  │  │  │     │  │  └── ContextLogProcessor.php
   │  │  │  │     │  ├── Hashing
   │  │  │  │     │  │  └── Hasher.php
   │  │  │  │     │  ├── Bus
   │  │  │  │     │  │  ├── Dispatcher.php
   │  │  │  │     │  │  └── QueueingDispatcher.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── ShouldDispatchAfterCommit.php
   │  │  │  │     │  │  ├── Dispatcher.php
   │  │  │  │     │  │  └── ShouldHandleEventsAfterCommit.php
   │  │  │  │     │  ├── View
   │  │  │  │     │  │  ├── View.php
   │  │  │  │     │  │  ├── ViewCompilationException.php
   │  │  │  │     │  │  ├── Engine.php
   │  │  │  │     │  │  └── Factory.php
   │  │  │  │     │  ├── Queue
   │  │  │  │     │  │  ├── QueueableEntity.php
   │  │  │  │     │  │  ├── ClearableQueue.php
   │  │  │  │     │  │  ├── QueueableCollection.php
   │  │  │  │     │  │  ├── EntityNotFoundException.php
   │  │  │  │     │  │  ├── ShouldBeUniqueUntilProcessing.php
   │  │  │  │     │  │  ├── Job.php
   │  │  │  │     │  │  ├── Factory.php
   │  │  │  │     │  │  ├── ShouldQueue.php
   │  │  │  │     │  │  ├── EntityResolver.php
   │  │  │  │     │  │  ├── Monitor.php
   │  │  │  │     │  │  ├── Queue.php
   │  │  │  │     │  │  ├── ShouldBeUnique.php
   │  │  │  │     │  │  ├── ShouldBeEncrypted.php
   │  │  │  │     │  │  └── ShouldQueueAfterCommit.php
   │  │  │  │     │  ├── Mail
   │  │  │  │     │  │  ├── Attachable.php
   │  │  │  │     │  │  ├── Mailable.php
   │  │  │  │     │  │  ├── Mailer.php
   │  │  │  │     │  │  ├── Factory.php
   │  │  │  │     │  │  └── MailQueue.php
   │  │  │  │     │  ├── Encryption
   │  │  │  │     │  │  ├── EncryptException.php
   │  │  │  │     │  │  ├── StringEncrypter.php
   │  │  │  │     │  │  ├── DecryptException.php
   │  │  │  │     │  │  └── Encrypter.php
   │  │  │  │     │  ├── Http
   │  │  │  │     │  │  └── Kernel.php
   │  │  │  │     │  ├── Config
   │  │  │  │     │  │  └── Repository.php
   │  │  │  │     │  ├── Filesystem
   │  │  │  │     │  │  ├── Cloud.php
   │  │  │  │     │  │  ├── FileNotFoundException.php
   │  │  │  │     │  │  ├── Factory.php
   │  │  │  │     │  │  ├── Filesystem.php
   │  │  │  │     │  │  └── LockTimeoutException.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  ├── Application.php
   │  │  │  │     │  │  ├── PromptsForMissingInput.php
   │  │  │  │     │  │  ├── Isolatable.php
   │  │  │  │     │  │  └── Kernel.php
   │  │  │  │     │  ├── Auth
   │  │  │  │     │  │  ├── Authenticatable.php
   │  │  │  │     │  │  ├── SupportsBasicAuth.php
   │  │  │  │     │  │  ├── StatefulGuard.php
   │  │  │  │     │  │  ├── UserProvider.php
   │  │  │  │     │  │  ├── Middleware
   │  │  │  │     │  │  │  └── AuthenticatesRequests.php
   │  │  │  │     │  │  ├── PasswordBrokerFactory.php
   │  │  │  │     │  │  ├── PasswordBroker.php
   │  │  │  │     │  │  ├── Factory.php
   │  │  │  │     │  │  ├── CanResetPassword.php
   │  │  │  │     │  │  ├── MustVerifyEmail.php
   │  │  │  │     │  │  ├── Access
   │  │  │  │     │  │  │  ├── Gate.php
   │  │  │  │     │  │  │  └── Authorizable.php
   │  │  │  │     │  │  └── Guard.php
   │  │  │  │     │  ├── Cookie
   │  │  │  │     │  │  ├── QueueingFactory.php
   │  │  │  │     │  │  └── Factory.php
   │  │  │  │     │  ├── Validation
   │  │  │  │     │  │  ├── CompilableRules.php
   │  │  │  │     │  │  ├── Validator.php
   │  │  │  │     │  │  ├── Rule.php
   │  │  │  │     │  │  ├── InvokableRule.php
   │  │  │  │     │  │  ├── DataAwareRule.php
   │  │  │  │     │  │  ├── ValidatesWhenResolved.php
   │  │  │  │     │  │  ├── UncompromisedVerifier.php
   │  │  │  │     │  │  ├── Factory.php
   │  │  │  │     │  │  ├── ValidatorAwareRule.php
   │  │  │  │     │  │  ├── ImplicitRule.php
   │  │  │  │     │  │  └── ValidationRule.php
   │  │  │  │     │  ├── Routing
   │  │  │  │     │  │  ├── Registrar.php
   │  │  │  │     │  │  ├── BindingRegistrar.php
   │  │  │  │     │  │  ├── UrlGenerator.php
   │  │  │  │     │  │  ├── UrlRoutable.php
   │  │  │  │     │  │  └── ResponseFactory.php
   │  │  │  │     │  ├── Broadcasting
   │  │  │  │     │  │  ├── ShouldRescue.php
   │  │  │  │     │  │  ├── ShouldBroadcastNow.php
   │  │  │  │     │  │  ├── Broadcaster.php
   │  │  │  │     │  │  ├── ShouldBroadcast.php
   │  │  │  │     │  │  ├── HasBroadcastChannel.php
   │  │  │  │     │  │  ├── Factory.php
   │  │  │  │     │  │  └── ShouldBeUnique.php
   │  │  │  │     │  ├── JsonSchema
   │  │  │  │     │  │  └── JsonSchema.php
   │  │  │  │     │  ├── Notifications
   │  │  │  │     │  │  ├── Dispatcher.php
   │  │  │  │     │  │  └── Factory.php
   │  │  │  │     │  ├── Pagination
   │  │  │  │     │  │  ├── LengthAwarePaginator.php
   │  │  │  │     │  │  ├── CursorPaginator.php
   │  │  │  │     │  │  └── Paginator.php
   │  │  │  │     │  ├── Process
   │  │  │  │     │  │  ├── InvokedProcess.php
   │  │  │  │     │  │  └── ProcessResult.php
   │  │  │  │     │  ├── Foundation
   │  │  │  │     │  │  ├── Application.php
   │  │  │  │     │  │  ├── MaintenanceMode.php
   │  │  │  │     │  │  ├── ExceptionRenderer.php
   │  │  │  │     │  │  ├── CachesRoutes.php
   │  │  │  │     │  │  └── CachesConfiguration.php
   │  │  │  │     │  ├── Cache
   │  │  │  │     │  │  ├── Repository.php
   │  │  │  │     │  │  ├── Store.php
   │  │  │  │     │  │  ├── Factory.php
   │  │  │  │     │  │  ├── LockProvider.php
   │  │  │  │     │  │  ├── Lock.php
   │  │  │  │     │  │  ├── CanFlushLocks.php
   │  │  │  │     │  │  └── LockTimeoutException.php
   │  │  │  │     │  └── Translation
   │  │  │  │     │    ├── Translator.php
   │  │  │  │     │    ├── Loader.php
   │  │  │  │     │    └── HasLocalePreference.php
   │  │  │  │    ├── Auth
   │  │  │  │     │  ├── Authenticatable.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── AuthManager.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Middleware
   │  │  │  │     │  │  ├── RequirePassword.php
   │  │  │  │     │  │  ├── RedirectIfAuthenticated.php
   │  │  │  │     │  │  ├── AuthenticateWithBasicAuth.php
   │  │  │  │     │  │  ├── Authorize.php
   │  │  │  │     │  │  ├── Authenticate.php
   │  │  │  │     │  │  └── EnsureEmailIsVerified.php
   │  │  │  │     │  ├── GenericUser.php
   │  │  │  │     │  ├── Passwords
   │  │  │  │     │  │  ├── DatabaseTokenRepository.php
   │  │  │  │     │  │  ├── TokenRepositoryInterface.php
   │  │  │  │     │  │  ├── PasswordBroker.php
   │  │  │  │     │  │  ├── PasswordResetServiceProvider.php
   │  │  │  │     │  │  ├── CanResetPassword.php
   │  │  │  │     │  │  ├── CacheTokenRepository.php
   │  │  │  │     │  │  └── PasswordBrokerManager.php
   │  │  │  │     │  ├── RequestGuard.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── Logout.php
   │  │  │  │     │  │  ├── PasswordResetLinkSent.php
   │  │  │  │     │  │  ├── Validated.php
   │  │  │  │     │  │  ├── Verified.php
   │  │  │  │     │  │  ├── PasswordReset.php
   │  │  │  │     │  │  ├── Login.php
   │  │  │  │     │  │  ├── Registered.php
   │  │  │  │     │  │  ├── Lockout.php
   │  │  │  │     │  │  ├── Authenticated.php
   │  │  │  │     │  │  ├── Failed.php
   │  │  │  │     │  │  ├── Attempting.php
   │  │  │  │     │  │  ├── CurrentDeviceLogout.php
   │  │  │  │     │  │  └── OtherDeviceLogout.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  ├── ClearResetsCommand.php
   │  │  │  │     │  │  └── stubs
   │  │  │  │     │  │    └── make
   │  │  │  │     │  │       └── views
   │  │  │  │     │  │          └── layouts
   │  │  │  │     │  │             └── app.stub
   │  │  │  │     │  ├── SessionGuard.php
   │  │  │  │     │  ├── GuardHelpers.php
   │  │  │  │     │  ├── TokenGuard.php
   │  │  │  │     │  ├── MustVerifyEmail.php
   │  │  │  │     │  ├── CreatesUserProviders.php
   │  │  │  │     │  ├── EloquentUserProvider.php
   │  │  │  │     │  ├── DatabaseUserProvider.php
   │  │  │  │     │  ├── Notifications
   │  │  │  │     │  │  ├── VerifyEmail.php
   │  │  │  │     │  │  └── ResetPassword.php
   │  │  │  │     │  ├── Access
   │  │  │  │     │  │  ├── Gate.php
   │  │  │  │     │  │  ├── Response.php
   │  │  │  │     │  │  ├── Events
   │  │  │  │     │  │  │  └── GateEvaluated.php
   │  │  │  │     │  │  ├── HandlesAuthorization.php
   │  │  │  │     │  │  └── AuthorizationException.php
   │  │  │  │     │  ├── AuthServiceProvider.php
   │  │  │  │     │  ├── Listeners
   │  │  │  │     │  │  └── SendEmailVerificationNotification.php
   │  │  │  │     │  ├── Recaller.php
   │  │  │  │     │  └── AuthenticationException.php
   │  │  │  │    ├── Cookie
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Middleware
   │  │  │  │     │  │  ├── EncryptCookies.php
   │  │  │  │     │  │  └── AddQueuedCookiesToResponse.php
   │  │  │  │     │  ├── CookieValuePrefix.php
   │  │  │  │     │  ├── CookieJar.php
   │  │  │  │     │  └── CookieServiceProvider.php
   │  │  │  │    ├── Validation
   │  │  │  │     │  ├── NotPwnedVerifier.php
   │  │  │  │     │  ├── DatabasePresenceVerifier.php
   │  │  │  │     │  ├── Validator.php
   │  │  │  │     │  ├── ValidatesWhenResolvedTrait.php
   │  │  │  │     │  ├── ValidationRuleParser.php
   │  │  │  │     │  ├── Rule.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── ConditionalRules.php
   │  │  │  │     │  ├── ValidationException.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── PresenceVerifierInterface.php
   │  │  │  │     │  ├── DatabasePresenceVerifierInterface.php
   │  │  │  │     │  ├── ValidationData.php
   │  │  │  │     │  ├── UnauthorizedException.php
   │  │  │  │     │  ├── ValidationServiceProvider.php
   │  │  │  │     │  ├── Concerns
   │  │  │  │     │  │  ├── ReplacesAttributes.php
   │  │  │  │     │  │  ├── FilterEmailValidation.php
   │  │  │  │     │  │  ├── ValidatesAttributes.php
   │  │  │  │     │  │  └── FormatsMessages.php
   │  │  │  │     │  ├── Factory.php
   │  │  │  │     │  ├── ClosureValidationRule.php
   │  │  │  │     │  ├── NestedRules.php
   │  │  │  │     │  ├── InvokableValidationRule.php
   │  │  │  │     │  └── Rules
   │  │  │  │     │    ├── Exists.php
   │  │  │  │     │    ├── RequiredIf.php
   │  │  │  │     │    ├── Date.php
   │  │  │  │     │    ├── File.php
   │  │  │  │     │    ├── ProhibitedUnless.php
   │  │  │  │     │    ├── NotIn.php
   │  │  │  │     │    ├── ImageFile.php
   │  │  │  │     │    ├── StringRule.php
   │  │  │  │     │    ├── ArrayRule.php
   │  │  │  │     │    ├── Email.php
   │  │  │  │     │    ├── DatabaseRule.php
   │  │  │  │     │    ├── ProhibitedIf.php
   │  │  │  │     │    ├── Contains.php
   │  │  │  │     │    ├── RequiredUnless.php
   │  │  │  │     │    ├── ExcludeIf.php
   │  │  │  │     │    ├── Enum.php
   │  │  │  │     │    ├── In.php
   │  │  │  │     │    ├── ExcludeUnless.php
   │  │  │  │     │    ├── DoesntContain.php
   │  │  │  │     │    ├── AnyOf.php
   │  │  │  │     │    ├── Password.php
   │  │  │  │     │    ├── Numeric.php
   │  │  │  │     │    ├── Unique.php
   │  │  │  │     │    ├── Dimensions.php
   │  │  │  │     │    └── Can.php
   │  │  │  │    ├── Routing
   │  │  │  │     │  ├── RouteBinding.php
   │  │  │  │     │  ├── PendingResourceRegistration.php
   │  │  │  │     │  ├── Attributes
   │  │  │  │     │  │  └── Controllers
   │  │  │  │     │  │    ├── Middleware.php
   │  │  │  │     │  │    └── Authorize.php
   │  │  │  │     │  ├── RouteParameterBinder.php
   │  │  │  │     │  ├── CallableDispatcher.php
   │  │  │  │     │  ├── Matching
   │  │  │  │     │  │  ├── HostValidator.php
   │  │  │  │     │  │  ├── MethodValidator.php
   │  │  │  │     │  │  ├── UriValidator.php
   │  │  │  │     │  │  ├── ValidatorInterface.php
   │  │  │  │     │  │  └── SchemeValidator.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── UrlGenerator.php
   │  │  │  │     │  ├── ViewController.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Middleware
   │  │  │  │     │  │  ├── ThrottleRequestsWithRedis.php
   │  │  │  │     │  │  ├── ThrottleRequests.php
   │  │  │  │     │  │  ├── SubstituteBindings.php
   │  │  │  │     │  │  └── ValidateSignature.php
   │  │  │  │     │  ├── ImplicitRouteBinding.php
   │  │  │  │     │  ├── ControllerMiddlewareOptions.php
   │  │  │  │     │  ├── RoutingServiceProvider.php
   │  │  │  │     │  ├── RouteCollectionInterface.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── PreparingResponse.php
   │  │  │  │     │  │  ├── RouteMatched.php
   │  │  │  │     │  │  ├── ResponsePrepared.php
   │  │  │  │     │  │  └── Routing.php
   │  │  │  │     │  ├── SortedMiddleware.php
   │  │  │  │     │  ├── Pipeline.php
   │  │  │  │     │  ├── Redirector.php
   │  │  │  │     │  ├── RouteUri.php
   │  │  │  │     │  ├── ControllerDispatcher.php
   │  │  │  │     │  ├── RedirectController.php
   │  │  │  │     │  ├── RouteAction.php
   │  │  │  │     │  ├── RouteGroup.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  ├── ControllerMakeCommand.php
   │  │  │  │     │  │  ├── MiddlewareMakeCommand.php
   │  │  │  │     │  │  └── stubs
   │  │  │  │     │  │    ├── controller.model.stub
   │  │  │  │     │  │    ├── controller.nested.api.stub
   │  │  │  │     │  │    ├── controller.nested.singleton.api.stub
   │  │  │  │     │  │    ├── controller.stub
   │  │  │  │     │  │    ├── controller.model.api.stub
   │  │  │  │     │  │    ├── controller.api.stub
   │  │  │  │     │  │    ├── controller.invokable.stub
   │  │  │  │     │  │    ├── controller.singleton.api.stub
   │  │  │  │     │  │    ├── controller.singleton.stub
   │  │  │  │     │  │    ├── controller.nested.singleton.stub
   │  │  │  │     │  │    ├── middleware.stub
   │  │  │  │     │  │    ├── controller.plain.stub
   │  │  │  │     │  │    └── controller.nested.stub
   │  │  │  │     │  ├── CompiledRouteCollection.php
   │  │  │  │     │  ├── Contracts
   │  │  │  │     │  │  ├── CallableDispatcher.php
   │  │  │  │     │  │  └── ControllerDispatcher.php
   │  │  │  │     │  ├── AbstractRouteCollection.php
   │  │  │  │     │  ├── Controllers
   │  │  │  │     │  │  ├── Middleware.php
   │  │  │  │     │  │  └── HasMiddleware.php
   │  │  │  │     │  ├── Exceptions
   │  │  │  │     │  │  ├── BackedEnumCaseNotFoundException.php
   │  │  │  │     │  │  ├── MissingRateLimiterException.php
   │  │  │  │     │  │  ├── StreamedResponseException.php
   │  │  │  │     │  │  ├── UrlGenerationException.php
   │  │  │  │     │  │  └── InvalidSignatureException.php
   │  │  │  │     │  ├── PendingSingletonResourceRegistration.php
   │  │  │  │     │  ├── RouteFileRegistrar.php
   │  │  │  │     │  ├── RouteCollection.php
   │  │  │  │     │  ├── Router.php
   │  │  │  │     │  ├── ResolvesRouteDependencies.php
   │  │  │  │     │  ├── RouteSignatureParameters.php
   │  │  │  │     │  ├── Controller.php
   │  │  │  │     │  ├── CreatesRegularExpressionRouteConstraints.php
   │  │  │  │     │  ├── RouteUrlGenerator.php
   │  │  │  │     │  ├── ResponseFactory.php
   │  │  │  │     │  ├── ResourceRegistrar.php
   │  │  │  │     │  ├── MiddlewareNameResolver.php
   │  │  │  │     │  ├── Route.php
   │  │  │  │     │  ├── FiltersControllerMiddleware.php
   │  │  │  │     │  ├── RouteDependencyResolverTrait.php
   │  │  │  │     │  └── RouteRegistrar.php
   │  │  │  │    ├── Broadcasting
   │  │  │  │     │  ├── InteractsWithBroadcasting.php
   │  │  │  │     │  ├── EncryptedPrivateChannel.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── FakePendingBroadcast.php
   │  │  │  │     │  ├── BroadcastException.php
   │  │  │  │     │  ├── BroadcastServiceProvider.php
   │  │  │  │     │  ├── BroadcastManager.php
   │  │  │  │     │  ├── AnonymousEvent.php
   │  │  │  │     │  ├── Broadcasters
   │  │  │  │     │  │  ├── LogBroadcaster.php
   │  │  │  │     │  │  ├── RedisBroadcaster.php
   │  │  │  │     │  │  ├── UsePusherChannelConventions.php
   │  │  │  │     │  │  ├── Broadcaster.php
   │  │  │  │     │  │  ├── AblyBroadcaster.php
   │  │  │  │     │  │  ├── NullBroadcaster.php
   │  │  │  │     │  │  └── PusherBroadcaster.php
   │  │  │  │     │  ├── PendingBroadcast.php
   │  │  │  │     │  ├── PresenceChannel.php
   │  │  │  │     │  ├── PrivateChannel.php
   │  │  │  │     │  ├── BroadcastController.php
   │  │  │  │     │  ├── BroadcastEvent.php
   │  │  │  │     │  ├── InteractsWithSockets.php
   │  │  │  │     │  ├── Channel.php
   │  │  │  │     │  └── UniqueBroadcastEvent.php
   │  │  │  │    ├── JsonSchema
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── JsonSchema.php
   │  │  │  │     │  ├── JsonSchemaTypeFactory.php
   │  │  │  │     │  ├── Types
   │  │  │  │     │  │  ├── BooleanType.php
   │  │  │  │     │  │  ├── Type.php
   │  │  │  │     │  │  ├── ObjectType.php
   │  │  │  │     │  │  ├── ArrayType.php
   │  │  │  │     │  │  ├── IntegerType.php
   │  │  │  │     │  │  ├── NumberType.php
   │  │  │  │     │  │  └── StringType.php
   │  │  │  │     │  └── Serializer.php
   │  │  │  │    ├── Notifications
   │  │  │  │     │  ├── AnonymousNotifiable.php
   │  │  │  │     │  ├── DatabaseNotificationCollection.php
   │  │  │  │     │  ├── Notification.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Action.php
   │  │  │  │     │  ├── SendQueuedNotifications.php
   │  │  │  │     │  ├── DatabaseNotification.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── NotificationFailed.php
   │  │  │  │     │  │  ├── NotificationSending.php
   │  │  │  │     │  │  ├── NotificationSent.php
   │  │  │  │     │  │  └── BroadcastNotificationCreated.php
   │  │  │  │     │  ├── Notifiable.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  ├── NotificationTableCommand.php
   │  │  │  │     │  │  └── stubs
   │  │  │  │     │  │    └── notifications.stub
   │  │  │  │     │  ├── HasDatabaseNotifications.php
   │  │  │  │     │  ├── RoutesNotifications.php
   │  │  │  │     │  ├── resources
   │  │  │  │     │  │  └── views
   │  │  │  │     │  │    └── email.blade.php
   │  │  │  │     │  ├── ChannelManager.php
   │  │  │  │     │  ├── Channels
   │  │  │  │     │  │  ├── DatabaseChannel.php
   │  │  │  │     │  │  ├── MailChannel.php
   │  │  │  │     │  │  └── BroadcastChannel.php
   │  │  │  │     │  ├── NotificationServiceProvider.php
   │  │  │  │     │  ├── Messages
   │  │  │  │     │  │  ├── SimpleMessage.php
   │  │  │  │     │  │  ├── DatabaseMessage.php
   │  │  │  │     │  │  ├── MailMessage.php
   │  │  │  │     │  │  └── BroadcastMessage.php
   │  │  │  │     │  └── NotificationSender.php
   │  │  │  │    ├── Pagination
   │  │  │  │     │  ├── PaginationState.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── UrlWindow.php
   │  │  │  │     │  ├── LengthAwarePaginator.php
   │  │  │  │     │  ├── CursorPaginator.php
   │  │  │  │     │  ├── AbstractCursorPaginator.php
   │  │  │  │     │  ├── PaginationServiceProvider.php
   │  │  │  │     │  ├── Paginator.php
   │  │  │  │     │  ├── resources
   │  │  │  │     │  │  └── views
   │  │  │  │     │  │    ├── semantic-ui.blade.php
   │  │  │  │     │  │    ├── simple-bootstrap-4.blade.php
   │  │  │  │     │  │    ├── bootstrap-3.blade.php
   │  │  │  │     │  │    ├── bootstrap-4.blade.php
   │  │  │  │     │  │    ├── simple-tailwind.blade.php
   │  │  │  │     │  │    ├── simple-bootstrap-3.blade.php
   │  │  │  │     │  │    ├── tailwind.blade.php
   │  │  │  │     │  │    ├── bootstrap-5.blade.php
   │  │  │  │     │  │    └── simple-bootstrap-5.blade.php
   │  │  │  │     │  ├── AbstractPaginator.php
   │  │  │  │     │  └── Cursor.php
   │  │  │  │    ├── Process
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── InvokedProcess.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Pool.php
   │  │  │  │     │  ├── Pipe.php
   │  │  │  │     │  ├── PendingProcess.php
   │  │  │  │     │  ├── FakeProcessSequence.php
   │  │  │  │     │  ├── FakeProcessDescription.php
   │  │  │  │     │  ├── ProcessPoolResults.php
   │  │  │  │     │  ├── Factory.php
   │  │  │  │     │  ├── Exceptions
   │  │  │  │     │  │  ├── ProcessFailedException.php
   │  │  │  │     │  │  └── ProcessTimedOutException.php
   │  │  │  │     │  ├── InvokedProcessPool.php
   │  │  │  │     │  ├── FakeProcessResult.php
   │  │  │  │     │  ├── FakeInvokedProcess.php
   │  │  │  │     │  └── ProcessResult.php
   │  │  │  │    ├── Conditionable
   │  │  │  │     │  ├── Traits
   │  │  │  │     │  │  └── Conditionable.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  └── HigherOrderWhenProxy.php
   │  │  │  │    ├── Reflection
   │  │  │  │     │  ├── Traits
   │  │  │  │     │  │  └── ReflectsClosures.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── Reflector.php
   │  │  │  │     │  └── helpers.php
   │  │  │  │    ├── Foundation
   │  │  │  │     │  ├── Application.php
   │  │  │  │     │  ├── Bootstrap
   │  │  │  │     │  │  ├── LoadConfiguration.php
   │  │  │  │     │  │  ├── SetRequestForConsole.php
   │  │  │  │     │  │  ├── RegisterProviders.php
   │  │  │  │     │  │  ├── BootProviders.php
   │  │  │  │     │  │  ├── LoadEnvironmentVariables.php
   │  │  │  │     │  │  ├── HandleExceptions.php
   │  │  │  │     │  │  └── RegisterFacades.php
   │  │  │  │     │  ├── MaintenanceModeManager.php
   │  │  │  │     │  ├── Support
   │  │  │  │     │  │  └── Providers
   │  │  │  │     │  │    ├── RouteServiceProvider.php
   │  │  │  │     │  │    ├── AuthServiceProvider.php
   │  │  │  │     │  │    └── EventServiceProvider.php
   │  │  │  │     │  ├── ViteManifestNotFoundException.php
   │  │  │  │     │  ├── MixManifestNotFoundException.php
   │  │  │  │     │  ├── Cloud.php
   │  │  │  │     │  ├── Configuration
   │  │  │  │     │  │  ├── Middleware.php
   │  │  │  │     │  │  ├── Exceptions.php
   │  │  │  │     │  │  └── ApplicationBuilder.php
   │  │  │  │     │  ├── Precognition.php
   │  │  │  │     │  ├── Bus
   │  │  │  │     │  │  ├── Dispatchable.php
   │  │  │  │     │  │  ├── PendingChain.php
   │  │  │  │     │  │  ├── PendingClosureDispatch.php
   │  │  │  │     │  │  ├── PendingDispatch.php
   │  │  │  │     │  │  └── DispatchesJobs.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── Dispatchable.php
   │  │  │  │     │  │  ├── LocaleUpdated.php
   │  │  │  │     │  │  ├── DiscoverEvents.php
   │  │  │  │     │  │  ├── PublishingStubs.php
   │  │  │  │     │  │  ├── MaintenanceModeEnabled.php
   │  │  │  │     │  │  ├── Terminating.php
   │  │  │  │     │  │  ├── MaintenanceModeDisabled.php
   │  │  │  │     │  │  ├── DiagnosingHealth.php
   │  │  │  │     │  │  └── VendorTagPublished.php
   │  │  │  │     │  ├── Queue
   │  │  │  │     │  │  ├── InteractsWithUniqueJobs.php
   │  │  │  │     │  │  └── Queueable.php
   │  │  │  │     │  ├── CacheBasedMaintenanceMode.php
   │  │  │  │     │  ├── FileBasedMaintenanceMode.php
   │  │  │  │     │  ├── Http
   │  │  │  │     │  │  ├── HtmlDumper.php
   │  │  │  │     │  │  ├── Attributes
   │  │  │  │     │  │  │  ├── StopOnFirstFailure.php
   │  │  │  │     │  │  │  ├── ErrorBag.php
   │  │  │  │     │  │  │  ├── RedirectTo.php
   │  │  │  │     │  │  │  ├── FailOnUnknownFields.php
   │  │  │  │     │  │  │  └── RedirectToRoute.php
   │  │  │  │     │  │  ├── Middleware
   │  │  │  │     │  │  │  ├── CheckForMaintenanceMode.php
   │  │  │  │     │  │  │  ├── PreventRequestForgery.php
   │  │  │  │     │  │  │  ├── ValidateCsrfToken.php
   │  │  │  │     │  │  │  ├── ValidatePostSize.php
   │  │  │  │     │  │  │  ├── InvokeDeferredCallbacks.php
   │  │  │  │     │  │  │  ├── VerifyCsrfToken.php
   │  │  │  │     │  │  │  ├── ConvertEmptyStringsToNull.php
   │  │  │  │     │  │  │  ├── PreventRequestsDuringMaintenance.php
   │  │  │  │     │  │  │  ├── HandlePrecognitiveRequests.php
   │  │  │  │     │  │  │  ├── Concerns
   │  │  │  │     │  │  │  │  └── ExcludesPaths.php
   │  │  │  │     │  │  │  ├── TransformsRequest.php
   │  │  │  │     │  │  │  └── TrimStrings.php
   │  │  │  │     │  │  ├── Events
   │  │  │  │     │  │  │  └── RequestHandled.php
   │  │  │  │     │  │  ├── MaintenanceModeBypassCookie.php
   │  │  │  │     │  │  ├── FormRequest.php
   │  │  │  │     │  │  └── Kernel.php
   │  │  │  │     │  ├── Vite.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  ├── PackageDiscoverCommand.php
   │  │  │  │     │  │  ├── MailMakeCommand.php
   │  │  │  │     │  │  ├── PolicyMakeCommand.php
   │  │  │  │     │  │  ├── ObserverMakeCommand.php
   │  │  │  │     │  │  ├── ConfigShowCommand.php
   │  │  │  │     │  │  ├── AboutCommand.php
   │  │  │  │     │  │  ├── DocsCommand.php
   │  │  │  │     │  │  ├── ScopeMakeCommand.php
   │  │  │  │     │  │  ├── StubPublishCommand.php
   │  │  │  │     │  │  ├── InterfaceMakeCommand.php
   │  │  │  │     │  │  ├── ViewCacheCommand.php
   │  │  │  │     │  │  ├── EventCacheCommand.php
   │  │  │  │     │  │  ├── LangPublishCommand.php
   │  │  │  │     │  │  ├── ServeCommand.php
   │  │  │  │     │  │  ├── ClassMakeCommand.php
   │  │  │  │     │  │  ├── RouteListCommand.php
   │  │  │  │     │  │  ├── ApiInstallCommand.php
   │  │  │  │     │  │  ├── InteractsWithComposerPackages.php
   │  │  │  │     │  │  ├── RuleMakeCommand.php
   │  │  │  │     │  │  ├── ModelMakeCommand.php
   │  │  │  │     │  │  ├── ReloadCommand.php
   │  │  │  │     │  │  ├── OptimizeCommand.php
   │  │  │  │     │  │  ├── EnvironmentEncryptCommand.php
   │  │  │  │     │  │  ├── ExceptionMakeCommand.php
   │  │  │  │     │  │  ├── EventMakeCommand.php
   │  │  │  │     │  │  ├── EnvironmentDecryptCommand.php
   │  │  │  │     │  │  ├── RouteClearCommand.php
   │  │  │  │     │  │  ├── QueuedCommand.php
   │  │  │  │     │  │  ├── ConsoleMakeCommand.php
   │  │  │  │     │  │  ├── ResourceMakeCommand.php
   │  │  │  │     │  │  ├── ListenerMakeCommand.php
   │  │  │  │     │  │  ├── EventClearCommand.php
   │  │  │  │     │  │  ├── ClearCompiledCommand.php
   │  │  │  │     │  │  ├── StorageLinkCommand.php
   │  │  │  │     │  │  ├── EventListCommand.php
   │  │  │  │     │  │  ├── EnumMakeCommand.php
   │  │  │  │     │  │  ├── OptimizeClearCommand.php
   │  │  │  │     │  │  ├── UpCommand.php
   │  │  │  │     │  │  ├── RouteCacheCommand.php
   │  │  │  │     │  │  ├── JobMakeCommand.php
   │  │  │  │     │  │  ├── DownCommand.php
   │  │  │  │     │  │  ├── JobMiddlewareMakeCommand.php
   │  │  │  │     │  │  ├── ConfigMakeCommand.php
   │  │  │  │     │  │  ├── TraitMakeCommand.php
   │  │  │  │     │  │  ├── CastMakeCommand.php
   │  │  │  │     │  │  ├── EventGenerateCommand.php
   │  │  │  │     │  │  ├── ClosureCommand.php
   │  │  │  │     │  │  ├── ViewClearCommand.php
   │  │  │  │     │  │  ├── VendorPublishCommand.php
   │  │  │  │     │  │  ├── KeyGenerateCommand.php
   │  │  │  │     │  │  ├── ChannelListCommand.php
   │  │  │  │     │  │  ├── CliDumper.php
   │  │  │  │     │  │  ├── TestMakeCommand.php
   │  │  │  │     │  │  ├── ConfigPublishCommand.php
   │  │  │  │     │  │  ├── ViewMakeCommand.php
   │  │  │  │     │  │  ├── ProviderMakeCommand.php
   │  │  │  │     │  │  ├── ChannelMakeCommand.php
   │  │  │  │     │  │  ├── ConfigClearCommand.php
   │  │  │  │     │  │  ├── ComponentMakeCommand.php
   │  │  │  │     │  │  ├── EnvironmentCommand.php
   │  │  │  │     │  │  ├── RequestMakeCommand.php
   │  │  │  │     │  │  ├── BroadcastingInstallCommand.php
   │  │  │  │     │  │  ├── StorageUnlinkCommand.php
   │  │  │  │     │  │  ├── NotificationMakeCommand.php
   │  │  │  │     │  │  ├── ConfigCacheCommand.php
   │  │  │  │     │  │  ├── stubs
   │  │  │  │     │  │  │  ├── pest.stub
   │  │  │  │     │  │  │  ├── test.stub
   │  │  │  │     │  │  │  ├── config.stub
   │  │  │  │     │  │  │  ├── echo-js-reverb.stub
   │  │  │  │     │  │  │  ├── console.stub
   │  │  │  │     │  │  │  ├── job.queued.stub
   │  │  │  │     │  │  │  ├── cast.inbound.stub
   │  │  │  │     │  │  │  ├── maintenance-mode.stub
   │  │  │  │     │  │  │  ├── job.stub
   │  │  │  │     │  │  │  ├── listener.queued.stub
   │  │  │  │     │  │  │  ├── markdown.stub
   │  │  │  │     │  │  │  ├── view-mail.stub
   │  │  │  │     │  │  │  ├── exception.stub
   │  │  │  │     │  │  │  ├── class.invokable.stub
   │  │  │  │     │  │  │  ├── view.pest.stub
   │  │  │  │     │  │  │  ├── request.stub
   │  │  │  │     │  │  │  ├── observer.stub
   │  │  │  │     │  │  │  ├── class.stub
   │  │  │  │     │  │  │  ├── routes.stub
   │  │  │  │     │  │  │  ├── listener.typed.stub
   │  │  │  │     │  │  │  ├── provider.stub
   │  │  │  │     │  │  │  ├── resource.stub
   │  │  │  │     │  │  │  ├── rule.stub
   │  │  │  │     │  │  │  ├── listener.stub
   │  │  │  │     │  │  │  ├── exception-render.stub
   │  │  │  │     │  │  │  ├── echo-js-ably.stub
   │  │  │  │     │  │  │  ├── policy.plain.stub
   │  │  │  │     │  │  │  ├── resource-collection.stub
   │  │  │  │     │  │  │  ├── observer.plain.stub
   │  │  │  │     │  │  │  ├── scope.stub
   │  │  │  │     │  │  │  ├── job.batched.queued.stub
   │  │  │  │     │  │  │  ├── exception-report.stub
   │  │  │  │     │  │  │  ├── interface.stub
   │  │  │  │     │  │  │  ├── echo-bootstrap-js.stub
   │  │  │  │     │  │  │  ├── markdown-mail.stub
   │  │  │  │     │  │  │  ├── resource-json-api.stub
   │  │  │  │     │  │  │  ├── markdown-notification.stub
   │  │  │  │     │  │  │  ├── notification.stub
   │  │  │  │     │  │  │  ├── pest.unit.stub
   │  │  │  │     │  │  │  ├── view.test.stub
   │  │  │  │     │  │  │  ├── exception-render-report.stub
   │  │  │  │     │  │  │  ├── channel.stub
   │  │  │  │     │  │  │  ├── model.pivot.stub
   │  │  │  │     │  │  │  ├── job.middleware.stub
   │  │  │  │     │  │  │  ├── test.unit.stub
   │  │  │  │     │  │  │  ├── listener.typed.queued.stub
   │  │  │  │     │  │  │  ├── echo-js-pusher.stub
   │  │  │  │     │  │  │  ├── enum.stub
   │  │  │  │     │  │  │  ├── model.stub
   │  │  │  │     │  │  │  ├── rule.implicit.stub
   │  │  │  │     │  │  │  ├── enum.backed.stub
   │  │  │  │     │  │  │  ├── view-component.stub
   │  │  │  │     │  │  │  ├── trait.stub
   │  │  │  │     │  │  │  ├── api-routes.stub
   │  │  │  │     │  │  │  ├── broadcasting-routes.stub
   │  │  │  │     │  │  │  ├── mail.stub
   │  │  │  │     │  │  │  ├── model.morph-pivot.stub
   │  │  │  │     │  │  │  ├── event.stub
   │  │  │  │     │  │  │  ├── cast.stub
   │  │  │  │     │  │  │  ├── policy.stub
   │  │  │  │     │  │  │  └── view.stub
   │  │  │  │     │  │  └── Kernel.php
   │  │  │  │     │  ├── Concerns
   │  │  │  │     │  │  └── ResolvesDumpSource.php
   │  │  │  │     │  ├── AliasLoader.php
   │  │  │  │     │  ├── Mix.php
   │  │  │  │     │  ├── Exceptions
   │  │  │  │     │  │  ├── ReportableHandler.php
   │  │  │  │     │  │  ├── Whoops
   │  │  │  │     │  │  │  ├── WhoopsExceptionRenderer.php
   │  │  │  │     │  │  │  └── WhoopsHandler.php
   │  │  │  │     │  │  ├── Handler.php
   │  │  │  │     │  │  ├── Renderer
   │  │  │  │     │  │  │  ├── Renderer.php
   │  │  │  │     │  │  │  ├── Listener.php
   │  │  │  │     │  │  │  ├── Exception.php
   │  │  │  │     │  │  │  ├── Frame.php
   │  │  │  │     │  │  │  └── Mappers
   │  │  │  │     │  │  │    └── BladeMapper.php
   │  │  │  │     │  │  ├── RegisterErrorViewPaths.php
   │  │  │  │     │  │  └── views
   │  │  │  │     │  │    ├── 500.blade.php
   │  │  │  │     │  │    ├── layout.blade.php
   │  │  │  │     │  │    ├── 429.blade.php
   │  │  │  │     │  │    ├── 403.blade.php
   │  │  │  │     │  │    ├── 404.blade.php
   │  │  │  │     │  │    ├── 503.blade.php
   │  │  │  │     │  │    ├── 401.blade.php
   │  │  │  │     │  │    ├── 402.blade.php
   │  │  │  │     │  │    ├── 419.blade.php
   │  │  │  │     │  │    └── minimal.blade.php
   │  │  │  │     │  ├── Auth
   │  │  │  │     │  │  ├── EmailVerificationRequest.php
   │  │  │  │     │  │  ├── User.php
   │  │  │  │     │  │  └── Access
   │  │  │  │     │  │    ├── Authorizable.php
   │  │  │  │     │  │    └── AuthorizesRequests.php
   │  │  │  │     │  ├── EnvironmentDetector.php
   │  │  │  │     │  ├── PackageManifest.php
   │  │  │  │     │  ├── Validation
   │  │  │  │     │  │  └── ValidatesRequests.php
   │  │  │  │     │  ├── Routing
   │  │  │  │     │  │  ├── PrecognitionControllerDispatcher.php
   │  │  │  │     │  │  └── PrecognitionCallableDispatcher.php
   │  │  │  │     │  ├── Inspiring.php
   │  │  │  │     │  ├── Providers
   │  │  │  │     │  │  ├── ArtisanServiceProvider.php
   │  │  │  │     │  │  ├── ComposerServiceProvider.php
   │  │  │  │     │  │  ├── ConsoleSupportServiceProvider.php
   │  │  │  │     │  │  ├── FoundationServiceProvider.php
   │  │  │  │     │  │  └── FormRequestServiceProvider.php
   │  │  │  │     │  ├── ComposerScripts.php
   │  │  │  │     │  ├── resources
   │  │  │  │     │  │  ├── server.php
   │  │  │  │     │  │  ├── exceptions
   │  │  │  │     │  │  │  └── renderer
   │  │  │  │     │  │  │    ├── dist
   │  │  │  │     │  │  │     │  ├── styles.css
   │  │  │  │     │  │  │     │  └── scripts.js
   │  │  │  │     │  │  │    ├── show.blade.php
   │  │  │  │     │  │  │    ├── styles.css
   │  │  │  │     │  │  │    ├── vite.config.js
   │  │  │  │     │  │  │    ├── markdown.blade.php
   │  │  │  │     │  │  │    ├── package.json
   │  │  │  │     │  │  │    ├── package-lock.json
   │  │  │  │     │  │  │    ├── components
   │  │  │  │     │  │  │     │  ├── vendor-frame.blade.php
   │  │  │  │     │  │  │     │  ├── request-header.blade.php
   │  │  │  │     │  │  │     │  ├── layout.blade.php
   │  │  │  │     │  │  │     │  ├── formatted-source.blade.php
   │  │  │  │     │  │  │     │  ├── query.blade.php
   │  │  │  │     │  │  │     │  ├── icons
   │  │  │  │     │  │  │     │  │  ├── database.blade.php
   │  │  │  │     │  │  │     │  │  ├── folder.blade.php
   │  │  │  │     │  │  │     │  │  ├── check.blade.php
   │  │  │  │     │  │  │     │  │  ├── chevron-left.blade.php
   │  │  │  │     │  │  │     │  │  ├── chevrons-right.blade.php
   │  │  │  │     │  │  │     │  │  ├── alert.blade.php
   │  │  │  │     │  │  │     │  │  ├── chevrons-up-down.blade.php
   │  │  │  │     │  │  │     │  │  ├── info.blade.php
   │  │  │  │     │  │  │     │  │  ├── folder-open.blade.php
   │  │  │  │     │  │  │     │  │  ├── chevrons-left.blade.php
   │  │  │  │     │  │  │     │  │  ├── chevron-right.blade.php
   │  │  │  │     │  │  │     │  │  ├── globe.blade.php
   │  │  │  │     │  │  │     │  │  ├── copy.blade.php
   │  │  │  │     │  │  │     │  │  ├── chevrons-down-up.blade.php
   │  │  │  │     │  │  │     │  │  └── laravel-ascii.blade.php
   │  │  │  │     │  │  │     │  ├── laravel-ascii-spotlight.blade.php
   │  │  │  │     │  │  │     │  ├── header.blade.php
   │  │  │  │     │  │  │     │  ├── badge.blade.php
   │  │  │  │     │  │  │     │  ├── empty-state.blade.php
   │  │  │  │     │  │  │     │  ├── frame-code.blade.php
   │  │  │  │     │  │  │     │  ├── vendor-frames.blade.php
   │  │  │  │     │  │  │     │  ├── section-container.blade.php
   │  │  │  │     │  │  │     │  ├── syntax-highlight.blade.php
   │  │  │  │     │  │  │     │  ├── routing.blade.php
   │  │  │  │     │  │  │     │  ├── request-body.blade.php
   │  │  │  │     │  │  │     │  ├── topbar.blade.php
   │  │  │  │     │  │  │     │  ├── request-url.blade.php
   │  │  │  │     │  │  │     │  ├── http-method.blade.php
   │  │  │  │     │  │  │     │  ├── previous-exceptions.blade.php
   │  │  │  │     │  │  │     │  ├── file-with-line.blade.php
   │  │  │  │     │  │  │     │  ├── trace.blade.php
   │  │  │  │     │  │  │     │  ├── routing-parameter.blade.php
   │  │  │  │     │  │  │     │  ├── frame.blade.php
   │  │  │  │     │  │  │     │  └── separator.blade.php
   │  │  │  │     │  │  │    └── scripts.js
   │  │  │  │     │  │  └── health-up.blade.php
   │  │  │  │     │  ├── ProviderRepository.php
   │  │  │  │     │  ├── MixFileNotFoundException.php
   │  │  │  │     │  ├── stubs
   │  │  │  │     │  │  └── facade.stub
   │  │  │  │     │  ├── helpers.php
   │  │  │  │     │  ├── ViteException.php
   │  │  │  │     │  └── Testing
   │  │  │  │     │    ├── WithCachedRoutes.php
   │  │  │  │     │    ├── Attributes
   │  │  │  │     │     │  ├── UnitTest.php
   │  │  │  │     │     │  ├── Seed.php
   │  │  │  │     │     │  ├── SetUp.php
   │  │  │  │     │     │  ├── Seeder.php
   │  │  │  │     │     │  └── TearDown.php
   │  │  │  │     │    ├── WithFaker.php
   │  │  │  │     │    ├── DatabaseTruncation.php
   │  │  │  │     │    ├── Traits
   │  │  │  │     │     │  └── CanConfigureMigrationCommands.php
   │  │  │  │     │    ├── RefreshDatabaseState.php
   │  │  │  │     │    ├── Wormhole.php
   │  │  │  │     │    ├── WithoutMiddleware.php
   │  │  │  │     │    ├── DatabaseTransactions.php
   │  │  │  │     │    ├── WithCachedConfig.php
   │  │  │  │     │    ├── LazilyRefreshDatabase.php
   │  │  │  │     │    ├── RefreshDatabase.php
   │  │  │  │     │    ├── DatabaseMigrations.php
   │  │  │  │     │    ├── WithConsoleEvents.php
   │  │  │  │     │    ├── Concerns
   │  │  │  │     │     │  ├── InteractsWithSession.php
   │  │  │  │     │     │  ├── MakesHttpRequests.php
   │  │  │  │     │     │  ├── InteractsWithRedis.php
   │  │  │  │     │     │  ├── InteractsWithTime.php
   │  │  │  │     │     │  ├── InteractsWithTestCaseLifecycle.php
   │  │  │  │     │     │  ├── WithoutExceptionHandlingHandler.php
   │  │  │  │     │     │  ├── InteractsWithAuthentication.php
   │  │  │  │     │     │  ├── InteractsWithDeprecationHandling.php
   │  │  │  │     │     │  ├── InteractsWithConsole.php
   │  │  │  │     │     │  ├── InteractsWithExceptionHandling.php
   │  │  │  │     │     │  ├── InteractsWithContainer.php
   │  │  │  │     │     │  ├── InteractsWithDatabase.php
   │  │  │  │     │     │  └── InteractsWithViews.php
   │  │  │  │     │    ├── TestCase.php
   │  │  │  │     │    ├── DatabaseTransactionsManager.php
   │  │  │  │     │    └── CachedState.php
   │  │  │  │    ├── Cache
   │  │  │  │     │  ├── DatabaseLock.php
   │  │  │  │     │  ├── RateLimiting
   │  │  │  │     │  │  ├── Limit.php
   │  │  │  │     │  │  ├── GlobalLimit.php
   │  │  │  │     │  │  └── Unlimited.php
   │  │  │  │     │  ├── MemoizedStore.php
   │  │  │  │     │  ├── RedisLock.php
   │  │  │  │     │  ├── CacheServiceProvider.php
   │  │  │  │     │  ├── TaggableStore.php
   │  │  │  │     │  ├── CacheManager.php
   │  │  │  │     │  ├── TaggedCache.php
   │  │  │  │     │  ├── SessionStore.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── RedisStore.php
   │  │  │  │     │  ├── Repository.php
   │  │  │  │     │  ├── MemcachedStore.php
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── RedisTagSet.php
   │  │  │  │     │  ├── ApcStore.php
   │  │  │  │     │  ├── TagSet.php
   │  │  │  │     │  ├── Limiters
   │  │  │  │     │  │  ├── ConcurrencyLimiter.php
   │  │  │  │     │  │  ├── LimiterTimeoutException.php
   │  │  │  │     │  │  └── ConcurrencyLimiterBuilder.php
   │  │  │  │     │  ├── NullStore.php
   │  │  │  │     │  ├── FileStore.php
   │  │  │  │     │  ├── MemcachedConnector.php
   │  │  │  │     │  ├── LuaScripts.php
   │  │  │  │     │  ├── Events
   │  │  │  │     │  │  ├── CacheFailedOver.php
   │  │  │  │     │  │  ├── CacheFlushFailed.php
   │  │  │  │     │  │  ├── KeyWriteFailed.php
   │  │  │  │     │  │  ├── CacheLocksFlushFailed.php
   │  │  │  │     │  │  ├── CacheEvent.php
   │  │  │  │     │  │  ├── CacheFlushed.php
   │  │  │  │     │  │  ├── CacheHit.php
   │  │  │  │     │  │  ├── WritingManyKeys.php
   │  │  │  │     │  │  ├── CacheMissed.php
   │  │  │  │     │  │  ├── KeyForgotten.php
   │  │  │  │     │  │  ├── CacheLocksFlushing.php
   │  │  │  │     │  │  ├── CacheLocksFlushed.php
   │  │  │  │     │  │  ├── WritingKey.php
   │  │  │  │     │  │  ├── RetrievingManyKeys.php
   │  │  │  │     │  │  ├── ForgettingKey.php
   │  │  │  │     │  │  ├── RetrievingKey.php
   │  │  │  │     │  │  ├── KeyForgetFailed.php
   │  │  │  │     │  │  ├── KeyWritten.php
   │  │  │  │     │  │  └── CacheFlushing.php
   │  │  │  │     │  ├── DynamoDbStore.php
   │  │  │  │     │  ├── RetrievesMultipleKeys.php
   │  │  │  │     │  ├── HasCacheLock.php
   │  │  │  │     │  ├── PhpRedisLock.php
   │  │  │  │     │  ├── Console
   │  │  │  │     │  │  ├── ForgetCommand.php
   │  │  │  │     │  │  ├── CacheTableCommand.php
   │  │  │  │     │  │  ├── PruneStaleTagsCommand.php
   │  │  │  │     │  │  ├── stubs
   │  │  │  │     │  │  │  └── cache.stub
   │  │  │  │     │  │  └── ClearCommand.php
   │  │  │  │     │  ├── CacheLock.php
   │  │  │  │     │  ├── RateLimiter.php
   │  │  │  │     │  ├── Lock.php
   │  │  │  │     │  ├── FileLock.php
   │  │  │  │     │  ├── ArrayStore.php
   │  │  │  │     │  ├── RedisTaggedCache.php
   │  │  │  │     │  ├── DatabaseStore.php
   │  │  │  │     │  ├── ArrayLock.php
   │  │  │  │     │  ├── FailoverStore.php
   │  │  │  │     │  ├── NoLock.php
   │  │  │  │     │  ├── DynamoDbLock.php
   │  │  │  │     │  ├── MemcachedLock.php
   │  │  │  │     │  └── ApcWrapper.php
   │  │  │  │    ├── Macroable
   │  │  │  │     │  ├── Traits
   │  │  │  │     │  │  └── Macroable.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  └── composer.json
   │  │  │  │    ├── Testing
   │  │  │  │     │  ├── Assert.php
   │  │  │  │     │  ├── TestComponent.php
   │  │  │  │     │  ├── TestResponseAssert.php
   │  │  │  │     │  ├── TestResponse.php
   │  │  │  │     │  ├── LICENSE.md
   │  │  │  │     │  ├── composer.json
   │  │  │  │     │  ├── PendingCommand.php
   │  │  │  │     │  ├── Constraints
   │  │  │  │     │  │  ├── SeeInHtml.php
   │  │  │  │     │  │  ├── CountInDatabase.php
   │  │  │  │     │  │  ├── HasInDatabase.php
   │  │  │  │     │  │  ├── SeeInOrder.php
   │  │  │  │     │  │  ├── ArraySubset.php
   │  │  │  │     │  │  ├── SoftDeletedInDatabase.php
   │  │  │  │     │  │  └── NotSoftDeletedInDatabase.php
   │  │  │  │     │  ├── LoggedExceptionCollection.php
   │  │  │  │     │  ├── Concerns
   │  │  │  │     │  │  ├── TestCaches.php
   │  │  │  │     │  │  ├── RunsInParallel.php
   │  │  │  │     │  │  ├── TestViews.php
   │  │  │  │     │  │  ├── TestDatabases.php
   │  │  │  │     │  │  └── AssertsStatusCodes.php
   │  │  │  │     │  ├── Exceptions
   │  │  │  │     │  │  └── InvalidArgumentException.php
   │  │  │  │     │  ├── TestView.php
   │  │  │  │     │  ├── AssertableJsonString.php
   │  │  │  │     │  ├── ParallelTestingServiceProvider.php
   │  │  │  │     │  ├── ParallelTesting.php
   │  │  │  │     │  ├── ParallelRunner.php
   │  │  │  │     │  ├── Fluent
   │  │  │  │     │  │  ├── AssertableJson.php
   │  │  │  │     │  │  └── Concerns
   │  │  │  │     │  │    ├── Has.php
   │  │  │  │     │  │    ├── Debugging.php
   │  │  │  │     │  │    ├── Interaction.php
   │  │  │  │     │  │    └── Matching.php
   │  │  │  │     │  └── ParallelConsoleOutput.php
   │  │  │  │    └── Translation
   │  │  │  │       ├── FileLoader.php
   │  │  │  │       ├── MessageSelector.php
   │  │  │  │       ├── LICENSE.md
   │  │  │  │       ├── composer.json
   │  │  │  │       ├── lang
   │  │  │  │        │  └── en
   │  │  │  │        │    ├── validation.php
   │  │  │  │        │    ├── passwords.php
   │  │  │  │        │    ├── pagination.php
   │  │  │  │        │    └── auth.php
   │  │  │  │       ├── ArrayLoader.php
   │  │  │  │       ├── Translator.php
   │  │  │  │       ├── CreatesPotentiallyTranslatedStrings.php
   │  │  │  │       ├── TranslationServiceProvider.php
   │  │  │  │       └── PotentiallyTranslatedString.php
   │  │  │  └── config-stubs
   │  │  │    └── app.php
   │  │  ├── serializable-closure
   │  │  │  ├── LICENSE.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  └── src
   │  │  │    ├── Serializers
   │  │  │     │  ├── Native.php
   │  │  │     │  └── Signed.php
   │  │  │    ├── UnsignedSerializableClosure.php
   │  │  │    ├── Support
   │  │  │     │  ├── ClosureScope.php
   │  │  │     │  ├── ClosureStream.php
   │  │  │     │  ├── ReflectionClosure.php
   │  │  │     │  └── SelfReference.php
   │  │  │    ├── SerializableClosure.php
   │  │  │    ├── Contracts
   │  │  │     │  ├── Signer.php
   │  │  │     │  └── Serializable.php
   │  │  │    ├── Exceptions
   │  │  │     │  ├── MissingSecretKeyException.php
   │  │  │     │  └── InvalidSignatureException.php
   │  │  │    └── Signers
   │  │  │       └── Hmac.php
   │  │  └── pint
   │  │    ├── overrides
   │  │     │  ├── FixerFactory.php
   │  │     │  └── Runner
   │  │     │    └── Parallel
   │  │     │       └── ProcessFactory.php
   │  │    ├── LICENSE.md
   │  │    ├── composer.json
   │  │    └── builds
   │  │       └── pint
   │  ├── doctrine
   │  │  ├── inflector
   │  │  │  ├── docs
   │  │  │  │  └── en
   │  │  │  │    └── index.rst
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── Language.php
   │  │  │  │  ├── LanguageInflectorFactory.php
   │  │  │  │  ├── GenericLanguageInflectorFactory.php
   │  │  │  │  ├── Inflector.php
   │  │  │  │  ├── WordInflector.php
   │  │  │  │  ├── CachedWordInflector.php
   │  │  │  │  ├── NoopWordInflector.php
   │  │  │  │  ├── RulesetInflector.php
   │  │  │  │  ├── Rules
   │  │  │  │  │  ├── Pattern.php
   │  │  │  │  │  ├── Ruleset.php
   │  │  │  │  │  ├── Patterns.php
   │  │  │  │  │  ├── French
   │  │  │  │  │  │  ├── Inflectible.php
   │  │  │  │  │  │  ├── Rules.php
   │  │  │  │  │  │  ├── Uninflected.php
   │  │  │  │  │  │  └── InflectorFactory.php
   │  │  │  │  │  ├── Turkish
   │  │  │  │  │  │  ├── Inflectible.php
   │  │  │  │  │  │  ├── Rules.php
   │  │  │  │  │  │  ├── Uninflected.php
   │  │  │  │  │  │  └── InflectorFactory.php
   │  │  │  │  │  ├── Transformation.php
   │  │  │  │  │  ├── Substitutions.php
   │  │  │  │  │  ├── Spanish
   │  │  │  │  │  │  ├── Inflectible.php
   │  │  │  │  │  │  ├── Rules.php
   │  │  │  │  │  │  ├── Uninflected.php
   │  │  │  │  │  │  └── InflectorFactory.php
   │  │  │  │  │  ├── Substitution.php
   │  │  │  │  │  ├── Transformations.php
   │  │  │  │  │  ├── NorwegianBokmal
   │  │  │  │  │  │  ├── Inflectible.php
   │  │  │  │  │  │  ├── Rules.php
   │  │  │  │  │  │  ├── Uninflected.php
   │  │  │  │  │  │  └── InflectorFactory.php
   │  │  │  │  │  ├── Portuguese
   │  │  │  │  │  │  ├── Inflectible.php
   │  │  │  │  │  │  ├── Rules.php
   │  │  │  │  │  │  ├── Uninflected.php
   │  │  │  │  │  │  └── InflectorFactory.php
   │  │  │  │  │  ├── Italian
   │  │  │  │  │  │  ├── Inflectible.php
   │  │  │  │  │  │  ├── Rules.php
   │  │  │  │  │  │  ├── Uninflected.php
   │  │  │  │  │  │  └── InflectorFactory.php
   │  │  │  │  │  ├── English
   │  │  │  │  │  │  ├── Inflectible.php
   │  │  │  │  │  │  ├── Rules.php
   │  │  │  │  │  │  ├── Uninflected.php
   │  │  │  │  │  │  └── InflectorFactory.php
   │  │  │  │  │  ├── Esperanto
   │  │  │  │  │  │  ├── Inflectible.php
   │  │  │  │  │  │  ├── Rules.php
   │  │  │  │  │  │  ├── Uninflected.php
   │  │  │  │  │  │  └── InflectorFactory.php
   │  │  │  │  │  └── Word.php
   │  │  │  │  └── InflectorFactory.php
   │  │  │  └── LICENSE
   │  │  └── lexer
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── UPGRADE.md
   │  │    ├── src
   │  │     │  ├── Token.php
   │  │     │  └── AbstractLexer.php
   │  │    └── LICENSE
   │  ├── symfony
   │  │  ├── mime
   │  │  │  ├── FileBinaryMimeTypeGuesser.php
   │  │  │  ├── Header
   │  │  │  │  ├── UnstructuredHeader.php
   │  │  │  │  ├── MailboxHeader.php
   │  │  │  │  ├── DateHeader.php
   │  │  │  │  ├── ParameterizedHeader.php
   │  │  │  │  ├── IdentificationHeader.php
   │  │  │  │  ├── Headers.php
   │  │  │  │  ├── PathHeader.php
   │  │  │  │  ├── HeaderInterface.php
   │  │  │  │  ├── AbstractHeader.php
   │  │  │  │  └── MailboxListHeader.php
   │  │  │  ├── DependencyInjection
   │  │  │  │  └── AddMimeTypeGuesserPass.php
   │  │  │  ├── MimeTypes.php
   │  │  │  ├── Resources
   │  │  │  │  └── bin
   │  │  │  ├── Message.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── DraftEmail.php
   │  │  │  ├── Encoder
   │  │  │  │  ├── Base64Encoder.php
   │  │  │  │  ├── ContentEncoderInterface.php
   │  │  │  │  ├── MimeHeaderEncoderInterface.php
   │  │  │  │  ├── QpContentEncoder.php
   │  │  │  │  ├── Base64MimeHeaderEncoder.php
   │  │  │  │  ├── EncoderInterface.php
   │  │  │  │  ├── IdnAddressEncoder.php
   │  │  │  │  ├── Rfc2231Encoder.php
   │  │  │  │  ├── QpEncoder.php
   │  │  │  │  ├── QpMimeHeaderEncoder.php
   │  │  │  │  ├── AddressEncoderInterface.php
   │  │  │  │  ├── EightBitContentEncoder.php
   │  │  │  │  └── Base64ContentEncoder.php
   │  │  │  ├── Email.php
   │  │  │  ├── RawMessage.php
   │  │  │  ├── Exception
   │  │  │  │  ├── LogicException.php
   │  │  │  │  ├── RuntimeException.php
   │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  ├── AddressEncoderException.php
   │  │  │  │  ├── ExceptionInterface.php
   │  │  │  │  └── RfcComplianceException.php
   │  │  │  ├── BodyRendererInterface.php
   │  │  │  ├── Test
   │  │  │  │  └── Constraint
   │  │  │  │    ├── EmailAddressContains.php
   │  │  │  │    ├── EmailHtmlBodyContains.php
   │  │  │  │    ├── EmailTextBodyContains.php
   │  │  │  │    ├── EmailSubjectContains.php
   │  │  │  │    ├── EmailHasHeader.php
   │  │  │  │    ├── EmailHeaderSame.php
   │  │  │  │    └── EmailAttachmentCount.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── MessageConverter.php
   │  │  │  ├── LICENSE
   │  │  │  ├── MimeTypesInterface.php
   │  │  │  ├── Address.php
   │  │  │  ├── Crypto
   │  │  │  │  ├── SMimeEncrypter.php
   │  │  │  │  ├── SMimeSigner.php
   │  │  │  │  ├── DkimOptions.php
   │  │  │  │  ├── DkimSigner.php
   │  │  │  │  └── SMime.php
   │  │  │  ├── HtmlToTextConverter
   │  │  │  │  ├── DefaultHtmlToTextConverter.php
   │  │  │  │  ├── LeagueHtmlToMarkdownConverter.php
   │  │  │  │  └── HtmlToTextConverterInterface.php
   │  │  │  ├── FileinfoMimeTypeGuesser.php
   │  │  │  ├── Part
   │  │  │  │  ├── File.php
   │  │  │  │  ├── TextPart.php
   │  │  │  │  ├── AbstractPart.php
   │  │  │  │  ├── MessagePart.php
   │  │  │  │  ├── AbstractMultipartPart.php
   │  │  │  │  ├── DataPart.php
   │  │  │  │  ├── Multipart
   │  │  │  │  │  ├── DigestPart.php
   │  │  │  │  │  ├── MixedPart.php
   │  │  │  │  │  ├── AlternativePart.php
   │  │  │  │  │  ├── FormDataPart.php
   │  │  │  │  │  └── RelatedPart.php
   │  │  │  │  └── SMimePart.php
   │  │  │  ├── MimeTypeGuesserInterface.php
   │  │  │  └── CharacterStream.php
   │  │  ├── translation-contracts
   │  │  │  ├── TranslatorTrait.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── LocaleAwareInterface.php
   │  │  │  ├── Test
   │  │  │  │  └── TranslatorTest.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── LICENSE
   │  │  │  ├── TranslatorInterface.php
   │  │  │  └── TranslatableInterface.php
   │  │  ├── http-foundation
   │  │  │  ├── StreamedResponse.php
   │  │  │  ├── Session
   │  │  │  │  ├── SessionFactoryInterface.php
   │  │  │  │  ├── Storage
   │  │  │  │  │  ├── PhpBridgeSessionStorageFactory.php
   │  │  │  │  │  ├── SessionStorageInterface.php
   │  │  │  │  │  ├── MockFileSessionStorage.php
   │  │  │  │  │  ├── MetadataBag.php
   │  │  │  │  │  ├── SessionStorageFactoryInterface.php
   │  │  │  │  │  ├── NativeSessionStorageFactory.php
   │  │  │  │  │  ├── Proxy
   │  │  │  │  │  │  ├── AbstractProxy.php
   │  │  │  │  │  │  └── SessionHandlerProxy.php
   │  │  │  │  │  ├── NativeSessionStorage.php
   │  │  │  │  │  ├── Handler
   │  │  │  │  │  │  ├── MongoDbSessionHandler.php
   │  │  │  │  │  │  ├── IdentityMarshaller.php
   │  │  │  │  │  │  ├── PdoSessionHandler.php
   │  │  │  │  │  │  ├── SessionHandlerFactory.php
   │  │  │  │  │  │  ├── StrictSessionHandler.php
   │  │  │  │  │  │  ├── AbstractSessionHandler.php
   │  │  │  │  │  │  ├── MarshallingSessionHandler.php
   │  │  │  │  │  │  ├── RedisSessionHandler.php
   │  │  │  │  │  │  ├── MemcachedSessionHandler.php
   │  │  │  │  │  │  ├── NativeFileSessionHandler.php
   │  │  │  │  │  │  ├── MigratingSessionHandler.php
   │  │  │  │  │  │  └── NullSessionHandler.php
   │  │  │  │  │  ├── MockFileSessionStorageFactory.php
   │  │  │  │  │  ├── PhpBridgeSessionStorage.php
   │  │  │  │  │  └── MockArraySessionStorage.php
   │  │  │  │  ├── Session.php
   │  │  │  │  ├── Attribute
   │  │  │  │  │  ├── AttributeBag.php
   │  │  │  │  │  └── AttributeBagInterface.php
   │  │  │  │  ├── SessionInterface.php
   │  │  │  │  ├── Flash
   │  │  │  │  │  ├── AutoExpireFlashBag.php
   │  │  │  │  │  ├── FlashBagInterface.php
   │  │  │  │  │  └── FlashBag.php
   │  │  │  │  ├── FlashBagAwareSessionInterface.php
   │  │  │  │  ├── SessionFactory.php
   │  │  │  │  ├── SessionUtils.php
   │  │  │  │  ├── SessionBagProxy.php
   │  │  │  │  └── SessionBagInterface.php
   │  │  │  ├── IpUtils.php
   │  │  │  ├── ParameterBag.php
   │  │  │  ├── ChainRequestMatcher.php
   │  │  │  ├── Request.php
   │  │  │  ├── Response.php
   │  │  │  ├── AcceptHeaderItem.php
   │  │  │  ├── StreamedJsonResponse.php
   │  │  │  ├── BinaryFileResponse.php
   │  │  │  ├── InputBag.php
   │  │  │  ├── composer.json
   │  │  │  ├── RequestMatcherInterface.php
   │  │  │  ├── ResponseHeaderBag.php
   │  │  │  ├── README.md
   │  │  │  ├── RateLimiter
   │  │  │  │  ├── AbstractRequestRateLimiter.php
   │  │  │  │  ├── PeekableRequestRateLimiterInterface.php
   │  │  │  │  └── RequestRateLimiterInterface.php
   │  │  │  ├── Exception
   │  │  │  │  ├── UnverifiedSignedUriException.php
   │  │  │  │  ├── LogicException.php
   │  │  │  │  ├── BadRequestException.php
   │  │  │  │  ├── JsonException.php
   │  │  │  │  ├── UnsignedUriException.php
   │  │  │  │  ├── SuspiciousOperationException.php
   │  │  │  │  ├── ConflictingHeadersException.php
   │  │  │  │  ├── SessionNotFoundException.php
   │  │  │  │  ├── UnexpectedValueException.php
   │  │  │  │  ├── SignedUriException.php
   │  │  │  │  ├── ExceptionInterface.php
   │  │  │  │  ├── ExpiredSignedUriException.php
   │  │  │  │  └── RequestExceptionInterface.php
   │  │  │  ├── JsonResponse.php
   │  │  │  ├── Test
   │  │  │  │  └── Constraint
   │  │  │  │    ├── ResponseIsUnprocessable.php
   │  │  │  │    ├── ResponseHeaderSame.php
   │  │  │  │    ├── ResponseHasCookie.php
   │  │  │  │    ├── ResponseFormatSame.php
   │  │  │  │    ├── ResponseCookieValueSame.php
   │  │  │  │    ├── ResponseHasHeader.php
   │  │  │  │    ├── ResponseHeaderLocationSame.php
   │  │  │  │    ├── ResponseIsRedirected.php
   │  │  │  │    ├── ResponseIsSuccessful.php
   │  │  │  │    ├── ResponseStatusCodeSame.php
   │  │  │  │    └── RequestAttributeValueSame.php
   │  │  │  ├── ServerBag.php
   │  │  │  ├── EventStreamResponse.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── AcceptHeader.php
   │  │  │  ├── LICENSE
   │  │  │  ├── File
   │  │  │  │  ├── File.php
   │  │  │  │  ├── Stream.php
   │  │  │  │  ├── UploadedFile.php
   │  │  │  │  └── Exception
   │  │  │  │    ├── CannotWriteFileException.php
   │  │  │  │    ├── FileNotFoundException.php
   │  │  │  │    ├── PartialFileException.php
   │  │  │  │    ├── FormSizeFileException.php
   │  │  │  │    ├── NoTmpDirFileException.php
   │  │  │  │    ├── UnexpectedTypeException.php
   │  │  │  │    ├── IniSizeFileException.php
   │  │  │  │    ├── NoFileException.php
   │  │  │  │    ├── ExtensionFileException.php
   │  │  │  │    ├── UploadException.php
   │  │  │  │    ├── FileException.php
   │  │  │  │    └── AccessDeniedException.php
   │  │  │  ├── FileBag.php
   │  │  │  ├── RedirectResponse.php
   │  │  │  ├── UriSigner.php
   │  │  │  ├── HeaderBag.php
   │  │  │  ├── HeaderUtils.php
   │  │  │  ├── UrlHelper.php
   │  │  │  ├── Cookie.php
   │  │  │  ├── RequestStack.php
   │  │  │  ├── RequestMatcher
   │  │  │  │  ├── AttributesRequestMatcher.php
   │  │  │  │  ├── PathRequestMatcher.php
   │  │  │  │  ├── MethodRequestMatcher.php
   │  │  │  │  ├── HostRequestMatcher.php
   │  │  │  │  ├── HeaderRequestMatcher.php
   │  │  │  │  ├── PortRequestMatcher.php
   │  │  │  │  ├── IsJsonRequestMatcher.php
   │  │  │  │  ├── QueryParameterRequestMatcher.php
   │  │  │  │  ├── SchemeRequestMatcher.php
   │  │  │  │  ├── ExpressionRequestMatcher.php
   │  │  │  │  └── IpsRequestMatcher.php
   │  │  │  └── ServerEvent.php
   │  │  ├── routing
   │  │  │  ├── CompiledRoute.php
   │  │  │  ├── RouterInterface.php
   │  │  │  ├── Annotation
   │  │  │  │  └── Route.php
   │  │  │  ├── DependencyInjection
   │  │  │  │  ├── AddExpressionLanguageProvidersPass.php
   │  │  │  │  ├── RoutingResolverPass.php
   │  │  │  │  └── RoutingControllerPass.php
   │  │  │  ├── composer.json
   │  │  │  ├── Attribute
   │  │  │  │  ├── DeprecatedAlias.php
   │  │  │  │  └── Route.php
   │  │  │  ├── README.md
   │  │  │  ├── Alias.php
   │  │  │  ├── RequestContext.php
   │  │  │  ├── Exception
   │  │  │  │  ├── LogicException.php
   │  │  │  │  ├── RuntimeException.php
   │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  ├── NoConfigurationException.php
   │  │  │  │  ├── ResourceNotFoundException.php
   │  │  │  │  ├── RouteCircularReferenceException.php
   │  │  │  │  ├── ExceptionInterface.php
   │  │  │  │  ├── MethodNotAllowedException.php
   │  │  │  │  ├── InvalidParameterException.php
   │  │  │  │  ├── MissingMandatoryParametersException.php
   │  │  │  │  └── RouteNotFoundException.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── Matcher
   │  │  │  │  ├── CompiledUrlMatcher.php
   │  │  │  │  ├── ExpressionLanguageProvider.php
   │  │  │  │  ├── RequestMatcherInterface.php
   │  │  │  │  ├── UrlMatcherInterface.php
   │  │  │  │  ├── Dumper
   │  │  │  │  │  ├── CompiledUrlMatcherTrait.php
   │  │  │  │  │  ├── MatcherDumper.php
   │  │  │  │  │  ├── StaticPrefixCollection.php
   │  │  │  │  │  ├── CompiledUrlMatcherDumper.php
   │  │  │  │  │  └── MatcherDumperInterface.php
   │  │  │  │  ├── UrlMatcher.php
   │  │  │  │  ├── RedirectableUrlMatcher.php
   │  │  │  │  ├── TraceableUrlMatcher.php
   │  │  │  │  └── RedirectableUrlMatcherInterface.php
   │  │  │  ├── RouteCompilerInterface.php
   │  │  │  ├── LICENSE
   │  │  │  ├── RouteCollection.php
   │  │  │  ├── Loader
   │  │  │  │  ├── PhpFileLoader.php
   │  │  │  │  ├── ObjectLoader.php
   │  │  │  │  ├── DirectoryLoader.php
   │  │  │  │  ├── XmlFileLoader.php
   │  │  │  │  ├── GlobFileLoader.php
   │  │  │  │  ├── schema
   │  │  │  │  │  ├── routing
   │  │  │  │  │  │  └── routing-1.0.xsd
   │  │  │  │  │  └── routing.schema.json
   │  │  │  │  ├── YamlFileLoader.php
   │  │  │  │  ├── Psr4DirectoryLoader.php
   │  │  │  │  ├── AttributeFileLoader.php
   │  │  │  │  ├── ContainerLoader.php
   │  │  │  │  ├── ClosureLoader.php
   │  │  │  │  ├── Configurator
   │  │  │  │  │  ├── RoutesReference.php
   │  │  │  │  │  ├── Traits
   │  │  │  │  │  │  ├── RouteTrait.php
   │  │  │  │  │  │  ├── AddTrait.php
   │  │  │  │  │  │  ├── HostTrait.php
   │  │  │  │  │  │  ├── PrefixTrait.php
   │  │  │  │  │  │  └── LocalizedRouteTrait.php
   │  │  │  │  │  ├── RouteConfigurator.php
   │  │  │  │  │  ├── CollectionConfigurator.php
   │  │  │  │  │  ├── ImportConfigurator.php
   │  │  │  │  │  ├── RoutingConfigurator.php
   │  │  │  │  │  └── AliasConfigurator.php
   │  │  │  │  ├── AttributeClassLoader.php
   │  │  │  │  ├── AttributeServicesLoader.php
   │  │  │  │  └── AttributeDirectoryLoader.php
   │  │  │  ├── Router.php
   │  │  │  ├── Requirement
   │  │  │  │  ├── EnumRequirement.php
   │  │  │  │  └── Requirement.php
   │  │  │  ├── Generator
   │  │  │  │  ├── CompiledUrlGenerator.php
   │  │  │  │  ├── UrlGenerator.php
   │  │  │  │  ├── Dumper
   │  │  │  │  │  ├── CompiledUrlGeneratorDumper.php
   │  │  │  │  │  ├── GeneratorDumper.php
   │  │  │  │  │  └── GeneratorDumperInterface.php
   │  │  │  │  ├── UrlGeneratorInterface.php
   │  │  │  │  └── ConfigurableRequirementsInterface.php
   │  │  │  ├── Route.php
   │  │  │  ├── RequestContextAwareInterface.php
   │  │  │  └── RouteCompiler.php
   │  │  ├── polyfill-intl-grapheme
   │  │  │  ├── composer.json
   │  │  │  ├── Grapheme.php
   │  │  │  ├── README.md
   │  │  │  ├── bootstrap.php
   │  │  │  ├── LICENSE
   │  │  │  └── bootstrap80.php
   │  │  ├── polyfill-uuid
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── bootstrap.php
   │  │  │  ├── LICENSE
   │  │  │  ├── Uuid.php
   │  │  │  └── bootstrap80.php
   │  │  ├── event-dispatcher
   │  │  │  ├── EventDispatcher.php
   │  │  │  ├── Debug
   │  │  │  │  ├── WrappedListener.php
   │  │  │  │  └── TraceableEventDispatcher.php
   │  │  │  ├── DependencyInjection
   │  │  │  │  ├── RegisterListenersPass.php
   │  │  │  │  └── AddEventAliasesPass.php
   │  │  │  ├── composer.json
   │  │  │  ├── Attribute
   │  │  │  │  └── AsEventListener.php
   │  │  │  ├── GenericEvent.php
   │  │  │  ├── README.md
   │  │  │  ├── EventSubscriberInterface.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── LICENSE
   │  │  │  ├── ImmutableEventDispatcher.php
   │  │  │  └── EventDispatcherInterface.php
   │  │  ├── mailer
   │  │  │  ├── Header
   │  │  │  │  ├── MetadataHeader.php
   │  │  │  │  └── TagHeader.php
   │  │  │  ├── SentMessage.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── Transport.php
   │  │  │  ├── Exception
   │  │  │  │  ├── LogicException.php
   │  │  │  │  ├── RuntimeException.php
   │  │  │  │  ├── UnexpectedResponseException.php
   │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  ├── HttpTransportException.php
   │  │  │  │  ├── TransportExceptionInterface.php
   │  │  │  │  ├── TransportException.php
   │  │  │  │  ├── IncompleteDsnException.php
   │  │  │  │  ├── ExceptionInterface.php
   │  │  │  │  └── UnsupportedSchemeException.php
   │  │  │  ├── Test
   │  │  │  │  ├── AbstractTransportFactoryTestCase.php
   │  │  │  │  ├── TransportFactoryTestCase.php
   │  │  │  │  ├── Constraint
   │  │  │  │  │  ├── EmailIsQueued.php
   │  │  │  │  │  └── EmailCount.php
   │  │  │  │  └── IncompleteDsnTestTrait.php
   │  │  │  ├── Transport
   │  │  │  │  ├── FailoverTransport.php
   │  │  │  │  ├── AbstractApiTransport.php
   │  │  │  │  ├── AbstractHttpTransport.php
   │  │  │  │  ├── Smtp
   │  │  │  │  │  ├── SmtpTransport.php
   │  │  │  │  │  ├── Auth
   │  │  │  │  │  │  ├── XOAuth2Authenticator.php
   │  │  │  │  │  │  ├── LoginAuthenticator.php
   │  │  │  │  │  │  ├── PlainAuthenticator.php
   │  │  │  │  │  │  ├── AuthenticatorInterface.php
   │  │  │  │  │  │  └── CramMd5Authenticator.php
   │  │  │  │  │  ├── EsmtpTransport.php
   │  │  │  │  │  ├── EsmtpTransportFactory.php
   │  │  │  │  │  └── Stream
   │  │  │  │  │    ├── ProcessStream.php
   │  │  │  │  │    ├── AbstractStream.php
   │  │  │  │  │    └── SocketStream.php
   │  │  │  │  ├── SendmailTransport.php
   │  │  │  │  ├── TransportInterface.php
   │  │  │  │  ├── AbstractTransportFactory.php
   │  │  │  │  ├── AbstractTransport.php
   │  │  │  │  ├── NullTransportFactory.php
   │  │  │  │  ├── Transports.php
   │  │  │  │  ├── NullTransport.php
   │  │  │  │  ├── RoundRobinTransport.php
   │  │  │  │  ├── TransportFactoryInterface.php
   │  │  │  │  ├── SendmailTransportFactory.php
   │  │  │  │  ├── NativeTransportFactory.php
   │  │  │  │  └── Dsn.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── Mailer.php
   │  │  │  ├── Command
   │  │  │  │  └── MailerTestCommand.php
   │  │  │  ├── DelayedEnvelope.php
   │  │  │  ├── LICENSE
   │  │  │  ├── Envelope.php
   │  │  │  ├── MailerInterface.php
   │  │  │  ├── EventListener
   │  │  │  │  ├── SmimeCertificateRepositoryInterface.php
   │  │  │  │  ├── SmimeEncryptedMessageListener.php
   │  │  │  │  ├── MessengerTransportListener.php
   │  │  │  │  ├── MessageListener.php
   │  │  │  │  ├── DkimSignedMessageListener.php
   │  │  │  │  ├── EnvelopeListener.php
   │  │  │  │  ├── SmimeSignedMessageListener.php
   │  │  │  │  └── MessageLoggerListener.php
   │  │  │  ├── DataCollector
   │  │  │  │  └── MessageDataCollector.php
   │  │  │  ├── Messenger
   │  │  │  │  ├── MessageHandler.php
   │  │  │  │  └── SendEmailMessage.php
   │  │  │  └── Event
   │  │  │    ├── FailedMessageEvent.php
   │  │  │    ├── MessageEvents.php
   │  │  │    ├── SentMessageEvent.php
   │  │  │    └── MessageEvent.php
   │  │  ├── http-kernel
   │  │  │  ├── CacheWarmer
   │  │  │  │  ├── CacheWarmerInterface.php
   │  │  │  │  ├── CacheWarmerAggregate.php
   │  │  │  │  ├── CacheWarmer.php
   │  │  │  │  └── WarmableInterface.php
   │  │  │  ├── Controller
   │  │  │  │  ├── ArgumentResolver.php
   │  │  │  │  ├── ErrorController.php
   │  │  │  │  ├── TraceableControllerResolver.php
   │  │  │  │  ├── ContainerControllerResolver.php
   │  │  │  │  ├── ArgumentResolver
   │  │  │  │  │  ├── SessionValueResolver.php
   │  │  │  │  │  ├── NotTaggedControllerValueResolver.php
   │  │  │  │  │  ├── DateTimeValueResolver.php
   │  │  │  │  │  ├── BackedEnumValueResolver.php
   │  │  │  │  │  ├── UidValueResolver.php
   │  │  │  │  │  ├── RequestAttributeValueResolver.php
   │  │  │  │  │  ├── DefaultValueResolver.php
   │  │  │  │  │  ├── TraceableValueResolver.php
   │  │  │  │  │  ├── QueryParameterValueResolver.php
   │  │  │  │  │  ├── VariadicValueResolver.php
   │  │  │  │  │  ├── ServiceValueResolver.php
   │  │  │  │  │  ├── RequestValueResolver.php
   │  │  │  │  │  └── RequestPayloadValueResolver.php
   │  │  │  │  ├── ControllerResolverInterface.php
   │  │  │  │  ├── TraceableArgumentResolver.php
   │  │  │  │  ├── ArgumentResolverInterface.php
   │  │  │  │  ├── ControllerReference.php
   │  │  │  │  ├── ValueResolverInterface.php
   │  │  │  │  └── ControllerResolver.php
   │  │  │  ├── Debug
   │  │  │  │  ├── VirtualRequestStack.php
   │  │  │  │  ├── ErrorHandlerConfigurator.php
   │  │  │  │  └── TraceableEventDispatcher.php
   │  │  │  ├── DependencyInjection
   │  │  │  │  ├── Extension.php
   │  │  │  │  ├── FragmentRendererPass.php
   │  │  │  │  ├── RegisterLocaleAwareServicesPass.php
   │  │  │  │  ├── MergeExtensionConfigurationPass.php
   │  │  │  │  ├── LoggerPass.php
   │  │  │  │  ├── RegisterControllerArgumentLocatorsPass.php
   │  │  │  │  ├── ResettableServicePass.php
   │  │  │  │  ├── AddAnnotatedClassesToCachePass.php
   │  │  │  │  ├── ServicesResetterInterface.php
   │  │  │  │  ├── LazyLoadingFragmentHandler.php
   │  │  │  │  ├── RemoveEmptyControllerArgumentLocatorsPass.php
   │  │  │  │  ├── ControllerArgumentValueResolverPass.php
   │  │  │  │  ├── ConfigurableExtension.php
   │  │  │  │  └── ServicesResetter.php
   │  │  │  ├── Resources
   │  │  │  │  └── welcome.html.php
   │  │  │  ├── CacheClearer
   │  │  │  │  ├── CacheClearerInterface.php
   │  │  │  │  ├── Psr6CacheClearer.php
   │  │  │  │  └── ChainCacheClearer.php
   │  │  │  ├── KernelInterface.php
   │  │  │  ├── Bundle
   │  │  │  │  ├── AbstractBundle.php
   │  │  │  │  ├── BundleExtension.php
   │  │  │  │  ├── BundleInterface.php
   │  │  │  │  └── Bundle.php
   │  │  │  ├── composer.json
   │  │  │  ├── HttpKernelInterface.php
   │  │  │  ├── Attribute
   │  │  │  │  ├── MapUploadedFile.php
   │  │  │  │  ├── AsTargetedValueResolver.php
   │  │  │  │  ├── MapQueryParameter.php
   │  │  │  │  ├── Cache.php
   │  │  │  │  ├── IsSignatureValid.php
   │  │  │  │  ├── MapRequestPayload.php
   │  │  │  │  ├── AsController.php
   │  │  │  │  ├── ValueResolver.php
   │  │  │  │  ├── MapQueryString.php
   │  │  │  │  ├── WithHttpStatus.php
   │  │  │  │  ├── MapDateTime.php
   │  │  │  │  └── WithLogLevel.php
   │  │  │  ├── HttpKernelBrowser.php
   │  │  │  ├── README.md
   │  │  │  ├── HttpKernel.php
   │  │  │  ├── Log
   │  │  │  │  ├── DebugLoggerInterface.php
   │  │  │  │  ├── DebugLoggerConfigurator.php
   │  │  │  │  └── Logger.php
   │  │  │  ├── TerminableInterface.php
   │  │  │  ├── Fragment
   │  │  │  │  ├── FragmentRendererInterface.php
   │  │  │  │  ├── InlineFragmentRenderer.php
   │  │  │  │  ├── SsiFragmentRenderer.php
   │  │  │  │  ├── HIncludeFragmentRenderer.php
   │  │  │  │  ├── EsiFragmentRenderer.php
   │  │  │  │  ├── FragmentHandler.php
   │  │  │  │  ├── AbstractSurrogateFragmentRenderer.php
   │  │  │  │  ├── FragmentUriGeneratorInterface.php
   │  │  │  │  ├── RoutableFragmentRenderer.php
   │  │  │  │  └── FragmentUriGenerator.php
   │  │  │  ├── KernelEvents.php
   │  │  │  ├── HttpCache
   │  │  │  │  ├── SubRequestHandler.php
   │  │  │  │  ├── ResponseCacheStrategyInterface.php
   │  │  │  │  ├── SurrogateInterface.php
   │  │  │  │  ├── Store.php
   │  │  │  │  ├── StoreInterface.php
   │  │  │  │  ├── CacheWasLockedException.php
   │  │  │  │  ├── AbstractSurrogate.php
   │  │  │  │  ├── ResponseCacheStrategy.php
   │  │  │  │  ├── Esi.php
   │  │  │  │  ├── Ssi.php
   │  │  │  │  └── HttpCache.php
   │  │  │  ├── Exception
   │  │  │  │  ├── HttpException.php
   │  │  │  │  ├── UnprocessableEntityHttpException.php
   │  │  │  │  ├── MethodNotAllowedHttpException.php
   │  │  │  │  ├── NotAcceptableHttpException.php
   │  │  │  │  ├── ControllerDoesNotReturnResponseException.php
   │  │  │  │  ├── NearMissValueResolverException.php
   │  │  │  │  ├── UnsupportedMediaTypeHttpException.php
   │  │  │  │  ├── UnauthorizedHttpException.php
   │  │  │  │  ├── NotFoundHttpException.php
   │  │  │  │  ├── HttpExceptionInterface.php
   │  │  │  │  ├── PreconditionFailedHttpException.php
   │  │  │  │  ├── LockedHttpException.php
   │  │  │  │  ├── InvalidMetadataException.php
   │  │  │  │  ├── ResolverNotFoundException.php
   │  │  │  │  ├── PreconditionRequiredHttpException.php
   │  │  │  │  ├── BadRequestHttpException.php
   │  │  │  │  ├── AccessDeniedHttpException.php
   │  │  │  │  ├── TooManyRequestsHttpException.php
   │  │  │  │  ├── ConflictHttpException.php
   │  │  │  │  ├── GoneHttpException.php
   │  │  │  │  ├── LengthRequiredHttpException.php
   │  │  │  │  ├── UnexpectedSessionUsageException.php
   │  │  │  │  └── ServiceUnavailableHttpException.php
   │  │  │  ├── Config
   │  │  │  │  └── FileLocator.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── LICENSE
   │  │  │  ├── RebootableInterface.php
   │  │  │  ├── EventListener
   │  │  │  │  ├── ErrorListener.php
   │  │  │  │  ├── DumpListener.php
   │  │  │  │  ├── DisallowRobotsIndexingListener.php
   │  │  │  │  ├── SessionListener.php
   │  │  │  │  ├── ProfilerListener.php
   │  │  │  │  ├── RouterListener.php
   │  │  │  │  ├── IsSignatureValidAttributeListener.php
   │  │  │  │  ├── ResponseListener.php
   │  │  │  │  ├── CacheAttributeListener.php
   │  │  │  │  ├── AbstractSessionListener.php
   │  │  │  │  ├── ValidateRequestListener.php
   │  │  │  │  ├── AddRequestFormatsListener.php
   │  │  │  │  ├── LocaleListener.php
   │  │  │  │  ├── SurrogateListener.php
   │  │  │  │  ├── FragmentListener.php
   │  │  │  │  ├── DebugHandlersListener.php
   │  │  │  │  └── LocaleAwareListener.php
   │  │  │  ├── DataCollector
   │  │  │  │  ├── DataCollector.php
   │  │  │  │  ├── DumpDataCollector.php
   │  │  │  │  ├── ConfigDataCollector.php
   │  │  │  │  ├── EventDataCollector.php
   │  │  │  │  ├── MemoryDataCollector.php
   │  │  │  │  ├── LateDataCollectorInterface.php
   │  │  │  │  ├── RouterDataCollector.php
   │  │  │  │  ├── TimeDataCollector.php
   │  │  │  │  ├── ExceptionDataCollector.php
   │  │  │  │  ├── AjaxDataCollector.php
   │  │  │  │  ├── DataCollectorInterface.php
   │  │  │  │  ├── LoggerDataCollector.php
   │  │  │  │  └── RequestDataCollector.php
   │  │  │  ├── Event
   │  │  │  │  ├── RequestEvent.php
   │  │  │  │  ├── ResponseEvent.php
   │  │  │  │  ├── ControllerArgumentsEvent.php
   │  │  │  │  ├── ExceptionEvent.php
   │  │  │  │  ├── FinishRequestEvent.php
   │  │  │  │  ├── KernelEvent.php
   │  │  │  │  ├── ControllerEvent.php
   │  │  │  │  ├── ViewEvent.php
   │  │  │  │  └── TerminateEvent.php
   │  │  │  ├── Profiler
   │  │  │  │  ├── Profiler.php
   │  │  │  │  ├── Profile.php
   │  │  │  │  ├── ProfilerStateChecker.php
   │  │  │  │  ├── FileProfilerStorage.php
   │  │  │  │  └── ProfilerStorageInterface.php
   │  │  │  ├── ControllerMetadata
   │  │  │  │  ├── ArgumentMetadataFactory.php
   │  │  │  │  ├── ArgumentMetadata.php
   │  │  │  │  └── ArgumentMetadataFactoryInterface.php
   │  │  │  ├── HttpClientKernel.php
   │  │  │  └── Kernel.php
   │  │  ├── clock
   │  │  │  ├── ClockAwareTrait.php
   │  │  │  ├── Resources
   │  │  │  │  └── now.php
   │  │  │  ├── NativeClock.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── MonotonicClock.php
   │  │  │  ├── Test
   │  │  │  │  └── ClockSensitiveTrait.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── LICENSE
   │  │  │  ├── Clock.php
   │  │  │  ├── DatePoint.php
   │  │  │  ├── ClockInterface.php
   │  │  │  └── MockClock.php
   │  │  ├── polyfill-php80
   │  │  │  ├── Php80.php
   │  │  │  ├── Resources
   │  │  │  │  └── stubs
   │  │  │  │    ├── ValueError.php
   │  │  │  │    ├── PhpToken.php
   │  │  │  │    ├── Attribute.php
   │  │  │  │    ├── Stringable.php
   │  │  │  │    └── UnhandledMatchError.php
   │  │  │  ├── composer.json
   │  │  │  ├── PhpToken.php
   │  │  │  ├── README.md
   │  │  │  ├── bootstrap.php
   │  │  │  └── LICENSE
   │  │  ├── polyfill-intl-idn
   │  │  │  ├── Resources
   │  │  │  │  └── unidata
   │  │  │  │    ├── disallowed_STD3_mapped.php
   │  │  │  │    ├── deviation.php
   │  │  │  │    ├── mapped.php
   │  │  │  │    ├── disallowed_STD3_valid.php
   │  │  │  │    ├── virama.php
   │  │  │  │    ├── disallowed.php
   │  │  │  │    ├── ignored.php
   │  │  │  │    ├── Regex.php
   │  │  │  │    └── DisallowedRanges.php
   │  │  │  ├── composer.json
   │  │  │  ├── Idn.php
   │  │  │  ├── README.md
   │  │  │  ├── bootstrap.php
   │  │  │  ├── LICENSE
   │  │  │  ├── bootstrap80.php
   │  │  │  └── Info.php
   │  │  ├── deprecation-contracts
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── function.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  └── LICENSE
   │  │  ├── service-contracts
   │  │  │  ├── ServiceSubscriberTrait.php
   │  │  │  ├── ServiceCollectionInterface.php
   │  │  │  ├── composer.json
   │  │  │  ├── ServiceSubscriberInterface.php
   │  │  │  ├── Attribute
   │  │  │  │  ├── Required.php
   │  │  │  │  └── SubscribedService.php
   │  │  │  ├── README.md
   │  │  │  ├── Test
   │  │  │  │  ├── ServiceLocatorTestCase.php
   │  │  │  │  └── ServiceLocatorTest.php
   │  │  │  ├── ServiceLocatorTrait.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── ServiceMethodsSubscriberTrait.php
   │  │  │  ├── LICENSE
   │  │  │  ├── ServiceProviderInterface.php
   │  │  │  └── ResetInterface.php
   │  │  ├── translation
   │  │  │  ├── MetadataAwareInterface.php
   │  │  │  ├── Writer
   │  │  │  │  ├── TranslationWriterInterface.php
   │  │  │  │  └── TranslationWriter.php
   │  │  │  ├── DependencyInjection
   │  │  │  │  ├── DataCollectorTranslatorPass.php
   │  │  │  │  ├── TranslatorPass.php
   │  │  │  │  ├── TranslationDumperPass.php
   │  │  │  │  ├── TranslatorPathsPass.php
   │  │  │  │  ├── TranslationExtractorPass.php
   │  │  │  │  └── LoggingTranslatorPass.php
   │  │  │  ├── TranslatorBagInterface.php
   │  │  │  ├── Resources
   │  │  │  │  ├── bin
   │  │  │  │  │  └── translation-status.php
   │  │  │  │  ├── functions.php
   │  │  │  │  ├── schemas
   │  │  │  │  │  ├── xliff-core-2.0.xsd
   │  │  │  │  │  ├── xliff-core-1.2-transitional.xsd
   │  │  │  │  │  └── xml.xsd
   │  │  │  │  └── data
   │  │  │  │    └── parents.json
   │  │  │  ├── TranslatableMessage.php
   │  │  │  ├── Catalogue
   │  │  │  │  ├── OperationInterface.php
   │  │  │  │  ├── AbstractOperation.php
   │  │  │  │  ├── MergeOperation.php
   │  │  │  │  └── TargetOperation.php
   │  │  │  ├── IdentityTranslator.php
   │  │  │  ├── composer.json
   │  │  │  ├── CatalogueMetadataAwareInterface.php
   │  │  │  ├── DataCollectorTranslator.php
   │  │  │  ├── README.md
   │  │  │  ├── Provider
   │  │  │  │  ├── NullProvider.php
   │  │  │  │  ├── ProviderInterface.php
   │  │  │  │  ├── ProviderFactoryInterface.php
   │  │  │  │  ├── TranslationProviderCollection.php
   │  │  │  │  ├── FilteringProvider.php
   │  │  │  │  ├── NullProviderFactory.php
   │  │  │  │  ├── TranslationProviderCollectionFactory.php
   │  │  │  │  ├── AbstractProviderFactory.php
   │  │  │  │  └── Dsn.php
   │  │  │  ├── Translator.php
   │  │  │  ├── Formatter
   │  │  │  │  ├── MessageFormatter.php
   │  │  │  │  ├── MessageFormatterInterface.php
   │  │  │  │  ├── IntlFormatter.php
   │  │  │  │  └── IntlFormatterInterface.php
   │  │  │  ├── Dumper
   │  │  │  │  ├── DumperInterface.php
   │  │  │  │  ├── PoFileDumper.php
   │  │  │  │  ├── YamlFileDumper.php
   │  │  │  │  ├── MoFileDumper.php
   │  │  │  │  ├── PhpFileDumper.php
   │  │  │  │  ├── XliffFileDumper.php
   │  │  │  │  ├── QtFileDumper.php
   │  │  │  │  ├── JsonFileDumper.php
   │  │  │  │  ├── IcuResFileDumper.php
   │  │  │  │  ├── FileDumper.php
   │  │  │  │  ├── IniFileDumper.php
   │  │  │  │  └── CsvFileDumper.php
   │  │  │  ├── Exception
   │  │  │  │  ├── LogicException.php
   │  │  │  │  ├── MissingRequiredOptionException.php
   │  │  │  │  ├── InvalidResourceException.php
   │  │  │  │  ├── RuntimeException.php
   │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  ├── NotFoundResourceException.php
   │  │  │  │  ├── IncompleteDsnException.php
   │  │  │  │  ├── ProviderException.php
   │  │  │  │  ├── ExceptionInterface.php
   │  │  │  │  ├── ProviderExceptionInterface.php
   │  │  │  │  └── UnsupportedSchemeException.php
   │  │  │  ├── Test
   │  │  │  │  ├── ProviderTestCase.php
   │  │  │  │  ├── AbstractProviderFactoryTestCase.php
   │  │  │  │  ├── IncompleteDsnTestTrait.php
   │  │  │  │  └── ProviderFactoryTestCase.php
   │  │  │  ├── Util
   │  │  │  │  ├── ArrayConverter.php
   │  │  │  │  └── XliffUtils.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── Command
   │  │  │  │  ├── TranslationTrait.php
   │  │  │  │  ├── XliffLintCommand.php
   │  │  │  │  ├── TranslationPushCommand.php
   │  │  │  │  ├── TranslationLintCommand.php
   │  │  │  │  └── TranslationPullCommand.php
   │  │  │  ├── MessageCatalogueInterface.php
   │  │  │  ├── LICENSE
   │  │  │  ├── PseudoLocalizationTranslator.php
   │  │  │  ├── StaticMessage.php
   │  │  │  ├── TranslatorBag.php
   │  │  │  ├── LocaleSwitcher.php
   │  │  │  ├── Loader
   │  │  │  │  ├── IniFileLoader.php
   │  │  │  │  ├── FileLoader.php
   │  │  │  │  ├── CsvFileLoader.php
   │  │  │  │  ├── PhpFileLoader.php
   │  │  │  │  ├── JsonFileLoader.php
   │  │  │  │  ├── ArrayLoader.php
   │  │  │  │  ├── YamlFileLoader.php
   │  │  │  │  ├── IcuResFileLoader.php
   │  │  │  │  ├── MoFileLoader.php
   │  │  │  │  ├── PoFileLoader.php
   │  │  │  │  ├── IcuDatFileLoader.php
   │  │  │  │  ├── XliffFileLoader.php
   │  │  │  │  ├── QtFileLoader.php
   │  │  │  │  └── LoaderInterface.php
   │  │  │  ├── DataCollector
   │  │  │  │  └── TranslationDataCollector.php
   │  │  │  ├── LoggingTranslator.php
   │  │  │  ├── Extractor
   │  │  │  │  ├── AbstractFileExtractor.php
   │  │  │  │  ├── PhpAstExtractor.php
   │  │  │  │  ├── Visitor
   │  │  │  │  │  ├── ConstraintVisitor.php
   │  │  │  │  │  ├── TranslatableMessageVisitor.php
   │  │  │  │  │  ├── AbstractVisitor.php
   │  │  │  │  │  └── TransMethodVisitor.php
   │  │  │  │  ├── ChainExtractor.php
   │  │  │  │  └── ExtractorInterface.php
   │  │  │  ├── MessageCatalogue.php
   │  │  │  └── Reader
   │  │  │    ├── TranslationReaderInterface.php
   │  │  │    └── TranslationReader.php
   │  │  ├── polyfill-php85
   │  │  │  ├── Php85.php
   │  │  │  ├── Resources
   │  │  │  │  └── stubs
   │  │  │  │    ├── Filter
   │  │  │  │     │  ├── FilterException.php
   │  │  │  │     │  └── FilterFailedException.php
   │  │  │  │    ├── NoDiscard.php
   │  │  │  │    └── DelayedTargetValidation.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── bootstrap.php
   │  │  │  ├── LICENSE
   │  │  │  └── bootstrap80.php
   │  │  ├── process
   │  │  │  ├── PhpExecutableFinder.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── ProcessUtils.php
   │  │  │  ├── Pipes
   │  │  │  │  ├── WindowsPipes.php
   │  │  │  │  ├── AbstractPipes.php
   │  │  │  │  ├── UnixPipes.php
   │  │  │  │  └── PipesInterface.php
   │  │  │  ├── ExecutableFinder.php
   │  │  │  ├── Exception
   │  │  │  │  ├── LogicException.php
   │  │  │  │  ├── RuntimeException.php
   │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  ├── ProcessFailedException.php
   │  │  │  │  ├── RunProcessFailedException.php
   │  │  │  │  ├── ProcessStartFailedException.php
   │  │  │  │  ├── ExceptionInterface.php
   │  │  │  │  ├── ProcessSignaledException.php
   │  │  │  │  └── ProcessTimedOutException.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── LICENSE
   │  │  │  ├── Messenger
   │  │  │  │  ├── RunProcessContext.php
   │  │  │  │  ├── RunProcessMessage.php
   │  │  │  │  └── RunProcessMessageHandler.php
   │  │  │  ├── InputStream.php
   │  │  │  ├── PhpProcess.php
   │  │  │  ├── Process.php
   │  │  │  └── PhpSubprocess.php
   │  │  ├── console
   │  │  │  ├── Output
   │  │  │  │  ├── ConsoleSectionOutput.php
   │  │  │  │  ├── NullOutput.php
   │  │  │  │  ├── BufferedOutput.php
   │  │  │  │  ├── OutputInterface.php
   │  │  │  │  ├── Output.php
   │  │  │  │  ├── TrimmedBufferOutput.php
   │  │  │  │  ├── AnsiColorMode.php
   │  │  │  │  ├── ConsoleOutput.php
   │  │  │  │  ├── StreamOutput.php
   │  │  │  │  └── ConsoleOutputInterface.php
   │  │  │  ├── Application.php
   │  │  │  ├── Terminal.php
   │  │  │  ├── Helper
   │  │  │  │  ├── TreeStyle.php
   │  │  │  │  ├── ProgressBar.php
   │  │  │  │  ├── ProcessHelper.php
   │  │  │  │  ├── InputAwareHelper.php
   │  │  │  │  ├── Dumper.php
   │  │  │  │  ├── FormatterHelper.php
   │  │  │  │  ├── TableCell.php
   │  │  │  │  ├── DebugFormatterHelper.php
   │  │  │  │  ├── OutputWrapper.php
   │  │  │  │  ├── TableSeparator.php
   │  │  │  │  ├── TerminalInputHelper.php
   │  │  │  │  ├── Helper.php
   │  │  │  │  ├── TableCellStyle.php
   │  │  │  │  ├── Table.php
   │  │  │  │  ├── TableRows.php
   │  │  │  │  ├── HelperSet.php
   │  │  │  │  ├── HelperInterface.php
   │  │  │  │  ├── ProgressIndicator.php
   │  │  │  │  ├── TreeHelper.php
   │  │  │  │  ├── DescriptorHelper.php
   │  │  │  │  ├── QuestionHelper.php
   │  │  │  │  ├── TableStyle.php
   │  │  │  │  ├── SymfonyQuestionHelper.php
   │  │  │  │  └── TreeNode.php
   │  │  │  ├── Debug
   │  │  │  │  └── CliRequest.php
   │  │  │  ├── DependencyInjection
   │  │  │  │  └── AddConsoleCommandPass.php
   │  │  │  ├── Input
   │  │  │  │  ├── Input.php
   │  │  │  │  ├── StringInput.php
   │  │  │  │  ├── InputAwareInterface.php
   │  │  │  │  ├── ArrayInput.php
   │  │  │  │  ├── InputArgument.php
   │  │  │  │  ├── InputInterface.php
   │  │  │  │  ├── ArgvInput.php
   │  │  │  │  ├── StreamableInputInterface.php
   │  │  │  │  ├── InputDefinition.php
   │  │  │  │  └── InputOption.php
   │  │  │  ├── Resources
   │  │  │  │  ├── bin
   │  │  │  │  │  └── hiddeninput.exe
   │  │  │  │  ├── completion.fish
   │  │  │  │  ├── completion.bash
   │  │  │  │  └── completion.zsh
   │  │  │  ├── composer.json
   │  │  │  ├── Attribute
   │  │  │  │  ├── Interact.php
   │  │  │  │  ├── MapInput.php
   │  │  │  │  ├── AsCommand.php
   │  │  │  │  ├── InteractiveAttributeInterface.php
   │  │  │  │  ├── Ask.php
   │  │  │  │  ├── Argument.php
   │  │  │  │  ├── Option.php
   │  │  │  │  └── Reflection
   │  │  │  │    └── ReflectionMember.php
   │  │  │  ├── README.md
   │  │  │  ├── SignalRegistry
   │  │  │  │  ├── SignalMap.php
   │  │  │  │  └── SignalRegistry.php
   │  │  │  ├── Completion
   │  │  │  │  ├── Suggestion.php
   │  │  │  │  ├── Output
   │  │  │  │  │  ├── FishCompletionOutput.php
   │  │  │  │  │  ├── BashCompletionOutput.php
   │  │  │  │  │  ├── ZshCompletionOutput.php
   │  │  │  │  │  └── CompletionOutputInterface.php
   │  │  │  │  ├── CompletionSuggestions.php
   │  │  │  │  └── CompletionInput.php
   │  │  │  ├── Interaction
   │  │  │  │  └── Interaction.php
   │  │  │  ├── Descriptor
   │  │  │  │  ├── MarkdownDescriptor.php
   │  │  │  │  ├── TextDescriptor.php
   │  │  │  │  ├── XmlDescriptor.php
   │  │  │  │  ├── ReStructuredTextDescriptor.php
   │  │  │  │  ├── JsonDescriptor.php
   │  │  │  │  ├── Descriptor.php
   │  │  │  │  ├── DescriptorInterface.php
   │  │  │  │  └── ApplicationDescription.php
   │  │  │  ├── Formatter
   │  │  │  │  ├── OutputFormatterStyleInterface.php
   │  │  │  │  ├── NullOutputFormatter.php
   │  │  │  │  ├── OutputFormatterStyle.php
   │  │  │  │  ├── WrappableOutputFormatterInterface.php
   │  │  │  │  ├── NullOutputFormatterStyle.php
   │  │  │  │  ├── OutputFormatter.php
   │  │  │  │  ├── OutputFormatterInterface.php
   │  │  │  │  └── OutputFormatterStyleStack.php
   │  │  │  ├── Style
   │  │  │  │  ├── SymfonyStyle.php
   │  │  │  │  ├── StyleInterface.php
   │  │  │  │  └── OutputStyle.php
   │  │  │  ├── Question
   │  │  │  │  ├── ChoiceQuestion.php
   │  │  │  │  ├── ConfirmationQuestion.php
   │  │  │  │  └── Question.php
   │  │  │  ├── Exception
   │  │  │  │  ├── LogicException.php
   │  │  │  │  ├── RuntimeException.php
   │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  ├── CommandNotFoundException.php
   │  │  │  │  ├── InvalidOptionException.php
   │  │  │  │  ├── NamespaceNotFoundException.php
   │  │  │  │  ├── ExceptionInterface.php
   │  │  │  │  ├── MissingInputException.php
   │  │  │  │  └── RunCommandFailedException.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── Command
   │  │  │  │  ├── LazyCommand.php
   │  │  │  │  ├── InvokableCommand.php
   │  │  │  │  ├── DumpCompletionCommand.php
   │  │  │  │  ├── HelpCommand.php
   │  │  │  │  ├── LockableTrait.php
   │  │  │  │  ├── Command.php
   │  │  │  │  ├── ListCommand.php
   │  │  │  │  ├── SignalableCommandInterface.php
   │  │  │  │  ├── CompleteCommand.php
   │  │  │  │  └── TraceableCommand.php
   │  │  │  ├── LICENSE
   │  │  │  ├── CI
   │  │  │  │  └── GithubActionReporter.php
   │  │  │  ├── EventListener
   │  │  │  │  └── ErrorListener.php
   │  │  │  ├── Tester
   │  │  │  │  ├── CommandTester.php
   │  │  │  │  ├── CommandCompletionTester.php
   │  │  │  │  ├── Constraint
   │  │  │  │  │  └── CommandIsSuccessful.php
   │  │  │  │  ├── ApplicationTester.php
   │  │  │  │  └── TesterTrait.php
   │  │  │  ├── ConsoleEvents.php
   │  │  │  ├── Logger
   │  │  │  │  └── ConsoleLogger.php
   │  │  │  ├── SingleCommandApplication.php
   │  │  │  ├── DataCollector
   │  │  │  │  └── CommandDataCollector.php
   │  │  │  ├── Color.php
   │  │  │  ├── Messenger
   │  │  │  │  ├── RunCommandMessage.php
   │  │  │  │  ├── RunCommandContext.php
   │  │  │  │  └── RunCommandMessageHandler.php
   │  │  │  ├── Event
   │  │  │  │  ├── ConsoleTerminateEvent.php
   │  │  │  │  ├── ConsoleAlarmEvent.php
   │  │  │  │  ├── ConsoleSignalEvent.php
   │  │  │  │  ├── ConsoleCommandEvent.php
   │  │  │  │  ├── ConsoleEvent.php
   │  │  │  │  └── ConsoleErrorEvent.php
   │  │  │  ├── CommandLoader
   │  │  │  │  ├── ContainerCommandLoader.php
   │  │  │  │  ├── CommandLoaderInterface.php
   │  │  │  │  └── FactoryCommandLoader.php
   │  │  │  └── Cursor.php
   │  │  ├── yaml
   │  │  │  ├── Dumper.php
   │  │  │  ├── Unescaper.php
   │  │  │  ├── Yaml.php
   │  │  │  ├── Resources
   │  │  │  │  └── bin
   │  │  │  │    └── yaml-lint
   │  │  │  ├── Tag
   │  │  │  │  └── TaggedValue.php
   │  │  │  ├── Escaper.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── Exception
   │  │  │  │  ├── DumpException.php
   │  │  │  │  ├── RuntimeException.php
   │  │  │  │  ├── ParseException.php
   │  │  │  │  └── ExceptionInterface.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── Command
   │  │  │  │  └── LintCommand.php
   │  │  │  ├── LICENSE
   │  │  │  ├── Inline.php
   │  │  │  └── Parser.php
   │  │  ├── css-selector
   │  │  │  ├── composer.json
   │  │  │  ├── CssSelectorConverter.php
   │  │  │  ├── README.md
   │  │  │  ├── Exception
   │  │  │  │  ├── SyntaxErrorException.php
   │  │  │  │  ├── ParseException.php
   │  │  │  │  ├── ExceptionInterface.php
   │  │  │  │  ├── InternalErrorException.php
   │  │  │  │  └── ExpressionErrorException.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── LICENSE
   │  │  │  ├── Node
   │  │  │  │  ├── NegationNode.php
   │  │  │  │  ├── Specificity.php
   │  │  │  │  ├── FunctionNode.php
   │  │  │  │  ├── ElementNode.php
   │  │  │  │  ├── AbstractNode.php
   │  │  │  │  ├── NodeInterface.php
   │  │  │  │  ├── AttributeNode.php
   │  │  │  │  ├── MatchingNode.php
   │  │  │  │  ├── ClassNode.php
   │  │  │  │  ├── SelectorNode.php
   │  │  │  │  ├── SpecificityAdjustmentNode.php
   │  │  │  │  ├── PseudoNode.php
   │  │  │  │  ├── CombinedSelectorNode.php
   │  │  │  │  └── HashNode.php
   │  │  │  ├── Parser
   │  │  │  │  ├── Reader.php
   │  │  │  │  ├── Tokenizer
   │  │  │  │  │  ├── Tokenizer.php
   │  │  │  │  │  ├── TokenizerEscaping.php
   │  │  │  │  │  └── TokenizerPatterns.php
   │  │  │  │  ├── Shortcut
   │  │  │  │  │  ├── ClassParser.php
   │  │  │  │  │  ├── HashParser.php
   │  │  │  │  │  ├── ElementParser.php
   │  │  │  │  │  └── EmptyStringParser.php
   │  │  │  │  ├── Token.php
   │  │  │  │  ├── ParserInterface.php
   │  │  │  │  ├── TokenStream.php
   │  │  │  │  ├── Handler
   │  │  │  │  │  ├── WhitespaceHandler.php
   │  │  │  │  │  ├── StringHandler.php
   │  │  │  │  │  ├── CommentHandler.php
   │  │  │  │  │  ├── IdentifierHandler.php
   │  │  │  │  │  ├── NumberHandler.php
   │  │  │  │  │  ├── HashHandler.php
   │  │  │  │  │  └── HandlerInterface.php
   │  │  │  │  └── Parser.php
   │  │  │  └── XPath
   │  │  │    ├── Translator.php
   │  │  │    ├── TranslatorInterface.php
   │  │  │    ├── XPathExpr.php
   │  │  │    └── Extension
   │  │  │       ├── ExtensionInterface.php
   │  │  │       ├── CombinationExtension.php
   │  │  │       ├── PseudoClassExtension.php
   │  │  │       ├── AbstractExtension.php
   │  │  │       ├── HtmlExtension.php
   │  │  │       ├── NodeExtension.php
   │  │  │       ├── AttributeMatchingExtension.php
   │  │  │       └── FunctionExtension.php
   │  │  ├── var-dumper
   │  │  │  ├── Caster
   │  │  │  │  ├── CutStub.php
   │  │  │  │  ├── StubCaster.php
   │  │  │  │  ├── FiberCaster.php
   │  │  │  │  ├── MysqliCaster.php
   │  │  │  │  ├── ClassStub.php
   │  │  │  │  ├── ImgStub.php
   │  │  │  │  ├── ScalarStub.php
   │  │  │  │  ├── TraceStub.php
   │  │  │  │  ├── DateCaster.php
   │  │  │  │  ├── FrameStub.php
   │  │  │  │  ├── IntlCaster.php
   │  │  │  │  ├── ProxyManagerCaster.php
   │  │  │  │  ├── MemcachedCaster.php
   │  │  │  │  ├── UuidCaster.php
   │  │  │  │  ├── LinkStub.php
   │  │  │  │  ├── GmpCaster.php
   │  │  │  │  ├── PdoCaster.php
   │  │  │  │  ├── EnumStub.php
   │  │  │  │  ├── ArgsStub.php
   │  │  │  │  ├── SplCaster.php
   │  │  │  │  ├── ConstStub.php
   │  │  │  │  ├── DOMCaster.php
   │  │  │  │  ├── DoctrineCaster.php
   │  │  │  │  ├── AmqpCaster.php
   │  │  │  │  ├── GdCaster.php
   │  │  │  │  ├── CutArrayStub.php
   │  │  │  │  ├── OpenSSLCaster.php
   │  │  │  │  ├── ResourceCaster.php
   │  │  │  │  ├── ImagineCaster.php
   │  │  │  │  ├── AddressInfoCaster.php
   │  │  │  │  ├── SocketCaster.php
   │  │  │  │  ├── XmlReaderCaster.php
   │  │  │  │  ├── XmlResourceCaster.php
   │  │  │  │  ├── DsCaster.php
   │  │  │  │  ├── ExceptionCaster.php
   │  │  │  │  ├── PgSqlCaster.php
   │  │  │  │  ├── RedisCaster.php
   │  │  │  │  ├── SymfonyCaster.php
   │  │  │  │  ├── RdKafkaCaster.php
   │  │  │  │  ├── FFICaster.php
   │  │  │  │  ├── SqliteCaster.php
   │  │  │  │  ├── VirtualStub.php
   │  │  │  │  ├── DsPairStub.php
   │  │  │  │  ├── ReflectionCaster.php
   │  │  │  │  ├── CurlCaster.php
   │  │  │  │  ├── UninitializedStub.php
   │  │  │  │  └── Caster.php
   │  │  │  ├── Resources
   │  │  │  │  ├── css
   │  │  │  │  │  └── htmlDescriptor.css
   │  │  │  │  ├── bin
   │  │  │  │  │  └── var-dump-server
   │  │  │  │  ├── js
   │  │  │  │  │  └── htmlDescriptor.js
   │  │  │  │  └── functions
   │  │  │  │    └── dump.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── Dumper
   │  │  │  │  ├── AbstractDumper.php
   │  │  │  │  ├── HtmlDumper.php
   │  │  │  │  ├── CliDumper.php
   │  │  │  │  ├── ContextProvider
   │  │  │  │  │  ├── SourceContextProvider.php
   │  │  │  │  │  ├── CliContextProvider.php
   │  │  │  │  │  ├── ContextProviderInterface.php
   │  │  │  │  │  └── RequestContextProvider.php
   │  │  │  │  ├── ServerDumper.php
   │  │  │  │  ├── ContextualizedDumper.php
   │  │  │  │  └── DataDumperInterface.php
   │  │  │  ├── Exception
   │  │  │  │  └── ThrowingCasterException.php
   │  │  │  ├── Test
   │  │  │  │  └── VarDumperTestTrait.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── Command
   │  │  │  │  ├── Descriptor
   │  │  │  │  │  ├── DumpDescriptorInterface.php
   │  │  │  │  │  ├── CliDescriptor.php
   │  │  │  │  │  └── HtmlDescriptor.php
   │  │  │  │  └── ServerDumpCommand.php
   │  │  │  ├── LICENSE
   │  │  │  ├── Cloner
   │  │  │  │  ├── ClonerInterface.php
   │  │  │  │  ├── DumperInterface.php
   │  │  │  │  ├── Stub.php
   │  │  │  │  ├── Data.php
   │  │  │  │  ├── VarCloner.php
   │  │  │  │  ├── AbstractCloner.php
   │  │  │  │  └── Cursor.php
   │  │  │  ├── Server
   │  │  │  │  ├── Connection.php
   │  │  │  │  └── DumpServer.php
   │  │  │  └── VarDumper.php
   │  │  ├── error-handler
   │  │  │  ├── ErrorEnhancer
   │  │  │  │  ├── UndefinedFunctionErrorEnhancer.php
   │  │  │  │  ├── UndefinedMethodErrorEnhancer.php
   │  │  │  │  ├── ErrorEnhancerInterface.php
   │  │  │  │  └── ClassNotFoundErrorEnhancer.php
   │  │  │  ├── ErrorRenderer
   │  │  │  │  ├── CliErrorRenderer.php
   │  │  │  │  ├── SerializerErrorRenderer.php
   │  │  │  │  ├── ErrorRendererInterface.php
   │  │  │  │  ├── FileLinkFormatter.php
   │  │  │  │  └── HtmlErrorRenderer.php
   │  │  │  ├── Resources
   │  │  │  │  ├── bin
   │  │  │  │  │  ├── extract-tentative-return-types.php
   │  │  │  │  │  └── patch-type-declarations
   │  │  │  │  ├── assets
   │  │  │  │  │  ├── css
   │  │  │  │  │  │  ├── error.css
   │  │  │  │  │  │  ├── exception.css
   │  │  │  │  │  │  └── exception_full.css
   │  │  │  │  │  ├── images
   │  │  │  │  │  │  ├── symfony-ghost.svg.php
   │  │  │  │  │  │  ├── icon-plus-square.svg
   │  │  │  │  │  │  ├── icon-minus-square-o.svg
   │  │  │  │  │  │  ├── favicon.png.base64
   │  │  │  │  │  │  ├── symfony-logo.svg
   │  │  │  │  │  │  ├── icon-plus-square-o.svg
   │  │  │  │  │  │  ├── icon-support.svg
   │  │  │  │  │  │  ├── icon-copy.svg
   │  │  │  │  │  │  ├── icon-book.svg
   │  │  │  │  │  │  ├── chevron-right.svg
   │  │  │  │  │  │  └── icon-minus-square.svg
   │  │  │  │  │  └── js
   │  │  │  │  │    └── exception.js
   │  │  │  │  └── views
   │  │  │  │    ├── exception.html.php
   │  │  │  │    ├── error.html.php
   │  │  │  │    ├── traces.html.php
   │  │  │  │    ├── exception_full.html.php
   │  │  │  │    ├── logs.html.php
   │  │  │  │    ├── trace.html.php
   │  │  │  │    └── traces_text.html.php
   │  │  │  ├── composer.json
   │  │  │  ├── BufferingLogger.php
   │  │  │  ├── Error
   │  │  │  │  ├── OutOfMemoryError.php
   │  │  │  │  ├── FatalError.php
   │  │  │  │  ├── ClassNotFoundError.php
   │  │  │  │  ├── UndefinedMethodError.php
   │  │  │  │  └── UndefinedFunctionError.php
   │  │  │  ├── README.md
   │  │  │  ├── ThrowableUtils.php
   │  │  │  ├── Exception
   │  │  │  │  ├── FlattenException.php
   │  │  │  │  └── SilencedErrorContext.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── Command
   │  │  │  │  └── ErrorDumpCommand.php
   │  │  │  ├── LICENSE
   │  │  │  ├── Debug.php
   │  │  │  ├── DebugClassLoader.php
   │  │  │  ├── Internal
   │  │  │  │  └── TentativeTypes.php
   │  │  │  └── ErrorHandler.php
   │  │  ├── polyfill-php83
   │  │  │  ├── bootstrap81.php
   │  │  │  ├── Php83.php
   │  │  │  ├── Resources
   │  │  │  │  └── stubs
   │  │  │  │    ├── DateRangeError.php
   │  │  │  │    ├── Override.php
   │  │  │  │    ├── DateMalformedIntervalStringException.php
   │  │  │  │    ├── SQLite3Exception.php
   │  │  │  │    ├── DateMalformedPeriodStringException.php
   │  │  │  │    ├── DateObjectError.php
   │  │  │  │    ├── DateError.php
   │  │  │  │    ├── DateException.php
   │  │  │  │    ├── DateInvalidTimeZoneException.php
   │  │  │  │    ├── DateMalformedStringException.php
   │  │  │  │    └── DateInvalidOperationException.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── bootstrap.php
   │  │  │  ├── LICENSE
   │  │  │  └── bootstrap80.php
   │  │  ├── polyfill-mbstring
   │  │  │  ├── Resources
   │  │  │  │  └── unidata
   │  │  │  │    ├── caseFolding.php
   │  │  │  │    ├── upperCase.php
   │  │  │  │    ├── lowerCase.php
   │  │  │  │    └── titleCaseRegexp.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── bootstrap.php
   │  │  │  ├── LICENSE
   │  │  │  ├── bootstrap80.php
   │  │  │  └── Mbstring.php
   │  │  ├── finder
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── Exception
   │  │  │  │  ├── DirectoryNotFoundException.php
   │  │  │  │  └── AccessDeniedException.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── LICENSE
   │  │  │  ├── Glob.php
   │  │  │  ├── Finder.php
   │  │  │  ├── Iterator
   │  │  │  │  ├── ExcludeDirectoryFilterIterator.php
   │  │  │  │  ├── PathFilterIterator.php
   │  │  │  │  ├── DepthRangeFilterIterator.php
   │  │  │  │  ├── RecursiveDirectoryIterator.php
   │  │  │  │  ├── MultiplePcreFilterIterator.php
   │  │  │  │  ├── DateRangeFilterIterator.php
   │  │  │  │  ├── CustomFilterIterator.php
   │  │  │  │  ├── FileTypeFilterIterator.php
   │  │  │  │  ├── FilecontentFilterIterator.php
   │  │  │  │  ├── LazyIterator.php
   │  │  │  │  ├── FilenameFilterIterator.php
   │  │  │  │  ├── VcsIgnoredFilterIterator.php
   │  │  │  │  ├── SortableIterator.php
   │  │  │  │  └── SizeRangeFilterIterator.php
   │  │  │  ├── SplFileInfo.php
   │  │  │  ├── Gitignore.php
   │  │  │  └── Comparator
   │  │  │    ├── NumberComparator.php
   │  │  │    ├── DateComparator.php
   │  │  │    └── Comparator.php
   │  │  ├── uid
   │  │  │  ├── UuidV1.php
   │  │  │  ├── HashableInterface.php
   │  │  │  ├── NilUlid.php
   │  │  │  ├── UuidV7.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── UuidV3.php
   │  │  │  ├── NilUuid.php
   │  │  │  ├── MaxUlid.php
   │  │  │  ├── MaxUuid.php
   │  │  │  ├── Exception
   │  │  │  │  ├── LogicException.php
   │  │  │  │  └── InvalidArgumentException.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── Command
   │  │  │  │  ├── InspectUlidCommand.php
   │  │  │  │  ├── GenerateUlidCommand.php
   │  │  │  │  ├── InspectUuidCommand.php
   │  │  │  │  └── GenerateUuidCommand.php
   │  │  │  ├── LICENSE
   │  │  │  ├── Uuid.php
   │  │  │  ├── BinaryUtil.php
   │  │  │  ├── UuidV6.php
   │  │  │  ├── UuidV5.php
   │  │  │  ├── Ulid.php
   │  │  │  ├── Factory
   │  │  │  │  ├── UlidFactory.php
   │  │  │  │  ├── RandomBasedUuidFactory.php
   │  │  │  │  ├── TimeBasedUuidFactory.php
   │  │  │  │  ├── UuidFactory.php
   │  │  │  │  ├── MockUuidFactory.php
   │  │  │  │  └── NameBasedUuidFactory.php
   │  │  │  ├── UuidV8.php
   │  │  │  ├── UuidV4.php
   │  │  │  ├── AbstractUid.php
   │  │  │  └── TimeBasedUidInterface.php
   │  │  ├── event-dispatcher-contracts
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── LICENSE
   │  │  │  ├── Event.php
   │  │  │  └── EventDispatcherInterface.php
   │  │  ├── string
   │  │  │  ├── Resources
   │  │  │  │  ├── bin
   │  │  │  │  ├── functions.php
   │  │  │  │  └── data
   │  │  │  │    ├── wcswidth_table_zero.php
   │  │  │  │    └── wcswidth_table_wide.php
   │  │  │  ├── UnicodeString.php
   │  │  │  ├── Slugger
   │  │  │  │  ├── AsciiSlugger.php
   │  │  │  │  └── SluggerInterface.php
   │  │  │  ├── composer.json
   │  │  │  ├── TruncateMode.php
   │  │  │  ├── README.md
   │  │  │  ├── ByteString.php
   │  │  │  ├── Exception
   │  │  │  │  ├── RuntimeException.php
   │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  └── ExceptionInterface.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── AbstractString.php
   │  │  │  ├── LICENSE
   │  │  │  ├── AbstractUnicodeString.php
   │  │  │  ├── LazyString.php
   │  │  │  ├── CodePointString.php
   │  │  │  └── Inflector
   │  │  │    ├── InflectorInterface.php
   │  │  │    ├── SpanishInflector.php
   │  │  │    ├── EnglishInflector.php
   │  │  │    └── FrenchInflector.php
   │  │  ├── polyfill-intl-normalizer
   │  │  │  ├── Resources
   │  │  │  │  ├── unidata
   │  │  │  │  │  ├── combiningClass.php
   │  │  │  │  │  ├── compatibilityDecomposition.php
   │  │  │  │  │  ├── canonicalComposition.php
   │  │  │  │  │  └── canonicalDecomposition.php
   │  │  │  │  └── stubs
   │  │  │  │    └── Normalizer.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── Normalizer.php
   │  │  │  ├── bootstrap.php
   │  │  │  ├── LICENSE
   │  │  │  └── bootstrap80.php
   │  │  ├── polyfill-php84
   │  │  │  ├── Resources
   │  │  │  │  ├── Deprecated.php
   │  │  │  │  ├── stubs
   │  │  │  │  │  ├── Deprecated.php
   │  │  │  │  │  ├── Pdo
   │  │  │  │  │  │  ├── Sqlite.php
   │  │  │  │  │  │  ├── Mysql.php
   │  │  │  │  │  │  ├── Odbc.php
   │  │  │  │  │  │  ├── Dblib.php
   │  │  │  │  │  │  ├── Firebird.php
   │  │  │  │  │  │  └── Pgsql.php
   │  │  │  │  │  ├── ReflectionConstant.php
   │  │  │  │  │  └── RoundingMode.php
   │  │  │  │  └── RoundingMode.php
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── bootstrap.php
   │  │  │  ├── LICENSE
   │  │  │  ├── bootstrap80.php
   │  │  │  ├── bootstrap82.php
   │  │  │  └── Php84.php
   │  │  └── polyfill-ctype
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── bootstrap.php
   │  │    ├── LICENSE
   │  │    ├── bootstrap80.php
   │  │    └── Ctype.php
   │  ├── dflydev
   │  │  └── dot-access-data
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── Util.php
   │  │     │  ├── Exception
   │  │     │  │  ├── DataException.php
   │  │     │  │  ├── MissingPathException.php
   │  │     │  │  └── InvalidPathException.php
   │  │     │  ├── DataInterface.php
   │  │     │  └── Data.php
   │  │    ├── CHANGELOG.md
   │  │    └── LICENSE
   │  ├── myclabs
   │  │  └── deep-copy
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  └── DeepCopy
   │  │     │    ├── Filter
   │  │     │     │  ├── ReplaceFilter.php
   │  │     │     │  ├── Doctrine
   │  │     │     │  │  ├── DoctrineCollectionFilter.php
   │  │     │     │  │  ├── DoctrineEmptyCollectionFilter.php
   │  │     │     │  │  └── DoctrineProxyFilter.php
   │  │     │     │  ├── Filter.php
   │  │     │     │  ├── ChainableFilter.php
   │  │     │     │  ├── SetNullFilter.php
   │  │     │     │  └── KeepFilter.php
   │  │     │    ├── Exception
   │  │     │     │  ├── CloneException.php
   │  │     │     │  └── PropertyException.php
   │  │     │    ├── DeepCopy.php
   │  │     │    ├── Matcher
   │  │     │     │  ├── Doctrine
   │  │     │     │  │  └── DoctrineProxyMatcher.php
   │  │     │     │  ├── PropertyNameMatcher.php
   │  │     │     │  ├── Matcher.php
   │  │     │     │  ├── PropertyTypeMatcher.php
   │  │     │     │  └── PropertyMatcher.php
   │  │     │    ├── deep_copy.php
   │  │     │    ├── TypeFilter
   │  │     │     │  ├── Spl
   │  │     │     │  │  ├── SplDoublyLinkedList.php
   │  │     │     │  │  ├── SplDoublyLinkedListFilter.php
   │  │     │     │  │  └── ArrayObjectFilter.php
   │  │     │     │  ├── Date
   │  │     │     │  │  ├── DateIntervalFilter.php
   │  │     │     │  │  └── DatePeriodFilter.php
   │  │     │     │  ├── ReplaceFilter.php
   │  │     │     │  ├── ShallowCopyFilter.php
   │  │     │     │  └── TypeFilter.php
   │  │     │    ├── Reflection
   │  │     │     │  └── ReflectionHelper.php
   │  │     │    └── TypeMatcher
   │  │     │       └── TypeMatcher.php
   │  │    └── LICENSE
   │  ├── carbonphp
   │  │  └── carbon-doctrine-types
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  └── Carbon
   │  │     │    └── Doctrine
   │  │     │       ├── DateTimeImmutableType.php
   │  │     │       ├── CarbonTypeConverter.php
   │  │     │       ├── CarbonImmutableType.php
   │  │     │       ├── DateTimeDefaultPrecision.php
   │  │     │       ├── CarbonDoctrineType.php
   │  │     │       ├── CarbonType.php
   │  │     │       └── DateTimeType.php
   │  │    └── LICENSE
   │  ├── vlucas
   │  │  └── phpdotenv
   │  │    ├── composer.json
   │  │    ├── src
   │  │     │  ├── Validator.php
   │  │     │  ├── Store
   │  │     │  │  ├── FileStore.php
   │  │     │  │  ├── StoreInterface.php
   │  │     │  │  ├── StringStore.php
   │  │     │  │  ├── File
   │  │     │  │  │  ├── Reader.php
   │  │     │  │  │  └── Paths.php
   │  │     │  │  └── StoreBuilder.php
   │  │     │  ├── Exception
   │  │     │  │  ├── InvalidEncodingException.php
   │  │     │  │  ├── InvalidFileException.php
   │  │     │  │  ├── ValidationException.php
   │  │     │  │  ├── ExceptionInterface.php
   │  │     │  │  └── InvalidPathException.php
   │  │     │  ├── Util
   │  │     │  │  ├── Str.php
   │  │     │  │  └── Regex.php
   │  │     │  ├── Dotenv.php
   │  │     │  ├── Repository
   │  │     │  │  ├── RepositoryBuilder.php
   │  │     │  │  ├── AdapterRepository.php
   │  │     │  │  ├── Adapter
   │  │     │  │  │  ├── WriterInterface.php
   │  │     │  │  │  ├── ApacheAdapter.php
   │  │     │  │  │  ├── ServerConstAdapter.php
   │  │     │  │  │  ├── GuardedWriter.php
   │  │     │  │  │  ├── PutenvAdapter.php
   │  │     │  │  │  ├── MultiReader.php
   │  │     │  │  │  ├── MultiWriter.php
   │  │     │  │  │  ├── AdapterInterface.php
   │  │     │  │  │  ├── ReplacingWriter.php
   │  │     │  │  │  ├── ArrayAdapter.php
   │  │     │  │  │  ├── ImmutableWriter.php
   │  │     │  │  │  ├── ReaderInterface.php
   │  │     │  │  │  └── EnvConstAdapter.php
   │  │     │  │  └── RepositoryInterface.php
   │  │     │  ├── Loader
   │  │     │  │  ├── Resolver.php
   │  │     │  │  ├── Loader.php
   │  │     │  │  └── LoaderInterface.php
   │  │     │  └── Parser
   │  │     │    ├── Lexer.php
   │  │     │    ├── ParserInterface.php
   │  │     │    ├── EntryParser.php
   │  │     │    ├── Parser.php
   │  │     │    ├── Value.php
   │  │     │    ├── Lines.php
   │  │     │    └── Entry.php
   │  │    └── LICENSE
   │  ├── ralouphie
   │  │  └── getallheaders
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  └── getallheaders.php
   │  │    └── LICENSE
   │  ├── brick
   │  │  └── math
   │  │    ├── composer.json
   │  │    ├── src
   │  │     │  ├── BigRational.php
   │  │     │  ├── BigInteger.php
   │  │     │  ├── Exception
   │  │     │  │  ├── RoundingNecessaryException.php
   │  │     │  │  ├── NegativeNumberException.php
   │  │     │  │  ├── DivisionByZeroException.php
   │  │     │  │  ├── MathException.php
   │  │     │  │  ├── IntegerOverflowException.php
   │  │     │  │  └── NumberFormatException.php
   │  │     │  ├── BigNumber.php
   │  │     │  ├── BigDecimal.php
   │  │     │  ├── Internal
   │  │     │  │  ├── CalculatorRegistry.php
   │  │     │  │  ├── Calculator.php
   │  │     │  │  └── Calculator
   │  │     │  │    ├── GmpCalculator.php
   │  │     │  │    ├── NativeCalculator.php
   │  │     │  │    └── BcMathCalculator.php
   │  │     │  └── RoundingMode.php
   │  │    ├── CHANGELOG.md
   │  │    └── LICENSE
   │  ├── dragonmantank
   │  │  └── cron-expression
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  └── Cron
   │  │     │    ├── FieldFactoryInterface.php
   │  │     │    ├── MonthField.php
   │  │     │    ├── FieldFactory.php
   │  │     │    ├── DayOfWeekField.php
   │  │     │    ├── DayOfMonthField.php
   │  │     │    ├── HoursField.php
   │  │     │    ├── FieldInterface.php
   │  │     │    ├── CronExpression.php
   │  │     │    ├── MinutesField.php
   │  │     │    └── AbstractField.php
   │  │    ├── CHANGELOG.md
   │  │    └── LICENSE
   │  ├── nesbot
   │  │  └── carbon
   │  │    ├── composer.json
   │  │    ├── bin
   │  │     │  ├── carbon.bat
   │  │     │  └── carbon
   │  │    ├── extension.neon
   │  │    ├── src
   │  │     │  └── Carbon
   │  │     │    ├── Language.php
   │  │     │    ├── Lang
   │  │     │     │  ├── sr.php
   │  │     │     │  ├── no.php
   │  │     │     │  ├── fr_GA.php
   │  │     │     │  ├── fr_WF.php
   │  │     │     │  ├── or.php
   │  │     │     │  ├── en_TK.php
   │  │     │     │  ├── nus.php
   │  │     │     │  ├── cgg.php
   │  │     │     │  ├── es_419.php
   │  │     │     │  ├── bn_BD.php
   │  │     │     │  ├── zh_Hant_HK.php
   │  │     │     │  ├── so_KE.php
   │  │     │     │  ├── zu.php
   │  │     │     │  ├── uz_Latn.php
   │  │     │     │  ├── ug.php
   │  │     │     │  ├── ber_DZ.php
   │  │     │     │  ├── shn.php
   │  │     │     │  ├── qu_BO.php
   │  │     │     │  ├── ki.php
   │  │     │     │  ├── ta_LK.php
   │  │     │     │  ├── sgs_LT.php
   │  │     │     │  ├── ses.php
   │  │     │     │  ├── en_JM.php
   │  │     │     │  ├── mfe.php
   │  │     │     │  ├── nn.php
   │  │     │     │  ├── ff_CM.php
   │  │     │     │  ├── te_IN.php
   │  │     │     │  ├── ss_ZA.php
   │  │     │     │  ├── luo.php
   │  │     │     │  ├── zh_Hans_HK.php
   │  │     │     │  ├── en_150.php
   │  │     │     │  ├── ky_KG.php
   │  │     │     │  ├── ta.php
   │  │     │     │  ├── ha_GH.php
   │  │     │     │  ├── sah.php
   │  │     │     │  ├── bs.php
   │  │     │     │  ├── fr_GF.php
   │  │     │     │  ├── wa_BE.php
   │  │     │     │  ├── en_VU.php
   │  │     │     │  ├── oc_FR.php
   │  │     │     │  ├── anp_IN.php
   │  │     │     │  ├── en_DK.php
   │  │     │     │  ├── ha_NE.php
   │  │     │     │  ├── ce.php
   │  │     │     │  ├── mjw_IN.php
   │  │     │     │  ├── sv_FI.php
   │  │     │     │  ├── csb_PL.php
   │  │     │     │  ├── sl.php
   │  │     │     │  ├── en_PG.php
   │  │     │     │  ├── zh_SG.php
   │  │     │     │  ├── yuw_PG.php
   │  │     │     │  ├── uz_UZ@cyrillic.php
   │  │     │     │  ├── hr.php
   │  │     │     │  ├── it_CH.php
   │  │     │     │  ├── az_Cyrl.php
   │  │     │     │  ├── sid.php
   │  │     │     │  ├── ps_AF.php
   │  │     │     │  ├── ar_IN.php
   │  │     │     │  ├── to.php
   │  │     │     │  ├── en_SE.php
   │  │     │     │  ├── en_SS.php
   │  │     │     │  ├── nan_TW@latin.php
   │  │     │     │  ├── xh_ZA.php
   │  │     │     │  ├── en_KE.php
   │  │     │     │  ├── nan.php
   │  │     │     │  ├── zh_CN.php
   │  │     │     │  ├── my_MM.php
   │  │     │     │  ├── am.php
   │  │     │     │  ├── en_NL.php
   │  │     │     │  ├── pt_MO.php
   │  │     │     │  ├── li_NL.php
   │  │     │     │  ├── vo.php
   │  │     │     │  ├── en_SC.php
   │  │     │     │  ├── ccp.php
   │  │     │     │  ├── kn.php
   │  │     │     │  ├── en_DE.php
   │  │     │     │  ├── th.php
   │  │     │     │  ├── ve.php
   │  │     │     │  ├── fr_PF.php
   │  │     │     │  ├── ar_ER.php
   │  │     │     │  ├── ga_IE.php
   │  │     │     │  ├── en_FI.php
   │  │     │     │  ├── zh_Hans_MO.php
   │  │     │     │  ├── en_GU.php
   │  │     │     │  ├── fr_FR.php
   │  │     │     │  ├── tg.php
   │  │     │     │  ├── ar_SA.php
   │  │     │     │  ├── uz_Arab.php
   │  │     │     │  ├── es_IC.php
   │  │     │     │  ├── mt_MT.php
   │  │     │     │  ├── el.php
   │  │     │     │  ├── en_CY.php
   │  │     │     │  ├── sm_WS.php
   │  │     │     │  ├── mfe_MU.php
   │  │     │     │  ├── nb_SJ.php
   │  │     │     │  ├── es_BO.php
   │  │     │     │  ├── km_KH.php
   │  │     │     │  ├── en_SD.php
   │  │     │     │  ├── mk_MK.php
   │  │     │     │  ├── om.php
   │  │     │     │  ├── sw_CD.php
   │  │     │     │  ├── niu.php
   │  │     │     │  ├── zh.php
   │  │     │     │  ├── en_HK.php
   │  │     │     │  ├── fur_IT.php
   │  │     │     │  ├── agr_PE.php
   │  │     │     │  ├── ky.php
   │  │     │     │  ├── tg_TJ.php
   │  │     │     │  ├── hr_BA.php
   │  │     │     │  ├── tr_CY.php
   │  │     │     │  ├── kk_KZ.php
   │  │     │     │  ├── ig.php
   │  │     │     │  ├── en_ZA.php
   │  │     │     │  ├── brx.php
   │  │     │     │  ├── iw.php
   │  │     │     │  ├── aa_ET.php
   │  │     │     │  ├── ar.php
   │  │     │     │  ├── se_SE.php
   │  │     │     │  ├── ne.php
   │  │     │     │  ├── dz_BT.php
   │  │     │     │  ├── tet.php
   │  │     │     │  ├── en_ZM.php
   │  │     │     │  ├── fr_ML.php
   │  │     │     │  ├── as_IN.php
   │  │     │     │  ├── es_AR.php
   │  │     │     │  ├── sw.php
   │  │     │     │  ├── dav.php
   │  │     │     │  ├── ms_MY.php
   │  │     │     │  ├── quz_PE.php
   │  │     │     │  ├── ko.php
   │  │     │     │  ├── nl_CW.php
   │  │     │     │  ├── nb.php
   │  │     │     │  ├── gu.php
   │  │     │     │  ├── ar_KW.php
   │  │     │     │  ├── chr.php
   │  │     │     │  ├── kn_IN.php
   │  │     │     │  ├── es_PR.php
   │  │     │     │  ├── miq_NI.php
   │  │     │     │  ├── ca.php
   │  │     │     │  ├── lu.php
   │  │     │     │  ├── ug_CN.php
   │  │     │     │  ├── ar_DZ.php
   │  │     │     │  ├── nl_BE.php
   │  │     │     │  ├── fr_MC.php
   │  │     │     │  ├── de_LI.php
   │  │     │     │  ├── gu_IN.php
   │  │     │     │  ├── fr_VU.php
   │  │     │     │  ├── en_IM.php
   │  │     │     │  ├── nl_SX.php
   │  │     │     │  ├── ru_UA.php
   │  │     │     │  ├── mg.php
   │  │     │     │  ├── kea.php
   │  │     │     │  ├── aa_ER@saaho.php
   │  │     │     │  ├── nso.php
   │  │     │     │  ├── mag_IN.php
   │  │     │     │  ├── wal_ET.php
   │  │     │     │  ├── sr_RS@latin.php
   │  │     │     │  ├── iu.php
   │  │     │     │  ├── es_US.php
   │  │     │     │  ├── dv.php
   │  │     │     │  ├── wo.php
   │  │     │     │  ├── ur_PK.php
   │  │     │     │  ├── crh_UA.php
   │  │     │     │  ├── guz.php
   │  │     │     │  ├── en_NU.php
   │  │     │     │  ├── hu.php
   │  │     │     │  ├── kkj.php
   │  │     │     │  ├── se.php
   │  │     │     │  ├── ayc_PE.php
   │  │     │     │  ├── ar_IQ.php
   │  │     │     │  ├── hsb_DE.php
   │  │     │     │  ├── ms.php
   │  │     │     │  ├── fr_BE.php
   │  │     │     │  ├── nn_NO.php
   │  │     │     │  ├── ro.php
   │  │     │     │  ├── en_NF.php
   │  │     │     │  ├── sw_TZ.php
   │  │     │     │  ├── fr_GQ.php
   │  │     │     │  ├── ar_TN.php
   │  │     │     │  ├── qu_EC.php
   │  │     │     │  ├── en_WS.php
   │  │     │     │  ├── doi_IN.php
   │  │     │     │  ├── es_PH.php
   │  │     │     │  ├── en_PH.php
   │  │     │     │  ├── en_ER.php
   │  │     │     │  ├── en_AU.php
   │  │     │     │  ├── bg.php
   │  │     │     │  ├── es_GT.php
   │  │     │     │  ├── af_ZA.php
   │  │     │     │  ├── st_ZA.php
   │  │     │     │  ├── tpi_PG.php
   │  │     │     │  ├── ts.php
   │  │     │     │  ├── smn.php
   │  │     │     │  ├── yi.php
   │  │     │     │  ├── sat.php
   │  │     │     │  ├── ja_JP.php
   │  │     │     │  ├── lzh.php
   │  │     │     │  ├── bez.php
   │  │     │     │  ├── pa_Arab.php
   │  │     │     │  ├── xh.php
   │  │     │     │  ├── fr_DZ.php
   │  │     │     │  ├── en_MT.php
   │  │     │     │  ├── hif.php
   │  │     │     │  ├── gom_Latn.php
   │  │     │     │  ├── sah_RU.php
   │  │     │     │  ├── ks_IN.php
   │  │     │     │  ├── pt_GW.php
   │  │     │     │  ├── mi_NZ.php
   │  │     │     │  ├── wo_SN.php
   │  │     │     │  ├── yo_NG.php
   │  │     │     │  ├── sg.php
   │  │     │     │  ├── es_CL.php
   │  │     │     │  ├── cy.php
   │  │     │     │  ├── en_SZ.php
   │  │     │     │  ├── ar_BH.php
   │  │     │     │  ├── en_US_Posix.php
   │  │     │     │  ├── en_MY.php
   │  │     │     │  ├── da_GL.php
   │  │     │     │  ├── en_FM.php
   │  │     │     │  ├── tcy_IN.php
   │  │     │     │  ├── az_Latn.php
   │  │     │     │  ├── mr_IN.php
   │  │     │     │  ├── zh_HK.php
   │  │     │     │  ├── id_ID.php
   │  │     │     │  ├── se_NO.php
   │  │     │     │  ├── be_BY.php
   │  │     │     │  ├── hak.php
   │  │     │     │  ├── saq.php
   │  │     │     │  ├── ms_SG.php
   │  │     │     │  ├── hif_FJ.php
   │  │     │     │  ├── ln.php
   │  │     │     │  ├── en_LS.php
   │  │     │     │  ├── am_ET.php
   │  │     │     │  ├── gv_GB.php
   │  │     │     │  ├── csb.php
   │  │     │     │  ├── ar_SD.php
   │  │     │     │  ├── rm.php
   │  │     │     │  ├── ta_IN.php
   │  │     │     │  ├── szl_PL.php
   │  │     │     │  ├── ks_IN@devanagari.php
   │  │     │     │  ├── pa_PK.php
   │  │     │     │  ├── cv.php
   │  │     │     │  ├── lag.php
   │  │     │     │  ├── es_BR.php
   │  │     │     │  ├── ln_CF.php
   │  │     │     │  ├── fr_MF.php
   │  │     │     │  ├── fy_DE.php
   │  │     │     │  ├── ca_IT.php
   │  │     │     │  ├── om_KE.php
   │  │     │     │  ├── ru_KZ.php
   │  │     │     │  ├── sv_SE.php
   │  │     │     │  ├── ar_TD.php
   │  │     │     │  ├── agr.php
   │  │     │     │  ├── fr_YT.php
   │  │     │     │  ├── es_CR.php
   │  │     │     │  ├── tzl.php
   │  │     │     │  ├── de_CH.php
   │  │     │     │  ├── sa.php
   │  │     │     │  ├── gom.php
   │  │     │     │  ├── ar_Shakl.php
   │  │     │     │  ├── mai.php
   │  │     │     │  ├── lv_LV.php
   │  │     │     │  ├── az_IR.php
   │  │     │     │  ├── it_VA.php
   │  │     │     │  ├── ne_NP.php
   │  │     │     │  ├── ha.php
   │  │     │     │  ├── si_LK.php
   │  │     │     │  ├── st.php
   │  │     │     │  ├── nhn_MX.php
   │  │     │     │  ├── cmn_TW.php
   │  │     │     │  ├── ca_FR.php
   │  │     │     │  ├── yue_Hans.php
   │  │     │     │  ├── he_IL.php
   │  │     │     │  ├── ks.php
   │  │     │     │  ├── en_MG.php
   │  │     │     │  ├── bho.php
   │  │     │     │  ├── prg.php
   │  │     │     │  ├── li.php
   │  │     │     │  ├── es_BZ.php
   │  │     │     │  ├── mas_TZ.php
   │  │     │     │  ├── ksb.php
   │  │     │     │  ├── szl.php
   │  │     │     │  ├── dz.php
   │  │     │     │  ├── be.php
   │  │     │     │  ├── sr_Latn_BA.php
   │  │     │     │  ├── en_MW.php
   │  │     │     │  ├── sr_RS.php
   │  │     │     │  ├── en_IO.php
   │  │     │     │  ├── fr_CD.php
   │  │     │     │  ├── ru_KG.php
   │  │     │     │  ├── hy.php
   │  │     │     │  ├── ta_MY.php
   │  │     │     │  ├── pa_IN.php
   │  │     │     │  ├── en_TO.php
   │  │     │     │  ├── lb_LU.php
   │  │     │     │  ├── en_KY.php
   │  │     │     │  ├── ms_BN.php
   │  │     │     │  ├── fr_SY.php
   │  │     │     │  ├── tr.php
   │  │     │     │  ├── tzm.php
   │  │     │     │  ├── tt_RU@iqtelif.php
   │  │     │     │  ├── he.php
   │  │     │     │  ├── ff_MR.php
   │  │     │     │  ├── lzh_TW.php
   │  │     │     │  ├── en_KN.php
   │  │     │     │  ├── en_MU.php
   │  │     │     │  ├── cmn.php
   │  │     │     │  ├── en_CA.php
   │  │     │     │  ├── rwk.php
   │  │     │     │  ├── ka.php
   │  │     │     │  ├── teo_KE.php
   │  │     │     │  ├── bi_VU.php
   │  │     │     │  ├── mgo.php
   │  │     │     │  ├── pap_AW.php
   │  │     │     │  ├── pt_TL.php
   │  │     │     │  ├── aa_DJ.php
   │  │     │     │  ├── en_IN.php
   │  │     │     │  ├── ro_MD.php
   │  │     │     │  ├── bhb.php
   │  │     │     │  ├── sc.php
   │  │     │     │  ├── pap_CW.php
   │  │     │     │  ├── en_IE.php
   │  │     │     │  ├── es_GQ.php
   │  │     │     │  ├── en_MH.php
   │  │     │     │  ├── tk.php
   │  │     │     │  ├── sat_IN.php
   │  │     │     │  ├── yav.php
   │  │     │     │  ├── ca_ES_Valencia.php
   │  │     │     │  ├── de_IT.php
   │  │     │     │  ├── fo_FO.php
   │  │     │     │  ├── ro_RO.php
   │  │     │     │  ├── sl_SI.php
   │  │     │     │  ├── ru_RU.php
   │  │     │     │  ├── ii.php
   │  │     │     │  ├── kde.php
   │  │     │     │  ├── jmc.php
   │  │     │     │  ├── mer.php
   │  │     │     │  ├── nb_NO.php
   │  │     │     │  ├── es_PA.php
   │  │     │     │  ├── en_TT.php
   │  │     │     │  ├── ga.php
   │  │     │     │  ├── jv.php
   │  │     │     │  ├── miq.php
   │  │     │     │  ├── khq.php
   │  │     │     │  ├── ksh.php
   │  │     │     │  ├── th_TH.php
   │  │     │     │  ├── zh_Hant.php
   │  │     │     │  ├── hi_IN.php
   │  │     │     │  ├── ksf.php
   │  │     │     │  ├── ca_ES.php
   │  │     │     │  ├── nds_NL.php
   │  │     │     │  ├── en_GY.php
   │  │     │     │  ├── pt_MZ.php
   │  │     │     │  ├── da.php
   │  │     │     │  ├── sq_MK.php
   │  │     │     │  ├── fr_TG.php
   │  │     │     │  ├── fr_SC.php
   │  │     │     │  ├── ta_SG.php
   │  │     │     │  ├── ar_MA.php
   │  │     │     │  ├── mas.php
   │  │     │     │  ├── gsw_CH.php
   │  │     │     │  ├── my.php
   │  │     │     │  ├── fr.php
   │  │     │     │  ├── af.php
   │  │     │     │  ├── ml_IN.php
   │  │     │     │  ├── kab.php
   │  │     │     │  ├── az_AZ.php
   │  │     │     │  ├── en_UG.php
   │  │     │     │  ├── kok.php
   │  │     │     │  ├── ne_IN.php
   │  │     │     │  ├── es_EC.php
   │  │     │     │  ├── ml.php
   │  │     │     │  ├── byn_ER.php
   │  │     │     │  ├── en_ISO.php
   │  │     │     │  ├── bo.php
   │  │     │     │  ├── ts_ZA.php
   │  │     │     │  ├── so_DJ.php
   │  │     │     │  ├── en_RW.php
   │  │     │     │  ├── gd.php
   │  │     │     │  ├── bg_BG.php
   │  │     │     │  ├── dje.php
   │  │     │     │  ├── wae.php
   │  │     │     │  ├── chr_US.php
   │  │     │     │  ├── bs_Latn.php
   │  │     │     │  ├── ku.php
   │  │     │     │  ├── zh_Hant_MO.php
   │  │     │     │  ├── lt.php
   │  │     │     │  ├── ik.php
   │  │     │     │  ├── ln_CD.php
   │  │     │     │  ├── ar_SS.php
   │  │     │     │  ├── de_DE.php
   │  │     │     │  ├── en_TC.php
   │  │     │     │  ├── ff.php
   │  │     │     │  ├── shs_CA.php
   │  │     │     │  ├── en_SI.php
   │  │     │     │  ├── en_UM.php
   │  │     │     │  ├── hne.php
   │  │     │     │  ├── mr.php
   │  │     │     │  ├── ar_SY.php
   │  │     │     │  ├── nan_TW.php
   │  │     │     │  ├── ar_JO.php
   │  │     │     │  ├── eu.php
   │  │     │     │  ├── en_PN.php
   │  │     │     │  ├── pt_LU.php
   │  │     │     │  ├── es_PY.php
   │  │     │     │  ├── en_GH.php
   │  │     │     │  ├── en_US.php
   │  │     │     │  ├── fr_NC.php
   │  │     │     │  ├── tt_RU.php
   │  │     │     │  ├── yue_HK.php
   │  │     │     │  ├── mhr_RU.php
   │  │     │     │  ├── fil_PH.php
   │  │     │     │  ├── os_RU.php
   │  │     │     │  ├── fr_BL.php
   │  │     │     │  ├── zh_Hans.php
   │  │     │     │  ├── sq_XK.php
   │  │     │     │  ├── ar_KM.php
   │  │     │     │  ├── gez.php
   │  │     │     │  ├── el_CY.php
   │  │     │     │  ├── sm.php
   │  │     │     │  ├── ar_PS.php
   │  │     │     │  ├── mni_IN.php
   │  │     │     │  ├── en_MS.php
   │  │     │     │  ├── unm_US.php
   │  │     │     │  ├── ti.php
   │  │     │     │  ├── cs_CZ.php
   │  │     │     │  ├── sv.php
   │  │     │     │  ├── in.php
   │  │     │     │  ├── shs.php
   │  │     │     │  ├── ar_LY.php
   │  │     │     │  ├── sv_AX.php
   │  │     │     │  ├── en_FK.php
   │  │     │     │  ├── an.php
   │  │     │     │  ├── zh_TW.php
   │  │     │     │  ├── ka_GE.php
   │  │     │     │  ├── kln.php
   │  │     │     │  ├── hak_TW.php
   │  │     │     │  ├── fi_FI.php
   │  │     │     │  ├── pt_CH.php
   │  │     │     │  ├── en.php
   │  │     │     │  ├── gsw_FR.php
   │  │     │     │  ├── om_ET.php
   │  │     │     │  ├── ca_AD.php
   │  │     │     │  ├── agq.php
   │  │     │     │  ├── gez_ER.php
   │  │     │     │  ├── shi.php
   │  │     │     │  ├── en_BE.php
   │  │     │     │  ├── fr_GP.php
   │  │     │     │  ├── to_TO.php
   │  │     │     │  ├── gl.php
   │  │     │     │  ├── luy.php
   │  │     │     │  ├── dsb_DE.php
   │  │     │     │  ├── fr_RE.php
   │  │     │     │  ├── pt_AO.php
   │  │     │     │  ├── en_VG.php
   │  │     │     │  ├── gsw_LI.php
   │  │     │     │  ├── cu.php
   │  │     │     │  ├── nd.php
   │  │     │     │  ├── dua.php
   │  │     │     │  ├── uz.php
   │  │     │     │  ├── the.php
   │  │     │     │  ├── yi_US.php
   │  │     │     │  ├── kam.php
   │  │     │     │  ├── en_CK.php
   │  │     │     │  ├── ln_AO.php
   │  │     │     │  ├── fr_CI.php
   │  │     │     │  ├── es_HN.php
   │  │     │     │  ├── fr_RW.php
   │  │     │     │  ├── lg_UG.php
   │  │     │     │  ├── ti_ET.php
   │  │     │     │  ├── de.php
   │  │     │     │  ├── en_AG.php
   │  │     │     │  ├── fr_PM.php
   │  │     │     │  ├── doi.php
   │  │     │     │  ├── fr_CM.php
   │  │     │     │  ├── es_CU.php
   │  │     │     │  ├── vi.php
   │  │     │     │  ├── en_BM.php
   │  │     │     │  ├── sh.php
   │  │     │     │  ├── aa.php
   │  │     │     │  ├── oc.php
   │  │     │     │  ├── brx_IN.php
   │  │     │     │  ├── twq.php
   │  │     │     │  ├── mgh.php
   │  │     │     │  ├── sr_ME.php
   │  │     │     │  ├── bm.php
   │  │     │     │  ├── en_NZ.php
   │  │     │     │  ├── nmg.php
   │  │     │     │  ├── mag.php
   │  │     │     │  ├── en_VC.php
   │  │     │     │  ├── de_LU.php
   │  │     │     │  ├── as.php
   │  │     │     │  ├── aa_ER.php
   │  │     │     │  ├── ee_TG.php
   │  │     │     │  ├── ar_LB.php
   │  │     │     │  ├── sr_Latn_ME.php
   │  │     │     │  ├── ha_NG.php
   │  │     │     │  ├── pa.php
   │  │     │     │  ├── en_BW.php
   │  │     │     │  ├── kk.php
   │  │     │     │  ├── yo_BJ.php
   │  │     │     │  ├── mai_IN.php
   │  │     │     │  ├── sr_Latn_XK.php
   │  │     │     │  ├── en_GD.php
   │  │     │     │  ├── ar_OM.php
   │  │     │     │  ├── en_BI.php
   │  │     │     │  ├── zgh.php
   │  │     │     │  ├── so_SO.php
   │  │     │     │  ├── pt.php
   │  │     │     │  ├── iu_CA.php
   │  │     │     │  ├── hsb.php
   │  │     │     │  ├── ln_CG.php
   │  │     │     │  ├── fa_IR.php
   │  │     │     │  ├── eu_ES.php
   │  │     │     │  ├── pl_PL.php
   │  │     │     │  ├── fr_DJ.php
   │  │     │     │  ├── fr_MR.php
   │  │     │     │  ├── mk.php
   │  │     │     │  ├── mjw.php
   │  │     │     │  ├── zh_YUE.php
   │  │     │     │  ├── sk_SK.php
   │  │     │     │  ├── km.php
   │  │     │     │  ├── ar_EG.php
   │  │     │     │  ├── vi_VN.php
   │  │     │     │  ├── br_FR.php
   │  │     │     │  ├── et_EE.php
   │  │     │     │  ├── ru.php
   │  │     │     │  ├── en_SX.php
   │  │     │     │  ├── az_Arab.php
   │  │     │     │  ├── br.php
   │  │     │     │  ├── ar_DJ.php
   │  │     │     │  ├── is.php
   │  │     │     │  ├── rn.php
   │  │     │     │  ├── fy_NL.php
   │  │     │     │  ├── is_IS.php
   │  │     │     │  ├── kl.php
   │  │     │     │  ├── en_AI.php
   │  │     │     │  ├── fr_KM.php
   │  │     │     │  ├── af_NA.php
   │  │     │     │  ├── en_CC.php
   │  │     │     │  ├── en_CM.php
   │  │     │     │  ├── nds_DE.php
   │  │     │     │  ├── ee.php
   │  │     │     │  ├── cs.php
   │  │     │     │  ├── pt_GQ.php
   │  │     │     │  ├── ff_SN.php
   │  │     │     │  ├── xog.php
   │  │     │     │  ├── lt_LT.php
   │  │     │     │  ├── ia.php
   │  │     │     │  ├── nds.php
   │  │     │     │  ├── ast_ES.php
   │  │     │     │  ├── fr_HT.php
   │  │     │     │  ├── lij_IT.php
   │  │     │     │  ├── en_SL.php
   │  │     │     │  ├── el_GR.php
   │  │     │     │  ├── en_BS.php
   │  │     │     │  ├── es_CO.php
   │  │     │     │  ├── zu_ZA.php
   │  │     │     │  ├── nhn.php
   │  │     │     │  ├── en_SB.php
   │  │     │     │  ├── nl_BQ.php
   │  │     │     │  ├── rw_RW.php
   │  │     │     │  ├── uk.php
   │  │     │     │  ├── tl_PH.php
   │  │     │     │  ├── sd_IN@devanagari.php
   │  │     │     │  ├── nyn.php
   │  │     │     │  ├── unm.php
   │  │     │     │  ├── te.php
   │  │     │     │  ├── zh_Hant_TW.php
   │  │     │     │  ├── vai.php
   │  │     │     │  ├── en_DG.php
   │  │     │     │  ├── bhb_IN.php
   │  │     │     │  ├── so_ET.php
   │  │     │     │  ├── mi.php
   │  │     │     │  ├── ia_FR.php
   │  │     │     │  ├── en_LC.php
   │  │     │     │  ├── yue.php
   │  │     │     │  ├── sr_Cyrl_ME.php
   │  │     │     │  ├── ti_ER.php
   │  │     │     │  ├── crh.php
   │  │     │     │  ├── it_IT.php
   │  │     │     │  ├── qu.php
   │  │     │     │  ├── fr_MU.php
   │  │     │     │  ├── pl.php
   │  │     │     │  ├── sgs.php
   │  │     │     │  ├── fo_DK.php
   │  │     │     │  ├── anp.php
   │  │     │     │  ├── es_UY.php
   │  │     │     │  ├── en_PW.php
   │  │     │     │  ├── en_IL.php
   │  │     │     │  ├── lb.php
   │  │     │     │  ├── ber.php
   │  │     │     │  ├── sid_ET.php
   │  │     │     │  ├── rw.php
   │  │     │     │  ├── wa.php
   │  │     │     │  ├── en_GI.php
   │  │     │     │  ├── ps.php
   │  │     │     │  ├── sa_IN.php
   │  │     │     │  ├── eo.php
   │  │     │     │  ├── nl_NL.php
   │  │     │     │  ├── se_FI.php
   │  │     │     │  ├── lrc.php
   │  │     │     │  ├── hy_AM.php
   │  │     │     │  ├── lv.php
   │  │     │     │  ├── ig_NG.php
   │  │     │     │  ├── id.php
   │  │     │     │  ├── ik_CA.php
   │  │     │     │  ├── ar_MR.php
   │  │     │     │  ├── naq.php
   │  │     │     │  ├── pt_BR.php
   │  │     │     │  ├── sw_KE.php
   │  │     │     │  ├── bs_Cyrl.php
   │  │     │     │  ├── quz.php
   │  │     │     │  ├── mni.php
   │  │     │     │  ├── lrc_IQ.php
   │  │     │     │  ├── cy_GB.php
   │  │     │     │  ├── ja.php
   │  │     │     │  ├── es_PE.php
   │  │     │     │  ├── pt_PT.php
   │  │     │     │  ├── ss.php
   │  │     │     │  ├── en_MO.php
   │  │     │     │  ├── ko_KR.php
   │  │     │     │  ├── hne_IN.php
   │  │     │     │  ├── vai_Latn.php
   │  │     │     │  ├── ur_IN.php
   │  │     │     │  ├── haw.php
   │  │     │     │  ├── kok_IN.php
   │  │     │     │  ├── fi.php
   │  │     │     │  ├── fil.php
   │  │     │     │  ├── fr_MA.php
   │  │     │     │  ├── uz_UZ.php
   │  │     │     │  ├── teo.php
   │  │     │     │  ├── the_NP.php
   │  │     │     │  ├── zh_MO.php
   │  │     │     │  ├── sbp.php
   │  │     │     │  ├── mn_MN.php
   │  │     │     │  ├── ber_MA.php
   │  │     │     │  ├── es_NI.php
   │  │     │     │  ├── pt_ST.php
   │  │     │     │  ├── it.php
   │  │     │     │  ├── en_PK.php
   │  │     │     │  ├── tzm_Latn.php
   │  │     │     │  ├── en_ZW.php
   │  │     │     │  ├── lkt.php
   │  │     │     │  ├── si.php
   │  │     │     │  ├── bem_ZM.php
   │  │     │     │  ├── en_JE.php
   │  │     │     │  ├── es_ES.php
   │  │     │     │  ├── fr_TD.php
   │  │     │     │  ├── ko_KP.php
   │  │     │     │  ├── sn.php
   │  │     │     │  ├── kw_GB.php
   │  │     │     │  ├── vai_Vaii.php
   │  │     │     │  ├── en_PR.php
   │  │     │     │  ├── cv_RU.php
   │  │     │     │  ├── fr_LU.php
   │  │     │     │  ├── kl_GL.php
   │  │     │     │  ├── ebu.php
   │  │     │     │  ├── raj_IN.php
   │  │     │     │  ├── raj.php
   │  │     │     │  ├── ar_SO.php
   │  │     │     │  ├── sr_Cyrl.php
   │  │     │     │  ├── so.php
   │  │     │     │  ├── tlh.php
   │  │     │     │  ├── fr_BJ.php
   │  │     │     │  ├── bho_IN.php
   │  │     │     │  ├── sr_Cyrl_XK.php
   │  │     │     │  ├── en_CH.php
   │  │     │     │  ├── gd_GB.php
   │  │     │     │  ├── az.php
   │  │     │     │  ├── en_BZ.php
   │  │     │     │  ├── fr_BF.php
   │  │     │     │  ├── tig.php
   │  │     │     │  ├── en_AT.php
   │  │     │     │  ├── es_MX.php
   │  │     │     │  ├── wae_CH.php
   │  │     │     │  ├── mo.php
   │  │     │     │  ├── ur.php
   │  │     │     │  ├── bas.php
   │  │     │     │  ├── en_VI.php
   │  │     │     │  ├── it_SM.php
   │  │     │     │  ├── fr_SN.php
   │  │     │     │  ├── en_SH.php
   │  │     │     │  ├── en_CX.php
   │  │     │     │  ├── fr_GN.php
   │  │     │     │  ├── uk_UA.php
   │  │     │     │  ├── zh_Hans_SG.php
   │  │     │     │  ├── es_EA.php
   │  │     │     │  ├── lg.php
   │  │     │     │  ├── en_TZ.php
   │  │     │     │  ├── ar_QA.php
   │  │     │     │  ├── fr_TN.php
   │  │     │     │  ├── es_VE.php
   │  │     │     │  ├── mg_MG.php
   │  │     │     │  ├── wal.php
   │  │     │     │  ├── ar_YE.php
   │  │     │     │  ├── fur.php
   │  │     │     │  ├── tt.php
   │  │     │     │  ├── fa_AF.php
   │  │     │     │  ├── yuw.php
   │  │     │     │  ├── ar_AE.php
   │  │     │     │  ├── hi.php
   │  │     │     │  ├── ayc.php
   │  │     │     │  ├── sq_AL.php
   │  │     │     │  ├── asa.php
   │  │     │     │  ├── tcy.php
   │  │     │     │  ├── fr_CF.php
   │  │     │     │  ├── yue_Hant.php
   │  │     │     │  ├── an_ES.php
   │  │     │     │  ├── shn_MM.php
   │  │     │     │  ├── de_BE.php
   │  │     │     │  ├── es_SV.php
   │  │     │     │  ├── mzn.php
   │  │     │     │  ├── nnh.php
   │  │     │     │  ├── sc_IT.php
   │  │     │     │  ├── fa.php
   │  │     │     │  ├── lo.php
   │  │     │     │  ├── de_AT.php
   │  │     │     │  ├── bn.php
   │  │     │     │  ├── ak_GH.php
   │  │     │     │  ├── gv.php
   │  │     │     │  ├── en_GG.php
   │  │     │     │  ├── fr_CH.php
   │  │     │     │  ├── mn.php
   │  │     │     │  ├── lij.php
   │  │     │     │  ├── fr_CG.php
   │  │     │     │  ├── en_NA.php
   │  │     │     │  ├── nl.php
   │  │     │     │  ├── rof.php
   │  │     │     │  ├── fr_NE.php
   │  │     │     │  ├── ar_IL.php
   │  │     │     │  ├── nl_SR.php
   │  │     │     │  ├── bi.php
   │  │     │     │  ├── fo.php
   │  │     │     │  ├── sd_IN.php
   │  │     │     │  ├── fr_BI.php
   │  │     │     │  ├── tn.php
   │  │     │     │  ├── bo_IN.php
   │  │     │     │  ├── kab_DZ.php
   │  │     │     │  ├── niu_NU.php
   │  │     │     │  ├── i18n.php
   │  │     │     │  ├── en_GB.php
   │  │     │     │  ├── sr_Cyrl_BA.php
   │  │     │     │  ├── ru_MD.php
   │  │     │     │  ├── jgo.php
   │  │     │     │  ├── en_TV.php
   │  │     │     │  ├── ccp_IN.php
   │  │     │     │  ├── nso_ZA.php
   │  │     │     │  ├── ht_HT.php
   │  │     │     │  ├── es.php
   │  │     │     │  ├── fr_MQ.php
   │  │     │     │  ├── shi_Tfng.php
   │  │     │     │  ├── pt_CV.php
   │  │     │     │  ├── fy.php
   │  │     │     │  ├── or_IN.php
   │  │     │     │  ├── yo.php
   │  │     │     │  ├── shi_Latn.php
   │  │     │     │  ├── en_NG.php
   │  │     │     │  ├── ewo.php
   │  │     │     │  ├── bn_IN.php
   │  │     │     │  ├── ve_ZA.php
   │  │     │     │  ├── tk_TM.php
   │  │     │     │  ├── nr.php
   │  │     │     │  ├── dsb.php
   │  │     │     │  ├── uz_Cyrl.php
   │  │     │     │  ├── en_GM.php
   │  │     │     │  ├── lo_LA.php
   │  │     │     │  ├── en_AS.php
   │  │     │     │  ├── mhr.php
   │  │     │     │  ├── hr_HR.php
   │  │     │     │  ├── mua.php
   │  │     │     │  ├── ff_GN.php
   │  │     │     │  ├── gsw.php
   │  │     │     │  ├── gez_ET.php
   │  │     │     │  ├── vun.php
   │  │     │     │  ├── en_DM.php
   │  │     │     │  ├── dyo.php
   │  │     │     │  ├── en_FJ.php
   │  │     │     │  ├── byn.php
   │  │     │     │  ├── tig_ER.php
   │  │     │     │  ├── en_NR.php
   │  │     │     │  ├── pa_Guru.php
   │  │     │     │  ├── fr_CA.php
   │  │     │     │  ├── hu_HU.php
   │  │     │     │  ├── es_DO.php
   │  │     │     │  ├── fr_MG.php
   │  │     │     │  ├── sr_Latn.php
   │  │     │     │  ├── sd.php
   │  │     │     │  ├── sw_UG.php
   │  │     │     │  ├── bem.php
   │  │     │     │  ├── bs_BA.php
   │  │     │     │  ├── ht.php
   │  │     │     │  ├── en_KI.php
   │  │     │     │  ├── tl.php
   │  │     │     │  ├── en_MP.php
   │  │     │     │  ├── ckb.php
   │  │     │     │  ├── sk.php
   │  │     │     │  ├── ku_TR.php
   │  │     │     │  ├── en_LR.php
   │  │     │     │  ├── nr_ZA.php
   │  │     │     │  ├── dv_MV.php
   │  │     │     │  ├── en_BB.php
   │  │     │     │  ├── sq.php
   │  │     │     │  ├── en_001.php
   │  │     │     │  ├── bo_CN.php
   │  │     │     │  ├── ast.php
   │  │     │     │  ├── ce_RU.php
   │  │     │     │  ├── gl_ES.php
   │  │     │     │  ├── tpi.php
   │  │     │     │  ├── en_SG.php
   │  │     │     │  ├── da_DK.php
   │  │     │     │  ├── ar_EH.php
   │  │     │     │  ├── tr_TR.php
   │  │     │     │  ├── ak.php
   │  │     │     │  ├── seh.php
   │  │     │     │  ├── os.php
   │  │     │     │  ├── pap.php
   │  │     │     │  ├── kw.php
   │  │     │     │  ├── be_BY@latin.php
   │  │     │     │  ├── nl_AW.php
   │  │     │     │  ├── et.php
   │  │     │     │  ├── mt.php
   │  │     │     │  ├── tn_ZA.php
   │  │     │     │  └── ru_BY.php
   │  │     │    ├── List
   │  │     │     │  ├── regions.php
   │  │     │     │  └── languages.php
   │  │     │    ├── Traits
   │  │     │     │  ├── Serialization.php
   │  │     │     │  ├── DeprecatedPeriodProperties.php
   │  │     │     │  ├── Date.php
   │  │     │     │  ├── Timestamp.php
   │  │     │     │  ├── ObjectInitialisation.php
   │  │     │     │  ├── Units.php
   │  │     │     │  ├── Options.php
   │  │     │     │  ├── Difference.php
   │  │     │     │  ├── Macro.php
   │  │     │     │  ├── Localization.php
   │  │     │     │  ├── Modifiers.php
   │  │     │     │  ├── LocalFactory.php
   │  │     │     │  ├── Mutability.php
   │  │     │     │  ├── IntervalRounding.php
   │  │     │     │  ├── Comparison.php
   │  │     │     │  ├── Rounding.php
   │  │     │     │  ├── IntervalStep.php
   │  │     │     │  ├── Converter.php
   │  │     │     │  ├── ToStringFormat.php
   │  │     │     │  ├── MagicParameter.php
   │  │     │     │  ├── Week.php
   │  │     │     │  ├── StaticLocalization.php
   │  │     │     │  ├── Cast.php
   │  │     │     │  ├── StaticOptions.php
   │  │     │     │  ├── Boundaries.php
   │  │     │     │  ├── Mixin.php
   │  │     │     │  ├── Test.php
   │  │     │     │  └── Creator.php
   │  │     │    ├── FactoryImmutable.php
   │  │     │    ├── Callback.php
   │  │     │    ├── Carbon.php
   │  │     │    ├── Month.php
   │  │     │    ├── WrapperClock.php
   │  │     │    ├── CarbonInterface.php
   │  │     │    ├── Translator.php
   │  │     │    ├── CarbonConverterInterface.php
   │  │     │    ├── CarbonImmutable.php
   │  │     │    ├── TranslatorStrongTypeInterface.php
   │  │     │    ├── WeekDay.php
   │  │     │    ├── Factory.php
   │  │     │    ├── Exceptions
   │  │     │     │  ├── UnknownMethodException.php
   │  │     │     │  ├── EndLessPeriodException.php
   │  │     │     │  ├── ImmutableException.php
   │  │     │     │  ├── InvalidTimeZoneException.php
   │  │     │     │  ├── OutOfRangeException.php
   │  │     │     │  ├── UnknownUnitException.php
   │  │     │     │  ├── RuntimeException.php
   │  │     │     │  ├── NotACarbonClassException.php
   │  │     │     │  ├── InvalidDateException.php
   │  │     │     │  ├── InvalidArgumentException.php
   │  │     │     │  ├── BadMethodCallException.php
   │  │     │     │  ├── UnknownSetterException.php
   │  │     │     │  ├── UnsupportedUnitException.php
   │  │     │     │  ├── InvalidTypeException.php
   │  │     │     │  ├── ParseErrorException.php
   │  │     │     │  ├── BadFluentConstructorException.php
   │  │     │     │  ├── UnreachableException.php
   │  │     │     │  ├── UnitNotConfiguredException.php
   │  │     │     │  ├── Exception.php
   │  │     │     │  ├── NotLocaleAwareException.php
   │  │     │     │  ├── UnitException.php
   │  │     │     │  ├── InvalidPeriodDateException.php
   │  │     │     │  ├── BadComparisonUnitException.php
   │  │     │     │  ├── InvalidPeriodParameterException.php
   │  │     │     │  ├── InvalidIntervalException.php
   │  │     │     │  ├── UnknownGetterException.php
   │  │     │     │  ├── NotAPeriodException.php
   │  │     │     │  ├── InvalidFormatException.php
   │  │     │     │  ├── InvalidCastException.php
   │  │     │     │  └── BadFluentSetterException.php
   │  │     │    ├── TranslatorImmutable.php
   │  │     │    ├── CarbonInterval.php
   │  │     │    ├── CarbonPeriodImmutable.php
   │  │     │    ├── Constants
   │  │     │     │  ├── TranslationOptions.php
   │  │     │     │  ├── DiffOptions.php
   │  │     │     │  ├── Format.php
   │  │     │     │  └── UnitValue.php
   │  │     │    ├── PHPStan
   │  │     │     │  ├── MacroExtension.php
   │  │     │     │  └── MacroMethodReflection.php
   │  │     │    ├── MessageFormatter
   │  │     │     │  └── MessageFormatterMapper.php
   │  │     │    ├── Unit.php
   │  │     │    ├── CarbonPeriod.php
   │  │     │    ├── Cli
   │  │     │     │  └── Invoker.php
   │  │     │    ├── AbstractTranslator.php
   │  │     │    ├── CarbonTimeZone.php
   │  │     │    └── Laravel
   │  │     │       └── ServiceProvider.php
   │  │    ├── LICENSE
   │  │    ├── lazy
   │  │     │  └── Carbon
   │  │     │    ├── TranslatorStrongType.php
   │  │     │    ├── MessageFormatter
   │  │     │     │  ├── MessageFormatterMapperWeakType.php
   │  │     │     │  └── MessageFormatterMapperStrongType.php
   │  │     │    ├── ProtectedDatePeriod.php
   │  │     │    ├── UnprotectedDatePeriod.php
   │  │     │    └── TranslatorWeakType.php
   │  │    └── readme.md
   │  ├── guzzlehttp
   │  │  ├── promises
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── Create.php
   │  │  │  │  ├── Is.php
   │  │  │  │  ├── FulfilledPromise.php
   │  │  │  │  ├── EachPromise.php
   │  │  │  │  ├── RejectionException.php
   │  │  │  │  ├── AggregateException.php
   │  │  │  │  ├── Promise.php
   │  │  │  │  ├── Coroutine.php
   │  │  │  │  ├── PromisorInterface.php
   │  │  │  │  ├── CancellationException.php
   │  │  │  │  ├── TaskQueueInterface.php
   │  │  │  │  ├── Utils.php
   │  │  │  │  ├── Each.php
   │  │  │  │  ├── PromiseInterface.php
   │  │  │  │  ├── TaskQueue.php
   │  │  │  │  └── RejectedPromise.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  └── LICENSE
   │  │  ├── uri-template
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  └── UriTemplate.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  └── LICENSE
   │  │  ├── guzzle
   │  │  │  ├── UPGRADING.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── functions_include.php
   │  │  │  │  ├── MessageFormatter.php
   │  │  │  │  ├── Middleware.php
   │  │  │  │  ├── TransferStats.php
   │  │  │  │  ├── RetryMiddleware.php
   │  │  │  │  ├── ClientInterface.php
   │  │  │  │  ├── RequestOptions.php
   │  │  │  │  ├── Pool.php
   │  │  │  │  ├── MessageFormatterInterface.php
   │  │  │  │  ├── PrepareBodyMiddleware.php
   │  │  │  │  ├── functions.php
   │  │  │  │  ├── Exception
   │  │  │  │  │  ├── TransferException.php
   │  │  │  │  │  ├── ServerException.php
   │  │  │  │  │  ├── GuzzleException.php
   │  │  │  │  │  ├── ClientException.php
   │  │  │  │  │  ├── BadResponseException.php
   │  │  │  │  │  ├── ConnectException.php
   │  │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  │  ├── TooManyRedirectsException.php
   │  │  │  │  │  └── RequestException.php
   │  │  │  │  ├── HandlerStack.php
   │  │  │  │  ├── Handler
   │  │  │  │  │  ├── CurlMultiHandler.php
   │  │  │  │  │  ├── CurlFactory.php
   │  │  │  │  │  ├── HeaderProcessor.php
   │  │  │  │  │  ├── MockHandler.php
   │  │  │  │  │  ├── CurlHandler.php
   │  │  │  │  │  ├── EasyHandle.php
   │  │  │  │  │  ├── CurlFactoryInterface.php
   │  │  │  │  │  ├── StreamHandler.php
   │  │  │  │  │  └── Proxy.php
   │  │  │  │  ├── Cookie
   │  │  │  │  │  ├── SetCookie.php
   │  │  │  │  │  ├── FileCookieJar.php
   │  │  │  │  │  ├── CookieJarInterface.php
   │  │  │  │  │  ├── CookieJar.php
   │  │  │  │  │  └── SessionCookieJar.php
   │  │  │  │  ├── ClientTrait.php
   │  │  │  │  ├── Client.php
   │  │  │  │  ├── RedirectMiddleware.php
   │  │  │  │  ├── BodySummarizerInterface.php
   │  │  │  │  ├── Utils.php
   │  │  │  │  └── BodySummarizer.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  ├── LICENSE
   │  │  │  └── package-lock.json
   │  │  └── psr7
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── ServerRequest.php
   │  │     │  ├── Stream.php
   │  │     │  ├── StreamWrapper.php
   │  │     │  ├── HttpFactory.php
   │  │     │  ├── Rfc7230.php
   │  │     │  ├── UriNormalizer.php
   │  │     │  ├── Request.php
   │  │     │  ├── Response.php
   │  │     │  ├── UriComparator.php
   │  │     │  ├── NoSeekStream.php
   │  │     │  ├── Message.php
   │  │     │  ├── FnStream.php
   │  │     │  ├── InflateStream.php
   │  │     │  ├── CachingStream.php
   │  │     │  ├── Uri.php
   │  │     │  ├── MultipartStream.php
   │  │     │  ├── UploadedFile.php
   │  │     │  ├── Header.php
   │  │     │  ├── DroppingStream.php
   │  │     │  ├── Query.php
   │  │     │  ├── Exception
   │  │     │  │  └── MalformedUriException.php
   │  │     │  ├── BufferStream.php
   │  │     │  ├── PumpStream.php
   │  │     │  ├── MimeType.php
   │  │     │  ├── LazyOpenStream.php
   │  │     │  ├── LimitStream.php
   │  │     │  ├── Utils.php
   │  │     │  ├── AppendStream.php
   │  │     │  ├── UriResolver.php
   │  │     │  ├── StreamDecoratorTrait.php
   │  │     │  └── MessageTrait.php
   │  │    ├── CHANGELOG.md
   │  │    └── LICENSE
   │  ├── theseer
   │  │  └── tokenizer
   │  │    ├── tools
   │  │     │  └── php-cs-fixer.d
   │  │     │    └── PhpdocSingleLineVarFixer.php
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── NamespaceUri.php
   │  │     │  ├── Token.php
   │  │     │  ├── Tokenizer.php
   │  │     │  ├── Exception.php
   │  │     │  ├── TokenCollection.php
   │  │     │  ├── NamespaceUriException.php
   │  │     │  ├── XMLSerializer.php
   │  │     │  └── TokenCollectionException.php
   │  │    ├── CHANGELOG.md
   │  │    ├── LICENSE
   │  │    └── composer.lock
   │  ├── filp
   │  │  └── whoops
   │  │    ├── LICENSE.md
   │  │    ├── composer.json
   │  │    ├── src
   │  │     │  └── Whoops
   │  │     │    ├── RunInterface.php
   │  │     │    ├── Resources
   │  │     │     │  ├── css
   │  │     │     │  │  ├── whoops.base.css
   │  │     │     │  │  └── prism.css
   │  │     │     │  ├── js
   │  │     │     │  │  ├── clipboard.min.js
   │  │     │     │  │  ├── whoops.base.js
   │  │     │     │  │  ├── prism.js
   │  │     │     │  │  └── zepto.min.js
   │  │     │     │  └── views
   │  │     │     │    ├── layout.html.php
   │  │     │     │    ├── frame_code.html.php
   │  │     │     │    ├── frames_container.html.php
   │  │     │     │    ├── header_outer.html.php
   │  │     │     │    ├── frames_description.html.php
   │  │     │     │    ├── panel_left_outer.html.php
   │  │     │     │    ├── panel_details.html.php
   │  │     │     │    ├── panel_left.html.php
   │  │     │     │    ├── env_details.html.php
   │  │     │     │    ├── panel_details_outer.html.php
   │  │     │     │    ├── frame_list.html.php
   │  │     │     │    └── header.html.php
   │  │     │    ├── Exception
   │  │     │     │  ├── Inspector.php
   │  │     │     │  ├── ErrorException.php
   │  │     │     │  ├── FrameCollection.php
   │  │     │     │  ├── Formatter.php
   │  │     │     │  └── Frame.php
   │  │     │    ├── Util
   │  │     │     │  ├── HtmlDumperOutput.php
   │  │     │     │  ├── TemplateHelper.php
   │  │     │     │  ├── Misc.php
   │  │     │     │  └── SystemFacade.php
   │  │     │    ├── Handler
   │  │     │     │  ├── XmlResponseHandler.php
   │  │     │     │  ├── CallbackHandler.php
   │  │     │     │  ├── Handler.php
   │  │     │     │  ├── PlainTextHandler.php
   │  │     │     │  ├── HandlerInterface.php
   │  │     │     │  ├── PrettyPageHandler.php
   │  │     │     │  └── JsonResponseHandler.php
   │  │     │    ├── Run.php
   │  │     │    └── Inspector
   │  │     │       ├── InspectorFactory.php
   │  │     │       ├── InspectorFactoryInterface.php
   │  │     │       └── InspectorInterface.php
   │  │    ├── CHANGELOG.md
   │  │    └── SECURITY.md
   │  ├── phar-io
   │  │  ├── version
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── VersionConstraintParser.php
   │  │  │  │  ├── exceptions
   │  │  │  │  │  ├── NoPreReleaseSuffixException.php
   │  │  │  │  │  ├── InvalidVersionException.php
   │  │  │  │  │  ├── UnsupportedVersionConstraintException.php
   │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  ├── InvalidPreReleaseSuffixException.php
   │  │  │  │  │  └── NoBuildMetaDataException.php
   │  │  │  │  ├── VersionNumber.php
   │  │  │  │  ├── PreReleaseSuffix.php
   │  │  │  │  ├── BuildMetaData.php
   │  │  │  │  ├── constraints
   │  │  │  │  │  ├── AndVersionConstraintGroup.php
   │  │  │  │  │  ├── OrVersionConstraintGroup.php
   │  │  │  │  │  ├── AbstractVersionConstraint.php
   │  │  │  │  │  ├── GreaterThanOrEqualToVersionConstraint.php
   │  │  │  │  │  ├── SpecificMajorAndMinorVersionConstraint.php
   │  │  │  │  │  ├── SpecificMajorVersionConstraint.php
   │  │  │  │  │  ├── VersionConstraint.php
   │  │  │  │  │  ├── AnyVersionConstraint.php
   │  │  │  │  │  └── ExactVersionConstraint.php
   │  │  │  │  ├── Version.php
   │  │  │  │  └── VersionConstraintValue.php
   │  │  │  ├── CHANGELOG.md
   │  │  │  └── LICENSE
   │  │  └── manifest
   │  │    ├── tools
   │  │     │  └── php-cs-fixer.d
   │  │     │    ├── header.txt
   │  │     │    └── PhpdocSingleLineVarFixer.php
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── exceptions
   │  │     │  │  ├── ManifestDocumentMapperException.php
   │  │     │  │  ├── ManifestLoaderException.php
   │  │     │  │  ├── ManifestDocumentLoadingException.php
   │  │     │  │  ├── ElementCollectionException.php
   │  │     │  │  ├── NoEmailAddressException.php
   │  │     │  │  ├── Exception.php
   │  │     │  │  ├── InvalidEmailException.php
   │  │     │  │  ├── InvalidUrlException.php
   │  │     │  │  ├── ManifestDocumentException.php
   │  │     │  │  ├── ManifestElementException.php
   │  │     │  │  └── InvalidApplicationNameException.php
   │  │     │  ├── values
   │  │     │  │  ├── Extension.php
   │  │     │  │  ├── Application.php
   │  │     │  │  ├── Type.php
   │  │     │  │  ├── PhpVersionRequirement.php
   │  │     │  │  ├── AuthorCollection.php
   │  │     │  │  ├── RequirementCollection.php
   │  │     │  │  ├── Manifest.php
   │  │     │  │  ├── RequirementCollectionIterator.php
   │  │     │  │  ├── BundledComponentCollection.php
   │  │     │  │  ├── BundledComponent.php
   │  │     │  │  ├── PhpExtensionRequirement.php
   │  │     │  │  ├── Email.php
   │  │     │  │  ├── Author.php
   │  │     │  │  ├── AuthorCollectionIterator.php
   │  │     │  │  ├── License.php
   │  │     │  │  ├── Library.php
   │  │     │  │  ├── Url.php
   │  │     │  │  ├── Requirement.php
   │  │     │  │  ├── BundledComponentCollectionIterator.php
   │  │     │  │  ├── CopyrightInformation.php
   │  │     │  │  └── ApplicationName.php
   │  │     │  ├── xml
   │  │     │  │  ├── BundlesElement.php
   │  │     │  │  ├── ExtensionElement.php
   │  │     │  │  ├── ContainsElement.php
   │  │     │  │  ├── PhpElement.php
   │  │     │  │  ├── ExtElement.php
   │  │     │  │  ├── LicenseElement.php
   │  │     │  │  ├── ComponentElementCollection.php
   │  │     │  │  ├── AuthorElement.php
   │  │     │  │  ├── ElementCollection.php
   │  │     │  │  ├── ExtElementCollection.php
   │  │     │  │  ├── ComponentElement.php
   │  │     │  │  ├── RequiresElement.php
   │  │     │  │  ├── ManifestElement.php
   │  │     │  │  ├── ManifestDocument.php
   │  │     │  │  ├── CopyrightElement.php
   │  │     │  │  └── AuthorElementCollection.php
   │  │     │  ├── ManifestLoader.php
   │  │     │  ├── ManifestSerializer.php
   │  │     │  └── ManifestDocumentMapper.php
   │  │    ├── CHANGELOG.md
   │  │    ├── LICENSE
   │  │    ├── manifest.xsd
   │  │    └── composer.lock
   │  ├── phpunit
   │  │  ├── php-timer
   │  │  │  ├── ChangeLog.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── exceptions
   │  │  │  │  │  ├── TimeSinceStartOfRequestNotAvailableException.php
   │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  └── NoActiveTimerException.php
   │  │  │  │  ├── ResourceUsageFormatter.php
   │  │  │  │  ├── Duration.php
   │  │  │  │  └── Timer.php
   │  │  │  ├── LICENSE
   │  │  │  └── SECURITY.md
   │  │  ├── php-text-template
   │  │  │  ├── ChangeLog.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── exceptions
   │  │  │  │  │  ├── RuntimeException.php
   │  │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  │  └── Exception.php
   │  │  │  │  └── Template.php
   │  │  │  ├── LICENSE
   │  │  │  └── SECURITY.md
   │  │  ├── php-code-coverage
   │  │  │  ├── ChangeLog-12.5.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── TestStatus
   │  │  │  │  │  ├── Known.php
   │  │  │  │  │  ├── Unknown.php
   │  │  │  │  │  ├── Success.php
   │  │  │  │  │  ├── Failure.php
   │  │  │  │  │  └── TestStatus.php
   │  │  │  │  ├── TestSize
   │  │  │  │  │  ├── Known.php
   │  │  │  │  │  ├── Unknown.php
   │  │  │  │  │  ├── Small.php
   │  │  │  │  │  ├── Medium.php
   │  │  │  │  │  ├── TestSize.php
   │  │  │  │  │  └── Large.php
   │  │  │  │  ├── Filter.php
   │  │  │  │  ├── Target
   │  │  │  │  │  ├── Target.php
   │  │  │  │  │  ├── TargetCollectionValidator.php
   │  │  │  │  │  ├── ClassesThatExtendClass.php
   │  │  │  │  │  ├── MapBuilder.php
   │  │  │  │  │  ├── Class_.php
   │  │  │  │  │  ├── ValidationSuccess.php
   │  │  │  │  │  ├── TargetCollectionIterator.php
   │  │  │  │  │  ├── Namespace_.php
   │  │  │  │  │  ├── ValidationResult.php
   │  │  │  │  │  ├── TargetCollection.php
   │  │  │  │  │  ├── ValidationFailure.php
   │  │  │  │  │  ├── Method.php
   │  │  │  │  │  ├── Function_.php
   │  │  │  │  │  ├── Trait_.php
   │  │  │  │  │  ├── ClassesThatImplementInterface.php
   │  │  │  │  │  └── Mapper.php
   │  │  │  │  ├── Exception
   │  │  │  │  │  ├── NoCodeCoverageDriverAvailableException.php
   │  │  │  │  │  ├── InvalidCodeCoverageTargetException.php
   │  │  │  │  │  ├── DirectoryCouldNotBeCreatedException.php
   │  │  │  │  │  ├── NoCodeCoverageDriverWithPathCoverageSupportAvailableException.php
   │  │  │  │  │  ├── PcovNotAvailableException.php
   │  │  │  │  │  ├── TestIdMissingException.php
   │  │  │  │  │  ├── PathExistsButIsNotDirectoryException.php
   │  │  │  │  │  ├── WriteOperationFailedException.php
   │  │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  │  ├── XdebugNotAvailableException.php
   │  │  │  │  │  ├── XdebugNotEnabledException.php
   │  │  │  │  │  ├── UnintentionallyCoveredCodeException.php
   │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  ├── ReflectionException.php
   │  │  │  │  │  ├── XdebugVersionNotSupportedException.php
   │  │  │  │  │  ├── BranchAndPathCoverageNotSupportedException.php
   │  │  │  │  │  ├── ParserException.php
   │  │  │  │  │  ├── ReportAlreadyFinalizedException.php
   │  │  │  │  │  ├── XmlException.php
   │  │  │  │  │  ├── StaticAnalysisCacheNotConfiguredException.php
   │  │  │  │  │  └── FileCouldNotBeWrittenException.php
   │  │  │  │  ├── Util
   │  │  │  │  │  ├── Xml.php
   │  │  │  │  │  ├── Percentage.php
   │  │  │  │  │  └── Filesystem.php
   │  │  │  │  ├── Version.php
   │  │  │  │  ├── Driver
   │  │  │  │  │  ├── PcovDriver.php
   │  │  │  │  │  ├── Driver.php
   │  │  │  │  │  ├── XdebugDriver.php
   │  │  │  │  │  └── Selector.php
   │  │  │  │  ├── Report
   │  │  │  │  │  ├── Html
   │  │  │  │  │  │  ├── Renderer.php
   │  │  │  │  │  │  ├── Colors.php
   │  │  │  │  │  │  ├── Facade.php
   │  │  │  │  │  │  ├── Renderer
   │  │  │  │  │  │  │  ├── File.php
   │  │  │  │  │  │  │  ├── Dashboard.php
   │  │  │  │  │  │  │  ├── Template
   │  │  │  │  │  │  │  │  ├── file_branch.html.dist
   │  │  │  │  │  │  │  │  ├── css
   │  │  │  │  │  │  │  │  │  ├── billboard.min.css
   │  │  │  │  │  │  │  │  │  ├── custom.css
   │  │  │  │  │  │  │  │  │  ├── bootstrap.min.css
   │  │  │  │  │  │  │  │  │  ├── style.css
   │  │  │  │  │  │  │  │  │  └── octicons.css
   │  │  │  │  │  │  │  │  ├── directory.html.dist
   │  │  │  │  │  │  │  │  ├── coverage_bar.html.dist
   │  │  │  │  │  │  │  │  ├── icons
   │  │  │  │  │  │  │  │  │  ├── file-code.svg
   │  │  │  │  │  │  │  │  │  └── file-directory.svg
   │  │  │  │  │  │  │  │  ├── paths.html.dist
   │  │  │  │  │  │  │  │  ├── line.html.dist
   │  │  │  │  │  │  │  │  ├── dashboard.html.dist
   │  │  │  │  │  │  │  │  ├── file.html.dist
   │  │  │  │  │  │  │  │  ├── directory_item.html.dist
   │  │  │  │  │  │  │  │  ├── lines.html.dist
   │  │  │  │  │  │  │  │  ├── method_item_branch.html.dist
   │  │  │  │  │  │  │  │  ├── method_item.html.dist
   │  │  │  │  │  │  │  │  ├── directory_item_branch.html.dist
   │  │  │  │  │  │  │  │  ├── dashboard_branch.html.dist
   │  │  │  │  │  │  │  │  ├── directory_branch.html.dist
   │  │  │  │  │  │  │  │  ├── branches.html.dist
   │  │  │  │  │  │  │  │  ├── js
   │  │  │  │  │  │  │  │  │  ├── jquery.min.js
   │  │  │  │  │  │  │  │  │  ├── file.js
   │  │  │  │  │  │  │  │  │  ├── billboard.pkgd.min.js
   │  │  │  │  │  │  │  │  │  └── bootstrap.bundle.min.js
   │  │  │  │  │  │  │  │  ├── coverage_bar_branch.html.dist
   │  │  │  │  │  │  │  │  ├── file_item.html.dist
   │  │  │  │  │  │  │  │  └── file_item_branch.html.dist
   │  │  │  │  │  │  │  └── Directory.php
   │  │  │  │  │  │  └── CustomCssFile.php
   │  │  │  │  │  ├── Xml
   │  │  │  │  │  │  ├── BuildInformation.php
   │  │  │  │  │  │  ├── File.php
   │  │  │  │  │  │  ├── Node.php
   │  │  │  │  │  │  ├── Report.php
   │  │  │  │  │  │  ├── Directory.php
   │  │  │  │  │  │  ├── Facade.php
   │  │  │  │  │  │  ├── Source.php
   │  │  │  │  │  │  ├── Totals.php
   │  │  │  │  │  │  ├── Tests.php
   │  │  │  │  │  │  ├── Coverage.php
   │  │  │  │  │  │  ├── Unit.php
   │  │  │  │  │  │  ├── Method.php
   │  │  │  │  │  │  └── Project.php
   │  │  │  │  │  ├── Crap4j.php
   │  │  │  │  │  ├── Text.php
   │  │  │  │  │  ├── Clover.php
   │  │  │  │  │  ├── Thresholds.php
   │  │  │  │  │  ├── OpenClover.php
   │  │  │  │  │  ├── Cobertura.php
   │  │  │  │  │  └── PHP.php
   │  │  │  │  ├── Node
   │  │  │  │  │  ├── CrapIndex.php
   │  │  │  │  │  ├── File.php
   │  │  │  │  │  ├── AbstractNode.php
   │  │  │  │  │  ├── Directory.php
   │  │  │  │  │  ├── Builder.php
   │  │  │  │  │  └── Iterator.php
   │  │  │  │  ├── CodeCoverage.php
   │  │  │  │  ├── Data
   │  │  │  │  │  ├── ProcessedPathCoverageData.php
   │  │  │  │  │  ├── ProcessedFunctionCoverageData.php
   │  │  │  │  │  ├── ProcessedCodeCoverageData.php
   │  │  │  │  │  ├── RawCodeCoverageData.php
   │  │  │  │  │  ├── ProcessedClassType.php
   │  │  │  │  │  ├── ProcessedFunctionType.php
   │  │  │  │  │  ├── ProcessedBranchCoverageData.php
   │  │  │  │  │  ├── ProcessedMethodType.php
   │  │  │  │  │  └── ProcessedTraitType.php
   │  │  │  │  └── StaticAnalysis
   │  │  │  │    ├── Value
   │  │  │  │     │  ├── Interface_.php
   │  │  │  │     │  ├── Visibility.php
   │  │  │  │     │  ├── Class_.php
   │  │  │  │     │  ├── LinesOfCode.php
   │  │  │  │     │  ├── Method.php
   │  │  │  │     │  ├── Function_.php
   │  │  │  │     │  ├── Trait_.php
   │  │  │  │     │  └── AnalysisResult.php
   │  │  │  │    ├── FileAnalyser.php
   │  │  │  │    ├── CachingSourceAnalyser.php
   │  │  │  │    ├── ParsingSourceAnalyser.php
   │  │  │  │    ├── Visitor
   │  │  │  │     │  ├── ExecutableLinesFindingVisitor.php
   │  │  │  │     │  ├── AttributeParentConnectingVisitor.php
   │  │  │  │     │  ├── IgnoredLinesFindingVisitor.php
   │  │  │  │     │  └── CodeUnitFindingVisitor.php
   │  │  │  │    ├── CacheWarmer.php
   │  │  │  │    └── SourceAnalyser.php
   │  │  │  ├── LICENSE
   │  │  │  └── SECURITY.md
   │  │  ├── phpunit
   │  │  │  ├── ChangeLog-12.5.md
   │  │  │  ├── DEPRECATIONS.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── schema
   │  │  │  │  ├── 11.3.xsd
   │  │  │  │  ├── 9.0.xsd
   │  │  │  │  ├── 8.5.xsd
   │  │  │  │  ├── 12.3.xsd
   │  │  │  │  ├── 11.1.xsd
   │  │  │  │  ├── 10.3.xsd
   │  │  │  │  ├── 10.1.xsd
   │  │  │  │  ├── 10.4.xsd
   │  │  │  │  ├── 9.6.xsd
   │  │  │  │  ├── 11.0.xsd
   │  │  │  │  ├── 9.1.xsd
   │  │  │  │  ├── 12.4.xsd
   │  │  │  │  ├── 10.0.xsd
   │  │  │  │  ├── 9.5.xsd
   │  │  │  │  ├── 12.1.xsd
   │  │  │  │  ├── 10.2.xsd
   │  │  │  │  ├── 11.4.xsd
   │  │  │  │  ├── 9.2.xsd
   │  │  │  │  ├── 12.2.xsd
   │  │  │  │  ├── 12.0.xsd
   │  │  │  │  ├── 9.3.xsd
   │  │  │  │  ├── 9.4.xsd
   │  │  │  │  ├── 10.5.xsd
   │  │  │  │  ├── 11.5.xsd
   │  │  │  │  └── 11.2.xsd
   │  │  │  ├── src
   │  │  │  │  ├── Logging
   │  │  │  │  │  ├── JUnit
   │  │  │  │  │  │  ├── JunitXmlLogger.php
   │  │  │  │  │  │  └── Subscriber
   │  │  │  │  │  │    ├── TestSuiteStartedSubscriber.php
   │  │  │  │  │  │    ├── TestMarkedIncompleteSubscriber.php
   │  │  │  │  │  │    ├── TestSuiteSkippedSubscriber.php
   │  │  │  │  │  │    ├── TestFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestPreparationFailedSubscriber.php
   │  │  │  │  │  │    ├── TestErroredSubscriber.php
   │  │  │  │  │  │    ├── TestPrintedUnexpectedOutputSubscriber.php
   │  │  │  │  │  │    ├── TestSuiteFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestPreparedSubscriber.php
   │  │  │  │  │  │    ├── TestRunnerExecutionFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestFailedSubscriber.php
   │  │  │  │  │  │    ├── TestPreparationStartedSubscriber.php
   │  │  │  │  │  │    ├── TestPreparationErroredSubscriber.php
   │  │  │  │  │  │    ├── Subscriber.php
   │  │  │  │  │  │    └── TestSkippedSubscriber.php
   │  │  │  │  │  ├── TestDox
   │  │  │  │  │  │  ├── HtmlRenderer.php
   │  │  │  │  │  │  ├── PlainTextRenderer.php
   │  │  │  │  │  │  ├── NamePrettifier.php
   │  │  │  │  │  │  └── TestResult
   │  │  │  │  │  │    ├── TestResult.php
   │  │  │  │  │  │    ├── TestResultCollection.php
   │  │  │  │  │  │    ├── TestResultCollector.php
   │  │  │  │  │  │    ├── TestResultCollectionIterator.php
   │  │  │  │  │  │    └── Subscriber
   │  │  │  │  │  │       ├── TestMarkedIncompleteSubscriber.php
   │  │  │  │  │  │       ├── TestTriggeredPhpunitWarningSubscriber.php
   │  │  │  │  │  │       ├── TestFinishedSubscriber.php
   │  │  │  │  │  │       ├── TestTriggeredPhpNoticeSubscriber.php
   │  │  │  │  │  │       ├── TestTriggeredPhpDeprecationSubscriber.php
   │  │  │  │  │  │       ├── TestErroredSubscriber.php
   │  │  │  │  │  │       ├── TestTriggeredWarningSubscriber.php
   │  │  │  │  │  │       ├── TestTriggeredNoticeSubscriber.php
   │  │  │  │  │  │       ├── TestTriggeredPhpWarningSubscriber.php
   │  │  │  │  │  │       ├── TestConsideredRiskySubscriber.php
   │  │  │  │  │  │       ├── TestPreparedSubscriber.php
   │  │  │  │  │  │       ├── TestFailedSubscriber.php
   │  │  │  │  │  │       ├── TestTriggeredDeprecationSubscriber.php
   │  │  │  │  │  │       ├── TestTriggeredPhpunitErrorSubscriber.php
   │  │  │  │  │  │       ├── Subscriber.php
   │  │  │  │  │  │       ├── TestPassedSubscriber.php
   │  │  │  │  │  │       ├── TestSkippedSubscriber.php
   │  │  │  │  │  │       └── TestTriggeredPhpunitDeprecationSubscriber.php
   │  │  │  │  │  ├── TeamCity
   │  │  │  │  │  │  ├── TeamCityLogger.php
   │  │  │  │  │  │  └── Subscriber
   │  │  │  │  │  │    ├── TestSuiteStartedSubscriber.php
   │  │  │  │  │  │    ├── TestMarkedIncompleteSubscriber.php
   │  │  │  │  │  │    ├── TestSuiteSkippedSubscriber.php
   │  │  │  │  │  │    ├── TestFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestErroredSubscriber.php
   │  │  │  │  │  │    ├── TestSuiteFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestConsideredRiskySubscriber.php
   │  │  │  │  │  │    ├── TestPreparedSubscriber.php
   │  │  │  │  │  │    ├── TestRunnerExecutionFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestFailedSubscriber.php
   │  │  │  │  │  │    ├── TestSuiteBeforeFirstTestMethodErroredSubscriber.php
   │  │  │  │  │  │    ├── Subscriber.php
   │  │  │  │  │  │    └── TestSkippedSubscriber.php
   │  │  │  │  │  ├── EventLogger.php
   │  │  │  │  │  └── OpenTestReporting
   │  │  │  │  │    ├── Status.php
   │  │  │  │  │    ├── schema
   │  │  │  │  │     │  ├── events-0.2.0.xsd
   │  │  │  │  │     │  ├── git-0.2.0.xsd
   │  │  │  │  │     │  ├── phpunit.xsd
   │  │  │  │  │     │  ├── otr.xsd
   │  │  │  │  │     │  ├── php.xsd
   │  │  │  │  │     │  └── core-0.2.0.xsd
   │  │  │  │  │    ├── Exception
   │  │  │  │  │     │  ├── Exception.php
   │  │  │  │  │     │  └── CannotOpenUriForWritingException.php
   │  │  │  │  │    ├── OtrXmlLogger.php
   │  │  │  │  │    ├── InfrastructureInformationProvider.php
   │  │  │  │  │    └── Subscriber
   │  │  │  │  │       ├── BeforeFirstTestMethodErroredSubscriber.php
   │  │  │  │  │       ├── TestRunnerStartedSubscriber.php
   │  │  │  │  │       ├── TestSuiteStartedSubscriber.php
   │  │  │  │  │       ├── TestSuiteSkippedSubscriber.php
   │  │  │  │  │       ├── TestFinishedSubscriber.php
   │  │  │  │  │       ├── TestRunnerFinishedSubscriber.php
   │  │  │  │  │       ├── AfterLastTestMethodFailedSubscriber.php
   │  │  │  │  │       ├── TestPreparationFailedSubscriber.php
   │  │  │  │  │       ├── TestErroredSubscriber.php
   │  │  │  │  │       ├── TestSuiteFinishedSubscriber.php
   │  │  │  │  │       ├── TestAbortedSubscriber.php
   │  │  │  │  │       ├── TestPreparedSubscriber.php
   │  │  │  │  │       ├── BeforeFirstTestMethodFailedSubscriber.php
   │  │  │  │  │       ├── TestFailedSubscriber.php
   │  │  │  │  │       ├── TestPreparationErroredSubscriber.php
   │  │  │  │  │       ├── AfterLastTestMethodErroredSubscriber.php
   │  │  │  │  │       ├── Subscriber.php
   │  │  │  │  │       └── TestSkippedSubscriber.php
   │  │  │  │  ├── Util
   │  │  │  │  │  ├── GlobalState.php
   │  │  │  │  │  ├── Xml
   │  │  │  │  │  │  ├── Xml.php
   │  │  │  │  │  │  └── Loader.php
   │  │  │  │  │  ├── Reflection.php
   │  │  │  │  │  ├── ExcludeList.php
   │  │  │  │  │  ├── Json.php
   │  │  │  │  │  ├── Filter.php
   │  │  │  │  │  ├── PHP
   │  │  │  │  │  │  ├── Job.php
   │  │  │  │  │  │  ├── JobRunnerRegistry.php
   │  │  │  │  │  │  ├── JobRunner.php
   │  │  │  │  │  │  └── Result.php
   │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  ├── InvalidVersionOperatorException.php
   │  │  │  │  │  │  ├── InvalidJsonException.php
   │  │  │  │  │  │  ├── PhpProcessException.php
   │  │  │  │  │  │  ├── InvalidDirectoryException.php
   │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  └── XmlException.php
   │  │  │  │  │  ├── Http
   │  │  │  │  │  │  ├── PhpDownloader.php
   │  │  │  │  │  │  └── Downloader.php
   │  │  │  │  │  ├── GlobalStateResult.php
   │  │  │  │  │  ├── VersionComparisonOperator.php
   │  │  │  │  │  ├── Filesystem.php
   │  │  │  │  │  ├── ThrowableToStringMapper.php
   │  │  │  │  │  ├── Color.php
   │  │  │  │  │  ├── Exporter.php
   │  │  │  │  │  └── Test.php
   │  │  │  │  ├── Framework
   │  │  │  │  │  ├── TestStatus
   │  │  │  │  │  │  ├── Notice.php
   │  │  │  │  │  │  ├── Known.php
   │  │  │  │  │  │  ├── Deprecation.php
   │  │  │  │  │  │  ├── Unknown.php
   │  │  │  │  │  │  ├── Success.php
   │  │  │  │  │  │  ├── Risky.php
   │  │  │  │  │  │  ├── Failure.php
   │  │  │  │  │  │  ├── Error.php
   │  │  │  │  │  │  ├── Incomplete.php
   │  │  │  │  │  │  ├── Warning.php
   │  │  │  │  │  │  ├── TestStatus.php
   │  │  │  │  │  │  └── Skipped.php
   │  │  │  │  │  ├── Reorderable.php
   │  │  │  │  │  ├── Attributes
   │  │  │  │  │  │  ├── ExcludeStaticPropertyFromBackup.php
   │  │  │  │  │  │  ├── PostCondition.php
   │  │  │  │  │  │  ├── RequiresFunction.php
   │  │  │  │  │  │  ├── BeforeClass.php
   │  │  │  │  │  │  ├── DataProvider.php
   │  │  │  │  │  │  ├── IgnorePhpunitDeprecations.php
   │  │  │  │  │  │  ├── RequiresPhpExtension.php
   │  │  │  │  │  │  ├── DependsExternalUsingShallowClone.php
   │  │  │  │  │  │  ├── IgnoreDeprecations.php
   │  │  │  │  │  │  ├── TestDoxFormatterExternal.php
   │  │  │  │  │  │  ├── ExcludeGlobalVariableFromBackup.php
   │  │  │  │  │  │  ├── UsesClassesThatExtendClass.php
   │  │  │  │  │  │  ├── CoversMethod.php
   │  │  │  │  │  │  ├── RequiresEnvironmentVariable.php
   │  │  │  │  │  │  ├── DependsUsingShallowClone.php
   │  │  │  │  │  │  ├── RequiresOperatingSystem.php
   │  │  │  │  │  │  ├── UsesNamespace.php
   │  │  │  │  │  │  ├── IgnorePhpunitWarnings.php
   │  │  │  │  │  │  ├── UsesMethod.php
   │  │  │  │  │  │  ├── Small.php
   │  │  │  │  │  │  ├── BackupGlobals.php
   │  │  │  │  │  │  ├── WithoutErrorHandler.php
   │  │  │  │  │  │  ├── PreCondition.php
   │  │  │  │  │  │  ├── TestDox.php
   │  │  │  │  │  │  ├── TestWith.php
   │  │  │  │  │  │  ├── CoversClassesThatImplementInterface.php
   │  │  │  │  │  │  ├── DependsOnClassUsingShallowClone.php
   │  │  │  │  │  │  ├── PreserveGlobalState.php
   │  │  │  │  │  │  ├── CoversTrait.php
   │  │  │  │  │  │  ├── RunTestsInSeparateProcesses.php
   │  │  │  │  │  │  ├── Medium.php
   │  │  │  │  │  │  ├── DependsUsingDeepClone.php
   │  │  │  │  │  │  ├── UsesClassesThatImplementInterface.php
   │  │  │  │  │  │  ├── UsesTrait.php
   │  │  │  │  │  │  ├── UsesClass.php
   │  │  │  │  │  │  ├── Before.php
   │  │  │  │  │  │  ├── Ticket.php
   │  │  │  │  │  │  ├── DisableReturnValueGenerationForTestDoubles.php
   │  │  │  │  │  │  ├── CoversClass.php
   │  │  │  │  │  │  ├── DependsExternalUsingDeepClone.php
   │  │  │  │  │  │  ├── After.php
   │  │  │  │  │  │  ├── CoversNamespace.php
   │  │  │  │  │  │  ├── Group.php
   │  │  │  │  │  │  ├── RequiresMethod.php
   │  │  │  │  │  │  ├── RunInSeparateProcess.php
   │  │  │  │  │  │  ├── DataProviderExternal.php
   │  │  │  │  │  │  ├── WithEnvironmentVariable.php
   │  │  │  │  │  │  ├── AfterClass.php
   │  │  │  │  │  │  ├── AllowMockObjectsWithoutExpectations.php
   │  │  │  │  │  │  ├── CoversNothing.php
   │  │  │  │  │  │  ├── Large.php
   │  │  │  │  │  │  ├── TestWithJson.php
   │  │  │  │  │  │  ├── RunClassInSeparateProcess.php
   │  │  │  │  │  │  ├── DoesNotPerformAssertions.php
   │  │  │  │  │  │  ├── CoversClassesThatExtendClass.php
   │  │  │  │  │  │  ├── RequiresOperatingSystemFamily.php
   │  │  │  │  │  │  ├── DependsOnClass.php
   │  │  │  │  │  │  ├── DependsOnClassUsingDeepClone.php
   │  │  │  │  │  │  ├── TestDoxFormatter.php
   │  │  │  │  │  │  ├── RequiresPhpunit.php
   │  │  │  │  │  │  ├── CoversFunction.php
   │  │  │  │  │  │  ├── UsesFunction.php
   │  │  │  │  │  │  ├── Depends.php
   │  │  │  │  │  │  ├── RequiresPhp.php
   │  │  │  │  │  │  ├── BackupStaticProperties.php
   │  │  │  │  │  │  ├── DependsExternal.php
   │  │  │  │  │  │  ├── RequiresSetting.php
   │  │  │  │  │  │  ├── RequiresPhpunitExtension.php
   │  │  │  │  │  │  └── Test.php
   │  │  │  │  │  ├── TestRunner
   │  │  │  │  │  │  ├── ChildProcessResultProcessor.php
   │  │  │  │  │  │  ├── SeparateProcessTestRunner.php
   │  │  │  │  │  │  ├── TestRunner.php
   │  │  │  │  │  │  └── templates
   │  │  │  │  │  │    ├── method.tpl
   │  │  │  │  │  │    └── class.tpl
   │  │  │  │  │  ├── Assert.php
   │  │  │  │  │  ├── TestSize
   │  │  │  │  │  │  ├── Known.php
   │  │  │  │  │  │  ├── Unknown.php
   │  │  │  │  │  │  ├── Small.php
   │  │  │  │  │  │  ├── Medium.php
   │  │  │  │  │  │  ├── TestSize.php
   │  │  │  │  │  │  └── Large.php
   │  │  │  │  │  ├── ExecutionOrderDependency.php
   │  │  │  │  │  ├── MockObject
   │  │  │  │  │  │  ├── TestStubBuilder.php
   │  │  │  │  │  │  ├── TestDoubleBuilder.php
   │  │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  │  ├── MatcherAlreadyRegisteredException.php
   │  │  │  │  │  │  │  ├── ReturnValueNotConfiguredException.php
   │  │  │  │  │  │  │  ├── MatchBuilderNotFoundException.php
   │  │  │  │  │  │  │  ├── RuntimeException.php
   │  │  │  │  │  │  │  ├── MethodNameAlreadyConfiguredException.php
   │  │  │  │  │  │  │  ├── IncompatibleReturnValueException.php
   │  │  │  │  │  │  │  ├── MethodCannotBeConfiguredException.php
   │  │  │  │  │  │  │  ├── NeverReturningMethodException.php
   │  │  │  │  │  │  │  ├── BadMethodCallException.php
   │  │  │  │  │  │  │  ├── NoMoreReturnValuesConfiguredException.php
   │  │  │  │  │  │  │  ├── MethodNameNotConfiguredException.php
   │  │  │  │  │  │  │  ├── MethodParametersAlreadyConfiguredException.php
   │  │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  │  └── CannotUseOnlyMethodsException.php
   │  │  │  │  │  │  ├── ConfigurableMethod.php
   │  │  │  │  │  │  ├── Runtime
   │  │  │  │  │  │  │  ├── Rule
   │  │  │  │  │  │  │  │  ├── InvokedCount.php
   │  │  │  │  │  │  │  │  ├── InvokedAtLeastOnce.php
   │  │  │  │  │  │  │  │  ├── AnyParameters.php
   │  │  │  │  │  │  │  │  ├── InvocationOrder.php
   │  │  │  │  │  │  │  │  ├── AnyInvokedCount.php
   │  │  │  │  │  │  │  │  ├── ParametersRule.php
   │  │  │  │  │  │  │  │  ├── MethodName.php
   │  │  │  │  │  │  │  │  ├── InvokedAtLeastCount.php
   │  │  │  │  │  │  │  │  ├── Parameters.php
   │  │  │  │  │  │  │  │  └── InvokedAtMostCount.php
   │  │  │  │  │  │  │  ├── InvocationStubberImplementation.php
   │  │  │  │  │  │  │  ├── MethodNameConstraint.php
   │  │  │  │  │  │  │  ├── InvocationHandler.php
   │  │  │  │  │  │  │  ├── Matcher.php
   │  │  │  │  │  │  │  ├── Invocation.php
   │  │  │  │  │  │  │  ├── Api
   │  │  │  │  │  │  │  │  ├── TestDoubleState.php
   │  │  │  │  │  │  │  │  ├── ProxiedCloneMethod.php
   │  │  │  │  │  │  │  │  ├── MockObjectApi.php
   │  │  │  │  │  │  │  │  ├── DoubledCloneMethod.php
   │  │  │  │  │  │  │  │  ├── StubApi.php
   │  │  │  │  │  │  │  │  └── Method.php
   │  │  │  │  │  │  │  ├── Interface
   │  │  │  │  │  │  │  │  ├── MockObjectInternal.php
   │  │  │  │  │  │  │  │  ├── StubInternal.php
   │  │  │  │  │  │  │  │  ├── Stub.php
   │  │  │  │  │  │  │  │  ├── InvocationStubber.php
   │  │  │  │  │  │  │  │  └── MockObject.php
   │  │  │  │  │  │  │  ├── ReturnValueGenerator.php
   │  │  │  │  │  │  │  ├── Stub
   │  │  │  │  │  │  │  │  ├── ReturnCallback.php
   │  │  │  │  │  │  │  │  ├── ReturnStub.php
   │  │  │  │  │  │  │  │  ├── Stub.php
   │  │  │  │  │  │  │  │  ├── ReturnArgument.php
   │  │  │  │  │  │  │  │  ├── ReturnReference.php
   │  │  │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  │  │  ├── ReturnSelf.php
   │  │  │  │  │  │  │  │  ├── ReturnValueMap.php
   │  │  │  │  │  │  │  │  └── ConsecutiveCalls.php
   │  │  │  │  │  │  │  └── PropertyHook
   │  │  │  │  │  │  │    ├── PropertyHook.php
   │  │  │  │  │  │  │    ├── PropertyGetHook.php
   │  │  │  │  │  │  │    └── PropertySetHook.php
   │  │  │  │  │  │  ├── MockBuilder.php
   │  │  │  │  │  │  └── Generator
   │  │  │  │  │  │    ├── DoubledClass.php
   │  │  │  │  │  │    ├── DoubledMethod.php
   │  │  │  │  │  │    ├── Generator.php
   │  │  │  │  │  │    ├── HookedProperty.php
   │  │  │  │  │  │    ├── Exception
   │  │  │  │  │  │     │  ├── ClassIsFinalException.php
   │  │  │  │  │  │     │  ├── UnknownInterfaceException.php
   │  │  │  │  │  │     │  ├── RuntimeException.php
   │  │  │  │  │  │     │  ├── InvalidMethodNameException.php
   │  │  │  │  │  │     │  ├── Exception.php
   │  │  │  │  │  │     │  ├── ClassIsEnumerationException.php
   │  │  │  │  │  │     │  ├── ReflectionException.php
   │  │  │  │  │  │     │  ├── DuplicateMethodException.php
   │  │  │  │  │  │     │  ├── MethodNamedMethodException.php
   │  │  │  │  │  │     │  ├── UnknownTypeException.php
   │  │  │  │  │  │     │  └── NameAlreadyInUseException.php
   │  │  │  │  │  │    ├── DoubledMethodSet.php
   │  │  │  │  │  │    ├── TemplateLoader.php
   │  │  │  │  │  │    ├── templates
   │  │  │  │  │  │     │  ├── intersection.tpl
   │  │  │  │  │  │     │  ├── deprecation.tpl
   │  │  │  │  │  │     │  ├── test_double_class.tpl
   │  │  │  │  │  │     │  ├── doubled_static_method.tpl
   │  │  │  │  │  │     │  └── doubled_method.tpl
   │  │  │  │  │  │    └── HookedPropertyGenerator.php
   │  │  │  │  │  ├── TestBuilder.php
   │  │  │  │  │  ├── DataProviderTestSuite.php
   │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  ├── EmptyStringException.php
   │  │  │  │  │  │  ├── ErrorLogNotWritableException.php
   │  │  │  │  │  │  ├── Incomplete
   │  │  │  │  │  │  │  ├── IncompleteTest.php
   │  │  │  │  │  │  │  └── IncompleteTestError.php
   │  │  │  │  │  │  ├── UnknownClassOrInterfaceException.php
   │  │  │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  │  │  ├── InvalidDependencyException.php
   │  │  │  │  │  │  ├── AssertionFailedError.php
   │  │  │  │  │  │  ├── InvalidDataProviderException.php
   │  │  │  │  │  │  ├── PhptAssertionFailedError.php
   │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  ├── NoChildTestSuiteException.php
   │  │  │  │  │  │  ├── UnknownNativeTypeException.php
   │  │  │  │  │  │  ├── GeneratorNotSupportedException.php
   │  │  │  │  │  │  ├── ObjectEquals
   │  │  │  │  │  │  │  ├── ComparisonMethodDoesNotAcceptParameterTypeException.php
   │  │  │  │  │  │  │  ├── ActualValueIsNotAnObjectException.php
   │  │  │  │  │  │  │  ├── ComparisonMethodDoesNotExistException.php
   │  │  │  │  │  │  │  ├── ComparisonMethodDoesNotDeclareBoolReturnTypeException.php
   │  │  │  │  │  │  │  ├── ComparisonMethodDoesNotDeclareExactlyOneParameterException.php
   │  │  │  │  │  │  │  └── ComparisonMethodDoesNotDeclareParameterTypeException.php
   │  │  │  │  │  │  ├── ProcessIsolationException.php
   │  │  │  │  │  │  ├── Skipped
   │  │  │  │  │  │  │  ├── SkippedWithMessageException.php
   │  │  │  │  │  │  │  ├── SkippedTestSuiteError.php
   │  │  │  │  │  │  │  └── SkippedTest.php
   │  │  │  │  │  │  └── ExpectationFailedException.php
   │  │  │  │  │  ├── Constraint
   │  │  │  │  │  │  ├── Cardinality
   │  │  │  │  │  │  │  ├── IsEmpty.php
   │  │  │  │  │  │  │  ├── GreaterThan.php
   │  │  │  │  │  │  │  ├── Count.php
   │  │  │  │  │  │  │  ├── SameSize.php
   │  │  │  │  │  │  │  └── LessThan.php
   │  │  │  │  │  │  ├── JsonMatches.php
   │  │  │  │  │  │  ├── Boolean
   │  │  │  │  │  │  │  ├── IsTrue.php
   │  │  │  │  │  │  │  └── IsFalse.php
   │  │  │  │  │  │  ├── Callback.php
   │  │  │  │  │  │  ├── Type
   │  │  │  │  │  │  │  ├── IsType.php
   │  │  │  │  │  │  │  ├── IsInstanceOf.php
   │  │  │  │  │  │  │  └── IsNull.php
   │  │  │  │  │  │  ├── IsIdentical.php
   │  │  │  │  │  │  ├── Constraint.php
   │  │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  │  ├── ExceptionMessageIsOrContains.php
   │  │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  │  ├── ExceptionCode.php
   │  │  │  │  │  │  │  └── ExceptionMessageMatchesRegularExpression.php
   │  │  │  │  │  │  ├── Filesystem
   │  │  │  │  │  │  │  ├── IsReadable.php
   │  │  │  │  │  │  │  ├── DirectoryExists.php
   │  │  │  │  │  │  │  ├── FileExists.php
   │  │  │  │  │  │  │  └── IsWritable.php
   │  │  │  │  │  │  ├── Traversable
   │  │  │  │  │  │  │  ├── IsList.php
   │  │  │  │  │  │  │  ├── TraversableContainsIdentical.php
   │  │  │  │  │  │  │  ├── TraversableContainsOnly.php
   │  │  │  │  │  │  │  ├── TraversableContains.php
   │  │  │  │  │  │  │  ├── ArrayHasKey.php
   │  │  │  │  │  │  │  └── TraversableContainsEqual.php
   │  │  │  │  │  │  ├── String
   │  │  │  │  │  │  │  ├── StringContains.php
   │  │  │  │  │  │  │  ├── IsJson.php
   │  │  │  │  │  │  │  ├── RegularExpression.php
   │  │  │  │  │  │  │  ├── StringEndsWith.php
   │  │  │  │  │  │  │  ├── StringStartsWith.php
   │  │  │  │  │  │  │  ├── StringMatchesFormatDescription.php
   │  │  │  │  │  │  │  └── StringEqualsStringIgnoringLineEndings.php
   │  │  │  │  │  │  ├── IsAnything.php
   │  │  │  │  │  │  ├── Equality
   │  │  │  │  │  │  │  ├── IsEqualCanonicalizing.php
   │  │  │  │  │  │  │  ├── IsEqualIgnoringCase.php
   │  │  │  │  │  │  │  ├── IsEqual.php
   │  │  │  │  │  │  │  └── IsEqualWithDelta.php
   │  │  │  │  │  │  ├── Object
   │  │  │  │  │  │  │  ├── ObjectEquals.php
   │  │  │  │  │  │  │  └── ObjectHasProperty.php
   │  │  │  │  │  │  ├── Operator
   │  │  │  │  │  │  │  ├── Operator.php
   │  │  │  │  │  │  │  ├── LogicalAnd.php
   │  │  │  │  │  │  │  ├── LogicalOr.php
   │  │  │  │  │  │  │  ├── BinaryOperator.php
   │  │  │  │  │  │  │  ├── LogicalNot.php
   │  │  │  │  │  │  │  ├── UnaryOperator.php
   │  │  │  │  │  │  │  └── LogicalXor.php
   │  │  │  │  │  │  └── Math
   │  │  │  │  │  │    ├── IsNan.php
   │  │  │  │  │  │    ├── IsFinite.php
   │  │  │  │  │  │    └── IsInfinite.php
   │  │  │  │  │  ├── TestSuiteIterator.php
   │  │  │  │  │  ├── SelfDescribing.php
   │  │  │  │  │  ├── TestCase.php
   │  │  │  │  │  ├── Assert
   │  │  │  │  │  │  └── Functions.php
   │  │  │  │  │  ├── TestSuite.php
   │  │  │  │  │  ├── NativeType.php
   │  │  │  │  │  └── Test.php
   │  │  │  │  ├── Exception.php
   │  │  │  │  ├── Metadata
   │  │  │  │  │  ├── ExcludeStaticPropertyFromBackup.php
   │  │  │  │  │  ├── PostCondition.php
   │  │  │  │  │  ├── MetadataCollection.php
   │  │  │  │  │  ├── RequiresFunction.php
   │  │  │  │  │  ├── BeforeClass.php
   │  │  │  │  │  ├── DataProvider.php
   │  │  │  │  │  ├── IgnorePhpunitDeprecations.php
   │  │  │  │  │  ├── RequiresPhpExtension.php
   │  │  │  │  │  ├── IgnoreDeprecations.php
   │  │  │  │  │  ├── ExcludeGlobalVariableFromBackup.php
   │  │  │  │  │  ├── UsesClassesThatExtendClass.php
   │  │  │  │  │  ├── CoversMethod.php
   │  │  │  │  │  ├── RequiresEnvironmentVariable.php
   │  │  │  │  │  ├── RequiresOperatingSystem.php
   │  │  │  │  │  ├── UsesNamespace.php
   │  │  │  │  │  ├── IgnorePhpunitWarnings.php
   │  │  │  │  │  ├── DependsOnMethod.php
   │  │  │  │  │  ├── UsesMethod.php
   │  │  │  │  │  ├── BackupGlobals.php
   │  │  │  │  │  ├── WithoutErrorHandler.php
   │  │  │  │  │  ├── PreCondition.php
   │  │  │  │  │  ├── TestDox.php
   │  │  │  │  │  ├── TestWith.php
   │  │  │  │  │  ├── CoversClassesThatImplementInterface.php
   │  │  │  │  │  ├── PreserveGlobalState.php
   │  │  │  │  │  ├── CoversTrait.php
   │  │  │  │  │  ├── RunTestsInSeparateProcesses.php
   │  │  │  │  │  ├── UsesClassesThatImplementInterface.php
   │  │  │  │  │  ├── Api
   │  │  │  │  │  │  ├── ProvidedData.php
   │  │  │  │  │  │  ├── Requirements.php
   │  │  │  │  │  │  ├── DataProvider.php
   │  │  │  │  │  │  ├── Dependencies.php
   │  │  │  │  │  │  ├── CodeCoverage.php
   │  │  │  │  │  │  ├── HookMethods.php
   │  │  │  │  │  │  └── Groups.php
   │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  ├── InvalidVersionRequirementException.php
   │  │  │  │  │  │  ├── InvalidAttributeException.php
   │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  └── NoVersionRequirementException.php
   │  │  │  │  │  ├── UsesTrait.php
   │  │  │  │  │  ├── UsesClass.php
   │  │  │  │  │  ├── Before.php
   │  │  │  │  │  ├── MetadataCollectionIterator.php
   │  │  │  │  │  ├── DisableReturnValueGenerationForTestDoubles.php
   │  │  │  │  │  ├── CoversClass.php
   │  │  │  │  │  ├── After.php
   │  │  │  │  │  ├── CoversNamespace.php
   │  │  │  │  │  ├── Group.php
   │  │  │  │  │  ├── RequiresMethod.php
   │  │  │  │  │  ├── RunInSeparateProcess.php
   │  │  │  │  │  ├── WithEnvironmentVariable.php
   │  │  │  │  │  ├── AfterClass.php
   │  │  │  │  │  ├── AllowMockObjectsWithoutExpectations.php
   │  │  │  │  │  ├── CoversNothing.php
   │  │  │  │  │  ├── RunClassInSeparateProcess.php
   │  │  │  │  │  ├── DoesNotPerformAssertions.php
   │  │  │  │  │  ├── CoversClassesThatExtendClass.php
   │  │  │  │  │  ├── RequiresOperatingSystemFamily.php
   │  │  │  │  │  ├── DependsOnClass.php
   │  │  │  │  │  ├── TestDoxFormatter.php
   │  │  │  │  │  ├── Version
   │  │  │  │  │  │  ├── ComparisonRequirement.php
   │  │  │  │  │  │  ├── ConstraintRequirement.php
   │  │  │  │  │  │  └── Requirement.php
   │  │  │  │  │  ├── RequiresPhpunit.php
   │  │  │  │  │  ├── Metadata.php
   │  │  │  │  │  ├── CoversFunction.php
   │  │  │  │  │  ├── UsesFunction.php
   │  │  │  │  │  ├── RequiresPhp.php
   │  │  │  │  │  ├── BackupStaticProperties.php
   │  │  │  │  │  ├── Parser
   │  │  │  │  │  │  ├── AttributeParser.php
   │  │  │  │  │  │  ├── Registry.php
   │  │  │  │  │  │  ├── CachingParser.php
   │  │  │  │  │  │  └── Parser.php
   │  │  │  │  │  ├── RequiresSetting.php
   │  │  │  │  │  ├── RequiresPhpunitExtension.php
   │  │  │  │  │  └── Test.php
   │  │  │  │  ├── Runner
   │  │  │  │  │  ├── Filter
   │  │  │  │  │  │  ├── ExcludeNameFilterIterator.php
   │  │  │  │  │  │  ├── NameFilterIterator.php
   │  │  │  │  │  │  ├── Factory.php
   │  │  │  │  │  │  ├── IncludeGroupFilterIterator.php
   │  │  │  │  │  │  ├── GroupFilterIterator.php
   │  │  │  │  │  │  ├── IncludeNameFilterIterator.php
   │  │  │  │  │  │  ├── ExcludeGroupFilterIterator.php
   │  │  │  │  │  │  └── TestIdFilterIterator.php
   │  │  │  │  │  ├── HookMethod
   │  │  │  │  │  │  ├── HookMethod.php
   │  │  │  │  │  │  └── HookMethodCollection.php
   │  │  │  │  │  ├── Baseline
   │  │  │  │  │  │  ├── Baseline.php
   │  │  │  │  │  │  ├── Reader.php
   │  │  │  │  │  │  ├── RelativePathCalculator.php
   │  │  │  │  │  │  ├── Generator.php
   │  │  │  │  │  │  ├── Writer.php
   │  │  │  │  │  │  ├── Issue.php
   │  │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  │  ├── CannotLoadBaselineException.php
   │  │  │  │  │  │  │  ├── FileDoesNotHaveLineException.php
   │  │  │  │  │  │  │  └── CannotWriteBaselineException.php
   │  │  │  │  │  │  └── Subscriber
   │  │  │  │  │  │    ├── TestTriggeredPhpNoticeSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredPhpDeprecationSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredWarningSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredNoticeSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredPhpWarningSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredDeprecationSubscriber.php
   │  │  │  │  │  │    └── Subscriber.php
   │  │  │  │  │  ├── GarbageCollection
   │  │  │  │  │  │  ├── GarbageCollectionHandler.php
   │  │  │  │  │  │  └── Subscriber
   │  │  │  │  │  │    ├── TestFinishedSubscriber.php
   │  │  │  │  │  │    ├── ExecutionFinishedSubscriber.php
   │  │  │  │  │  │    ├── ExecutionStartedSubscriber.php
   │  │  │  │  │  │    └── Subscriber.php
   │  │  │  │  │  ├── CodeCoverageInitializationStatus.php
   │  │  │  │  │  ├── TestSuiteSorter.php
   │  │  │  │  │  ├── ResultCache
   │  │  │  │  │  │  ├── NullResultCache.php
   │  │  │  │  │  │  ├── DefaultResultCache.php
   │  │  │  │  │  │  ├── ResultCacheHandler.php
   │  │  │  │  │  │  ├── ResultCache.php
   │  │  │  │  │  │  ├── ResultCacheId.php
   │  │  │  │  │  │  └── Subscriber
   │  │  │  │  │  │    ├── TestSuiteStartedSubscriber.php
   │  │  │  │  │  │    ├── TestMarkedIncompleteSubscriber.php
   │  │  │  │  │  │    ├── TestFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestErroredSubscriber.php
   │  │  │  │  │  │    ├── TestSuiteFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestConsideredRiskySubscriber.php
   │  │  │  │  │  │    ├── TestPreparedSubscriber.php
   │  │  │  │  │  │    ├── TestFailedSubscriber.php
   │  │  │  │  │  │    ├── Subscriber.php
   │  │  │  │  │  │    └── TestSkippedSubscriber.php
   │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  ├── InvalidOrderException.php
   │  │  │  │  │  │  ├── ClassIsAbstractException.php
   │  │  │  │  │  │  ├── ErrorException.php
   │  │  │  │  │  │  ├── FileDoesNotExistException.php
   │  │  │  │  │  │  ├── ClassCannotBeFoundException.php
   │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  ├── ClassDoesNotExtendTestCaseException.php
   │  │  │  │  │  │  ├── ParameterDoesNotExistException.php
   │  │  │  │  │  │  ├── CodeCoverageFileExistsException.php
   │  │  │  │  │  │  └── DirectoryDoesNotExistException.php
   │  │  │  │  │  ├── IssueFilter.php
   │  │  │  │  │  ├── BackedUpEnvironmentVariable.php
   │  │  │  │  │  ├── ShutdownHandler.php
   │  │  │  │  │  ├── Version.php
   │  │  │  │  │  ├── TestSuiteLoader.php
   │  │  │  │  │  ├── CodeCoverage.php
   │  │  │  │  │  ├── Extension
   │  │  │  │  │  │  ├── PharLoader.php
   │  │  │  │  │  │  ├── Extension.php
   │  │  │  │  │  │  ├── ExtensionBootstrapper.php
   │  │  │  │  │  │  ├── ParameterCollection.php
   │  │  │  │  │  │  └── Facade.php
   │  │  │  │  │  ├── Phpt
   │  │  │  │  │  │  ├── Renderer.php
   │  │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  │  ├── PhptExternalFileCannotBeLoadedException.php
   │  │  │  │  │  │  │  ├── InvalidPhptFileException.php
   │  │  │  │  │  │  │  └── UnsupportedPhptSectionException.php
   │  │  │  │  │  │  ├── Parser.php
   │  │  │  │  │  │  ├── TestCase.php
   │  │  │  │  │  │  └── templates
   │  │  │  │  │  │    └── phpt.tpl
   │  │  │  │  │  ├── DeprecationCollector
   │  │  │  │  │  │  ├── InIsolationCollector.php
   │  │  │  │  │  │  ├── Collector.php
   │  │  │  │  │  │  ├── Facade.php
   │  │  │  │  │  │  └── Subscriber
   │  │  │  │  │  │    ├── TestPreparedSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredDeprecationSubscriber.php
   │  │  │  │  │  │    └── Subscriber.php
   │  │  │  │  │  ├── TestResult
   │  │  │  │  │  │  ├── TestResult.php
   │  │  │  │  │  │  ├── Collector.php
   │  │  │  │  │  │  ├── Issue.php
   │  │  │  │  │  │  ├── Facade.php
   │  │  │  │  │  │  ├── PassedTests.php
   │  │  │  │  │  │  └── Subscriber
   │  │  │  │  │  │    ├── AfterTestClassMethodFailedSubscriber.php
   │  │  │  │  │  │    ├── TestRunnerTriggeredWarningSubscriber.php
   │  │  │  │  │  │    ├── TestSuiteStartedSubscriber.php
   │  │  │  │  │  │    ├── TestMarkedIncompleteSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredPhpunitNoticeSubscriber.php
   │  │  │  │  │  │    ├── TestSuiteSkippedSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredPhpunitWarningSubscriber.php
   │  │  │  │  │  │    ├── TestFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredErrorSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredPhpNoticeSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredPhpDeprecationSubscriber.php
   │  │  │  │  │  │    ├── TestErroredSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredWarningSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredNoticeSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredPhpWarningSubscriber.php
   │  │  │  │  │  │    ├── BeforeTestClassMethodErroredSubscriber.php
   │  │  │  │  │  │    ├── TestRunnerTriggeredDeprecationSubscriber.php
   │  │  │  │  │  │    ├── ExecutionStartedSubscriber.php
   │  │  │  │  │  │    ├── TestSuiteFinishedSubscriber.php
   │  │  │  │  │  │    ├── TestConsideredRiskySubscriber.php
   │  │  │  │  │  │    ├── ChildProcessErroredSubscriber.php
   │  │  │  │  │  │    ├── TestPreparedSubscriber.php
   │  │  │  │  │  │    ├── TestFailedSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredDeprecationSubscriber.php
   │  │  │  │  │  │    ├── TestTriggeredPhpunitErrorSubscriber.php
   │  │  │  │  │  │    ├── BeforeTestClassMethodFailedSubscriber.php
   │  │  │  │  │  │    ├── TestRunnerTriggeredNoticeSubscriber.php
   │  │  │  │  │  │    ├── AfterTestClassMethodErroredSubscriber.php
   │  │  │  │  │  │    ├── Subscriber.php
   │  │  │  │  │  │    ├── TestSkippedSubscriber.php
   │  │  │  │  │  │    └── TestTriggeredPhpunitDeprecationSubscriber.php
   │  │  │  │  │  └── ErrorHandler.php
   │  │  │  │  ├── TextUI
   │  │  │  │  │  ├── Output
   │  │  │  │  │  │  ├── SummaryPrinter.php
   │  │  │  │  │  │  ├── TestDox
   │  │  │  │  │  │  │  └── ResultPrinter.php
   │  │  │  │  │  │  ├── Printer
   │  │  │  │  │  │  │  ├── DefaultPrinter.php
   │  │  │  │  │  │  │  ├── NullPrinter.php
   │  │  │  │  │  │  │  └── Printer.php
   │  │  │  │  │  │  ├── Facade.php
   │  │  │  │  │  │  └── Default
   │  │  │  │  │  │    ├── UnexpectedOutputPrinter.php
   │  │  │  │  │  │    ├── ResultPrinter.php
   │  │  │  │  │  │    └── ProgressPrinter
   │  │  │  │  │  │       ├── ProgressPrinter.php
   │  │  │  │  │  │       └── Subscriber
   │  │  │  │  │  │          ├── TestRunnerExecutionStartedSubscriber.php
   │  │  │  │  │  │          ├── TestMarkedIncompleteSubscriber.php
   │  │  │  │  │  │          ├── TestTriggeredPhpunitNoticeSubscriber.php
   │  │  │  │  │  │          ├── TestSuiteSkippedSubscriber.php
   │  │  │  │  │  │          ├── TestTriggeredPhpunitWarningSubscriber.php
   │  │  │  │  │  │          ├── TestFinishedSubscriber.php
   │  │  │  │  │  │          ├── TestTriggeredErrorSubscriber.php
   │  │  │  │  │  │          ├── TestTriggeredPhpNoticeSubscriber.php
   │  │  │  │  │  │          ├── TestTriggeredPhpDeprecationSubscriber.php
   │  │  │  │  │  │          ├── TestErroredSubscriber.php
   │  │  │  │  │  │          ├── TestTriggeredWarningSubscriber.php
   │  │  │  │  │  │          ├── TestTriggeredNoticeSubscriber.php
   │  │  │  │  │  │          ├── TestTriggeredPhpWarningSubscriber.php
   │  │  │  │  │  │          ├── BeforeTestClassMethodErroredSubscriber.php
   │  │  │  │  │  │          ├── TestConsideredRiskySubscriber.php
   │  │  │  │  │  │          ├── ChildProcessErroredSubscriber.php
   │  │  │  │  │  │          ├── TestPreparedSubscriber.php
   │  │  │  │  │  │          ├── TestFailedSubscriber.php
   │  │  │  │  │  │          ├── TestTriggeredDeprecationSubscriber.php
   │  │  │  │  │  │          ├── Subscriber.php
   │  │  │  │  │  │          ├── TestSkippedSubscriber.php
   │  │  │  │  │  │          └── TestTriggeredPhpunitDeprecationSubscriber.php
   │  │  │  │  │  ├── Application.php
   │  │  │  │  │  ├── ShellExitCodeCalculator.php
   │  │  │  │  │  ├── Configuration
   │  │  │  │  │  │  ├── Xml
   │  │  │  │  │  │  │  ├── LoadedFromFileConfiguration.php
   │  │  │  │  │  │  │  ├── PHPUnit.php
   │  │  │  │  │  │  │  ├── SchemaDetector
   │  │  │  │  │  │  │  │  ├── FailedSchemaDetectionResult.php
   │  │  │  │  │  │  │  │  ├── SchemaDetector.php
   │  │  │  │  │  │  │  │  ├── SchemaDetectionResult.php
   │  │  │  │  │  │  │  │  └── SuccessfulSchemaDetectionResult.php
   │  │  │  │  │  │  │  ├── Generator.php
   │  │  │  │  │  │  │  ├── Logging
   │  │  │  │  │  │  │  │  ├── Logging.php
   │  │  │  │  │  │  │  │  ├── TestDox
   │  │  │  │  │  │  │  │  │  ├── Text.php
   │  │  │  │  │  │  │  │  │  └── Html.php
   │  │  │  │  │  │  │  │  ├── TeamCity.php
   │  │  │  │  │  │  │  │  ├── Junit.php
   │  │  │  │  │  │  │  │  └── Otr.php
   │  │  │  │  │  │  │  ├── Migration
   │  │  │  │  │  │  │  │  ├── SnapshotNodeList.php
   │  │  │  │  │  │  │  │  ├── MigrationBuilder.php
   │  │  │  │  │  │  │  │  ├── Migrations
   │  │  │  │  │  │  │  │  │  ├── MoveCoverageDirectoriesToSource.php
   │  │  │  │  │  │  │  │  │  ├── RemoveBeStrictAboutTodoAnnotatedTestsAttribute.php
   │  │  │  │  │  │  │  │  │  ├── CoverageTextToReport.php
   │  │  │  │  │  │  │  │  │  ├── RenameForceCoversAnnotationAttribute.php
   │  │  │  │  │  │  │  │  │  ├── CoverageCloverToReport.php
   │  │  │  │  │  │  │  │  │  ├── RemoveRegisterMockObjectsFromTestArgumentsRecursivelyAttribute.php
   │  │  │  │  │  │  │  │  │  ├── UpdateSchemaLocation.php
   │  │  │  │  │  │  │  │  │  ├── RenameBeStrictAboutCoversAnnotationAttribute.php
   │  │  │  │  │  │  │  │  │  ├── CoverageHtmlToReport.php
   │  │  │  │  │  │  │  │  │  ├── CoverageCrap4jToReport.php
   │  │  │  │  │  │  │  │  │  ├── RenameBackupStaticAttributesAttribute.php
   │  │  │  │  │  │  │  │  │  ├── LogToReportMigration.php
   │  │  │  │  │  │  │  │  │  ├── MoveAttributesFromFilterWhitelistToCoverage.php
   │  │  │  │  │  │  │  │  │  ├── RemoveBeStrictAboutResourceUsageDuringSmallTestsAttribute.php
   │  │  │  │  │  │  │  │  │  ├── RemoveTestSuiteLoaderAttributes.php
   │  │  │  │  │  │  │  │  │  ├── MoveAttributesFromRootToCoverage.php
   │  │  │  │  │  │  │  │  │  ├── RemoveEmptyFilter.php
   │  │  │  │  │  │  │  │  │  ├── RemoveTestDoxGroupsElement.php
   │  │  │  │  │  │  │  │  │  ├── MoveWhitelistExcludesToCoverage.php
   │  │  │  │  │  │  │  │  │  ├── Migration.php
   │  │  │  │  │  │  │  │  │  ├── IntroduceCacheDirectoryAttribute.php
   │  │  │  │  │  │  │  │  │  ├── ReplaceRestrictDeprecationsWithIgnoreDeprecations.php
   │  │  │  │  │  │  │  │  │  ├── RemoveLoggingElements.php
   │  │  │  │  │  │  │  │  │  ├── RemoveVerboseAttribute.php
   │  │  │  │  │  │  │  │  │  ├── RemoveCoverageElementCacheDirectoryAttribute.php
   │  │  │  │  │  │  │  │  │  ├── RemovePrinterAttributes.php
   │  │  │  │  │  │  │  │  │  ├── CoveragePhpToReport.php
   │  │  │  │  │  │  │  │  │  ├── IntroduceCoverageElement.php
   │  │  │  │  │  │  │  │  │  ├── RemoveCacheResultFileAttribute.php
   │  │  │  │  │  │  │  │  │  ├── RemoveConversionToExceptionsAttributes.php
   │  │  │  │  │  │  │  │  │  ├── RemoveLogTypes.php
   │  │  │  │  │  │  │  │  │  ├── RemoveCoverageElementProcessUncoveredFilesAttribute.php
   │  │  │  │  │  │  │  │  │  ├── CoverageXmlToReport.php
   │  │  │  │  │  │  │  │  │  ├── MoveWhitelistIncludesToCoverage.php
   │  │  │  │  │  │  │  │  │  ├── RemoveCacheTokensAttribute.php
   │  │  │  │  │  │  │  │  │  ├── RemoveListeners.php
   │  │  │  │  │  │  │  │  │  ├── ConvertLogTypes.php
   │  │  │  │  │  │  │  │  │  └── RemoveNoInteractionAttribute.php
   │  │  │  │  │  │  │  │  ├── Migrator.php
   │  │  │  │  │  │  │  │  └── MigrationException.php
   │  │  │  │  │  │  │  ├── CodeCoverage
   │  │  │  │  │  │  │  │  ├── Report
   │  │  │  │  │  │  │  │  │  ├── Crap4j.php
   │  │  │  │  │  │  │  │  │  ├── Text.php
   │  │  │  │  │  │  │  │  │  ├── Clover.php
   │  │  │  │  │  │  │  │  │  ├── Xml.php
   │  │  │  │  │  │  │  │  │  ├── Html.php
   │  │  │  │  │  │  │  │  │  ├── OpenClover.php
   │  │  │  │  │  │  │  │  │  ├── Cobertura.php
   │  │  │  │  │  │  │  │  │  └── Php.php
   │  │  │  │  │  │  │  │  └── CodeCoverage.php
   │  │  │  │  │  │  │  ├── Configuration.php
   │  │  │  │  │  │  │  ├── SchemaFinder.php
   │  │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  │  ├── Validator
   │  │  │  │  │  │  │  │  ├── Validator.php
   │  │  │  │  │  │  │  │  └── ValidationResult.php
   │  │  │  │  │  │  │  ├── Loader.php
   │  │  │  │  │  │  │  ├── DefaultConfiguration.php
   │  │  │  │  │  │  │  ├── Groups.php
   │  │  │  │  │  │  │  └── TestSuiteMapper.php
   │  │  │  │  │  │  ├── SourceFilter.php
   │  │  │  │  │  │  ├── Value
   │  │  │  │  │  │  │  ├── IniSetting.php
   │  │  │  │  │  │  │  ├── TestDirectoryCollectionIterator.php
   │  │  │  │  │  │  │  ├── File.php
   │  │  │  │  │  │  │  ├── ExtensionBootstrapCollection.php
   │  │  │  │  │  │  │  ├── VariableCollection.php
   │  │  │  │  │  │  │  ├── FileCollection.php
   │  │  │  │  │  │  │  ├── IniSettingCollectionIterator.php
   │  │  │  │  │  │  │  ├── ConstantCollectionIterator.php
   │  │  │  │  │  │  │  ├── TestFile.php
   │  │  │  │  │  │  │  ├── ExtensionBootstrap.php
   │  │  │  │  │  │  │  ├── TestFileCollectionIterator.php
   │  │  │  │  │  │  │  ├── Directory.php
   │  │  │  │  │  │  │  ├── TestSuiteCollection.php
   │  │  │  │  │  │  │  ├── GroupCollectionIterator.php
   │  │  │  │  │  │  │  ├── Variable.php
   │  │  │  │  │  │  │  ├── Source.php
   │  │  │  │  │  │  │  ├── GroupCollection.php
   │  │  │  │  │  │  │  ├── FileCollectionIterator.php
   │  │  │  │  │  │  │  ├── Group.php
   │  │  │  │  │  │  │  ├── ConstantCollection.php
   │  │  │  │  │  │  │  ├── TestSuiteCollectionIterator.php
   │  │  │  │  │  │  │  ├── Constant.php
   │  │  │  │  │  │  │  ├── IniSettingCollection.php
   │  │  │  │  │  │  │  ├── FilterDirectory.php
   │  │  │  │  │  │  │  ├── TestDirectoryCollection.php
   │  │  │  │  │  │  │  ├── FilterDirectoryCollection.php
   │  │  │  │  │  │  │  ├── TestFileCollection.php
   │  │  │  │  │  │  │  ├── Php.php
   │  │  │  │  │  │  │  ├── VariableCollectionIterator.php
   │  │  │  │  │  │  │  ├── TestSuite.php
   │  │  │  │  │  │  │  ├── DirectoryCollection.php
   │  │  │  │  │  │  │  ├── DirectoryCollectionIterator.php
   │  │  │  │  │  │  │  ├── FilterDirectoryCollectionIterator.php
   │  │  │  │  │  │  │  ├── TestDirectory.php
   │  │  │  │  │  │  │  └── ExtensionBootstrapCollectionIterator.php
   │  │  │  │  │  │  ├── PhpHandler.php
   │  │  │  │  │  │  ├── Merger.php
   │  │  │  │  │  │  ├── Registry.php
   │  │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  │  ├── CodeCoverageReportNotConfiguredException.php
   │  │  │  │  │  │  │  ├── NoBaselineException.php
   │  │  │  │  │  │  │  ├── NoDefaultTestSuiteException.php
   │  │  │  │  │  │  │  ├── BootstrapScriptDoesNotExistException.php
   │  │  │  │  │  │  │  ├── NoCustomCssFileException.php
   │  │  │  │  │  │  │  ├── NoCacheDirectoryException.php
   │  │  │  │  │  │  │  ├── BootstrapScriptException.php
   │  │  │  │  │  │  │  ├── LoggingNotConfiguredException.php
   │  │  │  │  │  │  │  ├── NoConfigurationFileException.php
   │  │  │  │  │  │  │  ├── NoCoverageCacheDirectoryException.php
   │  │  │  │  │  │  │  ├── ConfigurationCannotBeBuiltException.php
   │  │  │  │  │  │  │  ├── NoPharExtensionDirectoryException.php
   │  │  │  │  │  │  │  ├── FilterNotConfiguredException.php
   │  │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  │  ├── SpecificDeprecationToStopOnNotConfiguredException.php
   │  │  │  │  │  │  │  ├── NoBootstrapException.php
   │  │  │  │  │  │  │  └── CannotFindSchemaException.php
   │  │  │  │  │  │  ├── BootstrapLoader.php
   │  │  │  │  │  │  ├── Configuration.php
   │  │  │  │  │  │  ├── Builder.php
   │  │  │  │  │  │  ├── CodeCoverageFilterRegistry.php
   │  │  │  │  │  │  ├── SourceMapper.php
   │  │  │  │  │  │  ├── Cli
   │  │  │  │  │  │  │  ├── XmlConfigurationFileFinder.php
   │  │  │  │  │  │  │  ├── Configuration.php
   │  │  │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  │  │  └── Builder.php
   │  │  │  │  │  │  └── TestSuiteBuilder.php
   │  │  │  │  │  ├── Exception
   │  │  │  │  │  │  ├── TestDirectoryNotFoundException.php
   │  │  │  │  │  │  ├── RuntimeException.php
   │  │  │  │  │  │  ├── CannotOpenSocketException.php
   │  │  │  │  │  │  ├── TestFileNotFoundException.php
   │  │  │  │  │  │  ├── InvalidSocketException.php
   │  │  │  │  │  │  └── Exception.php
   │  │  │  │  │  ├── Help.php
   │  │  │  │  │  ├── Command
   │  │  │  │  │  │  ├── Commands
   │  │  │  │  │  │  │  ├── ListGroupsCommand.php
   │  │  │  │  │  │  │  ├── AtLeastVersionCommand.php
   │  │  │  │  │  │  │  ├── ShowVersionCommand.php
   │  │  │  │  │  │  │  ├── WarmCodeCoverageCacheCommand.php
   │  │  │  │  │  │  │  ├── MigrateConfigurationCommand.php
   │  │  │  │  │  │  │  ├── ListTestSuitesCommand.php
   │  │  │  │  │  │  │  ├── ShowHelpCommand.php
   │  │  │  │  │  │  │  ├── CheckPhpConfigurationCommand.php
   │  │  │  │  │  │  │  ├── ListTestFilesCommand.php
   │  │  │  │  │  │  │  ├── ListTestsAsTextCommand.php
   │  │  │  │  │  │  │  ├── VersionCheckCommand.php
   │  │  │  │  │  │  │  ├── GenerateConfigurationCommand.php
   │  │  │  │  │  │  │  └── ListTestsAsXmlCommand.php
   │  │  │  │  │  │  ├── Command.php
   │  │  │  │  │  │  └── Result.php
   │  │  │  │  │  ├── TestRunner.php
   │  │  │  │  │  └── TestSuiteFilterProcessor.php
   │  │  │  │  └── Event
   │  │  │  │    ├── Dispatcher
   │  │  │  │     │  ├── DeferringDispatcher.php
   │  │  │  │     │  ├── Dispatcher.php
   │  │  │  │     │  ├── SubscribableDispatcher.php
   │  │  │  │     │  ├── DirectDispatcher.php
   │  │  │  │     │  └── CollectingDispatcher.php
   │  │  │  │    ├── Value
   │  │  │  │     │  ├── Telemetry
   │  │  │  │     │  │  ├── System.php
   │  │  │  │     │  │  ├── MemoryMeter.php
   │  │  │  │     │  │  ├── SystemMemoryMeter.php
   │  │  │  │     │  │  ├── MemoryUsage.php
   │  │  │  │     │  │  ├── SystemGarbageCollectorStatusProvider.php
   │  │  │  │     │  │  ├── HRTime.php
   │  │  │  │     │  │  ├── SystemStopWatchWithOffset.php
   │  │  │  │     │  │  ├── Snapshot.php
   │  │  │  │     │  │  ├── Info.php
   │  │  │  │     │  │  ├── Duration.php
   │  │  │  │     │  │  ├── GarbageCollectorStatusProvider.php
   │  │  │  │     │  │  ├── SystemStopWatch.php
   │  │  │  │     │  │  ├── StopWatch.php
   │  │  │  │     │  │  └── GarbageCollectorStatus.php
   │  │  │  │     │  ├── ComparisonFailureBuilder.php
   │  │  │  │     │  ├── ComparisonFailure.php
   │  │  │  │     │  ├── TestSuite
   │  │  │  │     │  │  ├── TestSuiteWithName.php
   │  │  │  │     │  │  ├── TestSuiteForTestClass.php
   │  │  │  │     │  │  ├── TestSuiteForTestMethodWithDataProvider.php
   │  │  │  │     │  │  ├── TestSuite.php
   │  │  │  │     │  │  └── TestSuiteBuilder.php
   │  │  │  │     │  ├── Test
   │  │  │  │     │  │  ├── TestData
   │  │  │  │     │  │  │  ├── DataFromTestDependency.php
   │  │  │  │     │  │  │  ├── TestDataCollection.php
   │  │  │  │     │  │  │  ├── TestDataCollectionIterator.php
   │  │  │  │     │  │  │  ├── TestData.php
   │  │  │  │     │  │  │  └── DataFromDataProvider.php
   │  │  │  │     │  │  ├── TestDox.php
   │  │  │  │     │  │  ├── Phpt.php
   │  │  │  │     │  │  ├── TestCollectionIterator.php
   │  │  │  │     │  │  ├── Issue
   │  │  │  │     │  │  │  ├── Code.php
   │  │  │  │     │  │  │  └── IssueTrigger.php
   │  │  │  │     │  │  ├── TestMethod.php
   │  │  │  │     │  │  ├── TestDoxBuilder.php
   │  │  │  │     │  │  ├── TestMethodBuilder.php
   │  │  │  │     │  │  ├── TestCollection.php
   │  │  │  │     │  │  └── Test.php
   │  │  │  │     │  ├── ThrowableBuilder.php
   │  │  │  │     │  ├── ClassMethod.php
   │  │  │  │     │  ├── Runtime
   │  │  │  │     │  │  ├── PHPUnit.php
   │  │  │  │     │  │  ├── Runtime.php
   │  │  │  │     │  │  ├── PHP.php
   │  │  │  │     │  │  └── OperatingSystem.php
   │  │  │  │     │  └── Throwable.php
   │  │  │  │    ├── Events
   │  │  │  │     │  ├── TestRunner
   │  │  │  │     │  │  ├── GarbageCollectionEnabledSubscriber.php
   │  │  │  │     │  │  ├── ChildProcessErrored.php
   │  │  │  │     │  │  ├── StaticAnalysisForCodeCoverageFinishedSubscriber.php
   │  │  │  │     │  │  ├── DeprecationTriggered.php
   │  │  │  │     │  │  ├── GarbageCollectionDisabled.php
   │  │  │  │     │  │  ├── Configured.php
   │  │  │  │     │  │  ├── ExtensionLoadedFromPharSubscriber.php
   │  │  │  │     │  │  ├── EventFacadeSealedSubscriber.php
   │  │  │  │     │  │  ├── GarbageCollectionTriggered.php
   │  │  │  │     │  │  ├── ConfiguredSubscriber.php
   │  │  │  │     │  │  ├── WarningTriggeredSubscriber.php
   │  │  │  │     │  │  ├── ExecutionFinishedSubscriber.php
   │  │  │  │     │  │  ├── FinishedSubscriber.php
   │  │  │  │     │  │  ├── BootstrapFinishedSubscriber.php
   │  │  │  │     │  │  ├── GarbageCollectionDisabledSubscriber.php
   │  │  │  │     │  │  ├── Finished.php
   │  │  │  │     │  │  ├── NoticeTriggered.php
   │  │  │  │     │  │  ├── ChildProcessFinished.php
   │  │  │  │     │  │  ├── ExtensionBootstrappedSubscriber.php
   │  │  │  │     │  │  ├── ExecutionAborted.php
   │  │  │  │     │  │  ├── ExecutionStartedSubscriber.php
   │  │  │  │     │  │  ├── StaticAnalysisForCodeCoverageStartedSubscriber.php
   │  │  │  │     │  │  ├── StaticAnalysisForCodeCoverageStarted.php
   │  │  │  │     │  │  ├── NoticeTriggeredSubscriber.php
   │  │  │  │     │  │  ├── ExtensionLoadedFromPhar.php
   │  │  │  │     │  │  ├── Started.php
   │  │  │  │     │  │  ├── ExecutionAbortedSubscriber.php
   │  │  │  │     │  │  ├── ChildProcessErroredSubscriber.php
   │  │  │  │     │  │  ├── GarbageCollectionTriggeredSubscriber.php
   │  │  │  │     │  │  ├── StaticAnalysisForCodeCoverageFinished.php
   │  │  │  │     │  │  ├── BootstrapFinished.php
   │  │  │  │     │  │  ├── ExecutionStarted.php
   │  │  │  │     │  │  ├── WarningTriggered.php
   │  │  │  │     │  │  ├── EventFacadeSealed.php
   │  │  │  │     │  │  ├── StartedSubscriber.php
   │  │  │  │     │  │  ├── GarbageCollectionEnabled.php
   │  │  │  │     │  │  ├── ChildProcessStartedSubscriber.php
   │  │  │  │     │  │  ├── ChildProcessFinishedSubscriber.php
   │  │  │  │     │  │  ├── ExtensionBootstrapped.php
   │  │  │  │     │  │  ├── ExecutionFinished.php
   │  │  │  │     │  │  ├── ChildProcessStarted.php
   │  │  │  │     │  │  └── DeprecationTriggeredSubscriber.php
   │  │  │  │     │  ├── EventCollection.php
   │  │  │  │     │  ├── Application
   │  │  │  │     │  │  ├── FinishedSubscriber.php
   │  │  │  │     │  │  ├── Finished.php
   │  │  │  │     │  │  ├── Started.php
   │  │  │  │     │  │  └── StartedSubscriber.php
   │  │  │  │     │  ├── TestSuite
   │  │  │  │     │  │  ├── SortedSubscriber.php
   │  │  │  │     │  │  ├── FinishedSubscriber.php
   │  │  │  │     │  │  ├── FilteredSubscriber.php
   │  │  │  │     │  │  ├── Finished.php
   │  │  │  │     │  │  ├── Filtered.php
   │  │  │  │     │  │  ├── Started.php
   │  │  │  │     │  │  ├── Sorted.php
   │  │  │  │     │  │  ├── StartedSubscriber.php
   │  │  │  │     │  │  ├── LoadedSubscriber.php
   │  │  │  │     │  │  ├── Loaded.php
   │  │  │  │     │  │  ├── SkippedSubscriber.php
   │  │  │  │     │  │  └── Skipped.php
   │  │  │  │     │  ├── Test
   │  │  │  │     │  │  ├── AdditionalInformationProvided.php
   │  │  │  │     │  │  ├── HookMethod
   │  │  │  │     │  │  │  ├── BeforeFirstTestMethodErrored.php
   │  │  │  │     │  │  │  ├── BeforeTestMethodCalled.php
   │  │  │  │     │  │  │  ├── BeforeFirstTestMethodErroredSubscriber.php
   │  │  │  │     │  │  │  ├── AfterTestMethodErrored.php
   │  │  │  │     │  │  │  ├── PreConditionFinishedSubscriber.php
   │  │  │  │     │  │  │  ├── PostConditionFailedSubscriber.php
   │  │  │  │     │  │  │  ├── AfterLastTestMethodCalledSubscriber.php
   │  │  │  │     │  │  │  ├── PreConditionErroredSubscriber.php
   │  │  │  │     │  │  │  ├── BeforeFirstTestMethodFailed.php
   │  │  │  │     │  │  │  ├── BeforeTestMethodFailedSubscriber.php
   │  │  │  │     │  │  │  ├── BeforeFirstTestMethodFinishedSubscriber.php
   │  │  │  │     │  │  │  ├── PreConditionFailed.php
   │  │  │  │     │  │  │  ├── AfterLastTestMethodFailed.php
   │  │  │  │     │  │  │  ├── PostConditionFinishedSubscriber.php
   │  │  │  │     │  │  │  ├── AfterLastTestMethodFailedSubscriber.php
   │  │  │  │     │  │  │  ├── BeforeTestMethodErroredSubscriber.php
   │  │  │  │     │  │  │  ├── BeforeTestMethodCalledSubscriber.php
   │  │  │  │     │  │  │  ├── BeforeFirstTestMethodCalledSubscriber.php
   │  │  │  │     │  │  │  ├── AfterTestMethodFailedSubscriber.php
   │  │  │  │     │  │  │  ├── BeforeTestMethodFailed.php
   │  │  │  │     │  │  │  ├── AfterLastTestMethodFinished.php
   │  │  │  │     │  │  │  ├── BeforeTestMethodErrored.php
   │  │  │  │     │  │  │  ├── AfterTestMethodCalled.php
   │  │  │  │     │  │  │  ├── PostConditionFinished.php
   │  │  │  │     │  │  │  ├── AfterTestMethodErroredSubscriber.php
   │  │  │  │     │  │  │  ├── PreConditionErrored.php
   │  │  │  │     │  │  │  ├── PostConditionCalled.php
   │  │  │  │     │  │  │  ├── AfterTestMethodFailed.php
   │  │  │  │     │  │  │  ├── BeforeFirstTestMethodFinished.php
   │  │  │  │     │  │  │  ├── PreConditionFinished.php
   │  │  │  │     │  │  │  ├── BeforeFirstTestMethodCalled.php
   │  │  │  │     │  │  │  ├── PreConditionCalled.php
   │  │  │  │     │  │  │  ├── BeforeFirstTestMethodFailedSubscriber.php
   │  │  │  │     │  │  │  ├── AfterTestMethodFinished.php
   │  │  │  │     │  │  │  ├── AfterLastTestMethodFinishedSubscriber.php
   │  │  │  │     │  │  │  ├── PostConditionErrored.php
   │  │  │  │     │  │  │  ├── AfterTestMethodFinishedSubscriber.php
   │  │  │  │     │  │  │  ├── PostConditionCalledSubscriber.php
   │  │  │  │     │  │  │  ├── PostConditionErroredSubscriber.php
   │  │  │  │     │  │  │  ├── BeforeTestMethodFinishedSubscriber.php
   │  │  │  │     │  │  │  ├── PreConditionCalledSubscriber.php
   │  │  │  │     │  │  │  ├── PreConditionFailedSubscriber.php
   │  │  │  │     │  │  │  ├── AfterLastTestMethodErrored.php
   │  │  │  │     │  │  │  ├── BeforeTestMethodFinished.php
   │  │  │  │     │  │  │  ├── AfterLastTestMethodErroredSubscriber.php
   │  │  │  │     │  │  │  ├── PostConditionFailed.php
   │  │  │  │     │  │  │  ├── AfterLastTestMethodCalled.php
   │  │  │  │     │  │  │  └── AfterTestMethodCalledSubscriber.php
   │  │  │  │     │  │  ├── Outcome
   │  │  │  │     │  │  │  ├── PassedSubscriber.php
   │  │  │  │     │  │  │  ├── Passed.php
   │  │  │  │     │  │  │  ├── Errored.php
   │  │  │  │     │  │  │  ├── MarkedIncompleteSubscriber.php
   │  │  │  │     │  │  │  ├── MarkedIncomplete.php
   │  │  │  │     │  │  │  ├── ErroredSubscriber.php
   │  │  │  │     │  │  │  ├── Failed.php
   │  │  │  │     │  │  │  ├── FailedSubscriber.php
   │  │  │  │     │  │  │  ├── SkippedSubscriber.php
   │  │  │  │     │  │  │  └── Skipped.php
   │  │  │  │     │  │  ├── ComparatorRegistered.php
   │  │  │  │     │  │  ├── PrintedUnexpectedOutput.php
   │  │  │  │     │  │  ├── Lifecycle
   │  │  │  │     │  │  │  ├── PreparationFailed.php
   │  │  │  │     │  │  │  ├── DataProviderMethodCalled.php
   │  │  │  │     │  │  │  ├── FinishedSubscriber.php
   │  │  │  │     │  │  │  ├── DataProviderMethodFinished.php
   │  │  │  │     │  │  │  ├── Finished.php
   │  │  │  │     │  │  │  ├── DataProviderMethodFinishedSubscriber.php
   │  │  │  │     │  │  │  ├── PreparationStartedSubscriber.php
   │  │  │  │     │  │  │  ├── PreparationErroredSubscriber.php
   │  │  │  │     │  │  │  ├── Prepared.php
   │  │  │  │     │  │  │  ├── PreparationErrored.php
   │  │  │  │     │  │  │  ├── PreparationStarted.php
   │  │  │  │     │  │  │  ├── PreparationFailedSubscriber.php
   │  │  │  │     │  │  │  ├── PreparedSubscriber.php
   │  │  │  │     │  │  │  └── DataProviderMethodCalledSubscriber.php
   │  │  │  │     │  │  ├── TestDouble
   │  │  │  │     │  │  │  ├── MockObjectForIntersectionOfInterfacesCreated.php
   │  │  │  │     │  │  │  ├── TestStubForIntersectionOfInterfacesCreatedSubscriber.php
   │  │  │  │     │  │  │  ├── TestStubForIntersectionOfInterfacesCreated.php
   │  │  │  │     │  │  │  ├── MockObjectForIntersectionOfInterfacesCreatedSubscriber.php
   │  │  │  │     │  │  │  ├── PartialMockObjectCreatedSubscriber.php
   │  │  │  │     │  │  │  ├── MockObjectCreated.php
   │  │  │  │     │  │  │  ├── TestStubCreatedSubscriber.php
   │  │  │  │     │  │  │  ├── PartialMockObjectCreated.php
   │  │  │  │     │  │  │  ├── TestStubCreated.php
   │  │  │  │     │  │  │  └── MockObjectCreatedSubscriber.php
   │  │  │  │     │  │  ├── AdditionalInformationProvidedSubscriber.php
   │  │  │  │     │  │  ├── Issue
   │  │  │  │     │  │  │  ├── PhpunitDeprecationTriggered.php
   │  │  │  │     │  │  │  ├── DeprecationTriggered.php
   │  │  │  │     │  │  │  ├── WarningTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── ConsideredRisky.php
   │  │  │  │     │  │  │  ├── NoticeTriggered.php
   │  │  │  │     │  │  │  ├── ConsideredRiskySubscriber.php
   │  │  │  │     │  │  │  ├── PhpunitWarningTriggered.php
   │  │  │  │     │  │  │  ├── PhpWarningTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── PhpunitErrorTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── PhpunitNoticeTriggered.php
   │  │  │  │     │  │  │  ├── NoticeTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── PhpunitWarningTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── PhpDeprecationTriggered.php
   │  │  │  │     │  │  │  ├── PhpWarningTriggered.php
   │  │  │  │     │  │  │  ├── WarningTriggered.php
   │  │  │  │     │  │  │  ├── PhpDeprecationTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── PhpunitDeprecationTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── PhpNoticeTriggered.php
   │  │  │  │     │  │  │  ├── PhpunitNoticeTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── ErrorTriggered.php
   │  │  │  │     │  │  │  ├── ErrorTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── DeprecationTriggeredSubscriber.php
   │  │  │  │     │  │  │  ├── PhpNoticeTriggeredSubscriber.php
   │  │  │  │     │  │  │  └── PhpunitErrorTriggered.php
   │  │  │  │     │  │  ├── ComparatorRegisteredSubscriber.php
   │  │  │  │     │  │  └── PrintedUnexpectedOutputSubscriber.php
   │  │  │  │     │  ├── EventCollectionIterator.php
   │  │  │  │     │  └── Event.php
   │  │  │  │    ├── Exception
   │  │  │  │     │  ├── RuntimeException.php
   │  │  │  │     │  ├── UnknownSubscriberTypeException.php
   │  │  │  │     │  ├── UnknownEventTypeException.php
   │  │  │  │     │  ├── InvalidArgumentException.php
   │  │  │  │     │  ├── UnknownSubscriberException.php
   │  │  │  │     │  ├── SubscriberTypeAlreadyRegisteredException.php
   │  │  │  │     │  ├── UnknownEventException.php
   │  │  │  │     │  ├── EventFacadeIsSealedException.php
   │  │  │  │     │  ├── InvalidEventException.php
   │  │  │  │     │  ├── Exception.php
   │  │  │  │     │  ├── EventAlreadyAssignedException.php
   │  │  │  │     │  ├── MapError.php
   │  │  │  │     │  ├── InvalidSubscriberException.php
   │  │  │  │     │  ├── NoPreviousThrowableException.php
   │  │  │  │     │  ├── NoTestCaseObjectOnCallStackException.php
   │  │  │  │     │  ├── NoComparisonFailureException.php
   │  │  │  │     │  └── NoDataSetFromDataProviderException.php
   │  │  │  │    ├── Facade.php
   │  │  │  │    ├── Emitter
   │  │  │  │     │  ├── DispatchingEmitter.php
   │  │  │  │     │  └── Emitter.php
   │  │  │  │    ├── Tracer.php
   │  │  │  │    ├── TypeMap.php
   │  │  │  │    └── Subscriber.php
   │  │  │  ├── phpunit.xsd
   │  │  │  ├── LICENSE
   │  │  │  ├── SECURITY.md
   │  │  │  ├── composer.lock
   │  │  │  └── phpunit
   │  │  ├── php-invoker
   │  │  │  ├── ChangeLog.md
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── exceptions
   │  │  │  │  │  ├── Exception.php
   │  │  │  │  │  ├── TimeoutException.php
   │  │  │  │  │  └── ProcessControlExtensionNotLoadedException.php
   │  │  │  │  └── Invoker.php
   │  │  │  ├── LICENSE
   │  │  │  └── SECURITY.md
   │  │  └── php-file-iterator
   │  │    ├── ChangeLog.md
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── Facade.php
   │  │     │  ├── Factory.php
   │  │     │  ├── Iterator.php
   │  │     │  └── ExcludeIterator.php
   │  │    ├── LICENSE
   │  │    └── SECURITY.md
   │  ├── phpoption
   │  │  └── phpoption
   │  │    ├── composer.json
   │  │    ├── src
   │  │     │  └── PhpOption
   │  │     │    ├── LazyOption.php
   │  │     │    ├── None.php
   │  │     │    ├── Some.php
   │  │     │    └── Option.php
   │  │    └── LICENSE
   │  ├── ramsey
   │  │  ├── collection
   │  │  │  ├── composer.json
   │  │  │  ├── README.md
   │  │  │  ├── src
   │  │  │  │  ├── AbstractCollection.php
   │  │  │  │  ├── DoubleEndedQueue.php
   │  │  │  │  ├── Sort.php
   │  │  │  │  ├── CollectionInterface.php
   │  │  │  │  ├── Map
   │  │  │  │  │  ├── AbstractMap.php
   │  │  │  │  │  ├── NamedParameterMap.php
   │  │  │  │  │  ├── AssociativeArrayMap.php
   │  │  │  │  │  ├── MapInterface.php
   │  │  │  │  │  ├── TypedMapInterface.php
   │  │  │  │  │  ├── AbstractTypedMap.php
   │  │  │  │  │  └── TypedMap.php
   │  │  │  │  ├── Exception
   │  │  │  │  │  ├── CollectionException.php
   │  │  │  │  │  ├── NoSuchElementException.php
   │  │  │  │  │  ├── InvalidArgumentException.php
   │  │  │  │  │  ├── InvalidPropertyOrMethod.php
   │  │  │  │  │  ├── CollectionMismatchException.php
   │  │  │  │  │  ├── UnsupportedOperationException.php
   │  │  │  │  │  └── OutOfBoundsException.php
   │  │  │  │  ├── ArrayInterface.php
   │  │  │  │  ├── QueueInterface.php
   │  │  │  │  ├── Collection.php
   │  │  │  │  ├── AbstractSet.php
   │  │  │  │  ├── GenericArray.php
   │  │  │  │  ├── Set.php
   │  │  │  │  ├── Queue.php
   │  │  │  │  ├── AbstractArray.php
   │  │  │  │  ├── DoubleEndedQueueInterface.php
   │  │  │  │  └── Tool
   │  │  │  │    ├── ValueExtractorTrait.php
   │  │  │  │    ├── TypeTrait.php
   │  │  │  │    └── ValueToStringTrait.php
   │  │  │  ├── LICENSE
   │  │  │  └── SECURITY.md
   │  │  └── uuid
   │  │    ├── composer.json
   │  │    ├── README.md
   │  │    ├── src
   │  │     │  ├── Fields
   │  │     │  │  ├── SerializableFieldsTrait.php
   │  │     │  │  └── FieldsInterface.php
   │  │     │  ├── Codec
   │  │     │  │  ├── OrderedTimeCodec.php
   │  │     │  │  ├── StringCodec.php
   │  │     │  │  ├── TimestampLastCombCodec.php
   │  │     │  │  ├── TimestampFirstCombCodec.php
   │  │     │  │  ├── GuidStringCodec.php
   │  │     │  │  └── CodecInterface.php
   │  │     │  ├── FeatureSet.php
   │  │     │  ├── Provider
   │  │     │  │  ├── NodeProviderInterface.php
   │  │     │  │  ├── TimeProviderInterface.php
   │  │     │  │  ├── DceSecurityProviderInterface.php
   │  │     │  │  ├── Node
   │  │     │  │  │  ├── FallbackNodeProvider.php
   │  │     │  │  │  ├── RandomNodeProvider.php
   │  │     │  │  │  ├── SystemNodeProvider.php
   │  │     │  │  │  ├── NodeProviderCollection.php
   │  │     │  │  │  └── StaticNodeProvider.php
   │  │     │  │  ├── Time
   │  │     │  │  │  ├── SystemTimeProvider.php
   │  │     │  │  │  └── FixedTimeProvider.php
   │  │     │  │  └── Dce
   │  │     │  │    └── SystemDceSecurityProvider.php
   │  │     │  ├── UuidFactoryInterface.php
   │  │     │  ├── Type
   │  │     │  │  ├── TypeInterface.php
   │  │     │  │  ├── Integer.php
   │  │     │  │  ├── Time.php
   │  │     │  │  ├── NumberInterface.php
   │  │     │  │  ├── Hexadecimal.php
   │  │     │  │  └── Decimal.php
   │  │     │  ├── DegradedUuid.php
   │  │     │  ├── Rfc4122
   │  │     │  │  ├── Validator.php
   │  │     │  │  ├── MaxTrait.php
   │  │     │  │  ├── NilTrait.php
   │  │     │  │  ├── UuidV1.php
   │  │     │  │  ├── UuidV7.php
   │  │     │  │  ├── VariantTrait.php
   │  │     │  │  ├── Fields.php
   │  │     │  │  ├── UuidV3.php
   │  │     │  │  ├── NilUuid.php
   │  │     │  │  ├── MaxUuid.php
   │  │     │  │  ├── FieldsInterface.php
   │  │     │  │  ├── UuidV6.php
   │  │     │  │  ├── UuidV5.php
   │  │     │  │  ├── UuidInterface.php
   │  │     │  │  ├── VersionTrait.php
   │  │     │  │  ├── UuidV8.php
   │  │     │  │  ├── UuidV4.php
   │  │     │  │  ├── UuidBuilder.php
   │  │     │  │  ├── UuidV2.php
   │  │     │  │  └── TimeTrait.php
   │  │     │  ├── functions.php
   │  │     │  ├── DeprecatedUuidInterface.php
   │  │     │  ├── Exception
   │  │     │  │  ├── RandomSourceException.php
   │  │     │  │  ├── UnableToBuildUuidException.php
   │  │     │  │  ├── DceSecurityException.php
   │  │     │  │  ├── InvalidArgumentException.php
   │  │     │  │  ├── InvalidBytesException.php
   │  │     │  │  ├── NameException.php
   │  │     │  │  ├── TimeSourceException.php
   │  │     │  │  ├── UnsupportedOperationException.php
   │  │     │  │  ├── DateTimeException.php
   │  │     │  │  ├── NodeException.php
   │  │     │  │  ├── UuidExceptionInterface.php
   │  │     │  │  ├── BuilderNotFoundException.php
   │  │     │  │  └── InvalidUuidStringException.php
   │  │     │  ├── Guid
   │  │     │  │  ├── GuidBuilder.php
   │  │     │  │  ├── Fields.php
   │  │     │  │  └── Guid.php
   │  │     │  ├── Uuid.php
   │  │     │  ├── BinaryUtils.php
   │  │     │  ├── Validator
   │  │     │  │  ├── ValidatorInterface.php
   │  │     │  │  └── GenericValidator.php
   │  │     │  ├── UuidFactory.php
   │  │     │  ├── UuidInterface.php
   │  │     │  ├── Builder
   │  │     │  │  ├── FallbackBuilder.php
   │  │     │  │  ├── DefaultUuidBuilder.php
   │  │     │  │  ├── DegradedUuidBuilder.php
   │  │     │  │  ├── BuilderCollection.php
   │  │     │  │  └── UuidBuilderInterface.php
   │  │     │  ├── Lazy
   │  │     │  │  └── LazyUuidFromString.php
   │  │     │  ├── Math
   │  │     │  │  ├── CalculatorInterface.php
   │  │     │  │  ├── BrickMathCalculator.php
   │  │     │  │  └── RoundingMode.php
   │  │     │  ├── Converter
   │  │     │  │  ├── Number
   │  │     │  │  │  ├── BigNumberConverter.php
   │  │     │  │  │  ├── DegradedNumberConverter.php
   │  │     │  │  │  └── GenericNumberConverter.php
   │  │     │  │  ├── Time
   │  │     │  │  │  ├── DegradedTimeConverter.php
   │  │     │  │  │  ├── UnixTimeConverter.php
   │  │     │  │  │  ├── BigNumberTimeConverter.php
   │  │     │  │  │  ├── GenericTimeConverter.php
   │  │     │  │  │  └── PhpTimeConverter.php
   │  │     │  │  ├── NumberConverterInterface.php
   │  │     │  │  └── TimeConverterInterface.php
   │  │     │  ├── DeprecatedUuidMethodsTrait.php
   │  │     │  ├── Generator
   │  │     │  │  ├── PeclUuidTimeGenerator.php
   │  │     │  │  ├── PeclUuidNameGenerator.php
   │  │     │  │  ├── DceSecurityGeneratorInterface.php
   │  │     │  │  ├── DceSecurityGenerator.php
   │  │     │  │  ├── NameGeneratorFactory.php
   │  │     │  │  ├── UnixTimeGenerator.php
   │  │     │  │  ├── RandomBytesGenerator.php
   │  │     │  │  ├── CombGenerator.php
   │  │     │  │  ├── TimeGeneratorInterface.php
   │  │     │  │  ├── NameGeneratorInterface.php
   │  │     │  │  ├── RandomGeneratorFactory.php
   │  │     │  │  ├── DefaultNameGenerator.php
   │  │     │  │  ├── TimeGeneratorFactory.php
   │  │     │  │  ├── RandomLibAdapter.php
   │  │     │  │  ├── DefaultTimeGenerator.php
   │  │     │  │  ├── RandomGeneratorInterface.php
   │  │     │  │  └── PeclUuidRandomGenerator.php
   │  │     │  └── Nonstandard
   │  │     │    ├── Fields.php
   │  │     │    ├── Uuid.php
   │  │     │    ├── UuidV6.php
   │  │     │    └── UuidBuilder.php
   │  │    └── LICENSE
   │  └── sebastian
   │    ├── type
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  ├── type
   │     │  │  │  ├── NullType.php
   │     │  │  │  ├── StaticType.php
   │     │  │  │  ├── MixedType.php
   │     │  │  │  ├── Type.php
   │     │  │  │  ├── ObjectType.php
   │     │  │  │  ├── GenericObjectType.php
   │     │  │  │  ├── IntersectionType.php
   │     │  │  │  ├── FalseType.php
   │     │  │  │  ├── TrueType.php
   │     │  │  │  ├── SimpleType.php
   │     │  │  │  ├── IterableType.php
   │     │  │  │  ├── NeverType.php
   │     │  │  │  ├── UnknownType.php
   │     │  │  │  ├── UnionType.php
   │     │  │  │  ├── VoidType.php
   │     │  │  │  └── CallableType.php
   │     │  │  ├── exception
   │     │  │  │  ├── RuntimeException.php
   │     │  │  │  └── Exception.php
   │     │  │  ├── ReflectionMapper.php
   │     │  │  ├── TypeName.php
   │     │  │  └── Parameter.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── global-state
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  ├── exceptions
   │     │  │  │  ├── RuntimeException.php
   │     │  │  │  └── Exception.php
   │     │  │  ├── ExcludeList.php
   │     │  │  ├── Restorer.php
   │     │  │  ├── CodeExporter.php
   │     │  │  └── Snapshot.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── object-enumerator
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  └── Enumerator.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── diff
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  ├── Output
   │     │  │  │  ├── UnifiedDiffOutputBuilder.php
   │     │  │  │  ├── StrictUnifiedDiffOutputBuilder.php
   │     │  │  │  ├── DiffOnlyOutputBuilder.php
   │     │  │  │  ├── AbstractChunkOutputBuilder.php
   │     │  │  │  └── DiffOutputBuilderInterface.php
   │     │  │  ├── TimeEfficientLongestCommonSubsequenceCalculator.php
   │     │  │  ├── Exception
   │     │  │  │  ├── InvalidArgumentException.php
   │     │  │  │  ├── Exception.php
   │     │  │  │  └── ConfigurationException.php
   │     │  │  ├── MemoryEfficientLongestCommonSubsequenceCalculator.php
   │     │  │  ├── Parser.php
   │     │  │  ├── Diff.php
   │     │  │  ├── LongestCommonSubsequenceCalculator.php
   │     │  │  ├── Differ.php
   │     │  │  ├── Line.php
   │     │  │  └── Chunk.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── complexity
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  ├── Calculator.php
   │     │  │  ├── Exception
   │     │  │  │  ├── RuntimeException.php
   │     │  │  │  └── Exception.php
   │     │  │  ├── Complexity
   │     │  │  │  ├── ComplexityCollectionIterator.php
   │     │  │  │  ├── ComplexityCollection.php
   │     │  │  │  └── Complexity.php
   │     │  │  └── Visitor
   │     │  │    ├── CyclomaticComplexityCalculatingVisitor.php
   │     │  │    └── ComplexityCalculatingVisitor.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── cli-parser
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  ├── exceptions
   │     │  │  │  ├── AmbiguousOptionException.php
   │     │  │  │  ├── RequiredOptionArgumentMissingException.php
   │     │  │  │  ├── Exception.php
   │     │  │  │  ├── UnknownOptionException.php
   │     │  │  │  └── OptionDoesNotAllowArgumentException.php
   │     │  │  └── Parser.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── comparator
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  ├── exceptions
   │     │  │  │  ├── RuntimeException.php
   │     │  │  │  └── Exception.php
   │     │  │  ├── DateTimeComparator.php
   │     │  │  ├── TypeComparator.php
   │     │  │  ├── EnumerationComparator.php
   │     │  │  ├── ComparisonFailure.php
   │     │  │  ├── SplObjectStorageComparator.php
   │     │  │  ├── DOMNodeComparator.php
   │     │  │  ├── ObjectComparator.php
   │     │  │  ├── Factory.php
   │     │  │  ├── NumberComparator.php
   │     │  │  ├── Comparator.php
   │     │  │  ├── ResourceComparator.php
   │     │  │  ├── NumericComparator.php
   │     │  │  ├── MockObjectComparator.php
   │     │  │  ├── ExceptionComparator.php
   │     │  │  ├── ClosureComparator.php
   │     │  │  ├── ScalarComparator.php
   │     │  │  └── ArrayComparator.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── version
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  └── Version.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── lines-of-code
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  ├── Counter.php
   │     │  │  ├── LineCountingVisitor.php
   │     │  │  ├── LinesOfCode.php
   │     │  │  └── Exception
   │     │  │    ├── RuntimeException.php
   │     │  │    ├── Exception.php
   │     │  │    └── IllogicalValuesException.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── exporter
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  └── Exporter.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── environment
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  ├── Runtime.php
   │     │  │  └── Console.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    ├── object-reflector
   │     │  ├── ChangeLog.md
   │     │  ├── composer.json
   │     │  ├── README.md
   │     │  ├── src
   │     │  │  └── ObjectReflector.php
   │     │  ├── LICENSE
   │     │  └── SECURITY.md
   │    └── recursion-context
   │       ├── ChangeLog.md
   │       ├── composer.json
   │       ├── README.md
   │       ├── src
   │        │  └── Context.php
   │       ├── LICENSE
   │       └── SECURITY.md
  ├── DAZO-picto-carre-noir.svg
  ├── routes
   │  ├── console.php
   │  ├── web.php
   │  └── api.php
  ├── composer.json
  ├── vite.config.js
  ├── docker-compose.yml
  ├── README.md
  ├── tests
   │  ├── Feature
   │  │  ├── ExampleTest.php
   │  │  └── DecisionFlowTest.php
   │  ├── Unit
   │  │  ├── ExampleTest.php
   │  │  └── FeedbackServiceTest.php
   │  └── TestCase.php
  ├── config
   │  ├── cache.php
   │  ├── logging.php
   │  ├── auth.php
   │  ├── app.php
   │  ├── queue.php
   │  ├── database.php
   │  ├── sanctum.php
   │  ├── filesystems.php
   │  ├── mail.php
   │  ├── session.php
   │  └── services.php
  ├── bootstrap
   │  ├── app.php
   │  ├── cache
   │  │  ├── packages.php
   │  │  └── services.php
   │  └── providers.php
  ├── database
   │  ├── database.sqlite
   │  ├── seeders
   │  │  ├── CircleSeeder.php
   │  │  ├── UserSeeder.php
   │  │  ├── CategorySeeder.php
   │  │  ├── DatabaseSeeder.php
   │  │  └── DecisionModelSeeder.php
   │  ├── factories
   │  │  ├── FeedbackFactory.php
   │  │  ├── DecisionVersionFactory.php
   │  │  ├── CircleFactory.php
   │  │  ├── DecisionFactory.php
   │  │  ├── NotificationFactory.php
   │  │  ├── DecisionModelFactory.php
   │  │  ├── UserFactory.php
   │  │  ├── CircleMemberFactory.php
   │  │  ├── CategoryFactory.php
   │  │  └── DecisionParticipantFactory.php
   │  └── migrations
   │    ├── 2026_04_17_095109_create_consents_table.php
   │    ├── 2026_04_17_095102_create_help_texts_table.php
   │    ├── 2026_04_17_095115_create_attachments_table.php
   │    ├── 2026_04_17_095111_create_feedback_joins_table.php
   │    ├── 2026_04_17_095121_create_instance_config_table.php
   │    ├── 2026_04_17_095117_create_notifications_table.php
   │    ├── 2026_04_17_095113_create_feedback_messages_table.php
   │    ├── 2026_04_17_095118_create_notification_preferences_table.php
   │    ├── 2026_04_17_095057_create_circle_members_table.php
   │    ├── 2026_04_17_095114_create_thread_messages_table.php
   │    ├── 2026_04_17_095107_create_decision_relations_table.php
   │    ├── 0001_01_01_000000_create_users_table.php
   │    ├── 0001_01_01_000001_create_cache_table.php
   │    ├── 2026_04_17_095059_create_categories_table.php
   │    ├── 2026_04_17_095116_create_decision_labels_table.php
   │    ├── 0001_01_01_000002_create_jobs_table.php
   │    ├── 2026_04_17_095100_create_labels_table.php
   │    ├── 2026_04_17_095120_create_invitations_table.php
   │    ├── 2026_04_17_095108_create_decision_animator_logs_table.php
   │    ├── 2026_04_17_095103_create_decisions_table.php
   │    ├── 2026_04_17_095122_create_app_logs_table.php
   │    ├── 2026_04_18_015208_make_decision_version_id_nullable_in_attachments_table.php
   │    ├── 2026_04_17_094732_create_personal_access_tokens_table.php
   │    ├── 2026_04_17_095106_create_decision_participants_table.php
   │    ├── 2026_04_17_095104_create_decision_versions_table.php
   │    ├── 2026_04_17_095101_create_decision_models_table.php
   │    ├── 2026_04_17_095056_create_circles_table.php
   │    └── 2026_04_17_095110_create_feedbacks_table.php
  ├── LICENSE
  ├── phpunit.xml
  ├── package.json
  ├── storage
   │  ├── app
   │  │  ├── public
   │  │  │  └── attachments
   │  │  │    └── KUpfpjc0ywO1eXqbNpnBjMS9Ef2DnrEKCOnOTpwV.png
   │  │  └── private
   │  ├── pail
   │  │  └── 69e4ba6868947.pail
   │  ├── framework
   │  │  ├── sessions
   │  │  ├── testing
   │  │  ├── cache
   │  │  │  └── data
   │  │  └── views
   │  │    └── 7216721a7bd9189166977db888932c66.php
   │  └── logs
   │    └── laravel.log
  ├── package-lock.json
  ├── resources
   │  ├── css
   │  │  ├── dazo-theme.css
   │  │  └── app.css
   │  ├── js
   │  │  ├── App.vue
   │  │  ├── router
   │  │  │  └── index.js
   │  │  ├── layouts
   │  │  │  └── AppLayout.vue
   │  │  ├── bootstrap.js
   │  │  ├── stores
   │  │  │  ├── decision.js
   │  │  │  ├── auth.js
   │  │  │  ├── circle.js
   │  │  │  └── pending.js
   │  │  ├── components
   │  │  │  ├── RichTextEditor.vue
   │  │  │  ├── AttachmentPanel.vue
   │  │  │  ├── FeedbackEngine.vue
   │  │  │  ├── AnimatorSelector.vue
   │  │  │  ├── ParticipantPhasePanel.vue
   │  │  │  ├── CreateDecisionModal.vue
   │  │  │  ├── ImpersonationBanner.vue
   │  │  │  └── DecisionListItem.vue
   │  │  ├── app.js
   │  │  └── views
   │  │    ├── CircleList.vue
   │  │    ├── DecisionDetail.vue
   │  │    ├── DecisionCreate.vue
   │  │    ├── CircleDetail.vue
   │  │    ├── Dashboard.vue
   │  │    ├── PendingList.vue
   │  │    ├── admin
   │  │     │  ├── AdminCircles.vue
   │  │     │  ├── AdminCategories.vue
   │  │     │  ├── AdminDashboard.vue
   │  │     │  ├── AdminUsers.vue
   │  │     │  └── AdminConfig.vue
   │  │    ├── DecisionList.vue
   │  │    └── Login.vue
   │  └── views
   │    └── welcome.blade.php
  ├── Dockerfile
  ├── ROADMAP.md
  ├── composer.lock
  └── daha_dazo_github.pub
```

### File List
- /home/daha/DEV/DAZO/PROJECT/DAZO_logo.png
- /home/daha/DEV/DAZO/PROJECT/artisan
- /home/daha/DEV/DAZO/PROJECT/daha_dazo_github
- /home/daha/DEV/DAZO/PROJECT/DAZO-logo.svg
- /home/daha/DEV/DAZO/PROJECT/compose.yaml
- /home/daha/DEV/DAZO/PROJECT/CONTRIBUTING.md
- /home/daha/DEV/DAZO/PROJECT/DAZO-picto-carre-noir.svg
- /home/daha/DEV/DAZO/PROJECT/composer.json
- /home/daha/DEV/DAZO/PROJECT/vite.config.js
- /home/daha/DEV/DAZO/PROJECT/docker-compose.yml
- /home/daha/DEV/DAZO/PROJECT/README.md
- /home/daha/DEV/DAZO/PROJECT/LICENSE
- /home/daha/DEV/DAZO/PROJECT/phpunit.xml
- /home/daha/DEV/DAZO/PROJECT/package.json
- /home/daha/DEV/DAZO/PROJECT/package-lock.json
- /home/daha/DEV/DAZO/PROJECT/Dockerfile
- /home/daha/DEV/DAZO/PROJECT/ROADMAP.md
- /home/daha/DEV/DAZO/PROJECT/composer.lock
- /home/daha/DEV/DAZO/PROJECT/daha_dazo_github.pub
- /home/daha/DEV/DAZO/PROJECT/public/DAZO-logo-carre-blanc.svg

... and 8947 more files
