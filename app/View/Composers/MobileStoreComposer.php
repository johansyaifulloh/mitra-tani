<?php

namespace App\View\Composers;

use App\Support\TokoViewData;
use Illuminate\View\View;

class MobileStoreComposer
{
    public function compose(View $view): void
    {
        $view->with(TokoViewData::resolve());
    }
}
