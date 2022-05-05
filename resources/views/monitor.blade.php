<x-layout>
    <x-slot name="title">Alive Logs</x-slot>
    <input id="site" type="hidden" value="{{ $site }}" />
    <script src="{{ asset('/js/monitor.js') }}"></script>

    <!-- Latest status -->
    <h3><span id="status-badge" class="badge"></span> {{ $site }}</h3>
    <div id="alert-messages" class="alert alert-danger invisible" role="alert"></div>
    <script>
        loadLatestStatus();
    </script>

    <!-- History -->
    <table id="alive-logs" class="table" style="border-collapse: separate; border-spacing: 0;">
        <thead>
            <tr class="sticky-top bg-light">
                <th scope="col">Status</th>
                <th scope="col">Logged at</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <template id="site-status-row">
        <tr>
            <td class="logged-status"></td>
            <td class="logged-at">
                <time datetime=""></time>
            </td>
            <td>
                <time class="from-now" datetime=""></time>
            </td>
        </tr>
    </template>
    <script>
        loadHistory();
    </script>
</x-layout>
