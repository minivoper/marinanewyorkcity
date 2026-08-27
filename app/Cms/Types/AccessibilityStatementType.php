<?php

namespace App\Cms\Types;

use Eshlink\Cms\Rules\SafeHtml;
use Eshlink\Cms\Schema\Fields\RichText;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Textarea;
use Eshlink\Cms\Schema\Schema;

/**
 * `/accessibility-statement` — a legal-ish page that changes when the site
 * does, and that nobody should have to open a Blade file to amend.
 *
 * `sanitizeOnSave(false)` with `SafeHtml` attached is deliberate and applies to
 * every long-form field on this site. Sanitising on save rewrites what it
 * accepts — it HTML-encodes apostrophes and `@`, so `doesn't` and
 * `@marina.newyorkcity` come back as entities — which would change the bytes
 * this page already serves. Refusing markup by name instead gives the same
 * safety property (nothing outside the allowlist is ever stored) without the
 * CMS quietly rewriting copy behind the person who wrote it.
 */
class AccessibilityStatementType extends PageSingleton
{
    public function key(): string
    {
        return 'accessibility_statement';
    }

    public function label(): string
    {
        return 'Accessibility';
    }

    public function blurb(): ?string
    {
        return 'Your promise about who can use this site.';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('heading')->required()->max(120),
            RichText::make('body')->required()->sanitizeOnSave(false)->max(200000)
                ->allow(['p', 'br', 'strong', 'em', 'a', 'ul', 'ol', 'li', 'h2', 'h3'])
                ->governedBy(new SafeHtml(allowedTags: ['p', 'br', 'strong', 'em', 'a', 'ul', 'ol', 'li', 'h2', 'h3'])),
            Text::make('seo_title')->required()->max(255),
            Textarea::make('seo_description')->required()->max(320)->rows(3),
        ]);
    }

    /**
     * The literals this page's Blade template used to hold, indentation
     * included: the template supplies the first line's indent and the closing
     * newline, so every line after the first carries its own.
     *
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'heading' => 'ACCESSIBILITY STATEMENT',
            'body' => <<<'HTML'
<p>marinanewyorkcity.com</p>
                <p>Last updated: May 27, 2025</p>
                <p>We at Marina New York City are working to make our website, marinanewyorkcity.com, accessible to people with disabilities. We believe that the internet should be available and accessible to everyone, and we are committed to providing a website that is accessible to the widest possible audience, regardless of ability or technology.</p>

                <h2>What Web Accessibility is</h2>
                <p>An accessible website allows visitors with disabilities to browse the site with the same or a similar level of ease and enjoyment as other visitors. This can be achieved with the capabilities of the system on which the site is operating, and through assistive technologies such as screen readers, text enlargers, voice recognition software, and alternative input devices.</p>
                <p>We are committed to ensuring that our content is accessible to people with a wide range of disabilities, including visual, auditory, physical, speech, cognitive, and neurological disabilities.</p>

                <h2>Accessibility Adjustments on This Site</h2>
                <p>We have adapted this site in accordance with WCAG 2.1 guidelines and have made the site accessible to the AA level. This site's contents have been adapted to work with assistive technologies, such as screen readers and keyboard navigation. As part of this effort, we have:</p>
                <ul>
                    <li>Used the Wix Accessibility Wizard to identify and address potential accessibility issues across the site;</li>
                    <li>Set the language of the site to English so that assistive technologies can correctly interpret and present the content;</li>
                    <li>Set the content order of the site's pages to follow a logical and meaningful reading sequence;</li>
                    <li>Defined clear heading structures (H1, H2, H3) on all pages to allow users to navigate content efficiently;</li>
                    <li>Added descriptive alternative text to images throughout the site so that screen reader users can understand visual content;</li>
                    <li>Implemented color combinations that meet the required contrast ratio to ensure readability for users with low vision or color blindness;</li>
                    <li>Reduced the use of motion and auto-playing animations on the site to minimize discomfort for users with vestibular or cognitive sensitivities;</li>
                    <li>Ensured that all videos, audio content, and downloadable files on the site are accessible, including the use of captions or transcripts where applicable;</li>
                    <li>Ensured that all interactive elements, including navigation menus, buttons, forms, and links, are accessible via keyboard without requiring a mouse;</li>
                    <li>Designed contact and comment forms with clearly labeled fields and accessible error messaging.</li>
                </ul>

                <h3>Declaration of Partial Compliance Due to Third-Party Content</h3>
                <p>The accessibility of certain pages and features on this site may depend on content or tools that do not belong to Marina New York City, but instead belong to third-party providers. The following areas may be affected:</p>
                <ul>
                    <li>External store links: pages or widgets linking to third-party retailers operate under those retailers' own platforms and accessibility standards, which are outside our control;</li>
                    <li>Affiliate links and tracking tools: affiliate network platforms and their embedded tracking technologies are operated by third parties and may not fully conform to WCAG 2.1 AA;</li>
                    <li>Embedded media: any video content hosted on third-party platforms (such as YouTube or Vimeo) is subject to those platforms' own accessibility implementations;</li>
                    <li>User-generated content: comments submitted by users are posted as-is and may not always meet full accessibility standards.</li>
                </ul>
                <p>We therefore declare partial compliance with WCAG 2.1 AA for pages and features that rely on third-party content or technology. We are committed to working with our third-party providers to improve accessibility where possible</p>

                <h2>Our Ongoing Commitment</h2>
                <p>We view web accessibility as an ongoing effort and are committed to continuously improving the user experience for all visitors. We regularly review our site against accessibility standards and implement improvements as new content is added or the site is updated.</p>
                <p>If you use assistive technology and experience difficulty accessing any part of marinanewyorkcity.com, we want to hear from you so we can make improvements. Your feedback is important to us and helps us prioritize accessibility enhancements.</p>

                <h2>Requests, Issues, and Suggestions</h2>
                <p>If you find an accessibility issue on the site, encounter a barrier to access, or would like to request information in an alternative accessible format, you are welcome to contact our accessibility coordinator through the contact form available on the Website at marinanewyorkcity.com.</p>
                <p>Please include the following information in your message so we can assist you effectively:</p>
                <ul>
                    <li>A description of the accessibility issue or barrier you encountered;</li>
                    <li>The URL or page name where the issue occurred;</li>
                    <li>The assistive technology or browser you were using, if applicable;</li>
                    <li>Your preferred method of contact and, if relevant, your preferred accessible format for a response.</li>
                </ul>
                <p>We aim to respond to all accessibility inquiries within 5 business days and will make every reasonable effort to provide the requested information or resolve the issue in a timely manner.</p>
HTML,
            'seo_title' => 'ACCESSIBILITY STATEMENT | marina.newyorkcity',
            'seo_description' => 'Accessibility statement for marinanewyorkcity.com.',
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    public function publicPath(array $entry): ?string
    {
        return '/accessibility-statement';
    }
}
