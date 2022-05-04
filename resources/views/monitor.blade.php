<x-layout>
    <x-slot name="title">Alive Logs</x-slot>
    <input id="site" type="hidden" value="{{ $site }}" />
    <script>
        const site = document.querySelector('#site').value;
        const timezone = 'Asia/Tokyo';
        moment.locale('ja');

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

        function switchAlertStyle(target, status) {
            switch (status) {
                case 'ALIVE':
                case 'OK':
                    target.classList.add('alert-success');
                    target.classList.remove('alert-danger');
                    break;
                case 'WARNING':
                    target.classList.remove('alert-success');
                    target.classList.add('alert-danger');
                    break;
                default:
                    target.classList.remove('alert-success');
                    target.classList.add('alert-danger');
                    break;
            }
        }

        function switchTableRowStyle(tr, status) {
            switch (status.toUpperCase()) {
                case 'ALIVE':
                case 'OK':
                    tr.classList.remove('bg-warning');
                    tr.classList.remove('bg-danger');
                    tr.classList.remove('text-dark');
                    break;
                case 'WARNING':
                    tr.classList.add('bg-warning');
                    tr.classList.remove('bg-danger');
                    tr.classList.add('text-dark');
                    break;
                default:
                    tr.classList.remove('bg-warning');
                    tr.classList.add('bg-danger');
                    tr.classList.remove('text-dark');
                    break;
            }
        }

        function loadStatus() {
            axios.get(`/api/alive-log/${site}/status`).then((response) => {
                console.log(response);
                const status = response.data.status;
                const messages = response.data.messages;
                // Update status
                const statusBadge = document.querySelector('#status-badge');
                statusBadge.textContent = status.toUpperCase();
                // Update alert messages
                const alertBlock = document.querySelector('#alert-messages');
                while (alertBlock.firstChild) {
                    alertBlock.removeChild(alertBlock.firstChild);
                }
                if (Array.isArray(messages) && messages.length) {
                    alertBlock.classList.remove('invisible');
                    for (const message of messages) {
                        const div = document.createElement('div');
                        div.textContent = message;
                        alertBlock.appendChild(div);
                    }
                } else {
                    alertBlock.classList.add('invisible');
                }
                // Update CSS
                switchBadgeStyle(statusBadge, status);
                switchAlertStyle(alertBlock, status);
            }).finally(() => {
                // Auto reload
                setTimeout(() => {
                    loadStatus();
                }, 30_000);
            });
        }

        function loadList() {
            axios.get(`/api/alive-log/${site}/list`).then((response) => {
                const tbody = document.querySelector('#alive-logs tbody');
                while (tbody.firstChild) {
                    tbody.removeChild(tbody.firstChild);
                }
                const template = document.querySelector('#site-status-row');
                for (const record of response.data) {
                    const status = record.status;
                    const createdAt = moment(record.created_at).tz(timezone);
                    const loggedAt = createdAt.format('LLL');
                    const fromNow = createdAt.fromNow();

                    const clone = template.content.cloneNode(true);
                    const tr = clone.querySelector('tr');
                    const statusCell = tr.querySelector('.logged-status');
                    statusCell.textContent = status.toUpperCase();
                    const timeCell = tr.querySelector('.logged-at');
                    timeCell.querySelector('time').textContent = loggedAt;
                    timeCell.querySelector('time').setAttribute('datetime', createdAt.format());
                    const fromNowElement = tr.querySelector('.from-now');
                    fromNowElement.textContent = createdAt.fromNow();
                    fromNowElement.setAttribute('datetime', createdAt.format());

                    switchTableRowStyle(tr, status);

                    tbody.appendChild(tr);
                }
            }).finally(() => {
                // Auto reload
                setTimeout(() => {
                    loadList();
                }, 30_000);
            });
        }
    </script>

    <h3><span id="status-badge" class="badge"></span> {{ $site }}</h3>
    <div id="alert-messages" class="alert alert-danger invisible" role="alert"></div>
    <script>
        loadStatus();
    </script>

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
        loadList();
    </script>
</x-layout>
