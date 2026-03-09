import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import Chart from 'chart.js/auto';

// Alpine.js Setup
window.Alpine = Alpine;
Alpine.plugin(persist);

// ===========================================
// DARK MODE
// ===========================================
Alpine.store('darkMode', {
    init() {
        const saved = localStorage.getItem('darkMode');
        if (saved !== null) {
            this.on = JSON.parse(saved);
        } else {
            this.on = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this.apply();
    },
    on: false,
    toggle() {
        this.on = !this.on;
        localStorage.setItem('darkMode', JSON.stringify(this.on));
        this.apply();
    },
    apply() {
        if (this.on) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
});

// ===========================================
// SIDEBAR STATE
// ===========================================
Alpine.store('sidebar', {
    open: true,
    toggle() {
        this.open = !this.open;
    }
});

// ===========================================
// NOTIFICATIONS
// ===========================================
Alpine.store('notifications', {
    items: [],
    add(message, type = 'info', duration = 4000) {
        const id = Date.now();
        this.items.push({ id, message, type });
        setTimeout(() => this.remove(id), duration);
    },
    remove(id) {
        this.items = this.items.filter(n => n.id !== id);
    }
});

// ===========================================
// CHART.JS HELPERS
// ===========================================
window.createLineChart = function (ctx, labels, datasets, options = {}) {
    return new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
                        font: { family: 'Inter', size: 12 }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: document.documentElement.classList.contains('dark') ? '#1f2937' : '#f3f4f6' },
                    ticks: { color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280', font: { family: 'Inter', size: 11 } }
                },
                y: {
                    grid: { color: document.documentElement.classList.contains('dark') ? '#1f2937' : '#f3f4f6' },
                    ticks: { color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280', font: { family: 'Inter', size: 11 } }
                }
            },
            ...options
        }
    });
};

window.createBarChart = function (ctx, labels, datasets, options = {}) {
    return new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
                        font: { family: 'Inter', size: 12 }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280', font: { family: 'Inter', size: 11 } }
                },
                y: {
                    grid: { color: document.documentElement.classList.contains('dark') ? '#1f2937' : '#f3f4f6' },
                    ticks: { color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280', font: { family: 'Inter', size: 11 } }
                }
            },
            ...options
        }
    });
};

window.createDoughnutChart = function (ctx, labels, data, colors) {
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
                        font: { family: 'Inter', size: 12 },
                        padding: 16,
                        usePointStyle: true,
                    }
                }
            },
            cutout: '70%',
        }
    });
};

// ===========================================
// UTILITY FUNCTIONS
// ===========================================
window.formatNumber = function (n) {
    return new Intl.NumberFormat().format(n);
};

window.copyToClipboard = function (text) {
    navigator.clipboard.writeText(text);
    Alpine.store('notifications').add('Copied to clipboard!', 'success', 2000);
};

window.confirmDelete = function (form) {
    if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        form.submit();
    }
};

// ===========================================
// INIT
// ===========================================
document.addEventListener('DOMContentLoaded', () => {
    Alpine.store('darkMode').init();
});

Alpine.start();
