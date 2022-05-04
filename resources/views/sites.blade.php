<x-layout>
    <x-slot name="title">Sites</x-slot>
    <div id="site-list" class="list-group"></div>
    <template id="site-list-item">
        <a class="list-group-item list-group-item-action fs-2 d-flex justify-content-between">
            <div>
                <span class="status"></span>
            </div>
            <div>
                <span class="badge"></span>
            </div>
        </a>
    </template>
    <script>
        const sites = [];
        function reloadStatus() {
            if (!sites.length) {
                return;
            }
            const interval = (30_000 / sites.length);
            const site = sites.shift();
            sites.push(site);
            axios.get(`/api/alive-log/${site}/status`).then((response) => {
                const status = response.data.status.toUpperCase();
                const badge = document.querySelector(`#site-${site} .badge`);
                badge.textContent = site;
                badge.textContent = status;
                switchBadgeStyle(badge, status);
            }).finally(() => {
                setInterval(() => {
                    reloadStatus();
                }, interval);
            });
        }

        function switchBadgeStyle(target, status) {
            switch (status.toUpperCase()) {
                case 'ALIVE':
                case 'OK':
                    target.classList.add('bg-success');
                    target.classList.remove('bg-warning');
                    target.classList.remove('bg-danger');
                    target.classList.remove('text-dark');
                    break;
                case 'WARNING':
                    target.classList.remove('bg-success');
                    target.classList.add('bg-warning');
                    target.classList.remove('bg-danger');
                    target.classList.add('text-dark');
                    break;
                default:
                    target.classList.remove('bg-success');
                    target.classList.remove('bg-warning');
                    target.classList.add('bg-danger');
                    target.classList.remove('text-dark');
                    break;
            }
        }

        axios.get('/api/sites').then((response) => {
            const listElement = document.querySelector('#site-list');
            const template = document.querySelector('#site-list-item');
            for (const site of response.data) {
                sites.push(site);
                const clone = template.content.cloneNode(true);
                const anker = clone.querySelector('a');
                anker.id = 'site-' + site;
                anker.href = '/monitor/' + site;
                const label = anker.querySelector('.status');
                label.textContent = site;
                listElement.appendChild(anker);
            }
        }).finally(() => {
            reloadStatus();
        });
    </script>
</x-layout>
