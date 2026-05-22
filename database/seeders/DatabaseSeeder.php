<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\ProjectStatus;
use App\Enums\TeamMemberRole;
use App\Enums\TeamMemberStatus;
use App\Enums\TestimonialStatus;
use App\Models\Post;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Users ────────────────────────────────────────────────────────────
        $admin = User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $dev = User::factory()->create([
            'name'  => 'Dev',
            'email' => 'dev@gmail.com',
        ]);

        $member = User::factory()->create([
            'name'  => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        // ── Tags ─────────────────────────────────────────────────────────────
        $tagLaravel = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $tagVue     = Tag::create(['name' => 'Vue.js',  'slug' => 'vue-js']);

        // ── Projects ─────────────────────────────────────────────────────────
        $project1 = Project::create([
            'user_id'     => $admin->id,
            'title'       => 'Portfolio Platform',
            'description' => 'A full-featured portfolio and blog platform built with Laravel, Livewire, and x-ui. UI.',
            'status'      => ProjectStatus::Completed,
            'category'    => 'Web',
        ]);

        $project2 = Project::create([
            'user_id'     => $admin->id,
            'title'       => 'E-Commerce Dashboard',
            'description' => 'A real-time admin dashboard for managing orders, inventory, and customers.',
            'status'      => ProjectStatus::Completed,
            'category'    => 'SaaS',
        ]);

        // ── Posts ─────────────────────────────────────────────────────────────
        $post1 = Post::create([
            'user_id'      => $dev->id,
            'title'        => 'Getting Started with Livewire 4',
            'slug'         => 'getting-started-with-livewire-4',
            'excerpt'      => 'A practical introduction to building reactive UIs with Livewire 4 and single-file components.',
            'content'      => 'Livewire 4 introduces single-file components, islands, and async actions. In this post we walk through building your first component from scratch.',
            'status'       => PostStatus::Published,
            'featured'     => true,
            'view_count'   => 142,
            'published_at' => now()->subDays(3),
        ]);

        $post2 = Post::create([
            'user_id'      => $dev->id,
            'title'        => 'Tailwind CSS v4 — What Changed',
            'slug'         => 'tailwind-css-v4-what-changed',
            'excerpt'      => 'Tailwind v4 drops the config file and moves everything into CSS. Here is what you need to know.',
            'content'      => 'Tailwind CSS v4 is a ground-up rewrite. The config file is gone, replaced by @theme blocks in CSS. Utility generation is now on-demand and much faster.',
            'status'       => PostStatus::Published,
            'featured'     => false,
            'view_count'   => 89,
            'published_at' => now()->subDays(7),
        ]);

        $post1->tags()->sync([$tagLaravel->id, $tagVue->id]);
        $post2->tags()->sync([$tagVue->id]);

        // ── Team Members ──────────────────────────────────────────────────────
        TeamMember::create([
            'user_id'    => $admin->id,
            'project_id' => $project1->id,
            'role'       => TeamMemberRole::Lead,
            'status'     => TeamMemberStatus::Active,
        ]);

        TeamMember::create([
            'user_id'    => $member->id,
            'project_id' => $project1->id,
            'role'       => TeamMemberRole::Developer,
            'status'     => TeamMemberStatus::Active,
        ]);

        // ── Testimonials ──────────────────────────────────────────────────────
        Testimonial::create([
            'user_id'    => $member->id,
            'project_id' => $project1->id,
            'rating'     => 5,
            'comment'    => 'Exceptional work. The platform was delivered on time and exceeded every expectation we had.',
            'status'     => TestimonialStatus::Approved,
        ]);

        Testimonial::create([
            'user_id'    => $member->id,
            'project_id' => $project2->id,
            'rating'     => 4,
            'comment'    => 'Great dashboard, very clean UI. Would love to see more reporting features in the future.',
            'status'     => TestimonialStatus::Approved,
        ]);

        // ── Pages ─────────────────────────────────────────────────────────────
        $this->call(PageSeeder::class);
    }
}
