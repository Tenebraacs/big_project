<?php

use Livewire\Component;
use Livewire\WithFileUploads;


new class extends Component
{
     use WithFileUploads;

     public string $name ='';
     public string $description ='';
     public $picture = null;

    public function save()
    {
        
    }

};
?>

<div>
    {{-- An unexamined life is not worth living. - Socrates --}}
</div>