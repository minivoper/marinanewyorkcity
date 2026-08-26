<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
Route::get('/news', [PostController::class, 'index'])->defaults('type', 'news')->name('posts.news');
Route::get('/guides', [PostController::class, 'index'])->defaults('type', 'guide')->name('posts.guides');
Route::get('/post/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/event-list', [EventController::class, 'index'])->name('events.index');
Route::get('/event-details/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/news-and-press', [PageController::class, 'newsAndPress'])->name('pages.news-and-press');
Route::get('/travel-usa', [PageController::class, 'travelUsa'])->name('pages.travel-usa');
Route::get('/how-i-create', [PageController::class, 'howICreate'])->name('pages.how-i-create');
Route::get('/shop', [PageController::class, 'shop'])->name('pages.shop');
Route::get('/merch', [PageController::class, 'merch'])->name('pages.merch');
Route::get('/free-events', [PageController::class, 'freeEvents'])->name('pages.free-events');
Route::get('/accessibility-statement', [PageController::class, 'accessibility'])->name('pages.accessibility');

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::redirect('/blank', '/news-and-press', 301);
Route::redirect('/blank-2', '/travel-usa', 301);
Route::redirect('/blank-3', '/how-i-create', 301);
Route::redirect('/blank-4', '/shop', 301);
Route::redirect('/blank-5', '/contact', 301);
Route::redirect('/blank-6', '/free-events', 301);
Route::redirect('/blank-7', '/merch', 301);

Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/work-with-me', [PageController::class, 'work'])->name('pages.work');
Route::get('/press', [PageController::class, 'press'])->name('pages.press');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('seo.llms');
Route::get('/ai.txt', [SeoController::class, 'llms'])->name('seo.ai');

Route::get('/feed.xml', [FeedController::class, 'rss'])->name('feeds.rss');
Route::get('/feed.json', [FeedController::class, 'json'])->name('feeds.json');
