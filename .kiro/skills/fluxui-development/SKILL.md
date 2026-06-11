---
name: x-ui.ui-development
description: "Use this skill for x-ui. UI development in Livewire applications only. Trigger when working with <x-ui.*> components, building or customizing Livewire component UIs, creating forms, modals, tables, or other interactive elements. Covers: x-ui. components (buttons, inputs, modals, forms, tables, date-pickers, kanban, badges, tooltips, etc.), component composition, Tailwind CSS styling, Heroicons/Lucide icon integration, validation patterns, responsive design, and theming. Do not use for non-Livewire frameworks or non-component styling."
license: MIT
metadata:
  author: laravel
---

# x-ui. UI Development

## Documentation

Use `search-docs` for detailed x-ui. UI patterns and documentation.

## Basic Usage

This project uses the free edition of x-ui. UI, which includes all free components and variants but not Pro components.

x-ui. UI is a component library for Livewire built with Tailwind CSS. It provides components that are easy to use and customize.

Use x-ui. UI components when available. Fall back to standard Blade components when no x-ui. component exists for your needs.

<!-- Basic Button -->
```blade
<x-ui.button variant="primary">Click me</x-ui.button>
```

## Available Components (Free Edition)

Available: avatar, badge, brand, breadcrumbs, button, callout, checkbox, dropdown, field, heading, icon, input, modal, navbar, otp-input, profile, radio, select, separator, skeleton, switch, text, textarea, tooltip

## Icons

x-ui. includes [Heroicons](https://heroicons.com/) as its default icon set. Search for exact icon names on the Heroicons site - do not guess or invent icon names.

<!-- Icon Button -->
```blade
<x-ui.button icon="arrow-down-tray">Export</x-ui.button>
```

For icons not available in Heroicons, use [Lucide](https://lucide.dev/). Import the icons you need with the Artisan command:

```bash
php artisan x-ui.icon crown grip-vertical github
```

## Common Patterns

### Form Fields

<!-- Form Field -->
```blade
<x-ui.field>
    <x-ui.label>Email</x-ui.label>
    <x-ui.input type="email" wire:model="email" />
    <x-ui.error name="email" />
</x-ui.field>
```

### Modals

<!-- Modal -->
```blade
<x-ui.modal wire:model="showModal">
    <x-ui.heading>Title</x-ui.heading>
    <p>Content</p>
</x-ui.modal>
```

## Verification

1. Check component renders correctly
2. Test interactive states
3. Verify mobile responsiveness

## Common Pitfalls

- Trying to use Pro-only components in the free edition
- Not checking if a x-ui. component exists before creating custom implementations
- Forgetting to use the `search-docs` tool for component-specific documentation
- Not following existing project patterns for x-ui. usage