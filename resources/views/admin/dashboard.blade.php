@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-6">
        <div>
            <p class="eyebrow">Admin workspace</p>
            <h1 class="mt-3 font-display text-4xl font-extrabold tracking-tight text-coffee-espresso">Dashboard</h1>
            <p class="mt-2 max-w-xl text-coffee-mocha">Keep the coffee catalog accurate, useful, and ready to brew.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.beans.create') }}" class="inline-flex min-h-11 items-center rounded-xl bg-coffee-espresso px-4 text-sm font-semibold text-coffee-latte transition-[background-color,transform] duration-150 ease-out hover:bg-coffee-roast motion-safe:active:scale-[0.96] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber">Add bean</a>
            <a href="{{ route('admin.recipes.create') }}" class="inline-flex min-h-11 items-center rounded-xl bg-coffee-amber px-4 text-sm font-semibold text-coffee-espresso transition-[background-color,transform] duration-150 ease-out hover:bg-coffee-amber/80 motion-safe:active:scale-[0.96] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber-ink">Add recipe</a>
        </div>
    </div>

    <div class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" data-reveal-stagger>
        @foreach ([
            ['label' => 'Beans', 'value' => $counts['beans'], 'detail' => $counts['beans_regular'].' regular · '.$counts['beans_cheap'].' cheap', 'route' => route('admin.beans.index')],
            ['label' => 'Recipes', 'value' => $counts['recipes'], 'detail' => $counts['recipes_featured'].' featured · '.$counts['recipes_cheap'].' cheap', 'route' => route('admin.recipes.index')],
            ['label' => 'Brew methods', 'value' => $counts['methods'], 'detail' => $counts['methods_regular'].' regular · '.$counts['methods_cheap'].' cheap', 'route' => route('admin.methods.index')],
            ['label' => 'Categories', 'value' => $counts['categories'], 'detail' => 'Bean classifications', 'route' => route('admin.categories.index')],
        ] as $stat)
            <a href="{{ $stat['route'] }}" class="group rounded-2xl border border-coffee-mocha/20 bg-coffee-cream p-5 transition-[border-color,transform,box-shadow] duration-150 ease-out hover:-translate-y-0.5 hover:border-coffee-amber hover:shadow-[0_10px_24px_rgba(44,26,20,0.10)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber">
                <p class="eyebrow">{{ $stat['label'] }}</p>
                <p class="mt-4 font-display text-4xl font-bold tabular-nums text-coffee-espresso">{{ $stat['value'] }}</p>
                <p class="mt-2 text-sm text-coffee-mocha">{{ $stat['detail'] }}</p>
            </a>
        @endforeach
    </div>

    <section class="mt-8 rounded-2xl bg-coffee-roast p-6 text-coffee-latte sm:p-8" aria-labelledby="cheap-page-title">
        <div class="flex flex-wrap items-end justify-between gap-5">
            <div>
                <p class="eyebrow text-coffee-amber">Public collection</p>
                <h2 id="cheap-page-title" class="mt-2 font-display text-2xl font-bold">Cheap page</h2>
                <p class="mt-2 text-sm text-coffee-latte/75">{{ $counts['beans_cheap'] }} products · {{ $counts['methods_cheap'] }} methods · {{ $counts['recipes_cheap'] }} recipes</p>
            </div>
            <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold">
                <a href="{{ route('admin.beans.index') }}?filter=cheap" class="text-coffee-amber underline-offset-4 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber">Manage products</a>
                <a href="{{ route('cheap.index') }}" target="_blank" rel="noopener" class="text-coffee-latte underline-offset-4 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coffee-amber">View page ↗</a>
            </div>
        </div>
    </section>

    <div class="mt-10 grid gap-8 xl:grid-cols-2">
        <x-admin-recent-table title="Recent recipes" view-route="admin.recipes.index">
            <x-slot:headers><th>Title</th><th>Pairing</th><th>Tags</th><th class="text-right">Action</th></x-slot:headers>
            @forelse ($recentRecipes as $recipe)
                <tr class="transition-colors hover:bg-coffee-cream/60">
                    <td class="px-4 py-3 font-medium">{{ $recipe->title }}</td>
                    <td class="px-4 py-3 text-coffee-mocha">{{ $recipe->coffeeBean->name ?? '—' }} · {{ $recipe->brewMethod->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs font-semibold">{{ $recipe->cheap ? 'Cheap' : ($recipe->is_featured ? 'Featured' : '—') }}</td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('admin.recipes.edit', $recipe) }}" class="font-semibold text-coffee-amber-ink hover:underline">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-coffee-mocha">No recipes yet.</td></tr>
            @endforelse
        </x-admin-recent-table>

        <x-admin-recent-table title="Recent beans" view-route="admin.beans.index">
            <x-slot:headers><th>Name</th><th>Category</th><th>Origin</th><th class="text-right">Action</th></x-slot:headers>
            @forelse ($recentBeans as $bean)
                <tr class="transition-colors hover:bg-coffee-cream/60">
                    <td class="px-4 py-3 font-medium">{{ $bean->name }}</td>
                    <td class="px-4 py-3 text-coffee-mocha">{{ $bean->category->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-coffee-mocha">{{ $bean->origin_country }}</td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('admin.beans.edit', $bean) }}" class="font-semibold text-coffee-amber-ink hover:underline">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-coffee-mocha">No beans yet.</td></tr>
            @endforelse
        </x-admin-recent-table>
    </div>
@endsection
