<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $description }}">
<meta name="author" content="Marina Kapler">
@unless ($canonicalSuppressed)
<link rel="canonical" href="{{ $canonicalUrl }}">
<link rel="alternate" hreflang="en-US" href="{{ $canonicalUrl }}">
@endunless
<meta property="og:site_name" content="marina.newyorkcity">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $description }}">
@unless ($canonicalSuppressed)
<meta property="og:url" content="{{ $canonicalUrl }}">
@endunless
<meta property="og:image" content="{{ $imageUrl }}">
@if ($imageIsDefault)
{{-- Only the default card is known to be 1200x630. A post or event supplies its
     own cover at whatever size it happens to be, and declaring the wrong
     dimensions renders worse than declaring none. --}}
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $imageUrl }}">
