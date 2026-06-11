---
title: "Laravel 13 Meets PHP 8.4: The Ultimate Guide to Attributes"
excerpt: "Say goodbye to boilerplate. Discover how Laravel 13 leverages PHP 8.4 attributes to make your codebase cleaner, faster, and more beautiful."
category: "Tech"
tags:
  - "laravel 13"
  - "php 8.4"
  - "attributes"
  - "clean code"
author: "Zicify Insider"
seo.title: "Laravel 13 and PHP 8.4 Attributes: A Complete Guide"
seo.description: "Master Laravel 13's PHP 8.4 attributes. Learn how to drastically reduce boilerplate using native attributes for routing, models, DI, and more."
---

# Laravel 13 Meets PHP 8.4: The Ultimate Guide to Attributes

Hey fellow artisans! 👋

If you've been following the evolution of Laravel over the past few years, you know that Taylor and the core team have been on an absolute mission to strip away boilerplate. With the release of **Laravel 13** and the widespread adoption of **PHP 8.4**, we're seeing the full realization of that dream: native PHP Attributes.

Gone are the days of massive `$route` registries, clunky middleware declarations in constructors, and verbose dependency injection setups. Today, we're diving deep into the complete list of Laravel 13 attributes and exactly how they function. Grab a coffee, and let's get into it.

> [!TIP]
> **Why Attributes?**
> Attributes allow you to add metadata to classes, methods, and properties *right where they are defined*. It keeps your related logic tightly coupled visually, without bleeding into massive config files.

---

## 🚦 Routing Attributes

Routing attributes completely change the game. Instead of registering routes in `web.php` or `api.php`, you can map them directly on your controllers!

### `#[Get]`, `#[Post]`, `#[Put]`, `#[Patch]`, `#[Delete]`
These are the bread and butter. They map an HTTP verb and a URI directly to your controller method.

```php
use Illuminate\Routing\Attributes\Get;

class UserController extends Controller
{
    #[Get('/users/{user}')] // [tl! highlight]
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}
```

### `#[Middleware]` & `#[WithoutMiddleware]`
Remember adding `$this->middleware('auth')` inside your controller's `__construct()`? That's old news. Now, you apply middleware exactly where you need it.

```php
use Illuminate\Routing\Attributes\Middleware;

#[Middleware('auth')] // [tl! highlight]
class DashboardController extends Controller
{
    // Every method here is protected by 'auth'
}
```

---

## 💉 Dependency Injection & Container Attributes

### `#[Autowire]`
Need to inject a specific implementation or a configuration value directly into your class? `#[Autowire]` is your best friend. It saves you from writing complex service provider bindings.

```php
use Illuminate\Container\Attributes\Autowire;

class PaymentService
{
    public function __construct(
        #[Autowire(config: 'services.stripe.key')] // [tl! highlight]
        private string $stripeKey
    ) {}
}
```

### `#[CurrentUser]`
This is a personal favorite. Inject the currently authenticated user directly into your controller method or service class without needing to call `Auth::user()` or `$request->user()`.

```php
use Illuminate\Container\Attributes\CurrentUser;

public function profile(#[CurrentUser] User $user) // [tl! highlight]
{
    return view('profile', ['user' => $user]);
}
```

---

## 🗄️ Eloquent Models & Database

Laravel 13 brings attributes to the data layer, making model configuration a breeze.

### `#[Observe]`
Linking Observers to Models used to require jumping into an `EventServiceProvider`. Now? Just tag the Observer class with the Model it watches.

```php
use Illuminate\Database\Eloquent\Attributes\Observe;

#[Observe(User::class)] // [tl! highlight]
class UserObserver
{
    public function created(User $user)
    {
        // Fire off welcome email
    }
}
```

### `#[ScopedBy]`
Adding Global Scopes to a model is now incredibly elegant. No more overriding the `booted()` method just to add a scope.

```php
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy(ActiveScope::class)] // [tl! highlight]
class Post extends Model
{
    // This model is now automatically scoped to only active posts.
}
```

---

## 🛠️ Console & Artisan

### `#[AsCommand]`
When writing custom Artisan commands, you used to define the signature and description as protected properties. Now, they are attributes mapped onto the class itself.

```php
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'emails:send', description: 'Send out marketing emails')] // [tl! highlight]
class SendEmailsCommand extends Command
{
    public function handle()
    {
        // 
    }
}
```

---

## The Verdict

Laravel 13's embrace of PHP 8.4 attributes isn't just syntactic sugar—it's a fundamental shift in how we structure our applications. By collocating metadata with the actual implementation, our code becomes infinitely more readable and easier to maintain.

Are you already using these in your projects? Drop a comment below and let us know. Happy coding!

