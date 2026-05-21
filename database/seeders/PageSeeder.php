<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'dev@gmail.com')->firstOrFail();

        $pages = [
            [
                'title'            => 'About',
                'slug'             => 'about',
                'excerpt'          => 'Learn more about Zicify and our web development services.',
                'content'          => '<h2>About Zicify</h2><p>Zicify is a web development services company dedicated to building fast, modern, and reliable web applications. We help businesses of all sizes establish a strong online presence through clean code and thoughtful design.</p><p>Our team brings deep expertise in Laravel, Livewire, and modern frontend technologies. Whether you need a new website, a custom web application, or ongoing development support, we deliver solutions that are built to last.</p><p>We believe great software starts with understanding your goals. That is why we work closely with every client from discovery through delivery, ensuring the final product truly fits their needs.</p>',
                'meta_description' => 'Zicify is a web development services company building fast, modern, and reliable web applications.',
                'status'           => PageStatus::Published,
                'published_at'     => now(),
            ],
            [
                'title'            => 'Privacy Policy',
                'slug'             => 'privacy-policy',
                'excerpt'          => 'How Zicify collects, uses, and protects your personal information.',
                'content'          => '<h2>Privacy Policy</h2><p>At Zicify, your privacy is important to us. This policy explains how we collect, use, and safeguard your personal information when you use our website or engage our web development services.</p><h3>Information We Collect</h3><p>We collect information you provide directly to us, such as your name, email address, and project details when you contact us or request a quote.</p><h3>How We Use Your Information</h3><p>We use the information we collect to respond to your enquiries, deliver our services, communicate project updates, and improve our website.</p><h3>Data Security</h3><p>Zicify implements appropriate technical and organisational measures to protect your personal information against unauthorised access, alteration, disclosure, or destruction.</p><h3>Third Parties</h3><p>We do not sell or share your personal information with third parties except where required to deliver our services or comply with legal obligations.</p><h3>Contact Us</h3><p>If you have any questions about this Privacy Policy, please reach out to us through our contact page.</p>',
                'meta_description' => 'Read Zicify\'s privacy policy to understand how we handle your personal data.',
                'status'           => PageStatus::Published,
                'published_at'     => now(),
            ],
            [
                'title'            => 'Terms of Service',
                'slug'             => 'terms-of-service',
                'excerpt'          => 'The terms and conditions governing your use of Zicify services.',
                'content'          => '<h2>Terms of Service</h2><p>By engaging Zicify for web development services or using our website, you agree to be bound by these Terms of Service. Please read them carefully.</p><h3>Services</h3><p>Zicify provides web development services including but not limited to website design, custom web application development, and ongoing maintenance. The scope of each engagement is defined in a separate project agreement.</p><h3>Intellectual Property</h3><p>Upon full payment, clients receive ownership of the custom code and assets developed specifically for their project. Zicify retains the right to use general frameworks, libraries, and tools that are not project-specific.</p><h3>Payment</h3><p>Payment terms are outlined in individual project agreements. Zicify reserves the right to pause or terminate work on projects with outstanding payments.</p><h3>Limitation of Liability</h3><p>To the fullest extent permitted by law, Zicify shall not be liable for any indirect, incidental, or consequential damages arising from the use of our services.</p><h3>Changes to Terms</h3><p>We reserve the right to update these terms at any time. Continued use of our services after changes constitutes acceptance of the revised terms.</p>',
                'meta_description' => 'Read Zicify\'s terms of service governing the use of our web development services.',
                'status'           => PageStatus::Published,
                'published_at'     => now(),
            ],
            [
                'title'            => 'Cookie Policy',
                'slug'             => 'cookie-policy',
                'excerpt'          => 'Information about how Zicify uses cookies on our website.',
                'content'          => '<h2>Cookie Policy</h2><p>This Cookie Policy explains how Zicify uses cookies and similar tracking technologies when you visit our website.</p><h3>What Are Cookies</h3><p>Cookies are small text files stored on your device when you visit a website. They help the site remember your preferences and improve your browsing experience.</p><h3>How We Use Cookies</h3><p>Zicify uses cookies to keep sessions active, remember your preferences, and understand how visitors use our site so we can improve it.</p><h3>Managing Cookies</h3><p>You can control and manage cookies through your browser settings. Please note that disabling certain cookies may affect the functionality of our website.</p>',
                'meta_description' => 'Learn how Zicify uses cookies and how you can manage your cookie preferences.',
                'status'           => PageStatus::Published,
                'published_at'     => now(),
            ],
        ];

        foreach ($pages as $data) {
            Page::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['user_id' => $admin->id])
            );
        }
    }
}
