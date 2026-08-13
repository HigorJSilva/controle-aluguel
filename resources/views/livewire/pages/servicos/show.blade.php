<?php

use App\Traits\ClearsFilters;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;


new class extends Component {
    use Toast;
    use WithPagination;
    use ClearsFilters;

}; ?>


<x-pages.layout :page-title="__('messages.payment_index_title')" :subtitle="__('messages.payment_index_subtitle')">
    
</x-pages.layout>

