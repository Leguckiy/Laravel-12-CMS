<?php

namespace App\Support;

use App\Models\Language;
use App\Models\User;

class AdminContext
{
    public User $user;

    public Language $language;

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }
}
