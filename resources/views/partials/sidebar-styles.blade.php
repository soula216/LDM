<style>
    /* Sidebar — styles critiques (chargés avec la vue, indépendants du build Vite) */
    @media (min-width: 1024px) {
        .app-main {
            padding-left: 17.5rem;
        }
    }

    .app-sidebar {
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 50%, #0f172a 100%) !important;
        box-shadow: 4px 0 24px rgba(15, 23, 42, 0.15);
        width: 17.5rem;
        color: #cbd5e1;
    }

    .sidebar-brand {
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .sidebar-brand p {
        color: #64748b !important;
    }

    .sidebar-section-title {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        margin-bottom: 0.5rem;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        color: #64748b !important;
    }

    .app-sidebar .sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0.75rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #cbd5e1 !important;
        transition: all 0.2s;
        text-decoration: none;
    }

    .app-sidebar .sidebar-link:hover {
        color: #fff !important;
        background-color: rgba(255, 255, 255, 0.08);
    }

    .app-sidebar .sidebar-link-active {
        color: #fff !important;
        font-weight: 600;
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.25) 0%, rgba(14, 165, 233, 0.08) 100%) !important;
        box-shadow: inset 3px 0 0 0 #0ea5e9;
    }

    .app-sidebar .sidebar-link-external {
        color: #94a3b8 !important;
        font-size: 0.75rem;
    }

    .app-sidebar .sidebar-icon {
        width: 1.25rem !important;
        height: 1.25rem !important;
        min-width: 1.25rem;
        min-height: 1.25rem;
        max-width: 1.25rem;
        max-height: 1.25rem;
        flex-shrink: 0;
        opacity: 0.8;
    }

    .app-sidebar .sidebar-link-active .sidebar-icon {
        opacity: 1;
        color: #38bdf8 !important;
    }

    .sidebar-scroll {
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 4px;
    }

    .app-sidebar .sidebar-footer {
        background: rgba(0, 0, 0, 0.2);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .app-sidebar .sidebar-user-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem;
        border-radius: 0.75rem;
        transition: color 0.2s, background-color 0.2s;
        width: 100%;
        border: none;
        background: transparent;
        cursor: pointer;
        color: inherit;
    }

    .app-sidebar .sidebar-user-btn:hover {
        background-color: rgba(255, 255, 255, 0.08);
    }

    .app-sidebar .sidebar-user-btn p.text-white {
        color: #fff !important;
    }

    .app-sidebar .sidebar-user-btn p.text-slate-400 {
        color: #94a3b8 !important;
    }

    .app-sidebar .sidebar-user-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: linear-gradient(135deg, #0ea5e9 0%, #22c55e 100%);
    }

    .app-sidebar .sidebar-dropdown-link {
        display: block;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        color: #cbd5e1 !important;
        text-decoration: none;
        transition: color 0.2s, background-color 0.2s;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .app-sidebar .sidebar-dropdown-link:hover {
        color: #fff !important;
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
