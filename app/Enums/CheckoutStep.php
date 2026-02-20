<?php

namespace App\Enums;

enum CheckoutStep: int
{
    case PersonalAddress = 1;
    case Delivery = 2;
    case Payment = 3;
}
