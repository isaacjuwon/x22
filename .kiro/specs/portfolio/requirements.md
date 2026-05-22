# Requirements Document

## Introduction

A dynamic, content-manageable company portfolio application built on Laravel 13, Livewire 4, and x-ui. UI v2 with Tailwind CSS v4. The portfolio presents the company's identity through structured sections — Hero, About, Team, Services, Projects, Testimonials, Clients, and Contact — using a hybrid content strategy: static Markdown files for editorial content and a relational database for Projects and Contact Messages. An authenticated admin manages Projects and reads Contact Messages through a protected admin panel.

---

## Glossary

- **Portfolio**: The public-facing website that presents the company's professional profile.
- **Owner**: The authenticated user who manages portfolio content via the admin panel.
- **Visitor**: An unauthenticated user browsing the public Portfolio.
- **Admin_Panel**: The authenticated, protected area where the Owner manages Projects and Contact Messages.
- **Hero_Section**: The top-most section of the Portfolio displaying the company name, tagline, and call-to-action links.
- **About_Section**: A section containing the company story, mission, and key details.
- **Team_Section**: A section listing team members with photo, name, role, and bio.
- **Services_Section**: A section listing the services the company offers.
- **Projects_Section**: A section showcasing portfolio projects with descriptions, tags, links, and images.
- **Testimonials_Section**: A section displaying client testimonials.
- **Clients_Section**: A section displaying client logos.
- **Contact_Section**: A section containing a contact form that Visitors can submit.
- **Contact_Message**: A record of a message submitted by a Visitor through the Contact_Section.
- **Project**: A portfolio item with a title, description, image, tags, and optional URLs.
- **Tag**: A short label attached to a Project for filtering and display.
- **Markdown_Content**: Editorial content stored as Markdown files with YAML frontmatter in the `content/` directory.
- **ContentLoader**: A service class that reads, parses, and caches Markdown content files.
- **Dark_Mode**: A visual theme variant using dark backgrounds and light text.
- **Theme_Toggle**: A UI control that switches between light and dark themes.
- **Contact_Form**: The Livewire component that collects and submits a Visitor's message.
- **Slug**: A URL-safe string identifier derived from a Project's title.

---

## Requirements

### Requirement 1: Public Portfolio Layout and Navigation

**User Story:** As a Visitor, I want a polished, responsive single-page portfolio with smooth section navigation, so that I can explore the company's profile without full page reloads.

#### Acceptance Criteria

1. THE Portfolio SHALL render a fixed top navigation bar containing anchor links to each section (Hero, About, Team, Services, Projects, Testimonials, Contact).
2. WHEN a Visitor clicks a navigation anchor, THE Portfolio SHALL scroll smoothly to the corresponding section without a full page reload.
3. WHEN the viewport width is below 768px, THE Portfolio SHALL collapse the navigation bar into a mobile-friendly menu, displaying a visible toggle button that shows or hides the navigation links when clicked.
4. THE Portfolio SHALL apply the x-ui. UI component library and Tailwind CSS v4 utility classes for all layout and styling.
5. THE Portfolio SHALL be accessible at the application root URL (`/`).
6. WHEN the section occupying the topmost visible area of the viewport changes during scroll, THE Portfolio SHALL apply a visually distinct style to the corresponding navigation anchor that differs from all other anchors.

---

### Requirement 2: Dark Mode and Theme Toggle

**User Story:** As a Visitor, I want to switch between light and dark themes, so that I can view the portfolio comfortably in any lighting condition.

#### Acceptance Criteria

1. THE Portfolio SHALL display a Theme_Toggle control in the navigation bar.
2. WHEN a Visitor activates the Theme_Toggle, THE Portfolio SHALL switch between light and Dark_Mode without a page reload by toggling a `dark` class on the root `<html>` element.
3. THE Portfolio SHALL persist the Visitor's theme preference in `localStorage` under the key `theme` across sessions.
4. WHEN a Visitor first loads the Portfolio and no `theme` key exists in `localStorage`, THE Portfolio SHALL apply the operating system's preferred color scheme via the `prefers-color-scheme` media query.
5. WHEN a Visitor first loads the Portfolio and no `theme` key exists in `localStorage` and the operating system reports no color scheme preference, THE Portfolio SHALL default to light mode.
6. WHILE Dark_Mode is active, THE Portfolio SHALL apply Tailwind CSS v4 dark-variant utility classes to all sections by maintaining the `dark` class on the root `<html>` element.
7. THE Theme_Toggle SHALL display a visual indicator (e.g., sun or moon icon) reflecting the currently active theme.

---

### Requirement 3: Hero Section

**User Story:** As a Visitor, I want to see a compelling hero introduction at the top of the portfolio, so that I immediately understand who the company is and what they do.

#### Acceptance Criteria

1. THE Hero_Section SHALL display the company name, professional tagline, and a short introduction text, all sourced from `content/hero.md`.
2. THE Hero_Section SHALL display at least one call-to-action button that scrolls to or links to the Projects_Section and one that scrolls to or links to the Contact_Section.
3. THE Hero_Section SHALL display the company logo or hero image with a non-empty descriptive `alt` attribute.
4. THE Hero_Section SHALL display icon links for the company's social profiles (e.g. GitHub, LinkedIn), sourced from the frontmatter of `content/hero.md`.
5. WHEN the `content/hero.md` file is updated and the application cache is cleared, THE Portfolio SHALL reflect the updated values on the next page load without requiring a code deployment.

---

### Requirement 4: About Section

**User Story:** As a Visitor, I want to read about the company's story and mission, so that I can understand the company's background and values.

#### Acceptance Criteria

1. THE About_Section SHALL display the company biography/story sourced from the body of `content/about.md`, rendered as HTML from Markdown.
2. THE About_Section SHALL display key detail fields sourced from the frontmatter of `content/about.md`, including at minimum: location, founding year, and a short tagline.
3. WHEN the `content/about.md` file is updated and the application cache is cleared, THE Portfolio SHALL reflect the updated content on the next page load.

---

### Requirement 5: Team Section

**User Story:** As a Visitor, I want to see the company's team members, so that I can understand who is behind the company.

#### Acceptance Criteria

1. THE Team_Section SHALL display all team members sourced from Markdown files in `content/team/`, each rendered as a card showing name, role, bio, and photo.
2. EACH team member file SHALL use YAML frontmatter for name, role, and photo path, with the Markdown body used as the bio.
3. THE Team_Section SHALL display team members in the order determined by a `sort_order` frontmatter field (ascending).
4. WHEN no team member files exist in `content/team/`, THE Team_Section SHALL display a placeholder message.
5. EACH team member photo `<img>` element SHALL include a non-empty descriptive `alt` attribute.

---

### Requirement 6: Services Section

**User Story:** As a Visitor, I want to see the services the company offers, so that I can evaluate whether they meet my needs.

#### Acceptance Criteria

1. THE Services_Section SHALL display all services sourced from Markdown files in `content/services/`, each rendered as a card showing title, description, and optional icon.
2. EACH service file SHALL use YAML frontmatter for title and icon, with the Markdown body used as the description.
3. THE Services_Section SHALL display services in the order determined by a `sort_order` frontmatter field (ascending).
4. WHEN no service files exist in `content/services/`, THE Services_Section SHALL display a placeholder message.

---

### Requirement 7: Projects Section

**User Story:** As a Visitor, I want to browse the company's projects with filtering by tag, so that I can find work relevant to my interests.

#### Acceptance Criteria

1. THE Projects_Section SHALL display all published Projects as cards, each showing the project title, a description excerpt truncated to a maximum of 200 characters, cover image, and Tags.
2. WHEN a Visitor selects a single Tag filter, THE Projects_Section SHALL display only Projects associated with that Tag without a full page reload.
3. WHEN a Visitor clicks a Project card, THE Portfolio SHALL navigate to a dedicated Project detail page at `/projects/{slug}`.
4. IF a published Project has a live URL set, THEN THE Project detail page SHALL display a live URL button linking to that URL.
5. IF a published Project has a repository URL set, THEN THE Project detail page SHALL display a repository URL button linking to that URL.
6. IF a Project has no cover image, THEN THE Projects_Section SHALL display a placeholder element with a fixed background colour and the project title as its text content in place of the image.
7. THE Projects_Section SHALL display Projects in the sort order defined by the Owner in the Admin_Panel.
8. WHEN no published Projects exist, THE Projects_Section SHALL display a message stating that no projects are available.
9. WHEN a Visitor clears the active Tag filter, THE Projects_Section SHALL display all published Projects.

---

### Requirement 8: Testimonials Section

**User Story:** As a Visitor, I want to read client testimonials, so that I can assess the company's reputation and quality of work.

#### Acceptance Criteria

1. THE Testimonials_Section SHALL display all testimonials sourced from Markdown files in `content/testimonials/`, each showing the client name, company, quote, and optional photo.
2. EACH testimonial file SHALL use YAML frontmatter for client_name, company, and photo_path (nullable), with the Markdown body used as the quote.
3. WHEN no testimonial files exist in `content/testimonials/`, THE Testimonials_Section SHALL display a placeholder message.
4. EACH testimonial photo `<img>` element SHALL include a non-empty descriptive `alt` attribute.

---

### Requirement 9: Clients Section

**User Story:** As a Visitor, I want to see the company's clients, so that I can gauge the company's experience and credibility.

#### Acceptance Criteria

1. THE Clients_Section SHALL display all client entries sourced from Markdown files in `content/clients/`, each showing the client logo and name.
2. EACH client file SHALL use YAML frontmatter for name, logo_path, and url (nullable).
3. WHEN a client has a url set, THE Clients_Section SHALL wrap the client logo in an anchor tag linking to that URL (opening in a new tab).
4. EACH client logo `<img>` element SHALL include a non-empty descriptive `alt` attribute containing the client name.

---

### Requirement 10: Contact Section and Form

**User Story:** As a Visitor, I want to send a message to the company through a contact form, so that I can reach out for opportunities or enquiries.

#### Acceptance Criteria

1. THE Contact_Form SHALL collect the Visitor's name (required), email address (required, valid format), subject (required), and message body (required, minimum 10 characters).
2. WHEN a Visitor submits the Contact_Form with valid data, THE Contact_Form SHALL store the submission as a Contact_Message record and display a success confirmation to the Visitor.
3. IF a Visitor submits the Contact_Form with invalid data, THEN THE Contact_Form SHALL display inline validation errors for each invalid field without a page reload.
4. WHEN a Contact_Message is stored, THE Portfolio SHALL dispatch a queued notification to the Owner's email address.
5. THE Contact_Form SHALL enforce rate limiting of 5 submissions per IP address per hour.
6. IF the rate limit is exceeded, THEN THE Contact_Form SHALL display an error message informing the Visitor to try again later.
7. WHEN a Contact_Form submission succeeds, THE Contact_Form SHALL reset all fields to their empty state.

---

### Requirement 11: Admin Panel — Content Management

**User Story:** As an Owner, I want a protected admin panel to manage Projects and read Contact Messages, so that I can keep the portfolio's project showcase up to date.

#### Acceptance Criteria

1. THE Admin_Panel SHALL be accessible only to authenticated users at `/admin`.
2. WHEN an unauthenticated user attempts to access `/admin`, THE Admin_Panel SHALL redirect them to the login page.
3. THE Admin_Panel SHALL provide CRUD interfaces for: Projects and Contact Messages.
4. WHEN the Owner saves changes to a Project, THE Admin_Panel SHALL display a success toast notification using x-ui. UI.
5. IF a save operation fails due to a validation error, THEN THE Admin_Panel SHALL display inline field-level error messages.
6. THE Admin_Panel SHALL allow the Owner to reorder Projects via a manual sort-order input.
7. THE Admin_Panel SHALL allow the Owner to toggle a Project's published status between draft and published.
8. THE Admin_Panel SHALL display all received Contact_Messages in a list with sender name, email, subject, and received timestamp.
9. WHEN the Owner marks a Contact_Message as read, THE Admin_Panel SHALL update the message's read status without a full page reload.

---

### Requirement 12: Project Detail Page

**User Story:** As a Visitor, I want to view a dedicated page for each project with full details, so that I can deeply evaluate the company's work.

#### Acceptance Criteria

1. THE Portfolio SHALL resolve a Project detail page at the URL `/projects/{slug}` where `{slug}` is the Project's unique Slug.
2. WHEN a Visitor navigates to a Project detail page for a published Project, THE Portfolio SHALL display the full description, all associated images, Tags, live URL button, and repository URL button.
3. IF a Visitor navigates to a Project detail page for a non-existent or unpublished Project, THEN THE Portfolio SHALL return a 404 response.
4. THE Project detail page SHALL include a back-navigation link to the Projects_Section on the main Portfolio page.
5. THE Portfolio SHALL generate the Slug automatically from the Project title when a new Project is created in the Admin_Panel.
6. WHEN the Slug already exists for a different Project, THE Admin_Panel SHALL append a numeric suffix to ensure uniqueness (e.g., `my-project-2`).

---

### Requirement 13: SEO and Meta Tags

**User Story:** As an Owner, I want the portfolio to have proper SEO meta tags, so that search engines and social platforms can index and preview the company's work correctly.

#### Acceptance Criteria

1. THE Portfolio SHALL render a unique `<title>` tag for each page using the format `{Page Title} — {Company Name}`.
2. THE Portfolio SHALL render `<meta name="description">` tags with page-specific content for the main portfolio page and each Project detail page.
3. THE Portfolio SHALL render Open Graph meta tags (`og:title`, `og:description`, `og:image`) for the main portfolio page and each Project detail page.
4. THE Portfolio SHALL render a canonical `<link rel="canonical">` tag on every page.
5. WHEN a Project has a cover image, THE Portfolio SHALL use that image as the `og:image` value for the Project detail page.

---

### Requirement 14: Performance and Accessibility

**User Story:** As a Visitor, I want the portfolio to load quickly and be usable with assistive technologies, so that I have a smooth and inclusive experience.

#### Acceptance Criteria

1. THE Portfolio SHALL lazy-load images below the fold using the HTML `loading="lazy"` attribute.
2. THE Portfolio SHALL achieve a Lighthouse performance score of 80 or above on the production build.
3. THE Portfolio SHALL include descriptive `alt` attributes on all `<img>` elements.
4. THE Portfolio SHALL use semantic HTML5 landmark elements (`<header>`, `<main>`, `<section>`, `<footer>`, `<nav>`) for each major section.
5. THE Portfolio SHALL maintain a colour contrast ratio of at least 4.5:1 for all body text against its background in both light and Dark_Mode.
6. THE Portfolio SHALL be fully keyboard-navigable, with all interactive elements reachable and operable via the Tab key.

---

### Requirement 15: Markdown Content Loading

**User Story:** As an Owner, I want to update portfolio content by editing Markdown files, so that I can manage editorial content without touching the database or admin panel.

#### Acceptance Criteria

1. THE Portfolio SHALL load Hero, About, Team, Services, Testimonials, and Client content from Markdown files in the `content/` directory.
2. THE ContentLoader service SHALL parse YAML frontmatter and Markdown body from each content file using `spatie/yaml-front-matter` and `league/commonmark`.
3. THE ContentLoader service SHALL cache parsed content using `Cache::remember()` to avoid re-parsing on every request.
4. WHEN the application cache is cleared (e.g. `php artisan cache:clear`), THE Portfolio SHALL re-parse and serve the latest Markdown file content on the next request.
5. IF a required content file is missing, THE ContentLoader SHALL return a sensible default (empty string or empty array) rather than throwing an exception.
