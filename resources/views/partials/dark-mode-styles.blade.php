<style>
    html {
        scroll-behavior: smooth;
    }

    /* Transition common properties */
    *, ::before, ::after {
        transition-property: background-color, border-color, color, fill, stroke;
        transition-duration: 300ms;
    }

    /* ---------- Dark Mode - Global overrides ---------- */
    html.dark {
        color-scheme: dark;
    }

    html.dark body {
        background-color: #020617; /* slate-950 */
        color: #f1f5f9; /* slate-100 */
    }

    /* ---------- Surfaces (blancs & gris clairs) ---------- */
    html.dark .bg-white,
    html.dark .bg-gray-50,
    html.dark .bg-slate-100 {
        background-color: #0f172a; /* slate-900 */
    }

    html.dark .bg-gray-100 {
        background-color: #1e293b; /* slate-800 */
    }

    html.dark .bg-gray-200,
    html.dark .bg-slate-200 {
        background-color: #334155; /* slate-700 */
    }

    html.dark .bg-slate-50,
    html.dark .bg-gray-300 {
        background-color: #1e293b; /* slate-800 */
    }

    /* ---------- Surfaces teintées (badges, chips, icônes) ---------- */
    html.dark .bg-indigo-50 {
        background-color: #312e81; /* indigo-900 */
        color: #e0e7ff;
    }

    html.dark .bg-blue-50 {
        background-color: #1e293b; /* slate-800 */
        color: #f1f5f9;
    }

    html.dark .bg-green-50,
    html.dark .bg-emerald-50 {
        background-color: #064e3b; /* emerald-900 */
        color: #d1fae5;
    }

    html.dark .bg-red-50,
    html.dark .bg-rose-50 {
        background-color: #450a0a; /* red-950 */
        color: #fecaca;
    }

    html.dark .bg-yellow-50,
    html.dark .bg-amber-50 {
        background-color: #422006; /* amber-950 */
        color: #fef3c7;
        border-color: #92400e;
    }

    html.dark .bg-purple-50,
    html.dark .bg-violet-50 {
        background-color: #2e1065; /* violet-950 */
        color: #ddd6fe;
    }

    html.dark .bg-orange-50 {
        background-color: #431407; /* orange-950 */
        color: #fed7aa;
    }

    html.dark .bg-pink-50 {
        background-color: #500724; /* pink-950 */
        color: #fbcfe8;
    }

    html.dark .bg-cyan-50 {
        background-color: #083344; /* cyan-950 */
        color: #a5f3fc;
    }

    html.dark .bg-teal-50 {
        background-color: #042f2e; /* teal-950 */
        color: #99f6e4;
    }

    html.dark .bg-primary-50 {
        background-color: #0b2b54; /* primary-950 */
        color: #bae0fd;
    }

    html.dark .bg-success-50 {
        background-color: #052e16; /* success-950 */
        color: #bbf7d0;
    }

    html.dark .bg-warning-50 {
        background-color: #451a03; /* warning-950 */
        color: #fde68a;
    }

    html.dark .bg-danger-50 {
        background-color: #450a0a; /* danger-950 */
        color: #fecaca;
    }

    html.dark .bg-info-50 {
        background-color: #172554; /* info-950 */
        color: #bfdbfe;
    }

    /* Niveaux 100 (fond avec icône) */
    html.dark .bg-indigo-100 {
        background-color: #312e81;
        color: #c7d2fe;
    }

    html.dark .bg-blue-100 {
        background-color: #1e3a8a;
        color: #bfdbfe;
    }

    html.dark .bg-green-100 {
        background-color: #14532d;
        color: #bbf7d0;
    }

    html.dark .bg-emerald-100 {
        background-color: #064e3b;
        color: #a7f3d0;
    }

    html.dark .bg-red-100,
    html.dark .bg-rose-100 {
        background-color: #7f1d1d;
        color: #fecaca;
    }

    html.dark .bg-yellow-100,
    html.dark .bg-amber-100 {
        background-color: #78350f;
        color: #fde68a;
    }

    html.dark .bg-purple-100,
    html.dark .bg-violet-100 {
        background-color: #4c1d95;
        color: #ddd6fe;
    }

    html.dark .bg-orange-100 {
        background-color: #7c2d12;
        color: #fed7aa;
    }

    html.dark .bg-pink-100 {
        background-color: #831843;
        color: #fbcfe8;
    }

    html.dark .bg-cyan-100 {
        background-color: #155e75;
        color: #a5f3fc;
    }

    /* ---------- Bordures ---------- */
    html.dark .border-slate-100,
    html.dark .border-gray-100 {
        border-color: #1e293b;
    }

    html.dark .border-slate-200,
    html.dark .border-gray-200 {
        border-color: #334155;
    }

    html.dark .border-slate-300,
    html.dark .border-gray-300 {
        border-color: #475569;
    }

    html.dark .border-indigo-100 { border-color: #312e81; }
    html.dark .border-indigo-200 { border-color: #3730a3; }
    html.dark .border-blue-100 { border-color: #1e3a8a; }
    html.dark .border-blue-200 { border-color: #1e40af; }
    html.dark .border-green-100 { border-color: #166534; }
    html.dark .border-green-200 { border-color: #15803d; }
    html.dark .border-emerald-100 { border-color: #065f46; }
    html.dark .border-emerald-200 { border-color: #047857; }
    html.dark .border-red-100 { border-color: #7f1d1d; }
    html.dark .border-red-200 { border-color: #991b1b; }
    html.dark .border-rose-100 { border-color: #881337; }
    html.dark .border-rose-200 { border-color: #be123c; }
    html.dark .border-amber-100 { border-color: #78350f; }
    html.dark .border-amber-200 { border-color: #92400e; }
    html.dark .border-yellow-100 { border-color: #713f12; }
    html.dark .border-yellow-200 { border-color: #854d0e; }
    html.dark .border-purple-100 { border-color: #4c1d95; }
    html.dark .border-purple-200 { border-color: #581c87; }
    html.dark .border-violet-100 { border-color: #4c1d95; }
    html.dark .border-orange-100 { border-color: #7c2d12; }
    html.dark .border-orange-200 { border-color: #9a3412; }
    html.dark .border-pink-100 { border-color: #831843; }
    html.dark .border-pink-200 { border-color: #9d174d; }
    html.dark .border-cyan-100 { border-color: #155e75; }
    html.dark .border-cyan-200 { border-color: #0e7490; }

    /* ---------- Couleurs de texte ---------- */
    html.dark .text-gray-900,
    html.dark .text-gray-800,
    html.dark .text-gray-700,
    html.dark .text-slate-900,
    html.dark .text-slate-800,
    html.dark .text-slate-700 {
        color: #f1f5f9;
    }

    html.dark .text-gray-600,
    html.dark .text-gray-500,
    html.dark .text-slate-600,
    html.dark .text-slate-500 {
        color: #94a3b8; /* slate-400 */
    }

    html.dark .text-gray-400,
    html.dark .text-slate-400 {
        color: #64748b; /* slate-500 */
    }

    /* Note : .text-gray-300 reste volontairement sombre (étoiles de notation) */
    html.dark .text-gray-300 {
        color: #334155;
    }

    /* Texte teinté (usage principal : liens + petits textes) */
    html.dark .text-indigo-600 { color: #818cf8; }
    html.dark .text-indigo-700 { color: #a5b4fc; }
    html.dark .text-blue-600 { color: #60a5fa; }
    html.dark .text-blue-700 { color: #93c5fd; }
    html.dark .text-green-600 { color: #4ade80; }
    html.dark .text-green-700 { color: #86efac; }
    html.dark .text-emerald-600 { color: #34d399; }
    html.dark .text-emerald-700 { color: #6ee7b7; }
    html.dark .text-red-600 { color: #f87171; }
    html.dark .text-red-700 { color: #fca5a5; }
    html.dark .text-rose-600 { color: #fb7185; }
    html.dark .text-rose-700 { color: #fda4af; }
    html.dark .text-amber-600 { color: #fbbf24; }
    html.dark .text-amber-700 { color: #fcd34d; }
    html.dark .text-yellow-600 { color: #facc15; }
    html.dark .text-yellow-700 { color: #fde047; }
    html.dark .text-purple-600 { color: #a78bfa; }
    html.dark .text-purple-700 { color: #c4b5fd; }
    html.dark .text-violet-600 { color: #a78bfa; }
    html.dark .text-violet-700 { color: #c4b5fd; }
    html.dark .text-orange-600 { color: #fb923c; }
    html.dark .text-orange-700 { color: #fdba74; }
    html.dark .text-pink-600 { color: #f472b6; }
    html.dark .text-pink-700 { color: #f9a8d4; }
    html.dark .text-cyan-600 { color: #22d3ee; }
    html.dark .text-cyan-700 { color: #67e8f9; }
    html.dark .text-teal-600 { color: #2dd4bf; }
    html.dark .text-teal-700 { color: #5eead4; }
    html.dark .text-primary-600 { color: #7cc2fb; }
    html.dark .text-primary-700 { color: #bae0fd; }
    html.dark .text-success-600 { color: #4ade80; }
    html.dark .text-success-700 { color: #86efac; }
    html.dark .text-warning-600 { color: #fbbf24; }
    html.dark .text-warning-700 { color: #fcd34d; }
    html.dark .text-danger-600 { color: #f87171; }
    html.dark .text-danger-700 { color: #fca5a5; }
    html.dark .text-info-600 { color: #38bdf8; }
    html.dark .text-info-700 { color: #7dd3fc; }

    /* ---------- Composants (providers.css / admin.css) ---------- */
    html.dark .provider-dashboard,
    html.dark .admin-dashboard {
        background-color: #020617;
    }

    html.dark .provider-header,
    html.dark .admin-header {
        background-color: #0f172a;
        border-bottom-color: #334155;
    }

    html.dark .provider-title,
    html.dark .admin-title,
    html.dark .chart-title,
    html.dark .activities-title,
    html.dark .table-title,
    html.dark .modal-title,
    html.dark .form-section-title,
    html.dark .assignment-title,
    html.dark .booking-title {
        color: #f1f5f9;
    }

    html.dark .provider-subtitle,
    html.dark .admin-subtitle,
    html.dark .stat-label,
    html.dark .assignment-client,
    html.dark .booking-client,
    html.dark .activity-time,
    html.dark .user-email,
    html.dark .provider-category,
    html.dark .provider-stat-label,
    html.dark .filter-label,
    html.dark .form-help {
        color: #94a3b8;
    }

    html.dark .stat-card,
    html.dark .chart-card,
    html.dark .recent-activities,
    html.dark .data-table,
    html.dark .user-card,
    html.dark .provider-card,
    html.dark .assignment-card,
    html.dark .booking-card,
    html.dark .form-section,
    html.dark .media-item,
    html.dark .modal-content,
    html.dark .toast {
        background-color: #111827; /* gray-900 */
        border-color: #374151; /* gray-700 */
    }

    html.dark .assignment-header,
    html.dark .booking-header,
    html.dark .modal-header,
    html.dark .modal-footer,
    html.dark .form-section-title {
        border-bottom-color: #374151;
    }

    html.dark .assignment-footer,
    html.dark .booking-footer {
        background-color: #1e293b;
        border-top-color: #374151;
    }

    /* Tableaux */
    html.dark .table th {
        background-color: #1e293b;
        color: #94a3b8;
    }

    html.dark .table td {
        border-top-color: #334155;
        color: #e5e7eb;
    }

    html.dark .table tbody tr:hover {
        background-color: #1e293b;
    }

    /* Formulaires (classes du boilerplate) */
    html.dark .form-input,
    html.dark .form-textarea,
    html.dark .form-select {
        background-color: #1e293b;
        color: #f1f5f9;
        border-color: #475569;
    }

    html.dark .form-input:disabled {
        background-color: #111827;
        color: #64748b;
    }

    html.dark .form-label,
    html.dark .assignment-detail-label,
    html.dark .booking-detail-label {
        color: #cbd5e1;
    }

    html.dark .assignment-detail-value,
    html.dark .booking-detail-value {
        color: #f1f5f9;
    }

    /* Boutons & badges */
    html.dark .btn-outline {
        border-color: #475569;
        color: #e2e8f0;
    }

    html.dark .btn-outline:hover {
        background-color: #1e293b;
    }

    html.dark .badge-primary {
        background-color: #1e3a8a;
        color: #bfdbfe;
    }

    html.dark .badge-success {
        background-color: #14532d;
        color: #bbf7d0;
    }

    html.dark .badge-warning {
        background-color: #78350f;
        color: #fde68a;
    }

    html.dark .badge-danger {
        background-color: #7f1d1d;
        color: #fecaca;
    }

    html.dark .badge-gray {
        background-color: #1e293b;
        color: #cbd5e1;
    }

    html.dark .user-avatar,
    html.dark .provider-logo,
    html.dark .media-placeholder {
        background-color: #334155;
    }

    html.dark .activity-item:hover,
    html.dark .pagination-button:hover:not(.disabled) {
        background-color: #1e293b;
    }

    /* Pagination */
    html.dark .pagination-info {
        color: #cbd5e1;
    }

    html.dark .pagination-button {
        border-color: #475569;
        color: #cbd5e1;
    }

    /* ---------- Forms (éléments natifs) ---------- */
    html.dark input,
    html.dark select,
    html.dark textarea {
        background-color: #1e293b;
        color: #f1f5f9;
        border-color: #475569;
    }

    html.dark input::placeholder,
    html.dark textarea::placeholder {
        color: #64748b;
    }

    html.dark select option {
        background-color: #0f172a;
        color: #f1f5f9;
    }

    /* ---------- Survol ---------- */
    html.dark .hover\:bg-slate-50:hover,
    html.dark .hover\:bg-gray-50:hover {
        background-color: #1e293b;
    }

    html.dark .hover\:bg-slate-100:hover,
    html.dark .hover\:bg-gray-100:hover {
        background-color: #334155;
    }

    html.dark .hover\:bg-indigo-50:hover {
        background-color: #312e81;
    }

    html.dark .hover\:bg-indigo-100:hover {
        background-color: #3730a3;
    }

    html.dark .hover\:bg-blue-50:hover {
        background-color: #1e3a8a;
    }

    html.dark .hover\:bg-red-50:hover,
    html.dark .hover\:bg-rose-50:hover {
        background-color: #7f1d1d;
    }

    html.dark .hover\:bg-emerald-50:hover,
    html.dark .hover\:bg-green-50:hover {
        background-color: #064e3b;
    }

    /* ---------- Ombres ---------- */
    html.dark .shadow-sm,
    html.dark .shadow-md,
    html.dark .shadow-lg,
    html.dark .shadow-xl {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.3) !important;
    }

    /* ---------- Scrollbar ---------- */
    html.dark ::-webkit-scrollbar {
        width: 10px;
    }

    html.dark ::-webkit-scrollbar-track {
        background: #020617;
    }

    html.dark ::-webkit-scrollbar-thumb {
        background: #1e293b;
        border-radius: 5px;
    }

    html.dark ::-webkit-scrollbar-thumb:hover {
        background: #334155;
    }

    /* ---------- Header & menus ---------- */
    html.dark header {
        background-color: rgba(15, 23, 42, 0.9) !important;
        border-bottom-color: #1e293b !important;
        backdrop-filter: blur(10px);
    }

    html.dark .preferences-menu {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
    }

    /* ---------- Contenu typographique (prose) ---------- */
    html.dark .prose {
        color: #e5e7eb;
    }

    html.dark .prose h1,
    html.dark .prose h2,
    html.dark .prose h3,
    html.dark .prose h4 {
        color: #f1f5f9;
    }

    html.dark .prose a {
        color: #818cf8;
    }

    html.dark .prose strong {
        color: #f1f5f9;
    }

    html.dark .prose hr {
        border-color: #334155;
    }
</style>