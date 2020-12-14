<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form\Field\HasMany;
use Tests\Models\Painter;
use Tests\Models\Painting;
use Tests\PHPUnit;

class PainterEditPage extends PainterCreatePage
{
    /**
     * @var \Tests\Models\Painter
     */
    protected $painter;

    public function __construct($model)
    {
        $this->painter = $model instanceof Painter ? $model : Painter::findOrFail($model);

        PHPUnit::assertTrue($this->painter->getKey() > 0);
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return admin_base_path("tests/painters/{$this->painter->getKey()}/edit");
    }

    public function assert(Browser $browser)
    {
        parent::assert($browser);

        $browser->with('@form', function (Browser $browser) {
            $browser->assertInputValue('username', $this->painter->username);
            $browser->assertInputValue('bio', $this->painter->bio);
            $browser->scrollToBottom();

            $browser->within(new HasMany('paintings'), function (Browser $browser) {
                $this->painter->paintings->each(function (Painting $painting, $key) use ($browser) {
                    $browser->withFormGroup($key + 1, function (Browser $browser) use ($painting) {
                        $browser->assertFormGroupInputValue('title', $painting->title, $painting->getKey());
                        $browser->assertFormGroupInputValue('body', $painting->body, $painting->getKey());
                        $browser->assertFormGroupInputValue('completed_at', $painting->completed_at, $painting->getKey());
                    });
                });
            });
        });
    }
}
