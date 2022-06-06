<x-layout>
    <x-slot name="title">Sites</x-slot>
    <div id="site-list" class="list-group"></div>
    <template id="site-list-item">
        <a class="list-group-item list-group-item-action fs-2 d-flex justify-content-between">
            <div>
                <span class="site-name"></span>
            </div>
            <div>
                <span class="badge"></span>
            </div>
        </a>
    </template>
    <script src="{{ asset('/js/sites.js') }}"></script>
    <script>
        loadSites();
    </script>
</x-layout>
