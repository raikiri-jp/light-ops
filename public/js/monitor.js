const site = document.querySelector("#site").value;
const timezone = "Asia/Tokyo";
moment.locale("ja");

/**
 * Display an error message.
 *
 * @param {string} message Alert message
 */
function showError(message) {
    const div = document.createElement("div");
    div.classList.add("alert");
    div.classList.add("alert-danger");
    div.setAttribute("role", "alert");
    div.textContent = message;
    document.querySelector("main").appendChild(div);
}

/**
 * Switch status badge styles.
 *
 * @param {Element} badge A status badge
 * @param {string} status A status
 */
function switchBadgeStyle(badge, status) {
    switch (status.toUpperCase()) {
        case "ALIVE":
        case "OK":
            badge.classList.add("bg-success");
            badge.classList.remove("bg-warning");
            badge.classList.remove("bg-danger");
            badge.classList.remove("text-dark");
            break;
        case "WARNING":
            badge.classList.remove("bg-success");
            badge.classList.add("bg-warning");
            badge.classList.remove("bg-danger");
            badge.classList.add("text-dark");
            break;
        default:
            badge.classList.remove("bg-success");
            badge.classList.remove("bg-warning");
            badge.classList.add("bg-danger");
            badge.classList.remove("text-dark");
            break;
    }
}

/**
 * Switch alert message styles.
 *
 * @param {Element} target An alert message element
 * @param {string} status A status
 */
function switchAlertStyle(target, status) {
    switch (status) {
        case "ALIVE":
        case "OK":
            target.classList.add("alert-success");
            target.classList.remove("alert-danger");
            break;
        case "WARNING":
            target.classList.remove("alert-success");
            target.classList.add("alert-danger");
            break;
        default:
            target.classList.remove("alert-success");
            target.classList.add("alert-danger");
            break;
    }
}

/**
 * Switch table row styles.
 *
 * @param {Element} tr A table row element
 * @param {string} status A status
 */
function switchTableRowStyle(tr, status) {
    switch (status.toUpperCase()) {
        case "ALIVE":
        case "OK":
            tr.classList.remove("bg-warning");
            tr.classList.remove("bg-danger");
            tr.classList.remove("text-dark");
            break;
        case "WARNING":
            tr.classList.add("bg-warning");
            tr.classList.remove("bg-danger");
            tr.classList.add("text-dark");
            break;
        default:
            tr.classList.remove("bg-warning");
            tr.classList.add("bg-danger");
            tr.classList.remove("text-dark");
            break;
    }
}

/**
 * Recursively display the latest status.
 */
function loadLatestStatus() {
    axios
        .get(`/api/alive-log/${site}/status`)
        .then((response) => {
            const status = response.data.status;
            const messages = response.data.messages;
            // Update status
            const statusBadge = document.querySelector("#status-badge");
            statusBadge.textContent = status.toUpperCase();
            // Update alert messages
            const alertBlock = document.querySelector("#alert-messages");
            while (alertBlock.firstChild) {
                alertBlock.removeChild(alertBlock.firstChild);
            }
            if (Array.isArray(messages) && messages.length) {
                alertBlock.classList.remove("invisible");
                for (const message of messages) {
                    const div = document.createElement("div");
                    div.textContent = message;
                    alertBlock.appendChild(div);
                }
            } else {
                alertBlock.classList.add("invisible");
            }
            // Update CSS
            switchBadgeStyle(statusBadge, status);
            switchAlertStyle(alertBlock, status);
        })
        .catch((error) => {
            showError(error.message);
        })
        .finally(() => {
            // Auto reload
            setTimeout(() => {
                loadLatestStatus();
            }, 15_000);
        });
}

/**
 * Recursively display history.
 */
function loadHistory() {
    axios
        .get(`/api/alive-log/${site}/list`)
        .then((response) => {
            const tbody = document.querySelector("#alive-logs tbody");
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }
            const template = document.querySelector("#site-status-row");
            for (const record of response.data) {
                const status = record.status;
                const createdAt = moment(record.created_at).tz(timezone);
                const loggedAt = createdAt.format("LLL");

                const clone = template.content.cloneNode(true);
                const tr = clone.querySelector("tr");
                const statusCell = tr.querySelector(".logged-status");
                statusCell.textContent = status.toUpperCase();
                const timeCell = tr.querySelector(".logged-at");
                timeCell.querySelector("time").textContent = loggedAt;
                timeCell
                    .querySelector("time")
                    .setAttribute("datetime", createdAt.format());
                const fromNowElement = tr.querySelector(".from-now");
                fromNowElement.textContent = createdAt.fromNow();
                fromNowElement.setAttribute("datetime", createdAt.format());

                switchTableRowStyle(tr, status);

                tbody.appendChild(tr);
            }
        })
        .catch((error) => {
            showError(error.message);
        })
        .finally(() => {
            // Auto reload
            setTimeout(() => {
                loadHistory();
            }, 15_000);
        });
}
