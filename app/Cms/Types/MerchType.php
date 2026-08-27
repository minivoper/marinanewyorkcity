<?php

namespace App\Cms\Types;

use Eshlink\Cms\Schema\Fields\Repeater;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Fields\Url;
use Eshlink\Cms\Schema\Schema;

/**
 * `/merch` — prints on one storefront, physical merch on another.
 */
class MerchType extends PageSingleton
{
    public function key(): string
    {
        return 'merch';
    }

    public function label(): string
    {
        return 'Merch';
    }

    public function blurb(): ?string
    {
        return 'Prints and products.';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('heading')->required()->max(120),

            Text::make('prints_heading')->required()->max(120),
            Text::make('prints_cta_label')->required()->max(60),
            Url::make('prints_cta_url')->required()->max(255),

            Text::make('merch_heading')->required()->max(120),
            Repeater::make('merch_products')->max(24)->of(Schema::make([
                Text::make('title')->required()->max(120),
                Text::make('image')->required()->max(255)
                    ->help('Path under public/, e.g. media/shop/example.jpeg.'),
                Text::make('alt')->required()->max(255),
            ])),
            Text::make('merch_cta_label')->required()->max(60),
            Url::make('merch_cta_url')->required()->max(255),

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
            'heading' => 'Merch and Prints',

            'prints_heading' => 'Prints',
            'prints_cta_label' => 'SHOP HERE',
            'prints_cta_url' => 'https://pixels.com/profiles/marina-newyorkcity/shop',

            'merch_heading' => 'Merch',
            'merch_products' => [
                [
                    'title' => 'Scented Soy Candle',
                    'image' => 'media/shop/e628c7_12ac5abb6e1c4f14a935eac26e4e6719.jpeg',
                    'alt' => 'Scented Soy Candle',
                ],
                [
                    'title' => 'Festive Christmas Ball Ornament',
                    'image' => 'media/shop/e628c7_8e5615aa3f2248c3ba809f16e50dd928.jpeg',
                    'alt' => 'Festive Christmas Ball Ornament',
                ],
                [
                    'title' => 'Glass Ornaments',
                    'image' => 'media/shop/e628c7_96a036a3e2b94b34bd139c78a1dceb3f.jpeg',
                    'alt' => 'Glass Ornaments',
                ],
            ],
            'merch_cta_label' => 'SHOP HERE',
            'merch_cta_url' => 'https://marinanewyorkcity.printify.me/',

            'seo_title' => 'Merch and Prints | marina.newyorkcity',
            'seo_description' => 'New York prints and merch by marina.newyorkcity.',
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    public function publicPath(array $entry): ?string
    {
        return '/merch';
    }
}
