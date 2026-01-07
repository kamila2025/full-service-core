<?php

namespace App\Livewire;

use App\Livewire\BaseComponent;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Home extends BaseComponent
{
  public function render()
  {
    return view('livewire.home');
  }
}
