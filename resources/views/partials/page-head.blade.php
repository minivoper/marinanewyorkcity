{{--
    The shared opening for every page that is not a full-bleed hero.

    This partial is the whole answer to "the pages do not look related". Before
    it, each template invented its own top: different measure, different title
    size, some with a kicker and some without. Now one include takes an eyebrow,
    a title, an optional right-hand meta line and an optional kicker, and every
    page starts the same way.

        @include('partials.page-head', [
            'eyebrow' => 'Journal',
            'meta' => $posts->total().' stories',
            'title' => "WHAT'S GOING ON",
            'kicker' => 'New York News and events',
        ])
--}}
<header class="page-head">
    <div class="wrap">
        <div class="page-head-top">
            <p class="eyebrow">{{ $eyebrow }}</p>
            <span class="rule" aria-hidden="true"></span>
            @isset($meta)
                <p class="eyebrow eyebrow--muted">{{ $meta }}</p>
            @endisset
        </div>
        <div class="mask">
            <h1 class="display t-h1 page-head-title @isset($sentence) display--sentence @endisset">{{ $title }}</h1>
        </div>
        @isset($kicker)
            <p class="kicker">{{ $kicker }}</p>
        @endisset
    </div>
</header>
