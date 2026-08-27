<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Page;
use App\Models\Post;
use Eshlink\Cms\Services\EntryService;
use Eshlink\Cms\Support\TypeRegistry;
use Eshlink\Cms\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * Everything this site ships with has to survive the gate this site writes
 * through.
 *
 * The bug this guards against is quiet and unpleasant to meet. Content arrives
 * from the seeders and the importer, and the editorial rules arrive from the
 * type declarations, and nothing checks that the two agree. When they do not,
 * Marina finds out by opening a page she has never touched, pressing Save, and
 * being refused over a dash somebody else typed into a meta title in 2024. The
 * page is readable, it is live, and it is not editable, which is the worst of
 * the three states a page can be in.
 *
 * So: seed the site the way a fresh install seeds it, then put every record
 * back through the validator exactly as a save would. Nothing is written and
 * nothing is changed — the question is only whether it *could* be.
 */
class SeededContentPassesHouseStyleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_every_seeded_record_could_be_saved_again_untouched(): void
    {
        $this->seedTheWholeSite();

        $validator = app(Validator::class);
        $entries = app(EntryService::class);
        $checked = 0;

        foreach (app(TypeRegistry::class)->all() as $type) {
            foreach ($entries->list($type) as $entry) {
                $checked++;

                $result = $validator->validateSave($type, $entry['data'], [
                    'entry_id' => $entry['id'],
                    'locale' => $entry['locale'],
                ]);

                $this->assertFalse($result->fails(), $this->refusal($type->key(), $entry['id'], $result->errors()));
            }
        }

        $this->assertGreaterThan(0, $checked, 'Nothing was seeded, so this test proved nothing.');
    }

    /**
     * The same question at the stricter gate, asked only of the records that
     * are already public. A published entry that cannot be re-published is a
     * page Marina can take down but not put back.
     */
    public function test_every_published_record_could_be_published_again(): void
    {
        $this->seedTheWholeSite();

        $validator = app(Validator::class);
        $entries = app(EntryService::class);

        foreach (app(TypeRegistry::class)->all() as $type) {
            foreach ($entries->list($type) as $entry) {
                if ($entry['status'] !== 'published') {
                    continue;
                }

                $result = $validator->validatePublish($type, $entry['data'], [
                    'entry_id' => $entry['id'],
                    'locale' => $entry['locale'],
                ]);

                $this->assertFalse($result->fails(), $this->refusal($type->key(), $entry['id'], $result->errors()));
            }
        }
    }

    /**
     * The eight values that actually shipped, and the sentences they have to
     * become.
     *
     * Written out rather than derived, because the only question worth asking
     * about a mechanical correction is whether a person would have written what
     * came out of it. "Privacy Policy , marina.newyorkcity" contains no dash
     * and is still not a title anyone would put on a page.
     *
     * @return array<string, array{model: class-string<Model>, id: int, field: string, before: string, after: string}>
     */
    public static function importedCopy(): array
    {
        return [
            'privacy policy meta title' => [
                'model' => Page::class, 'id' => 1, 'field' => 'meta_title',
                'before' => "Privacy Policy \u{2014} marina.newyorkcity",
                'after' => 'Privacy Policy, marina.newyorkcity',
            ],
            'terms meta title' => [
                'model' => Page::class, 'id' => 2, 'field' => 'meta_title',
                'before' => "Terms and Conditions \u{2014} marina.newyorkcity",
                'after' => 'Terms and Conditions, marina.newyorkcity',
            ],
            'about meta title' => [
                'model' => Page::class, 'id' => 3, 'field' => 'meta_title',
                'before' => "About Marina Kapler \u{2014} NYC Content Creator",
                'after' => 'About Marina Kapler, NYC Content Creator',
            ],
            'work with me meta title' => [
                'model' => Page::class, 'id' => 4, 'field' => 'meta_title',
                'before' => "Work with Marina Kapler \u{2014} NYC Brand Storytelling",
                'after' => 'Work with Marina Kapler, NYC Brand Storytelling',
            ],
            'press meta title' => [
                'model' => Page::class, 'id' => 5, 'field' => 'meta_title',
                'before' => "Press \u{2014} Marina Kapler and marina.newyorkcity",
                'after' => 'Press, Marina Kapler and marina.newyorkcity',
            ],
            'west side fest excerpt' => [
                'model' => Post::class, 'id' => 2, 'field' => 'excerpt',
                'before' => "West Side Fest returns July 10\u{2013}12, 2026 with free artmaking, workshops, "
                    ."performances, dancing, crafts, and special programs across Manhattan\u{2019}s West Side "
                    .'cultural institutions.',
                'after' => 'West Side Fest returns July 10 to 12, 2026 with free artmaking, workshops, '
                    ."performances, dancing, crafts, and special programs across Manhattan\u{2019}s West Side "
                    .'cultural institutions.',
            ],
            'west side fest meta description' => [
                'model' => Post::class, 'id' => 2, 'field' => 'meta_description',
                'before' => "Explore West Side Fest in Manhattan from July 10\u{2013}12, 2026, with free "
                    .'cultural events, workshops, artmaking, and performances.',
                'after' => 'Explore West Side Fest in Manhattan from July 10 to 12, 2026, with free '
                    .'cultural events, workshops, artmaking, and performances.',
            ],
            'the knights meta title' => [
                'model' => Event::class, 'id' => 2, 'field' => 'meta_title',
                'before' => "The Knights at Bryant Park \u{2014} July 3, 2026",
                'after' => 'The Knights at Bryant Park, July 3, 2026',
            ],
        ];
    }

    /**
     * The blocker itself, reproduced and then closed.
     *
     * Every one of these was live on marina.newyorkcity when the dash rule was
     * switched on, and every one of them turned its record into a page that
     * could be read and not saved. They are written straight onto the rows here
     * rather than through the CMS, because that is how they got there: an
     * import does not go through the gate it predates.
     *
     * The date range is the one that earns its own line. "July 10–12" is not a
     * sentence with a dash in it, it is two dates and the word between them, so
     * a comma would say something false about when the festival is on.
     */
    public function test_the_imported_dashes_are_corrected_in_place(): void
    {
        $this->seed();
        $this->artisan('cms:install', ['--seed-defaults' => true])->assertSuccessful();

        foreach (self::importedCopy() as $case) {
            $case['model']::query()->whereKey($case['id'])->update([$case['field'] => $case['before']]);
        }

        $this->artisan('cms:normalize-content')->assertSuccessful();

        foreach (self::importedCopy() as $label => $case) {
            $this->assertSame(
                $case['after'],
                $case['model']::query()->whereKey($case['id'])->value($case['field']),
                $label.' was not corrected into the sentence it should have become.',
            );
        }

        // And the point of correcting them: the records are editable again.
        $this->artisan('cms:normalize-content')
            ->expectsOutputToContain('0 normalized')
            ->assertSuccessful();
    }

    /**
     * A first run, in the order a first run happens.
     *
     * Marina's own rows come from the seeders; the page singletons come from
     * the type defaults, which only `cms:install` writes. Both halves have to
     * be present or the sweep above misses whichever one is not.
     *
     * `cms:normalize-content` belongs in this sequence rather than beside it.
     * This content predates the rules: the bodies came out of Wix carrying
     * markup the field allowlist strips today, and the meta titles came out of
     * it carrying dashes house style refuses today. The cleanup is what closes
     * that gap, and a site that has not had it run is a site whose imported
     * pages cannot be saved. Asserting the sweep passes *without* it would be
     * asserting something this site has never been true of.
     */
    private function seedTheWholeSite(): void
    {
        $this->seed();

        $this->artisan('cms:install', ['--seed-defaults' => true])->assertSuccessful();
        $this->artisan('cms:normalize-content')->assertSuccessful();
    }

    /**
     * The refusal, spelled out. A bare "failed asserting that true is false"
     * would send whoever hits this reading the validator instead of reading
     * the sentence it already wrote about the value.
     *
     * @param  array<string, array<int, string>>  $errors
     */
    private function refusal(string $type, string $id, array $errors): string
    {
        $lines = [];

        foreach ($errors as $path => $messages) {
            $lines[] = sprintf('  %s: %s', $path, $messages[0] ?? '');
        }

        return sprintf("%s %s cannot be saved as it stands:\n%s", $type, $id, implode("\n", $lines));
    }
}
