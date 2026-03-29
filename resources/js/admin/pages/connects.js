import * as Turbo from "@hotwired/turbo";

export function initConnectsPage() {
    if (!window.location.pathname.includes('connect')) return;

    window.deleteQuery = (id) => {
        if (!confirm(`Delete this Query no. #${id}?`)) return;

        window.axios.delete(`/admin/connect-actions/${id}`)
            .then(() => Turbo.visit(window.location.href, { action: "replace" }));
    };
}