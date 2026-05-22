# Design Document — Portfolio

## Overview

A database-driven professional portfolio built on Laravel 13, Livewire 4, x-ui. UI v2, and Tailwind CSS v4. The application has two distinct surfaces:

- **Public portfolio** — a single-page-style Livewire application served at `/` with smooth anchor navigation, dark mode, and a contact form.
- **Admin panel** — an auth-protected area at `/admin/*` where the owner manages all content via Livewire CRUD components.

Content is stored in a relational SQLite (dev) / MySQL (prod) database. File uploads use Laravel's `public` storage disk. Contact form submissions dispatch a queued mailable. SEO meta tags are injected via Blade layout slots. Dark mode is handled entirely client-side with Alpine.js and `localStorage`.

---

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Browser                              │
│  Alpine.js (dark mode, mobile nav, scroll spy)              │
│  Livewire 4 (reactive components over HTTP)                 │
│  x-ui. UI v2 (component library)                             │
│  Tailwind CSS v4 (utility styling)                          │
└────────────────────┬────────────────────────────────────────┘
                     │ HTTP / Livewire wire requests
┌────────────────────▼────────────────────────────────────────┐
│                    Laravel 13                               │
│  routes/web.php  →  Livewire components / controllers       │
│  Middleware: auth (admin), throttle (contact form)          │
│  Models: Eloquent ORM                                       │
│  Mail: queued ContactMessageNotification                    │
│  Storage: public disk (profile photos, project images)      │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│                    Database (SQLite / MySQL)                 │
│  hero_contents, about_contents, skills, projects,           │
│  tags, project_tag, experience_entries,                     │
│  contact_messages, social_links                             │
└─────────────────────────────────────────────────────────────┘
```

### Key Architectural Decisions

- **Single-page-style via anchor links** — the public portfolio is one Livewire page component (`PortfolioPage`) that renders all sections. Navigation uses `<a href="#section-id">` anchors with CSS `scroll-behavior: smooth`. No client-side router is needed.
- **Livewire full-page components** — both `PortfolioPage` and admin components are full-page Livewire components using the `#[Layout]` attribute, consistent with the existing starter kit pattern.
- **No separate API** — the admin panel communicates with the server exclusively through Livewire wire calls. No REST or JSON API is needed.
- **Queued mail** — `ContactMessageNotification` implements `ShouldQueue` so the contact form response is instant regardless of mail server latency.
- **Rate limiting in `AppServiceProvider`** — the `contact` rate limiter is registered alongside the existing Fortify limiters, following the project's existing pattern in `FortifyServiceProvider`.

---

## Components and Interfaces

### Public Livewire Components

#### `App\Livewire\PortfolioPage`

Full-page component. Loads all content models in `mount()` and passes them to the view. Renders all portfolio sections in a single response.

```
Properties:
  HeroContent $hero
  AboutContent $about
  Skill[] $skills (grouped by category)
  Project[] $projects (published, ordered)
  Tag[] $tags
  ExperienceEntry[] $experiences
```

#### `App\Livewire\Portfolio\ProjectsSection`

Nested component within `PortfolioPage` responsible for tag filtering. Keeps the active tag filter in Livewire state so filtering happens without a full page reload.

```
Properties:
  string|null $activeTag = null
  Tag[] $tags
  Project[] $filteredProjects (computed)

Actions:
  filterByTag(string $tag): void
  clearFilter(): void
```

#### `App\Livewire\Portfolio\ContactForm`

Handles contact form submission, validation, rate limiting, and mail dispatch.

```
Properties:
  string $name = ''
  string $email = ''
  string $subject = ''
  string $message = ''
  bool $submitted = false

Actions:
  submit(): void  — validates, rate-checks, stores ContactMessage, dispatches mail
```

### Admin Livewire Components

All admin components use the existing `layouts.app` (sidebar) layout via `#[Layout('layouts.app')]`.

| Component | Responsibility |
|---|---|
| `App\Livewire\Admin\HeroEditor` | Edit hero name, tagline, intro, photo, social links |
| `App\Livewire\Admin\AboutEditor` | Edit biography, photo, location, availability, years of experience |
| `App\Livewire\Admin\SkillManager` | CRUD for skills; drag-and-drop / manual sort_order |
| `App\Livewire\Admin\ProjectManager` | CRUD for projects; tag assignment; publish toggle; sort_order |
| `App\Livewire\Admin\ExperienceManager` | CRUD for experience entries |
| `App\Livewire\Admin\ContactInbox` | List contact messages; mark as read |

Each manager component follows the pattern: list view with inline edit modal (using `<x-ui.modal>`), save dispatches `x-ui.:toast()` on success, validation errors surface via `$this->addError()` / `@error` directives.

### Blade Layouts

#### `resources/views/layouts/portfolio.blade.php`

New public layout (not the existing admin sidebar layout). Includes:
- `<head>` with `@stack('meta')` slot for SEO tags
- Fixed nav bar with anchor links and theme toggle
- Alpine.js dark mode initialisation script on `<html>`
- `@x-ui.Scripts` and Vite assets

#### `resources/views/layouts/portfolio/head.blade.php`

Partial included in the portfolio layout `<head>`. Renders title, meta description, OG tags, and canonical link using variables passed from the page component.

---

## Data Models

### `hero_contents`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | string(100) | Owner's full name |
| `tagline` | string(150) | Professional tagline |
| `introduction` | string(500) | Short intro text |
| `profile_photo_path` | string nullable | Relative path on public disk |
| `created_at` / `updated_at` | timestamps | |

Single-row table (seeded with one record; admin edits that record).

### `about_contents`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `biography` | text | Rich text, max 2000 chars |
| `profile_photo_path` | string nullable | Independent of hero photo |
| `location` | string(100) nullable | |
| `availability_status` | string(100) nullable | e.g. "Open to work" |
| `years_of_experience` | unsignedTinyInteger nullable | |
| `created_at` / `updated_at` | timestamps | |

Single-row table.

### `social_links`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `platform` | string(50) | e.g. "github", "linkedin" |
| `url` | string(500) | |
| `icon` | string(50) nullable | Heroicon name |
| `sort_order` | unsignedSmallInteger default 0 | |
| `created_at` / `updated_at` | timestamps | |

Max 10 rows enforced at application layer.

### `skills`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | string(100) | |
| `category` | string(100) nullable | Grouping label |
| `proficiency` | unsignedTinyInteger nullable | 0–100 or null |
| `proficiency_label` | string(20) nullable | Beginner/Intermediate/Advanced/Expert |
| `is_active` | boolean default true | |
| `sort_order` | unsignedSmallInteger default 0 | |
| `created_at` / `updated_at` | timestamps | |

### `projects`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `title` | string(150) | |
| `slug` | string(170) unique | Auto-generated from title |
| `description` | text | Full description |
| `excerpt` | string(200) nullable | Truncated for card display |
| `cover_image_path` | string nullable | Relative path on public disk |
| `live_url` | string(500) nullable | |
| `repository_url` | string(500) nullable | |
| `is_published` | boolean default false | |
| `sort_order` | unsignedSmallInteger default 0 | |
| `created_at` / `updated_at` | timestamps | |

### `tags`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | string(50) unique | |
| `created_at` / `updated_at` | timestamps | |

### `project_tag` (pivot)

| Column | Type |
|---|---|
| `project_id` | bigint FK → projects |
| `tag_id` | bigint FK → tags |

Composite primary key on `(project_id, tag_id)`.

### `experience_entries`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `title` | string(150) | Job title or degree |
| `organisation` | string(150) | |
| `type` | enum('work','education') | |
| `start_date` | date | |
| `end_date` | date nullable | null = "Present" |
| `description` | string(500) nullable | |
| `created_at` / `updated_at` | timestamps | |

### `contact_messages`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `sender_name` | string(100) | |
| `sender_email` | string(150) | |
| `subject` | string(200) | |
| `body` | text | min 10 chars |
| `ip_address` | string(45) nullable | For audit |
| `is_read` | boolean default false | |
| `created_at` / `updated_at` | timestamps | |

### Eloquent Model Relationships

```
Project  belongsToMany  Tag  (via project_tag)
Tag      belongsToMany  Project
```

All other models are standalone (no FK relationships beyond the pivot).

### Slug Generation Logic

Implemented as a static helper method on the `Project` model (or a dedicated `SlugGenerator` class):

```php
public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
{
    $base = Str::slug($title);
    $slug = $base;
    $counter = 2;

    while (
        static::where('slug', $slug)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists()
    ) {
        $slug = $base . '-' . $counter++;
    }

    return $slug;
}
```

Called in the `ProjectManager` component's `save()` action before persisting.

---

## Routing

```php
// routes/web.php

// Public portfolio
Route::get('/', PortfolioPage::class)->name('portfolio');
Route::get('/projects/{slug}', ProjectDetailPage::class)->name('projects.show');

// Admin panel — auth-protected
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.hero'))->name('index');
    Route::get('/hero',       HeroEditor::class)->name('hero');
    Route::get('/about',      AboutEditor::class)->name('about');
    Route::get('/skills',     SkillManager::class)->name('skills');
    Route::get('/projects',   ProjectManager::class)->name('projects');
    Route::get('/experience', ExperienceManager::class)->name('experience');
    Route::get('/messages',   ContactInbox::class)->name('messages');
});
```

Unauthenticated access to `/admin/*` is handled by Laravel's built-in `auth` middleware, which redirects to the Fortify login route.

---

## File Storage

All uploads use the `public` disk (`storage/app/public`), symlinked to `public/storage` via `php artisan storage:link`.

| Upload type | Path pattern |
|---|---|
| Hero profile photo | `photos/hero/{uuid}.{ext}` |
| About profile photo | `photos/about/{uuid}.{ext}` |
| Project cover image | `projects/{uuid}.{ext}` |

Upload handling in admin components uses `Livewire\WithFileUploads`. Temporary files are validated before the final move. Old files are deleted when replaced.

```php
// Example in HeroEditor
use Livewire\WithFileUploads;

#[Validate(['photo' => 'nullable|image|max:2048'])]
public ?TemporaryUploadedFile $photo = null;

public function save(): void
{
    $this->validate();

    if ($this->photo) {
        // Delete old file
        if ($hero->profile_photo_path) {
            Storage::disk('public')->delete($hero->profile_photo_path);
        }
        $path = $this->photo->store('photos/hero', 'public');
        $hero->profile_photo_path = $path;
    }
    // ...
}
```

---

## Mail and Queue

### `App\Mail\ContactMessageNotification`

```php
class ContactMessageNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: config('portfolio.owner_email'),
            subject: 'New contact message: ' . $this->contactMessage->subject,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.contact-message');
    }
}
```

Dispatched from `ContactForm::submit()`:

```php
Mail::to(config('portfolio.owner_email'))->queue(new ContactMessageNotification($message));
```

A `config/portfolio.php` config file holds `owner_email` (read from `PORTFOLIO_OWNER_EMAIL` env var).

---

## Rate Limiting

Registered in `AppServiceProvider::boot()` alongside existing rate limiters:

```php
RateLimiter::for('contact', function (Request $request) {
    return Limit::perHour(5)->by($request->ip())->response(function () {
        // Livewire components check via RateLimiter::tooManyAttempts()
    });
});
```

In `ContactForm::submit()`:

```php
if (RateLimiter::tooManyAttempts('contact:' . request()->ip(), 5)) {
    $this->addError('form', 'Too many submissions. Please try again later.');
    return;
}
RateLimiter::hit('contact:' . request()->ip(), 3600);
```

---

## SEO and Meta Tags

The portfolio layout defines a `@stack('meta')` section in `<head>`. Each page component pushes its meta tags:

```blade
{{-- In PortfolioPage view --}}
@push('meta')
    <title>{{ $hero->name }} — Portfolio</title>
    <meta name="description" content="{{ $hero->tagline }}">
    <meta property="og:title" content="{{ $hero->name }} — Portfolio">
    <meta property="og:description" content="{{ $hero->tagline }}">
    <meta property="og:image" content="{{ $hero->profile_photo_url }}">
    <link rel="canonical" href="{{ url('/') }}">
@endpush
```

```blade
{{-- In ProjectDetailPage view --}}
@push('meta')
    <title>{{ $project->title }} — {{ $hero->name }}</title>
    <meta name="description" content="{{ $project->excerpt ?? Str::limit($project->description, 160) }}">
    <meta property="og:title" content="{{ $project->title }} — {{ $hero->name }}">
    <meta property="og:description" content="{{ $project->excerpt ?? Str::limit($project->description, 160) }}">
    <meta property="og:image" content="{{ $project->cover_image_url ?? $hero->profile_photo_url }}">
    <link rel="canonical" href="{{ route('projects.show', $project->slug) }}">
@endpush
```

---

## Dark Mode

Dark mode is handled entirely client-side with Alpine.js. The portfolio layout initialises the theme on `<html>` before the first paint to avoid flash:

```html
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data
    x-init="
        const stored = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (stored === 'dark' || (!stored && prefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    "
>
```

The theme toggle component reads and writes `localStorage.theme` and toggles the `dark` class:

```html
<button
    x-data="{ dark: document.documentElement.classList.contains('dark') }"
    @click="
        dark = !dark;
        dark
            ? document.documentElement.classList.add('dark')
            : document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    "
    aria-label="Toggle dark mode"
>
    <template x-if="dark"><x-ui.icon.moon /></template>
    <template x-if="!dark"><x-ui.icon.sun /></template>
</button>
```

The existing `app.css` already defines `@custom-variant dark (&:where(.dark, .dark *))` which enables Tailwind's `dark:` variant based on the `dark` class on `<html>`.

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Hero content round-trip

*For any* valid `HeroContent` record (with name, tagline, and introduction), rendering the portfolio page should produce HTML that contains all three field values.

**Validates: Requirements 3.1, 3.5**

---

### Property 2: Social links are all rendered

*For any* set of `SocialLink` records, the rendered hero section should contain a link element for every social link in the set.

**Validates: Requirements 3.4**

---

### Property 3: About content round-trip

*For any* valid `AboutContent` record, rendering the portfolio page should produce HTML that contains the biography, location, availability status, and years of experience values.

**Validates: Requirements 4.1, 4.3, 4.4**

---

### Property 4: Admin validation errors surface for every invalid field

*For any* admin form submission where one or more fields violate their validation rules, the Livewire component should add an error for each invalid field, and no record should be persisted.

**Validates: Requirements 4.5, 9.5**

---

### Property 5: Skills are grouped and ordered correctly

*For any* set of active `Skill` records with assigned categories and sort orders, the rendered skills section should group skills by category and within each category render them in ascending `sort_order` order.

**Validates: Requirements 5.1, 5.3**

---

### Property 6: Skill proficiency rendering

*For any* `Skill`, if `proficiency` is set the rendered output should include a proficiency indicator; if `proficiency` is null the rendered output should not include a proficiency indicator.

**Validates: Requirements 5.2, 5.6**

---

### Property 7: Published projects appear on the portfolio; unpublished do not

*For any* set of `Project` records, the portfolio page should display exactly the projects where `is_published = true` and none where `is_published = false`.

**Validates: Requirements 6.1, 9.7**

---

### Property 8: Tag filter returns only matching projects

*For any* active tag filter, the `ProjectsSection` component should return only projects that have that tag associated, and clearing the filter should restore the full published project list.

**Validates: Requirements 6.2, 6.9**

---

### Property 9: Project URL buttons appear only when URLs are set

*For any* published `Project`, the detail page should display a live URL button if and only if `live_url` is non-null, and a repository URL button if and only if `repository_url` is non-null.

**Validates: Requirements 6.4, 6.5**

---

### Property 10: Projects are rendered in sort_order

*For any* set of published `Project` records with distinct `sort_order` values, the rendered projects section should display them in ascending `sort_order` order.

**Validates: Requirements 6.7**

---

### Property 11: Experience entries are rendered in reverse chronological order

*For any* set of `ExperienceEntry` records with varying `start_date` values, the rendered experience section should display them sorted by `start_date` descending (most recent first).

**Validates: Requirements 7.1**

---

### Property 12: Experience entry fields are all rendered

*For any* `ExperienceEntry`, the rendered output should contain the title, organisation, date range, description, and type indicator. When `end_date` is null, the string "Present" should appear in the date range.

**Validates: Requirements 7.2, 7.3, 7.5**

---

### Property 13: Contact form accepts valid submissions and rejects invalid ones

*For any* combination of contact form inputs, the `ContactForm` component should accept the submission (create a `ContactMessage` record) if and only if all fields pass their validation rules (name non-empty, email valid format, subject non-empty, body ≥ 10 chars).

**Validates: Requirements 8.1, 8.2, 8.3**

---

### Property 14: Valid contact submission dispatches a queued notification

*For any* valid contact form submission, a `ContactMessageNotification` mailable should be queued for the owner's email address.

**Validates: Requirements 8.4**

---

### Property 15: Contact form fields reset after successful submission

*For any* valid contact form submission, all form fields (`name`, `email`, `subject`, `message`) should be empty strings and `submitted` should be `true` after the action completes.

**Validates: Requirements 8.7**

---

### Property 16: Slug is generated from title and is URL-safe

*For any* project title string, the generated slug should equal `Str::slug($title)` when no collision exists, and should be a valid URL-safe string in all cases.

**Validates: Requirements 10.5**

---

### Property 17: Slug uniqueness with numeric suffix

*For any* set of projects created with the same base title, each project should have a unique slug, with collisions resolved by appending `-2`, `-3`, etc.

**Validates: Requirements 10.6**

---

### Property 18: Project detail page renders all fields for published projects

*For any* published `Project`, the detail page at `/projects/{slug}` should render the full description, all associated tags, and the live/repo URL buttons (when set).

**Validates: Requirements 10.2**

---

### Property 19: SEO meta tags are present and correctly formatted on all pages

*For any* page (portfolio root and project detail), the rendered HTML should contain a `<title>` matching the format `{Page Title} — {Owner Name}`, a non-empty `<meta name="description">`, all three Open Graph tags (`og:title`, `og:description`, `og:image`), and a `<link rel="canonical">`.

**Validates: Requirements 11.1, 11.2, 11.3, 11.4, 11.5**

---

### Property 20: All img elements have non-empty alt attributes

*For any* rendered portfolio page, every `<img>` element in the HTML output should have a non-empty `alt` attribute.

**Validates: Requirements 3.3, 12.4**

---

## Error Handling

| Scenario | Handling |
|---|---|
| `/projects/{slug}` for non-existent or unpublished project | `abort(404)` in `ProjectDetailPage::mount()` |
| File upload exceeds size limit | Livewire validation error displayed inline |
| Mail dispatch failure | Exception caught; logged; contact message is still saved |
| Rate limit exceeded on contact form | `addError('form', ...)` displayed above submit button |
| Admin saves invalid data | `$this->validate()` throws `ValidationException`; Livewire surfaces `@error` messages inline |
| Unauthenticated admin access | Laravel `auth` middleware redirects to Fortify login |
| Missing `HeroContent` / `AboutContent` row | Seeder guarantees one row exists; `firstOrFail()` used in page components |

---

## Testing Strategy

### Unit Tests

- `SlugGeneratorTest` — tests `Project::generateUniqueSlug()` with various titles and collision scenarios (Properties 16, 17).
- `ContactFormValidationTest` — tests the validation rules in isolation (Property 13).

### Feature Tests (Pest)

Feature tests cover HTTP-level behavior and Livewire component interactions using `Livewire::test()`.

**Public portfolio:**
- `PortfolioPageTest` — asserts GET `/` returns 200, renders hero/about/skills/projects/experience sections, and contains correct SEO meta tags (Properties 1, 2, 3, 5, 6, 7, 10, 11, 12, 19, 20).
- `ProjectDetailPageTest` — asserts published projects resolve at `/projects/{slug}`, non-existent/unpublished return 404, all fields render, URL buttons appear conditionally (Properties 9, 18; edge cases 10.3).
- `ProjectsSectionTest` — tests tag filtering and clear filter via `Livewire::test()` (Properties 7, 8).
- `ContactFormTest` — tests valid submission creates record and dispatches mail, invalid submission shows errors, rate limit blocks 6th submission (Properties 13, 14, 15; edge case 8.5).

**Admin panel:**
- `AdminAuthTest` — asserts unauthenticated access to `/admin` redirects to login (Requirement 9.1).
- `HeroEditorTest` — tests save updates hero content and dispatches toast (Properties 1, 4).
- `AboutEditorTest` — tests save updates about content (Properties 3, 4).
- `SkillManagerTest` — tests CRUD, sort order update, grouping (Properties 5, 6).
- `ProjectManagerTest` — tests CRUD, publish toggle, slug generation, tag assignment (Properties 7, 16, 17).
- `ExperienceManagerTest` — tests CRUD, reverse chronological order (Properties 11, 12).
- `ContactInboxTest` — tests messages list and mark-as-read (Properties 13, 14).

### Property-Based Testing

This project uses **Pest** with **[PestPHP/Pest-Plugin-Faker](https://pestphp.com/)** for data generation. For true property-based testing, the `nunomaduro/collision` package is already present. Properties are implemented as Pest tests with `it()` blocks that use `fake()` to generate varied inputs across multiple iterations.

Each property test is tagged with a comment in the format:
`// Feature: portfolio, Property {N}: {property_text}`

Minimum 100 iterations per property test where randomised input is used (achieved via `foreach (range(1, 100) as $_)` or a dataset).

### Testing Approach Summary

| Test type | Tool | Focus |
|---|---|---|
| Unit | Pest | Slug generation, validation rules |
| Feature / Livewire | Pest + `Livewire::test()` | Component behavior, HTTP responses |
| Property (data-driven) | Pest + `fake()` datasets | Universal properties across varied inputs |
| Smoke / visual | Manual / Lighthouse | Dark mode, responsive layout, performance, accessibility |
