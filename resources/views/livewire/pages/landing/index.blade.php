<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.landing')]  class  extends Component
{
    public array $features = [];
    public array $testimonials = [];
    public array $faqs = [];

    public function mount()
    {
        $this->features = [
            [
                'icon' => 'o-building-office-2',
                'title' => __('messages.landing_page_feature1_title'),
                'description' => __('messages.landing_page_feature1_subtitle'),
            ],
            [
                'icon' => 'o-users',
                'title' => __('messages.landing_page_feature2_title'),
                'description' => __('messages.landing_page_feature2_subtitle'),
            ],
            [
                'icon' => 'o-credit-card',
                'title' => __('messages.landing_page_feature3_title'),
                'description' => __('messages.landing_page_feature3_subtitle'),
            ]
        ];

        $this->testimonials = [
            [
                'name' => 'Carlos Silva',
                'role' => __('messages.landing_page_testemonial1_role'),
                'content' => __('messages.landing_page_testemonial1_content'),
                'rating' => 5,
            ],
            [
                'name' => 'Ana Beatriz',
                'role' => __('messages.landing_page_testemonial2_role'),
                'content' => __('messages.landing_page_testemonial2_content'),
                'rating' => 5,
            ],
            [
                'name' => 'Roberto Mendes',
                'role' => __('messages.landing_page_testemonial3_role'),
                'content' => __('messages.landing_page_testemonial3_content'),
                'rating' => 5,
            ],
        ];

        $this->faqs = [
            [
                'question' => __('messages.landing_page_faq1_question'),
                'answer' => __('messages.landing_page_faq1_answer'),
            ],
            [
                'question' => __('messages.landing_page_faq2_question'),
                'answer' => __('messages.landing_page_faq2_answer'),
            ],
            [
                'question' => __('messages.landing_page_faq3_question'),
                'answer' => __('messages.landing_page_faq3_answer'),
            ],
        ];
    }

    public function demonstracao(): void
    {
        $user = User::where('email', 'demonstracao@aluguefacil.com')->first();

        Auth::login($user, true);
        redirect()->to('/dashboard');
    }
} ?>
<x-pages.layout>
    <section class="pt-10 px-4 text-center bg-gray-50 dark:bg-base-300 transition-colors duration-300">
        <div class="container mx-auto max-w-4xl">

            <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100 mb-6">
                {{__('messages.landing_page_title1')}} <br />

                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">
                    {{__('messages.landing_page_title2')}}
                </span>
            </h1>

            <p class="text-xl text-gray-600 dark:text-gray-400 mb-10 max-w-2xl mx-auto">
                {{__('messages.landing_page_subtitle')}}
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <x-mary-button
                    :label="__('messages.landing_page_start')"
                    icon-right="o-arrow-right"
                    class="btn-primary btn-lg rounded-full px-8" link="/dashboard" />
                <x-mary-button
                    :label="__('messages.landing_page_demo')"
                    wire:click="demonstracao"
                    icon="o-play-circle"
                    class="btn-outline btn-lg rounded-full px-8 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-800" />
            </div>
        </div>
    </section>

    <section class="pt-12 bg-gray-50 dark:bg-base-300 transition-colors duration-300">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{__('messages.landing_page_feature_section_title')}}</h2>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    {{__('messages.landing_page_feature_section_subtitle')}}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($features as $feature)
                <x-mary-card class="shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-base-100">
                    <div class="flex flex-col gap-4 p-2">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                            <x-mary-icon name="{{ $feature['icon'] }}" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 dark:text-gray-100">{{ $feature['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ $feature['description'] }}
                        </p>
                    </div>
                </x-mary-card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="pt-12 bg-gray-50 dark:bg-base-300 dark:border-gray-800 transition-colors duration-300">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-gray-100 mb-8">{{__('messages.landing_page_testemonials_section_title')}}</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($testimonials as $item)
                <x-mary-card class="bg-gray-50 dark:bg-base-100 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <div class="flex flex-col gap-4 p-2">
                        <div class="flex gap-1">
                            @for($i = 0; $i
                            < 5; $i++)
                                <x-mary-icon name="s-star" class="w-4 h-4 text-yellow-500" />
                            @endfor
                        </div>

                        <p class="text-gray-700 dark:text-gray-300 italic">"{{ $item['content'] }}"</p>

                        <div class="flex items-center gap-3 mt-2">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center font-bold text-blue-600 dark:text-blue-300">
                                {{ substr($item['name'], 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-sm text-gray-900 dark:text-gray-100">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['role'] }}</p>
                            </div>
                        </div>
                    </div>
                </x-mary-card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-10 bg-gray-50 dark:bg-base-300 dark:border-gray-800 transition-colors duration-300">
        <div class="container mx-auto px-4 max-w-3xl">
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-gray-100 mb-10">{{__('messages.landing_page_faq_section_subtitle')}}</h2>

            <div class="flex flex-col gap-4">
                @foreach($faqs as $faq)

                <div class="bg-gray-50 dark:bg-base-100 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <x-mary-collapse class="bg-gray-50 dark:bg-base-100 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-900 dark:text-gray-100">

                        <x-slot:heading>
                            {{ $faq['question'] }}
                        </x-slot:heading>

                        <x-slot:content>
                            <p class="text-gray-600 text-md dark:text-gray-300 px-4 pb-4">
                                {{ $faq['answer'] }}
                            </p>
                        </x-slot:content>
                    </x-mary-collapse>
                </div>
                @endforeach
            </div>

        </div>
    </section>
</x-pages.layout>