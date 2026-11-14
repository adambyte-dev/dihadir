<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Hasilkan XML Sitemap dinamis sesuai standar Google Search Central (sitemaps.org).
     * Hanya memasukkan URL kanonikal publik yang diizinkan untuk diindeks.
     */
    public function index(): Response
    {
        $urls = [
            [
                'loc' => route('landing'),
                'lastmod' => '2026-11-14T20:00:00+07:00',
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('legal.privacy'),
                'lastmod' => '2026-11-14T20:00:00+07:00',
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('legal.terms'),
                'lastmod' => '2026-11-14T20:00:00+07:00',
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
        ];

        $content = view('sitemap.xml', compact('urls'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex, follow', // File sitemap sendiri tidak perlu terindeks di SERP
        ]);
    }
}
