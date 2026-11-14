<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoOptimizationTest extends TestCase
{
    /**
     * Memastikan robots.txt publik menyertakan sitemap dan mengizinkan perayapan landing page.
     */
    public function test_robots_txt_allows_public_and_disallows_private(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringContainsString('Allow: /', $content);
        $this->assertStringContainsString('Disallow: /admin', $content);
        $this->assertStringContainsString('Disallow: /absensi', $content);
        $this->assertStringContainsString('Disallow: /p/', $content);
        $this->assertStringContainsString('sitemap.xml', $content);
    }

    /**
     * Memastikan endpoint /sitemap.xml menghasilkan XML yang valid dengan URL kanonikal publik.
     */
    public function test_sitemap_xml_renders_valid_structure(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        
        $content = $response->getContent();
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString(route('landing'), $content);
        $this->assertStringContainsString(route('legal.privacy'), $content);
        $this->assertStringContainsString(route('legal.terms'), $content);
        // Pastikan halaman admin atau token privat tidak bocor ke sitemap
        $this->assertStringNotContainsString('/admin', $content);
        $this->assertStringNotContainsString('/absensi', $content);
        $this->assertStringNotContainsString('/p/', $content);
    }

    /**
     * Memastikan Landing Page menyertakan SEO Meta Tags dan Schema.org JSON-LD lengkap.
     */
    public function test_landing_page_has_meta_tags_and_structured_data(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        
        // Header X-Robots-Tag untuk halaman publik harus mengizinkan index
        $this->assertStringContainsString('index, follow', (string) $response->headers->get('X-Robots-Tag'));

        // Meta tags & Canonical
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<link rel="canonical" href="' . route('landing') . '"', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('<meta name="twitter:card"', false);

        // Schema.org JSON-LD
        $response->assertSee('"@context": "https://schema.org"', false);
        $response->assertSee('"@type": "EducationalOrganization"', false);
        $response->assertSee('"@type": "SoftwareApplication"', false);
        $response->assertSee('"@type": "WebSite"', false);
    }

    /**
     * Memastikan Halaman Kebijakan Privasi menyertakan Breadcrumb Schema.org.
     */
    public function test_privacy_page_has_breadcrumb_schema(): void
    {
        $response = $this->get('/kebijakan-privasi');

        $response->assertStatus(200);
        $this->assertStringContainsString('index, follow', (string) $response->headers->get('X-Robots-Tag'));
        $response->assertSee('<link rel="canonical" href="' . route('legal.privacy') . '"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    /**
     * Memastikan Halaman Syarat & Ketentuan menyertakan Breadcrumb Schema.org.
     */
    public function test_terms_page_has_breadcrumb_schema(): void
    {
        $response = $this->get('/syarat-ketentuan');

        $response->assertStatus(200);
        $this->assertStringContainsString('index, follow', (string) $response->headers->get('X-Robots-Tag'));
        $response->assertSee('<link rel="canonical" href="' . route('legal.terms') . '"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    /**
     * Memastikan halaman privat (seperti login admin) tetap memiliki header X-Robots-Tag noindex.
     */
    public function test_private_admin_login_retains_noindex(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $this->assertStringContainsString('noindex', (string) $response->headers->get('X-Robots-Tag'));
    }
}
