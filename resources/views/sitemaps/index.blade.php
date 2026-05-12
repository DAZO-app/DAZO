{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>{{ url('/sitemap/main.xml') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </sitemap>
    @foreach($circles as $circle)
    <sitemap>
        <loc>{{ url('/sitemap/circle-'.$circle->id.'.xml') }}</loc>
        <lastmod>{{ $circle->updated_at ? $circle->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
    </sitemap>
    @endforeach
</sitemapindex>
