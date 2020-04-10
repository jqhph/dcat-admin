<?php

namespace Laravel\Dusk
{
    use Laravel\Dusk\Component;

    /**
     * @method $this extend(string|Component $selector, \Closure $callback)
     * @method $this whenTextAvailable(string $text, $callbackOrSeconds = null, int $seconds = null)
     * @method $this whenElementAvailable($selector, $callbackOrSeconds = null, int $seconds = null)
     * @method $this hasInput($field)
     * @method $this assertHidden($selector)
     * @method $this is(Component $component)
     * @method $this assertSeeTextIn(string $selector, string $text)
     * @method $this assertSeeText(string $text)
     * @method $this assertSeeInBody(string $text)
     * @method $this waitForTextInBody(string $text, int $seconds = null)
     * @method $this scrollToBottom()
     */
    class Browser
    {
    }
}
