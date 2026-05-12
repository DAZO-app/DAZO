{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/front?circle='.$circle->id) }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @foreach($decisions as $decision)
    <url>
        <loc>{{ url('/front/decision/'.$decision->id) }}</loc>
        <lastmod>{{ $decision->updated_at ? $decision->updated_at->toAtomString() : $decision->created_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
</urlset>
