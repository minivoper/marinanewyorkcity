<?php

namespace App\Cms\Types;

use Eshlink\Cms\Schema\Fields\Repeater;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Fields\Url;
use Eshlink\Cms\Schema\Schema;

/**
 * `/shop` — digital products, the merch teaser, and an affiliate section that
 * is still "coming soon".
 *
 * The two card grids are repeaters rather than three and four fixed slots, so
 * adding a preset pack is an edit rather than a deploy.
 */
class ShopType extends PageSingleton
{
    public function key(): string
    {
        return 'shop';
    }

    public function label(): string
    {
        return 'Shop page';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('heading')->required()->max(120),

            Text::make('digital_heading')->required()->max(120),
            Repeater::make('digital_products')->max(24)->of(Schema::make([
                Text::make('title')->required()->max(120),
                Text::make('image')->required()->max(255)
                    ->help('Path under public/, e.g. media/shop/example.jpg.'),
                Text::make('alt')->required()->max(255),
                Url::make('url')->required()->max(255),
            ])),
            Text::make('digital_cta_label')->required()->max(60),
            Url::make('digital_cta_url')->required()->max(255),

            Text::make('merch_heading')->required()->max(120),
            Text::make('merch_kicker')->max(255),
            Repeater::make('merch_images')->max(24)->of(Schema::make([
                Text::make('image')->required()->max(255),
                Text::make('alt')->required()->max(255),
            ])),
            Text::make('merch_cta_label')->required()->max(60),

            Text::make('affiliate_heading')->required()->max(120),
            Text::make('affiliate_kicker')->max(255),

            Text::make('seo_title')->required()->max(255),
            Textarea::make('seo_description')->required()->max(320)->rows(3),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'heading' => 'SHOP',

            'digital_heading' => 'Digital products',
            'digital_products' => [
                [
                    'title' => 'Lightroom Presets Collection',
                    'image' => 'media/shop/e628c7_fc77fbaabee5473a806e75cbba584320.jpg',
                    'alt' => 'Lightroom Presets Collection',
                    'url' => 'https://links.marinanewyorkcity.com/digitalproducts',
                ],
                [
                    'title' => 'Lightroom Presets Collections',
                    'image' => 'media/shop/e628c7_d414b7c345ed42e39c358fe73d43d22b.jpg',
                    'alt' => 'Lightroom Presets Collections',
                    'url' => 'https://links.marinanewyorkcity.com/digitalproducts',
                ],
                [
                    'title' => 'Free Screen Savers',
                    'image' => 'media/shop/e628c7_6775031bad1d46f7b776d8cfff12760b.jpg',
                    'alt' => 'Free Screen Savers, sunset by marinanewyorkcity',
                    'url' => 'https://links.marinanewyorkcity.com/digitalproducts',
                ],
            ],
            'digital_cta_label' => 'SHOP HERE',
            'digital_cta_url' => 'https://links.marinanewyorkcity.com/digitalproducts',

            'merch_heading' => 'New York Merch',
            'merch_kicker' => 'Keep or wear a piece of New York',
            'merch_images' => [
                [
                    'image' => 'media/shop/e628c7_9012ec656b1a4826b97e8ccaa8632aba.jpg',
                    'alt' => 'New York merch cup',
                ],
                [
                    'image' => 'media/shop/e628c7_8e5615aa3f2248c3ba809f16e50dd928.jpeg',
                    'alt' => 'New York merch ornament',
                ],
                [
                    'image' => 'media/shop/e628c7_470106c04e654b97b37fbf88d643dcc9.jpg',
                    'alt' => 'New York merch sticker',
                ],
                [
                    'image' => 'media/shop/e628c7_1bb8f279d4a640828c7f7452b4254701.jpg',
                    'alt' => 'New York merch hoodie',
                ],
            ],
            'merch_cta_label' => 'Explore',

            'affiliate_heading' => 'Affiliate links',
            'affiliate_kicker' => 'coming soon',

            'seo_title' => 'SHOP | marina.newyorkcity',
            'seo_description' => 'Digital products and New York merch by marina.newyorkcity.',
        ];
    }
}
