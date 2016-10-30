<?php

namespace CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * LocaleListener constructor.
     *
     * @param string $defaultLocale
     */
    public function __construct(string $defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $acceptLanguage = ['fr', 'en'];

        $locale = $request->query->get('_locale', null);

        if (in_array($locale, $acceptLanguage)) {
            $this->defaultLocale = $locale;
        } else {
            $languages = $request->getLanguages();

            foreach ($languages as $language) {
                if (in_array($language, $acceptLanguage)) {
                    $this->defaultLocale = $language;
                    break;
                }
            }
        }

        $request->setLocale($this->defaultLocale);
    }
}
