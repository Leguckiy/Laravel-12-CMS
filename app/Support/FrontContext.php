<?php

namespace App\Support;

use App\Models\Currency;
use App\Models\Language;

class FrontContext
{
    public Language $language;
    public Currency $currency;

    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
