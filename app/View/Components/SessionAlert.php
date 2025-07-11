<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SessionAlert extends Component
{
    public ?string $successMessage;
    public ?string $errorMessage;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->successMessage = session('success');
        $this->errorMessage = session('error');
    }

    /**
     * Determine if the component should render.
     *
     * @return bool
     */
    public function shouldRender(): bool
    {
        return !empty($this->successMessage) || !empty($this->errorMessage);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.session-alert');
    }
}
