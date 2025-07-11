<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatCard extends Component
{
    public string $title;
    public string $value;
    public string $iconClass; // Ex: 'text-blue-500'
    public string $iconPath;  // SVG path data

    /**
     * Create a new component instance.
     *
     * @param string $title
     * @param string $value
     * @param string $iconClass
     * @param string $iconPath
     */
    public function __construct(string $title, string $value, string $iconPath, string $iconClass = 'text-gray-500')
    {
        $this->title = $title;
        $this->value = $value;
        $this->iconPath = $iconPath;
        $this->iconClass = $iconClass;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.stat-card');
    }
}
