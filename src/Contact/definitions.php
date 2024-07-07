<?php

use App\Contact\ContactAction;
use function DI\{autowire, get};

return [
    'contact.to' => get('mail.to'),
    ContactAction::class => autowire()->constructorParameter('to', get('contact.to'))
];
