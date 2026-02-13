<?php

namespace App\Support;

use App\Models\Cart;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Support\Collection;

class FrontContext
{
    public ?Language $language = null;

    public ?Currency $currency = null;

    public ?Collection $languages = null;

    public ?Collection $currencies = null;

    public ?Cart $cart = null;

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

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setLanguages(Collection $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getLanguages(): Collection
    {
        return $this->languages ?? collect();
    }

    public function setCurrencies(Collection $currencies): self
    {
        $this->currencies = $currencies;

        return $this;
    }

    public function getCurrencies(): Collection
    {
        return $this->currencies ?? collect();
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }
}
