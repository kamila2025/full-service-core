<?php

namespace App\Livewire;

use Livewire\Component;

class BaseComponent extends Component
{
  public function notify(string $status, string $message, ?string $redirect = null): void
  {
    $this->dispatch('notify', [
      'status'    => $status,
      'message'   => $message,
      'redirect'  => $redirect,
    ]);
  }
}
